<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $gonggao = $db->get_row("select * from demo_list where id=$id and comId=$comId");
}
$channels = $db->get_results("select id,title from demo_list_channel where comId=$comId and parentId = 0");

$pchannels = $db->get_results("select id,title from demo_product_channel where comId=$comId and parentId = 0");
$source = (int)$request['source'];
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
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 新增资讯
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=banner&a=addGonggao&id=<?=$id?>" class="layui-form">
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
                             <select name="channelId">
                                <option value="0">请选择</option>
                                <?
                                    if(!empty($channels)){
                                        foreach ($channels as $channel) {
                                            $channel1 = $db->get_results("select id,title from demo_list_channel where comId=$comId and parentId = $channel->id");
                                            ?>
                                            <option value="<?=$channel->id?>" <?if($gonggao->channelId==$channel->id){?>selected="true"<? }?>><?=$channel->title?></option>
                                            <? foreach ($channel1 as $v){?>
                                                <option value="<?=$v->id?>" <?if($gonggao->channelId==$v->id){?>selected="true"<? }?>>--<?=$v->title?></option>
                                            <?} ?>
                                            <?}} ?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>

                    <li>
                           <div class="yx_guanggaoadd_01_left">
                            <span></span>  图片
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
                            <span></span>  视频链接
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="video" value="<?=$gonggao->video?>"  placeholder="视频地址链接" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
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
                            <span></span>  权重
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" min="0" value="0" step="1" name="ordering" value="<?=$gonggao->ordering?>"  placeholder="请输入权重，数字越大越先显示" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  

                    <li style="display:none;">
                        <div class="yx_guanggaoadd_01_left">
                            <span></span>  是否推荐
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="if_index" value="0" title="否" <? if(empty($gonggao->if_index)){?>checked="checked"<? }?>>
                            <input type="radio" name="if_index" value="1" title="是" <? if($gonggao->if_index==1){?>checked="checked"<? }?>>
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
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span>是否展示
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="if_show" value="0" title="否" <? if(empty($gonggao->if_show)){?>checked="checked"<? }?>>
                            <input type="radio" name="if_show" value="1" title="是" <? if($gonggao->if_show==1){?>checked="checked"<? }?>>
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
                           <span>*</span> 简介
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <textarea name="jianjie" style="width:654px;height:200px;border: medium dashed green;" class="layui-textarea" placeholder="输入资讯列表的资讯简介"><?=$gonggao->jianjie?></textarea>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 内容
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <?php
                            ewebeditor(EDITORSTYLE,'content',$gonggao->content,'800');
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                    	<div class="yx_guanggaoadd_01_left">
                        	发布时间
                        </div>
                    	<div class="yx_guanggaoadd_01_right">
                        	<input type="text" name="dtTime" value="<?=empty($gonggao->dtTime)?'':($gonggao->dtTime=='0000-00-00'?'':$gonggao->dtTime)?>" id="dtTime" placeholder="发布时间" class="addhuiyuan_2_02_input"/>
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
     laydate.render({
		  	elem: '#dtTime'
		  	,max:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($gonggao->dtTime)&&$gonggao->dtTime!='0000-00-00'){?>,value:'<?=$gonggao->dtTime?>'<?}?>
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd'
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
});
</script>
<? require('views/help.html');?>
</body>
</html>