<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

//

global $prodList;
global $page;
global $methods;
global $qb;
global $user;
global $userInfo;
global $server;
global $defaultAvatarList;

$userIsLogin = $user->isLogin();

if (!$userIsLogin)
    $userInfo['id'] = -1;

$avatarBg = in_array($this->userInfo['avatar'], $defaultAvatarList) ? $this->userInfo['cl_bg'] : 'wb';

$isMyProfile = $this->userInfo['id'] == $userInfo['id'];
$followInfo = $user->getFollowInfo($this->userInfo['id']);
$isFollow = false;

$clBtn = $this->userInfo['cl_btn'] . ' ' . $methods->cToTextCl($this->userInfo['cl_btn']);
$followBtn = '
    <div  style="margin-left: auto; margin-top: 60px">
        <aform>
            <input type="hidden" name="id" value="' . $this->userInfo['id'] . '">
            <button name="user-follow" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim">Подписаться</button>
        </aform>
        ' . ($this->userInfo['is_public'] ? '<a spa="im/' . $this->userInfo['id'] . '" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim"><i class="material-icons-round">send</i></a>' : '') . '
    </div>
';

if ($user->isFollowMe($this->userInfo['id'])) {
    $followBtn = '
        <div  style="margin-left: auto; margin-top: 60px">
            <aform>
                <input type="hidden" name="id" value="' . $this->userInfo['id'] . '">
                <button name="user-follow" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim">В ответ</button>
            </aform>
            ' . ($this->userInfo['is_public'] ? '<a spa="im/' . $this->userInfo['id'] . '" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim"><i class="material-icons-round">send</i></a>' : '') . '
        </div>
    ';
}

if (!empty($followInfo)) {
    if ($followInfo['is_request']) {
        $followBtn = '
            <aform style="margin-left: auto; margin-top: 60px">
                <input type="hidden" name="id" value="' . $this->userInfo['id'] . '">
                <button name="user-unfollow" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim">Отменить запрос</button>
            </aform>
        ';
    }
    else {
        $isFollow = true;
        $followBtn = '
            <div style="margin-left: auto; margin-top: 60px">
                <aform>
                    <input type="hidden" name="id" value="' . $this->userInfo['id'] . '">
                    <button name="user-unfollow" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim">Отписаться</button>
                </aform>
                <a spa="im/' . $this->userInfo['id'] . '" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim"><i class="material-icons-round">send</i></a>
            </div>
        ';
    }
}


if ($isMyProfile)
    $followBtn = '<a spa="settings" class="btn ' . $clBtn . ' z-depth-s waves-effect animated slafy-anim" style="display: block; margin-left: auto; margin-top: 60px">Редактировать</a>';

if (!$userIsLogin)
    $followBtn = '';

$countFeed = 0;

if ($this->userInfo['is_show_feed']) {
    $countFeed = $qb
        ->createQueryBuilder('feed')
        ->selectSql('COUNT(*) as count')
        ->where('is_draft = 0')
        ->andWhere('type = \'img\'')
        ->andWhere('user_id = ' . $this->userInfo['id'])
        ->executeQuery()
        ->getSingleResult()
    ;
}
else {
    $countFeed = $qb
        ->createQueryBuilder('feed')
        ->selectSql('SUM(likes) as count')
        ->where('is_draft = 0')
        ->andWhere('type = \'img\'')
        ->andWhere('user_id = ' . $this->userInfo['id'])
        ->executeQuery()
        ->getSingleResult()
    ;
}

$countFeed = intval(reset($countFeed));

$website = '';
if ($this->userInfo['website'])
    $website = ' · <a target="_blank" href="https://' . $this->userInfo['website'] . '" class="animated slafy-anim">' . $this->userInfo['website'] . '</a>';

$about = $server->parseText($this->userInfo['about'], true, true, true);

$status = '';
$offset = 0;
$marginLeft = 94;
if ($this->userInfo['is_verify']) {
    $status .= '<i class="material-icons-round blue-text text-accent-4 tooltipped icon-status-profile" style="margin-left: ' . $marginLeft . 'px;" data-position="top" data-tooltip="Официальный аккаунт">verified</i>';
    //$status .= '<div class="official official18px blue accent-4 white-text tooltipped" style="position: absolute; margin-left: ' . $marginLeft . 'px; margin-top: 90px;" data-position="top" data-tooltip="Официальный аккаунт"><i class="material-icons-round">done</i></div>';
    $offset += 32;
}

if ($this->userInfo['id'] < 100000) {
    $label = 'В первых 100.000 рядах 🌚';
    $color = 'blue-grey';
    if ($this->userInfo['id'] < 10000) {
        $label = 'В первых 10.000 рядах 💦';
        $color = 'cyan';
    }
    if ($this->userInfo['id'] < 1000) {
        $label = 'В первой тысячи 🍩';
        $color = 'green';
    }
    if ($this->userInfo['id'] < 100) {
        $label = 'В первой сотке ❤️';
        $color = 'amber';
    }
    $status .= '<i class="material-icons-round ' . $color . '-text text-accent-4 tooltipped icon-status-profile" style="margin-left: ' . ($marginLeft + $offset) . 'px;" data-position="top" data-tooltip="' . $label . '">rocket_launch</i>';
    //$status .= '<div class="official official18px ' . $color . ' accent-4 white-text tooltipped" style="position: absolute; margin-left: ' . ($marginLeft + $offset) . 'px; margin-top: 90px;" data-position="top" data-tooltip="' . $label . '"><i class="material-icons-round">emoji_events</i></div>';
    $offset += 32;
}
if ($this->userInfo['subscribe'] > time()) {
    $status .= '<i class="material-icons-round purple-text text-accent-1 tooltipped icon-status-profile" style="margin-left: ' . ($marginLeft + $offset) . 'px;" data-position="top" data-tooltip="Premium">auto_awesome</i>';
    //$status .= '<div class="official official18px red accent-4 white-text tooltipped" style="position: absolute; margin-left: ' . ($marginLeft + $offset) . 'px; margin-top: 90px;" data-position="top" data-tooltip="Подписка Slafy Red"><i class="material-icons-round">favorite</i></div>';
    $offset += 32;
}
if ($this->userInfo['is_tester']) {
    $status .= '<i class="material-icons-round brown-text text-accent-4 tooltipped icon-status-profile" style="margin-left: ' . ($marginLeft + $offset) . 'px;" data-position="top" data-tooltip="Тестер">bug_report</i>';
    //$status .= '<div class="official official18px ' . $color . ' accent-4 white-text tooltipped" style="position: absolute; margin-left: ' . ($marginLeft + $offset) . 'px; margin-top: 90px;" data-position="top" data-tooltip="' . $label . '"><i class="material-icons-round">emoji_events</i></div>';
    $offset += 32;
}
if ($this->userInfo['is_content_maker']) {
    $status .= '<i class="material-icons-round cyan-text text-accent-4 tooltipped icon-status-profile" style="margin-left: ' . ($marginLeft + $offset) . 'px;" data-position="top" data-tooltip="Контент-Мейкер">color_lens</i>';
    //$status .= '<div class="official official18px ' . $color . ' accent-4 white-text tooltipped" style="position: absolute; margin-left: ' . ($marginLeft + $offset) . 'px; margin-top: 90px;" data-position="top" data-tooltip="' . $label . '"><i class="material-icons-round">emoji_events</i></div>';
    $offset += 32;
}
?>
<style>
    .feed-image-collection {
        z-index: 1;
        position: absolute;
        right: 12px;
        top: 12px;
    }

    .icon-status-profile {
        position: absolute;
        font-size: 2rem;
        user-select: none;
        margin-top: 84px;
    }
</style>
<div class="<?php echo $this->userInfo['cl_bg'] != 'black' ?:'white'; ?>" style="width: 100%; height: 200px; top: 0; position: absolute; z-index: -1; overflow: hidden; background: url('<?php echo IMAGE_CDN_PATH ?>/upload/user/<?php echo $this->userInfo['id'] . '/' . $this->userInfo['background'] ?>') center no-repeat; background-size: cover;">
    <div class="<?php echo $this->userInfo['background'] ? 'hide' : '' ?>" aria-hidden="true">
        <svg style="height: 400px; width: 100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 810" preserveAspectRatio="xMinYMin slice" aria-hidden="true">
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> lighten-2" fill="#fbfbfc" d="M153.89 0H0v809.5h415.57C345.477 500.938 240.884 211.874 153.89 0z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> lighten-1" fill="#f7f7f7" d="M153.89 0c74.094 180.678 161.088 417.448 228.483 674.517C449.67 506.337 527.063 279.465 592.56 0H153.89z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?>" fill="#f6f6f6" d="M545.962 183.777c-53.796 196.576-111.592 361.156-163.49 490.74 11.7 44.494 22.8 89.49 33.1 134.883h404.07c-71.294-258.468-185.586-483.84-273.68-625.623z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> darken-1" fill="#efefee" d="M592.66 0c-15 64.092-30.7 125.285-46.598 183.777C634.056 325.56 748.348 550.932 819.642 809.5h419.672C1184.518 593.727 1083.124 290.064 902.637 0H592.66z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> darken-2" fill="#ebebec" d="M1144.22 501.538c52.596-134.583 101.492-290.964 134.09-463.343 1.2-6.1 2.3-12.298 3.4-18.497 0-.2.1-.4.1-.6 1.1-6.3 2.3-12.7 3.4-19.098H902.536c105.293 169.28 183.688 343.158 241.684 501.638v-.1z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> darken-3" fill="#e7e7e7" d="M1278.31,38.196C1245.81,209.874 1197.22,365.556 1144.82,499.838L1144.82,503.638C1185.82,615.924 1216.41,720.211 1239.11,809.6L1439.7,810L1439.7,256.768C1379.4,158.78 1321.41,86.288 1278.31,38.195L1278.31,38.196z"></path>
            <path class="fill <?php echo $this->userInfo['cl_bg'] ?> darken-4" fill="#e1e1e1" d="M1285.31 0c-2.2 12.798-4.5 25.597-6.9 38.195C1321.507 86.39 1379.603 158.98 1440 257.168V0h-154.69z"></path>
        </svg>
    </div>
</div>
<div class="container">
    <div class="section" style="padding-top: 120px">
        <div class="row">
            <div class="col s12">
                <div class="flex" style="height: 80px;">
                    <img alt="<?php echo $this->userInfo['login'] ?>" class="animated slafy-anim circle <?php echo $avatarBg ?> lighten-2 center-align profile-avatar" src="<?php echo $user->getUserAvatarGif($this->userInfo['id'], $this->userInfo['avatar']) ?>">
                    <?php echo $status . $followBtn ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="section" style="padding-bottom: 0">
        <div class="row" style="margin-bottom: 0;">
            <div class="col s12">
                <div class="bw-text" style="margin-bottom: 20px; overflow: hidden; flex-wrap: nowrap; text-overflow: ellipsis; white-space: nowrap;">
                    <h5 class="bw-text animated slafy-anim">
                        <b><?php echo $this->userInfo['name'] ?></b>
                    </h5>
                    <label class="animated slafy-anim" style="text-transform: uppercase;"><nick style="cursor: pointer" onclick="$.copyTextToClipboard('@<?php echo $this->userInfo['login'] ?>', 'Логин был скопирован')">@<?php echo $this->userInfo['login'] . '</nick>' . $website ?></label>
                </div>
                <div class="bw-text animated slafy-anim" style="font-weight: 200">
                    <?php echo nl2br($about) ?>
                </div>
                <div class="flex card-panel animated slafy-anim" style="width: 100%; margin-top: 24px">
                    <div class="center" style="margin: auto">
                        <h5 style="margin: 0"><a class="grey-text"><?php echo $server->numberToKkk($countFeed) ?></a></h5>
                        <label><?php echo $this->userInfo['is_show_feed'] ? 'Публикаций' : 'Лайков' ?></label>
                    </div>
                    <div class="center" style="margin: auto">
                        <h5 style="margin: 0"><a class="grey-text"><?php echo $server->numberToKkk($this->userInfo['count_followers']) ?></a></h5>
                        <label>Подписчиков</label>
                    </div>
                    <div class="center" style="margin: auto">
                        <h5 style="margin: 0"><a class="grey-text"><?php echo $server->numberToKkk($this->userInfo['count_follows']) ?></a></h5>
                        <label>Подписок</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (!$this->userInfo['is_public'] && !$userIsLogin || !$this->userInfo['is_public'] && $userInfo['id'] != $this->userInfo['id'] && !$isFollow) {
    echo '
    <div class="container">
        <div class="section" style="padding-bottom: 0">
            <div class="row">
                <div class="col s12 l3"></div>
                <div class="col s12 l6">
                    <div class="row">
                        <div class="col s12 center"></div>
                        <div class="col s12 center"><img style="max-width: 128px; width: 100%; height: 100%;" src="/client/images/stickers/512/preduprezhdenie.png"></div>
                        <div class="col s12">
                            <h5 class="grey-text center" style="margin-top: 0">
                                На этой странице стоит строгий фейс-контроль, чтобы видеть посты этого пользователя, необходимо подписаться ;)
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
    return;
}
?>

<div class="container container-full-mobile hide">
    <div class="section" style="padding: 0">
        <div class="row" style="margin: 0; border-radius: 0">
            <div class="col s12">
                <ul class="tabs profile-tab card">
                    <li class="tab col s6"><a class="bw-text flex" href="#publish"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">apps</i> Публикации</a></span></li>
                    <li class="tab col s6"><a class="bw-text flex" href="#tag"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">people</i> Отметки</a></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12" id="tag">
                <?php
                echo $methods->showError('Раздел в разработке', 0);
                ?>
            </div>
            <div class="col s12" id="publish">
                <div class="flex" id="feed-profile" style="flex-wrap: wrap;">
                    <?php

                    $resultFeed = $qb
                        ->createQueryBuilder('feed')
                        ->selectSql()
                        ->orderBy('timestamp DESC')
                        ->limit(12)
                        ->where('is_draft = 0')
                        ->andWhere('type = \'img\'')
                        ->andWhere('user_id = ' . $this->userInfo['id'])
                        ->executeQuery()
                        ->getResult()
                    ;

                    if (empty($resultFeed))
                        echo $methods->showError('Публикаций пока нет', 0, 'zadumalsya');

                    foreach ($resultFeed as $item) {
                        $img = json_decode(htmlspecialchars_decode($item['img']),true);
                        echo '
                            <a spa="uf/' . $item['hash'] . '" class="square" style="width: 33%; margin: 0.2% auto; flex-wrap: wrap;">
                                ' . (count($img) > 1 ? '<i class="material-icons-round feed-image-collection white-text">collections</i>' : '') . '
                                <img alt="Photo by @' . $this->userInfo['login'] . ' on SLAFY RU (' . $item['location'] . ')" class="square-content" style="width: 100%; height: 100%; object-fit: cover" src="' . IMAGE_CDN_PATH . '/upload/feed/' . $this->userInfo['id'] . '/' . reset($img) . '">
                            </a>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
