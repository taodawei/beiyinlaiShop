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
$jilu = $db->get_row("select * from demo_caigou where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$jiluDetails = $db->get_results("select * from demo_caigou_detail$fenbiao where jiluId=".(int)$jilu->id.' order by id asc');
$jiluNum = $db->get_row("select sum(num) as zong,sum(hasNum) as yiruku from demo_caigou_detail$fenbiao where jiluId=".(int)$jilu->id);
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>采购明细</title>
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
						url: "?m=system&s=caigou&a=shenhe&status=1",
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
						url: "?m=system&s=caigou&a=shenhe&status=-1",
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
		<? if($request['print']==1){?>
			window.onload = function () {
				doPrint();
				location.href='?m=system&s=caigou&a=detail&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>';
			}
		<? }?>
	</script>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>"><img src="images/biao_63.png"/> 采购明细</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div class="purchase_order"><a href="javascript:">订单详情</a></div>
			<? if($jilu->status==1){?>
			<div class="storage"><a href="?m=system&s=caigou&a=ruku&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">入库记录</a></div>
			<? }?>
		</div>
		<div class="state_right spchukuxiangxi_01_right_right">
			<? if($jilu->status==0&&$jilu->shenheUser==$_SESSION[TB_PREFIX.'admin_userID']){?>
				<a href="javascript:" onclick="bohui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02" style="float:right;">审批驳回</a>
				<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">审批通过</a>
			<? }else if($jilu->status==1&&$jilu->rukuStatus<2&&$jilu->userId==$_SESSION[TB_PREFIX.'admin_userID']){?>
			<div class="ruku"><a href="?m=system&s=caigou&a=ruku&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=$request['url']?>">入库</a></div>
			<? }?>
			<div class="derive">
				<div><a href="?m=system&s=caigou&a=daochu&id=<?=$id?>" target="_blank" style="width:auto;margin-right:0px;"><img src="images/derive.gif">导出</a></div>
				<div><a href="javascript:doPrint();location.reload();" style="width:auto;margin-right:0px;"><img src="images/print2.gif">打印</a></div>
			</div>
		</div>
	</div>
	<!--startprint-->
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="sprukuxiangxi_01">	
				<div class="sprukuxiangxi_01_left">
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
					<? if($jilu->ifjiaji==1){?><span style="color:#ff4747;">紧急采购</span><? }?>
					<span>采购单号：<?=$jilu->orderId?></span>
					<span>供应商：<?=$jilu->supplierName?></span>
					<span>日期：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div class="purchase_xiang_tt2">
				<span>应采购入库数量：<span style="color:#4b94d2;"><?=getXiaoshu($jiluNum->zong,$product_set->number_num)?></span></span>
				<span>已入库数量：<span style="color:#ff4747;"><?=getXiaoshu($jiluNum->yiruku,$product_set->number_num)?></span></span>
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
							采购数量
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							采购单价
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							小计
						</td>
					</tr>
					<?
					$heji = 0;
					if(!empty($jiluDetails)){
						foreach ($jiluDetails as $detail) {
							$pdtInfo = json_decode($detail->pdtInfo);
							$num = getXiaoshu($detail->num,$product_set->number_num);
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
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=getXiaoshu($detail->unit_price,$product_set->price_num)?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=getXiaoshu($detail->price,$product_set->price_num)?>
								</td>
							</tr>
							<?
						}
					}
					?>
					<tr>
						<td colspan="7" height="130">
							 <div class="total_mun2">
							 	<?=$jilu->price_other?><br>
							  	<span style="color:#ff3636; font-size:24px; "><?=$jilu->price?></span>
							 </div>
							 <div class="total_tt"> 
							 	其他金额：<br>
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
							到货仓库：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							采购方式：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->price_type==1?'现购':'赊购'?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							已付款：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->price-$jilu->price_weikuan?>元
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="spchukuxiangxi_04_left">
							物流费用：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->price_wuliu?>元
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
					<li>
						<div class="spchukuxiangxi_04_left">
							采购员：
						</div>
						<div class="spchukuxiangxi_04_right">
							<?=$jilu->caigouyuan?>
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
					<div class="clearBoth"></div>
				</ul>
			</div>
			<!--endprint-->
		</div>
	</div>
	<? require('views/help.html');?>
</body>
</html>