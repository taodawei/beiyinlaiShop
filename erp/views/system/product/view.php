<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_inventory = $db->get_row("select * from demo_product_inventory where id=$id and comId=$comId");
if(empty($product_inventory)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
$nowSelect = array();
if(!empty($product_inventory->key_ids)){
	$nowSelect = explode('-', $product_inventory->key_ids);
}
$productId = $product_inventory->productId;
$product = $db->get_row("select * from demo_product where id=$productId");
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$keys = $db->get_results("select id,title,parentId,originalPic from demo_product_key where productId=$productId and isnew=0 order by parentId asc,id asc");
$inventory_keys = $db->get_var("select group_concat(key_ids) from demo_product_inventory where productId=$productId");
$keysArry = array();
$rows = 0;
if(!empty($keys)&&count($keys)>1){
	foreach ($keys as $k){
		$keysArry[$k->parentId][$k->id]['title'] = $k->title;
		$keysArry[$k->parentId][$k->id]['image'] = $k->originalPic;
	}
	$rows = count($keysArry[0]);
}
$originalPics = array();
if(!empty($product->originalPic)){
	$originalPics = explode('|',$product->originalPic);
}
$pdtUnits = json_decode($product->untis,true);
$pdtUnitstr = $pdtUnits[0]['title'];
$pdtUnitstr1 = '';
if(!empty($pdtUnits[1])){
	$pdtUnitstr1 .= $pdtUnits[1]['title'].'（'.$pdtUnits[1]['num'].$pdtUnits[0]['title'].'）';
}
if(!empty($pdtUnits[2])){
	$pdtUnitstr1 .= ';'.$pdtUnits[2]['title'].'（'.$pdtUnits[2]['num'].$pdtUnits[0]['title'].'）';
}
$tags = array();
if(!empty($product->tags)){
	$tags = explode(',',$product->tags);
}
$cont1 = empty($product_inventory->cont1)?$product->cont1:$product_inventory->cont1;
$cont2 = empty($product_inventory->cont2)?$product->cont2:$product_inventory->cont2;
$cont3 = empty($product_inventory->cont3)?$product->cont3:$product_inventory->cont3;
$url = urlencode($request['url']);
$if_kehu_dinghuo = $db->get_var("select id from demo_product_dinghuo where comId=$comId and inventoryId=$id and type=1 limit 1");
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
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/scrollPic.js"></script>
	<script type="text/javascript" src="js/jquery.jqzoom.js"></script>
	<script type="text/javascript">
		var inventory_keys = '<?=$inventory_keys?>';
		var rows = <?=$rows?>;
		var productId = <?=$productId?>;
		var url = '<?=$url?>';
		var ifshowImg = <?=empty($originalPics)?0:1?>;
	</script>
	<script type="text/javascript" src="js/product_view.js"></script>
	<style type="text/css">
	<? if($product_set->if_dinghuo_min==0){?>
		.dinghuo_if_min{display:none;}
	<? }?>
	<? if($product_set->if_dinghuo_max==0){?>
		.dinghuo_if_max{display:none;}
	<? }?>
	</style>
</head>
<body>
	<div class="spxiangqing">
		<div class="spxiangqing_01">
			<div class="spxiangqing_01_left">
				<a href="<?=empty($request['url'])?'?m=system&s=product':urldecode($request['url'])?>"><img src="images/biao_46.png"/></a> 商品详情
			</div>
			<div class="spxiangqing_01_right">
				<a href="javascript:" onclick="editPdt();"><img src="images/biao_31.png"/> 修改</a><a href="javascript:void()" onclick="del_pdt(<?=$id?>);" class="spxx_shanchu"><img src="images/biao_32.png"/> 删除</a>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="spxiangqing_02">
			<? if(!empty($originalPics)&&$product_set->if_image==1){?>
			<div class="spxiangqing_02_img">
				<div class="spxiangqing_02_img_big" style="position:relative;">
					<img src="<?=ispic($originalPics[0])?>" jqimg="<?php echo ispic($originalPics[0])?>" width="390" height="390" />
				</div>
				<div class="spxiangqing_02_img_small">
					<div class="spxiangqing_02_img_small_01">
						<a href="javascript:" id="LeftArr"><img src="images/biao_40.png"/></a>
					</div>
					<div class="spxiangqing_02_img_small_02">
						<ul id="ISL_Cont_1">
							<? if(!empty($originalPics)){
								foreach ($originalPics as $pic) {
									?>
									<li>
										<a href="javascript:" onclick="showImg('<?=$pic?>');"><img src="<?=$pic?>" width="57"/></a>
									</li>
									<?
								}
							}?>
							<div class="clearBoth"></div>
						</ul>
					</div>
					<div class="spxiangqing_02_img_small_03">
						<a href="javascript:" id="RightArr"><img src="images/biao_41.png"/></a>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<? }?>
			<div class="spxiangqing_02_tt">
				<div class="spxiangqing_02_tt_01">
					<h2><?=$product->title?></h2>
					商品编号：<span id="sn"><?=$product_inventory->sn?></span>&nbsp;&nbsp;&nbsp;
					商品分类：<span><?=$db->get_var("select title from demo_product_channel where id=$product->channelId")?></span>&nbsp;&nbsp;&nbsp;
					<? if(!empty($product->brandId)){?>品牌：<?=$db->get_var("select title from demo_product_brand where id=$product->brandId")?>&nbsp;&nbsp;&nbsp;<? }?>
					条形码：<span id="code"><?=$product_inventory->code?></span>
					商品ID：<span id="code"><?=$product_inventory->id?></span>
				</div>
				<div class="spxiangqing_02_tt_02">
					<? if($product_set->if_lingshou==1){?>
					<div class="spxiangqing_02_tt_02_left">
						市场价 <span><i>￥</i><font id="price_market" style="text-decoration:line-through;"><?=getXiaoshu($product_inventory->price_market,$product_set->price_num) ?></font></span><br>
						零售价 <b><i>￥</i><font id="price_sale"><?=getXiaoshu($product_inventory->price_sale,$product_set->price_num) ?></font></b>
					</div>
					<? }?>
					<? if($product_set->if_dinghuo==1){?>
					<div class="spxiangqing_02_tt_02_right" style="position: relative;">
						<a href="javascript:">订货价 <img src="images/biao_39.png"/></a>
						<div class="ant-tooltip-inner" id="show_dinghuo_price">
							<div class="ant-tabs-nav-wrap">
								<div class="ant-tabs-nav-scroll">
									<div class="ant-tabs-nav ant-tabs-nav-animated" id="d_pMenu">
										<div role="tab" id="d_pMenu1" onclick="qiehuan('d_p',1,'ant-tabs-tab-active');loadPrice(1);" class="ant-tabs-tab-active ant-tabs-tab">按级别定价</div>
										<? if(!empty($if_kehu_dinghuo)){?>
										<div role="tab" id="d_pMenu2" onclick="qiehuan('d_p',2,'ant-tabs-tab-active');loadPrice(2);" class=" ant-tabs-tab">按客户定价</div>
										<? }?>
									</div>
								</div>
							</div>
							<div id="d_pCont1" class="d_pCont">
								<div class="ant-table-small">
									<div class="ant-table-scroll">
										<div class="ant-table-header" style="padding-bottom: 0px;">
											<table width="683">
												<thead class="ant-table-thead">
													<tr>
														<th width="20%"><span>客户级别</span></th><th width="20%"><span>允许订货</span></th><th width="20%"><span>订货价</span></th><th width="20%" class="dinghuo_if_min"><span>起订量</span></th><th width="20%" class="dinghuo_if_max"><span>限订量</span></th>
													</tr>
												</thead>
											</table>
										</div>
										<div class="ant-table-body" style="max-height: 180px; overflow-y:auto;">
											<table width="683">
												<tbody class="ant-table-tbody" id="d_p_table1">
													<tr class="ant-table-row  ant-table-row-level-0">
														<td width="20%" align="center"><div class="load"><img src="images/loading.gif"></div></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div id="d_pCont2" class="d_pCont" style="display:none">
								<div class="ant-table-small">
									<div class="ant-table-scroll">
										<div class="ant-table-header" style="padding-bottom: 0px;">
											<table width="683">
												<thead class="ant-table-thead">
													<tr>
														<th width="20%"><span>客户名称</span></th><th width="20%"><span>允许订货</span></th><th width="20%"><span>订货价</span></th><th width="20%" class="dinghuo_if_min"><span>起订量</span></th><th width="20%" class="dinghuo_if_max"><span>限订量</span></th>
													</tr>
												</thead>
											</table>
										</div>
										<div class="ant-table-body" style="max-height:180px;overflow-y:auto;">
											<table width="683">
												<tbody class="ant-table-tbody" id="d_p_table2">
													<tr class="ant-table-row  ant-table-row-level-0">
														<td width="20%" align="center"><div class="load"><img src="images/loading.gif"></div></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<? }?>
					<div class="clearBoth"></div>
				</div>
				<div class="spxiangqing_02_tt_04">
					<ul>
						<? if(!empty($keysArry)){
							$i=0;
							foreach ($keysArry[0] as $key => $val) {
								$i++;
								if(!empty($keysArry[$key])){
								?>
								<li id="key-<?=$key?>" row="<?=$i?>">
									<div class="spxiangqing_02_tt_04_left">
										<?=$val['title']?>
									</div>
									<div class="spxiangqing_02_tt_04_right">
										<div class="chima">
											<? foreach($keysArry[$key] as $key1 => $val1){
												?>
												<a href="javascript:" <? if(in_array($key1,$nowSelect)){?>class="on"<? }?> data-id="<?=$key1?>" data-row="<?=$i?>" data-key="<?=$key?>" data-img="<?=$val1['image']?>"><?=$val1['title']?></a>
												<?
											}?>
										</div>
									</div>
									<div class="clearBoth"></div>
								</li>
								<?
								}
							}
						}?>
						
						<li>
							<div style="color:#949494;line-height:35px;font-size:13px;">
								<? if($product_set->if_weight==1){?>重量：<span id="weight"><?=$product_inventory->weight?></span><?=$product_set->weight?>&nbsp;&nbsp;&nbsp;<? }?>
								计量单位：<?=$pdtUnitstr?>&nbsp;&nbsp;&nbsp;库存：<span id="kucun"><?=getXiaoshu($product_inventory->kucun,$product_set->number_num) ?></span>
								<? if(!empty($pdtUnitstr1)){?><Br>副单位：<?echo $pdtUnitstr1; }?>
								<? if(!empty($tags)){?><Br>商品标签：<? 
									foreach ($tags as $tag) {
										?><span class="pd-tag"><?=$tag?></span><?
									}
								}?>
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="spxiangqing_03">
			<div class="spxiangqing_03_up" id="pdtcontMenu">
				<ul>
					<li>
						<a href="javascript:" id="pdtcontMenu1" onclick="qiehuan('pdtcont',1,'spxiangqing_03_up_on');" class="spxiangqing_03_up_on">商品介绍</a>
					</li>
					<li>
						<a href="javascript:" id="pdtcontMenu2" onclick="qiehuan('pdtcont',2,'spxiangqing_03_up_on');">规格与包装</a>
					</li>
					<li>
						<a href="javascript:" id="pdtcontMenu3" onclick="qiehuan('pdtcont',3,'spxiangqing_03_up_on');">售后保障</a>
					</li>
					<div class="clearBoth"></div>
				</ul>
			</div>
			<div class="spxiangqing_03_down pdtcontCont" id="pdtcontCont1">
				<? if(!empty($product->addrows)){
					$addrows = json_decode($product->addrows,true);
				?>
				<div class="pd-body-customlabelsx">
					<table>
						<? foreach($addrows as $key=>$val){?>
						<tr class="line">
							<td class="left"><?=$key?></td><td class="right"><?=$val?></td>
						</tr>
						<? }?>
					</table>
				</div>
				<? }?>
				<?=empty($cont1)?'暂无商品介绍':$cont1?>
			</div>
			<div class="spxiangqing_03_down pdtcontCont" id="pdtcontCont2" style="display:none;">
				<?=empty($cont2)?'暂无内容':$cont2?>
			</div>
			<div class="spxiangqing_03_down pdtcontCont" id="pdtcontCont3" style="display:none;">
				<?=empty($cont3)?'暂无内容':$cont3?>
			</div>
		</div>
	</div>
	<input type="hidden" id="inventoryId" value="<?=$product_inventory->id?>">
</body>
</html>