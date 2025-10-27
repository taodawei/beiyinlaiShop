<?php 
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
$m = file_get_contents("php://input");
if($m){
	libxml_disable_entity_loader(true);
	$fhData = simplexml_load_string($m, 'SimpleXMLElement', LIBXML_NOCDATA);
	$type = (int)$fhData->attach;
}else{
    $type = 3;
	exit;
}
require_once 'config/dt-config.php';
require_once 'inc/class.database.php';
$comId = 888;
$fenbiao = intval($comId%20);
$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=$type limit 1");
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

// $fhData = new \StdClass();
// $fhData->out_trade_no = 'Y20221029092049177863';
// $fhData->transaction_id = '2323123123123123123123';
// $fhData->total_fee = 100*100;

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
if(!empty($fhData->out_trade_no)){
	$out_trade_no	= $fhData->out_trade_no;	//商户订单号
	$transaction_id		= $fhData->transaction_id;		//微信支付订单号
	$total_fee		= ($fhData->total_fee)/100;		//获取总价格
// 	Log::DEBUG("begin $out_trade_no,$transaction_id,$total_fee  ");
	//查支付表信息
	$payLog = $db->get_row("select * from pay_log where payNo = '$out_trade_no' ");
	
// 		Log::DEBUG("select * from pay_log where payNo = '$out_trade_no' ");
	if(!$payLog || $payLog->status == 1){
	    die();
	}
// 	Log::DEBUG("hahah ");
	$updateLog = array(
	    'id' => $payLog->id,
	    'transaction_id' => $transaction_id,
	    'status' => 1,
	    'payTime' => date('Y-m-d H:i:s')
	);
	$db->insert_update("pay_log", $updateLog, "id");
	
	$order = $db->get_row("select * from user_chongzhi where id = $payLog->typeId order by id desc limit 1");
	if(empty($order)){
		file_put_contents('chongzhi_err.log',$out_trade_no.$transaction_id.PHP_EOL,FILE_APPEND);
	}else if($order->status==1){
		die();
	}else{
		$db->query("update user_chongzhi set return_orderId='$transaction_id',status=1 where id=$order->id");
		$db->query("update users set wx_money=wx_money+$total_fee where id=$order->userId");
		
// 		$card = array(
// 		    'userId' => $order->userId,
// 		    'card_no' => 'CZ'.date('YmdHis').rand(10000,99999),
// 		    'earn' => $total_fee,
// 		    'yue' => $total_fee,
// 		    'dtTime' => date('Y-m-d H:i:s'),
// 		    'updateTime' => date('Y-m-d H:i:s')
// 		);
		
// 		$db->insert_update("user_card", $card, "id");
// 		$cardId = $db->get_var("select last_insert_id();");

        $cardId = 0;
		
		$liushui = array();
		$liushui['userId']=$order->userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$total_fee;
		$liushui['yue']= $db->get_var("select wx_money from users where id = $order->userId");
		$liushui['cardId'] = $cardId;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='充值';
		$liushui['orderInfo']='微信充值，微信单号：'.$transaction_id;
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
		$huodong = $db->get_row("select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and is_del = 0 limit 1");
		if(!empty($huodong)){
			$rules = json_decode($huodong->guizes,true);
			$columns = array_column($rules,'man');
			array_multisort($columns,SORT_DESC,$rules);
			$zong_money = $total_fee;
			foreach ($rules as $rule){
				if($zong_money >= $rule['man']){
					if($huodong->type==1){
						$money = $rule['jian'];
						$num = floor($zong_money/$rule['man']);
						$num = 1;
						$gift_money = $money*$num;
						$db->query("update users set wx_money=wx_money+$gift_money where id=$order->userId");
						
				// 		$db->query("update user_card set yue = yue + $gift_money, earn=earn + $gift_money where id = $cardId");
						$liushui = array();
						$liushui['userId']=$order->userId;
						$liushui['comId']=$comId;
						$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
						$liushui['money']=$gift_money;
						$liushui['yue']= $db->get_var("select wx_money from users where id = $order->userId");
						$liushui['type']=2;
						$liushui['dtTime']=date("Y-m-d H:i:s");
						$liushui['cardId'] = $cardId;
						$liushui['remark']='充值赠送';
						$liushui['orderInfo']='充值赠送';
						$db->insert_update('user_liushui'.$fenbiao, $liushui,'id');
						break;
						$zong_money = $zong_money%$rule['man'];
					}else if($huodong->type==2){//满赠
						$money = $rule['jian'];
						$num = floor($zong_money/$rule['man']);
						$gift_money = $money*$num;
						$db->query("update users set jifen=jifen+$gift_money where id=$order->userId");
						$jifen_jilu = array();
						$jifen_jilu['userId'] = $order->userId;
						$jifen_jilu['comId'] = $comId;
						$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
						$jifen_jilu['jifen'] = $gift_money;
						$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$order->userId);
						$jifen_jilu['type'] = 1;
						$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
						$jifen_jilu['remark'] = '充值赠送';
						$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
						$zong_money = $zong_money%$rule['man'];
					}else if($huodong->type==3){//优惠券
						$yhqId = $rule['yhqId'];
						$yhq = $db->get_row("select * from yhq where id=$yhqId and comId=$comId and status=1");
						if(empty($yhq)){
							return false;
						}
						if($yhq->hasNum>=$yhq->num){
							return false;
						}
						$num = floor($zong_money/$rule['man']);
						for($i=0;$i<$num;$i++){
							$user_yhq = array();
						  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
						  	$user_yhq['comId'] = $comId;
						  	$user_yhq['userId'] = $order->userId;
						  	$user_yhq['jiluId'] = $yhqId;
						  	$user_yhq['fafangId'] = 0;
						  	$user_yhq['title'] = $yhq->title;
						  	$user_yhq['man'] = $yhq->man;
						  	$user_yhq['jian'] = $yhq->money;
						  	$user_yhq['startTime'] = $yhq->startTime;
						  	$user_yhq['endTime'] = $yhq->endTime;
						  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
						  	$db->insert_update('user_yhq'.$fenbiao,$user_yhq,'id');
						  	$db->query("update yhq set hasnum=hasnum+1 where id=$id");
						}
						$zong_money = $zong_money%$rule['man'];
					}
				}
			}
		}
	}
	ob_clean();
	echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
	exit;
}else{
	file_put_contents('weixin_pay_err.log',json_encode($_POST).PHP_EOL,FILE_APPEND);
}