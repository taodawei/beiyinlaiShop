<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fahuoTime = $request['fahuoTime'];
$fenbiao = getFenbiao($comId,20);
$orderIds = $db->get_var("select group_concat(orderIds) from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1");
if(empty($orderIds))$orderIds='-1';
$sql = "select id,userId,orderId,productId,pdtInfo,num,unit_price from order_detail$fenbiao where orderId in($orderIds)";
//file_put_contents('request.txt',$sql);
$jilus = $db->get_results($sql);
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
	.c1 span{font-weight: normal;margin-left:50px;font-size:15pt}
	.c2{white-space:pre-wrap;text-align:center;border-top:thin solid;border-right:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c2 span{margin-right: 50px;}
	.c2 div{width:25px;word-wrap:break-word;word-break:break-all;}
	.c3{white-space:pre-wrap;text-align:center;border-top:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c4{white-space:pre-wrap;text-align:center;background-color:silver;border-top:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:18pt;}
	.c5{white-space:pre-wrap;border-top:thin solid;border-right:thin solid;border-bottom:thin solid;border-left:thin solid;color: black; font-size:11pt;}
	.c6{white-space:pre-wrap;text-align:center;color: black; font-size:11pt;}
</style>
</head>
<body class="b1">
	<table class="t1" style="table-layout:fixed;">
		<colgroup>
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
			<col width="25">
		</colgroup>
		<tbody>
			<tr class="r2">
				<td class="c2"><div>客户姓名</div></td>
				<td class="c2"><div>下单日期</div></td>
				<td class="c2"><div>发货日期</div></td>
				<td class="c2"><div>配送姓名</div></td>
				<td class="c2"><div>收件姓名</div></td>
				<td class="c2"><div>品种编号</div></td>
				<td class="c2"><div>品种名称</div></td>
				<td class="c2"><div>盆径</div></td>
				<td class="c2"><div>规格</div></td>
				<td class="c2"><div>头数</div></td>
				<td class="c2"><div>件数</div></td>
				<td class="c2"><div>数量</div></td>
				<td class="c2"><div>小计</div></td>
				<td class="c2"><div>到货地址</div></td>
				<td class="c2"><div>物流方式</div></td>
			</tr>
			<? if(!empty($jilus)){
				foreach ($jilus as $i=>$j) {
					$o = $db->get_row("select shuohuo_json,remark,fahuoId,dtTime from order$fenbiao where id=$j->orderId");
					$fahuo = $db->get_row("select fahuoTime,rider_id from order_fahuo$fenbiao where id=$o->fahuoId");
					$j->dtTime = date("Y-m-d",strtotime($o->dtTime));
					$j->fahuoTime = date("Y-m-d",strtotime($fahuo->fahuoTime));
					$pdtInfo = json_decode($j->pdtInfo);
					$shouhuo = json_decode($o->shuohuo_json,true);
					$addrows = $db->get_var("select addrows from demo_product where id=$j->productId");
					$addrows_arr = json_decode($addrows,true);
					$j->name = $db->get_var("select nickname from users where id=$j->userId");
					$j->shoujian_name = $shouhuo['收件人'];
					$j->sn = $pdtInfo->sn;
					$j->product = $pdtInfo->title;
					$j->penjing = $addrows_arr['盆径'];
					$j->guige = $addrows_arr['规格'];
					$j->toushu = $addrows_arr['头数'];
					$j->shuliang = $j->num * intval($addrows_arr['数量']);
					$j->xiaoji = $j->num*$j->unit_price;
					$j->num = getXiaoshu($j->num,0);
					$j->wuliufangshi = $db->get_var("select row2 from demo_peisong_rider where id=$fahuo->rider_id");
					?>
					<tr class="r2">
						<td class="c2"><div><?=$j->name?></div></td>
						<td class="c2"><div><?=$j->dtTime?></div></td>
						<td class="c2"><div><?=$j->fahuoTime?></div></td>
						<td class="c2"><div></div></td>
						<td class="c2"><div><?=$j->shoujian_name?></div></td>
						<td class="c2"><div><?=$j->sn?></div></td>
						<td class="c2"><div><?=$j->product?></div></td>
						<td class="c2"><div><?=$j->penjing?></div></td>
						<td class="c2"><div><?=$j->guige?></div></td>
						<td class="c2"><div><?=$j->toushu?></div></td>
						<td class="c2"><div><?=$j->num?></div></td>
						<td class="c2"><div><?=$j->shuliang?></div></td>
						<td class="c2"><div><?=$j->xiaoji?></div></td>
						<td class="c2"><div></div></td>
						<td class="c2"><div><?=$j->wuliufangshi?></div></td>
					</tr>
					<?
				}
			}?>
		</tbody>
	</table>
</body>
<script type="text/javascript"> 
	window.print();  
</script>
</html>
