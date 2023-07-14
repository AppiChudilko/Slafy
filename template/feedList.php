<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $methods;

$result = [];
$page = $this->page?:0;

if ($this->hash && $this->hash != 'null') {

    $limit = $page ? $page * 5 : 5;

    $feed = new \Server\Feed();
    $feedItem = $feed->getFeedByHash($this->hash);

    if (!empty($feedItem)) {
        $result = $qb
            ->createQueryBuilder('feed')
            ->selectSql('*, feed.id as fid')
            ->orderBy('feed.timestamp DESC')
            ->limit(($limit - 5) . ', 5')
            ->leftJoin('users ON feed.user_id = users.id')
            ->where('feed.is_draft = 0')
            ->andWhere('feed.type = \'img\'')
            ->andWhere('feed.user_id = ' . intval($feedItem['user_id']))
            ->andWhere('feed.timestamp < \'' . $feedItem['timestamp'] . '\'')
            ->executeQuery()
            ->getResult()
        ;
    }
}
else {
    $limit = $page ? $page * 5 : 5;
    $followList = $user->getFollowUserList();
    $query = '';
    foreach ($followList as $item)
        $query .= ' OR feed.user_id = ' . $item['user_to'];
    
    $result = $qb
        ->createQueryBuilder('feed')
        ->selectSql('*, feed.id as fid')
        ->orderBy('feed.timestamp DESC')
        ->limit(($limit - 5) . ', 5')
        ->leftJoin('users ON feed.user_id = users.id')
        ->where('feed.is_draft = 0')
        ->andWhere('feed.type = \'img\'')
        ->andWhere('(feed.user_id = ' . $userInfo['id'] . $query . ')')
        ->executeQuery()
        ->getResult()
    ;
}
if (empty($result) && $page < 2)
    echo $methods->showError('Лента пустая, вам бы подписаться на кого-нибудь тут', 0, 'spravka');

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
        $user->feedIsLike($item['fid']),
    );
}