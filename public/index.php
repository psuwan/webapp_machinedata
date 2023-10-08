<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Bangkok');

const DS = DIRECTORY_SEPARATOR;

$dir_root = realpath(dirname(dirname(__FILE__)));
$dir_app  = $dir_root . DS . 'app';

defined('APPLICATION_PATH') || define('APPLICATION_PATH', $dir_app);

require_once APPLICATION_PATH . DS . 'config' . DS . 'config.php';

// index.php?page=products
$page  = get('page', 'home');
$model = $config['PATH_TO_MODEL'] . $page . '.php';
$view  = $config['PATH_TO_VIEW'] . $page . '.phtml';
$_404  = $config['PATH_TO_VIEW'] . '404.phtml';

if (file_exists($model)) {
    require_once $model;
}

$main_content = $_404;
if (file_exists($view)) {
    $main_content =  $view;
}

include_once $config['PATH_TO_VIEW'] . 'layout.phtml';
