<?php
namespace devStorm\Models;
use Phalcon\Mvc\Model;
/**
 * IndexPage
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.models
 */

class Thread extends Model {

    public $id;

    public $name;

    public $onlyAdmin;


    public function getSource() {
        return "Thread";
    }

}