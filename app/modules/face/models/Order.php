<?php


namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Order extends Db
{
//    public $_zcollection = "Orders";
//
//    public $collection = null;
//
//
//    public $_limit = 40;
//
//    public $_offset = 0;
//
//    public $_query = [];
//
//    public $_sort = -1;
//
//    public $_fieldSort = 'id';
    public function __construct()
    {
        parent::__construct();
        $this->_zcollection='Orders';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function getAll(){
        return $this->collection->find();
    }
    public function getItemById($id){
        $data=['CustomerID'=>$id];
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
        $this->collection->updateOne(['OrderID'=>(int)$filter],['$set'=>$data]);
    }
    public function delete($filter){

        $this->collection->deleteOne(['OrderID'=>(int)$filter]);
    }

    public function join($id){
        $data=[
            [
                '$match'=>['OrderID'=>(int)$id]
            ],
            [
            '$lookup'=>[
            'from'=>'OrderInfo',
            'localField'=>'OrderID',
            'foreignField'=>'OrderID',
            'as'=>'info_ordered'
            ]
        ]
        ];

        return $this->collection->aggregate($data);
    }


}