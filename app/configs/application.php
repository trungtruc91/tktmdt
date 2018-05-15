<?php
$application = new Phalcon\Mvc\Application($di);
$application->registerModules([
    'face'=>[
        'className' =>'Zs\Face\Module',
        'path'=> MODULE_PATH.'/face/Module.php'
    ]
]);

// Handle the request
$response = $application->handle();
$response->send();