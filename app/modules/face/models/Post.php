<?php

namespace Zs\Face\Models;


use Zs\Library\Core\Db;

class Post extends Db
{
    public function __construct()
    {
        parent::__construct();
        $this->_zcollection = 'Posts';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->collection = $this->setCollection($this->_zcollection);
    }

    public function getAll()
    {
        return $this->collection->find();
    }

    public function getItemById($id)
    {
        $data = ['PageID' => $id];
        return $this->collection->find($data);
    }

    public function save($document)
    {
        $this->collection->insertOne($document);
    }

    public function join($id)
    {

        $data = [
            [
                '$match' => ['PostID' => $id]
            ],
            [
                '$lookup' => [
                    'from' => 'Members',
                    'localField' => 'PostID',
                    'foreignField' => 'PostID',
                    'as' => 'tb1'
                ]
            ],
            [
                '$unwind'=>'$tb1'
            ],
            [
                '$lookup' => [
                    'from' => 'Pages',
                    'localField' => 'PageID',
                    'foreignField' => 'PageID',
                    'as' => 'tb2'
                ]
            ],
            [
                '$unwind'=>'$tb2'
            ],
            [
                '$project'=>[
                    '_id'=>1,
                    'NamePage'=>'$tb2.NamePage',
                    'Type'=>'$tb1.TypeInteract',
                    'Comment'=>'$tb1.Comment',
                    'Username'=>'$tb1.Username',
                    'MemberID'=>'$tb1.MemberID'
                ]
            ]
        ];
        return $this->collection->aggregate($data);
    }

}