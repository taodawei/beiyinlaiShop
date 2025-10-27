<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="cangkuguanli_1">
		<div class="cangkuguanli_1_left">
			<img src="images/biao_87.png"> 库存设置
		</div>
		<div class="clearBoth"></div>
	</div>
	<div class="right_down">
		<div class="cukunshezhi">
			<form action="?m=system&s=kucun_set&a=index&tijiao=1" id="setForm" class="layui-form" method="post">
				<div class="cukunshezhi_01">
					<div class="cukunshezhi_01_left">
						<?=$kehu_title?>订货仓库限制
					</div>
					<div class="cukunshezhi_01_right">
						<div class="cukunshezhi_01_right_01">
							<select name="dinghuo_store">
								<option value="0">按总仓库订购</option>
								<option value="1" <? if($product_set->dinghuo_store==1){?>selected="selected"<? }?>>按<?=$kehu_title?>对应仓库订购</option>
							</select>
						</div>                   
						<div class="cukunshezhi_01_right_02">
							1. 按总仓库订购：不限订<?=$kehu_title?>的供货仓库，即<?=$kehu_title?>可订购所有仓库的商品。<br>
							2. 按<?=$kehu_title?>对应仓库订购：仓库定向供货给<?=$kehu_title?>，即<?=$kehu_title?>只能订购对应仓库的商品。
						</div>
						<div class="cukunshezhi_01_right_03">
							<input type="checkbox" name="dinghuo_limit" <? if($product_set->dinghuo_limit==1){?>checked<? }?> title="商品库存（扣减预购值）数量小于等于零，禁止订货" lay-skin="primary">
						</div>
					</div>                
					<div class="clearBoth"></div>
				</div>
				<div class="cukunshezhi_01">
					<div class="cukunshezhi_01_left">
						<?=$kehu_title?>订货系统库存显示方式
					</div>
					<div class="cukunshezhi_01_right">
						<div class="cukunshezhi_01_right_01">
							<select name="kucun_type">
								<option value="1" <? if($product_set->kucun_type==1){?>selected="selected"<? }?>>不显示库存</option>
								<option value="2" <? if($product_set->kucun_type==2){?>selected="selected"<? }?>>显示库存有无</option>
								<option value="3" <? if($product_set->kucun_type==3){?>selected="selected"<? }?>>显示库存数量</option>
							</select>
						</div>                   
						<div class="cukunshezhi_01_right_02">
							1.不显示库存：库存数量仅用于公司内部库存管理，<?=$kehu_title?>订货系统不显示商品库存信息。
							<br>2.显示库存有无：<?=$kehu_title?>订货系统显示商品库存有无，但不显示具体数量。
							<br>3.显示库存数量：<?=$kehu_title?>订货系统显示商品库存具体数量。
						</div>
					</div>                
					<div class="clearBoth"></div>
				</div>
				<div class="cukunshezhi_01">
					<div class="cukunshezhi_01_left">
						是否允许超库存出库
					</div>
					<div class="cukunshezhi_01_right">
						<div class="cukunshezhi_01_right_011">
							<input type="radio" name="chuku_limit" value="0" <? if($product_set->chuku_limit==0){?>checked<? }?> title="允许"/>
							<input type="radio" name="chuku_limit" value="1" <? if($product_set->chuku_limit==1){?>checked<? }?> title="不允许"/>
						</div>
						<div class="cukunshezhi_01_right_012">
							<span>1.允许：即商品出库时， 出库数量可超出仓库实际库存数量。</span><br>
							如无需在订货系统中管理库存，可选择此选项。<br>
							<span>2.不允许：即商品出库时，出库数量不可超出仓库实际库存数量。</span><br>
							如需要在本系统中严格管理库存，建议选择此项。<br>
						</div>
					</div>                
					<div class="clearBoth"></div>
				</div>
				<div class="cukunshezhi_02">
					<button class="layui-btn layui-btn-normal" lay-submit="" > 保 存 </button>
				</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		layui.use(['form'], function(){});
	</script>
	<? require('views/help.html');?>
</body>
</html>