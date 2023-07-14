<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $userInfo;
global $page;
global $user;
global $qb;

$isMy = isset($_GET['my']);

$notifyCount = 0;
$notifyCountDialog = 0;
if ($user->isLogin()) {
    $resultNotify = $qb->createQueryBuilder('notify')->selectSql()->where('user_id = ' . $userInfo['id'])->andWhere('is_read = 0')->orderBy('id DESC')->limit(101)->executeQuery()->getResult();
    $notifyCount = count($resultNotify);
    $resultNotify = null;
}
if ($user->isLogin()) {
    $dialogList = $user->getDialogList(0, 20, -99);
    foreach ($dialogList as $item) {
        if ($item['type'] == 99)
            $notifyCountDialog++;
        else
            $notifyCountDialog += $user->getDialogUnRead($item['id']);
    }
    $dialogList = null;
}

$enableLightTheme = !isset($_COOKIE['slafy-is-light']);
//if ($user->isLogin()) {
    //if ($userInfo['is_light_theme'])
    //    $enableLightTheme = true;
//}
?>
<!--

Фея Винкс всегда на страже вашей страницы

``````````{\
````````{\{*\
````````{*\~\__&&&
```````{```\`&&&&&&.
``````{~`*`\((((((^^^)
`````{`*`~((((((( ♛ ♛
````{`*`~`)))))))). _' )
````{*```*`((((((('\ ~
`````{~`*``*)))))`.&
``````{.*~``*((((`\`\)) ?
````````{``~* ))) `\_.-'``
``````````{.__ ((`-*.*
````````````.*```~``*.
``````````.*.``*```~`*.
`````````.*````.````.`*.
````````.*``~`````*````*.
```````.*``````*`````~``*.
`````.*````~``````.`````*.
```.*```*```.``~```*``~ *.¤´҉ .

-->
<!DOCTYPE html>
<html lang="<?php echo $this->langType ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title><?php echo $this->titleHtml; ?></title>

    <meta property="og:title" content="<?php echo $this->titleHtml; ?>" />
    <meta property="og:description" content="Это сайт, где собаранна вся информация о том, чтобы комфортно начать свою жизнь в США, от полезных советов до поиска работы и недвижимости" />
    <meta property="og:site_name" content="<?php echo $this->title; ?>">
    <meta property="og:type" content="social">
    <meta property="og:url" content="<?php echo $this->siteName ?>">
    <meta property="og:image" content="<?php echo $this->img?:'https://i.imgur.com/BUUmC0X.png' ?>">

    <meta name="description" content="Это сайт, где собаранна вся информация о том, чтобы комфортно начать свою жизнь в США, от полезных советов до поиска работы и недвижимости">
    <meta name="keywords" content="usa" />
    <meta name="generator" content="Appi <?php echo $this->version ?>">

    <link rel="shortcut icon" href="https://i.imgur.com/BUUmC0X.png" type="image/x-icon" />
    <link rel="apple-touch-icon" href="https://i.imgur.com/BUUmC0X.png">

    <meta id="theme-color" name="theme-color" content="<?php echo ($enableLightTheme ? '#fff' : '#000' ) ?>">

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paste.js/0.0.18/paste.min.js"></script>
    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Round" rel="stylesheet">

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">


    <link href="/client/css/material-charts.css" rel="stylesheet" media="screen,projection">
    <link href="/client/css/material-appi.css?<?php echo time() ?>" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/slider.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/animate.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/main.css?<?php echo time() ?>" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/extended.css?<?php echo time() ?>" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link id="theme-css" href="/client/css/<?php echo ($enableLightTheme ? 'light' : 'dark' ) ?>-theme.css?<?php echo time() ?>" type="text/css" rel="stylesheet" media="screen,projection"/>

    <style>
        td, th {
            padding: 5px 15px;
        }
    </style>
</head>
<body>
<div class="backdrop-blur nav-mobile-main hide-on-med-and-down">
    <nav class="bw-text z-depth-s2" role="navigation">
        <div class="nav-wrapper container flex">
            <a href="#" data-target="slide-out" style="line-height: 64px;" class="sidenav-trigger bw-text"><i style="line-height: 64px;" class="material-icons-round">menu</i></a>
            <a id="logo-container" spa="<?php echo $user->isLogin()?'feed':'index' ?>" class="brand-logo hide-on-med-and-down" style="width: 70px">
                <img src="/client/images/logo/svg/logoUsa.svg" class="logo" style="margin: 17px; height: 30px;" alt="Slafy Logo">
            </a>
            <form action="/search" class="search-input hide" style="width: calc(100% - 600px); margin: auto">
                <div class="input-field">
                    <input id="search" placeholder="Поиск" type="search" name="q" required="" value="">
                    <label class="label-icon active" style="font-size: 0px;" for="search"><i class="search-i material-icons-round grey-text">search</i></label>
                </div>
            </form>
            <ul class="hide-on-med-and-down" style="line-height: 64px; margin-left: auto">
                <li><a class="bw-text tooltipped" id="hover" data-position="bottom" data-tooltip="Главная" spa="search?work"><i class="material-icons-round">home</i></a></li>
                <li><a class="bw-text tooltipped <?php echo $user->isLogin()?:'hide' ?>" id="hover" data-position="bottom" data-tooltip="Лента" spa="feed"><i class="material-icons-round">feed</i></a></li>
                <li><a class="bw-text tooltipped hide" id="hover" data-position="bottom" data-tooltip="Объявления" spa="search?work"><i class="material-icons-round">badge</i></a></li>
                <li><a class="bw-text tooltipped <?php echo $user->isLogin()?:'hide' ?>" id="hover" data-position="bottom" data-tooltip="Сообщения" spa="im"><div data-notify-count="<?php echo $notifyCountDialog; ?>" id="notify-d-indicator" class="notify-d-indicator red nav-notify-indicator <?php echo $notifyCountDialog > 0 ? '' : 'hide'; ?>"><?php echo $notifyCountDialog < 99 ? $notifyCountDialog : '99+'; ?></div><i class="material-icons-round">forum</i></a></li>
                <li><a class="bw-text tooltipped <?php echo $user->isLogin()?:'hide' ?>" id="hover" data-position="bottom" data-tooltip="Уведомления" spa="notify"><div data-notify-count="<?php echo $notifyCount; ?>" id="notify-indicator" class="notify-indicator red nav-notify-indicator <?php echo $notifyCount > 0 ? '' : 'hide'; ?>"><?php echo $notifyCount < 99 ? $notifyCount : '99+'; ?></div><i class="material-icons-round">favorite</i></a></li>
                <li><a class="bw-text tooltipped modal-trigger <?php echo $user->isLogin()?:'hide' ?>" data-position="bottom" data-tooltip="Новая публикация" href="#modalUpload" id="hover"><i class="material-icons-round">add_box</i></a></li>
                <li><a class="bw-text tooltipped modal-trigger <?php echo !$user->isLogin()?:'hide' ?>" data-position="bottom" data-tooltip="Новая публикация" spa="login" id="hover"><i class="material-icons-round">add_box</i></a></li>
                <li><a class="bw-text tooltipped modal-trigger <?php echo $user->isLogin()?'hide':'' ?>" data-position="bottom" data-tooltip="Авторизация" spa="login" id="hover"><i class="material-icons-round">account_circle</i></a></li>
                <li><a class="bw-text tooltipped <?php echo $user->isLogin()?:'hide' ?>" id="hover" data-position="bottom" data-tooltip="Профиль" spa="@<?php echo $userInfo['login'] ?>"><i class="material-icons-round">account_circle</i></a></li>
            </ul>
        </div>
    </nav>
</div>
<div class="wb nav-mobile-main show-on-medium-and-down" style="position absolute; height: 100px; top: -100px;"></div>
<div id="nav-top-menu" class="backdrop-blur nav-mobile-main show-on-medium-and-down" style="display: none;">
    <nav style="height: 50px;" class="bw-text z-depth-s2" role="navigation">
        <div class="nav-wrapper container flex">
            <a id="logo-container" spa="<?php echo $user->isLogin()?'feed':'search?work' ?>" class="brand-logo flex" style="width: 70px; height: 50px">
                <img src="/client/images/logo/svg/logoUsa.svg" class="logo" style="margin: 10px; height: 30px;" alt="Slafy Logo">
            </a>
            <ul style="line-height: 50px; margin-left: auto">
                <li><a class="bw-text <?php echo $user->isLogin()?:'hide' ?>" spa="feed"><i style="line-height: 50px" class="material-icons-round">feed</i></a></li>
                <li><a class="bw-text <?php echo $user->isLogin()?:'hide' ?>" spa="im"><div data-notify-count="<?php echo $notifyCountDialog; ?>" id="notify-d-indicator" class="red nav-notify-d-indicator-mob <?php echo $notifyCount > 0 ? '' : 'hide'; ?>"><?php echo $notifyCount < 99 ? $notifyCount : '99+'; ?></div><i style="line-height: 50px" class="material-icons-round">forum</i></a></li>

                <li><a class="bw-text <?php echo $user->isLogin()?'hide':'' ?>" spa="search?work"><i style="line-height: 50px" class="material-icons-round">home</i></a></li>
                <li><a class="bw-text <?php echo $user->isLogin()?'hide':'hide' ?>" spa="search?work"><i style="line-height: 50px" class="material-icons-round">badge</i></a></li>
                <li><a class="bw-text <?php echo $user->isLogin()?'hide':'' ?>" spa="login"><i style="line-height: 50px" class="material-icons-round">add_box</i></a></li>
                <li><a class="bw-text <?php echo $user->isLogin()?'hide':'' ?>" spa="login"><i style="line-height: 50px" class="material-icons-round">account_circle</i></a></li>

            </ul>
        </div>
    </nav>
</div>
<div id="nav-bottom-menu" class="backdrop-blur show-on-medium-and-down nav-mobile" style="display: none <?php echo $user->isLogin()?'':'!important' ?>;">
    <nav class="bw-text z-depth-s2 show-on-medium-and-down" role="navigation">
        <div class="nav-wrapper container flex">
            <ul>
                <li><a class="bw-text" spa="search?work"><i class="material-icons-round">home</i></a></li>
                <li><a class="bw-text" spa="search?work"><i class="material-icons-round">badge</i></a></li>
                <li><a class="bw-text modal-trigger" href="#modalUpload"><i class="material-icons-round">add_box</i></a></li>
                <li><a class="bw-text" spa="notify"><div data-notify-count="<?php echo $notifyCount; ?>" class="red notify-indicator nav-notify-indicator-mob <?php echo $notifyCount > 0 ? '' : 'hide'; ?>"><?php echo $notifyCount < 99 ? $notifyCount : '99+'; ?></div><i class="material-icons-round">favorite</i></a></li>
                <li><a class="bw-text" spa="@<?php echo $userInfo['login'] ?>"><i class="material-icons-round">account_circle</i></a></li>
            </ul>
        </div>
    </nav>
</div>

<div class="container center animated fadeOut" id="preloader">
    <div class="preloader-wrapper active">
        <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
</div>
<div id="modalUpload" class="modal bottom-sheet">
    <div class="collection transparent center" style="margin-bottom: 50px">
        <a class="collection-item bw-text waves-effect" onclick="$('#upload-file-feed').click()">Публикация в ленту</a>
        <a spa="publish/work" class="collection-item bw-text waves-effect">Объявления - Работа</a>
        <a spa="publish/service" class="collection-item bw-text waves-effect">Объявления - Услуги</a>
        <a class="collection-item bw-text waves-effect" onclick="$('#upload-file-apartment').click()">Объявления - Недвижимость</a>
        <a class="collection-item bw-text waves-effect" onclick="$('#upload-file-other').click()">Объявления - Остальное</a>
        <a class="collection-item red-text waves-effect modal-close">Закрыть</a>
    </div>
    <form id="upload-file-feed-form" action="/publish/feed" enctype="multipart/form-data" method="post" class="hide">
        <input id="upload-file-feed" name="upload-feed-file[]" onchange="$('#upload-file-feed-form').submit();" type="file" multiple accept="image/png,image/x-png,image/jpeg">
        <input name="upload-feed" type="hidden" value="true">
        <input name="nonclear" type="hidden" value="true">
    </form>
    <form id="upload-file-ad-apart-form" action="/publish/apartment" enctype="multipart/form-data" method="post" class="hide">
        <input id="upload-file-apartment" name="upload-apartment-file[]" onchange="$('#upload-file-ad-apart-form').submit();" type="file" multiple accept="image/png,image/x-png,image/jpeg">
        <input name="upload-apartment" type="hidden" value="true">
        <input name="nonclear" type="hidden" value="true">
    </form>
    <form id="upload-file-ad-other-form" action="/publish/other" enctype="multipart/form-data" method="post" class="hide">
        <input id="upload-file-other" name="upload-other-file[]" onchange="$('#upload-file-ad-other-form').submit();" type="file" multiple accept="image/png,image/x-png,image/jpeg">
        <input name="upload-other" type="hidden" value="true">
        <input name="nonclear" type="hidden" value="true">
    </form>
</div>
<main>