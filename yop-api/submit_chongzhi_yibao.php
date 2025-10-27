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
$userId = empty($_REQUEST['userId'])?(int)$_SESSION['demo_user_ID']:(int)$_REQUEST['userId'];
$order_comId = (int)$_REQUEST['comId'];
$money = $_REQUEST['money'];
if(empty($order_comId))$order_comId = (int)$_SESSION['demo_comId'];
//$db_service = getCrmdb();
/*$openId = $db_service->get_var("select openid from demo_user where id=".$_SESSION['demo_zhishangId']);
if(empty($openId)){
	$urltob = urlencode('/yop-api/submit_chongzhi_order.php?money='.$money.'&comId='.$order_comId);
	echo '<script>location.href="/index.php?p=8&a=bind_weixin&urltob='.$urltob.'"</script>';
	exit;
}*/
$orderId = $order_comId.'_'.$userId.'_'.uniqid();
$db->query("insert into demo_chongzhi_jilu(orderId) value('$orderId')");
include 'conf.php';
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php"); 
require_once ("./lib/YopRsaClient.php");
$data=array();
$data['parentMerchantNo']=$parentMerchantNo;
$data['merchantNo']=$merchantNo;
$data['orderId']=$orderId;
$data['orderAmount']=$money;
$data['notifyUrl']='http://buy.zhishangez.com/yop-api/notify_chongzhi.php';
$hmacstr = hash_hmac('sha256', toString($data), $hmacKey, true);
$hmac = bin2hex($hmacstr);
$data['comId'] = $order_comId;
$data['userId'] = $userId;
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
function getUrl($response,$private_key)
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
	$goodsParamExt = array('goodsName'=>'余额充值','goodsDesc'=>'');
	$divideDetail = array();//分账详细
	$fenzhang_money = 0;
	if($order['order_comId']!=10){
		$fenzhang_money = $order['orderAmount'];
		$shouxufei = getXiaoshu($order['orderAmount']*7/1000,2);
		//商家收益减去手续费
		if($shouxufei>0){
			$fenzhang_money-=$shouxufei;
		}
	}
	$shops = $db->get_row("select pay_info,pay_comInfo from demo_shops where comId=".$order['comId']);
	$pay_info = array();
	$pay_comInfo = array();
	$fenzhang_money = getXiaoshu($fenzhang_money,2);
	$yibao_fenzhang = array();
	$yibao_fenzhang['comId'] = $order['comId'];
	$yibao_fenzhang['money'] = $fenzhang_money;
	$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
	$yibao_fenzhang['orderId'] = 0;
	$yibao_fenzhang['payId'] = 0;
	$yibao_fenzhang['type'] = 1;
	if(!empty($shops->pay_info)){
		$pay_info = json_decode($shops->pay_info);
		$pay_comInfo = json_decode($shops->pay_comInfo);
		$yibao_fenzhang['income_type'] = 1;
		$yibao_fenzhang['ledgerNo'] = $pay_info->merchantNo;
		$yibao_fenzhang['ledgerName'] = urldecode($pay_comInfo->merShortName);
		$fenzhang = array();
		$fenzhang['ledgerNo'] = $pay_info->merchantNo;
		$fenzhang['ledgerName'] = urldecode($pay_comInfo->merShortName);
		$fenzhang['amount'] = $fenzhang_money;
		$divideDetail[] = $fenzhang;
	}else{//如果没有支付信息，就在确认收货的时候将钱返回到商铺余额中
		//$fanli_json->shop_fanli = $fenzhang_money;
		//$fanli_str = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
		//$db->query("update order$order_fenbiao set fanli_json='$fanli_str' where id=$orderId");
		$yibao_fenzhang['income_type'] = 2;
	}
	$yibao_fenzhang['remark'] = '用户充值';
	//$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
	//$goodsParamExt['goodsName'] = substr($goodsParamExt['goodsName'],1);
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $merchantNo);
    $request->addParam("orderId",$order['orderId']);
    $request->addParam("orderAmount", $order['orderAmount']);
    $request->addParam("timeoutExpress",120);
    $request->addParam("timeoutExpressType",'MINUTE');
    $request->addParam("requestDate", date("Y-m-d H:i:s"));
    $request->addParam("redirectUrl", 'http://'.($order_comId>10?'':$order_comId.'.').'buy.zhishangez.com/index.php?p=8&a=qianbao');
    $request->addParam("notifyUrl",'http://buy.zhishangez.com/yop-api/notify_chongzhi.php');
    $request->addParam("goodsParamExt",json_encode($goodsParamExt,JSON_UNESCAPED_UNICODE));
    $request->addParam("paymentParamExt",'');
    $request->addParam("industryParamExt",'');
    $request->addParam("memo",$order['comId'].'_'.$order['userId']);
    $request->addParam("riskParamExt",'');
    $request->addParam("csUrl",'');
    $request->addParam("assureType", 'REALTIME');
    //$request->addParam("fundProcessType", 'REAL_TIME');
    //分账后期测试
    if($order['comId']!=10){
    	$request->addParam("fundProcessType", 'REAL_TIME_DIVIDE');
    	$request->addParam("divideDetail",json_encode($divideDetail,JSON_UNESCAPED_UNICODE));
    	$request->addParam("divideNotifyUrl",'http://buy.zhishangez.com/yop-api/callback.php');
    }else{
    	$request->addParam("fundProcessType", 'REAL_TIME');
    }
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
    file_put_contents('url.txt',$url);
	//echo  $token;
	//echo '<a href="'.$url.'">支付</a>';
    redirect($url);
  
}
order($hmac,$data);