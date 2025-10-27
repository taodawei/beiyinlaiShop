<?php
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
include 'conf.php';
require_once ("./lib/YopClient.php");
require_once ("./lib/YopClient3.php");
require_once ("./lib/Util/YopSignUtils.php");

function callback($source){
    global $merchantno;
	global $private_key;
	global $yop_public_key;
    return YopSignUtils::decrypt($source,$private_key, $yop_public_key);
}
$data = $_REQUEST["response"];
$result = callback($data);
//$result = json_decode(json_encode($result,JSON_UNESCAPED_UNICODE),true);
file_put_contents('logs/'.date("Y-m-d").'_notify.txt',$result.PHP_EOL,FILE_APPEND);
$result = json_decode($result,true);
if($result['status']=='SUCCESS' && $result['payAmount']>0){
	$order_info = explode('_',$result['orderId']);
	$comId = $order_info[0];
	$userId = $order_info[1];
	$total_fee = $result['payAmount'];
	$db->query("update demo_chongzhi_jilu set status=1,`return`='".$result['uniqueOrderNo']."' where orderId='".$result['orderId']."' limit 1");
	if($comId!=10){
		$fenbiao = getFenbiao($comId,20);
		$db->query("update users set money=money+$total_fee where id=$userId");
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$total_fee;
		$liushui['yue']=$db->get_var("select money from users where id=$userId");
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='充值';
		$liushui['orderInfo']='微信(易宝)充值，单号：'.$result['uniqueOrderNo'];
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
		$fenzhang_money = $total_fee;
		$shouxufei = getXiaoshu($total_fee*7/1000,2);
		//商家收益减去手续费
		if($shouxufei>0){
			$fenzhang_money-=$shouxufei;
		}
		$yibao_fenzhang = array();
		$yibao_fenzhang['comId'] = $comId;
		$yibao_fenzhang['money'] = $fenzhang_money;
		$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
		$yibao_fenzhang['orderId'] = 0;
		$yibao_fenzhang['payId'] = 0;
		$yibao_fenzhang['type'] = 1;
		$yibao_fenzhang['income_type'] = 1;
		$yibao_fenzhang['status'] = 2;
		$yibao_fenzhang['remark'] = '用户充值';
		$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
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
						$db->query("update users set money=money+$gift_money where id=$userId");
						$liushui = array();
						$liushui['userId']=$userId;
						$liushui['comId']=$comId;
						$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
						$liushui['money']=$gift_money;
						$liushui['yue']=$db->get_var("select money from users where id=$userId");
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
						$db->query("update users set jifen=jifen+$gift_money where id=$userId");
						$jifen_jilu = array();
						$jifen_jilu['userId'] = $userId;
						$jifen_jilu['comId'] = $comId;
						$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
						$jifen_jilu['jifen'] = $gift_money;
						$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
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
						  	$user_yhq['userId'] = $userId;
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
	}else{
		$fenbiao = 10;
		$db_service = getCrmDb();
		$db_service->query("update demo_user set money=money+$total_fee where id=$userId");
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$total_fee;
		$liushui['yue']=$db->get_var("select money from users where id=$userId");
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='充值';
		$liushui['orderInfo']='微信(易宝)充值，单号：'.$result['uniqueOrderNo'];
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
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
						$db_service->query("update demo_user set money=money+$gift_money where id=$userId");
						$liushui = array();
						$liushui['userId']=$userId;
						$liushui['comId']=$comId;
						$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
						$liushui['money']=$gift_money;
						$liushui['yue']=$db_service->get_var("select money from demo_user where id=$userId");
						$liushui['type']=2;
						$liushui['dtTime']=date("Y-m-d H:i:s");
						$liushui['remark']='充值赠送';
						$liushui['orderInfo']='充值赠送';
						$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
						$zong_money = $zong_money%$rule['man'];
					}else if($huodong->type==2){//满赠
						$money = $rule['jian'];
						$num = floor($zong_money/$rule['man']);
						$gift_money = $money*$num;
						$db_service->query("update demo_user set jifen=jifen+$gift_money where id=$userId");
						$jifen_jilu = array();
						$jifen_jilu['userId'] = $userId;
						$jifen_jilu['comId'] = $comId;
						$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
						$jifen_jilu['jifen'] = $gift_money;
						$jifen_jilu['yue'] = $db_service->get_var('select jifen from users where id='.$userId);
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
						  	$user_yhq['userId'] = $userId;
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
}else{
	file_put_contents('pay_err.txt',json_encode($result,JSON_UNESCAPED_UNICODE),FILE_APPEND);
}
echo "SUCCESS";