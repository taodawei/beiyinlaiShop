<?php
function index(){}
function detail(){}
function orders(){}
function orders2(){}
function orders3(){}
function daochuOrders1(){}
function daochuOrders2(){}
function daochuOrders3(){}
function wanglais(){}
function wanglais1(){}
function wanglais2(){}
function wanglais3(){}
function wanglais4(){}
function wanglais5(){}
function wanglais6(){}
function daochuWanglais1(){}
function daochuWanglais2(){}
function daochuWanglais3(){}
function daochuWanglais4(){}
function daochuWanglais5(){}
function daochuWanglais6(){}
function jiesuan(){}
function jiesuan_detail(){}
function gonghuo(){}
function addGonghuo(){}
function delete(){
	global $db,$request;
	$id = $request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ifhas = $db->get_var("select id from demo_caigou where comId=$comId and supplierId=$id and status>-1 limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"该供应商已存在有效的采购订单，无法删除！"}';
	}else{
		$db->query("delete from demo_supplier where comId=$comId and id=$id");
		echo '{"code":1,"message":"采购"}';
	}
	exit;
}
function addJiesuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$repay = array();
	if(empty($request['selectedIds'])){
		echo '{"code":0,"message":"未选中任何采购单"}';
		exit;
	}
	$yz = $db->get_var("select sum(price_weikuan) from demo_caigou where id in(".$request['selectedIds'].") and comId=$comId");
	if($yz!=$request['money']){
		echo '{"code":0,"message":"操作失败！采购单已发生变化，请刷新该页面后重新操作"}';
		exit;
	}
	$repay['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$repay['supplierId'] = (int)$request['id'];
	$repay['type'] = (int)$request['type'];
	$repay['orderId'] = $request['orderId'];
	$repay['caigouIds'] = $request['selectedIds'];
	$repay['money'] = $request['money'];
	$repay['dtTime'] = date("Y-m-d H:i",$request['dtTime']);
	$repay['payType'] = $request['payType'];
	$repay['payAccount'] = $request['payAccount'];
	$repay['payOrder'] = $request['payOrder'];
	$repay['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$repay['username'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_caigou_repay',$repay,'id');
	$db->query("update demo_caigou set price_weikuan='0.00',jiesuanTime='".date("Y-m-d H:i:s")."' where id in(".$request['selectedIds'].")");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function add(){
	global $request;
	if($request['tijiao']==1){
		$supplier = array();
		$supplier['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$supplier['id'] = (int)$request['id'];
		$supplier['title'] = $request['title'];
		$supplier['sn'] = $request['sn'];
		$supplier['areaId'] = (int)$request['psarea'];
		$supplier['address'] = $request['address'];
		$supplier['status'] = (int)$request['status'];
		$supplier['name'] = $request['name'];
		$supplier['phone'] = $request['phone'];
		$supplier['phone1'] = $request['phone1'];
		$supplier['email'] = $request['email'];
		$supplier['position'] = $request['position'];
		$supplier['kaihu_title'] = $request['kaihu_title'];
		$supplier['kaihu_bank'] = $request['kaihu_bank'];
		$supplier['kaihu_user'] = $request['kaihu_user'];
		$supplier['kaihu_fapiao'] = $request['kaihu_fapiao'];
		$supplier['beizhu'] = $request['beizhu'];
		insert_update('demo_supplier',$supplier,'id');
		redirect('?m=system&s=supplier');
	}
}
function getJilus(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('supplierPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,sn,title,areaId,address,name,phone,status from demo_supplier where comId=$comId";
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or name like '%$keyword%' or phone like '%$keyword%' or phone1 like '%$keyword%')";
	}
	$count = $db->get_var(str_replace('id,sn,title,areaId,address,name,phone,status','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$status = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已禁用</span>';
				break;
				case 1:
					$j->layclass = '';
					$status = '<span style="color:green">已启用</span>';
				break;
			}
			$j->status = $status;
			$j->title = '<span onclick="view_detail(\'supplier\','.$j->id.')" style="cursor:pointer;">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getOrders1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,username,price_type,price,price_weikuan,status from demo_caigou where comId=$comId and supplierId=$supplierId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,orderId,dtTime,username,price_type,price,price_weikuan,status','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$status = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$j->layclass = '';
					$status = '<span>待审核</span>';
				break;
				case 1:
					$j->layclass = '';
					$status = '<span style="color:green">已通过</span>';
				break;
			}
			$j->status = $status;
			$price_type = '';
			if($j->price_type==1){
				$price_type = '现购';
			}else{
				$price_type = '赊购';
			}
			$j->price_type = $price_type;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			//$j->price = getXiaoshu($j->price,$product_set->price_num);
			//$j->price_payed = getXiaoshu(($j->price-$j->price_payed),$product_set->price_num);
			$j->num = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where jiluId=".$j->id);
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getOrders2(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select jiluId,pdtInfo,num,price,units,status,dtTime from demo_caigou_detail$fenbiao where comId=$comId and supplierId=$supplierId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('jiluId as id,pdtInfo,num,price,units','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$status = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$j->layclass = '';
					$status = '<span>待审核</span>';
				break;
				case 1:
					$j->layclass = '';
					$status = '<span style="color:green">已通过</span>';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->price = getXiaoshu($j->price,$product_set->price_num);
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$j->price_zong = getXiaoshu($j->price*$j->num,$product_set->price_num);
			$pdtInfo = json_decode($j->pdtInfo);
			$j->id = $j->jiluId;
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->orderId = $db->get_var("select orderId from demo_caigou where id=".$j->jiluId);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getOrders3(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="SELECT pdtInfo,sum(num) as num,sum(num*price) as price,units FROM `demo_caigou_detail$fenbiao` where comId=$comId and supplierId=$supplierId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$sql.=" group by inventoryId";
	$count = $db->get_var(str_replace('pdtInfo,sum(num) as num,sum(num*price) as price,units','count(*)',$sql));
	$sql.=" limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->price = getXiaoshu($j->price,$product_set->price_num);
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$pdtInfo = json_decode($j->pdtInfo);
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
	
}
//获取往来采购订单
function getWanglai1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$price_type = (int)$request['price_type'];
	$weikuan = (int)$request['weikuan'];
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,price_type,price,price_weikuan,price_payed,username from demo_caigou where comId=$comId and supplierId=$supplierId and status=1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($price_type)){
		$sql.=" and price_type=$price_type";
	}
	if($weikuan==1){
		$sql.=" and price_weikuan>0";
	}
	$count = $db->get_var(str_replace('id,orderId,dtTime,price_type,price,price_weikuan,price_payed,username','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$price_type = '';
			if($j->price_type==1){
				$price_type = '现购';
				$j->price_payed = 0;
			}else{
				$price_type = '赊购';
			}
			$j->price_type = $price_type;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->LAY_CHECKED=true;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getJiesuanOrders(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];	
	$id = (int)$request['id'];
	$caigouIds = $db->get_var("select caigouIds from demo_caigou_repay where id=$id and comId=$comId");
	$sql="select id,orderId,dtTime,price from demo_caigou where id in($caigouIds)";
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//获取结算
function getWanglai4(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,money,username from demo_caigou_repay where comId=$comId and supplierId=$supplierId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,orderId,dtTime,money,username','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//获取退款订单
function getWanglai6(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['id'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,money,username from demo_caigou_tuikuan where comId=$comId and supplierId=$supplierId and status=1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,orderId,dtTime,money,username','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getGonghuoList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$supplierId = (int)$request['id'];
	$channelId = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
	$sql="select id,title,key_vals,sn,productId from demo_product_inventory where comId=$comId and id in($pdts)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,title,key_vals,sn','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$untis=$db->get_var("select untis from demo_product where id=".$j->productId);
			$unitsArr = json_decode($untis,true);
			$j->units = $unitsArr[0]['title'];
			$j->price = $db->get_var("select price from demo_supplier_gonghuo where supplierId=$supplierId and inventoryId=".$j->id.' limit 1');
			$j->price = empty($j->price)?0:getXiaoshu($j->price,$product_set->price_num);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getGonghuoList1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['id'];
	$channelId = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
	if(empty($pdts))$pdts=0;
	$sql="select id,title,key_vals,sn from demo_product_inventory where comId=$comId and id not in($pdts)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,title,key_vals,sn','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function addGonghuoPdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['id'];
	$selectedIds = $request['selectedIds'];
	$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
	if(empty($pdts)){
		$pdts=$selectedIds;
	}else{
		$pdts = $pdts.','.$selectedIds;
	}
	$db->query("update demo_supplier set pdts='$pdts' where id=$supplierId and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function setGonghuoPrice(){
	global $db,$request;
	$gonghuo = array();
	$gonghuo['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$gonghuo['supplierId'] = (int)$request['id'];
	$gonghuo['inventoryId'] = (int)$request['pdtId'];
	$gonghuo['price'] = $request['price'];
	$gonghuo['id'] = (int)$db->get_var("select id from demo_supplier_gonghuo where comId=".$gonghuo['comId']." and supplierId=".$gonghuo['supplierId']." and inventoryId=".$gonghuo['inventoryId']." limit 1");
	insert_update('demo_supplier_gonghuo',$gonghuo,'id');
	echo '{"code":1,"message":"成功"}';
}
function qxguanlian(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['supplierId'];
	$ids = $request['ids'];
	$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
	if(!empty($pdts)&&!empty($ids)){
		$pdtsArr = explode(',',$pdts);
		$idsArr = explode(',',$ids);
		foreach ($pdtsArr as $key=>$id) {
			if(in_array($id,$idsArr)){
				unset($pdtsArr[$key]);
			}
		}
		$pdts = implode(',',$pdtsArr);
		$db->query("update demo_supplier set pdts='$pdts' where id=$supplierId and comId=$comId");
		$db->query("delete from demo_supplier_gonghuo where supplierId=$supplierId and inventoryId in($ids)");
	}
	echo '{"code":1,"message":"成功"}';
}
function qxguanlianAll(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$supplierId = (int)$request['supplierId'];
	$db->query("update demo_supplier set pdts='' where id=$supplierId and comId=$comId");
	$db->query("delete from demo_supplier_gonghuo where supplierId=$supplierId and comId=$comId");
	echo '{"code":1,"message":"成功"}';
}