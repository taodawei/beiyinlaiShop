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
$jilu = $db->get_row("select * from demo_caigou where id=$id and comId=$comId and status=1");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
$jilus = $db->get_results("select * from demo_kucun_jilu$fenbiao where comId=$comId and caigouId=$id order by id desc");
$rukuJilus = array();
$zuofeiJilus = array();
if(!empty($jilus)){
	foreach ($jilus as $j) {
		if($j->status==-2){
			$zuofeiJilus[] = $j;
		}else{
			$rukuJilus[] = $j;
		}
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>采购入库记录</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style type="text/css">.return_xiang_td1 input{width:80%;margin:auto;}</style>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>"><img src="images/biao_63.png"/> 采购明细</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div class="storage"><a href="?m=system&s=caigou&a=detail&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">订单详情</a></div>
			<div class="purchase_order"><a href="javascript:">入库记录</a></div>
		</div>
	</div>
	<div class="return_xiang" style="background:#fff;padding-top:14px;">
				<div class="purchase_xiang_tt3">
					<span style="color:#ff4747;font-size:18px"><?
					if($jilu->rukuStatus==2){
						echo '已入库';
					}else if($jilu->rukuStatus==1){
						echo '部分入库';
					}else{
						echo '待入库';
					}?></span>
					<span>采购单号：<?=$jilu->orderId?></span>
					<span>供应商：<?=$jilu->supplierName?></span>
				</div>
				<? if($jilu->rukuStatus<2){
					$details = $db->get_results("select * from demo_caigou_detail$fenbiao where jiluId=$jilu->id and hasNum<num order by id asc");
					$orderId = $kucun_set->ruku_pre.'_'.date("Ymd").'_'.getOrderId($comId,1);
				?>
				<div class="ruku_wait">
					<form action="" method="post" id="rukuForm" class="layui-form">
						<input type="hidden" id="lock" name="lock" value="<?=$jilu->locked?>">
					<div class="ruku_nam">
						<div class="ruku_nam_left">
							<img src="images/ruku_wait.png"><span> 待入库商品清单</span>
						</div>
						<div class="ruku_nam_right">
							<a href="javascript:ruku();"><img src="images/pullup.gif"></a>
						</div>
					</div>
					<div class="clearBoth"></div>
					<div class="ruku_search">
						<div class="ruku_search_left">
							<span style="color:#ff0000;">*</span>入仓库
							<div style="width:262px;display:inline-block;margin-left:14px;">
								<select name="storeId" id="storeId">
									<option value="">选择入库仓库</option>
									<? foreach($cangkus as $cangku){
										?><option value="<?=$cangku->id?>" <? if($cangku->id==$jilu->storeId){?>selected="true"<? }?>><?=$cangku->title?></option><?
									}?>
								</select>
							</div>
						</div>
						<div class="ruku_search_right">
							<div>本次入库数设为 0 表示此商品暂不入库</div>
							<div><button class="layui-btn" lay-submit="" lay-filter="tijiao">入库</button></div>
						<div class="clearBoth"></div>
						</div>
						<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #e5e5e5;">
						<tbody><tr>
							<td class="ruku_td" width="70"></td>
							<td class="ruku_td" width="176">商品编码</td>
							<td class="ruku_td" width="464">商品名称</td>
							<td class="ruku_td" width="254">规格</td>
							<td class="ruku_td" width="136">单位</td>
							<td class="ruku_td" width="104">采购数</td>
							<td class="ruku_td" width="130">已入库数</td>
							<td class="ruku_td" width="190">本次入库数</td>
						</tr>
						<?
						foreach ($details as $i=>$detail) {
							$pdtInfo = json_decode($detail->pdtInfo,true);
							?>
							<tr>
								<td class="return_xiang_td1"><?=$i+1?></td>
								<td class="return_xiang_td1"><?=$pdtInfo['sn']?></td>
								<td class="return_xiang_td1"><?=$pdtInfo['title']?></td>
								<td class="return_xiang_td1"><?=$pdtInfo['key_vals']?></td>
								<td class="return_xiang_td1"><?=$detail->units?></td>
								<td class="return_xiang_td1"><?=$detail->num?></td>
								<td class="return_xiang_td1"><?=$detail->hasNum?></td>
								<td class="return_xiang_td1"><input type="text" name="rukuNum[<?=$detail->id?>]" value="<?=$detail->num-$detail->hasNum?>" max="<?=$detail->num-$detail->hasNum?>" class="layui-input" lay-verify="required|number|kucun" onmouseover="tips(this,'可入库数量：'+max,1);" onmouseout="hideTips()"></td>
							</tr>
							<?
						}
						?>
					</tbody></table>
					<div class="ruku_wait_tt">
						<ul>
							<li>
								<div class="ruku_wait_tt1"><span style="color:#ff0000;">*</span>入库日期</div>
								<input name="dtTime" id="dtTime" value="<?=date("Y-m-d")?>" lay-verify="required" class="ruku_wait_input">
							</li>
							<li>
								<div class="ruku_wait_tt1">经办人</div>
								<input name="jingbanren" value="" class="ruku_wait_input">
							</li>
							<li>
								<div class="ruku_wait_tt1">入库类型</div>
								<input disabled="disabled" value="采购入库" class="ruku_wait_input">
							</li>
						</ul>
						<ul>
							<li>
								<div class="ruku_wait_tt1"><span style="color:#ff0000;">*</span>入库单号</div>
								<input value="<?=$orderId?>" name="orderId" class="ruku_wait_input" lay-verify="required">
							</li>
							<li>
								<div class="ruku_wait_tt1">入库备注</div>
								<textarea name="beizhu" class="ruku_wait_textarea" placeholder="请输入备注信息，最多不超过100字"></textarea>
							</li>
						</ul>
						<div class="clearBoth"></div>
						<div class="ruku_wait_tt2">
							<span>采购单号：<?=$jilu->orderId?></span>
							<span>采购员：<?=$jilu->caigouyuan?></span>
							<span> 制单人：<?=$jilu->username?></span><br>
							<span> 采购备注：<?=$jilu->beizhu?></span>
						</div>
					</div>
				</div>
			</form>
			</div>
			<? }
			if(!empty($rukuJilus)){
			?>
			<div class="ruku_record">
				<div class="ruku_nam">
					<div class="ruku_nam_left">
						<img src="images/ruku_record.png"><span> 入库记录</span>
					</div>
					<div class="ruku_nam_right toggleNext">
						<a href="javascript:"><img src="images/pullup.gif"></a>
					</div>
					<div class="clearBoth"></div>
				</div>
				<ul>
					<?
					foreach ($rukuJilus as $j) {
						$details = $db->get_results("select pdtInfo,units,num,caigouId from demo_kucun_jiludetail$fenbiao where jiluId=".$j->id." order by id asc");
						?>
						<li id="print_<?=$j->id?>">
							<div class="ruku_search">
								<div class="ruku_search_left">
									入库日期：<?=date("Y-m-d H:i",strtotime($j->dtTime))?>&nbsp;&nbsp;&nbsp;入库状态：<? switch($j->status){
										case 0:echo '<font color="red">待审核</font>';break;
										case 1:echo '<font color="green">已审核</font>';break;
										case -1:echo '<font color="red">已驳回</font>';break;
									}?>
								</div>
								<div class="ruku_record_right noprint">
									<div><a href="?m=system&s=caigou&a=daochuRuku&id=<?=$j->id?>" target="_blank"><img src="images/derive.gif">导出</a></div>
									<div><a href="javascript:doPrint(<?=$j->id?>);location.reload();" target="_blank"><img src="images/print2.gif">打印</a></div>
									<div><a href="javascript:zuofei(<?=$j->id?>);"><img src="images/delete.gif">作废</a></div>
								</div>
								<div class="clearBoth"></div>
							</div>
							<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #e5e5e5;">
								<tbody><tr>
									<td class="ruku_td" width="70"></td>
									<td class="ruku_td" width="176">商品编码</td>
									<td class="ruku_td" width="464">商品名称</td>
									<td class="ruku_td" width="254">规格</td>
									<td class="ruku_td" width="136">单位</td>
									<td class="ruku_td" width="104">采购数</td>
									<td class="ruku_td" width="190">本次入库数</td>
								</tr>
								<? if(!empty($details)){
									foreach ($details as $i=>$d){
										$pdtInfo = json_decode($d->pdtInfo,true);
										$caigouNum = $db->get_var("select num from demo_caigou_detail$fenbiao where id=".$d->caigouId);
										?>
										<tr>
											<td class="return_xiang_td1"><?=$i+1?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['sn']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['title']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['key_vals']?></td>
											<td class="return_xiang_td1"><?=$d->units?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($caigouNum,$product_set->number_num)?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($d->num,$product_set->number_num)?></td>
										</tr>
										<?
									}
								}?>
							</tbody></table>
							<div class="ruku_record_tt2">
								<span>入库单号：<?=$j->orderId?></span>
								<span>入库仓：<?=$db->get_var("select title from demo_kucun_store where id=$j->storeId")?></span>
								<span>经办人：<?=$j->jingbanren?></span>
								<span>入库备注：<?=$j->beizhu?></span><br>
								<span>采购单号：<?=$jilu->orderId?></span>
								<span>采购员：<?=$jilu->caigouyuan?></span>
								<span>制单人：<?=$jilu->username?></span>
								<span>采购备注：<?=$jilu->beizhu?></span>
							</div>
						</li>
						<?
					}
					?>
				</ul>
			</div>
			<? }
			if(!empty($zuofeiJilus)){
			?>
			<div class="ruku_cancel">
				<div class="ruku_nam">
					<div class="ruku_nam_left">
						<img src="images/cancellation.png"><span> 已作废记录</span>
					</div>
					<div class="ruku_nam_right toggleNext">
						<a href="javascript:"><img src="images/pullup.gif"></a>
					</div>
					<div class="clearBoth"></div>
				</div>
				<ul>
					<?
					foreach ($zuofeiJilus as $j) {
						$details = $db->get_results("select pdtInfo,units,num,caigouId from demo_kucun_jiludetail$fenbiao where jiluId=".$j->id." order by id asc");
						?>
						<li id="print_<?=$j->id?>">
							<div class="ruku_search">
								<div class="ruku_search_left">
									入库日期：<?=date("Y-m-d H:i",strtotime($j->dtTime))?>
								</div>
								<div class="ruku_record_right noprint">
									<div><a href="?m=system&s=caigou&a=daochuRuku&id=<?=$j->id?>" target="_blank"><img src="images/derive.gif">导出</a></div>
									<div><a href="javascript:doPrint(<?=$j->id?>);location.reload();" target="_blank"><img src="images/print2.gif">打印</a></div>
								</div>
								<div class="clearBoth"></div>
							</div>
							<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #e5e5e5;">
								<tbody><tr>
									<td class="ruku_td" width="70"></td>
									<td class="ruku_td" width="176">商品编码</td>
									<td class="ruku_td" width="464">商品名称</td>
									<td class="ruku_td" width="254">规格</td>
									<td class="ruku_td" width="136">单位</td>
									<td class="ruku_td" width="104">采购数</td>
									<td class="ruku_td" width="190">本次入库数</td>
								</tr>
								<? if(!empty($details)){
									foreach ($details as $i=>$d){
										$pdtInfo = json_decode($d->pdtInfo,true);
										$caigouNum = $db->get_var("select num from demo_caigou_detail$fenbiao where id=".$d->caigouId);
										?>
										<tr>
											<td class="return_xiang_td1"><?=$i+1?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['sn']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['title']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['key_vals']?></td>
											<td class="return_xiang_td1"><?=$d->units?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($caigouNum,$product_set->number_num)?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($d->num,$product_set->number_num)?></td>
										</tr>
										<?
									}
								}?>
							</tbody></table>
							<div class="ruku_record_tt2">
								<span>状态：已作废</span><br>
								<span>作废时间：<?=$j->shenheTime?></span><br>
								<span>作废原因：<?=$j->shenheCont?></span><br>
								<span>入库单号：<?=$j->orderId?></span>
								<span>入库仓：<?=$db->get_var("select title from demo_kucun_store where id=$jilu->storeId")?></span>
								<span>经办人：<?=$j->jingbanren?></span>
								<span>入库备注：<?=$j->beizhu?></span><br>
								<span>采购单号：<?=$jilu->orderId?></span>
								<span>采购员：<?=$jilu->caigouyuan?></span>
								<span>制单人：<?=$jilu->username?></span>
								<span>采购备注：<?=$jilu->beizhu?></span>
							</div>
						</li>
						<?
					}
					?>
				</ul>
			</div>
			<? }?>
			<div class="ruku_bottom">
			</div>
		</div>
<script type="text/javascript">
	layui.use(['laydate','form'], function(){
		var laydate = layui.laydate
		,form = layui.form
		laydate.render({
		  	elem: '#dtTime'
		  	,max:'<?=date("Y-m-d H:i:s")?>'
  			,value:'<?=date("Y-m-d H:i")?>'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
		});
		form.verify({
			kucun:function(value,item){
				if(value<0){
					return '入库数量不能小于0';
				}
				var max = parseFloat($(item).attr('max'));
				if(value>max){
					$(item).focus();
					return '入库数量'+value+'不能大于'+max;
				}
			}
		});
		form.on('submit(tijiao)', function(data){
			var cangku = data.field.storeId;
			if(cangku=='' || cangku==0){
				layer.msg("请先选择要入库的仓库！",function(){});
				return false;
			}
			var cangkuName = $("#storeId option:selected").html();
			var zhonglei = 0;
			var num = 0;
			$("#rukuForm input[name^='rukuNum[']").each(function(){
				if($(this).val()>0){
					zhonglei = zhonglei+1;
					num = num+parseFloat($(this).val());
				}
			});
			if(zhonglei==0){
				layer.msg('没有要入库的商品！',function(){});
				return false;
			}
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
						url: '?m=system&s=caigou&a=addRuku&id=<?=$id?>',
						data: data.field,
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
				'<div class="spxx_shanchu_tanchu_01_left">入库信息核对</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
				'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_02" style="height:120px;padding:0px;margin-top:10px;text-align:center;padding-top:40px;line-height:25px">'+
				'入库仓库：'+cangkuName+'<br>商品种类：'+zhonglei+'<Br>商品数量：'+num+
				'</div>'+
				'</div>'
			});
			return false;
		});
	});
	$(function(){
		$(".toggleNext").click(function(){
			$(this).toggleClass('openIcon');
			$(this).parent().next().slideToggle(200);
		});
	});
	function zuofei(id){
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
					layer.msg('请输入作废原因',function(){});
					return false;
				}
				layer.load();
				ajaxpost=$.ajax({
					type: "POST",
					url: "?m=system&s=caigou&a=zuofei&caigouId=<?=$id?>",
					data: "&id="+id+"&beizhu="+beizhu,
					dataType:"json",timeout : 8000,
					success: function(resdata){
						layer.closeAll('loading');
						location.reload();
					},
					error: function() {
						layer.closeAll();
						layer.msg('数据请求失败', {icon: 5});
					}
				});
			}
			,btnAlign: 'r'
			,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
			'<div class="spxx_shanchu_tanchu_01">'+
			'<div class="spxx_shanchu_tanchu_01_left">确定要作废该入库记录吗？</div>'+
			'<div class="spxx_shanchu_tanchu_01_right">'+
			'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
			'</div>'+
			'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
				'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入作废原因"></textarea>'+
			'</div>'+
			'</div>'
		});
	}
	function doPrint(id) {
		window.document.body.innerHTML=$("#print_"+id).html();  
		window.print();
	}
</script>
<? require('views/help.html');?>
</body>
</html>