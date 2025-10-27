<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$fenbiao = getFenbiao($comId,20);
$sql = "select id,userId,orderId,productId,pdtInfo,num from order_detail$fenbiao where id=$id";
$j = $db->get_row($sql);
$o = $db->get_row("select shuohuo_json,remark,fahuoId from order$fenbiao where id=$j->orderId");
$pdtInfo = json_decode($j->pdtInfo);
$shouhuo = json_decode($o->shuohuo_json,true);
$addrows = $db->get_var("select addrows from demo_product where id=$j->productId");
$addrows_arr = json_decode($addrows,true);
$j->name = $shouhuo['收件人'];
$j->product = $pdtInfo->title;
$j->penjing = $addrows_arr['盆径'];
$j->guige = $addrows_arr['规格'];
$j->toushu = $addrows_arr['头数'];
$j->shuliang = $j->num * intval($addrows_arr['数量']);
$j->remark = $o->remark;
$wuliu = $db->get_var("select row2 from demo_peisong_rider where id=(select rider_id from order_fahuo$fenbiao where id=$o->fahuoId)");
?>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">
	table td{border:.5mm solid #ccc;}
	table td div{width: 30mm;height:18mm;overflow:hidden;word-break: break-all;white-space:break;line-height: 5mm;font-size: 12px;}
</style>
</head>
<body class="b1">
	<table class="t1" style="table-layout:fixed;border-collapse:collapse;">
		<tbody>
			<tr class="r1">
				<td class="c1"><div>收件姓名</div></td><td><div><?=$j->name?></div></td>
			</tr>
			<tr class="r1">
				<td class="c1"><div>品种名称</div></td><td><div><?=$j->product?></div></td>
			</tr>
			<tr class="r1">
				<td class="c1"><div>盆径</div></td><td><div><?=$j->penjing?></div></td>
			</tr>
			<tr class="r1">
				<td class="c1"><div>件数</div></td><td><div><?=getXiaoshu($j->num,0)?></div></td>
			</tr>
			<tr class="r1">
				<td class="c1"><div>到货地址</div></td><td><div><?=$shouhuo['所在地区'].$shouhuo['详细地址']?></div></td>
			</tr>
			<tr class="r1">
				<td class="c1"><div>物流方式</div></td><td><div><?=$wuliu?></div></td>
			</tr>
		</tbody>
	</table>
</body>
<script type="text/javascript"> 
	window.print();  
</script>
</html>
