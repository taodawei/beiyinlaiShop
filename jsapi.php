<?php 
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
//设置配置信息
define('WX_APPID',$weixin_arr->appid);
define('WX_MCHID',$weixin_arr->mch_id);
define('WX_KEY',$weixin_arr->key);
define('WX_APPSECRET',$weixin_arr->appsecret);
//初始化日志
$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
//①、获取用户openid
$tools = new JsApiPay();
$openId = '';//获取openId
if(empty($openId)){
	$openId = $tools->GetOpenid();
	//修改用户的openId
	//$db->query("update users set openId='$openId' where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
}
//构建支付参数
$subject = '商品标题';
$body = $subject;
$pay_price = 1;//1分钱
$orderId = '123123';//系统内的订单号，回调时根据这个获取订单

$dtTime = date("YmdHis");
$expireTime = date("YmdHis", time() + 60*60*24);

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($comId);//自定义数据
$input->SetOut_trade_no($orderId);
$input->SetTotal_fee($pay_price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire($expireTime);
$input->SetGoods_tag($subject);
$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$orders = WxPayApi::unifiedOrder($input);
if($orders['err_code']){
	echo $orders['err_code'].':'.$orders['err_code_des'];exit;
}
//前端支付所需要的参数都在jsApiParameters中
$jsApiParameters = $tools->GetJsApiParameters($orders);

