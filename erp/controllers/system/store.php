<?php
function index(){}
function getList(){
	global $db,$request;
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from demo_kucun_store where comId=$comId";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id asc limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$layclass = '';
			if($pdt->status!=1)$layclass ='deleted';
			$pdtstr.=',{"id":'.$pdt->id.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","address":"'.$pdt->address.'","position":"'.$pdt->position.'","areaId":"'.$pdt->areaId.'","name":"'.$pdt->name.'","phone":"'.$pdt->phone.'","status":"'.($pdt->status==1?'<font color=\"green\">已启用</font>':'<font color=\"red\">已禁用</font>').'","layclass":"'.$layclass.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function editStore(){
	global $db,$request;
	if($_SESSION['tijiao']==1){
		$_SESSION['tijiao'] = 0;
		$id = (int)$request['id'];
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$title = $request['title'];
		$sn = $request['sn'];
		$address = $request['address'];
		$position = $request['hengzuobiao'];
		if(!empty($position)&&!empty($request['zongzuobiao'])){
			$position.='|'.$request['zongzuobiao'];
		}
		$areaId = (int)$request['areaId'];
		$name = $request['name'];
		$phone = $request['phone'];
		if(empty($id)){
			$db->query("insert into demo_kucun_store(comId,title,sn,address,position,areaId,name,phone) value($comId,'$title','$sn','$address','$position',$areaId,'$name','$phone')");
			$storeId = $db->get_var("select last_insert_id();");
			initKucun($storeId);
		}else{
			$db->query("update demo_kucun_store set title='$title',sn='$sn',address='$address',position='$position',areaId=$areaId,name='$name',phone='$phone' where id=$id");
		}
	}
	redirect("?m=system&s=store");
}
function delete(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ifhas = $db->query("select id from demo_kucun where storeId=$id limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"删除失败，不能删除有业务关联的仓库！"}';
	}else{
		$db->query("delete from demo_kucun_store where id=$id and comId=$comId");
		echo '{"code":1,"message":"删除成功！"}';
	}
	exit;
}
function jinyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_kucun_store set status=0 where id=$id and comId=$comId");
	echo '{"code":1,"message":"禁用成功！"}';
	exit;
}
function qiyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_kucun_store set status=1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"启用成功！"}';
	exit;
}
//初始化库存数据
function initKucun($storeId){
	global $db;
	set_time_limit(100);
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$nums = $db->get_var("select count(*) from demo_product_inventory where comId=$comId");
	$sqlNums = ceil($nums/1000);
	for($i=0;$i<$sqlNums;$i++){
		$insertSql = "insert into demo_kucun(comId,inventoryId,productId,storeId,entitle) values";
		$insertSql1 = '';
		$ids = $db->get_results("select id,productId,title from demo_product_inventory where comId=$comId order by id asc limit ".($i*1000).',1000');
		if(!empty($ids)){
			foreach($ids as $in){
				$entitle = getFirstCharter($in->title);
				$insertSql1.=",($comId,".$in->id.",".$in->productId.",$storeId,'$entitle')";
			}
			$insertSql1 = substr($insertSql1,1);
			$db->query($insertSql.$insertSql1);
		}
	}
}
//电子面单信息
function kuaidi(){}
function getKuaidiList(){
	global $db,$request;
	$page = (int)$request['page'];
	$pageNum = 10;
	$storeId = $request['storeId'];
	$sql = "select * from demo_kuaidiniao where storeId=$storeId";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id asc limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$layclass = '';
			if($pdt->status!=1)$layclass ='deleted';
			$pdtstr.=',{"id":'.$pdt->id.',"EBusinessID":"'.$pdt->EBusinessID.'","AppKey":"'.$pdt->AppKey.'","kuaidi_company":"'.$pdt->kuaidi_company.'","kuaidi_title":"'.$pdt->kuaidi_title.'","CustomerName":"'.$pdt->CustomerName.'","CustomerPwd":"'.$pdt->CustomerPwd.'","MonthCode":"'.$pdt->MonthCode.'","SendSite":"'.$pdt->SendSite.'","fahuo_user":"'.$pdt->fahuo_user.'","fahuo_phone":"'.$pdt->fahuo_phone.'","print_name":"'.$pdt->print_name.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function editKuaidi(){
	global $db,$request;
	$kuaidi = array();
	$kuaidi['id'] = (int)$request['kuaidiId'];
	$kuaidi['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$kuaidi['storeId'] = (int)$request['storeId'];
	$kuaidi['EBusinessID'] = $request['EBusinessID'];
	$kuaidi['AppKey'] = $request['AppKey'];
	$kuaidi['kuaidi_company'] = $request['kuaidi_company'];
	$kuaidi['kuaidi_title'] = $request['kuaidi_title'];
	$kuaidi['CustomerName'] = $request['CustomerName'];
	$kuaidi['CustomerPwd'] = $request['CustomerPwd'];
	$kuaidi['MonthCode'] = $request['MonthCode'];
	$kuaidi['SendSite'] = $request['SendSite'];
	$kuaidi['fahuo_user'] = $request['fahuo_user'];
	$kuaidi['fahuo_phone'] = $request['fahuo_phone'];
	$kuaidi['print_name'] = $request['print_name'];
	$db->insert_update('demo_kuaidiniao',$kuaidi,'id');
	redirect("?m=system&s=store&a=kuaidi&storeId=".$kuaidi['storeId']);
}
function del_kuaidi(){
	global $db,$request;
	$db->query("delete from demo_kuaidiniao where id=".(int)$request['id']);
	die('{"code":1,"message":"撒旦法"}');
}