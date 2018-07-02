<?php

namespace Zs\Face\Controllers;

use Facebook\Facebook as face;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Zs\Face\Models\Page;
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
    public $_url_cancelOrder = 'https://console.ghn.vn/api/v1/apiv3/CancelOrder';
    public $_client = '';

    public function initialize()
    {
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

    public function indexAction()
    {
        $face = $this->FB;
        $this->view->helper = $face->getRedirectLoginHelper();
        if (isset($_SESSION['userData']['id'])) {
            $this->response->redirect("/face/index/profile");
        } else {
            $this->view->setMainView('login/index');
        }
    }

    public function faceCallBackAction()
    {
        $query = new User();
        $face = $this->FB;
        $helper = $face->getRedirectLoginHelper();
        $_SESSION['FBRLH_state'] = $_GET['state'];
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
        $profileUser = $query->getItemById($userData['id'])->toArray();
        $_SESSION['userData'] = $userData;
        if (empty($profileUser)) {
            $query->save($userData);
        } else {
            $_SESSION['userData']['DistrictID'] = $profileUser[0]['district_code'];
        }
        $this->savePage($accessToken,$userData['id']);
        $_SESSION['access_token'] = (string)$accessToken;

        $this->response->redirect("/face/index/profile");
    }

    private function savePage($accessToken,$userID)
    {
        $dtPage = $this->getPage($accessToken, $userID);
        $qrPage=new Page();

        foreach ($dtPage as $value){
            $checkExist=$qrPage->getItemById($userID,$value['id'])->toArray();
            if(empty($checkExist)){
                $value['userID']=$userID;
                $qrPage->save($value);
            }
        }

    }

    private function getPage($access_token, $userId)
    {
        //get info post of user
        $id_user_app = $userId;
        $post = "/$id_user_app/accounts";
        $accessToken = $access_token;

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

        return $total_post;
    }


}