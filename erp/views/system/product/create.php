<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$addrows = array();
$tags = array();
if(!empty($product_set->addrows)){
	$addrows = explode('@_@',$product_set->addrows);
}
if(!empty($product_set->tags)){
	$tags = explode('@_@',$product_set->tags);
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
$snInt = $db->get_var("select snInt from demo_product_inventory where comId=$comId order by id desc limit 1");
$snInt++;
$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
$productId = !empty($request['productId'])?$request['productId']:0;
$brands = $db->get_results("select id,title from demo_product_brand where comId=$comId order by ordering desc,id asc");
$_SESSION['tijiao'] = 1;
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$levels = $db->get_results("select * from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$chushu = pow(10,$product_set->price_num);
$step = 1/$chushu;
$chushu1 = pow(10,$product_set->number_num);
$step1 = 1/$chushu1;
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
		var step = <?=$step?>;
		var step1 = <?=$step1?>;
	</script>
	<style type="text/css">
		.edit_guige .layui-form-select,#moreGuige .layui-form-select{width:80%;margin:0px auto;}
		.guige_set table tr td .layui-select-title input{width:100%;margin:0px auto;height:32px;}
	</style>
</head>
<body>
	<form action="?m=system&s=product&a=create&tijiao=1" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<div class="content_edit">
			<div class="edit_h">
				<a href="javascript:history.go(-1);"><img src="images/back.jpg" /></a>
				<span>新增商品</span>
			</div>
			
			<div class="edit_jichu" style="display:none;">
				<div class="jichu_h">京东抓取</div>
				<ul>
    				<li>
    					<div class="gaojisousuo_left">
    						链接
    					</div>
    			
    					<div class="gaojisousuo_right">
    						<input type="text" class="layui-input" width="400px;" name="jd_url" id="jd_url" value="" placeholder="请输入京东抓取">
    					</div>
    				</li>
    				<li style="padding-left:80px;padding-top:20px;">
    					<div class="gaojisousuo_left">
    						<button type="button" class="layui-btn layui-btn-primary" style="background-color:lightGreen"  onclick="jd_fetch();return false;">抓取</button>
    					</div>
    				</li>
				</ul>		
			</div>	
			
			
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="shangjia">
					<input type="checkbox" checked name="status" lay-skin="primary" title="立即上架" />
				</div>
				<div class="clearBoth"></div>
				<div class="jichu_message">
					<ul>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品名称 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="title" id="title" value="<? if(!empty($productId)){echo $db->get_var("select title from demo_product where id=$productId");}?>" onblur="checkPdtTitle(<?=$productId?>);" lay-verify="required" placeholder="请输入商品名称">
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>货号 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="skuId" lay-verify="required" placeholder="请输入货号">
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								有效期 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="skuDay" placeholder="请输入有效期">
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品分类
							</div>
							<div class="gaojisousuo_right">
								<div class="layui-form-select">
									<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
									<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
								</div>
								<input type="hidden" name="channelId" id="channelId" lay-verify="required">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								顺序
							</div>
							<div class="gaojisousuo_right">
								<input type="number" class="layui-input" name="ordering" id="ordering" onmouseover="tips(this,'输入0-99999999之间的数字',1);" onmouseout="hideTips();" placeholder="数字越大排名越前">
							</div>
						</li>
						<li <?=$if_pintuan==0?'style="display:none;"':'' ?>>
							<div class="gaojisousuo_left">
								团购设置 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="sale_tuan" id="sale_tuan_btn" lay-skin="primary" title="拼团">&nbsp;&nbsp;&nbsp;&nbsp;成团数量：<input type="number" name="tuan_num" id="tuan_num" lay-verify="required" value="0" step="1" class="layui-input" style="width:70px;display:inline-block;">
							</div>
						</li>
						
						<li style="display:none;">
							<div class="gaojisousuo_left">
								运费模板
							</div>
							<div class="gaojisousuo_right">
								<div style="width:60%;display:inline-block;">
								<select name="yunfei_moban">
									<option value="0" selected="true">选择运费模板</option>
									<?
									$yunfei_mobans = $db->get_results("select id,title,scene from yunfei_moban where comId=$comId");
									if(!empty($yunfei_mobans)){
										foreach ($yunfei_mobans as $moban) {
											if($moban->scene==1){
												?><option value="<?=$moban->id?>" <? if($product->yunfei_moban==$moban->id){?>selected="true"<? }?>><?=$moban->title?></option><?
											}
										}
									}
									?>
								</select>
								</div>
								<div style="width:37%;display:inline-block;margin-left:2%"><a href="?m=system&s=product&a=set_yunfei&tijiao=1" style="color:#35a6dd">设置运费模板</a></div>
							</div>
						</li>
				
						<li>
							<div class="gaojisousuo_left">
								计量单位 
							</div>
							<div class="gaojisousuo_right" style="width:50%;margin-right:20px;">
								<input type="text" id="duounit" class="layui-input" readonly="true" style="display:none;cursor:pointer;">
								<select name="unit" id="danunit">
									<option value="">选择单位</option>
									<?=$unitOptions?>
								</select>
							</div>
							<!--<input type="checkbox" name="usetmoreunit" lay-filter="moreUnit" lay-skin="primary" title="启用多单位" />-->
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
											?><option value="<?=$brand->id?>"><?=$brand->title?></option><?
										}
									}?>
								</select>
							</div>
						</li>
						<? }
						if($product_set->if_addrows==1&&!empty($product_set->addrows)){
							$addrows = explode('@_@',$product_set->addrows);
							foreach ($addrows as $row) {
								?>
								<li>
									<div class="gaojisousuo_left">
										<?=$row?>
									</div>
									<div class="gaojisousuo_right">
										<input type="text" class="layui-input" name="addrows[<?=$row?>]" placeholder="请输入<?=$row?>">
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
										<input type="checkbox" name="tags[<?=$i?>]" value="<?=$row?>" title="<?=$row?>" />
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
                                (用于商品分享缩略图和海报，500k以内)
							</div>
						</li>
						<li style="width:100%;height:auto">
							<div class="gaojisousuo_left" style="vertical-align:top;padding-top:5px;">
								商品说明
							</div>
							<div class="gaojisousuo_right">
								<textarea name="remark" class="layui-textarea" placeholder="输入商品的说明信息，该信息显示在产品介绍首页"><?=!empty($product->remark)?$product->remark:''?></textarea>
							</div>
						</li>
						<div class="clearBoth"></div>
					</ul>
				</div>
			</div>
			<div class="clearBoth"></div>
			<div class="edit_guige">
				<div class="jichu_h">商品规格</div>
				<div class="table1">
					<div class="table1_set">
						<input type="checkbox" id="ifmoresn" name="ifmoresn" lay-filter="ifmoresn" lay-skin="primary" title="设置产品多规格" />
					</div>
					<div class="table1_tb">
						<table width="100%">
							<tr>
								<th width="67px"></th>
								<th width="103px">规格</th>
								<th width="174px">商品编码 </th>
						    	<?
						        if($product_set->if_weight==1){?>
								<th width="183px">重量（<?=$product_set->weight?>）</th>
								<? } ?>
								<th width="213px">零售价（元）</th>
								<th width="167px">市场价（元）</th>
								<th width="167px" <?=$if_pintuan==0?'style="display:none"':''?>>拼团价格</th>
								<th width="145px" <?=$if_fenxiao==0?'style="display:none"':''?>>分销提成(总)</th>
								<th width="356px">条形码</th>
								<th width="112px">库存</th>
							</tr>
							<tr>
								<td>1</td>
								<td>无</td>
								<td><input type="text" name="sn0" mustrow value="<?=$sn?>" style="width:148px;" /></td>
								<?
								    if($product_set->if_weight==1){?>
									<td><input type="number" name="weight0" mustrow step="0.01" min="0" style="width:102px;" /></td>
									<? }
									?>
									<td><input type="number" step="<?=$step?>" mustrow name="price_sale0" min="0" style="width:102px;" /></td>
									<td><input type="number" step="<?=$step?>" mustrow name="price_market0" id="shichangjia0" min="0" style="width:102px;" /></td>
									<td <?=$if_pintuan==0?'style="display:none"':''?>><input type="number" step="<?=$step?>" value="0" min="0" mustrow name="price_tuan0" style="width:102px;" /></td>
									<td <?=$if_fenxiao==0?'style="display:none"':''?>><input type="number" step="<?=$step?>" mustrow name="fanli_tuanzhang0" min="0" value="0" style="width:102px;" /></td>
								
									<td><input type="text" name="code0" style="width:312px;" /></td>
									<td><input type="number" name="kucun0" value="0" style="width:112px;" /></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="table2_tb" style="display:none;" id="duoguigeTable" rowNums="1" nums="1">
					<table width="100%">
						<tr>
							<th width="67px"></th>
							<th width="155px">规格名称</th>
							<th style="text-align:left; padding-left:37px;">规格值<span>（规格值之间请用“逗号”间隔，规格图片需处理成比例1:1，大小不超过2M</span>）</span></th>
						</tr>
						<tr id="moreGuigeTr1" data-id="1" snNums="0">
							<td class="td1"><a href="javascript:" onclick="delDuoTr(1);"><img src="images/reduce2.png" /></a></td>
							<td class="td1"><input type="text" name="gg[1]" onblur="updateGGName(this);" placeholder="规格名称" maxlength="10" style="width:116px;" /></td>
							<td class="td2">
								<div class="guigezhi">
									<ul>
									</ul>
									<div class="ggz_add">
										<a href="javascript:" onclick="addGuige(1);">+ 添加</a>
									</div>
									<div class="clearBoth"></div>
								</div>
							</td>
							<input type="hidden" name="pdtKeyId1" id="pdtKeyId1" value="0">
						</tr>
						<tr id="addGuigeTr">
							<td class="td1" colspan="3" style="text-align:left;padding-left:18px;"><a href="javascript:" onclick="addMoreGuige();"><img src="images/add.png" /> 添加规格</a></td>
						</td>
					</tr>
				</table>
			</div>
			<div class="guige_shuoming">
				<!--说明：如果不需要商品重量，需要去商品设置里进行停用商品重量<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">设置</a>；    如果商品无零售方式，可去价格设置进行停用零售价格<a href="index.php?url=<?=urlencode('?m=system&s=product_set')?>" target="_blank">设置</a>。-->
			</div>
		</div>
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
					<li id="uploadImages" data-num="0" style="position:relative;">
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
				商品描述
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
					ewebeditor(EDITORSTYLE,'cont1','');
				?>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont2" style="display:none;">
				<?php
					ewebeditor(EDITORSTYLE,'cont2','');
				?>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont3" style="display:none;">
				<?php
					ewebeditor(EDITORSTYLE,'cont3','');
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
					<span style="color:red">*</span> 市场价：<input type="number" step="0.01" id="shichangjia" name="shichangjia" lay-verify="required" placeholder=""/> <span>订货价=市场价 * 级别折扣 </span>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div id="dinghuo_dansn">
				<div class="jiebie_table">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th width="127">客户级别</th>
						<th width="127">默认折扣</th>
						<th width="127">允许订货</th>
						<th width="178">订货价</th>
						<th width="178" <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>>起订量</th>
						<th width="178" <? if(empty($product_set->if_dinghuo_max)){?>style="display:none"<? }?>>限订量</th>
					</tr>
					<? foreach($levels as $i=>$level){
						?>
						<tr height="48">
							<td><?=$level->title?></td>
							<td><?=$level->zhekou?>%</td>
							<td width="127">
								<input name="d_ifsale_0[<?=$level->id?>]" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/>
							</td>
							<td><input type="number" step="<?=$step?>" mustrow class="dinghuo_money" data-zhekou="<?=$level->zhekou?>" name="d_price_sale0[<?=$level->id?>]" min="0" style="width:102px;" /></td>
							<td <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="0" name="dinghuo_min0[<?=$level->id?>]" min="0" style="width:102px;" class="piliang_dansn_min" onchange="<? if($i==0){?>piliang_set('dansn_min',this.value);<? }?>checkDinghuoNum(this,1);" /></td>
							<td <? if(empty($product_set->if_dinghuo_min)){?>style="display:none"<? }?>><input type="number" step="<?=$step1?>" value="0" name="dinghuo_max0[<?=$level->id?>]" class="piliang_dansn_max" onmouseover="tips(this,'0或空代表不限制',1)" onmouseout="hideTips();" min="0" style="width:102px;" onchange="<? if($i==0){?>piliang_set('dansn_max',this.value);<? }?>checkDinghuoNum(this,2);"/></td>
						</tr>
						<?
					}?>
				</table>
			</div>
			</div>
			<div id="dinghuo_moresn" style="display:none;">
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
				<div id="jibieCont<?=$level->id?>" class="jibieCont" data-id="<?=$level->id?>" data-zhekou="<?=$level->zhekou?>" <? if($i>0){?>style="display:none;"<? }?>>
					<div class="jiebie2_table"></div>
				</div>
				<? }?>
			</div>
			<div class="jiage_kehu">
				<input class="checkbox" name="dinghuo_bykehu" lay-skin="primary" type="checkbox" lay-filter="dinghuo_bykehu" title="按客户定价" />
				<div class="khjg_table" id="khjg_table_dan" style="display:none;">
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
						<tr id="rowTr1">
							<td>
								<div style="width:95px;">
									<div class="kehu_set1">
										<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>
									</div>
									<div class="kehu_set2">
										<a href="javascript:" onclick="delRow(1);"><img src="images/reduce.png"></a>
									</div>
								</div>
							</td>
							<td colspan="6">
								<div class="sprukuadd_03_tt_addsp">
	                            	<div class="sprukuadd_03_tt_addsp_left">
	                                	<input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入<?=$kehu_title?>名称/编码/联系人/手机" >
	                                </div>
	                            	<div class="sprukuadd_03_tt_addsp_right" onclick="showKehus(event,1);">
	                                	●●●
	                                </div>
	                            	<div class="clearBoth"></div>
	                                <div class="sprukuadd_03_tt_addsp_erji" id="pdtList1">
	                                	<ul>
	                                		<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
	                                	</ul>
	                                </div>
	                            </div>
							</td>
						</tr>
					</tbody></table>
				</div>
				<div class="khjg_table" id="khjg_table_duo" style="display:none;">
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
<input type="hidden" id="firstLevel" value="<?=$levels[0]->id?>">
<input type="hidden" name="originalPic" id="originalPic">
<input type="hidden" name="unit_type" id="unit_type" value="0">
<input type="hidden" name="units" id="units" value="">
<input type="hidden" name="dinghuo_units" id="dinghuo_units" value="">
<input type="hidden" name="productId" id="productId" value="<?=$productId?>">
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
<script type="text/javascript">
	var jishiqi;
	var kehu_title = '<?=$kehu_title?>';
	var dinghuoHtml = '';
	$("#shichangjia0").bind('input propertychange', function(){
		var val = parseFloat($(this).val());
		if(!isNaN(val)){
			$("#shichangjia").val(val);
			$(".dinghuo_money").each(function(){
				var zhekou = parseFloat($(this).attr("data-zhekou"))/100;
				var price = parseInt(val*zhekou*100)/100;
				$(this).val(price);
			});
		}
	});
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
	$('#searchInput1').bind('input propertychange', function() {
		clearTimeout(jishiqi);
		var row = $(this).attr('row');
		var val = $(this).val();
		jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
	});
	$('#searchInput1').click(function(eve){
		var nowRow = $(this).attr("row");
		if($("#pdtList"+nowRow).css("display")=="none"){
			$("#pdtList"+nowRow).show();
			getPdtInfo(nowRow,$(this).val());
		}
		stopPropagation(eve);
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
	
	function jd_fetch()
	{
    	layer.confirm('确定要通过京东抓取吗？', {
    	  btn: ['确定','取消'],
    	},function(){
    		layer.closeAll();
    		layer.load();
    		var url = $("#jd_url").val();
    		
    		$.ajax({
    			type: "POST",
    			url: "?m=system&s=product&a=collectJd",
    			data: "url="+url,
    			dataType:'json',timeout : 5000,
    			success: function(resdata){
    			    layer.closeAll('loading');
    				if(resdata.code==0){
    					layer.msg(resdata.msg,{icon:5});
    				}else{
    				    // alert(11111);
    				    var title = resdata.data.name;
    				    var price_market = resdata.data.marketPrice;
    				    var price_sale = resdata.data.skuPrice;
    				    var skuId = resdata.data.skuId;
    				    var images = resdata.data.images;
    				    
    				    $("#title").val(title);
    				    $("input[name=sn0]").val(skuId);
    				    $("input[name=price_sale0]").val(price_sale);
    				    $("input[name=price_market0]").val(price_market);
    				    images.forEach(function(key, val){
    				        var res = key;
    				        var nums = parseInt($('#uploadImages').attr("data-num"))+1;
                	      	$('#uploadImages').before('<li  class="gallery-item" draggable ="true" id="image_li'+nums+'"><img src="'+res+'?x-oss-process=image/resize,w_122" width="122" height="122"><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
                	      	var originalPic = $("#originalPic").val();
                	      	if(originalPic==''){
                	      		originalPic = res;
                	      	}else{
                	      		originalPic = originalPic+'|'+res;
                	      	}
                	      	$("#originalPic").val(originalPic);
                	      	$('#uploadImages').attr("data-num",nums);
    				    });
    				    
    				    return false;
    				}
    			}
    		});
    	});
    	
    	return false;
	}
</script>
<script type="text/javascript" src="js/product_create.js"></script>
<script type="text/javascript" src="js/gallery_drag.js"></script>
<script>
	galleryDrag('#gallery-list')
</script>
<? require('views/help.html');?>
</body>
</html>