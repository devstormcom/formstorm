<?php
/**
 +------------------------------------------------------------------------+
 | dev-storm.com                                                          |
 +------------------------------------------------------------------------+
 | Copyright (c) 2014 dev-storm.com Team                                  |
 +------------------------------------------------------------------------+
 | @author flaver <flaver@dev-storm.com>                                  |
 | @copyright flaver, dev-storm.com                                       |
 | @package devstorm.controllers                                          |
 | @desc Basic Controller for all controllers                             |
 +------------------------------------------------------------------------+
*/
namespace devStorm\Controllers;
use devStorm\Library\Error\Notification;
use devStorm\Models\User;

class BaseController extends \Phalcon\Mvc\Controller {

    /**
     * Init function
     *
     * @return void
     */
	public function initialize() {
		$this->tag->setTitle("devStorm");
        $this->loadOnlineUser(30);
	}

    /**
     * Returns the last online user
     *
     * @param int $min
     * @return void
     */
    private function loadOnlineUser($min = 10) {
        $online = strtotime("-".$min." minutes");
        $user = User::find(array('last_time_online >= :minutes:', 'bind' => array('minutes' => $online)));
        if($user !== false) {
            $this->view->onlineUser = $user;
        }
    }
}

?>