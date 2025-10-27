<?php
function index(){}
function create(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$zhaopin = array();
		$zhaopin['id'] = (int)$request['id'];
		$zhaopin['comId'] = $comId;
		$zhaopin['inventoryId'] = (int)$request['inventoryId'];
		$zhaopin['money'] = $request['money'];
		$db->insert_update('demo_xinren_discount',$zhaopin,'id');
		redirect("?s=xinren");
	}
}
function delete(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("delete from demo_xinren_discount where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function jinyong(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("update demo_xinren_discount set status=-1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function qiyong(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("update demo_xinren_discount set status=1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql="select * from demo_xinren_discount where comId=$comId";
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$inventorys = $db->get_var("select group_concat(id) from demo_product_inventory where comId=$comId and (title like '%$keyword%' or sn='$keyword')");
		if(empty($inventorys))$inventorys='0';
		$sql.=" and inventoryId in($inventorys)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
				break;
				case 0:
					$j->layclass = 'deleted';
				break;
				case 1:
					$j->layclass = '';
				break;
			}
			$inventory = $db->get_row("select title,key_vals,sn from demo_product_inventory where id=$j->inventoryId");
			$j->title = $inventory->title;
			$j->key_vals = $inventory->key_vals;
			$j->sn = $inventory->sn;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}