<?php
use \Phalcon\Mvc\Router;
/**
 * Router for routing routes ;)
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.loader
 */

$router = new Router(false);

$router->add('/forum', array(
	'controller' => 'forum',
	'action'	=> 'index'
));

$router->add('/forum/view-thread/:params', array(
    'controller' => 'forum',
    'action'	=> 'thread',
    'params'    => 1
));

$router->add('/forum/create/:params', array(
    'controller' => 'forum',
    'action'	=> 'create',
    'params'    => 1
));

$router->add('/forum/replay/:params', array(
    'controller' => 'forum',
    'action'	=> 'replay',
    'params'    => 1
));

$router->add(
    '/forum/view-post/:params', array(
        'controller' => 'forum',
        'action'	=> 'viewPost',
        'params'    => 1
    )
);

$router->add('/register', array(
	'controller' => 'session',
	'action'	=> 'registerUser'
));

$router->add('/login', array(
	'controller' => 'session',
	'action'	=> 'login'
));

$router->add('/logout', array(
    'controller' => 'session',
    'action'	=> 'logout'
));
$router->add('/auth/github/authUser', array(
	'controller' => 'session',
	'action'	=> 'registerGit'
));

$router->add('/auth/github/accessToken', array(
	'controller' => 'session',
	'action'	=> 'accessToken'
));

$router->add(
    '/confirm/:params',
    array(
        'controller'    => 'session',
        'action'        => 'confirm',
        'params'        => 1
    )
);

?>