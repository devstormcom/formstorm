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
| @desc userpage                                                         |
+------------------------------------------------------------------------+
 */
namespace devStorm\Controllers;
use devStorm\Controllers\BaseController;
use devStorm\Models\User;

class UserController extends BaseController {

    public function indexAction($username) {
        $this->tag->prependTitle("&hearts; $username - ");
        $user = User::findFirst(array('username = :username:', 'bind' => array('username' => $username)));
        if($user) {
            $this->view->user = $user;
        }
    }
}
?>