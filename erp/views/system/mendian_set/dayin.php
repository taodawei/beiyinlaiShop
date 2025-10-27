<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$storeId = (int)$request['storeId'];
$shezhi = $db->get_row("select * from demo_prints where comId=$comId and storeId=$storeId limit 1");
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
			<img src="images/biao_35.png"> 打印设置
		</div>
		<form id="productSetForm" action="?m=system&s=mendian_set&a=dayin&id=<?=$shezhi->id?>&storeId=<?=$storeId?>&tijiao=1" method="post" class="layui-form">
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>易联云打印机设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									易联云userId：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="userId" value="<?=$shezhi->userId?>" lay-verify="required" placeholder="" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									易联云Akey：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="Akey" value="<?=$shezhi->Akey?>" lay-verify="required" placeholder="" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									打印机Tnumber：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="Tnumber" value="<?=$shezhi->Tnumber?>" lay-verify="required" placeholder="" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									打印机Tkey：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="Tkey" value="<?=$shezhi->Tkey?>" lay-verify="required" placeholder="" class="layui-input" style="width:280px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									自动打印：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="checkbox" name="status" lay-skin="switch" <? if($shezhi->status==1){?>checked="true"<? }?> lay-text="开启|关闭">
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