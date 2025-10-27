<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
if(is_file("../cache/kucun_set_$comId.php")){
	$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$jilu = $db->get_row("select orderId,storeName,dtTime,dinghuoId from demo_kucun_jilu$fenbiao where id=$id and comId=$comId");
if(empty($jilu)){
	die('记录不存在！');
}
$shouhuoInfo = $db->get_var("select shouhuoInfo from demo_dinghuo_order where id=$jilu->dinghuoId");
$shouhuo = array();
if(!empty($shouhuoInfo))$shouhuo = json_decode($shouhuoInfo);
$details = $db->get_results("select pdtInfo,units,num,dinghuoId from demo_kucun_jiludetail$fenbiao where jiluId=".$id." order by id asc");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=发货单-".$jilu->orderId.".xls");
?>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">.b1{text-align:center;white-space-collapsing:preserve;}
	.t1{border-collapse:collapse;border-spacing:0;}
	.r1{height:37.5pt;}
	.r2{height:25.5pt;}
	.r3{height:40.5pt;}
	.c1{white-space:pre-wrap;text-align:center;background-color:silver;border-top:thin solid;border-right:thin solid;border-bottom:thin solid;border-left:thin solid;font-weight:bold;color: black; font-size:20pt;}
	.c2{white-space:pre-wrap;text-align:center;border-top:thin solid;border-right:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c3{white-space:pre-wrap;text-align:center;border-top:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c4{white-space:pre-wrap;text-align:center;background-color:silver;border-top:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:18pt;}
	.c5{white-space:pre-wrap;border-top:thin solid;border-right:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c6{white-space:pre-wrap;text-align:center;color: black; font-size:11pt;}
</style>
</head>
<body class="b1">
	<table class="t1">
		<colgroup>
			<col width="79">
			<col width="160">
			<col width="148">
			<col width="209">
			<col width="83">
			<col width="67">
			<col width="68">
			<col width="87">
		</colgroup>
		<tbody>
			<tr class="r1">
				<td class="c1" colspan="8">发 货 单</td>
			</tr>
			<tr class="r2">
				<td class="c2">发货编号</td><td class="c2"><?=$jilu->orderId?></td><td class="c2">客户名称</td><td class="c2"><?=$shouhuo->company?></td><td class="c3">发货仓库</td><td class="c2"><?=$jilu->storeName?></td><td class="c2">联系人</td><td class="c2"><?=$shouhuo->name?></td>
			</tr>
			<tr class="r2">
				<td class="c2">收货地址</td><td class="c2" colspan="3"><?=$shouhuo->address?></td><td class="c3" colspan="2">联系电话</td><td class="c2" colspan="2"><?=$shouhuo->phone?></td>
			</tr>
			<tr class="r3">
				<td class="c5" colspan="8">商 品 明 细</td>
			</tr>
			<tr class="r2">
				<td class="c2">序号</td><td class="c2">商品编码</td><td class="c2">商品名称</td><td class="c2">商品规格</td><td class="c2">数量</td><td class="c2">计量单位</td><td class="c2">重量(kg)</td><td class="c2">备注</td>
			</tr>
			<? if(!empty($details)){
				foreach ($details as $i=>$d) {
					$d->num = abs($d->num);
					$pdtInfo = json_decode($d->pdtInfo,true);
					$dinghuo = $db->get_row("select weight,unit_price,beizhu from demo_dinghuo_detail$fenbiao where id=$d->dinghuoId");
					?>
					<tr class="r2">
						<td class="c5"><?=$i+1?></td><td class="c2"><?=$pdtInfo['sn']?></td><td class="c2"><?=$pdtInfo['title']?></td><td class="c2"><?=$pdtInfo['key_vals']?></td><td class="c5"><?=$d->num?></td><td class="c2"><?=$d->units?></td><td class="c5"><?=$dinghuo->weight*$d->num?><?=$product_set->weight?></td><td class="c2"><?=$dinghuo->beizhu?></td>
					</tr>
					<?
				}
			}?>
			<tr class="r2">
				<td class="c8">送货人：</td><td></td><td class="c8">日期：</td><td></td><td class="c8">收货人：</td><td class="c8">&nbsp;</td><td class="c8">日期：</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
