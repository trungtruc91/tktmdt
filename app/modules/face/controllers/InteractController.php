<?php


namespace Zs\Face\Controllers;
use Phalcon\Http\Request;
use Zs\Face\Models\Member;
use Zs\Face\Models\Post;

class InteractController extends LoginController
{
    public $_access='';
    public $_request='';
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
        $this->view->titlePage='Interaction';
    }

    public function reactionAction()
    {
        $params = $this->_request->get();
        if(isset($params['PostID'])) {
            $token = $params['access_token'];
            $id_post = $params['PostID'];
            $option = $params['form']['slcReaction'];
            $url = "https://graph.facebook.com/v2.12/$id_post/$option?access_token=$token";

            while (1) {
                $response = json_decode($this->_client->createCurl($url), 1);

                $next = @$response['paging']['next'];
                $data[] = $response['data'];
                if (isset($next)) {
                    $url = $next;
                } else {
                    break;
                }
            }
            if($this->_request->isPost()){
                $this->view->select=$params['form']['slcReaction'];
                $this->view->postId=$id_post;
            }
            $this->view->result = $data;
        }
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

    public function listPostAction(){
        $params=$this->_request->get();
        if(isset($params['PageID'])){
            $qrPost=new Post();
            $result=$qrPost->getItemById($params['PageID'])->toArray();
            $this->view->result=$result;
            $this->view->token=$params['access_token'];
        }

    }

    public function submitMemberAction(){
        $params=$this->_request->get();
        if(isset($params['arrData'])){
            $qrMember=new Member();
            foreach ($params['arrData'] as $key => $value){
                $checkExist=$qrMember->getItem($value['MemberID'],$value['TypeInteract'])->toArray();
                if(empty($checkExist)){
                    $qrMember->save($params['arrData'][$key]);
                }
            }
            $noti=['code'=>1,'message'=>'success'];
            echo json_encode($noti);
            $this->view->disable();
        }

    }

}