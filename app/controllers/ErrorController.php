<?php
 namespace devStorm\Controllers;
 use devStorm\Controllers\BaseController;
 use devStorm\Library\Github\OAuth;
/**
 * Error
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.controllers
 */
  
 class ErrorController extends BaseController {
 	
 	public function notFound404Action() {
		$this->tag->prependTitle("&hearts; Error 404 - ");
	}
 }
?>