<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$fenbiao = getFenbiao($comId,20);
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$areaId = $request['areaId'];
$paystatus = $request['paystatus'];
$level = $request['level'];
$kehuStatus = $request['kehuStatus'];
$sql="select * from demo_dinghuo_order where comId=$comId and status>-1";
$sql1 = "select sum(price) as price,sum(price_wuliu) as price_wuliu,sum(price_payed) as price_payed,sum(price_weikuan) as price_weikuan from demo_dinghuo_order where comId=$comId and status>-1";
if(!empty($keyword)){
	$sql.=" and (kehuName like '%$keyword%' or orderId like '%$keyword%')";
	$sql1.=" and (kehuName like '%$keyword%' or orderId like '%$keyword%')";
}
if(!empty($startTime)){
	$sql.=" and dtTime>='$startTime 00:00:00'";
	$sql1.=" and dtTime>='$startTime 00:00:00'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<='$endTime 23:59:59'";
	$sql1.=" and dtTime<='$endTime 23:59:59'";
}
if(!empty($kehuName)){
	$sql.=" and kehuName like '%$kehuName%'";
	$sql1.=" and kehuName like '%$kehuName%'";
}
if(!empty($areaId)){
	$areaIds = $areaId.getZiAreas($areaId);
	$sql.=" and areaId in($areaIds)";
	$sql1.=" and areaId in($areaIds)";
}
if(!empty($paystatus)){
	if($paystatus==1){
		$sql.=" and payStatus<4";
		$sql1.=" and payStatus<4";
	}else{
		$sql.=" and payStatus=4";
		$sql1.=" and payStatus=4";
	}
}
if(!empty($level)){
	if($level>0){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
		$sql1.=" and kehuId in($kehuIds)";
	}
}
$sql.=" order by id desc";
$jilus = $db->get_results($sql);
$jilu = $db->get_row($sql1);
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment; filename=订单收款统计.xls");
$allRows = array(
	"dtTime"=>"下单时间",
	"kehuName"=>$kehu_title."名称",
	"orderId"=>"订单号",
	"price_dinghuo"=>"订货金额",
	"price_wuliu"=>"运费",
	"price"=>"应收金额",
	"price_payed"=>"已收金额",
	"price_weikuan"=>"待收金额"
);
?>
<table border="1">
	<tbody>
		<tr><td colspan="8">
			应收金额总计：<?=empty($jilu->price)?'0.00':$jilu->price?>（订货金额：<?=$jilu->price-$jilu->price_wuliu?>&nbsp;&nbsp;运费：<?=empty($jilu->price_wuliu)?'0.00':$jilu->price_wuliu?>）&nbsp;&nbsp;已收金额总计：<?=empty($jilu->price_payed)?'0.00':$jilu->price_payed?>&nbsp;&nbsp;待收金额总计：<?=empty($jilu->price_weikuan)?'0.00':$jilu->price_weikuan?>
		</td></tr>
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
				$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
				$j->price_dinghuo = $j->price-$j->price_wuliu;
				$dataJson['data'][] = $j;
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
