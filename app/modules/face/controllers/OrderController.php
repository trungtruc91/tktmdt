<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 6/9/2018
 * Time: 12:49 AM
 */

namespace Zs\Face\Controllers;

use Phalcon\Http\Request;
use Zs\Face\Models\OrderCreate;
use Zs\Face\Models\Ordered;

class OrderController extends LoginController
{
    public function initialize()
    {
        parent::initialize();
        session_start();
//        $this->_client = new WebClient("https://google.com");
        $this->_request = new Request();
        if (!isset($_SESSION['userData'])) {
            $this->response->redirect('http://tructt.laptrinhaz.com/face/login/index');
        }
        $this->_access = $_SESSION['access_token'];
    }
    public function cancelOrderAction(){
        $params=$this->_request->get();
        $response=$this->getCancelOrder($this->_url_cancelOrder,$this->_token_ghn,$params['orderCode']);

        if($response['code']==1){
            $qrOrdered=new Ordered();
            $qrOrderCreate=new OrderCreate();
            $qrOrdered->delete($params['id_create']);
            $qrOrderCreate->delete($params['id_create']);
        }
        echo json_encode($response);
        $this->view->disable();
    }

    public function orderedAction()
    {
        $qrOrdered = new Ordered();
        $dtOrder = $qrOrdered->getItemById($_SESSION['userData']['id'])->toArray();
        $this->view->result = $dtOrder;
    }

    public function updateOrderAction()
    {
        $params = $this->_request->get();

        if (isset($params['id_order'])) {
            $id_order = $params['id_order'];
            $query = new Ordered();
            $data = $query->join($id_order);
            foreach ($data as $val) {
                $dtNew[] = $val;
            }
            $dtService = $this->getService($this->_url_findServiceAvailable, $this->_token_ghn, $this->jsonParse($dtNew)[0]['info_ordered'][0]);
            $dis = $this->getDistrict($this->_url_district, $this->_token_ghn)['data'];
            $wards = $this->getDistrict($this->_url_wards, $this->_token_ghn)['data'];
            foreach ($dis as $value) {
                $province[$value['ProvinceID']] = $value['ProvinceName'];
            }
            $this->view->service = $dtService;
            $this->view->wards = $wards;
            $this->view->district = $dis;
            $this->view->allProvince = array_unique($province);
            $this->view->result = $this->jsonParse($dtNew);
        }
    }

    private function getService($url, $token, $arrParams = [])
    {

        $data = ['Token' => $token,
            "Weight" => $arrParams['Weight'],
            "Length" => $arrParams['Length'],
            "Width" => $arrParams['Width'],
            "Height" => $arrParams['Height'],
            "FromDistrictID" => $arrParams['FromDistrictID'],
            "ToDistrictID" => $arrParams['ToDistrictID'],
            "CouponCode" => $arrParams['CouponCode']
        ];

        $this->_client->setPost(json_encode($data));
        $response = json_decode($this->_client->createCurl($url), 1);
        return $response;
    }

    private function getCancelOrder($url, $token, $code)
    {
        $data = ['Token' => $token,
            'OrderCode' => $code
        ];
        $this->_client->setPost(json_encode($data));
        $response = json_decode($this->_client->createCurl($url), 1);
        return $response;
    }
}