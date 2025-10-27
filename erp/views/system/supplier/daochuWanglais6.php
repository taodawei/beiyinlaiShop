<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
}
$id=$supplierId = (int)$request['id'];
$fenbiao = getFenbiao($comId,20);
$price_type = (int)$request['price_type'];
$weikuan = (int)$request['weikuan'];
$supplierId = (int)$request['id'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$supplier = $db->get_row("select id,title from demo_supplier where id=$supplierId");
$xiangouSql = "select sum(price) from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=0 and status=1";
//赊购
$shegouSql = "select sum(price) as alls,sum(price_payed) as payed,sum(price_weikuan) as weikuan from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=1 and status=1";
//已结
$yijieSql = "select sum(money) from demo_caigou_repay where comId=$comId and supplierId=$supplierId";
//退款
$tuikuanSql = "select sum(money) from demo_caigou_tuikuan where comId=$comId and supplierId=$supplierId and status=1";
if(!empty($startTime)){
	$xiangouSql.=" and dtTime>='$startTime 00:00:00'";
	$shegouSql.=" and dtTime>='$startTime 00:00:00'";
	$yijieSql.=" and dtTime>='$startTime 00:00:00'";
	$tuikuanSql.=" and dtTime>='$startTime 00:00:00'";
}
if(!empty($endTime)){
	$xiangouSql.=" and dtTime<='$startTime 00:00:00'";
	$shegouSql.=" and dtTime<='$startTime 00:00:00'";
	$yijieSql.=" and dtTime<='$startTime 00:00:00'";
	$tuikuanSql.=" and dtTime<='$startTime 00:00:00'";
}
$xiangou = $db->get_var($xiangouSql);
$shegou = $db->get_row($shegouSql);
$yijie = $db->get_var($yijieSql);
$tuikuan = $db->get_var($tuikuanSql);
$xiangou = empty($xiangou)?0:$xiangou;
$yijie = empty($yijie)?0:$yijie;
$tuikuan = empty($tuikuan)?0:$tuikuan;
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=".$supplier->title."—往来账-采购退货订单.xls");
$allRows = array(
	"orderId"=>"退货单号",
	"dtTime"=>"退货时间",
	"money"=>"退款金额",
	"username"=>"经办人"
);
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$sql="select id,orderId,dtTime,money,username from demo_caigou_tuikuan where comId=$comId and supplierId=$supplierId and status=1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
$sql.=" order by id desc limit 30000";
$pdts = $db->get_results($sql);
?>
<table border="1">
	<tbody>
	<tr>
		<td>采购总金额:<?=$xiangou+$shegou->alls;?></td>
		<td>现购金额:<?=$xiangou?></td>
		<td>预付款金额:<?=empty($shegou->payed)?0:$shegou->payed;?></td>
		<td>已结金额:<?=$yijie?></td>
		<td>欠款金额:<?=empty($shegou->weikuan)?0:$shegou->weikuan?></td>
		<td>退货金额:<?=$tuikuan?></td>
		<td>实际总金额:<?=$xiangou+$shegou->alls-$tuikuan?></td>
	</tr>
	<tr></tr>
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
	if(!empty($pdts)){
		foreach ($pdts as $pdt){
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
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
