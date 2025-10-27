<?php
require("WxPayBack.php");
function index(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	//$db->query("update order$fenbiao set status=-1 where comId=$comId and status=-5 and pay_endtime<'".date("Y-m-d H:i:s")."'");
}
function daochu(){}
function daochu_fapiao(){}
function tuikuan_order(){}
function tuihuo_order(){}
function huanhuo_order(){}
function caiwu_queren(){}
function tuikuan_queren(){}
function service(){}
function comment(){}
function guidang(){}
function fapiao(){}
function quehuo(){}
function yushou(){}
function yushou_order(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$if_jifen = (int)$request['if_jifen'];
	$status = $request['status'];
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$moneystart = $request['moneystart'];
	$moneyend = $request['moneyend'];
	$payStatus = $request['payStatus'];
	$pdtInfo = $request['pdtInfo'];
	$kaipiao = (int)$request['kaipiao'];
	$if_beizhu = (int)$request['if_beizhu'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];

	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,comId,mendianId,type,status,dtTime,remark,ispay,ifkaipiao,price,fahuo_json,shuohuo_json,kaipiao_status,product_json from order$fenbiao where comId=$comId and if_jifen = $if_jifen";
	
	$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];
	if($mendianId > 0){
	    $sql .= " and mendianId = $mendianId ";
	}
	
	$paytype = (int)$request['paytype'];
	if($paytype > 0){
	   	$sql.=" and find_in_set($paytype, pay_types) ";
	}
	
	$card = $request['card'] ? $request['card'] : '';
	if($card){
	    $sql .= " and pay_json like '%$card%' ";
	}
	
	switch ($scene){
		case 0:
			//$sql .= " and status in(0,1,2,3)";
		break;
		case 1:
			$sql .= " and type=2";
		break;
		case 2:
			//一小时内未支付的
			$last_time = date("Y-m-d H:i:s");
			$sql .= " and status=-5 and pay_endtime>'$last_time'";
		break;
		case 3:
			$sql .= " and status=-2";
		break;
		case 4:
			$sql .= " and status=-3";
		break;
		case 5:
			$sql .= " and status=-4";
		break;
		case 6:
			$sql .= " and type=4";
		break;
		case 7:
			$sql .= " and status=4";
		break;
		case 8:
			$last_time = date("Y-m-d H:i:s");
			$sql .= " and (status=-1 or (status=-5 and pay_endtime<'$last_time'))";
		break;
	}
	if(!empty($status)){
		$status = str_replace('9','0',$status);
		if(strstr($status,'-1')){
			$last_time = date("Y-m-d H:i:s");
			$sql.=" and (status in($status) or (status=-5 and pay_endtime<'$last_time'))";
		}else{
			$sql.=" and status in($status)";
		}
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($kehuName)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($shouhuoInfo)){
		$sql.=" and shuohuo_json like '%$shouhuoInfo%'";
	}
	if(!empty($moneystart)){
		$sql.=" and price>='$moneystart'";
	}
	if(!empty($moneyend)){
		$sql.=" and price<='$moneyend'";
	}
	if(!empty($payStatus)){
		$payStatus = $payStatus%2;
		$sql.=" and payStatus=$payStatus";
	}
	if(!empty($pdtInfo)){
		$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";
	}
	if(!empty($kaipiao)){
		$kaipiao = $kaipiao%2;
		$sql.=" and ifkaipiao=$kaipiao";
	}
	if($if_beizhu==1){
		$sql.=" and beizhu_json!=''";
	}
	$countsql = str_replace('id,orderId,userId,comId,mendianId,type,status,dtTime,remark,ispay,ifkaipiao,price,fahuo_json,shuohuo_json,kaipiao_status','count(*)',$sql);
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	//file_put_contents('request.txt',$sql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待成团</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待财务审核</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">待收货</span>';
				break;
				case 4:
					$status = '<span style="color:green;">已完成</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">异常</span>';
				break;
				case -3:
					$status = '<span style="color:#f00;">退换货</span>';
				break;
				case -4:
					$status = '<span style="color:#f00;">缺货</span>';
				break;
				case -5:
					$status = '<span style="color:#ff3333;">待支付</span>';
				break;
				case -1:
					$status = '<span>无效</span>';
					$qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
					if(in_array($j->remark,$qx_remarks)){
						$status = '<span>'.$j->remark.'</span>';
					}
					$j->layclass ='deleted';
				break;
			}
			$j->status_info = $status;
			switch ($j->ispay){
				case 0:
					$j->payStatus = '未付款';
				break;
				case 1:
					$j->payStatus = '已付款';
				break;
				case 2:
					$j->payStatus = '部分退款';
				break;
				case 3:
					$j->payStatus = '全部退款';
				break;
			}
			switch ($j->type){
				case 1:
					$j->type = '商城订单';
				break;
				case 2:
					$j->type = '货到付款';
				break;
				case 3:
					$j->type = '门店订单';
				break;
				case 4:
					$j->type = '预售订单';
				break;
			}
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->fahuoStatus = empty($j->fahuo_json)?'无':'已发货';
			$j->kaipiao_status = $j->kaipiao_status==1?'未开票':'已开票';
			switch ($j->ifkaipiao){
				case 0:
					$j->fapiao = '不开发票';
				break;
				case 1:
					$j->fapiao = '纸质发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
				case 2:
					$j->fapiao = '电子发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
			}
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			foreach ($product_array as $val) {
				$j->pdt_info.= ','.$val->title.'['.$val->key_vals.']'.' * '.$val->num;
			}
			$j->pdt_info = substr($j->pdt_info, 1);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getGuidangList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$status = $request['status'];
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$moneystart = $request['moneystart'];
	$moneyend = $request['moneyend'];
	$payStatus = $request['payStatus'];
	$pdtInfo = $request['pdtInfo'];
	$kaipiao = (int)$request['kaipiao'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,comId,mendianId,type,status,dtTime,remark,ispay,ifkaipiao,price,fahuo_json,shuohuo_json from order_guidang$fenbiao where comId=$comId";
	if(!empty($status)){
		$status = str_replace('9','0',$status);
		if(strstr($status,'-1')){
			$last_time = date("Y-m-d H:i:s");
			$sql.=" and (status in($status) or (status=-5 and pay_endtime<'$last_time'))";
		}else{
			$sql.=" and status in($status)";
		}
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($kehuName)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($shouhuoInfo)){
		$sql.=" and shuohuo_json like '%$shouhuoInfo%'";
	}
	if(!empty($moneystart)){
		$sql.=" and price>='$moneystart'";
	}
	if(!empty($moneyend)){
		$sql.=" and price<='$moneyend'";
	}
	if(!empty($payStatus)){
		$payStatus = $payStatus%2;
		$sql.=" and payStatus=$payStatus";
	}
	if(!empty($pdtInfo)){
		$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";
	}
	if(!empty($kaipiao)){
		$kaipiao = $kaipiao%2;
		$sql.=" and ifkaipiao=$kaipiao";
	}
	$countsql = str_replace('id,orderId,userId,comId,mendianId,type,status,dtTime,remark,ispay,ifkaipiao,price,fahuo_json,shuohuo_json','count(*)',$sql);
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	//file_put_contents('request.txt',$sql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待审核</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待财务审核</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">待收货</span>';
				break;
				case 4:
					$status = '<span style="color:green;">已完成</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">异常</span>';
				break;
				case -3:
					$status = '<span style="color:#f00;">退换货</span>';
				break;
				case -4:
					$status = '<span style="color:#f00;">缺货</span>';
				break;
				case -5:
					$status = '<span style="color:#ff3333;">待支付</span>';
				break;
				case -1:
					$status = '<span>无效</span>';
					$qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
					if(in_array($j->remark,$qx_remarks)){
						$status = '<span>'.$j->remark.'</span>';
					}
					$j->layclass ='deleted';
				break;
			}
			$j->status_info = $status;
			switch ($j->ispay){
				case 0:
					$j->payStatus = '未付款';
				break;
				case 1:
					$j->payStatus = '已付款';
				break;
				case 2:
					$j->payStatus = '部分退款';
				break;
				case 3:
					$j->payStatus = '全部退款';
				break;
			}
			switch ($j->type){
				case 1:
					$j->type = '商城订单';
				break;
				case 2:
					$j->type = '货到付款';
				break;
				case 3:
					$j->type = '门店订单';
				break;
				case 4:
					$j->type = '预售订单';
				break;
			}
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->fahuoStatus = empty($j->fahuo_json)?'无':'已发货';
			switch ($j->ifkaipiao){
				case 0:
					$j->fapiao = '不开发票';
				break;
				case 1:
					$j->fapiao = '纸质发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
				case 2:
					$j->fapiao = '电子发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
			}
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getYushouOrders(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$yushouId = (int)$request['id'];
	$scene = (int)$request['scene'];
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$kaipiao = (int)$request['kaipiao'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,comId,status,dtTime,remark,ispay,ifkaipiao,price,shuohuo_json,pdtNums from order$fenbiao where yushouId=$yushouId and comId=$comId";
	switch ($scene){
		case 1:
			$sql .= " and ispay=0";
		break;
		case 2:
			$sql .= " and status=2";
		break;
		case 3:
			$sql .= " and status=3";
		break;
		case 4:
			$sql .= " and status=4";
		break;
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($shouhuoInfo)){
		$sql.=" and shuohuo_json like '%$shouhuoInfo%'";
	}
	if(!empty($kaipiao)){
		$kaipiao = $kaipiao%2;
		$sql.=" and ifkaipiao=$kaipiao";
	}
	$countsql = str_replace('id,orderId,userId,comId,status,dtTime,remark,ispay,ifkaipiao,price,shuohuo_json,pdtNums','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 2:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">待收货</span>';
				break;
				case 4:
					$status = '<span style="color:green;">已完成</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">异常</span>';
				break;
				case -3:
					$status = '<span style="color:#f00;">退换货</span>';
				break;
				case -4:
					$status = '<span style="color:#f00;">缺货</span>';
				break;
				case -5:
					$status = '<span style="color:#ff3333;">待支付</span>';
				break;
				case -1:
					$status = '<span>无效</span>';
					$qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
					if(in_array($j->remark,$qx_remarks)){
						$status = '<span>'.$j->remark.'</span>';
					}
					$j->layclass ='deleted';
				break;
			}
			$j->status_info = $status;
			$j->username = $db->get_var("select username from users where id=$j->userId");
			switch ($j->ifkaipiao){
				case 0:
					$j->fapiao = '不开发票';
				break;
				case 1:
					$j->fapiao = '纸质发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
				case 2:
					$j->fapiao = '电子发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
			}
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getFapiaoList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$moneystart = $request['moneystart'];
	$moneyend = $request['moneyend'];
	$payStatus = $request['payStatus'];
	$pdtInfo = $request['pdtInfo'];
	$kaipiao = (int)$request['kaipiao'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,comId,status,ispay,price,ifkaipiao,kaipiao_status,shuohuo_json,fapiao_json from order$fenbiao where comId=$comId and ifkaipiao>0 and status not in(-1,-5)";
	if(!empty($status)){
		$status = str_replace('9','0',$status);
		if(strstr($status,'-1')){
			$last_time = date("Y-m-d H:i:s");
			$sql.=" and (status in($status) or (status=-5 and pay_endtime<'$last_time'))";
		}else{
			$sql.=" and status in($status)";
		}
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($kehuName)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($shouhuoInfo)){
		$sql.=" and shuohuo_json like '%$shouhuoInfo%'";
	}
	if(!empty($moneystart)){
		$sql.=" and price>='$moneystart'";
	}
	if(!empty($moneyend)){
		$sql.=" and price<='$moneyend'";
	}
	if(!empty($payStatus)){
		$payStatus = $payStatus%2;
		$sql.=" and payStatus=$payStatus";
	}
	if(!empty($pdtInfo)){
		$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";
	}
	if(!empty($kaipiao)){
		$sql.=" and kaipiao_status=$kaipiao";
	}
	$countsql = str_replace('id,orderId,userId,comId,status,ispay,price,ifkaipiao,kaipiao_status,shuohuo_json,fapiao_json','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->kaipiao_fangshi = $j->ifkaipiao==2?'电子':'纸质';
			$j->status = $j->kaipiao_status;
			$j->kaipiao_status = $j->kaipiao_status==2?'<font color="green">已开票</font>':'<font color="red">待开票</font>';
			$fapiao_json = json_decode($j->fapiao_json,true);
			$j->kaipiao_type = $fapiao_json['发票类型'];
			$j->kaipiao_title = $fapiao_json['公司名称'];
			$j->kaipiao_shibie = $fapiao_json['识别码'];
			$j->kaipiao_cont = $fapiao_json['发票明细'];
			$j->shoupiao_phone=$fapiao_json['收票人手机'];
			$j->shoupiao_email=$fapiao_json['收票人邮箱'];
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getQuehuoList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$keyword = $request['keyword'];
	$pdtChanelOpt = $request['pdtChanelOpt'];
	$pdtChanelNum = $request['pdtChanelNum'];
	$pdtNumsOpt = $request['pdtNumsOpt'];
	$pdtNumsNum = $request['pdtNumsNum'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('quehuoPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,price,ifkaipiao,shuohuo_json,weight,pdtNums,dtTime,remark from order$fenbiao where comId=$comId and status=-4";
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($pdtChanelNum)){
		$sql.=" and pdtChanel".$pdtChanelOpt.$pdtChanelNum;
	}
	if(!empty($pdtNumsNum)){
		$sql.=" and weight".$pdtNumsOpt.$pdtNumsNum;
	}
	$countsql = str_replace('id,orderId,userId,price,ifkaipiao,shuohuo_json,weight,pdtNums,dtTime,remark','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->fapiao = empty($j->ifkaipiao)?'不开票':($j->ifkaipiao==2?'电子':'纸质');
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$now = time();
			$orderTime = strtotime($j->dtTime);
			$time = $now-$orderTime;
			if($time<86400){
				$hours = ceil($time/3600);
				$j->time = '<span style="quehuo_span">超过'.$hours.'小时</span>';
			}else{
				$days = floor($time/86400);
				if($days>2 && $days<7){
					$j->time = '<span class="quehuo_span" style="background-color:#f60;">超过'.$days.'天</span>';
				}else if($days>=7){
					$j->time = '<span class="quehuo_span" style="background-color:#f00;">超过'.$days.'天</span>';
				}else{
					$j->time = '<span class="quehuo_span">超过'.$days.'天</span>';
				}
			}
			$j->dtTime = date("Y-m-d H:i",$orderTime);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getTuihuanList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$type = (int)$request['type'];
	$ifJifen = (int)$request['if_jifen'];
	$status = empty($request['status'])?1:(int)$request['status'];
	$keyword = $request['keyword'];
	$reason = $request['reason'];
	$sn = $request['sn'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$pdtInfo = $request['pdtInfo'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('tuihuanPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from order_tuihuan where comId=$comId and if_jifen = $ifJifen ";
	if(!empty($type)){
		$sql.=" and type=$type";
	}
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or sn like '$keyword' or userId in($userIds))";
	}
	if(!empty($reason)){
		$sql.=" and reason='$reason'";
	}
	if(!empty($sn)){
		$sql.=" and sn like '%$sn%'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($kehuName)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($pdtInfo)){
		$sql.=" and pdtInfo like '%$pdtInfo%'";
	}
	//echo $sql;die;
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	//file_put_contents('request.txt',$sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));

			$u = $db->get_row("select username,nickname from users where id=$j->userId");
			$j->username = $u->nickname.'('.$u->username.')';
			$j->tuihuanId = $j->id;
			$j->id = $j->orderId;
// 			var_dump($j->pdtInfo);die;
            $pdtInfo = array();
			$pdtInfo[] = json_decode($j->pdtInfo,JSON_UNESCAPED_UNICODE);
			$j->pdtInfo = '';
			foreach ($pdtInfo as $pdt){
				$j->pdtInfo.=$pdt['title'].$pdt['key_vals'];
			}
			$j->pdtInfo = '<span onmouseover="tips(this,\''.$j->pdtInfo.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->pdtInfo),35,true).'</span>';
			$j->type=$j->type==1?'仅退款':'退货退款';
// 			var_dump($j);die;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getServiceList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$status = empty($request['status'])?1:(int)$request['status'];
	$keyword = $request['keyword'];
	$reason = $request['reason'];
	$sn = $request['sn'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('tuihuanPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from order_service where comId=$comId ";
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		/*$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';*/
		$sql.=" and (orderId like '%$keyword%' or sn like '$keyword' or name='$keyword' or phone='$keyword')";
	}
	if(!empty($reason)){
		$sql.=" and serviceId='$reason'";
	}
	if(!empty($sn)){
		$sql.=" and sn like '%$sn%'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($kehuName)){
		$sql.=" and (name='$keyword' or phone='$keyword')";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	//file_put_contents('request.txt',$sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->ispay = $j->ispay==1?'已支付':'未支付';
			$j->service_time = date("Y-m-d H:i",strtotime($j->service_time));
			$j->dealTime = date("Y-m-d H:i",strtotime($j->dealTime));
			$j->serviceId = $j->id;
			$j->id = $j->orderId;
			$status_info = '';
			switch ($j->status) {
				case 1:
					$status_info='待分配';
				break;
				case 2:
					$status_info='待服务';
				break;
				case 3:
					$status_info='已完成';
				break;
				case -1:
					$status_info='已取消';
				break;
			}
			$j->status_info = $status_info;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getCommentList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$status = empty($request['status'])?1:(int)$request['status'];
	$star = (int)$request['star'];
	$ifJifen = (int)$request['if_jifen'];
	$keyword = $request['keyword'];
	$pdtName = $request['pdtName'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$username = $request['username'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('tuihuanPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from order_comment$fenbiao where comId=$comId and if_jifen = $ifJifen ";
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	switch ($star) {
		case 1:
			$sql.=" and star in(1,2)";
		break;
		case 3:
			$sql.=" and star=3";
		break;
		case 5:
			$sql.=" and star in(4,5)";
		break;
	}
	if(!empty($keyword)){
		$sql.=" and (pdtName like '%$keyword%' or name='$keyword' or order_orderId like '%$keyword%')";
	}
	if(!empty($pdtName)){
		$sql.=" and pdtName like '%$pdtName%'";
	}
	if(!empty($orderId)){
		$sql.=" and order_orderId='$orderId'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($username)){
		$sql.=" and name='$username'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pingjia = '';
			switch ($j->star){
				case 1:
					$pingjia = '<img src="images/pingjia_12.png" style="margin-right:5px;">差评';
				break;
				case 2:
					$pingjia = '<img src="images/pingjia_12.png" style="margin-right:5px;">差评';
				break;
				case 3:
					$pingjia = '<img src="images/pingjia_11.png" style="margin-right:5px;">中评';
				break;
				case 4:
					$pingjia = '<img src="images/pingjia_1.png" style="margin-right:5px;">好评';
				break;
				case 5:
					$pingjia = '<img src="images/pingjia_1.png" style="margin-right:5px;">好评';
				break;
			}
			$j->pingjia=$pingjia;
			$j->content = '<div style="word-break:break-all;white-space:normal;">'.$j->cont1.'</div>';
			if(!empty($j->images1)){
				$j->content .= '<div style="padding-top:6px;">';
				$imgs = explode('|',$j->images1);
				foreach ($imgs as $img){
					$j->content .= '<a href="'.$img.'" target="_blank" style="margin-right:5px;"><img src="'.$img.'?x-oss-process=image/resize,w_63" height="63"></a>';
				}
				$j->content .= '</div>';
			}
			$j->content.= '<div style="font-size:12px;color:#919191;">'.date("Y-m-d H:i",strtotime($j->dtTime1)).'</div>';
			if(!empty($j->cont2)){
				$j->content .= '<div style="word-break:break-all;white-space:normal;">追加：'.$j->cont2.'</div>';
				if(!empty($j->images2)){
					$imgs = explode('|',$j->images2);
					foreach ($imgs as $img){
						$j->content .= '<img src="'.$img.'?x-oss-process=image/resize,w_63" height="63">';
					}
				}
				$j->content .= '<div style="font-size:12px;color:#919191;">'.date("Y-m-d H:i",strtotime($j->dtTime2)).'</div>';
			}
			if(!empty($j->reply)){
				$j->content .= '<div style="color:#2786bc;word-break:break-all;white-space:normal;">掌柜回复：'.$j->reply.'</div><div style="font-size:12px;color:#2786bc;">'.date("Y-m-d H:i",strtotime($j->dtTime3)).'</div>';
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function service_fenpei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$worker_id = (int)$request['worker_id'];
	$worker_name = $request['worker_name'];
	$service_time = $request['service_time'];
	$worker_phone = $request['worker_phone'];
	$db->query("update order_service set status=2,worker_id=$worker_id,worker_name='$worker_name',service_time='$service_time',worker_phone='$worker_phone' where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function service_zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$cont = $request['cont'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$worker_name = $_SESSION[TB_PREFIX.'name'];
	$deal_time = date("Y-m-d H:i:s");
	$db->query("update order_service set status=-1,worker_name='$worker_name',dealTime='$deal_time',remark='$cont' where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function quehuo_huifu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$worker_name = $_SESSION[TB_PREFIX.'name'];
	$deal_time = date("Y-m-d H:i:s");
	//判断处理流程是否需要审核和财务，如果直接是发货还需要写发货表
	$db->query("update order$fenbiao set status=0 where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function service_wancheng(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$worker_name = $_SESSION[TB_PREFIX.'name'];
	$deal_time = date("Y-m-d H:i:s");
	$db->query("update order_service set status=3,dealTime='$deal_time' where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function comment_shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$db->query("update order_comment$fenbiao set status=2 where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function comment_delete(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$db->query("delete from order_comment$fenbiao where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function comment_huifu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$cont = $request['cont'];
	$db->query("update order_comment$fenbiao set status=3,reply='$cont',dtTime3='".date("Y-m-d H:i:s")."' where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function order_info_index(){
	global $db,$request,$arr;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from order$fenbiao where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$price_json = json_decode($order->price_json,true);
	$pay_json = array();
	$fahuo_json = array(
	    'kuaidi_type' => '快递配送'
	);
	$shuohuo_json = array();
	if(!empty($order->pay_json))$pay_json=json_decode($order->pay_json,true);
	if(!empty($order->fahuo_json))$fahuo_json=json_decode($order->fahuo_json,true);
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	$user = $db->get_row("select nickname,username,level from users where id=$order->userId");
	if($user->level>0)$user_level = $db->get_var("select title from user_level where id=$user->level");
	$details = $db->get_results("select * from order_detail$fenbiao where orderId=$id order by id asc");
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';

	if($order->status>-1||$order->status==-5){
		$str .='<div class="ddxx_jibenxinxi_1" id="order_operate">订单操作：';
		switch ($order->status) {
			/*case 0:
				$str.='<a href="javascript:" onclick="order_shenhe('.$id.');">订单审核</a><a href="javascript:" onclick="order_edit('.$id.');">编辑</a><a href="javascript:" onclick="qiehuan(\'orderInfo\',2,\'dqddxiangqing_up_on\');order_error_index(0);">订单异常操作</a><a href="javascript:" onclick="qiehuan(\'orderInfo\',3,\'dqddxiangqing_up_on\');order_tuihuan_index(0);;">退换货申请</a><a href="javascript:" onclick="quxiao('.$id.');">取消订单</a>';
			break;*/
			case 1:
			//	$str.='<a href="javascript:" onclick="order_shenhe('.$id.');">订单审核</a><a href="javascript:" onclick="order_edit('.$id.');">编辑</a><a href="javascript:" onclick="qiehuan(\'orderInfo\',2,\'dqddxiangqing_up_on\');order_error_index(0);">订单异常操作</a><a href="javascript:" onclick="qiehuan(\'orderInfo\',3,\'dqddxiangqing_up_on\');order_tuihuan_index(0);;">退换货申请</a>';
				$str.='<a href="javascript:" onclick="order_shenhe('.$id.');">订单审核</a>';
			break;
			case -5:
				$str.=chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=order_info_getedit" onclick="order_edit('.$id.');">编辑</a>',1);
			break;
			case 2:
				$str.=chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=order_info_getedit" onclick="order_edit('.$id.');">编辑</a>',1);
				$str.='<a href="javascript:" onclick="quxiao('.$id.');">取消订单</a>';
			break;
// 			case 3:
// 				$str.='<a href="javascript:" onclick="order_tuihuan('.$id.');">退换货申请</a>';
// 			break;
// 			case 4:
// 				$str.='<a href="javascript:" onclick="order_tuihuan('.$id.');">退换货申请</a>';
// 			break;
		}
	    $str.='</div>';
	}
	$str.='<div class="ddxx_jibenxinxi_2">
	    	<div class="ddxx_jibenxinxi_2_01" id="order_info_price">
	        	<div class="ddxx_jibenxinxi_2_01_up">
	            	商品价格
	            </div>
	        	<div class="ddxx_jibenxinxi_2_01_down">
	            	<ul>
	            		<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	订单总额：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>￥'.$order->price.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                    if(!empty($price_json['goods'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品总额：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['goods']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yunfei'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	运费：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['yunfei']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yhq'])){
		                	$yhq_title = $db->get_var("select title from user_yhq$fenbiao where id=".$price_json['yhq']['desc']);
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	优惠券：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['yhq']['price'].'('.$yhq_title.')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao']['price'].'('.$price_json['cuxiao']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao_order'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	订单促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao_order']['price'].'('.$price_json['cuxiao_order']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['admin'])){
		                	if($price_json['admin']['price']>0){
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动优惠：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">-￥'.$price_json['admin']['price'].'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}else{
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动提价：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">+￥'.abs($price_json['admin']['price']).'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}
		                }
		                if(!empty($pay_json)){
	                    $str.='<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	 支付：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">';
	                    		if(!empty($pay_json['jifen'])){
	                    			$str.='积分抵现 <b>￥'.$pay_json['jifen']['price'].'</b>('.$pay_json['jifen']['desc'].'积分)<br>';
	                    		}
	                    		if(!empty($pay_json['yue'])){
	                    		    $yue = 0;
	                    		    foreach ($pay_json['yue'] as $pv){
	                    		        $yue = bcadd($yue, $pv['price'], 2);
	                    		    }
	                    			$str.='储值卡支付 <b>￥'.$yue.'</b><br>';
	                    		}
	                    		if(!empty($pay_json['weixin'])){
	                    			$str.='微信储值 <b>￥'.$pay_json['weixin']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['applet'])){
	                    			$str.='微信支付 <b>￥'.$pay_json['applet']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['alipay'])){
	                    			$str.='支付宝支付 <b>￥'.$pay_json['alipay']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['cash'])){
	                    			$str.='现金支付 <b>￥'.$pay_json['cash']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['paypal'])){
	                    			$str.='银联支付 <b>￥'.$pay_json['paypal']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka'])){
	                    			$str.='抵扣金支付 <b>￥'.$pay_json['lipinka']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka1'])){
	                    			$str.='礼品卡支付 <b>￥'.$pay_json['lipinka1']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['other'])){
	                    			$str.='其他支付 <b>￥'.$pay_json['other']['price'].'</b>('.$pay_json['ohter']['desc'].')<br>';
	                    		}
	                    		if(!empty($pay_json['yibao'])){
	                    			$pay_way = $pay_json['yibao']['pay_way']=='NCPAY'?'银行卡支付':'易宝微信支付';
                    				$str.=$pay_way.' ：<b>￥'.$pay_json['yibao']['price'].'</b>';
	                    		}
	                        $str.='</div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                }
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_02" id="order_info_fapiao">
	        	<div class="ddxx_jibenxinxi_2_02_up">
	            	<div class="ddxx_jibenxinxi_2_02_up_left">
	                	发票信息
	                </div>
	            	<div class="ddxx_jibenxinxi_2_02_up_right">
	                	'.($order->ifkaipiao==0?'不':'').'需要开票
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_02_down">
	            	<ul>';
	            	if($order->ifkaipiao>0){
	            		$fapiao_json = json_decode($order->fapiao_json,true);
	            		foreach ($fapiao_json as $key => $val){
	            			$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">'.$key.'：</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right" style="word-break:break-all;">'.$val.'</div>
                            	<div class="clearBoth"></div>
                            </li>';
	            		}
	            	}
	            	if($fapiao_json['发票类型']=='电子普通发票'){
	            		$fapiao_type = 1;
	            		$fapiao_cont = empty($fapiao_json['电子发票地址'])?'http://':$fapiao_json['电子发票地址'];
	            	}else{
	            		$fapiao_type = 2;
	            		$fapiao_cont = empty($fapiao_json['发票快递'])?'':$fapiao_json['发票快递'];
	            	}
	            	
	            	if($order->status==4 && $order->ifkaipiao>0 && $order->kaipiao_status==1){
	            		$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">&nbsp;</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right"><a href="javascript:" style="color:red" onclick="order_kaipiao('.$id.','.$fapiao_type.',\''.$fapiao_cont.'\');">开票</a></div>
                            	<div class="clearBoth"></div>
                            </li>';
	            	}else if($order->status==4 && $order->ifkaipiao>0 && $order->kaipiao_status==2){
	            		$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">&nbsp;</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right"><a href="javascript:" onclick="order_kaipiao('.$id.','.$fapiao_type.',\''.$fapiao_cont.'\');" style="color:red">修改发票信息</a></div>
                            	<div class="clearBoth"></div>
                            </li>';
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_03">	
	        	<div class="ddxx_jibenxinxi_2_03_up">	
	            	其它信息
	            </div>
	        	<div class="ddxx_jibenxinxi_2_03_down">
	            	<div class="ddxx_jibenxinxi_2_03_down_1">
	                	<ul>';
	                	$str.='<li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员名称：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->nickname.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员级别：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user_level.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">手机号：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->username.'</div>
	                            <div class="clearBoth"></div>
	                        </li>';
	                    $str.='</ul>
	                </div>
	            	<div class="ddxx_jibenxinxi_2_03_down_2">
	                	<ul>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">可得积分：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$order->jifen.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">配送方式：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$fahuo_json['kuaidi_type'].'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">商品重量：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$order->weight.$product_set->weight.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_04" id="order_info_shouhuo">
	        	<div class="ddxx_jibenxinxi_2_04_up">
	            	<div class="ddxx_jibenxinxi_2_04_up_left">
	                	收货人信息 <img src="images/dingdanxx_12.png" data-show="0" onclick="toggle_shouhuo_info(this);"/>
	                </div>
	            	<div class="ddxx_jibenxinxi_2_04_up_right">
	                	<a href="javascript:" id="copy_order_shouhuo" data-clipboard-text="'.$shuohuo_json['所在地区'].'，'.$shuohuo_json['详细地址'].'，'.$shuohuo_json['收件人'].'，'.$shuohuo_json['手机号'].'">复制收货人信息</a> <img src="images/dingdanxx_14.png" onmouseover="tips(this,\'此功能按照将收货人信息整合后复制到剪贴板，方便店主粘贴至目标位置，<br>如：给顾客确认地址的邮件<br>复制格式：<br>地区，地址，姓名，手机号\',1);" onmouseout="hideTips()"/>
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_04_down">
	            	<ul>';
	            	foreach ($shuohuo_json as $key => $val){
	            		if($key=='收件人'){
	            			$username = $val;
	            			$val = substr($val,0,3).'*'.substr($val,-3);
	            		}else if($key=='手机号'){
	            			$phone = $val;
	            			$val = substr($val,0,3).'****'.substr($val,-4);
	            		}
	            		$str.='<li>
		            		<div class="ddxx_jibenxinxi_2_01_down_left">'.$key.'：</div>
		            		<div class="ddxx_jibenxinxi_2_01_down_right" '.($key=='收件人'?'id="order_shoujianren" data-val="'.$username.'" data-hide="'.$val.'"':($key='手机号'?'id="order_shoujihao" data-val="'.$phone.'" data-hide="'.$val.'"':'')).'>'.$val.'</div>
		            		<div class="clearBoth"></div>
	            		</li>';
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_4">
	    	<div class="ddxx_jibenxinxi_4_up">
	        	订单明细：
	        </div>
	    	<div class="ddxx_jibenxinxi_4_down">
	        	<table width="100%" border="0" cellpadding="0" cellspacing="0">	
	            	<tr height="34">
	                	<td align="center" width="34" valign="middle" class="ddxx_jibenxinxi_4_down_bj"></td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品编码</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品名称</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">规格</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">数量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">库存</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单位</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单价</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">小计</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">已发货量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">已退货量</td>
	                </tr>';
	                foreach ($details as $i=>$jilu){
	                	$pdtInfo = json_decode($jilu->pdtInfo);
						$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$jilu->inventoryId and storeId=$order->storeId");
						$kucun = $kc->kucun-$kc->yugouNum;
						if($order->fahuoId==0 && $order->status!=-1){
							$kucun += $jilu->num;
						}
	                	$str.='<tr height="34">
	                	<td align="center" valign="middle">'.($i+1).'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->sn.'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->title.'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->key_vals.'</td>
	                    <td align="center" valign="middle">'.getXiaoshu($jilu->num,$product_set->number_num).'</td>
	                    <td align="center" valign="middle">'.getXiaoshu($kucun,$product_set->number_num).'</td>
	                    <td align="center" valign="middle">'.$jilu->unit.'</td>
	                    <td align="center" valign="middle">'.$jilu->unit_price.'</td>
	                    <td align="center" valign="middle">'.($jilu->unit_price*$jilu->num).'</td>
	                    <td align="center" valign="middle">'.getXiaoshu($jilu->hasNum,$product_set->number_num).'</td>
	                    <td align="center" valign="middle">'.getXiaoshu($jilu->tuihuoNum,$product_set->number_num).'</td>
	                </tr>';
	                }
	                $str.='</table>
	        </div>
	    </div>
		<div class="ddxx_jibenxinxi_5">
	    	<div class="ddxx_jibenxinxi_5_up">	
	        	备注信息：
	        </div>
	    	<div class="ddxx_jibenxinxi_5_down">
	        	<div class="ddxx_jibenxinxi_5_down_01">
	            	会员备注：'.(empty($order->remark)?'无':$order->remark).'
	            </div>
	        	<div class="ddxx_jibenxinxi_5_down_02">
	            	<div class="ddxx_jibenxinxi_5_down_02_left">
	                	商家备注：
	                </div>
	            	<div class="ddxx_jibenxinxi_5_down_02_right">';
	            	if(!empty($order->beizhu_json)){
	            		$beizhus = json_decode($order->beizhu_json,true);
	            		foreach ($beizhus as $b){
	            			$str.='<div style="padding-bottom:10px;">'.$b['content'].'【'.$b['name'].'&nbsp;&nbsp;'.$b['time'].'】</div>';
	            		}
	            	}
	                $str.='<textarea id="add_order_beizhu_content"></textarea>
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	            <div class="ddxx_jibenxinxi_5_down_03">
	            	<a href="javascript:" onclick="add_order_beizhu('.$id.');">新增备注</a>
	            </div>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}
function order_info_guidang(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from order_guidang$fenbiao where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$price_json = json_decode($order->price_json,true);
	$pay_json = array();
	$fahuo_json = array();
	$shuohuo_json = array();
	if(!empty($order->pay_json))$pay_json=json_decode($order->pay_json,true);
	if(!empty($order->fahuo_json))$fahuo_json=json_decode($order->fahuo_json,true);
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	$user = $db->get_row("select nickname,username,level from users where id=$order->userId");
	if($user->level>0)$user_level = $db->get_var("select title from user_level where id=$user->level");
	$details = $db->get_results("select * from order_detail$fenbiao where orderId=$id order by id asc");
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';
	$str.='<div class="ddxx_jibenxinxi_2">
	    	<div class="ddxx_jibenxinxi_2_01" id="order_info_price">
	        	<div class="ddxx_jibenxinxi_2_01_up">
	            	商品价格
	            </div>
	        	<div class="ddxx_jibenxinxi_2_01_down">
	            	<ul>
	            		<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	订单总额：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>￥'.$order->price.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                    if(!empty($price_json['goods'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品总额：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['goods']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yunfei'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	运费：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['yunfei']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yhq'])){
		                	$yhq_title = $db->get_var("select title from user_yhq$fenbiao where id=".$price_json['yhq']['desc']);
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	优惠券：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['yhq']['price'].'('.$yhq_title.')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao']['price'].'('.$price_json['cuxiao']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao_order'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	订单促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao_order']['price'].'('.$price_json['cuxiao_order']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['admin'])){
		                	if($price_json['admin']['price']>0){
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动优惠：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">-￥'.$price_json['admin']['price'].'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}else{
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动提价：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">+￥'.abs($price_json['admin']['price']).'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}
		                }
		                if(!empty($pay_json)){
	                    $str.='<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	 支付：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">';
	                    		if(!empty($pay_json['jifen'])){
	                    			$str.='积分抵现 <b>￥'.$pay_json['jifen']['price'].'</b>('.$pay_json['admin']['desc'].'积分)<br>';
	                    		}
	                    		if(!empty($pay_json['yue'])){
	                    			$str.='余额支付 <b>￥'.$pay_json['yue']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['weixin'])){
	                    			$str.='微信支付 <b>￥'.$pay_json['weixin']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['applet'])){
	                    			$str.='小程序支付 <b>￥'.$pay_json['applet']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['alipay'])){
	                    			$str.='支付宝支付 <b>￥'.$pay_json['alipay']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['cash'])){
	                    			$str.='现金支付 <b>￥'.$pay_json['cash']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['paypal'])){
	                    			$str.='银联支付 <b>￥'.$pay_json['paypal']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka'])){
	                    			$str.='抵扣金支付 <b>￥'.$pay_json['lipinka']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka1'])){
	                    			$str.='礼品卡支付 <b>￥'.$pay_json['lipinka1']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['other'])){
	                    			$str.='其他支付 <b>￥'.$pay_json['other']['price'].'</b>('.$pay_json['ohter']['desc'].')<br>';
	                    		}
	                        $str.='</div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                }
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_02" id="order_info_fapiao">
	        	<div class="ddxx_jibenxinxi_2_02_up">
	            	<div class="ddxx_jibenxinxi_2_02_up_left">
	                	发票信息
	                </div>
	            	<div class="ddxx_jibenxinxi_2_02_up_right">
	                	'.($order->ifkaipiao==0?'不':'').'需要开票
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_02_down">
	            	<ul>';
	            	if($order->ifkaipiao>0){
	            		$fapiao_json = json_decode($order->fapiao_json);
	            		foreach ($fapiao_json as $key => $val){
	            			$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">'.$key.'：</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right">'.$val.'</div>
                            	<div class="clearBoth"></div>
                            </li>';
	            		}
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_03">	
	        	<div class="ddxx_jibenxinxi_2_03_up">	
	            	其它信息
	            </div>
	        	<div class="ddxx_jibenxinxi_2_03_down">
	            	<div class="ddxx_jibenxinxi_2_03_down_1">
	                	<ul>';
	                	$str.='<li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员名称：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->nickname.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员级别：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user_level.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">手机号：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->username.'</div>
	                            <div class="clearBoth"></div>
	                        </li>';
	                    $str.='</ul>
	                </div>
	            	<div class="ddxx_jibenxinxi_2_03_down_2">
	                	<ul>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">可得积分：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$order->jifen.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">配送方式：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$fahuo_json['kuaidi_type'].'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">商品重量：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$order->weight.$product_set->weight.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_04" id="order_info_shouhuo">
	        	<div class="ddxx_jibenxinxi_2_04_up">
	            	<div class="ddxx_jibenxinxi_2_04_up_left">
	                	收货人信息 <img src="images/dingdanxx_12.png" data-show="0" onclick="toggle_shouhuo_info(this);"/>
	                </div>
	            	<div class="ddxx_jibenxinxi_2_04_up_right">
	                	<a href="javascript:" id="copy_order_shouhuo" data-clipboard-text="'.$shuohuo_json['所在地区'].'，'.$shuohuo_json['详细地址'].'，'.$shuohuo_json['收件人'].'，'.$shuohuo_json['手机号'].'">复制收货人信息</a> <img src="images/dingdanxx_14.png" onmouseover="tips(this,\'此功能按照将收货人信息整合后复制到剪贴板，方便店主粘贴至目标位置，<br>如：给顾客确认地址的邮件<br>复制格式：<br>地区，地址，姓名，手机号\',1);" onmouseout="hideTips()"/>
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_04_down">
	            	<ul>';
	            	foreach ($shuohuo_json as $key => $val){
	            		if($key=='收件人'){
	            			$username = $val;
	            			$val = substr($val,0,3).'*'.substr($val,-3);
	            		}else if($key=='手机号'){
	            			$phone = $val;
	            			$val = substr($val,0,3).'****'.substr($val,-4);
	            		}
	            		$str.='<li>
		            		<div class="ddxx_jibenxinxi_2_01_down_left">'.$key.'：</div>
		            		<div class="ddxx_jibenxinxi_2_01_down_right" '.($key=='收件人'?'id="order_shoujianren" data-val="'.$username.'" data-hide="'.$val.'"':($key='手机号'?'id="order_shoujihao" data-val="'.$phone.'" data-hide="'.$val.'"':'')).'>'.$val.'</div>
		            		<div class="clearBoth"></div>
	            		</li>';
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_4">
	    	<div class="ddxx_jibenxinxi_4_up">
	        	订单明细：
	        </div>
	    	<div class="ddxx_jibenxinxi_4_down">
	        	<table width="100%" border="0" cellpadding="0" cellspacing="0">	
	            	<tr height="34">
	                	<td align="center" width="34" valign="middle" class="ddxx_jibenxinxi_4_down_bj"></td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品编码</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品名称</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">规格</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">数量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单位</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单价</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">小计</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">已发货量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">已退货量</td>
	                </tr>';
	                foreach ($details as $i=>$jilu){
	                	$pdtInfo = json_decode($jilu->pdtInfo);
	                	$str.='<tr height="34">
		                	<td align="center" valign="middle">'.($i+1).'</td>
		                    <td align="center" valign="middle">'.$pdtInfo->sn.'</td>
		                    <td align="center" valign="middle">'.$pdtInfo->title.'</td>
		                    <td align="center" valign="middle">'.$pdtInfo->key_vals.'</td>
		                    <td align="center" valign="middle">'.$jilu->num.'</td>
		                    <td align="center" valign="middle">'.$jilu->unit.'</td>
		                    <td align="center" valign="middle">'.$jilu->unit_price.'</td>
		                    <td align="center" valign="middle">'.($jilu->unit_price*$jilu->num).'</td>
		                    <td align="center" valign="middle">'.getXiaoshu($jilu->hasNum,$product_set->number_num).'</td>
		                    <td align="center" valign="middle">'.getXiaoshu($jilu->tuihuoNum,$product_set->tuihuoNum).'</td>
		                </tr>';
	                }
	                $str.='</table>
	        </div>
	    </div>
		<div class="ddxx_jibenxinxi_5">
	    	<div class="ddxx_jibenxinxi_5_up">	
	        	备注信息：
	        </div>
	    	<div class="ddxx_jibenxinxi_5_down">
	        	<div class="ddxx_jibenxinxi_5_down_01">
	            	会员备注：'.(empty($order->remark)?'无':$order->remark).'
	            </div>
	        	<div class="ddxx_jibenxinxi_5_down_02">
	            	<div class="ddxx_jibenxinxi_5_down_02_left">
	                	商家备注：
	                </div>
	            	<div class="ddxx_jibenxinxi_5_down_02_right">';
	            	if(!empty($order->beizhu_json)){
	            		$beizhus = json_decode($order->beizhu_json,true);
	            		foreach ($beizhus as $b){
	            			$str.='<div style="padding-bottom:10px;">'.$b['content'].'【'.$b['name'].'&nbsp;&nbsp;'.$b['time'].'】</div>';
	            		}
	            	}
	                $str.='</div>
	            	<div class="clearBoth"></div>
	            </div>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}
function order_tuihuan_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$tuihuans = $db->get_results("select * from order_tuihuan where comId=$comId and id=$id order by id desc limit 1");
	$lastStatus = $tuihuans[0]->status;
	if(empty($tuihuans)){
		$str = '<div class="ddxx_tuihuanhuoguanli">
	            	<div class="ddxx_tuihuanhuoguanli_1">
	                	退换货信息 ';	                	 
            $str.='</div>
            <div class="ddxx_yichangchuli_wu_2">
                <img src="images/dingdanxx_15.png"/> 暂无任何数据
            </div>
        </div>';
	}else{
		foreach ($tuihuans as $tuihuan) {
			$u = $db->get_row("select username,nickname from users where id=$tuihuan->userId");
			$str = '<div class="ddxx_tuihuanhuoguanli">
	            	<div class="ddxx_tuihuanhuoguanli_1">
	                	退换货商品信息 ';
	    	switch ($tuihuan->status){
	    		case 1:
	    			$str.='<span>待审核</span>';
	    		break;
	    		case 2:
	    			$str.='<span>退货待收货</span>';
	    		break;
	    		case 3:
	    			$str.='<span>待退款</span>';
	    		break;
	    		case 4:
	    			$str.='<span>换货待发货</span>';
	    		break;
	    		case 5:
	    			$str.='<span>待客户收货</span>';
	    		break;
	    		case 6:
	    			$str.='<span>已完成</span>';
	    		break;
	    		case -1:
	    			$str.='<span>已驳回</span>';
	    		break;
	    	}
	    	$pdts[] = json_decode($tuihuan->pdtInfo,true);
	    	$imgs = array();

	    	if(!empty($tuihuan->images))$imgs = explode('|', $tuihuan->images);
	        $str.='</div>
	           <div class="ddxx_tuihuanhuoguanli_2">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="36">
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	商品编号
                            </td>
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	商品名称
                            </td>
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	商品规格
                            </td>
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	商品价格
                            </td>
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	数量
                            </td>
                            <td align="center" valign="middle" class="ddxx_tuihuanhuoguanli_2_title"> 	
                            	退款金额
                            </td>
                        </tr>';
                        if(!empty($pdts)){
                        	foreach ($pdts as $pdt) {
                        		$str.='<tr height="36">
		                            <td align="center" valign="middle">'.$pdt['sn'].'</td>
		                            <td align="center" valign="middle">'.$pdt['title'].'</td>
		                            <td align="center" valign="middle">'.$pdt['key_vals'].'</td>
		                            <td align="center" valign="middle">'.$pdt['price_sale'].'</td>
		                            <td align="center" valign="middle">'.$pdt['num'].'</td>
		                            <td align="center" valign="middle">'.$tuihuan->tk_price.'</td>
		                        </tr>';
                        	}
                        }
                    $str.='</table>
                </div>
            	<div class="ddxx_tuihuanhuoguanli_3">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="40">
                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">退换货编号：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">'.$tuihuan->sn.'</div>
                            </td>
                            <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;"> 订单号：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">'.$tuihuan->order_orderId.'</div>
                            </td>
                        </tr>
                        <tr height="40">
                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">申请类型：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;"> '.($tuihuan->type==1?'退款':($tuihuan->type==2?'退货退款':'换货')).'</div>
                            </td>
                            <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;"> 申请时间：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">'.date("Y-m-d H:i",strtotime($tuihuan->dtTime)).'</div>
                            </td>
                        </tr>
                        <tr height="40">
                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">申请原因：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;"><b>'.$tuihuan->reason.'</b></div>
                            </td>
                            <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;"> 申请人：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">'.$u->nickname.'('.$u->username.')'.'</div>
                            </td>
                        </tr>
                        <tr height="84">
                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">图片凭证：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">';
                            	if(!empty($imgs)){
                            		foreach ($imgs as $img){
                            			$str.='<a href="'.$img.'" target="_blank"><img src="'.$img.'?x-oss-process=image/resize,w_70" height="70"></a>';
                            		}
                            	}
                            	$str.='</div>
                            </td>
                            <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">  说明：</div>
                            </td>
                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle">
                            	<div style="padding-left:2%;">'.$tuihuan->remark.'</div>
                            </td>
                        </tr>';
                        if($tuihuan->type>1){
                        	$kuaidi_json = array();
                        	$shouhuo_json =array();
                        	$fahuo_json = array();
                        	if(!empty($tuihuan->kuaidi_json))$kuaidi_json = json_decode($tuihuan->kuaidi_json,true);
                        	if(!empty($tuihuan->shouhuo_json))$shouhuo_json=json_decode($tuihuan->shouhuo_json,true);
                        	if(!empty($tuihuan->fahuo_json))$fahuo_json=json_decode($tuihuan->fahuo_json,true);
                        	$str.='<tr>
	                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
	                            	<div style="padding-right:2%;">客户运费：</div>
	                            </td>
	                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle"><div style="padding-left:10px;line-height:25px;">
	                            	'.($tuihuan->kuaidi_type==2?'卖家承担<span style="color:red;font-weight:bold">￥'.$tuihuan->kuaidi_money.'</span>':'买家自行承担').'
	                            </div></td>
	                            <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
	                            	<div style="padding-right:2%;">物流信息：</div>
	                            </td>
	                            <td width="40%" bgcolor="#ffffff" align="left" valign="middle"><div style="padding-left:10px;line-height:25px;">
	                            	'.(empty($shouhuo_json)?'':'退货地址:'.$shouhuo_json['address'].'('.$shouhuo_json['name'].'&nbsp;&nbsp;'.$shouhuo_json['phone'].')'.'<br>').(empty($kuaidi_json)?'':'快递公司：'.$kuaidi_json['company'].'&nbsp;&nbsp;快递单号：'.$kuaidi_json['orderId']).'
	                            </div></td>
                            <tr>';
                            if(!empty($fahuo_json)){
                            	$str.='<tr height="40">
		                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
		                            	<div style="padding-right:2%;">新的发货信息：</div>
		                            </td>
		                            <td colspan="3"><div style="padding-left:10px;line-height:25px;">
		                            	快递公司：'.$fahuo_json['company'].'&nbsp;&nbsp;快递单号：'.$fahuo_json['orderId'].'
		                            </div></td>
	                            </tr>';
                            }
                        }
                        $str.='<tr>
                        	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
                            	<div style="padding-right:2%;">处理记录：</div>
                            </td>
                            <td colspan="3"><div style="padding-left:10px;line-height:25px;">';
                            if(!empty($tuihuan->genjin_json)){
                            	$gengjins = json_decode($tuihuan->genjin_json,true);
                            	foreach ($gengjins as $genjin){
	                            	$str.=$genjin['content'].'【'.$genjin['name'].'&nbsp;&nbsp;'.$genjin['time'].'】<br>';
	                            }
                            }
                            $str.='</div></td>
                        </tr>';
                        if($tuihuan->status==1){
                        	$str.='<tr height="40">
	                        	<td width="10%" colspan="4" bgcolor="#ffffff" align="left" valign="middle">
	                            	<div class="ddxx_tuihuanhuoguanli_3_03">
	                                	<a href="javascript:" onclick="tuihuan_shenhe('.$tuihuan->tk_price.');" class="ddxx_tuihuanhuoguanli_3_03_1">审批通过</a>
	                                    <a href="javascript:" onclick="tuihuan_quxiao('.$tuihuan->id.');" class="ddxx_tuihuanhuoguanli_3_03_2">审批驳回</a>
	                                </div>
	                            </td>
	                        </tr>';
                        }else if($tuihuan->status==2){
                        	$str.='<tr height="40">
	                        	<td width="10%" colspan="4" bgcolor="#ffffff" align="left" valign="middle">
	                            	<div class="ddxx_tuihuanhuoguanli_3_03">
	                                	<a href="javascript:" onclick="tuihuan_shenhe(1,'.$tuihuan->id.');" class="ddxx_tuihuanhuoguanli_3_03_1">确认收货</a>
	                                </div>
	                            </td>
	                        </tr>';
                        }else if($tuihuan->status==3){
                        	$str.='<tr height="40">
	                        	<td width="10%" colspan="4" bgcolor="#ffffff" align="left" valign="middle">
	                            	<div class="ddxx_tuihuanhuoguanli_3_03">
	                                	<a href="javascript:" onclick="tuihuan_wancheng('.$tuihuan->id.');" class="ddxx_tuihuanhuoguanli_3_03_1">退款完成</a>
	                                </div>
	                            </td>
	                        </tr>';
                        }else if($tuihuan->status==4){
                        	$str.='<tr height="40">
	                        	<td width="10%" colspan="4" bgcolor="#ffffff" align="left" valign="middle">
	                            	<div class="ddxx_tuihuanhuoguanli_3_03">
	                                	<a href="javascript:" onclick="tuihuan_fahuo('.$tuihuan->id.');" class="ddxx_tuihuanhuoguanli_3_03_1">换货发货</a>
	                                </div>
	                            </td>
	                        </tr>';
                        }
                    $str.='</table>
                </div>
	        </div>';
			}
	}
	echo $str;
	exit;
}
function order_service_index(){
	global $db,$request,$arr;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$status = $db->get_var("select status from order$fenbiao where id=$id");
	$services = $db->get_results("select * from order_service where comId=$comId and orderId=$id order by id desc");
	if(empty($services)){
		$str = '<div class="ddxx_dingdanfuwu">
	            	<div class="ddxx_dingdanfuwu_1">
	                	<div class="ddxx_dingdanfuwu_1_up">
	                    	订单服务 '.(($status!=-1 && $status!=-5 && !empty($status))?chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=order_edit_service" onclick="service_edit('.$id.',0,0);">新增服务</a>',1):'').'
	                    </div>
	                	<div class="ddxx_yichangchuli_wu_2">
	                    	<img src="images/dingdanxx_15.png"/> 暂无任何数据
	                    </div>
	                </div>
                </div>';
	}else{
		$history = false;
		$str = '<div class="ddxx_dingdanfuwu">
	            	<div class="ddxx_dingdanfuwu_1">
	                	<div class="ddxx_dingdanfuwu_1_up">
	                    	订单服务 '.(($status!=-1 && $status!=-5 && !empty($status))?chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=order_edit_service" onclick="service_edit('.$id.',0,0);">新增服务</a>',1):'').'
	                    </div>';
	                    foreach ($services as $service){
	                    	if($service->status==1||$service->status==2){
	                    		$str.='<div class="ddxx_dingdanfuwu_1_down">
			                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
			                        	<tbody><tr height="43">
			                            	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;">服务订单编号：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->sn.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 订单号：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->orderId.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 提单时间：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.date("Y-m-d H:i",strtotime($service->dtTime)).'</div>
			                                </td>
			                            </tr>
			                            <tr height="43">
			                            	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 联系人：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->name.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 联系电话：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->phone.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 详细地址：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->address.'</div>
			                                </td>
			                            </tr>
			                            <tr height="43">
			                            	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 服务项目：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%; color:#ff0000"> <b>'.$service->title.'</b></div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;">  费用：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%; color:#ff0000">￥'.$service->price.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 支付状态：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.($service->ispay==1?'已支付':'未支付').'</div>
			                                </td>
			                            </tr>
			                            <tr height="43">
			                            	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 预约服务时间：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.(empty($service->service_time)?'':date('Y-m-d H:i',strtotime($service->service_time))).'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"> 工作人员：</div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->worker_name.'</div>
			                                </td>
			                                <td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;"></div>
			                                </td>
			                                <td width="23%" bgcolor="#ffffff" align="left" valign="middle">
			                                    <div style="padding-left:2%;"></div>
			                                </td>
			                            </tr>
			                            <tr height="43">
			                            	<td width="10%" bgcolor="#e0f4ff" align="right" valign="middle">
			                                    <div style="padding-right:2%;">备注：</div>
			                                </td>
			                                <td width="90%" bgcolor="#ffffff" colspan="5" align="left" valign="middle">
			                                    <div style="padding-left:2%;">'.$service->remark.'</div>
			                                </td>
			                            </tr>
			                            <tr height="43">
			                            	<td width="100%" bgcolor="#ffffff" colspan="6" align="left" valign="middle">
			                                    <div style="padding-left:10%;">';
			                                    if($service->status==2){
			                                    	$str.='<a href="javascript:" onclick="service_done('.$service->id.');">服务完成</a><a href="javascript:" class="btn" onclick="service_shenhe('.$service->id.');">重新分配</a>';
			                                    }else{
			                                    	$str.='<a href="javascript:" onclick="service_shenhe('.$service->id.');">重新分配</a>';
			                                    }
			                                    $str.='<a href="javascript:" class="btn" onclick="service_edit('.$id.','.$service->id.',0);">编辑信息</a>
			                                    <a href="javascript:" class="btn" onclick="service_zuofei('.$service->id.');">服务作废</a></div>
			                                </td>
			                            </tr>
			                        </tbody></table>
			                    </div>';
	                    	}else{
	                    		$history = true;
	                    	}
	                    }
	                $str.='</div>';
	                if($history){
	                	$str.='<div class="ddxx_dingdanfuwu_2">
		                	<div class="ddxx_dingdanfuwu_2_up">
		                    	历史服务
		                    </div>
		                	<div class="ddxx_dingdanfuwu_2_down">
		                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		                        	<tbody><tr height="32">
		                            	<td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">服务单号</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">提单时间</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">服务项目</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">支付费用</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">联系人</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">服务人员</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">完成时间</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">订单状态</td>
		                                <td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title">备注</td>
		                            </tr>';
		                            foreach ($services as $service){
	                    				if($service->status==3||$service->status==-1){
				                            $str.='<tr height="32">
				                            	<td align="center" valign="middle">'.$service->sn.'</td>
				                                <td align="center" valign="middle">'.date("Y-m-d H:i",strtotime($service->dtTime)).'</td>
				                                <td align="center" valign="middle">'.$service->title.'</td>
				                                <td align="center" valign="middle"><div style="color:#ff0000;">￥'.$service->price.'</div></td>
				                                <td align="center" valign="middle">'.$service->name.'('.$service->phone.')</td>
				                                <td align="center" valign="middle">'.$service->worker_name.'</td>
				                                <td align="center" valign="middle">'.date("Y-m-d H:i",strtotime($service->dealTime)).'</td>
				                                <td align="center" valign="middle">'.($service->status==-1?'已作废':'已完成').'</td>
				                                <td align="center" valign="middle">'.$service->remark.'</td>
				                            </tr>';
				                        }
				                    }
		                        $str.='</tbody></table>
		                    </div>
		                </div>';
	                }
                $str.='</div>';
	}
	echo $str;
	exit;
}
function order_error_index(){
	global $db,$request,$arr;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$status = $db->get_var("select status from order$fenbiao where id=$id");
	$errors = $db->get_results("select * from order_error where orderId=$id and comId=$comId order by id desc limit 10");
	if(empty($errors)){
		$str = '<div class="ddxx_yichangchuli">
	            	<div class="ddxx_yichangchuli_wu">
	                	<div class="ddxx_yichangchuli_wu_1">
	                    	订单异常信息 '.(($status==0||$status==1||$status==2||$status==3)?chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=add_error" onclick="order_add_error('.$id.');">新增异常</a>',1):'').'
	                    </div>
	                	<div class="ddxx_yichangchuli_wu_2">
	                    	<img src="images/dingdanxx_15.png"/> 暂无任何数据
	                    </div>
	                </div>
                </div>';
	}else{
		$str = '<div class="ddxx_yichangchuli">
			<div class="ddxx_yichangchuli_wu_1">
                订单异常信息 '.(($status==0||$status==1||$status==2||$status==3)?chekurl($arr,'<a href="javascript:" _href="?m=system&s=order&a=add_error" onclick="order_add_error('.$id.');">新增异常</a>',1):'').'
            </div>';
        foreach ($errors as $e) {
        	$str.='<div class="ddxx_yichangchuli_2">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                	<tr height="40">
                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常时间：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;"> '.date("Y-m-d H:i",strtotime($e->dtTime)).'	</div>
                        </td> 
                        <td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">操作人：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;">'.$e->username.'</div>
                        </td> 
                        <td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常来源流程：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;">'.get_liucheng_title($e->liuchengId).'</div>	
                        </td> 
                    </tr>
                    <tr height="40">
                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常备注：</div>
                        </td>
                        <td bgcolor="#ffffff" width="9%" colspan="5" align="left" valign="middle">
                        	<div style="padding-left:2%;"> '.$e->remark.'</div>
                        </td> 
                    </tr>';
                    if(!empty($e->genjin_json)){
                    	$gengjins = json_decode($e->genjin_json,true);
                    	$str.='<tr height="40">
	                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
	                        	<div style="padding-right:2%;">跟进备注：</div>
	                        </td>
	                        <td bgcolor="#ffffff" width="9%" colspan="5" align="left" valign="middle">
	                        	<div style="padding-left:2%;">';  
	                        	foreach ($gengjins as $genjin){
	                        		$str.=$genjin['content'].'【'.$genjin['name'].'&nbsp;&nbsp;'.$genjin['time'].'】<br>';
	                        	}
	                        	$str.='</div>
	                        </td> 
	                    </tr>';
                    }
                    if($e->status==0){
	                    $str.='<tr height="70">
	                    	<td bgcolor="#ffffff" width="100%" colspan="6" align="left" valign="middle">
	                        	<div class="ddxx_yichangchuli_2_02">
	                            	<input type="text" id="error_beizhu_input" placeholder="请填写异常订单跟进备注"/>
	                                <a href="javascript:" onclick="add_error_beizhu('.$e->id.',0);" class="ddxx_yichangchuli_2_02_1">发布跟进备注</a>
	                                <a href="javascript:" onclick="add_error_beizhu('.$e->id.',1);" class="ddxx_yichangchuli_2_02_2">处理完成</a>
	                            </div>
	                        </td>
	                    </tr>';
                	}
                $str.='</table>
            </div>';
        }
		$str .='</div>';
	}
	echo $str;
	exit;
}
function order_error_guidang(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	//$status = $db->get_var("select status from order_guidang$fenbiao where id=$id");
	$errors = $db->get_results("select * from order_error where orderId=$id and comId=$comId order by id desc limit 10");
	if(empty($errors)){
		$str = '<div class="ddxx_yichangchuli">
	            	<div class="ddxx_yichangchuli_wu">
	                	<div class="ddxx_yichangchuli_wu_1">
	                    	订单异常信息
	                    </div>
	                	<div class="ddxx_yichangchuli_wu_2">
	                    	<img src="images/dingdanxx_15.png"/> 暂无任何数据
	                    </div>
	                </div>
                </div>';
	}else{
		$str = '<div class="ddxx_yichangchuli">
			<div class="ddxx_yichangchuli_wu_1">
                订单异常信息
            </div>';
        foreach ($errors as $e) {
        	$str.='<div class="ddxx_yichangchuli_2">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                	<tr height="40">
                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常时间：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;"> '.date("Y-m-d H:i",strtotime($e->dtTime)).'	</div>
                        </td> 
                        <td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">操作人：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;">'.$e->username.'</div>
                        </td> 
                        <td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常来源流程：</div>
                        </td>
                        <td bgcolor="#ffffff" width="23%" align="left" valign="middle">
                        	<div style="padding-left:2%;">'.get_liucheng_title($e->liuchengId).'</div>	
                        </td> 
                    </tr>
                    <tr height="40">
                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
                        	<div style="padding-right:2%;">异常备注：</div>
                        </td>
                        <td bgcolor="#ffffff" width="9%" colspan="5" align="left" valign="middle">
                        	<div style="padding-left:2%;"> '.$e->remark.'</div>
                        </td> 
                    </tr>';
                    if(!empty($e->genjin_json)){
                    	$gengjins = json_decode($e->genjin_json,true);
                    	$str.='<tr height="40">
	                    	<td bgcolor="#e0f4ff" width="10%" align="right" valign="middle">
	                        	<div style="padding-right:2%;">跟进备注：</div>
	                        </td>
	                        <td bgcolor="#ffffff" width="9%" colspan="5" align="left" valign="middle">
	                        	<div style="padding-left:2%;">';  
	                        	foreach ($gengjins as $genjin){
	                        		$str.=$genjin['content'].'【'.$genjin['name'].'&nbsp;&nbsp;'.$genjin['time'].'】<br>';
	                        	}
	                        	$str.='</div>
	                        </td>
	                    </tr>';
                    }
                $str.='</table>
            </div>';
        }
		$str .='</div>';
	}
	echo $str;
	exit;
}
function order_jilu_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$jilus = $db->get_results("select * from order_jilu$fenbiao where orderId=$id order by type asc,id desc");
	$jilu1 = '<div class="ddxx_caozuojilu_1">
		<div class="ddxx_caozuojilu_1_up">
			本单操作记录
		</div>
		<div class="ddxx_dingdanfuwu_2_down">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
		    	<tbody><tr height="33">
		        	<td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
		            	<div style="padding-left:3%;">操作时间</div>
		            </td>
		            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
		            	<div style="padding-left:3%;">操作人</div>
		            </td>
		            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
		            	<div style="padding-left:3%;">行为</div>
		            </td>
		            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
		            	<div style="padding-left:3%;">操作内容</div>
		            </td>
		        </tr>';
    $nowtype = 1;
    $jilu2 = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			if($jilu->type==$nowtype){
				$bianliang = 'jilu'.$nowtype;
				$$bianliang.='<tr height="34">
                	<td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->username.'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->operate.'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->remark.'</div>
                    </td>
                </tr>';
			}else{
				$nowtype = $jilu->type;
				$bianliang = 'jilu'.$nowtype;
				$$bianliang = '<div class="ddxx_caozuojilu_1">
					<div class="ddxx_caozuojilu_1_up">
						发货操作记录
					</div>
					<div class="ddxx_dingdanfuwu_2_down">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
					    	<tbody><tr height="33">
					        	<td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作时间</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作人</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">行为</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作内容</div>
					            </td>
					        </tr>
					        <tr height="34">
			                	<td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->username.'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->operate.'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->remark.'</div>
			                    </td>
			                </tr>';
			}
		}
	}
	$jilu1.='</tbody></table></div></div>';
    if(!empty($jilu2)){
    	$jilu2.='</tbody></table></div></div>';
    }
    echo $jilu1.$jilu2;
    exit;
}
function order_info_getedit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from order$fenbiao where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$price_json = json_decode($order->price_json,true);
	$pay_json = array();
	$fahuo_json = array();
	$shuohuo_json = array();
	if(!empty($order->pay_json))$pay_json=json_decode($order->pay_json,true);
	if(!empty($order->fahuo_json))$fahuo_json=json_decode($order->fahuo_json,true);
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	if($order->ifkaipiao>0)$fapiao_json = json_decode($order->fapiao_json,true);
	$areaId = $order->areaId;
	$firstId=0;
	$secondId=0;
	$thirdId=0;
	$areas = $db->get_results("select * from demo_area where parentId=0");
	if($areaId>0){
		$area = $db->get_row("select * from demo_area where id=".$areaId);
		if($area->parentId==0){
			$firstId = $area->id;
		}else{
			$firstId = $area->parentId;
			$secondId = $area->id;
			$farea = $db->get_row("select * from demo_area where id=".$area->parentId);
			if($farea->parentId!=0){
				$firstId = $farea->parentId;
				$secondId = $farea->id;
				$thirdId=$area->id;
			}
		}
	}
	if($secondId>0){
		$areas2 = $db->get_results("select * from demo_area where parentId=$secondId");
	}
	if($firstId>0){
		$areas1 = $db->get_results("select * from demo_area where parentId=$firstId");
	}
	$areastr = '';$areastr1 = '<option value="">请选择市</option>';$areastr2 = '<option value="">请选择区</option>';
	foreach ($areas as $a){
		$areastr.='<option value="'.$a->id.'" '.($a->id==$firstId?'selected="true"':'').'>'.$a->title.'</option>';
	}
	if(!empty($areas1)){
		foreach ($areas1 as $a){
			$areastr1.='<option value="'.$a->id.'" '.($a->id==$secondId?'selected="true"':'').'>'.$a->title.'</option>';
		}
	}
	if(!empty($areas2)){
		foreach ($areas2 as $a){
			$areastr2.='<option value="'.$a->id.'" '.($a->id==$thirdId?'selected="true"':'').'>'.$a->title.'</option>';
		}
	}
	$price_str = '<div class="ddxx_jibenxinxi_2_01_up">商品价格</div>
	<div class="ddxx_jibenxinxi_2_01_down">
    	<ul>
    		<li>
            	<div class="ddxx_jibenxinxi_2_01_down_left">订单总额：</div>
            	<div class="ddxx_jibenxinxi_2_01_down_right"><b>￥'.$order->price.'</b></div>
            	<div class="clearBoth"></div>
            </li>';
            if(!empty($price_json['goods'])){
            	$price_str.='<li>
                	<div class="ddxx_jibenxinxi_2_01_down_left">
                    	商品总额：
                    </div>
                	<div class="ddxx_jibenxinxi_2_01_down_right">
                    	+￥'.$price_json['goods']['price'].'
                    </div>
                	<div class="clearBoth"></div>
                </li>';
            }
            if(!empty($price_json['yunfei'])){
            	$price_str.='<li>
                	<div class="ddxx_jibenxinxi_2_01_down_left">
                    	运费：
                    </div>
                	<div class="ddxx_jibenxinxi_2_01_down_right">
                    	+￥'.$price_json['yunfei']['price'].'
                    </div>
                	<div class="clearBoth"></div>
                </li>';
            }
            if(!empty($price_json['yhq'])){
            	$yhq_title = $db->get_var("select title from user_yhq$fenbiao where id=".$price_json['yhq']['desc']);
            	$price_str.='<li>
                	<div class="ddxx_jibenxinxi_2_01_down_left">
                    	优惠券：
                    </div>
                	<div class="ddxx_jibenxinxi_2_01_down_right">
                    	-￥'.$price_json['yhq']['price'].'('.$yhq_title.')
                    </div>
                	<div class="clearBoth"></div>
                </li>';
            }
            if(!empty($price_json['cuxiao'])){
            	$price_str.='<li>
                	<div class="ddxx_jibenxinxi_2_01_down_left">
                    	商品促销：
                    </div>
                	<div class="ddxx_jibenxinxi_2_01_down_right">
                    	-￥'.$price_json['cuxiao']['price'].'('.$price_json['cuxiao']['desc'].')
                    </div>
                	<div class="clearBoth"></div>
                </li>';
            }
            if(!empty($price_json['cuxiao_order'])){
            	$price_str.='<li>
                	<div class="ddxx_jibenxinxi_2_01_down_left">
                    	订单促销：
                    </div>
                	<div class="ddxx_jibenxinxi_2_01_down_right">
                    	-￥'.$price_json['cuxiao_order']['price'].'('.$price_json['cuxiao_order']['desc'].')
                    </div>
                	<div class="clearBoth"></div>
                </li>';
            }
            if(!empty($price_json['admin'])){
            	if($price_json['admin']['price']>0){
            		$price_str.='<li>
	            		<div class="ddxx_jibenxinxi_2_01_down_left">手动优惠：</div>
	            		<div class="ddxx_jibenxinxi_2_01_down_right">-￥'.$price_json['admin']['price'].'</div>
            			<div class="clearBoth"></div>
            		</li>';
            	}else{
            		$price_str.='<li>
	            		<div class="ddxx_jibenxinxi_2_01_down_left">手动提价：</div>
	            		<div class="ddxx_jibenxinxi_2_01_down_right">+￥'.abs($price_json['admin']['price']).'</div>
	            		<div class="clearBoth"></div>
            		</li>';
            	}
            }
            if(!empty($pay_json)){
            $price_str.='<li><div class="ddxx_jibenxinxi_2_01_down_left">支付：</div>
            	<div class="ddxx_jibenxinxi_2_01_down_right">';
        		if(!empty($pay_json['jifen'])){
        			$price_str.='积分抵现 <b>￥'.$pay_json['jifen']['price'].'</b>('.$pay_json['admin']['desc'].'积分)<br>';
        		}
        		if(!empty($pay_json['yue'])){
        			$price_str.='余额支付 <b>￥'.$pay_json['yue']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['weixin'])){
        			$price_str.='微信支付 <b>￥'.$pay_json['weixin']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['applet'])){
        			$str.='小程序支付 <b>￥'.$pay_json['applet']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['alipay'])){
        			$price_str.='支付宝支付 <b>￥'.$pay_json['alipay']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['cash'])){
        			$price_str.='现金支付 <b>￥'.$pay_json['cash']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['paypal'])){
        			$price_str.='银联支付 <b>￥'.$pay_json['paypal']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['lipinka'])){
        			$price_str.='抵扣金支付 <b>￥'.$pay_json['lipinka']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['lipinka1'])){
        			$str.='礼品卡支付 <b>￥'.$pay_json['lipinka1']['price'].'</b><br>';
        		}
        		if(!empty($pay_json['other'])){
        			$price_str.='其他支付 <b>￥'.$pay_json['other']['price'].'</b>('.$pay_json['ohter']['desc'].')<br>';
        		}
            $price_str.='</div><div class="clearBoth"></div></li>';
        }
    if($order->ispay==0){
    	$price_min = 0;
    	if(!empty($pay_json['jifen'])){
    		$price_min += $pay_json['jifen']['price'];
    	}
    	if(!empty($pay_json['yue'])){
    		$price_min += $pay_json['yue']['price'];
    	}
    	$price_str.='<li>
        	<div class="ddxx_jibenxinxi_2_01_down_left">待付款金额：</div>
        	<div class="ddxx_jibenxinxi_2_01_down_right"><input id="order_edit_price" value="'.($order->price-$price_min).'" type="number" class="ddxx_jibenxinxi_2_02_down_right_input"></div>
        	<div class="clearBoth"></div>
        </li>';
    }
   	$price_str.='</ul></div>';
   	$fapiao_str = '<form id="order_fapiao_form" class="layui-form"><div class="ddxx_jibenxinxi_2_02_up">
		<div class="ddxx_jibenxinxi_2_02_up_left">发票信息</div>
        <div class="ddxx_jibenxinxi_2_02_up_right" style="padding-top:8px;">
            <input type="checkbox" name="ifkaipiao" value="1" '.($order->ifkaipiao>0?'checked="true"':'').' lay-skin="primary" title="开票" />
        </div>
        <div class="clearBoth"></div>
    </div>';
    $fapiao_str .='<div class="ddxx_jibenxinxi_2_02_down">
        	<ul>
        		<li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	开票方式：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	 <input type="radio" name="kaipiao_type" value="1" '.($order->ifkaipiao==1?'checked="true"':'').' title="纸质">
                         <input type="radio" name="kaipiao_type" value="2" '.($order->ifkaipiao==2?'checked="true"':'').' title="电子">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	发票类型：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right" style="width:66%">
                		<select name="fapiao_json[发票类型]"><option value="普通发票" '.($fapiao_json['发票类型']=='普通'?'selected="true"':'').'>普通发票</option><option value="增值税发票" '.($fapiao_json['发票类型']=='增值税发票'?'selected="true"':'').'>增值税发票</option></select>         
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	开票内容：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                		<input type="radio" name="fapiao_json[开票内容]" value="商品名称" '.($fapiao_json['开票内容']=='商品名称'?'checked="true"':'').' title="商品名称">
                        <input type="radio" name="fapiao_json[开票内容]" value="商品类别" '.($fapiao_json['开票内容']=='商品类别'?'checked="true"':'').' title="商品类别">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	发票抬头：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[发票抬头]" value="'.$fapiao_json['发票抬头'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	税 号：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[税号]" value="'.$fapiao_json['税号'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	开户行：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[开户行]" value="'.$fapiao_json['开户行'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	账 号：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[账号]" value="'.$fapiao_json['账号'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	电 话：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[电话]" value="'.$fapiao_json['电话'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="ddxx_jibenxinxi_2_02_down_left">
                    	地 址：
                    </div>
                	<div class="ddxx_jibenxinxi_2_02_down_right">
                    	<input type="text" name="fapiao_json[地址]" value="'.$fapiao_json['地址'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
                    </div>
                	<div class="clearBoth"></div>
                </li>
        	</ul>
        </div></form>';
        $shouhuo_str = '<div class="ddxx_jibenxinxi_2_04_up">
        	<div class="ddxx_jibenxinxi_2_04_up_left">收货人信息</div>
        	<div class="ddxx_jibenxinxi_2_04_up_right"></div>
        	<div class="clearBoth"></div>
        </div>';
        $shouhuo_str .= '<form id="order_shouhuo_form" class="layui-form"><div class="ddxx_jibenxinxi_2_04_down">
        	<input type="hidden" name="areaId" id="order_areaId" value="'.$order->areaId.'">
	    	<ul>
	    		<li>
	            	<div class="ddxx_jibenxinxi_2_01_down_left">收件人：</div>
	            	<div class="ddxx_jibenxinxi_2_01_down_right">
	                	<input type="text" name="shuohuo_json[收件人]" id="order_edit_shoujianren" value="'.$shuohuo_json['收件人'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
	                </div>
	            	<div class="clearBoth"></div>
	            </li>
	            <li>
	            	<div class="ddxx_jibenxinxi_2_01_down_left">手机号：</div>
	            	<div class="ddxx_jibenxinxi_2_01_down_right">
	                	<input type="text" name="shuohuo_json[手机号]" id="order_edit_phone" value="'.$shuohuo_json['手机号'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
	                </div>
	            	<div class="clearBoth"></div>
	            </li>
	            <li>
	            	<div class="ddxx_jibenxinxi_2_01_down_left">所在地区：</div>
	            	<div class="ddxx_jibenxinxi_2_01_down_right" style="width:54%;">
	                	<select id="order_ps1" lay-filter="order_ps1">'.$areastr.'</select><select id="order_ps2" lay-filter="order_ps2">'.$areastr1.'</select><select id="order_ps3" lay-filter="order_ps3">'.$areastr2.'</select>
	                </div>
	            	<div class="clearBoth"></div>
	            </li>
	            <li>
	            	<div class="ddxx_jibenxinxi_2_01_down_left">
	                	 详细地址：
	                </div>
	            	<div class="ddxx_jibenxinxi_2_01_down_right">
	                	<input type="text" name="shuohuo_json[详细地址]" id="order_edit_address" value="'.$shuohuo_json['详细地址'].'" class="ddxx_jibenxinxi_2_02_down_right_input">
	                </div>
	            	<div class="clearBoth"></div>
	            </li>
	    	</ul>
	    </div>';
	    $return_arr = array();
	    $return_arr['code'] = 1;
	    $return_arr['price_str'] = $price_str;
	    $return_arr['fapiao_str'] = $fapiao_str;
	    $return_arr['shouhuo_str'] = $shouhuo_str;
	    echo json_encode($return_arr,JSON_UNESCAPED_UNICODE);
	    exit;
}
function add_order_beizhu(){
	global $db,$request;
	$jiluId = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluOrder = $db->get_row("select id,beizhu_json from order$fenbiao where id=$jiluId and comId=$comId limit 1");
	if(!empty($jiluOrder)){
		$results = array();
		if(!empty($jiluOrder->beizhu_json)){
			$results = json_decode($jiluOrder->beizhu_json,true);
		}
		$fankui = array();
		$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['cont']);
		$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
		$fankui['time'] = date('Y-m-d H:i:s');
		array_unshift($results,$fankui);
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update order$fenbiao set beizhu_json='$resultstr' where id=$jiluId");
		$fankui['content'] = str_replace('\n','<br>',$fankui['content']);
		$fankui['content'] = str_replace('"','',$fankui['content']);
		$fankui['content'] = str_replace("'",'',$fankui['content']);
		echo '{"code":1,"message":"<div style=\"padding-bottom:10px;\">'.$fankui['content'].'【'.$fankui['name'].'&nbsp;&nbsp;'.$fankui['time'].'】</div>"}';
	}else{
		echo '{"code":0,"message":"记录不存在"}';
	}
	exit;
}
function pi_shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilus = $db->get_results("select id,status from order$fenbiao where id in($ids) and comId=$comId and status in(0,1)");
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			if($jilu->status==0){
				$status = 1;
				//如果不需要财务审核改成2if($liucheng['if_caiwu']==0)$status=2
				$jiluId = $jilu->id;
				$statusType='订单审核';
				$content = str_replace('订单','订单已通过',$statusType);
				$db->query("update order$fenbiao set status=$status where id=$jiluId");
				$db->query("update order_detail$fenbiao set status=1 where orderId=$jiluId");
				addTaskMsg(31,$jiluId,'有新的订单需要您进行财务审核，请及时处理！');
				addJilu($jiluId,$fenbiao,1,$statusType,$content);
			}else{
				$jiluId = $jilu->id;
				$statusType='订单财务审核';
				$content = str_replace('订单','订单已通过',$statusType);
				$db->query("update order$fenbiao set status=2 where id=$jiluId");
				$db->query("update order_detail$fenbiao set status=1 where orderId=$jiluId");
				addJilu($jiluId,$fenbiao,1,$statusType,$content);
				addTaskMsg(33,$jiluId,'有新的订单需要发货，请及时处理！');
			}
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function pi_shenhe_tuihuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$time = date('Y-m-d H:i:s');
	$jilus = $db->get_results("select id,status,type,genjin_json,orderId from order_tuihuan where id in($ids) and comId=$comId and status in(1,2)");
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			$jiluId = $jilu->id;
			$results = array();
			if(!empty($jilu->genjin_json)){
				$results = json_decode($jilu->genjin_json,true);
			}
			$fankui = array();
			$fankui['name'] = $username;
			$fankui['time'] = $time;
			$newstatus = 3;
			$task_cont = '有新的退换货订单需要进行退款操作，请及时处理！';
			if($jilu->status==1){
				$fankui['content'] = '申请已通过审核';
				if($jilu->type>1){
					$newstatus = 2;
				}
			}else{
				$fankui['content'] = '已确认收到退货';
				if($jilu->type==3){
					$newstatus = 4;
					$task_cont = '有新的退换货订单需要进行发货，请及时处理！';
					//待加：添加发货单
				}
			}
			$results[] = $fankui;
			$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
			$db->query("update order_tuihuan set status=$newstatus,genjin_json='$resultstr' where id=$jiluId");
			$db->query("update order$fenbiao set status=-3 where id=$jilu->orderId");
			if($newstatus==3||$newstatus==4){
				addTaskMsg(34,$jilu->orderId,$task_cont);
			}
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function pi_tuikuan_tuihuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$time = date('Y-m-d H:i:s');
	$jilus = $db->get_results("select id,status,type,genjin_json,userId,orderId from order_tuihuan where id in($ids) and comId=$comId and status =3");
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			$jiluId = $jilu->id;
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
			$db->query("update order_tuihuan set status=$newstatus,genjin_json='$resultstr' where id=$jiluId");
			addUserMsg($jilu->userId,$fenbiao,'您的退换货申请已完成，请检查退款情况',1,$jilu->orderId);
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function shenhe_tuihuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$tuihuanId = (int)$request['tuihuanId'];
	$tk_price = (int)$request['tk_price'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$time = date('Y-m-d H:i:s');
	$jilu = $db->get_row("select id,status,type,genjin_json,orderId,money from order_tuihuan where id=$tuihuanId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($tk_price >$jilu->money ){
		echo '{"code":0,"message":"退款金额不能大于商品金额"}';
		exit;
	}
	if(empty($tk_price)){
	    $tk_price = $jilu->money;
	}
	if($jilu->status>2){
		echo '{"code":0,"message":"该退换货订单不需要再审核了"}';
		exit;
	}
	$results = array();
	if(!empty($jilu->genjin_json)){
		$results = json_decode($jilu->genjin_json,true);
	}
	$fankui = array();
	$fankui['name'] = $username;
	$fankui['time'] = $time;
	$newstatus = 3;
	$task_cont = '有新的退换货订单需要进行退款操作，请及时处理！';
	if($jilu->status==1){
		$fankui['content'] = '申请已通过审核';
		if($jilu->type>1){
			$newstatus = 2;
		}
	}else{
		$fankui['content'] = '已确认收到退货';
		if($jilu->type==3){
			$newstatus = 4;
			$task_cont = '有新的退换货订单需要进行发货，请及时处理！';
			//待加：添加发货单
		}
	}
	$results[] = $fankui;
	$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
	$db->query("update order_tuihuan set status=$newstatus,genjin_json='$resultstr',tk_price = '$tk_price' where id=$tuihuanId");
	$db->query("update order$fenbiao set status=-3 where id=$jilu->orderId");
	if($newstatus==3||$newstatus==4){
		addTaskMsg(34,$jilu->orderId,$task_cont);
	}
	echo '{"code":1}';
}

//TODO 售后的金额该如何处理
function wancheng_tuihuan(){
	global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$tuihuanId = (int)$request['tuihuanId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
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
	if($jilu->type==2 || $jilu->type==1){
		$orderId = $jilu->orderId;
		$order = $db->get_row("select * from order$fenbiao where id=$jilu->orderId"); 
		$db->query("update order$fenbiao set remark='订单已退款',price_tuikuan='$jilu->money' where id=$orderId");
		$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
		
		if($jilu->kuaidi_type==2 && $jilu->kuaidi_money>0){
		
			$userId = $order->userId;
			$db->query("update users set money=money+$jilu->kuaidi_money where id=$order->userId");
			$yue = $db->get_var("select money from users where id=$order->userId");
			
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
	
		}
        
        //todo 减去佣金记录
        /**
         * 1.减去 个人业绩累计  减去下级业绩累计  减去下下级业绩累计
         * 2.直推奖励 减少  
         * 3.间推简历 减少
         * 4.积分退还
         **/
        // $money = $jilu->money;
        // $db->query("update user_tuan_price set money=money-$jilu->money where order_id = $order->id and (remark = '个人业绩累计' or remark = '下级业绩累计' or remark= '下下级业绩累计' )");
        // $log = $db->get_row("select * from user_tuan_price where remark = '间推返利' and order_id = $order->id");
        // if($log){
        //     $bili = bcdiv($log->money, $log->order_price, 2);
        //     $orderPrice = bcsub($log->order_price, $money, 2);
        //     $fenhong = bcmul($orderPrice, $bili, 2);
        //     $db->query("update user_tuan_price set money = $fenhong, order_price = $orderPrice where id = $log->id");
        // }
        
        // $log = $db->get_row("select * from user_tuan_price where remark = '直推返利' and order_id = $order->id");
        // if($log){
        //     $bili = bcdiv($log->money, $log->order_price, 2);
        //     $orderPrice = bcsub($log->order_price, $money, 2);
        //     $fenhong = bcmul($orderPrice, $bili, 2);
        //     $db->query("update user_tuan_price set money = $fenhong, order_price = $orderPrice where id = $log->id");
        // }
        
		tuikuan($order, $jilu);
	}
	//addUserMsg($jilu->userId,$fenbiao,'您的退换货申请已完成，请检查退款情况',1,$jilu->orderId);
	echo '{"code":1}';
}
function bohui_tuihuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$tuihuanId = (int)$request['tuihuanId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$time = date('Y-m-d H:i:s');
	$cont = $request['cont'];
	$jilu = $db->get_row("select id,status,type,genjin_json,orderId,userId,inventoryId from order_tuihuan where id=$tuihuanId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=1){
		echo '{"code":0,"message":"操作失败，该退换货已经处理过了。"}';
		exit;
	}
	$results = array();
	if(!empty($jilu->genjin_json)){
		$results = json_decode($jilu->genjin_json,true);
	}
	$fankui = array();
	$fankui['name'] = $username;
	$fankui['time'] = $time;
	$fankui['content'] = '退换货申请被驳回，原因：'.$cont;
	$results[] = $fankui;
	$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
	$db->query("update order_tuihuan set status=-1,genjin_json='$resultstr',dealTime='$time',dealUser=$userId,dealCont='$cont' where id=$tuihuanId");
	//todo 设置ifshouhou=0
	$detail = $db->get_row("select * from order_detail$fenbiao where orderId = $jilu->orderId and inventoryId = $jilu->inventoryId ");
	if($detail){
	    $db->query("update order_detail$fenbiao set ifshouhou = 0 where id = ".$detail->id);
	}
	
	addUserMsg($jilu->userId,$fenbiao,'您的退换货申请被驳回，原因：'.$cont,1,$jilu->orderId);
	echo '{"code":1}';
	exit;
}
function tuihuan_fahuo(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$fahuo_json['company'] = $request['company'];
	$fahuo_json['orderId'] = $request['orderId'];
	$sn = '';
	$jilu = $db->get_row("select * from order_tuihuan where id=$id");
	if($jilu->status!=4){
		echo '{"code":0,"message":"操作失败，该退换货已经处理过了。"}';
		exit;
	}
	$product_json = json_decode($jilu->pdtInfo);
	if(strstr($company,'顺丰')){
		$fahuo_json['sn'] = 'SF';
	}else if(strstr($company,'圆通')){
		$fahuo_json['sn'] = 'YTO';
	}else if(strstr($company,'邮政')){
		$fahuo_json['sn'] = 'YZPY';
	}else if(strstr($company,'百世')){
		$fahuo_json['sn'] = 'HTKY';
	}
	$results = array();
	if(!empty($jilu->genjin_json)){
		$results = json_decode($jilu->genjin_json,true);
	}
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$date = $time = date('Y-m-d H:i:s');
	$fankui = array();
	$fankui['name'] = $username;
	$fankui['time'] = $date;
	$newstatus = 5;
	$fankui['content'] = '店家已经重新发货';
	$results[] = $fankui;
	$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
	$db->query("update order_tuihuan set status=5,fahuo_json='".json_encode($fahuo_json,JSON_UNESCAPED_UNICODE)."',genjin_json='$resultstr',dealTime='$time',dealUser=$userId,dealCont='已重新发货' where id=$id");
	$orderId = (int)$jilu->orderId;
	
	$order = $db->get_row("select * from order$fenbiao where id=$jilu->orderId");
	$db->query("update order$fenbiao set status=3 where id=$orderId");
	$db->query("update order_detail$fenbiao set status=1 where orderId=$orderId");
	//创建定时收货任务
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$db_service = getCrmDb();
	}
	$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
	$shouhuo_time = strtotime("+$shuohuo_day days");
	$timed_task = array();
	$timed_task['dtTime'] = $shouhuo_time;
	$timed_task['comId'] = $order->comId;
	$timed_task['router'] = 'order_autoShouhuo';
	$timed_task['params'] = '{"order_id":'.$orderId.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
	if($jilu->kuaidi_type==2 && $jilu->kuaidi_money>0){
		if($_SESSION['if_tongbu']==1){
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
	/*require_once(ABSPATH.'/wxmbxx.php');
	$openId = $db->get_var("select openId from users where id=".$jilu->userId);
	//退换货进度提醒
	$arr1 = array(
	    'first' => array(
	        'value' => '您的换货申请已重新发货！'.($jilu->kuaidi_type==2?'运费已经返还至您的账户余额。':''),
	        'color' => '#FF0000'
	    ),
	    'keyword1' => array(
	        'value' => $jilu->sn,
	        'color' => '#FF0000'
	    ),
	    'keyword2' => array(
	        'value' => '已重新发货',
	        'color' => '#FF0000'
	    ),
	    'keyword3' => array(
	        'value' => $product_json->title,
	        'color' => '#FF0000'
	    ),
	    'keyword4' => array(
	        'value' => $jilu->nums,
	        'color' => '#FF0000'
	    ),
	    'keyword5' => array(
	        'value' => $jilu->money,
	        'color' => '#FF0000'
	    ),
	    'remark' => array(
	        'value' => '点击查看处理进度及发货信息',
	        'color' => '#FF0000'
	    )
	);
	post_template_msg('14RsjmT3HuPjgcYIgaR9mzB6PYkInDTN3bb1mF2sB0Q',$arr1,$openId,'https://new.nmgyzwc.com/index.php?p=21&a=shouhou');*/
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function pi_add_error(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$cont = $request['cont'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilus = $db->get_results("select id,status from order$fenbiao where id in($ids) and comId=$comId and status>-1 and status<4");
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			$error = array();
			$error['orderId'] = $jilu->id;
			$error['comId'] = $comId;
			$error['liuchengId'] = $jilu->status;
			$error['dtTime'] = date("Y-m-d H:i:s");
			$error['username'] = $_SESSION[TB_PREFIX.'name'];
			$error['remark'] = $cont;
			$db->query("update order$fenbiao set status=-2 where id=$jilu->id");
			insert_update('order_error',$error,'id');
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function pi_add_beizhu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$cont = $request['cont'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilus = $db->get_results("select id,beizhu_json from order$fenbiao where id in($ids) and comId=$comId");
	if(!empty($jilus)){
		foreach ($jilus as $jiluOrder) {
			$results = array();
			if(!empty($jiluOrder->beizhu_json)){
				$results = json_decode($jiluOrder->beizhu_json,true);
			}
			$fankui = array();
			$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$cont);
			$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
			$fankui['time'] = date('Y-m-d H:i:s');
			array_unshift($results,$fankui);
			$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
			$db->query("update order$fenbiao set beizhu_json='$resultstr' where id=$jiluOrder->id");
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function pi_fapiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$jilus = $db->get_results("select id from order$fenbiao where id in($ids) and comId=$comId and kaipiao_status=1 and status<3 and status!=1");
	$statusType='订单修改';
	$content = '订单被（批量）修改为不开发票';
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			$jiluId = $jilu->id;
			$db->query("update order$fenbiao set ifkaipiao=0 where id=$jiluId");
			addJilu($jiluId,$fenbiao,1,$statusType,$content);
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function pi_yikaipiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$jilus = $db->get_results("select id from order$fenbiao where id in($ids) and comId=$comId and kaipiao_status=1");
	$statusType='订单修改';
	$content = '订单修改为已开发票';
	if(!empty($jilus)){
		foreach ($jilus as $jilu) {
			$jiluId = $jilu->id;
			$db->query("update order$fenbiao set kaipiao_status=2 where id=$jiluId");
			addJilu($jiluId,$fenbiao,1,$statusType,$content);
		}
	}
	echo '{"code":1,"num":'.count($jilus).'}';
	exit;
}
function kaipiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$db->query("update order$fenbiao set kaipiao_status=2 where id=$jiluId and comId=$comId");
	addJilu($jiluId,$fenbiao,1,'订单修改','订单修改为已开发票');
	echo '{"code":1}';
	exit;
}
function bukaipiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$db->query("update order$fenbiao set ifkaipiao=0 where id=$jiluId and comId=$comId");
	addJilu($jiluId,$fenbiao,1,'订单修改','订单修改为不开发票');
	echo '{"code":1}';
	exit;
}
//取消订单， 审核订单
function shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select * from order$fenbiao where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	//$liucheng = getLiucheng();//获取订单处理流程
	if($jilu->status!=0 && $jilu->status!=1 && $jilu->status!=-4 && $jilu->status!=2){
		echo '{"code":0,"message":"该任务不需要审核！"}';
		exit;
	}
	if($status==1){
		switch ($jilu->status){
			case 0:
				$status = 1;
				/*如果不需要财务审核改成2
				if($liucheng['if_caiwu']==0)$status=2
				addTaskMsg(33,$jiluId,'有新的订单需要发货，请及时处理！');
				*/
				$statusType='订单审核';
				addTaskMsg(31,$jiluId,'有新的订单需要您进行财务审核，请及时处理！');
			break;
			case 1:
				$status = 2;
				$statusType='订单财务审核';
				addTaskMsg(33,$jiluId,'有新的订单需要发货，请及时处理！');
				//发货
				addFaHuo($jilu);
			break;
		}
		$content = str_replace('订单','订单已通过',$statusType);
		if(!empty($cont)){
			$content.='，备注：'.$cont;
		}
	}else{
		$statusType='订单审核不通过';$content=$statusType.',原因：'.$cont;
        //恢复库存数量
        addKuCun($jilu->id);
        //退款
		tuikuan($jilu);
        $db->query("update order_fahuo$fenbiao set status=-1 where id=$jilu->fahuoId");
        $db->query("update order$fenbiao set status=-1,remark='订单已取消',qx_time='".date("Y-m-d H:i:s")."' where id=$jilu->id");
        $db->query("update order_detail$fenbiao set status=-1 where orderId=$jilu->id");

	}
	$db->query("update order$fenbiao set status=$status where id=$jiluId");
	$db->query("update order_detail$fenbiao set status=".$request['status']." where orderId=$jiluId");
	addJilu($jiluId,$fenbiao,1,$statusType,$content);
	if($status==-1){
		addUserMsg($order->userId,$fenbiao,'您的订单被取消，原因：'.$cont,1,$order->id);
	}
	echo '{"code":1,"message":"操作成功","status":'.$status.',"status_info":"'.get_liucheng_title($status).'"}';
	exit;
}
function order_edit_save(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from order$fenbiao where id=$id and comId=$comId");
	if(empty($order)||$order->status==-1||$order->status>2){
		echo '{"code":0,"message":"该订单不可编辑"}';
		exit;
	}
	$order_edit_price = $request['order_edit_price'];
	$orderarr = array();
	$orderarr['id'] = $id;
	$remark = '修改订单信息';
	if(!empty($order_edit_price)){
		$price_json = json_decode($order->price_json,true);
		$pay_json = json_decode($order->pay_json,true);
		//原价=订单价格+手动优惠的金额
		$yuanjia = $order->price;
		if(!empty($price_json['admin'])){
			$yuanjia += $price_json['admin']['price'];
		}
		$new_price = $order_edit_price;
    	if(!empty($pay_json['jifen'])){
    		$new_price += $pay_json['jifen']['price'];
    	}
    	if(!empty($pay_json['yue'])){
    		$new_price += $pay_json['yue']['price'];
    	}
		$price_json['admin']['price'] = $yuanjia-$new_price;
		$price_json['admin']['desc'] ='';
		$orderarr['price'] = $new_price;
		$orderarr['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
		if($new_price!=$order->price){
			$remark = '修改订单信息，手动'.($price_json['admin']['price']>0?'优惠￥':'提价￥').abs($price_json['admin']['price']);
		}
	}
	if($request['ifkaipiao']==1){
		$orderarr['ifkaipiao'] = (int)$request['kaipiao_type'];
		$fapiao_json = array();
		$fapiao_json['开票方式'] = $orderarr['ifkaipiao']==2?'电子':'纸质';
		foreach ($request['fapiao_json'] as $key => $val){
			$fapiao_json[$key] = $val;
		}
		$orderarr['fapiao_json'] = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
	}else{
		$orderarr['ifkaipiao'] = 0;
	}
	$orderarr['areaId'] = (int)$request['areaId'];
	$shouhuoArr = array();
	$shouhuoArr['收件人'] = $request['shuohuo_json']['收件人'];
	$shouhuoArr['手机号'] = $request['shuohuo_json']['手机号'];
	$shouhuoArr['所在地区'] = getAreaName($orderarr['areaId']);
	$shouhuoArr['详细地址'] = $request['shuohuo_json']['详细地址'];
	$orderarr['shuohuo_json'] = json_encode($shouhuoArr,JSON_UNESCAPED_UNICODE);
	insert_update('order'.$fenbiao,$orderarr,'id');
	addJilu($id,$fenbiao,1,'修改订单',$remark);
	echo '{"code":1}';
	exit;
}
function add_error(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$cont = $request['cont'];
	$status = $db->get_var("select status from order$fenbiao where id=$id");
	if($status>-1 && $status<4){
		$error = array();
		$error['orderId'] = $id;
		$error['comId'] = $comId;
		$error['liuchengId'] = $status;
		$error['dtTime'] = date("Y-m-d H:i:s");
		$error['username'] = $_SESSION[TB_PREFIX.'name'];
		$error['remark'] = $cont;
		$db->query("update order$fenbiao set status=-2 where id=$id");
		insert_update('order_error',$error,'id');
		echo '{"code":1}';
	}else{
		echo '{"code":0,"message":"当前状态不能添加异常"}';
	}
	exit;
}
function add_error_beizhu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$cont = $request['cont'];
	$is_done = (int)$request['is_done'];
	$error = $db->get_row("select status,genjin_json,liuchengId,orderId from order_error where id=$id");
	if($error->status==1){
		echo '{"code":0,"message":"该异常已处理，不能再添加跟进"}';
		exit;
	}
	$genjin_arr = array();
	if(!empty($error->genjin_json))$genjin_arr = json_decode($error->genjin_json,true);
	//file_put_contents('request.txt',$error->genjin_json);
	$fankui = array();
	$fankui['content'] = $cont;
	$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
	$fankui['time'] = date('Y-m-d H:i:s');
	$genjin_arr[] = $fankui;
	$db->query("update order_error set status=$is_done,genjin_json='".json_encode($genjin_arr,JSON_UNESCAPED_UNICODE)."' where id=$id");
	if($is_done==1){
		$db->query("update order$fenbiao set status=$error->liuchengId where id=$error->orderId");
	}
	echo '{"code":1}';
	exit;
}
function fapiao_kaipiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$type = (int)$request['type'];
	$url = $request['url'];
	$fapiao_json_str = $db->get_var("select fapiao_json from order$fenbiao where id=$id");
	$fapiao_json = json_decode($fapiao_json_str,true);
	if($type==1){
		$fapiao_json['电子发票地址'] = $url;
	}else{
		$fapiao_json['发票快递'] = empty($url)?'发票随商品一同寄出':$url;
	}
	
	$fapiao_json_str = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
	$db->query("update order$fenbiao set fapiao_json='$fapiao_json_str',kaipiao_status=2 where id=$id");
	echo '{"code":1}';
}
function service_edit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$items = get_service_items();
	$service = array();
	$service['id'] = (int)$request['service_id'];
	$service['comId'] = $comId;
	$service['orderId'] = (int)$request['orderId'];
	$service['name'] = $request['name'];
	$service['phone'] = $request['phone'];
	$service['address'] = $request['address'];
	$service['serviceId'] = $request['serviceId'];
	$service['title'] = $items[$service['serviceId']];
	$service['price'] = $request['price'];
	$service['ispay'] = (int)$request['ispay'];
	$service['worker_id'] = (int)$request['worker_id'];
	$service['worker_name'] = $request['worker_name'];
	$service['worker_phone'] = $request['worker_phone'];
	$service['remark'] = $request['remark'];
	$service['service_time'] = $request['service_time'];
	if(empty($service['id'])){
		$o = $db->get_row("select userId,orderId from order$fenbiao where id=".$service['orderId']);
		$service['userId'] = empty($o)?0:$o->userId;
		$service['sn'] = date("YmdHis").$comId.rand(1000,9999);
		$service['order_orderId'] = empty($o)?0:$o->orderId;
		$service['dtTime'] = date("Y-m-d H:i:s");
	}
	insert_update('order_service',$service,'id');
	echo '{"code":1}';
	exit;
}
function order_edit_service(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$orderId = (int)$request['orderId'];
	$serviceId = (int)$request['serviceId'];
	$reload = (int)$request['reload'];
	if(!empty($serviceId))$service = $db->get_row("select * from order_service where id=$serviceId and comId=$comId");
	$items = get_service_items();
	$str = '<div class="ddfw_adddingdangfuwu">
    	<div class="dqpiliangshenhe_01">
        	<div class="dqpiliangshenhe_01_left">'.(empty($serviceId)?'新增':'修改').'服务</div>
        	<div class="dqpiliangshenhe_01_right" onclick="hide_service_edit();">
            	<img src="images/close_1.png" >
            </div>
        	<div class="clearBoth"></div>
        </div>
        <form action="?s=order&a=service_edit&orderId='.$orderId.'&service_id='.$serviceId.'" method="post" id="service_edit_form">
        <div class="ddfw_adddingdangfuwu1">
        	<div class="ddfw_adddingdangfuwu1_1">
            	<ul>';
            		if(!empty($orderId)){
            			$order_id = $db->get_var("select orderId from order$fenbiao where id=$orderId");
	            		$str .='<li>
		                    	<div class="ddfw_adddingdangfuwu1_1_title">
		                        	订单号：
		                        </div>
		                    	<div class="ddfw_adddingdangfuwu1_1_tt">
		                        	<span>'.$order_id.'</span>
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
                	}
                    $str.='<li>
                    	<div class="ddfw_adddingdangfuwu1_1_title">
                        	<span>*</span> 服务项目：
                        </div>
                    	<div class="ddfw_adddingdangfuwu1_1_tt">
                        	<select name="serviceId">';
                        	foreach ($items as $key => $val){
                        		$str.='<option value="'.$key.'" '.($key==$service->serviceId?'selected="true"':'').'>'.$val.'</option>';
                        	}
                        	$str.='</select>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="ddfw_adddingdangfuwu1_1_title">
                        	<span>*</span> 费用：
                        </div>
                    	<div class="ddfw_adddingdangfuwu1_1_tt">
                        	<input name="price" type="number" value="'.$service->price.'" required="required" placeholder="请输入费用" style="width:100px"> 元
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
        	<div class="ddfw_adddingdangfuwu1_2">
            	<div class="ddfw_adddingdangfuwu1_2_up">
                	联系人信息：
                </div>
            	<div class="ddfw_adddingdangfuwu1_2_down">
                	<ul>
                    	<li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 联系人：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input name="name" type="text" required="required" value="'.$service->name.'" placeholder="请输入被服务方的联系人" style="width:380px;">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 联系电话：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input name="phone" type="text" required="required" value="'.$service->phone.'" placeholder="请输入准确的联系方式" style="width:380px;">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 详细地址：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input name="address" type="text" required="required" value="'.$service->address.'" placeholder="请输入详细的联系地址" style="width:380px;">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="ddfw_adddingdangfuwu1_2">
            	<div class="ddfw_adddingdangfuwu1_2_up">
                	服务人员信息：
                </div>
            	<div class="ddfw_adddingdangfuwu1_2_down">
                	<ul>
                    	<li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 服务人员：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input type="text" id="fanwei_2" value="'.$service->worker_name.'" required="required" readonly="true" onclick="fanwei(2);" placeholder="选择服务人员" style="width:380px;"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 联系电话：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input name="worker_phone" value="'.$service->worker_phone.'" required="required" type="text" placeholder="请输入准确的联系方式" style="width:380px;">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 预约服务时间：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <input name="service_time" value="'.(empty($service->service_time)?'':date("Y-m-d H:i",strtotime($service->service_time))).'" required="required" type="text" id="service_edit_time" placeholder="请选择预约服务时间" readonly="true" style="width:380px;"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                <span>*</span> 是否支付：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <select name="ispay">
                                	<option value="0">未支付</option>
                                	<option value="1" '.($service->ispay==1?'selected="true"':'').'>已支付</option>
                                </select>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="ddfw_adddingdangfuwu1_1_title">
                                备注：
                            </div>
                            <div class="ddfw_adddingdangfuwu1_1_tt">
                                <textarea name="remark">'.$service->remark.'</textarea>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="dqpiliangshenhe_03">
        	<a href="javascript:" onclick="submit_service_edit('.$reload.');">确定</a>
        </div>
        </form>
    </div>';
    echo $str;
    exit;
}
//插入订单操作记录
function addJilu($orderId,$fenbiao,$type,$operate,$remark){
	$jilu = array();
	$jilu['orderId'] = $orderId;
	$jilu['username'] = $_SESSION[TB_PREFIX.'name'];
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['type'] = $type;
	$jilu['remark'] = $remark;
	$jilu['operate'] = $operate;
	insert_update('order_jilu'.$fenbiao,$jilu,'id');
}
function get_tuihuan_reasons(){
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$qxReason = $db->get_var("select tuihuan_reason from demo_shezhi where comId = $comId");
	$reasons = explode('@_@', $qxReason);
	
	return $reasons;
}
function get_service_items(){
	$items = array(0=>'服务项目');
	return $items;
}
function get_liucheng_title($id){
	switch ($id) {
		case 0:
			return '待审核';
		break;
		case 1:
			return '待财务审核';
		break;
		case 2:
			return '待发货';
		break;
		case 3:
			return '待收货';
		break;
		case 4:
			return '已完成';
		break;
		case -2:
			return '异常';
		break;
		case -3:
			return '退换货';
		break;
		case -1:
			return '无效';
		break;
		case -4:
			return '缺货';
		break;
		case -5:
			return '待支付';
		break;
		default:
			return '其他';
		break;
	}
}
//订单退款操作,优惠券、积分、余额、礼品卡、微信、支付宝==
/*$order = $db->get_row("select * from order1 where id=3");
*/
function tuikuan_($order, $jilu){
	global $db;

	$userId = $order->userId;
	$comId = $order->comId;
	$orderId = $order->id;
	$zong_fenbiao = $fenbiao = getFenbiao($comId,20);

	$pay_json = json_decode($order->pay_json,true);
	$pay_json = array(
	    'yue' => array(
	        'if_zong' => 0,
	        'price' => $jilu->money
	    ), 
	    'jifen' => array(
	        'desc' => $jilu->money,
	        'price' => $jilu->money
	    ),    
	);
	//积分返回
	if(!empty($pay_json['jifen']['desc'])){
		$jifen = (int)$pay_json['jifen']['desc'];
		$db->query("update users set jifen=jifen-$jifen where id=$userId");
		$yue = $db->get_var('select jifen from users where id='.$userId);
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = -$jifen;
		$jifen_jilu['yue'] = $yue;
		$jifen_jilu['type'] = 2;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '订单售后退款，订单号：'.$order->orderId;
		$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	}

	//优惠券返还
	if(!empty($pay_json['yhq']['desc'])){
		$db->query("update user_yhq$zong_fenbiao set status=0,orderId=0 where id=".(int)$pay_json['yhq']['desc']);
	}

	//余额支付
	if(!empty($pay_json['yue']['price'])){
		$money = $pay_json['yue']['price'];
		
		$db->query("update users set money=money+$money where id=".($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
		$yue = $db->get_var('select money from users where id='.($pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId));
		
		$liushui = array();
		$liushui['userId']=$pay_json['yue']['if_zong']==1?$order->zhishangId:$order->userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$money;
		$liushui['yue']=$yue;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='订单售后退款';
		$liushui['orderInfo']='订单售后退款，订单号：'.$order->orderId;
		$liushui['order_id']=$order->id;
		$db->insert_update('user_liushui'.($pay_json['yue']['if_zong']==1?10:$fenbiao),$liushui,'id');
	}

	//微信小程序返余额
	if(!empty($pay_json['applet']['price'])){
	    $refundid = $orderid   = date("Ymd") .rand(100,999) . rand(100,999);
	    $wxpb = new WxPayBack();
	    $res = $wxpb->unifiedorder($order->orderId,$refundid,$order->price * 100,$order->price_payed * 100);
	}
		
	//微信支付返余额
	if(!empty($pay_json['weixin']['price'])){
		$money = $pay_json['weixin']['price'];
	
		$db->query("update users set money=money+$money where id=$userId");
		$yue = $db->get_var('select money from users where id='.$userId);
		
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
	//支付宝返余额
	if(!empty($pay_json['alipay']['price'])){
		$money = $pay_json['alipay']['price'];
	
		$db->query("update users set money=money+$money where id=$userId");
		$yue = $db->get_var('select money from users where id='.$userId);
		
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
	

}

function cards(){}
function xxk(){}
function getCardsList(){
    global $db,$request;
    $comId = (int)$_SESSION[TB_PREFIX.'comId'];
    $fenbiao = getFenbiao($comId,20);
    $storeId = (int)$request['storeId'];
    $print_type = (int)$request['print_type'];
    $type = (int)$request['type'];
    $keyword = $request['keyword'];
    $orderId = $request['orderId'];
    $startTime = $request['startTime'];
    $endTime = $request['endTime'];
    $payStatus = $request['payStatus'];
    $status = $request['status'];
    $page = (int)$request['page'];
    $pageNum = (int)$request["limit"];
    $fahuoTime = $request['fahuoTime'];
    setcookie('orderPageNum',$pageNum,time()+3600*24*30);
    $order1 = empty($request['order1'])?'id':$request['order1'];
    $order2 = empty($request['order2'])?'desc':$request['order2'];
    if(empty($request['order2'])){
        $order1 = 'id';
        $order2 = 'desc';
    }
    $where = 'id > 0';
    if($status == 1){
        $where .=  ' AND status  = 1 and is_give = 0';
    }
    if($status == 2){
        $where .=  ' AND is_give  =1';
    }
    if($status == 3){
        $where .=  ' AND status  > 1  and is_give = 0';
    }
    if($keyword){
        $where .=  " AND (orderId = '$keyword' or product_json like '%$keyword%')";
    }
    $sql = "select * from cards where $where";
   // echo $sql;die;
    $countsql = str_replace('*','count(*)',$sql);
    $count = $db->get_var($countsql);
    //if(empty($kczt))$count=$count*count($cangkus);
    //file_put_contents('request.txt',$sql);
    if(!$page){
        $page = 1;
    }
    if(!$pageNum){
        $pageNum = 20;
    }
    $sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
    //file_put_contents('request.txt',$sql);
    //echo $sql;die;
    $jilus = $db->get_results($sql);

    $dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    if(!empty($jilus)){
        foreach ($jilus as $i=>$j) {
            $j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
            $j->dtTime = date("Y-m-d H:i",$j->created_at);
            $product_json = json_decode($j->product_json,true);
            $j->title = $product_json['title'];
            $j->name = $db->get_var("select nickname from users where id = ".$j->userId);;
            if($j->status == 1){
                $j->status_info  = '未使用';
            }
            if($j->created_at+ (86400*365*2) < time()){
                $j->status_info  = '已经过期';
            }
            if($j->status == 3){
                $j->status_info  = '已使用';
            }

            if($j->is_give == 1){
                $j->status_info  = '已赠送';
            }
            $j->beizhu =$j->content;
            $dataJson['data'][] = $j;
        }
    }
    echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
    exit;
}

function xxkList(){
    global $db,$request;
    $comId = (int)$_SESSION[TB_PREFIX.'comId'];
    $print_type = (int)$request['print_type'];
    $keyword = $request['keyword'];
    $startTime = $request['startTime'];
    $endTime = $request['endTime'];
    $status = $request['status'];
    $page = (int)$request['page'];
    $pageNum = (int)$request["limit"];
    $order1 = empty($request['order1'])?'id':$request['order1'];
    $order2 = empty($request['order2'])?'desc':$request['order2'];
    if(empty($request['order2'])){
        $order1 = 'id';
        $order2 = 'desc';
    }
    $where = 'id > 0';
    if($status == 1){
        $where .=  ' AND is_use = 0';
    }
    if($status == 2){
        $where .=  ' AND is_use = 1';
    }
    if($keyword){
        $where .=  ' AND code = "'.$keyword.'"';
    }
    $sql = "select * from code where $where";
    // echo $sql;die;
    $countsql = str_replace('*','count(*)',$sql);
    $count = $db->get_var($countsql);
    //if(empty($kczt))$count=$count*count($cangkus);
    //file_put_contents('request.txt',$sql);
    if(!$page){
        $page = 1;
    }
    if(!$pageNum){
        $pageNum = 20;
    }
    $sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
    //file_put_contents('request.txt',$sql);
    //echo $sql;die;
    $jilus = $db->get_results($sql);

    $dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    if(!empty($jilus)){
        foreach ($jilus as $i=>$j) {
            $j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
            $j->dtTime = $j->created_at;
            $j->title = '';
            if($j->productId){
                $j->title = $db->get_var("select title from demo_product_inventory where id = ".$j->productId);;;
            }
            $j->name = $db->get_var("select nickname from users where id = ".$j->userId);;
            if($j->is_use == 0){
                $j->status_info  = '未使用';
            }
            $time = strtotime("+2 year",strtotime($j->created_at));
            if($time < time()){
                $j->status_info  = '已经过期';
            }
            if($j->is_use == 1){
                $j->status_info  = '已使用';
            }
            $j->status_export = $j->is_export == 1 ? '已导出':'未导出';
            $j->beizhu =$j->content;
            $dataJson['data'][] = $j;
        }
    }
    echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
    exit;
}
function create(){
    global $db,$request;
    $comId = (int)$_SESSION[TB_PREFIX.'comId'];
    if($request['tijiao']==1){
        $_SESSION['tijiao'] = 0;
        $inventory_id = (int)$request['inventoryId'];
        $num = (int)$request['number'];
        if(!$inventory_id){
            echo '<script>alert("请选择生成卡卷商品");location.href="?m=system&s=order&a=create";</script>';
            exit;
        }
        if(!$num){
            echo '<script>alert("请输入生成卡卷数量");location.href="?m=system&s=order&a=create";</script>';
            exit;
        }
        $last_id = $db->get_var("select id from code where id >0 order by id desc limit 1");
        $str = 'Card';
        $s = 889000+$last_id;
        $e = 889000+$last_id+$num;
        for ($i = $s ;$i < $e; $i++){
            $data = array();
            $data['comId'] = $comId;
            $data['code'] = $str.$i;
            $data['productId'] = $inventory_id;
            $data['pass'] = substr(md5($data['code']),10,6);
            $data['created_at'] = date('Y-m-d H:i:s',time());
            $data['startTime'] = $request['startTime'];
            $data['endTime'] = $request['endTime'];
            $db->insert_update('code',$data,'id');
        }
        
        echo '{"code":1,"message":"添加成功"}';
        exit;
    }
}

function xxkExport(){
    //导出未导出的实体卡并修改导出状态
    global $db,$request;
     $data = $db->get_results("select * from code where is_export = 0 and is_use = 0");
    // 文件名，这里都要将utf-8编码转为gbk，要不可能出现乱码现象
    $filename = utfToGbk('实体卡密.csv');

    // 拼接文件信息，这里注意两点
    // 1、字段与字段之间用逗号分隔开
    // 2、行与行之间需要换行符
    $fileData = utfToGbk('卡号, 密码, 开始时间, 结束时间') . "\n";
    foreach ($data as $value) {
        $temp = $value->code . ',' .
            $value->pass. ',' .
            $value->startTime. ',' .
            $value->endTime;
        $fileData .= utfToGbk($temp) . "\n";
        //修改导出状态
    }

    // 头信息设置
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=" . $filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    echo $fileData;
    $db->query("update code set is_export = 1 where is_export = 0 and is_use = 0");
    exit;
}

/**
 * 字符转换（utf-8 => GBK）
 */
function utfToGbk($data)
{
    return iconv('utf-8', 'GBK', $data);
}


