<?php 
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
$m = file_get_contents("php://input");
if($m){
	libxml_disable_entity_loader(true);
	$fhData = simplexml_load_string($m, 'SimpleXMLElement', LIBXML_NOCDATA);
	$_REQUEST['com_id'] = $comId = (int)$fhData->attach;
}else{
	exit;
}
require_once 'config/dt-config.php';
require_once 'inc/function.php';
require_once 'inc/class.database.php';
$fenbiao = intval($comId%20);
$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
	die('微信配置信息有误');
}
$weixin_arr = json_decode($weixin_set->info);
define('WX_APPID',$weixin_arr->appid);
define('WX_MCHID',$weixin_arr->mch_id);
define('WX_KEY',$weixin_arr->key);
define('WX_APPSECRET',$weixin_arr->appsecret);

require_once "inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php";
require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Notify.php';
require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
//初始化日志
$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
if(!empty($fhData->out_trade_no)){
	$out_trade_no	= $fhData->out_trade_no;	//商户订单号
	$transaction_id		= $fhData->transaction_id;		//微信支付订单号
	$total_fee		= ($fhData->total_fee)/100;		//获取总价格
	$arr = explode('_', $out_trade_no);
	$order = $db->get_row("select * from demo_shequ_yuyue where id='".$arr['1']."' limit 1");
	if($order->status==0||$order->status==-1){
		$orderId = $order->id;
		$o = array();
		$o['id'] = $orderId;
		$o['status'] = 1;
		$pay_json = array();
		if(!empty($order->payed_json)){
			$pay_json = json_decode($order->payed_json,true);
		}
		$pay_json['weixin']['price'] = $total_fee;
		$pay_json['weixin']['desc'] = $transaction_id;
		$o['payed_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);

		$db->insert_update('demo_shequ_yuyue',$o,'id');
	}else{
		file_put_contents('weixin_pay_err.log',json_encode($_POST).PHP_EOL,FILE_APPEND);
	}
	ob_clean();
	echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
	exit;
}