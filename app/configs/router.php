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
return $router;

