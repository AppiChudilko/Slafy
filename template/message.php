<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $userInfo;
global $page;
global $methods;
global $server;

$isDialogShow = false;
if (isset($page['im/'])) {
    if ($page['im/'] != 'request' && $page['im/'] != 'archive' && $page['im/'] != 'hidden')
        $isDialogShow = true;
    if (isset($page['im/request/']) || isset($page['im/archive/']) || isset($page['im/hidden/']))
        $isDialogShow = true;
}

$notifyCountDialogRequest = 0;
$dialogList = $user->getDialogList(0, 20, 99);
foreach ($dialogList as $item)
    $notifyCountDialogRequest++;
$notifyCountDialogRequest = $notifyCountDialogRequest > 0 ? ($notifyCountDialogRequest > 99 ? '99+' : $notifyCountDialogRequest) : '';
$dialogList = null;

$dialogType = -1;
$pagePrefix = '';
if (isset($page['im/']) && $page['im/'] == 'request') {
    $pagePrefix = 'request';
    $dialogType = 99;
}
if (isset($page['im/']) && $page['im/'] == 'archive') {
    $pagePrefix = 'archive';
    $dialogType = 2;
}
if (isset($page['im/']) && $page['im/'] == 'hidden') {
    $pagePrefix = 'hidden';
    $dialogType = 3;
}
?>
<style>
    .ddw {
        min-width: 220px;
    }
    .ddw2 {
        width: 299px !important;
    }
</style>
<script>
    $('#notify-d-indicator').attr('data-notify-count', 0);
    $('.notify-d-indicator').addClass('hide');
</script>
<div class="container container-full-mobile container-padding container-padding-msg">
    <div class="section" style="padding: 0">
        <div class="row" style="margin: 0;">
            <div class="col s12 no-mar-s no-pad-s">
                <div class="card flex" style="margin: 0">
                    <ul class="collection message-dialog-list <?php echo $isDialogShow ? 'hide-on-small-and-down' : '' ?>">
                        <div class="flex message-search-panel">
                            <a class="show-on-small" style="display: none" spa="feed"><i class="material-icons-round bw-text">home</i></a>

                            <input style="margin: 0;" id="input-dialog-list-search" onkeyup="$.searchDialog()" placeholder="Поиск по логину" type="search" name="q" required="" value="">
                            <a class="hide" spa="im"><i class="material-icons-round bw-text">visibility_off</i></a>
                            <a class="hide" spa="im"><i class="material-icons-round bw-text">archive</i></a>
                            <a class="hide" spa="im">
                                <i class="material-icons-round bw-text">person_add</i>
                            </a>
                            <a href="#" data-target="dropdownDialogFilter" class="dropdown-trigger dropdown-btn">
                                <div class="red nav-notify-indicator <?php echo $notifyCountDialogRequest == '' ? 'hide' : '' ?>"><?php echo $notifyCountDialogRequest ?></div>
                                <i class="material-icons-round bw-text">menu</i>
                            </a>
                        </div>
                        <div id="dialog-main-list">
                        <?php
                        $dialogListItem = $user->getDialogList(0, 15, $dialogType);
                        foreach ($dialogListItem as $item) {
                            $uDialogId = $item['uid1'] == $userInfo['id'] ? $item['uid2'] : $item['uid1'];
                            $userDialogListInfo = $user->getUserInfoById($uDialogId);
                            $dlm = $user->getDialogLastMessage($item['id']);
                            $readStatus = 'hide';
                            $newCount = $user->getDialogUnRead($item['id']);
                            if ($dlm['user_id'] == $userInfo['id'])
                                $readStatus = $dlm['is_read'] ? 'blue-text' : '';
                            echo $methods->getDialogListItem($userDialogListInfo, $pagePrefix, $newCount, $item['last_update'], $dlm['text'], $readStatus, $item['type'] == 1);
                        }
                        if (empty($dialogListItem))
                            echo '<h5 class="grey-text center">Список пуст</h5>';
                        ?>
                        </div>
                        <div class="hide" id="dialog-search-list">
                        </div>
                    </ul>
                    <div class="message-dialog-message-list" <?php echo $isDialogShow ? 'style="display: block !important"' : '' ?>>


<?php
if (
    isset($page['im/']) && is_numeric($page['im/']) ||
    isset($page['im/request/']) && is_numeric($page['im/request/']) ||
    isset($page['im/archive/']) && is_numeric($page['im/archive/']) ||
    isset($page['im/hidden/']) && is_numeric($page['im/hidden/'])
) {
    $userDialogId = $userInfo['id'];
    if (isset($page['im/']))
        $userDialogId = intval($page['im/']);
    if (isset($page['im/request/']))
        $userDialogId = intval($page['im/request/']);
    if (isset($page['im/archive/']))
        $userDialogId = intval($page['im/archive/']);
    if (isset($page['im/hidden/']))
        $userDialogId = intval($page['im/hidden/']);
    $dialogItem = $user->getDialogByUserId($userDialogId);
    $userDialogInfo = $user->getUserInfoById($userDialogId);
    $uIsOnline = $userDialogInfo['online_status'] > ($server->timeStampNow() - 60);
    if (!empty($dialogItem))
    {
        echo '
        <a data-target="dropdownChatMenu" style="position: absolute; top: 19px; right: 16px; z-index: 1;" class="dropdown-trigger dropdown-btn secondary-content bw-text hide-on-small-and-down"><i class="material-icons-round">more_vert</i></a>
        <ul id="dropdownChatMenu" class="dropdown-content ddw">
            <li class="hide"><a href="#!"><i class="material-icons-round">attachment</i>Вложения</a></li>
            <aform>
                <li>
                    <input type="hidden" name="uid" value="' . $userDialogId . '">
                    <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                    <input type="hidden" name="type" value="0">
                    <a name="dialog-update-settings"><i class="material-icons-round">forum</i>Переместить в основное</a>
                </li>
            </aform>
            <aform>
                <li>
                    <input type="hidden" name="uid" value="' . $userDialogId . '">
                    <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                    <input type="hidden" name="type" value="1">
                    <a name="dialog-update-settings"><i class="material-icons-round">push_pin</i>Закрепить</a>
                </li>
            </aform>
            <aform>
                <li>
                    <input type="hidden" name="uid" value="' . $userDialogId . '">
                    <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                    <input type="hidden" name="type" value="2">
                    <a name="dialog-update-settings"><i class="material-icons-round">archive</i>Архивировать</a>
                </li>
            </aform>
            <aform>
                <li>
                    <input type="hidden" name="uid" value="' . $userDialogId . '">
                    <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                    <input type="hidden" name="type" value="3">
                    <a name="dialog-update-settings"><i class="material-icons-round">visibility_off</i>Скрыть</a>
                </li>
            </aform>
            <li class="hide"><a href="#!"><i class="material-icons-round">color_lens</i>Сменить тему</a></li>
            <li class="divider" tabindex="-1"></li>
            <li><a class="modal-trigger" href="#modalChatMenuDel"><i class="material-icons-round red-text">delete</i>Удалить для всех</a></li>
        </ul>
        <div id="modalChatMenu" class="modal bottom-sheet">
            <div class="collection transparent center" style="margin-bottom: 50px">
                <a href="#!" class="collection-item bw-text waves-effect hide">Вложения</a>
            <aform>
                <input type="hidden" name="uid" value="' . $userDialogId . '">
                <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                <input type="hidden" name="type" value="0">
                <a name="dialog-update-settings" class="collection-item bw-text waves-effect">Переместить в основное</a>
            </aform>
            <aform>
                <input type="hidden" name="uid" value="' . $userDialogId . '">
                <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                <input type="hidden" name="type" value="1">
                <a name="dialog-update-settings" class="collection-item bw-text waves-effect">Закрепить</a>
            </aform>
            <aform>
                <input type="hidden" name="uid" value="' . $userDialogId . '">
                <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                <input type="hidden" name="type" value="2">
                <a name="dialog-update-settings" class="collection-item bw-text waves-effect">Архивировать</a>
            </aform>
            <aform>
                <input type="hidden" name="uid" value="' . $userDialogId . '">
                <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                <input type="hidden" name="type" value="3">
                <a name="dialog-update-settings" class="collection-item bw-text waves-effect">Скрыть диалог</a>
            </aform>
                <a href="#!" class="collection-item bw-text waves-effect hide">Сменить тему</a>
                <a href="#modalChatMenuDel" class="modal-trigger modal-close collection-item red-text waves-effect">Удалить для всех</a>
            </div>
        </div>
        
        <div id="modalChatMenuDel" class="modal bottom-sheet">
            <div class="collection transparent center" style="margin-bottom: 50px">
                <a class="collection-item bw-text waves-effect modal-close">Нет, стой!</a>
                <aform>
                    <input type="hidden" name="id" value="' . $dialogItem['id'] . '">
                    <a name="dialog-delete" class="collection-item red-text waves-effect">Удалить безвозвратно</a>
                </aform>
            </div>
        </div>';
    }
    echo '
        <div class="collection message-dialog-info">
            <a spa="im' . ($pagePrefix == '' ? '' : '/' . $pagePrefix) . '" style="position: absolute; top: 19px; left: 16px; z-index: 1;  display: none" class="secondary-content bw-text show-on-small"><i class="material-icons-round">arrow_back_ios</i></a>
            <a href="#modalChatMenu" style="position: absolute; top: 19px; right: 16px; z-index: 1; display: none" class="modal-trigger secondary-content bw-text dialog-menu-action show-on-small"><i class="material-icons-round">more_vert</i></a>
            <div class="collection-item avatar message-dialog-info-margin">
                <a class="message-a-block" spa="@' . $userDialogInfo['login'] . '">
                    <div class="message-user-online green ' . ($uIsOnline ? '' : 'hide') . '"></div>
                    <img src="' . $user->getUserAvatar($userDialogInfo['id'], $userDialogInfo['avatar']) . '" alt="" class="circle">
                    <span class="title flex bw-text message-preview">' . $user->getUserName($userDialogInfo['login'], $userDialogInfo['name']) . $methods->getUserStatusIcon($userDialogInfo) . '</span>
                    <p id="message-user-online-status" class="grey-text message-preview">' . $methods->getOnlineBlock($userDialogInfo['online_status'], $userDialogInfo['online_hidden']) . '</p>
                </a>
            </div>
        </div>
        <div class="message-dialog-chat">
    ';
    if (!empty($dialogItem)) {
        $messageList = $user->getDialogMessages($dialogItem['id']);
        $user->updateDialogRead($dialogItem['id'], $dialogItem['uid1'] == $userInfo['id'] ? $dialogItem['uid2'] : $dialogItem['uid1']);
        foreach ($messageList as $item) {
            if ($item['user_id'] == $userInfo['id'])
                echo $methods->getMessageSent($item['id'], $item['text'], $item['reaction'], $item['timestamp'], $item['is_read']);
            else
                echo $methods->getMessageRecived($item['id'], $item['text'], $item['reaction'], $item['timestamp'], $item['is_read']);

        }
    }
    echo '        
        </div>
        <aform class="message-dialog-input">
            <input type="hidden" name="reply-id" value="0">
            <input type="hidden" name="id" value="' . $userDialogInfo['id'] . '">
            <span onclick="M.toast({html: \'Очень скоро будет доступно!\', classes: \'rounded\'});" class="message-btn-send"><i class="material-icons-round bw-text">image</i></span>
            <textarea name="text" data-enter-event="true" onkeyup="$.lpKp()" class="lp-keyup message-dialog-area materialize-textarea bw-text" placeholder="Введите сообщение..."></textarea>
            <span onclick="M.toast({html: \'Очень скоро будут доступны стикеры как в Telegram <3\', classes: \'rounded\'});" class="message-btn-send"><i class="material-icons-round bw-text">face_retouching_natural</i></span>
            <a name="send-message" class="message-btn-send"><i class="material-icons-round bw-text">send</i></a>
        </aform>
    ';
}
else
    echo $methods->showError('Выбери диалог чтобы начать общение',0, 'spravka');
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<ul id="dropdownDialogFilter" class="dropdown-content ddw2">
    <li><a spa="im"><i class="material-icons-round">chat</i>Все диалоги</a></li>
    <li><a spa="im/request"><i class="material-icons-round">person_add</i>Запросы <span class="badge red white-text <?php echo $notifyCountDialogRequest == '' ? 'hide' : '' ?>"><?php echo $notifyCountDialogRequest ?></span></a></li>
    <li><a spa="im/archive"><i class="material-icons-round">archive</i>Архив</a></li>
    <li><a spa="im/hidden"><i class="material-icons-round">visibility_off</i>Скрытые диалоги</a></li>
    <li class="divider" tabindex="-1"></li>
    <li><a><i class="material-icons-round red-text">close</i>Закрыть</a></li>
</ul>