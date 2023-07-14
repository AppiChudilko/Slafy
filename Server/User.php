<?php

namespace Server;

use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * User
 */
class User
{

    protected $qb;
    protected $valid;
    protected $server;
    protected $cookieInstagram = 'Cookie: csrftoken=Q9uh1TUPvVkFpBJszPLPxuH5AYtFQIvT; ds_user_id=57610962640; rur="EAG\05457610962640\0541704055345:01f797d3de81398e90e3ca72af69b8df5ac5ed299c2e6b1eb75011ddc4aaf0ce9fd0d061"; dpr=2; sessionid=57610962640%3AWTp7mFlOUlAmSA%3A29%3AAYcrQ4aVYb_1WsluMnWKOdgQdp5LDoYBi4inBH8WQA; shbid="15975\0541966960230\0541704055260:01f7a93cf5aaf0b859b7fd795f6a687ed0f8378edc03c673cb8e12f0327ca3be3178cca5"; shbts="1672519260\0541966960230\0541704055260:01f76ec500673a3103428064256f4bc6e4a6179b8ed71a6016f435a25f6d2f6ad374b85b"; ig_did=FEF8403F-08CE-43A8-8FBF-810BBF884758; mid=Yka6DgAEAAG2oWqxGGn---P45j2a';

    function __construct(QueryBuilder $qb, $check = null, $param = null)
    {
        $this->qb = $qb;
        $this->server = new Server($qb);
    }

    public function logout() {
        setcookie('user', '', time()-3600, '/');
        header( "refresh:0; url=/");
    }

    public function getUserCookie($name) {
        return $_COOKIE[$name];
    }

    public function isLogin() {
        if (isset($_COOKIE['user'])) {
            global $userInfo;
            if(!empty($userInfo) && isset($userInfo['id']) && $userInfo['id'] > 0)
                return true;
        }
        return false;
    }

    public function isAdmin($adminLevel = 1) {
        if ($this->isLogin()) {
            global $userInfo;
            if($userInfo['admin_level'] >= $adminLevel)
                return true;
        }
        return false;
    }

    public function isSubscribe(): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            if($userInfo['subscribe'] > $server->timeStampNow())
                return true;
        }
        return false;
    }

    public function setSubscribe($days = 1): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            $this->notify($userInfo['id'], 3, 99, $days);
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['subscribe'], [$server->timeStampNow() + ($days * 86400)])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setSubscribeByUserId($uId, $days = 1): bool
    {
        global $server;
        $this->notify($uId, 3, 99, $days);
        return $this->qb
            ->createQueryBuilder('users')
            ->updateSql(['subscribe'], [$server->timeStampNow() + ($days * 86400)])
            ->where('id = ' . $uId)
            ->executeQuery()
            ->getResult()
        ;
    }

    public function isActivate(): bool
    {
        return true;
        /*
        if ($this->isLogin()) {
            global $userInfo;
            if($userInfo['is_active'])
                return true;
        }
        return false;*/
    }

    public function updateOnlineStatus(): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['online_status'], [$server->timeStampNow()])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setPublicProfile($val): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['is_public'], [$val ? 1 : 0])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setPublicOnline($val): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['online_hidden'], [$val ? 0 : 1])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setShowFollowers($val): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['is_show_followers'], [$val ? 1 : 0])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setShowFollows($val): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['is_show_followers'], [$val ? 1 : 0])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setShowProfileFeed($val): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['is_show_feed'], [$val ? 1 : 0])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setName($name): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['name'], [$name])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setLogin($login): string
    {
        if ($this->isLogin()) {
            global $userInfo;

            $login = strtolower($login);

            if (!empty($this->getUserInfoByLogin($login)))
                return 'Логин уже занят';

            if (preg_match_all('/[a-zA-Zа-яА-Я0-9_.-]/iu', $login) != strlen($login))
                return 'Разрешены только русские и английские буквы, цифры, точка и тире';

            if (strlen($login) < 5 && !$this->isSubscribe())
                return 'Чтобы установить логин короче 5 символов, необходимо купить подписку Premium';
            if (strlen($login) < 5)
                return 'По подарочной подписке нельзя установить логин короче 5 символов';
            if (strlen($login) > 60)
                return 'Cлишком длинный у тебя ник, давай не больше 60 символов';

            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['login'], [$login])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
            $_SESSION['modal-show'] = true;
            $_SESSION['modal-msg'] = 'Логин был обновлен';
            return 'refresh';
        }
        return 'Аккаунт не авторизован';
    }

    public function setEmail($email): string
    {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;

            $email = strtolower($email);

            $userEmailInfo = $this->getUserInfoByEmail($email);
            if (!empty($userEmailInfo) && $userEmailInfo['id'] != $userInfo['id'])
                return 'Email уже занят';

            if (strlen($email) > 100)
                return 'Чета слишком длинный у тебя Email, давай не больше 100 символов';

            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['email'], [$email])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;

            $server->sendEmail($email, 'https://adaptation-usa.com/settings?token=' . hash('sha256', $userInfo['id'] . $email));
            return 'Email был обновлен';
        }
        return 'Аккаунт не авторизован';
    }

    public function setPassword($pass1, $pass2): string
    {
        if ($this->isLogin()) {
            global $userInfo;

            $pass1 = hash('sha256', $userInfo['id'] . '|SLAFY|' . $pass1);
            $pass2 = hash('sha256', $userInfo['id'] . '|SLAFY|' . $pass2);

            if ($pass1 != $pass2)
                return 'Пароли не совпадают';

            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['password'], [$pass1])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;

            return 'Пароль был обновлен';
        }
        return 'Аккаунт не авторизован';
    }

    public function setActivate(): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['is_active'], [1])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setWebSite($content): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            $content = str_replace('https://', '', $content);
            $content = str_replace('http://', '', $content);
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['website'], [$content])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setColor($bg, $btn): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['cl_bg', 'cl_btn'], [$bg, $btn])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setAbout($content): bool
    {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['about'], [$content])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function notify($uId, $uIdAction, $type, $feed = 0) {
        /*
        Types
        0 - Follow
        1 - Unfollow
        2 - AcceptFollow
        3 - Comment
        4 - LikeComment
        5 - LikePost
        6 - LikeStory
        7 - ReplyComment
        99 - GiveSubscribe
        */

        if ($uIdAction == $uId)
            return false;

        global $server;
        global $redis;

        $uId = intval($uId);
        $uIdAction = intval($uIdAction);

        $hasNotify = $redis->get('notify:' . $uId);
        if ($hasNotify)
            $redis->setEx('notify:' . $uId, 300, $hasNotify + 1);
        else
            $redis->setEx('notify:' . $uId, 300, 1);

        return $this->qb
            ->createQueryBuilder('notify')
            ->insertSql(
                ['user_id', 'user_id_action', 'type', 'feed', 'timestamp'],
                [$uId, $uIdAction, $type, $feed, $server->timeStampNow()]
            )
            ->executeQuery()
            ->getResult()
        ;
    }

    public function followUser($id, $followersCount, $isRequest = false) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            $this->qb
                ->createQueryBuilder('users_follow')
                ->insertSql(
                    ['user_from', 'user_to', 'is_request', 'timestamp'],
                    [$userInfo['id'], $id, $isRequest ? 1 : 0, $server->timeStampNow()]
                )
                ->executeQuery()
                ->getResult()
            ;
            if (!$isRequest) {
                $this->qb
                    ->createQueryBuilder('users')
                    ->updateSql(['count_followers'],[++$followersCount])
                    ->where('id = ' . $id)
                    ->executeQuery()
                    ->getResult()
                ;
                $this->qb
                    ->createQueryBuilder('users')
                    ->updateSql(['count_follows'],[++$userInfo['count_follows']])
                    ->where('id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getResult()
                ;
            }
        }
        return false;
    }

    public function hasFollowUser($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $hasFollow = $this->qb
                ->createQueryBuilder('users_follow')
                ->selectSql()
                ->where('user_from = ' . $userInfo['id'])
                ->andWhere('user_to = ' . $id)
                ->andWhere('is_request = 0')
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
            return !empty($hasFollow);
        }
        return false;
    }

    public function followAccept($id, $uid) {
        if ($this->isLogin()) {
            global $userInfo;

            $id = intval($id);
            $uid = intval($uid);

            $userInfoFollow = $this->getUserInfoById($uid);
            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['count_follows'],[++$userInfoFollow['count_follows']])
                ->where('id = ' . $userInfoFollow['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['count_followers'],[++$userInfo['count_followers']])
                ->where('id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;

            return $this->qb
                ->createQueryBuilder('users_follow')
                ->updateSql(['is_request'],[0])
                ->where('id = ' . $id)
                ->andWhere('user_to = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function followDecline($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $id = intval($id);
            return $this->qb
                ->createQueryBuilder('users_follow')
                ->deleteSql()
                ->where('id = ' . $id)
                ->andWhere('user_to = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function getFollowInfo($id) {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users_follow')
                ->selectSql()
                ->where('user_from = ' . $userInfo['id'])
                ->andWhere('user_to = ' . $id)
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return false;
    }

    public function isFollowMe($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $result = $this->qb
                ->createQueryBuilder('users_follow')
                ->selectSql()
                ->where('user_from = ' . $id)
                ->andWhere('user_to = ' . $userInfo['id'])
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
            return !empty($result);
        }
        return false;
    }

    public function isFollow($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $result = $this->qb
                ->createQueryBuilder('users_follow')
                ->selectSql()
                ->where('user_to = ' . $id)
                ->andWhere('user_from = ' . $userInfo['id'])
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
            return !empty($result);
        }
        return false;
    }

    public function unfollowUser($id, $followersCount) {
        if ($this->isLogin()) {
            global $userInfo;
            $return = 1;
            if (!empty($this->hasFollowUser($id))) {
                $this->qb
                    ->createQueryBuilder('users')
                    ->updateSql(['count_followers'],[--$followersCount])
                    ->where('id = ' . $id)
                    ->executeQuery()
                    ->getResult()
                ;
                $this->qb
                    ->createQueryBuilder('users')
                    ->updateSql(['count_follows'],[--$userInfo['count_follows']])
                    ->where('id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getResult()
                ;
                $return = 2;
            }

            $this->qb
                ->createQueryBuilder('users_follow')
                ->deleteSql()
                ->where('user_from = ' . $userInfo['id'])
                ->andWhere('user_to = ' . $id)
                ->executeQuery()
                ->getResult()
            ;
            return $return;
        }
        return 0;
    }

    public function getFollowUserList($colums = 'user_to') {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('users_follow')
                ->selectSql($colums)
                ->where('user_from = ' . $userInfo['id'])
                ->andWhere('is_request = 0')
                ->executeQuery()
                ->getResult()
            ;
        }
        return [];
    }

    public function createFeed($imgList) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            $imgListJson = json_encode($imgList);
            $this->qb
                ->createQueryBuilder('feed')
                ->insertSql(
                    [
                        'user_id',
                        'img',
                        'hash',
                    ],
                    [
                        $userInfo['id'],
                        $imgListJson,
                        $server->getHash(md5(time() . $userInfo['id'] . rand(-1000, 1000))),
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;

            $result = $this->qb
                ->createQueryBuilder('feed')
                ->selectSql('id')
                ->where('user_id = ' . $userInfo['id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
            return reset($result);
        }
        return 0;
    }

    public function publishFeed($id, $content, $location, $isFriend, $isDisableComment, $isDisableLike) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('feed')
                ->updateSql(
                    [
                        'content',
                        'location',
                        'is_friend',
                        'is_disable_comment',
                        'is_disable_likes',
                        'is_draft',
                        'timestamp',
                    ],
                    [
                        $server->charsString($content),
                        $server->charsString($location),
                        $isFriend ? 1 : 0,
                        $isDisableComment ? 1 : 0,
                        $isDisableLike ? 1 : 0,
                        0,
                        $server->timeStampNow()
                    ]
                )
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function feedDelete($id) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;

            $feed = new Feed();
            $files = new Files();
            $feedItem = $feed->getFeedByHash($id);
            if ($feedItem['user_id'] == $userInfo['id']) {

                $imgList = json_decode(htmlspecialchars_decode($feedItem['img']));
                foreach ($imgList as $img)
                    $files->deleteFile('/upload/feed/' . $userInfo['id'] . '/' . $img);

                return $this->qb
                    ->createQueryBuilder('feed')
                    ->deleteSql()
                    ->where('hash = \'' . $server->charsString($id) . '\'')
                    ->andWhere('user_id = ' . $userInfo['id'])
                    ->orderBy('id DESC')
                    ->limit(1)
                    ->executeQuery()
                    ->getResult()
                ;
            }
            return false;
        }
        return false;
    }

    public function feedComment($feedItem, $replyId, $text) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            $replyId = intval($replyId);
            if ($replyId > 0)
                $this->notify($replyId, $userInfo['id'], 7, $feedItem['id']);
            else
                $this->notify($feedItem['user_id'], $userInfo['id'], 3, $feedItem['id']);
            $this->qb
                ->createQueryBuilder('feed')
                ->updateSql(['comments'], [++$feedItem['comments']])
                ->where('id = ' . $feedItem['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_comments')
                ->insertSql(
                    ['feed_id', 'user_id', 'reply_id', 'text', 'timestamp'],
                    [$feedItem['id'], $userInfo['id'], $replyId, $text, $server->timeStampNow()])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function feedCommentDelete($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $this->qb
                ->createQueryBuilder('feed_comments')
                ->deleteSql()
                ->where('id = \'' . intval($id) . '\'')
                ->andWhere('user_id = \'' . $userInfo['id'] . '\'')
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_comments_likes')
                ->deleteSql()
                ->where('comment_id = \'' . intval($id) . '\'')
                ->executeQuery()
                ->getResult()
            ;
            return true;
        }
        return false;
    }

    public function feedCommentIsLike($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $result = $this->qb
                ->createQueryBuilder('feed_comments_likes')
                ->selectSql()
                ->where('comment_id = \'' . intval($id) . '\'')
                ->andWhere('user_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
            return !empty($result);
        }
        return false;
    }

    public function feedCommentLike($commentItem) {
        if ($this->isLogin()) {
            global $userInfo;
            $this->qb
                ->createQueryBuilder('feed_comments')
                ->updateSql(['likes'], [++$commentItem['likes']])
                ->where('id = ' . $commentItem['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_comments_likes')
                ->insertSql(['comment_id', 'user_id'], [$commentItem['id'], $userInfo['id']])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function feedCommentUnLike($commentItem) { //Кто будет читать этот код, це пиздец, извините, но мне так лень переписывать DataBase.php
        if ($this->isLogin()) {
            global $userInfo;
            $this->qb
                ->createQueryBuilder('feed_comments')
                ->updateSql(['likes'], [--$commentItem['likes']])
                ->where('id = ' . $commentItem['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_comments_likes')
                ->deleteSql()
                ->where('comment_id = ' . $commentItem['id'])
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function feedIsLike($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $result = $this->qb
                ->createQueryBuilder('feed_likes')
                ->selectSql()
                ->where('feed_id = \'' . intval($id) . '\'')
                ->andWhere('user_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
            return !empty($result);
        }
        return false;
    }

    public function feedLike($feedItem) { //Кто будет читать этот код, це пиздец, извините, но мне так лень переписывать DataBase.php
        if ($this->isLogin()) {
            global $userInfo;
            $this->notify($feedItem['user_id'], $userInfo['id'], 5, $feedItem['id']);
            $this->qb
                ->createQueryBuilder('feed')
                ->updateSql(['likes'], [++$feedItem['likes']])
                ->where('id = ' . $feedItem['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_likes')
                ->insertSql(['feed_id', 'user_id'], [$feedItem['id'], $userInfo['id']])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function feedUnLike($feedItem) { //Кто будет читать этот код, це пиздец, извините, но мне так лень переписывать DataBase.php
        if ($this->isLogin()) {
            global $userInfo;
            $this->qb
                ->createQueryBuilder('feed')
                ->updateSql(['likes'], [--$feedItem['likes']])
                ->where('id = ' . $feedItem['id'])
                ->executeQuery()
                ->getResult()
            ;
            $this->qb
                ->createQueryBuilder('feed_likes')
                ->deleteSql()
                ->where('feed_id = ' . $feedItem['id'])
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function getDialogList($offset = 0, $limit = 15, $type = -1) { //TODO мб доделать leftjoin user
        if ($this->isLogin()) {
            global $userInfo;
            $limitOffset = $offset ? $offset * $limit : $limit;
            $typeSql = 'dialog_settings.type >= 0';
            if ($type == -1)
                $typeSql = '(dialog_settings.type = 0 OR dialog_settings.type = 1)';
            else if ($type >= 0)
                $typeSql = 'dialog_settings.type >= ' . intval($type);
            return $this->qb
                ->createQueryBuilder('dialog_list')
                ->selectSql('dialog_list.*, dialog_settings.type')
                ->where('(uid1 = ' . $userInfo['id'] . ' OR uid2 = ' . $userInfo['id'] . ')')
                ->andWhere($typeSql)
                ->orderBy('dialog_settings.type DESC, last_update DESC')
                ->rightJoin('dialog_settings ON dialog_list.id = dialog_settings.dialog_id AND dialog_settings.user_id = ' . $userInfo['id'])
                ->limit(($limitOffset - $limit) . ', ' . $limit)
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function getDialogById($id) {
        if ($this->isLogin()) {
            return $this->qb
                ->createQueryBuilder('dialog_list')
                ->selectSql()
                ->where('id = ' . $id)
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return [];
    }

    public function hasDialogById($id) {
        return !empty($this->getDialogById($id));
    }

    public function getDialogByUserId($id) {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('dialog_list')
                ->selectSql()
                ->where('(uid1 = ' . $id . ' AND uid2 = ' . $userInfo['id'] . ')')
                ->orWhere('(uid2 = ' . $id . ' AND uid1 = ' . $userInfo['id'] . ')')
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return [];
    }

    public function hasDialogByUserId($id) {
        return !empty($this->getDialogByUserId($id));
    }

    public function createDialog($id, $isRequest = true) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            $this->qb
                ->createQueryBuilder('dialog_list')
                ->insertSql(['uid1', 'uid2', 'last_update'], [$userInfo['id'], $id, $server->timeStampNow()])
                ->executeQuery()
                ->getResult()
            ;

            $dialogItem = $this->getDialogByUserId($id);

            $this->qb
                ->createQueryBuilder('dialog_settings')
                ->insertSql(['dialog_id', 'user_id', 'type'], [$dialogItem['id'], $userInfo['id'], 0])
                ->executeQuery()
                ->getResult()
            ;

            if ($userInfo['id'] != $id)
            {
                $this->qb
                    ->createQueryBuilder('dialog_settings')
                    ->insertSql(['dialog_id', 'user_id', 'type'], [$dialogItem['id'], $id, $isRequest ? 99 : 0])
                    ->executeQuery()
                    ->getResult()
                ;
            }
            return $dialogItem;
        }
        return 0;
    }

    public function deleteDialog($id, $isRequest = true) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            $this->qb
                ->createQueryBuilder('dialog_list')
                ->deleteSql()
                ->where('id = ' . $id)
                ->executeQuery()
                ->getResult()
            ;

            $this->qb
                ->createQueryBuilder('dialog_settings')
                ->deleteSql()
                ->where('dialog_id = ' . $id)
                ->executeQuery()
                ->getResult()
            ;

            return $this->qb
                ->createQueryBuilder('dialog')
                ->deleteSql()
                ->where('dialog_id = ' . $id)
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function updateDialogTimestamp($id) {
        global $server;
        $this->qb
            ->createQueryBuilder('dialog_list')
            ->updateSql(['last_update'], [$server->timeStampNow()])
            ->where('id = ' . $id)
            ->executeQuery()
            ->getResult()
        ;
    }

    public function updateDialogSettingsType($id, $type) {
        global $userInfo;
        $this->qb
            ->createQueryBuilder('dialog_settings')
            ->updateSql(['type'], [intval($type)])
            ->where('user_id = ' . $userInfo['id'])
            ->andWhere('dialog_id = ' . intval($id))
            ->executeQuery()
            ->getResult()
        ;
    }

    public function updateDialogRead($id, $userId) {
        $this->qb
            ->createQueryBuilder('dialog')
            ->updateSql(['is_read'], [1])
            ->where('dialog_id = ' . $id)
            ->andWhere('user_id = ' . $userId)
            ->andWhere('is_read = 0')
            ->executeQuery()
            ->getResult()
        ;
    }

    public function sendDialogMessage($id, $text, $replyId = 0, $stickerId = 0) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            $this->updateDialogTimestamp($id);
            return $this->qb
                ->createQueryBuilder('dialog')
                ->insertSql(
                    [
                        'dialog_id',
                        'user_id',
                        'reply_id',
                        'sticker_id',
                        'text',
                        'timestamp',
                    ],
                    [
                        $id,
                        $userInfo['id'],
                        $replyId,
                        $stickerId,
                        $text,
                        $server->timeStampNow()
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function setDialogMessageReaction($id, $reaction) {
        if ($this->isLogin()) {
            return $this->qb
                ->createQueryBuilder('dialog')
                ->updateSql(['reaction'], [$reaction])
                ->where('id = ' . intval($id))
                ->executeQuery()
                ->getResult()
            ;
        }
        return [];
    }

    public function getDialogMessages($id, $offset = 0, $limit = 20, $isReverse = false) {
        if ($this->isLogin()) {
            $limitFrom = $offset ? $offset * $limit : $limit;
            $result = $this->qb
                ->createQueryBuilder('dialog')
                ->selectSql()
                ->where('dialog_id = ' . intval($id))
                ->limit(($limitFrom - $limit) . ', ' . $limit)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getResult()
            ;
            if (!empty($result) && $isReverse)
                return array_reverse($result);
            return $result;
        }
        return [];
    }

    public function getDialogMessage($id) {
        if ($this->isLogin()) {
            return $this->qb
                ->createQueryBuilder('dialog')
                ->selectSql()
                ->where('id = ' . intval($id))
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return [];
    }

    public function getDialogLastMessage($id) {
        if ($this->isLogin()) {
            return $this->qb
                ->createQueryBuilder('dialog')
                ->selectSql()
                ->where('dialog_id = ' . intval($id))
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return 0;
    }

    public function getDialogUnRead($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $result = $this->qb
                ->createQueryBuilder('dialog')
                ->selectSql('COUNT(*)')
                ->where('dialog_id = ' . intval($id))
                ->andWhere('is_read = 0')
                ->andWhere('user_id != ' . $userInfo['id'])
                ->limit(100)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
            return reset($result);
        }
        return 0;
    }

    public function getDialogLastMyMessage($id) {
        if ($this->isLogin()) {
            global $userInfo;
            return $this->qb
                ->createQueryBuilder('dialog')
                ->selectSql()
                ->where('dialog_id = ' . intval($id))
                ->where('user_id = ' . $userInfo['id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return 0;
    }

    public function getInstagramData($nickname) {
        $nickname = str_replace('@', '', $nickname);
        header('Content-Type: application/json; charset=utf-8');
        $headers[] = $this->cookieInstagram;
        $ch = curl_init('https://www.instagram.com/' . $nickname . '/?__a=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getInstagramFeed($id, $queryHash) {
        header('Content-Type: application/json; charset=utf-8');
        $headers[] = $this->cookieInstagram;
        $ch = curl_init('https://www.instagram.com/graphql/query/?query_hash=396983faee97f4b49ccbe105b4daf7a0&variables=%7B%22id%22%3A%22' . $id . '%22%2C%22first%22%3A12%2C%22after%22%3A%22' . $queryHash . '%22%7D');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getInstagramHighlights($id) {
        header('Content-Type: application/json; charset=utf-8');
        $headers[] = $this->cookieInstagram;
        $ch = curl_init('https://i.instagram.com/api/v1/highlights/' . $id . '/highlights_tray/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getInstagramHighlightId($id) {
        header('Content-Type: application/json; charset=utf-8');
        $headers[] = $this->cookieInstagram;
        $ch = curl_init('https://i.instagram.com/api/v1/feed/reels_media/?reel_ids=' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserAvatar($id, $avatar) {
        global $defaultAvatarList;
        if ($avatar == '')
            $avatar = '404';
        if (in_array($avatar, $defaultAvatarList))
            return IMAGE_CDN_PATH . '/client/images/stickers/512/' . $avatar . '.png';
        else {
            $files = new Files();
            if ($files->getFileFormat($avatar) === 'gif')
                return IMAGE_CDN_PATH . '/upload/user/' . $id . '/' . $files->getFileNameWithoutFormat($avatar) . '.jpg';
            return IMAGE_CDN_PATH . '/upload/user/' . $id . '/' . $avatar;
        }
    }

    public function getUserAvatarGif($id, $avatar) {
        global $defaultAvatarList;
        if ($avatar == '')
            $avatar = '404';
        if (in_array($avatar, $defaultAvatarList))
            return IMAGE_CDN_PATH . '/client/images/stickers/512/' . $avatar . '.png';
        else
            return IMAGE_CDN_PATH . '/upload/user/' . $id . '/' . $avatar;
    }

    public function getUserName($login, $name) {
        if (trim($name) == '')
            return $login;
        else
            return $name;
    }

    public function getUserInfoById($where) {
        if($where == '' || empty($where) || is_null($where)) return [];
        return $this->qb
            ->createQueryBuilder('users')
            ->selectSql()
            ->where('id = \'' . intval($where) . '\'')
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getUserInfoByLogin($where) {

        global $server;

        if($where == '' || empty($where) || is_null($where)) return false;

        $where = $server->charsString($where);

        return $this->qb
            ->createQueryBuilder('users')
            ->selectSql()
            ->where('login = \'' . $where . '\'')
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getUserInfoByEmail($where) {
        global $server;
        if($where == '' || empty($where) || is_null($where)) return false;
        $where = $server->charsString($where);
        return $this->qb
            ->createQueryBuilder('users')
            ->selectSql()
            ->where('email = \'' . $where . '\'')
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getUserToken($where) {
        global $server;
        if($where == '' || empty($where) || is_null($where)) return false;
        if (!is_numeric($where)) {
            return $this->qb
                ->createQueryBuilder('log_login')
                ->selectSql()
                ->where('token = \'' . $server->charsString($where) . '\'')
                ->limit(1)
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        return $this->qb
            ->createQueryBuilder('log_login')
            ->selectSql()
            ->where('user_id = \'' . intval($where) . '\'')
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getLoginLog($limit = 5) {
        if ($this->isLogin()) {
            global $userInfo;
            return $this
                ->qb
                ->createQueryBuilder('log_login')
                ->selectSql()
                ->where('user_id = \'' . $userInfo['id'] . '\'')
                ->limit($limit)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getResult()
            ;
        }
        return [];

    }

    public function createAdWork($title, $textDesc, $textMain, $city, $tag, $englishId, $isSearch) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;

            $adItem = $this->getLastAdWorkByUserId();
            if (isset($adItem['timestamp']) && $adItem['timestamp'] > ($server->timeStampNow() - 3600))
                return false;

            return $this->qb
                ->createQueryBuilder('ad_work')
                ->insertSql(
                    [
                        'title',
                        'text_desc',
                        'text_main',
                        'city',
                        'tag',
                        'english_id',
                        'is_search',
                        'user_id',
                        'timestamp'
                    ],
                    [
                        $server->charsString($title),
                        $server->charsString($textDesc),
                        $server->charsString($textMain),
                        intval($city),
                        $server->charsString($tag),
                        intval($englishId),
                        $isSearch ? 1 : 0,
                        $userInfo['id'],
                        $server->timeStampNow()
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function getAdWorkList($offset = 0, $limit = 50, $groupUser = true, $city = -1, $en = -1, $work = -1, $q = '') {
        $limitFrom = $offset ? $offset * $limit : $limit;
        if ($groupUser) {
            $query = $this->qb
                ->createQueryBuilder('ad_work')
                ->selectSql('*, ad_work.id as wid')
                ->limit(($limitFrom - $limit) . ', ' . $limit)
                ->orderBy('ad_work.id DESC')
                ->leftJoin('users ON ad_work.user_id = users.id')
                ->where('ad_work.id > 0') //TODO TIMESTAMP
            ;

            if ($city >= 0)
                $query->andWhere('city = ' . intval($city));
            if ($en >= 0)
                $query->andWhere('english_id = ' . intval($en));
            if ($work >= 0)
                $query->andWhere('is_search = ' . intval($work));
            if (!empty($q)) {
                global $server;
                $q = $server->charsString($q);
                $query->andWhere('(text_main LIKE \'%' . $q . '%\' OR tag = \'' . $q . '\')');
            }

            return $query->executeQuery()->getResult();
        }
        return $this->qb
            ->createQueryBuilder('ad_work')
            ->selectSql()
            ->limit(($limitFrom - $limit) . ', ' . $limit)
            ->orderBy('id DESC')
            ->executeQuery()
            ->getResult()
            ;
    }

    public function getLastAdWorkByUserId() {
        global $userInfo;
        global $server;
        return $this->qb
            ->createQueryBuilder('ad_work')
            ->selectSql()
            ->where('user_id = ' . $userInfo['id'])
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
            ;
    }

    public function deleteAdWork($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $id = intval($id);
            return $this->qb
                ->createQueryBuilder('ad_work')
                ->deleteSql()
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function createAdService($title, $textDesc, $textMain, $city, $tag, $englishId, $isSearch) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;

            $adItem = $this->getLastAdSerivceByUserId();
            if (isset($adItem['timestamp']) && $adItem['timestamp'] > ($server->timeStampNow() - 3600))
                return false;

            return $this->qb
                ->createQueryBuilder('ad_service')
                ->insertSql(
                    [
                        'title',
                        'text_desc',
                        'text_main',
                        'city',
                        'tag',
                        'english_id',
                        'is_search',
                        'user_id',
                        'timestamp'
                    ],
                    [
                        $server->charsString($title),
                        $server->charsString($textDesc),
                        $server->charsString($textMain),
                        intval($city),
                        $server->charsString($tag),
                        intval($englishId),
                        $isSearch ? 1 : 0,
                        $userInfo['id'],
                        $server->timeStampNow()
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function getAdServiceList($offset = 0, $limit = 50, $groupUser = true, $city = -1, $en = -1, $work = -1, $q = '') {
        $limitFrom = $offset ? $offset * $limit : $limit;
        if ($groupUser) {
            $query = $this->qb
                ->createQueryBuilder('ad_service')
                ->selectSql('*, ad_service.id as sid')
                ->limit(($limitFrom - $limit) . ', ' . $limit)
                ->orderBy('ad_service.id DESC')
                ->leftJoin('users ON ad_service.user_id = users.id')
                ->where('ad_service.id > 0') //TODO TIMESTAMP
            ;

            if ($city >= 0)
                $query->andWhere('city = ' . intval($city));
            if ($en >= 0)
                $query->andWhere('english_id = ' . intval($en));
            if ($work >= 0)
                $query->andWhere('is_search = ' . intval($work));
            if (!empty($q)) {
                global $server;
                $q = $server->charsString($q);
                $query->andWhere('(text_main LIKE \'%' . $q . '%\' OR tag = \'' . $q . '\')');
            }
            return $query->executeQuery()->getResult();
        }
        return $this->qb
            ->createQueryBuilder('ad_service')
            ->selectSql()
            ->limit(($limitFrom - $limit) . ', ' . $limit)
            ->orderBy('id DESC')
            ->executeQuery()
            ->getResult()
            ;
    }

    public function getLastAdSerivceByUserId() {
        global $userInfo;
        global $server;
        return $this->qb
            ->createQueryBuilder('ad_service')
            ->selectSql()
            ->where('user_id = ' . $userInfo['id'])
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function deleteAdService($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $id = intval($id);
            return $this->qb
                ->createQueryBuilder('ad_service')
                ->deleteSql()
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
                ;
        }
        return false;
    }

    public function createAdApartment($imgList) {
        if ($this->isLogin()) {
            global $userInfo;
            global $server;
            $imgListJson = json_encode($imgList);
            $this->qb
                ->createQueryBuilder('ad_apartment')
                ->insertSql(
                    [
                        'user_id',
                        'img'
                    ],
                    [
                        $userInfo['id'],
                        $imgListJson
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;

            $result = $this->qb
                ->createQueryBuilder('ad_apartment')
                ->selectSql('id')
                ->where('user_id = ' . $userInfo['id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
            return reset($result);
        }
        return 0;
    }

    public function publishAdApartnemt($id, $title, $textMain, $city, $tag, $price, $type) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            $locationData = json_decode($server->getLocationByQuery($tag));
            $locationItem = reset($locationData->data);

            return $this->qb
                ->createQueryBuilder('ad_apartment')
                ->updateSql(
                    [
                        'title',
                        'text_main',
                        'city',
                        'tag',
                        'type',
                        'price',
                        'latitude',
                        'longitude',
                        'timestamp'
                    ],
                    [
                        $server->charsString($title),
                        $server->charsString($textMain),
                        intval($city),
                        $server->charsString($tag),
                        intval($type),
                        intval($price),
                        $locationItem->latitude,
                        $locationItem->longitude,
                        $server->timeStampNow()
                    ]
                )
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
                ;
        }
        return false;
    }

    public function getAdApartmentList($offset = 0, $limit = 20, $groupUser = true, $city = -1, $type = -1, $from = 0, $to = 999999999, $q = '') {
        $limitFrom = $offset ? $offset * $limit : $limit;
        if ($groupUser) {
            $query = $this->qb
                ->createQueryBuilder('ad_apartment')
                ->selectSql('*, ad_apartment.id as aid')
                ->limit(($limitFrom - $limit) . ', ' . $limit)
                ->orderBy('ad_apartment.id DESC')
                ->leftJoin('users ON ad_apartment.user_id = users.id')
                ->where('timestamp > 0');

            if ($city >= 0)
                $query->andWhere('city = ' . intval($city));
            if ($type >= 0)
                $query->andWhere('type = ' . intval($type));

            if (intval($to) <= 0)
                $to = 999999999;
            if (intval($from) <= 0)
                $from = 0;

            $query->andWhere('price >= ' . intval($from));
            $query->andWhere('price <= ' . intval($to));

            if (!empty($q)) {
                global $server;
                $q = $server->charsString($q);
                $query->andWhere('(text_main LIKE \'%' . $q . '%\' OR tag = \'' . $q . '\')');
            }
            return $query->executeQuery()->getResult();
        }
        return $this->qb
            ->createQueryBuilder('ad_apartment')
            ->selectSql()
            ->limit(($limitFrom - $limit) . ', ' . $limit)
            ->orderBy('id DESC')
            ->where('timestamp > 0')
            ->executeQuery()
            ->getResult()
        ;
    }

    public function getLastAdApartmentByUserId() {
        global $userInfo;
        return $this->qb
            ->createQueryBuilder('ad_apartment')
            ->selectSql()
            ->where('user_id = ' . $userInfo['id'])
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getLastAdApartmentById($id) {
        return $this->qb
            ->createQueryBuilder('ad_apartment')
            ->selectSql()
            ->where('id = ' . intval($id))
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function deleteAdApartment($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $id = intval($id);
            return $this->qb
                ->createQueryBuilder('ad_apartment')
                ->deleteSql()
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
                ;
        }
        return false;
    }

    public function createAdOther($imgList) {
        if ($this->isLogin()) {
            global $userInfo;
            $imgListJson = json_encode($imgList);
            $this->qb
                ->createQueryBuilder('ad_other')
                ->insertSql(
                    [
                        'user_id',
                        'img'
                    ],
                    [
                        $userInfo['id'],
                        $imgListJson
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;

            $result = $this->qb
                ->createQueryBuilder('ad_other')
                ->selectSql('id')
                ->where('user_id = ' . $userInfo['id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
            return reset($result);
        }
        return 0;
    }

    public function publishAdOther($id, $title, $textMain, $city, $tag, $price, $type, $isBuy) {
        if ($this->isLogin()) {
            global $server;
            global $userInfo;
            global $cities;

            $locationItem = (object) [
                'latitude' => 0,
                'longitude' => 0,
            ];
            if (!empty($tag)) {
                $locationData = json_decode($server->getLocationByQuery($tag));
                $locationItem = reset($locationData->data);
            }
            else {
                $cityName = $cities[$city][2];
                if ($cityName == 'New York City')
                    $cityName = 'New York';
                $locationData = json_decode($server->getLocationByQuery($cityName));
                $locationItem = reset($locationData->data);
                $tag = $locationItem->label;
            }

            return $this->qb
                ->createQueryBuilder('ad_other')
                ->updateSql(
                    [
                        'title',
                        'text_main',
                        'city',
                        'tag',
                        'type',
                        'price',
                        'latitude',
                        'longitude',
                        'is_buy',
                        'timestamp'
                    ],
                    [
                        $server->charsString($title),
                        $server->charsString($textMain),
                        intval($city),
                        $server->charsString($tag),
                        intval($type),
                        intval($price),
                        $locationItem->latitude,
                        $locationItem->longitude,
                        $isBuy ? 1 : 0,
                        $server->timeStampNow()
                    ]
                )
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
                ;
        }
        return false;
    }

    public function getAdOtherList($offset = 0, $limit = 20, $groupUser = true, $city = -1, $type = -1, $search = -1, $from = 0, $to = 999999999, $q = '') {
        $limitFrom = $offset ? $offset * $limit : $limit;
        if ($groupUser) {
            $query = $this->qb
                ->createQueryBuilder('ad_other')
                ->selectSql('*, ad_other.id as aid')
                ->limit(($limitFrom - $limit) . ', ' . $limit)
                ->orderBy('ad_other.id DESC')
                ->leftJoin('users ON ad_other.user_id = users.id')
                ->where('timestamp > 0');

            if ($city >= 0)
                $query->andWhere('city = ' . intval($city));
            if ($type >= 0)
                $query->andWhere('type = ' . intval($type));
            if ($search >= 0)
                $query->andWhere('is_buy = ' . intval($search));

            if (intval($to) <= 0)
                $to = 999999999;
            if (intval($from) <= 0)
                $from = 0;

            $query->andWhere('price >= ' . intval($from));
            $query->andWhere('price <= ' . intval($to));

            if (!empty($q)) {
                global $server;
                $q = $server->charsString($q);
                $query->andWhere('(text_main LIKE \'%' . $q . '%\' OR tag = \'' . $q . '\')');
            }
            return $query->executeQuery()->getResult();
        }
        return $this->qb
            ->createQueryBuilder('ad_other')
            ->selectSql()
            ->limit(($limitFrom - $limit) . ', ' . $limit)
            ->orderBy('id DESC')
            ->where('timestamp > 0')
            ->executeQuery()
            ->getResult()
            ;
    }

    public function getLastAdOtherByUserId() {
        global $userInfo;
        return $this->qb
            ->createQueryBuilder('ad_other')
            ->selectSql()
            ->where('user_id = ' . $userInfo['id'])
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getLastAdOtherById($id) {
        return $this->qb
            ->createQueryBuilder('ad_other')
            ->selectSql()
            ->where('id = ' . intval($id))
            ->limit(1)
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function deleteAdOther($id) {
        if ($this->isLogin()) {
            global $userInfo;
            $id = intval($id);
            return $this->qb
                ->createQueryBuilder('ad_other')
                ->deleteSql()
                ->where('id = ' . $id)
                ->andWhere('user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;
        }
        return false;
    }

    public function showMessage($message) {
        global $view;
        global $modal;
        $modal['show'] = true;
        $modal['text'] = $message;
        $view->set('modal', $modal);
    }
}