<?php
function index(){}
function orders(){}
function orders2(){}
function orders3(){}
function daochuOrders1(){}
function daochuOrders2(){}
function daochuOrders3(){}
function getOrders1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('caigouhPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql = "select id,title from demo_supplier where comId=$comId ";
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$suppliers = $db->get_results($sql);
	$count = $db->get_var("select count(*) from demo_supplier where comId=$comId and status=1");
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($suppliers)){
		foreach ($suppliers as $j){
			$ordersql = "select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and supplierId=$j->id and status=1";
			$pdtsql = "select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and supplierId=$j->id and status=1";
			if(!empty($startTime)){
				$ordersql.=" and dtTime>='$startTime 00:00:00'";
				$pdtsql.=" and dtTime>='$startTime 00:00:00'";
			}
			if(!empty($endTime)){
				$ordersql.=" and dtTime<='$endTime 23:59:59'";
				$pdtsql.=" and dtTime<='$endTime 23:59:59'";
			}
			$o1 = $db->get_row($ordersql);
			$o2 = $db->get_var($pdtsql);
			$j->orderIds = empty($o1->orderNum)?0:$o1->orderNum;
			$j->price = empty($o1->priceNum)?0:$o1->priceNum;
			$j->nums = empty($o2)?0:$o2;
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
	setcookie('caigouh2PageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select jiluId,pdtInfo,num,price,units,status,dtTime,unit_price from demo_caigou_detail$fenbiao where comId=$comId and status=1";
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
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$j->price_zong = getXiaoshu($j->price,$product_set->price_num);
			$j->price = getXiaoshu($j->unit_price,$product_set->price_num);
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
	setcookie('caigouh3PageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="SELECT pdtInfo,sum(num) as num,sum(price) as price,units FROM `demo_caigou_detail$fenbiao` where comId=$comId and status=1";
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
			$j->price = getXiaoshu($j->price,2);
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