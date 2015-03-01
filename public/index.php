<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: index.php
| Author: Flavio Kleiber (flaver12)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);

/** DEFINE SOME USES **/
use Phalcon\Mvc\Application;
use Phalcon\Loader;
use Phalcon\DI\FactoryDefault;
use Phalcon\Config\Adapter\Ini as Configreader;

if(!isset($_GET['_url'])) {
    $_GET['_url'] = '/';
}
/** DEFINE MY APP_PATH **/
define("APP_PATH", realpath('..'));

/** READ THE CONFIGS **/
$config 	= new Configreader(APP_PATH.'/common/config/main.ini');

/** COMPOSER BABY!! **/
//include APP_PATH.'/vendor/autoload.php';

//START UP APP
try {
	$di = new FactoryDefault();
	include APP_PATH.'/common/config/services.php';
	$app = new Application($di);
    $app->registerModules(include APP_PATH.'/common/config/modules.php');
	echo $app->handle()->getContent();
} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e){
    echo $e->getMessage();
}