<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];

$id = (int)$request['id'];
if(!empty($id)){
    $list = $db->get_row("select * from demo_jifenlist where id=$id and comId=$comId");
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
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 积分产品管理
        <!-- <div class="bangzhulist_up_right" onclick="showHelp(328);">帮助</div> -->
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=product&a=create_jifen&id=<?=$id?>" class="layui-form">
                <input type="hidden" name="parentId" value="<?=$parentId?>">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 积分
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="number" name="jifen" value="<?=$list->jifen?>" lay-verify="required" placeholder="请输入兑换所需积分" class="yx_guanggaoadd_01_right_input" min="1"/>
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
                                <input type="text" class="layui-input" <? if(!empty($list->inventoryId)){?>value="<?=$db->get_var("select title from demo_product_inventory where id=$list->inventoryId");?>"<? }?> id="searchKehuInput" autocomplete="off" placeholder="请输入产品编号/名称">
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
                            <span>*</span> 状态
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" name="status" value="0" title="下架" <? if(empty($list->status)){?>checked="checked"<? }?>>
                            <input type="radio" name="status" value="1" title="上架" <? if($list->status==1){?>checked="checked"<? }?>>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="inventoryId" id="inventoryId" value="<?=$list->inventoryId?>">
            </form>
        </div>
    </div>
<script type="text/javascript" src="js/pdts/jifen.js"></script>
<? require('views/help.html');?>
</body>
</html>