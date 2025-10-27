<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$gonggao = null;
if(!empty($id)){
    $gonggao = $db->get_row("select * from demo_study where id=$id ");
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
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="/keditor/kindeditor.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 新增学习内容
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=study&a=add&submit=1&id=<?=$id?>" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$gonggao->title?>" lay-verify="required" placeholder="请输入广告标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 分类
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <div class="layui-form-select">
								<div class="layui-select-title" id="selectChannel" style="width:655px;"><input type="text" readonly placeholder="请选择分类" value="<?=$gonggao ? $db->get_var("select title from demo_study_channel where id=".$gonggao->channelId) : '';?>" class="layui-input"><i class="layui-edge"></i></div>
								<dl class="layui-anim layui-anim-upbit" id="selectChannels" style="z-index:9999;"></dl>
							</div>
							<input type="hidden" name="channelId" id="channelId" value="<?=$gonggao->channelId?>" lay-verify="required"> 
                        </div>
                        <div class="clearBoth"></div>
                    </li>

                    <li>
                           <div class="yx_guanggaoadd_01_left">
                            <span>*</span>  图片
                        </div>
                        <div class="yx_guanggaoadd_01_right_addtupian">
                            <div class="yx_guanggaoadd_01_right_addtupian_01" id="uploadImg" style="height:100px;width:100px">
                                <? if(empty($gonggao->originalPic)){?>
                                    <b style="margin-top:20px;display:block;">+</b><br>上传图片
                                <? }else{
                                    ?><img src="<?=$gonggao->originalPic?>" height="100"><?
                                }?>
                            </div>
                            <div class="yx_guanggaoadd_01_right_addtupian_02">
                                仅支持jpg,jpeg,png,bmp格式，文件小于1M
                            </div>
                        </div>
                        <input type="hidden" name="originalPic" id="originalPic" value="<?=$gonggao->originalPic?>">
                    </li>
                   <li style="display:none;">
                           <div class="yx_guanggaoadd_01_left">
                            <span></span>  视频封面
                        </div>
                        <div class="yx_guanggaoadd_01_right_addtupian">
                            <div class="yx_guanggaoadd_01_right_addtupian_01" id="uploadLogo" style="height:100px;width:100px">
                                <? if(empty($gonggao->video_img)){?>
                                    <b style="margin-top:20px;display:block;">+</b><br>视频封面
                                <? }else{
                                    ?><img src="<?=$gonggao->video_img?>" height="100"><?
                                }?>
                            </div>
                            <div class="yx_guanggaoadd_01_right_addtupian_02">
                                仅支持jpg,jpeg,png,bmp格式，文件小于1M
                            </div>
                        </div>
                        <input type="hidden" name="video_img" id="logo" value="<?=$gonggao->video_img?>">
                    </li>
                    
                    <li style="display:none;">
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  外链
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="path" value="<?=$gonggao->path?>"  placeholder="外链" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    
                    <li style="display:none;">
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  绑定商品分类
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="product_channel">
                                <option value="0">请选择</option>
                                <?
                                    if(!empty($pchannels)){
                                        foreach ($pchannels as $channel) {
                                            
                                            ?>
                                            <option value="<?=$channel->id?>" <?if($gonggao->product_channel==$channel->id){?>selected="true"<? }?>><?=$channel->title?></option>
                                            
                                            <?}} ?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    
                     <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  视频链接
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="video" value="<?=$gonggao->video?>"  placeholder="视频类型内容，此项必填" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  下载链接
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="download" value="<?=$gonggao->download?>"  placeholder="文件下载类型内容，此项必填" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  权重
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="ordering" value="<?=$gonggao->ordering?>"  placeholder="请输入权重，数字越大越先显示" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  

                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span>  展示
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="status" value="1" title="是" <? if(empty($gonggao) || $gonggao->status==1){?>checked="checked"<? }?>>
                            <input type="radio" name="status" value="0" title="否" <? if($gonggao && $gonggao->status == 0 ){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span>语言选择
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="language" value="0" title="中文" <? if(empty($gonggao->language)){?>checked="checked"<? }?>>
                            <input type="radio" name="language" value="1" title="英文" <? if($gonggao->language==1){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  


                    <!-- <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 发送对象
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="level">
                                <option value="0">所有客户</option>
                                <?foreach($levels as $l){ ?>
                                <option value="<?=$l->id?>" <? if($l->id==$gonggao->level){?>selected="true"<? }?>><?=$l->title?></option> 
                                <? }?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li> -->
					
					<li>
                        <div class="yx_guanggaoadd_01_left">
                           <span></span> 简介
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <textarea name="jianjie" style="width:654px;height:200px;border: medium dashed green;" class="layui-textarea" placeholder="图文信息必填简介和详细内容"><?=$gonggao->jianjie?></textarea>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
					
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span></span> 内容
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <?php
                            ewebeditor(EDITORSTYLE,'content',$gonggao->content,'800');
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            </form>
        </div>
    </div>
<script type="text/javascript">
  layui.use(['laydate','form','upload'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    ,upload = layui.upload
    upload.render({
        elem: '#uploadImg'
        ,url: '?m=system&s=upload&a=upload'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadImg').html('<img src="'+res.url+'" height="100">');
                $("#originalPic").val(res.url);
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    form.on('submit(tijiao)', function(data){
        layer.load();
    });
});


  layui.use(['laydate','form','upload'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    ,upload = layui.upload
    upload.render({
        elem: '#uploadLogo'
        ,url: '?m=system&s=upload&a=upload'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadLogo').html('<img src="'+res.url+'" height="100">');
                $("#logo").val(res.url);
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    form.on('submit(tijiao)', function(data){
        layer.load();
    });
    
    ajaxpost = $.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_study_channels2",
		data: "pid="+channelId,
		dataType:"text",timeout : 8000,
		success: function(resdata){
			$("#selectChannels").append(resdata);
// 			if(unit_type==1){
// 				$("#danunit").next().hide();
// 			}
		},
		error: function() {
			layer.msg('数据请求失败', {icon: 5});
		}
	});
});


function selectMenu(eve,dom){
	if($(dom).find('.menuLeft').length>0){
		var id = $(dom).attr("lay-value");
		showNextMenus(eve,$(dom).find('span').eq(0),id);
	}else{
		$("#channelId").val($(dom).attr("lay-value"));
		$("#selectChannel").find('input').val($(dom).html());
		console.log("111111");
	}
}

$("#selectChannel").click(function(){
	$(this).parent().toggleClass('layui-form-selected');
});

function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}

</script>
<? require('views/help.html');?>
</body>
</html>