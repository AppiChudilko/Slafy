<?php
define("AppiEngine", true);
define("IMAGE_CDN_PATH", 'https://adaptation-usa.com');

header('Powered: Alexander Pozharov');
header("Cache-control: public");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT");
//header("Cache-Control: no-store,no-cache,mustrevalidate");

//echo '<h1 style="margin-top: 150px; width: 100%; text-align: center">Тех. работы</h1>';
//return;

/*if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http') {
    header('Location: https://adaptation-usa.com');
    die;
}*/

@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
error_reporting(E_ALL);
ini_set("display_errors", 1);

spl_autoload_register(function($class) {
    include_once str_replace('\\', '/', $class) . '.php';
});

$langType = 'en';
if (isset($_COOKIE['lang']))
    if ($_COOKIE['lang'] == 'ru')
        $langType = 'ru';

include_once 'globals.php';
include_once 'lang/' . $langType . '.php';

use Server\Core\Init;
use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Request;
use Server\Core\Template;
use Server\Core\Server;
use Server\Core\Settings;
use Server\Manager\PermissionManager;
use Server\Manager\RequestManager;
use Server\Manager\TemplateManager;
use Server\Methods;
use Server\User;
use Server\Files;

global $modal;
global $lang;
global $UTC_TO_TIME;
global $sections;
global $subSections;
global $userInfo;
global $userInfoSession;
global $imgVehList;

$UTC = 0;
if (isset($_COOKIE['UTC']))
    $UTC = $_COOKIE['UTC'];
$UTC_TO_TIME = $UTC * 3600;

$init = new Init;
$init->initAppi();

$qb = new QueryBuilder();
$qb->connectDataBase(EnumConst::DB_HOST, EnumConst::DB_NAME, EnumConst::DB_USER, EnumConst::DB_PASS);

$view = new Template('/template/');
$requests = new RequestManager();
$permissionManager = new PermissionManager();
$tmp = new TemplateManager($view, $init);
$request = new Request();
$server = new Server();
$methods = new Methods();
$settings = new Settings();
$user = new User($qb);
$files = new Files();

$redis = new Redis();
$redis->connect('127.0.0.1');

if (isset($_SESSION['modal-show'])) {

    $modal['show'] = $_SESSION['modal-show'];
    $modal['text'] = $_SESSION['modal-msg'];

    unset($_SESSION['modal-show']);
    unset($_SESSION['modal-msg']);
}

$requests->checkRequests($qb);
$page = $request->getRequest(['/']);
$view->set('siteName', $settings->getSiteName());
$view->set('version', $settings->getVersion());
$view->set('langType', $langType);
$view->set('metaImg', '/images/logoBG.png');
$view->set('title', $settings->getTitle());
$view->set('titleHtml', 'NotFound 404 | ' . $settings->getTitle());
$view->set('modal', $modal);
$view->set('error404', false);

if (isset($_POST['ajax'])) {
    if ($_POST['type'] == 'img:texture') {
        header('Content-Type: application/json; charset=utf-8');
        $files = new \Server\Files();
        echo json_encode($files->uploadImageTexture());
    }
    die;
}

/*if (!empty($_FILES)) {
    header('Content-Type: application/json; charset=utf-8');
    print_r($_FILES);
    die;
}*/

if (isset($page['p'])) {
    $request->showPage($page);
}
else {
    $view->set('overflowHidden', true);
    $tmp->showBlockPage('index');
}