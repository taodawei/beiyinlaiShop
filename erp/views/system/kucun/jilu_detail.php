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
$jilu = $db->get_row("select * from demo_kucun_jilu$fenbiao where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
if($jilu->type==3){
	$jiluDetails = $db->get_results("select * from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->id.' and num<0 and caigouId=0');
}else{
	//$jiluDetails = $db->get_results("select * from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->id." and (caigouId=0 or typeInfo='采购入库')");
	$jiluDetails = $db->get_results("select * from demo_kucun_jiludetail$fenbiao where jiluId=".(int)$jilu->id);
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$jilu->type_info?>明细</title>
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
						url: "?m=system&s=kucun&a=shenhe&status=1",
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
						url: "?m=system&s=kucun&a=shenhe&status=-1",
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
		function ruku(jiluId){
			layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '530px;'
				,shade: 0.3
				,id: 'LAY_layuipro'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					layer.load();
					$.ajax({
						type: "POST",
						url: "?m=system&s=kucun&a=diaoboRuku",
						data: "jiluId="+jiluId,
						dataType:'json',timeout:30000,
						success: function(resdata){
							layer.closeAll();
							if(resdata.code==0){
								layer.msg(resdata.message,{icon:5});
							}else{
								layer.msg(resdata.message,{icon:5});
								location.reload();
							}
						}
					});
				}
				,btnAlign: 'r'
				,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
				'<div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">调拨单入库确认</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
				'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_02" style="height:120px;padding:0px;padding-top:60px;margin-top:10px;text-align:center;">'+
				'确认将调拨单<?=$jilu->orderId?>入库到<?=$jilu->store1Name?>？<br>此操作不可撤销。'+
				'</div>'+
				'</div>'
			});
		}
	</script>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['url'])?>"><img src="images/biao_63.png"/> <?=$jilu->type_info?>明细</a>
	</div>
	<div class="spchukuxiangxi_01_right" style="display:inline-block;position:relative;top:10px;margin-right:20px;">
		<div class="spchukuxiangxi_01_right_left">
			<a href="javascript:doPrint();"><img src="images/biao_64.png"/> 打印</a>
		</div>
		<? if($jilu->status==0&&$jilu->shenheUser==$_SESSION[TB_PREFIX.'admin_userID']){?>
		<div class="spchukuxiangxi_01_right_right">
			<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01">审批通过</a><a href="javascript:" onclick="bohui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02">审批驳回</a>
		</div>
		<? }else if($jilu->status==1&&$jilu->type==3){?>
		<div class="spchukuxiangxi_01_right_right">
			<a href="javascript:" onclick="ruku(<?=$id?>);" class="spchukuxiangxi_01_right_right_01">入库</a>
		</div>
		<? }?>
		<div class="clearBoth"></div>
	</div>
	<!--startprint-->
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="sprukuxiangxi_01">	
				<div class="sprukuxiangxi_01_left">
					<? if($jilu->type==3){?>
						<span>调出仓库：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?></span>
						<span>调出时间：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
						<span>调入仓库：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->store1Id")?></span>
						<? if(!empty($jilu->dtTime1)){?><span>调入时间：<?=date("Y-m-d H:i",strtotime($jilu->dtTime1))?></span><? }?>
						<span>单号：<?=$jilu->orderId?></span>
					<?}else{?>
						<span>仓库：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?></span>
						<span>单号：<?=$jilu->orderId?></span>
						<span>日期：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
					<? }?>
					
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
						<? if($jilu->type==6){?>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							成本调整
						</td>
						<?}else{?>
						<td class="sprukuxiangxi_02_title" width="103" bgcolor="#7bc8ed" valign="middle" align="center"> 
							单位
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							出入库数量
						</td>
						<td class="sprukuxiangxi_02_title" width="281" bgcolor="#7bc8ed" valign="middle" align="center"> 
							备注
						</td>
						<? }?>
					</tr>
					<?
					$heji = 0;
					if(!empty($jiluDetails)){
						foreach ($jiluDetails as $detail) {
							$pdtInfo = json_decode($detail->pdtInfo);
							$num = getXiaoshu($detail->num,$product_set->number_num);
							if($jilu->type==3){
								$num=abs($num);
							}
							$chengben_one = getXiaoshu($detail->zongchengben/abs($num),2);
							$heji+=$num;
							if($jilu->type!=3){
								if($num>=0){
									$num='<span style="color:green">+'.$num.'</span>';
								}else{
									$num='<span style="color:red">'.$num.'</span>';
								}
							}
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
								<? if($jilu->type==6){?>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->chengben?>
								</td>
								<?}else{?>
								<td class="sprukuxiangxi_02_tt" width="103" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->units?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$num?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="281" bgcolor="#ffffff" valign="middle" align="left"> 
									<b><?=$detail->beizhu?></b>
								</td>
								<? }?>
							</tr>
							<?
						}
					}
					if($jilu->type!=6){
					?>
					<tr height="53">
						<td class="sprukuxiangxi_02_tt" width="167" bgcolor="#ffffff" valign="middle" align="center"> 

						</td>
						<td class="sprukuxiangxi_02_tt" colspan="3" width="592" bgcolor="#ffffff" valign="middle" align="left"> 
							<span>合计</span>
						</td>
						<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
							<?
							if($jilu->type!=3){
								$heji>=0?'<span style="color:green">+'.$heji.'</span>':'<span style="color:red">'.$heji.'</span>';
							}
							echo $heji;
							?>
						</td>
						<td class="sprukuxiangxi_02_tt" width="281" bgcolor="#ffffff" valign="middle" align="left"> 
						</td>
					</tr>
					<? }?>
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
							状态：
						</div>
						<div class="spchukuxiangxi_04_right">
							<? switch($jilu->status){
								case 0:echo '<span>待审批</span>';break;
								case -1:echo '<span>已驳回</span>';break;
								case -2:echo '<span>已作废</span>';break;
								case 2:echo '<span style="color:green">已完成</span>';break;
								case 1:if($jilu->type==3){echo '<span>在途</span>';}else{echo '<span style="color:green">已通过</span>';}break;
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