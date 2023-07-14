<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $methods;
?>


<div class="container" style="padding-top: 30px;">
    <div class="section">
        <?php echo $methods->showError($this->text); ?>
    </div>
</div>
