<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$dazhuanpan_id = (int)$request['dazhuanpan_id'];
if(!empty($id)){
    $banner = $db->get_row("select * from demo_dazhuanpan_prize where id=$id");
    $dazhuanpan_id = (int)$banner->dazhuanpan_id;
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
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 奖品新增
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=yingxiao&a=addGift&id=<?=$id?>&dazhuanpan_id=<?=$dazhuanpan_id?>" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="name" value="<?=$banner->name?>" lay-verify="required" placeholder="请输入标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 概率
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" name="chance" value="<?=$banner->chance?$banner->chance/100:1?>" min="1" max="99" lay-verify="required" placeholder="请输入概率" class="yx_guanggaoadd_01_right_input" style="width: 100px;"/>%
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 数量
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" name="num" value="<?=$banner->num?$banner->num:0;?>" min="0" lay-verify="required" placeholder="请输入数量" class="yx_guanggaoadd_01_right_input" style="width: 100px;"/><span style="color:red;">&nbsp;&nbsp;PS:奖品数量为0时奖品只会显示在抽奖页面中，不会有人中奖</span>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 排序(降序)
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" name="ordering" value="<?=$banner->ordering?$banner->ordering:0;?>" min="0" lay-verify="required" placeholder="" class="yx_guanggaoadd_01_right_input" style="width: 100px;"/><span style="color:red;">&nbsp;&nbsp;PS:排序大的优先计算概率</span>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_right_addtupian">
                            <div class="yx_guanggaoadd_01_right_addtupian_01" id="uploadImg" style="height:100px;width:100px">
                                <? if(empty($banner->image)){?>
                                    <b style="margin-top:20px;display:block;">+</b><br>上传图片
                                <? }else{
                                    ?><img src="<?=$banner->image?>" width="100" height="100"><?
                                }?>
                            </div>
                            <div class="yx_guanggaoadd_01_right_addtupian_02">
                                仅支持jpg,jpeg,png,bmp格式，文件小于1M，广告图片尺寸100*100像素
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 内容
                        </div>
                        <div class="yx_guanggaoadd_01_right" style="width:500px;">
                            <textarea name="content" class="layui-textarea"><?=$banner->content?></textarea>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" id="originalPic" value="<?=$banner->image?>">
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
        ,url: '?m=system&s=upload&a=upload&width=100&height=100'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadImg').html('<img src="'+res.url+'" width="100" height="100">');
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
</script>
<? require('views/help.html');?>
</body>
</html>