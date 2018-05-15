<?php
namespace Zs\Library\Core;
// Reference: mycurl.php
class WebClient { 
     protected $_useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36'; 
     protected $_url; 
     protected $_followlocation; 
     protected $_timeout; 
     protected $_maxRedirects; 
     protected $_cookieFileLocation = './cookie.txt'; 
     protected $_post; 
     protected $_postFields; 
     protected $_referer ="http://www.google.com"; 

     protected $_session; 
     protected $_webpage; 
     protected $_includeHeader; 
     protected $_noBody; 
     protected $_status; 
     protected $_binaryTransfer; 
     public    $authentication = 0; 
     public    $auth_name      = ''; 
     public    $auth_pass      = ''; 

     public function useAuth($use){ 
       $this->authentication = 0; 
       if($use == true) $this->authentication = 1; 
     } 

     public function setName($name){ 
       $this->auth_name = $name; 
     } 
     public function setPass($pass){ 
       $this->auth_pass = $pass; 
     } 

     public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false) 
     { 
         $this->_url = $url; 
         $this->_followlocation = $followlocation; 
         $this->_timeout = $timeOut; 
         $this->_maxRedirects = $maxRedirecs; 
         $this->_noBody = $noBody; 
         $this->_includeHeader = $includeHeader; 
         $this->_binaryTransfer = $binaryTransfer; 

         $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt'; 

     } 

     public function setReferer($referer){ 
       $this->_referer = $referer; 
     } 

     public function setCookieFileLocation($path) 
     { 
         $this->_cookieFileLocation = $path; 
     } 

     public function setGet()
     {
        $this->_post = false; 
     }

     public function setPost ($postFields) 
     { 
        $this->_post = true; 
        $this->_postFields = $postFields; 
     } 

     public function setUserAgent($userAgent) 
     { 
         $this->_useragent = $userAgent; 
     } 

     public function createCurl($url = 'nul') 
     { 
        if($url != 'nul'){ 
          $this->_url = $url; 
        } 

         $s = curl_init(); 

         curl_setopt($s,CURLOPT_URL,$this->_url); 
         curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:')); 
         curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout); 
         curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects); 
         curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
         curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation); 
         curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation); 
         curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);
         curl_setopt($s, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($s, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt($s, CURLOPT_FAILONERROR, 0); 

         if($this->authentication == 1){ 
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass); 
         } 
         if($this->_post) 
         { 
             curl_setopt($s,CURLOPT_POST,true); 
             curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields); 

         } 

         if($this->_includeHeader) 
         { 
               curl_setopt($s,CURLOPT_HEADER,true); 
         } 

         if($this->_noBody) 
         { 
             curl_setopt($s,CURLOPT_NOBODY,true); 
         } 
         /* 
         if($this->_binary) 
         { 
             curl_setopt($s,CURLOPT_BINARYTRANSFER,true); 
         } 
         */ 
         curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent); 
         curl_setopt($s,CURLOPT_REFERER,$this->_referer); 

         $this->_webpage = curl_exec($s); 
         $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE);

         curl_close($s);

         return $this->_webpage;
     } 

   public function getHttpStatus() 
   { 
       return $this->_status; 
   } 

   public function __tostring(){ 
      return $this->_webpage; 
   } 
} 
?> 