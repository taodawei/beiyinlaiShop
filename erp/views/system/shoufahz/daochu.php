<?php
global $db,$request,$adminRole,$qx_arry;
$comId = $_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}
$storeIds = $request['storeIds'];
$keyword = $request['keyword'];
$channelId = (int)$request['channelId'];
$brandId = (int)$request['brandId'];
$startTime = empty($request['startTime'])?date("Y-m-01"):$request['startTime'];
$endTime = empty($request['endTime'])?date("Y-m-d"):$request['endTime'];
$fenbiao = getFenbiao($comId,20);
$sql1 = "SELECT distinct inventoryId,storeId  FROM `demo_kucun_jiludetail$fenbiao` where comId=$comId and status=1 and dtTime<'$startTime'";
if(!empty($storeIds)){
	$sql1.=" and storeId in($storeIds)";
}
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
	$sql1.=" and storeId in(".$qx_arry['kucun']['storeIds'].")";
}
if(!empty($keyword)){
	$sql1.=" and pdtInfo like '%$keyword%'";
}
if(!empty($channelId)){
	$channelIds = $channelId.getZiIds($channelId);
	$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
	if(empty($pdtIds))$pdtIds='0';
	$sql1.=" and productId in($pdtIds)";
}
if(!empty($brandId)){
	$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
	if(empty($productIds))$productIds='0';
	$sql1.=" and productId in($productIds)";
}
$qichuJilus = $db->get_results($sql1);
/*$price1 = 0;
$price2 = 0;
$price3 = 0;
if(!empty($qichuJilus)){
	foreach ($qichuJilus as $jilu){
		$chengben = $db->get_var("select zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$jilu->inventoryId and storeId=$jilu->storeId order by id desc limit 1");
		$price1+=$chengben;
	}
}
$sql2 = "select sum(chengben) as zongchengben,type from demo_kucun_jiludetail$fenbiao where comId=$comId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59'";
if(!empty($storeIds)){
	$sql2.=" and storeId in($storeIds)";
}
if(!empty($keyword)){
	$sql2.=" and pdtInfo like '%$keyword%'";
}
if(!empty($channelId)){
	$channelIds = $channelId.getZiIds($channelId);
	$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
	if(empty($pdtIds))$pdtIds='0';
	$sql2.=" and productId in($pdtIds)";
}
if(!empty($brandId)){
	$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
	if(empty($productIds))$productIds='0';
	$sql2.=" and productId in($productIds)";
}
$sql2.=" group by type";
$churuku = $db->get_results($sql2);
if(!empty($churuku)){
	foreach ($churuku as $c) {
		switch ($c->type){
			case 1:
			$price2+=$c->zongchengben;
			break;
			case 2:
			$price3+=$c->zongchengben;
			break;
		}
	}
}*/
$sql="select inventoryId,storeId from demo_kucun_jiludetail$fenbiao where comId=$comId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59' ";
if(!empty($keyword)){
	$sql.=" and pdtInfo like '%$keyword%'";
}
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
$sql.=" group by inventoryId,storeId";
$sql.=" order by inventoryId desc";
$jilus = $db->get_results($sql);
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment;filename=商品收发汇总.xls");
$allRows = array(
	"sn"=>"商品编码",
	"title"=>"商品名称",
	"key_vals"=>"规格",
	"storeName"=>"所属仓库",
	"units"=>"单位",
	"num_qichu"=>"期初数量",
	"price_qichu"=>"期初总成本",
	"price_qichu_per"=>"期初平均成本",
	"num_ruku"=>"入库数量",
	"price_ruku"=>"入库成本金额",
	"num_chuku"=>"出库数量",
	"price_chuku"=>"出库成本金额",
	"num_qimo"=>"期末数量",
	"price_qimo"=>"期末总成本",
	"price_qimo_per"=>"期末平均成本"
);
?>
<table border="1">
	<tbody>
	<!-- <tr><td colspan="13">
		<span>期初成本金额：<?=$price1?></span>
		<span>入库成本金额：<?=$price2?></span>
		<span>出库成本金额：<?=$price3?></span>
		<span>期末成本金额：<?=$price2-$price3?></span>
	</td></tr> -->
	<tr>
	<?
		foreach ($allRows as $row=>$isshow){
			?>
			<td><?=$isshow?></td>
			<?
		}
		?>
	</tr>
	<?
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
			?>
			<tr>
				<?
				foreach ($allRows as $row=>$isshow){
					?>
					<td style="vnd.ms-excel.numberformat:@"><?=$j->$row?></td>
					<?
				}
				?>
			</tr>
			<?
		}
	}
	?>
</tbody></table>
