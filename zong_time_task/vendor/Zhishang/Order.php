<?php
namespace Zhishang;
class Order{
	//检查订单是否已经支付
	public function checkPay($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$fenbiao = (int)$params['fenbiao'];
		$order = $db->get_row("select * from order$fenbiao where id=$orderId");
		if($order->status==-5){
			//修改订单状态
			$db->query("update order$fenbiao set status=-1 where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
			if(!empty($details)){
				foreach ($details as $detail){
					$db->query("update demo_kucun set yugouNum=yugouNum-".$detail->num." where inventoryId=$detail->inventoryId and storeId=".$order->storeId." limit 1");
				}
			}
			$if_tongbu = $db->get_var("select if_tongbu from demo_shezhi where comId=$order->comId");
			$db->query("delete from cuxiao_pdt_buy where orderId=$orderId and comId=".($if_tongbu==1?10:$order->comId));
			if($order->price_payed>0){
				$this->tuikuan($order);
			}
		}
		return '{"code":"1"}';
	}
	//检测预约
	function checkYuyue($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$db->query("update demo_shequ_yuyue set status=-1 where id=$orderId and status=0");
		return '{"code":"1"}';
	}
	function checkPays($params){
		global $db,$db_service;
		$pay = $db->get_row("select * from order_pay where id=".$params['pay_id']);
		if($pay->is_pay==0){
			if(!empty($pay->pay_json)){
				$pay_json = json_decode($pay->pay_json,true);
				if(!empty($pay_json['yhq']['desc'])){
					$db->query("update user_yhq10 set status=0,orderId=0 where id=".$pay_json['yhq']['desc']);
				}
			}
			$orderInfo = json_decode($pay->orderInfo);
			foreach($orderInfo as $info){
				$params = array();
				$params['order_id'] = $info->orderId;
				$params['fenbiao'] = getFenbiao($info->comId,20);
				$this->checkPay($params);
			}
		}
	}
	public function pdtShouhuo($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$db->query("update demo_pdt_order set status=4 where id=$orderId and status=3");
		return '{"code":"1"}';
	}
	/*自动评价*/
	public function autoComment($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$fenbiao = (int)$params['fenbiao'];
		$order = $db->get_row("select status,product_json,ifpingjia,comId,userId,orderId from order$fenbiao where id=$orderId");
		if($order->status!=4 || $order->ifpingjia==1){
			return '{"code":"1"}';
		}
		$db->query("update order$fenbiao set ifpingjia=1 where id=$orderId");
		$products = json_decode($order->product_json);
		foreach ($products as $product) {
			/*$inventory = $db->get_row("select comId,if_kuaidi,channelId,price_sale,price_market,price_gonghuo,image,key_vals,title from demo_product_inventory where id=".$product->id);*/
			$comment = array();
			$comment['orderId'] = $orderId;
			$comment['pdtId'] = $product->productId;
			$comment['inventoryId'] = $product->id;
			$comment['comId'] = $order->comId;
			$comment['userId'] = $order->userId;
			//$comment['mendianId'] = $order->mendianId;
			$comment['name'] = $db->get_var("select nickname from users where id=$order->userId");
			$comment['order_orderId'] = $order->orderId;
			$comment['pdtName'] = $product->title;
			$comment['star'] = 5;
			$comment['star1'] = 5;
			$comment['star2'] = 5;
			$comment['cont1'] = '好评！';
			$comment['images1'] = '';
			$comment['dtTime1'] = date('Y-m-d H:i:s');
			$comment['status'] = 1;
			$db->insert_update('order_comment'.$fenbiao,$comment,'id');
		}
	}
	public function checkPdtPay($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$order = $db->get_row("select status,inventoryId,pdtNums from demo_pdt_order where id=$orderId");
		if($order->status==-5){
			//修改订单状态
			$db->query("update demo_pdt_order set status=-1 where id=$orderId");
			$db->query("update demo_pdt_inventory set kucun=kucun+$order->pdtNums where id=$order->inventoryId");
		}
		return '{"code":"1"}';
	}
	//检测团购是否成功
	public function checkTuan($params){
		global $db;
		$tuanId = (int)$params['tuan_id'];
		$fenbiao = (int)$params['fenbiao'];
		$tuan = $db->get_row("select * from demo_tuan where id=$tuanId");
		if($tuan->status==0){
			$db->query("update demo_tuan set status=-1 where id=$tuanId");
			if(!empty($tuan->orderIds)){
				$ids = explode(',',$tuan->orderIds);
				foreach ($ids as $oid){
					if($oid>0){
						$order = $db->get_row("select * from order$fenbiao where id=$oid");
						$db->query("update order$fenbiao set status=-1,price_tuikuan='$order->price_payed' where id=$oid");
						$db->query("update order_detail$fenbiao set status=-1 where orderId=$oid");
						$this->tuikuan($order);
					}
				}
			}
			/*$uidstr = $tuan->orderIds;
			$uids = array_filter(array_unique(explode(',',$uidstr)));
			$product = $db->get_var("select title from demo_product where id=$tuan->productId");
			if(!empty($uids)){
				foreach ($uids as $oid) {
					$order = $db->get_row("select price_payed,userId from order0 where id=$oid");
					$openId = $db->get_var("select openId from users where id=$order->userId");
					$arr1 = array(
						'first' => array(
							'value' => '您参加的拼团由于团已过期，拼团失败。',
							'color' => '#FF0000'
						),
						'keyword1' => array(
							'value' => $product,
							'color' => '#FF0000'
						),
						'keyword2' => array(
							'value' => $order->price_payed,
							'color' => '#FF0000'
						),
						'keyword3' => array(
							'value' => $order->price_payed,
							'color' => '#FF0000'
						),
						'remark' => array(
							'value' => '您支付的资金将原路返还，请注意查收',
							'color' => '#FF0000'
						)
					);
					$this->post_template_msg('JrsMWfgB8kLjMoB7ryZT5nXKfoyV7k2OSsAHS3KdHRk',$arr1,$openId,'https://new.nmgyzwc.com/index.php?p=19&a=view_tuan&id='.$tuanId);
				}
			}*/
		}
		return '{"code":"1"}';
	}
	//异步执行退款操作
	public function autotuikuan($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$fenbiao = (int)$params['fenbiao'];
		$order = $db->get_row("select * from order$fenbiao where id=$orderId");
		if($order->price_payed>0 && $order->price_tuikuan==0){
			//修改订单状态
			$db->query("update order$fenbiao set status=-1,price_tuikuan='$order->price_payed' where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			$this->tuikuan($order);
		}
		return '{"code":"1"}';
	}
	//手动添加退款
	public function sdtuikuan($params){
		global $db;
		$orderId = (int)$params['order_id'];
		$fenbiao = (int)$params['fenbiao'];
		$order = $db->get_row("select * from order$fenbiao where id=$orderId");
		//if($order->price_payed>0 && $order->price_tuikuan==0){
			//修改订单状态
			$db->query("update order$fenbiao set status=-1,price_tuikuan='$order->price_payed' where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			$this->tuikuan($order);
		//}
		return '{"code":"1"}';
	}
	//订单自动收货返利
	public function autoShouhuo($params){
		global $db,$db_service;
		$orderId = (int)$params['order_id'];
		$order_fenbiao = (int)$params['fenbiao'];
		$order = $db->get_row("select * from order$order_fenbiao where id=$orderId");
		$if_tongbu = $order->if_zong;
		if($if_tongbu==1){
			$userId = $order->zhishangId;
			$comId = 10;
		}else{
			$userId = $order->userId;
			$comId = $order->comId;
		}
		$yzFenbiao = $fenbiao = getFenbiao($comId,20);
		if($order->status==3){
			$db->query("update order$order_fenbiao set status=4 where id=$orderId");
			$db->query("update order_detail$order_fenbiao set status=2 where orderId=$orderId");
			$jilu = array();
			$jilu['orderId'] = $orderId;
			$jilu['username'] = $db->get_var("select nickname from users where id=$order->userId");
			$jilu['dtTime'] = date("Y-m-d H:i:s");
			$jilu['type'] = 1;
			$jilu['remark'] = '系统自动确认收货';
			$jilu['operate'] = '确认收货';
			$db->insert_update('order_jilu'.$fenbiao,$jilu,'id');
			$date = date("Y-m-d H:i:s");
			
			if($order->jifen>0){
				if($order->if_zong==1){
					$db_service->query("update demo_user set jifen=jifen+$order->jifen where id=$order->zhishangId");
					$jifen_jilu = array();
					$jifen_jilu['userId'] = $order->zhishangId;
					$jifen_jilu['comId'] = $comId;
					$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
					$jifen_jilu['jifen'] = $order->jifen;
					$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$order->userId);
					$jifen_jilu['type'] = 1;
					$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
					$jifen_jilu['remark'] = '订单返积分，订单号：'.$order->orderId;
					//$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
					$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
				}else{
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
					$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
				}
			}
			if(!empty($order->fanli_json)){
				$fanli_json = json_decode($order->fanli_json);
				
				//自营收入，如果shagnji为0算到平台收益
				if($fanli_json->shangji_fanli>0 && $fanli_json->shangji){
					//返利给团长
    			    $shangji = $fanli_json->shangji;
    		        $personalFenhong = $fanli_json->shangji_fanli;
    		        
                    $db->query("update users set yongjin = yongjin+$personalFenhong, yongjins=yongjins+$personalFenhong where id = $shangji");
                    
                    $liushui = array();
                    $liushui['comId'] = $comId;
                    $liushui['userId']=$shangji;
                    $liushui['orderId']= $order->orderId;
                    $liushui['order_id'] = $order->id;
                    $liushui['bili'] = 1;
                    $liushui['order_total'] = $order->price;
                    $liushui['money'] = $personalFenhong;
                    $liushui['yue'] = $db->get_var("select yongjin from users where id = $shangji");
                    $liushui['type'] = 0;
                    $liushui['from_userId'] = $order->userId;
                    $liushui['from_mendianId'] = $order->mendianId;
                    $liushui['remark'] = '直推下单返利';
                    $liushui['orderInfo'] = "直推下单返利，直推用户：".$user->nickname."(".$user->phone.")";
                    $liushui['dtTime']=date("Y-m-d H:i:s");
        
                    $db->insert_update('user_yongjin8', $liushui,'id');
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
					$liushui['remark']='间推返利';
					$liushui['orderInfo']='间推返利，订单号：'.$order->orderId;
					$liushui['order_id']=$orderId;
					$liushui['from_user']=$userId;
			
					$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				}
			}
			$db->query("update user_yugu_shouru set status=1,qrTime='".date("Y-m-d")."' where comId=".($order->if_zong==1?'10':$order->comId)." and orderId=$order->id and order_type=1");
			$pay_json = json_decode($order->pay_json,true);
			if(!empty($pay_json['yibao']['price'])){
				$yibao_orderId = $pay_json['yibao']['orderId'];
				$uniqueOrderNo = $pay_json['yibao']['desc'];
				file_get_contents('http://buy.zhishangez.com/yop-api/sendDivide.php?orderId='.$order->id.'&comId='.$order->comId.'&payId='.$order->pay_id.'&yibao_orderId='.$yibao_orderId.'&uniqueOrderNo='.$uniqueOrderNo);
			}else{
				$db->query("update demo_yibao_fenzhang set status=2 where orderId=$order->id and comId=$order->comId and status=1");
			}
		}
		return '{"code":"1"}';
	}
	public function tuikuan($order){
		global $db,$db_service;
		$userId = $order->userId;
		$comId = $order->comId;
		$orderId = $order->id;
		$if_tongbu = $db->get_var("select if_tongbu from demo_shezhi where comId=$comId");
		$zong_fenbiao = $fenbiao = getFenbiao($comId,20);
		if($if_tongbu==1){
			$zong_fenbiao = 10;
			$userId = (int)$order->zhishangId;
		}
		$pay_json = json_decode($order->pay_json,true);
		//积分返回
		if(!empty($pay_json['jifen']['desc'])){
			$jifen = (int)$pay_json['jifen']['desc'];
			$db->query("update users set jifen=jifen+$jifen where id=$userId");
			$yue = $db->get_var('select jifen from users where id='.$userId);
			$jifen_jilu = array();
			$jifen_jilu['userId'] = $userId;
			$jifen_jilu['comId'] = $comId;
			$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$jifen_jilu['jifen'] = $jifen;
			$jifen_jilu['yue'] = $yue;
			$jifen_jilu['type'] = 1;
			$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
			$jifen_jilu['remark'] = '订单取消，订单号：'.$order->orderId;
			$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
		}
		//优惠券返还
		if(!empty($pay_json['yhq']['desc'])){
			$db->query("update user_yhq$zong_fenbiao set status=0,orderId=0 where id=".(int)$pay_json['yhq']['desc']);
		}
		//抵扣金支付
		if(!empty($pay_json['lipinka']['price'])){
			$giftId = (int)$pay_json['lipinka']['cardId'];
			$money = $pay_json['lipinka']['price'];
			$db->query("update gift_card$zong_fenbiao set yue=yue+$money where id=$giftId");
			$liushui = array();
			$liushui['cardId']=$giftId;
			$liushui['money']=$money;
			$liushui['yue']=$db->get_var("select yue from gift_card$zong_fenbiao where id=$giftId");
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='订单取消';
			$liushui['orderInfo']='订单取消，支付号：'.$order->orderId;
			$liushui['orderId']=$orderId;
			$db->insert_update('gift_card_liushui'.$zong_fenbiao,$liushui,'id');
		}
		//礼品卡支付
		if(!empty($pay_json['lipinka1']['price'])){
			$giftId = (int)$pay_json['lipinka1']['cardId'];
			$money = $pay_json['lipinka1']['price'];
			$db->query("update lipinka set yue=yue+$money where id=$giftId");
			$liushui = array();
			$liushui['cardId']=$giftId;
			$liushui['money']=$money;
			$liushui['yue']=$db->get_var("select yue from lipinka where id=$giftId");
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='订单取消';
			$liushui['orderInfo']='订单取消，支付号：'.$order->orderId;
			$liushui['orderId']=$orderId;
			$db->insert_update('lipinka_liushui',$liushui,'id');
		}
		//余额支付
		if(!empty($pay_json['yue']['price'])){
			$money = $pay_json['yue']['price'];
			if($pay_json['yue']['if_zong']==1){
				$db_service->query("update demo_user set money=money+$money where id=".($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
				$yue = $db_service->get_var('select money from demo_user where id='.($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
			}else{
				$db->query("update users set money=money+$money where id=".($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
				$yue = $db->get_var('select money from users where id='.($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
			}
			$liushui = array();
			$liushui['userId']=$pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$money;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='订单取消';
			$liushui['orderInfo']='订单取消，订单号：'.$order->orderId;
			$liushui['order_id']=$order->id;
			$db->insert_update('user_liushui'.($pay_json['yue']['if_zong']==1?10:$fenbiao),$liushui,'id');
		}
		//微信支付返余额
		if(!empty($pay_json['weixin']['price'])){
			$money = $pay_json['weixin']['price'];
			$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 and status=1 limit 1");
			if(!empty($weixin_set->info)){
				$weixin_arr = json_decode($weixin_set->info);
			}
			if(!empty($weixin_arr->sslkey) && !empty($weixin_arr->sslcert)){
				define('WX_APPID',$weixin_arr->appid);
				define('WX_MCHID',$weixin_arr->mch_id);
				define('WX_KEY',$weixin_arr->key);
				define('WX_APPSECRET',$weixin_arr->appsecret);
				define('WX_SSLKEY','/www/wwwroot/buy.zhishangez.com'.$weixin_arr->sslkey);
				define('WX_SSLCERT','/www/wwwroot/buy.zhishangez.com'.$weixin_arr->sslcert);
				require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
				require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
				$logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
				$log = \Log::Init($logHandler, 15);
				$transaction_id = $pay_json['weixin']['desc'][0];
				$total_fee = $money*100;
				$refund_fee = $total_fee;
				$input = new \WxPayRefund();
				$input->SetTransaction_id($transaction_id);
				$input->SetTotal_fee($total_fee);
				$input->SetRefund_fee($refund_fee);
				$input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
				$input->SetOp_user_id(WX_MCHID);
				file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE));
				$result = \WxPayApi::refund($input);
				if($result['result_code'] != "SUCCESS"){
					file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
					addTaskMsg(51,$order->id,'订单退款失败，请登录商户平台手动退款,订单号：'.$order->orderId.'，微信商户订单号：'.$transaction_id.',失败原因：'.$result['err_code_des'],$comId);
				}
			}else{
				if($if_tongbu==1){
					$db_service->query("update demo_user set money=money+$money where id=$userId");
					$yue = $db_service->get_var('select money from demo_user where id='.$userId);
				}else{
					$db->query("update users set money=money+$money where id=$userId");
					$yue = $db->get_var('select money from users where id='.$userId);
				}
				$liushui = array();
				$liushui['userId']=$userId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$money;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='订单取消';
				$liushui['orderInfo']='订单取消,微信支付返回账号余额，订单号：'.$order->orderId;
				$liushui['order_id']=$order->id;
				$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
			}
		}
		//微信小程序返余额
		if(!empty($pay_json['applet']['price'])){
			$money = $pay_json['applet']['price'];
			$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 and status=1 limit 1");
			if(!empty($weixin_set->info)){
				$weixin_arr = json_decode($weixin_set->info);
			}
			if(!empty($weixin_arr->sslkey) && !empty($weixin_arr->sslcert)){
				define('WX_APPID',$weixin_arr->appid);
				define('WX_MCHID',$weixin_arr->mch_id);
				define('WX_KEY',$weixin_arr->key);
				define('WX_APPSECRET',$weixin_arr->appsecret);
				define('WX_SSLKEY',ABSPATH.$weixin_arr->sslkey);
				define('WX_SSLCERT',ABSPATH.$weixin_arr->sslcert);
				require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
				require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
				$logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
				$log = \Log::Init($logHandler, 15);
				$transaction_id = $pay_json['applet']['desc'][0];
				$total_fee = $money*100;
				$refund_fee = $total_fee;
				$input = new \WxPayRefund();
				$input->SetTransaction_id($transaction_id);
				$input->SetTotal_fee($total_fee);
				$input->SetRefund_fee($refund_fee);
				$input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
				$input->SetOp_user_id(WX_MCHID);
				//file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE));
				$result = \WxPayApi::refund($input);
				if($result['result_code'] != "SUCCESS"){
					file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
					addTaskMsg(51,$order->id,'订单退款失败，请登录商户平台手动退款,订单号：'.$order->orderId.'，微信商户订单号：'.$transaction_id.',失败原因：'.$result['err_code_des'],$comId);
				}
			}else{
				if($order->if_zong==1){
					$db_service->query("update demo_user set money=money+$money where id=$userId");
					$yue = $db_service->get_var('select money from demo_user where id='.$userId);
				}else{
					$db->query("update users set money=money+$money where id=$userId");
					$yue = $db->get_var('select money from users where id='.$userId);
				}
				$liushui = array();
				$liushui['userId']=$userId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$money;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='订单取消';
				$liushui['orderInfo']='订单取消,微信支付返回账号余额，订单号：'.$order->orderId;
				$liushui['order_id']=$order->id;
				$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
			}		
		}
		//支付宝返余额
		if(!empty($pay_json['alipay']['price'])){
			$money = $pay_json['alipay']['price'];
			if($if_tongbu==1){
				$db_service->query("update demo_user set money=money+$money where id=$userId");
				$yue = $db_service->get_var('select money from demo_user where id='.$userId);
			}else{
				$db->query("update users set money=money+$money where id=$userId");
				$yue = $db->get_var('select money from users where id='.$userId);
			}
			$liushui = array();
			$liushui['userId']=$userId;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$money;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='订单取消';
			$liushui['orderInfo']='订单取消,支付宝支付返回账号余额，订单号：'.$order->orderId;
			$liushui['order_id']=$order->id;
			$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
		}
		$db->query("update user_yugu_shouru set status=-1 where comId=$comId and orderId=$order->id and order_type=1");
	}
}