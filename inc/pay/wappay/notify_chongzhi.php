<?php
$subject = explode('_',$_POST['subject']);
$comId = $_REQUEST['com_id'] = (int)$subject[1];
require_once("../../../config/dt-config.php");
require_once("../../class.database.php");
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
$alipay_arr = json_decode($alipay_set->info);
$alipay_config['partner'] = $alipay_arr->partnerId;
$alipay_config['seller_id']	= $alipay_config['partner'];
$alipay_config['private_key']	= $alipay_arr->private_key;
$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result) {
	$fenbiao = (floor($comId/20))%20;
	$out_trade_no = $_POST['out_trade_no'];
	$trade_no = $_POST['trade_no'];
	$total_fee = $_POST['total_fee'];
	$order = $db->get_row("select * from user_chongzhi where orderId='$out_trade_no' order by id desc limit 1");
	if(empty($order)){
		file_put_contents('chongzhi_err.log',$out_trade_no.$trade_no.PHP_EOL,FILE_APPEND);
	}else if($order->status==1){
		die();
	}else{
		$db->query("update user_chongzhi set return_orderId='$trade_no',status=1 where id=$order->id");
		$db->query("update users set money=money+$total_fee where id=$order->userId");
		$liushui = array();
		$liushui['userId']=$order->userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$total_fee;
		$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='充值';
		$liushui['orderInfo']='支付宝充值，支付宝单号：'.$trade_no;
		insert_update('user_liushui'.$fenbiao,$liushui,'id');
		$huodong = $db->get_row("select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1");
		if(!empty($huodong)){
			$rules = json_decode($huodong->guizes,true);
			$columns = array_column($rules,'man');
			array_multisort($columns,SORT_DESC,$rules);
			$zong_money = $total_fee;
			foreach ($rules as $rule){
				if($zong_money>$rule['man']){
					if($huodong->type==1){
						$money = $rule['jian'];
						$num = floor($zong_money/$rule['man']);
						$gift_money = $money*$num;
						$db->query("update users set money=money+$gift_money where id=$order->userId");
						$liushui = array();
						$liushui['userId']=$order->userId;
						$liushui['comId']=$comId;
						$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
						$liushui['money']=$gift_money;
						$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
						$liushui['type']=2;
						$liushui['dtTime']=date("Y-m-d H:i:s");
						$liushui['remark']='充值赠送';
						$liushui['orderInfo']='充值赠送';
						insert_update('user_liushui'.$fenbiao,$liushui,'id');
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
}
else {
    file_put_contents("yanzheng1.txt",'error');
}