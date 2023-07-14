<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $methods;
global $qb;
global $userInfo;
global $userInfoSession;
global $server;
global $cities;
global $englishLevel;
global $apartmentTypes;
global $otherCats;

$countAllUser = $qb->createQueryBuilder('users')->selectSql('id')->limit(1)->orderBy('id DESC')->executeQuery()->getSingleResult();

?>
<style>
    .collection .collection-item.avatar {
        min-height: 30px !important;
    }
    .official {
        margin-left: 5px;
    }

    .blocktest {
        padding: 2px 4px;
        border-radius: 4px;
        margin: 0 4px;
        color: #fff;
    }

    .collapsible li {
        margin-bottom: 24px;
    }
</style>
<div class="container container-full-mobile" style="padding-top: 80px">
    <div class="section" style="padding: 0">
        <div class="row" style="margin: 0; border-radius: 0">
            <div class="col s12 flex">
                <div class="flex" style="padding: 20px 0">
                    <img style="width: 80px; object-fit: contain" src="/client/images/stickers/512/spravka.png">
                    <div style="margin-left: 8px">
                        <h5 style="    margin-top: 5px;" class="grey-text">Справка</h5>
                        Раздел с объявлениями и полезной информацией, на данынй момент он находится в <a spa="uf/Q-_bJa6e8CBd05BVkpwSKg">Альфа-версии</a>, мы безусловно заинтересованы в развитии и добавления новых функций не только для раздела "объявления" но и для всего сайта
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container container-full-mobile">
    <div class="section" style="padding: 0">
        <div class="row" style="margin: 0; border-radius: 0">
            <div class="col s12">
                <ul class="tabs profile-tab card">
                    <li class="tab col s8 l3"><a class="bw-text flex <?php echo isset($_GET['work']) ? 'active' : '' ?>" spa="search?work"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">work</i> Работа</a></span></a></li>
                    <li class="tab col s8 l3"><a class="bw-text flex <?php echo isset($_GET['service']) ? 'active' : '' ?>" spa="search?service"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">badge</i> Услуги</a></span></a></li>
                    <li class="tab col s8 l3"><a class="bw-text flex <?php echo isset($_GET['apartment']) ? 'active' : '' ?>" spa="search?apartment"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">apartment</i> Недвижимость</a></span></a></li>
                    <li class="tab col s8 l3"><a class="bw-text flex <?php echo isset($_GET['other']) || empty($_GET) ? 'active' : '' ?>" spa="search?other"><span class="flex profile-tab-span"><i class="material-icons-round profile-tab-icon">newspaper</i> Остальное</a></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_GET['work'])) {
?>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <form action="/search" class="row">
                    <div class="input-field col s6 l3">
                        <input type="hidden" name="work">
                        <select name="city">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="-1">Вся страна</option>';
                            foreach ($cities as $item) {
                                if ($userInfoSession['city'] == $item[2])
                                    echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                else
                                    echo '<option ' . (isset($_GET['city']) && $_GET['city'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s2 l1">
                        <select name="en">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['en']) ? '' : 'selected') . ' value="-1">-</option>';
                            foreach ($englishLevel as $item) {
                                echo '<option ' . (isset($_GET['en']) && $_GET['en'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s4 l2">
                        <select name="search">
                            <option <?php echo isset($_GET['search']) ? '' : 'selected'?> value="-1">-</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 0 ? 'selected' : ''?> value="0">Вакансия</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 1 ? 'selected' : ''?> value="1">Ищу работу</option>
                        </select>
                    </div>
                    <div class="input-field col s12 l6 flex">
                        <input style="width: calc(100% - 68px);" type="text" maxlength="60" placeholder="Поиск" value="<?php echo $_GET['q'] ?? '' ?>" name="q" class="bw-text">
                        <button style="margin-left: 12px;" class="btn btn-floating blue accent-4 waves-effect right">
                            <i class="material-icons-round">done</i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col s12">
                <ul class="collapsible transparent z-depth-0">
                    <?php
                    $adWorkList = $user->getAdWorkList(
                            0,
                            50,
                            true,
                        $_GET['city'] ?? -1,
                        $_GET['en'] ?? -1,
                        $_GET['search'] ?? -1,
                        $_GET['q'] ?? '',
                    );
                    foreach ($adWorkList as $item) {
                        echo $methods->getAdBlockType1(
                            $item['wid'],
                            'work',
                            $item['title'],
                            $item['text_desc'],
                            $item['text_main'],
                            $item['tag'],
                            $item['user_id'],
                            $item['login'],
                            $item['name'],
                            $item['avatar'],
                            $item['cl_btn'],
                            $item['timestamp'],
                            $item['english_id'],
                            $item['city'],
                            $item['is_search'],
                        );
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
    return;
}
?>
<?php
if (isset($_GET['service'])) {
?>
        <style>
            .collection .collection-item.avatar {
                min-height: 30px !important;
            }
            .official {
                margin-left: 5px;
            }
        </style>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <form action="/search" class="row">
                    <div class="input-field col s6 l3">
                        <input type="hidden" name="service">
                        <select name="city">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="-1">Вся страна</option>';
                            foreach ($cities as $item) {
                                if ($userInfoSession['city'] == $item[2])
                                    echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                else
                                    echo '<option ' . (isset($_GET['city']) && $_GET['city'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s2 l1">
                        <select name="en">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['en']) ? '' : 'selected') . ' value="-1">-</option>';
                            foreach ($englishLevel as $item) {
                                echo '<option ' . (isset($_GET['en']) && $_GET['en'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s4 l2">
                        <select name="search">
                            <option <?php echo isset($_GET['search']) ? '' : 'selected'?> value="-1">-</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 0 ? 'selected' : ''?> value="0">Ищу услугу</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 1 ? 'selected' : ''?> value="1">Предлагаю услугу</option>
                        </select>
                    </div>

                    <div class="input-field col s12 l6 flex">
                        <input style="width: calc(100% - 68px);" type="text" maxlength="60" placeholder="Поиск" value="<?php echo $_GET['q'] ?? '' ?>" name="q" class="bw-text">
                        <button style="margin-left: 12px;" class="btn btn-floating blue accent-4 waves-effect right">
                            <i class="material-icons-round">done</i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col s12">
                <ul class="collapsible transparent z-depth-0">
                    <?php
                    $adWorkList = $user->getAdServiceList(
                        0,
                        50,
                        true,
                        $_GET['city'] ?? -1,
                        $_GET['en'] ?? -1,
                        $_GET['search'] ?? -1,
                        $_GET['q'] ?? '',
                    );

                    foreach ($adWorkList as $item) {
                        echo $methods->getAdBlockType1(
                            $item['sid'],
                            'service',
                            $item['title'],
                            $item['text_desc'],
                            $item['text_main'],
                            $item['tag'],
                            $item['user_id'],
                            $item['login'],
                            $item['name'],
                            $item['avatar'],
                            $item['cl_btn'],
                            $item['timestamp'],
                            $item['english_id'],
                            $item['city'],
                            $item['is_search'],
                        );
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
    return;
}
?>
<?php
if (isset($_GET['apartment'])) {
?>
        <style>
            .collection .collection-item.avatar {
                min-height: 30px !important;
            }
            .official {
                margin-left: 5px;
            }
        </style>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <form action="/search" class="row">
                    <div class="input-field col s7 l3">
                        <input type="hidden" name="apartment">
                        <select name="city">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="-1">Вся страна</option>';
                            foreach ($cities as $item) {
                                if ($userInfoSession['city'] == $item[2])
                                    echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                else
                                    echo '<option ' . (isset($_GET['city']) && $_GET['city'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s5 l2">
                        <select name="type">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['type']) ? '' : 'selected') . ' value="-1">-</option>';
                            foreach ($apartmentTypes as $item) {
                                echo '<option ' . (isset($_GET['type']) && $_GET['type'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s6 l2 flex">
                        <input type="number" maxlength="10" placeholder="Цена от" value="<?php echo $_GET['pf'] ?? '' ?>" name="pf" class="bw-text">
                    </div>
                    <div class="input-field col s6 l2 flex">
                        <input type="number" maxlength="10" placeholder="Цена до" value="<?php echo $_GET['pt'] ?? '' ?>" name="pt" class="bw-text">
                    </div>
                    <div class="input-field col s12 l3 flex">
                        <input style="width: calc(100% - 68px);" type="text" maxlength="60" placeholder="Поиск" value="<?php echo $_GET['q'] ?? '' ?>" name="q" class="bw-text">
                        <button style="margin-left: 12px;" class="btn btn-floating blue accent-4 waves-effect right">
                            <i class="material-icons-round">done</i>
                        </button>
                    </div>

                </form>
            </div>
            <?php
            $adWorkList = $user->getAdApartmentList(
                0,
                50,
                true,
                $_GET['city'] ?? -1,
                $_GET['type'] ?? -1,
                $_GET['pf'] ?? 0,
                $_GET['pt'] ?? 999999999,
                $_GET['q'] ?? '',
            );

            foreach ($adWorkList as $item) {
                echo '
                    <div class="col s12 l4">
                        ' . $methods->getAdBlockType2(
                            'ad/apartment/' . $item['aid'],
                            $item['title'],
                            '$' . number_format($item['price']) . '/мес',
                            $item['tag'],
                            $apartmentTypes[$item['type']],
                            $item['name'] ?: $item['login'],
                            $item['user_id'],
                            json_decode(htmlspecialchars_decode($item['img']))
                        ) . '
                    </div>
                ';
            }
            ?>
        </div>
    </div>
</div>

<?php
    return;
}
?>
<?php
if (isset($_GET['car'])) {
?>
        <style>
            .collection .collection-item.avatar {
                min-height: 30px !important;
            }
            .official {
                margin-left: 5px;
            }
        </style>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12 l4">
                <a href="#" style="display: block" class="card">
                    <div class="card-image">
                        <img src="https://i.imgur.com/avkitDD.png">
                        <span class="card-title">Ford Mustang</span>
                    </div>
                    <div class="card-action">
                        <div class="btn grey-text" href="#" style="padding-left: 0">Бруклин</div>
                        <div class="btn green-text right" href="#" style="padding-right: 0">$3000</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
    return;
}
?>
<div class="container container-full-mobile">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <form action="/search" class="row">
                    <div class="input-field col s8 l3">
                        <input type="hidden" name="other">
                        <select name="city">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="-1">Вся страна</option>';
                            foreach ($cities as $item) {
                                if ($userInfoSession['city'] == $item[2])
                                    echo '<option ' . (isset($_GET['city']) ? '' : 'selected') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                else
                                    echo '<option ' . (isset($_GET['city']) && $_GET['city'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item[0] . ' - ' . $item[1] . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s4 l2">
                        <select name="search">
                            <option <?php echo isset($_GET['search']) ? '' : 'selected'?> value="-1">-</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 0 ? 'selected' : ''?> value="0">Продажа</option>
                            <option <?php echo isset($_GET['search']) && $_GET['search'] == 1 ? 'selected' : ''?> value="1">Аренда</option>
                        </select>
                    </div>
                    <div class="input-field col s12 l2">
                        <select name="type">
                            <?php
                            $idx = 0;
                            echo '<option ' . (isset($_GET['type']) ? '' : 'selected') . ' value="-1">-</option>';
                            foreach ($otherCats as $item) {
                                echo '<option ' . (isset($_GET['type']) && $_GET['type'] == $idx ? 'selected' : '') . ' value="' . $idx . '">' . $item . '</option>';
                                $idx++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-field col s6 l1 flex">
                        <input type="number" maxlength="10" placeholder="От $" value="<?php echo $_GET['pf'] ?? '' ?>" name="pf" class="bw-text">
                    </div>
                    <div class="input-field col s6 l1 flex">
                        <input type="number" maxlength="10" placeholder="До $" value="<?php echo $_GET['pt'] ?? '' ?>" name="pt" class="bw-text">
                    </div>
                    <div class="input-field col s12 l3 flex">
                        <input style="width: calc(100% - 68px);" type="text" maxlength="60" placeholder="Поиск" value="<?php echo $_GET['q'] ?? '' ?>" name="q" class="bw-text">
                        <button style="margin-left: 12px;" class="btn btn-floating blue accent-4 waves-effect right">
                            <i class="material-icons-round">done</i>
                        </button>
                    </div>

                </form>
            </div>
            <?php
            $adWorkList = $user->getAdOtherList(
                0,
                50,
                true,
                $_GET['city'] ?? -1,
                $_GET['type'] ?? -1,
                $_GET['search'] ?? -1,
                $_GET['pf'] ?? 0,
                $_GET['pt'] ?? 999999999,
                $_GET['q'] ?? '',
            );
            foreach ($adWorkList as $item) {
                echo '
                <div class="col s12 l4">
                    ' . $methods->getAdBlockType3(
                        'ad/other/' . $item['aid'],
                        $item['title'],
                        $item['price'] > 0 ? '$' . number_format($item['price']) . ($item['is_buy']?'/мес':'') : 'Бесплатно',
                        $otherCats[$item['type']],
                        $item['is_buy'] ? 'Аренда' : 'Продажа',
                        $item['name'] ?: $item['login'],
                        $item['user_id'],
                        json_decode(htmlspecialchars_decode($item['img']))
                    ) . '
                </div>
            ';
            }
            ?>
        </div>
    </div>
</div>
