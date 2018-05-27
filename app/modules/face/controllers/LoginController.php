<?php

namespace Zs\Face\Controllers;

use Facebook\Facebook as face;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Zs\Face\Models\User;

class LoginController extends Controller
{
    public $FB;
    public $area = 'facebook';
    public function initialize(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        session_start();
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
            $this->response->redirect("/face/index/index");
        }
        $oAuth2Client = $this->FB->getOAuth2Client();

        if (!$accessToken->isLongLived()) {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        }

        $response = $this->FB->get("/me?fields=id, first_name,age_range,address,friends, last_name, email, picture.type(large)", $accessToken);
        $userData = $response->getGraphNode()->asArray();

        if(empty($query->getItemById($userData['id'])->toArray())) {
            $query->save($userData);
        }
        $_SESSION['userData'] = $userData;
        $_SESSION['access_token'] = (string)$accessToken;

        $this->response->redirect("/face/index/index");
    }
    public function conversationAction(){
        //get info post of user
        $post='/871090909744554/conversations';
        $accessToken='EAAcRwFfKtnMBAFEWgbZBGoi0AJdc09kh2s8iwr7ykgQP5ITUkgyMMjuM6GxBJLY3ZCfp2h8IUd3JuYycAu2erZBqsSoewlNhkuK6ZBSLjyVWPDeRV3hUB7JiuOSaN5307YTyajrZB3ioxImVj2kkmCZAJh7bcP2pURnge5OiCtSRrLMxngAG7LBm9lDQxKQ4beleEeDVyVrwZDZD';
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
        echo "<pre>";
        print_r($posts_response);
        echo "</pre>";
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
        echo "<pre>";
        print_r($total_post);
        echo "</pre>";die;
        return $total_post;
    }

}