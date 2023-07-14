<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $userInfo;
global $methods;
global $cities;
global $userInfoSession;
global $otherCats;
global $server;

$result = null;
$errorMessage = null;
$imgList = [];

if (isset($_POST['upload-other'])) {

    $adItem = $user->getLastAdOtherByUserId();
    if (isset($adItem['timestamp']) && $adItem['timestamp'] > ($server->timeStampNow() - 3600))
    {
        ?>
        <div class="container" style="padding-top: 30px">
            <div class="section">
                <?php echo $methods->showError('Нельзя так часто публиковать объявления, после публикации ожидайте 1 час') ?>
            </div>
        </div>
        <?php
        return;
    }

    $files = new \Server\Files();
    $result = $files->uploadImageOther();
    if (isset($result['error'])) {
        $errorMessage = $result['error'];
        ?>
        <div class="container" style="padding-top: 30px">
            <div class="section">
                <?php echo $methods->showError($errorMessage) ?>
            </div>
        </div>
        <?php
        return;
    }
    $imgList = $result['success']['files'];
    $apartId = $user->createAdOther($imgList);
}

?>
<div class="container" style="padding-top: 60px">
    <div class="section">
        <div class="row">
            <div class="col s12 flex">
                <div class="flex" style="padding: 20px 0">
                    <img style="width: 80px; object-fit: contain" src="/client/images/stickers/512/preduprezhdenie.png">
                    <div style="margin-left: 8px">
                        <h5 style="    margin-top: 5px;" class="grey-text">Внимание!</h5>
                        При подаче объявления пожалуйста сверяйте данные объявлений. В случае нарушения <a>правил подачи объявлений</a> ваше объявление будет удалено и вы получите блокировку на подачу всех объявлений, при повторных нарушениях ваш аккаунт может быть заблокирован.
                    </div>
                </div>
            </div>
            <?php
            foreach ($imgList as $img) {
                echo '<div class="col s12 l2"><img class="materialboxed" style="width: 100%; border-radius: 8px; margin-bottom: 12px" src="' . IMAGE_CDN_PATH . '/upload/other/' . $userInfo['id'] . '/' . $img . '"></div>';
            }
            ?>
            <form method="post" class="col s12">
                <div class="card-panel" style="margin-top: 0">
                    <div class="row" style="margin: 0">
                        <input type="hidden" name="id" value="<?php echo $apartId ?>">
                        <div class="input-field col s12 l4">
                            <input type="text" maxlength="30" required placeholder="Заголовок" name="title" class="bw-text">
                        </div>
                        <div class="input-field col s12 l4">
                            <input type="text" maxlength="120" id="input-geo" placeholder="Адрес (Если необходим)" name="tag" class="autocomplete bw-text">
                        </div>
                        <div class="input-field col s12 l4">
                            <input type="number" min="0" maxlength="10" required placeholder="Цена (Если бесплатно, то напишите 0)" name="price" class="bw-text">
                        </div>
                        <div class="input-field col s12 l6">
                            <select name="type">
                                <?php
                                $idx = 0;
                                foreach ($otherCats as $item) {
                                    echo '<option value="' . $idx . '">' . $item . '</option>';
                                    $idx++;
                                }
                                ?>
                            </select>
                            <label>Категория</label>
                        </div>
                        <div class="input-field col s12 l6">
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
                            <textarea data-length="5000" maxlength="5000" required placeholder="Описание" name="content" class="materialize-textarea bw-text"></textarea>
                        </div>
                        <div class="input-field col s12">
                            <div class="switch center">
                                <label>
                                    Продажа
                                    <input name="buy" type="checkbox">
                                    <span class="lever"></span>
                                    Аренда
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex" style="width: 100%;">
                    <button name="ad-publish-other" class="btn btn-large slafy-color waves-effect z-depth-s" style="width: 100%; max-width: 250px; margin: auto">Опубликовать</button>
                </div>
            </form>
        </div>
    </div>
</div>