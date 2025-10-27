<?
global $db,$request;
$productId = $id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(!empty($id)){
	$product = $db->get_row("select * from zhuisu_pdt where id=$productId");
	if(empty($product)){
		die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
	}
}
$url = urlencode($request['url']);
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
	<link href="styles/yingxiaoguanli.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/keditor/kindeditor1.js"></script>
</head>
<body>
	<form action="?m=system&s=zhuisu&a=editPdts&tijiao=1&id=<?=$id?>" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
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
								<input type="text" class="layui-input" name="title" id="title" value="<?=$product->title?>" lay-verify="required" placeholder="请输入商品名称">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								商品规格 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="key_vals" value="<?=$product->key_vals?>">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								商品规格 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="key_vals" value="<?=$product->key_vals?>">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								商品检测证书名称 
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="jiance_name" value="<?=$product->jiance_name?>">
							</div>
						</li>
						<div class="clearBoth"></div>
						<li style="height:auto;width:100%">
							<div class="gaojisousuo_left">
								商品视频
							</div>
							<div class="gaojisousuo_right">
								<? if(!empty($product->shipin)){?>
			                            <video src="<?=$product->shipin?>" controls="controls" preload="none" width="412" height="240" style="margin-left:40px;"></video>
			                            <div class="clearBoth"></div>
			                    <? }?>
			                    <input type="button" onclick="showBanner();" id="upload_btn" value="上传视频" style="background-color:#FFFFFF; width:82px; height:26px; color:#333333; border:none; border:1px #cccccc solid; font-size:12px; cursor:pointer;">
			                    请上传mp4格式视频，大小不超过25M
							</div>
						</li>
						
						<div class="clearBoth"></div>
					</ul>
				</div>
			</div>
		<div class="edit_miaoshu">
			<div class="miaoshu_tt">
				商品介绍
			</div>
			<div class="miaoshu_edit">
				<?php
					ewebeditor(EDITORSTYLE,'content',$product->content);
				?>
			</div>
		</div>
		<div class="edit_miaoshu">
			<div class="miaoshu_tt">
				检测证书
			</div>
			<div class="miaoshu_edit">
				<?php
					ewebeditor(EDITORSTYLE,'jiance_content',$product->jiance_content);
				?>
			</div>
		</div>
		<div class="edit_save">
			<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
			<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
		</div>
	</div>
</div>
<input type="hidden" name="shipin" id="url" value="<?=empty($product->shipin)?'':$product->shipin?>">
</form>
<div class="bj1" id="bj1"></div>
<div class="shangchuanwenjian_tk" id="shangchuanwenjian_tk" style="display:none;">
    <div class="shangchuanwenjian_tk_01">
        <div class="shangchuanwenjian_tk_01_left">
            上传视频
        </div>
        <div class="shangchuanwenjian_tk_01_right" onclick="$('#bj1').hide();$('#shangchuanwenjian_tk').hide();">
            <a href="javascript:"><img src="images/guanbi.png"></a>
        </div>
        <div class="clearBoth"></div>
    </div>
    <input id="upload_type" name="upload_type" value="1" type="hidden">
    <div class="shangchuanwenjian_tk_04" id="container" style="position: relative;">
        <a class="shangchuanwenjian_tk_04_left" id="selectfiles" style="position:relative;z-index:1;line-height:30px;" onmouseover="layer.tips('非H.264编码的MP4文件在浏览器中会出现只有声音没有图像的问题，请上传前检查好',this,{tips: [1,'#666'],time:0});" onmouseout="layer.closeAll('tips');">
            选择视频文件<span style="font-size:10px;color:#f09306;font-family:'微软雅黑','宋体';">(请上传H.264编码且小于25M的MP4文件)</span>
        </a>
        <div class="shangchuanwenjian_tk_04_right">
            <div class="shangchuanwenjian_tk_06">
                <pre id="console" style="color:red"></pre>
                <a id="postfiles" href="javascript:void(0);">上 传</a>
            </div>
        </div>
        <div class="clearBoth"></div>
        <div id="html5_1cmhaeoj219njdr1sr7lg6dvt3_container" class="moxie-shim moxie-shim-html5" style="position: absolute; top: 0px; left: 0px; width: 0px; height: 0px; overflow: hidden; z-index: 0;"><input id="html5_1cmhaeoj219njdr1sr7lg6dvt3" type="file" style="font-size: 999px; opacity: 0; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;" multiple="" accept="video/mp4"></div>
    </div>
    <div id="ossfile" class="shangchuanwenjian_tk_03"></div>
</div>
<script type="text/javascript" src="file/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="js/yingxiao/addShipin.js"></script>
</body>
</html>