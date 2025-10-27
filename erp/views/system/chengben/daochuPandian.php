<?php
global $db,$request;
$storeId = (int)$request['storeId'];
$storeTitle = $db->get_var("select title from demo_kucun_store where id=$storeId");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=".$storeTitle.'-'.date("Y年m月d日")."-库存成本数据.xls");
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"chengben"=>array("title"=>"成本调整","rowCode"=>"{field:'chengben',title:'成本调整',width:100}")
			);
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
			$pdt->sn = $inventory->sn;
			$pdt->title = $inventory->title;
			$pdt->key_vals = $inventory->key_vals;
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
