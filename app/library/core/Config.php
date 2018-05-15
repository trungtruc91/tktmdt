<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 5/15/2018
 * Time: 10:36 PM
 */

namespace Zs\Library\Core;


class Config
{
    public $_configs = [];

    public $_di = null;
    public function __construct($controllerObj = null, $options = [])
    {
        $this->_di = \Phalcon\DI::getDefault();

    }

    public function getAllConfig(){
        return $this->_configs;
    }

    public function get($name = null, $options = []){

        if($name != null) {
            $di = $this->_di->get('zs_configs');
            $di->offsetSet('offset', 'offset');
            return $di->$name;
        }

        return false;
    }

    public function getDb(){

        $di = $this->_di->get('zs_configs');
        $di->offsetSet('offset', 'offset');
        return $di->db;

    }

    public function getTheme($name){

    }
}