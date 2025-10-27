<?php	
include 'conf.php';
require_once ("./lib/YopClient3.php");
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
function balancequery(){
	global $merchantNo;
	global $parentMerchantNo;
	global $private_key;
	global $yop_public_key;
	global $appKey;
	$request = new YopRequest($appKey, $private_key);
	$request->addParam("parentMerchantNo", $parentMerchantNo);
	$request->addParam("merchantNo", $_REQUEST['merchantNo']);
	$response = YopClient3::post("/rest/v1.0/sys/merchant/balancequery", $request);
	//var_dump($response);
	if($response->validSign==1){
		//echo "返回结果签名验证成功!\n";
	}
    //取得返回结果
	$data=object_array($response);
	return $data;
}
$array=balancequery();  
if( $array['result'] == NULL)
{
	echo "error:".$array['error'];
	var_dump($array['error']);
	return;
}
else{
	$result= $array['result'] ;
	echo round($result['merBalance'],2);
}