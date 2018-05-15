<?php
;
$loader =new \Phalcon\Loader();
$loader->registerNamespaces([
    'Zs\Library\Core'=>APP_PATH."/library/core/",
    "MongoDB"       => APP_PATH .  "/library/MongoDB/",

]);

$loader->registerFiles([
    APP_PATH . '/library/MongoDB/functions.php'

]);
$loader->register();
