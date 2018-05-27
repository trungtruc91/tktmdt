<?php

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class User extends Db
{
    public $_zcollection = "user_face";

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
        return $this->collection->find(['id_user'=>$id]);
    }
    public function save($document){

            $data = [
                'id_user' => $document['id'],
                'full_name'=>'',
                'first_name' => $document['first_name'],
                'last_name' => $document['last_name'],
                'age_range' => $document['age_range']['min'],
                'email' => ((isset($document['email'])) ? $document['email'] : ''),
                'address' => ((isset($document['address'])) ? $document['address'] : ''),
                'total_friends'=>$document['friends']['summary']['total_count'],
                'district_code' => '',
                'province_code' => '',
                'wards_code' => '',
                'number_phone' => ''
            ];


        $this->collection->insertOne($data);
    }
    public function update($document){

        $data=[
            'full_name'=>$document['full_name'],
            'email'=>((isset($document['email']))? $document['email']:'' ),
            'address'=>((isset($document['address']))? $document['address']:'' ),
            'total_friends'=>$document['friends']['summary']['total_count'],
            'district_code'=>$document['district_code'],
            'province_code'=>$document['province_code'],
            'wards_code'=>$document['wards_code'],
            'number_phone'=>$document['number_phone']
        ];
        $this->collection->updateOne(['id_user'=>$document['id']],['$set'=>$data]);
    }
}