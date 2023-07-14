<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $qb;
global $userInfo;
global $user;
global $methods;
global $server;

$feed = new \Server\Feed();

$verifyIcon = '<div class="official blue accent-4"><i class="material-icons-round">done</i></div>';

$followRequest = $qb
    ->createQueryBuilder('users_follow')
    ->selectSql('*, users_follow.id as fid, users.id as uid')
    ->where('users_follow.user_to = ' . $userInfo['id'])
    ->andWhere('users_follow.is_request = 1')
    ->leftJoin('users ON users_follow.user_from = users.id')
    ->limit(5)
    ->orderBy('users_follow.id DESC')
    ->executeQuery()
    ->getResult()
;
$notifyList = $qb
    ->createQueryBuilder('notify')
    ->selectSql('*, notify.id as nid, users.id as uid')
    ->where('notify.user_id = ' . $userInfo['id'])
    ->leftJoin('users ON notify.user_id_action = users.id')
    ->limit(50)
    ->orderBy('notify.id DESC')
    ->executeQuery()
    ->getResult()
;
$qb
    ->createQueryBuilder('notify')
    ->updateSql(['is_read'], [1])
    ->where('user_id = ' . $userInfo['id'])
    ->andWhere('is_read = 0')
    ->executeQuery()
    ->getResult()
;
?>
<script>
    $('#notify-indicator').attr('data-notify-count', 0);
    $('.notify-indicator').addClass('hide');
</script>
<style>
    .collection-item {
        min-height: 12px !important;
    }

    .notify-icon {
        position: absolute;
        left: 44px;
        top: 36px;
        z-index: 1;
        border-radius: 50%;
        width: 16px;
        height: 16px;
    }
    .notify-icon i {
        position: absolute;
        margin-left: 2px;
        margin-top: 2px;
        font-size: 12px;
        line-height: 12px;
        color: #fff !important;
    }

    .official {
        margin-left: 5px;
    }
</style>
<div class="container container-feed" style="padding-top: 80px">
    <div class="section">
        <div class="row">
            <div class="col s12 <?php echo !empty($followRequest) ? '' : 'hide' ?>">
                <h5 class="grey-text">Запросы на подписку</h5>
                <ul class="collection card" style="border: none">
                    <?php
                        foreach ($followRequest as $item) {
                            echo '
                                <li class="collection-item avatar flex" id="notify-id-' . $item['fid'] . '">
                                    <a spa="@' . $item['login'] . '"><img src="' . $user->getUserAvatar($item['uid'], $item['avatar']) . '" alt="" class="circle"></a>
                                    <div style="max-width: calc(100% - 84px);">
                                        <a spa="@' . $item['login'] . '" class="title flex bw-text">' . $user->getUserName($item['login'], $item['name']) . ' ' . $methods->getUserStatusIconAll($item) . '</a>
                                        <label class="grey-text">' . $server->timeStampToAgoUTC($item['timestamp']) . '</label>
                                    </div>
                                    <div style="margin-left: auto;margin-top: 6px;" class="flex">
                                        <aform>
                                            <input type="hidden" name="id" value="' . $item['fid'] . '">
                                            <input type="hidden" name="uid" value="' . $item['uid'] . '">
                                            <button name="user-follow-request-accept" class="btn btn-small waves-effect blue accent-4"><span style="line-height: 32px;" class="material-icons-round">done</span></button>
                                        </aform>
                                        <aform>
                                            <input type="hidden" name="id" value="' . $item['fid'] . '">
                                            <button name="user-follow-request-decline" style="margin-left: 4px" class="btn btn-small waves-effect grey accent-4"><span style="line-height: 32px;" class="material-icons-round">close</span></button>
                                        </aform>
                                    </div>
                                </li>
                            ';
                        }
                    ?>
                </ul>
            </div>
            <div class="col s12 <?php echo !empty($notifyList) ? '' : 'hide' ?>">
                <h5 class="grey-text">Уведомления</h5>
                <ul class="collection card" style="border: none">
                    <li class="collection-item avatar <?php echo ($userInfo['password'] == '' ? : 'hide') ?>">
                        <div class="red notify-icon"><i class="material-icons-round">vpn_key</i></div>
                        <i class="material-icons circle white-text red">warning</i>
                        <div>
                            <span class="title flex">Установите пароль</span>
                            <label class="grey-text">Пожалуйста установите ваш пароль в <a spa="settings">настройках</a> аккаунта</label>
                        </div>
                    </li>
                    <li class="collection-item avatar">
                        <div class="red notify-icon"><i class="material-icons-round">favorite</i></div>
                        <i class="material-icons circle white-text green">new_releases</i>
                        <div>
                            <span class="title flex">Системное уведомление</span>
                            <label class="grey-text">Уважаемые пользователи сайта, продукт находится в стадии <a spa="uf/Q-_bJa6e8CBd05BVkpwSKg">Альфа-Тестировании</a>, то есть это самая-самая первая версия портала, поэтому не кидайтесь палками, а лучше поддержите нас и если вы хотите внести какие-то изменения в наш продукт, то пожалуйста напишите в форме <a spa="im/3">обратной связи</a>. На данный момент мы исправляем все баги, дорабатываем и готовим новые обновления.<br><br>Только с вашей поддержкой мы сможем создать лучший продукт! Спасибо за внимание, мы вас очень любим &#x1F970;</label>
                        </div>
                    </li>
                    <?php
                    foreach ($notifyList as $item) {

                        $img = '';
                        if ($item['feed'] > 0 && $item['type'] < 99) {
                            $feedItem = $feed->getFeedById($item['feed']);
                            if (!empty($feedItem)) {
                                $img = json_decode($server->decodeString($feedItem['img']), true);
                                $img = '<a style="margin-left: auto; " spa="uf/' . $feedItem['hash'] . '"><img style="height: 46px; margin-left: auto; object-fit: cover; border-radius: 8px; width: 46px; object-fit: cover;" src="' . IMAGE_CDN_PATH . '/upload/feed/' . $feedItem['user_id'] . '/' . reset($img) . '"></a>';
                            }
                        }

                        $notifyIcon = '<div class="blue accent-4 notify-icon"><i class="material-icons-round">person_add</i></div>';
                        $notifyLabel = 'Поставил(а) лайк';
                        if ($item['type'] == 0) {
                            $notifyIcon = '<div class="blue accent-4 notify-icon"><i class="material-icons-round">person_add</i></div>';
                            $notifyLabel = 'Подписался на вас';
                        }
                        if ($item['type'] == 1) {
                            $notifyIcon = '<div class="amber accent-4 notify-icon"><i class="material-icons-round">person_remove</i></div>';
                            $notifyLabel = 'Отписался от вас';
                        }
                        if ($item['type'] == 2) {
                            $notifyIcon = '<div class="blue accent-4 notify-icon"><i class="material-icons-round">person_add</i></div>';
                            $notifyLabel = 'Одобрил запрос на подписку';
                        }
                        if ($item['type'] == 3) {
                            $notifyIcon = '<div class="green accent-4 notify-icon"><i class="material-icons-round">chat</i></div>';
                            $notifyLabel = 'Оставил(а) комментарий';
                        }
                        if ($item['type'] == 7) {
                            $notifyIcon = '<div class="green accent-4 notify-icon"><i class="material-icons-round">reply</i></div>';
                            $notifyLabel = 'Ответил(а) на комментарий';
                        }
                        if ($item['type'] == 5) {
                            $notifyIcon = '<div class="red accent-4 notify-icon"><i class="material-icons-round">favorite</i></div>';
                            $notifyLabel = 'Поставил(а) лайк';
                        }
                        if ($item['type'] >= 99) {
                            $notifyIcon = '<div class="pink accent-2 notify-icon"><i class="material-icons-round">auto_awesome</i></div>';
                            $notifyLabel = 'Вам был выдан подарочный Premium на ' . $item['feed'] . ' дней';
                            echo '
                                <li class="collection-item avatar flex">
                                    ' . $notifyIcon . '
                                    <a spa="@' . $item['login'] . '"><img src="' . $user->getUserAvatar($item['uid'], $item['avatar']) . '" alt="" class="circle"></a>
                                    <div style="max-width: 100%/*calc(100% - 64px)*/;">
                                        <a spa="@' . $item['login'] . '" class="title flex bw-text">' . $user->getUserName($item['login'], $item['name']) . ' ' . $methods->getUserStatusIcon($item) . '</a>
                                        <label class="grey-text">' . $notifyLabel . ' ' . $server->timeStampToAgoUTC($item['timestamp']) . '</label>
                                    </div>
                                </li>
                            ';
                        }
                        else {
                            echo '
                                <li class="collection-item avatar flex">
                                    ' . $notifyIcon . '
                                    <a spa="@' . $item['login'] . '"><img src="' . $user->getUserAvatar($item['uid'], $item['avatar']) . '" alt="" class="circle"></a>
                                    <div style="max-width: 100%/*calc(100% - 64px)*/;">
                                        <a spa="@' . $item['login'] . '" class="title flex bw-text">' . $user->getUserName($item['login'], $item['name']) . ' ' . $methods->getUserStatusIcon($item) . '</a>
                                        <label class="grey-text">' . $notifyLabel . ' ' . $server->timeStampToAgoUTC($item['timestamp']) . '</label>
                                    </div>
                                    ' . $img . '
                                    <button name="user-follow" class="secondary-content btn btn-small waves-effect blue accent-4 hide"><span style="line-height: 32px;" class="material-icons-round">person_add</span></button>
                                </li>
                            ';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>