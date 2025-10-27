<?php
function index(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fahuoTime = $request['fahuoTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'asc';
	}
	$fenbiao = getFenbiao($comId,20);
	$sql = "select shuohuo_json,rider_id from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1 ";
	$countsql = str_replace('shuohuo_json,rider_id','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->name = $shuohuo_json['收件人'];
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->phone = $shuohuo_json['手机号'];
			$rider = $db->get_row("select * from demo_peisong_rider where id=$j->rider_id");
			$j->wuliu = $rider->row2;
			$j->wuliu_phone = $rider->phone;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function prints(){}
function daochu(){}