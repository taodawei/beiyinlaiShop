<?php
global $db,$request;
$kehuId = $id = (int)$request['id'];
$kehuTitle = $db->get_var("select title from demo_kehu where id=$id");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=".$kehuTitle."-报价单.xls");
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"units"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"price"=>array("title"=>"订货价","rowCode"=>"{field:'price',title:'订货价',width:100,sort:true}")
			);
$comId = $_SESSION[TB_PREFIX.'comId'];
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$order1 = 'id';
$order2 = 'desc';
$sql="select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId ";
if(!empty($channelId)){
	$channelIds = $channelId.getZiIds($channelId);
	$sql.=" and channelId in($channelIds)";
}
if(!empty($keyword)){
	$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
	if(empty($pdtIds))$pdtIds='0';
	$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
}
$sql.=" order by $order1 $order2";
$pdts = $db->get_results($sql);
?>
<table border="1" >   
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
	if(!empty($pdts)){
		foreach ($pdts as $j){
			$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$j->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$j->units = $units[0]['title'];
			$price = getKehuPrice($j->id,$kehuId);
			$j->price = getXiaoshu($price,$product_set->price_num);
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
