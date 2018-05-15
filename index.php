<?php

include_once 'define.php';


try {
    // autoLoader
    include_once CONFIG_PATH . "/loader.php";

    //dependency injection
    include_once CONFIG_PATH . "/service.php";

    //application
    include_once CONFIG_PATH . "/application.php";

    $listener = new \Phalcon\Debug();

    $listener->listen(true, true);

} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
