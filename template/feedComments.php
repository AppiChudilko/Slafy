<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $methods;
global $server;

$feed = new \Server\Feed();
$feedItem = $feed->getFeedByHash($_POST['id']);
$feedComments = $feed->getFeedComments($feedItem['id']);
if (empty($feedComments))
    echo '<h4 class="grey-text center">Список комментариев пуст</h4>';
foreach ($feedComments as $comment) {
    $deleteBtn = '';
    if ($comment['user_id'] == $userInfo['id'])
        $deleteBtn = '<aform><input type="hidden" name="id" value="' . $comment['fid'] . '"><a name="feed-comment-del" class="secondary-content flex feed-comment-menu"><i class="material-icons-round grey-text">delete</i></a></aform>';
    echo '
        <li class="collection-item avatar" id="feed-comment-' . $comment['fid'] . '">
            <img src="' . $user->getUserAvatar($comment['user_id'], $comment['avatar']) . '" alt="" class="circle">
            <a spa="@' . $comment['login'] . '" class="title flex grey-text">' . $user->getUserName($comment['login'], $comment['name']) . ' ' . $methods->getUserStatusIcon($comment) . '</a>
            <p>
                ' . nl2br($server->parseText($comment['text'])) . '
                <br>
                <label><a onclick="$(\'#feed-comment-reply\').val(' . $comment['user_id'] . '); $(\'#feed-comment-area\').val($(\'#feed-comment-area\').val() + \'@' . $comment['login'] . ' \'); ">Ответить</a> · ' . $server->timeStampToAgoUTC($comment['timestamp']) . '</label>
            </p>
            <aform>
                <input type="hidden" name="id" value="' . $comment['fid'] . '">
                <a name="feed-comment-like" class="secondary-content flex feed-comment-like waves-effect"><i class="material-icons-round ' . ($user->feedCommentIsLike($comment['fid']) ? 'red' : 'bw') . '-text">favorite</i> <span>' . $server->numberToKkk($comment['likes']) . '</span></a>
            </aform>
            ' . $deleteBtn . '
        </li>
    ';
}