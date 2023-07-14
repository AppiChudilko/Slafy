<?php

namespace Server;

use Server\Core\EnumConst;
use Server\Core\Parsedown;
use Server\Core\QueryBuilder;
use Server\Core\Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Methods
 */
class Methods
{
    //

    public function showFeedBlock($id, $authorId, $authorLogin, $authorName, $authorAvatar, $content, $location, $likes, $comment, $dComment, $dLike, $timestamp, $imgList, $isLike = false) {
        global $userInfo;
        global $server;

        $content = $server->parseText($server->decodeString($content), true, true, true, true);

        if (trim($location) != '')
            $location .= ' · ';
        $imgContent = '<img class="card-image-feed-box" alt="' . $location . $authorName . '" src="' . IMAGE_CDN_PATH .'/upload/feed/' . $authorId . '/' . reset($imgList) . '">';

        if (count($imgList) > 1) {
            $imgSlider = '';
            foreach ($imgList as $img) {
                $imgSlider .= '
                    <div class="carousel-item black white-text">
                        <img style="height: auto" alt="' . $location . $authorName . '" src="' . IMAGE_CDN_PATH .'/upload/feed/' . $authorId . '/' . $img . '">
                    </div>
                ';
            }

            $imgContent = '
                <a onclick="$(\'#carousel-' . $id . '\').carousel(\'prev\');" class="carousel-arrow-left"><i class="material-icons-round white-text">arrow_back_ios</i></a>
                <a onclick="$(\'#carousel-' . $id . '\').carousel(\'next\');" class="carousel-arrow-right"><i class="material-icons-round white-text">arrow_forward_ios</i></a>
                <div id="carousel-' . $id . '" class="carousel carousel-slider center">
                    ' . $imgSlider . '
                </div>
            ';
        }

        $likeBtn = '<a class="btn-feed-like btn btn-floating waves-effect animated" id="feed-like-' . $id . '"><i class="material-icons-round ' . ($isLike ? 'red' : 'bw') . '-text">favorite</i></a>
                    <label class="card-label-count" id="feed-like-count-' . $id . '">' . $server->numberToKkk($likes) . '</label>';

        $commentBtn = '<a class="btn-feed-comment btn btn-floating waves-effect modal-trigger" href="#modalFeedComment"><i class="material-icons-round bw-text">chat</i></a>
                    <label class="card-label-count">' . $server->numberToKkk($comment) . '</label>';

        if ($dComment)
            $commentBtn = '<a class="btn-feed-comment btn btn-floating waves-effect" onclick="M.toast({html: \'Комментарии были отключены автором поста\', classes: \'rounded\'});"><i class="material-icons-round bw-text">chat</i></a>';

        if ($dLike)
            $likeBtn = '<a class="btn-feed-like btn btn-floating waves-effect animated" id="feed-like-' . $id . '"><i class="material-icons-round ' . ($isLike ? 'red' : 'bw') . '-text">favorite</i></a>';

        return '
            <script type="application/ld+json">
              {
                "@context": "http://schema.org",
                "@type": "Article",
                "headline": "' . $authorLogin .'",
                "author": "' . $authorLogin . '",
                "publisher": "' . $authorLogin . '",
                "datePublished": "' . gmdate('c', $timestamp)  . '",
                "image": [
                  "' . (IMAGE_CDN_PATH .'/upload/feed/' . $authorId . '/' . reset($imgList)) . '"
                ]
              }
            </script>
            <div id="feed-' . $id . '" data-feed="' . $id . '" class="card card-feed">
                <ul class="collection">
                    <li class="collection-item avatar">
                        <a class="bw-text" spa="@' . $authorLogin . '"><img style="background: rgba(0,0,0,0.5)" src="' . $authorAvatar . '" alt="' . $authorName . '" class="circle"></a>
                        <label class="title bw-text"><a class="bw-text" spa="@' . $authorLogin . '">' . $authorName . '</a></label><br>
                        <label class="grey-text label-location">' . $location . $server->timeStampToAgoUTC($timestamp) . '</label>
                        <a onclick="' . ($userInfo['id'] == $authorId ? '$(\'#modalFeedEitHash\').val(\'' . $id . '\')' : '') . '" href="#' . ($userInfo['id'] == $authorId ? 'modalFeedEdit' : 'modalFeedReport') . '" class="modal-trigger secondary-content"><i class="bw-text material-icons-round">more_horiz</i></a>
                    </li>
                </ul>
                <div class="card-image">                
                    ' . $imgContent . '
                </div>
                <div class="card-content ' . (empty(trim($content)) ? 'hide' : '') . '">
                    <label style="font-size: 1rem; overflow-wrap: anywhere">' . nl2br($content) . '</label>
                </div>
                <div class="card-action flex">
                    ' . $likeBtn . $commentBtn . '
                    <a class="btn btn-floating waves-effect" style="margin-left: auto;" onclick="$.copyTextToClipboard(\'https://adaptation-usa.com/uf/' . $id . '\', \'Ссылка на пост была скопирована\')"><i class="material-icons-round bw-text">share</i></a>
                </div>
            </div>
        ';
    }

    public function getFeedModals() {
        return '
            <div id="modalFeedReport" class="modal bottom-sheet">
                <div class="collection transparent center" style="margin-bottom: 50px">
                    <a onclick="M.toast({html: \'Жалоба была отправлена\', classes: \'rounded\'});" class="collection-item bw-text waves-effect modal-close">Пожаловаться</a>
                </div>
            </div>
            <div id="modalFeedEdit" class="modal bottom-sheet">
                <div class="collection transparent center" style="margin-bottom: 50px">
                    <a onclick="M.toast({html: \'Кнопка в разработке\', classes: \'rounded\'});" class="collection-item bw-text waves-effect modal-close">Редактировать</a>
                    <aform><input id="modalFeedEitHash" type="hidden" name="id"><a name="feed-delete" class="collection-item red-text waves-effect modal-close">Удалить</a></aform>
                </div>
            </div>
            <div id="modalFeedComment" class="modal bottom-sheet">
                <div class="container container-feed" style="margin-bottom: 50px">
                    <div style="height: 100%; padding-bottom: 100px">
                        <div style="padding: 24px 0" class="center">
                            Комментарии
                            <a class="modal-close right" onclick="$(\'#feed-comment-list\').html(\'\')" style="margin-top: -4px"><i class="material-icons-round bw-text">close</i></a>
                        </div>
                        <ul class="collection" id="feed-comment-list" style="border: none">
            
                        </ul>
                    </div>
                    <hr>
                    <aform class="flex card-panel" style="height: auto; position: fixed; bottom: 0; width: inherit; z-index: 1">
                        <div style="width: calc(100% - 40px);">
                            <input id="feed-comment-reply" type="hidden" name="reply-id" value="0">
                            <input id="feed-comment-id" type="hidden" name="id" value="">
                            <textarea data-original-height="30" data-enter-event="true" id="feed-comment-area" name="text" style="height: 100px" maxlength="200" data-length="200" class="materialize-textarea bw-text" placeholder="Текст комментария..."></textarea>
                        </div>
                        <button name="feed-send-comment" class="btn btn-floating waves-effect"><i class="material-icons-round bw-text">send</i></button>
                    </aform>
                </div>
            </div>
        ';
    }

    public function getUserStatusIcon($uInfo, $fontSize = 18) {
        if ($uInfo['is_verify'])
            return '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round blue-text text-accent-4 status-icon">verified</i>';
        if ($uInfo['id'] < 100000) {
            $color = 'blue-grey';
            if ($uInfo['id'] < 10000)
                $color = 'cyan';
            if ($uInfo['id'] < 1000)
                $color = 'green';
            if ($uInfo['id'] < 100)
                $color = 'amber';
            return '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round ' . $color . '-text text-accent-4 status-icon">rocket_launch</i>';
        }
        if ($uInfo['subscribe'] > time())
            return '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round purple-text text-accent-1 status-icon">auto_awesome</i>';
        if ($uInfo['is_tester'])
            return '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round brown-text text-accent-4 status-icon">bug_report</i>';
        if ($uInfo['is_content_maker'])
            return '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round cyan-text text-accent-4 status-icon">color_lens</i>';

        /*if ($uInfo['is_verify'])
            return '<div class="official blue accent-4"><i class="material-icons-round">done</i></div>';
        if ($uInfo['id'] < 100000) {
            $color = 'blue-grey';
            if ($uInfo['id'] < 10000)
                $color = 'cyan';
            if ($uInfo['id'] < 1000)
                $color = 'green';
            if ($uInfo['id'] < 100)
                $color = 'amber';
            return '<div class="official ' . $color . ' accent-4"><i class="material-icons-round">emoji_events</i></div>';
        }
        if ($uInfo['subscribe'] > time())
            return '<div class="official red accent-4"><i class="material-icons-round">favorite</i></div>';*/
        return '';
    }

    public function getUserStatusIconAll($uInfo, $fontSize = 18) {
        $icon = '';
        if ($uInfo['is_verify'])
            $icon .= '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round blue-text text-accent-4 status-icon">verified</i>';
        if ($uInfo['id'] < 100000) {
            $color = 'blue-grey';
            if ($uInfo['id'] < 10000)
                $color = 'cyan';
            if ($uInfo['id'] < 1000)
                $color = 'green';
            if ($uInfo['id'] < 100)
                $color = 'amber';
            $icon .= '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round ' . $color . '-text text-accent-4 status-icon">rocket_launch</i>';
        }
        if ($uInfo['subscribe'] > time())
            $icon .= '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round purple-text text-accent-1 status-icon">auto_awesome</i>';
        if ($uInfo['is_tester'])
            $icon .= '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round brown-text text-accent-4 status-icon">bug_report</i>';
        if ($uInfo['is_content_maker'])
            $icon .= '<i style="font-size: ' . $fontSize . 'px;" class="material-icons-round cyan-text text-accent-4 status-icon">color_lens</i>';

        /*if ($uInfo['is_verify'])
            return '<div class="official blue accent-4"><i class="material-icons-round">done</i></div>';
        if ($uInfo['id'] < 100000) {
            $color = 'blue-grey';
            if ($uInfo['id'] < 10000)
                $color = 'cyan';
            if ($uInfo['id'] < 1000)
                $color = 'green';
            if ($uInfo['id'] < 100)
                $color = 'amber';
            return '<div class="official ' . $color . ' accent-4"><i class="material-icons-round">emoji_events</i></div>';
        }
        if ($uInfo['subscribe'] > time())
            return '<div class="official red accent-4"><i class="material-icons-round">favorite</i></div>';*/
        return $icon;
    }

    public function getDialogListItem($uDialogInfo, $prefixPage, $newCount, $timestamp, $message, $readStatus, $isPin = false) {
        global $user;
        global $methods;
        global $server;
        global $UTC_TO_TIME;
        $uIsOnline = $uDialogInfo['online_status'] > ($server->timeStampNow() - 60);
        $time = '';
        if ($timestamp > 0) {
            if (gmdate('d', $timestamp + $UTC_TO_TIME) != gmdate('d', $server->timeStampNow() + $UTC_TO_TIME))
                $time = $server->timeStampToDateFormat($timestamp, false);
            else
                $time = $server->timeStampToTime($timestamp);
        }
        return '
            <li id="dialog-id-' . $uDialogInfo['id'] . '" class="collection-item avatar ' . ($isPin ? 'message-pin' : '') . '">
                <a class="message-a-block" spa="im/' . ($prefixPage == '' ? '' : $prefixPage . '/') . $uDialogInfo['id'] . '">
                    <div class="message-user-online green ' . ($uIsOnline ? '' : 'hide') . '"></div>
                    <img src="' . $user->getUserAvatar($uDialogInfo['id'], $uDialogInfo['avatar']) . '" alt="" class="circle">
                    <span class="title flex bw-text message-preview">' . $user->getUserName($uDialogInfo['login'], $uDialogInfo['name']) . ' ' . $methods->getUserStatusIcon($uDialogInfo) . '</span>
                    <p class="grey-text message-preview">' . $message . '</p>
                </a>
                <label class="message-time ' . ($timestamp > 0 ? '' : 'hide') . '"><i class="material-icons-round ' . $readStatus . '">done_all</i> ' . $time . '</label>
                <label class="message-new ' . ($newCount > 0 ? '' : 'hide') . '">' . $newCount . '</label>
            </li>
        ';
    }

    public function getMessageRecived($id, $text, $reaction, $timestamp, $isRead = false) {
        global $UTC_TO_TIME;
        global $server;
        $time = '';
        if (gmdate('d', $timestamp + $UTC_TO_TIME) != gmdate('d', $server->timeStampNow() + $UTC_TO_TIME))
            $time = $server->timeStampToDateFormat($timestamp, false) . ' ' . $server->timeStampToTime($timestamp);
        else
            $time = $server->timeStampToTime($timestamp);
        return '
            <div data-message-id="' . $id . '" class="message-chat-line">
                <div class="message-chat-received">
                    <div class="message-chat-box">
                        ' . nl2br($server->parseText($text, true, true, true, true)) . '
                    </div>
                </div>
                <label class="message-chat-time">' . $time . ' <label class="message-react">️' . $reaction . '</label>
                    <i data-message-id="' . $id . '" class="message-reply material-icons-round grey-text hide">reply</i>
                    ' . ($isRead ? '' : '<i class="message-new-icon message-reply material-icons-round grey-text">fiber_new</i>') . '
                </label>
            </div>
        ';
    }

    public function getMessageSent($id, $text, $reaction, $timestamp, $isRead) {
        global $UTC_TO_TIME;
        global $server;
        $time = '';
        if (gmdate('d', $timestamp + $UTC_TO_TIME) != gmdate('d', $server->timeStampNow() + $UTC_TO_TIME))
            $time = $server->timeStampToDateFormat($timestamp, false) . ' ' . $server->timeStampToTime($timestamp);
        else
            $time = $server->timeStampToTime($timestamp);
        return '
            <div data-message-id="' . $id . '" class="message-chat-line">
                <div class="message-chat-send">
                    <div class="message-chat-box">
                        ' . nl2br($server->parseText($text, true, true, true, true)) . '
                    </div>
                </div>
                <label class="message-chat-time message-chat-time-right">' . $time . ' <label class="message-react">️' . $reaction . '</label>
                    <i class="message-read-icon material-icons-round ' . ($isRead ? 'blue-text' : '') . '">done_all</i>
                    <i data-message-id="' . $id . '" class="message-reply material-icons-round grey-text hide">reply</i>
                </label>
            </div>
        ';
    }

    public function getOnlineBlock($timestamp, $lastHidden = false) {
        global $server;
        $uIsOnline = $timestamp > ($server->timeStampNow() - 60);
        if ($uIsOnline)
            return 'В сети';
        if ($lastHidden)
            return 'Был(а) в сети недавно';
        return 'Был(а) в сети ' . $server->timeStampToAgoUTC($timestamp);
    }

    public function getAdBlockType1($id, $dType, $title, $desc, $content, $tag, $uId, $uLogin, $uName, $uAvatar, $clBtn, $timestamp, $engId, $cityId, $isSearch) {
        global $cities;
        global $englishLevel;
        global $server;
        global $user;
        global $userInfo;

        $labelTag = ($isSearch ? 'Поиск' : 'Вакансия');
        if ($dType == 'service')
            $labelTag = ($isSearch ? 'Ищу услугу' : 'Предлагаю услугу');

        $btnItem1 = '<a spa="im/' . $uId . '" style="margin-left: auto;" class="btn hide-on-med-and-down btn-small blue accent-4 white-text z-depth-s waves-effect animated slafy-anim">Написать</a>';
        $btnItem2 = '<a spa="im/' . $uId . '" style="margin: auto; display: none; margin-top: 16px;" class="btn show-on-medium-and-down btn-small blue accent-4 white-text z-depth-s waves-effect animated slafy-anim">Написать</a>';

        if ($uId == $userInfo['id']) {
            $btnItem1 = '
                <form method="post" style="margin-left: auto;">
                    <a onclick="M.toast({html: \'Кнопка в разработке и скоро будет доступна, а пока можете удалить и отправить заново\', classes: \'rounded\'});" class="btn hide-on-med-and-down btn-small blue white-text z-depth-s waves-effect animated slafy-anim">Редактировать</a>
                
                    <input type="hidden" name="id" value="' . $id . '">
                    <button name="delete-ad-' . $dType . '" class="btn hide-on-med-and-down btn-small red white-text z-depth-s waves-effect animated slafy-anim">Удалить</button>
                </form>
            ';
            $btnItem2 = '
                <form method="post" class="show-on-medium-and-down" style="margin: auto; display: none; margin-top: 16px;">
                    <a onclick="M.toast({html: \'Кнопка в разработке и скоро будет доступна, а пока можете удалить и отправить заново\', classes: \'rounded\'});" class="btn btn-small blue white-text z-depth-s waves-effect animated slafy-anim">Редактировать</a>
                
                <input type="hidden" name="id" value="' . $id . '">
                    <button name="delete-ad-' . $dType . '" class="btn btn-small red white-text z-depth-s waves-effect animated slafy-anim">Удалить</button>
                </form>
            ';
        }

        return '
<div class="card-panel">
    <span class="flex" style="margin-bottom: 4px">
        <a spa="@' . $uLogin . '"><img class="circle" style="width: 45px; height: 45px;  margin: 6px; margin-right: 12px" src="' . $user->getUserAvatar($uId, $uAvatar) . '"></a>
        <a spa="@' . $uLogin . '"><h5 class="bw-text">' . ($uName ?: ucfirst($uLogin)) . '</h5></a>
        ' . $btnItem1 . '
    </span>
    <hr>
    <div>
        <br>
        <div style="font-size: 1.4rem"><b>' . $title . '</b></div>
        <br>
    </div>
    <span>' . nl2br($server->parseText($server->decodeString($content), true, true, true, true)) . '</span>
    <br>
    <br>
    <label>' . $cities[$cityId][0] . ' · ' . $server->timeStampToAgoUTC($timestamp) . '</label>
    ' . $btnItem2 . '
    <div style="margin-top: 16px">
        <a spa="search?' . $dType . '&search=' . $isSearch . '" style="margin-left: 0" class="blocktest ' . ($isSearch ? 'blue' : 'green') . '">' . $labelTag . '</a>
        <a spa="search?' . $dType . '&en=' . $engId . '" class="blocktest teal">' . $englishLevel[$engId] . '</a>
        <a spa="search?' . $dType . '&q=' . $tag . '" class="blocktest amber">' . $tag . '</a>
    </div>
</div>
        ';

        return '
            <li>
                <div class="collapsible-header z-depth-s">
                    <i style="margin-top: 12px" class="material-icons-round ' . ($isSearch ? 'blue' : 'green') . '-text">' . ($isSearch ? 'travel_explore' : 'public') . '</i>
                    <div>
                        <span style="font-size: 1.4rem">' . $title . '</span>
                        <br>
                        <label>
                            ' . $desc . ' · ' . $cities[$cityId][0] . '
                        </label>
                        <span class="show-on-medium-and-down" style="display: none; margin-top: 8px; width: 100%;">
                            <a class="blocktest amber">' . $tag . '</a>
                            <a class="blocktest teal">' . $englishLevel[$engId] . '</a>
                            <a class="blocktest ' . ($isSearch ? 'blue' : 'green') . '">' . $labelTag . '</a>
                        </span>
                    </div>
                    <span class="hide-on-med-and-down" style="margin-left: auto">
                        <a class="blocktest amber">' . $tag . '</a>
                        <a class="blocktest teal">' . $englishLevel[$engId] . '</a>
                        <a class="blocktest ' . ($isSearch ? 'blue' : 'green') . '">' . $labelTag . '</a>
                    </span>
                </div>
                <div class="collapsible-body">
                    <div class="card-panel" style="margin: 0">
                        <span class="flex" style="margin-bottom: 18px">
                            <a spa="@' . $uLogin . '"><img class="circle" style="width: 45px; height: 45px;  margin: 6px; margin-right: 12px" src="' . $user->getUserAvatar($uId, $uAvatar) . '"></a>
                            <a spa="@' . $uLogin . '"><h5 class="bw-text">' . ($uName ?: $uLogin) . '</h5></a>
                            ' . $btnItem1 . '
                        </span>
                        <span>' . nl2br($server->parseText($server->decodeString($content), true, true, true, true)) . '</span>
                        <br>
                        <br>
                        <label>' . $cities[$cityId][0] . ' · ' . $englishLevel[$engId] . ' · ' . $server->timeStampToAgoUTC($timestamp) . '</label>
                        ' . $btnItem2 . '
                    </div>
                </div>
            </li>
        ';
    }

    public function getAdBlockType2($url, $title, $price, $location, $type, $authorName, $authorId, $imgList) {
        $imgContent = '<img style="object-fit: cover; position: absolute; top: 0; height: 100%" alt="' . $location . ' - '. $authorName . '" src="' . IMAGE_CDN_PATH .'/upload/apartment/' . $authorId . '/' . reset($imgList) . '">';
        return '
            <a spa="' . $url . '" style="display: block" itemscope itemtype="https://schema.org/RentAction" class="card">
                <div style="position: relative; padding-top: 56.25%;" class="card-image">
                    ' . $imgContent . '
                    <span class="card-title">
                    ' . $title . '<br>
                    <label class="white-text">' . $location . '</label>
                    </span>
                </div>
                <div class="card-action">
                    <div class="btn grey-text" style="padding-left: 0">' . $type . '</div>
                    <div class="btn green-text right" style="padding-right: 0">' . $price . '</div>
                </div>
                <div class="hide">
                    <span itemprop="@person">John</span>
                </div>
            </a>
        ';
    }

    public function getAdBlockType3($url, $title, $price, $location, $type, $authorName, $authorId, $imgList) {
        $imgContent = '<img style="object-fit: cover; position: absolute; top: 0; height: 100%" alt="' . $location . ' - '. $authorName . '" src="' . IMAGE_CDN_PATH .'/upload/other/' . $authorId . '/' . reset($imgList) . '">';
        return '
            <a spa="' . $url . '" style="display: block; " class="card">
                <div style="position: relative; padding-top: 56.25%;" class="card-image">
                    ' . $imgContent . '
                    <span class="card-title">
                    ' . $title . '<br>
                    <label class="white-text">' . $location . '</label>
                    </span>
                </div>
                <div class="card-action">
                    <div class="btn grey-text" style="padding-left: 0">' . $type . '</div>
                    <div class="btn green-text right" style="padding-right: 0">' . $price . '</div>
                </div>
            </a>
        ';
    }

    public function showError404($padding = 7) {
        return '
            <div class="row" style="padding: ' . $padding . 'rem 0">
                <div class="col s12 center"></div>
                <div class="col s12 center"><img style="max-width: 300px; width: 100%; height: 100%;" src="' . IMAGE_CDN_PATH . '/client/images/stickers/512/404.png"></div>
                <div class="col s12">
                    <h5 class="grey-text center" style="margin-top: 0">
                        Ой, ты потерялся, такой страницы не существует!
                    </h5>
                </div>
            </div>
        ';
    }

    public function showError($message, $padding = 5, $sticker = 'preduprezhdenie') {
        return '
            <div id="error-block" class="row" style="padding: ' . $padding . 'rem 0; margin: 0; width: 100%">
                <div class="col s12 center"></div>
                <div class="col s12 center"><img style="margin: auto; max-width: 300px; width: 100%; height: 100%;" src="' . IMAGE_CDN_PATH . '/client/images/stickers/512/' . $sticker . '.png"></div>
                <div class="col s12">
                    <h5 class="grey-text center" style="margin: auto; margin-top: 0;">
                        ' . $message . '
                    </h5>
                </div>
            </div>
        ';
    }

    public function showFooter() {
        return '
            <footer class="page-footer transparent">
                <div class="footer-copyright transparent">
                    <div class="container grey-text text-darken-2">
                        <div class="row">
                            <div class="col s12 m4">
                                Copyright © 2022  <a class="grey-text text-darken-2" spa="@levi">Adaptation USA Team</a>
                            </div>
                            <div class="col s12 m4 center-align links hide-on-small-and-down">
                            </div>
                            <div class="col s12 m4 hide-on-small-and-down">
                                <a class="grey-text text-darken-2 right">With love <i class="material-icons red-text" style="font-size: 14px;">favorite</i></a>
                            </div>
                            <div class="col s12 m4 show-on-small" style="display: none">
                                <a class="grey-text text-darken-2">With love <i class="material-icons red-text" style="font-size: 14px;">favorite</i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        ';
    }

    public function cToTextCl($color) {
        return match ($color) {
            'yellow', 'amber', 'lime', 'white' => 'black-text',
            default => 'white-text',
        };
    }

    public function showAdminLeftMenu() {
        return '
            <div class="admin-left" style="padding-top: 32px">
                <div class="collection z-depth-0">
                    <a href="/rainbow" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">home</i>
                        Главная
                    </a>
                    <a href="/rainbow/orders" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">fact_check</i>
                        Заказы
                    </a>
                    <a href="/rainbow/upload" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">texture</i>
                        Текстуры
                    </a>
                    <a href="/rainbow/stock" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">inventory</i>
                        Товары
                    </a>
                    <a href="/rainbow/paint" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">palette</i>
                        Покраска
                    </a>
                    <a href="/rainbow/repair" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">build</i>
                        Ремонт
                    </a>
                    <a href="/rainbow/stats" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">equalizer</i>
                        Статистика
                    </a>
                    <a href="/rainbow/finance" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">account_balance</i>
                        Финансы
                    </a>
                    <a href="/rainbow/logs" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">code</i>
                        Логи
                    </a>
                    <a href="/rainbow/users" class="collection-item flex grey-text text-darken-1">
                        <i class="material-icons">people</i>
                        Пользователи
                    </a>
                </div>
            </div>
        ';
    }
}