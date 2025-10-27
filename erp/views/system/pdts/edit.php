<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_inventory = $db->get_row("select * from demo_pdt_inventory where id=$id");
if(empty($product_inventory)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
$productId = $product_inventory->productId;
$product = $db->get_row("select * from demo_pdt where id=$productId");
if(!empty($product->originalPic)){
	$originalPics = explode('|',$product->originalPic);
}
$url = urlencode($request['url']);
if(!empty($product_inventory->key_ids)){
	redirect('?m=system&s=pdts&a=editProduct&id='.$productId.'&url='.$url);
}
$step = 0.01;
$step1 = 0.01;
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
	</script>
</head>
<body>
	<form action="?m=system&s=pdts&a=edit&tijiao=1&id=<?=$id?>&productId=<?=$productId?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="javascript:history.go(-1);"><img src="images/back.jpg" /></a>
				<span>修改商品</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="shangjia">
					<input type="checkbox" name="status" <? if($product_inventory->status==1){?>checked="true"<? }?> lay-skin="primary" title="立即上架" value="1" />
				</div>
				<div class="clearBoth"></div>
				<div class="jichu_message">
					<ul>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品名称 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="title" id="title" value="<?=$product->title?>" onblur="checkPdtTitle(<?=$productId?>);" lay-verify="required" placeholder="请输入商品名称">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								搜索关键字 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" onmouseout="hideTips();" name="keywords" value="<?=$product->keywords?>" placeholder="多个以逗号，分开">
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
			<div class="edit_guige">
				<div class="jichu_h" style="line-height:40px;">商品规格&nbsp;&nbsp;&nbsp;</div>
				<div class="table1">
					<div class="table1_tb">
						<table width="100%">
							<tr>
								<th width="174px">商品编码 </th>
								<th width="167px">售价（元）</th>
								<th width="167px">门市价（元）</th>
								<th width="167px">成本价(供货价)</th>							
								<th width="167px">会员返利</th>
								<th width="167px">使用次数</th>
								<th width="167px">库存</th>
							</tr>
							<tr>
								<td><input type="text" name="sn0" mustrow value="<?=$product_inventory->sn?>" style="width:148px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_sale0" onchange="checkPrice('<?=$product_inventory->price_sale?>',this.value);" value="<?=$product_inventory->price_sale?>" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_market0" value="<?=$product_inventory->price_market?>" id="shichangjia0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_cost0" onchange="checkPrice('<?=$product_inventory->price_cost?>',this.value);" value="<?=$product_inventory->price_cost?>" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="fanli_tuanzhang0" value="<?=$product_inventory->fanli_tuanzhang?>" min="0" style="width:102px;"/></td>
								<td><input type="number" step="1" mustrow name="hexiaos0" value="<?=$product_inventory->hexiaos?>" min="0" style="width:102px;"/></td>
								<td><input type="number" step="1" mustrow name="kucun0" value="<?=$product_inventory->kucun?>" min="0" style="width:102px;"/></td>
							</tr>
						</table>
						</div>
					</div>
				</div>
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
				<div class="edit_miaoshu">
					<div class="miaoshu_tt">
						商品描述
					</div>
					<div class="miaoshu_fenlei" id="pdtcontMenu">
						<ul>
							<li><a href="javascript:" id="pdtcontMenu1" onclick="qiehuan('pdtcont',1,'on');" class="on">商品详情</a></li>
							<!-- <li><a href="javascript:" id="pdtcontMenu2" onclick="qiehuan('pdtcont',2,'on');">规格参数</a></li>
							<li><a href="javascript:" id="pdtcontMenu3" onclick="qiehuan('pdtcont',3,'on');">售后保障</a></li> -->
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
					<div class="edit_save">
						<? if($product->status==-2){?>
							<a style="color:red;font-size:16px;">商品已驳回，需商家重新提交</a>
						<?}else{?>
							<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
							<? if($product->status==0){?>
								<button class="layui-btn layui-btn-primary" onclick="bohui(<?=$productId?>);return false;">驳回</button>
							<?}?>
								<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
						<? }?>
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
	<div id="bg"></div>
	<script type="text/javascript">
		var jishiqi;
		var kehu_title = '<?=$kehu_title?>';
		var dinghuoHtml = '';
		var productId = <?=$productId?>;
		var channelId = <?=$product->channelId?>;
		var fahuoTime = '<?=$product->fahuoTime?>';
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
	<script type="text/javascript" src="js/pdts/product_edit.js"></script>
</body>
</html>