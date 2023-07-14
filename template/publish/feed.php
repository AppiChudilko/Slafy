<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $userInfo;
global $methods;

$result = null;
$errorMessage = null;

if (isset($_POST['upload-feed'])) {
    $files = new \Server\Files();
    $result = $files->uploadImageFeed();
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
    $feedId = $user->createFeed($imgList);
}
?>
<div class="container container-feed" style="padding-top: 80px">
    <div class="section">
        <div class="row">
            <div class="col s12 l4">
                <img style="width: 100%; border-radius: 8px; margin-bottom: 12px" src="<?php echo IMAGE_CDN_PATH . '/upload/feed/' . $userInfo['id'] . '/' . reset($imgList) ?>">
            </div>
            <form method="post" action="../../index.php" class="col s12 l8">
                <input type="hidden" name="id" value="<?php echo $feedId ?>">
                <div class="card-panel" style="margin-top: 0">
                    <div class="row" style="margin: 0">
                        <div class="input-field col s12">
                            <textarea placeholder="Описание" name="content" class="materialize-textarea bw-text"></textarea>
                        </div>
                        <div class="input-field col s12" id>
                            <input type="text" id="input-geo" placeholder="Геолокация" name="geo" class="autocomplete bw-text">
                        </div>
                        <div class="input-field col s12 hide">
                            <div class="switch">
                                <label>
                                    <input name="only-friend" type="checkbox">
                                    <span class="lever" style="margin-left: 0;"></span>
                                    Только для друзей
                                </label>
                            </div>
                        </div>
                        <div class="input-field col s12">
                            <div class="switch">
                                <label>
                                    <input name="disable-like" type="checkbox">
                                    <span class="lever" style="margin-left: 0;"></span>
                                    Отключить лайки
                                </label>
                            </div>
                        </div>
                        <div class="input-field col s12">
                            <div class="switch">
                                <label>
                                    <input name="disable-comment" type="checkbox">
                                    <span class="lever" style="margin-left: 0;"></span>
                                    Отключить комментарии
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button name="feed-publish" class="btn slafy-color waves-effect z-depth-s" style="width: 100%">Опубликовать</button>
            </form>
        </div>
    </div>
</div>