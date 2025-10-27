<?php
global $db,$request,$adminRole,$qx_arry;
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=出入库明细.xls");
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"units"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"dtTime"=>array("title"=>"出入库日期","rowCode"=>"{field:'dtTime',title:'出入库日期',width:200,sort:true}"),
				"num"=>array("title"=>"出入库数量","rowCode"=>"{field:'num',title:'出入库数量',width:100}"),
				"kucun"=>array("title"=>"库存量","rowCode"=>"{field:'kucun',title:'库存量',width:100}"),
				"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:150}")
			);
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
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
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$sql="select * from demo_kucun_jiludetail$fenbiao where comId=$comId ";
if(!empty($inventoryId)){
	$sql.=" and inventoryId=$inventoryId";
}else if(!empty($keyword)){
	$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
	if(empty($pdtIds))$pdtIds='0';
	$productIds = $db->get_var("select group_concat(distinct(productId)) from demo_product_inventory where comId=$comId and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds))");
	if(empty($productIds))$productIds='0';
	$sql.=" and productId in($productIds)";
}
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
	$sql.=" and storeId in(".$qx_arry['kucun']['storeIds'].")";
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
$sql.=" order by id desc";
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
			$pdtInfo = json_decode($pdt->pdtInfo);
			$pdt->sn = $pdtInfo->sn;
			$pdt->title = $pdtInfo->title;
			$pdt->key_vals = $pdtInfo->key_vals;
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$pdt->chengben_one = getXiaoshu($pdt->zongchengben/abs($pdt->num),2);
			$pdt->num = getXiaoshu($pdt->num,$product_set->number_num);
			$pdt->num = $pdt->num>0?'<span style="color:green">+'.$pdt->num.'</span>':'<span style="color:red">'.$pdt->num.'</span>';
			$pdt->kucun = getXiaoshu($pdt->kucun,$product_set->number_num);
			$status = '';
			switch ($pdt->status){
				case -1:
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$status = '待审核';
				break;
				case 1:
					$status = '<span style="color:green">已审核</span>';
				break;
			}
			$pdt->status = $status;
			$pdt->shenheTime = empty($pdt->shenheTime)?'':date("Y-m-d H:i",strtotime($pdt->shenheTime));
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
