<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $userInfo;
global $methods;
global $cities;
global $userInfoSession;
global $englishLevel;

$result = null;
$errorMessage = null;

?>
<div class="container" style="padding-top: 60px">
    <div class="section">
        <div class="row">
            <div class="col s12 flex">
                <div class="flex" style="padding: 20px 0">
                    <img style="width: 80px; object-fit: contain" src="/client/images/stickers/512/preduprezhdenie.png">
                    <div style="margin-left: 8px">
                        <h5 style="    margin-top: 5px;" class="grey-text">Внимание!</h5>
                        При подаче объявления пожалуйста сверяйте данные объявлений, если вы хотите найти или предложить постоянную работу, подайте объявления в раздел <a spa="publish/work">Работа</a>. В случае нарушения <a>правил подачи объявлений</a> ваше объявление будет удалено и вы получите блокировку на подачу всех объявлений, при повторных нарушениях ваш аккаунт может быть заблокирован.
                   </div>
                </div>
            </div>
            <form method="post" class="col s12">
                <div class="card-panel" style="margin-top: 0">
                    <div class="row" style="margin: 0">
                        <div class="input-field col s12 l6">
                            <input type="text" maxlength="30" required placeholder="Заголовок" name="title" class="bw-text">
                        </div>
                        <div class="input-field col s12 l6">
                            <input type="text" maxlength="30" required placeholder="Услуга (Например Репетитор)" name="tag" class="bw-text">
                        </div>
                        <div class="input-field col s12 l2">
                            <select name="english">
                                <?php
                                $idx = 0;
                                foreach ($englishLevel as $item) {
                                    echo '<option value="' . $idx . '">' . $item . '</option>';
                                    $idx++;
                                }
                                ?>
                            </select>
                            <label>Уровень английского</label>
                        </div>
                        <div class="input-field col s12 l10">
                            <select name="city">
                                <?php
                                $idx = 0;
                                foreach ($cities as $item) {
                                    if ($userInfoSession['city'] == $item[2])
                                        echo '<option selected value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                    else
                                        echo '<option value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                    $idx++;
                                }
                                ?>
                            </select>
                            <label>Выберите город</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea data-length="2000" maxlength="2000" required placeholder="Подробное описание" name="content" class="materialize-textarea bw-text"></textarea>
                        </div>
                        <div class="input-field col s12">
                            <div class="switch center">
                                <label>
                                    Предлагаю услугу
                                    <input name="type" type="checkbox">
                                    <span class="lever"></span>
                                    Ищу услугу
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex" style="width: 100%;">
                    <button name="ad-publish-service" class="btn btn-large slafy-color waves-effect z-depth-s" style="width: 100%; max-width: 250px; margin: auto">Опубликовать</button>
                </div>
            </form>
        </div>
    </div>
</div>