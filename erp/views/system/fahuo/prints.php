<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$order = $db->get_row("select * from order_fahuo$fenbiao where id=$id and comId=$comId");
if(empty($order))die("订单不存在！！");
$shuohuo_json = array();
if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
if(!empty($order->orderIds))$orderIds = explode(',', $order->orderIds);
$orders = '';
if(!empty($orderIds)){
	foreach ($orderIds as $key => $value) {
		$o = $db->get_row("select orderId,status,remark from order$fenbiao where id=".$value);
		$orders .= $o->orderId.',';
		$order->remark = $o->remark;
	}
}
$orders = substr($orders, 0,strlen($orders)-1);
$orderlist = $db->get_results("select pdtInfo,num from order_detail$fenbiao where orderId in (".$order->orderIds.")");
$products = array();
if(!empty($orderlist)){
	foreach ($orderlist as $list) {
		$arr = json_decode($list->pdtInfo,true);
		$arr['num'] = $list->num;
		$products[] = $arr;
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>发货单明细</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		function doPrint() {   
			bdhtml=window.document.body.innerHTML;   
			sprnstr="<!--startprint-->";   
			eprnstr="<!--endprint-->";   
			prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);   
			prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));   
			window.document.body.innerHTML=prnhtml;  
			window.print();
		}
		window.onload=function(){
			doPrint();
		};
	</script>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['url'])?>"><img src="images/biao_63.png"/> 发货单明细</a>
	</div>
	<div class="spchukuxiangxi_01_right" style="display:inline-block;position:relative;top:10px;margin-right:20px;">
		<div class="spchukuxiangxi_01_right_left">
			<a href="javascript:doPrint();"><img src="images/biao_64.png"/> 打印</a>
		</div>
		<div class="clearBoth"></div>
	</div>
	<!--startprint-->
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="sprukuxiangxi_01">	
				<div class="sprukuxiangxi_01_left">
						<span>发货单号：<?=$order->orderId?></span>
						<span>订单号：<?=$orders?></span><br>
						<span>成单时间：<?=date("Y-m-d H:i",strtotime($order->dtTime))?></span>
						<span>收货人：<?=$shuohuo_json["收件人"]?></span>
						<span>手机号：<?=$shuohuo_json["手机号"]?></span><br>
						<span>收货地区：<?=$shuohuo_json["所在地区"]?></span>
						<span>详细地址：<?=$shuohuo_json["详细地址"]?></span><br>
						<span>备注：<?=$order->remark?></span>
				</div>
				
				<div class="clearBoth"></div>
			</div>
			<div class="sprukuxiangxi_02">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr height="43">
						<td class="sprukuxiangxi_02_title" width="167" bgcolor="#7bc8ed" valign="middle" align="center">编号</td>
						<td class="sprukuxiangxi_02_title" width="167" bgcolor="#7bc8ed" valign="middle" align="center"> 
							编码
						</td>
						<td class="sprukuxiangxi_02_title" width="592" bgcolor="#7bc8ed" valign="middle" align="center"> 
							商品
						</td>
						<td class="sprukuxiangxi_02_title" width="258" bgcolor="#7bc8ed" valign="middle" align="center"> 
							规格
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							数量
						</td>
					</tr>
					<?
					foreach($products as $k=>$v){$i++;
							?>
							<tr height="53">
								<td class="sprukuxiangxi_02_tt" width="167" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$i?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="592" bgcolor="#ffffff" valign="middle" align="left"> 
									<span><?=$v['sn']?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="258" bgcolor="#ffffff" valign="middle" align="center"> 
									<span><?=$v['title']?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="258" bgcolor="#ffffff" valign="middle" align="center"> 
									<span><?=$v['key_vals']?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="258" bgcolor="#ffffff" valign="middle" align="center"> 
									<span><?=$v['num']?></span>
								</td>
							</tr>
							<?
						}
					?>
				</table>
			</div>
			<!--endprint-->
		</div>
	</div>
</body>
</html>