<?php
/**
+------------------------------------------------------------------------+
| dev-storm.com                                                          |
+------------------------------------------------------------------------+
| Copyright (c) 2014 dev-storm.com Team                                  |
+------------------------------------------------------------------------+
| @author flaver <flaver@dev-storm.com>                                  |
| @copyright flaver, dev-storm.com                                       |
| @package devstorm.loader                                               |
| @desc class loader                                                     |
+------------------------------------------------------------------------+
 */
 
 /** DEFINE SOME USES **/
 use Phalcon\Loader;
 
//Create a new loader
$loader = new Loader();

//Register the needed namespace's
$loader->registerNamespaces(array(
	'devStorm\Controllers'		=> $config->site->controllersDir,
	'devStorm\Models'			=> $config->site->modelsDir,
	'devStorm'	=> '../app/',
));
 
//Register now!
$loader->register();

?>