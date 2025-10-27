<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
$addrows = array();
$tags = array();
if(!empty($product_set->addrows)){
	$addrows = explode('@_@',$product_set->addrows);
}
if(!empty($product_set->tags)){
	$tags = explode('@_@',$product_set->tags);
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		var priceNum = <?=$product_set->price_num?>;
		var numNum = <?=$product_set->number_num?>;
	</script>
	<script type="text/javascript" src="js/product_set.js"></script>
	<style type="text/css">
		.churukushezhi_01_down_1{width:110px;float:left;text-align:right;line-height:32px;font-size:13px;color:#333}
		.churukushezhi_01_down_2{width:auto;float:left;padding-left:10px;line-height:32px;font-size:13px;color:#333}
		.churukushezhi_01_down_2 input{vertical-align:middle}
		.churukushezhi_01_down_2_1{width:223px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-left:10px;font-size:13px;color:#acacac}
		.churukushezhi_01_down_2_2{width:83px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-right:20px;text-align:right;font-size:13px;color:#acacac}
		.churukushezhi_01_down_shuoming{padding-left:114px;font-size:13px;color:#a8a8a8;padding-top:10px;}
	</style>
</head>
<body>
	<div class="spshezhi">
		<div class="spshezhi_1">
			<img src="images/biao_35.png"> 商品设置
		</div>
		<form id="productSetForm" action="?m=system&s=product_set&a=set" method="post" class="layui-form">
			<div class="spshezhi_2">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>商品编码设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									商品编码：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="sn_rule" value="<?=$product_set->sn_rule?>" lay-verify="required" placeholder="编码前缀" maxlength="10" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_shuoming">
									说明：前缀由管理员自主设置， 000000为自动生成序号位数。<br>此处设置本系统的编码规则，您可以在创建产品的时候自主设置商品编码
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="spshezhi_2_up">
					<span>商品设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_image" <? if($product_set->if_image==1){?>checked<? }?> title="启用商品图片" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">
							</div>
						</li>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_brand" <? if($product_set->if_brand==1){?>checked<? }?> title="启用商品品牌" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">
								启用商品品牌后，商品将增加品牌选项，且商品品牌将作为商品筛选条件。
							</div>
						</li>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" id="if_weight" name="if_weight" <? if($product_set->if_weight==1){?>checked<? }?> title="启用商品重量字段" lay-skin="primary" lay-filter="if_weight">
								<span id="if_weight_unit" <? if($product_set->if_weight==0){?>style="display:none;"<? }?>>，并设置商品重量计量单位为:<input type="text" name="weight" value="<?=empty($product_set->weight)?'kg':$product_set->weight?>" placeholder="kg"></span>
							</div>
							<div class="spshezhi_2_down_02">
								启用商品重量字段后，订单商品清单中将显示商品重量字段，且将按此字段计算重量合计。
							</div>
						</li>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_addrows" <? if($product_set->if_addrows==1){?>checked<? }?> title="启用商品自定义字段" lay-filter="if_addrows" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">
								启用本功能后，所有商品均会增加自定义字段内容，最多支持10个自定义字段。
							</div>
							<div class="spshezhi_2_down_03" style="width:900px;<? if($product_set->if_addrows==0){?>display:none;<? }?>" id="if_addrows_unit" >
								<input type="text" name="addrows[0]" class="layui-input" value="<?=empty($addrows[0])?'':$addrows[0]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[1]" class="layui-input" value="<?=empty($addrows[1])?'':$addrows[1]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[2]" class="layui-input" value="<?=empty($addrows[2])?'':$addrows[2]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[3]" class="layui-input" value="<?=empty($addrows[3])?'':$addrows[3]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[4]" class="layui-input" value="<?=empty($addrows[4])?'':$addrows[4]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[5]" class="layui-input" value="<?=empty($addrows[5])?'':$addrows[5]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[6]" class="layui-input" value="<?=empty($addrows[6])?'':$addrows[6]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[7]" class="layui-input" value="<?=empty($addrows[7])?'':$addrows[7]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[8]" class="layui-input" value="<?=empty($addrows[8])?'':$addrows[8]?>" placeholder="请输入自定义字段的名称">
								<input type="text" name="addrows[9]" class="layui-input" value="<?=empty($addrows[9])?'':$addrows[9]?>" placeholder="请输入自定义字段的名称">
							</div>
						</li>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_tags" <? if($product_set->if_tags==1){?>checked<? }?> title="商品标签管理" lay-filter="if_tags" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">
								管理您的商品标签，最多支持7个标签。
							</div>
							<div class="spshezhi_2_down_03" id="if_tags_unit" <? if($product_set->if_tags==0){?>style="display:none;"<? }?>>
								<input type="text" name="tags[]" value="<?=empty($tags[0])?'':$tags[0]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[0]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[1])?'':$tags[1]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[1]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[2])?'':$tags[2]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[2]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[3])?'':$tags[3]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[3]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[4])?'':$tags[4]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[4]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[5])?'':$tags[5]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[5]?><br>
								<input type="text" name="tags[]" value="<?=empty($tags[6])?'':$tags[6]?>" placeholder="添加新标签">&nbsp;&nbsp;前台链接地址：/index.php?p=4&tags=<?=$tags[6]?><br>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_2" style="display:none;">
				<div class="spshezhi_2_up">
					<span>零售设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_lingshou" <? if($product_set->if_lingshou==1){?>checked<? }?> title="启用商品零售价格" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">	
								启用商品零售价格后，新增商品将增加零售价格、市场价格、成本价格选项。
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_2" style="display:none;">
				<div class="spshezhi_2_up">
					<span>订货设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_dinghuo" <? if($product_set->if_dinghuo==1){?>checked<? }?> title="启用商品订货价格" lay-filter="if_dinghuo" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">
								启用商品订货价格后，商品将增加订货价格选项，可按级别/客户设置订货价格。
							</div>
						</li>
						<li class="if_dinghuo_info" <? if($product_set->if_dinghuo==0){?>style="display:none;"<?}?>>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_dinghuo_min" <? if($product_set->if_dinghuo_min==1){?>checked<? }?> title="启用商品起订量" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">

							</div>
						</li>
						<li class="if_dinghuo_info" <? if($product_set->if_dinghuo==0){?>style="display:none;"<?}?>>
							<div class="spshezhi_2_down_01">
								<input type="checkbox" name="if_dinghuo_max" <? if($product_set->if_dinghuo_max==1){?>checked<? }?> title="启用商品限购量（单次）" lay-skin="primary">
							</div>
							<div class="spshezhi_2_down_02">

							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_3" style="display:none;">
				<div class="spshezhi_3_up">
					<h2>数字精度</h2>精度一旦设置不要改小，以避免精度丢失造成的数据错误。
				</div>
				<div class="spshezhi_3_down">
					<ul>
						<li>
							商品价格小数位  <div class="layui-input-inline" style="width:190px;">
								<select name="price_num">
									<option value="2" <? if($product_set->price_num>2){?>disabled<? }?>>2</option>
									<option value="3" <? if($product_set->price_num==3){?>selected="selected"<? }else if($product_set->price_num>3){?>disabled<? }?>>3</option>
									<option value="4" <? if($product_set->price_num==4){?>selected="selected"<? }?>>4</option>
								</select>
							</div>
						</li>
						<li>
							商品数量小数位  <div class="layui-input-inline" style="width:190px;">
								<select name="number_num" >
									<option value="0" <? if($product_set->number_num>0){?>disabled<? }?>>0</option>
									<option value="1" <? if($product_set->number_num==1){?>selected="selected"<? }else if($product_set->number_num>1){?>disabled<? }?>>1</option>
									<option value="2" <? if($product_set->number_num==2){?>selected="selected"<? }?>>2</option>
								</select>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_4">
				<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
			</div>
		</form>
	</div>
	<? require('views/help.html');?>
</body>
</html>