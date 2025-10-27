<?php
function index(){}
function areas(){}
function pdts(){}
function getPdtList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//$mendianId = (int)$_SESSION[TB_PREFIX.'mendianId'];
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$status = (int)$request['status'];
	$if_tongbu = (int)$request['if_tongbu'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('pdtPageNum',$pageNum,time()+3600*24*30);
	$keyword = $request['keyword'];
	$tags = $request['tags'];
	$cangkus = $request['cangkus'];
	$source = (int)$request['source'];
	$cuxiao = (int)$request['cuxiao'];
	$payType = (int)$request['payType'];
	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql="select id,title,key_vals,status from zhuisu_pdt where comId=$comId";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and (title like '%$keyword%' or key_vals='$keyword')";
	}
	if(!empty($cuxiao)){
		$sql.=" and find_in_set($cuxiao,cuxiao_ids)";
	}
	if(!empty($payType)){
		switch ($payType) {
			case 1:
				$sql.=" and sale_tuan=1";
			break;
			case 2:
				$sql.=" and sale_area>0";
			break;
			case 3:
				$sql.=" and sale_lingyuangou=1";
			break;
			case 4:
				$sql.=" and sale_sharegou=1";
			break;
		}
	}
	if(!empty($source)){
		$sql.=" and source=$source";
	}
	if(!empty($if_tongbu)){
		$sql.=" and if_tongbu=$if_tongbu";
	}
	$count = $db->get_var(str_replace('id,title,key_vals,status','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($product_set->tags)){
		$tagsarry = explode('@_@',$product_set->tags);
	}
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			
			$layclass = '';
			if($pdt->status!=1)$layclass ='deleted';
			if($pdt->status==1){
				$statusInfo = '<font color=\"green\">已上架</font>';
			}else if($pdt->status==-1){
				$statusInfo = '<font color=\"red\">商家下架</font>';
			}else{
				$statusInfo = '<font color=\"red\">待审核</font>';
			}
			$tongbu = $pdt->if_tongbu==1?'<font color=\"green\">已同步</font>':'<font color=\"red\">未同步</font>';
			$pdtstr.=',{"id":'.$pdt->id.',"title":"<span onclick=\"view_product('.$pdt->id.')\">'.$pdt->title.'</span>","key_vals":"'.$pdt->key_vals.'","dtTime":"'.$pdt->dtTime.'","status":"'.$statusInfo.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function erweima(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from zhuisu_jilu where comId=$comId";
	if(!empty($keyword)){
		$sql.=" and (pdt_title like '%$keyword%' or shequ_name like '%$keyword%')";
	}
	if(!empty($startTime)){
		$sql.=" and create_time>'$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and create_time<'$endTime'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($product_set->tags)){
		$tagsarry = explode('@_@',$product_set->tags);
	}
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$pdtstr.=',{"id":'.$pdt->id.',"title":"'.$pdt->pdt_title.'","user":"'.$pdt->shequ_name.'","create_time":"'.$pdt->create_time.'","erweima":"<a href=\"?m=system&s=zhuisu&a=erweima&id='.$pdt->id.'&tuijianren='.$pdt->shequId.'\" target=\"_blank\">查看</a>"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function xiajia(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update zhuisu_pdt set status=-1 where id=$id and comId=$comId");
	die('{"code":1}');
}
function shangjia(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update zhuisu_pdt set status=1 where id=$id and comId=$comId");
	die('{"code":1}');
}
function editPdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($request['tijiao']==1){

		$pdt = array();
		$pdt['id'] = (int)$request['id'];
		$pdt['comId'] = $comId;
		$pdt['title'] = $request['title'];
		$pdt['key_vals'] = $request['key_vals'];
		$pdt['content'] = $request['content'];
		$pdt['shipin'] = $request['shipin'];
		$pdt['jiance_name'] = $request['jiance_name'];
		$pdt['jiance_content'] = $request['jiance_content'];
		$pdt['status'] = empty($request['status'])?-1:1;
		$db->insert_update('zhuisu_pdt',$pdt,'id');
		redirect('?m=system&s=zhuisu&a=pdts');
	}
}
function create(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$pdtId = (int)$request['pdtId'];
		$shequId = (int)$request['shequId'];
		$shequ = $db->get_row("select title,userId from demo_shequ where id=$shequId");
		$jilu = array();
		$jilu['comId'] = $comId;
		$jilu['pdtId'] = $pdtId;
		$jilu['shequId'] = $shequ->userId;
		$jilu['create_time'] = date("Y-m-d");
		$jilu['pdt_title'] = $db->get_var("select title from zhuisu_pdt where id=$pdtId");
		$jilu['shequ_name'] = $shequ->title;
		$db->insert_update('zhuisu_jilu',$jilu,'id');
		redirect('?m=system&s=zhuisu');
	}
}