<?php
namespace Zs\Face\Controllers;

use Phalcon\Mvc\Controller ;
use Phalcon\Http\Request;
use Zs\Library\Core\WebClient;

use Zs\Face\Models\Index;
class IndexController extends LoginController
{

    public $_client='';
    public $_request='';
    public $_access='EAAcRwFfKtnMBANIZC7AZBpswJdKXwOsJzXz5aPTgznJi66vMajYA4GjxVzoLP2o036qcxVobLGCXWhlaQS67AztrQ1lDwgPnYzSXbu0ZBPYMmcRWdMildzqmNvMLCj3IF3UqZAoz9vTeW7qbRy1EAnZAcHZAj5fBObXl23kJXXYSbFp9GzzX6SP2tdE5AZCJeQPbYJZAxbeXeQZDZD';
    public function initialize(){
        parent::initialize();
        $this->_client = new WebClient("https://google.com");
        $this->_request=new Request();

//        if(!isset($_SESSION['userData'])){
//            $this->response->redirect('http://tructt.laptrinhaz.com/face/login/index');
//        }
    }
    public function indexAction(){

    }
    public function postAction()
    {

        $params=$this->_request->get();
        $data = 'access_token='.$params['form']['txtToken'].'&url='.$params['form']['txtLink'].'&caption='.$params['form']['txtCaption'];
        $url = 'https://graph.facebook.com/v2.12/me/photos';
        $this->_client->setPost($data);
        $response=$this->_client->createCurl($url);

        $this->view->result=json_decode($response,1)['post_id'];
    }
    public function reactionAction(){
        $params=$this->_request->get();
        $token=$params['form']['txtToken'];
        $id_post=$params['form']['txtId_post'];
        $option=$params['form']['slcReaction'];
        $url = "https://graph.facebook.com/v2.12/$id_post/$option?access_token=$token";




        while (1){
            $response=json_decode($this->_client->createCurl($url),1);

            $next=@$response['paging']['next'];
            $data[]=$response['data'];
            if(isset($next)){
                $url=$next;
            }else{
                break;
            }
        }

        $this->view->result=$data;
    }
    public function conversationAction(){
        //get info post of user
        $post='/871090909744554/conversations?fields=updated_time,senders';
        $accessToken=$this->_access;

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

       $this->view->result=$total_post;
    }
    public function messageAction(){
        $params=$this->_request->get();

        $idConv=$params['data'];
        //get info post of user
        $post="/$idConv/messages?fields=message,from,created_time";
        $accessToken=$this->_access;

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
        $format=array_reverse($total_post);
        echo json_encode($format);
        $this->view->disable();
    }



}