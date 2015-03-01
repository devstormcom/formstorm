<?php
/*-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: Module.php
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

namespace Stormform\Page;

use Phalcon\Loader,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\View,
    Phalcon\Mvc\ModuleDefinitionInterface,
    Phalcon\Mvc\View\Engine\Volt;

class Module implements ModuleDefinitionInterface
{

    /**
     * Register a specific autoloader for the module
     */
    public function registerAutoloaders()
    {

        $loader = new Loader();

        $loader->registerNamespaces(
            array(
                'Stormform\Page\Controllers' => '../app/page/controllers/',
                'Stormfrom\Models'      => '../common/models/',
            )
        );

        $loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices($di)
    {

        //Registering a dispatcher
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("Stormform\Page\Controllers");
            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di->set('view', function() {
            $view = new View();
            $view->setViewsDir(__DIR__.'/views/');
            $view->setLayoutsDir('../../../common/layouts/');
            $view->setTemplateAfter('main');
            $view->registerEngines(array(
                ".volt" => function($view, $di) {
                    $volt = new Volt($view, $di);
                    $volt->setOptions(array(
                        "compiledPath" => __DIR__."/../../common/volt/",
                        "compiledExtension" => ".php"
                    ));
                    return $volt;
                }
            ));
            return $view;
        });
    }

}

