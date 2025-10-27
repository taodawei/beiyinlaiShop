<?php
function index(){}
function addShipin(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = 10;
		$shipin = array();
		$shipin['id'] = (int)$request['id'];
		$shipin['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$shipin['title'] = $request['title'];
		$shipin['originalPic'] = $request['originalPic'];
		$shipin['content'] = $request['content'];
		$shipin['shipin'] = $request['shipin'];
		$shipin['pdtIds'] = implode(',',$request['inventoryId']);
		$shipin['dtTime'] = date("Y-m-d H:i:s");
		$shipin['remark'] = $request['remark'];
		//$shipin['status'] = 0;
		$db->insert_update('demo_product_dapei',$shipin,'id');
		redirect("?s=product_dapei");
	}
}
function get_shipin_list(){
	global $db,$request;
	$keyword = $request['keyword'];
	$status = (int)$request['status'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = 6;
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_product_dapei where comId=$comId";
	/*if($status!=1){
		$sql.=" and status=0";
	}else{
		$sql.=" and status=1";
	}*/
	if(!empty($keyword)){
		/*$mendian_ids = $db->get_var("select group_concat(id) from demo_shezhi where com_title like '%$keyword%'");
		if(empty($mendian_ids))$mendian_ids='0';
		$sql.=" and (mendianId in($mendian_ids) or title like '%$keyword%')";*/
		$sql.=" and title like '%$keyword%'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	file_put_contents('request.txt',$sql);
	$pdts = $db->get_results($sql);
	$nums = $db->get_results("select count(*) as num,status from demo_product_dapei where comId=$comId group by status");
	$weishenhe = 0;
	$yishenhe = 0;
	if(!empty($nums)){
		foreach ($nums as $n){
			if($n->status==0){
				$weishenhe = $n->num;
			}else{
				$yishenhe = $n->num;
			}
		}
	}
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['wei_num'] = $weishenhe;
	$return['yi_num'] = $yishenhe;
	$return['data'] = array();
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$pdt->mendian = $db->get_var("select com_title from demo_shezhi where comId=$pdt->comId");
			$pdt->title = sys_substr($pdt->title,15,true);
			$pdt->mendian = sys_substr($pdt->mendian,15,true);
			$pdt->originalPic = ispic($pdt->originalPic);
			$return['data'][] = $pdt;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function shenhe_shipin(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("update demo_product_dapei set status=1 where id=$id");
	die('{"code":1}');
}
function del_shipin(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("delete from demo_product_dapei where id=$id");
	die('{"code":1}');
}