<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $methods;

$exchange = $qb->createQueryBuilder('panel_exchange')->selectSql()->orderBy('id DESC')->limit(1)->executeQuery()->getSingleResult();
if ($exchange['timestamp'] < $server->timeStampNow()) {

    $data = json_decode(file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js'));
    $usdVal = $data->Valute->USD->Value;
    $eurVal = $data->Valute->EUR->Value;

    $newTimestamp = $server->timeStampNow() + 1200;

    $qb->createQueryBuilder('panel_exchange')->insertSql(['usd', 'eur', 'timestamp'], [$usdVal, $eurVal, $newTimestamp])->executeQuery()->getResult();
}

?>
<div style="padding-top: 20px"></div>
<div class="flex">
    <?php echo $methods->showAdminLeftMenu(); ?>
    <div class="admin-right">
        <div class="row center">
            <div class="col s12 m4">
                <label>Заказов</label>
                <h5>$999.999</h5>
            </div>
            <div class="col s12 m4">
                <label>Выручка</label>
                <h5>$999.999</h5>
            </div>
            <div class="col s12 m4">
                <label>Выручка</label>
                <h5>$999.999</h5>
            </div>
            <div class="col s12 m4">
                <label>Топ популярных текстур</label>
                <h5>$999.999</h5>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m4">

            </div>
        </div>
    </div>
</div>