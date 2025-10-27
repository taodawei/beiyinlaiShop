<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=商品列表—".date("Y-m-d H:i:s").".xls");
global $db,$request;
$allRows = array(
				"sn"=>array("title"=>"商品编码(请勿修改)","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true}"),
				"title"=>array("title"=>"商品名称(请勿修改)","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格(请勿修改)","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"ordering"=>array("title"=>"排序(降序)","rowCode"=>"{field:'ordering',title:'排序(降序)',width:100,sort:true}"),
				"status"=>array("title"=>"上架状态(0下架 1上架)","rowCode"=>"{field:'status',title:'上架状态(0下架 1上架)',width:100,sort:true}"),
				"weight"=>array("title"=>"重量","rowCode"=>"{field:'weight',title:'重量',width:100,sort:true}"),
				"price_sale"=>array("title"=>"零售价","rowCode"=>"{field:'price_sale',title:'零售价',width:100,sort:true}"),
				"price_market"=>array("title"=>"市场价","rowCode"=>"{field:'price_market',title:'市场价',width:100,sort:true}"),
				"code"=>array("title"=>"条形码","rowCode"=>"{field:'code',title:'条形码',width:100,sort:true}")
			);
$comId = $_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}

$channelId = (int)$request['channelId'];
$status = (int)$request['status'];
$keyword = $request['keyword'];
$tags = $request['tags'];
$source = (int)$request['source'];
$cuxiao = (int)$request['cuxiao'];
$sql="select * from demo_product_inventory where comId=$comId";
if(!empty($channelId)){
	$channelIds = $channelId.getZiIds($channelId);
	$sql.=" and channelId in($channelIds)";
}
if(!empty($status)){
	$sql.=" and status=$status";
}

$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];
if($mendianId > 0){
    $pdtIds = $db->get_var("select group_concat(id) from demo_product where mendianId = $mendianId ");
	if(empty($pdtIds))$pdtIds='0';
    $sql.=" and productId in($pdtIds)";
}

if(!empty($keyword)){
	$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
	if(empty($pdtIds))$pdtIds='0';
	$sql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
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
if(!empty($cuxiao)){
	$sql.=" and cuxiao=$cuxiao";
}
if(!empty($source)){
	$sql.=" and source=$source";
}
$sql.=" order by channelId asc,id desc limit 50000";
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
		foreach ($pdts as $pdt){
			$product=$db->get_row("select unit_type,untis,brandId,mendianId from demo_product where id=".$pdt->productId);
			/*$unitstr = '';
			$untis = json_decode($product->untis,true);
			foreach ($untis as $u) {
				$unitstr.=$u['title'].'/';
			}
			$unitstr = substr($unitstr,0,strlen($unitstr)-1);
			$pdt->untis = $unitstr;*/
			$mendianTitle = '自营产品';
			if($product->mendianId > 0){
			    $mendianTitle = $db->get_var("select title from demo_shequ where id = $product->mendianId");
			}
			
			$pdt->mendianTitle = $mendianTitle;
			$pdt->price_market = getXiaoshu($pdt->price_market,$product_set->price_num);
			$pdt->price_cost = getXiaoshu($pdt->price_cost,$product_set->price_num);
			$pdt->price_sale = getXiaoshu($pdt->price_sale,$product_set->price_num);
			$pdt->price_tuan = getXiaoshu($pdt->price_tuan,$product_set->price_num);
			$pdt->price_shequ_tuan = getXiaoshu($pdt->price_shequ_tuan,$product_set->price_num);
			$pdt->fanli_shequ = getXiaoshu($pdt->fanli_shequ,$product_set->price_num);
			$pdt->fanli_tuanzhang = getXiaoshu($pdt->fanli_tuanzhang,$product_set->price_num);
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
