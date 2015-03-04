<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: BaseModel.php
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

namespace Stormform\Models;

use Phalcon\Mvc\Model;

class BaseModel extends Model
{

    /**
     * Phalcon __construct event
     *
     * @return void
     */
    public function onConstruct()
    {
        Model::setup(array(
            'notNullValidations' => false
        ));
    }
}