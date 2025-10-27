<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$fenbiao = getFenbiao($comId,20);
$orderNum = $db->get_row("select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and status=1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
$pdtNum = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and status=1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
$orderNum->orderNum=empty($orderNum->orderNum)?0:$orderNum->orderNum;
$orderNum->priceNum=empty($orderNum->priceNum)?0:$orderNum->priceNum;
if(empty($pdtNum))$pdtNum=0;
$orderNum->priceNum = getXiaoshu($orderNum->priceNum,2);
$pdtNum = getXiaoshu($pdtNum,$product_set->number_num);
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=采购总汇—按供应商-".date("Y-m-d").".xls");
$allRows = array(
				"title"=>array("title"=>"供应商名称","rowCode"=>"{field:'title',title:'供应商名称',width:200,sort:true}"),
				"orderIds"=>array("title"=>"采购单总数","rowCode"=>"{field:'orderIds',title:'采购单总数',width:250}"),
				"nums"=>array("title"=>"采购商品数","rowCode"=>"{field:'nums',title:'采购商品数',width:250}"),
				"price"=>array("title"=>"采购总金额","rowCode"=>"{field:'price',title:'采购总金额',width:100}")
			);
$sql = "select id,title from demo_supplier where comId=$comId order by id asc ";
$suppliers = $db->get_results($sql);
?>
<table border="1">   
	<tbody>
	<tr>
		<td>采购单总数:<?=$orderNum->orderNum?></td>
		<td>采购商品数:<?=$pdtNum?></td>
		<td>采购总金额:<?=$orderNum->priceNum?></td>
	</tr>
	<tr></tr>
	<tr>
		<?
		foreach ($allRows as $row=>$isshow){
			?>
			<td><?=$isshow['title']?></td>
			<?
		}
		?>
	</tr>

	<?
	if(!empty($suppliers)){
		foreach ($suppliers as $j){
			$ordersql = "select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and supplierId=$j->id and status=1";
			$pdtsql = "select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and supplierId=$j->id and status=1";
			if(!empty($startTime)){
				$ordersql.=" and dtTime>='$startTime 00:00:00'";
				$pdtsql.=" and dtTime>='$startTime 00:00:00'";
			}
			if(!empty($endTime)){
				$ordersql.=" and dtTime<='$endTime 23:59:59'";
				$pdtsql.=" and dtTime<='$endTime 23:59:59'";
			}
			$o1 = $db->get_row($ordersql);
			$o2 = $db->get_var($pdtsql);
			$j->orderIds = empty($o1->orderNum)?0:$o1->orderNum;
			$j->price = empty($o1->priceNum)?0:$o1->priceNum;
			$j->nums = empty($o2)?0:$o2;
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
