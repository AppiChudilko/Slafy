<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $methods;

?>
<div style="padding-top: 20px"></div>
<div class="flex">
    <?php echo $methods->showAdminLeftMenu(); ?>
    <div class="admin-right">
        <div class="row">
            <div class="col s12 m4">

            </div>
        </div>
    </div>
</div>