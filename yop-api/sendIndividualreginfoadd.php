<?php
session_start();
if(empty($_SESSION['demo_comId'])){
  die('尚未登陆');
}
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');	
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



function individualreginfoadd(){
    
       global $parentMerchantNo;
	   global $private_key;
	   global $yop_public_key;
    global $appKey;

    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("requestNo", $_REQUEST['requestNo']);
    $request->addParam("merFullName", $_REQUEST['merFullName']);
    $request->addParam("merShortName", $_REQUEST['merShortName']);
    $request->addParam("merCertNo", $_REQUEST['merCertNo']);
    $request->addParam("legalName", $_REQUEST['legalName']); 
    $request->addParam("legalIdCard", $_REQUEST['legalIdCard']);
    $request->addParam("merLegalPhone", $_REQUEST['merLegalPhone']);
    $request->addParam("merContactPhone", $_REQUEST['merLegalPhone']);
    $request->addParam("merLegalEmail", $_REQUEST['merLegalEmail']);
    $request->addParam("merLevel1No", $_REQUEST['merLevel1No']);
    $request->addParam("merLevel2No", $_REQUEST['merLevel2No']);
    $request->addParam("merProvince", $_REQUEST['merProvince']);
    $request->addParam("merCity", $_REQUEST['merCity']);
    $request->addParam("merDistrict", $_REQUEST['merDistrict']);
    $request->addParam("merAddress", $_REQUEST['merAddress']); 
    $request->addParam("merScope", $_REQUEST['merScope']);
    $request->addParam("cardNo", $_REQUEST['cardNo']);
    $request->addParam("headBankCode", $_REQUEST['headBankCode']);
    $request->addParam("bankCode", $_REQUEST['bankCode']);
    $request->addParam("bankProvince", $_REQUEST['bankProvince']);
    $request->addParam("bankCity", $_REQUEST['bankCity']);
    //$request->addParam("productInfo", $_REQUEST['productInfo']);
    //$request->addParam("fileInfo", $_REQUEST['fileInfo']);
    $request->addParam("businessFunction", '{"SUBACCOUNT_IS_OPENED":["YES"],"SUBACCOUNT_TYPE":["SUBACCOUNT_ORDER"],"FEE_TYPE":["REALTIME"],"ELEC_SIGN":["URL"]}');
    $request->addParam("notifyUrl", $_REQUEST['notifyUrl']);
    $request->addParam("merAuthorizeType", $_REQUEST['merAuthorizeType']);
    $request->addParam("signCallBackUrl",'https://www.zhishangez.com/yop-api/callback_qianzhang.php?comId='.$_SESSION['demo_comId']);
    $fileInfo = '[';
    $fileInfo .= '{"quaType":"IDCARD_FRONT","quaUrl":"'.$_REQUEST['IDCARD_FRONT'].'"}';
 	$fileInfo .= ',{"quaType":"IDCARD_BACK","quaUrl":"'.$_REQUEST['IDCARD_BACK'].'"}';
 	$fileInfo .= ',{"quaType":"CORP_CODE","quaUrl":"'.$_REQUEST['CORP_CODE'].'"}';
 	$fileInfo .= ',{"quaType":"SETTLE_BANKCARD","quaUrl":"'.$_REQUEST['SETTLE_BANKCARD'].'"}';
 	$fileInfo .= ',{"quaType":"HAND_IDCARD","quaUrl":"'.$_REQUEST['HAND_IDCARD'].'"}';
 	$fileInfo .=']';
 	$request->addParam("fileInfo",$fileInfo); 
    $response = YopClient3::post("/rest/v1.0/sys/merchant/individualreginfoadd", $request);
    if($response->validSign==1){
        //echo "返回结果签名验证成功!\n";
    }
    //取得返回结果
    $data=object_array($response);
    $result= $data['result'] ;
    if(!empty($result['merchantNo'])){
    	global $db;
	 	$pay_info = array();
	 	$pay_info['merchantNo'] = $result['merchantNo'];
	 	$pay_info['requestNo'] = $result['requestNo'];
	 	$pay_info['externalId'] = $result['externalId'];
	 	$db->query("update demo_shops set pay_info='".json_encode($pay_info)."',pay_status='0',pay_comInfo='".json_encode($request->paramMap,JSON_UNESCAPED_UNICODE)."' where comId=".(int)$_SESSION['demo_comId']);
	 	//redirect('receiveauthorizenum.php?merchantNo='.$pay_info['merchantNo'].'&phone='.$_REQUEST['merLegalPhone']);
	}
    return $data;
    
 }
  $array=individualreginfoadd();  
   
 if( $array['result'] == NULL)
 {
 	echo "error:".$array['error'];
  return;}
 else{
 $result= $array['result'] ;
 
 
}
?> 


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> 个体工商户注册--返回参数 </title>
</head>
	<body>	
		<br /> <br />
		<table width="70%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
		  		<th align="center" height="30" colspan="5" bgcolor="#6BBE18">
					个体工商户注册--返回参数 
				</th>
		  	</tr>
		  	<?
		  	if(!empty($result['merchantNo'])){
		  		?>
		  		<tr >
					<td width="25%" align="left">&nbsp;申请成功</td>
					<td width="5%"  align="center"> : </td> 
					<td width="45"  align="left"> 请注意查看手机短信，并按要求进行回复</td>
					<td width="5%"  align="center"> - </td> 
					<td width="30%" align="left">returnCode</td> 
				</tr>
		  		<?
		  	}
		  	?>
			<tr >
				<td width="25%" align="left">&nbsp;请求返回码</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <?php echo $result['returnCode'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnCode</td> 
			</tr>

			<tr>
				<td width="25%" align="left">&nbsp;请求返回信息</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['returnMsg'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnMsg</td> 
			</tr>
 
 
			
			<tr>
				<td width="25%" align="left">&nbsp;代理商编号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['parentMerchantNo'];?></td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">parentMerchantNo</td> 
			</tr>
			
			<tr >
				<td width="25%" align="left">&nbsp;商户编号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <?php echo $result['merchantNo'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">merchantNo</td> 
			</tr>

			<tr>
				<td width="25%" align="left">&nbsp;入网请求号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['requestNo'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">requestNo</td> 
			</tr>

			<tr>
				<td width="25%" align="left">&nbsp;内部流水号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['externalId'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">externalId</td> 
			</tr>

		<tr>
				<td width="25%" align="left">&nbsp;协议内容</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo $result['agreementContent'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">agreementContent</td> 
			</tr>
			
			 

		</table>

	</body>
</html>