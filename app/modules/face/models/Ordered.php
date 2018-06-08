<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 5/29/2018
 * Time: 11:54 PM
 */

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Ordered extends Db
{
    public $_zcollection = "ordered";

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
    public function getAll(){
        return $this->collection->find();
    }
    public function getItemById($id){
        $data=['userId'=>$id];
        return $this->collection->find($data);
    }

    public function save($document){
        $this->collection->insertOne($document);
    }
    public function update($filter,$document){

        $data=[
            "TotalServiceFee"=>$document['TotalServiceFee'],
            "ClientHubID"=>$document['ClientHubID']
        ];
        $this->collection->updateOne(['id_create'=>(int)$filter],['$set'=>$data]);
    }
    public function delete($filter){

        $this->collection->deleteOne(['id_create'=>(int)$filter]);
    }

    public function join($id){
        $data=[
            [
                '$match'=>['id_create'=>(int)$id]
            ],
            [
            '$lookup'=>[
            'from'=>'order_create',
            'localField'=>'id_create',
            'foreignField'=>'id_create',
            'as'=>'info_ordered'
            ]
        ]
        ];

        return $this->collection->aggregate($data);
    }


}