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
	$order = $db->get_row("select * from demo_pdt_order where orderId='$out_trade_no' limit 1");
	if($order->status==-5||$order->status==-1){
		$orderId = $order->id;
		$o = array();
		$o['id'] = $orderId;
		$o['status'] = $order->address_id>0?2:4;//需要收货的设置2 不需要的设置4
		$o['ispay'] = 1;
		$o['pay_type'] = 2;
		$o['price_payed'] = $total_fee+$order->price_payed;
		$pay_json = array();
		if(!empty($order->pay_json)){
			$pay_json = json_decode($order->pay_json,true);
		}
		if($order->price_dingjin==0){
			$pay_json['weixin']['price'] = $total_fee;
			$pay_json['weixin']['desc'] = $transaction_id;
		}else{
			$pay_json['dingjin']['price'] = $total_fee;
			$pay_json['dingjin']['paytype'] = '微信，订单号：'.$transaction_id;
		}
		$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
		$db->insert_update('demo_pdt_order',$o,'id');
		$db->query("update demo_pdt_inventory set orders=orders+1 where id=$order->inventoryId");
		$fanli_json = json_decode($order->fanli_json);
		if($fanli_json->shangji>0 && $fanli_json->shangji_fanli>0){
			$yugu_shouru = array();
			$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
			$yugu_shouru['userId'] = $fanli_json->shangji;
			$yugu_shouru['order_type'] = 2;
			$yugu_shouru['orderId'] = $order->id;
			$yugu_shouru['dtTime'] = date("Y-m-d");
			$yugu_shouru['money'] = $fanli_json->shangji_fanli;
			$yugu_shouru['from_user'] = $order->userId;
			$yugu_shouru['remark'] = '下级返利';
			$yugu_shouru['order_orderId'] = $order->orderId;
			$yugu_shouru['order_comId'] = $order->comId;
			$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
		}
		if($fanli_json->shangshangji>0 && $fanli_json->shangshangji_fanli>0){
			$yugu_shouru = array();
			$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
			$yugu_shouru['userId'] = $fanli_json->shangshangji;
			$yugu_shouru['order_type'] = 2;
			$yugu_shouru['orderId'] = $order->id;
			$yugu_shouru['dtTime'] = date("Y-m-d");
			$yugu_shouru['money'] = $fanli_json->shangshangji_fanli;
			$yugu_shouru['from_user'] = $order->userId;
			$yugu_shouru['remark'] = '团队返利';
			$yugu_shouru['order_orderId'] = $order->orderId;
			$yugu_shouru['order_comId'] = $order->comId;
			$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
		}
		if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
			$yugu_shouru = array();
			$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
			$yugu_shouru['userId'] = $fanli_json->tuijian;
			$yugu_shouru['order_type'] = 2;
			$yugu_shouru['orderId'] = $order->id;
			$yugu_shouru['dtTime'] = date("Y-m-d");
			$yugu_shouru['money'] = $fanli_json->tuijian_fanli;
			$yugu_shouru['from_user'] = $order->userId;
			$yugu_shouru['remark'] = '推荐店铺返利';
			$yugu_shouru['order_orderId'] = $order->orderId;
			$yugu_shouru['order_comId'] = $order->comId;
			$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
		}
		if($order->address_id>0){
			$address_id = $order->address_id;
			$address = $db->get_row("select * from user_address where id=$address_id");
			$areaId = (int)$address->areaId;
			$shouhuo_json = array();
			if(!empty($address)){
				$shouhuo_json['收件人'] = $address->name;
				$shouhuo_json['手机号'] = $address->phone;
				$shouhuo_json['所在地区'] = $address->areaName;
				$shouhuo_json['详细地址'] = $address->address;
			}
			$pdt_title = $db->get_var("select title from demo_pdt_inventory where id=$order->inventoryId");
			$fahuo = array();
			$fahuo['comId'] = $order->comId;
			$fahuo['mendianId'] = 0;
			$fahuo['addressId'] = $address_id;
			$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$fahuo['orderIds'] = $order->id;
			$fahuo['type'] = 1;
			$fahuo['showTime'] = date("Y-m-d H:i:s");
			$fahuo['storeId'] = 0;
			$fahuo['dtTime'] = date("Y-m-d H:i:s");
			$fahuo['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
			$fahuo['productId'] = (int)$order->inventoryId;
			$fahuo['tuanzhang'] = 0;
			$fahuo['product_title'] = $pdt_title;
			$fahuo['fahuo_title'] = $pdt_title;
			$fahuo['product_num'] = $order->pdtNums;
			$fahuo['weight'] = 0;
			$fahuo['areaId'] = $areaId;
			$fahuo['shequ_id'] = 0;
			$db->insert_update('pdt_order_fahuo',$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update demo_pdt_order set fahuoId=$fahuoId where id=$order->id");
		}
	}else{
		file_put_contents('weixin_pay_err.log',json_encode($_POST).PHP_EOL,FILE_APPEND);
	}
	ob_clean();
	echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
	exit;
}