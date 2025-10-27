<?php
function index(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from demo_tanchuang where comId=$comId";
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and endTime<'$now'";
		break;
		case 4:
			$sql.=" and status<>1";
		break;
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->image = '<a href="'.$j->image.'" target="_blank"><img src="'.$j->image.'" height="50"></a>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function create(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$startTime = $request['startTime'].' 00:00:00';
		$endTime = $request['endTime'].' 23:59:59';
		$cuxiao = array();
		$nowNum = $db->get_var("select id from demo_tanchuang where comId=$comId and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime')) limit 1");
		if($nowNum>0){
			echo '{"code":0,"message":"该时间段已经存在弹窗活动！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['comId'] = $comId;
		$cuxiao['image'] = $request['originalPic'];
		$cuxiao['inventoryId'] = (int)$request['inventoryId'];
		$cuxiao['url'] = $request['url'];
		$db->insert_update('demo_tanchuang',$cuxiao,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function piliang_zuofei_reg(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("delete from demo_tanchuang where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}