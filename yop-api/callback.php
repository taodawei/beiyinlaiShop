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
$result = callback($data);
file_put_contents('logs/'.date("Y-m-d").'_callback.txt',$result.PHP_EOL,FILE_APPEND);
echo "SUCCESS";
?>