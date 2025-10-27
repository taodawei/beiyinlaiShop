<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shezhi = $db->get_row("select * from demo_shezhi where comId=$comId");
if(!empty($shezhi->zuobiao)){
	$zuobiaos = explode('|',$shezhi->zuobiao);
	if(!empty($zuobiaos)){
		$heng = $zuobiaos[0];
		$zong = $zuobiaos[1];
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/keditor/kindeditor.js"></script>
	<style type="text/css">
		.churukushezhi_01_down ul li{height:45px;line-height:45px;}
		.churukushezhi_01_down_1{width:140px;float:left;text-align:right;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2{display:inline-block;padding-left:10px;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2 input{vertical-align:middle}
		.churukushezhi_01_down_2_1{width:223px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-left:10px;font-size:13px;color:#acacac}
		.churukushezhi_01_down_2_2{width:83px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-right:20px;text-align:right;font-size:13px;color:#acacac}
		.churukushezhi_01_down_shuoming{padding-left:114px;font-size:13px;color:#a8a8a8;padding-top:10px;}
		.mingpianshezhi_2_01{padding:18px 0}
		.mingpianshezhi_2_02{padding-bottom:57px;height:27px;line-height:27px;font-size:14px;color:#b7b7b7}
		.mingpianshezhi_2_02 a{width:80px;height:25px;display:inline-block;vertical-align:middle;margin-right:8px;border:#2791fa 1px solid;border-radius:5px;text-align:center;line-height:25px;font-size:16px;color:#2791fa}
		.mingpianshezhi_2_03{height:90px;display:none}
		.mingpianshezhi_2_03_1{width:365px;float:left;height:54px;}
		.mingpianshezhi_2_03_3{width:auto;float:left}
		.mingpianshezhi_2_03_3 img{vertical-align:middle;margin-right:20px}
		.mingpianshezhi_2_04{width:100%}
		.mingpianshezhi_2_04 span{display:inline-block;padding-right:30px;font-size:16px;color:#757575;line-height:32px}
		.mingpianshezhi_2_04 input{vertical-align:middle;margin-right:4px;width:16px;height:16px}
		.mingpianshezhi_2_04 span b{font-weight:400;font-size:13px;color:#a5a5a5}
		.mingpianshezhi_2_04 span b strong{font-weight:400;font-size:13px;color:#ff3434}
		.mingpianshezhi_2_xingxiang_left{width:211px;float:left}
		.mingpianshezhi_2_xingxiang{padding-bottom:20px;display:none}
		.mingpianshezhi_2_xingxiang_right{width:auto;float:left;padding-top:90px}
		.mingpianshezhi_2_xingxiang_right img{vertical-align:middle;margin-right:20px}
	</style>
</head>
<body>
	<div class="spshezhi">
		<div class="spshezhi_1">
			<img src="images/biao_35.png"> 店铺设置
		</div>
		<form id="productSetForm" action="?m=system&s=mendian_set&a=index1&tijiao=1" method="post" class="layui-form">
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>店铺设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									店铺联系电话 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_phone" value="<?=$shezhi->com_phone?>" lay-verify="required" placeholder="" maxlength="25" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									店铺地址 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_address" value="<?=$shezhi->com_address?>" lay-verify="required" placeholder="" maxlength="25" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="height:auto;">
								<div class="churukushezhi_01_down_1">
									标注地址 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<style type="text/css">
										html,body{margin:0;padding:0;}
										.iw_poi_title {color:#CC5522;font-size:14px;font-weight:bold;overflow:hidden;padding-right:13px;white-space:nowrap}
										.iw_poi_content {font:12px arial,sans-serif;overflow:visible;padding-top:4px;white-space:-moz-pre-wrap;word-wrap:break-word}
									</style>
									<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=cIUKusewZaKmqALQv6lKtIcY&s=1"></script>
									地址坐标：
									<input type="text" id="TextBox1" name="hengzuobiao" value="<?=$heng?>" style="height: 35px;line-height: 35px;border: 1px #e1e4ea solid;" />
									<input type="text" id="TextBox2" name="zongzuobiao" value="<?=$zong?>" style="height: 35px;line-height: 35px;border: 1px #e1e4ea solid;" /> <span style="color:red;">点击地图标注签到详细坐标位置</span><br>
									<input id="address" type="text" class="new_qdgz_input" placeholder="收入所在地址" style="width:400px;">
									<input type="button" class="new_qdgz_input" onclick="searchMap();" value="检索" style="width:50px;padding-left:0px;height:37px;"/>
									<div style="width:600px;height:400px;border:#ccc solid 1px;margin:0px;margin-top:10px;" id="container"></div>

									<script type="text/javascript">
										var map = new BMap.Map("container");
										<? if($heng>0){?>
											map.centerAndZoom(new BMap.Point(<?=$heng?>, <?=$zong?>), 18);
											var point1 = new BMap.Point(<?=$heng?>,<?=$zong?>);
											var marker1 = new BMap.Marker(point1);
											map.addOverlay(marker1);
										<? }else{?>
											map.centerAndZoom("保定",12);
										<? }?>
										var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});// 左上角，添加比例尺
										var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件      
										map.addControl(top_left_control);
										map.addControl(top_left_navigation);
										map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
										map.enableContinuousZoom();
										//单击获取点击的经纬度
										map.addEventListener("click",function(e){
											document.getElementById('TextBox1').value = e.point.lng;
											document.getElementById('TextBox2').value = e.point.lat;
											map.clearOverlays();
											var point = new BMap.Point(e.point.lng,e.point.lat);
											var marker = new BMap.Marker(point);
											map.addOverlay(marker);
											//alert("标注成功，您标注的位置："+e.point.lng+","+e.point.lat);
										});
										function searchMap(){
											var add = $("#address").val();
											var local = new BMap.LocalSearch(map, {
												renderOptions:{map: map, panel:"r-result"},
												pageCapacity:5
											});
											local.search(add);
										}
									</script>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="height: auto;">
								<div class="churukushezhi_01_down_1">
									店铺简介 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<?php
			                            ewebeditor(EDITORSTYLE,'com_desc',$shezhi->com_desc,'800');
			                        ?>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="height: auto;">
								<div class="churukushezhi_01_down_1">
									资质荣誉 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<?php
			                            ewebeditor(EDITORSTYLE,'com_honor',$shezhi->com_honor,'800');
			                        ?>
								</div>
								<div class="clearBoth"></div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="spshezhi_4" style="position:fixed;padding-bottom:10px;background:#fff;padding-left:20px;bottom:0px;width:100%">
				<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
			</div>
		</form>
	</div>
	<script type="text/javascript">
	  layui.use(['laydate','form'], function(){
	    var laydate = layui.laydate
	    ,form = layui.form
	    form.on('submit(tijiao)', function(data){
	        layer.load();
	    });
	});
	</script>
</body>
</html>