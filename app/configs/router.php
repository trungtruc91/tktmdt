<?php
$router =new \Phalcon\Mvc\Router();
//$router->setDefaultModule('face');
//$router->add('/:controller/:action(/)*',[
//   'module'=>'face',
//   'controller'=>1,
//   'action'=>2
//]);
$router->add('/:module/:controller/:action(/)*',[
   'module'=>1,
   'controller'=>2,
   'action'=>3
]);

$router->add('/',[
   'module'=>'face',
   'controller'=>'login',
   'action'=>'index'
]);
$router->add('/profile(.html)*',[
   'module'=>'face',
   'controller'=>'index',
   'action'=>'profile'
]);
$router->add('/:controller/select-page(.html)*',[
   'module'=>'face',
   'controller'=>1,
   'action'=>'selectpage'
]);
$router->add('/:controller/:action(/:params)*',[
   'module'=>'face',
   'controller'=>1,
   'action'=>2,
    'params'=>3,

]);

$router->add('/profile(.html)*',[
   'module'=>'face',
   'controller'=>'index',
   'action'=>'profile'
]);
$router->add('/login(.html)*',[
   'module'=>'face',
   'controller'=>'login',
   'action'=>'index'
]);

return $router;

