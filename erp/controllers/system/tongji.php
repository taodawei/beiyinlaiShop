<?php
function index(){}
function number(){}
function money(){}
function daochu(){}
function day(){}

function get_inventorys(){
	global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'z_num':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	
	$timeSelect = '';
    if(!empty($startTime)){
		$timeSelect.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$timeSelect.=" and dtTime<='$endTime'";
	}
	$field = 'd.id,d.productId,d.sn,d.key_vals,d.title,d.sn,a.z_num,a.z_price';
	$sql="SELECT $field from demo_product_inventory d inner join (select inventoryId,sum(num) as z_num,sum(num * unit_price) as z_price from order_detail8 a where status>-1 and comId=$comId $timeSelect  group by inventoryId) a on a.inventoryId = d.id ";
	$countSql="SELECT count(*) from demo_product_inventory d inner join (select inventoryId,sum(num) as sale_num,sum(num * unit_price) as sale_money from order_detail8 a where status>-1 and comId=$comId $timeSelect group by inventoryId) a on a.inventoryId = d.id ";
	
// 	$sql="select sum(a.num) as z_num,sum(a.num*a.unit_price) as z_price,a.inventoryId,a.pdtInfo,a.id as did,a.params,b.dtTime from order_detail$fenbiao a left join order$fenbiao b on a.orderId=b.id where a.comId=$comId and a.status>-1 ";
    
    $where = array();
	if(!empty($keyword)){
	    $sql .= " where  (d.sn='$keyword' or d.title like '%$keyword%')";
	    $countSql .= "where (d.sn='$keyword' or d.title like '%$keyword%')";
	}

// 	if(!empty($userId)){
// 		$sql.=" and b.userId='$userId'";
// 	}
	$count = $db->get_var($countSql);
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	$shequTitles = array();
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$data = array();
			$pdtInfo = json_decode($j->pdtInfo);
			$data['id'] = $j->id;
			$data['z_num'] = intval($j->z_num);
			$data['z_price'] = getXiaoshu($j->z_price,2);
			$data['sn'] = $j->sn;
			$data['title'] = $j->title;
// 			$data['shequ_id'] = $j->shequ_id;
// 			if(empty($shequ_title)){
// 			    $shequTitles[$j->shequ_id] = $shequ_title;
// 			    $shequ_title = $db->get_var("select title from demo_shequ where id = $j->shequ_id");
// 			}
// 			$data['shequ_title'] = $shequ_title;
			$data['key_vals'] = $j->key_vals;
			$dataJson['data'][] = $data;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function get_inventorysBak(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'z_num':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select sum(a.num) as z_num,sum(a.num*a.unit_price) as z_price,a.inventoryId,a.pdtInfo,b.dtTime from order_detail$fenbiao a left join order$fenbiao b on a.orderId=b.id where a.comId=$comId and a.status>-1 ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(productId) from demo_product_inventory where comId=$comId and (sn='$keyword' or title like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and a.productId in($pdtId)";
	}
	if(!empty($startTime)){
		$sql.=" and b.dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and b.dtTime<='$endTime'";
	}
	if(!empty($userId)){
		$sql.=" and b.userId='$userId'";
	}
	$count = $db->get_var(str_replace('sum(a.num) as z_num,sum(a.num*a.unit_price) as z_price,a.inventoryId,a.pdtInfo,b.dtTime','count(distinct(a.inventoryId))',$sql));
	$sql.=" group by a.inventoryId";
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$data = array();
			$pdtInfo = json_decode($j->pdtInfo);
			$data['id'] = $j->inventoryId;
			$data['z_num'] = $j->z_num;
			$data['z_price'] = getXiaoshu($j->z_price,2);
			$data['sn'] = $pdtInfo->sn;
			$data['title'] = $pdtInfo->title;
			$data['key_vals'] = $pdtInfo->key_vals;
			$dataJson['data'][] = $data;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
	/*echo $sql;
	echo str_replace('sum(a.num) as z_num,sum(a.num*a.unit_price) as z_price,a.inventoryId,a.pdtInfo,b.dtTime','distinct(inventoryId)',$sql);
	exit;*/
}
function get_tongjis(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$sql = "select sum(price) as z_price,count(*) as z_num,status from order$fenbiao where comId=$comId and status>-1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime'";	
	}
	$sql.=" group by status";
	$return = array();
	$return['code'] = 1;
	$return['total_money1'] = 0;
	$return['total_money2'] = 0;
	$return['total_money3'] = 0;
	$return['total_money4'] = 0;
	$return['total_num1'] = 0;
	$return['total_num2'] = 0;
	$return['total_num3'] = 0;
	$return['total_num4'] = 0;	
	$lists = $db->get_results($sql);
	if(!empty($lists)){
		foreach ($lists as $val) {
			$return['total_money1']+=$val->z_price;
			$return['total_num1']+=$val->z_num;
			switch ($val->status) {
				case 2:
					$return['total_money2']+=$val->z_price;
					$return['total_num2']+=$val->z_num;
				break;
				case 3:
					$return['total_money3']+=$val->z_price;
					$return['total_num3']+=$val->z_num;
				break;
				case 4:
					$return['total_money4']+=$val->z_price;
					$return['total_num4']+=$val->z_num;
				break;
			}
		}
		
		$return['total_money1'] = bcadd($return['total_money1'], 0, 2);
	}
	
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_numbers(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$startTime = empty($request['startTime'])?date('Y-m-01'):$request['startTime'];
	$endTime = empty($request['endTime'])?date('Y-m-d'):$request['endTime'];
	$days = abs(diffBetweenTwoDays($startTime,$endTime));
	$return = array("code"=>0,"msg"=>'成功',"count"=>$days,"data"=>array());
	$return['code'] = 0;
	$return['data'] = array();
	for($i=0;$i<=$days;$i++){
		$dtTime = date("Y-m-d",strtotime("+ $i days",strtotime($startTime)));
		$shuju = $db->get_row("select sum(price) as z_price,count(*) as z_num from order$fenbiao where comId=$comId and status>-1 and dtTime>='$dtTime 00:00:00' and dtTime<='$dtTime 23:59:59'");
		$data = array();
		$data['date'] = $dtTime;
		$data['z_num'] = empty($shuju->z_num)?0:$shuju->z_num;
		$data['z_price'] = empty($shuju->z_price)?0:$shuju->z_price;
		$data['caozuo'] = '<a href="?s=tongji&a=day&time='.$dtTime.'" style="color:#1898d6">查看时间段订单</a>';
		$return['data'][] = $data;
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_day_tongji(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$time = $request['time'];
	$shujus = $db->get_results("select sum(price) as z_price,count(*) as z_num,hour(dtTime) as xiaoshi from order$fenbiao where comId=$comId and status>-1 and dtTime>='$time 00:00:00' and dtTime<='$time 23:59:59' group by xiaoshi");
	$return = array("code"=>0,"msg"=>'成功',"count"=>6,"data"=>array());
	$return['data'][0]['time'] = '0:00-4:00';
	$return['data'][0]['z_num'] = 0;
	$return['data'][0]['z_price'] = 0;
	$return['data'][1]['time'] = '4:00-8:00';
	$return['data'][1]['z_num'] = 0;
	$return['data'][1]['z_price'] = 0;
	$return['data'][2]['time'] = '8:00-12:00';
	$return['data'][2]['z_num'] = 0;
	$return['data'][2]['z_price'] = 0;
	$return['data'][3]['time'] = '12:00-16:00';
	$return['data'][3]['z_num'] = 0;
	$return['data'][3]['z_price'] = 0;
	$return['data'][4]['time'] = '16:00-20:00';
	$return['data'][4]['z_num'] = 0;
	$return['data'][4]['z_price'] = 0;
	$return['data'][5]['time'] = '20:00-24:00';
	$return['data'][5]['z_num'] = 0;
	$return['data'][5]['z_price'] = 0;
	if(!empty($shujus)){
		foreach ($shujus as $val) {
			if($val->xiaoshi<5){
				$return['data'][0]['z_num']+=$val->z_num;
				$return['data'][0]['z_price']+=$val->z_price;
			}else if($val->xiaoshi>4&&$val->xiaoshi<9){
				$return['data'][1]['z_num']+=$val->z_num;
				$return['data'][1]['z_price']+=$val->z_price;
			}else if($val->xiaoshi>8&&$val->xiaoshi<13){
				$return['data'][2]['z_num']+=$val->z_num;
				$return['data'][2]['z_price']+=$val->z_price;
			}else if($val->xiaoshi>12&&$val->xiaoshi<17){
				$return['data'][3]['z_num']+=$val->z_num;
				$return['data'][3]['z_price']+=$val->z_price;
			}else if($val->xiaoshi>16&&$val->xiaoshi<21){
				$return['data'][4]['z_num']+=$val->z_num;
				$return['data'][4]['z_price']+=$val->z_price;
			}else if($val->xiaoshi>20){
				$return['data'][5]['z_num']+=$val->z_num;
				$return['data'][5]['z_price']+=$val->z_price;
			}
		}
	}
	$return['total_money1'] = getXiaoshu($return['total_money1'],2);
	$return['total_money2'] = getXiaoshu($return['total_money2'],2);
	$return['total_money3'] = getXiaoshu($return['total_money3'],2);
	$return['total_money4'] = getXiaoshu($return['total_money4'],2);
	$return['total_money5'] = getXiaoshu($return['total_money5'],2);
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function diffBetweenTwoDays ($day1, $day2)
{
  $second1 = strtotime($day1);
  $second2 = strtotime($day2);
  if ($second1 < $second2) {
    $tmp = $second2;
    $second2 = $second1;
    $second1 = $tmp;
  }
  return ($second1 - $second2) / 86400;
}