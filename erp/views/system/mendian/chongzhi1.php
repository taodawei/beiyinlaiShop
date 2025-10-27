<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id))$mendian=$db->get_row("select title,baozhengjin from mendian where id=$id");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <a href="javascript:" onclick="history.go(-1);"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 保证金充值/扣款
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanchongzhi">
                <div class="huiyuanchongzhi_1">
                    <ul>
                        <li>
                            <div class="huiyuanchongzhi_1_left">
                                <span>*</span> 操作商家：
                            </div>
                            <div class="huiyuanchongzhi_1_right" style="position:relative;">
                                <?=empty($mendian)?'':$mendian->title?>
                                <div class="sprukuadd_03_tt_addsp_erji" id="kehuList" style="top:32px;left:0px;">
                                    <ul>
                                        <li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="huiyuanchongzhi_1_left">
                                保证金余额
                            </div>
                            <div class="huiyuanchongzhi_1_right" id="yue">
                                <?=$mendian->baozhengjin?>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
                <div class="huiyuanchongzhi_2">
                    <form method="post" id="chongzhiForm" action="?s=mendian&a=chongzhi1" class="layui-form">
                        <input type="hidden" name="mendianId" id="kehuId" value="<?=$id?>">
                        <ul>
                            <li>
                                <div class="huiyuanchongzhi_2_left">
                                    充值金额
                                </div>
                                <div class="huiyuanchongzhi_2_right">
                                    <input type="number" name="money" id="money" step="1" lay-verify="required" placeholder="输入金额">
                                </div>
                                <div class="huiyuanchongzhi_2_right">
                                    元&nbsp;&nbsp;<font color="red">（注：输入正数是充值，输入负数为扣款）</font>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="huiyuanchongzhi_2_left">
                                    备注
                                </div>
                                <div class="huiyuanchongzhi_2_right">
                                    <input type="text" maxlength="100" name="beizhu" id="beizhu" placeholder="输入备注信息" lay-verify="required" style="width:500px;">
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                                <button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
     </div>
    <script type="text/javascript">
        layui.use(['form'], function(){
            var form = layui.form;
            form.on('submit(tijiao)', function(data){
                if(data.field.userId=='0'){
                    layer.msg("请先选择会员",function(){});
                    return false;
                }
                layer.load();
                $.ajax({
                    type: "POST",
                    url: '?s=mendian&a=chongzhi1&submit=1',
                    data: data.field,
                    dataType:'json',timeout:30000,
                    success: function(resdata){
                        layer.closeAll();
                        if(resdata.code==0){
                            layer.msg(resdata.message,{icon:5});
                        }else{
                            layer.msg(resdata.message,{icon:1});
                            $("#money").val('');
                            $("#beizhu").val('');
                            history.go(-1);
                        }
                    }
                });
                return false;
            });
        });
        $(function(){
            var jishiqi;
        });
    </script>
</body>
</html>