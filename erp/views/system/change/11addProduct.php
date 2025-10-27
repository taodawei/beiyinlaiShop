<?
global $db,$request;

$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $list = $db->get_row("select * from kmd_change_product where id=$id ");
}
$changeId = (int)$request['changeId'];
$change = $db->get_row("select * from kmd_change where id = $changeId");
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
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> <?=$change->title?>  添加商品
        <!-- <div class="bangzhulist_up_right" onclick="showHelp(328);">帮助</div> -->
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=change&a=addProduct&tijiao=1&id=<?=$id?>" class="layui-form">
                <input type="hidden" name="changeId" value="<?=$change->id?>">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 绑定产品
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <div class="dhd_adddinghuodan_1_right" style="width:503px;">
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
                            <span>*</span> 生效时间
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="startTime" value="<?=empty($list->startTime)?'':($list->startTime=='0000-00-00'?'':$list->startTime)?>" id="startTime" placeholder="请选择生效日期" class="addhuiyuan_2_02_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 失效日期
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="endTime" value="<?=empty($list->endTime)?'':($list->endTime=='0000-00-00'?'':$list->endTime)?>" id="endTime" placeholder="请选择失效日期" class="addhuiyuan_2_02_input"/>
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
<script>
	layui.use(['laydate','form'], function(){
		  var laydate = layui.laydate
		  ,form = layui.form
		  laydate.render({
		  	elem: '#startTime'
		  	,min:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
		  });
		  
		  laydate.render({
		  	elem: '#endTime'
		  	,min:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
		  });
		 
		});
		
</script>
<? require('views/help.html');?>
</body>
</html>