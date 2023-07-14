<?php

namespace Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Methods
 */
class Feed
{
    public function getFeedComments($id, $offset = 0) {
        global $qb;
        global $server;
        return $qb
            ->createQueryBuilder('feed_comments')
            ->selectSql('*, feed_comments.id as fid')
            ->where('feed_id = ' . $id)
            ->leftJoin('users ON feed_comments.user_id = users.id')
            ->limit(20)
            ->orderBy('likes DESC, feed_comments.id DESC')
            ->executeQuery()
            ->getResult()
        ;
    }

    public function getFeedComment($id) {
        global $qb;
        return $qb->createQueryBuilder('feed_comments')->selectSql()->where('id = ' . intval($id))->limit(1)->executeQuery()->getSingleResult();
    }

    public function getFeedByHash($hash) {
        global $qb;
        global $server;
        return $qb->createQueryBuilder('feed')->selectSql()->where('hash = \'' . $server->charsString($hash) . '\'')->limit(1)->executeQuery()->getSingleResult();
    }

    public function getFeedById($id) {
        global $qb;
        return $qb->createQueryBuilder('feed')->selectSql()->where('id = \'' . intval($id) . '\'')->limit(1)->executeQuery()->getSingleResult();
    }
}