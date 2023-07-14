<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
/**
 * Request
 */
class Request
{
    protected $arrayRequest = [
        'index',
        'login',
        'signup',
        'signup112233',
        'feed',
        'privacy',
        'terms',
        'publish/feed',
        'publish/work',
        'publish/service',
        'publish/apartment',
        'publish/other',
        'ad/apartment/',
        'ad/other/',
        'settings',
        'notify',
        'search',
        'ads',
        'help',
        'im',
        'im/archive/',
        'im/hidden/',
        'im/request/',
        'im/',
        '@',
        'uf/',
        /*'auth',
        'code',
        'curl',
        'curl2',
        'test',
        'test2',
        'testBlock',*/
        'test',
        '1a500fd3affcbb9833675100db5910714b8d8d75cb245a3f7992dc80521f3254', //Export Instagram Feed
        '40479243b961580e26afd115b905d97f20fe977f7fcf9034acf20fdac86dc5bc', //Export Instagram Story
        '77c792444d6f775b3116fca5c574f996f93aa6ba16d7740264756d0586d3b8ea', //1sec Cron
        '77c792444d6f775b3116fca5c574f996f93aa6ba16d7740264756d0586d3b8ea2', //1sec Cron
    ];

    public function getRequest($params = []) {

        $result = [];
        //$params = array_merge($params, json_decode(file_get_contents('config/request.json'), true));

        $params = array_merge($params, $this->arrayRequest);

        if (empty($params)) return false;

        foreach ($params as $value) {
            if (preg_match('#/' . $value . '([^/?]+)#', $_SERVER['REQUEST_URI'], $match)) {
                $result[$value] = $match[1];
            }
            else if (preg_match('#^/?(' . $value . ')#', $_SERVER['REQUEST_URI'], $match)) {
                $result['p'] = $match[1];
            }
        }
        return $result;
    }

    public function getAjaxRequest($url, $params = []) {

        $result = [];
        //$params = array_merge($params, json_decode(file_get_contents('config/request.json'), true));

        //$url = 'https://adaptation-usa.com/' . $url;
        $url = '/' . $url;
        $params = array_merge($params, $this->arrayRequest);

        if (empty($params)) return false;


        foreach ($params as $value) {
            if (preg_match('#/' . $value . '([^/?]+)#', $url, $match)) {
                $result[$value] = $match[1];
            }
            else if (preg_match('#^/?(' . $value . ')#', $url, $match)) {
                $result['p'] = $match[1];
            }
        }

        return $result;
    }

    public function showPage($page, $ajax = false) {

        global $methods;
        global $tmp;
        global $qb;
        global $view;
        global $user;
        global $userInfo;

        $view->set('img', '');

        if (!isset($page['p']))
            $page['p'] = 'костыляемкароче';

        switch ($page['p']) {
            case 'login':
                if ($user->isLogin()) {
                    header('Location: /feed');
                    return;
                }
                $tmp->showPage('login', 'Авторизация', $ajax);
                break;
            case 'signup':
                if ($user->isLogin()) {
                    header('Location: /feed');
                    return;
                }
                $tmp->showPage('signup', 'Авторизация', $ajax);
                break;
            case 'signup112233':
                $tmp->showPage('signup112233', 'Авторизация', $ajax);
                break;
            case 'privacy':
                $tmp->showPage('privacy', 'Политика', $ajax);
                break;
            case 'terms':
                $tmp->showPage('terms', 'Правила', $ajax);
                break;
            case 'auth':
                $tmp->showPage('auth', 'auth', $ajax);
                break;
            case 'testBlock':
                $tmp->showPage('testBlock', 'testBlock', $ajax);
                break;
            case '1a500fd3affcbb9833675100db5910714b8d8d75cb245a3f7992dc80521f3254':
                $tmp->showBlockPage('exportInstagramFeed');
                break;
            case '40479243b961580e26afd115b905d97f20fe977f7fcf9034acf20fdac86dc5bc':
                $tmp->showBlockPage('exportInstagramStory');
                break;
            case 'feed':
                if ($user->isLogin())
                    $tmp->showPage('feed', 'Лента', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'settings':
                if ($user->isLogin())
                    $tmp->showPage('settings', 'Настройки', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'notify':
                if ($user->isLogin())
                    $tmp->showPage('notify', 'Уведомления', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'ads':
            case 'search':
                $tmp->showPage('search', 'Доска объявлений', $ajax);
                break;
            case 'help':
                $tmp->showPage('help', 'Помощь', $ajax);
                break;
            case 'publish/feed':
                if ($user->isLogin())
                    $tmp->showPage('publish/feed', 'Заметки', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'publish/work':
                if ($user->isLogin())
                    $tmp->showPage('publish/work', 'Публикация - Работа', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'publish/service':
                if ($user->isLogin())
                    $tmp->showPage('publish/service', 'Публикация - Услуги', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'publish/apartment':
                if ($user->isLogin())
                    $tmp->showPage('publish/apartment', 'Публикация - Жилье', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'publish/other':
                if ($user->isLogin())
                    $tmp->showPage('publish/other', 'Публикация - Остальное', $ajax);
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case 'im':
                if ($user->isLogin()) {
                    $view->set('hideFooter', true);
                    $tmp->showPage('message', 'Диалоги', $ajax);
                }
                else
                    $tmp->showPage('errors/access', 'Ошибка доступа', $ajax);
                break;
            case '77c792444d6f775b3116fca5c574f996f93aa6ba16d7740264756d0586d3b8ea2':
                $tmp->showPage('parser/rusrek', 'parser', $ajax);
                break;
            case '77c792444d6f775b3116fca5c574f996f93aa6ba16d7740264756d0586d3b8ea':

                return;
                global $server;

                header('Content-Type: application/json; charset=utf-8');
                $filesDb = scandir('bd');

                $fd = fopen("bd/" . $filesDb[2], 'r');
                $tm = fopen($tmpname = tempnam('.', 'list'), 'w+');
                if($fd === false) exit('Не могу открыть целевой файл');
                if($tm === false) exit('Не могу открыть временный файл');
                $i = 0;
                $countLines = 0;
                while (($line = fgets($fd)) !== false) {

                    if(++$i < 100)
                    {
                        $r = str_getcsv($line);
                        if ($r[0] == 'id') {
                            $countLines = count($r);
                            fwrite($tm, $line);
                            continue;
                        }

                        if (count($r) < $countLines)
                            continue;

                        print_r($r);

                        $qb
                            ->createQueryBuilder('users_y')
                            ->insertSql
                            (
                                [
                                    'first_name',
                                    'full_name',
                                    'email',
                                    'phone_number',
                                    'address_city',
                                    'address_street',
                                    'address_house',
                                    'address_entrance',
                                    'address_floor',
                                    'address_office',
                                    'address_comment',
                                    'location_latitude',
                                    'location_longitude',
                                    'user_agent',
                                    'address_doorcode',
                                ],
                                [
                                    isset($r[1]) ? substr($r[1], 0, 60) : '',
                                    $r[2] ?? '',
                                    $r[3] ?? '',
                                    $r[4] ?? '',
                                    $r[5] ?? '',
                                    $r[6] ?? '',
                                    $r[7] ?? '',
                                    $r[8] ?? '',
                                    $r[9] ?? '',
                                    $r[10] ?? '',
                                    $r[11] ?? '',
                                    isset($r[12]) ? substr($r[12], 0, 32) : '',
                                    isset($r[13]) ? substr($r[13], 0, 32) : '',
                                    $r[16] ?? '',
                                    $r[18] ?? '',
                                ]
                            )
                            ->executeQuery()
                            ->getResult()
                        ;

                        //if (isset($r[3]) && !empty($r[3]))
                        //    $server->sendMailSpam($r[3]);
                        continue;
                    }
                    fwrite($tm, $line);
                }
                fclose($fd);
                fclose($tm);
                rename($tmpname, "bd/" . $filesDb[2]);
                if ($i < 5)
                    return unlink("bd/" . $filesDb[2]);
                break;
            case 'test2':
                global $server;
                $offset = 0;
                $limit = 20;
                $limitOffset = $offset ? $offset * $limit : $limit;

                $server->sendMailSpam('rackety@yandex.ru');
                //$server->sendEmail('rackety@yandex.ru', 'https://adaptation-usa.com/feed');

                //mail('channelappi@gmail.com', 'Активация аккаунта', 'Ваш емайл активирован', 'FROM: Slafy <admin@adaptation-usa.com>');
                //mail('rackety@yandex.ru', 'Активация аккаунта', 'Ваш емайл активирован');

                print_r($qb
                    ->createQueryBuilder('dialog_list')
                    ->selectSql('dialog_list.*, dialog_settings.type')
                    ->where('(uid1 = ' . $userInfo['id'] . ' OR uid2 = ' . $userInfo['id'] . ')')
                    ->andWhere('(dialog_settings.type = 0 OR dialog_settings.type = 1)')
                    ->orderBy('last_update DESC, dialog_settings.type DESC')
                    ->rightJoin('dialog_settings ON dialog_list.id = dialog_settings.dialog_id AND dialog_settings.user_id = ' . $userInfo['id'])
                    ->limit(($limitOffset - $limit) . ', ' . $limit)
                    ->executeQuery()
                    ->getQuery()
                ) ;

                /*$result = $qb->createQueryBuilder('feed')->selectSql()->executeQuery()->getResult();
                foreach ($result as $item) {
                    $hash = $server->getHash(md5(time() . $item['id'] . rand(-1000, 1000)));
                    $qb->createQueryBuilder('feed')->updateSql(['hash'], [$hash])->where('id = ' . $item['id'])->executeQuery()->getResult();
                }*/
                /*for ($i = 0; $i < 5000; $i++) {
                    $qb
                        ->createQueryBuilder('users_follow_test')
                        ->insertSql(
                            ['user_from', 'user_to', 'is_request', 'timestamp'],
                            [rand(0, 1000000000), 1, 0, time()]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;
                }*/
                break;
            case 'test':
                global $server;
                echo $server->charsString('#iPod');
                return;
                /*
                https://www.instagram.com/accounts/login/?force_authentication=1&enable_fb_login=1&next=/oauth/authorize/%3Fredirect_uri%3Dhttps%3A//developers.facebook.com/instagram/token_generator/oauth/%26client_id%3D678017163538368%26response_type%3Dcode%26scope%3Duser_profile%2Cuser_media%26state%3D%257B%2522app_id%2522%3A%2522678017163538368%2522%2C%2522user_id%2522%3A%252217841401805871748%2522%2C%2522nonce%2522%3A%2522EAbpJWSRHKqmdigd%2522%257D


                https://www.instagram.com/accounts/login/?force_authentication=1&enable_fb_login=1&next=/oauth/authorize/%3Fredirect_uri%3Dhttps%3A//developers.facebook.com/instagram/token_generator/oauth/%26client_id%3D659021289293564%26response_type%3Dcode%26scope%3Duser_profile%2Cuser_media%26state%3D%257B%2522app_id%2522%3A%2522678017163538368%2522%2C%2522user_id%2522%3A%252217841401805871748%2522%2C%2522nonce%2522%3A%2522EAbpJWSRHKqmdigd%2522%257D

                 * */

                $post = [
                    'client_id' => '678017163538368',
                    'redirect_uri' => 'https://adaptation-usa.com/code/',
                    'scope' => 'user_profile,user_media',
                    'response_type' => 'code'
                ];

                $ch = curl_init('https://www.instagram.com/oauth/authorize');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                $response = curl_exec($ch);
                curl_close($ch);
                print_r($response);
                break;
            case 'code':

                /*
                AQCTg5eaIUCk9jtN2ThoLrJm2jST80PdQ7bQIEkr6dSNfixK1tr__TDJy8N3NO6lqslBHltK8z7-yBgpXGjr_IVHsW62rzgE3UuTFLB5XYLHaEQyIbSTytKUr1awu2QQlKEO-zRiV98qRKpmq6CMU6bUTtG2eKowi9MzxMcF2gmGM5i8S6_VM318TILTkBlK7aRUexc8iD4l2QvRmjBbXZ44sR1lk7fih6sdT36WWdzOEg#_

                 * */

                $post = [
                    'client_id' => '678017163538368',
                    'client_secret' => '01c22bb6a3f957b482f30ab431a676f2',
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => 'https://adaptation-usa.com/',
                    'code' => $_GET['code']
                ];

                $ch = curl_init('https://api.instagram.com/oauth/access_token');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                //https://www.instagram.com/graphql/query/?query_hash=56a7068fea504063273cc2120ffd54f3&variables=%7B%22id%22%3A%221476919210%22%2C%22first%22%3A12%2C%22after%22%3A%22QVFBdzl5NGZscTZzUzh0R21jbGJENUJRYVkya0N5dTYxZmNMd0I1bUhSUkZkYUplZjVZZmZnTHp5TGNjSDJXSWM4N2M1Nk5tYTFzeVg2RXBCZWNhaFN1Uw%3D%3D%22%7D

                //{"access_token": "IGQVJYVjM1U2tPWVlsc3pUbHJDT0FzeFhvVHJBWnZASa01SSUxESlFfYnV4NkxUWE5ZAa01VTGxWZADQ3QlotRGFWbFdXdDhHZADhMSV8tVFhiNFZASVFRncm1BR0h6QXFuTTNsTEgxWHpBR3UtSXRKOFlpZAWl0Wm0wVGpweC13", "user_id": 17841401805871748}

                $response = curl_exec($ch);
                curl_close($ch);
                print_r($response);
                break;
            case 'curl': // Экспорт хайлайтов
                header('Content-Type: application/json; charset=utf-8');
                $post = [
                    'user_ids' => '17841401805871748'
                ];
                /*curl --compressed 'https://i.instagram.com/api/v1/highlights/19318909/highlights_tray/' \
-X 'GET' \
-H 'Accept: *\/*' \
-H 'Accept-Encoding: gzip, deflate, br' \
-H 'Accept-Language: ru' \
-H 'Host: i.instagram.com' \
-H 'Origin: https://www.instagram.com' \
-H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Safari/605.1.15' \
-H 'Connection: keep-alive' \
-H 'Referer: https://www.instagram.com/github/' \
-H 'Cookie: csrftoken=Y1DX2RCofLmJJ6nah5K6Q1OcEyrfcl4L; ds_user_id=1391932195; rur="LDC\0541391932195\0541680338574:01f70314ede3e1a9cd953005891bd682621a7b9609f26d24699bdde69400ea551b071299"; shbid="4604\0541391932195\0541680338333:01f788f10fae1307d368d5bf4a3dc2aa2728cb935fa388ca1ec7687d10344c086989bda6"; shbts="1648802333\0541391932195\0541680338333:01f7d9f94d0d8f0667e70c7b1dc085a1a38e4e5515b70a5ebfa614349890505865f7ef99"; sessionid=1391932195%3ANiAlFdAymcJE3V%3A13; ig_did=FEF8403F-08CE-43A8-8FBF-810BBF884758; mid=Yka6DgAEAAG2oWqxGGn---P45j2a' \
-H 'X-ASBD-ID: 198387' \
-H 'X-IG-App-ID: 936619743392459' \
-H 'X-IG-WWW-Claim: hmac.AR3HuHg-TEg3ZnZ5cS744uoyidAkpBdd38dI5ChoNHNnIFZl'*/


                $headers[] = 'Cookie: csrftoken=Y1DX2RCofLmJJ6nah5K6Q1OcEyrfcl4L; ds_user_id=1391932195; rur="LDC\0541391932195\0541680417893:01f703f7c113c27be5198e1a94c8e25b491c60b8c7f2a3ad2ac7c9cf8356474d681982bc"; shbid="4604\0541391932195\0541680338333:01f788f10fae1307d368d5bf4a3dc2aa2728cb935fa388ca1ec7687d10344c086989bda6"; shbts="1648802333\0541391932195\0541680338333:01f7d9f94d0d8f0667e70c7b1dc085a1a38e4e5515b70a5ebfa614349890505865f7ef99"; sessionid=1391932195%3ANiAlFdAymcJE3V%3A13; ig_did=FEF8403F-08CE-43A8-8FBF-810BBF884758; mid=Yka6DgAEAAG2oWqxGGn---P45j2a';
                //curl -A "Instagram 219.0.0.12.117 Android" https://i.instagram.com/api/v1/users/17841401805871748/info/
                //curl -A "Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)" https://i.instagram.com/api/v1/highlights/19318909/highlights_tray/
                $ch = curl_init('https://i.instagram.com/api/v1/highlights/1391932195/highlights_tray/');
                //$ch = curl_init('https://i.instagram.com/api/v1/archive/reel/day_shells/');
                //$ch = curl_init('https://i.instagram.com/api/v1/feed/reels_media/');
                //$ch = curl_init('https://graph.facebook.com/v11.0/17841401805871748/stories?access_token=IGQVJYVjM1U2tPWVlsc3pUbHJDT0FzeFhvVHJBWnZASa01SSUxESlFfYnV4NkxUWE5ZAa01VTGxWZADQ3QlotRGFWbFdXdDhHZADhMSV8tVFhiNFZASVFRncm1BR0h6QXFuTTNsTEgxWHpBR3UtSXRKOFlpZAWl0Wm0wVGpweC13');
                //$ch = curl_init('https://i.instagram.com/api/v1/users/17841401805871748/info/?access_token=IGQVJYVjM1U2tPWVlsc3pUbHJDT0FzeFhvVHJBWnZASa01SSUxESlFfYnV4NkxUWE5ZAa01VTGxWZADQ3QlotRGFWbFdXdDhHZADhMSV8tVFhiNFZASVFRncm1BR0h6QXFuTTNsTEgxWHpBR3UtSXRKOFlpZAWl0Wm0wVGpweC13');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_POST, true);
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                //curl_setopt($ch, CURLOPT_USERAGENT, 'Instagram 85.0.0.21.100 Android (21/5.0.2; 480dpi; 1080x1776; Sony; C6603; C6603; qcom; ru_RU; 146536611)');
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                curl_close($ch);
                print_r(json_decode($response));
                break;
            case 'curl2': //Эспорт постов
                header('Content-Type: application/json; charset=utf-8');
                $headers[] = 'Cookie: csrftoken=Y1DX2RCofLmJJ6nah5K6Q1OcEyrfcl4L; ds_user_id=1391932195; rur="CLN\0541391932195\0541682129604:01f7c22298702dacf9efcdf89441f1bc8c8c5d4d4212fd810acce8bb1116a2d64fc84e43"; sessionid=1391932195%3ANiAlFdAymcJE3V%3A13; shbid="4604\0541391932195\0541682129501:01f71ffc943e69296e22c43aa00f05aea02edd450060d9c5ffe89a45a6833c41cce5e9f7"; shbts="1650593501\0541391932195\0541682129501:01f74d8b5f99f19434bc08a477b5bfe6d8736b02180683911eb0d237d6ceeaf142b2318c"; ig_did=FEF8403F-08CE-43A8-8FBF-810BBF884758; mid=Yka6DgAEAAG2oWqxGGn---P45j2a';
                //curl -A "Instagram 219.0.0.12.117 Android" https://i.instagram.com/api/v1/users/17841401805871748/info/
                //curl -A "Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)" https://i.instagram.com/api/v1/highlights/19318909/highlights_tray/
                $ch = curl_init('https://www.instagram.com/graphql/query/?query_hash=396983faee97f4b49ccbe105b4daf7a0&variables=%7B%22id%22%3A%222221776439%22%2C%22first%22%3A12%2C%22after%22%3A%22QVFDSkt0X1Q0ZlJkLWd1TGJqMkZnZzEyMFZ3MnZuMEpJUTJtUnNJdlhiU3RyOGVzSXhzcXRwRm1Pcm92a21uNHAtLWVYUnVaMW9DbFlZaUg0YURySE1LRw%3D%3D%22%7D');
                //$ch = curl_init('https://i.instagram.com/api/v1/archive/reel/day_shells/');
                //$ch = curl_init('https://i.instagram.com/api/v1/feed/reels_media/');
                //$ch = curl_init('https://graph.facebook.com/v11.0/17841401805871748/stories?access_token=IGQVJYVjM1U2tPWVlsc3pUbHJDT0FzeFhvVHJBWnZASa01SSUxESlFfYnV4NkxUWE5ZAa01VTGxWZADQ3QlotRGFWbFdXdDhHZADhMSV8tVFhiNFZASVFRncm1BR0h6QXFuTTNsTEgxWHpBR3UtSXRKOFlpZAWl0Wm0wVGpweC13');
                //$ch = curl_init('https://i.instagram.com/api/v1/users/17841401805871748/info/?access_token=IGQVJYVjM1U2tPWVlsc3pUbHJDT0FzeFhvVHJBWnZASa01SSUxESlFfYnV4NkxUWE5ZAa01VTGxWZADQ3QlotRGFWbFdXdDhHZADhMSV8tVFhiNFZASVFRncm1BR0h6QXFuTTNsTEgxWHpBR3UtSXRKOFlpZAWl0Wm0wVGpweC13');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_POST, true);
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                //curl_setopt($ch, CURLOPT_USERAGENT, 'Instagram 85.0.0.21.100 Android (21/5.0.2; 480dpi; 1080x1776; Sony; C6603; C6603; qcom; ru_RU; 146536611)');
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 9; SM-A102U Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.136 Mobile Safari/537.36 Instagram 155.0.0.37.107 Android (28/9; 320dpi; 720x1468; samsung; SM-A102U; a10e; exynos7885; en_US; 239490550)');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                curl_close($ch);
                print_r(json_decode($response));
                break;

            default:
                if ($page['p'] == 'index' || $_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == 'index.php' || $_SERVER['REQUEST_URI'] == 'index') {
                    $tmp->showPage('search', 'Объявления', $ajax);
                    //$tmp->showPage('index', 'Главная', $ajax);
                }
                else if (isset($page['@'])) {
                    $targetInfo = $user->getUserInfoByLogin(strtolower($page['@']));
                    if (!empty($targetInfo)) {
                        $view->set('userInfo', $targetInfo);
                        $view->set('img', $user->getUserAvatar($targetInfo['id'], $targetInfo['avatar']));
                        $tmp->showPage('profile', $user->getUserName($targetInfo['login'], $targetInfo['name']), $ajax);
                    }
                    else {
                        if (!$ajax)
                            header("HTTP/1.1 404 Not Found");
                        $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                    }

                }
                else if (isset($page['uf/'])) {
                    global $server;
                    $result = $qb
                        ->createQueryBuilder('feed')
                        ->selectSql('*, feed.id as fid')
                        ->orderBy('feed.timestamp DESC')
                        ->leftJoin('users ON feed.user_id = users.id')
                        ->where('feed.hash = \'' . $server->charsString($page['uf/']) . '\'')
                        ->limit(1)
                        ->executeQuery()
                        ->getResult()
                    ;

                    $singleReset = reset($result);

                    if (!empty($singleReset)) {
                        $imgReset = json_decode(htmlspecialchars_decode($singleReset['img']));
                        $view->set('img', IMAGE_CDN_PATH .'/upload/feed/' . $singleReset['user_id'] . '/' . reset($imgReset));
                    }

                    $view->set('hash', $page['uf/']);
                    $view->set('feedItem', $result);
                    $tmp->showPage('feedProfile', 'Пост', $ajax);

                }
                else if (isset($page['ad/apartment/'])) {
                    $adItem = $user->getLastAdApartmentById($page['ad/apartment/']);
                    $view->set('ad', $adItem);
                    $imgReset = json_decode(htmlspecialchars_decode($adItem['img']));
                    $view->set('img', IMAGE_CDN_PATH .'/upload/other/' . $adItem['user_id'] . '/' . reset($imgReset));
                    $tmp->showPage('ad/apartment', '$' . $adItem['price'] . ' · ' .  $adItem['tag'], $ajax);
                }
                else if (isset($page['ad/other/'])) {
                    $adItem = $user->getLastAdOtherById($page['ad/other/']);
                    $view->set('ad', $adItem);
                    $imgReset = json_decode(htmlspecialchars_decode($adItem['img']));
                    $view->set('img', IMAGE_CDN_PATH .'/upload/other/' . $adItem['user_id'] . '/' . reset($imgReset));
                    $tmp->showPage('ad/other', $adItem['title'] . ' · ' .  $adItem['tag'], $ajax);
                }
                else {
                    if (!$ajax)
                        header("HTTP/1.1 404 Not Found");
                    $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                }
        }
    }
}