<?php
 include 'conf.php';
require_once ("./lib/YopClient.php");
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php");

function callback($source){
	 
       global $merchantno;
	   global $private_key;
	   global $yop_public_key;
    return YopSignUtils::decrypt($source,$private_key, $yop_public_key);

}

$data = $_REQUEST["response"];
file_put_contents('callback_qianzhagn.txt',json_encode($data,JSON_UNESCAPED_UNICODE));
callback($data);
echo "SUCCESS";
?>