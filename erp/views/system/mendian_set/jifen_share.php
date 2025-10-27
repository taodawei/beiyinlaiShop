<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select * from user_shezhi where comId=$comId");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .layui-form-switch{height:32px;line-height:32px;width:65px;}
        .layui-form-switch em{font-size:16px;width:35px;right:8px;}
        .layui-form-switch i{top:4px;width:25px;height:25px;}
        .layui-form-onswitch i{left:44px;}
    </style>
</head>
<body>
    <div class="yueshezhi">
        <div class="yueshezhi_up">
            <img src="images/mdsz_12.png" alt=""/>  积分规则
        </div>
        <div class="jifenguize">
            <div class="jifenguize_up">
                <ul>
                    <li>
                        <a href="?s=mendian_set&a=jifen">交易积分</a>
                    </li>
                    <li>
                        <a href="?s=mendian_set&a=jifen_jiazhi">价值管理</a>
                    </li>
                    <li>
                        <a href="?s=mendian_set&a=jifen_qiandao">每日签到</a>
                    </li>
                    <li>
                        <a href="javascript:" class="jifenguize_up_on">分享积分/抵扣金</a>
                    </li>
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="jifenguize_down">
                <div class="jifenguize_down_01">
                    <div class="jiazhiguanli">
                        <form action="?s=mendian_set&a=jifen_share&submit=1" method="post" class="layui-form">
                            <div class="jiazhiguanli_up">
                                <div class="jiaoyijifen_up_1">
                                    分享设置
                                </div>
                                <div class="jiazhiguanli_up_1" style="width:200px">
                                    <select name="if_share" lay-filter="if_jifen_pay">
                                        <option value="0">关闭</option>
                                        <option value="1" <? if($user_shezhi->if_share==1){?>selected="true"<? }?>>分享得积分</option>
                                        <? if($_SESSION['if_tongbu']==1){?>
                                            <option value="2" <? if($user_shezhi->if_share==2){?>selected="true"<? }?>>分享得抵扣金</option>
                                        <? }?>
                                    </select>
                                </div>
                                <div class="jiazhiguanli_up_3" id="jifen_div" <? if($user_shezhi->if_share!=1){echo 'style="display:none"';}?>>
                                    <div class="jiazhiguanli_up_2">
                                        分享一次得<input name="share_jifen" value="<?=$user_shezhi->share_jifen?>" lay-verify="shuzi" type="text"/>  积分
                                        每日最多得<input name="share_limit" value="<?=$user_shezhi->share_limit?>" lay-verify="shuzi" type="text"/>  积分
                                    </div>
                                </div>
                                <div class="jiazhiguanli_up_3" id="dikoujin_div" <? if($user_shezhi->if_share!=2){echo 'style="display:none"';}?>>
                                    <div class="jiazhiguanli_up_2">
                                        分享一次得<input name="share_dikoujin" value="<?=$user_shezhi->share_dikoujin?>" lay-verify="shuzi" type="text"/>  抵扣金
                                        每日最多得<input name="share_limit_dikoujin" value="<?=$user_shezhi->share_limit_dikoujin?>" lay-verify="shuzi" type="text"/>  抵扣金
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="jiazhiguanli_down">
                                <div class="jiaoyijifen_up_1">
                                    分销商积分价值
                                </div>
                                <div class="jiazhiguanli_down_2">
                                    提升自身的分销等级<br>    
                                    分销等级和自身的分润相关<b> 等级越高，分销的分润值越大</b>
                                </div>
                            </div> -->
                            <div class="yueshezhi_down_03">
                                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        layui.use(['form'], function(){
            var form = layui.form;
            form.verify({
                shuzi:function(value,item){
                    value = parseInt(value);
                    if(isNaN(value)||value<1){
                        return '字段必须是大于的正整数';
                    }
                },
                bili:function(value,item){
                    value = parseFloat(value);
                    if(isNaN(value)||value<0||value>100){
                        return '比例必须是0-100之间的数字';
                    }
                }
            });
            form.on('select(if_jifen_pay)',function(data){
                if(data.value=='0'){
                    $("#jifen_div").hide(100);
                    $("#dikoujin_div").hide(100);
                }else if(data.value=='1'){
                    $("#jifen_div").show(100);
                    $("#dikoujin_div").hide(100);
                }else{
                    $("#jifen_div").hide(100);
                    $("#dikoujin_div").show(100);
                }
            });
            form.on('submit(tijiao)', function(data){
                layer.load();
            });
        });
    </script>
    <script type="text/javascript" src="js/users/jifen.js"></script>
    <? require('views/help.html');?>
</body>
</html>