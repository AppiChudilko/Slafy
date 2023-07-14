<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $methods;

$orderList = $qb->createQueryBuilder('orders')->selectSql()->orderBy('status ASC, id DESC')->executeQuery()->getResult();
?>

<style>
    tr .card {
        width: 50px;
    }
</style>
<div style="padding-top: 20px"></div>
<div class="flex">
    <?php echo $methods->showAdminLeftMenu(); ?>
    <div class="admin-right">
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Модель</th>
                            <th>Цена</th>
                            <th>Время</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        foreach ($orderList as $item) {
                            echo '
                                <tr>
                                    
                                    <td><b><a href="#" class="black-text">' . $item['id'] . '.</a></b></td>
                                    <td>iPhone ' . $item['model'] . '</td>
                                    <td>' . number_format($item['price']) . ' ₽</td>
                                    <td>' . $server->timeStampToDate($item['timestamp']) . ' ' . $server->timeStampToTime($item['timestamp']) . '</td>
                                    <td>
                                        <a href="/rainbow/orders?id=' . $item['id'] . '" style="margin-right: 8px" class="btn right border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Подробнее</a>
                                    </td>
                                </tr>
                            ';
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>