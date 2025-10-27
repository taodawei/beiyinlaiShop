<?php
global $request;
$no_login = array('login','wancheng_tuihuan','qx_fahuo','houtai_hexiao','houtai_diancan_wancheng');
if( !in_array($request['a'], $no_login) && empty($_SESSION[TB_PREFIX.'user_ID'])){
	redirect('/index.php?p=8&a=login');
}
function index(){}
function tuan(){}
function alone(){}
function shequ_order(){}
function view(){}
function view_tuan(){}
function opratejilu(){}
function mytuan(){}
function queren(){}
function getwlinfo(){}
function pay(){}
function pay_zong(){}
function getlipinka(){}
function view_diancan(){}
function jiacan(){
	global $db,$request;
	$id = (int)$request['id'];
	$type = (int)$request['type'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select * from order$fenbiao where id=$id and status=-5");
	if(empty($order)){
		die("<script>alert('该订单已不支持加餐操作');history.go(-1);</script>");
	}
	$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=".$order->shequ_id);
	$canzhuo = $db->get_row("select * from demo_shequ_table where id=$order->table_id");
	$_SESSION[TB_PREFIX.'shequ_id'] = $order->shequ_id;
	$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
	$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
	$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
	$_SESSION[TB_PREFIX.'table_id'] = $canzhuo->id;
	$_SESSION[TB_PREFIX.'table_title'] = $canzhuo->title;
	$_SESSION[TB_PREFIX.'jiacan_id'] = $id;
	$_SESSION[TB_PREFIX.'jiacan_type'] = $type;
	redirect('/index.php?p=4&a=channels&peisong_type=4');
}
function getlipinka_verify(){
	global $db,$request;
	$orderId = (int)$request['orderId'];
	$tiqu_type = (int)$request['tiqu_type'];
	$pay_pass = $request['pay_pass'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($tiqu_type==1){
		$db_service = getCrmDb();
		$payPass = $db_service->get_var("select payPass from demo_user where id=$userId");
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new shlEncryption($pay_pass);
		if($payPass!=$shlencryption->to_string()){
			die('{"code":0,"message":"支付密码不正确！"}');
		}
	}else{
		if($pay_pass!=$_SESSION['yzm'] || empty($pay_pass)){
			die('{"code":0,"message":"验证码不正确！"}');
		}
	}
	$_SESSION['get_lipinka'] = $orderId;
	$if_order = $db->get_var("select id from lipinka where orderId=$orderId limit 1");
	if(empty($if_order)){
		$details = $db->get_results("select num,lipinkaId from order_detail9 where orderId=$orderId and status=1");
		if(!empty($details)){
			foreach ($details as $detail){
				$db->query("update lipinka set orderId=$orderId where jiluId=$detail->lipinkaId and userId=0 and orderId=0 order by id asc limit ".(int)$detail->num);
			}
		}
	}
	die('{"code":1}');
}
function bind_lipinka(){
	global $db,$request;
	$id = (int)$request['id'];
	$orderId = (int)$request['orderId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$db->query("update lipinka set userId=$userId,bind_time='".date("Y-m-d H:i:s")."' where id=$id and userId=0 and orderId=$orderId");
	die('{"code":1}');
}
function bind_lipinka_all(){
	global $db,$request;
	$id = (int)$request['id'];
	$orderId = (int)$request['orderId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$db->query("update lipinka set userId=$userId,bind_time='".date("Y-m-d H:i:s")."' where userId=0 and orderId=(select id from order9 where id=$orderId and userId=$userId)");
	die('{"code":1}');
}
function pingjia(){
	global $db,$request;
	if($request['tijiao']==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = empty($request['comId'])?(int)$_SESSION['demo_comId']:(int)$request['comId'];
		$fenbiao = getFenbiao($comId,20);
		$orderId = (int)$request['orderId'];
		$inventoryId = (int)$request['inventoryId'];
		$star = (int)$request['star'];
		$star1 =(int)$request['star1'];
		$star2 =(int)$request['star2'];
		$imgs = $request['imgs'];
		$content = $request['content'];
		/*$order = $db->get_row("select status,ifpingjia,orderId,storeId,mendianId from order$fenbiao where id=$orderId and userId=$userId");
		if($order->status!=4 || $order->ifpingjia==1){
			die('{"code":0,"message":"您已经评价过该订单了！"}');
		}*/
		$db->query("update order_detail$fenbiao set ifpingjia=1 where orderId=$orderId and inventoryId=$inventoryId limit 1");
		$ifhas = $db->get_var("select id from order_detail$fenbiao where orderId=$orderId and ifpingjia=0 limit 1");
		if(empty($ifhas)){
			$db->query("update order$fenbiao set ifpingjia=1 where id=$orderId");
		}
		if($_SESSION['if_tongbu']==1){
			$userId = $_SESSION[TB_PREFIX.'zhishangId'];
			$db_service = getCrmDb();
			$u = $db_service->get_row("select name as nickname from demo_user where id=$userId");
		}else{
			$u = $db->get_row("select nickname from users where id=$userId");
		}
		$p = $db->get_row("select productId,title from demo_product_inventory where id=$inventoryId");
		$comment = array();
		$comment['orderId'] = $orderId;
		$comment['pdtId'] = $p->productId;
		$comment['inventoryId'] = $inventoryId;
		$comment['comId'] = $comId;
		$comment['userId'] = $userId;
		//$comment['mendianId'] = $order->mendianId;
		$comment['name'] = $u->nickname;
		$comment['order_orderId'] = $order->orderId;
		$comment['pdtName'] = $p->title;
		$comment['star'] = $star;
		$comment['star1'] = $star1;
		$comment['star2'] = $star2;
		$comment['cont1'] = $content;
		$comment['images1'] = $imgs;
		$comment['dtTime1'] = date('Y-m-d H:i:s');
		//$comment['storeId'] = $order->storeId;
		$db->insert_update('order_comment'.$fenbiao,$comment,'id');
		die('{"code":1,"message":"评价成功"}');
	}
}
function get_order_list(){
	global $db,$request;
	$scene = (int)$request['scene'];
	$type = $request['type'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	if($comId==10){
		$sql = "select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order0 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order1 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order2 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order3 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order4 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order5 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order6 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order7 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order8 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order9 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order10 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order11 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order12 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order13 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order14 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order15 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order16 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order17 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order18 where zhishangId=$userId and is_del=0 and 1=1 union all
				select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id from order19 where zhishangId=$userId and is_del=0 and 1=1 
		";
		if(!empty($scene)){
			switch($scene){
				case 1:
					$sql = str_replace('1=1',"1=1 and status=-5 and pay_endtime>'$now'", $sql);
				break;
				case 2:
					$sql = str_replace('1=1',"1=1 and status=2", $sql);
				break;
				case 3:
					$sql = str_replace('1=1',"1=1 and status=3", $sql);
				break;
				case 4:
					$sql = str_replace('1=1',"1=1 and status=4 and ifpingjia=0", $sql);
				break;
				case 5:
					$sql = str_replace('1=1',"1=1 and status=4 and ifpingjia=1", $sql);
				break;
			}
		}
		if(!empty($keyword)){
			$sql = str_replace('1=1',"1=1 and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')", $sql);
		}
		$countsql = str_replace('id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id','count(*) as num',$sql);
		$counts = $db->get_results($countsql);
		$count = 0;
		if(!empty($counts)){
			foreach ($counts as $c) {
				$count+=(int)$c->num;
			}
		}
	}else{
		$fenbiao = getFenbiao($comId,20);
		$sql="select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type from order$fenbiao where comId=$comId and userId=$userId and is_del=0 ";
		if($_SESSION['if_tongbu']==1){
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
			$sql="select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type from order$fenbiao where comId=$comId and zhishangId=$userId and is_del=0 ";
		}
		if(!empty($scene)){
			switch($scene){
				case 1:
					$sql.=" and status=-5 and pay_endtime>'$now'";
				break;
				case 2:
					$sql.=" and status=2";
				break;
				case 3:
					$sql.=" and status=3";
				break;
				case 4:
					$sql.=" and status=4 and ifpingjia=0";
				break;
				case 5:
					$sql.=" and status=4 and ifpingjia=1";
				break;
				case 6:
					$sql.=" and status=-1 and price_payed>0";
				break;
			}
		}
		if(!empty($keyword)){
			$sql.=" and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')";
		}
		//file_put_contents('request.txt',$sql);
		$count = $db->get_var(str_replace('id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type','count(*)',$sql));
	}
	
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
			$data['tuan_id'] = $pdt->tuan_id;
			$data['orderId'] = $pdt->orderId;
			switch ($pdt->status) {
				case 0:
					$data['statusInfo'] = '<span style="color:#cf2950;">待成团</span>';
				break;
				case 2:
					$data['statusInfo'] = '<span style="color:#cf2950;">待发货</span>';
				break;
				case 3:
					$data['statusInfo'] = '<span style="color:#cf2950;">待收货</span>';
				break;
				case 4:
					$data['statusInfo'] = '<span style="color:green;">已完成</span>';
				break;
				case -3:
					$data['statusInfo'] = '<span style="color:#f00;">退换货</span>';
				break;
				case -5:
					$pay_end = strtotime($pdt->pay_endtime);
					if($pay_end>$now){
						$data['statusInfo'] = '<span style="color:#cf2950;">待支付</span>';
					}else{
						$data['statusInfo'] = '<span>无效</span>';
					}
				break;
				case -1:
					$data['statusInfo'] = '<span>无效</span>';
					$qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
					if(in_array($pdt->remark,$qx_remarks)){
						$data['statusInfo'] = '<span>'.$pdt->remark.'</span>';
					}
				break;
			}
			$product_json = json_decode($pdt->product_json);
			$data['products'] = $product_json;
			$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$data['endTime'] = strtotime($pdt->pay_endtime)*1000;
			$data['jishiqi'] = 0;
			if($data['statusInfo']=='<span style="color:#cf2950;">待支付</span>' && $pdt->yushouId==0){
				$data['jishiqi'] = 1;
			}
			//$data['jishiqi'] = $data['statusInfo']=='<span style="color:#cf2950;">待支付</span>'?1:0;
			$data['price'] = $pdt->price;
			$data['price_payed'] = $pdt->price_payed;
			$data['num'] = $pdt->pdtNums;
			$data['comId'] = $pdt->comId;
			$data['status'] = $pdt->status;
			$data['peisong_type'] = $pdt->peisong_type;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_earnorder_list(){
	global $db,$request;
	$scene = (int)$request['scene'];
	$type = $request['type'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$sql="select order_type,orderId,money,dtTime,status,order_comId from user_yugu_shouru where comId=$comId and userId=$userId";
	switch ($scene) {
		case 1:
			$sql.=" and status=0";
		break;
		case 2:
			$sql.=" and status=1";
		break;
		case 3:
			$sql.=" and status=-1";
		break;
	}
	if(!empty($keyword)){
		if($comId==10){
			$db_service = getCrmDb();
			$userIds = $db_service->get_var("select group_concat(id) from demo_user where username='$keyword' or name='$keyword'");
		}else{
			$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and username='$keyword' or nickname='$keyword'");
		}
		if(empty($userIds))$userIds = '0';
		$sql.=" and (order_orderId='$keyword' or from_user in($userIds)) ";
	}
	$count = $db->get_var(str_replace('order_type,orderId,money,dtTime,status,order_comId','count(*)',$sql));
	$sql.=" order by id desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
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
			if($pdt->order_type==1){
				$fenbiao = getFenbiao($pdt->order_comId,20);
				$order = $db->get_row("select product_json,orderId from order$fenbiao where id=$pdt->orderId");
			}else{
				$order = $db->get_row("select product_json,orderId from demo_pdt_order where id=$pdt->orderId");
			}			
			$data['id'] = $pdt->orderId;
			$data['orderId'] = $order->orderId;
			switch ($pdt->status) {
				case 0:
					$data['statusInfo'] = '已付款';
				break;
				case 1:
					$data['statusInfo'] = '已结算';
				break;
				case -1:
					$data['statusInfo'] = '无效';
				break;
			}
			$product_json = json_decode($order->product_json);
			$data['products'] = $product_json;
			$data['dtTime'] = date("Y-m-d",strtotime($pdt->dtTime));
			$data['status'] = $pdt->status;
			$data['yongjin'] = $pdt->money;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//获取社区订单
function get_shequ_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$shequ_id = (int)$request['shequ_id'];
	$keyword = $request['keyword'];
	$scene = (int)$request['scene'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	$fenbiao = getFenbiao($comId,20);
	$sql="select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type,shuohuo_json,peisong_time from order$fenbiao where comId=$comId and shequ_id=$shequ_id";
	if(!empty($scene)){
		switch($scene){
			case 1:
				$sql.=" and status in(2,3) and peisong_type=2";
			break;
			case 2:
				$sql.=" and status in(2,3) and peisong_type=1";
			break;
			case 3:
				$sql.=" and status=4";
			break;
		}
	}
	if(!empty($keyword)){
		$sql.=" and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')";
	}
	//file_put_contents('request.txt',$sql);
	$count = $db->get_var(str_replace('id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type,shuohuo_json,peisong_time','count(*)',$sql));
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
					$data['statusInfo'] = '<span style="color:#cf2950;">待成团</span>';
				break;
				case 2:
					$data['statusInfo'] = '<span style="color:#cf2950;">待发货</span>';
				break;
				case 3:
					$data['statusInfo'] = '<span style="color:#cf2950;">待收货</span>';
				break;
				case 4:
					$data['statusInfo'] = '<span style="color:green;">已完成</span>';
				break;
				case -3:
					$data['statusInfo'] = '<span style="color:#f00;">退换货</span>';
				break;
				case -5:
					$pay_end = strtotime($pdt->pay_endtime);
					if($pay_end>$now){
						$data['statusInfo'] = '<span style="color:#cf2950;">待支付</span>';
					}else{
						$data['statusInfo'] = '<span>无效</span>';
					}
				break;
				case -1:
					$data['statusInfo'] = '<span>无效</span>';
					$qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
					if(in_array($pdt->remark,$qx_remarks)){
						$data['statusInfo'] = '<span>'.$pdt->remark.'</span>';
					}
				break;
			}
			$product_json = json_decode($pdt->product_json);
			$shouhuo_json = json_decode($pdt->shuohuo_json,true);
			$data['products'] = $product_json;
			$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$data['endTime'] = strtotime($pdt->pay_endtime)*1000;
			$data['jishiqi'] = 0;
			//$data['jishiqi'] = $data['statusInfo']=='<span style="color:#cf2950;">待支付</span>'?1:0;
			$data['price'] = $pdt->price;
			$data['price_payed'] = $pdt->price_payed;
			$data['num'] = $pdt->pdtNums;
			$data['comId'] = $pdt->comId;
			$data['status'] = $pdt->status;
			$data['peisong_type'] = $pdt->peisong_type;
			$data['peisong_time'] = $pdt->peisong_time;
			$data['address'] = $shouhuo_json['详细地址'];
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function change_address(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$addressId = (int)$request['addressId'];
	$order_id = (int)$request['order_id'];
	$address = $db->get_row("select * from user_address where id=$addressId");
	$shouhuo_json = array();
	$shouhuo_json['收件人'] = $address->name;
	$shouhuo_json['手机号'] = $address->phone;
	$shouhuo_json['所在地区'] = $address->areaName;
	$shouhuo_json['详细地址'] = '【'.$address->title.'】'.$address->address;
	$shuohuo_json = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
	$db->query("update order0 set address_id=$addressId,shuohuo_json='$shuohuo_json' where id=$order_id and userId=$userId");
	die('{"code":1}');
}
//获取会员中心首页订单数量
function get_order_num(){
	global $db,$request;
	$index = (int)$request['index'];
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$return = array();
	$return['code'] = 1;
	$return['data'] = array();
	$now = date('Y-m-d H:i:s');
	$fenbiao = getFenbiao($comId,20);
	$num8 = 0;
	if($comId==10){
		$num1 = 0;$num2 = 0;$num3 = 0;$num4 = 0;$num5 = 0;
		for ($i=0; $i <20; $i++) {
			$num1 += (int)$db->get_var("select count(*) from order$i where zhishangId=$userId and status=-5 and pay_endtime>'$now'");
			$num2 += (int)$db->get_var("select count(*) from order$i where zhishangId=$userId and status=2");
			$num3 += (int)$db->get_var("select count(*) from order$i where zhishangId=$userId and status=3");
			$num4 += (int)$db->get_var("select count(*) from order$i where zhishangId=$userId and status=3 and status=4 and ifpingjia=0");
		}
		$num5 += (int)$db->get_var("select count(*) from order_tuihuan where zhishangId=$userId and status>-1 and status<6");
		$num6 = (int)$db->get_var("select count(*) from  user_yhq$fenbiao where comId=10 and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
		$num8 = $db->get_var("select sum(yue) from gift_card$fenbiao where comId=10 and userId=$userId and (endTime>'".date("Y-m-d H:i:s")."' or endTime is null)");
	}else if($_SESSION['if_tongbu']==1){
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		//待支付
		$num1 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and zhishangId=$userId and status=-5 and pay_endtime>'$now'");
		//待发货
		$num2 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and zhishangId=$userId and status=2");
		//待收货
		$num3 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and zhishangId=$userId and status=3");
		//待评价
		$num4 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and zhishangId=$userId and status=3 and status=4 and ifpingjia=0");
		//售后
		$num5 = (int)$db->get_var("select count(*) from order_tuihuan where comId=$comId and zhishangId=$userId and status>-1 and status<6");
		//优惠券数量
		$fenbiao = getFenbiao(10,20);
		$num6 = (int)$db->get_var("select count(*) from  user_yhq$fenbiao where comId=10 and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
		$num8 = $db->get_var("select sum(yue) from gift_card$fenbiao where comId=10 and userId=$userId and (endTime>'".date("Y-m-d H:i:s")."' or endTime is null)");
	}else{
		//待支付
		$num1 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=-5 and pay_endtime>'$now'");
		//待发货
		$num2 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=2");
		//待收货
		$num3 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=3");
		//待评价
		$num4 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=3 and status=4 and ifpingjia=0");
		//售后
		$num5 = (int)$db->get_var("select count(*) from order_tuihuan where comId=$comId and userId=$userId and status>-1 and status<6");
		//优惠券数量
		$num6 = (int)$db->get_var("select count(*) from  user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
	}
	
	//未读消息
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$myMsgId = (int)$db->get_var("select msgId from user_msg_read where userId=$userId and comId=$comId");
	$num7 = (int)$db->get_var("select count(*) from user_msg$fenbiao where id>$myMsgId and comId=$comId and userId=$userId");
	$num9 = $db->get_var("select sum(yue) from lipinka where comId=10 and userId=$userId and (endTime>'".date("Y-m-d H:i:s")."' or endTime is null)");
	$return['data'][] = $num1;
	$return['data'][] = $num2;
	$return['data'][] = $num3;
	$return['data'][] = $num4;
	$return['data'][] = $num5;
	$return['data'][] = $num6;
	$return['data'][] = $num7;
	$return['data'][] = empty($num8)?0:$num8;
	$return['data'][] = empty($num9)?0:$num9;
	//$return['data'] = array(1,2,3,4);
	echo json_encode($return);
	exit;
}
//余额支付订单
function yue_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$order_comId = (int)$request['comId'];
	if(empty($order_comId))$order_comId = $comId;
	/*if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}*/
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($order_comId,20);
	$zhifumm = $request['zhifumm'];
	if($comId==10||$comId==1009){
		$db_service = getCrmDb();
		$u = $db_service->get_row("select payPass,money from demo_user where id=$userId");
	}else{
		$u = $db->get_row("select payPass,money from users where id=$userId");
	}
	if($_SESSION['if_tongbu']==1){
		if(empty($db_service))$db_service = getCrmDb();
		$u->payPass = $db_service->get_var("select payPass from demo_user where id=".$_SESSION[TB_PREFIX.'zhishangId']);
	}
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$shlencryption = new shlEncryption($zhifumm);
	if($u->payPass!=$shlencryption->to_string()){
		die('{"code":0,"message":"支付密码不正确"}');
	}
	$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
	if($order->price_dingjin==0){
		$if_shop_fanli = $comId==10?1:0;
		order_jisuan_fanli($order,$if_shop_fanli);
	}
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $order->price = $order->price_dingjin-$order->price_payed;
	}
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->money<$order->price){
		die('{"code":0,"message":"余额不足！请选择其他支付方式"}');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('{"code":0,"message":"该订单已超过支付时间"}');
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	//$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	if($comId==10 || $comId==1009){
		$db_service->query("update demo_user set money=money-$order->price where id=$userId");
	}else{
		$db->query("update users set money=money-$order->price where id=$userId");
	}
	$liushui = array();
	$liushui['userId']=$userId;
	$liushui['comId']=$comId;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=-$order->price;
	$liushui['yue']=$u->money-$order->price;
	$liushui['type']=1;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
	$liushui['order_id']=$orderId;
	insert_update('user_liushui'.($comId==1009?'10':$fenbiao),$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	if($order->price_dingjin==0){
		$o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
		$o['ispay'] = 1;
		$o['pay_type'] = 1;
	}
	$o['price_payed'] = $order->price+$order->price_payed;
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	if($order->price_dingjin==0){
		$pay_json['yue']['price'] = $order->price;
		$pay_json['yue']['if_zong'] = $comId==10?1:0;//是否是总平台的余额,退款时要按这个字段来退款
	}else{
		$pay_json['dingjin']['price'] = $order->price;
		$pay_json['dingjin']['paytype'] = '余额';
	}
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($comId==1009 && $order->lipinkaType==2){
		$o['status'] = 4;
	}
	$db->insert_update('order'.$order_fenbiao,$o,'id');
	if($order->price_dingjin==0){
		//调用支付完成、生成发货单或者更改拼团状态
		$order->price+=$order->price_payed;
		order_pay_done($order);
	}else{
		$yushou = $db->get_row("select * from yushou where id=$order->yushouId");
		$tixing_time = strtotime($yushou->startTime1);
		$pay_endtime = strtotime($yushou->endTime1);
		$db->query("update order$order_fenbiao set pay_endtime='$yushou->endTime1',price_dingjin=0 where id=$orderId");
		$db->query("delete from demo_timed_task where comId=$order_comId and params='{\"order_id\":".$orderId."}' and router='order_checkPay' limit 1");
		$timed_task = array();
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $pay_endtime;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$orderId.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $tixing_time;
		$timed_task['router'] = 'order_payTixing';
		$timed_task['params'] = '{"order_id":'.$orderId.',"user_id":"'.$order->userId.'"}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
	}
	die('{"code":1,"message":"支付成功","buy_type":'.$order->type.'}');
}
function yue_pay_zong(){
	global $db,$request;
	$db_service = getCrmDb();
	$payId = (int)$request['pay_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$zhifumm = $request['zhifumm'];
	$u = $db_service->get_row("select payPass,money from demo_user where id=$userId");
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$shlencryption = new shlEncryption($zhifumm);
	if($u->payPass!=$shlencryption->to_string()){
		die('{"code":0,"message":"支付密码不正确"}');
	}
	$order_pay = $db->get_row("select * from order_pay where id=$payId");
	$order_price = $order_pay->price-$order_pay->price_payed;
	if(empty($order_pay)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order_pay->ispay!=0){
		die('{"code":0,"message":"订单不是待支付状态"}');
	}
	if($u->money<$order_price){
		die('{"code":0,"message":"余额不足！请选择其他支付方式"}');
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$db_service->query("update demo_user set money=money-$order_price where id=$userId");
	$liushui = array();
	$liushui['userId']=$userId;
	$liushui['comId']=$comId;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=-$order_price;
	$liushui['yue']=$u->money-$order_price;
	$liushui['type']=1;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order_pay->orderId;
	$liushui['order_id']=$payId;
	insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $payId;
	$o['price_payed'] = $order_price+$order_pay->price_payed;
	$pay_json = array();
	if(!empty($order_pay->pay_json)){
		$pay_json = json_decode($order_pay->pay_json,true);
	}
	$pay_json['yue']['price'] = $order_price;
	$pay_json['yue']['desc'] = '';
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
			order_jisuan_fanli($order,1);
			$o = array();
			$o['id'] = $ord['orderId'];
			$o['price_payed'] = $order->price;
			$pay_json = array();
			if(!empty($order->pay_json)){
				$pay_json = json_decode($order->pay_json,true);
			}
			$pay_json['yue']['price'] = $order->price-$order->price_payed;
			$pay_json['yue']['if_zong'] = 1;
			$pay_json['yue']['desc'] = '';
			$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
			$o['status'] = empty($order->tuan_id)?2:0;;//普通订单要设置为待发货状态，并且添加发货单
			$o['ispay'] = 1;
			$o['pay_type'] = 1;
			$db->insert_update('order'.$order_fenbiao,$o,'id');
			order_pay_done($order);
		}
	}
	die('{"code":1,"message":"支付成功"}');
}
function jifen_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$jifen = $request['jifen'];
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$u = $db->get_row("select jifen from users where id=$userId");
	$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
	if(empty($order)){
		echo "select * from order$fenbiao where id=$orderId and userId=$userId";
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->jifen<$jifen){
		die('{"code":0,"message":"积分不足！请刷新重试"}');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('{"code":0,"message":"该订单已超过支付时间"}');
	}
	$jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
	if($jifen_pay->if_jifen_pay!=1){
		die('{"code":0,"message":"积分抵现功能已关闭"}');
	}
	$jifen_rule = json_decode($jifen_pay->jifen_pay_rule);
	$money = (int)($jifen*100/$jifen_rule->jifen)/100;
	$daizhifu = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $daizhifu = $order->price_dingjin-$order->price_payed;
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	$db->query("update users set jifen=jifen-$jifen where id=$userId");
	$jifen_jilu = array();
	$jifen_jilu['userId'] = $userId;
	$jifen_jilu['comId'] = $order->comId;
	$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$jifen_jilu['jifen'] = -$jifen;
	$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
	$jifen_jilu['type'] = 2;
	$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
	$jifen_jilu['remark'] = '订单支付，订单号：'.$order->orderId;
	//$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
	$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	if($money==$daizhifu && $order->price_dingjin==0){
		$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
		$o['ispay'] = 1;
		$o['pay_type'] = 1;
	}
	$o['price_payed'] = $order->price_payed + $money;
	$pay_json = array();
	$pay_json['jifen']['price'] = $money;
	$pay_json['jifen']['desc'] = $jifen;
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	$db->insert_update('order'.$fenbiao,$o,'id');
	if($money==$daizhifu){
		if($order->price_dingjin==0){//常规订单
			$db->query("update order_detail$fenbiao set status=1 where orderId=$orderId");
			$product_json = json_decode($order->product_json);
			$product_title = '';
			foreach ($product_json as $pdt){
				$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
			}
			if(!empty($product_title)){
				$product_title = substr($product_title,1);
			}
			$fahuo = array();
			$fahuo['comId'] = $comId;
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
			$fahuo['shequ_id'] = (int)$order->shequ_id;
			$db->insert_update('order_fahuo'.$fenbiao,$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update order$fenbiao set fahuoId=$fahuoId where id=$orderId");
			$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
			foreach ($details as $detail){
				$detail->num = (int)$detail->num;
				$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
				$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
			}
		}else{//支付定金
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
		addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order->comId);
		print_order($order);
		die('{"code":2,"message":"支付成功","buy_type":'.$order->type.'}');
	}else{
		die('{"code":1,"message":"支付成功","buy_type":'.$order->type.'}');
	}
}
function card_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$order_comId = (int)$request['comId'];
	if(empty($order_comId))$order_comId = $comId;
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($order_comId,20);
	$giftId = (int)$request['cardId'];
	$u = $db->get_row("select cardId,password,yue from gift_card$fenbiao where id=$giftId and userId=$userId");
	if(empty($u)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}
	$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
	$money = $request['money'];
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $order->price = $order->price_dingjin-$order->price_payed;
	}
	if($money>$order->price || $money>$u->yue){
		die('{"code":0,"message":"系统错误,请刷新重试"}');
	}
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('{"code":0,"message":"该订单已超过支付时间"}');
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	//$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$db->query("update gift_card$fenbiao set yue=yue-$money where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
	$liushui['orderId']=$orderId;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	$o['price_payed'] = $money+$order->price_payed;
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	$pay_json['lipinka']['price'] = $money;
	$pay_json['lipinka']['desc'] = $u->cardId;
	$pay_json['lipinka']['cardId'] = $giftId;
	/*if($order->price_dingjin==0){
		
	}else{
		$pay_json['dingjin']['price'] = $order->price;
		$pay_json['dingjin']['paytype'] = '礼品卡，卡号：'.$u->cardId;
	}*/
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($money==$order->price){
		if($order->price_dingjin==0){
			$o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
			$o['ispay'] = 1;
			$o['pay_type'] = 4;
		}
	}
	if($u->daili_id>0){
		$fanli_json = json_decode($order->fanli_json);
		$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli;
		$bili = $db->get_var("select daili_bili from demo_shezhi where comId=10");
		$daili_fanli = intval($zong_fanli*$bili)/100;
		$fanli_json->daili_id = $u->daili_id;
		$fanli_json->daili_fanli = $daili_fanli;
		$fanli_json->pingtai_fanli = $fanli_json->pingtai_fanli-$daili_fanli;
		$o['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
	}
	$db->insert_update('order'.$order_fenbiao,$o,'id');

	if($money==$order->price && $order->price_dingjin==0){
		order_pay_done($order);
		die('{"code":2,"message":"支付成功","buy_type":'.$order->type.'}');
	}else if($money==$order->price && $order->price_dingjin>0){
		$yushou = $db->get_row("select * from yushou where id=$order->yushouId");
		$tixing_time = strtotime($yushou->startTime1);
		$pay_endtime = strtotime($yushou->endTime1);
		$db->query("update order$order_fenbiao set pay_endtime='$yushou->endTime1',price_dingjin=0 where id=$orderId");
		$db->query("delete from demo_timed_task where comId=$comId and params='{\"order_id\":".$orderId."}' and router='order_checkPay' limit 1");
		$timed_task = array();
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $pay_endtime;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$orderId.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $tixing_time;
		$timed_task['router'] = 'order_payTixing';
		$timed_task['params'] = '{"order_id":'.$orderId.',"user_id":"'.$order->userId.'"}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		die('{"code":2,"message":"支付成功","buy_type":'.$order->type.'}');
	}
	die('{"code":1,"message":"支付成功","buy_type":'.$order->type.'}');
}
function card_pay_zong(){
	global $db,$request;
	$payId = (int)$request['pay_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$giftId = (int)$request['cardId'];
	$u = $db->get_row("select * from gift_card$fenbiao where id=$giftId and userId=$userId");
	if(empty($u)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}
	$order_pay = $db->get_row("select * from order_pay where id=$payId");
	$money = $request['money'];
	$order_price = $order_pay->price-$order_pay->price_payed;
	if($money>$order_price || $money>$u->yue){
		die('{"code":0,"message":"系统错误,请刷新重试"}');
	}
	if(empty($order_pay)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->is_pay!=0){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	$db->query("update gift_card$fenbiao set yue=yue-$money where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，支付号：'.$order_pay->orderId;
	$liushui['orderId']=$payId;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $payId;
	$o['price_payed'] = $money+$order_pay->price_payed;
	$pay_json = array();
	if(!empty($order_pay->pay_json)){
		$pay_json = json_decode($order_pay->pay_json,true);
	}
	$pay_json['lipinka']['price'] = $money;
	$pay_json['lipinka']['desc'] = $u->cardId;
	$pay_json['lipinka']['cardId'] = $giftId;
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($money==$order_price){
		$o['is_pay'] = 1;
	}
	$db->insert_update('order_pay',$o,'id');
	$orders = json_decode($order_pay->orderInfo,true);
	$zong_price_card = $money;
	$bili = $db->get_var("select daili_bili from demo_shezhi where comId=10");
	if(!empty($orders)){
		foreach ($orders as $ord) {
			if($ord['price_card']>0 && $zong_price_card>0){
				$order_fenbiao = getFenbiao($ord['comId'],20);
				$order_comId = $ord['comId'];
				$orderId = $ord['orderId'];
				$order = $db->get_row("select pay_json,price_payed from order$order_fenbiao where id=".$ord['orderId']);
				$o = array();
				$p_card = $ord['price_card']>$zong_price_card?$zong_price_card:$ord['price_card'];
				$o['id'] = $ord['orderId'];
				$o['price_payed'] = $order->price_payed+$p_card;
				$pay_json = array();
				if(!empty($order->pay_json)){
					$pay_json = json_decode($order->pay_json,true);
				}
				$pay_json['lipinka']['price'] = $p_card;
				$pay_json['lipinka']['desc'] = $u->cardId;
				$pay_json['lipinka']['cardId'] = $giftId;
				$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
				if($u->daili_id>0){
					$fanli_json = json_decode($order->fanli_json);
					$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli;
					$daili_fanli = intval($zong_fanli*$bili)/100;
					$fanli_json->daili_id = $u->daili_id;
					$fanli_json->daili_fanli = $daili_fanli;
					$fanli_json->pingtai_fanli = $fanli_json->pingtai_fanli-$daili_fanli;
					$o['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
				}
				$db->insert_update('order'.$order_fenbiao,$o,'id');
				$zong_price_card-=$p_card;
			}
		}
	}
	if($money==$order_price){
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
				$pay_json['lipinka']['price'] = $order->price;
				$pay_json['lipinka']['desc'] = $u->cardId;
				$pay_json['lipinka']['cardId'] = $giftId;
				$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
				$o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
				$o['ispay'] = 1;
				$o['pay_type'] = 4;
				$db->insert_update('order'.$order_fenbiao,$o,'id');
				order_pay_done($order);
			}
		}
		die('{"code":2,"message":"支付成功"}');
	}
	die('{"code":1,"message":"支付成功"}');
}
function gift_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$giftId = (int)$request['card_id'];
	$zhifumm = $request['zhifumm'];
	$u = $db->get_row("select cardId,password,yue from gift_card$fenbiao where id=$giftId and userId=$userId");
	if(empty($u)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}
	if($u->password!=$zhifumm){
		die('{"code":0,"message":"支付密码不正确"}');
	}
	$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $order->price = $order->price_dingjin-$order->price_payed;
	}
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->yue<$order->price){
		die('{"code":0,"message":"余额不足！请选择其他支付方式"}');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('{"code":0,"message":"该订单已超过支付时间"}');
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	//$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$db->query("update gift_card$fenbiao set yue=yue-$order->price where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$order->price;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
	$liushui['orderId']=$orderId;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	if($order->price_dingjin==0){
		$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
		$o['ispay'] = 1;
		$o['pay_type'] = 4;
	}
	$o['price_payed'] = $order->price+$order->price_payed;
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	if($order->price_dingjin==0){
		$pay_json['lipinka']['price'] = $order->price;
		$pay_json['lipinka']['desc'] = $u->cardId;
		$pay_json['lipinka']['cardId'] = $giftId;
	}else{
		$pay_json['dingjin']['price'] = $order->price;
		$pay_json['dingjin']['paytype'] = '礼品卡，卡号：'.$u->cardId;
	}
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	$db->insert_update('order'.$fenbiao,$o,'id');
	if($order->price_dingjin==0){
		$db->query("update order_detail$fenbiao set status=1 where orderId=$orderId");
		$product_json = json_decode($order->product_json);
		$product_title = '';
		foreach ($product_json as $pdt){
			$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
		}
		if(!empty($product_title)){
			$product_title = substr($product_title,1);
		}
		$fahuo = array();
		$fahuo['comId'] = $comId;
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
		$fahuo['shequ_id'] = (int)$order->shequ_id;
		$db->insert_update('order_fahuo'.$fenbiao,$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update order$fenbiao set fahuoId=$fahuoId where id=$orderId");
		$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
		foreach ($details as $detail){
			$detail->num = (int)$detail->num;
			$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
			$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
		}
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
	die('{"code":1,"message":"支付成功","buy_type":'.$order->type.'}');
}
function alipay_pay(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$orderId = (int)$request['order_id'];
	$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
		$order->price = $order->price_dingjin-$order->price_payed;
	}
	if(empty($order)){
		die('订单不存在');
	}
	if($order->status!=-5){
		die('订单当前不是待支付状态');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('该订单已超过支付时间');
	}
	$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
	if(empty($alipay_set)||$alipay_set->status==0||empty($alipay_set->info)){
		die('支付宝配置信息有误');
	}
	$alipay_arr = json_decode($alipay_set->info);
	$subject = '';
	$product_json = json_decode($order->product_json);
	foreach ($product_json as $pdt) {
		$subject.=','.$pdt->title.'*'.$pdt->num;
	}
	$body = substr($subject,1);
	$subject = sys_substr($body,50,true);
	$subject = str_replace('_','',$subject).'_'.$comId;
	require_once(ABSPATH."/inc/pay/wappay/alipay.config.php");
	$alipay_config['partner'] = $alipay_arr->partnerId;
	$alipay_config['seller_id']	= $alipay_config['partner'];
	$alipay_config['private_key']	= $alipay_arr->private_key;
	$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
	require_once(ABSPATH."/inc/pay/wappay/lib/alipay_submit.class.php");
	$out_trade_no = $order->orderId;
	$total_fee =  $order->price;
	$show_url = "http://".$_SERVER['HTTP_HOST']."/index.php?p=19&a=view&id=$orderId";
	$parameter = array(
		"service"       => $alipay_config['service'],
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/notify_url.php",
		"return_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/return_url.php",
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"show_url"	=> $show_url,
		"body"	=> $body,
	);
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
	exit;
}
//支付宝冲余额
function alipay_chongzhi(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$money = $request['money'];
	$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
	if(empty($alipay_set)||$alipay_set->status==0||empty($alipay_set->info)){
		die('支付宝配置信息有误');
	}
	$orderId = date("YmdHis").rand(100000,999999);
	$chongzhi = array();
	$chongzhi['comId'] = $comId;
	$chongzhi['userId'] = $userId;
	$chongzhi['type'] = 2;
	$chongzhi['money'] = $money;
	$chongzhi['orderId'] = $orderId;
	$db->insert_update('user_chongzhi',$chongzhi,'id');
	$alipay_arr = json_decode($alipay_set->info);
	$subject = '余额充值';
	$body = $subject;
	$subject = $subject.'_'.$comId;
	require_once(ABSPATH."/inc/pay/wappay/alipay.config.php");
	$alipay_config['partner'] = $alipay_arr->partnerId;
	$alipay_config['seller_id']	= $alipay_config['partner'];
	$alipay_config['private_key']	= $alipay_arr->private_key;
	$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
	require_once(ABSPATH."/inc/pay/wappay/lib/alipay_submit.class.php");
	$out_trade_no = $orderId;
	$total_fee =  $money;
	$show_url = "http://".$_SERVER['HTTP_HOST']."/index.php?p=8&a=qianbao";
	$parameter = array(
		"service"       => $alipay_config['service'],
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/notify_chongzhi.php",
		"return_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/return_chongzhi.php",
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"show_url"	=> $show_url,
		"body"	=> $body,
	);
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
	exit;
}
function weixin_pay(){
	if(is_weixin()){
		global $db,$request,$order;
		$orderId = (int)$request['order_id'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION['demo_comId'];
		$fenbiao = getFenbiao($comId,20);
		$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
		if(empty($order)){
			die('<script>alert("订单不存在");location.href="/index.php?p=19&a=alone";</script>');
		}
		if($order->status!=-5){
			die('<script>location.href="/index.php?p=19&a=alone";</script>');
		}
		$pay_end = strtotime($order->pay_endtime);
		$now = time();
		if($pay_end<$now){
			die('<script>alert("该订单已超过支付时间");location.href="/index.php?p=19&a=alone";</script>');
			//die('{"code":0,"message":"该订单已超过支付时间"}');
		}
		require('inc/pay/WxpayAPI_php_v3/example/jsapi.php');
		exit;
	}
}
function weixin_chongzhi(){
	if(is_weixin()){
		global $db,$request;
		require('inc/pay/WxpayAPI_php_v3/example/jsapi_chongzhi.php');
		exit;
	}
}
function to_chongzhi(){}
//检查团购是否结束
function check_tuan_status($tuanId){
	global $db;
	$tuan = $db->get_row("select status,endTime from demo_tuan where id=$tuanId");
	if(empty($tuan) || $tuan->status!=0){
		return array("code"=>0,"message"=>"该团购已结束");
	}
	$pay_end = strtotime($tuan->endTime);
	$now = time();
	if($pay_end<$now){
		return array("code"=>0,"message"=>"该团购已结束");
	}
	return array("code"=>1);
}
//确认收货
function qr_shouhuo($userId=0){
	global $db,$request;
	$orderId = (int)$request['orderId'];
	$userId = empty($userId)?(int)$_SESSION[TB_PREFIX.'user_ID']:$userId;
	$comId = $order_comId = (int)$_SESSION['demo_comId'];
	if(!empty($request['comId'])){
		$order_comId = (int)$request['comId'];
	}
	$order_fenbiao = getFenbiao($order_comId,20);
	$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
	if($order->if_zong==1){
		$db_service = getCrmDb();
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		$comId = 10;
	}
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	if($order->status!=2 && $order->status!=3 && $order->peisong_type!=4){
		die('{"code":0,"message":"订单不是待收货状态"}');
	}
	$db->query("update order$order_fenbiao set status=4 where id=$orderId");
	$db->query("update order_detail$order_fenbiao set status=2 where orderId=$orderId");
	$jilu = array();
	$jilu['orderId'] = $orderId;
	$jilu['username'] = $_SESSION[TB_PREFIX.'user_name'];
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['type'] = 1;
	$jilu['remark'] = '手动确认收货';
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
			$db->insert_update('user_jifen'.$order_fenbiao,$jifen_jilu,'id');
		}
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
		//购买者返佣
		if($fanli_json->buyer_fanli>0){
			//返利给
			if($order->if_zong==1){
				$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->buyer_fanli.",earn=earn+".$fanli_json->buyer_fanli." where id=$order->zhishangId");
				$yue = $db_service->get_var("select yongjin from demo_user where id=$order->zhishangId");
			}else{
				$db->query("update users set money=money+".$fanli_json->buyer_fanli.",earn=earn+".$fanli_json->buyer_fanli." where id=$order->userId");
				$yue = $db->get_var("select money from users where id=$order->userId");
			}
			//$yzFenbiao = getYzFenbiao($fanli_json->shangji,20);
			$liushui = array();
			$liushui['userId']=$order->userId;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$fanli_json->buyer_fanli;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=$date;
			$liushui['remark']='自购返利';
			$liushui['orderInfo']='自购返利，订单号：'.$order->orderId;
			$liushui['order_id']=$orderId;
			$liushui['from_user']=$order->userId;
			if($order->if_zong==1){
				$db->insert_update('user_yongjin10',$liushui,'id');
			}else{
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			}
		}
		//上级收入，如果shagnji为0算到平台收益
		if($fanli_json->shangji_fanli>0 && $fanli_json->shangji){
			//返利给团长
			if($order->if_zong==1){
				$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->shangji_fanli.",earn=earn+".$fanli_json->shangji_fanli." where id=$fanli_json->shangji");
				$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->shangji");
			}else{
				$db->query("update users set money=money+".$fanli_json->shangji_fanli.",earn=earn+".$fanli_json->shangji_fanli." where id=$fanli_json->shangji");
				$yue = $db->get_var("select money from users where id=$fanli_json->shangji");
			}
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
			if($order->if_zong==1){
				$db->insert_update('user_yongjin10',$liushui,'id');
			}else{
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			}
			/*$fromUser = $db->get_var("select name from users where id=$userId");
			$openId = $db->get_var("select openId from users where id=$fanli_json->shangji");
			//返现到账通知
			$arr = array(
			    'first' => array(
			        'value' => '佣金到账通知',
			        'color' => '#FF0000'
			    ),
			    'order' => array(
			        'value' => $order->orderId,
			        'color' => '#FF0000'
			    ),
			    'money' => array(
			        'value' => $fanli_json->shangji_fanli,
			        'color' => '#FF0000'
			    ),
			    'remark' => array(
			        'value' => '收入类型：自营收入，来自成员：'.$fromUser.'购买的'.$product_json->title,
			        'color' => '#FF0000'
			    )
			);
			post_template_msg('47ycPbcQAkqZQ9OY0zw0SjyagxCNMJ1m2SVnVYkdbG8',$arr,$openId,'https://new.nmgyzwc.com/index.php?p=8&a=qianbao');*/
			//上级得积分
		}
		//团队奖励
		if($fanli_json->shangshangji_fanli>0 && $fanli_json->shangshangji>0){
			if($order->if_zong==1){
				$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
				$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->shangshangji");
			}else{
				$db->query("update users set money=money+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
				$yue = $db->get_var("select money from users where id=$fanli_json->shangshangji");
			}
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
			if($order->if_zong==1){
				$db->insert_update('user_yongjin10',$liushui,'id');
			}else{
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			}
		}
		//店铺推荐返利
		if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
			if($order->if_zong==1){
				$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->tuijian_fanli.",earn=earn+".$fanli_json->tuijian_fanli." where id=$fanli_json->tuijian");
				$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->tuijian");
			}else{
				$db->query("update users set money=money+".$fanli_json->tuijian_fanli.",earn=earn+".$fanli_json->tuijian_fanli." where id=$fanli_json->tuijian");
				$yue = $db->get_var("select money from users where id=$fanli_json->tuijian");
			}
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
			if($order->if_zong==1){
				$db->insert_update('user_yongjin10',$liushui,'id');
			}else{
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			}
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
		$db->query("update demo_yibao_fenzhang set status=2 where orderId=$order->id and comId=$order->comId and status=1");
	}
	//设置自动好评
	$days = $db->get_var("select time_comment from demo_shezhi where comId=$order->comId");
	if(empty($days))$days=10;
	$comment_time = strtotime("+ $days day");
	$timed_task = array();
	$timed_task['comId'] = (int)$order->comId;
	$timed_task['dtTime'] = $comment_time;
	$timed_task['router'] = 'order_autoComment';
	$timed_task['params'] = '{"order_id":'.$orderId.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
	die('{"code":1,"message":"操作成功"}');
}
function qx_order(){
	global $db,$request;
	$orderId = (int)$request['orderId'];
	$comId = (int)$_SESSION['demo_comId'];
	if(!empty($request['comId'])){
		$comId = (int)$request['comId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select * from order$fenbiao where id=$orderId");
	if($order->status==0 || $order->status==-5 || $order->status==2){
		if($order->tuan_id>0){
			$tuan = $db->get_row("select nums,status,userIds,orderIds from demo_tuan where id=$order->tuan_id");
			if($tuan->status!=0){
				die('{"code":0,"message":"团购已结束，不能取消订单"}');
			}
			$userIds = explode(',',$tuan->userIds);
			$orderIds= explode(',',$tuan->orderIds);
			foreach($userIds as $k=>$v) {
				if($order->userId == $v){
					unset($userIds[$k]);
					break;
				}
			}
			foreach($orderIds as $k=>$v) {
				if($order->id == $v){
					unset($orderIds[$k]);
					break;
				}
			}
			$uids = empty($userIds)?'':implode(',',$userIds);
			$oids = empty($orderIds)?'':implode(',',$orderIds);
			$nums = $tuan->nums - $order->pdtNums;
			$db->query("update demo_tuan set nums=$nums,userIds='$uids',orderIds='$oids' where id=$order->tuan_id");
			$db->query("update order$fenbiao set status=-1,remark='订单已取消',qx_time='".date("Y-m-d H:i:s")."' where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			if($order->price_payed>0){
				tuikuan($order);
			}
		}else{
			$db->query("update order_fahuo$fenbiao set status=-1 where id=$order->fahuoId");
			$db->query("update order$fenbiao set status=-1,remark='订单已取消',qx_time='".date("Y-m-d H:i:s")."' where id=$orderId");
			$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
			//恢复库存预购数量
			$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
			if(!empty($details)){
				foreach ($details as $detail){
					$db->query("update demo_kucun set yugouNum=yugouNum-".$detail->num." where inventoryId=$detail->inventoryId and storeId=".$order->storeId." limit 1");
					if($order->status>-1){
						$db->query("update demo_product_inventory set orders=orders-$detail->num where id=$detail->inventoryId");
						$db->query("update demo_product set orders=orders-$detail->num where id=$detail->productId");
					}
				}
			}
			$db->query("delete from cuxiao_pdt_buy where orderId=$orderId and comId=".($_SESSION['if_tongbu']==1?10:$comId));
			if($order->price_payed>0 || !empty($order->pay_json)){
				tuikuan($order);
			}
		}
		$db->query("update user_yugu_shouru set status=-1 where comId=$comId and orderId=$order->id and order_type=1");
	}else{
		die('{"code":0,"message":"订单当前状态不支持取消"}');
	}
	die('{"code":"1","message":"取消成功"}');
}
function get_tuan_list(){
	global $db,$request;
	$status = (int)$request['status'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;

	$sql="select * from demo_tuan where comId=$comId";
	switch ($status) {
		case 0:
			$sql.=" and find_in_set($userId,userIds)";
		break;
		case 1:
			$sql.=" and tuanzhang=$userId";
		break;
		case 2:
			$sql.=" and find_in_set($userId,userIds) and status=0";
		break;
		case 3:
			$sql.=" and find_in_set($userId,userIds) and status=1";
		break;
		case 4:
			$sql.=" and find_in_set($userId,userIds) and status=-1";
		break;
	}
	//file_put_contents('request.txt',$sql);
	$count = $db->get_var(str_replace('*','count(*)',$sql));
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
			$data['status'] = $pdt->status;
			switch ($pdt->status) {
				case 0:
					$pay_end = strtotime($pdt->endTime);
					if($pay_end>$now){
						$data['statusInfo'] = '待成团';
					}else{
						$data['statusInfo'] = '拼团失败';
					}
				break;
				case 1:
					$data['statusInfo'] = '拼团成功';
				break;
				case -1:
					$data['statusInfo'] = '拼团失败';
				break;
			}
			$product_json = $db->get_row("select title,key_vals,image,price_sale,price_market from demo_product_inventory where id=$pdt->inventoryId");
			$data['product'] = $product_json->title;
			$data['image'] = ispic($product_json->image);
			$data['price_sale'] = $product_json->price_sale;
			$data['price_market'] = $product_json->price_market;
			$data['num_yi'] = 0;
			if(!empty($pdt->orderIds)){
				$data['num_yi'] = count(explode(',',$pdt->orderIds));
			}
			$data['num_cha'] = $pdt->user_num-$data['num_yi'];
			$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//微信相关的方法
function createNoncestr( $length = 32 ){
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}
function postXmlCurl($xml,$url,$second = 30){
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //设置 header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post 提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行 curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    }else{
        $error = curl_errno($ch);
        curl_close($ch);
        echo "curl 出错，错误码:$error"."<br>";
    }
}
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    $ip         =   'unknown';
    if ($ip !== 'unknown') return $ip[$type];
    if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实 IP
        $ip=$_SERVER['HTTP_X_REAL_IP'];
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的 ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的 ip 地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP 地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
function del_order(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$request['comId'];
	$fenbiao = getFenbiao($comId,20);
	$db->query("update order$fenbiao set is_del=1 where id=$id");
	die('{"code":"1","message":"删除成功"}');
}
function is_weixin(){
	if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false){
		return true;
	}
	return false;
}
//核销订单
function hexiao(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION['demo_comId'];
		$fenbiao = getFenbiao($comId,20);
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$order_id = get_hexiao_id($request['hexiaoma']);
		$order = $db->get_row("select status,shequ_id,userId,fahuoId from order$fenbiao where id=$order_id");
		if(empty($order)){
			echo '{"code":0,"message":"未找到该订单，请检查核销码"}';
			exit;
		}
		if($order->status!=3 && $order->status!=2){
			echo '{"code":0,"message":"该订单状态不支持核销！请检查确认"}';
			exit;
		}
		$if_user_shequ = $db->get_var("select id from demo_shequ where id=$order->shequ_id and userId=$userId");
		if(empty($if_user_shequ)){
			echo '{"code":0,"message":"该订单不隶属于您的社区！请检查核实"}';
			exit;
		}
		$db->query("update order_fahuo$fenbiao set status=3 where id=$order->fahuoId limit 1");
		$request['orderId'] = $order_id;
		qr_shouhuo($order->userId);
	}
}
//后台核销
function houtai_hexiao(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	if(empty($userId)){
		echo '{"code":0,"message":"请重新登录后操作"}';
		exit;
	}
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$order_id = get_hexiao_id($request['code']);
	$order = $db->get_row("select status,shequ_id,userId,fahuoId from order$fenbiao where id=$order_id");
	if(empty($order)){
		echo '{"code":0,"message":"未找到该订单，请检查核销码"}';
		exit;
	}
	if($order->status!=3 && $order->status!=2){
		echo '{"code":0,"message":"该订单状态不支持核销！请检查确认"}';
		exit;
	}
	//$if_user_shequ = $db->get_var("select id from demo_shequ where id=$order->shequ_id and userId=$userId");
	if($order->shequ_id != $_SESSION['demo_shequ_id']){
		echo '{"code":0,"message":"该订单不隶属于您的社区！请检查核实"}';
		exit;
	}
	$db->query("update order_fahuo$fenbiao set status=3 where id=$order->fahuoId limit 1");
	$request['orderId'] = $order_id;
	qr_shouhuo($order->userId);
}
function wancheng_order(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$order_id = (int)$request['id'];
	$order = $db->get_row("select status,shequ_id,userId,fahuoId from order$fenbiao where id=$order_id");
	if(empty($order)){
		echo '{"code":0,"message":"未找到该订单，请检查核销码"}';
		exit;
	}
	if($order->status!=3 && $order->status!=2){
		echo '{"code":0,"message":"该订单状态不支持核销！请检查确认"}';
		exit;
	}
	$if_user_shequ = $db->get_var("select id from demo_shequ where id=$order->shequ_id and userId=$userId");
	if(empty($if_user_shequ)){
		echo '{"code":0,"message":"该订单不隶属于您的社区！请检查核实"}';
		exit;
	}
	$db->query("update order_fahuo$fenbiao set status=3 where id=$order->fahuoId limit 1");
	$request['orderId'] = $order_id;
	qr_shouhuo($order->userId);
}
//再来一单
function moreOrder(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$fenbiao = getFenbiao($comId,20);
	$product_json = $db->get_var("select product_json from order$fenbiao where id=$id");
	$gouwuche = array();
	if(!empty($product_json)){
		$products = json_decode($product_json);
		foreach ($products as $product) {
			$inventory = $db->get_row("select comId,if_kuaidi,channelId,price_sale,price_market,price_gonghuo,image,key_vals,title from demo_product_inventory where id=".$product->id);
			$item = array();
			$item['productId'] = $product->productId;
			$item['inventoryId'] = $product->id;
			$item['num'] = $product->num;
			$item['comId'] = $inventory->comId;
			$item['if_kuaidi'] = $inventory->if_kuaidi;
			$item['channelId'] = $inventory->channelId;
			$item['price_sale'] = $inventory->price_sale;
			$item['price_market'] = $inventory->price_market;
			$item['price_gonghuo'] = $inventory->price_gonghuo;
			$item['title'] = $inventory->title;
			$item['key_vals'] = $inventory->key_vals;
			$item['image'] = $inventory->image;
			$gouwuche[$product->id] = $item;
		}
		$db->query("update demo_gouwuche set content='".json_encode($gouwuche,JSON_UNESCAPED_UNICODE)."' where userId=$userId and comId=$comId limit 1");
	}
	redirect('/index.php?p=4&a=channels');
}
//36进制转10进制
function get_hexiao_id($char){
	$array=array("1","2","3","4","5","6","7","8","9","A", "B", "C", "D","E", "F", "G", "H", "I", "J", "K", "L","M", "N", "O","P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y","Z");
	while (true) {
		if(substr($char,0,1)=='0'){
			$char = substr($char,1);
		}else{
			break;
		}
	}
	$len=strlen($char);
	for($i=0;$i<$len;$i++){
		$index=array_search($char[$i],$array);
		$sum+=($index+1)*pow(35,$len-$i-1);
	}
	return $sum;
}
//10进制转36进制
function get_36id($char){
	$num = intval($char);
	if ($num <= 0)
	return false;
	$charArr = array("1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$char = '';
	do {
		$key = ($num - 1) % 35;
		$char= $charArr[$key] . $char;
		$num = floor(($num - $key) / 35);
		//echo $num;
	} while ($num > 0);
	$char = buling($char,6);
	return $char;
}
//订单退款操作,优惠券、积分、余额、礼品卡、微信、支付宝==
/*$order = $db->get_row("select * from order1 where id=3");
tuikuan($order);*/
function tuikuan($order){
	global $db;
	$userId = $order->userId;
	$comId = $order->comId;
	$orderId = $order->id;
	$zong_fenbiao = $fenbiao = getFenbiao($comId,20);
	if($_SESSION['if_tongbu']==1){
		$zong_fenbiao = 10;
		$userId = (int)$order->zhishangId;
		$db_service = getCrmDb();
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
		$jifen_jilu['remark'] = '取消订单，订单号：'.$order->orderId;
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
		insert_update('gift_card_liushui'.$zong_fenbiao,$liushui,'id');
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
		insert_update('lipinka_liushui',$liushui,'id');
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
			define('WX_SSLKEY',ABSPATH.$weixin_arr->sslkey);
			define('WX_SSLCERT',ABSPATH.$weixin_arr->sslcert);
			require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
			require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
			$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
			$log = Log::Init($logHandler, 15);
			$transaction_id = $pay_json['weixin']['desc'][0];
			$total_fee = $money*100;
			$refund_fee = $total_fee;
			$input = new WxPayRefund();
			$input->SetTransaction_id($transaction_id);
			$input->SetTotal_fee($total_fee);
			$input->SetRefund_fee($refund_fee);
			$input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
			$input->SetOp_user_id(WX_MCHID);
			//file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE));
			$result = WxPayApi::refund($input);
			if($result['result_code'] != "SUCCESS"){
				addTaskMsg(51,$order->id,'订单退款失败，请登录商户平台手动退款,订单号：'.$order->orderId.'，微信商户订单号：'.$transaction_id.',失败原因：'.$result['err_code_des'],$comId);
				file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
			}
		}else{
			if($_SESSION['if_tongbu']==1){
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
			$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
			$log = Log::Init($logHandler, 15);
			$transaction_id = $pay_json['applet']['desc'][0];
			$total_fee = $money*100;
			$refund_fee = $total_fee;
			$input = new WxPayRefund();
			$input->SetTransaction_id($transaction_id);
			$input->SetTotal_fee($total_fee);
			$input->SetRefund_fee($refund_fee);
			$input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
			$input->SetOp_user_id(WX_MCHID);
			//file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE),FILE_APPEND);
			$result = WxPayApi::refund($input);
			//file_put_contents('refund.txt',$result,FILE_APPEND);
			if($result['result_code'] != "SUCCESS"){
				addTaskMsg(51,$order->id,'订单退款失败，请登录商户平台手动退款,订单号：'.$order->orderId.'，微信商户订单号：'.$transaction_id.',失败原因：'.$result['err_code_des'],$comId);
				file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
			}
		}else{
			if($_SESSION['if_tongbu']==1){
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
		if($_SESSION['if_tongbu']==1){
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
	if(!empty($pay_json['yibao']['price'])){
		$money = $pay_json['yibao']['price'];
		$yibao_orderId = $pay_json['yibao']['desc'];
		$verify = md5(substr($yibao_orderId.$money,0,10));
		//是否已经分过账
		$fenzhang = $db->get_row("select id,payId,ledgerNo,ledgerName from demo_yibao_fenzhang where orderId=$order->id and income_type=1 limit 1");
		if(!empty($fenzhang) && $fenzhang->status==2){
			file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
			if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
				$money = $pay_json['dingjin']['price'];
				$yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
				$verify = md5(substr($yibao_orderId.$money,0,10));
				file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
			}
		}else{
			$db->query("update demo_yibao_fenzhang set status=-1 where orderId=$order->id and income_type=1 and status=1 limit 1");
			file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
			if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
				$money = $pay_json['dingjin']['price'];
				$yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
				$verify = md5(substr($yibao_orderId.$money,0,10));
				file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
			}
		}
	}
}
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
	$db->query("update order_detail$order_fenbiao set status=2 where orderId=$orderId");
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
		$db->query("update demo_yibao_fenzhang set status=2 where orderId=$order->id and comId=$order->comId and status=1");
	}
	return true;
}
//订单付完全款执行的方法
function order_pay_done($order){
	global $db,$request;
	if($order->peisong_type==4){
		$db->query("update users set `cost`=`cost`+$order->price where id=$order->userId and comId=$order->comId");
		$request['orderId'] = $orderId;
		diancan_pay_done($order->id,$order->userId,$order->comId);
	}else{
		$order_fenbiao = getFenbiao($order->comId,20);
		$orderId = $order->id;
		$userId = $order->userId;
		$order_comId = $order->comId;
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
							if($_SESSION['if_tongbu']==1){
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
							if($_SESSION['if_tongbu']==1){
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
			if($order_comId==1121){
				$fahuo['fahuoTime'] = date("Y-m-d 00:00:00",strtotime('+1 day'));
			}
			$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
			if($order->yushouId>0){
				$fahuo['yushouId'] = $order->yushouId;
				$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
			}
			$fahuo['shequ_id'] = $order->shequ_id;
			$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$orderId");
			if($_SESSION['if_tongbu']==1){
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
		//添加会员的消费总金额
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
		$content .= '<FB>联系电话:</FB><FS>'.$shouhuo_json['手机号'].'</FS>\n';
		$content .= '<FB>配送地址:'.($order->peisong_type==1?'站点自提':$shouhuo_json['所在地区'].$shouhuo_json['详细地址']).'</FB>\n';
		$content .= '<FB>下单时间: '.$order->dtTime.'</FB>\n';
		if(!empty($order->peisong_time) && $order->peisong_type==2){
			$content .= '<FB>配送时间: '.$order->peisong_time.'</FB>\n';
		}
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
//后台调用点餐支付完成方法
function houtai_diancan_wancheng(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$orderId = (int)$request['id'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	if(empty($userId)){
		echo '{"code":0,"message":"请重新登录后操作"}';
		exit;
	}
	$order = $db->get_row("select id,comId,userId,pay_json from order$fenbiao where id=$orderId and status=-5");
	if(empty($order)){
		echo '{"code":0,"message":"订单当前状态已不支持结账，请刷新后重试"}';
		exit;
	}
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	$pay_json['admin']['price'] = $order->price-$order->price_payed;
	$pay_json['admin']['desc'] = $request['cont'];
	$db->query("update order$fenbiao set pay_json='".json_encode($pay_json,JSON_UNESCAPED_UNICODE)."',price_payed=price,ispay=1 where id=$orderId");
	diancan_pay_done($order->id,$order->userId,$order->comId);
	echo '{"code":1,"message":"ok"}';
	exit;
}
//后台调用的完成退换货退款的方法
function wancheng_tuihuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$tuihuanId = (int)$request['tuihuanId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	if(empty($userId)){
		echo '{"code":0,"message":"请重新登录后操作"}';
		exit;
	}
	$username = $_SESSION[TB_PREFIX.'name'];
	$time = date('Y-m-d H:i:s');
	$jilu = $db->get_row("select * from order_tuihuan where id=$tuihuanId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=3){
		echo '{"code":0,"message":"该退换货订单不需要再操作了"}';
		exit;
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
	if($jilu->type==2){
		$orderId = $jilu->orderId;
		$order = $db->get_row("select * from order$fenbiao where id=$jilu->orderId");
		$db->query("update order$fenbiao set status=-1,remark='订单已退款',price_tuikuan='$jilu->money' where id=$orderId");
		$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
		
		if($jilu->kuaidi_type==2 && $jilu->kuaidi_money>0){
			if($_SESSION['if_tongbu']==1){
				$db_service = getCrmDb();
				$comId = 10;
				$userId = $order->zhishangId;
				$db_service->query("update demo_user set money=money+$jilu->kuaidi_money where id=$order->zhishangId");
				$yue = $db_service->get_var("select money from demo_user where id=$order->zhishangId");
			}else{
				$userId = $order->userId;
				$db->query("update users set money=money+$jilu->kuaidi_money where id=$order->userId");
				$yue = $db->get_var("select money from users where id=$order->userId");
			}
			$liushui = array();
			$liushui['userId']=$userId;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$jilu->kuaidi_money;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='退换货运费补偿';
			$liushui['orderInfo']='退换货运费补偿，订单号：'.$order->orderId;
			$liushui['order_id']=$order->id;
			$yzFenbiao = getFenbiao($comId,20);
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			if($_SESSION['if_tongbu']==1){
				$db->query("update demo_shops set money=money-$jilu->kuaidi_money where comId=$order->comId");
				$liushui = array();
				$liushui['mendianId']=$order->comId;
				$liushui['comId']=10;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=-$jilu->kuaidi_money;
				$liushui['yue']=$db->get_var("select money from demo_shops where comId=$order->comId");
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='退货快递费用,快递费用由卖家承担,单号：'.$order->orderId;
				$liushui['typeInfo']='订单退货快递费用';
				insert_update('demo_mendian_liushui'.$yzFenbiao,$liushui,'id');
			}
		}
		tuikuan($order);
	}
	//addUserMsg($jilu->userId,$fenbiao,'您的退换货申请已完成，请检查退款情况',1,$jilu->orderId);
	echo '{"code":1}';
	exit;
}
//后台取消发货
function qx_fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$fahuoIds = $request['ids'];
	$fenbiao = getFenbiao($comId,20);
	$fahuos = $db->get_results("select id,orderIds from order_fahuo$fenbiao where id in($fahuoIds) and status >-1");
	if(!empty($fahuos)){
		//设置发货单状态
		$db->query("update order_fahuo$fenbiao set status=-1,remark='管理员取消，操作人：".$_SESSION[TB_PREFIX.'name']."' where id in($fahuoIds)");
		foreach ($fahuos as $fahuo){
			$oids = explode(',',$fahuo->orderIds);
			if(!empty($oids)){
				foreach ($oids as $orderId) {
					$order = $db->get_row("select * from order$fenbiao where id=$orderId");
					if($order->status>-1){
						$db->query("update order$fenbiao set status=-1,remark='管理员取消订单',qx_time='".date("Y-m-d H:i:s")."' where id=$orderId");
						$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
						$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
						foreach ($details as $detail){
							if($order->status==2){
								$db->query("update demo_kucun set yugouNum=yugouNum-".$detail->num." where inventoryId=$detail->inventoryId and storeId=".$order->storeId." limit 1");
							}
							$db->query("update demo_product_inventory set orders=orders-$detail->num where id=$detail->inventoryId");
							$db->query("update demo_product set orders=orders-$detail->num where id=$detail->productId");
						}
						if($order->price_payed>0){
							tuikuan($order);
						}
						$db->query("update user_yugu_shouru set status=-1 where comId=$comId and orderId=$orderId and order_type=1");
					}
				}
			}
		}
	}
	die('{"code":1,"message":"操作成功"}');
}
//新增积分支付
function jifen_pays(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$u = $db->get_row("select jifen from users where id=$userId");
	$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
	$p = json_decode($order->product_json);
	$jifen = $p[0]->jifen*$p[0]->num;
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->jifen<$jifen){
		die('{"code":0,"message":"积分不足！请刷新重试"}');
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		die('{"code":0,"message":"该订单已超过支付时间"}');
	}
	/*$jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
	if($jifen_pay->if_jifen_pay!=1){
		die('{"code":0,"message":"积分抵现功能已关闭"}');
	}
	$jifen_rule = json_decode($jifen_pay->jifen_pay_rule);
	$money = (int)($jifen*100/$jifen_rule->jifen)/100;
	$daizhifu = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $daizhifu = $order->price_dingjin-$order->price_payed;
	}*/
	//修改账号余额及流水记录
	$db->query("update users set jifen=jifen-$jifen where id=$userId");
	$jifen_jilu = array();
	$jifen_jilu['userId'] = $userId;
	$jifen_jilu['comId'] = $order->comId;
	$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$jifen_jilu['jifen'] = -$jifen;
	$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
	$jifen_jilu['type'] = 2;
	$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
	$jifen_jilu['remark'] = '订单支付，订单号：'.$order->orderId;
	//$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
	$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
	$o['ispay'] = 1;
	$o['pay_type'] = 1;
	//$o['price_payed'] = $order->price_payed + $money;
	$pay_json = array();
	$pay_json['jifen']['price'] = 0;
	$pay_json['jifen']['desc'] = $jifen;
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	$db->insert_update('order'.$fenbiao,$o,'id');
	if($order->price_dingjin==0){//常规订单
		$db->query("update order_detail$fenbiao set status=1 where orderId=$orderId");
		$product_json = json_decode($order->product_json);
		$product_title = '';
		foreach ($product_json as $pdt){
			$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
		}
		if(!empty($product_title)){
			$product_title = substr($product_title,1);
		}
		$fahuo = array();
		$fahuo['comId'] = $comId;
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
		$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
		if($order->yushouId>0){
			$fahuo['yushouId'] = $order->yushouId;
			$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
		}
		$fahuo['shequ_id'] = (int)$order->shequ_id;
		$db->insert_update('order_fahuo'.$fenbiao,$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update order$fenbiao set fahuoId=$fahuoId where id=$orderId");
		$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
		foreach ($details as $detail){
			$detail->num = (int)$detail->num;
			$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
			$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
		}
	}else{//支付定金
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
	addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order->comId);
	print_order($order);
	redirect('/index.php?p=19&a=alone');
}