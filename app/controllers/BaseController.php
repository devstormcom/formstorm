<?php
use devStorm\Library\Error\Notification;
use devStorm\Models\User;
/**
 * Simple BaseController
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.controllers
 */
 
namespace devStorm\Controllers;
 
use devStorm\Models\User;

class BaseController extends \Phalcon\Mvc\Controller {
	
	public function initialize() {
		$this->tag->setTitle("devStorm");
        $this->loadOnlineUser(30);
	}

	protected function getMSG($msg) {
		return Notification::$msg;
	}

    private function loadOnlineUser($min = 10) {
        $online = strtotime("-".$min." minutes");
        $user = User::find(array('last_time_online >= :minutes:', 'bind' => array('minutes' => $online)));
        if($user !== false) {
            $this->view->onlineUser = $user;
        }
    }
}

?>