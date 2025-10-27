<?
global $db,$request;
$productId = $id = !empty($request['id'])?$request['id']:0;
$inventoryId = (int)$request['inventoryId'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(empty($productId)){
	$productId = $db->get_var("select productId from demo_product_inventory where id=$inventoryId");
}
$product = $db->get_row("select * from demo_product where id=$productId");
$product_inventory = $db->get_row("select * from demo_product_inventory where productId=$productId limit 1");
if(empty($product)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$product_keys = $db->get_results("select * from demo_product_key where productId=$productId and parentId=0 order by id");
$addrows = array();
$tags = array();
$pdtaddrows = array();
$pdtTags = array();
$originalPics = array();
if(!empty($product_set->addrows)){
	$addrows = explode('@_@',$product_set->addrows);
}
if(!empty($product_set->tags)){
	$tags = explode('@_@',$product_set->tags);
}
if(!empty($product->addrows)){
	$pdtaddrows = json_decode($product->addrows,true);
}
if(!empty($product->tags)){
	$pdtTags = explode(',',$product->tags);
}
if(!empty($product->originalPic)){
	$originalPics = explode('|',$product->originalPic);
}else {
    
    //获取上级分类id
    //864  863  853    861  862
    $root_id = 864;
    if($product_inventory->channelId == 861 || $product_inventory->channelId == 862){
        $root_id = $product_inventory->channelId;
    }else{
       $root_id = $db->get_var("select parentId from demo_product_channel where id = $product_inventory->channelId"); 
    }
    $objectUrl = "product/$root_id/$product->skuId/";
    $objectUrl = "img/$product->skuId/";
    $fileList = listObjectsFile($objectUrl, 100);
    if(!empty($fileList['data'])){
         $originalPics = $fileList['data']; 
    }

}


$pdtUnits = json_decode($product->untis,true);
$pdtUnitstr = $pdtUnits[0]['title'];
$pdtUnitstr1 = $pdtUnits[0]['title'].'|'.$pdtUnits[0]['num'];
if(!empty($pdtUnits[1])){
	$pdtUnitstr .= ' / '.$pdtUnits[1]['title'].'（'.$pdtUnits[1]['num'].$pdtUnits[0]['title'].'）';
	$pdtUnitstr1 .= ','.$pdtUnits[1]['title'].'|'.$pdtUnits[1]['num'];
}
if(!empty($pdtUnits[2])){
	$pdtUnitstr .= ' / '.$pdtUnits[2]['title'].'（'.$pdtUnits[2]['num'].$pdtUnits[0]['title'].'）';
	$pdtUnitstr1 .= ','.$pdtUnits[2]['title'].'|'.$pdtUnits[2]['num'];
}
$unitsql = "select * from demo_product_unit where comId in(0,$comId) ";
if(!empty($product_set->no_units)){
	$unitsql.=" and id not in(".$product_set->no_units.")";
}
$unitsql.=" order by id asc";
$units = $db->get_results($unitsql);
$unitOptions = '';
if(!empty($units)){
	foreach ($units as $u) {
		$unitOptions.='<option value="'.$u->title.'">'.$u->title.'</option>';
	}
}
$brands = $db->get_results("select id,title from demo_product_brand where comId=$comId order by ordering desc,id asc");
$url = urlencode($request['url']);
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$levels = $db->get_results("select * from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$chushu = pow(10,$product_set->price_num);
$step = 1/$chushu;
$chushu1 = pow(10,$product_set->number_num);
$step1 = 1/$chushu1;
$kehuDinghuos = $db->get_results("select id,inventoryId,type,levelId,kehuId,ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where productId=$productId and comId=$comId order by id asc");
$lipinka_str = '';
if($comId==1009){
	$lipinkas = $db->get_results("select id,title from lipinka_jilu where status=1 and (endTime>'".date("Y-m-d")."' or endTime is NULL)");
	if(!empty($lipinkas)){
		foreach ($lipinkas as $lipinka) {
			$lipinka_str.='<option value="'.$lipinka->id.'">'.$lipinka->title.'</option>';
		}
	}
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
	<link href="styles/spgl.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/keditor/kindeditor1.js"></script>
	<script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>
	<script type="text/javascript">
		var $unitOptions = '<?=$unitOptions?>';
		var lipinka_str = '<?=$lipinka_str?>';
		var channelId = <?=$product->channelId?>;
		var unit_type = <?=$product->unit_type?>;
		var step = <?=$step?>;
		var step1 = <?=$step1?>;
		var levelPrices = new Array();
		var kehuPrices = new Array();
		<? if(!empty($kehuDinghuos)){
			foreach ($kehuDinghuos as $kd){
				$kd->price_sale = getXiaoshu($kd->price_sale,$product_set->price_num);
				$kd->dinghuo_min = getXiaoshu($kd->dinghuo_min,$product_set->number_num);
				$kd->dinghuo_max = getXiaoshu($kd->dinghuo_max,$product_set->number_num);
				if($kd->type==0){?>
					levelPrices.push(['<?=$kd->id?>','<?=$kd->levelId?>','<?=$kd->inventoryId?>','<?=$kd->ifsale?>','<?=$kd->price_sale?>','<?=$kd->dinghuo_min?>','<?=$kd->dinghuo_max?>']);
				<?}else{
					$k = $db->get_row("select title,level from demo_kehu where id=$kd->kehuId");
					if(!empty($k)){
						$level = $db->get_var("select title from demo_kehu_level where id=$k->level");
					?>
					kehuPrices.push(['<?=$kd->id?>','<?=$kd->kehuId?>','<?=$kd->inventoryId?>','<?=$kd->ifsale?>','<?=$kd->price_sale?>','<?=$kd->dinghuo_min?>','<?=$kd->dinghuo_max?>','<?=$k->title?>','<?=$level?>']);
					<?}
				}
			}
		}?>
	</script>
	<style type="text/css">
		.edit_guige .layui-form-select,#moreGuige .layui-form-select{width:80%;margin:0px auto;}
		.guige_set table tr td .layui-select-title input{width:100%;margin:0px auto;height:32px;}
	</style>
</head>
<body>
	<form action="?m=system&s=product&a=editProduct&tijiao=1&id=<?=$product->id?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="<?=urldecode($request['url'])?>"><img src="images/back.jpg" /></a>
				<span>修改商品</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="shangjia">
					<input type="checkbox" name="status" lay-skin="primary" <? if($product->status==1){?>checked="true"<? }?> title="立即上架" />
				</div>
				<div class="clearBoth"></div>
				<div class="jichu_message">
					<ul>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品名称 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="title" id="title" value="<?=$product->title?>" onblur="checkPdtTitle(<?=$productId?>);" onmouseover="tips(this,'一经修改，所有同名商品都会修改',1);" onmouseout="hideTips();" lay-verify="required" placeholder="请输入商品名称">
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>货号 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="skuId" lay-verify="required" value="<?=$product->skuId?>" placeholder="请输入货号">
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								有效期 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="skuDay" value="<?=$product->skuDay?>" placeholder="请输入有效期">
							</div>
						</li>
						
						<li style="display:none;">
							<div class="gaojisousuo_left">
								搜索关键字 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" onmouseover="tips(this,'一经修改，所有同名商品都会修改',1);" onmouseout="hideTips();" name="keywords" value="<?=$product->keywords?>" placeholder="多个以逗号，分开">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品分类
							</div>
							<div class="gaojisousuo_right">
								<div class="layui-form-select" onmouseenter="tips(this,'一经修改，所有同名商品都会修改',1);" onmouseout="hideTips();">
									<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="<?=$db->get_var("select title from demo_product_channel where id=".$product->channelId);?>" class="layui-input"><i class="layui-edge"></i></div>
									<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
								</div>
								<input type="hidden" name="channelId" id="channelId" value="<?=$product->channelId?>" lay-verify="required">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								顺序
							</div>
							<div class="gaojisousuo_right" >
								<input type="number" class="layui-input" id="ordering" onmouseover="tips(this,'输入0-99999999之间的数字',1);" onmouseout="hideTips();" name="ordering" value="<?=$product->ordering?>" placeholder="数字越大排名越前">
							</div>
						</li>
						<li <?=$if_pintuan==0?'style="display:none;"':'' ?>>
							<div class="gaojisousuo_left">
								<span>*</span>团购设置 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="sale_tuan" id="sale_tuan_btn" <? if($product_inventory->sale_tuan==1){?>checked="true"<? }?> lay-skin="primary" title="拼团">&nbsp;&nbsp;&nbsp;&nbsp;成团数量：<input type="number" name="tuan_num" id="tuan_num" lay-verify="required" value="<?=$product_inventory->tuan_num?>" step="1" class="layui-input" style="width:70px;display:inline-block;">
							</div>
						</li>

						<li>
							<div class="gaojisousuo_left">
								计量单位 
							</div>
							<div class="gaojisousuo_right" onmouseenter="tips(this,'一经修改，所有同名商品都会修改',1);" onmouseout="hideTips();" style="width:50%;margin-right:20px;">
								<input type="text" id="duounit" class="layui-input" value="<?=$pdtUnitstr?>" readonly="true" style="<? if($product->unit_type==0){?>display:none;<? }?>cursor:pointer;">
								<select name="unit" id="danunit">
									<option value="">选择单位</option>
									<?=empty($pdtUnits[0]['title'])?$unitOptions:str_replace($pdtUnits[0]['title'].'"',$pdtUnits[0]['title'].'" selected="selected"',$unitOptions)?>
								</select>
							</div>
							<!--<input type="checkbox" name="usetmoreunit" <? if($product->unit_type==1){?>checked="true"<? }?> lay-filter="moreUnit" lay-skin="primary" title="启用多单位" />-->
						</li>
						<? if($product_set->if_brand==1){?>
						<li>
							<div class="gaojisousuo_left">
								研究领域
							</div>
							<div class="gaojisousuo_right">
								<select name="brandId" id="brandId" lay-search>
									<option value="">选择研究领域或输入搜索</option>
									<? if(!empty($brands)){
										foreach ($brands as $brand){
											?><option value="<?=$brand->id?>" <? if($product->brandId==$brand->id){?>selected="selected"<? }?>><?=$brand->title?></option><?
										}
									}?>
								</select>
							</div>
						</li>
						<? }?>
						<? if($product_set->if_addrows==1&&!empty($product_set->addrows)){
							$addrows = explode('@_@',$product_set->addrows);
							foreach ($addrows as $row) {
								?>
								<li>
									<div class="gaojisousuo_left">
										<?=$row?>
									</div>
									<div class="gaojisousuo_right">
										<input type="text" class="layui-input" name="addrows[<?=$row?>]" value="<?=$pdtaddrows[$row]?>" placeholder="请输入<?=$row?>">
									</div>
								</li>
								<?
							}
						}?>
						<? if($product_set->if_tags==1&&!empty($product_set->tags)){
							$addrows = explode('@_@',$product_set->tags);
							?>
							<li style="width:100%">
								<div class="gaojisousuo_left">
									商品标签
								</div>
								<div class="gaojisousuo_right">
									<?
									foreach ($addrows as $i=>$row) {
									?>
										<input type="checkbox" name="tags[<?=$i?>]" title="<?=$row?>" value="<?=$row?>" <? if(in_array($row,$pdtTags)){?>checked="true"<? }?> />
									<?
									}
									?>
								</div>
							</li>
							<?
						}
						?>
						<li style="height:auto;margin-bottom: 10px;display:none;">
							<div class="gaojisousuo_left">
								分享海报 
							</div>
							<div class="gaojisousuo_right">
								<a href="<?=$product->share_img?>" <? if(empty($product->share_img)){?>style="display:none;"<? }?> target="_blank"><img src="<?=$product->share_img?>" id="haibao_img" width="100"></a>
                                <input type="hidden" name="share_img" value="" id="share_img">
                                <button type="button" id="upload1" class="layui-btn">上传</button>
							</div>
						</li>
						<li style="width:100%;height:auto">
							<div class="gaojisousuo_left" style="vertical-align:top;padding-top:5px;">
								商品说明
							</div>
							<div class="gaojisousuo_right">
								<textarea name="remark" class="layui-textarea" placeholder="输入商品的说明信息，该信息显示在产品介绍首页"><?=$product->remark?></textarea>
							</div>
						</li>
						<div class="clearBoth"></div>
					</ul>
				</div>
			</div>
			<div class="clearBoth"></div>
			<div class="edit_guige">
				<div class="jichu_h" style="line-height: 40px;">商品规格</div>
				<div class="table2_tb" id="duoguigeTable" rowNums="<?=count($product_keys)?>" nums="<?=count($product_keys)?>">
					<table width="100%">
						<tr>
							<th width="67px"></th>
							<th width="155px">规格名称</th>
							<th style="text-align:left; padding-left:37px;">规格值<span>（规格值之间请用“逗号”间隔，规格图片需处理成比例1:1，大小不超过2M</span>）</span></th>
						</tr>
						<?
						if(!empty($product_keys)){
							$i = 0;
							foreach ($product_keys as $pdtkey){
								$ziKeys = $db->get_results("select * from demo_product_key where productId=$productId and parentId=$pdtkey->id order by kg");
								$snNums = $ziKeys[count($ziKeys)-1]->kg;
								$i++;
								?>
								<tr id="moreGuigeTr<?=$i?>" data-id="<?=$i?>" snNums="<?=$snNums?>">
									<td class="td1"><a href="javascript:" onclick="delDuoTr(<?=$i?>);"><img src="images/reduce2.png" /></a></td>
									<td class="td1"><input type="text" name="gg[<?=$i?>]" onchange="updateGGName(this);" value="<?=$pdtkey->title?>" placeholder="规格名称" maxlength="10" style="width:116px;" /></td>
									<td class="td2">
										<div class="guigezhi">
											<ul>
												<? if(!empty($ziKeys)){
													foreach ($ziKeys as $zikey) {
														$img = empty($zikey->originalPic)?'images/mrtp.gif':$zikey->originalPic.'?x-oss-process=image/resize,w_350';
														$j=$zikey->kg;
														?>
														<li id="pdtKey_<?=$i?>_<?=$j?>">
															<div class="guigezhi_tt" onmouseenter="tips(this,'点击可修改，修改之后需要点击下方的“重新生成所有规格”',1)" onmouseout="hideTips();" contenteditable="true"><?=$zikey->title?></div>
															<div class="uploadSnImg1" onclick="upload_img(<?=$i?>,<?=$j?>);">
																<img src="<?=$img?>">
															</div>
															<input type="hidden" name="ggseci<?=$i?>[<?=$j?>]" value="<?=$zikey->title?>" id="ggseci_<?=$i?>_<?=$j?>">
															<input type="hidden" name="image<?=$i?>[<?=$j?>]" value="<?=$zikey->originalPic?>" id="image_<?=$i?>_<?=$j?>">
															<div class="close-modal small js-remove-sku-atom" onclick="del_guigezhi(<?=$i?>,<?=$j?>);">×</div>
														</li>
														<?
													}
												}?>
											</ul>
											<div class="ggz_add">
												<a href="javascript:" onclick="addGuige(<?=$i?>);">+ 添加</a>
											</div>
											<div class="clearBoth"></div>
										</div>
									</td>
									<input type="hidden" name="pdtKeyId<?=$i?>" id="pdtKeyId<?=$i?>" value="<?=$pdtkey->id?>">
								</tr>
								<?
							}
						}
						?>
						<tr id="addGuigeTr" <? if(!empty($product_keys)&& count($product_keys)==3){?>style="display:none;"<? }?>>
							<td class="td1" colspan="3" style="text-align:left;padding-left:18px;"><a href="javascript:" onclick="addMoreGuige();"><img src="images/add.png" /> 添加规格</a></td>
						</td>
					</tr>
				</table>
			</div>
			<div class="guige_shuoming">
				<!--说明：如果不需要商品重量，需要去商品设置里进行停用商品重量<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">设置</a>；    如果商品无零售方式，可去价格设置进行停用零售价格<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">设置</a>。-->
			</div>
		</div>
		<div style="margin:20px 0px 0px 20px;"><a href="javascript:" style="color:#FF5722" onclick="guigeTable();">重新生成所有规格</a><span style="color:#999;font-size:12px">（误删除多规格产品中的某个产品时可以重新生成）</span></div>
		<div class="guige_set" id="moreGuige">

		</div>
		<? if($product_set->if_image==1){?>
		<div class="edit_photo">
			<div class="photo_tt">
				商品图片<span style="color:#9b9b9b">(说明：图片需处理成大小比例1:1，不超过2M)</span>
			</div>
			<style>
				.photo_tu{position: relative;}
				.photo_tu ul{position: relative;width: 100%;}
				.photo_tu ul .gallery-item{position: relative;cursor: move;margin-bottom: 10px;}
				.photo_tu ul .gallery-item img{display: block;padding: 5px;width:124px;height:124px;border: 1px #b6cfe2 solid;background-color: #f2fafc;}						
			</style>
			<div class="photo_tu">
				<ul id="gallery-list">
					<?
					if(!empty($originalPics)){
						$i=0;
						foreach ($originalPics as $originalPic){
							$i++;
							?>
							<li class="gallery-item" draggable ="true"  id="image_li<?=$i?>"><img src="<?=$originalPic?>?x-oss-process=image/resize,w_122" width="122" height="122"><div class="close-modal small js-remove-sku-atom" onclick="del_image(<?=$i?>);">×</div></li>
							<?
						}
					}
					?>
					<li id="uploadImages" data-num="<?=count($originalPics)?>" style="position:relative;">
						<img src="images/photo1.jpg" width="136" height="136" />
						<input type="file" name="file" id="uploadPdtImage" multiple="true">
					</li>
					<div class="clearBoth"></div>
				</ul>
			</div>
		</div>
		<? }?>
		<div class="edit_miaoshu">
			<div class="miaoshu_tt">
				商品描述<span style="color:#9b9b9b">(说明：此处修改只会影响没有单独设置过的规格的商品描述)</span>
			</div>
			<div class="miaoshu_fenlei" id="pdtcontMenu">
				<ul>
					<li><a href="javascript:" id="pdtcontMenu1" onclick="qiehuan('pdtcont',1,'on');" class="on">商品描述</a></li>
					<li><a href="javascript:" id="pdtcontMenu2" onclick="qiehuan('pdtcont',2,'on');">规格与包装</a></li>
					<li><a href="javascript:" id="pdtcontMenu3" onclick="qiehuan('pdtcont',3,'on');">售后保障</a></li>
				</ul>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont1">
				<?php
					ewebeditor(EDITORSTYLE,'cont1',$product->cont1);
				?>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont2" style="display:none;">
				<?php
					ewebeditor(EDITORSTYLE,'cont2',$product->cont2);
				?>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont3" style="display:none;">
				<?php
					ewebeditor(EDITORSTYLE,'cont3',$product->cont3);
				?>
			</div>
		</div>
		<div class="edit_jiage">
			<? if($product_set->if_dinghuo==1){?>
			<div class="jiage_tt">
				<div class="jiage_h">
					订货价格设置
				</div>
				<div class="jiage_shuoming">
					说明：价格及起订量/限订量均按最小单位设置，价格及数量的精度可在设置中<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">配置</a>
				</div>
			</div>
			<div class="jiebie_check">
				<input type="checkbox" name="dinghuo_bylevel" lay-skin="primary" checked="true" disabled title="按<?=$kehu_title?>级别定价" />
			</div>
			<div class="add_gsj">
				<div class="add_gsj_left">
					<a>按折扣一键设置订货价</a>
				</div>
				<div class="add_gsj_right">
					<span style="color:red">*</span> 市场价：<input type="number" step="0.01" id="shichangjia" value="<?=$product_inventory->shichangjia?>" name="shichangjia" lay-verify="required" placeholder=""/> <span>订货价=市场价 * 级别折扣 </span>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div id="dinghuo_moresn">
				<div class="jiebie_fenlei" id="jibieMenu">
					<ul>
						<? foreach($levels as $i=>$level){?>
							<li>
								<a id="jibieMenu<?=$level->id?>" href="javascript:" onclick="qiehuan('jibie',<?=$level->id?>,'jiebie_fenlei_on');" <? if($i==0){?>class="jiebie_fenlei_on"<? }?>><?=$level->title?></a>
							</li>
						<? }?>
					</ul>
				</div>
				<div class="clearBoth"></div>
				<? foreach($levels as $i=>$level){?>
				<div id="jibieCont<?=$level->id?>" class="jibieCont" data-id="<?=$level->id?>" data-zhekou="<?=$level->zhekou?>%" <? if($i>0){?>style="display:none;"<? }?>>
					<div class="jiebie2_table"></div>
				</div>
				<? }?>
			</div>
			<div class="jiage_kehu">
				<input class="checkbox" name="dinghuo_bykehu" lay-skin="primary" <? if(!empty($kehuDinghuos)){?>checked="true"<? }?> type="checkbox" lay-filter="dinghuo_bykehu" title="按客户定价" />
				<div class="khjg_table" id="khjg_table_duo" <? if(empty($kehuDinghuos)){?> style="display:none;"<? }?>>
					<div class="dhd_adddinghuodan_1_right">
	                    <div class="dhd_adddinghuodan_1_right_01">
	                        <input type="text" class="layui-input" id="searchKehuInput" placeholder="选择/搜索客户">
	                        <div class="sprukuadd_03_tt_addsp_erji" id="kehuList" style="left:0px;top:33px;margin-top:0px;">
	                            <ul>
	                            	<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
	                        	</ul>
	                      	</div>
	                    </div>
	                    <div class="dhd_adddinghuodan_1_right_02">
	                        <span></span><span></span><span></span>
	                    </div>
	                    <div class="clearBoth"></div>
	                </div>
	                <div class="jiage_kehu_xiang" id="jiage_kehu_xiang">
	                	
	                </div>
				</div>
			</div>
		<? }?>
		<div class="edit_save">
			<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
			<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
		</div>
	</div>
</div>
<input type="hidden" name="originalPic" value="<?=$product->originalPic?>" id="originalPic">
<input type="hidden" name="unit_type" value="<?=$product->unit_type?>" id="unit_type" >
<input type="hidden" name="units" id="units" value="<?=$pdtUnitstr1?>">
<input type="hidden" name="dinghuo_units" id="dinghuo_units" value="<?=$product->dinghuo_units?>">
<input type="hidden" name="productId" id="productId" value="<?=$productId?>">
<input type="hidden" name="pdt_status" id="pdt_status" value="<?=$product->status?>">
</form>
<div id="addSndiv" data-id="0">
	<div class="spxx_shanchu_tanchu" style="display: block;">
		<div class="spxx_shanchu_tanchu_01">
			<div class="spxx_shanchu_tanchu_01_left">添加规格
			</div>
			<div class="spxx_shanchu_tanchu_01_right">
				<a href="javascript:closeAddSn();"><img src="images/biao_47.png"></a>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="spxx_shanchu_tanchu_02" style="padding-left:0px;padding-top:30px;">
			<div class="jiliang_tanchu">
				<input type="text" id="guigesInput" class="xla_k" style="width:450px;">
				<Br><span style="padding-left:17px;padding-top:5px;">多个规格用，分开</span>
			</div>
			<div class="spxx_shanchu_tanchu_03">
				<a href="javascript:" onclick="addSn();" class="spxx_shanchu_tanchu_03_2">确定</a><a href="javascript:" onclick="closeAddSn();" class="spxx_shanchu_tanchu_03_1">取消</a>
			</div>
		</div>
	</div>
</div>
<div class="zhutu" id="zhutu">
	<div class="zhutu_h">
		添加商品规格主图
	</div>
	<div class="zhutu_cont">
		<button type="button" class="layui-btn" id="uploadSnImg">上传图片</button>
		<div style="display:inline-block;top:10px;position: relative;margin-left:10px;">
			仅支持JPG、jpep、bmp、png格式，文件小于2Ｍ，大小比例1:1；图片将自动生成三种<br />尺寸，请注意生成图片是否清晰
		</div>
		<ul>
			<li>
				<img id="zhutu1" src="/inc/img/nopic.svg" width="350" /><br />350*350像素
			</li>
			<li>
				<img id="zhutu2" src="/inc/img/nopic.svg" width="200"/><br />200*200像素
			</li>
			<li>
				<img id="zhutu3" src="/inc/img/nopic.svg" width="60" /><br />60*60像素
			</li>
		</ul>
		<div class="clearBoth"></div>
		<div class="zhutu_cho">
			<a href="javascript:select_zhutu();">确定</a>
		</div>
		<div class="zhutu_cho2">
			<a href="javascript:hide_zhutu();" class="zhtu_cancel">取消</a>
		</div>
		<div class="clearBoth"></div>
	</div>
	<input type="hidden" id="snId1">
	<input type="hidden" id="snId2">
</div>
<div id="bg"></div>
<script type="text/javascript">
	var jishiqi;
	var kehu_title = '<?=$kehu_title?>';
	var dinghuoHtml = '';
	$("#shichangjia").bind('input propertychange', function(){
		var val = parseFloat($(this).val());
		if(!isNaN(val)){
			$(".dinghuo_money").each(function(){
				var zhekou = parseFloat($(this).attr("data-zhekou"))/100;
				var price = parseInt(val*zhekou*100)/100;
				$(this).val(price);
			});
		}
	});
	$('#searchKehuInput').bind('input propertychange', function() {
		clearTimeout(jishiqi);
		var row = $(this).attr('row');
		var val = $(this).val();
		jishiqi=setTimeout(function(){getKehuList(val);},500);
	});
	$('#searchKehuInput').click(function(eve){
		var nowRow = $(this).attr("row");
		if($("#kehuList").css("display")=="none"){
			$("#kehuList").show();
			getKehuList($(this).val());
		}
		stopPropagation(eve);
	});
	$(".dhd_adddinghuodan_1_right_02").click(function(eve){
		var nowRow = $(this).attr("row");
		if($("#kehuList").css("display")=="none"){
			$("#kehuList").show();
			getKehuList($(this).val());
		}
		stopPropagation(eve);
	});
</script>
<script type="text/javascript" src="js/product_editProduct.js?v=2"></script>
<script type="text/javascript" src="js/gallery_drag.js"></script>
	<script>
		galleryDrag('#gallery-list')
	</script>
<? require('views/help.html');?>
</body>
</html>