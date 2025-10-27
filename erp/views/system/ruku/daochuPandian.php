<?php
global $db,$request;
$storeId = (int)$request['storeId'];
$storeTitle = $db->get_var("select title from demo_kucun_store where id=$storeId");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=".$storeTitle.'-'.date("Y年m月d日")."-入库数据.xls");
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"untis"=>array("title"=>"单位","rowCode"=>"{field:'untis',title:'单位',width:100}"),
				"kucun"=>array("title"=>"库存数量","rowCode"=>"{field:'kucun',title:'库存数量',width:100}"),
				"pandian"=>array("title"=>"入库数量","rowCode"=>"{field:'pandian',title:'入库数量',width:100}"),
				"chengben"=>array("title"=>"入库总成本","rowCode"=>"{field:'chengben',title:'入库总成本',width:100}"),
				"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:100}")
			);
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}
$comId = (int)$_SESSION[TB_PREFIX.'comId'];

$channelId = (int)$request['channelId'];
$sql = "select inventoryId,shangxian,xiaxian,kucun from demo_kucun where comId=$comId and storeId=$storeId";
if(!empty($channelId)){
	$channelIds = $channelId.getZiIds($channelId);
	$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
	if(empty($pdtIds))$pdtIds='0';
	$sql.=" and productId in($pdtIds)";
}
$jilus = $db->get_results($sql);
?>
<table border="1">
	<tbody><tr>
		<?
		foreach ($allRows as $row=>$isshow){
			?>
			<td><?=$isshow['title']?></td>
			<?
		}
		?>
	</tr>
	<?
	if(!empty($jilus)){
		foreach ($jilus as $pdt){
			$inventory = $db->get_row("select title,key_vals,sn,code,productId from demo_product_inventory where id=".$pdt->inventoryId);
			$product=$db->get_row("select unit_type,untis from demo_product where id=".$inventory->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			$pdt->sn = $inventory->sn;
			$pdt->title = $inventory->title;
			$pdt->key_vals = $inventory->key_vals;
			$pdt->code = $inventory->code;
			$pdt->untis = $unitstr;
			$pdt->kucun = getXiaoshu($pdt->kucun,$product_set->number_num);
			$pdt->shangxian = getXiaoshu($pdt->shangxian,$product_set->number_num);
			$pdt->xiaxian = getXiaoshu($pdt->xiaxian,$product_set->number_num);
			?>
			<tr>
				<?
				foreach ($allRows as $row=>$isshow){
					?>
					<td style="vnd.ms-excel.numberformat:@"><?=$pdt->$row?></td>
					<?
				}
				?>
			</tr>
		<?
		}
	}
?>
</tbody></table>
