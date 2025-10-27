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
file_put_contents('logs/'.date("Y-m-d").'_notify.txt',$result.PHP_EOL,FILE_APPEND);
$result = json_decode($result,true);
if($result['status']=='SUCCESS' && $result['payAmount']>0){
	$order_pay = $db->get_row("select * from order_pay where orderId='".$result['orderId']."' order by id desc limit 1");
	if($order_pay->is_pay==1){
		die('SUCCESS');
	}
	$db->query("update demo_yibao_fenzhang set status=1 where payId=$order_pay->id and status=0");
	//$order_price = $order_pay->price-$order_pay->price_payed;
	$money = $result['payAmount'];
	$o = array();
	$o['id'] = $order_pay->id;
	$o['price_payed'] = getXiaoshu($money+$order_pay->price_payed,2);
	$pay_json = array();
	if(!empty($order_pay->pay_json)){
		$pay_json = json_decode($order_pay->pay_json,true);
	}
	$pay_json['yibao']['price'] = $money;
	$pay_json['yibao']['desc'] = $result['uniqueOrderNo'];
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	$o['is_pay'] = 1;
	$db->insert_update('order_pay',$o,'id');
	$orders = json_decode($order_pay->orderInfo,true);
	if(!empty($orders)){
		foreach ($orders as $ord) {
			$order_fenbiao = getFenbiao($ord['comId'],20);
			$order_comId = $ord['comId'];
			$orderId = $ord['orderId'];
			$order = $db->get_row("select * from order$order_fenbiao where id=".$ord['orderId']);
			$o = array();
			$o['id'] = $ord['orderId'];
			$o['price_payed'] = $order->price;
			$pay_json = array();
			if(!empty($order->pay_json)){
				$pay_json = json_decode($order->pay_json,true);
			}
			$pay_json['yibao']['price'] = getXiaoshu($order->price-$order->price_payed,2);
			$pay_json['yibao']['desc'] = $result['uniqueOrderNo'];
			$pay_json['yibao']['orderId'] = $result['orderId'];
			$pay_json['yibao']['pay_way'] = $result['paymentProduct'];
			$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
			$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
			$o['ispay'] = 1;
			$o['pay_type'] = 1;
			$db->insert_update('order'.$order_fenbiao,$o,'id');
			$db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
			$product_json = json_decode($order->product_json);
			$product_title = '';
			foreach ($product_json as $pdt){
				$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
			}
			if(!empty($product_title)){
				$product_title = substr($product_title,1);
			}
			$fahuo = array();
			$fahuo['comId'] = $order_comId;
			$fahuo['mendianId'] = $order->mendianId;
			$fahuo['addressId'] = $order->address_id;
			$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$fahuo['orderIds'] = $orderId;
			$fahuo['type'] = 1;
			$fahuo['showTime'] = date("Y-m-d H:i:s");
			$fahuo['storeId'] = $order->storeId;
			$fahuo['dtTime'] = date("Y-m-d H:i:s");
			$fahuo['shuohuo_json'] = $order->shuohuo_json;
			$fahuo['productId'] = 0;
			$fahuo['tuanzhang'] = $order->zhishangId;
			$fahuo['product_title'] = $product_title;
			$fahuo['fahuo_title'] = $product_title;
			$fahuo['product_num'] = $order->pdtNums;
			$fahuo['weight'] = $order->weight;
			$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
			if($order->yushouId>0){
				$fahuo['yushouId'] = $order->yushouId;
				$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
			}
			$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$orderId");
			$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
			foreach ($details as $detail){
				$detail->num = (int)$detail->num;
				$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
				$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
			}
			$fanli_json = json_decode($order->fanli_json);
			if($fanli_json->shangji>0 && $fanli_json->shangji_fanli>0){
				$yugu_shouru = array();
				$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
				$yugu_shouru['userId'] = $fanli_json->shangji;
				$yugu_shouru['order_type'] = 1;
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
				$yugu_shouru['order_type'] = 1;
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
				$yugu_shouru['order_type'] = 1;
				$yugu_shouru['orderId'] = $order->id;
				$yugu_shouru['dtTime'] = date("Y-m-d");
				$yugu_shouru['money'] = $fanli_json->tuijian_fanli;
				$yugu_shouru['from_user'] = $order->userId;
				$yugu_shouru['remark'] = '推荐店铺返利';
				$yugu_shouru['order_orderId'] = $order->orderId;
				$yugu_shouru['order_comId'] = $order->comId;
				$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
			}
			if($fanli_json->shequ_id>0 && $fanli_json->shequ_fanli>0){
				$yugu_shouru = array();
				$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
				$yugu_shouru['userId'] = $fanli_json->shequ_id;
				$yugu_shouru['order_type'] = 1;
				$yugu_shouru['orderId'] = $order->id;
				$yugu_shouru['dtTime'] = date("Y-m-d");
				$yugu_shouru['money'] = $fanli_json->shequ_fanli;
				$yugu_shouru['from_user'] = $order->userId;
				$yugu_shouru['remark'] = '社区返利';
				$yugu_shouru['order_orderId'] = $order->orderId;
				$yugu_shouru['order_comId'] = $order->comId;
				$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
			}
			addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
			print_order($order);
		}
	}
}else{
	file_put_contents('pay_err.txt',json_encode($result,JSON_UNESCAPED_UNICODE),FILE_APPEND);
}
echo "SUCCESS";
function print_order($order){
	global $db;
	$comId = $order->comId;
	$print = $db->get_row("select * from demo_prints where comId=$comId and storeId=$order->storeId and status=1 and if_auto=1 limit 1");
	if(!empty($print)){
		require_once(ABSPATH.'/inc/print.class.php');
		$shouhuo_json = json_decode($order->shuohuo_json,true);
		$product_json = json_decode($order->product_json,true);
		$title = '订单详情';
		if($order->shequ_id>0){
			$title = $db->get_var("select title from demo_shequ where id=$order->shequ_id");
		}
		$content = '';//打印内容
		$content .= '<FB><center>'.$title.'</center></FB>';
		$content .= '\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$content .= '<FB>姓名:'.$shouhuo_json['收件人'].'</FB>\n';
		$content .= '<FB>联系电话:'.$shouhuo_json['手机号'].'</FB>\n';
		$content .= '<FB>配送地址:'.($order->peisong_type==1?'站点自提':$shouhuo_json['所在地区'].$shouhuo_json['详细地址']).'</FB>\n';
		$content .= '<FB>下单时间: '.$order->dtTime.'</FB>\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$num = 0;
		if(!empty($product_json))
		{
			foreach($product_json as $k=>$v){
				$num+=$v['num'];
				$content .= '<FS>'.$v['title'].($v['key_vals']=='无'?'':'【'.$v['key_vals'].'】').'：'.$v['price_sale'].'*'.$v['num'].'</FS>\n';
			}
		}
		$content .= str_repeat('-',32)."\n";
		$content .= '\n';
		$content .= '<FS>数量: '.$num.'</FS>\n';
		$content .= '<FS>总计: '.$order->price.'元</FS>\n';
		$content .= '<FS>备注: '.$order->remark.'</FS>\n';
		$content .= '<FS>订单编号: '.$order->orderId.'</FS>\n';
		$content .= '<FS>支付状态: 已支付</FS>\n';
		$content .= '<FS>打印时间: '.date("Y-m-d H:i:s").'</FS>\n';
		$prints = new Yprint();
		$content = $content;
		//$apiKey = "40f9b00bd79d73c056db5dcf906cbc97f02b920e";
		//$msign = 'a86n3hyzrfdy';
		//打印
		//file_put_contents('print.txt',$content);
		$prints->action_print($print->userId,$print->Tnumber,$content,$print->Akey,$print->Tkey);
	}
}