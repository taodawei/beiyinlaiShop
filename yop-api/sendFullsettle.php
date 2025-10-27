<?php
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$uniqueOrderNo = $_REQUEST['yibao_orderId'];
$orderId = $_REQUEST['orderId'];
include 'conf.php';
require_once ("./lib/YopClient3.php");

$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$orderId;
$data['uniqueOrderNo']=$uniqueOrderNo;

$hmacstr = hash_hmac('sha256', toString($data), $hmacKey, true);
$hmac = bin2hex($hmacstr);

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
 #将参数转换成k=v拼接的形式
function toString($arraydata){
	$Str="";
	foreach ($arraydata as $k=>$v){
		$Str .= strlen($Str) == 0 ? "" : "&";
		$Str.=$k."=".$v;
	}
	return $Str;
}

function fullsettle($hmac,$arr){
	global $merchantNo;
	global $parentMerchantNo;
	global $private_key;
	global $yop_public_key;
	global $appKey;
	$request = new YopRequest($appKey, $private_key);
	$request->addParam("parentMerchantNo", $parentMerchantNo);
	$request->addParam("merchantNo", $merchantNo);
	$request->addParam("orderId",$arr['orderId']);
	$request->addParam("uniqueOrderNo",$arr['uniqueOrderNo']);
	$request->addParam("hmac",$hmac);

	$response = YopClient3::post("/rest/v1.0/sys/trade/fullsettle", $request);
	if($response->validSign==1){
		//echo "返回结果签名验证成功!\n";
	}
    //取得返回结果
	$data=object_array($response);
	file_put_contents('logs/'.date("Y-m-d").'_settle.txt',json_encode($data,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
	return $data;

}
$array=fullsettle($hmac,$data);
/*if( $array['result'] == NULL)
{
	return;
}else{
	$result= $array['result'] ;
}*/