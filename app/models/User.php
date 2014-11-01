<?php
namespace devStorm\Models;
use Phalcon\Mvc\Model;
use devStorm\Library\Mail\Mail;
/**
 * IndexPage
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.models
 */

class User extends Model {

	public $id;

	public $username;

	public $email;

	public $created;

	public $last_time_online;

	public $validated;

	public $banned;

    public $admin;

	public function getSource() {
        return "User";
    }

    public function initialize() {
        $this->hasMany("id", "devStorm\Models\Post", "user_id", array('alias' => 'posts'));
    }

	public static function haveUser($username) {
		$user = User::findByUsername($username);
		if($user !== false) {
			return true;
		} else {
			return false;
		}
	}

	public function afterCreate() {
        $mail = new Mail();
        $mail->setTemplate('confirm');
        $mail->send($this->email, 'Registrierung abschliessen', array('username' => $this->username, 'emailUrl' => 'http://dev-storm.com/confirm/'.md5($this->email).'/'.$this->email));
	}
}
?>