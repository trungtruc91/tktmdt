<?php

namespace Zs\Face\Controllers;

use Facebook\Facebook as face;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;

class LoginController extends Controller
{
    public $FB;
    public $area = 'facebook';
    public function initialize(){

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

        $face = $this->FB;
        $helper = $face->getRedirectLoginHelper();
        $_SESSION['FBRLH_state']=$_GET['state'];

        try {
            $accessToken = $helper->getAccessToken("https://tools.vdevpro.com/admincc/facebook/facecallback");
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo "Reponse Exception:" . $e->getMessage();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo "SDK Exception" . $e->getMessage();
        }
        if (!$accessToken) {
            $this->response->redirect("/admincc/facebook/index");
        }
        $oAuth2Client = $this->FB->getOAuth2Client();

        if (!$accessToken->isLongLived()) {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        }

        $response = $this->FB->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
        $userData = $response->getGraphNode()->asArray();
        $_SESSION['userData'] = $userData;
        $_SESSION['access_token'] = (string)$accessToken;

        $this->response->redirect("/face/index/index");
    }

}