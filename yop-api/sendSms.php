<?php
session_start();
if(empty($_SESSION['demo_comId'])){
	die('尚未登陆');
}
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


function sms(){
	

	global $merchantNo;
	global $parentMerchantNo;
	global $private_key;
	global $yop_public_key;
	global $appKey;
	$request = new YopRequest($appKey, $private_key);
	$request->addParam("parentMerchantNo", $parentMerchantNo);
	$request->addParam("merchantNo", $_REQUEST['merchantNo']);
	$request->addParam("phone", $_REQUEST['phone']);
	$request->addParam("sourceType", $_REQUEST['sourceType']);



	$response = YopClient3::post("/rest/v1.0/merchantService/mer-out-invoke/send-mer-sms-notify", $request);
	if($response->validSign==1){
        //echo "返回结果签名验证成功!\n";
	}
    //取得返回结果
	$data=object_array($response);

	return $data;

}
$array=sms();  

if( $array['result'] == NULL)
{
	echo "error:".json_encode($array['error'],JSON_UNESCAPED_UNICODE);
	return;}
	else{
		$result= $array['result'] ;
		if($result['returnCode']=='REG00000'){
			echo '<script>location.href="change.php?merchantNo='.$_REQUEST['merchantNo'].'&requestNo='.$_REQUEST['requestNo'].'";</script>';
			exit;
		}
	}
	?> 


	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title> 发送短信验证码接口返回参数</title>
	</head>
	<body>	
		<br /> <br />
		<table width="70%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
				<th align="center" height="30" colspan="5" bgcolor="#6BBE18">
					发送短信验证码接口返回参数
				</th>
			</tr>

			<tr >
				<td width="25%" align="left">&nbsp;返回码</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <?php echo $result['returnCode'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnCode</td> 
			</tr>

			<tr>
				<td width="25%" align="left">&nbsp;返回信息</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['returnMsg'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnMsg</td> 
			</tr>




		</table>

	</body>
	</html>