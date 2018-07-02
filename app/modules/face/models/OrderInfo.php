<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 5/26/2018
 * Time: 11:35 PM
 */

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class OrderInfo extends Db
{
//    public $_zcollection = "OrderInfo";
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
        $this->_zcollection = "OrderInfo";
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function save($document){
        $this->collection->insertOne($document);
    }
    public function update($document){
        $dataUpdate=[
            "ProvinceCode"=>$document['province'],
            "PaymentTypeID" => (int)$document['PaymentTypeID'],//người nhận trả tiền
            "ToDistrictID" => (int)$document['district'],
            "ToWardCode" => $document['wards'],
            "Note" => $document['txtNote'],
            "CustomerName" => $document['name'],
            "CustomerPhone" => $document['sdt'],
            "ShippingAddress" => $document['shipping'],
            "CoDAmount" => (int)$document['amount'],
            "NoteCode" => $document['NoteCode'],
            "ServiceID" => (int)$document['ServiceID'],
            "CouponCode" => $document['coupon'],
            "Weight" => (float)$document['size'],
            "Length" => (float)$document['long'],
            "Width" => (float)$document['width'],
            "Height" => (float)$document['height'],
        ];

        $this->collection->updateOne(['OrderID'=>(int)$document['ID_create']],['$set'=>$dataUpdate]);
    }
    public function delete($filter){

       $this->collection->deleteOne(['id_create'=>(int)$filter]);
    }

}