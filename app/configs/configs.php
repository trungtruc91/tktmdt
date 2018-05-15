<?php


$db_name = 'nodetraining';
$db_user = 'minhnode';
$db_pass = 'minhnodenoob';

$host    = 'localhost';
$uri     = 'mongodb://' . $db_user . ':' . $db_pass . '@' . $host  . ':27017/' . $db_name . '?authMechanism=MongoDB-CR';


$conArr = [
    'db' => [
        'uri' => $uri,
        'uriOptions' => [],
        'driverOptions' => [],
        'db_name' => $db_name
    ]


];
//echo '<pre style="background-color:red; font-weight: bold">';
//print_r($conArr);
//echo '</pre>';die;
return $conArr;