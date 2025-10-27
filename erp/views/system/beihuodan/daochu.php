<?php
global $db,$request;
$fahuoTime = $request['fahuoTime'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=备货单-$fahuoTime.xls");
$allRows = array(
	"product"=>array("title"=>"品种名称","rowCode"=>"{field:'product',title:'品种名称',width:150}"),
	"penjing"=>array("title"=>"盆径","rowCode"=>"{field:'penjing',title:'盆径',width:100}"),
	"guige"=>array("title"=>"规格","rowCode"=>"{field:'guige',title:'规格',width:150}"),
	"toushu"=>array("title"=>"头数","rowCode"=>"{field:'toushu',title:'头数',width:150}"),
	"num"=>array("title"=>"件数小计","rowCode"=>"{field:'num',title:'件数小计',width:150}"),
	"shuliang"=>array("title"=>"件数总数","rowCode"=>"{field:'shuliang',title:'件数总数',width:150}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$orderIds = $db->get_var("select group_concat(orderIds) from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1");
if(empty($orderIds))$orderIds='-1';
$sql = "select productId,inventoryId,pdtInfo,sum(num) as num from order_detail$fenbiao where orderId in($orderIds)";
$sql.=" group by inventoryId";
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
			//$o = $db->get_row("select shuohuo_json,remark from order$fenbiao where id=$j->orderId");
			$pdtInfo = json_decode($j->pdtInfo);
			//$shouhuo = json_decode($o->shuohuo_json,true);
			$addrows = $db->get_var("select addrows from demo_product where id=$j->productId");
			$addrows_arr = json_decode($addrows,true);
			//$j->name = $shouhuo['收件人'];
			$j->product = $pdtInfo->title;
			$j->penjing = $addrows_arr['盆径'];
			$j->guige = $addrows_arr['规格'];
			$j->toushu = $addrows_arr['头数'];
			$j->shuliang = $j->num * intval($addrows_arr['数量']);
			//$j->remark = $o->remark;
			$j->num = getXiaoshu($j->num,0);
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
