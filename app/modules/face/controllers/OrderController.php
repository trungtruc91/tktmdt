<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 6/9/2018
 * Time: 12:49 AM
 */

namespace Zs\Face\Controllers;

use Phalcon\Http\Request;
use Zs\Face\Models\Customers;
use Zs\Face\Models\OrderInfo;
use Zs\Face\Models\Order;
use Zs\Face\Models\User;

class OrderController extends LoginController
{
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
        $this->view->titlePage='Orders';

    }

    public function cancelOrderAction()
    {
        $params = $this->_request->get();
        if(isset($params['order_id'])) {
            $response = $this->getCancelOrder($this->_url_cancelOrder, $this->_token_ghn, $params['orderCode']);

            if ($response['code'] == 1) {
                $qrOrdered = new Order();
                $qrOrderCreate = new OrderInfo();
                $qrOrdered->delete((int)$params['order_id']);
                $qrOrderCreate->delete((int)$params['order_id']);
            }
            echo json_encode($response);

        }
        $this->view->disable();
    }

    public function indexAction()
    {
        $qrOrdered = new Customers();
        $dtOrder = $qrOrdered->getAll(['UserID'=>$_SESSION['userData']['id']])->toArray();
        $this->view->result = $dtOrder;
    }

    public function orderListAction()
    {
        $params = $this->_request->get();

        if (isset($params['id'])) {
            $qrOrdered = new Order();
            $dtOrder = $qrOrdered->getItemById($params['id'])->toArray();
            $this->view->result = $dtOrder;
        }


    }

    public function updateOrderAction()
    {
        $params = $this->_request->get();

        if (isset($params['id_order'])) {

            $id_order = $params['id_order'];
            $query = new Order();
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

    public function createOrderAction()
    {
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

    public function submitUpdateOrderAction()
    {
        $params = $this->_request->get();
        $query = new User();
        $qrOrder = new OrderInfo();
        $dataUser = $query->getItemById($_SESSION['userData']['id'])->toArray()[0];
        foreach ($params['data'] as $value) {
            $name = $value['name'];
            $dataCustomer[$name] = $value['value'];
        }

        $data = [
            "ShippingOrderID" => (int)$dataCustomer['OrderID'],
            "OrderCode" => $dataCustomer['OrderCode'],
            "code_province" => $dataCustomer['province'],
            "token" => $this->_token_ghn,
            "PaymentTypeID" => (int)$dataCustomer['PaymentTypeID'],//ngu?i nh?n tr? ti?n
            "FromDistrictID" => (int)$dataUser['DistrictCode'],
            "FromWardCode" => $dataUser['WardCode'],
            "ToDistrictID" => (int)$dataCustomer['district'],
            "ToWardCode" => $dataCustomer['wards'],
            "Note" => $dataCustomer['txtNote'],
            "SealCode" => "tem niêm phong",
            "ExternalCode" => "",
            "ClientContactName" => $dataUser['FullName'],
            "ClientContactPhone" => $dataUser['PhoneNumber'],
            "ClientAddress" => $dataUser['Address'],
            "CustomerName" => $dataCustomer['name'],
            "CustomerPhone" => $dataCustomer['sdt'],
            "ShippingAddress" => $dataCustomer['shipping'],
            "CoDAmount" => (int)$dataCustomer['amount'],
            "NoteCode" => $dataCustomer['NoteCode'],
            "InsuranceFee" => 0,
            "ClientHubID" => 0,
            "ServiceID" => (int)$dataCustomer['ServiceID'],
            "Content" => "Test nội dung",
            "CouponCode" => $dataCustomer['coupon'],
            "Weight" => (float)$dataCustomer['size'],
            "Length" => (float)$dataCustomer['long'],
            "Width" => (float)$dataCustomer['width'],
            "Height" => (float)$dataCustomer['height'],
            "CheckMainBankAccount" => false,
//            "ShippingOrderCosts" =>
//                [
//                    [
//                        "ServiceID" => 53332
//                    ]
//                ],
            "ReturnContactName" => "huy",
            "ReturnContactPhone" => "",
            "ReturnAddress" => "",
            "ReturnDistrictCode" => "",
            "ExternalReturnCode" => "",
            "IsCreditCreate" => true
        ];


        $this->_client->setPost(json_encode($data));
        $response = $this->_client->createCurl($this->_url_updateOrder);
        $dataResponse = json_decode($response, 1);

        if ($dataResponse['code'] == 1) {
            $qrOrder->update($dataCustomer);
            $qr = new Order();
            $qr->update($dataCustomer['ID_create'], $dataResponse['data']);
        }
        echo $response;
        $this->view->disable();

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

    private function jsonParse($data)
    {
        $d = json_encode($data);
        return json_decode($d, 1);
    }

    private function getDistrict($url, $token)
    {
        $data = ['Token' => $token];
        $this->_client->setPost(json_encode($data));
        $response = json_decode($this->_client->createCurl($url), 1);

        return $response;
    }
}