<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;

$files = new \Server\Files();

header('Content-Type: application/json; charset=utf-8');
set_time_limit(280);

$result = $qb
    ->createQueryBuilder('instagram_export')
    ->selectSql()
    ->where('is_done_story = 0')
    ->limit(1)
    ->executeQuery()
    ->getResult()
;

foreach ($result as $item) {
    if ($item['highlights'] == 'done' || $item['highlights'] == '[]') {
        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['is_done_story'], [1])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;
        return;
    }

    if ($item['highlights'] == '') {
        $data = $user->getInstagramHighlights($item['account_id']);
        $trays = [];
        foreach ($data['tray'] as $tray) {
            $imgPreview = $files->uploadExternalFile('upload/highlight/' . $item['user_id'] . '/', $tray['cover_media']['cropped_image_version']['url']);
            $qb
                ->createQueryBuilder('highlight')
                ->insertSql(['user_id', 'name', 'avatar'], [$item['user_id'], $tray['title'], $imgPreview])
                ->executeQuery()
                ->getResult()
            ;
            $highlightDB = $qb
                ->createQueryBuilder('highlight')
                ->selectSql()
                ->where('user_id = ' . $item['user_id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;
            $trays[] = ['id' => $tray['id'], 'hid' => $highlightDB['id']];
            sleep(1);
        }

        $trayJson = json_encode($trays);
        if (empty($trays))
            $trayJson = 'done';

        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['highlights'], [$trayJson])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;

        echo 'done';
    }
    else {
        $highlightArray = json_decode(htmlspecialchars_decode($item['highlights']), true);
        if (empty($highlightArray)) {
            $qb
                ->createQueryBuilder('instagram_export')
                ->updateSql(['highlights', 'is_done_story'], ['done', 1])
                ->where('id = ' . $item['id'])
                ->executeQuery()
                ->getResult()
            ;
            return;
        }

        $highlightId = reset($highlightArray);
        array_shift($highlightArray);

        $data = $user->getInstagramHighlightId($highlightId['id']);
        foreach ($data['reels'][$highlightId['id']]['items'] as $highlight) {
            if (isset($highlight['video_versions'])) {
                $video = reset($highlight['video_versions']);
                $media = $files->uploadExternalFile('upload/highlight/' . $item['user_id'] . '/' . $highlightId['hid'] . '/', $video['url'], true);
                $qb
                    ->createQueryBuilder('story')
                    ->insertSql(
                        [
                            'user_id',
                            'media',
                            'type',
                            'is_draft',
                            'timestamp',
                        ],
                        [
                            $item['user_id'],
                            $media,
                            'vid',
                            0,
                            isset($highlight['imported_taken_at']) ? $highlight['imported_taken_at'] / 1000000 : time(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }
            else {
                $image = reset($highlight['image_versions2']['candidates']);
                $media = $files->uploadExternalFile('upload/highlight/' . $item['user_id'] . '/' . $highlightId['hid'] . '/', $image['url']);
                $qb
                    ->createQueryBuilder('story')
                    ->insertSql(
                        [
                            'user_id',
                            'media',
                            'type',
                            'is_draft',
                            'timestamp',
                        ],
                        [
                            $item['user_id'],
                            $media,
                            'img',
                            0,
                            isset($highlight['imported_taken_at']) ? $highlight['imported_taken_at'] / 1000000 : time(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
            }

            $storyId = $qb
                ->createQueryBuilder('story')
                ->selectSql('id')
                ->where('user_id = ' . $item['user_id'])
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;

            $qb
                ->createQueryBuilder('highlight_keys')
                ->insertSql(
                    [
                        'highlight_id',
                        'story_id',
                    ],
                    [
                        $highlightId['hid'],
                        $storyId['id'],
                    ]
                )
                ->executeQuery()
                ->getResult()
            ;

            sleep(1);
        }

        $qb
            ->createQueryBuilder('instagram_export')
            ->updateSql(['highlights'], [json_encode($highlightArray)])
            ->where('id = ' . $item['id'])
            ->executeQuery()
            ->getResult()
        ;

        echo 'done2';
    }
}