<?php

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class User extends Db
{
//    public $_zcollection = "User";
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
        $this->_zcollection="User";
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }
    public function getAll(){
        return $this->collection->find();
    }
    public function getItemById($id){
        return $this->collection->find(['UserID'=>$id]);
    }
    public function save($document){

            $data = [
                'UserID' => $document['id'],
                'FullName'=>'',
                'FirstName' => $document['first_name'],
                'LastName' => $document['last_name'],
//                'age_range' => $document['age_range']['min'],
                'Email' => ((isset($document['email'])) ? $document['email'] : ''),
                'Address' => ((isset($document['address'])) ? $document['address'] : ''),
//                'total_friends'=>$document['friends']['summary']['total_count'],
                'DistrictCode' => '',
                'ProvinceCode' => '',
                'WardCode' => '',
                'PhoneNumber' => ''
            ];


        $this->collection->insertOne($data);
    }
    public function update($document){

        $data=[
            'FullName'=>$document['full_name'],
            'Email'=>((isset($document['email']))? $document['email']:'' ),
            'Address'=>((isset($document['address']))? $document['address']:'' ),
//            'total_friends'=>$document['friends']['summary']['total_count'],
            'DistrictCode'=>$document['district_code'],
            'ProvinceCode'=>$document['province_code'],
            'WardCode'=>$document['wards_code'],
            'PhoneNumber'=>$document['number_phone']
        ];
        $this->collection->updateOne(['UserID'=>$document['id']],['$set'=>$data]);
    }
}