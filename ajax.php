<?php
define("AppiEngine", true);
define("IMAGE_CDN_PATH", 'https://adaptation-usa.com');

header('Powered: Alexander Pozharov');
header("Cache-control: public");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT");
//header("Cache-Control: no-store,no-cache,mustrevalidate");
header('Content-Type: application/json; charset=utf-8');

//echo '<h1 style="margin-top: 150px; width: 100%; text-align: center">Тех. работы</h1>';
//return;

@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("upload_max_filesize", '10M');

spl_autoload_register(function($class) {
    include_once str_replace('\\', '/', $class) . '.php';
});

$langType = 'en';
if (isset($_COOKIE['lang']))
    if ($_COOKIE['lang'] == 'ru')
        $langType = 'ru';

include_once 'globals.php';
include_once 'lang/' . $langType . '.php';

use Server\Core\Init;
use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Request;
use Server\Core\Template;
use Server\Core\Server;
use Server\Core\Settings;
use Server\Manager\PermissionManager;
use Server\Manager\RequestManager;
use Server\Manager\TemplateManager;
use Server\Methods;
use Server\User;
use Server\Files;

global $modal;
global $lang;
global $UTC_TO_TIME;
global $sections;
global $subSections;
global $userInfo;
global $userInfoSession;

$UTC = 0;
if (isset($_COOKIE['UTC']))
    $UTC = $_COOKIE['UTC'];
$UTC_TO_TIME = $UTC * 3600;

$init = new Init;
$init->initAppi();

$qb = new QueryBuilder();
$qb->connectDataBase(EnumConst::DB_HOST, EnumConst::DB_NAME, EnumConst::DB_USER, EnumConst::DB_PASS);

$view = new Template('/template/');
$requests = new RequestManager();
$permissionManager = new PermissionManager();
$tmp = new TemplateManager($view, $init);
$request = new Request();
$server = new Server();
$methods = new Methods();
$settings = new Settings();
$user = new User($qb);
$files = new Files();

$redis = new Redis();
$redis->connect('127.0.0.1');

//$redis->setEx('test', 300, 'test');

if (isset($_COOKIE['user'])) {
    $userInfoSession = $user->getUserToken($_COOKIE['user']);
    if (empty($userInfoSession)) {
        $userInfo['id'] = -1;
        setcookie('user', '', time()-3600, '/');
    }
    else
        $userInfo = $user->getUserInfoById($userInfoSession['user_id']);
}
else
    $userInfo['id'] = -1;

if (isset($_POST['ajax'])) {
    $page['p'] = '/';
    if (isset($_POST['page']))
        $page = $request->getAjaxRequest($_POST['page']);

    if (isset($_POST['page']) && strpos($_POST['page'], '?') !== false)
        $_POST['page'] = substr($_POST['page'], 0, strpos($_POST['page'], "?"));

    switch ($_POST['action']) {
        case 'show-page':
            $user->updateOnlineStatus();
            $request->showPage($page, true);
            break;
        case 'show-feed':
            $view->set('limit', intval($_POST['limit']));
            $view->set('page', intval($_POST['page']));
            $view->set('hash', $_POST['hash']);
            $tmp->showBlockPage('feedList');
            break;
        case 'show-im':

            if (!$user->isLogin())
                return;

            $uid = intval($_POST['id']);
            $p = intval($_POST['page']);
            $dialogItem = $user->getDialogByUserId($uid);
            if (!empty($dialogItem)) {
                $messageList = $user->getDialogMessages($dialogItem['id'], $p);
                foreach ($messageList as $item) {
                    if ($item['user_id'] == $userInfo['id'])
                        echo $methods->getMessageSent($item['id'], $item['text'], $item['reaction'], $item['timestamp'], $item['is_read']);
                    else
                        echo $methods->getMessageRecived($item['id'], $item['text'], $item['reaction'], $item['timestamp'], $item['is_read']);

                }
            }
            break;
        case 'show-feed-profile':
            $nPage = intval($_POST['page']);
            $limit = $nPage ? $nPage * 12 : 12;
            $userPageInfo = $user->getUserInfoByLogin($_POST['user']);

            if ($user->isLogin() && !empty($userPageInfo)) {
                if ($userPageInfo['id'] != $userInfo['id'])
                {
                    if (!$userPageInfo['is_public'] && !$user->hasFollowUser($userPageInfo['id']))
                        return;
                }
            }
            else if (empty($userPageInfo))
                return;
            else if (!$userPageInfo['is_public'])
                return;

            $resultFeed = $qb
                ->createQueryBuilder('feed')
                ->selectSql()
                ->orderBy('timestamp DESC')
                ->limit(($limit - 12) . ', 12')
                ->where('is_draft = 0')
                ->andWhere('type = \'img\'')
                ->andWhere('user_id = ' . $userPageInfo['id'])
                ->executeQuery()
                ->getResult()
            ;

            foreach ($resultFeed as $item) {
                $img = json_decode(htmlspecialchars_decode($item['img']),true);
                echo '
                    <a spa="uf/' . $item['hash'] . '" class="square grey" style="width: 33%; margin: 0.2% auto; flex-wrap: wrap;">
                        ' . (count($img) > 1 ? '<i class="material-icons-round feed-image-collection white-text">collections</i>' : '') . '
                        <img alt="Photo by @' . $userPageInfo['login'] . ' on SLAFY RU (' . $item['location'] . ')" class="square-content" style="width: 100%; height: 100%; object-fit: cover" src="' . IMAGE_CDN_PATH . '/upload/feed/' . $userPageInfo['id'] . '/' . reset($img) . '">
                    </a>
                ';
            }
            break;
        case 'enable-light-theme':
            $enable = $_POST['enabled'] == 'true' ? 1 : 0;
            if ($enable == 1)
                setcookie("slafy-is-light", true, 0x6FFFFFFF, "/", $_SERVER['HTTP_HOST'] . "");
            else
                setcookie("slafy-is-light", false, 0, "/", $_SERVER['HTTP_HOST'] . "");
            echo 'true';
            break;
        case 'user-edit-name':
            $user->setName($_POST['name']);
            echo json_encode(['message' => 'Ник был обновлён']);
            break;
        case 'user-edit-website':
            $user->setWebSite($_POST['website']);
            echo json_encode(['message' => 'Вебсайт был обновлён']);
            break;
        case 'user-edit-desc':
            $user->setAbout($_POST['content']);
            echo json_encode(['message' => 'Информация была обновлена']);
            break;
        case 'user-edit-login':
            echo json_encode(['message' => $user->setLogin($_POST['text'])]);
            break;
        case 'user-edit-email':
            if (isset($_SESSION['time']) && $_SESSION['time'] > time() || isset($_COOKIE['timeout'])) {
                echo json_encode(['message' => 'Нельзя так часто нажимать, подождите 1 минуту']);
                return;
            }
            $_SESSION['time'] = time() + 60;
            echo json_encode(['message' => $user->setEmail($_POST['email'])]);
            break;
        case 'user-edit-pass':
            echo json_encode(['message' => $user->setPassword($_POST['pass1'], $_POST['pass2'])]);
            break;
        case 'user-edit-color':
            $user->setColor($_POST['color-bg'], $_POST['color-btn']);
            echo json_encode(['message' => 'refresh']);
            break;
        case 'user-edit-type-profile':
            $user->setPublicProfile($_POST['check'] == 'true');
            echo json_encode(['message' => 'Значение было сохранено']);
            break;
        case 'user-edit-hide-online':
            $user->setPublicOnline($_POST['check'] == 'true');
            echo json_encode(['message' => 'Значение было сохранено']);
            break;
        case 'user-edit-hide-followers':
            $user->setShowFollowers($_POST['check'] == 'true');
            echo json_encode(['message' => 'Значение было сохранено']);
            break;
        case 'user-edit-hide-follows':
            $user->setShowFollows($_POST['check'] == 'true');
            echo json_encode(['message' => 'Значение было сохранено']);
            break;
        case 'user-edit-like-or-feed':
            $user->setShowProfileFeed($_POST['check'] == 'true');
            echo json_encode(['message' => 'Значение было сохранено']);
            break;
        case 'user-follow':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Для этого действия необходима авторизация']);
                return;
            }

            if (!$user->isActivate()) {
                echo json_encode(['message' => 'Необходимо привязать почту, чтобы пользоваться полноценно всеми функциями SLAFY']);
                return;
            }

            if ($user->isFollow($_POST['id'])) {
                echo json_encode(['message' => 'Вы уже подписались на этот аккаунт']);
                return;
            }

            $userFollowInfo = $user->getUserInfoById($_POST['id']);
            if (!empty($userFollowInfo) && $user->isLogin()) {

                $user->followUser($userFollowInfo['id'], $userFollowInfo['count_followers'], !$userFollowInfo['is_public']);
                if (!$userFollowInfo['is_public']) {
                    $hasNotify = $redis->get('notify:' . $userFollowInfo['id']);
                    if ($hasNotify)
                        $redis->setEx('notify:' . $userFollowInfo['id'], 300, $hasNotify + 1);
                    else
                        $redis->setEx('notify:' . $userFollowInfo['id'], 300, 1);
                    echo json_encode(['message' => 'Запрос на подписку был отправлен', 'button' => 'Отменить запрос', 'buttonName' => 'user-unfollow']);
                }
                else {
                    $user->notify($userFollowInfo['id'], $userInfo['id'], 0);
                    echo json_encode(['message' => 'Вы подписались на аккаунт', 'button' => 'Отписаться', 'buttonName' => 'user-unfollow']);
                }
            }
            else
                echo json_encode(['message' => 'Ошибка выполнения запроса']);
            break;
        case 'user-unfollow':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Для этого действия необходима авторизация']);
                return;
            }
            if (!$user->isActivate()) {
                echo json_encode(['message' => 'Необходимо привязать почту, чтобы пользоваться полноценно всеми функциями SLAFY']);
                return;
            }

            if (!$user->isFollow($_POST['id'])) {
                echo json_encode(['message' => 'Вы не подписаны на аккаунт']);
                return;
            }
            
            $userFollowInfo = $user->getUserInfoById($_POST['id']);
            if (!empty($userFollowInfo) && $user->isLogin()) {
                $status = $user->unfollowUser($userFollowInfo['id'], $userFollowInfo['count_followers']);
                if ($status == 2) {
                    $user->notify($userFollowInfo['id'], $userInfo['id'], 1);
                    echo json_encode(['message' => 'Вы отписались от этого аккаунта', 'button' => 'Подписаться', 'buttonName' => 'user-follow']);
                }
                else {
                    echo json_encode(['message' => 'Вы отменили запрос на подписку', 'button' => 'Подписаться', 'buttonName' => 'user-follow']);
                }
            }
            else
                echo json_encode(['message' => 'Ошибка выполнения запроса']);
            break;
        case 'user-follow-request-accept':
            if ($user->isLogin()) {
                $user->notify($_POST['uid'], $userInfo['id'], 2);
                $user->notify($userInfo['id'], $_POST['uid'], 0);
                $user->followAccept($_POST['id'], $_POST['uid']);
                echo json_encode(['message' => 'Запрос на подписку был одобрен', 'page' => 'notify']);
            }
            else
                echo json_encode(['message' => 'Ошибка выполнения запроса']);
            break;
        case 'user-follow-request-decline':
            if ($user->isLogin()) {
                $user->followDecline($_POST['id']);
                echo json_encode(['message' => 'Запрос на подписку был отклонён', 'page' => 'notify']);
            }
            else
                echo json_encode(['message' => 'Ошибка выполнения запроса']);
            break;
        case 'feed-like':
            if (!$user->isLogin())
                return;
            if (!$user->isActivate()) {
                echo '';
                return;
            }
            $feed = new \Server\Feed();
            $feedItem = $feed->getFeedByHash($_POST['id']);
            if ($user->feedIsLike($feedItem['id'])) {
                $user->feedUnLike($feedItem);
                echo $server->numberToKkk(--$feedItem['likes']);
            }
            else {
                $user->feedLike($feedItem);
                echo $server->numberToKkk(++$feedItem['likes']);
            }
            break;
        case 'feed-delete':
            if (!$user->isLogin())
                return;
            $user->feedDelete($_POST['id']);
            echo json_encode(['message' => 'Пост был удалён', 'action' => 'feedDelete', 'id' => $_POST['id']]);

            break;
        case 'feed-comment-load':
            if (!$user->isLogin())
                return;
            $tmp->showBlockPage('feedComments');
            break;
        case 'feed-send-comment':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Аккаунт не авторизован']);
                return;
            }
            if (!$user->isActivate()) {
                echo json_encode(['message' => 'Необходимо привязать почту, чтобы пользоваться полноценно всеми функциями SLAFY']);
                return;
            }
            if (trim($_POST['text']) == '') {
                echo json_encode(['message' => 'Комментарий не может быть пустым']);
                return;
            }
            $feed = new \Server\Feed();
            $feedItem = $feed->getFeedByHash($_POST['id']);
            $user->feedComment($feedItem, $_POST['reply-id'], $_POST['text']);
            echo json_encode(['message' => 'Комментарий был опубликован', 'action' => 'refreshComment', 'id' => $_POST['id']]);
            break;
        case 'feed-comment-like':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Аккаунт не авторизован']);
                return;
            }
            if (!$user->isActivate()) {
                echo json_encode(['message' => 'Необходимо привязать почту, чтобы пользоваться полноценно всеми функциями SLAFY']);
                return;
            }
            $feed = new \Server\Feed();
            $commentItem = $feed->getFeedComment($_POST['id']);
            $countLikes = 0;
            $likeColor = 'bw-text';
            if ($user->feedCommentIsLike($commentItem['id'])) {
                $user->feedCommentUnLike($commentItem);
                $countLikes = $server->numberToKkk(--$commentItem['likes']);
            }
            else {
                $likeColor = 'red-text';
                $user->feedCommentLike($commentItem);
                $countLikes = $server->numberToKkk(++$commentItem['likes']);
            }
            echo json_encode(['action' => 'feedCommentLike', 'id' => $_POST['id'], 'feedCommentColor' => $likeColor, 'feedCommentLikeCount' => $countLikes]);
            break;
        case 'feed-comment-del':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Аккаунт не авторизован']);
                return;
            }
            $feed = new \Server\Feed();
            $commentItem = $feed->getFeedComment($_POST['id']);
            if ($commentItem['user_id'] == $userInfo['id']) {
                $feedItem = $feed->getFeedById($commentItem['feed_id']);
                $qb
                    ->createQueryBuilder('feed')
                    ->updateSql(['comments'], [--$feedItem['comments']])
                    ->where('id = ' . $feedItem['id'])
                    ->executeQuery()
                    ->getResult()
                ;
                $user->feedCommentDelete($commentItem['id']);
                echo json_encode(['action' => 'feedCommentDelete', 'id' => $_POST['id']]);
            }
            break;
        case 'send-message':
            if (!$user->isLogin()) {
                echo json_encode(['message' => 'Аккаунт не авторизован']);
                return;
            }

            if (!$user->isActivate()) {
                echo json_encode(['message' => 'Необходимо привязать почту, чтобы пользоваться полноценно всеми функциями SLAFY']);
                return;
            }

            if (trim($_POST['text']) == '') {
                echo json_encode(['message' => 'Нельзя отправлять пустые сообщения']);
                return;
            }

            $uId = intval($_POST['id']);
            $dialogItem = $user->getDialogByUserId($uId);
            if (empty($dialogItem))
                $dialogItem = $user->createDialog($uId, !$user->isFollowMe($uId));

            $user->sendDialogMessage($dialogItem['id'], $_POST['text'], $_POST['reply-id']);
            $user->updateDialogRead($dialogItem['id'], $uId);
            $user->updateDialogSettingsType($dialogItem['id'], 0);

            $hasNotify = $redis->get('notify:dialog:' . $uId);
            if ($hasNotify)
                $redis->setEx('notify:dialog:' . $uId, 300, $hasNotify + 1);
            else
                $redis->setEx('notify:dialog:' . $uId, 300, 1);

            $lastMessage = $user->getDialogLastMyMessage($dialogItem['id']);
            $dialogMessage = $methods->getMessageSent($lastMessage['id'], htmlspecialchars($_POST['text']), '', $lastMessage['timestamp'], false);
            $dialogMessageR = $methods->getMessageRecived($lastMessage['id'], htmlspecialchars($_POST['text']), '', $lastMessage['timestamp'], false);

            $redis->setEx('notify:dialog:message:' . $userInfo['id'] . ':' . $uId, 10, $dialogMessageR);

            echo json_encode(['action' => 'sendDialogMessage', 'msg' => $dialogMessage]);
            break;
        case 'message-like':
            if (!$user->isLogin())
                return;
            $user->setDialogMessageReaction($_POST['id'], '❤️');
            echo '❤️';
            break;
        case 'dialog-update-settings':
            if (!$user->isLogin())
                return;
            $dType = intval($_POST['type']);
            $user->updateDialogSettingsType($_POST['id'], $dType);
            if ($dType == 0)
                echo json_encode(['message'=>'Диалог был перемещён в основное', 'page' => 'im/' . $_POST['uid']]);
            else if ($dType == 1)
                echo json_encode(['message'=>'Диалог был закреплён', 'page' => 'im/' . $_POST['uid']]);
            else if ($dType == 2)
                echo json_encode(['message'=>'Диалог был перемещен в архив', 'page' => 'im/archive/' . $_POST['uid']]);
            else if ($dType == 3)
                echo json_encode(['message'=>'Диалог был перемещен в скрытое', 'page' => 'im/hidden/' . $_POST['uid']]);
            break;
        case 'dialog-delete':
            if (!$user->isLogin())
                return;
            $dId = intval($_POST['id']);
            $dialogItem = $user->getDialogById($dId);
            if (empty($dialogItem)) {
                echo json_encode(['message'=>'Диалог не был найден']);
                return;
            }
            if ($dialogItem['uid1'] != $userInfo['id'] && $dialogItem['uid2'] != $userInfo['id']) {
                echo json_encode(['message'=>'Ошибка удаления диалога #666']);
                return;
            }
            $user->deleteDialog($dId);
            echo json_encode(['message'=>'Диалог был удалён и восстановлению не подлежит', 'page' => 'im']);
            break;
        case 'destroy-session':
            if (!$user->isLogin())
                return;
            $sId = intval($_POST['id']);
            $sItem = $qb->createQueryBuilder('log_login')->selectSql()->where('id = ' . $sId)->andWhere('user_id = ' . $userInfo['id'])->limit(1)->executeQuery()->getSingleResult();
            if (empty($sItem)) {
                echo json_encode(['message'=>'Сессия не найдена']);
                return;
            }
            $qb->createQueryBuilder('log_login')->updateSql(['token'], [''])->where('id = ' . $sId)->andWhere('user_id = ' . $userInfo['id'])->limit(1)->executeQuery()->getSingleResult();
            echo json_encode(['message'=>'Сессия была удалена']);
            break;
        case 'search-dialog':
            if (!$user->isLogin())
                return;
            global $server;
            $sQuery = $server->charsString(strtolower($_POST['q']));

            $uList = $qb->createQueryBuilder('users')->selectSql()->where('login LIKE \'%' . $sQuery . '%\'')->limit(20)->executeQuery()->getResult();

            if (empty($uList))
                echo '<h5 class="grey-text center">Список пуст</h5>';

            foreach ($uList as $item)
                echo $methods->getDialogListItem($item, '', 0, 0, 'Написать сообщение...', 0, false);
            break;
        case 'lp-notify':
            if ($user->isLogin()) {
                $hasNotify = $redis->get('notify:' . $userInfo['id']);
                $hasNotifyDialog = $redis->get('notify:dialog:' . $userInfo['id']);

                if ($hasNotify)
                    $redis->unlink('notify:' . $userInfo['id']);
                if ($hasNotifyDialog)
                    $redis->unlink('notify:dialog:' . $userInfo['id']);

                echo json_encode(['countNotify' => $hasNotify, 'countNotifyDialog' => $hasNotifyDialog]);
            }
            break;
        case 'lp-message':
            if ($user->isLogin()) {
                if ($_POST['id'] == $userInfo['id']) {
                    echo json_encode(['id' => 0, 'msg' => '', 'msgt' => 'В сети', 'msgo' => '']);
                    return;
                }
                $hasNotify = $redis->get('notify:dialog:message:' . $_POST['id'] . ':' . $userInfo['id']);
                $hasNotifyKp = $redis->get('notify:dialog:message:typing:' . $_POST['id'] . ':' . $userInfo['id']);
                $msgt = 'В сети';
                $msgo = true;
                if ($hasNotifyKp)
                    $msgt = 'Печатает...';
                else {
                    $uTypeInfo = $user->getUserInfoById($_POST['id']);
                    $msgt = $methods->getOnlineBlock($uTypeInfo['online_status'], $uTypeInfo['online_hidden']);
                    if ($msgt != 'В сети')
                        $msgo = false;
                }
                if ($hasNotify)
                {
                    $redis->unlink('notify:dialog:message:' . $_POST['id'] . ':' . $userInfo['id']);
                    echo json_encode(['id' => $_POST['id'], 'msg' => $hasNotify, 'msgt' => $msgt, 'msgo' => $msgo]);
                }
                else
                    echo json_encode(['id' => $_POST['id'], 'msg' => '', 'msgt' => $msgt, 'msgo' => $msgo]);
            }
            break;
        case 'lp-kp':
            if ($user->isLogin()) {
                $redis->setEx('notify:dialog:message:typing:' . $userInfo['id'] . ':' . $_POST['id'], 2, true);
            }
            break;
        case 'lp-online':
            if ($user->isLogin()) {
                $user->updateOnlineStatus();
            }
            break;
    }
    die;
}
if (isset($_GET['ajax'])) {
    if ($_GET['action'] == 'generate:signature') {
        echo $server->getFormSignature($_GET['account'], 'RUB', $_GET['desc'], $_GET['sum'], 'SECRET');
    }
    if ($_GET['action'] == 'get:location') {
        if (!$user->isLogin())
            return;
        echo $server->getLocationByQuery($_GET['q']);
    }
    if ($_GET['action'] == 'get:instagram:account') {
        if (!$user->isLogin()) {
            echo json_encode(['error' => 'Твой аккаунт не авторизован :(']);
            return;
        }

        $data = $user->getInstagramData($_GET['account']);

        if (empty($data)) {
            echo json_encode(['error' => 'Такого аккаунта не сущесвтует']);
            return;
        }

        if (strtoupper($data['graphql']['user']['is_private'])) {
            echo json_encode(['error' => 'Ваш аккаунт закрыт, необходимо его открыть на время экспорта']);
            return;
        }

        if (strtoupper($data['graphql']['user']['full_name']) != 'SLAFY' && strtoupper($data['graphql']['user']['biography']) != 'SLAFY') {
            $result['error'] = 'Аккаунт не прошел верификацию, укжите пожалуйста в вашем истаграме имя SLAFY согласно шагу №1';
            echo json_encode($result);
            return;
        }

        $image = file_get_contents($data['graphql']['user']['profile_pic_url_hd']);
        file_put_contents('upload/instagram/' . $_GET['account'] . '.jpg', $image);
        $result['login'] = $data['graphql']['user']['username'];
        echo json_encode($result);
    }
    if ($_GET['action'] == 'export:instagram:account') {
        if (!$user->isLogin()) {
            echo json_encode(['error' => 'Твой аккаунт не авторизован :(']);
            return;
        }
        $data = $user->getInstagramData($_GET['account']);

        if (empty($data) || strtoupper($data['graphql']['user']['full_name']) != 'SLAFY') {
            echo json_encode(['error' => 'Возможно что-то пошло не так, в чем мы сомневаемся, кажется ты попробовал подменить данные']);
            return;
        }

        $qb
            ->createQueryBuilder('instagram_export')
            ->insertSql(
                [
                    'user_id',
                    'account',
                    'account_id',
                    'is_story',
                    'is_feed',
                    'timestamp',
                ],
                [
                    $userInfo['id'],
                    $data['graphql']['user']['username'],
                    $data['graphql']['user']['id'],
                    $_GET['feed'] ? 1 : 0,
                    $_GET['story'] ? 1 : 0,
                    $server->timeStampNow()
                ]
            )
            ->executeQuery()
            ->getResult()
        ;

        echo json_encode(['message' => 'Мы начали экспорт данных, а пока можете продолжить сёрфить Slafy']);
    }
    die;
}