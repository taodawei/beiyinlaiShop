<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $banner = $db->get_row("select * from dinghuo_banner where id=$id and comId=$comId");
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
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 广告新增
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=yingxiao&a=addBanner&id=<?=$id?>" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 广告标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$banner->title?>" lay-verify="required" placeholder="请输入广告标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            链接地址
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="linkUrl" value="<?=empty($banner->linkUrl)?'http://':$banner->linkUrl?>" lay-verify="required" placeholder="请输入广告链接" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_right_addtupian">
                            <div class="yx_guanggaoadd_01_right_addtupian_01" id="uploadImg">
                                <? if(empty($banner->originalPic)){?>
                                    <b style="margin-top:100px;display:block;">+</b><br>上传广告图片
                                <? }else{
                                    ?><img src="<?=$banner->originalPic?>" width="720" height="288"><?
                                }?>
                            </div>
                            <div class="yx_guanggaoadd_01_right_addtupian_02">
                                仅支持jpg,jpeg,png,bmp格式，文件小于1M，广告图片尺寸720*288像素
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" id="originalPic" value="<?=$banner->originalPic?>">
            </form>
        </div>
    </div>
<script type="text/javascript">
  layui.use(['laydate','form','upload'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    ,upload = layui.upload
    var uploadInit = upload.render({
        elem: '#uploadImg'
        ,url: '?m=system&s=upload&a=upload&width=720&height=288'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadImg').html('<img src="'+res.url+'" width="720" height="288">');
                $("#originalPic").val(res.url);
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    form.on('submit(tijiao)', function(data){
        if($("#originalPic").val()==''){
            layer.msg('请上传图片',function(){});
            return false;
        }
        layer.load();
    });
});
</script>
<? require('views/help.html');?>
</body>
</html>