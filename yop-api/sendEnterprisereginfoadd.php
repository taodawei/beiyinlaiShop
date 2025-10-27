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



function enterprisereginfoadd(){
     
     global $parentMerchantNo;
	 global $private_key;
	 global $appKey,$yop_public_key;
    global $appKey;

    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("requestNo", $_REQUEST['requestNo']);
    $request->addParam("merFullName", $_REQUEST['merFullName']);
    $request->addParam("merShortName", $_REQUEST['merShortName']);
	$request->addParam("merCertType", $_REQUEST['merCertType']);
    $request->addParam("merCertNo", $_REQUEST['merCertNo']);
    $request->addParam("legalName", $_REQUEST['legalName']); 
    $request->addParam("legalIdCard", $_REQUEST['legalIdCard']);
    
    $request->addParam("merLevel1No", $_REQUEST['merLevel1No']);
    $request->addParam("merLevel2No", $_REQUEST['merLevel2No']);
    $request->addParam("merProvince", $_REQUEST['merProvince']);
    $request->addParam("merCity", $_REQUEST['merCity']);
    $request->addParam("merDistrict", $_REQUEST['merDistrict']);
    $request->addParam("merAddress", $_REQUEST['merAddress']);
	$request->addParam("merContactName", $_REQUEST['merContactName']);
	$request->addParam("merLegalPhone", $_REQUEST['merLegalPhone']);
	$request->addParam("merContactPhone", $_REQUEST['merLegalPhone']);
    $request->addParam("merLegalEmail", $_REQUEST['merLegalEmail']);
    $request->addParam("taxRegistCert", $_REQUEST['taxRegistCert']);
    $request->addParam("accountLicense", $_REQUEST['accountLicense']);
    $request->addParam("orgCode", $_REQUEST['orgCode']);
    $request->addParam("isOrgCodeLong", $_REQUEST['isOrgCodeLong']);
    $request->addParam("orgCodeExpiry", $_REQUEST['orgCodeExpiry']);

    $request->addParam("cardNo", $_REQUEST['cardNo']);
    $request->addParam("headBankCode", $_REQUEST['headBankCode']);
    $request->addParam("bankCode", $_REQUEST['bankCode']);
    $request->addParam("bankProvince", $_REQUEST['bankProvince']);
    $request->addParam("bankCity", $_REQUEST['bankCity']);
    //$request->addParam("productInfo", '');
    //$request->addParam("fileInfo", $_REQUEST['fileInfo']);
    
    $request->addParam("notifyUrl", $_REQUEST['notifyUrl']);
    $request->addParam("merAuthorizeType", $_REQUEST['merAuthorizeType']);
    $request->addParam("signCallBackUrl",'http://buy.zhishangez.com/yop-api/callback_qianzhang.php?comId='.$_SESSION['demo_comId']);
 	$fileInfo = '[';
 	$fileInfo .= '{"quaType":"IDCARD_FRONT","quaUrl":"'.$_REQUEST['IDCARD_FRONT'].'"}';
 	$fileInfo .= ',{"quaType":"IDCARD_BACK","quaUrl":"'.$_REQUEST['IDCARD_BACK'].'"}';
 	if(!empty($_REQUEST['UNI_CREDIT_CODE'])){
 		$fileInfo .= ',{"quaType":"UNI_CREDIT_CODE","quaUrl":"'.$_REQUEST['UNI_CREDIT_CODE'].'"}';
 	}else{
 		$fileInfo .= ',{"quaType":"CORP_CODE","quaUrl":"'.$_REQUEST['CORP_CODE'].'"}';
 		$fileInfo .= ',{"quaType":"TAX_CODE","quaUrl":"'.$_REQUEST['TAX_CODE'].'"}';
 		$fileInfo .= ',{"quaType":"ORG_CODE","quaUrl":"'.$_REQUEST['ORG_CODE'].'"}';
 	}
 	$fileInfo .= ',{"quaType":"OP_BANK_CODE","quaUrl":"'.$_REQUEST['OP_BANK_CODE'].'"}';
 	$fileInfo .= ',{"quaType":"HAND_IDCARD","quaUrl":"'.$_REQUEST['HAND_IDCARD'].'"}';
 	$fileInfo .=']';
 	if($_SESSION['demo_comId']==962){
 		$request->addParam("fileInfo",'[{"quaType":"IDCARD_FRONT","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/27/merchant-1566887775333-5d64cf5ed99901637021617dae44e5a4-rNyhGuXqhvxuxOCVcnAF"},{"quaType":"IDCARD_BACK","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/27/merchant-1566887779249-5d64cf62d95485091307916a8279f13e-gtxvwzieCskhdmjivsNA"},{"quaType":"UNI_CREDIT_CODE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/27/merchant-1566887786183-5d64cf6a14889242254240ec2257cafc-zMqyQCblRgMtBBlTVDNj"},{"quaType":"OP_BANK_CODE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/27/merchant-1566887789812-5d64cf6db4c47848202811826b575942-qSMnkzpvPCIdvJAXjJIG"},{"quaType":"HAND_IDCARD","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/27/merchant-1566887792380-5d64cf70468782737408198e661b6c70-HoGzhYvAAexSkekLlILg"},{"quaType":"BUSINESS_PLACE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062813101-5d677b1d05b24459723919f76624f195-fjSDfOveMvBSfRlqwEnY"},{"quaType":"CASHIER_SCENE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062813101-5d677b1d05b24459723919f76624f195-fjSDfOveMvBSfRlqwEnY"},{"quaType":"WECHAT_OPENID_HOME_PAGE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062875340-5d677b5b3eee607720653342dfc41785-VDDeZVtfEkVFAjLnntpd"},{"quaType":"WECHAT_OPENID_PRODUCT_PAGE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062907027-5d677b7ae271a68443972368dcab7702-XFvgSGucAQLmKSXrallQ"},{"quaType":"WECHAT_OPENID_PRODUCT_DETAIL","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062938416-5d677b9a4ee7e530696085c1ed72b478-aoJJJcNnYSrjltAkDvtQ"},{"quaType":"WECHAT_OPENID_PURCHASE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062971785-5d677bbbb0d5571196526809838e20e5-ztUgCyqTTrVMNbTPrvQI"},{"quaType":"WECHAT_OPENID_PAYMENT_PROCESS","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062994576-5d677bd27caf41703945597e1f402cbf-DOjaPmCpSNdzvMyLQweP"}]');
 		$request->addParam("productInfo", '{"payProductMap":{"OFFICIAL_ACCOUNT_PAY":{"dsPayBankMap":{"WECHAT_OPENID_ONLINE":{"rateType":"PERCENTAGE","rate":"1"}},"recommendOfficialAccAppId":"wx7a91a4f2eccb30db","officialAccAppId":"wx7a91a4f2eccb30db","weChatId":"知商购","officialAccAuthorizeDirectory":"http://buy.zhishangez.com/;http://buy.zhishangez.com/yop-api/"}},"payScenarioMap":{"OFFICIAL_ACCOUNT_ACCESS":{}}}');
 		$request->addParam("businessFunction", '{"FEE_TYPE":["REALTIME"],"ELEC_SIGN":["URL"]}');
 	}else if($_SESSION['demo_comId']==969){
 		$request->addParam("fileInfo",'[{"quaType":"IDCARD_FRONT","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/31/merchant-1567233184465-5d6a14a04c9659147323549315d3e33a-HeXMwoOPwaVRFccwlrBf"},{"quaType":"IDCARD_BACK","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/31/merchant-1567233216067-5d6a14bfed5979610839964e77b84b1d-OBhrdUGjEtMbkNRQjzno"},{"quaType":"UNI_CREDIT_CODE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/31/merchant-1567233239415-5d6a14d74bcbf234549200877b3336e2-GCHkZqVAQGgYiMSVDsHK"},{"quaType":"OP_BANK_CODE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/31/merchant-1567233269317-5d6a14f4d704f588044719980a7f81bf-beSMwqzHPXRCXBwdGplq"},{"quaType":"HAND_IDCARD","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/31/merchant-1567233293106-5d6a150d04063397033462448a57be24-mNdqZRbnWeBVdOsTxmYq"},{"quaType":"WECHAT_OPENID_HOME_PAGE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062875340-5d677b5b3eee607720653342dfc41785-VDDeZVtfEkVFAjLnntpd"},{"quaType":"WECHAT_OPENID_PRODUCT_PAGE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062907027-5d677b7ae271a68443972368dcab7702-XFvgSGucAQLmKSXrallQ"},{"quaType":"WECHAT_OPENID_PRODUCT_DETAIL","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062938416-5d677b9a4ee7e530696085c1ed72b478-aoJJJcNnYSrjltAkDvtQ"},{"quaType":"WECHAT_OPENID_PURCHASE","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062971785-5d677bbbb0d5571196526809838e20e5-ztUgCyqTTrVMNbTPrvQI"},{"quaType":"WECHAT_OPENID_PAYMENT_PROCESS","quaUrl":"http://staticres.yeepay.com/jcptb-merchant-netinjt02/2019/08/29/merchant-1567062994576-5d677bd27caf41703945597e1f402cbf-DOjaPmCpSNdzvMyLQweP"}]');
 		$request->addParam("productInfo", '{"payProductMap":{"OFFICIAL_ACCOUNT_PAY":{"dsPayBankMap":{"WECHAT_OPENID_ONLINE":{"rateType":"PERCENTAGE","rate":"1"}},"recommendOfficialAccAppId":"wx90de610bcac2ade0","officialAccAppId":"wx90de610bcac2ade0","weChatId":"互动企业营销","officialAccAuthorizeDirectory":"http://buy.zhishangez.com/;http://buy.zhishangez.com/yop-api/"}},"payScenarioMap":{"OFFICIAL_ACCOUNT_ACCESS":{}}}');
 		$request->addParam("businessFunction", '{"FEE_TYPE":["REALTIME"],"ELEC_SIGN":["URL"]}');
 	}else{
 		$request->addParam("fileInfo",$fileInfo);
 		$request->addParam("businessFunction", '{"SUBACCOUNT_IS_OPENED":["YES"],"SUBACCOUNT_TYPE":["SUBACCOUNT_ORDER"],"FEE_TYPE":["REALTIME"],"ELEC_SIGN":["URL"]}');
 	}
 	file_put_contents('err.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
    $response = YopClient3::post("/rest/v1.0/sys/merchant/enterprisereginfoadd", $request);
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
  $array=enterprisereginfoadd();  
   
 if( $array['result'] == NULL)
 {
 	file_put_contents('err1.txt',json_encode($array,JSON_UNESCAPED_UNICODE));
 	echo "error:".$array['error'];
  return;}
 else{
 $result= $array['result'] ;
}
?> 


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> 企业注册--返回参数 </title>
</head>
	<body>	
		<br /> <br />
		<table width="70%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
		  		<th align="center" height="30" colspan="5" bgcolor="#6BBE18">
					企业注册--返回参数 
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
 
			
			 

		</table>

	</body>
</html>