<?php

namespace Server\Manager;

use Server\Core\QueryBuilder;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Request
 */
class RequestManager
{
    protected $qb;

    /**
     * @param QueryBuilder $qb
     */
    public function checkRequests(QueryBuilder $qb) {
        $this->qb = $qb;

        global $server;
        global $methods;
        global $modal;
        global $userInfo;
        global $userInfoSession;
        global $user;

        if (isset($_COOKIE['user'])) {
            $userInfoSession = $user->getUserToken($_COOKIE['user']);
            if (empty($userInfoSession)) {
                $userInfo['id'] = -1;
                $user->logout();
            }
            else
                $userInfo = $user->getUserInfoById($userInfoSession['user_id']);
        }
        else
            $userInfo['id'] = -1;

        if(!empty($_POST)) {
            if (isset($_POST['act-login'])) {
                $userInfoTemp = $user->getUserInfoByLogin(strtolower($_POST['login']));
                $password = hash('sha256', $userInfoTemp['id'] . '|SLAFY|' . $_POST['pass']);

                setcookie('timeout', true, time() + 3, '/');

                if (isset($_SESSION['time']) && $_SESSION['time'] > time() || isset($_COOKIE['timeout'])) {
                    $user->showMessage('Таймаут на авторизацию 3 секунды');
                }
                else if (empty($userInfoTemp)) {
                    $user->showMessage('Не верно введен логин');
                    $_SESSION['time'] = time() + 3;
                }
                else {

                    if ($userInfoTemp['password'] == $password) {

                        $token = hash('sha256', $userInfoTemp['login'] . $userInfoTemp['id'] . $server->generateToken());

                        $ip = $server->getClientIp();
                        $ipInfo = json_decode(file_get_contents('http://ipinfo.io/' . $ip));

                        $qb
                            ->createQueryBuilder('log_login')
                            ->insertSql(['ip', 'city', 'country', 'timestamp', 'user_id', 'token'], [$ip, $ipInfo->city, $ipInfo->country, $server->timeStampNow(), $userInfoTemp['id'], $token])
                            ->executeQuery()
                            ->getSingleResult()
                        ;

                        $server->setCookie('user', $token);
                        header('Location: /feed');
                        return;

                    }
                    else {
                        $_SESSION['time'] = time() + 3;
                        $user->showMessage('Не верно введен пароль');
                    }
                }
            }

            if (isset($_POST['act-reg'])) {

                $email = $_POST['email'];
                if (empty($email)) {
                    $user->showMessage('Поле Email не должно быть пустым');
                    return;
                }
                if ($_POST['pass1'] != $_POST['pass2']) {
                    $user->showMessage('Пароли не совпадают');
                    return;
                }
                /*if (!isset($_POST['accept'])) {
                    $user->showMessage('Необходимо согласиться с политикой конфиденциальности');
                    return;
                }*/

                if (!empty($user->getUserInfoByEmail($email))) {
                    $user->showMessage('Email уже занят');
                    return;
                }

                global $defaultAvatarList;
                global $colors;

                $f_contents = file("client/eng.txt");
                $line1 = $f_contents[rand(0, count($f_contents) - 1)];
                $line2 = $f_contents[rand(0, count($f_contents) - 1)];

                $login = ucfirst($line1) . ucfirst($line2);
                $login = str_replace('`', '', $login);
                $login = str_replace('\'', '', $login);
                $login = str_replace('_', '', $login);
                $login = str_replace('-', '', $login);
                $login = str_replace('(', '', $login);
                $login = str_replace(')', '', $login);
                $login = str_replace("\n", '', $login);
                $login = strtolower($login);
                $token = hash('sha256', $login . time() . $server->generateToken());

                $qb
                    ->createQueryBuilder('users')
                    ->insertSql(['login', 'email', 'avatar', 'cl_bg', 'cl_btn', 'is_privacy_accept'], [$login, $email, $defaultAvatarList[rand(0, count($defaultAvatarList))], $colors[rand(0, count($colors))], $colors[rand(0, count($colors)) - 1], 1])
                    ->executeQuery()
                    ->getResult()
                ;

                $userInfoTemp = $user->getUserInfoByLogin($login);
                $user->setSubscribeByUserId($userInfoTemp['id'], 30);

                $ip = $server->getClientIp();
                $ipInfo = json_decode(file_get_contents('http://ipinfo.io/' . $ip));
                $qb
                    ->createQueryBuilder('log_login')
                    ->insertSql(['ip', 'city', 'country', 'timestamp', 'user_id', 'user_agent', 'token'], [$ip, $ipInfo->city, $ipInfo->country, $server->timeStampNow(), $userInfoTemp['id'], $_SERVER['HTTP_USER_AGENT'], $token])
                    ->executeQuery()
                    ->getSingleResult()
                ;


                $password = hash('sha256', $userInfoTemp['id'] . '|SLAFY|' . $_POST['pass1']);
                $qb
                    ->createQueryBuilder('users')
                    ->updateSql(['password'], [$password])
                    ->where('id = ' . $userInfoTemp['id'])
                    ->executeQuery()
                    ->getResult()
                ;

                $_SESSION['modal-show'] = true;
                $_SESSION['modal-msg'] = 'Ваш аккаунт был зарегистрирован';

                $server->setCookie('user', $token);

                header('Location: /@' . $login);
                return;
            }

            if (isset($_POST['upload-user-avatar'])) {
                if ($user->isLogin()) {
                    $files = new \Server\Files();
                    $result = $files->uploadUserAvatar();
                    if (isset($result['error']))
                        $user->showMessage($result['error']);
                    else {
                        $img = reset($result['success']['files']);
                        $files->deleteFile('/upload/user/' . $userInfo['id'] . '/' . $userInfo['avatar']);
                        if ($files->getFileFormat($userInfo['avatar']) === 'gif')
                            $files->deleteFile('/upload/user/' . $userInfo['id'] . '/' . $files->getFileNameWithoutFormat($userInfo['avatar']) . '.jpg');
                        $qb
                            ->createQueryBuilder('users')
                            ->updateSql(['avatar'], [$img])
                            ->where('id = ' . $userInfo['id'])
                            ->executeQuery()
                            ->getResult()
                        ;
                        $user->showMessage('Аватар был обновлён ;)');
                    }
                }
            }

            if (isset($_POST['upload-user-background'])) {
                if ($user->isLogin() && $user->isSubscribe()) {
                    $files = new \Server\Files();
                    $result = $files->uploadUserAvatar();
                    if (isset($result['error']))
                        $user->showMessage($result['error']);
                    else {
                        $img = reset($result['success']['files']);
                        $files->deleteFile('/upload/user/' . $userInfo['id'] . '/' . $userInfo['background']);
                        if ($files->getFileFormat($userInfo['background']) === 'gif')
                            $files->deleteFile('/upload/user/' . $userInfo['id'] . '/' . $files->getFileNameWithoutFormat($userInfo['background']) . '.jpg');
                        $qb
                            ->createQueryBuilder('users')
                            ->updateSql(['background'], [$img])
                            ->where('id = ' . $userInfo['id'])
                            ->executeQuery()
                            ->getResult()
                        ;
                        $user->showMessage('Фон был обновлён <3');
                    }
                }
            }

            if (isset($_POST['feed-publish'])) {
                if ($user->isLogin()) {
                    $result = $user->publishFeed(
                        $_POST['id'],
                        $_POST['content'],
                        $_POST['geo'],
                        isset($_POST['only-friend']),
                        isset($_POST['disable-comment']),
                        isset($_POST['disable-like'])
                    );
                    if ($result)
                        $user->showMessage('Пост был опубликован');
                    else
                        $user->showMessage('Ошибка публикации поста');

                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['ad-publish-work'])) {
                if ($user->isLogin()) {
                    $result = $user->createAdWork(
                        $_POST['title'],
                        '',
                        $_POST['content'],
                        $_POST['city'],
                        $_POST['tag'],
                        $_POST['english'],
                        isset($_POST['type'])
                    );
                    if ($result)
                        $user->showMessage('Ваше объявление было опубликовано');
                    else
                        $user->showMessage('Ошибка публикации объявления, нельзя публиковать чаще чем 1 раз в час');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['ad-publish-service'])) {
                if ($user->isLogin()) {
                    $result = $user->createAdService(
                        $_POST['title'],
                        '',
                        $_POST['content'],
                        $_POST['city'],
                        $_POST['tag'],
                        $_POST['english'],
                        isset($_POST['type']),
                    );
                    if ($result)
                        $user->showMessage('Ваше объявление было опубликовано');
                    else
                        $user->showMessage('Ошибка публикации объявления, нельзя публиковать чаще чем 1 раз в час');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['ad-publish-apartment'])) {
                if ($user->isLogin()) {
                    $result = $user->publishAdApartnemt(
                        $_POST['id'],
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['city'],
                        $_POST['tag'],
                        $_POST['price'],
                        $_POST['atype']
                    );
                    if ($result)
                        $user->showMessage('Ваше объявление было опубликовано');
                    else
                        $user->showMessage('Ошибка публикации объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['ad-publish-other'])) {
                if ($user->isLogin()) {
                    $result = $user->publishAdOther(
                        $_POST['id'],
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['city'],
                        $_POST['tag'],
                        $_POST['price'],
                        $_POST['type'],
                        isset($_POST['buy'])
                    );
                    if ($result)
                        $user->showMessage('Ваше объявление было опубликовано');
                    else
                        $user->showMessage('Ошибка публикации объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['delete-ad-work'])) {
                if ($user->isLogin()) {
                    $result = $user->deleteAdWork($_POST['id']);
                    if ($result)
                        $user->showMessage('Ваше объявление было удалено');
                    else
                        $user->showMessage('Ошибка удаления объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['delete-ad-service'])) {
                if ($user->isLogin()) {
                    $result = $user->deleteAdService($_POST['id']);
                    if ($result)
                        $user->showMessage('Ваше объявление было удалено');
                    else
                        $user->showMessage('Ошибка удаления объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['delete-ad-apartment'])) {
                if ($user->isLogin()) {
                    $result = $user->deleteAdApartment($_POST['id']);
                    if ($result)
                        $user->showMessage('Ваше объявление было удалено');
                    else
                        $user->showMessage('Ошибка удаления объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (isset($_POST['delete-ad-other'])) {
                if ($user->isLogin()) {
                    $result = $user->deleteAdOther($_POST['id']);
                    if ($result)
                        $user->showMessage('Ваше объявление было удалено');
                    else
                        $user->showMessage('Ошибка удаления объявления');
                }
                else {
                    $user->showMessage('Аккаунт не авторизован или недостаточно прав');
                }
            }

            if (!isset($_POST['nonclear'])) {
                $_SESSION['modal-show'] = $modal['show'];
                $_SESSION['modal-msg'] = $modal['text'];

                header("Cache-Control: no-store,no-cache,mustrevalidate");
                header("Location: " . $_SERVER['REQUEST_URI']);
                die;
            }
        }
        return true;
    }
}