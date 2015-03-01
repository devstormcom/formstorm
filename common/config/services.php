<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: services.php
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
 
 use Phalcon\Mvc\Dispatcher;
 use Phalcon\Mvc\View;
 use Phalcon\Mvc\View\Engine\Volt;
 use Phalcon\Mvc\Url;
 use Phalcon\Session\Adapter\Files as SessionManager;
 use Phalcon\Db\Adapter\Pdo\Mysql as DatabaseConnection;
 use devStorm\Library\Mail\Mail;
 use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;

//Url
$di->set('url', function() use($config){
	$url = new Url();
	$url->setBaseUri($config->site->baseUri);
	return $url;	
}, true);

//Router
$di->set('router', function(){
    require '../common/config/router.php';
    return $router;
});

//Session
$di->set('session', function(){
	$session = new SessionManager();
	$session->start();
	return $session;
});

//Session flash
$di->set(
    'flashSession',
    function () {
        return new Phalcon\Flash\Session(array(
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
        ));
    }
);

//Config
$di->set('config', $config);

//DB
$di->set('db', function() use($config){
	$db = new DatabaseConnection(
        array(
            'host'      => $config->database->host,
            'username'  => $config->database->username,
            'password'  => $config->database->password,
            'dbname'    => $config->database->dbname
    ));
    return $db;
});

/**
 * Set up the falsh msg with bootstrap
 */
$di->set('flash', function(){
    $flash = new Phalcon\Flash\Direct(array(
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info'
    ));
    return $flash;
});

//Register our email
$di->set('mail', function(){
    return new Mail();
});
$di->set('modelsMetadata', function() use($config) {
    return new MetaDataAdapter(array('metaDataDir' => APP_PATH . '/cache/metaData/'));
}, true);
?>