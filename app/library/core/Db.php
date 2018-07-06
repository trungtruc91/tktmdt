<?php
namespace Zs\Library\Core;

class Db extends \MongoDB\Client
{
    public $_zdb = '';
    public $_database = null;
    public $_zcollection = '';
    public $collection = null;
    public $_limit = 40;
    public $_offset = 0;
    public $_query = [];
    public $_sort = -1;
    public $_fieldSort = '';

    public function __construct($uri = 'mongodb://127.0.0.1/', array $uriOptions = [], array $driverOptions = [])
    {

        $config = new Config();

        $dbConfig = get_object_vars($config->getDb());
        //$uri = 'mongodb://127.0.0.1/', array $uriOptions = [], array $driverOptions = [])
        parent::__construct($dbConfig['uri'], [], []);

        $this->_database = new \MongoDB\Database($this->getManager(), $dbConfig['db_name']);

        $this->setDatabase($dbConfig['db_name']);

        $this->_zdb = $dbConfig['db_name'];

    }

    public function setDatabase($db_name = null)
    {

        if ($db_name != null) {
            return $this->selectDatabase($db_name);
        }

    }

    public function setCollection($collection_name = null)
    {

        if ($collection_name != null) {
            return $this->selectCollection($this->_zdb, $collection_name);
        }
    }

    public function createId( $options = [])
    {
        $cursor = $this->collection->findOne(
            $options,
            [
                'limit' => 1,
                'sort' => ['CustomerID' => -1],
            ]
        );

        $newID = isset($cursor->CustomerID) ? $cursor->CustomerID . $this->generateRandomString(4) : $this->generateRandomString(1);

        return $newID;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function filter($data = [], $name = '', $default = '', $types = [])
    {

        if (is_array($data) && count($data) > 0) {

            if (!isset($data[$name])) return $default;

            if (empty($data[$name])) return $default;

            if ($data[$name] == null) return $default;

            return $data[$name];

        } else {
            echo 'Error: [ data ] is empty. Please check again.' . "\n";
            exit();

        }


    }

    public function findOne($query)
    {

        $result = $this->collection->findOne($query);

        return $result;
    }

    public function dataType($value = '', $type = 'string')
    {

        if ($type == 'string') $value = (string)$value;

        if ($type == 'int') $value = (int)$value;

        if ($type == 'float') $value = (float)$value;

        return $value;
    }

    /**
     * Phương thức lấy danh sách các document với nhưng điều kiện đơn giản
     * @param string $collection : tên của collection
     * @param unknown $options : mảng tham số tùy biến
     *
     * $options['type']: 'all | key_val | key_val_select'. Default: all
     * $options['sort']: Sắp xếp theo chiều tăng dần. Default: all
     * $options['sort_field']: Tên của field sẽ sắp xếp. Default:  id
     * $options['limit']: true | false. Default: false
     * $options['limit_opt']: ['
     *
     */
    public function getDataList($collection = '', $option = [])
    {

        $default_option = [
            'type' => 'all',
            'sort' => -1,
            'fieldSort' => 'id',
            'limit' => false,
            'limit_opt' => ['offset' => 0, 'limit' => 10],
            'status_vals' => ['published']
        ];

        if (empty($collection)) return [];

        //Nhập 2 mảng Option
        $option = array_merge($default_option, $option);

        if (isset($option['query']) && is_array($option['query'])) $query = $option['query'];
        $query['status'] = ['$in' => $option['status_vals']];

        $filter['skip'] = 0;

        if (isset($option['limit']) && is_numeric($option['limit'])) {
            $filter['limit'] = $option['limit'];
        }
        if (isset($option['fieldSort']) && !empty($option['fieldSort']) && isset($option['sort']) && !empty($option['sort'])) $filter['sort'] = [$option['fieldSort'] => $option['sort']];

        $collection = $this->setCollection($collection);

        $result = $collection->find($query, $filter);

        $type = $option['type'];

        $newResult = [];

        //Xử lý trường hợp $option['type'] = 'all'
        if ($type == 'all') return $result;

        //Xử lý trường hợp $option['type'] = 'val'
        if ($type == 'val'){
            foreach ($result as $key => $document) {
                $newResult[$document['title']] = $document['title'];
            }
            return $newResult;

        }

        //Xử lý trường hợp $option['type'] = 'key_val'
        if ($type == 'key_val') {

            $tmp = [];
            foreach ($result as $key => $document) {

                $document = (array)$document;

                $tmp['id'] = $document['id'];
                $tmp['title'] = $document['title'];

                $newResult[] = $tmp;

            }

            return $newResult;
        }

        //Xử lý trường hợp $option['type'] = 'key_val_select'
        if ($type == 'key_val_select') {

            foreach ($result as $key => $document) {

                $newResult[$document['id']] = $document['title'];

            }

            return $newResult;
        }


    }

    /**
     *
     * Lấy ra các tập hợp giá trị trong bảng vitual
     * 'type' => 'all | key_val | key_val_select'
     *
     */
    public function getVirtualValues($name = null, $option = [])
    {


        $default_option = ['type' => 'all', 'collection' => COL_PREFIX . COL_VIRTUAL, 'fieldSort' => 'id', 'sort' => 1];

        if ($name == null || empty($name)) return [];


        $option = array_merge($default_option, $option);


        //Kiểu dữ liệu trả về
        $type = $option['type'];

        if (isset($option['query']) && is_array($option['query'])) $query = $option['query'];

        $query['status'] = 'published';

        $filter = ['skip' => 0, 'sort' => [$option['fieldSort'] => $option['sort']]];

        $collection = $this->setCollection($option['collection']);

        $newResult = [];

        if (!is_array($name)) {

            $query['collection'] = $name;

            $result = $collection->find($query, $filter);

            //Xử lý trường hợp $option['type'] = 'all'
            if ($type == 'all') return $result;


            //Xử lý trường hợp $option['type'] = 'key_val'
            if ($type == 'key_val') {

                $tmp = [];
                foreach ($result as $key => $document) {

                    $document = (array)$document;

                    $tmp['id'] = $document['id'];
                    $tmp['title'] = $document['title'];

                    $newResult[] = $tmp;

                }

                return $newResult;
            }

            //Xử lý trường hợp $option['type'] = 'key_val_select'
            if ($type == 'key_val_select') {

                foreach ($result as $key => $document) {

                    $newResult[$document['id']] = $document['title'];

                }

                return $newResult;
            }


        } else {

            $query['collection'] = ['$in' => $name];

            $collection = $this->setCollection($option['collection']);

            $result = $collection->find($query, $filter);

            $resultArr = [];

            foreach ($result as $key => $document) {

                $resultArr[$document->collection][] = $document;
            }

            if ($type == 'all') return $resultArr;

            $newResult = [];

            //Xử lý trường hợp $option['type'] = 'key_val'
            if ($type == 'key_val') {


                foreach ($resultArr as $key => $sub_collection) {

                    $sub_collection = (array)$sub_collection;
                    $tmp = [];
                    foreach ($sub_collection as $key_2 => $document) {


                        $tmp['id'] = $document['id'];
                        $tmp['title'] = $document['title'];
                        $newResult[$key][] = $tmp;
                    }

                }


                return $newResult;
            }


            //Xử lý trường hợp $option['type'] = 'key_val_select'
            if ($type == 'key_val_select') {


                foreach ($resultArr as $key => $sub_collection) {

                    $sub_collection = (array)$sub_collection;

                    $tmp = [];
                    foreach ($sub_collection as $key_2 => $document) {

                        $tmp[$document['id']] = $document['title'];

                    }

                    $newResult[$key] = $tmp;

                }


                return $newResult;
            }

        }

    }

    public function getOffset($p = 1)
    {
        $skip = 0;

        $p = (int)$p;

        if ($p > 0) {
            $skip = ($p - 1) * $this->_limit;
        }


        return $skip;
    }


    public function checkDbExist($coll = '', $value = '', $field = '', $options = [])
    {

        //echo '<br/>' . __METHOD__;

        //$options = field | except => ["field"=> 'id', 'val'=> 1];

        if (empty($coll) || empty($value) || !isset($field)) return false;


        $collection = $this->setCollection($coll);

        $document = $collection->findOne([$field => (string)$value]);

        $except = @$options['except'];


        if (is_array($except)) {

            //MongoDB: db.getCollection(COL_PREFIX.COL_GROUP).find({'slug':'abc-123','id':{'$ne':6}})
            if (isset($except['field']) && !empty($except['field']) && isset($except['val']) && !empty($except['val'])) {
                $filter = [];
                $exceptField = $except['field'];
                $exceptVal = $except['val'];
                $filter = [$field => (string)$value, $exceptField => ['$ne' => (int)$exceptVal]];
                if (isset($except['collection']) && !empty($except['collection'])){
                    $filter['collection'] = $except['collection'];
                }
                $document = $collection->findOne($filter);
            }
        }

        $id = (int)@$document->id;

        if ($id > 0) {
            return $id;
        } else {
            return false;
        }

    }

    public function curlbase64url($data_post = array())
    {
        if (empty($data_post['linkBuild']) || empty($data_post['fileName']) || empty($data_post['pathQRcode'])) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_UPLOAD_64);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}