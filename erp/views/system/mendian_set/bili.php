<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select if_fixed_zhekou,fixed_zhekou from user_shezhi where comId=$comId");
$levels = $db->get_results("select * from zc_release where 1=1 order by min asc,id asc");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .layui-form-switch{height:42px;line-height:42px;width:65px;}
        .layui-form-switch em{font-size:16px;width:35px;right:8px;}
        .layui-form-switch i{top:8px;width:25px;height:25px;}
        .layui-form-onswitch i{left:44px;}
    </style>
</head>
<body>
	<div class="yueshezhi">
        <div class="yueshezhi_up">
            <img src="images/mdsz_12.png" alt=""/> 分润比例
        </div>
        <div class="huiyuandengji">
            <div class="huiyuandengji_1" style="display:none;">
                <form action="?s=mendian_set&a=updateLevel" method="post" class="layui-form">
                    <div class="huiyuandengji_1_left">
                        <h2>不同等级单独设置价格</h2>
                        开启此功能产品需要单独设置不同会员级别的价格，会员等级折扣将失效。
                        <!-- <div class="huiyuandengji_1_left_01">
                            固定折扣：<input type="number" id="fixed_zhekou" min="0" step="0.01" max="10" lay-filter="required" name="fixed_zhekou" value="<?=$user_shezhi->fixed_zhekou?>" <? if($user_shezhi->if_fixed_zhekou==0){?>class="disabled" readonly="true"<? }?>>0-10之间，0和10为不打折
                        </div>
                        <div class="huiyuandengji_1_left_02">
                            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                        </div> -->
                    </div>
                    <div class="huiyuandengji_1_right" style="padding-top:30px;float:none;">
                        <input type="checkbox" name="if_fixed_zhekou" <? if($user_shezhi->if_fixed_zhekou){?>checked="true"<? }?> lay-skin="switch" lay-filter="zhekou" value="1" lay-text="开启|关闭">
                    </div>
                </form>
                <div class="clearBoth"></div>
            </div>
            <div class="huiyuandengji_2">
                <div class="huiyuandengji_2_left">
                    全部分润比例
                </div>
                <div class="huiyuandengji_2_right">
                    <div class="huiyuandengji_2_right_02">
                        <? chekurl($arr,'<a href="?m=system&s=mendian_set&a=add_bili">新 增</a>') ?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="huiyuandengji_3">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr height="43">
                        <td bgcolor="#d7ebf5" valign="middle" align="center">
                            佣金高于
                        </td>
                        <td bgcolor="#d7ebf5" valign="middle" align="center">
                            佣金低于
                        </td>
                        <!--<td bgcolor="#d7ebf5" valign="middle" align="center">-->
                        <!--    会员折扣-->
                        <!--</td>-->
                        <td bgcolor="#d7ebf5" valign="middle" align="center">
                            分润比例
                        </td>
                        <td bgcolor="#d7ebf5" valign="middle" align="center">
                            操作
                        </td>
                    </tr>
                    <? if(!empty($levels)){
                        foreach($levels as $l) {
                            ?>
                            <tr height="67" id="level_<?=$l->id?>">
                                <td bgcolor="#ffffff" class="huiyuandengji_3_line" valign="middle" align="center">
                                    <?=$l->min?>
                                </td>
                                <td bgcolor="#ffffff" class="huiyuandengji_3_line" valign="middle" align="center">
                                   <?=$l->max?>
                                </td>
                                <!--<td bgcolor="#ffffff" class="huiyuandengji_3_line" valign="middle" align="center">-->
                                <!--    <?=$l->bili?>折-->
                                <!--</td>-->
                                <td bgcolor="#ffffff" class="huiyuandengji_3_line" valign="middle" align="center">
                                    <?=$l->bili?>
                                </td>
                                <td bgcolor="#ffffff" class="huiyuandengji_3_line" valign="middle" align="center">
                                    <? chekurl($arr,'<a href="?m=system&s=mendian_set&a=add_bili&id='.$l->id.'">编辑</a>') ?>
                                    &nbsp; | &nbsp; 
                                    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=mendian_set&a=del_bili" onclick="z_confirm(\'确定要删除该分润比例吗？\',del_level,'.$l->id.');">删除</a>') ?>
                                </td>
                            </tr>
                            <?
                        }
                    }?>
                </table>
            </div>
            <div class="huiyuandengji_4">
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var layForm;
        layui.use(['form'], function(){
          var layForm = layui.form
          layForm.on('switch(zhekou)',function(data){
            var if_fixed_zhekou = this.checked?1:0;
            $.ajax({
                type: "POST",
                url: "?s=mendian_set&a=updateLevel",
                data: 'if_fixed_zhekou='+if_fixed_zhekou,
                dataType:"json",timeout :10000,
                success: function(resdata){},
                error: function() {
                    layer.closeAll();
                    layer.msg('操作失败，请检查网络', {icon: 5});
                }
            });
          });
          layForm.on('submit(tijiao)',function(data){
            layer.load();
            $.ajax({
                type: "POST",
                url: "?s=mendian_set&a=updateLevel",
                data: data.field,
                dataType:"json",timeout :10000,
                success: function(resdata){
                    layer.closeAll();
                    layer.msg('操作成功',{icon:1});
                },
                error: function() {
                    layer.closeAll();
                    layer.msg('操作失败，请检查网络', {icon: 5});
                }
            });
            return false;
          });
        });
        function del_level(id){
            layer.load();
            $.ajax({
                type: "POST",
                url: "?s=mendian_set&a=del_bili",
                data: "id="+id,
                dataType:'json',timeout : 8000,
                success: function(resdata){
                    layer.closeAll();
                    layer.msg(resdata.message);
                    if(resdata.code==1){
                        $("#level_"+id).remove();
                    }
                }
            });
        }
    </script>
    <? require('views/help.html');?>
</body>
</html>