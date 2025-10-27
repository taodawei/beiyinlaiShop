<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=商品销量统计（".$_REQUEST['startTime'].'-'.$_REQUEST['endTime']."）.xls");
global $db,$request;
$allRows = array(
	"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
	"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"z_num"=>array("title"=>"销售数量","rowCode"=>"{field:'z_num',title:'销售数量',width:100,sort:true}"),
	"z_price"=>array("title"=>"销售总金额","rowCode"=>"{field:'z_price',title:'销售总金额',width:100,sort:true}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$sql="select sum(a.num) as z_num,sum(a.num*a.unit_price) as z_price,a.inventoryId,a.pdtInfo,b.dtTime from order_detail$fenbiao a left join order$fenbiao b on a.orderId=b.id where a.comId=$comId and a.status>-1 ";
if(!empty($keyword)){
	$pdtId = $db->get_var("select productId from demo_product_inventory where comId=$comId and (sn='$keyword' or title like '%$keyword%') limit 1");
	if(empty($pdtId))$pdtId=0;
	$sql.=" and a.productId=$pdtId";
}
if(!empty($startTime)){
	$sql.=" and b.dtTime>='$startTime'";
}
if(!empty($endTime)){
	$sql.=" and b.dtTime<='$endTime'";
}
$sql.=" group by a.inventoryId";
$sql.=" order by z_num desc limit 50000";
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
			//$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
			/*$unitstr = '';
			$untis = json_decode($product->untis,true);
			foreach ($untis as $u) {
				$unitstr.=$u['title'].'/';
			}
			$unitstr = substr($unitstr,0,strlen($unitstr)-1);
			$pdt->untis = $unitstr;*/
			$pdtInfo = json_decode($pdt->pdtInfo);
			$pdt->sn = $pdtInfo->sn;
			$pdt->title = $pdtInfo->title;
			$pdt->key_vals = $pdtInfo->key_vals;
			$pdt->z_price = getXiaoshu($pdt->z_price,2);
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
