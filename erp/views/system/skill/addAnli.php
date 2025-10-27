<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];

$id = (int)$request['id'];
$skillId = (int)$request['skillId'];
$gonggao = null;
$customArr = [];

if($id > 0){
    $gonggao = $db->get_row("select * from demo_skill_process where id = $id and is_del = 0 ");
    $skillId = $gonggao->skillId;
    $ruleJson = $gonggao->file_info;
}

$skill = $db->get_row("select * from  demo_skill where id = $skillId and is_del = 0 ");

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
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a>服务<?=$skill->title?> 新增案例
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=skill&a=addAnli&submit=1&id=<?=$id?>" class="layui-form">
                <input type="hidden" name="id" value="<?=$id?>">
		        <input type="hidden" name="skillId" value="<?=$skillId?>">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 案例标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$gonggao->title?>" lay-verify="required" placeholder="请输入案例标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
            
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 副标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="subtitle" value="<?=$gonggao->subtitle?>" lay-verify="required" placeholder="请输入副标题" class="yx_guanggaoadd_01_right_input"/>
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
                            <span>*</span>展示
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="status" value="0" title="隐藏" <? if(empty($gonggao->status)){?>checked="checked"<? }?>>
                            <input type="radio" name="status" value="1" title="展示" <? if($gonggao->status==1 || empty($gonggao)){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
					
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
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" type="button" onclick="history.go(-1);return false;">取 消</button>
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
});
</script>
<? require('views/help.html');?>
</body>
</html>