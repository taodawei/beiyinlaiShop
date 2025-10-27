<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$dinghuo_set = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css" />
	<link href="styles/index.css" rel="stylesheet" type="text/css" />
	<link href="styles/duanxin.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="right_down">
		<form action="?m=system&s=dinghuo_set&a=index&tijiao=1" id="setForm" class="layui-form" method="post">
			<div class="kehushezhi_01">
				<img src="images/duanxin_20.png"/> <?=$kehu_title?>设置
			</div>
			<div class="kehushezhi_02">
				<div class="kehushezhi_02_up">	
					<img src="images/duanxin_21.gif"/> <?=$kehu_title?>设置
				</div>
				<div class="kehushezhi_02_down">
					<div class="kehushezhi_02_down_01">
						订货商名称：
					</div>
					<div class="kehushezhi_02_down_02">
						<input name="kehu_title" class="layui-input" value="<?=$dinghuo_set->kehu_title?>" lay-filter="required" type="text"/>
					</div>
					<div class="kehushezhi_02_down_03">
						（订货伙伴的统一称谓，如经销商、代理商、加盟店、最长4个字符）
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="kehushezhi_03">
				<div class="kehushezhi_02_up">	
					<img src="images/duanxin_21.gif"/> <?=$kehu_title?>资金帐户
				</div>
				<div class="kehushezhi_03_down">
					<ul>
						<li>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_ifxianjin" checked disabled title="启用" lay-skin="primary">
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_xianjin_pre" value="<?=$dinghuo_set->acc_xianjin_pre?>" lay-verify="required" placeholder="0001" class="layui-input"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_xianjin_name" lay-verify="required" readonly="true" value="现金账户" class="layui-input disabled"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" readonly="true" value="现金货币账户" class="layui-input disabled"/>
							</div>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_xianjin_queren" <? if($dinghuo_set->acc_xianjin_queren==1){?>checked<? }?> title="余额付款无需确认" lay-skin="primary"> 
							</div>
							<div class="kehushezhi_03_down_4">
								（此账户<?=$kehu_title?>可充值，可用余额支付订单）
							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_ifyufu" <? if($dinghuo_set->acc_ifyufu==1){?>checked<?}?> title="启用" lay-skin="primary" lay-filter="yufu" dtitle='<?=$dinghuo_set->acc_yufu_name?>'>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_yufu_pre" value="<?=$dinghuo_set->acc_yufu_pre?>" lay-verify="required" placeholder="0002" class="layui-input acc_yufu"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_yufu_name" lay-verify="required" value="<?=$dinghuo_set->acc_yufu_name?>" placeholder="预付款账户" class="layui-input acc_yufu"/>
							</div>
							<div class="kehushezhi_03_down_2">
							</div>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_yufu_queren" id="acc_yufu_queren" <? if($dinghuo_set->acc_yufu_queren==1){?>checked<? }?> title="余额付款无需确认" lay-skin="primary"> 
							</div>
							<div class="kehushezhi_03_down_4">
								（此账户<?=$kehu_title?>不可充值，可用余额支付订单）
							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_iffandian" <? if($dinghuo_set->acc_iffandian==1){?>checked<?}?> title="启用" lay-skin="primary" lay-filter="fandian" dtitle='<?=$dinghuo_set->acc_fandian_name?>'>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_fandian_pre" value="<?=$dinghuo_set->acc_fandian_pre?>" lay-verify="required" placeholder="0003" class="layui-input acc_fandian"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_fandian_name" lay-verify="required" value="<?=$dinghuo_set->acc_fandian_name?>" placeholder="预付款账户" class="layui-input acc_fandian"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" readonly="true" value="虚拟货币账户（积分、返点等）" class="layui-input disabled"/>
							</div>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_fandian_queren" id="acc_fandian_queren" <? if($dinghuo_set->acc_fandian_queren==1){?>checked<? }?> title="余额付款无需确认" lay-skin="primary"> 
							</div>
							<div class="kehushezhi_03_down_4">
								（此账户<?=$kehu_title?>不可充值，可用余额支付订单）
							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_ifbaozheng" <? if($dinghuo_set->acc_ifbaozheng==1){?>checked<?}?> title="启用" lay-skin="primary" lay-filter="baozheng" dtitle='<?=$dinghuo_set->acc_baozheng_name?>'>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_baozheng_pre" value="<?=$dinghuo_set->acc_baozheng_pre?>" lay-verify="required" placeholder="0004" class="layui-input acc_baozheng"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" name="acc_baozheng_name" lay-verify="required" value="<?=$dinghuo_set->acc_baozheng_name?>" placeholder="预付款账户" class="layui-input acc_baozheng"/>
							</div>
							<div class="kehushezhi_03_down_2">
								<input type="text" readonly="true" value="现金货币账户" class="layui-input disabled"/>
							</div>
							<div class="kehushezhi_03_down_1">
								<input type="checkbox" name="acc_baozheng_queren" id="acc_baozheng_queren" <? if($dinghuo_set->acc_baozheng_queren==1){?>checked<? }?> title="余额付款无需确认" lay-skin="primary"> 
							</div>
							<div class="kehushezhi_03_down_4">
								（此账户<?=$kehu_title?>可充值，不可用余额支付订单）
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
				</div>
			</div>
			<div class="cukunshezhi_02" style="padding-top:30px;padding-left:50px;">
				<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="js/dinghuo_set.js"></script>
	<? require('views/help.html');?>
</body>
</html>