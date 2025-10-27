<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$channelId = (int)$request['channelId'];
if(!empty($id)){
    $banner = $db->get_row("select * from banner where id=$id and comId=$comId");
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
        <form method="post" action="?m=system&s=banner&a=addBanner&id=<?=$id?>&channelId=<?=$channelId?>" class="layui-form">
            <input type="hidden" name="parentId" value="<?=$parentId?>">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 中文标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$banner->title?>" lay-verify="required" placeholder="请输入中文标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 英文标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="en_title" value="<?=$banner->en_title?>" lay-verify="required" placeholder="请输入英文标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_right_addtupian">
                            <div class="yx_guanggaoadd_01_right_addtupian_01" id="uploadImg" style="height:auto;width:200px">
                                <? if(empty($banner->originalPic)){?>
                                    <b style="margin-top:100px;display:block;">+</b><br>上传广告图片
                                <? }else{
                                    ?><img src="<?=$banner->originalPic?>" width="200" ><?
                                }?>
                            </div>
                            <div class="yx_guanggaoadd_01_right_addtupian_02">
                                仅支持jpg,jpeg,png,bmp格式，文件小于1M，广告图片推荐尺寸:720*360像素
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 位置
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="position">
                                <option value="1" <? if($banner->position==1){?>selected="true"<? }?>>PC</option>
                                <option value="2" <? if($banner->position==2){?>selected="true"<? }?>>手机</option>
                                <option value="3" <? if($banner->position==3){?>selected="true"<? }?>>小程序</option>

                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                             链接产品
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <div class="dhd_adddinghuodan_1_right">
                              <div class="dhd_adddinghuodan_1_right_01">
                                <input type="text" class="layui-input" <? if(!empty($banner->inventoryId)){?>value="<?=$db->get_var("select title from demo_product_inventory where id=$banner->inventoryId");?>"<? }?> id="searchKehuInput" autocomplete="off" placeholder="请输入产品编号/名称">
                                <div class="sprukuadd_03_tt_addsp_erji" id="kehuList" style="top:30px;left:0px;">
                                  <ul>
                                    <li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
                                  </ul>
                                </div>
                              </div>
                              <div class="dhd_adddinghuodan_1_right_02">
                                <span></span><span></span><span></span>
                              </div>
                              <div class="clearBoth"></div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            自定义链接
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="url" id="url" style="width:300px" value="<?=$banner->url?>" placeholder="http(s)://开头" class="yx_guanggaoadd_01_right_input"/>
                            <font color="red"></font>
                        </div>
                        <div class="clearBoth"></div>
                    </li>

                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" id="originalPic" value="<?=$banner->originalPic?>">
            <input type="hidden" name="inventoryId" id="inventoryId" value="<?=$banner->inventoryId?>">
        </form>
    </div>
</div>
<script type="text/javascript" src="js/yyyx/addBanner.js"></script>
<? require('views/help.html');?>
</body>
</html>