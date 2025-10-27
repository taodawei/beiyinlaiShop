<?php
$subject = explode('_',$_GET['subject']);
$comId = $_REQUEST['com_id'] = (int)$subject[1];
require_once("../../../config/dt-config.php");
require_once("../../../inc/function.php");
require_once("../../class.database.php");
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
$alipay_arr = json_decode($alipay_set->info);
$alipay_config['partner'] = $alipay_arr->partnerId;
$alipay_config['seller_id']	= $alipay_config['partner'];
$alipay_config['private_key']	= $alipay_arr->private_key;
$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
//echo "select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1";
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>支付宝手机网站支付接口</title>
	</head>
    <body>
    	<?php
			//计算得出通知验证结果
			$alipayNotify = new AlipayNotify($alipay_config);
			$verify_result = $alipayNotify->verifyReturn();
			if($verify_result) {
				$fenbiao = (floor($comId/20))%20;
				$out_trade_no = $_GET['out_trade_no'];
				$trade_no = $_GET['trade_no'];
				$total_fee = $_GET['total_fee'];
			    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
					$order = $db->get_row("select * from demo_pdt_order where orderId='$out_trade_no' limit 1");
					if($order->status==-5||$order->status==-1){
						$orderId = $order->id;
						$o = array();
						$o['id'] = $orderId;
						$o['status'] = $order->address_id>0?2:4;//需要收货的设置2 不需要的设置4
						$o['ispay'] = 1;
						$o['pay_type'] = 3;
						$o['price_payed'] = $total_fee+$order->price_payed;
						
						$pay_json = array();
						if(!empty($order->pay_json)){
							$pay_json = json_decode($order->pay_json,true);
						}
						if($order->price_dingjin==0){
							$pay_json['alipay']['price'] = $total_fee;
							$pay_json['alipay']['desc'] = $trade_no;
						}else{
							$pay_json['dingjin']['price'] = $total_fee;
							$pay_json['dingjin']['paytype'] = '支付宝，订单号：'.$trade_no;
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
						file_put_contents('alipay_err.log',json_encode($_GET).PHP_EOL,FILE_APPEND);
					}
			    }
			    else {
			      echo "trade_status=".$_GET['trade_status'];
			    }
					
				echo "验证成功<script>location.href='/index.php?p=19&a=alone';</script>";

				//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
				
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
			else {
			    //验证失败
			    //如要调试，请看alipay_notify.php页面的verifyReturn函数
			    echo "验证失败";
			}
			?>
    </body>
</html>