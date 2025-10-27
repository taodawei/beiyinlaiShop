<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shezhi = $db->get_row("select * from demo_shezhi where comId=$comId");
$tuihuan_reason = $shezhi->tuihuan_reason;
$tuihuan_reasons = explode('@_@',$tuihuan_reason);
$qxreasons = explode('@_@', $shezhi->qx_reason);
$websites = array();
if(!empty($shezhi->website)){
	$websites = explode('|',$shezhi->website);
}
$tuanzhang_rule = array();
if(!empty($shezhi->tuanzhang_rule)){
	$tuanzhang_rule = json_decode($shezhi->tuanzhang_rule,true);
}
$shequ_yunfei = array();
if(!empty($shezhi->shequ_yunfei)){
    $shequ_yunfei = json_decode($shezhi->shequ_yunfei,true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style type="text/css">
		.churukushezhi_01_down ul li{height:45px;line-height:45px;}
		.churukushezhi_01_down_1{width:140px;float:left;text-align:right;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2{display:inline-block;padding-left:10px;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2 input{vertical-align:middle}
		.churukushezhi_01_down_2_1{width:223px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-left:10px;font-size:13px;color:#acacac}
		.churukushezhi_01_down_2_2{width:83px;height:30px;border:#c5c5c5 1px solid;border-radius:7px;line-height:30px;padding-right:20px;text-align:right;font-size:13px;color:#acacac}
		.churukushezhi_01_down_shuoming{padding-left:114px;font-size:13px;color:#a8a8a8;padding-top:10px;}
		.mingpianshezhi_2_01{padding:18px 0}
		.mingpianshezhi_2_02{padding-bottom:57px;height:27px;line-height:27px;font-size:14px;color:#b7b7b7}
		.mingpianshezhi_2_02 a{width:80px;height:25px;display:inline-block;vertical-align:middle;margin-right:8px;border:#2791fa 1px solid;border-radius:5px;text-align:center;line-height:25px;font-size:16px;color:#2791fa}
		.mingpianshezhi_2_03{height:90px;display:none}
		.mingpianshezhi_2_03_1{width:365px;float:left;height:54px;}
		.mingpianshezhi_2_03_3{width:auto;float:left}
		.mingpianshezhi_2_03_3 img{vertical-align:middle;margin-right:20px}
		.mingpianshezhi_2_04{width:100%}
		.mingpianshezhi_2_04 span{display:inline-block;padding-right:30px;font-size:16px;color:#757575;line-height:32px}
		.mingpianshezhi_2_04 input{vertical-align:middle;margin-right:4px;width:16px;height:16px}
		.mingpianshezhi_2_04 span b{font-weight:400;font-size:13px;color:#a5a5a5}
		.mingpianshezhi_2_04 span b strong{font-weight:400;font-size:13px;color:#ff3434}
		.mingpianshezhi_2_xingxiang_left{width:211px;float:left}
		.mingpianshezhi_2_xingxiang{padding-bottom:20px;display:none}
		.mingpianshezhi_2_xingxiang_right{width:auto;float:left;padding-top:90px}
		.mingpianshezhi_2_xingxiang_right img{vertical-align:middle;margin-right:20px}
	</style>
</head>
<body>
	<div class="spshezhi">
		<div class="spshezhi_1">
			<img src="images/biao_35.png"> 基础设置
		</div>
		<form id="productSetForm" action="?m=system&s=mendian_set&a=index&tijiao=1" method="post" class="layui-form">
			<input type="hidden" name="com_logo" id="gift_img" value="<?=$shezhi->com_logo?>">
			<input type="hidden" name="com_back" id="back_img" value="<?=$shezhi->com_back?>">
			
			<input type="hidden" name="share_img" id="share_img" value="<?=$shezhi->share_img?>">
			
			<input type="hidden" name="zhishang_back" id="zhishang_back" value="<?=$shezhi->zhishang_back?>">
			<input type="hidden" name="yaoqing_back" id="yaoqing_back" value="<?=empty($tuanzhang_rule['yaoqing_back'])?'/skins/default/images/fenxianghaoyou_1.png':$tuanzhang_rule['yaoqing_back']?>">
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>店铺设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li style="height:auto;">
								<div class="churukushezhi_01_down_1">
									店铺logo ：
								</div>
								<div class="churukushezhi_01_down_2">
									<img src="<?=ispic($shezhi->com_logo)?>" id="upload_gift_img" width="100" style="cursor:pointer;">
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:请上传100K以内的图片，图片宽高比例1:1）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									店铺名称 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_title" value="<?=$shezhi->com_title?>" lay-verify="required" placeholder="" maxlength="25" class="layui-input" style="width:380px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li>
								<div class="churukushezhi_01_down_1">
									备案信息 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_beian" value="<?=$shezhi->com_beian?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									副标题 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_remark" value="<?=$shezhi->com_remark?>" lay-verify="required" placeholder="" maxlength="40" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:显示在店铺名称下边）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									分享介绍 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="share_desc" value="<?=$shezhi->share_desc?>" placeholder="" maxlength="100" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:通过微信分享店铺首页时的介绍说明）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									客服电话 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_kefu" value="<?=$shezhi->com_kefu?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									网站电话 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="pc_phone" value="<?=$shezhi->pc_phone?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:PC端电话）
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li>
								<div class="churukushezhi_01_down_1">
									网站邮箱 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_email" value="<?=$shezhi->com_email?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:PC端邮箱）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									联系电话 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_phone" value="<?=$shezhi->com_phone?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:小程序电话）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									客服微信 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="wx_kefu" value="<?=$shezhi->wx_kefu?>" lay-verify="required" placeholder="" class="layui-input" style="width:680px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:多微信|分隔）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									发货地址 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_address" value="<?=$shezhi->com_address?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li>
								<div class="churukushezhi_01_down_1">
									经纬度 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="com_coordinate" value="<?=$shezhi->com_coordinate?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
								    （PS:经度|纬度）
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li>
								<div class="churukushezhi_01_down_1">
									商品数量 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="number" step="1" name="pdt_max_num" value="<?=$shezhi->pdt_max_num?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
								    （PS:超过该商品数量，商品列表则变为表格形式）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									首页视频 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" step="1" name="index_video" value="<?=$shezhi->index_video?>" lay-verify="required" placeholder="" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
								    （PS:首页视频）
								</div>
								<div class="clearBoth"></div>
							</li>	
							
							
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									市场价名称 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="price_name" value="<?=$shezhi->price_name?>" lay-verify="required" placeholder="" maxlength="5" class="layui-input" style="width:380px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									（PS:例：京东价、天猫价，默认显示市场价）
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li style="height:300px;display:none;">
								<div class="churukushezhi_01_down_1">
									小程序图片 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<img src="<?=empty($shezhi->com_back)?'/skins/default/images/bj.gif':$shezhi->com_back?>" id="upload_back_img" width="300" style="cursor:pointer;">
								</div>
								<div class="churukushezhi_01_down_2"·>
									（PS:请上传500K以内的图片，图片推荐大小：720*628，<b style="color:red;">请务必使用暗色的背景图</b>）
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li style="height:300px;">
								<div class="churukushezhi_01_down_1">
									分享logo ：
								</div>
								<div class="churukushezhi_01_down_2">
									<img src="<?=empty($shezhi->share_img)?'/skins/default/images/bj.gif':$shezhi->share_img?>" id="upload_share_img" width="300" style="cursor:pointer;">
								</div>
								<div class="churukushezhi_01_down_2"·>
									（PS:分享logo）
								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li style="height:100px;">
								<div class="churukushezhi_01_down_1">
									公众号图片 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<img src="<?=empty($shezhi->zhishang_back)?'/skins/default/images/bj.gif':$shezhi->zhishang_back?>" id="upload_zhishang_back" width="300" style="cursor:pointer;">
								</div>
								<div class="churukushezhi_01_down_2"·>
									（PS:请上传500K以内的图片，图片推荐大小：720*628，<b style="color:red;">请务必使用暗色的背景图</b>）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									店铺网址 ：
								</div>
								<div class="churukushezhi_01_down_2" style="max-width:1000px;">
									<a href="http://<?=$comId?>.buy.zhishangez.com" style="color:#0f7eb3" target="_blank"><?=$comId?>.buy.zhishangez.com</a><br>
									绑定网址：
									<? if(!empty($websites)){
										foreach ($websites as $web){
											?>
											<input type="text" name="website[]" value="<?=$web?>" class="layui-input" style="width:380px;display:inline-block;margin-right:10px;"/>
											<?
										}
									}else{?>
									<input type="text" name="website[]" value="" class="layui-input" style="width:380px;display:inline-block;"/>
									<? }?>
									<a href="javascript:" onclick="add_yuming(this);" style="color:#0f7eb3;margin-left:20px;">添加域名</a>
								</div>
								<div style="margin-left:150px;margin-top:10px;color:red">
									1.请确定域名已在阿里云备案<br>2.请将域名解析A记录至47.105.74.42
								</div>
								<div class="clearBoth"></div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="spshezhi_2" style="padding-top:0px;display:none;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>手机商城模板</span>
					</div>
					<div>
						<ul>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="default" <? if($shezhi->moban=='default'||empty($shezhi->moban)){?>checked="true"<? }?> title="默认模板" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban1.png" target="_blank"><img src="images/moban1.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="muying" <? if($shezhi->moban=='muying'){?>checked="true"<? }?> title="母婴类模板" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban3.png" target="_blank"><img src="images/moban3.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="yizhi" <? if($shezhi->moban=='yizhi'){?>checked="true"<? }?> title="精简类模板(商品分类少的模板)" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban4.png" target="_blank"><img src="images/moban4.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<div class="clearBoth"></div>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="beiliang" <? if($shezhi->moban=='beiliang'){?>checked="true"<? }?> title="简约类模版(商品分类少的模板)" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban5.png" target="_blank"><img src="images/moban5.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px;display:none">
								<div style="text-align:center;margin-bottom:5px;">
									<input type="radio" name="moban" value="fushi" <? if($shezhi->moban=='fushi'){?>checked="true"<? }?> title="服饰类模板"> 
								</div>
								<div>
									<a href="images/moban2.png" target="_blank"><img src="images/moban2.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px;display:none">
								<div style="text-align:center;margin-bottom:5px;">
									<input type="radio" name="moban" value="wenju" <? if($shezhi->moban=='wenju'){?>checked="true"<? }?> title="文具类"> 
								</div>
								<div>
									<a href="images/moban2.png" target="_blank"><img src="images/moban2.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="dianqi" <? if($shezhi->moban=='dianqi'){?>checked="true"<? }?> title="电器类模版" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban6.png" target="_blank"><img src="images/moban6.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="moban7" <? if($shezhi->moban=='moban7'){?>checked="true"<? }?> title="模板7" /> 
								</div>
								<div style="text-align:center;">
									<a href="images/moban7.png" target="_blank"><img src="images/moban7.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px;display:none">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="xinlv" <? if($shezhi->moban=='xinlv'){?>checked="true"<? }?> title="立业新铝模板" /> 
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="width:326px;float:left;margin-left:30px;display:none">
								<div style="text-align:center;;margin-bottom:5px;">
									<input type="radio" name="moban" value="sakulun" <? if($shezhi->moban=='sakulun'){?>checked="true"<? }?> title="撒库伦模板" /> 
								</div>
								<div class="clearBoth"></div>
							</li>
							<!-- <li style="width:326px;float:left;margin-left:30px;">
								<div style="text-align:center;margin-bottom:5px;">
									<input type="radio" name="moban" value="hanqing" <? if($shezhi->moban=='hanqing'){?>checked="true"<? }?> title="酒水类模板"> 
								</div>
								<div>
									<a href="images/moban2.png" target="_blank"><img src="images/moban2.png" width="320"></a>
								</div>
								<div class="clearBoth"></div>
							</li> -->
							<div class="clearBoth"></div>
						</ul>
					</div>
				</div>
			</div>
			       <div class="spshezhi_2" style="padding-top:0px;display:none;">
                <div class="churukushezhi_01">
                    <div class="spshezhi_2_up">
                        <span>配送设置</span>
                    </div>
                    <div class="churukushezhi_01_down">
                        <ul>
                            <li>
                                <div class="churukushezhi_01_down_1">
                                    配送方式：
                                </div>
                                <div class="churukushezhi_01_down_2">
                                    <?
                                    $types = array();
                                    if(!empty($shequ_yunfei['peisong_types'])){
                                        $types = explode(',',$shequ_yunfei['peisong_types']);
                                    }
                                    ?>
                                    <input type="checkbox" name="peisong_types[]" value="2" lay-skin="primary" <? if(empty($shequ_yunfei['peisong_types']) || in_array(2,$types)){?>checked="true"<? }?> title="送货上门">
                                    <input type="checkbox" name="peisong_types[]" value="1" lay-skin="primary" <? if(empty($shequ_yunfei['peisong_types']) || in_array(1,$types)){?>checked="true"<? }?> title="站点自提">
                                    <input type="checkbox" name="peisong_types[]" value="3" lay-skin="primary" <? if(empty($shequ_yunfei['peisong_types']) || in_array(3,$types)){?>checked="true"<? }?> title="物流(快递)配送">
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="churukushezhi_01_down_1">
                                    送货上门起送费：
                                </div>
                                <div class="churukushezhi_01_down_2">
                                    <input type="text" name="peisong_qisong" value="<?=empty($shequ_yunfei['peisong_qisong'])?0:$shequ_yunfei['peisong_qisong']?>" lay-verify="required|number" class="layui-input" style="width:80px;display:inline-block;"/>（PS:不满起送费用不能下单，0代表不限制）
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="churukushezhi_01_down_1">
                                    站点自提起送费：
                                </div>
                                <div class="churukushezhi_01_down_2">
                                    <input type="text" name="peisong_qisong1" value="<?=empty($shequ_yunfei['peisong_qisong1'])?0:$shequ_yunfei['peisong_qisong1']?>" lay-verify="required|number" class="layui-input" style="width:80px;display:inline-block;"/>（PS:不满起送费用不能下单，0代表不限制）
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="churukushezhi_01_down_1">
                                    配送费：
                                </div>
                                <div class="churukushezhi_01_down_2">
                                    <input type="text" name="peisong_money" value="<?=empty($shequ_yunfei['peisong_money'])?0:$shequ_yunfei['peisong_money']?>" lay-verify="required|number" class="layui-input" style="width:80px;display:inline-block;"/>&nbsp;&nbsp;&nbsp;
                                    满多少免配送费：<input type="text" name="peisong_man" value="<?=empty($shequ_yunfei['peisong_man'])?0:$shequ_yunfei['peisong_man']?>" lay-verify="required|number" class="layui-input" style="width:80px;display:inline-block;"/>&nbsp;（PS:0代表不免配送费）
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="spshezhi_2" style="padding-top:170px;">
				<div class="spshezhi_2_up">
					<span>经销商设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
					    
					    <li>
							<div class="churukushezhi_01_down_1">
								开关 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="radio" name="show_nav_jingxiao" value="0" title="关闭" <? if(empty($shezhi->show_nav_jingxiao)){?>checked="checked"<? }?>>
                                <input type="radio" name="show_nav_jingxiao" value="1" title="开启" <? if($shezhi->show_nav_jingxiao==1){?>checked="checked"<? }?>>
							</div>
							<div class="clearBoth"></div>
						</li>
					    
						<li style="display:none;">
							<div class="churukushezhi_01_down_1">
								开关 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="radio" name="if_tixian" value="0" title="关闭" <? if(empty($shezhi->if_tixian)){?>checked="checked"<? }?>>
                                <input type="radio" name="if_tixian" value="1" title="开启" <? if($shezhi->if_tixian==1){?>checked="checked"<? }?>>
							</div>
							<div class="clearBoth"></div>
						</li>
						<li style="display:none;">
							<div class="churukushezhi_01_down_1">
								手续费 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="number" min="0" step="10" name="tixian_bili" value="<?=$shezhi->tixian_bili?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：1元需要手续费
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
					
				</div>
			</div>
			
			 <div class="spshezhi_2" style="padding-top:70px;">
				<div class="spshezhi_2_up">
					<span>积分设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="churukushezhi_01_down_1">
								邀请注册获取 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="number" min="0" step="10" name="invite_jifen" value="<?=$shezhi->invite_jifen?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：成功邀请注册，赠送邀请人积分
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li>
							<div class="churukushezhi_01_down_1">
								用户注册获取 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="number" min="0" step="10" name="register_jifen" value="<?=$shezhi->register_jifen?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：用户成功注册，赠送新用户积分
							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="churukushezhi_01_down_1">
								看视频获取 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="number" min="0" step="10" name="video_jifen" value="<?=$shezhi->video_jifen?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：用户看视频获取积分
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
					
				</div>
			</div>
			
			
			 <div class="spshezhi_2" style="padding-top:170px;">
				<div class="spshezhi_2_up">
					<span>线下转款设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="churukushezhi_01_down_1">
								收款单位 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text" name="offline_company" value="<?=$shezhi->offline_company?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：收款单位名称
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li>
							<div class="churukushezhi_01_down_1">
								收款账号 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text" name="offline_code" value="<?=$shezhi->offline_code?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：收款单位账号
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li>
							<div class="churukushezhi_01_down_1">
								开户银行 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text" name="offline_bank" value="<?=$shezhi->offline_bank?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：开户银行
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
					
				</div>
			</div>
			
			
			<div class="spshezhi_2" style="padding-top:70px;">
				<div class="spshezhi_2_up">
					<span>物流设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="churukushezhi_01_down_1">
								物流选择 ：
							</div>
							<div class="churukushezhi_01_down_2">
                                <input type="radio" name="express_type" value="3" title="暂不接入" <? if($shezhi->express_type==3){?>checked="checked"<? }?>>
								<input type="radio" name="express_type" value="0" title="快递鸟" <? if(empty($shezhi->express_type)){?>checked="checked"<? }?>>
                                <input type="radio" name="express_type" value="1" title="快递100" <? if($shezhi->express_type==1){?>checked="checked"<? }?>>
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li <?=$shezhi->express_type!=0?'style="display:none;"':'' ?>>
							<div class="churukushezhi_01_down_1">
								快递鸟用户id ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text"  name="kdn_EBusinessID" value="<?=$shezhi->kdn_EBusinessID?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：快递鸟 EBusinessID  用户ID
							</div>
							<div class="clearBoth"></div>
						</li>
						<li <?=$shezhi->express_type!=0?'style="display:none;"':'' ?>>
							<div class="churukushezhi_01_down_1">
								快递鸟Key ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text"  name="kdn_key" value="<?=$shezhi->kdn_key?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：快递鸟 key
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li <?=$shezhi->express_type!=0?'style="display:none;"':'' ?>>
							<div class="churukushezhi_01_down_1">
								快递鸟端口 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text"  name="kdn_port" value="<?=$shezhi->kdn_port?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：快递鸟端口 普通变为vip 接口指令查询即时查询接口，RequestType为1002，替换成8001，物流跟踪接口，RequestType为1008，替换成8008
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li <?=$shezhi->express_type!=1?'style="display:none;"':'' ?>>
							<div class="churukushezhi_01_down_1">
								快递100客户授权Key ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text"  name="kd100_key" value="<?=$shezhi->kd100_key?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：快递100-客户授权key
							</div>
							<div class="clearBoth"></div>
						</li>
						
						<li <?=$shezhi->express_type!=1?'style="display:none;"':'' ?>>
							<div class="churukushezhi_01_down_1">
								快递100公司编号 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<input type="text"  name="kd100_customer" value="<?=$shezhi->kd100_customer?>" placeholder="" class="layui-input" style="width:380px"/>
							</div>
							<div style="margin-left:150px;margin-top:10px;color:red">
							    注：快递100公司编号
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
					
				</div>
			</div>
			
			<div class="spshezhi_2" style="padding-top:0px;display:none;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>分销设置（<font color="red">如非必要请不要修改</font>）</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
						    <li>
								<div class="churukushezhi_01_down_1">
									分销开启/关闭 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<select name="if_fenxiao">
										<option value="0" <? if($shezhi->if_fenxiao==0){?>selected="true"<? }?>>关闭</option>
										<option value="1" <? if($shezhi->if_fenxiao==1){?>selected="true"<? }?>>开启</option>
									</select>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									拼团开启/关闭 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<select name="if_pintuan">
										<option value="0" <? if($shezhi->if_pintuan==0){?>selected="true"<? }?>>关闭</option>
										<option value="1" <? if($shezhi->if_pintuan==1){?>selected="true"<? }?>>开启</option>
									</select>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									分销类型 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<select name="fanli_type" lay-filter="fanli_type">
										<option value="1" <? if($shezhi->fanli_type==1){?>selected="true"<? }?>>按上下级返佣</option>
										<option value="2" <? if($shezhi->fanli_type==2){?>selected="true"<? }?>>按团队返佣</option>
									</select>
								</div>
								<div class="churukushezhi_01_down_2"·>
									按上下级返佣：返佣给购买者的上级和上上级会员；
									按团队返佣:返佣给购买者的上级和购买者所在团队的团长（团长需要申请）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li id="tuanzhang_rule_div" style="display:none" >
								<div class="churukushezhi_01_down_1">
									升级团长条件 ：
								</div>
								<div class="churukushezhi_01_down_2">
									邀请人数达到：<input type="text" name="yaoqing_num" value="<?=$tuanzhang_rule['yaoqing_num']?>" lay-verify="number" class="layui-input" style="width:80px;display:inline-block;"/>&nbsp;&nbsp;&nbsp;
									获得佣金达到：<input type="text" name="yaoqing_yongjin" value="<?=$tuanzhang_rule['yaoqing_yongjin']?>" lay-verify="number" class="layui-input" style="width:80px;display:inline-block;"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									代理直推返佣 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="number" name="shangji_price" value="<?=(int)$shezhi->shangji_price?>" lay-verify="required|number" placeholder="0-100之间" maxlength="6" class="layui-input" step="1" min="1" max="100" style="width:180px;display: inline-block;"/>
								</div>
								<div class="churukushezhi_01_down_2">

								</div>
								<div class="clearBoth"></div>
							</li>
							
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									合伙人直推返佣 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="number" name="shangshangji_price" value="<?=(int)$shezhi->shangshangji_price?>" lay-verify="required|number" placeholder="0-100之间" maxlength="6" class="layui-input" step="1" min="1" max="100" style="width:180px;display: inline-block;"/>
								</div>
								<div class="churukushezhi_01_down_2">

								</div>
								<div class="clearBoth"></div>
							</li>
                            <li style="display:none;">
                                <div class="churukushezhi_01_down_1">
                                    合伙人间推返佣 ：
                                </div>
                                <div class="churukushezhi_01_down_2">
                                    <input type="number" name="shangshangji_jt_price" value="<?=(int)$shezhi->shangshangji_jt_price?>" lay-verify="required|number" placeholder="0-100之间" maxlength="6" class="layui-input" step="1" min="1" max="100" style="width:180px;display: inline-block;"/>
                                </div>
                                <div class="churukushezhi_01_down_2">

                                </div>
                                <div class="clearBoth"></div>
                            </li>
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									购买者返佣比例 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="number" id="shangji_fanli" name="shang_bili" value="<?=$shezhi->shang_bili?>" lay-verify="required|number" placeholder="0-100之间" maxlength="6" class="layui-input" style="width:80px;display: inline-block;"/> %
								</div>
								<div class="churukushezhi_01_down_2">
									购买者返佣比例 = 商品设置的会员返利 * 购买者返佣比例
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									上级返佣比例 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" id="user_fanli" readonly="true" value="<?=100-$shezhi->shangji_bili-$shezhi->shang_bili?>" class="layui-input" style="width:80px;display: inline-block;background:#ccc"/> %
								</div>
								<div class="churukushezhi_01_down_2">
									上级返佣 = 商品设置的会员返利 - 上上级返佣 - 购买者返佣
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="display:none;">
								<div class="churukushezhi_01_down_1">
									是否开启社区团 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<select name="if_shequ_tuan" lay-filter="if_shequ_tuan">
										<option value="0" <? if($shezhi->if_shequ_tuan==0){?>selected="true"<? }?>>不开启</option>
										<option value="1" <? if($shezhi->if_shequ_tuan==1){?>selected="true"<? }?>>开启</option>
									</select>
								</div>
								<div class="churukushezhi_01_down_2">
									开启之后会员可以申请成为社区站长，社区站长可以开启社区团
								</div>
								<div class="clearBoth"></div>
							</li>
							<li style="height:auto;display:none;">
								<div class="churukushezhi_01_down_1">
									邀请背景图 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<img src="<?=empty($tuanzhang_rule['yaoqing_back'])?'/skins/default/images/fenxianghaoyou_1.png':$tuanzhang_rule['yaoqing_back']?>" id="upload_yaoqing_back" width="300" style="cursor:pointer;"><input class="layui-upload-file" type="file" name="file">
								</div>
								<div class="churukushezhi_01_down_2" ·="">
									（PS:请上传500K以内的图片，图片大小：704*1258，<b style="color:red;">请务必按原图的位置和大小留出二维码位置</b>）
								</div>
								<div class="clearBoth"></div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>时限设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									订单支付时限 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="time_pay" value="<?=$shezhi->time_pay?>" lay-verify="required|number" placeholder="1-120之间" maxlength="3" class="layui-input" style="width:80px"/>
								</div>
								<div class="churukushezhi_01_down_2"·>
									分钟（PS:即订单需要在多少分钟内支付，否则订单将无效）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									自动收货时间 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="time_shouhuo" value="<?=$shezhi->time_shouhuo?>" lay-verify="required|number" placeholder="" maxlength="4" class="layui-input" style="width:80px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									天（PS:即订单在发货多少天后自动收货，发放返利）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li <?=$if_pintuan==0?'style="display:none"':''?>>
								<div class="churukushezhi_01_down_1">
									成团时限 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="time_tuan" value="<?=$shezhi->time_tuan?>" lay-verify="required|number" placeholder="" maxlength="4" class="layui-input" style="width:80px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									小时（PS:即团购需要在多少小时内成团，否则团购将失败）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									自动好评时间 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="time_comment" value="<?=$shezhi->time_comment?>" lay-verify="required|number" placeholder="" maxlength="4" class="layui-input" style="width:80px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									天（PS:即确认收货多少天后系统自动好评）
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									可售后时间 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="time_shouhuo" value="<?=$shezhi->time_shouhuo?>" lay-verify="required|number" placeholder="" maxlength="4" class="layui-input" style="width:80px"/>
								</div>
								<div class="churukushezhi_01_down_2">
									天（PS:即确认收货多少天可售后）
								</div>
								<div class="clearBoth"></div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="spshezhi_2" style="padding-top:0px;display:none;">
				<div class="churukushezhi_01">
					<div class="spshezhi_2_up">
						<span>发货设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									默认发货仓库 ：
								</div>
								<div class="churukushezhi_01_down_2">
									<select name="storeId" class="layui-select"><?
										$cangkuSql = "select id,title from demo_kucun_store where comId=$comId order by id asc";
										$cangkus = $db->get_results($cangkuSql);
										foreach ($cangkus as $store) {
											?><option value="<?=$store->id?>" <? if($store->id==$shezhi->storeId){?>selected="true"<? }?>><?=$store->title?></option><?
										}
									?></select>
								</div>
								<div class="clearBoth"></div>
							</li>
						</ul>
					</div>
					<div style="margin-left:45px;margin-top:10px;padding-bottom:10px;border-bottom:1px dashed #ccc;">特殊区域发货设置(<span style="color:red;font-size:12px">不同的区域设置不同的发货仓库</span>)<a href="javascript:" id="add_area_btn" style="color:#35a6dd;margin-left:20px;">增加指定区域发货仓库</a></div>
					<div id="fahuo_areas">
						<? $fahuo_areas = $db->get_results("select * from demo_shezhi_fahuo where comId=$comId");
						if(!empty($fahuo_areas)){
							foreach ($fahuo_areas as $area) {
								$areaNames = $db->get_var("select group_concat(title) from demo_area where id in($area->areaIds)");
								$storeName = $db->get_var("select title from demo_kucun_store where id=$area->storeId");
								?><div style="margin:8px;line-height:35px;margin-left:45px;"><?=$areaNames?>&nbsp;&nbsp;发货仓库：<?=$storeName?>&nbsp;&nbsp;<a href="javascript:" onclick="del_fahuo_store(this,<?=$area->id?>);" style="color:red;">删除</a></div><?
							}
						}
						?>
					</div>
				</div>
			</div>
			
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="spshezhi_2_up">
					<span>填写取消原因</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_03" >
								<? if(!empty($qxreasons)){
									foreach ($qxreasons as $val){
										?><input type="text" name="qx_reason[]" value="<?=$val?>" placeholder="填写取消原因"><?
									}
								}?>
								<a href="javascript:" onclick="add_reci(this,'qx_reason');" style="color:#0f7eb3;margin-left:20px;">添加取消原因</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
			
			<div class="spshezhi_2" style="padding-top:0px;">
				<div class="spshezhi_2_up">
					<span>热词</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_03" >
								<? if(!empty($tuihuan_reasons)){
									foreach ($tuihuan_reasons as $val){
										?><input type="text" name="tuihuan_reason[]" value="<?=$val?>" placeholder="填写退换货原因"><?
									}
								}?>
								<a href="javascript:" onclick="add_reci(this,'tuihuan_reason');" style="color:#0f7eb3;margin-left:20px;">添加原因</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_2" style="padding-top:0px;display:none;">
				<div class="spshezhi_2_up">
					<span>可开发票设置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="churukushezhi_01_down_1">
								发票类型 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<select name="kaipiao_type" class="layui-select">
									<option value="0" <? if($shezhi->kaipiao_type==0){?>selected="true"<? }?>>不提供发票服务</option>
									<option value="1" <? if($shezhi->kaipiao_type==1){?>selected="true"<? }?>>提供普通发票</option>
									<option value="2" <? if($shezhi->kaipiao_type==2){?>selected="true"<? }?>>提供增值税发票</option>
								</select>
							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="churukushezhi_01_down_1">
								是否支持电子发票 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<select name="if_dianzi_fapiao" class="layui-select">
									<option value="0">不支持</option>
									<option value="1" <? if($shezhi->if_dianzi_fapiao==1){?>selected="true"<? }?>>支持</option>
								</select>
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
					
				</div>
			</div>
			<div class="spshezhi_2" style="padding-top:0px;display:none;">
				<div class="spshezhi_2_up">
					<span>价格搜索配置</span>
				</div>
				<div class="spshezhi_2_down">
					<ul>
						<li>
							<div class="spshezhi_2_down_03" >
								<textarea name="xieyi" class="layui-textarea"><?=$shezhi->xieyi?></textarea>
							</div>
							
							<div class="churukushezhi_01_down_2" style="color:red;">
								注：格式为:100元以下@0@100|名称@范围小的金额@范围大的金额
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
				</div>
			</div>
			<div class="spshezhi_4" style="position:fixed;padding-bottom:10px;background:#fff;padding-left:20px;bottom:0px;width:100%">
				<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
			</div>
		</form>
	</div>
	<div class="spxx_shanchu_tanchu" id="spxx_shanchu_tanchu" style="display:none;position:fixed;z-index:9;top:50%;left:50%;margin-left:-265px;margin-top:-150px;">
    	<div class="spxx_shanchu_tanchu_01">
            <div class="spxx_shanchu_tanchu_01_left">
                增加指定区域发货仓库
            </div>
            <div class="spxx_shanchu_tanchu_01_right">
                <a href="javascript:" onclick="$('#spxx_shanchu_tanchu').hide();"><img src="images/biao_47.png"></a>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="spxx_shanchu_tanchu_02">
            <div class="jiliang_tanchu">
                <span>*</span> 选择区域 
                <input type="text" id="areaIdsFanwei" placeholder="选择区域" onclick="area_fanwei('areaIds');" readonly="true" value="">
                <div style="margin-top:10px;"></div>
                <span>*</span> 选择仓库 <select id="add_store_id">
                	<?
                	foreach ($cangkus as $store) {
						?><option value="<?=$store->id?>"><?=$store->title?></option><?
					}
                	?>
                </select>
            </div>
            <input type="hidden" name="areaIds" id="areaIds" value="">
            <input type="hidden" id="departs" value="">
            <input type="hidden" id="departNames" value="">
            <input type="hidden" id="editId" value="">
        </div>
        <div class="spxx_shanchu_tanchu_03">
			<a href="javascript:" onclick="addStore();" class="spxx_shanchu_tanchu_03_2">确定</a><a href="javascript:" onclick="$('#spxx_shanchu_tanchu').hide();" class="spxx_shanchu_tanchu_03_1">取消</a>
		</div>
    </div>
    <div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
	<script type="text/javascript" src="js/shezhi/mendian_set.js?v=1.1"></script>
	
	<script>
	    function add_reci(dom,name){
        	$(dom).before('<input type="text" name="'+name+'[]" value="" placeholder="'+(name=='qx_reason'?'填写取消原因':'填写热搜词汇')+'">');
        }
	</script>
</body>
</html>