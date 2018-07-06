<?php


namespace Zs\Face\Controllers;


use Phalcon\Http\Request;
use Zs\Face\Models\Post;

class PostController extends LoginController
{
    public $_access='';
    public $_request='';
    public function initialize()
    {
        parent::initialize();
        session_start();
        $this->_request = new Request();
        if (!isset($_SESSION['userData'])) {
            $this->response->redirect('/login.html');
        }
        $this->_access = $_SESSION['access_token'];
        $this->view->titlePage='Post';

    }

    public function indexAction()
    {
        $params = $this->_request->get();
        if(isset($params['id_page'])) {
            if($this->_request->isPost()) {
                $qrPost = new Post();
                $data = 'access_token=' . $params['access_token'] . '&url=' . $params['form']['txtLink'] . '&caption=' . $params['form']['txtCaption'];
                $url = "https://graph.facebook.com/v2.12/{$params['id_page']}/photos";
                $this->_client->setPost($data);
                $response = $this->_client->createCurl($url);

                $data = [
                    'PostID' => json_decode($response, 1)['post_id'],
                    'Caption' => $params['form']['txtCaption'],
                    'UserID' => $_SESSION['userData']['id'],
                    'PageID'=>$params['id_page']
                ];
                $qrPost->save($data);
            }
            $this->view->result = json_decode($response, 1)['post_id'];
        }else{
            $this->response->redirect('/login.html');

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
}