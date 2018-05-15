<?php
namespace Zs\Face\Models;
use Zs\Library\Core\Db;

class Index extends Db
{
    public $_zcollection = "groups";

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
    public function findDoc(){
        return $this->collection->find()->toArray();
    }
}