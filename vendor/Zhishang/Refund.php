<?php
namespace Zhishang;
class Refund{
    
    public function lists(){
		global $db,$request,$comId;
		
		$userId = (int)$request['user_id'];
		$status = (int)$request['status'];
		$fenbiao = getFenbiao($comId,20);
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$sql="select id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json,dtTime from order_tuihuan where comId=$comId and userId=$userId and is_del = 0 ";
		if(isset($request['status']) && in_array($status, [0,1])){
	    	switch ($status) {
    			case 0:
    				$sql.=" and status>-1 and status<6";
    			break;
    			case 1:
    				$sql.=" and status=6";
    			break;
    			default:
    				$sql.=" and status=$status";
    			break;
    		}
		}
	
		$count = $db->get_var(str_replace('id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json,dtTime','count(*)',$sql));
		$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum; 
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data']['count'] = $count;
		$return['data']['pages'] = ceil($count/$pageNum);
		$return['data']['list'] = array();
		$now = time();
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$data = array();
				$data['id'] = $pdt->id;
				$data['sn'] = $pdt->order_orderId;
				$orderInfo = $db->get_row("select pay_type,mendianId from order$fenbiao where id =".$pdt->orderId);
        		if($orderInfo->mendianId > 0){
        		    $mendian = $db->get_row("select * from mendian where id =".$orderInfo->mendianId);
        			$data['mendianId'] = $mendian->id;
        		    $data['mendian_title'] = $mendian->title;    
        		}else{
            //         $data['mendianId'] = 0;
        		  //  $data['mendian_title'] = '自营';    
        		}
        		
				$data['type'] = $pdt->type;
				$data['status'] = $pdt->status;
				switch($pdt->type){
					case 1:
						$data['type_info'] = '退款补偿(不退货)';
					break;
					case 2:
						$data['type_info'] = '退货退款';
					break;
					case 3:
						$data['type_info'] = '换货不退款';
					break;
				}
				switch ($pdt->status) {
					case 1:
						$data['status_info'] = '待审核';
					break;
		    		case 2:
		    			if(empty($pdt->kuaidi_json)){
		    				$data['status_info'] = '待买家发货';
		    			}else{
		    				$data['status_info'] = '退货待收货';
		    			}
		    		break;
		    		case 3:
		    			$data['status_info'] = '待退款';
		    		break;
		    		case 4:
		    			$data['status_info'] = '换货待发货';
		    		break;
		    		case 5:
		    			$data['status_info'] = '待客户收货';
		    		break;
		    		case 6:
		    			$data['status_info'] = '已完成';
		    		break;
		    		case -1:
		    		    if($pdt->dealCont == '客户自行取消申请'){
		    		        $data['status_info'] = '已取消';
		    		        $pdt->status = -2;
		    		    }else{
		    			    $data['status_info'] = '已驳回';//1-待审核  2-待买家发货/退货待收货 3-待退款  4-换货待发货  5-待客户接货 6-已完成  -1 已驳回
		    		    }
		    		break;
				}
				//$pdt->product_json = $db->get_var("select product_json from order0 where id=$pdt->orderId");
				$product_json = json_decode($pdt->pdtInfo);
				/*$data['inventoryId'] = $product_json->id;
				$data['product'] = $product_json->title.'【'.$product_json->key_vals.'】';
				$data['image'] = ispic($product_json->image);
				$data['price_sale'] = $product_json->price_sale;
				$data['price_market'] = $product_json->price_market;
				$data['num'] = $pdt->nums;*/
				$data['products'] = $product_json;
				$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
				$data['money'] = $pdt->money;
				$data['dealCont'] = $pdt->dealCont;
				
				$return['data']['list'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function add(){
		global $db,$request,$comId;
		
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$inventoryId = (int)$request['inventoryId'];
		$fenbiao = getFenbiao($comId,20);
		$type = (int)$request['type'];
		$num = (int)$request['num'];
		$reason = $request['reason'];
		$remark = $request['remark'];
		$addressId = $request['address_id'];
		$product_info = json_decode($request['product_info']);
		//$order = $db->get_row("select price_payed,product_json,orderId,status,mendianId,storeId,product_json,zhishangId from order$fenbiao where id=$orderId and userId=$userId");
		//$order = $db->get_row("select d.id did,d.pdtInfo,d.productId, d.ifshouhou, o.product_json,o.mendianId,o.storeId,o.orderId from order_detail$fenbiao d inner join order$fenbiao o on d.orderId = o.id where d.inventoryId = $inventoryId and o.id = $orderId");
		
		$order = $db->get_row("select d.id did,d.pdtInfo,d.productId, d.ifshouhou, o.product_json,o.mendianId,o.storeId,o.orderId,d.unit_price,d.refund_price,d.num from order_detail$fenbiao d inner join order$fenbiao o on d.orderId = o.id where d.inventoryId = $inventoryId and o.id = $orderId");
	
	   // echo  "select d.id did,d.pdtInfo,d.productId, d.ifshouhou, o.product_json,o.mendianId,o.storeId,o.orderId from order_detail$fenbiao d inner join order$fenbiao o on d.orderId = o.id where d.inventoryId = $inventoryId and o.id = $orderId";die;
		if(empty($order)||$order->ifshouhou!=0){
			return '{"code":0,"message":"该订单当前状态不支持售后申请"}';
		}
		
		$lujing = 'upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$imgs = $request['uploadedfile1'];
		if(!empty($request['uploadedfile2'])){
			$imgs.='|'.$request['uploadedfile2'];
		}
		if(!empty($request['uploadedfile3'])){
			$imgs.='|'.$request['uploadedfile3'];
		}
		$nums = 0;
		$pdtIds = '';
		$pdtInfo = null;
		$product_json = json_decode($order->product_json);
		foreach ($product_json as $pro) {
			if($inventoryId==$pro->id){
				$pro->num = $num;
				$pdtInfo = $pro;
			}
		}
		$pdtIds = $inventoryId;
		$tuihuan = array();
		$tuihuan['orderId'] = $orderId;
		$tuihuan['if_jifen'] = (int)$db->get_var("select if_jifen from order$fenbiao where id = $orderId");
		$tuihuan['inventoryId'] = $inventoryId;
		$tuihuan['comId'] = $comId;
		$tuihuan['userId'] = $userId;
		$tuihuan['mendianId'] = $order->mendianId;
		$tuihuan['shopId'] = $order->mendianId;
		$tuihuan['sn'] = date("YmdHis").rand(1000000000,9999999999);
		$tuihuan['order_orderId'] = $order->orderId;
		$tuihuan['type'] = $type;
		if($type == 3 && $addressId){
		    $shouhuo_json = array();
            $address = $db->get_row("select * from user_address where id = $addressId");
            $shouhuo_json['收件人'] = $address->name;
            $shouhuo_json['手机号'] = $address->phone;
            $shouhuo_json['所在地区'] = $address->areaName;
            $shouhuo_json['详细地址'] = $address->address;
            $tuihuan['shouhuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
		}
		$tuihuan['liuchengId'] = 3;
		$tuihuan['dtTime'] = date("Y-m-d H:i:s");
		//$tuihuan['money'] =  $request['money'] ? $request['money'] : 0.00;
		$tuihuan['money'] =  $order->unit_price; 
		$tuihuan['tk_price'] =  $order->refund_price; 
		$tuihuan['pdtIds'] = $pdtIds;
		$tuihuan['pdtInfo'] = json_encode($pdtInfo,JSON_UNESCAPED_UNICODE);
		$tuihuan['nums'] = $num;
		$tuihuan['order_detail_id'] = $order->did;
		$tuihuan['reason'] = $request['reason'];
		$tuihuan['kuaidi_type'] = (int)$request['kuaidi_type'];
		$tuihuan['kuaidi_money'] = empty($request['kuaidi_money'])?0:$request['kuaidi_money'];
		$tuihuan['remark'] = $request['remark'];
		$tuihuan['storeId'] = $order->storeId;
		$tuihuan['images'] = $imgs;
		$db->insert_update('order_tuihuan',$tuihuan,'id');
		$tuihuanId = $db->get_var("select last_insert_id();");
		//$db->query("update order$fenbiao set status=-3 where id=$orderId");
		$db->query("update order_detail$fenbiao set ifshouhou = 1 where id = ".$order->did);
		//$db->query("delete from demo_timed_task where comId=$comId and router='order_autoShouhuo' and params='{\"order_id\":".$orderId."}' limit 1");
		$typeArr = ['', '退款订单', '退货退款订单', '换货订单'];
		addTaskMsg(34, $tuihuanId, '有新的'.$typeArr[$type].'需要进行审核，请及时处理');
		
		return '{"code":1,"message":"申请成功，请等待商家审核"}';
	}

	public function detail(){
		global $db,$request,$comId;
		
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$id = (int)$request['id'];
		$order = $db->get_row("select * from order_tuihuan where id=$id and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"退换货不存在，请检查id参数"}';
		}
		$product_json = json_decode($order->pdtInfo);
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据';
		$return['data'] = array();
		$return['data']['id'] = $order->id;
		
		$orderInfo = $db->get_row("select pay_type,mendianId,pay_json,price_json from order$fenbiao where id =".$order->orderId);
		$return['data']['pay_type'] = $orderInfo->pay_type;
		$return['data']['pay_json'] = json_decode($orderInfo->pay_json, true);
		$return['data']['price_json'] = json_decode($orderInfo->price_json, true);
		$return['data']['user_send'] = json_decode($order->kuaidi_json, true);
		$return['data']['mendian_send'] = json_decode($order->fahuo_json, true);
		$return['data']['shouhuo_json'] = null;
		if($order->shouhuo_json){
		    $return['data']['shouhuo_json'] = json_decode($order->shuohuo_json, true);
		}
		$return['data']['sn'] = $order->sn;
		$return['data']['type'] = $order->type;
		$return['data']['product_info'] = $product_json;
		$return['data']['dtTime'] = $order->dtTime;
		$return['data']['dealCont'] = $order->dealCont;
		$return['data']['money'] = $order->money;
		$return['data']['reason'] = $order->reason;
		$return['data']['remark'] = $order->remark;
		$return['data']['status'] = $order->status;
		$return['data']['uploadedfile1'] = $order->images ?  explode('|', $order->images) : [];
		$return['data']['mendianId'] = $order->mendianId;
		$shezhi = $db->get_row("select * from demo_shezhi where comId = $comId");
		$return['data']['shopMsg'] = array(
		    'name' => $shezhi->ai_fee,
		    'phone' => $shezhi->vip_daily_num,
		    'address' => $shezhi->putong_daily_num
		);
		switch ($order->status) {
			case 1:
				$return['data']['status_info'] = '待审核';
			break;
    		case 2:
    			if(empty($order->kuaidi_json)){
    				$return['data']['status_info'] = '待买家发货';
    			}else{
    				$return['data']['status_info'] = '退货待收货';
    			}
    		break;
    		case 3:
    			$return['data']['status_info'] = '待退款';
    			//https://admin.sdhmx.com/erp/index.php?s=order&a=wancheng_tuihuan&tuihuanId=11
    		break;
    		case 4:
    			$return['data']['status_info'] = '换货待发货';
    		break;
    		case 5:
    			$return['data']['status_info'] = '待客户收货';
    		break;
    		case 6:
    			$return['data']['status_info'] = '已完成';
    		break;
    		case -1:
    		    if($order->dealCont == '客户自行取消申请'){
    		        $order->status = -2;
    		        $return['data']['status_info'] = '已取消';
    		    }else{
    			    $return['data']['status_info'] = '已驳回';
    		    }
    		break;
		}
		return json_encode($return);
	}
	
	public function del()
	{
	    global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$id = (int)$request['id'];
		$order = $db->get_row("select * from order_tuihuan where id=$id and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"退换货不存在，请检查id参数"}';
		}
		
		$db->query("update order_tuihuan set if_del = 1 where id = $id");
		
		return '{"code":1,"message":"已经删除"}';
	}
	
	public function qxRefund(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$tuihuanId = (int)$request['id'];
		$jilu = $db->get_row("select comId,genjin_json,status,orderId,inventoryId from order_tuihuan where id=$tuihuanId and userId=$userId");
		$fenbiao = getFenbiao($jilu->comId,20);
		if($jilu->status!=1 && $jilu->status!=2){
			return '{"code":0,"message":"该记录当前状态不能取消售后"}';
		}
		$results = array();
		if(!empty($jilu->genjin_json)){
			$results = json_decode($jilu->genjin_json,true);
		}
		$fankui = array();
		$fankui['name'] = '客户';
		$fankui['time'] = date("Y-m-d H:i:s");
		$fankui['content'] = '客户自行取消申请';
		$results[] = $fankui;
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update order_tuihuan set status=-1,genjin_json='$resultstr',dealTime='".$fankui['time']."',dealUser=0,dealCont='客户自行取消申请' where id=$tuihuanId");
		//$db->query("update order$fenbiao set status=3 where id=$jilu->orderId");
		$db->query("update order_detail$fenbiao set ifshouhou=0 where orderId=$jilu->orderId and inventoryId = $jilu->inventoryId limit 1");
		//创建定时收货任务
		/*if($_SESSION['if_tongbu']==1){
			$comId = 10;
		}*/
		$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
		$shouhuo_time = strtotime("+$shuohuo_day days");
		$timed_task = array();
		$timed_task['comId'] = $jilu->comId;
		$timed_task['dtTime'] = $shouhuo_time;
		$timed_task['router'] = 'order_autoShouhuo';
		$timed_task['params'] = '{"order_id":'.$jilu->orderId.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		return '{"code":1,"message":"操作成功"}';
	}
	
	public function qrShouHuo()
	{
	    global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$tuihuanId = (int)$request['id'];
		$jilu = $db->get_row("select * from order_tuihuan where id=$tuihuanId and userId = $userId");
		if($jilu && $jilu->status == 5){
		    $db->query("update order_tuihuan set status = 6 where id = $tuihuanId");
		    
		    return '{"code":1,"message":"确认收货完成，服务单完成"}';
		}
	    
		return '{"code":0,"message":"该订单当前状态不支持售后确认收货"}';
	}
	
	public function addKuaidi(){
		global $db,$request,$comId;
		global $db,$request;
		$tuihuanId = (int)$request['id'];
		$jilu = $db->get_row("select genjin_json,kuaidi_json,status,type from order_tuihuan where id=$tuihuanId");
		
		if($jilu->status != 2){
		    return '{"code":0,"message":"该订单当前状态不是等待客户发货"}';
		}
		
		if(!in_array($jilu->type, [2, 3])){
		    return '{"code":0,"message":"该订单售后类型不需要客户发货"}';
		}
		
		if(empty($jilu->kuaidi_json)){
			$kuaidi_json['company'] = $request['kuaidi_company'];
			$kuaidi_json['orderId'] = $request['kuaidi_orderId'];
			$results = array();
			if(!empty($jilu->genjin_json)){
				$results = json_decode($jilu->genjin_json,true);
			}
			$date = $time = date('Y-m-d H:i:s');
			$fankui = array();
			$fankui['name'] = '客户';
			$fankui['time'] = $date;
			$fankui['content'] = '已上传快递信息，请注意查收';
			$results[] = $fankui;
			$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
			$kuaidi_str = json_encode($kuaidi_json,JSON_UNESCAPED_UNICODE);
			
			$db->query("update order_tuihuan set genjin_json='$resultstr',kuaidi_json='$kuaidi_str' where id=$tuihuanId");
		}
		return '{"code":1,"message":"操作成功"}';
	}
	
	//可售后列表
	public function afterSaleList()
	{
	    global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		//todo  过期处理
		
		$sql = "select  d.id, d.num, d.pdtInfo,d.unit,d.inventoryId,o.id orderId,o.mendianId,d.ifshouhou,d.unit_price,o.orderId orderNumber,o.dtTime from order_detail$fenbiao d inner join order$fenbiao o  on o.id = d.orderId where o.userId = $userId and (d.ifshouhou = 0 or d.ifshouhou = 2) and (o.status =  4 or  o.status = -3) and o.if_jifen = 0 ";
		
		$count = $db->get_var(str_replace('d.id, d.num, d.pdtInfo,d.unit,d.inventoryId,o.id orderId,o.mendianId,d.ifshouhou,d.unit_price,o.orderId orderNumber,o.dtTime','count(d.id)',$sql));
		$sql.=" order by d.id desc limit ".(($page-1)*$pageNum).",".$pageNum;
		$pdts = $db->get_results($sql);
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$data = [];
		$return['data']['count'] = $count;
		$return['data']['pages'] = ceil($count/$pageNum);
		$now = time();
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$data = array();
				$data['inventoryId'] = $pdt->inventoryId;
			    $data['dtTime'] = $pdt->dtTime;
				$data['order_id'] = $pdt->orderId;
				$address = $db->get_var("select shuohuo_json from order$fenbiao where id = $pdt->orderId");
			    $data['address'] = json_decode($address, true);
				$data['order_no'] = $pdt->orderNumber;
				$data['ifshouhou'] = $pdt->ifshouhou;
				//todo 如果超时过多久就不能发起售后了
				if(!$data['ifshouhou']){
				    $createTime = $db->get_var("select dtTime from order$fenbiao where id = $pdt->orderId");
				    $time_shouhuo = $db->get_var("select time_shouhuo from demo_shezhi where comId = $comId");
				    if($createTime < date('Y-m-d H:i:s', strtotime('-'.$time_shouhuo.' day'))){
				        $data['ifshouhou'] = 2;
				    }
				}
				$product_json = json_decode($pdt->pdtInfo);
				$image = $db->get_var("select image from demo_product_inventory where id=".$pdt->inventoryId);
				$product_json->image = $image;
				$product_json->unit = $pdt->unit;
				$product_json->num = (int)$pdt->num;
				$product_json->unit_price = $pdt->unit_price;
				$data['products'] = $product_json;
				$return['data']['list'][] = $data;
			}
		}
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	//售后原因
	public function reason(){
	    global $db,$request,$comId;
	    $tuihuan_reason = $db->get_var("select tuihuan_reason from demo_shezhi where comId = $comId");  
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据';
		$return['data'] = explode('@_@',$tuihuan_reason);
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
}