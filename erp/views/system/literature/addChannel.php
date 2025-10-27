<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$channelId = (int)$request['channelId'];
if(!empty($id)){
    $banner = $db->get_row("select * from banner_channel where id=$id and comId=$comId");
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
    <style type="text/css">.yx_guanggaoadd_01_left{width:120px;}</style>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 新增首页自定义模块
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=banner&a=addChannel&id=<?=$id?>&channelId=<?=$channelId?>" class="layui-form">
                <input type="hidden" name="parentId" value="<?=$parentId?>">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 模块名称
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$banner->title?>" lay-verify="required" placeholder="请输入模块名称" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            副标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="remark" value="<?=$banner->remark?>" placeholder="副标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            是否显示模块标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="show_title" value="1" title="显示" <? if($banner->show_title==1){?>checked="true"<? }?> />
                            <input type="radio" name="show_title" value="0" title="不显示" <? if($banner->show_title==0){?>checked="true"<? }?> />
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            每行显示图片数
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="shuliang">
                                <? for($i=1;$i<6;$i++){?>
                                    <option value="<?=$i?>" <? if($banner->shuliang==$i){?>selected="true"<? }?>><?=$i?>个</option>
                                <? }?>
                                <option value="6" <? if($banner->shuliang==6){?>selected="true"<? }?>>左一右二(共三张图片)</option>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            是否显示图片标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="show_img_title" value="1" title="显示" <? if($banner->show_img_title==1){?>checked="true"<? }?> />
                            <input type="radio" name="show_img_title" value="0" title="不显示" <? if($banner->show_img_title==0){?>checked="true"<? }?> />
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" id="originalPic" value="123231">
            </form>
        </div>
    </div>
<script type="text/javascript" src="js/yyyx/addBanner.js"></script>
<? require('views/help.html');?>
</body>
</html>