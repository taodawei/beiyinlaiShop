<?php
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$uniqueOrderNo = $_REQUEST['uniqueOrderNo'];
$yibao_orderId = $_REQUEST['yibao_orderId'];
$orderId = (int)$_REQUEST['orderId'];
$payId = (int)$_REQUEST['payId'];
$comId = (int)$_REQUEST['comId'];
include 'conf.php';
require_once ("./lib/YopClient3.php");
$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$yibao_orderId;
$data['uniqueOrderNo']= $uniqueOrderNo;
$data['divideRequestId']= "DS" . date("ymd_His") . rand(10, 99);
$hmacstr = hash_hmac('sha256', toString($data), $hmacKey, true);
$hmac = bin2hex($hmacstr);
if(!empty($payId)){
	$orders = $db->get_results("select * from demo_yibao_fenzhang where payId=$payId and status=1");
	$contractNo = $db->get_var("select orderId from order_pay where id=$payId");
	$divideDetail = array();//分账详细
	if(!empty($orders)){
		foreach ($orders as $ord) {
			$shouxufei = round($ord->money*7/1000,2);
			$fenzhang = array();
			$fenzhang['ledgerNo'] = $ord->ledgerNo;
			$fenzhang['ledgerName'] = $ord->ledgerName;
			$fenzhang['amount'] = $ord->money-$shouxufei;
			$divideDetail[] = $fenzhang;
		}
	}
}else{
	$orders = $db->get_results("select * from demo_yibao_fenzhang where orderId=$orderId and comId=$comId and status=1 limit 1");
	$fenbiao = getFenbiao($comId,20);
	$contractNo = $db->get_var("select orderId from order$fenbiao where id=$orderId");
	$divideDetail = array();//分账详细
	if(!empty($orders)){
		foreach ($orders as $ord) {
			$shouxufei = round($ord->money*7/1000,2);
			$fenzhang = array();
			$fenzhang['ledgerNo'] = $ord->ledgerNo;
			$fenzhang['ledgerName'] = $ord->ledgerName;
			$fenzhang['amount'] = $ord->money-$shouxufei;
			$divideDetail[] = $fenzhang;
		}
	}
}
$data['contractNo'] = $contractNo;
$data['divideDetail'] = json_encode($divideDetail,JSON_UNESCAPED_UNICODE);
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
function divide($hmac,$info){
	global $merchantNo;
	global $parentMerchantNo;
	global $private_key;
	global $yop_public_key;
	global $appKey;
	$request = new YopRequest($appKey, $private_key);
	$request->addParam("parentMerchantNo", $parentMerchantNo);
	$request->addParam("merchantNo", $merchantNo);
	$request->addParam("divideRequestId", $info['divideRequestId']);
	$request->addParam("orderId", $info['orderId']);
	$request->addParam("uniqueOrderNo", $info['uniqueOrderNo']); 
	$request->addParam("divideDetail", $info['divideDetail']);
	$request->addParam("contractNo",$info['contractNo']);
	$request->addParam("hmac",$hmac);
	file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	$response = YopClient3::post("/rest/v1.0/sys/trade/divide", $request);
	if($response->validSign==1){
		echo "返回结果签名验证成功!\n";
	}
    //取得返回结果
	$data=object_array($response);
	file_put_contents('logs/'.date("Y-m-d").'_divide.txt',json_encode($data,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
	return $data;

}
$array=divide($hmac,$data);  
if( $array['result'] == NULL){
	//echo "error:".$array['error'];
	return;
}else{
	$result= $array['result'];
	if($result['status']!='SUCCESS'){
		file_put_contents('logs/divide_error.txt',json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
	}else{
		if(!empty($payId)){
			$db->query("update demo_yibao_fenzhang set status=2 where payId=$payId and status=1");
		}else{
			$db->query("update demo_yibao_fenzhang set status=2 where orderId=$orderId and comId=$comId and status=1 limit 1");
		}
	}
}