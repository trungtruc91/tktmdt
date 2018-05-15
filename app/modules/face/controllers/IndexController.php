<?php
namespace Zs\Face\Controllers;

use Phalcon\Mvc\Controller ;
use Phalcon\Http\Request;
use Zs\Library\Core\WebClient;
use Zs\Face\Models\Index;
class IndexController extends Controller
{
    public $_client='';
    public $_request='';
    public function initialize(){
        $this->_client = new WebClient("https://google.com");
        $this->_request=new Request();
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

//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";die;

    }



}