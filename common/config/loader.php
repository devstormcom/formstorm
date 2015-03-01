<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: loader.php
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