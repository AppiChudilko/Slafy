<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $server;

$files = new \Server\Files();

header('Content-Type: application/json; charset=utf-8');
set_time_limit(200);

$result = $qb
    ->createQueryBuilder('instagram_export')
    ->selectSql()
    ->where('is_done_feed = 0')
    ->limit(1)
    ->executeQuery()
    ->getResult()
;

foreach ($result as $item) {
    if ($item['feed_page_hash'] == 'done') {
        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['is_done_feed'], [1])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;
        return;
    }
    if ($item['feed_page_hash'] != '') {

        $data = $user->getInstagramFeed($item['account_id'], $item['feed_page_hash']);

        foreach ($data['data']['user']['edge_owner_to_timeline_media']['edges'] as $mediaItem) {

            if ($mediaItem['node']['__typename'] == 'GraphVideo') {
                $imgPreview = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['display_url']);
                $videoUrl[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['video_url'], true);

                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgListJson = json_encode($videoUrl);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img_preview',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgPreview,
                            $imgListJson,
                            $caption,
                            $location,
                            'vid',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }
            else if ($mediaItem['node']['__typename'] == 'GraphSidecar') {
                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgList = [];
                foreach ($mediaItem['node']['edge_sidecar_to_children']['edges'] as $sliderItem) {
                    if ($sliderItem['node']['__typename'] == 'GraphImage') {
                        $imgList[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $sliderItem['node']['display_url']);
                        usleep(250000);
                    }
                }

                $imgListJson = json_encode($imgList);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgListJson,
                            $caption,
                            $location,
                            'img',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }
            else if ($mediaItem['node']['__typename'] == 'GraphImage') {
                $imgPreview = [];
                $imgPreview[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['display_url']);
                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgListJson = json_encode($imgPreview);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgListJson,
                            $caption,
                            $location,
                            'img',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }

            sleep(1);
        }

        $nextHash = $data['data']['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];
        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['feed_page_hash'], [$nextHash == '' ? 'done' : $nextHash])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;

        echo 'done';
    }
    else {
        $data = $user->getInstagramData($item['account']);
        if (!isset($data['graphql'])) {
            print_r($data);
            return;
        }
        $accountData = $data['graphql']['user'];
        $accountId = $data['graphql']['user']['id'];

        foreach ($data['graphql']['user']['edge_owner_to_timeline_media']['edges'] as $mediaItem) {

            if ($mediaItem['node']['__typename'] == 'GraphVideo') {
                $imgPreview = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['display_url']);
                $videoUrl[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['video_url'], true);

                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgListJson = json_encode($videoUrl);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img_preview',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgPreview,
                            $imgListJson,
                            $caption,
                            $location,
                            'vid',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }
            else if ($mediaItem['node']['__typename'] == 'GraphSidecar') {
                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgList = [];
                foreach ($mediaItem['node']['edge_sidecar_to_children']['edges'] as $sliderItem) {
                    if ($sliderItem['node']['__typename'] == 'GraphImage') {
                        $imgList[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $sliderItem['node']['display_url']);
                        usleep(250000);
                    }
                }

                $imgListJson = json_encode($imgList);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgListJson,
                            $caption,
                            $location,
                            'img',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }
            else if ($mediaItem['node']['__typename'] == 'GraphImage') {

                $imgPreview = [];
                $imgPreview[] = $files->uploadExternalFile('upload/feed/' . $item['user_id'] . '/', $mediaItem['node']['display_url']);
                $caption = $mediaItem['node']['edge_media_to_caption']['edges'][0]['node']['text'];
                $location = '';
                if (isset($mediaItem['node']['location']['name']))
                    $location = $mediaItem['node']['location']['name'];
                $publishTimestamp = $mediaItem['node']['taken_at_timestamp'];

                $imgListJson = json_encode($imgPreview);
                $qb
                    ->createQueryBuilder('feed')
                    ->insertSql(
                        [
                            'user_id',
                            'img',
                            'content',
                            'location',
                            'type',
                            'is_draft',
                            'timestamp',
                            'hash',
                        ],
                        [
                            $item['user_id'],
                            $imgListJson,
                            $caption,
                            $location,
                            'img',
                            0,
                            $publishTimestamp,
                            $server->getHashFull(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }

            sleep(1);
        }

        $nextHash = $data['graphql']['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];
        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['feed_page_hash'], [$nextHash == '' ? 'done' : $nextHash])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;
        echo 'done';

    }
}