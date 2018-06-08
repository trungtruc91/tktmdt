<?php
/**
 * Created by PhpStorm.
 * User: HAOHAO100
 * Date: 6/9/2018
 * Time: 12:48 AM
 */

namespace Zs\Face\Controllers;
use Phalcon\Http\Request;

class InteractController extends LoginController
{
    public $_access='';
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

    public function postAction()
    {

        $params = $this->_request->get();
        $data = 'access_token=' .$this->_access . '&url=' . $params['form']['txtLink'] . '&caption=' . $params['form']['txtCaption'];
        $url = 'https://graph.facebook.com/v2.12/me/photos';
        $this->_client->setPost($data);
        $response = $this->_client->createCurl($url);

        $this->view->result = json_decode($response, 1)['post_id'];
    }

    public function reactionAction()
    {
        $params = $this->_request->get();
        $token =$this->_access;
        $id_post = $params['form']['txtId_post'];
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

        $this->view->result = $data;
    }
}