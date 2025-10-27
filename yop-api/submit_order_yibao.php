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
//$db_service = getCrmdb();
/*$openId = $db_service->get_var("select openid from demo_user where id=$userId");
if(empty($openId)){
	$urltob = urlencode('/yop-api/submit_order_yibao.php?id='.$id.'&comId='.$order_comId);
	echo '<script>location.href="/index.php?p=8&a=bind_weixin&urltob='.$urltob.'"</script>';
	exit;
}*/
include 'conf.php';
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php"); 
require_once ("./lib/YopRsaClient.php");

$order_fenbiao = getFenbiao($order_comId,20);
$order = $db->get_row("select * from order$order_fenbiao where id=$id and userId=$userId");
$order_price = getXiaoshu($order->price-$order->price_payed,2);
if($order->price_dingjin>0){
    $order_price = getXiaoshu($order->price_dingjin-$order->price_payed,2);
}
if(empty($order)){
	die('{"code":0,"message":"订单不存在"}');
}
if($order->status!=-5){
	die('{"code":0,"message":"订单当前不是待支付状态"}');
}
$pay_end = strtotime($order->pay_endtime);
$now = time();
if($pay_end<$now){
	die('{"code":0,"message":"该订单已超过支付时间"}');
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
$data['notifyUrl']='http://buy.zhishangez.com/yop-api/notify.php';
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
function  getUrl($response,$private_key)
{
   $content=toString($response);
   $sign=YopSignUtils::signRsa($content,$private_key);
   $url=$content."&sign=".$sign;
   return  $url;
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
    $fanli_json = order_jisuan_fanli($order,$if_shop_fanli);
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
/*		$fenzhang = array();
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
	}
	$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
	$order_price = getXiaoshu($order->price-$order->price_payed,2);
    if($order->price_dingjin>0){
        $order_price = getXiaoshu($order->price_dingjin-$order->price_payed,2);
    }
    if($order->tuan_id>0){
        $url = '/index.php?p=19&a=view_tuan&id='.$order->tuan_id;
    }else{
        $url = '/index.php?p=19&a=alone';
    }
    $goodsParamExt['goodsName'] = substr($goodsParamExt['goodsName'],1);
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $merchantNo);
    $request->addParam("orderId",$order->orderId);
    $request->addParam("orderAmount", $order_price);
    $request->addParam("timeoutExpress",30);
    $request->addParam("timeoutExpressType",'MINUTE');
    $request->addParam("requestDate", date("Y-m-d H:i:s"));
    $request->addParam("redirectUrl", 'http://'.($order_comId>10?'':$order_comId.'.').'buy.zhishangez.com'.$url);
    $request->addParam("notifyUrl",'http://buy.zhishangez.com/yop-api/notify.php');
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
    /*if(!empty($divideDetail)){
    	$request->addParam("divideDetail", json_encode($divideDetail,JSON_UNESCAPED_UNICODE));
		$request->addParam("divideNotifyUrl",'http://buy.zhishangez.com/yop-api/callback.php');
    }*/
	
	$request->addParam("hmac", $hmac);
    file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
    
    $response = YopRsaClient::post("/rest/v1.0/sys/trade/order", $request);
	//    var_dump($response);
    if($response->state=='SUCCESS'){
    //取得返回结果
    $data=object_array($response);
	}
	file_put_contents('err.txt',json_encode($response,JSON_UNESCAPED_UNICODE));
    $token=$data['result']['token'];
    $cashter = array(
        "merchantNo" => $parentMerchantNo,
        "token" => $token,
        "timestamp" => time(),
        "directPayType" => '',
        "cardType" => '',
        "userNo" => (int)$_SESSION['demo_zhishangId'],
        "userType" => 'USER_ID',
        "ext" => '',
    ); 
    
    $getUrl = getUrl($cashter, $private_key);
    //$getUrl=str_replace("&timestamp","&amp;timestamp",$getUrl);
	// print_r($getUrl );
    $url = "https://cash.yeepay.com/cashier/std?" . $getUrl;
	//echo  $token;
	//echo '<a href="'.$url.'">支付</a>';
    redirect($url);
  
}
order($hmac,$order);