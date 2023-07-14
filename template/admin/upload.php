<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $logList;
global $userInfo;
global $qb;

?>

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
            <div class="col s12">


                <div class="row">
                    <div class="col s12 m2"></div>
                    <div class="col s12 m8">
                        <div class="card-panel">
                            <div class="row">

                                <div class="input-field col s12 m6">
                                    <input id="texture-name" value="Unknown" type="text">
                                    <label for="texture-name">Название текстуры</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <select id="texture-cat">
                                        <?php
                                        $result = $qb->createQueryBuilder('iphone_cats')->selectSql()->orderBy('priority DESC')->executeQuery()->getResult();
                                        foreach ($result as $item) {
                                            echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label>Категория</label>
                                </div>
                                <div class="col s12">
                                    <div class="demo demo-noninputable hide">I'm a div, using `$('.demo-noninputable').pastableNonInputable()`.</div>
                                    <textarea class="demo demo-textarea grey-text materialize-textarea">Сюда кидаем картинку через CTRL+V.</textarea>
                                    <div class="demo demo-contenteditable hide" contenteditable>I'm a div[contenteditable], using `$('.demo-contenteditable').pastableContenteditable()`.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
