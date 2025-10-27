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
$jilu = $db->get_row("select * from demo_caigou_tuikuan where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$jiluDetails = $db->get_results("select * from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->jiluId." and caigouId=-1");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>采购退货明细</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
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
		function tongguo(jiluId){
			layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '530px;'
				,shade: 0.3
				,id: 'LAY_layuipro'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					var beizhu = $("#e_beizhu").val();
					layer.load();
					$.ajax({
						type: "POST",
						url: "?m=system&s=caigou_tuihuo&a=shenhe&status=1",
						data: "jiluId="+jiluId+"&cont="+beizhu,
						dataType:'json',timeout:30000,
						success: function(resdata){
							layer.closeAll();
							if(resdata.code==0){
								layer.msg(resdata.message,{icon:5});
							}else{
								location.reload();
							}
						}
					});
				}
				,btnAlign: 'r'
				,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
				'<div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">确定要审核通过吗？</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
				'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
				'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入审批内容"></textarea>'+
				'</div>'+
				'</div>'
			});
		}
		function bohui(jiluId){
			layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '530px;'
				,shade: 0.3
				,id: 'LAY_layuipro'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					var beizhu = $("#e_beizhu").val();
					if(beizhu==''){
						layer.msg('请输入驳回原因',function(){});
						return false;
					}
					layer.load();
					$.ajax({
						type: "POST",
						url: "?m=system&s=caigou_tuihuo&a=shenhe&status=-1",
						data: "jiluId="+jiluId+"&cont="+beizhu,
						dataType:'json',timeout:30000,
						success: function(resdata){
							layer.closeAll();
							if(resdata.code==0){
								layer.msg(resdata.message,{icon:5});
							}else{
								location.reload();
							}
						}
					});
				}
				,btnAlign: 'r'
				,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
				'<div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">确定要驳回吗？</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
				'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
				'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入驳回原因"></textarea>'+
				'</div>'+
				'</div>'
			});
		}
	</script>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['url'])?>"><img src="images/biao_63.png"/> 采购退货明细</a>
	</div>
	<div class="spchukuxiangxi_01_right" style="display:inline-block;position:relative;top:10px;margin-right:20px;">
		<div class="spchukuxiangxi_01_right_left">
			<a href="javascript:doPrint();location.reload();"><img src="images/biao_64.png"/> 打印</a>
		</div>
		<? if($jilu->status==0&&$jilu->shenheUser==$_SESSION[TB_PREFIX.'admin_userID']){?>
		<div class="spchukuxiangxi_01_right_right">
			<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01">审批通过</a><a href="javascript:" onclick="bohui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02">审批驳回</a>
		</div>
		<? }?>
		<div class="clearBoth"></div>
	</div>
	<!--startprint-->
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="sprukuxiangxi_01">	
				<div class="sprukuxiangxi_01_left">
					<span>采购退货单号：<?=$jilu->orderId?></span>
					<span>关联采购订单号：<?=$db->get_var("select orderId from demo_caigou where id=".$jilu->caigouId);?></span>
					<span>供应商：<?=$jilu->supplierName?></span>
					<span>仓库：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?></span>
					<span>日期：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
				</div>
				
				<div class="clearBoth"></div>
			</div>
			<div class="sprukuxiangxi_02">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr height="43">
						<td class="sprukuxiangxi_02_title" width="167" bgcolor="#7bc8ed" valign="middle" align="center"> 
							编码
						</td>
						<td class="sprukuxiangxi_02_title" width="592" bgcolor="#7bc8ed" valign="middle" align="center"> 
							商品
						</td>
						<td class="sprukuxiangxi_02_title" width="258" bgcolor="#7bc8ed" valign="middle" align="center"> 
							规格
						</td>
						<td class="sprukuxiangxi_02_title" width="103" bgcolor="#7bc8ed" valign="middle" align="center"> 
							单位
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							退货数量
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							退货单价（元）
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							小计（元）
						</td>
					</tr>
					<?
					$heji = 0;
					if(!empty($jiluDetails)){
						foreach ($jiluDetails as $detail) {
							$pdtInfo = json_decode($detail->pdtInfo);
							$num = abs($detail->num);
							?>
							<tr height="53">
								<td class="sprukuxiangxi_02_tt" width="167" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$pdtInfo->sn?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="592" bgcolor="#ffffff" valign="middle" align="left"> 
									<span><?=$pdtInfo->title?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="258" bgcolor="#ffffff" valign="middle" align="center"> 
									<span><?=$pdtInfo->key_vals?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="103" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->units?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$num?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="left"> 
									<b><?=getXiaoshu($detail->price,$product_set->price_num)?></b>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="left"> 
									<b><?=$detail->price*$num?></b>
								</td>
							</tr>
							<?
						}
					}
					?>
					<tr>
						<td colspan="7" height="130">
							 <div class="total_mun">
							 	<?=$jilu->hejiPrice?><br>
							 	<? if($jilu->ifxieshang==1){?><?=$jilu->money?><br><? }?>
							  	<span style="color:#ff3636; font-size:18px; "><?=$jilu->money?></span>
							 </div>
							 <div class="total_tt"> 
								合计：<br>
							 	<? if($jilu->ifxieshang==1){?>已通过协商，获批退款金额为：<br><? }?>
							  	应付金额：
							 </div>
						</td>
					</tr>
				</table>
			</div>
			<div class="sprukuxiangxi_03">
				备注：<span><?=$jilu->beizhu?></span>
			</div>
			<div class="spchukuxiangxi_04">
				<ul>
					<li>
						<div class="spchukuxiangxi_04_left">
							经办人：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->jingbanren?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							制单人：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->username?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<? if(!empty($jilu->shenheUser)){?>
					<li>
						<div class="spchukuxiangxi_04_left">
							审批人：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->shenheName?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							审批状态：
						</div>
						<div class="spchukuxiangxi_04_right">
							<? switch($jilu->status){
								case 0:echo '<span>待审批</span>';break;
								case -1:echo '<span>已驳回</span>';break;
								case -2:echo '<span>已作废</span>';break;
								case 1:echo '<span style="color:green">已通过</span>';break;
							}?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							审批时间：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=empty($jilu->shenheTime)?'':date("Y-m-d H:i",strtotime($jilu->shenheTime))?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							审批说明：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=empty($jilu->shenheCont)?'':$jilu->shenheCont?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<? }?>
					
					<div class="clearBoth"></div>
				</ul>
			</div>
			<!--endprint-->
		</div>
	</div>
	<? require('views/help.html');?>
</body>
</html>