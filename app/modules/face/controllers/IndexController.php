<?php

namespace Zs\Face\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Zs\Face\Models\OrderInfo;
use Zs\Face\Models\Order;
use Zs\Face\Models\User;
//use Zs\Library\Core\WebClient;

use Zs\Face\Models\Index;

class IndexController extends LoginController
{

//    public $_client = '';
    public $_request = '';
    public $_access = '';
    public $_access_page = '';
//    public $_token_ghn = '5b06841f1070b07345645cf1';
//    public $_url_district = 'https://console.ghn.vn/api/v1/apiv3/GetDistricts';
//    public $_url_wards = 'https://console.ghn.vn/api/v1/apiv3/GetWards';
//    public $_url_createOrder = 'https://console.ghn.vn/api/v1/apiv3/CreateOrder';
//    public $_url_updateOrder = 'https://console.ghn.vn/api/v1/apiv3/UpdateOrder';
//    public $_url_findServiceAvailable = 'https://console.ghn.vn/api/v1/apiv3/FindAvailableServices';
//    public $_url_cancelOrder= 'https://console.ghn.vn/api/v1/apiv3/CancelOrder';

    public function initialize()
    {
        parent::initialize();
        session_start();
//        $this->_client = new WebClient("https://google.com");
        $this->_request = new Request();
        if (!isset($_SESSION['userData'])) {
            $this->response->redirect('/login.html');
        }
        $this->_access = $_SESSION['access_token'];
        $this->view->titlePage='Profile';

    }


    public function indexAction()
    {

    }

public function profileAction()
{

    $query = new User();
    $params = $this->_request->get();
    $data = $query->getItemById($_SESSION['userData']['id'])->toArray()[0];
    $dis = $this->getDistrict($this->_url_district, $this->_token_ghn)['data'];
    $wards = $this->getDistrict($this->_url_wards, $this->_token_ghn)['data'];
    foreach ($dis as $value) {
        $province[$value['ProvinceID']] = $value['ProvinceName'];
    }

    if ($this->_request->isPost()) {
        $document = $params['form'];

        $data = [
            'id' => $_SESSION['userData']['id'],
            'full_name' => $document['name'],
            'email' => $document['email'],
            'address' => $document['address'],
            'district_code' => $document['district'],
            'province_code' => $document['province'],
            'wards_code' => $document['wards'],
            'number_phone' => $document['sdt']
        ];

        $query->update($data);
    }
    $this->view->data = $data;
    $this->view->wards = $wards;
    $this->view->district = $dis;
    $this->view->allProvince = array_unique($province);

}
    private function getDistrict($url, $token)
    {
        $data = ['Token' => $token];
        $this->_client->setPost(json_encode($data));
        $response = json_decode($this->_client->createCurl($url), 1);

        return $response;
    }

}