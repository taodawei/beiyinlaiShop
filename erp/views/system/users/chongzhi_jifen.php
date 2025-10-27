<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id))$user=$db->get_row("select username,nickname,jifen from users where id=$id and comId=10");
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
            <a href="javascript:" onclick="history.go(-1);"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员积分操作
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanchongzhi">
                <div class="huiyuanchongzhi_1">
                    <ul>
                        <li>
                            <div class="huiyuanchongzhi_1_left">
                                <span>*</span> 会员
                            </div>
                            <div class="huiyuanchongzhi_1_right" style="position:relative;">
                                <input type="text" id="searchKehuInput" value="<?=empty($user)?'':$user->username.'('.$user->nickname.')'?>" placeholder="请输入会员手机号">
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
                                可用积分
                            </div>
                            <div class="huiyuanchongzhi_1_right" id="yue">
                                <?=$user->jifen?>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
                <div class="huiyuanchongzhi_2">
                    <form method="post" id="chongzhiForm" action="?m=system&s=users&a=chongzhi" class="layui-form">
                        <input type="hidden" name="userId" id="kehuId" value="<?=$id?>">
                        <input type="hidden" name="tousuId" id="tousuId" value="<?=$request['tousuId']?>">
                        <ul>
                            <li>
                                <div class="huiyuanchongzhi_2_left">
                                    积分变动
                                </div>
                                <div class="huiyuanchongzhi_2_right">
                                    <input type="number" name="money" id="money" step="1" lay-verify="required" placeholder="输入积分">
                                </div>
                                <div class="huiyuanchongzhi_2_right">
                                    &nbsp;&nbsp;<font color="red">（注：输入正数是充值，输入负数为扣除）</font>
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
                                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
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
                    url: '?m=system&s=users&a=chongzhi_jifen&submit=1',
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
                            <? if($request['tousuId']){?>
                                location.href='?m=system&s=users&a=tousu';
                            <? }?>
                        }
                    }
                });
                return false;
            });
        });
        $(function(){
            var jishiqi;
            $(document).bind('click',function(){
                $("#kehuList").hide();
                if($("#kehuId").val()==0){
                    $("#searchKehuInput").val('');
                }
            });
            $('#searchKehuInput').bind('input propertychange', function() {
                $("#kehuId").val(0);
                clearTimeout(jishiqi);
                var val = $(this).val();
                jishiqi=setTimeout(function(){getKehuList(val);},500);
            });
        });
        function getKehuList(keyword){
            $("#kehuList ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
            $.ajax({
                type: "POST",
                url: "?m=system&s=users&a=searchUsers&jifen=1",
                data: "keyword="+keyword,
                dataType:'text',timeout : 10000,
                success: function(resdata){
                    $("#kehuList").show();
                    $("#kehuList ul").html(resdata);
                }
            });
        }
        function selectKehu(id,title,yue){
            $("#kehuId").val(id);
            $("#searchKehuInput").val(title);
            $("#yue").html(yue);
        }
    </script>
    <? require('views/help.html');?>
</body>
</html>