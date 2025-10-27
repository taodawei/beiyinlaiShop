<?
global $db,$request;
$productId = $id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product = $db->get_row("select * from demo_pdt where id=$productId");
if(empty($product)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
$product_inventory = $db->get_row("select * from demo_pdt_inventory where productId=$productId limit 1");
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_pdt_set where comId=$comId");
}
$product_keys = $db->get_results("select * from demo_pdt_key where productId=$productId and parentId=0 order by id");
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

$unitOptions = '';
if(!empty($units)){
	foreach ($units as $u) {
		$unitOptions.='<option value="'.$u->title.'">'.$u->title.'</option>';
	}
}
//$brands = $db->get_results("select id,title from demo_pdt_brand where comId=$comId order by ordering desc,id asc");
$url = urlencode($request['url']);
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$levels = $db->get_results("select * from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$chushu = pow(10,$product_set->price_num);
$step = 1/$chushu;
$chushu1 = pow(10,$product_set->number_num);
$step1 = 1/$chushu1;
$areaId = $product_inventory->sale_area;
$firstId=0;
$secondId=0;
$thirdId=0;
if($areaId>0){
    $area = $db->get_row("select * from demo_area where id=".$areaId);
    if($area->parentId==0){
        $firstId = $area->id;
    }else{
        $firstId = $area->parentId;
        $secondId = $area->id;
        $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
        if($farea->parentId!=0){
            $firstId = $farea->parentId;
            $secondId = $farea->id;
            $thirdId=$area->id;
        }
    }
}
$areas = $db->get_results("select * from demo_area where parentId=0");
//$kehuDinghuos = $db->get_results("select id,inventoryId,type,levelId,kehuId,ifsale,price_sale,dinghuo_min,dinghuo_max from demo_pdt_dinghuo where productId=$productId and comId=$comId order by id asc");
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
	<script type="text/javascript">
		var $unitOptions = '<?=$unitOptions?>';
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
</head>
<body>
	<form action="?m=system&s=pdts&a=editProduct&tijiao=1&id=<?=$product->id?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="<?=urldecode($request['url'])?>"><img src="images/back.jpg" /></a>
				<span>修改商品</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="shangjia">
					<input type="checkbox" name="status" lay-skin="primary" <? if($product_inventory->status==1){?>checked="true"<? }?> title="立即上架" />
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
								<div class="layui-form-select">
									<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="<?=$db->get_var("select title from demo_pdt_channel where id=".$product_inventory->channelId);?>" class="layui-input"><i class="layui-edge"></i></div>
									<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
								</div>
								<input type="hidden" name="channelId" id="channelId" value="<?=$product_inventory->channelId?>" lay-verify="required">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>销售区域
							</div>
							<div class="gaojisousuo_right">
								<div style="width:32%;display:inline-block;">
                                    <input type="hidden" name="sale_area" id="psarea" value="<?=$areaId?>" />
                                    <input type="hidden" name="shiId" id="shiId" value="<?=$secondId?>">
                                    <select id="ps1" lay-filter="ps1" lay-verify="required">
                                        <option value="">选择省份</option>
                                        <?if(!empty($areas)){
                                            foreach ($areas as $hangye) {
                                                ?><option value="<?=$hangye->id?>" <?=($hangye->id==$firstId?'selected="selected"':'')?>><?=$hangye->title?></option><?
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps2" lay-filter="ps2" lay-verify="required"><option value="">请先选择省</option>
                                        <?
                                        if($firstId>0){
                                            $areas1 = $db->get_results("select id,title from demo_area where parentId=$firstId");
                                            if(!empty($areas1)){
                                                foreach ($areas1 as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$secondId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>
                                        <? if($secondId>0){
                                            $areas2 = $db->get_results("select id,title from demo_area where parentId=$secondId");
                                            if(!empty($areas2)){
                                                foreach ($areas2 as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$thirdId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								销售结束时间 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" id="endTime" style="width:200px;" name="endTime" lay-verify="required" value="<?=$product_inventory->endTime?>" readonly="true" class="layui-input" >
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								使用期限 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" id="youxiaoqi_start" value="<?=$product->youxiaoqi_start?>" style="width:200px;display:inline-block;" name="youxiaoqi_start" readonly="true" lay-verify="required" class="layui-input" > - <input type="text" id="youxiaoqi_end" style="width:200px;display:inline-block;" value="<?=$product->youxiaoqi_end?>" name="youxiaoqi_end" readonly="true" lay-verify="required" class="layui-input" >
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								是否需要客户填写资料 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="if_user_info" <? if($product->if_user_info==1){?>checked="true"<? }?> title="需要" value="1" />
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								是否需要快递/送货 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="if_kuaidi" <? if($product_inventory->if_kuaidi==1){?>checked="true"<? }?> title="需要" value="1" />
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
						<li style="height:auto;">
							<div class="gaojisousuo_left">
								分享海报 
							</div>
							<div class="gaojisousuo_right">
								<a href="<?=$product->share_img?>" <? if(empty($product->share_img)){?>style="display:none;"<? }?> target="_blank"><img src="<?=$product->share_img?>" id="haibao_img" width="100"></a>
                                <input type="hidden" name="share_img" value="<?=$product->share_img?>" id="share_img">
                                <button type="button" id="upload1" class="layui-btn">上传</button>
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
								$ziKeys = $db->get_results("select * from demo_pdt_key where productId=$productId and parentId=$pdtkey->id order by kg");
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
															<div class="guigezhi_tt"><?=$zikey->title?></div>
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
						<tr id="addGuigeTr" <? if(count($product_keys)==3){?>style="display:none;"<? }?>>
							<td class="td1" colspan="3" style="text-align:left;padding-left:18px;"><a href="javascript:" onclick="addMoreGuige();"><img src="images/add.png" /> 添加规格</a></td>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin:20px 0px 0px 20px;display:none"><a href="javascript:" style="color:#FF5722" onclick="guigeTable();">重新生成所有规格</a><span style="color:#999;font-size:12px">（误删除多规格产品中的某个产品时可以重新生成）</span></div>
		<div class="guige_set" id="moreGuige">

		</div>
		<? if($product_set->if_image==1){?>
		<div class="edit_photo">
			<div class="photo_tt">
				商品图片<span style="color:#9b9b9b">(说明：图片需处理成大小比例4:3，不超过2M)</span>
			</div>
			<div class="photo_tu">
				<ul>
					<?
					if(!empty($originalPics)){
						$i=0;
						foreach ($originalPics as $originalPic){
							$i++;
							?>
							<li id="image_li<?=$i?>"><a><img src="<?=$originalPic?>?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image(<?=$i?>);">×</div></li>
							<?
						}
					}
					?>
					<li id="uploadImages" data-num="<?=count($originalPics)?>" style="position:relative;">
						<img src="images/photo1.jpg" width="136" height="136" />
						<input type="file" name="file" id="uploadPdtImage">
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
				</ul>
			</div>
			<div class="miaoshu_edit pdtcontCont" id="pdtcontCont1">
				<?php
					ewebeditor(EDITORSTYLE,'cont1',$product->cont1);
				?>
			</div>
		</div>			
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
<input type="hidden" name="zong_status" id="pdt_status" value="<?=$product_inventory->zong_status?>">
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
<script type="text/javascript" src="js/pdts/product_editProduct.js"></script>
<? require('views/help.html');?>
</body>
</html>