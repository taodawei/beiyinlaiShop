<?php
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$uniqueOrderNo = $_REQUEST['yibao_orderId'];
$money = $_REQUEST['money'];
$orderId = $_REQUEST['orderId'];
$verify = $_REQUEST['verify'];
$ledgerNo = $_REQUEST['ledgerNo'];
$ledgerName = $_REQUEST['ledgerName'];
$payId = (int)$_REQUEST['payId'];
//file_put_contents('request1.txt',json_encode($_REQUEST,JSON_UNESCAPED_UNICODE));
if(md5(substr($uniqueOrderNo.$money,0,10))!=$verify){
	die('verify error');
}
include 'conf.php';
require_once ("./lib/YopClient3.php");
$shouxufei = 0;
$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$orderId;
$data['uniqueOrderNo']=$uniqueOrderNo;
$data['refundRequestId']="DS" . date("ymd_His") . rand(10, 99);
$data['refundAmount']=$money-$shouxufei;

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
function refund($hmac,$arr){
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
	$request->addParam("refundRequestId", $arr['refundRequestId']); 
	$request->addParam("refundAmount", $arr['refundAmount']);
	$request->addParam("description", '');
	$request->addParam("memo", '');
	if(!empty($_REQUEST['ledgerNo'])){
		//$tuifei = $arr['refundAmount'] - round($arr['refundAmount']*7/1000,2);
		$request->addParam("accountDivided",'[{"ledgerNo":"'.$_REQUEST['ledgerNo'].'","amount":'.$arr['refundAmount'].',"ledgerName":"'.$_REQUEST['ledgerName'].'"}]');
	}
	//$request->addParam("refundStrategy", $_REQUEST['refundStrategy']);
	$request->addParam("notifyUrl",'http://buy.zhishangez.com/yop-api/callback.php');
	$request->addParam("hmac",$hmac);
	file_put_contents('request1.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	$response = YopClient3::post("/rest/v1.0/sys/trade/refund", $request);
	file_put_contents('logs/'.date("Y-m-d").'_refund.txt',json_encode($response,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
	if($response->validSign==1){
		//echo "返回结果签名验证成功!\n";
	}
    //取得返回结果
	$data=object_array($response);
	return $data;
}
$if_tuikuan = 0;
if(empty($payId) && empty($ledgerNo)){
	$if_tuikuan = 1;
}else if(!empty($payId)){
	$nums = $db->get_var("select count(*) from demo_yibao_fenzhang where payId=$payId and status=1");
	if($nums==0){
		$if_tuikuan = 1;
	}
}
if($if_tuikuan==1){
	$arr=array();
	$arr['parentMerchantNo']=$parentMerchantNo;
	$arr['merchantNo']=$merchantNo;
	$arr['orderId']=$orderId;
	$arr['uniqueOrderNo']=$uniqueOrderNo;
	$hmacstr = hash_hmac('sha256', toString($arr), $hmacKey, true);
	$hmacss = bin2hex($hmacstr);
	fullsettle($hmacss,$arr);
}
$array=refund($hmac,$data);  
if( $array['result'] == NULL)
{
	//echo "error:".$array['error'];
	return;
}else{
	$result= $array['result'] ;
	if($result['status']=='SUCCESS' && !empty($ledgerNo)){
		$yibao_fenzhang = array();
		$yibao_fenzhang['comId'] = (int)$_REQUEST['comId'];
		$yibao_fenzhang['money'] = -($money-$shouxufei);
		$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
		$yibao_fenzhang['orderId'] = (int)$_REQUEST['oid'];
		$yibao_fenzhang['payId'] = 0;
		$yibao_fenzhang['type'] = 2;
		$yibao_fenzhang['income_type'] = 1;
		$yibao_fenzhang['status'] = 2;
		$yibao_fenzhang['ledgerNo'] = $ledgerNo;
		$yibao_fenzhang['ledgerName'] = $ledgerName;
		$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
	}
}