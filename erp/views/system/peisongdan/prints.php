<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fahuoTime = $request['fahuoTime'];
$fenbiao = getFenbiao($comId,20);
$sql = "select shuohuo_json,rider_id from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1 ";
$sql.=" order by id asc ";
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
			<col width="112">
			<col width="63">
			<col width="62">
			<col width="68">
			<col width="56">
		</colgroup>
		<tbody>
			<tr class="r1">
				<td class="c1" colspan="7">配 送 单<span>日期：<?=date("m-d",strtotime($fahuoTime))?></span></td>
			</tr>
			<tr class="r2">
				<td class="c2" colspan="7" style="text-align:right;">提货费:<span>&nbsp;</span>发货费:<span>&nbsp;</span>配送费:<span>&nbsp;</span>配送车型:<span>&nbsp;</span>配送姓名:<span>&nbsp;</span></td>
			</tr>
			<tr class="r2">
				<td class="c2">收件姓名</td><td class="c2">箱数</td><td class="c2">到货地址</td><td class="c2">收件电话</td><td class="c2">物流方式</td><td class="c2">物流电话</td><td class="c2">标记</td>
			</tr>
			<? if(!empty($jilus)){
				foreach ($jilus as $i=>$j) {
					$shuohuo_json = json_decode($j->shuohuo_json,true);
					$rider = $db->get_row("select * from demo_peisong_rider where id=$j->rider_id");
					$j->name = $shuohuo_json['收件人'];
					$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
					$j->phone = $shuohuo_json['手机号'];
					
					$j->wuliu = $rider->row2;
					$j->wuliu_phone = $rider->phone;
					?>
					<tr class="r2">
						<td class="c5"><?=$shuohuo_json['收件人']?></td><td class="c2"></td><td class="c2"><?=$shuohuo_json['所在地区'].$shuohuo_json['详细地址']?></td><td class="c2"><?=$shuohuo_json['手机号']?></td><td class="c5"><?=$rider->row2?></td><td class="c5"><?=$rider->phone?></td><td class="c2"></td>
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
