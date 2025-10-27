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
header("Content-Disposition:attachment; filename=采购总汇—按明细-".date("Y-m-d").".xls");
$allRows = array(
				"dtTime"=>array("title"=>"采购时间","rowCode"=>"{field:'dtTime',title:'采购时间',width:200,sort:true}"),
				"orderId"=>array("title"=>"采购单号","rowCode"=>"{field:'orderId',title:'采购单号',width:250}"),
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:250}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:100}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:100}"),
				"num"=>array("title"=>"采购数量","rowCode"=>"{field:'num',title:'采购数量',width:100}"),
				"units"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"price"=>array("title"=>"采购价","rowCode"=>"{field:'price',title:'采购价',width:100}"),
				"price_zong"=>array("title"=>"小计","rowCode"=>"{field:'price_zong',title:'小计',width:100}")
			);
$sql="select jiluId,pdtInfo,num,price,units,status,dtTime from demo_caigou_detail$fenbiao where comId=$comId and status=1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$sql.=" order by id desc";
	$jilus = $db->get_results($sql);
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
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d",strtotime($j->dtTime));
			$j->price = getXiaoshu($j->price,$product_set->price_num);
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$j->price_zong = getXiaoshu($j->price*$j->num,$product_set->price_num);
			$pdtInfo = json_decode($j->pdtInfo);
			$j->id = $j->jiluId;
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->orderId = $db->get_var("select orderId from demo_caigou where id=".$j->jiluId);
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
