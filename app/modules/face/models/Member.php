<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 7/2/2018
 * Time: 10:09 PM
 */

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Member extends Db
{
    public function __construct()
    {
        parent::__construct();
        $this->_zcollection='Members';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function getAll(){
        return $this->collection->find();
    }
    public function getItem($id,$type){
        $data=['MemberID'=>$id,
                'TypeInteract'=>$type
        ];
        return $this->collection->find($data);
    }

    public function save($document){
        $this->collection->insertOne($document);
    }

}