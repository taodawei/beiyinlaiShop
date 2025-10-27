<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}
$id=(int)$request['id'];
$fenbiao = getFenbiao($comId,20);
$jilu = $db->get_row("select * from demo_kucun_jilu$fenbiao where id=$id and comId=$comId");
if(empty($jilu)){
	die('记录不存在');
}
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=采购入库-".$jilu->orderId.".xls");
$jilu1 = $db->get_row("select * from demo_caigou where id=".$jilu->caigouId);
if($jilu->type==3){
	$jiluDetails = $db->get_results("select pdtInfo,units,num,caigouId from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->id.' and num>0');
}else{
	$jiluDetails = $db->get_results("select pdtInfo,units,num,caigouId from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->id);
}
$allRows = array(
	"sn"=>"商品编码",
	"title"=>"商品名称",
	"key_vals"=>"规格",
	"units"=>"单位",
	"num"=>"入库数"
);
?>
<table border="1">
	<tbody>
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
	if(!empty($jiluDetails)){
		foreach ($jiluDetails as $j){
			$pdtInfo = json_decode($j->pdtInfo,true);
			$j->sn = $pdtInfo['sn'];
			$j->title = $pdtInfo['title'];
			$j->key_vals = $pdtInfo['key_vals'];
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
	<tr><td colspan="5">
		<? if($jilu->status==-2){?>
		<span>入库时间：<?=date("Y-m-d",strtotime($jilu->dtTime))?></span><br>
		<span>状态：已作废</span><br>
		<span>作废时间：<?=$jilu->shenheTime?></span><br>
		<span>作废原因：<?=$jilu->shenheCont?></span><br>
		<? }else{
			switch($j->status){
				case 0:echo '<font color="red">待审核</font>';break;
				case 1:echo '<font color="green">已审核</font>';break;
				case -1:echo '<font color="red">已驳回</font>';break;
			}
		}?>
		<span>入库单号：<?=$jilu->orderId?></span>
		<span>入库仓：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?></span>
		<span>经办人：<?=$jilu->jingbanren?></span>
		<span>入库备注：<?=$jilu->beizhu?></span><br>
		<span>采购单号：<?=$jilu1->orderId?></span>
		<span>采购员：<?=$jilu1->caigouyuan?></span>
		<span>制单人：<?=$jilu1->username?></span>
		<span>采购备注：<?=$jilu1->beizhu?></span>
	</td></tr>
</tbody></table>
