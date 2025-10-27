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
		$order1 = 'inventoryId';
		$order2 = 'asc';
	}
	$fenbiao = getFenbiao($comId,20);
	$orderIds = $db->get_var("select group_concat(orderIds) from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1");
	if(empty($orderIds))$orderIds='-1';
	$countsql = "select count(distinct(inventoryId)) from order_detail$fenbiao where orderId in($orderIds)";
	$count = $db->get_var($countsql);
	$sql = "select productId,inventoryId,pdtInfo,sum(num) as num from order_detail$fenbiao where orderId in($orderIds)";
	$sql.=" group by inventoryId order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			//$o = $db->get_row("select shuohuo_json,remark from order$fenbiao where id=$j->orderId");
			$pdtInfo = json_decode($j->pdtInfo);
			//$shouhuo = json_decode($o->shuohuo_json,true);
			$addrows = $db->get_var("select addrows from demo_product where id=$j->productId");
			$addrows_arr = json_decode($addrows,true);
			//$j->name = $shouhuo['收件人'];
			$j->product = $pdtInfo->title;
			$j->penjing = $addrows_arr['盆径'];
			$j->guige = $addrows_arr['规格'];
			$j->toushu = $addrows_arr['头数'];
			$j->shuliang = $j->num * intval($addrows_arr['数量']);
			//$j->remark = $o->remark;
			$j->num = getXiaoshu($j->num,0);
			//$j->print = '<a href="?s=chuhuodan&a=prints&id='.$j->id.'" target="_blank">打印</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function daochu(){}
function prints(){}