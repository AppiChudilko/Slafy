<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $methods;

if (isset($_GET['edit']) || isset($_GET['editTech'])) {
    $editId = 0;
    if (isset($_GET['edit']))
        $editId = intval($_GET['edit']);
    if (isset($_GET['editTech']))
        $editId = intval($_GET['editTech']);

    $result = $qb->createQueryBuilder('iphone_list')->selectSql()->where('id = ' . $editId)->executeQuery()->getSingleResult();
?>
    <style>
        .panel-editor a {
            margin: auto;
        }
    </style>
    <div style="padding-top: 20px"></div>
    <div class="flex">
        <?php echo $methods->showAdminLeftMenu(); ?>
        <div class="admin-right">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <ul class="tabs card">
                            <li class="tab col s6"><a class="black-text" href="#panel-edit">Редактор</a></li>
                            <li class="tab col s6"><a class="black-text" href="#panel-prev">Предпросмотр</a></li>
                        </ul>
                    </div>
                    <div class="col s12">
                        <div class="card-panel" id="panel-prev">
                            <p id="preview-content">
                                <?php echo htmlspecialchars_decode(htmlspecialchars_decode(isset($_GET['edit']) ? $result['desc_1'] : $result['desc_2'])); ?>
                            </p>
                        </div>
                        <div class="card-panel" id="panel-edit">
                            <div class="row">
                                <div id="article-edit-panel-section" class="input-field col s12 black-text">
                                    <div id="article-edit-panel" class="flex flex-center flex-wrap panel-editor">
                                        <a onclick="$.updateFormat('h5')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Заголовок">
                                            <i class="material-icons">title</i>
                                        </a>
                                        <a onclick="$.updateFormat('label')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Мелкий шрифт">
                                            <i class="material-icons">text_fields</i>
                                        </a>
                                        <a onclick="$.updateFormat('b')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Жирный шрифт">
                                            <i class="material-icons">format_bold</i>
                                        </a>
                                        <a onclick="$.updateFormat('i')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Курсив">
                                            <i class="material-icons">format_italic</i>
                                        </a>
                                        <a onclick="$.updateFormat('u')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Подчеркнутый">
                                            <i class="material-icons">format_underlined</i>
                                        </a>
                                        <a onclick="$.updateFormat('strike')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Зачеркнутый">
                                            <i class="material-icons">format_strikethrough</i>
                                        </a>
                                        <a onclick="$.updateFormat('left')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Текст слева">
                                            <i class="material-icons">format_align_left</i>
                                        </a>
                                        <a onclick="$.updateFormat('center')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Текст по центру">
                                            <i class="material-icons">format_align_center</i>
                                        </a>
                                        <a onclick="$.updateFormat('right')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Текст справа">
                                            <i class="material-icons">format_align_right</i>
                                        </a>
                                        <a onclick="$.updateFormat('blockquote')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Цитата">
                                            <i class="material-icons">format_quote</i>
                                        </a>
                                        <a onclick="$.updateFormat('code')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Код">
                                            <i class="material-icons">code</i>
                                        </a>
                                        <a onclick="$.updateFormat('sup')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Верхний индекс">
                                            <i class="material-icons">vertical_align_top</i>
                                        </a>
                                        <a onclick="$.updateFormat('sub')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Нижний индекс">
                                            <i class="material-icons">vertical_align_bottom</i>
                                        </a>
                                        <a onclick="$.updateFormat('url')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Ссылка">
                                            <i class="material-icons">insert_link</i>
                                        </a>
                                        <a onclick="$.updateFormat('btn')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Кнопка">
                                            <i class="material-icons">label</i>
                                        </a>
                                        <a onclick="$.updateFormat('list')" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Список">
                                            <i class="material-icons">format_list_bulleted</i>
                                        </a>
                                        <a onclick="$.updateFormat('img')" id="upload-article-editor-btn" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Картинка">
                                            <i class="material-icons">insert_photo</i>
                                        </a>
                                        <a onclick="$.updateFormat('youtube')" id="upload-article-editor-btn" class="btn btn-floating blue-grey waves-effect z-depth-0 margin-4 tooltipped" data-position="top" data-tooltip="Видео с YouTube">
                                            <i class="material-icons">ondemand_video</i>
                                        </a>
                                    </div>
                                    <br>
                                </div>
                                <form method="post" class="input-field col s12 black-text">
                                    <input type="hidden" name="id" value="<?php echo $editId ?>">
                                    <textarea required onkeyup="$.updateArticlePreview()" id="article-editor-content" class="materialize-textarea" placeholder="Описание" maxlength="65000" name="content"><?php echo htmlspecialchars_decode(isset($_GET['edit']) ? $result['desc_1'] : $result['desc_2']); ?></textarea>
                                    <button name="panel-edit-phone-<?php echo isset($_GET['edit']) ? 'desc' : 'tech' ?>" class="btn border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Сохранить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    return;
}

$iphoneList = $qb->createQueryBuilder('iphone_list')->selectSql()->orderBy('id DESC')->executeQuery()->getResult();
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
                            <th style="width: 50px"></th>
                            <th>Модель</th>
                            <th>Цена</th>
                            <th>Скидка</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        foreach ($iphoneList as $item) {
                            echo '
                                <tr>
                                    <td>
                                        <div class="card z-depth-0"><div class="card-image">' . $methods->showPhoneBlock('/upload/iphone/texture/6fd15547a04bae4487e86cf5ccfe4314.png', $methods->modelToImgName($item['name']), false) . '</div></div>
                                    </td>
                                    <td><b><a href="/order?model=' . $item['name'] . '&id=1" class="black-text">iPhone ' . $item['name'] . '</a></b></td>
                                    <td>От ' . number_format($item['price']) . ' ₽</td>
                                    <td class="red-text">' . $item['sale'] . '%</td>
                                    <td>
                                        <a href="/rainbow/stock?editTech=' . $item['id'] . '" class="btn right border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Характеристики</a>
                                        <a href="/rainbow/stock?edit=' . $item['id'] . '" style="margin-right: 8px" class="btn right border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Описание</a>
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