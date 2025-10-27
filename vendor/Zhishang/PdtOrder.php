<?php
namespace Zhishang;
class PdtOrder{
	function createOrder(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$inventoryId = (int)$request['inventory_id'];
		$num = (int)$request['num'];
		$remark = $request['remark'];
		$addresssId = (int)$request['address_id'];
		if(empty($inventoryId)||$num<=0){
			return '{"code":0,"message":"inventory_id和num不能为空"}';
		}
	 	$inventory = $db->get_row("select * from demo_pdt_inventory where id=$inventoryId and status=1 and endTime>'".date("Y-m-d H:i:s")."'");
        if($inventory->kucun<$num){
        	return '{"code":0,"message":"下单失败！商品“'.$inventory->title.'”库存不足"}';
        }
        if($inventory->if_kuaidi==1&&empty($addresssId)){
        	return '{"code":0,"message":"下单失败！请选择收货地址"}';
        }
        $product = $db->get_row("select youxiaoqi_start,youxiaoqi_end,if_user_info from demo_pdt where id=$inventory->productId");
        if($product->if_user_info==1 && (empty($request['name']) || empty($request['phone']))){
        	return '{"code":0,"message":"下单失败！该商品必须要填写个人信息(姓名和电话)"}';
        }
		$shezhi = $db->get_row("select time_pay,user_bili,shangji_bili,fanli_type from demo_shezhi where comId=$comId");
		$time_pay = $shezhi->time_pay;
		$time_pay+=1;
		$check_pay_time = strtotime("+$time_pay minutes");
	    $zong_price = 0;
	    $zong_gonghuo_price = 0;
	    $pdtstr = '';
	    $product_json_arry = array();
	    $has_ids = array();
	    //返利信息
	    $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0);
	    $shop = $db->get_row("select tuijianren,tuijian_bili,pay_info,pingtai_fanli from demo_shops where comId=$inventory->comId");
	    if($comId==10){
	    	$db_service = get_zhishang_db();
	    	$u = $db_service->get_row("select shangji,shangshangji,tuan_id from demo_user where id=$userId");
	    	$fanli_json['shangji'] = $u->shangji;
	    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;
	    }else{
	    	$u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=".$userId);
	    	$fanli_json['shangji'] = $u->shangji;
	    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;//根据返利类型设定返利的上上级会员
	    }
	    $fanli_json['tuijian'] = (int)$shop->tuijianren;
	    //计算社区返利和团长返利
	    $fanli_shequ =0;$fanli_tuanzhang = 0;
        
        $order_comId = $inventory->comId;
	    $price = $inventory->price_sale;
	    $zong_price+=$price*$num;
        $zong_gonghuo_price+=$inventory->price_cost*$num;
        $pdtstr.=',{"'.$inventoryId.'":{"productId":'.$inventory->productId.',"yunfei_moban":0,"num":"'.$num.'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $inventory->productId;
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['num'] = $num;
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $price;
        $pdt['price_market'] = $this->getXiaoshu($inventory->price_market,2);
        $product_json_arry[] = $pdt;
        $fanli_tuanzhang +=$inventory->fanli_tuanzhang*$num;
        $db->query("update demo_pdt_inventory set kucun=kucun-".$num." where id=$inventory->id");	    
	    //价格相关
	    $price_json = new \StdClass();
	    $price_json_product = new \StdClass();
	    $price_json_product->price = $zong_price;
	    $price_json_product->desc = '';
	    $price_json->goods = $price_json_product;
	    if(!empty($pdtstr)){
	        $pdtstr = substr($pdtstr,1);
	        $pdt_arr = json_decode('['.$pdtstr.']');
	    }
	    //返利相关
	    $zongfanli = $zong_price-$zong_gonghuo_price;//商家返利
	    if($comId==10 && $zongfanli>0){
	    	$fanli_json['shop_fanli'] = $zong_gonghuo_price;
			$user_fanli = intval($zongfanli*$shezhi->user_bili)/100;
			$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
			if(!empty($fanli_json['shangji'])){
				$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
			}
			if(empty($fanli_json['shangshangji'])){
				$fanli_json['shangshangji_fanli'] = 0;
			}
			if(!empty($shop->tuijianren) && !empty($shop->tuijian_bili)){
				$fanli_json['tuijian_fanli'] = intval($zongfanli*$shop->tuijian_bili)/100;
			}
			$fanli_json['pingtai_fanli'] = $zongfanli-$fanli_json['shangshangji_fanli']-$fanli_json['shangji_fanli']-$fanli_json['tuijian_fanli'];
			$fanli_json['if_shop_fanli'] = 0;
			$fanli_json['user_type'] = 1;//1总平台  2商家平台 确认收货时按user_type判断用户返利去向
	    }else{
	    	$fanli_json['user_type'] = 2;
			$user_fanli = $fanli_tuanzhang;
			$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
			if(!empty($fanli_json['shangji'])){
				$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
			}
			if(empty($fanli_json['shangshangji'])){
				$fanli_json['shangshangji_fanli'] = 0;
			}
			if($shop->pingtai_fanli>0){
				$fanli_json['pingtai_fanli'] = intval($zong_price*$shop->pingtai_fanli)/100;
				$fanli_json['shop_fanli'] = $zong_price-$fanli_json['pingtai_fanli'];
			}else{
				$fanli_json['shop_fanli'] = $zong_price;
				$fanli_json['pingtai_fanli'] = 0;
			}
			$fanli_json['if_shop_fanli'] = 0;
	    }
	    //获取优惠券
	    $now = date("Y-m-d H:i:s");
		$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
	    //$jifen = get_order_jifen($pdt_arr,$zong_price);
	    //$storeId = get_fahuo_store($areaId,$order_comId);
		$order = array();
		$order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
		$order['userId'] = $userId;
		if($comId==10){
			$order['if_zong'] = 1;
			$order['zhishangId'] = $userId;
		}
		$order['comId'] = (int)$order_comId;
		$order['mendianId'] = (int)$order_comId;
		$order['status'] = -5;//待支付
		$order['dtTime'] = date("Y-m-d H:i:s");
		//$order['remark'] = $remark;
		$order['price'] = $zong_price;
		$order['pdtNums'] = $num;
		$order['pdtChanel'] = 0;
		$order['product_json'] = $product_json;
		$order['inventoryId'] = $inventory->id;
		$order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
		$order['hexiaos'] = $inventory->hexiaos;
		$order['youxiaoqi_start'] = $product->youxiaoqi_start;
		$order['youxiaoqi_end'] = $product->youxiaoqi_end;
		if($inventory->if_kuaidi==1){
			$order['address_id'] = $addresssId;
		}
		if(!empty($request['name']) && !empty($request['phone'])){
			$order['userInfo'] = '姓名：'.$request['name'].' 电话：'.$request['phone'];
			$user_info = array('name'=>$request['name'],'phone'=>$request['phone']);
			if($comId==10){
				$db_service->query("update demo_user set user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
			}else{
				$db->query("update users set user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
			}
		}
		//file_put_contents('request.txt',json_encode($order,JSON_UNESCAPED_UNICODE));
		$db->insert_update('demo_pdt_order',$order,'id');
		$order_id = $db->get_var("select last_insert_id();");
		$timed_task = array();
		$timed_task['comId'] = (int)$comId;
		$timed_task['dtTime'] = $check_pay_time;
		$timed_task['router'] = 'order_checkPdtPay';
		$timed_task['params'] = '{"order_id":'.$order_id.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		foreach ($product_json_arry as $detail) {
			$pdt = new \StdClass();
			$pdt->sn = $detail['sn'];
			$pdt->title = $detail['title'];
			$pdt->key_vals = $detail['key_vals'];
			$order_detail = array();
			$order_detail['comId'] = (int)$order_comId;
			$order_detail['mendianId'] = (int)$order_comId;
			$order_detail['userId'] = (int)$userId;
			$order_detail['orderId'] = $order_id;
			$order_detail['inventoryId'] = $detail['id'];
			$order_detail['productId'] = $detail['productId'];
			$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
			$order_detail['num'] = $detail['num'];
			$order_detail['dtTime'] = date("Y-m-d H:i:s");
			$db->insert_update('pdt_order_detail',$order_detail,'id');
		}
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['order_id'] = $order_id;
		$return['data']['orderId'] = $order['orderId'];
		$return['data']['order_price'] = $order['price'];
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function lists(){
		global $db,$request,$comId;
		$scene = (int)$request['scene'];
		$userId = (int)$request['user_id'];
		$keyword = $request['keyword'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		if($comId==10){
			$sql = "select * from demo_pdt_order where zhishangId=$userId and if_zong=1";
		}else{
			$sql="select * from demo_pdt_order where comId=$comId and userId=$userId and if_zong=0 ";
		}
		if(!empty($scene)){
			switch($scene){
				case 1:
					$sql.=" and status=-5";
				break;
				case 2:
					$sql.=" and status=4 and ishexiao<hexiaos";
				break;
				case 3:
					$sql.=" and status=4 and ishexiao>=hexiaos";
				break;
			}
		}
		if(!empty($keyword)){
			$sql.=" and product_json like '%$keyword%' ";
		}
		$count = $db->get_var(str_replace('*','count(*)',$sql));
		$sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		$now = time();
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$data = array();
				$data['id'] = $pdt->id;
				$data['orderId'] = $pdt->orderId;
				switch ($pdt->status) {
					case 0:
						$data['statusInfo'] = '待审核';
					break;
					case 4:
						if($pdt->iehexiao>=$pdt->hexiaos){
							$data['statusInfo'] = '已完成';
							$pdt->status = 5;
						}else{
							$data['statusInfo'] = '待核销';
						}
					break;
					case -5:
						$data['statusInfo'] = '待支付';
					break;
					case -1:
						$data['statusInfo'] = '无效';
					break;
				}
				$product_json = json_decode($pdt->product_json);
				$data['products'] = $product_json;
				$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
				//$data['endTime'] = strtotime($pdt->pay_endtime)*1000;
				/*$data['jishiqi'] = 0;
				if($data['statusInfo']=='<span style="color:#cf2950;">待支付</span>' && $pdt->yushouId==0){
					$data['jishiqi'] = 1;
				}*/
				//$data['jishiqi'] = $data['statusInfo']=='<span style="color:#cf2950;">待支付</span>'?1:0;
				$data['price'] = $pdt->price;
				$data['price_payed'] = $pdt->price_payed;
				$data['num'] = $pdt->pdtNums;
				$data['comId'] = $pdt->comId;
				$data['status'] = $pdt->status;
				if($pdt->status==4){
					$data['hexiaoma'] = $this->get_36id($pdt->id);
				}
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function detail(){
		global $db,$request,$comId;
		$order_id = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$order = $db->get_row("select * from demo_pdt_order where id=$order_id");
		if(empty($order) || $order->userId!=$userId){
			return '{"code":0,"message":"订单不存在"}';
		}
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['orderId'] = $order->orderId;
		switch ($order->status) {
			case 0:
				$return['data']['statusInfo'] = '待审核';
			break;
			case 4:
				if($order->iehexiao>=$order->hexiaos){
					$return['data']['statusInfo'] = '已完成';
					$order->status = 5;
				}else{
					$return['data']['statusInfo'] = '待核销';
				}
			break;
			case -5:
				$return['data']['statusInfo'] = '待支付';
			break;
			case -1:
				$return['data']['statusInfo'] = '无效';
			break;
		}
		$product_json = json_decode($order->product_json);
		$return['data']['products'] = $product_json;
		$return['data']['dtTime'] = date("Y-m-d H:i",strtotime($order->dtTime));
		$return['data']['price'] = $order->price;
		$return['data']['price_payed'] = $order->price_payed;
		$return['data']['num'] = $order->pdtNums;
		$return['data']['status'] = $order->status;
		if($order->status==4){
			$return['data']['hexiaoma'] = $this->get_36id($order->id);
		}
		$return['data']['youxiaoqi'] = $order->youxiaoqi_start.' - '.$order->youxiaoqi_end;
		$return['data']['if_user_info'] = empty($order->userInfo)?0:1;
		if(!empty($order->userInfo)){
			$userInfo = explode(' ', $order->userInfo);
			$return['data']['user_name'] = str_replace('姓名：','',$userInfo[0]);
			$return['data']['user_phone'] = str_replace('电话：','',$userInfo[1]);
		}
		$shezhi = $db->get_row("select com_phone,com_title,com_address,zuobiao from demo_shezhi where comId=$order->comId");
		$return['data']['shop_name'] = $shezhi->com_title;
		$return['data']['shop_phone'] = $shezhi->com_phone;
		$return['data']['shop_address'] = $shezhi->com_address;
		$return['data']['shop_zuobiao'] = $shezhi->zuobiao;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function getXiaoshu($num,$weishu=2){
		return str_replace(',','',number_format($num,$weishu));
	}
	function get_36id($char){
		$num = intval($char);
		if ($num <= 0)
		return false;
		$charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$char = '';
		do {
			$key = ($num - 1) % 36;
			$char= $charArr[$key] . $char;
			$num = floor(($num - $key) / 36);
		} while ($num > 0);
		$char = buling($char,6);
		return $char;
	}
}