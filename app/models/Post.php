<?php
namespace devStorm\Models;
use Phalcon\Mvc\Model;
/**
 * Posts
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.models
 */

class Post extends Model {

    public $id;

    public $user_id;

    public $category_id;

    public $thread_id;

    public $replay;

    public $title;

    public $body;

    public $created;

    public $modified;

    public $deleted;

    public $hidden;

    public $visible;

    public function getSource() {
        return "Post";
    }
    public function initialize() {
        $this->belongsTo("user_id", "devStorm\Models\User", "id", array('alias' => 'user'));
    }
}