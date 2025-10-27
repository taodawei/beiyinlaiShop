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
$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
if(empty($jilu)){
	die('记录不存在');
}
if(!empty($jilu->beizhu)){
	$beizhus = json_decode($jilu->beizhu,true);
}
if(!empty($jilu->shouhuoInfo)){
	$shouhuoInfo = json_decode($jilu->shouhuoInfo,true);
}
if(!empty($jilu->fapiaoInfo)){
	$fapiaoInfo = json_decode($jilu->fapiaoInfo,true);
}
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$k = $db->get_row("select title,level from demo_kehu where id=$jilu->kehuId");
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment; filename=订货单-".$jilu->orderId.".xls");
$jiluDetails = $db->get_results("select * from demo_dinghuo_detail$fenbiao where jiluId=".(int)$jilu->id.' order by id asc');
$allRows = array(
	"sn"=>"商品编码",
	"title"=>"商品名称",
	"key_vals"=>"规格",
	"units"=>"单位",
	"num"=>"数量",
	"unit_price"=>"单价",
	"price"=>"小计",
	"weight"=>"重量小计",
	"beizhu"=>"备注"
);
?>
<table border="1">
	<tbody>
	<tr><td colspan="9">
		<span style="color:#ff4747;font-size:18px;"><?
		switch ($jilu->status){
			case 0:
			$status = '订单待审核';
			break;
			case 1:
			$status = '待财务审核';
			break;
			case 2:
			$status = '待出库';
			break;
			case 3:
			$status = '待出库审核';
			break;
			case 4:
			$status = '待发货';
			break;
			case 5:
			$status = '待收货';
			break;
			case 6:
			$status = '已完成';
			break;
			case -1:
			$status = '已作废';
			break;
		}
		?></span>
		<span>订货单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>（代下单）<? }?></span>
		<span><?=$kehu_title?>名称：<?=$k->title?>【<?=$db->get_var("select title from demo_kehu_level where id=$k->level");?>】</span>
		<span>业务员：<?=$jilu->yewuyuan?></span>
		<span>日期：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
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
	$heji = 0;$zongNum=0;$zongweight=0;$zongPrice=0;
	if(!empty($jiluDetails)){
		foreach ($jiluDetails as $j){
			$pdtInfo = json_decode($j->pdtInfo,true);
			$j->num = getXiaoshu($j->num,$product_set->number_num);
			$zongNum +=$j->num;
			$zongweight +=$j->weight;
			$zongPrice +=$j->price;
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
	<tr>
		<td colspan="9">
			运费：<?=$jilu->price_wuliu?><br>
			应付金额：<span style="color:#ff3636; font-size:24px; "><?=$jilu->price?></span>
		</td>
	</tr>
	<tr>
		<td colspan="9">
			收货信息：客户名称：<?=$shouhuoInfo['company']?>&nbsp;&nbsp;联系人：<?=$shouhuoInfo['name']?>&nbsp;&nbsp;联系电话：<?=$shouhuoInfo['phone']?>&nbsp;&nbsp;地址：<?=$shouhuoInfo['address']?><br>
			交货日期：<?=$jilu->jiaohuoTime=='0000-00-00'?'':date("Y-m-d H:i",strtotime($jilu->jiaohuoTime))?><br>
			制单人：<?=$jilu->username?><br>
			发票信息：<? switch($fapiaoInfo['type']){
                    		case 0:echo '不开发票';break;
                    		case 1:echo '（普通发票）&nbsp;&nbsp;发票抬头：'.$fapiaoInfo['taitou'].'&nbsp;&nbsp;发票内容：'.$fapiaoInfo['content'].'&nbsp;&nbsp;纳税人识别号：'.$fapiaoInfo['shibie'];break;
                    		case 2:echo '（增值税发票）&nbsp;&nbsp;发票抬头：'.$fapiaoInfo['taitou'].'&nbsp;&nbsp;发票内容：'.$fapiaoInfo['content'].'&nbsp;&nbsp;纳税人识别号：'.$fapiaoInfo['shibie'].'&nbsp;&nbsp;地址：'.$fapiaoInfo['address'].'&nbsp;&nbsp;电话：'.$fapiaoInfo['phone'].'&nbsp;&nbsp;开户名称：'.$fapiaoInfo['kaihuming'].'&nbsp;&nbsp;开户银行：'.$fapiaoInfo['kaihuhang'].'&nbsp;&nbsp;银行账号：'.$fapiaoInfo['kaihubank'];break;
                    	}
                    	?><br>
			<? if(!empty($beizhus)){?>备注：<?
				foreach ($beizhus as $i=>$b) {
					if($i>0)echo '　　　';
					echo $b['content'].'【'.$b['name'].'&nbsp;/&nbsp;'.$b['company'].'&nbsp;&nbsp;'.$b['time'].'】<br>';
				}
			}?>
		</td>
	</tr>
</tbody></table>
