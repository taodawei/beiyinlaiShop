<?php
function index(){}
function index1(){}
function jilus(){}
function pandian(){}
function pandian1(){}
function pandian2(){}
function daochuPandian(){}
function daochuJilus(){}
function daochuExcel(){
	global $db,$request;
	require_once ABSPATH.'inc/excel.php';
	$pandianJsonData = stripcslashes($request['pandianJsonData']);
	$jilus = json_decode($pandianJsonData,true);
	$indexKey = array('商品编码','商品名称','商品规格','条形码','单位','库存上限','库存下限','库存数量','盘点数量','备注');
	exportExcel($jilus,'盘点失败记录',$indexKey);
	exit;
}
function edit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$inventoryId = (int)$request['inventoryId'];
	$storeId = (int)$request['storeId'];
	$shangxian = $request['shangxian'];
	$xiaxian = $request['xiaxian'];
	$yingyong = (int)$request['yingyong'];
	switch($yingyong){
		case 0:
			$db->query("update demo_kucun set shangxian='$shangxian',xiaxian='$xiaxian' where inventoryId=$inventoryId and storeId=$storeId limit 1");
		break;
		case 1:
			$cangkus = $db->get_results("select id from demo_kucun_store where comId=$comId order by id");
			if(!empty($cangkus)){
				foreach ($cangkus as $c) {
					$db->query("update demo_kucun set shangxian='$shangxian',xiaxian='$xiaxian' where inventoryId=$inventoryId and storeId=".$c->id.' limit 1');
				}
			}
		break;
		case 2:
			$productId = (int)$db->get_var("select productId from demo_product_inventory where id=$inventoryId and comId=$comId");
			$inventorys = $db->get_results("select id from demo_product_inventory where comId=$comId and productId=$productId");
			foreach ($inventorys as $in) {
				$db->query("update demo_kucun set shangxian='$shangxian',xiaxian='$xiaxian' where inventoryId=".$in->id." and storeId=$storeId limit 1");
			}
		break;
		case 3:
			$productId = (int)$db->get_var("select productId from demo_product_inventory where id=$inventoryId and comId=$comId");
			$inventorys = $db->get_results("select id from demo_product_inventory where comId=$comId and productId=$productId");
			$cangkus = $db->get_results("select id from demo_kucun_store where comId=$comId order by id");
			foreach ($inventorys as $in) {
				foreach ($cangkus as $c) {
					$db->query("update demo_kucun set shangxian='$shangxian',xiaxian='$xiaxian' where inventoryId=".$in->id." and storeId=".$c->id.' limit 1');
				}
			}
		break;
	}
	echo '{"code":1,"message":"成功"}';exit;
}
function getList(){
	global $db,$request,$adminRole,$qx_arry;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$storeIds = $request['storeIds'];
	
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('kucunPageNum',$pageNum,time()+3600*24*30);
	$keyword = $request['keyword'];
	$tags = $request['tags'];
	$kczt = $request['kczt'];
	$source = (int)$request['source'];
	$cuxiao = (int)$request['cuxiao'];
	$order1 = empty($request['order1'])?'b.id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'inventoryId';
		$order2 = 'desc';
	}
	if($order1=='title')$order1='entitle';
	$kucun_sql = "select a.inventoryId,a.entitle,a.storeId,a.shangxian,a.xiaxian,a.yugouNum,a.zaituNum,a.kucun,b.id,b.productId,b.title,b.key_vals,b.sn from demo_kucun a inner join demo_product_inventory b on a.inventoryId=b.id where a.comId=$comId ";
	if(!empty($kczt)){
		$kczts = explode(',',$kczt);
		if(in_array(1,$kczts)){
			$kucun_sql.=" and a.kucun>a.shangxian";
		}
		if(in_array(2,$kczts)){
			$kucun_sql.=" and a.kucun<a.xiaxian";
		}
		if(in_array(3,$kczts)){
			$kucun_sql.=" and a.kucun<=0";
		}
	}
	if(!empty($storeIds)){
		$kucun_sql.=" and a.storeId in($storeIds)";
	}
// 	if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
// 		$kucun_sql.=" and a.storeId in(".$qx_arry['kucun']['storeIds'].")";
// 	}
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$kucun_sql.=" and b.channelId in($channelIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$kucun_sql.=" and b.productId in($productIds)";
	}

	if(!empty($status)){
		$kucun_sql.=" and b.status=$status";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$kucun_sql.=" and (b.title like '%$keyword%' or b.sn='$keyword' or b.key_vals like '%$keyword%' or b.productId in($pdtIds))";
	}
	if(!empty($tags)){
		$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
		$pdtIdsql.=" and(1!=1";
		foreach ($tags as $t) {
			$pdtIdsql.=" or tags like '%$t%'";
		}
		$pdtIdsql.=")";
		$pdtIds = $db->get_var($pdtIdsql);
		if(empty($pdtIds))$pdtIds='0';
		$kucun_sql.=" and b.productId in($pdtIds)";
	}
	if(!empty($cuxiao)){
		$kucun_sql.=" and b.cuxiao=$cuxiao";
	}
	if(!empty($source)){
		$kucun_sql.=" and b.source=$source";
	}
	$countsql = str_replace('a.inventoryId,a.entitle,a.storeId,a.shangxian,a.xiaxian,a.yugouNum,a.zaituNum,a.kucun,b.id,b.productId,b.title,b.key_vals','count(*)',$kucun_sql);
	
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	$kucun_sql.=" order by a.kucun asc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($kucun_sql);
	if(!empty($jilus)){
		foreach ($jilus as $i){
			$pdts[] = $i;
		}
	}
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			/*foreach ($units as $u) {
				$unitstr.=$u['title'].'/';
			}
			$unitstr = substr($unitstr,0,strlen($unitstr)-1);*/
			$pdt->units = $unitstr;
			$pdt->store = $db->get_var("select title from demo_kucun_store where id=".$pdt->storeId);
			//$pdt->shangxian = empty($pdt->shangxian)?0:$pdt->shangxian,$product_set->number_num);
			//$pdt->xiaxian = empty($pdt->xiaxian)?0:getXiaoshu($pdt->xiaxian,$product_set->number_num);
			$pdt->yugouNum = $pdt->yugouNum==0?0:'<span style="color:red">'.getXiaoshu($pdt->yugouNum,$product_set->number_num).'</span>';
			$pdt->zaituNum = $pdt->zaituNum==0?0:'<span style="color:red">'.getXiaoshu($pdt->zaituNum,$product_set->number_num).'</span>';
			$pdt->kucun = empty($pdt->kucun)?0:getXiaoshu($pdt->kucun,$product_set->number_num);
			if($pdt->shangxian<$pdt->kucun&&$pdt->shangxian>0){
				$pdt->shangxian = '<span style="color:red">'.getXiaoshu($pdt->shangxian,$product_set->number_num).'</span>';
			}else{
				$pdt->shangxian = empty($pdt->shangxian)?0:getXiaoshu($pdt->shangxian,$product_set->number_num);
			}
			if($pdt->xiaxian>$pdt->kucun){
				$pdt->xiaxian = '<span style="color:red">'.getXiaoshu($pdt->xiaxian,$product_set->number_num).'</span>';
			}else{
				$pdt->xiaxian = empty($pdt->xiaxian)?0:getXiaoshu($pdt->xiaxian,$product_set->number_num);
			}
			if($pdt->kucun<20){
			    $pdt->kucun='<span style="color:red;">'.$pdt->kucun.'</span>';
			}
			$pdt->kucun_keyong = $pdt->kucun-$pdt->yugouNum;
			$pdt->kucun_keyong = empty($pdt->kucun_keyong)?0:getXiaoshu($pdt->kucun_keyong,$product_set->number_num);
			$dataJson['data'][] = $pdt;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getList1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$storeIds = $request['storeIds'];
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('kucunPageNum1',$pageNum,time()+3600*24*30);
	$keyword = $request['keyword'];
	$tags = $request['tags'];
	$kczt = $request['kczt'];
	$cangkus = $request['cangkus'];
	$source = (int)$request['source'];
	$cuxiao = (int)$request['cuxiao'];
	$order1 = empty($request['order1'])?'inventoryId':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'inventoryId';
		$order2 = 'desc';
	}
	if($order1=='title')$order1='entitle';
	$sql = "select inventoryId,productId,entitle,sum(yugouNum) as yugouNum,sum(zaituNum) as zaituNum,sum(kucun) as kucun from demo_kucun where comId=$comId";

	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$productIds = $db->get_var("select group_concat(id) from demo_product_inventory where comId=$comId and ".(empty($status)?'':"status=$status and")." (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds))");
		if(empty($productIds))$productIds='0';
		$sql.=" and inventoryId in($productIds)";
	}else if(!empty($status)){	
		$productIds = $db->get_var("select group_concat(distinct(productId)) from demo_product_inventory where comId=$comId and status=$status");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
	}
	if(!empty($tags)){
		$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
		$pdtIdsql.=" and(1!=1";
		foreach ($tags as $t) {
			$pdtIdsql.=" or tags like '%$t%'";
		}
		$pdtIdsql.=")";
		$pdtIds = $db->get_var($pdtIdsql);
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	$sql.=" group by inventoryId";
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$inventorySql = "select count(*) from demo_product_inventory where comId=$comId";
	if(!empty($status))$inventorySql.=" and status=$status";
	$inventoryNum = $db->get_var($inventorySql);
	$cangkuNum = $db->get_var("select count(*) from demo_kucun_store where comId=$comId");
	$count = $inventoryNum;
	$pdts = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$in = $db->get_row("select key_vals,status,sn from demo_product_inventory where id=".$pdt->inventoryId);
			//if(empty($status)||$status==$in->status){
				$pdt->key_vals = $in->key_vals;
				$pdt->id = $pdt->inventoryId;
				$pdt->sn = $in->sn;
				$product=$db->get_row("select unit_type,untis,title from demo_product where id=".$pdt->productId);
				$pdt->title = $product->title;
				$unitstr = '';
				$units = json_decode($product->untis,true);
				$unitstr = $units[0]['title'];
				$pdt->units = $unitstr;
				$pdt->yugouNum = empty($pdt->yugouNum)?0:getXiaoshu($pdt->yugouNum,$product_set->number_num);
				$pdt->zaituNum = empty($pdt->zaituNum)?0:getXiaoshu($pdt->zaituNum,$product_set->number_num);
				$pdt->kucun = empty($pdt->kucun)?0:getXiaoshu($pdt->kucun,$product_set->number_num);
				$pdt->kucun_keyong = $pdt->kucun-$pdt->yugouNum;
				$pdt->kucun_keyong = empty($pdt->kucun_keyong)?0:getXiaoshu($pdt->kucun_keyong,$product_set->number_num);
				$dataJson['data'][] = $pdt;
			//}
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getJilus(){
	global $db,$request,$adminRole,$qx_arry;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenBiao($comId,20);
	$inventoryId = (int)$request['inventoryId'];
	$channelId = (int)$request['channelId'];
	$storeIds = $request['storeIds'];
	$brandId = (int)$request['brandId'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$type = $request['type'];
	$tags = $request['tags'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('jiluPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_kucun_jiludetail$fenbiao where comId=$comId and num<>0";
	if(!empty($inventoryId)){
		$sql.=" and inventoryId=$inventoryId";
	}else if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$productIds = $db->get_var("select group_concat(distinct(id)) from demo_product_inventory where comId=$comId and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds))");
		if(empty($productIds))$productIds='0';
		$sql.=" and inventoryId in($productIds)";
	}
// 	if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
// 		$sql.=" and storeId in(".$qx_arry['kucun']['storeIds'].")";
// 	}
	if(!empty($storeIds)){
		$sql.=" and storeId in($storeIds)";
	}
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
	}
	if(!empty($tags)){
		$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
		$pdtIdsql.=" and(1!=1";
		foreach ($tags as $t) {
			$pdtIdsql.=" or tags like '%$t%'";
		}
		$pdtIdsql.=")";
		$pdtIds = $db->get_var($pdtIdsql);
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($type)){
		$types = str_replace(',',"','",$type);
		$types = "'".$type."'";
		$sql.=" and typeInfo in($types)";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$pdtInfo = json_decode($j->pdtInfo);
			$j->id = $j->jiluId;
			if($j->caigouId==-1){
				$j->id = 'T_'.$j->id;
			}
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->chengben_one = getXiaoshu($j->zongchengben/abs($j->num),2);
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$j->num = $j->num>0?'<span style="color:green">+'.$j->num.'</span>':'<span style="color:red">'.$j->num.'</span>';
			$j->kucun = getXiaoshu($j->kucun,$product_set->number_num);
			$status = '';
			$j->layclass = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$status = '待审核';
				break;
				case 1:
					$status = '<span style="color:green">已审核</span>';
				break;
				case -2:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已作废</span>';
				break;
			}
			$j->status = $status;
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			$j->storeName = $db->get_var("select title from demo_kucun_store where id=$j->storeId");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function jilu_detail(){}
function shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select shenheUser,status,caigouId,type,store1Id from demo_kucun_jilu$fenbiao where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=0){
		echo '{"code":0,"message":"该任务已经处理过了！"}';
		exit;
	}
	if($jilu->shenheUser!=$userId){
		echo '{"code":0,"message":"您没有权限处理该审批！"}';
		exit;
	}
	$jiluDetails = $db->get_results("select id,inventoryId,storeId,num,type,caigouId,chengben from demo_kucun_jiludetail$fenbiao where jiluId=$jiluId and status=0");
	$dtTime = date("Y-m-d H:i:s");
	if(!empty($jiluDetails)){
		if($status==1){
			foreach ($jiluDetails as $j){
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and status=1 order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				if($j->type==1){
					$zongchengben = $j->chengben+$lastJilu->zongchengben;
				}else{
					$zongchengben = $lastJilu->zongchengben-$j->chengben;
				}
				$zongNum = $lastJilu->kucun+$j->num;
				$chengben = getXiaoshu($zongchengben/$zongNum,4);
				if($chengben<0)$chengben=0;
				$sql = "update demo_kucun set kucun=kucun+".$j->num.",chengben='".$chengben."'";
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);
				$db->query("update demo_product_inventory set kucun=kucun+$j->num where id=$j->inventoryId");
				if($jilu->type==3){
					$db->query("update demo_kucun set zaituNum=zaituNum+".abs($j->num)." where inventoryId=$j->inventoryId and storeId=$jilu->store1Id limit 1");
				}
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1");
				$db->query("update demo_kucun_jiludetail$fenbiao set status=1,kucun='$kucun',shenheTime='$dtTime',zongchengben='$zongchengben' where id=".$j->id);
			}
		}else{
			foreach ($jiluDetails as $j){
				/*$sql = "update demo_kucun set ";
				if($j->type==1){
					$sql.="zaituNum=zaituNum-".abs($j->num);
				}else if($j->type==2){
					$sql.="yugouNum=yugouNum-".abs($j->num);
				}
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);*/
				if($j->type==1&&$j->caigouId>0){
					$db->query("update demo_caigou_detail$fenbiao set hasNum=hasNum-".$j->num." where id=".$j->caigouId);
				}
			}
			$db->query("update demo_kucun_jiludetail$fenbiao set status=$status,shenheTime='$dtTime' where jiluId=$jiluId and status=0");
			if($jilu->caigouId>0){
				$ifhas = $db->get_var("select id from demo_kucun_jiludetail$fenbiao where jiluId=$jilu->caigouId and hasNum>0 limit 1");
				if($ifhas>0){
					$rukuStatus = 1;
				}else{
					$rukuStatus = 0;
				}
				$db->query("update demo_caigou set rukuStatus=$rukuStatus where id=".$jilu->caigouId);
			}
		}
		$db->query("update demo_kucun_jilu$fenbiao set status=$status,shenheTime='$dtTime',shenheCont='$cont' where id=$jiluId");
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function diaoboRuku(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluId = (int)$request['jiluId'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select shenheUser,status,caigouId,store1Id,store1Name,userId from demo_kucun_jilu$fenbiao where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=1){
		echo '{"code":0,"message":"该订单不是待入库状态，请刷新后重试！"}';
		exit;
	}
	if($jilu->userId!=$userId){
		echo '{"code":0,"message":"您没有权限进行入库操作！"}';
		exit;
	}
	if(empty($jilu->store1Id)){
		echo '{"code":0,"message":"系统错误，请重试"}';
		exit;
	}
	$jiluDetails = $db->get_results("select id,inventoryId,storeId,num,type,caigouId,chengben,productId,pdtInfo,units from demo_kucun_jiludetail$fenbiao where jiluId=$jiluId and status=1");
	$dtTime = date("Y-m-d H:i:s");
	$storeId = $jilu->store1Id;
	$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values";
	$rukuSql1 = "";
	if(!empty($jiluDetails)){
		foreach ($jiluDetails as $j){
			$j->num = abs($j->num);
			$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$storeId and status=1 order by id desc limit 1");
			if(empty($lastJilu)){
				$lastJilu->zongchengben = 0;
				$lastJilu->kucun = 0;
			}
			$zongchengben = $j->chengben+$lastJilu->zongchengben;
			$zongNum = $lastJilu->kucun+$j->num;
			$chengben = getXiaoshu($zongchengben/$zongNum,4);
			if($chengben<0)$chengben=0;
			$sql = "update demo_kucun set kucun=kucun+".$j->num.",zaituNum=zaituNum-".$j->num.",chengben='".$chengben."'";
			$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$storeId." limit 1";
			$db->query($sql);
			$db->query("update demo_product_inventory set kucun=kucun+$j->num where id=$j->inventoryId");
			$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$j->inventoryId." and storeId=".$storeId." limit 1");
			$rukuSql1.=",($comId,$jiluId,$j->inventoryId,$j->productId,'$j->pdtInfo',$storeId,'$jilu->store1Name','$j->num',1,'$kucun','',1,'调拨入库','$dtTime','$j->units','$j->chengben','$zongchengben')";
		}
		$rukuSql1 = substr($rukuSql1,1);
		$db->query($rukuSql.$rukuSql1);
		$db->query("update demo_kucun_jilu$fenbiao set status=2,dtTime1='$dtTime' where id=$jiluId");
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}