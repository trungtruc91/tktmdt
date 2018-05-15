<?php
use Phalcon\Mvc\Url as UrlProvider;

$di = new Phalcon\Di\FactoryDefault();



//set module default
$di->set('router',function (){
   return include_once CONFIG_PATH."/router.php";


});


$di->setShared('zs_configs', function () use ($di) {
    $configPath = CONFIG_PATH . '/configs.php';
    $configs = new Phalcon\Config\Adapter\Php($configPath);

    return $configs;
});



// Setup a base URI so that all generated URIs include the "tutorial" folder
$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);