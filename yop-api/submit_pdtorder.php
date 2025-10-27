<?php
session_start();
if(empty($_SESSION['demo_zhishangId']) && empty($_REQUEST['userId'])){
  echo '<script>location.href="/"</script>';
}
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$id = (int)$_REQUEST['id'];
$userId = (int)$_SESSION['demo_zhishangId'];
if(empty($userId))$userId=(int)$_REQUEST['userId'];
$order_comId = (int)$_REQUEST['comId'];
if(empty($order_comId))$order_comId = (int)$_SESSION['demo_comId'];
$db_service = getCrmdb();
$openId = $db_service->get_var("select openid from demo_user where id=$userId");
if(empty($openId)){
	$urltob = urlencode('/yop-api/submit_pdtorder.php?id='.$id.'&comId='.$order_comId);
	echo '<script>location.href="/index.php?p=8&a=bind_weixin&urltob='.$urltob.'"</script>';
	exit;
}
include 'conf.php';
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php"); 
require_once ("./lib/YopRsaClient.php");

$order_fenbiao = getFenbiao($order_comId,20);
$order = $db->get_row("select * from demo_pdt_order where id=$id and userId=$userId");
$order_price = getXiaoshu($order->price-$order->price_payed,2);
if(empty($order)){
	die('{"code":0,"message":"订单不存在"}');
}
if($order->status!=-5){
	die('{"code":0,"message":"订单当前不是待支付状态"}');
}
if(!empty($order->pay_json)){
    $pay_json = json_decode($order->pay_json,true);
    if(!empty($pay_json['dingjin'])){
    	$order->orderId = str_replace('_','__',$order->orderId);
    }
}
$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$order->orderId;
$data['orderAmount']=$order_price;
$data['notifyUrl']='http://buy.zhishangez.com/yop-api/notify_pdts.php';
$hmacstr = hash_hmac('sha256', toString($data), $hmacKey, true);
$hmac = bin2hex($hmacstr);
 #将参数转换成k=v拼接的形式
function toString($arraydata){
    $Str="";
    foreach ($arraydata as $k=>$v){
       $Str .= strlen($Str) == 0 ? "" : "&";
        $Str.=$k."=".$v;
    }
    return $Str;
}

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

function order($hmac,$order){
	global $db,$merchantNo;
	global $parentMerchantNo;
	global $appKey,$private_key;
	global $yop_public_key;
	$goodsParamExt = array('goodsName'=>'','goodsDesc'=>'');
	$divideDetail = array();//分账详细
	$product_json = json_decode($order->product_json);
	foreach ($product_json as $pdt) {
		$goodsParamExt['goodsName'].=','.$pdt->title.'*'.$pdt->num;
	}

	$shops = $db->get_row("select pay_info,pay_comInfo from demo_shops where comId=".$order->comId);
	if($order->if_zong==1 && empty($shops->pay_info)){//是总平台的订单而且没有申请易宝支付的确认收货后执行商家返利
		$if_shop_fanli = 1;
	}else{
		$if_shop_fanli = 0;
	}
	$fanli_json = json_decode($order->fanli_json,true);
	$fenzhang_money = $fanli_json['shop_fanli'];
	$pay_info = array();
	$pay_comInfo = array();
	$fenzhang_money = getXiaoshu($fenzhang_money,2);
	$yibao_fenzhang = array();
	$yibao_fenzhang['comId'] = $order->comId;
	$yibao_fenzhang['money'] = $fenzhang_money;
	$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
	$yibao_fenzhang['orderId'] = $order->id;
	$yibao_fenzhang['payId'] = 0;
	$yibao_fenzhang['type'] = 1;
	if(!empty($shops->pay_info)){
		$pay_info = json_decode($shops->pay_info);
		$pay_comInfo = json_decode($shops->pay_comInfo);
		$yibao_fenzhang['income_type'] = 1;
		$yibao_fenzhang['ledgerNo'] = $pay_info->merchantNo;
		$yibao_fenzhang['ledgerName'] = urldecode($pay_comInfo->merShortName);
		/*$fenzhang = array();
		$fenzhang['ledgerNo'] = $pay_info->merchantNo;
		$fenzhang['ledgerName'] = urldecode($pay_comInfo->merShortName);
		$fenzhang['amount'] = $fenzhang_money;
		$divideDetail[] = $fenzhang;*/
	}else{//如果没有支付信息，就在确认收货的时候将钱返回到商铺余额中
		/*$fanli_json->shop_fanli = $fenzhang_money;
		$fanli_str = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
		$db->query("update order$order_fenbiao set fanli_json='$fanli_str' where id=$orderId");*/
		$yibao_fenzhang['ledgerNo'] = '';
		$yibao_fenzhang['ledgerName'] = '';
		$yibao_fenzhang['income_type'] = 2;
		if($order->if_zong==1){
			$fanli_json = json_decode($order->fanli_json,true);
			$fanli_json['if_shop_fanli'] = 1;
			$db->query("update demo_pdt_order set fanli_json='".json_encode($fanli_json,JSON_UNESCAPED_UNICODE)."' where id=$order->id");
		}
	}
	$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
	$order_price = getXiaoshu($order->price-$order->price_payed,2);
	$goodsParamExt['goodsName'] = substr($goodsParamExt['goodsName'],1);
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $merchantNo);
    $request->addParam("orderId",$order->orderId);
    $request->addParam("orderAmount", $order_price);
    $request->addParam("timeoutExpress",30);
    $request->addParam("timeoutExpressType",'MINUTE');
    $request->addParam("requestDate", date("Y-m-d H:i:s"));
    $request->addParam("redirectUrl", 'http://buy.zhishangez.com/index.php');
    $request->addParam("notifyUrl",'http://buy.zhishangez.com/yop-api/notify_pdts.php');
    $request->addParam("goodsParamExt",json_encode($goodsParamExt,JSON_UNESCAPED_UNICODE));
    $request->addParam("paymentParamExt",'');
    $request->addParam("industryParamExt",'');
    $request->addParam("memo",$order->comId.'_'.$order->id);
    $request->addParam("riskParamExt",'');
    $request->addParam("csUrl",'');
    
    if($order_comId==1009||$order_comId==1022){
    	$request->addParam("assureType", 'REALTIME');
    	$request->addParam("fundProcessType", 'REAL_TIME');
    }else{
    	$request->addParam("assureType", 'ASSURE');
    	$request->addParam("fundProcessType", 'DELAY_SETTLE');
    }
    //$request->addParam("fundProcessType", 'REAL_TIME');
    
    /*if(!empty($divideDetail)){
    	$request->addParam("divideDetail", json_encode($divideDetail,JSON_UNESCAPED_UNICODE));
		$request->addParam("divideNotifyUrl",'http://buy.zhishangez.com/yop-api/callback.php');
    }*/
	
	$request->addParam("hmac", $hmac);
    //file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
    
    $response = YopRsaClient::post("/rest/v1.0/sys/trade/order", $request);
	//    var_dump($response);
    if($response->state=='SUCCESS'){
    //取得返回结果
    	$data=object_array($response);
	}
	//file_put_contents('err.txt',json_encode($response,JSON_UNESCAPED_UNICODE));
    $token=$data['result']['token'];
	//echo  $token;
    return $token ;
  
}

function APIorder($token,$userId,$openId){
	global $merchantNo;
	global $parentMerchantNo;
	global $private_key;
	global $yop_public_key;
    global $appKey;
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("token", $token);
    $request->addParam("payTool",'WECHAT_OPENID');
    $request->addParam("payType", 'WECHAT');
    $request->addParam("userNo", $userId);
    $request->addParam("userType",'USER_ID');
    $request->addParam("appId",'wx7a91a4f2eccb30db');
    $request->addParam("openId", $openId);
    //$request->addParam("payEmpowerNo", $_REQUEST['payEmpowerNo']);
    //$request->addParam("merchantTerminalId", $_REQUEST['merchantTerminalId']);
    //$request->addParam("merchantStoreNo", $_REQUEST['merchantStoreNo']);
    $request->addParam("userIp", getip());
    $request->addParam("version", '1.0');
 	$request->addParam("extParamMap",'{"reportFee":"XIANSHANG"}');
     //var_dump($request);
    
    $response = YopClient3::post("/rest/v1.0/nccashierapi/api/pay", $request);
    if($response->validSign==1){
        //echo "返回结果签名验证成功!\n";
    }
    //取得返回结果
    $data=object_array($response);
    return $data ;  
}
$gettoken=order($hmac,$order);
$array=APIorder($gettoken,$userId,$openId);
if( $array['result'] == NULL)
{
	echo "error:".$array['error'];
	return;
}else{
	$result= $array['result'] ;
	file_put_contents('err1.txt',json_encode($array,JSON_UNESCAPED_UNICODE));
	if($result['code']!='CAS00000'){
 		die('系统错误，请联系管理员');
 		//var_dump($result);
 	}
}
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
$resultData = json_decode($result['resultData']);
$tools = new JsApiPay();
$jsapi = new WxPayJsApiPay();
$jsapi->SetAppid($resultData->appId);
$jsapi->SetTimeStamp($resultData->timeStamp);
$jsapi->SetNonceStr($resultData->nonceStr);
$jsapi->SetPackage($resultData->package);
$jsapi->SetSignType($resultData->signType);
$jsapi->SetPaySign($resultData->paySign);
$jsApiParameters = json_encode($jsapi->GetValues());
if($order->tuan_id>0){
	$url = '/index.php?p=19&a=view_tuan&id='.$order->tuan_id;
}else{
	$url = '/index.php?p=19&a=alone';
}
$website = '';
$shoukuanfang = '知商购-企业自主电商平台';
if(!empty($_REQUEST['userId'])){
	$website = 'http://'.$order_comId.'.buy.zhishangez.com';
	$shoukuanfang = $db->get_var("select com_title from demo_shezhi where comId=$order_comId");
}
?>
<html>
<head>
	<title>微信支付</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/> 
	<link href="/skins/erp_zong/styles/common.css" rel="stylesheet" type="text/css">
	<link href="/skins/erp_zong/styles/zhifu.css" rel="stylesheet" type="text/css">
	<script src="/skins/resource/scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:ok'){
					location.href='<?=$website.$url?>';
				}else{
					//alert(res.err_msg);
					location.href='<?=$website?>/index.php?p=8';//支付失败返回订单列表
				}
			}
			);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}
</script>
</head>
<body style="background-color:#f6f6f6;">
<div class="zhifu">
	<div class="zhifu_1">
    	支付
        <div class="zhifu_1_left" onclick="history.go(-1);">
        	取消
        </div>
    </div>
	<div class="zhifu_2">
    	订单编号：<?=$order->orderId?>
        <h2>￥<?=$data['orderAmount']?></h2>
    </div>
	<div class="zhifu_3">
    	<div class="zhifu_3_left">
        	收款方
        </div>
    	<div class="zhifu_3_right">
        	<?=$shoukuanfang?>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="zhifu_4">
    	<a href="javascript:void(0);" onClick="callpay()"><img src="/skins/erp_zong/images/pay_1.png" /></a>
    </div>
</div>
</body>
</html>