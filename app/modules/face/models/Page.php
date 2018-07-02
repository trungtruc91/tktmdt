<?php


namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Page extends Db
{
    public function __construct()
    {
        parent::__construct();
        $this->_zcollection = 'Pages';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }

    public function getAll()
    {
        return $this->collection->find();
    }

    public function getItemById($UserId,$PageId)
    {
        $data = ['UserID' => $UserId,'PageID'=>$PageId];
        return $this->collection->find($data);
    }

    public function save($document)
    {
        $data=[
          'UserID'=>$document['userID'],
          'PageID'=>$document['id'],
          'NamePage'=>$document['name'],
          'Token'=>$document['access_token'],
          'Category'=>$document['category']
        ];
        $this->collection->insertOne($data);
    }

}