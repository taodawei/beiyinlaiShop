<?php
function index(){}
function daochu(){}
function jilu_detail(){}
function getjilus(){
	global $db,$request,$adminRole,$qx_arry;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$keyword = $request['keyword'];
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('shoufaPageNum',$pageNum,time()+3600*24*30);
	$order1 = 'inventoryId';
	$order2 = 'desc';
	$sql="select inventoryId,storeId from demo_kucun_jiludetail$fenbiao where comId=$comId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59' ";
	$countSql = "SELECT COUNT(DISTINCT inventoryId,storeId) from demo_kucun_jiludetail$fenbiao where comId=$comId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59' ";
	if(!empty($keyword)){
		$sql.=" and pdtInfo like '%$keyword%'";
		$countSql.=" and pdtInfo like '%$keyword%'";
	}
	if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
		$sql.=" and storeId in(".$qx_arry['kucun']['storeIds'].")";
		$countSql.=" and storeId in(".$qx_arry['kucun']['storeIds'].")";
	}
	if(!empty($storeIds)){
		$sql.=" and storeId in($storeIds)";
		$countSql.=" and storeId in($storeIds)";
	}
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
		$countSql.=" and productId in($pdtIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
		$countSql.=" and productId in($productIds)";
	}
	$sql.=" group by inventoryId,storeId";
	$count = $db->get_var($countSql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>$countSql,"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$churuku = $db->get_results("select sum(num) as zongNum,sum(chengben) as zongchengben,type,units from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59' group by type");
			$qichu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and dtTime<'$startTime' and status=1 order by id desc limit 1");
			if(!empty($qichu)){
				$j->num_qichu = getXiaoshu($qichu->kucun,$product_set->number_num);
				$j->price_qichu = $qichu->zongchengben;
				$j->price_qichu_per = getXiaoshu($qichu->zongchengben/$j->num_qichu,2);
			}else{
				$j->num_qichu = 0;
				$j->price_qichu = 0;
				$j->price_qichu_per = 0;
			}
			$j->num_ruku = 0;
			$j->price_ruku = 0;
			$j->num_chuku = 0;
			$j->price_chuku = 0;
			if(!empty($churuku)){
				foreach ($churuku as $c) {
					if($c->type==1){
						$j->num_ruku = getXiaoshu($c->zongNum,$product_set->number_num);
						$j->price_ruku = $c->zongchengben;
						$j->units = $c->units;
						$j->storeName = $c->storeName;
					}else if($c->type==2){
						$j->num_chuku = ABS(getXiaoshu($c->zongNum,$product_set->number_num));
						$j->price_chuku = $c->zongchengben;
						$j->units = $c->units;
						$j->storeName = $c->storeName;
					}
				}
			}
			/*$j->num_qimo = $j->num_ruku-$j->num_chuku;
			$j->price_qimo = $j->price_ruku-$j->price_chuku;*/
			$qimo = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and dtTime<='$endTime 23:59:59' and status=1 order by id desc limit 1");
			if(!empty($qimo)){
				$j->num_qimo = getXiaoshu($qimo->kucun,$product_set->number_num);
				$j->price_qimo = $qimo->zongchengben;
				$j->price_qimo_per = getXiaoshu($qimo->zongchengben/$j->num_qimo,2);
			}else{
				$j->num_qimo = 0;
				$j->price_qimo = 0;
				$j->price_qimo_per = 0;
			}
			$pdtInfo = $db->get_row("select title,sn,key_vals from demo_product_inventory where id=$j->inventoryId");
			$j->id = $j->inventoryId;
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->storeName = $db->get_var("select title from demo_kucun_store where id=".$j->storeId);
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getPdtJilus(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$inventoryId = (int)$request['inventoryId'];
	$storeId = (int)$request['storeId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = 'id';
	$order2 = 'desc';
	$sql="select jiluId,dtTime,typeInfo,type,num,chengben,kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59' ";
	$countSql = "SELECT COUNT(*) from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59'";
	$count = $db->get_var($countSql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>$countSql,"count"=>$count,"data"=>array());
	$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 and dtTime<'$startTime' order by id desc limit 1");
	$lastJilu->typeInfo = '期初';
	$lastJilu->num_jieyu = empty($lastJilu->kucun)?0:$lastJilu->kucun;
	$lastJilu->chengben_jieyu = empty($lastJilu->zongchengben)?0:$lastJilu->zongchengben;
	$lastJilu->price_jieyu = empty($lastJilu->kucun)?0:getXiaoshu($lastJilu->zongchengben/$lastJilu->kucun,$product_set->price_num);
	$dataJson['data'][] = $lastJilu;
	if(!empty($jilus)){
		foreach ($jilus as $j){
			if($j->type==1){
				$j->num_ruku = $j->num;
				$j->chengben_ruku = $j->chengben;
				$j->price_ruku = getXiaoshu($j->chengben/$j->num,$product_set->price_num);
			}else{
				$j->num = abs($j->num);
				$j->num_chuku = $j->num;
				$j->chengben_chuku = $j->chengben;
				$j->price_chuku = getXiaoshu($j->chengben/$j->num,$product_set->price_num);
			}
			$j->num_jieyu = $j->kucun;
			$j->chengben_jieyu = $j->zongchengben;
			$j->price_jieyu = getXiaoshu($j->zongchengben/$j->kucun,$product_set->price_num);
			$j->orderId = $db->get_var("select orderId from demo_kucun_jilu$fenbiao where id=$j->jiluId");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}