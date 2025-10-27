<?php
function index(){}
function getRiderList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_peisong_rider where comId=$comId ";
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$statusInfo = '<font color="red">待审核</font>';
				break;
				case 1:
					$statusInfo = '<font color="green">已审核</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>未通过</font>';
				break;
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function sheheRider(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_peisong_rider set status=1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function bohuiRider(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_peisong_rider set status=-1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function delRider(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_peisong_rider where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function add_rider(){
	global $db,$request;
	if($request['tijiao']==1){
		$shequ = array();
		$shequ['id'] = (int)$request['id'];
		$shequ['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$shequ['shequId'] = (int)$request['shequId'];
		$shequ['name'] = $request['name'];
		$shequ['phone'] = $request['phone'];
		$shequ['row1'] = $request['row1'];
		$shequ['row2'] = $request['row2'];
		$shequ['row3'] = $request['row3'];
		$db->insert_update('demo_peisong_rider',$shequ,'id');
		redirect("?s=peisongyuan");
	}
}