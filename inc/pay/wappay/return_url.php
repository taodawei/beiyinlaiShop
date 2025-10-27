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
					$order = $db->get_row("select * from order$fenbiao where orderId='$out_trade_no' limit 1");
					if($order->status==-5||$order->status==-1){
						$orderId = $order->id;
						$o = array();
						$o['id'] = $orderId;
						if($order->price_dingjin==0){
							$o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
							$o['ispay'] = 1;
							$o['pay_type'] = 3;
						}
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
						$db->insert_update('order'.$fenbiao,$o,'id');
						if($order->price_dingjin==0){
							order_pay_done($order);
						}else{
							$yushou = $db->get_row("select * from yushou where id=$order->yushouId");
							$tixing_time = strtotime($yushou->startTime1);
							$pay_endtime = strtotime($yushou->endTime1);
							$db->query("update order$fenbiao set pay_endtime='$yushou->endTime1',price_dingjin=0 where id=$orderId");
							$db->query("delete from demo_timed_task where comId=$comId and params='{\"order_id\":".$orderId."}' and router='order_checkPay' limit 1");
							$timed_task = array();
							$timed_task['comId'] = $comId;
							$timed_task['dtTime'] = $pay_endtime;
							$timed_task['router'] = 'order_checkPay';
							$timed_task['params'] = '{"order_id":'.$orderId.'}';
							$db->insert_update('demo_timed_task',$timed_task,'id');
							$timed_task['comId'] = $comId;
							$timed_task['dtTime'] = $tixing_time;
							$timed_task['router'] = 'order_payTixing';
							$timed_task['params'] = '{"order_id":'.$orderId.',"user_id":"'.$order->userId.'"}';
							$db->insert_update('demo_timed_task',$timed_task,'id');
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
<?php
//点餐支付完成
function diancan_pay_done($orderId,$userId,$comId){
	global $db;
	$order_comId = $comId;
	$order_fenbiao = getFenbiao($order_comId,20);
	$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	if($order->status!=2 && $order->status!=3 && $order->peisong_type!=4){
		return false;
	}
	$db->query("update order$order_fenbiao set status=4 where id=$orderId");
	$db->query("update order_detail$order_fenbiao set status=2 where orderId=$orderId limit 1");
	$jilu = array();
	$jilu['orderId'] = $orderId;
	$jilu['username'] = '系统';
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['type'] = 1;
	$jilu['remark'] = '自动确认收货';
	$jilu['operate'] = '确认收货';
	$db->insert_update('order_jilu'.$fenbiao,$jilu,'id');
	$date = date("Y-m-d H:i:s");
	if($order->jifen>0){
		$db->query("update users set jifen=jifen+$order->jifen where id=$order->userId");
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $order->userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = $order->jifen;
		$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$order->userId);
		$jifen_jilu['type'] = 1;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '订单返积分，订单号：'.$order->orderId;
		//$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
		$db->insert_update('user_jifen'.$order_fenbiao,$jifen_jilu,'id');
	}
	if(!empty($order->fanli_json)){
		$fanli_json = json_decode($order->fanli_json);
		//社区收入
		if($fanli_json->shequ_id>0 && $fanli_json->shequ_fanli>0){
			$db->query("update users set money=money+".$fanli_json->shequ_fanli.",earn=earn+".$fanli_json->shequ_fanli." where id=$fanli_json->shequ_id");
			$yue = $db->get_var("select money from users where id=$fanli_json->shequ_id");
			//$yzFenbiao = getYzFenbiao($fanli_json->shangji,20);
			$liushui = array();
			$liushui['userId']=$fanli_json->shequ_id;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$fanli_json->shequ_fanli;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=$date;
			$liushui['remark']='社区订单返利';
			$liushui['orderInfo']='社区订单返利，订单号：'.$order->orderId;
			$liushui['order_id']=$orderId;
			$liushui['from_user']=$userId;
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		//上级收入，如果shagnji为0算到平台收益
		if($fanli_json->shangji_fanli>0 && $fanli_json->shangji){
			//返利给团长
			$db->query("update users set money=money+".$fanli_json->shangji_fanli.",earn=earn+".$fanli_json->shangji_fanli." where id=$fanli_json->shangji");
			$yue = $db->get_var("select money from users where id=$fanli_json->shangji");
			//$yzFenbiao = getYzFenbiao($fanli_json->shangji,20);
			$liushui = array();
			$liushui['userId']=$fanli_json->shangji;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$fanli_json->shangji_fanli;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=$date;
			$liushui['remark']='下级返利';
			$liushui['orderInfo']='下级返利，订单号：'.$order->orderId;
			$liushui['order_id']=$orderId;
			$liushui['from_user']=$userId;
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		//团队奖励
		if($fanli_json->shangshangji_fanli>0 && $fanli_json->shangshangji>0){
			$db->query("update users set money=money+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
			$yue = $db->get_var("select money from users where id=$fanli_json->shangshangji");
			$liushui = array();
			$liushui['userId']=$fanli_json->shangshangji;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$fanli_json->shangshangji_fanli;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=$date;
			$liushui['remark']='下下级返利';
			$liushui['orderInfo']='下下级返利，订单号：'.$order->orderId;
			$liushui['order_id']=$orderId;
			$liushui['from_user']=$userId;
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		//店铺推荐返利
		if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
			$db->query("update users set money=money+".$fanli_json->tuijian_fanli.",earn=earn+".$fanli_json->tuijian_fanli." where id=$fanli_json->tuijian");
			$yue = $db->get_var("select money from users where id=$fanli_json->tuijian");
			$liushui = array();
			$liushui['userId']=$fanli_json->tuijian;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$fanli_json->tuijian_fanli;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=$date;
			$liushui['remark']='推荐商铺奖励';
			$liushui['orderInfo']='推荐商铺奖励，订单号：'.$order->orderId;
			$liushui['order_id']=$orderId;
			$liushui['from_mendian']=$order->comId;
			$liushui['from_user']=0;
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		//平台收益计算
		$pingtai_shouyi = array();
		$pingtai_shouyi['mendianId'] = $order->comId;
		$pingtai_shouyi['type'] = 1;
		$pingtai_shouyi['money'] = $fanli_json->pingtai_fanli;
		$pingtai_shouyi['money_order'] = $order->price;
		$pingtai_shouyi['money_gonghuo'] = empty($fanli_json->shop_fanli)?0:$fanli_json->shop_fanli;
		$pingtai_shouyi['money_tuanzhang'] = $fanli_json->tuijian_fanli;
		if(!empty($fanli_json->shangji)){
			$pingtai_shouyi['money_tuanzhang'] += $fanli_json->shangji_fanli;
		}
		if(!empty($fanli_json->shangshangji)){
			$pingtai_shouyi['money_tuanzhang'] += $fanli_json->shangshangji_fanli;
		}
		$pingtai_shouyi['money_tuijian'] = $fanli_json->tuijian_fanli;
		$pingtai_shouyi['dtTime'] = $date;
		$pingtai_shouyi['orderId'] = $orderId;
		$pingtai_shouyi['remark'] = '';
		$pingtai_shouyi['ifcount'] = 1;
		$db->insert_update('demo_pingtai_shouyi',$pingtai_shouyi,'id');
		//商家收益计算
		if(!empty($fanli_json->shop_fanli) && $fanli_json->if_shop_fanli==1){
			$db->query("update demo_shops set money=money+".$fanli_json->shop_fanli." where comId=$order->comId");
			//$yzFenbiao = getYzFenbiao($order->mendianId,20);
			$mendian_liushui = array();
			$mendian_liushui['mendianId'] = $order->comId;
			$mendian_liushui['comId'] = 10;
			$mendian_liushui['type'] = 1;
			$mendian_liushui['money'] = $fanli_json->shop_fanli;
			$mendian_liushui['yue'] = $db->get_var("select money from demo_shops where comId=$order->comId");
			$mendian_liushui['dtTime'] = $date;
			$mendian_liushui['typeInfo'] = '订单收益';
			$mendian_liushui['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$mendian_liushui['remark'] = '订单号：'.$order->orderId;
			$db->insert_update('demo_mendian_liushui10',$mendian_liushui,'id');
		}
	}
	$db->query("update user_yugu_shouru set status=1,qrTime='".date("Y-m-d")."' where comId=$comId and orderId=$order->id and order_type=1");
	$pay_json = json_decode($order->pay_json,true);
	if(!empty($pay_json['yibao']['price'])){
		$yibao_orderId = $pay_json['yibao']['orderId'];
		$uniqueOrderNo = $pay_json['yibao']['desc'];
		file_get_contents('http://buy.zhishangez.com/yop-api/sendDivide.php?orderId='.$order->id.'&comId='.$order->comId.'&payId='.$order->pay_id.'&yibao_orderId='.$yibao_orderId.'&uniqueOrderNo='.$uniqueOrderNo);
	}else{
		$db->query("update demo_yibao_fenzhang set status=2 where orderId=$order->id and comId=$order->comId and status=1 limit 1");
	}
	return true;
}
//订单付完全款执行的方法
function order_pay_done($order){
	global $db;
	if($order->peisong_type==4){
		$db->query("update users set `cost`=`cost`+$order->price where id=$order->userId and comId=$order->comId");
		$request['orderId'] = $orderId;
		diancan_pay_done($order->id,$order->userId,$order->comId);
	}else{
		$order_fenbiao = getFenbiao($order->comId,20);
		$orderId = $order->id;
		$userId = $order->userId;
		$order_comId = $order->comId;
		$if_tongbu = $db->get_var("select if_tongbu from demo_shezhi where comId=$order_comId");
		$db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
		if($order->tuan_id>0){
			$tuan = $db->get_row("select * from demo_tuan where id=$order->tuan_id");
			if($tuan->status==0){
				$userIds = empty($tuan->userIds)?$order->userId:$tuan->userIds.','.$order->userId;
				$uids = explode(',',$userIds);
				$userIds = implode(',',array_unique($uids));
				$orderIds = empty($tuan->orderIds)?$orderId:$tuan->orderIds.','.$orderId;
				$nums = $tuan->nums+$order->pdtNums;
				$db->query("update demo_tuan set userIds='$userIds',orderIds='$orderIds',nums=$nums where id=$order->tuan_id");
				//团购成功
				if($nums>=$tuan->user_num){
					//检查库存
					$orders = $db->get_results("select inventoryId,storeId,pdtNums from order$order_fenbiao where id in($orderIds)");
					foreach ($orders as $ord){
						$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$ord->inventoryId and storeId=$ord->storeId limit 1");
						$kc = $kucun->kucun-$kucun->yugouNum;
						if($kc<$ord->pdtNums){
							$db->query("update demo_tuan set status=-1,reason='库存不足' where id=$order->tuan_id");
							$oids = explode(',',$orderIds);
							foreach ($oids as $oid){
								$timed_task = array();
								$timed_task['dtTime'] = 0;
								$timed_task['comId'] = $order_comId;
								$timed_task['router'] = 'order_autotuikuan';
								$timed_task['params'] = '{"order_id":'.$oid.',"message":"拼团失败"}';
								$db->insert_update('demo_timed_task',$timed_task,'id');
							}
							return;
						}
					}
					$db->query("update demo_tuan set status=1 where id=$order->tuan_id");
					if($tuan->type==2){
						$product =  $db->get_row("select title from demo_product where id=$tuan->productId");
						$weight = $db->get_var("select sum(weight) from order$order_fenbiao where id in($orderIds)");
						$fahuo = array();
						$fahuo['comId'] = $order_comId;
						$fahuo['mendianId'] = $order->mendianId;
						$fahuo['addressId'] = $tuan->addressId;
						$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
						$fahuo['orderIds'] = $orderIds;
						$fahuo['type'] = 2;
						$fahuo['showTime'] = date("Y-m-d H:i:s");
						$fahuo['storeId'] = $order->storeId;
						$fahuo['dtTime'] = date("Y-m-d H:i:s");
						$fahuo['shuohuo_json'] = $tuan->shouhuo_json;
						$fahuo['productId'] = $tuan->productId;
						$fahuo['tuanzhang'] = $tuan->tuanzhang;
						$fahuo['product_title'] = $product->title;
						$fahuo['fahuo_title'] = $product->title;
						$fahuo['product_num'] = $nums;
						$fahuo['weight'] = $weight;
						$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
						$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
						$fahuoId = $db->get_var("select last_insert_id();");
						$db->query("update order$order_fenbiao set fahuoId=$fahuoId,status=2 where id in($orderIds)");
						$db->query("update order_detail$order_fenbiao set status=1 where orderId in($orderIds)");
						//增加库存的预购数量
						$oids = explode(',',$orderIds);
						foreach ($oids as $oid){
							$o = $db->get_row("select * from order$order_fenbiao where id=$oid");
							$db->query("update demo_kucun set yugouNum=yugouNum+".$o->pdtNums." where inventoryId=$o->inventoryId and storeId=$o->storeId limit 1");
							//增加产品订单数量
							$db->query("update demo_product_inventory set orders=orders+".$o->pdtNums." where id=$o->inventoryId");
							$db->query("update demo_product set orders=orders+".$o->pdtNums." where id=$inventory->productId");
							//同步商家增加易宝收入记录
							if($if_tongbu==1){
								$fanli_json = json_decode($o->fanli_json);
								/*$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli + $fanli_json->daili_fanli;
								$fanli_json->shop_fanli = $order->price-$zong_fanli;
								$fanli_str = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);*/
								$yibao_fenzhang = array();
								$yibao_fenzhang['comId'] = $order_comId;
								$yibao_fenzhang['money'] = $fanli_json->shop_fanli;
								$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
								$yibao_fenzhang['orderId'] = $oid;
								$yibao_fenzhang['payId'] = 0;
								$yibao_fenzhang['type'] = 1;
								$yibao_fenzhang['income_type'] = 2;
								$yibao_fenzhang['status'] = 1;
								$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
							}
							addTaskMsg(31,$oid,'您的商城有新的团购订单，请及时处理',$order_comId);
							print_order($o);
						}
					}else{
						$oids = explode(',',$orderIds);
						foreach ($oids as $oid){
							$order = $db->get_row("select * from order$order_fenbiao where id=$oid");
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
							$fahuo['orderIds'] = $oid;
							$fahuo['type'] = 1;
							$fahuo['showTime'] = date("Y-m-d H:i:s");
							$fahuo['storeId'] = $order->storeId;
							$fahuo['dtTime'] = date("Y-m-d H:i:s");
							$fahuo['shuohuo_json'] = $order->shuohuo_json;
							$fahuo['productId'] = 0;
							$fahuo['tuanzhang'] = $userId;
							$fahuo['product_title'] = $product_title;
							$fahuo['fahuo_title'] = $product_title;
							$fahuo['product_num'] = $order->pdtNums;
							$fahuo['weight'] = $order->weight;
							$fahuo['peisong_time'] = $order->peisong_time;
							$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
							if($order->yushouId>0){
								$fahuo['yushouId'] = $order->yushouId;
								$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
							}
							$fahuo['shequ_id'] = $order->shequ_id;
							$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
							$fahuoId = $db->get_var("select last_insert_id();");
							$db->query("update order$order_fenbiao set fahuoId=$fahuoId,status=2 where id=$oid");
							$db->query("update order_detail$order_fenbiao set status=1 where orderId=$oid");
							$db->query("update demo_kucun set yugouNum=yugouNum+".$order->pdtNums." where inventoryId=$order->inventoryId and storeId=$order->storeId limit 1");
							if($if_tongbu==1){
								$fanli_json = json_decode($order->fanli_json);
								/*$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli + $fanli_json->daili_fanli;
								$fanli_json->shop_fanli = $order->price-$zong_fanli;
								$fanli_str = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);*/
								$yibao_fenzhang = array();
								$yibao_fenzhang['comId'] = $order_comId;
								$yibao_fenzhang['money'] = $fanli_json->shop_fanli;
								$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
								$yibao_fenzhang['orderId'] = $oid;
								$yibao_fenzhang['payId'] = 0;
								$yibao_fenzhang['type'] = 1;
								$yibao_fenzhang['income_type'] = 2;
								$yibao_fenzhang['status'] = 1;
								$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
							}
							$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$oid");
							foreach ($details as $detail){
								$detail->num = (int)$detail->num;
								$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
								$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
							}
							addTaskMsg(31,$oid,'您的商城有新的订单，请及时处理',$order_comId);
							print_order($order);
						}
					}
				}
			}else{
				//增加退款任务
				$timed_task = array();
				$timed_task['dtTime'] = 0;
				$timed_task['comId'] = $order_comId;
				$timed_task['router'] = 'order_autotuikuan';
				$timed_task['params'] = '{"order_id":'.$order->id.',"message":"拼团失败"}';
				$db->insert_update('demo_timed_task',$timed_task,'id');
				$no_yugu = 1;
			}
		}else{
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
			$fahuo['tuanzhang'] = $userId;
			$fahuo['product_title'] = $product_title;
			$fahuo['fahuo_title'] = $product_title;
			$fahuo['product_num'] = $order->pdtNums;
			$fahuo['weight'] = $order->weight;
			$fahuo['peisong_time'] = $order->peisong_time;
			$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
			if($order->yushouId>0){
				$fahuo['yushouId'] = $order->yushouId;
				$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
			}
			$fahuo['shequ_id'] = $order->shequ_id;
			$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$orderId");
			if($if_tongbu==1){
				$fanli_json = json_decode($order->fanli_json);
				/*$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli + $fanli_json->daili_fanli;
				$fanli_json->shop_fanli = $order->price-$zong_fanli;
				$fanli_str = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);*/
				$yibao_fenzhang = array();
				$yibao_fenzhang['comId'] = $order_comId;
				$yibao_fenzhang['money'] = $fanli_json->shop_fanli;
				$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
				$yibao_fenzhang['orderId'] = $orderId;
				$yibao_fenzhang['payId'] = 0;
				$yibao_fenzhang['type'] = 1;
				$yibao_fenzhang['income_type'] = 2;
				$yibao_fenzhang['status'] = 1;
				$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
			}
			$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
			foreach ($details as $detail){
				$detail->num = (int)$detail->num;
				$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
				$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
			}
			addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
			print_order($order);
		}
		if($no_yugu!=1){
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
			if($fanli_json->buyer_fanli>0){
				$yugu_shouru = array();
				$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
				$yugu_shouru['userId'] = $order->userId;
				$yugu_shouru['order_type'] = 1;
				$yugu_shouru['orderId'] = $order->id;
				$yugu_shouru['dtTime'] = date("Y-m-d");
				$yugu_shouru['money'] = $fanli_json->buyer_fanli;
				$yugu_shouru['from_user'] = $order->userId;
				$yugu_shouru['remark'] = '自购返利';
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
		}
		if($order->if_zong!=1){
			$db->query("update users set `cost`=`cost`+$order->price where id=$order->userId and comId=$order->comId");
		}
	}
}
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