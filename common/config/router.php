<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: router.php
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
use \Phalcon\Mvc\Router;

$router = new Router(false);

$router->removeExtraSlashes(true);

//=========================
//PAGE MODULE
//=========================
$router->add('', array(
    'module' => 'page',
    'namespace' => 'Stormform\Page\Controllers\\',
    'controller' => 'index',
    'action' => 'index'
));
$router->add('/', array(
    'module' => 'page',
    'namespace' => 'Stormform\Page\Controllers\\',
    'controller' => 'index',
    'action' => 'index'
));
$router->add('/index', array(
    'module' => 'page',
    'namespace' => 'Stormform\Page\Controllers\\',
    'controller' => 'index',
    'action' => 'index'
));

?>