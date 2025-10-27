<?php
$dazhuanpan_id = (int)$request['dazhuanpan_id'];
$title = $db->get_var("select title from demo_dazhuanpan where id=$dazhuanpan_id");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=$title".' - 中奖纪录.xls');
global $db,$request;
$allRows = array(
    "prizeName"=>array("title"=>"中奖奖项","rowCode"=>"{field:'prizeName',title:'中奖奖项',width:200}"),
    "name"=>array("title"=>"姓名","rowCode"=>"{field:'name',title:'姓名',width:100}"),
    "tel"=>array("title"=>"联系电话","rowCode"=>"{field:'tel',title:'联系电话',width:120}"),
    "dtTime"=>array("title"=>"中奖时间","rowCode"=>"{field:'dtTime',title:'中奖时间',width:200,sort:true}"),
    "status_info"=>array("title"=>"兑换状态","rowCode"=>"{field:'status_info',title:'兑换状态',width:120}"),
    "duihuan_time"=>array("title"=>"兑换时间","rowCode"=>"{field:'duihuan_time',title:'兑换时间',width:150}")
);

$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$channelId = (int)$request['channelId'];
$isduihuan = $request['isduihuan'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$sql = "select * from demo_dazhuanpan_record where dazhuanpan_id=$dazhuanpan_id";
if($channelId>0){
	$sql.=" and prize=$channelId";
}
if(!empty($isduihuan)){
	$sql.=" and isduihuan=".($isduihuan==1?1:0);
}
if(!empty($keyword)){
	$sql.=" and (name like '%$keyword%' or tel='$keyword')";
}
if(!empty($startTime)){
	$sql.=" and dtTime>='$startTime'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<='$endTime 23:59:59'";
}
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
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$pdt->layclass = '';
			if($pdt->isduihuan==1){
				$pdt->status_info = '已领';
			}else{
				$pdt->status_info = '未领';
			}
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
