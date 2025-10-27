<?php
session_start();
if(empty($_SESSION['demo_zhishangId'])){
  echo '<script>location.href="/"</script>';
}
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$id = (int)$_REQUEST['id'];
$userId = (int)$_SESSION['demo_zhishangId'];
$db_service = getCrmdb();
$openId = $db_service->get_var("select openid from demo_user where id=$userId");
if(empty($openId)){
	$urltob = urlencode('/yop-api/submit_order_zong.php?id='.$id);
	echo '<script>location.href="/index.php?p=8&a=bind_weixin&urltob='.$urltob.'"</script>';
	exit;
}
include 'conf.php';
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php");
require_once ("./lib/YopRsaClient.php");

$order_pay = $db->get_row("select * from order_pay where id=$id");
$order_price = getXiaoshu($order_pay->price-$order_pay->price_payed,2);
if(empty($order_pay)){
	die('{"code":0,"message":"订单不存在"}');
}
if($order->ispay!=0){
	die('{"code":0,"message":"订单不是待支付状态"}');
}
$orderInfo = json_decode($order_pay->orderInfo);
$fenbiao1 = getFenbiao($orderInfo[0]->comId,20);

$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$order_pay->orderId;
$data['orderAmount']=getXiaoshu($order_pay->price-$order_pay->price_payed,2);
$data['notifyUrl']='http://buy.zhishangez.com/yop-api/notify_zong.php';
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
function order($hmac,$order_pay){
	global $db,$merchantNo;
	global $parentMerchantNo;
	global $appKey,$private_key;
	global $yop_public_key;
	$orders = json_decode($order_pay->orderInfo,true);
	$goodsParamExt = array('goodsName'=>'','goodsDesc'=>'');
	$divideDetail = array();//分账详细
	$dtTime = date("Y-m-d H:i:s");
	if(!empty($orders)){
		foreach ($orders as $ord) {
			$order_fenbiao = getFenbiao($ord['comId'],20);
			$order_comId = $ord['comId'];
			$orderId = $ord['orderId'];
			$order = $db->get_row("select * from order$order_fenbiao where id=".$ord['orderId']);
			$product_json = json_decode($order->product_json);
			foreach ($product_json as $pdt) {
				$goodsParamExt['goodsName'].=','.$pdt->title.'*'.$pdt->num;
			}
			/*$fenzhang_money = 0;
			if(!empty($order->fanli_json)){
				$fanli_json = json_decode($order->fanli_json);
				$fenzhang_money = $fenzhang_money - $fanli_json->shangji_fanli - $fanli_json->shangshangji_fanli - $fanli_json->tuijian_fanli - $fanli_json->pingtai_fanli - $fanli_json->daili_fanli;
			}*/
			$shops = $db->get_row("select pay_info,pay_comInfo from demo_shops where comId=".$ord['comId']);
			$if_shop_fanli = empty($shops->pay_info)?1:0;
			$fanli_json = order_jisuan_fanli($order,$if_shop_fanli);
			$fenzhang_money = $fanli_json['shop_fanli'];
			$pay_info = array();
			$pay_comInfo = array();
			$fenzhang_money = getXiaoshu($fenzhang_money,2);
			$yibao_fenzhang = array();
			$yibao_fenzhang['comId'] = $order->comId;
			$yibao_fenzhang['money'] = $fenzhang_money;
			$yibao_fenzhang['dtTime'] = $dtTime;
			$yibao_fenzhang['orderId'] = $orderId;
			$yibao_fenzhang['payId'] = $order_pay->id;
			$yibao_fenzhang['type'] = 1;
			if(!empty($shops->pay_info)){
				$yibao_fenzhang['income_type'] = 1;
				$pay_info = json_decode($shops->pay_info);
				$pay_comInfo = json_decode($shops->pay_comInfo);
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
			}
			$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
		}
		$goodsParamExt['goodsName'] = substr($goodsParamExt['goodsName'],1);
	}


    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $merchantNo);
    $request->addParam("orderId",$order_pay->orderId);
    $request->addParam("orderAmount", getXiaoshu($order_pay->price-$order_pay->price_payed,2));
    $request->addParam("timeoutExpress",30);
	$request->addParam("timeoutExpressType",'MINUTE');
    $request->addParam("requestDate", date("Y-m-d H:i:s"));
    $request->addParam("redirectUrl", 'http://buy.zhishangez.com/index.php?p=19&a=alone');
    $request->addParam("notifyUrl",'http://buy.zhishangez.com/yop-api/notify_zong.php');
    $request->addParam("goodsParamExt",json_encode($goodsParamExt,JSON_UNESCAPED_UNICODE));
   	$request->addParam("paymentParamExt",'');
    $request->addParam("industryParamExt",'');
    $request->addParam("memo",'10_'.$order_pay->id);
    $request->addParam("riskParamExt",'');
    $request->addParam("csUrl",'');
    if(count($orders)==1 && $order_comId==1022){
    	$request->addParam("assureType", 'REALTIME');
        $request->addParam("fundProcessType", 'REAL_TIME');
    }else{
    	$request->addParam("assureType", 'ASSURE');
        $request->addParam("fundProcessType", 'DELAY_SETTLE');
    }
    //$request->addParam("assureType", 'ASSURE');
    //$request->addParam("fundProcessType", 'REAL_TIME');
    //分账信息后期测试
    //$request->addParam("fundProcessType", 'DELAY_SETTLE');
    /*if(!empty($divideDetail)){
    	$request->addParam("divideDetail", json_encode($divideDetail,JSON_UNESCAPED_UNICODE));
		$request->addParam("divideNotifyUrl",'http://buy.zhishangez.com/yop-api/callback.php');
    }*/
	$request->addParam("hmac", $hmac);
    file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
    $response = YopRsaClient::post("/rest/v1.0/sys/trade/order", $request);
	//var_dump($response);
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
order($hmac,$order_pay);