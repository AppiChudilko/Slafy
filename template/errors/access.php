<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $methods;
?>


<div class="container" style="padding-top: 30px;">
    <div class="section">
        <?php echo $methods->showError('Чтобы просматривать эту страницу необходимо войти или зарегистрировать аккаунт'); ?>
    </div>
</div>
