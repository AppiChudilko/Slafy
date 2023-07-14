<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $methods;
global $server;

$result = $qb
    ->createQueryBuilder('feed')
    ->selectSql('*, feed.id as fid')
    ->orderBy('feed.timestamp DESC')
    ->leftJoin('users ON feed.user_id = users.id')
    ->where('feed.hash = \'' . $server->charsString($this->hash) . '\'')
    ->limit(1)
    ->executeQuery()
    ->getResult()
;

if (!empty($result)) {
    $userPageInfo = $user->getUserInfoById($result[0]['user_id']);
    if ($userPageInfo['id'] != $userInfo['id'])
    {
        if (!$userPageInfo['is_public'] && !$user->hasFollowUser($userPageInfo['id']))
        {
            echo '
            <div class="container" style="padding-top: 80px">
                <div class="section">
                    ' . $methods->showError('Ошибка доступа, вам необходимо подписаться на аккаунт') . '
                </div>
            </div>
        ';
            return;
        }
    }
}

$result2 = $qb
    ->createQueryBuilder('feed')
    ->selectSql('*, feed.id as fid')
    ->orderBy('feed.timestamp DESC')
    ->limit('0, 4')
    ->leftJoin('users ON feed.user_id = users.id')
    ->where('feed.is_draft = 0')
    ->andWhere('feed.type = \'img\'')
    ->andWhere('feed.user_id = ' . intval($result[0]['user_id']))
    ->andWhere('feed.timestamp < \'' . $result[0]['timestamp'] . '\'')
    ->executeQuery()
    ->getResult()
;

$result = array_merge($result, $result2);
?>

    <div class="container container-feed" style="padding-top: 80px">
        <div class="section">
            <div class="row">
                <div class="col s12" id="user-feed-content">
                    <?php
                    if (empty($result))
                        echo $methods->showError('Пост не был найден', 0, 'spravka');
                    foreach ($result as $item) {
                        echo $methods->showFeedBlock(
                            $item['hash'],
                            $item['user_id'],
                            $item['login'],
                            $item['name'] ?: $item['login'],
                            $user->getUserAvatar($item['user_id'], $item['avatar']),
                            $item['content'],
                            $item['location'],
                            $item['likes'],
                            $item['comments'],
                            $item['is_disable_comment'],
                            $item['is_disable_likes'],
                            $item['timestamp'],
                            json_decode(htmlspecialchars_decode($item['img'])),
                            $user->feedIsLike($item['fid'])
                        );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
echo $methods->getFeedModals();