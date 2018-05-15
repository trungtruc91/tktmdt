<?php

namespace Zs\Face;

use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;

class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = null)
    {
        // TODO: Implement registerAutoloaders() method.
        $loader = new Loader();

        $namespacesArr = [
            'Zs\Face\Controllers' => MODULE_PATH . '/face/controllers/',
            'Zs\Face\Models' => MODULE_PATH . '/face/models/'
        ];


        $loader->registerNamespaces($namespacesArr);
        $loader->register();
    }

    public function registerServices(\Phalcon\DiInterface $dependencyInjector)
    {
        // TODO: Implement registerServices() method.
        $dependencyInjector->set('dispatcher',function (){
           $dispatcher=new Dispatcher();
           $dispatcher->setDefaultNamespace("Zs\Face\Controllers");
           return $dispatcher;
        });

        $dependencyInjector->set('view', function () {
            $view = new View();
            $view->setViewsDir(MODULE_PATH."/face/views/");
            return $view;
        });
    }
}