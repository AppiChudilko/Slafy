<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $userInfo;
global $methods;
global $colors;
global $modal;
global $server;
global $defaultAvatarList;

if (isset($_GET['token'])) {
    if ($_GET['token'] == hash('sha256', $userInfo['id'] . $userInfo['email'])) {
        $user->setActivate();
        $user->showMessage('Вы успешно активировали свой аккаунт');
    }
}

$avatarBg = in_array($userInfo['avatar'], $defaultAvatarList) ? $userInfo['cl_bg'] : 'wb';

$clBtn = $userInfo['cl_btn'] . ' ' . $methods->cToTextCl($userInfo['cl_btn']);
?>
<div class="<?php echo $userInfo['cl_bg'] != 'black' ?:'white'; ?>" style="width: 100%; height: 200px; top: 0; position: absolute; z-index: -1; overflow: hidden; background: url('<?php echo IMAGE_CDN_PATH ?>/upload/user/<?php echo $userInfo['id'] . '/' . $userInfo['background'] ?>') center no-repeat; background-size: cover;">
    <div class="<?php echo $userInfo['background'] ? 'hide' : '' ?>" aria-hidden="true">
        <svg style="height: 400px; width: 100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 810" preserveAspectRatio="xMinYMin slice" aria-hidden="true">
            <path class="fill <?php echo $userInfo['cl_bg'] ?> lighten-2" fill="#fbfbfc" d="M153.89 0H0v809.5h415.57C345.477 500.938 240.884 211.874 153.89 0z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?> lighten-1" fill="#f7f7f7" d="M153.89 0c74.094 180.678 161.088 417.448 228.483 674.517C449.67 506.337 527.063 279.465 592.56 0H153.89z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?>" fill="#f6f6f6" d="M545.962 183.777c-53.796 196.576-111.592 361.156-163.49 490.74 11.7 44.494 22.8 89.49 33.1 134.883h404.07c-71.294-258.468-185.586-483.84-273.68-625.623z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?> darken-1" fill="#efefee" d="M592.66 0c-15 64.092-30.7 125.285-46.598 183.777C634.056 325.56 748.348 550.932 819.642 809.5h419.672C1184.518 593.727 1083.124 290.064 902.637 0H592.66z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?> darken-2" fill="#ebebec" d="M1144.22 501.538c52.596-134.583 101.492-290.964 134.09-463.343 1.2-6.1 2.3-12.298 3.4-18.497 0-.2.1-.4.1-.6 1.1-6.3 2.3-12.7 3.4-19.098H902.536c105.293 169.28 183.688 343.158 241.684 501.638v-.1z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?> darken-3" fill="#e7e7e7" d="M1278.31,38.196C1245.81,209.874 1197.22,365.556 1144.82,499.838L1144.82,503.638C1185.82,615.924 1216.41,720.211 1239.11,809.6L1439.7,810L1439.7,256.768C1379.4,158.78 1321.41,86.288 1278.31,38.195L1278.31,38.196z"></path>
            <path class="fill <?php echo $userInfo['cl_bg'] ?> darken-4" fill="#e1e1e1" d="M1285.31 0c-2.2 12.798-4.5 25.597-6.9 38.195C1321.507 86.39 1379.603 158.98 1440 257.168V0h-154.69z"></path>
        </svg>
    </div>
</div>
<div class="container">
    <div class="section" style="padding-top: 120px">
        <div class="row">
            <div class="col s12">
                <div class="flex" style="height: 80px;">
                    <img alt="<?php echo $userInfo['login'] ?>" class="animated slafy-anim circle <?php echo $avatarBg ?> lighten-2 center-align profile-avatar" src="<?php echo $user->getUserAvatarGif($userInfo['id'], $userInfo['avatar']) ?>">
                    <a style="margin-left: -20px;margin-top: 60px;" onclick="$('#file-upload-avatar').click()" class="btn btn-floating <?php echo $clBtn ?> z-depth-s waves-effect tooltipped" data-position="bottom" data-tooltip="GIF изображения доступны по подписке Premium"><i class="material-icons-round <?php echo $methods->cToTextCl($userInfo['cl_btn']) ?>">add_a_photo</i></a>
                    <a onclick="$('#file-upload-background').click()" <?php echo $user->isSubscribe() ? '' : 'disabled' ?> class="btn <?php echo $clBtn ?> z-depth-s waves-effect animated slafy-anim" style="margin-left: auto; margin-top: 60px">Загрузить фон</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 center">
                <label class="bw-text"><b>Справка</b></label>
                <br>
                <label>Чтобы загружать анимированые (GIF) изображения и загружать фон профиля необходимо преобрести подписку Premium. Для баннера мы рекомендуем использовать изображения 1280x400.</label>
            </div>
            <div class="col s12" style="margin-top: 30px">
                <div class="switch animated center">
                    <label>
                        Светлая сторона
                        <input id="switch-theme" <?php echo isset($_COOKIE['slafy-is-light']) ? 'checked' : '' ?> onchange="$.enableLightTheme(this.checked)" type="checkbox">
                        <span class="lever"></span>
                        Тёмная сторона
                    </label>
                </div>
                <br><br>
            </div>
            <div class="col s12">
                <h4 class="grey-text" style="margin-top: 36px">Профиль</h4>
            </div>
            <div class="col s12 l4">
                <div class="card-panel flex">
                    <aform>
                        <label class="input-label">Имя в профиле</label>
                        <input placeholder="Имя в профиле" name="name" value="<?php echo $userInfo['name'] ?>" type="text">
                        <button name="user-edit-name" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                    </aform>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel flex">
                    <aform>
                        <label class="input-label">Логин</label>
                        <input placeholder="Логин" value="<?php echo $userInfo['login'] ?>" required name="text" type="text">
                        <button name="user-edit-login" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                    </aform>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel flex">
                    <aform>
                        <label class="input-label">Сайт</label>
                        <input placeholder="Сайт" name="website" value="<?php echo $userInfo['website'] ?>" type="text">
                        <button name="user-edit-website" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                    </aform>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel center">
                    <label class="bw-text"><b>Тип профиля</b></label>
                    <br>
                    <br>
                    <div class="switch">
                        <label>
                            Приватный
                            <input async-swtich="true" <?php echo $userInfo['is_public'] ? 'checked' : '' ?> name="user-edit-type-profile" type="checkbox">
                            <span class="lever"></span>
                            Публичный
                        </label>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel center">
                    <label class="bw-text"><b>Статус онлайн</b></label>
                    <br>
                    <br>
                    <div class="switch">
                        <label>
                            Скрыт
                            <input async-swtich="true" <?php echo !$userInfo['online_hidden'] ? 'checked' : '' ?> name="user-edit-hide-online" type="checkbox">
                            <span class="lever"></span>
                            Публичен
                        </label>
                    </div>
                </div>
            </div>
            <div class="col s12 l3 hide">
                <div class="card-panel center">
                    <label class="bw-text"><b>Подписчики</b></label>
                    <br>
                    <br>
                    <div class="switch">
                        <label>
                            Скрыты
                            <input async-swtich="true" <?php echo $userInfo['is_show_followers'] ? 'checked' : '' ?> name="user-edit-hide-followers" type="checkbox">
                            <span class="lever"></span>
                            Публичны
                        </label>
                    </div>
                </div>
            </div>
            <div class="col s12 l3 hide">
                <div class="card-panel center">
                    <label class="bw-text"><b>Подписки</b></label>
                    <br>
                    <br>
                    <div class="switch">
                        <label>
                            Скрыты
                            <input async-swtich="true" <?php echo $userInfo['is_show_follows'] ? 'checked' : '' ?> name="user-edit-hide-follows" type="checkbox">
                            <span class="lever"></span>
                            Публичны
                        </label>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel center">
                    <label class="bw-text"><b>Отображать в профиле</b></label>
                    <br>
                    <br>
                    <div class="switch">
                        <label>
                            Лайки
                            <input async-swtich="true" <?php echo $userInfo['is_show_feed'] ? 'checked' : '' ?> name="user-edit-like-or-feed" type="checkbox">
                            <span class="lever"></span>
                            Публикации
                        </label>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel">
                    <aform method="post" class="row" style="margin: 0">
                        <div class="col s12" style="margin: 0; padding: 0;">
                            <label class="input-label">Цвет фона профиля</label>
                            <select name="color-bg">
                                <?php
                                foreach ($colors as $item)
                                    echo '<option ' . ($userInfo['cl_bg'] == $item ? 'selected' : '') . ' value="' . $item . '">' . $item . '</option>';
                                ?>
                            </select>
                        </div>
                        <div class="col s12" style="margin: 0; padding: 0;">
                            <label class="input-label">Цвет кнопок профиля</label>
                            <select name="color-btn">
                                <?php
                                foreach ($colors as $item)
                                    echo '<option ' . ($userInfo['cl_btn'] == $item ? 'selected' : '') . ' value="' . $item . '">' . $item . '</option>';
                                ?>
                            </select>
                            <button name="user-edit-color" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                        </div>
                    </aform>
                </div>
            </div>
            <div class="col s12 l8">
                <div class="card-panel flex">
                    <aform style="width: 100%">
                        <label class="input-label">Описание профиля</label>
                        <textarea placeholder="Описание профиля" name="content" class="materialize-textarea bw-text"><?php echo $userInfo['about'] ?></textarea>
                        <button name="user-edit-desc" class="btn <?php echo $clBtn ?> waves-effect right">Сохранить</button>
                    </aform>
                </div>
            </div>

            <div class="col s12">
                <h4 class="grey-text" style="margin-top: 36px">Безопасность</h4>
            </div>

            <div class="col s12 l6">
                <div class="card-panel flex">
                    <aform>
                        <label class="input-label">Email</label>
                        <input placeholder="Email" name="email" value="<?php echo $userInfo['email'] ?>" type="email">
                        <label class="input-label hide">Для активации аккаунта введите вашу почту, нажмите кнопку сохранить, далее на почту придет письмо с активацией, если письмо не пришло, нажмите "Сохранить" еще раз и проверьте папку "Спам"</label>
                        <button name="user-edit-email" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                    </aform>
                </div>
            </div>
            <div class="col s12 l6">
                <div class="card-panel flex">
                    <aform>
                        <label class="input-label">Пароль</label>
                        <input placeholder="Пароль" name="pass1" autocomplete="off" type="password">
                        <input placeholder="Повторите пароль" name="pass2" autocomplete="off" type="password">
                        <button name="user-edit-pass" class="z-depth-0 waves-effect right btn btn-small <?php echo $clBtn ?>">Сохранить</button>
                    </aform>
                </div>
            </div>
            <div class="col s12">
                <h4 class="grey-text" style="margin-top: 36px">История авторизаций</h4>

                <ul class="collection card" style="border: none">
                    <?php

                    foreach ($user->getLoginLog(10) as $item) {
                        echo '
                            <li class="collection-item avatar" style="min-height: 12px">
                                <i style="font-style: normal;" class="circle ' . ($item['token'] == $_COOKIE['user'] ? 'green' : '') . '">' . $item['country'] . '</i>
                                <span class="title">' . $item['city'] . '</span>
                                <br><label>' . $server->timeStampToDateTimeFormat($item['timestamp']) . ' · ' . $item['ip'] . ' ' . ($item['token'] == $_COOKIE['user'] ? ' · <label class="green-text">Текущая</label>' : '') . '</label>
                                <aform><input type="hidden" name="id" value="' . $item['id'] . '"><a name="destroy-session" class="secondary-content ' . ($item['token'] == '' ? 'hide' : '') . '"><i class="material-icons-round bw-text">logout</i></a></aform>
                            </li>
                        ';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<form id="upload-file-avatar-form" enctype="multipart/form-data" method="post">
    <input class="hide" name="avatar" onchange="$('#upload-file-avatar-form').submit();" id="file-upload-avatar" type="file" accept="image/x-png<?php echo $user->isSubscribe() ? ',image/gif' : '' ?>,image/jpeg">
    <input type="hidden" name="upload-user-avatar" value="true">
</form>
<form id="upload-file-background-form" enctype="multipart/form-data" style="width: 100%; display: flex" method="post">
    <input class="hide" name="avatar" onchange="$('#upload-file-background-form').submit();" id="file-upload-background" type="file" accept="image/x-png<?php echo $user->isSubscribe() ? ',image/gif' : '' ?>,image/jpeg">
    <input type="hidden" name="upload-user-background" value="true">
</form>