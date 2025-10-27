<?
session_start();
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$inventory = $db->get_row("select snInt,if_kuaidi from demo_pdt_inventory where comId=$comId order by id desc limit 1");
$snInt = (int)$inventory->snInt;
$if_kuaidi = (int)$inventory->if_kuaidi;
$if_user_info = (int)$db->get_var("select if_user_info from demo_pdt where comId=$comId order by id desc limit 1");
$sn = 'P'.date("Ymd").rand(1000,9999).($snInt+1);
$step = 0.01;
$step1 = 0.01;
$_SESSION['tijiao']=1;
$areaId = (int)$db->get_var("select sale_area from demo_shezhi where comId=$comId");
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
		var step = <?=$step?>;
		var step1 = <?=$step1?>;
	</script>
</head>
<body>
	<form action="?m=system&s=pdts&a=create&tijiao=1" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<div class="content_edit">
			<div class="edit_h">
				<a href="javascript:history.go(-1);"><img src="images/back.jpg" /></a>
				<span>新增商品</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="shangjia">
					<input type="checkbox" name="status" lay-skin="primary" title="立即上架" />
				</div>
				<div class="clearBoth"></div>
				<div class="jichu_message">
					<ul>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>商品名称 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="title" id="title" value="<? if(!empty($productId)){echo $db->get_var("select title from demo_pdt where id=$productId");}?>" onblur="checkPdtTitle(<?=$productId?>);" lay-verify="required" placeholder="请输入商品名称">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								搜索关键字 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="keywords" placeholder="多个以逗号，分开">
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
								<input type="text" id="endTime" style="width:200px;" name="endTime" lay-verify="required" readonly="true" class="layui-input" >
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								使用期限 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" id="youxiaoqi_start" style="width:200px;display:inline-block;" name="youxiaoqi_start" readonly="true" lay-verify="required" class="layui-input" > - <input type="text" id="youxiaoqi_end" style="width:200px;display:inline-block;" name="youxiaoqi_end" readonly="true" lay-verify="required" class="layui-input" >
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								是否需要客户填写资料 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="if_user_info" <? if($if_user_info==1){?>checked="true"<? }?> title="需要" value="1" />
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								是否需要快递/送货 
							</div>
							<div class="gaojisousuo_right">
								<input type="checkbox" name="if_kuaidi" <? if($if_kuaidi==1){?>checked="true"<? }?> title="需要" value="1" />
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
                                <input type="hidden" name="share_img" value="" id="share_img">
                                <button type="button" id="upload1" class="layui-btn">上传</button>
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
								<th width="174px">商品编码 </th>
								<th width="167px">售价（元）</th>
								<th width="167px">门市价（元）</th>
								<th width="167px">成本价(供货价)</th>
								<th width="167px">会员返利</th>
								<th width="167px">使用次数</th>
								<th width="167px">库存</th>
							</tr>
							<tr>
								<td><input type="text" name="sn0" mustrow value="<?=$sn?>" style="width:148px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_sale0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_market0" id="shichangjia0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="price_cost0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="<?=$step?>" mustrow name="fanli_tuanzhang0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="1" mustrow name="hexiaos0" min="0" style="width:102px;" /></td>
								<td><input type="number" step="1" mustrow name="kucun" min="0" style="width:102px;" /></td>
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
			</div>
			<div class="guige_set" id="moreGuige">

			</div>
			<div class="edit_photo">
				<div class="photo_tt">
					商品图片<span style="color:#9b9b9b">(说明：图片需处理成大小比例4:3，不超过2M)</span>
				</div>
				<div class="photo_tu">
					<ul>
						<li id="uploadImages" data-num="0" style="position:relative;">
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
						<!--<li><a href="javascript:" id="pdtcontMenu2" onclick="qiehuan('pdtcont',2,'on');">规格参数</a></li>
						<li><a href="javascript:" id="pdtcontMenu3" onclick="qiehuan('pdtcont',3,'on');">售后保障</a></li> -->
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
</script>
<script type="text/javascript" src="js/pdts/pdt_create.js"></script>
</body>
</html>