<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: modules.php
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

//Add here all modules!
return array(
    'page' => array(
        'className' => 'Stormform\Page\Module',
        'path' => '../app/page/Module.php'
    ),
    'admin' => array(
        'className' => 'Stormform\Admin\Module',
        'path' => '../app/admin/Module.php'
    ),
    'forum' => array(
        'className' => 'Stormform\Forum\Module',
        'path' => '../app/forum/Module.php'
    ),
);