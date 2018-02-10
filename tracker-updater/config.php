<?php
// define('ROOT_DIR', dirname(dirname(__FILE__)));
define('ROOT_DIR', '/usr/share/nginx/07tracker/updater/');

function autoloadClasses($class) {
    $filePath = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    if(is_file($filePath)) {
        return require_once ($filePath);
    }
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    if(is_file($filePath)) {
        return require_once ($filePath);
    }
}
spl_autoload_register('autoloadClasses');

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$dbInfo = array( 'host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'name' => '07tracker' );