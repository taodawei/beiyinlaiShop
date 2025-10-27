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
file_put_contents('logs/'.date("Y-m-d").'_pdts_notify.txt',$result.PHP_EOL,FILE_APPEND);
$result = json_decode($result,true);
if($result['status']=='SUCCESS' && $result['payAmount']>0){
	$order_info = explode('_',$result['orderId']);
	$comId = $order_info[0];
	$total_fee = $result['payAmount'];
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select * from demo_pdt_order where orderId='".str_replace('__','_',$result['orderId'])."' limit 1");
	if($order->status==-5||$order->status==-1){
		$orderId = $order->id;
		$o = array();
		$o['id'] = $orderId;
		$o['status'] = $order->address_id>0?2:4;//需要收货的设置2 不需要的设置4
		$o['ispay'] = 1;
		$o['pay_type'] = 2;
		$o['price_payed'] = $total_fee+$order->price_payed;
		$o['pay_id'] = 0;
		$pay_json = array();
		if(!empty($order->pay_json)){
			$pay_json = json_decode($order->pay_json,true);
		}
		$pay_json['yibao']['price'] = $total_fee;
		$pay_json['yibao']['desc'] = $result['uniqueOrderNo'];
		$pay_json['yibao']['orderId'] = $result['orderId'];
		$pay_json['yibao']['pay_way'] = $result['paymentProduct'];
		
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
		//$db->query("update demo_yibao_fenzhang set status=1 where orderId=$order->id and payId=0 limit 1");
	}else{
		file_put_contents('weixin_pay_err.log',json_encode($_POST).PHP_EOL,FILE_APPEND);
	}
}else{
	file_put_contents('pay_err.txt',json_encode($result,JSON_UNESCAPED_UNICODE),FILE_APPEND);
}
echo "SUCCESS";