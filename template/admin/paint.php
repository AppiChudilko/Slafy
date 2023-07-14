<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $methods;

$paintList = $qb->createQueryBuilder('paint')->selectSql()->orderBy('id DESC')->executeQuery()->getResult();
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
                            <th>Тип</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        foreach ($paintList as $item) {
                            echo '
                                <tr>
                                    
                                    <td><b><a href="#" class="black-text">' . $item['id'] . '.</a></b></td>
                                    <td>iPhone ' . $item['iphone'] . '</td>
                                    <td>' . number_format($item['type'] === 1 ? 300 : 2000) . ' ₽</td>
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