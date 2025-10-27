<?php
function index(){}
function shouhou(){}
function view(){}
function add(){
	global $db,$request;
	if($request['tijiao']==1){
		$orderId = (int)$request['orderId'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION['demo_comId'];
		if(!empty($request['comId'])){
			$comId = (int)$request['comId'];
		}
		$fenbiao = getFenbiao($comId,20);
		if($_SESSION['if_tongbu']==1){
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		}
		$order = $db->get_row("select price_payed,product_json,orderId,status,mendianId,storeId,product_json,zhishangId from order$fenbiao where id=$orderId and userId=$userId");
		if(empty($order)||$order->status!=3){
			die('{"code":0,"message":"该订单当前状态不支持售后申请"}');
		}
		$ifhas = $db->get_var("select id from order_tuihuan where orderId=$orderId and comId=$comId and type=1 and status>-1 limit 1");
		if(!empty($ifhas)){
			die('{"code":0,"message":"该订单已经申请过补偿了，不能再次申请售后服务！"}');
		}
		$lujing = 'upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$images = '';
		if(!empty($request['images'])){
			foreach ($request['images'] as $img) {
				list($type,$data) = explode(',',$img);
				if(strstr($type,'image/jpeg')!==''){
					$ext = '.jpg';
				}elseif(strstr($type,'image/gif')!==''){
					$ext = '.gif';
				}elseif(strstr($type,'image/png')!==''){
					$ext = '.png';
				}elseif(strstr($type,'image/bmp')!==''){
					$ext = '.bmp';
				}else{
					$ext = '.png';
				}
				$decodedData = base64_decode(str_replace(' ','+',$data));
				$fileName = $lujing.date("YmdHis").rand(1,9999).$ext;
				file_put_contents($fileName,$decodedData);
				$images .= '|/'.$fileName;
			}
			$images = substr($images,1);
		}
		$nums = 0;
		$pdtIds = '';
		$pdtInfo = array();
		$product_json = json_decode($order->product_json);
		if(!empty($request['nums']) && $request['type']==3){
			foreach ($request['nums'] as $key => $num) {
				$nums+=$num;
				$pdtIds.=','.$key;
				foreach ($product_json as $pro) {
					if($key==$pro->id){
						$pro->num = $num;
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
		if($_SESSION['if_tongbu']==1){
			$tuihuan['zhishangId'] = $order->zhishangId;
		}
		$tuihuan['mendianId'] = $order->mendianId;
		$tuihuan['shopId'] = $order->mendianId;
		$tuihuan['sn'] = date("YmdHis").rand(1000000000,9999999999);
		$tuihuan['order_orderId'] = $order->orderId;
		$tuihuan['type'] = (int)$request['type'];
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
		$tuihuan['images'] = $images;
		$db->insert_update('order_tuihuan',$tuihuan,'id');
		$db->query("update order$fenbiao set status=-3 where id=$orderId");
		$db->query("delete from demo_timed_task where comId=$comId and router='order_autoShouhuo' and params='{\"order_id\":".$orderId."}' limit 1");
		die('{"code":1,"message":"申请成功，请等待商家审核"}');
	}
}
function get_list(){
	global $db,$request;
	$status = (int)$request['status'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$sql="select id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json from order_tuihuan where comId=$comId and userId=$userId";
	if($_SESSION['if_tongbu']==1){
		$sql="select id,orderId,order_orderId,type,money,pdtInfo,dealCont,status,nums,kuaidi_json from order_tuihuan where zhishangId=$userId";
	}
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
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//后台审核订单退款
function order_tuikuan(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	if(empty($_SESSION[TB_PREFIX.'admin_roleId'])){
		die('{"code":0,"message":"登录超时，请重新登录后台操作"}');
	}
	$tuihuanId = (int)$request['tuihuanId'];
	$jilu = $db->get_row("select * from order_tuihuan where id=$tuihuanId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=3){
		echo '{"code":0,"message":"该退换货订单不需要再操作了"}';
		exit;
	}
	$product_json = json_decode($jilu->pdtInfo);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$date = $time = date('Y-m-d H:i:s');
	$orderId = $jilu->orderId;
	$money = $jilu->money;
	$type = $jilu->type;//1只退款 2，退货退款，订单改为无效
	$order = $db->get_row("select * from order$fenbiao where id=$orderId");
	if(!empty($order) && $money>0){
		if($money>($order->price_payed-$order->price_tuikuan)){
			die('{"code":0,"message":"退款金额超出订单金额"}');
		}
		$results = array();
		if(!empty($jilu->genjin_json)){
			$results = json_decode($jilu->genjin_json,true);
		}
		$fankui = array();
		$fankui['name'] = $username;
		$fankui['time'] = $time;
		$newstatus = 6;
		$fankui['content'] = '已退款';
		$results[] = $fankui;
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update order_tuihuan set status=$newstatus,genjin_json='$resultstr',dealTime='$time',dealUser=$userId,dealCont='已退款' where id=$tuihuanId");
		//退货退款，先修改订单为无效，然后扣除快递费用
		if($type==2){
			$db->query("update order$fenbiao set status=-1,remark='订单已退款' where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			if($jilu->kuaidi_type==2 && $jilu->kuaidi_money>0){
				$db->query("update users set money=money+$jilu->kuaidi_money where id=$order->userId");
				$liushui = array();
				$liushui['userId']=$order->userId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$jilu->kuaidi_money;
				$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='退换货运费补偿';
				$liushui['orderInfo']='退换货运费补偿，订单号：'.$order->orderId;
				$liushui['order_id']=$order->id;
				$yzFenbiao = $fenbiao;
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				/*if($order->mendianId==0){
					$fanli_json = json_decode($order->fanli_json);
					$pingtai_shouyi = array();
					$pingtai_shouyi['mendianId'] = $order->shopId;
					$pingtai_shouyi['type'] = 6;
					$pingtai_shouyi['money'] = -$jilu->kuaidi_money;
					$pingtai_shouyi['money_order'] = 0;
					$pingtai_shouyi['money_gonghuo'] = 0;
					$pingtai_shouyi['money_tuanzhang'] = 0;
					$pingtai_shouyi['money_tuijian'] = 0;
					$pingtai_shouyi['dtTime'] = $date;
					$pingtai_shouyi['typeInfo'] = '退货快递费用';
					$pingtai_shouyi['orderId'] = $orderId;
					$pingtai_shouyi['remark'] = '直营商品订单退货退款，快递由平台付';
					$db->insert_update('demo_pingtai_shouyi',$pingtai_shouyi,'id');
				}else{
					$yzFenbiao = getYzFenbiao($order->mendianId,20);
					$db->query("update mendian set baozhengjin=baozhengjin-$jilu->kuaidi_money where id=$order->mendianId and comId=$comId");
					$liushui = array();
					$liushui['mendianId']=$order->mendianId;
					$liushui['comId']=$comId;
					$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
					$liushui['money']=-$jilu->kuaidi_money;
					$liushui['yue']=$db->get_var("select baozhengjin from mendian where id=$order->mendianId");
					$liushui['type']=3;
					$liushui['dtTime']=$date;
					$liushui['remark']='退货快递费用,快递费用由卖家承担,单号：'.$order->orderId;
					$liushui['typeInfo']='订单退货快递费用';
					insert_update('demo_mendian_liushui'.$yzFenbiao,$liushui,'id');
				}*/
			}
		}else{
			$db->query("update order$fenbiao set price_tuikuan=price_tuikuan+$money where id=$orderId");
			//修改为待收货状态
			$db->query("update order$fenbiao set status=3 where id=$orderId");
			$db->query("update order_detail$fenbiao set status=1 where orderId=$orderId limit 1");
			//创建定时收货任务
			$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
			$shouhuo_time = strtotime("+$shuohuo_day days");
			$timed_task = array();
			$timed_task['comId'] = $comId;
			$timed_task['dtTime'] = $shouhuo_time;
			$timed_task['router'] = 'order_autoShouhuo';
			$timed_task['params'] = '{"order_id":'.$orderId.'}';
			$db->insert_update('demo_timed_task',$timed_task,'id');
			//扣除商家的余额 自营的扣除流水
			/*if($order->mendianId==0){
				$fanli_json = json_decode($order->fanli_json);
				$pingtai_shouyi = array();
				$pingtai_shouyi['mendianId'] = $order->shopId;
				$pingtai_shouyi['type'] = 6;
				$pingtai_shouyi['money'] = -$money;
				$pingtai_shouyi['money_order'] = $order->price;
				$pingtai_shouyi['money_gonghuo'] = empty($fanli_json->mendian_fanli)?0:$fanli_json->mendian_fanli;
				$pingtai_shouyi['money_tuanzhang'] = $fanli_json->tuijian_fanli;
				$pingtai_shouyi['money_tuijian'] = $fanli_json->tuijian_fanli;
				$pingtai_shouyi['dtTime'] = $date;
				$pingtai_shouyi['typeInfo'] = '订单补偿';
				$pingtai_shouyi['orderId'] = $orderId;
				$pingtai_shouyi['remark'] = '订单补偿扣款';
				$db->insert_update('demo_pingtai_shouyi',$pingtai_shouyi,'id');
			}else{
				$yzFenbiao = getYzFenbiao($order->mendianId,20);
				$db->query("update mendian set money=money-$money where id=$order->mendianId and comId=$comId");
				$liushui = array();
				$liushui['mendianId']=$order->mendianId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=-$money;
				$liushui['yue']=$db->get_var("select money from mendian where id=$order->mendianId");
				$liushui['type']=3;
				$liushui['dtTime']=$date;
				$liushui['remark']='操作者：'.$username;
				$liushui['typeInfo']='订单补偿扣款';
				insert_update('demo_mendian_liushui'.$yzFenbiao,$liushui,'id');
			}*/
		}
		switch ($order->pay_type){
			case 1:
				$db->query("update users set money=money+$money where id=$order->userId");
				$liushui = array();
				$liushui['userId']=$order->userId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$money;
				$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='订单退款';
				$liushui['orderInfo']='管理员操作订单退款，订单号：'.$order->orderId;
				$liushui['order_id']=$order->id;
				$yzFenbiao = $fenbiao;
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			break;
			case 2:
				//微信退款
				$price = round($money*100);
				$pay_json = json_decode($order->pay_json,true);
				require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
				require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
				$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
				$log = Log::Init($logHandler, 15);
				$transaction_id = $pay_json['weixin']['wx_orderId'][0];
				$total_fee = $order->price*100;
				$refund_fee = $price;
				$input = new WxPayRefund();
				$input->SetTransaction_id($transaction_id);
				$input->SetTotal_fee($total_fee);
				$input->SetRefund_fee($refund_fee);
				$input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
				$input->SetOp_user_id(WxPayConfig::MCHID);
				$result = WxPayApi::refund($input);
				if($result['result_code'] != "SUCCESS"){
					file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
				}
			break;
		}
	}
	die('{"code":1,"message":"操作成功"}');
}
function add_kuaidi(){
	global $db,$request;
	$tuihuanId = (int)$request['shouhou_id'];
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
	die('{"code":1,"message":"操作成功"}');
}
function quxiao(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$tuihuanId = (int)$request['shouhou_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$jilu = $db->get_row("select comId,genjin_json,status,orderId from order_tuihuan where id=$tuihuanId");
	$fenbiao = getFenbiao($jilu->comId,20);
	if($jilu->status!=1 && $jilu->status!=2){
		die('{"code":1,"message":"该记录当前状态不能取消售后"}');
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
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
	}
	$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
	$shouhuo_time = strtotime("+$shuohuo_day days");
	$timed_task = array();
	$timed_task['comId'] = $jilu->comId;
	$timed_task['dtTime'] = $shouhuo_time;
	$timed_task['router'] = 'order_autoShouhuo';
	$timed_task['params'] = '{"order_id":'.$jilu->orderId.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
	die('{"code":1,"message":"操作成功"}');
}