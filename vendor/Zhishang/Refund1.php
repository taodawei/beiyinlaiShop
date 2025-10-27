<?php
namespace Zhishang;
class Refund{
	public function lists(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$status = (int)$request['status'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$sql="select id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json from order_tuihuan where comId=$comId and userId=$userId";
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
		$count = $db->get_var(str_replace('id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json','count(*)',$sql));
		$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
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
				$data['sn'] = $pdt->order_orderId;
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
						$data['type_info'] = '换货';
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
		    			$data['status_info'] = '已驳回';
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
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function add(){
		global $db,$request,$comId;
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$type = (int)$request['type'];
		$reason = $request['reason'];
		$remark = $request['remark'];
		$product_info = json_decode($request['product_info']);
		$order = $db->get_row("select price_payed,product_json,orderId,status,mendianId,storeId,product_json,zhishangId from order$fenbiao where id=$orderId and userId=$userId");
		if(empty($order)||$order->status!=3){
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
		$pdtInfo = array();
		$product_json = json_decode($order->product_json);
		if(!empty($product_info) && $request['type']==3){
			foreach ($product_info as $item) {
				$nums+=$item->num;
				$pdtIds.=','.$item->inventoryId;
				foreach ($product_json as $pro) {
					if($item->inventoryId==$pro->id){
						$pro->num = $item->num;
						$pdtInfo[] = $pro;
					}
				}
			}
			$pdtIds = substr($pdtIds,1);
		}else{
			foreach ($product_json as $key => $pro) {
				$nums+=$pro->num;
				$pdtIds.=','.$pro->id;
				$pdtInfo[] = $pro;
			}
			$pdtIds = substr($pdtIds,1);
		}
		$tuihuan = array();
		$tuihuan['orderId'] = $orderId;
		$tuihuan['comId'] = $comId;
		$tuihuan['userId'] = $userId;
		$tuihuan['mendianId'] = $order->mendianId;
		$tuihuan['shopId'] = $order->mendianId;
		$tuihuan['sn'] = date("YmdHis").rand(1000000000,9999999999);
		$tuihuan['order_orderId'] = $order->orderId;
		$tuihuan['type'] = $type;
		$tuihuan['liuchengId'] = 3;
		$tuihuan['dtTime'] = date("Y-m-d H:i:s");
		$tuihuan['money'] = $tuihuan['type']==3?0:$request['money'];
		$tuihuan['pdtIds'] = $pdtIds;
		$tuihuan['pdtInfo'] = json_encode($pdtInfo,JSON_UNESCAPED_UNICODE);
		$tuihuan['nums'] = $nums;
		$tuihuan['reason'] = $request['reason'];
		$tuihuan['kuaidi_type'] = (int)$request['kuaidi_type'];
		$tuihuan['kuaidi_money'] = empty($request['kuaidi_money'])?0:$request['kuaidi_money'];
		$tuihuan['remark'] = $request['remark'];
		$tuihuan['storeId'] = $order->storeId;
		$tuihuan['images'] = $imgs;
		$db->insert_update('order_tuihuan',$tuihuan,'id');
		$db->query("update order$fenbiao set status=-3 where id=$orderId");
		$db->query("delete from demo_timed_task where comId=$comId and router='order_autoShouhuo' and params='{\"order_id\":".$orderId."}' limit 1");
		return '{"code":1,"message":"申请成功，请等待商家审核"}';
	}
	public function detail(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$id = (int)$request['id'];
		$order = $db->get_row("select * from order_tuihuan where id=$id and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"退换货不存在，请检查id参数"}';
		}
		$product_json = json_decode($order->pdtInfo);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['id'] = $order->id;
		$return['data']['sn'] = $order->sn;
		$return['data']['type'] = $order->type;
		$return['data']['product_info'] = $product_json;
		$return['data']['dtTime'] = $order->dtTime;
		$return['data']['dealCont'] = $order->dealCont;
		$return['data']['money'] = $order->money;
		$return['data']['reason'] = $order->reason;
		$return['data']['remark'] = $order->remark;
		$return['data']['status'] = $order->status;
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
    			$return['data']['status_info'] = '已驳回';
    		break;
		}
		return json_encode($return);
	}
	public function qxRefund(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$tuihuanId = (int)$request['id'];
		$jilu = $db->get_row("select comId,genjin_json,status,orderId from order_tuihuan where id=$tuihuanId and userId=$userId");
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
		$db->query("update order$fenbiao set status=3 where id=$jilu->orderId");
		$db->query("update order_detail$fenbiao set status=1 where orderId=$jilu->orderId limit 1");
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
	public function addKuaidi(){
		global $db,$request,$comId;
		global $db,$request;
		$tuihuanId = (int)$request['id'];
		$jilu = $db->get_row("select genjin_json,kuaidi_json from order_tuihuan where id=$tuihuanId");
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
}