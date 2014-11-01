<?php
/**
 * Autoloader
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.loader
 */
 
 /** DEFINE SOME USES **/
 use Phalcon\Loader;
 
//Create a new loader
$loader = new Loader();

//Register the needed namespace's
$loader->registerNamespaces(array(
	'devStorm\Controllers'		=> $config->site->controllersDir,
	'devStorm\Models'			=> $config->site->modelsDir,
	'devStorm\Forms'			=> '../app/forms',
	'devStorm\Library\Github'	=> '../app/library/Github',
	'devStorm\Library\Error'	=> '../app/library/Errors',
	'devStorm\Library\Mail'		=> '../app/library/Mail',
    'devStorm\Library\BBCode'   => '../app/library/BBCode'
));
 
//Register now!
$loader->register();

?>