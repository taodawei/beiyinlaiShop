<?
global $db,$request;




$id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_inventory = $db->get_row("select * from demo_product_inventory where id=$id and comId=$comId");
if(empty($product_inventory)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
$productId = $product_inventory->productId;
$product = $db->get_row("select * from demo_product where id=$productId");
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
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
if(!empty($product->originalPic) && empty($product_inventory->key_ids)){
	$originalPics = explode('|',$product->originalPic);
}else if(!empty($product_inventory->key_ids) && !empty($product_inventory->originalPic)){
	$originalPics = explode('|',$product_inventory->originalPic);
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
$kehuDinghuos = $db->get_results("select id,kehuId,ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where inventoryId=$id and type=1 and comId=$comId order by id asc");
$chushu = pow(10,$product_set->price_num);
$step = 1/$chushu;
$chushu = pow(10,$product_set->number_num);
$step1 = 1/$chushu;
$lipinka_str = '';
if($comId==1009){
	$lipinkas = $db->get_results("select id,title from lipinka_jilu where status=1 and (endTime>'".date("Y-m-d")."' or endTime is NULL)");
	if(!empty($lipinkas)){
		foreach ($lipinkas as $lipinka) {
			$lipinka_str.='<option value="'.$lipinka->id.'" '.($product_inventory->lipinkaId==$lipinka->id?'selected="selected"':'').'>'.$lipinka->title.'</option>';
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
	</script>
	<style type="text/css">
		.edit_guige .layui-form-select,#moreGuige .layui-form-select{width:80%;margin:0px auto;}
		.guige_set table tr td .layui-select-title input{width:100%;margin:0px auto;height:32px;}
	</style>
</head>
<body>
	<form action="?m=system&s=product&a=edit&tijiao=1&id=<?=$id?>&productId=<?=$productId?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="<?=urldecode($request['url'])?>"><img src="images/back.jpg" /></a>
				<span>修改商品</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息<? if(!empty($product_inventory->key_ids)){?><span style="color:#f00">(基础信息的修改会同步到该产品所有的规格)</span><? }?></div>
				<div class="shangjia">
					<input type="checkbox" name="status" <? if($product->status==1){?>checked="true"<? }?> lay-skin="primary" title="立即上架" />
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
							<div class="gaojisousuo_right">
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
							<div class="gaojisousuo_right" style="width:50%;margin-right:20px;" onmouseenter="tips(this,'一经修改，所有同名商品都会修改',1);" onmouseout="hideTips();">
								<input type="text" id="duounit" class="layui-input" value="<?=$pdtUnitstr?>" readonly="true" style="<? if($product->unit_type==0){?>display:none;<? }?>cursor:pointer;">
								<select name="unit" id="danunit">
									<option value="">选择单位</option>
									<?=empty($pdtUnits[0]['title'])?$unitOptions:str_replace($pdtUnits[0]['title'].'"',$pdtUnits[0]['title'].'" selected="selected"',$unitOptions)?>
								</select>
							</div>
							<!--<input type="checkbox" name="usetmoreunit" <? if($product->unit_type==1){?>checked="true"<? }?> lay-filter="moreUnit" lay-skin="primary" title="启用多单位" />-->
						</li>
						<? 
						if($product_set->if_brand==1){?>
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
			<div class="edit_guige">
				<div class="jichu_h" style="line-height:40px;">商品规格<? if(!empty($product_inventory->key_ids)){?>
					<a href="?m=system&s=product&a=editProduct&id=<?=$productId?>&url=<?=$url?>" style="color:#d89623;margin-left:20px;">设置所有规格商品&gt;&gt;</a>
				<? }?></div>
				<div class="table1">
					<? if(empty($product_inventory->key_ids)){?>
					<div class="table1_set">
						<input type="checkbox" name="ifmoresn" lay-filter="ifmoresn" lay-skin="primary" title="设置产品多规格" />
					</div>
					<? }?>
					<div class="table1_tb">
						<table width="100%">
							<tr>
								<th width="103px">规格</th>
								<th width="174px">商品编码 </th>
								<? if($product_set->if_weight==1){?>
								<th width="183px">重量（<?=$product_set->weight?>）</th>
								<? } ?>
								<th width="213px">零售价（元）</th>
								<th width="167px">市场价（元）</th>
								<th width="167px" <?=$if_pintuan==0?'style="display:none;"':''?>>拼团价格</th>
								<th width="145px" <?=$if_fenxiao==0?'style="display:none;"':''?>>分销提成(总)</th>
								<th width="356px">条形码</th>
							</tr>
							<tr>
								<td><?=$product_inventory->key_vals?></td>
								<td><input type="text" name="sn0" value="<?=$product_inventory->sn?>" mustrow min="0" style="width:148px;" /></td>
								    <? if($product_set->if_weight==1){?>
									<td><input type="number" name="weight0" value="<?=$product_inventory->weight?>" mustrow min="0" step="0.01" style="width:102px;" /></td>
									<? } ?>
									<td><input type="number" step="<?=$step?>" value="<?=$product_inventory->price_sale?>" min="0" mustrow name="price_sale0" style="width:102px;" /></td>
									<td><input type="number" step="<?=$step?>" value="<?=$product_inventory->price_market?>" min="0" mustrow name="price_market0" style="width:102px;" /></td>
									<td <?=$if_pintuan==0?'style="display:none;"':''?>><input type="number" step="<?=$step?>" value="<?=$product_inventory->price_tuan?>" min="0" mustrow name="price_tuan0" style="width:102px;" /></td>
									<td <?=$if_fenxiao==0?'style="display:none;"':''?>><input type="number" step="<?=$step?>" mustrow name="fanli_tuanzhang0" value="<?=$product_inventory->fanli_tuanzhang?>" min="0" style="width:102px;" /></td>
									<td><input type="text" name="code0" value="<?=$product_inventory->code?>" style="width:312px;" /></td>
								</tr>
							</table>
						</div>
					</div>
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
						商品描述<span style="color:#9b9b9b">(说明：此处修改只会影响该规格的商品描述，不会影响同产品下其他的规格)</span>
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
						ewebeditor(EDITORSTYLE,'cont1',empty($product_inventory->cont1)?$product->cont1:$product_inventory->cont1);
						?>
					</div>
					<div class="miaoshu_edit pdtcontCont" id="pdtcontCont2" style="display:none;">
						<?php
						ewebeditor(EDITORSTYLE,'cont2',empty($product_inventory->cont2)?$product->cont2:$product_inventory->cont2);
						?>
					</div>
					<div class="miaoshu_edit pdtcontCont" id="pdtcontCont3" style="display:none;">
						<?php
						ewebeditor(EDITORSTYLE,'cont3',empty($product_inventory->cont3)?$product->cont3:$product_inventory->cont3);
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
							<span style="color:red">*</span> 市场价：<input type="number" step="0.01" id="shichangjia" name="shichangjia" lay-verify="required" value="<?=$product_inventory->shichangjia?>" placeholder=""/> <span>订货价=市场价 * 级别折扣 </span>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div id="dinghuo_dansn">
						<div class="jiebie_table">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<th width="394">客户级别</th>
									<th width="127">默认折扣</th>
									<th width="127">允许订货</th>
									<th width="178">订货价</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>起订量</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>限订量</th>
								</tr>
								<? foreach($levels as $level){
									$dinghuo = $db->get_row("select * from demo_product_dinghuo where inventoryId=$id and levelId=$level->id limit 1");
									$dinghuo->price_sale = getXiaoshu($dinghuo->price_sale,$product_set->price_num);
									$dinghuo->dinghuo_min = getXiaoshu($dinghuo->dinghuo_min,$product_set->number_num);
									$dinghuo->dinghuo_max = getXiaoshu($dinghuo->dinghuo_max,$product_set->number_num);
									?>
									<tr height="48">
										<td><?=$level->title?></td>
										<td><?=$level->zhekou?>%</td>
										<td width="127">
											<input name="d_ifsale_0[<?=$level->id?>]" class="checkbox" type="checkbox" lay-skin="primary" <? if($dinghuo->ifsale==1){?>checked="true"<? }?> title="" lay-filter="ifsale"/>
										</td>
										<td><input type="number" step="<?=$step?>" mustrow value="<?=$dinghuo->price_sale?>" name="d_price_sale0[<?=$level->id?>]" data-zhekou="<?=$level->zhekou?>" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled dinghuo_money"<? }else{?>class="dinghuo_money"<? }?>/></td>
										<td <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="<?=$dinghuo->dinghuo_min?>" name="dinghuo_min0[<?=$level->id?>]" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,1);"/></td>
										<td <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="<?=$dinghuo->dinghuo_max?>" name="dinghuo_max0[<?=$level->id?>]" onmouseover="tips(this,'0或空代表不限制',1)" onmouseout="hideTips();" min="0" style="width:102px;" <? if($dinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,2);"/></td>
									</tr>
									<?
								}?>
							</table>
						</div>
					</div>
					<div class="jiage_kehu">
						<input class="checkbox" name="dinghuo_bykehu" lay-skin="primary" type="checkbox" lay-filter="dinghuo_bykehu" title="按客户定价" <? if(!empty($kehuDinghuos)){?>checked="true"<? }?> />
						<div class="khjg_table" id="khjg_table_dan" <? if(empty($kehuDinghuos)){?>style="display:none;"<? }?>>
							<table width="100%" id="dataTable" rows="1">
								<tbody><tr>
									<th width="102"></th>
									<th width="285">客户名称</th>
									<th width="128">客户级别</th>
									<th width="129">允许订货</th>
									<th width="178">订货价</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>起订量</th>
									<th width="178" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>限订量</th>
								</tr>
								<? 
								$row=1;
								if(!empty($kehuDinghuos)){
									foreach ($kehuDinghuos as $kehuDinghuo) {
										$kehuDinghuo->price_sale = getXiaoshu($kehuDinghuo->price_sale,$product_set->price_num);
										$kehuDinghuo->dinghuo_min = getXiaoshu($kehuDinghuo->dinghuo_min,$product_set->number_num);
										$kehuDinghuo->dinghuo_max = getXiaoshu($kehuDinghuo->dinghuo_max,$product_set->number_num);
										$k = $db->get_row("select title,level from demo_kehu where id=$kehuDinghuo->kehuId and comId=$comId limit 1");
										if(!empty($k)){
											$level = $db->get_var("select title from demo_kehu_level where id=".$k->level);
											?>
											<tr id="rowTr<?=$row?>">
												<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle">
													<div style="width:95px;">
														<div class="kehu_set1">
															<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>
														</div>
														<div class="kehu_set2">
															<a href="javascript:" onclick="delRow(<?=$row?>);"><img src="images/reduce.png"></a>
														</div>
													</div>
												</td>
												<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle"><?=$k->title?></td>
												<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle"><?=$level?></td>
												<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">
													<input name="k_ifsale_0[<?=$row?>]" class="checkbox" type="checkbox" lay-skin="primary" <? if($kehuDinghuo->ifsale==1){?>checked="true"<? }?> title="" lay-filter="ifsale">
												</td>
												<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">
													<input type="number" step="<?=$step?>" mustrow name="k_price_sale0[<?=$row?>]" min="0" style="width:102px;" value="<?=$kehuDinghuo->price_sale?>" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?>>
													<input type="hidden" name="kehuId[<?=$row?>]" value="<?=$kehuDinghuo->kehuId?>">
													<input type="hidden" name="dinghuoId[<?=$row?>]" value="<?=$kehuDinghuo->id?>">
												</td>
												<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>
													<input type="number" step="<?=$step1?>" value="<?=$kehuDinghuo->dinghuo_min?>" name="k_dinghuo_min0[<?=$row?>]" min="0" style="width:102px;" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,1);">
												</td>
												<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>
													<input type="number" step="<?=$step1?>" value="<?=$kehuDinghuo->dinghuo_max?>" name="k_dinghuo_max0[<?=$row?>]" onmouseover="tips(this,'0或空代表不限制',1)" onmouseout="hideTips();" min="0" style="width:102px;" <? if($kehuDinghuo->ifsale==0){?>readonly="true" class="disabled"<? }?> onchange="checkDinghuoNum(this,2);">
												</td>
											</tr>
										<?
										}
										$row++;
									}
								}?>
								<tr id="rowTr<?=$row?>">
									<td>
										<div style="width:95px;">
											<div class="kehu_set1">
												<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>
											</div>
											<div class="kehu_set2">
												<a href="javascript:" onclick="delRow(<?=$row?>);"><img src="images/reduce.png"></a>
											</div>
										</div>
									</td>
									<td colspan="6">
										<div class="sprukuadd_03_tt_addsp">
											<div class="sprukuadd_03_tt_addsp_left">
												<input type="text" class="layui-input addRowtr" id="searchInput<?=$row?>" row="<?=$row?>" placeholder="输入<?=$kehu_title?>名称/编码/联系人/手机" >
											</div>
											<div class="sprukuadd_03_tt_addsp_right" onclick="showKehus(event,<?=$row?>);">
												●●●
											</div>
											<div class="clearBoth"></div>
											<div class="sprukuadd_03_tt_addsp_erji" id="pdtList<?=$row?>">
												<ul>
													<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
							</tbody></table>
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
	<div id="bg"></div>
	<script type="text/javascript">
		var jishiqi;
		var kehu_title = '<?=$kehu_title?>';
		var dinghuoHtml = '';
		var productId = <?=$productId?>;
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
		$('#searchInput<?=$row?>').bind('input propertychange', function() {
			clearTimeout(jishiqi);
			var row = $(this).attr('row');
			var val = $(this).val();
			jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
		});
		$('#searchInput<?=$row?>').click(function(eve){
			var nowRow = $(this).attr("row");
			if($("#pdtList"+nowRow).css("display")=="none"){
				$("#pdtList"+nowRow).show();
				getPdtInfo(nowRow,$(this).val());
			}
			stopPropagation(eve);
		});
	</script>
	<script type="text/javascript" src="js/product_edit.js"></script>
	<script type="text/javascript" src="js/gallery_drag.js"></script>
	<script>
		galleryDrag('#gallery-list')
	</script>
	<? require('views/help.html');?>
</body>
</html>