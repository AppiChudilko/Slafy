<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $methods;
?>

<div class="container" style="padding-top: 80px">
    <div class="section">
        <div class="row">
            <?php
                echo $methods->showError('Скоро заполню этот раздел', 0, 'spravka');
            ?>
        </div>
    </div>
</div>