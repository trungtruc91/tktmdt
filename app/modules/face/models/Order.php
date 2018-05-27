<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 5/26/2018
 * Time: 11:35 PM
 */

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Order extends Db
{
    public $_zcollection = "order";

    public $collection = null;


    public $_limit = 40;

    public $_offset = 0;

    public $_query = [];

    public $_sort = -1;

    public $_fieldSort = 'id';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function save($document){
        $this->collection->insertOne($document);
    }
}