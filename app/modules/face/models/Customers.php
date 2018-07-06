<?php
namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Customers extends Db
{
    public function __construct()
    {
        parent::__construct();
        $this->_zcollection = "Customers";
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function save($document){

        $data=[
          'CustomerID'=>$document['id_customer'],
          'UserID'=>$document['user_id'],
          'Name'=>$document['CustomerName'],
            'NumberPhone'=>$document['CustomerPhone'],
            'Address'=>$document['ShippingAddress'],
            'Email'=>''

        ];
        $this->collection->insertOne($data);
    }
    public function update($document){


        $this->collection->updateOne(['id_create'=>(int)$document['ID_create']],['$set'=>$dataUpdate]);
    }
    public function delete($filter){

        $this->collection->deleteOne(['id_create'=>(int)$filter]);
    }
    public function getItemById($id){
        return $this->collection->find(['CustomerID'=>$id]);
    }
    public function getAll($option=[]){
        return $this->collection->find($option);
    }
}