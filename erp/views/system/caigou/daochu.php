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
$jilu = $db->get_row("select * from demo_caigou where id=$id and comId=$comId");
if(empty($jilu)){
	die('记录不存在');
}
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=采购单-".$jilu->orderId.".xls");
$jiluDetails = $db->get_results("select * from demo_caigou_detail$fenbiao where jiluId=".(int)$jilu->id.' order by id asc');
$jiluNum = $db->get_row("select sum(num) as zong,sum(hasNum) as yiruku from demo_caigou_detail$fenbiao where jiluId=".(int)$jilu->id);
$allRows = array(
	"sn"=>"商品编码",
	"title"=>"商品名称",
	"key_vals"=>"规格",
	"units"=>"单位",
	"num"=>"采购数量",
	"unit_price"=>"采购单价",
	"price"=>"小计"
);
?>
<table border="1">
	<tbody>
	<tr><td colspan="7">
		<span style="color:#ff4747;font-size:18px;"><?
		switch ($jilu->status){
			case -1:
			echo '已驳回';
			break;
			case 0:
			echo '待审核';
			break;
			case 1:
			if($jilu->rukuStatus==2){
				echo '已入库';
			}else if($jilu->rukuStatus==1){
				echo '部分入库';
			}else{
				echo '待入库';
			}
			break;
		}
		?></span>
		<span>采购单号：<?=$jilu->orderId?></span>
		<span>供应商：<?=$jilu->supplierName?></span>
		<span>日期：<?=date("Y-m-d",strtotime($jilu->dtTime))?></span>
		<? if($jilu->ifjiajia){?><span style="color:#5a97d6;">紧急采购</span><? }?><br>
		<span>应采购入库数量：<span style="color:#4b94d2;"><?=getXiaoshu($jiluNum->zong,$product_set->number_num)?></span></span>
		<span>已入库数量：<span style="color:#ff4747;"><?=getXiaoshu($jiluNum->yiruku,$product_set->number_num)?></span></span>
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
	if(!empty($jiluDetails)){
		foreach ($jiluDetails as $j){
			$pdtInfo = json_decode($j->pdtInfo,true);
			$j->sn = $pdtInfo['sn'];
			$j->title = $pdtInfo['title'];
			$j->key_vals = $pdtInfo['key_vals'];
			$j->num = getXiaoshu($j->num,$product_set->number_num);
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
	<tr>
		<td colspan="7">
			其他金额：<?=$jilu->price_other?><br>
			应付金额：<span style="color:#ff3636; font-size:24px; "><?=$jilu->price?></span>
		</td>
	</tr>
	<tr>
		<td colspan="7">
			到货仓库：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?>&nbsp;&nbsp;&nbsp;采购方式：<?=$jilu->price_type==1?'现购':'赊购'?>&nbsp;&nbsp;&nbsp;已付款：<?=$jilu->price-$jilu->price_weikuan?>元<br>
			物流费用：<?=$jilu->price_wuliu?>元&nbsp;&nbsp;&nbsp;采购员：<?=$jilu->caigouyuan?>&nbsp;&nbsp;&nbsp;制单人：<?=$jilu->username?><br>
			<? if(!empty($jilu->shenheUser)){?>
			审批人：<?=$jilu->shenheName?>&nbsp;&nbsp;&nbsp;审批状态：
			<? switch($jilu->status){
				case 0:echo '<span>待审批</span>';break;
				case -1:echo '<span>已驳回</span>';break;
				case 1:echo '<span style="color:green">已通过</span>';break;
			}?>&nbsp;&nbsp;&nbsp;审批时间：<?=empty($jilu->shenheTime)?'':date("Y-m-d H:i",strtotime($jilu->shenheTime))?><br>审批说明：<?=empty($jilu->shenheCont)?'':$jilu->shenheCont?><Br>
			<? }?>
			备注：<?=$jilu->beizhu?>
		</td>
	</tr>
</tbody></table>
