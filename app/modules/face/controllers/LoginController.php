<?php

namespace Zs\Face\Controllers;

use Facebook\Facebook as face;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Zs\Face\Models\User;
use Zs\Library\Core\WebClient;

class LoginController extends Controller
{
    public $FB;
    public $area = 'facebook';
    public $_token_ghn = '5b06841f1070b07345645cf1';
    public $_url_district = 'https://console.ghn.vn/api/v1/apiv3/GetDistricts';
    public $_url_wards = 'https://console.ghn.vn/api/v1/apiv3/GetWards';
    public $_url_createOrder = 'https://console.ghn.vn/api/v1/apiv3/CreateOrder';
    public $_url_updateOrder = 'https://console.ghn.vn/api/v1/apiv3/UpdateOrder';
    public $_url_findServiceAvailable = 'https://console.ghn.vn/api/v1/apiv3/FindAvailableServices';
    public $_url_cancelOrder= 'https://console.ghn.vn/api/v1/apiv3/CancelOrder';
    public $_client = '';
    public function initialize(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        session_start();
        $this->_client = new WebClient("https://google.com");
        //thiết lập facebook
        $arr = [
            "app_id" => '1989842641270387',
            "app_secret" => '126cdf42eca6fd31e772cba6a15298f9',
            "default_graph_version" => 'v2.12'
        ];
        $this->FB = new face($arr);

    }
    public function indexAction(){
        $face = $this->FB;
        $this->view->helper = $face->getRedirectLoginHelper();
//        $this->view->setLayout('login/index');
        $this->view->setMainView('login/index');
    }
    public function faceCallBackAction()
    {
        $query=new User();
        $face = $this->FB;
        $helper = $face->getRedirectLoginHelper();
        $_SESSION['FBRLH_state']=$_GET['state'];
        try {
            $accessToken = $helper->getAccessToken("http://tructt.laptrinhaz.com/face/login/facecallback");
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo "Reponse Exception:" . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo "SDK Exception" . $e->getMessage();
        }
        if (!$accessToken) {
            $this->response->redirect("/face/index/login");
        }
        $oAuth2Client = $this->FB->getOAuth2Client();

        if (!$accessToken->isLongLived()) {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        }

        $response = $this->FB->get("/me?fields=id, first_name,age_range,address, last_name, email, picture.type(large)", $accessToken);
        $userData = $response->getGraphNode()->asArray();
        $profileUser=$query->getItemById($userData['id'])->toArray();
        $_SESSION['userData'] = $userData;
        if(empty($profileUser)) {
            $query->save($userData);
        }else{
            $_SESSION['userData']['DistrictID']=$profileUser[0]['district_code'];
        }
        phpinfo();
        $_SESSION['access_token'] = (string)$accessToken;

        $this->response->redirect("/face/index/profile");
    }


}