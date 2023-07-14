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

$authorInfo = $user->getUserInfoById($this->ad['user_id']);

$imgSlider = '';
$imgList = json_decode(htmlspecialchars_decode($this->ad['img']));
foreach ($imgList as $img) {
    $imgSlider .= '
        <div class="carousel-item black white-text" style="position: relative; padding-top: 56.25%;">
            <img style="border-radius: 8px !important; position: absolute; top: 0; height: 100%" alt="' . $this->ad['tag'] . ' ' . $authorInfo['login'] . '" src="' . IMAGE_CDN_PATH .'/upload/other/' . $authorInfo['id'] . '/' . $img . '">
        </div>
    ';
}
$imgContent = '
    <a onclick="$(\'#carousel-' . $this->ad['id'] . '\').carousel(\'prev\');" class="carousel-arrow-left"><i class="material-icons-round white-text">arrow_back_ios</i></a>
    <a onclick="$(\'#carousel-' . $this->ad['id'] . '\').carousel(\'next\');" class="carousel-arrow-right"><i class="material-icons-round white-text">arrow_forward_ios</i></a>
    <div id="carousel-' . $this->ad['id'] . '" style="border-radius: 8px;" class="carousel carousel-slider center">
        ' . $imgSlider . '
    </div>
';
?>
<style>
    .carousel-item {
        position: absolute !important;
    }
</style>
<script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Article",
    "headline": "<?php echo $this->ad['title']; ?>",
    "author": "<?php echo $authorInfo['login']; ?>",
    "publisher": "<?php echo $authorInfo['login']; ?>",
    "datePublished": "<?php echo gmdate('c', $this->ad['timestamp']) ?>",
    "image": [
      "<?php echo IMAGE_CDN_PATH .'/upload/other/' . $authorInfo['id'] . '/' . reset($imgList) ?>"
    ]
  }
</script>
<div class="container" style="padding-top: 60px">
    <div class="section">
        <div class="row">
            <div class="col s12 flex">
            </div>
            <div class="col s12 flex">
                <div class="card card-feed" style="width: 100%; margin: 0; border-radius: 8px;">
                    <div class="card-image">
                        <?php echo $imgContent; ?>
                    </div>
                </div>
                <iframe class="hide-on-med-and-down z-depth-s"
                        style="width: 30%; height: inherit; margin-left: 24px"
                        frameborder="0"
                        scrolling="no"
                        marginheight="0"
                        marginwidth="0"
                        src="https://maps.google.com/maps?q=<?php echo $this->ad['latitude'] ?>,<?php echo $this->ad['longitude'] ?>&hl=en&z=15&amp;output=embed"
                >
                </iframe>
            </div>
            <div class="col s12 flex">
                <div>
                    <h4><?php echo $this->ad['title'] ?></h4>
                    <div>
                        <a href="https://maps.google.com/maps?q=<?php echo $this->ad['latitude'] ?>,<?php echo $this->ad['longitude'] ?>&hl=en&z=15&amp;output=embed" target="_blank" class="grey-text flex"><?php echo $otherCats[$this->ad['type']] . ' · ' . $this->ad['tag'] ?>
                            <span style="font-size: 1rem;padding-top: 1px;margin-left: 4px;" class="material-icons-round">launch</span>
                        </a>
                    </div>
                </div>
                <h3 class="green-text" style="margin-left: auto">
                    <b><?php echo $this->ad['price'] > 0 ? '$' . number_format($this->ad['price']) . ($this->ad['is_buy']?'/мес':'') : 'Бесплатно' ?></b><label></label>
                </h3>
            </div>
            <div class="col s12">
                <br>
                <?php echo $server->parseText($server->decodeString(nl2br($this->ad['text_main'])), true, true, true, true) ?>
            </div>
            <div class="col s12 flex" style="margin-top: 12px">
                <a onclick="M.toast({html: 'Функция редактирования в разработке, скоро будет доступна, удалите объявление и отправьте заново', classes: 'rounded'});" style="margin: auto;" class="btn <?php echo $authorInfo['id'] == $userInfo['id'] ? '' : 'hide' ?> blue white-text z-depth-s waves-effect animated slafy-anim">
                    Редактировать
                </a>
            </div>
            <div class="col s12 flex" style="margin-top: 12px">
                <form method="post" class="<?php echo $authorInfo['id'] == $userInfo['id'] ? '' : 'hide' ?>" style="margin: auto;">
                    <input type="hidden" name="id" value="<?php echo $this->ad['id'] ?>">
                    <button name="delete-ad-other" class="btn red white-text z-depth-s waves-effect animated slafy-anim">Удалить</button>
                </form>

                <a spa="im/<?php echo $authorInfo['id'] ?>" style="margin: auto;" class="btn <?php echo $authorInfo['id'] != $userInfo['id'] ? '' : 'hide' ?> btn-large <?php echo $authorInfo['cl_btn'] . ' ' . $methods->cToTextCl($authorInfo['cl_btn']) ?> z-depth-s waves-effect animated slafy-anim">
                    Написать
                </a>
            </div>
        </div>
    </div>
</div>
<?php
echo $methods->showFooter();