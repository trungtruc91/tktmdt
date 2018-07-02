<?php

namespace Zs\Face\Controllers;
use Phalcon\Http\Request;
use Zs\Face\Models\Customers;
use Zs\Face\Models\OrderInfo;
use Zs\Face\Models\Order;
use Zs\Face\Models\User;

class CustomerController extends LoginController
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
    public function submitOrderAction()
    {
        $params = $this->_request->get();
        $qrUser = new User();
        $orderInfo = new OrderInfo();
        $qrCustomer=new Customers();
        $dataUser = $qrUser->getItemById($_SESSION['userData']['id'])->toArray()[0];
        $idCreate = time();
        foreach ($params['data'] as $value) {
            $name = $value['name'];
            $dataCustomer[$name] = $value['value'];
        }

        $data = [
//            "id_customer" => $dataCustomer['id_customer'],
//            "code_province" => $dataCustomer['province'],
//            "id_create" => $idCreate,
            "token" => $this->_token_ghn,
            "PaymentTypeID" => (int)$dataCustomer['PaymentTypeID'],//người nhận trả tiền
            "FromDistrictID" => (int)$dataUser['district_code'],
            "FromWardCode" => $dataUser['wards_code'],
            "ToDistrictID" => (int)$dataCustomer['district'],
            "ToWardCode" => $dataCustomer['wards'],
            "Note" => $dataCustomer['txtNote'],
            "SealCode" => "tem niêm phong",
            "ExternalCode" => "",
            "ClientContactName" => $dataUser['full_name'],
            "ClientContactPhone" => $dataUser['number_phone'],
            "ClientAddress" => $dataUser['address'],
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
            "ReturnContactName" => "",
            "ReturnContactPhone" => "",
            "ReturnAddress" => "",
            "ReturnDistrictCode" => "",
            "ExternalReturnCode" => "",
            "IsCreditCreate" => true
        ];


        $this->_client->setPost(json_encode($data));
        $response = $this->_client->createCurl($this->_url_createOrder);
        $dataResponse = json_decode($response, 1);
        if ($dataResponse['code'] == 1) {
            $qr = new Order();
//            $dataResponse['data']['userId'] = $_SESSION['userData']['id'];
            $dataResponse['data']['CustomerID'] = $dataCustomer['id_customer'];
//            $dataResponse['data']['id_create'] = $idCreate;
            $qr->save($dataResponse['data']);

            $data['OrderInfoID']=$idCreate;
            $data['OrderID']=$dataResponse['data']['OrderID'];
            $data['ProvinceCode']=$dataCustomer['province'];
            $orderInfo->save($data);
        }
        if(!empty($qrCustomer->getItemById($dataCustomer['id_customer']))){
            $data['id_customer']=$dataCustomer['id_customer'];
            $qrCustomer->save($data);
        }
        echo $response;
        $this->view->disable();

    }

    public function selectPageAction()
    {
        //get info post of user
        $id_user_app = $_SESSION['userData']['id'];
        $post = "/$id_user_app/accounts";
        $accessToken = $this->_access;

        try {
            // Returns a `Facebook\FacebookResponse` object
            $responsePost = $this->FB->get(
                $post,
                $accessToken
            );
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        $total_post = [];
        $posts_response = $responsePost->getGraphEdge();

        if ($this->FB->next($posts_response)) {
            $dem = 0;
            while ($posts_paging = $this->FB->next($posts_response)) {
                $response_array = $posts_response->asArray();
                if ($dem == 0) {
                    $total_post = array_merge($total_post, $response_array);
                }
                $posts_response = $posts_paging;
                $response_array = $posts_response->asArray();
                $total_post = array_merge($total_post, $response_array);
                $dem++;

            }

        } else {
            $posts_response = $responsePost->getGraphEdge()->asArray();
            $total_post = array_merge($total_post, $posts_response);
        }

        $this->view->result = $total_post;
    }

    public function conversationAction()
    {
        //get info post of user

        $params = $this->_request->get();
        if (isset($params['id_page'])) {
            $qr = new User();
            $dataUser = $qr->getItemById($_SESSION['userData']['id'])->toArray();

//            $_SESSION['userData']['DistrictID']=$dataUser[0]['district_code'];
            $dis = $this->getDistrict($this->_url_district, $this->_token_ghn)['data'];
            $wards = $this->getDistrict($this->_url_wards, $this->_token_ghn)['data'];
            foreach ($dis as $value) {
                $province[$value['ProvinceID']] = $value['ProvinceName'];
            }
            $this->view->wards = $wards;
            $this->view->district = $dis;
            $this->view->allProvince = array_unique($province);
            $id_page = $params['id_page'];
            $post = "/$id_page/conversations?fields=updated_time,senders";

            $accessToken_page = $params['access_token'];
            $_SESSION['token_page'] = $accessToken_page;


            try {
                // Returns a `Facebook\FacebookResponse` object
                $responsePost = $this->FB->get(
                    $post,
                    $accessToken_page
                );
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }


            $total_post = [];
            $posts_response = $responsePost->getGraphEdge();

            if ($this->FB->next($posts_response)) {
                $dem = 0;
                while ($posts_paging = $this->FB->next($posts_response)) {
                    $response_array = $posts_response->asArray();
                    if ($dem == 0) {
                        $total_post = array_merge($total_post, $response_array);
                    }
                    $posts_response = $posts_paging;
                    $response_array = $posts_response->asArray();
                    $total_post = array_merge($total_post, $response_array);
                    $dem++;

                }

            } else {
                $posts_response = $responsePost->getGraphEdge()->asArray();
                $total_post = array_merge($total_post, $posts_response);
            }
            $this->view->result = $total_post;
        }
    }

    public function messageAction()
    {

        $params = $this->_request->get();

        $idConv = $params['data'];
        //get info post of user
        $post = "/$idConv/messages?fields=message,from,created_time";
        $accessTokenPage = $_SESSION['token_page'];


        try {
            // Returns a `Facebook\FacebookResponse` object
            $responsePost = $this->FB->get(
                $post,
                $accessTokenPage
            );
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $total_post = [];
        $posts_response = $responsePost->getGraphEdge();

        if ($this->FB->next($posts_response)) {
            $dem = 0;
            while ($posts_paging = $this->FB->next($posts_response)) {
                $response_array = $posts_response->asArray();
                if ($dem == 0) {
                    $total_post = array_merge($total_post, $response_array);
                }
                $posts_response = $posts_paging;
                $response_array = $posts_response->asArray();
                $total_post = array_merge($total_post, $response_array);
                $dem++;

            }

        } else {
            $posts_response = $responsePost->getGraphEdge()->asArray();
            $total_post = array_merge($total_post, $posts_response);
        }

        $format = array_reverse($total_post);
        echo json_encode($format);
        $this->view->disable();
    }

    public function getWardByIdAction()
    {
        $params = $this->_request->get();
        $response = $this->getDistrict($this->_url_wards, $this->_token_ghn);
        $stt = 0;
        foreach ($response['data']['Wards'] as $val) {
            if ($val['DistrictID'] == $params['idDistrict']) {
                $idDistrict = $val['DistrictID'];
                $data[$idDistrict][$stt]['WardCode'] = $val['WardCode'];
                $data[$idDistrict][$stt]['WardName'] = $val['WardName'];
                $stt++;
            }
        }

        echo json_encode($data);
        $this->view->disable();
    }

    public function getDistrictByIdAction()
    {

        $params = $this->_request->get();
        $response = $this->getDistrict($this->_url_district, $this->_token_ghn);
        $stt = 0;
        foreach ($response['data'] as $val) {
            if ($val['ProvinceID'] == $params['idProvince']) {
                $idProvince = $val['ProvinceID'];
                $data[$idProvince][$stt]['DistrictID'] = $val['DistrictID'];
                $data[$idProvince][$stt]['DistrictName'] = $val['DistrictName'];
                $stt++;
            }
        }

        echo json_encode($data);

        $this->view->disable();
    }

    public function sendMessageAction()
    {
        $params = $this->_request->get();

        $idSend = $params['data'];
        $text = $params['text'];
        //get info post of user
        $endpoint = "/$idSend/messages";
        $paramsFb = ['message' => $text];
        $accessTokenPage = $_SESSION['token_page'];

        try {
            // Returns a `Facebook\FacebookResponse` object
            $responsePost = $this->FB->post(
                $endpoint,
                $paramsFb,
                $accessTokenPage
            );
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $posts_response = $responsePost->getGraphNode();
        echo $posts_response;
        $this->view->disable();

    }

    private function getDistrict($url, $token)
    {
        $data = ['Token' => $token];
        $this->_client->setPost(json_encode($data));
        $response = json_decode($this->_client->createCurl($url), 1);

        return $response;
    }


}