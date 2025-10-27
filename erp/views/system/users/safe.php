<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$user = $db->get_row("select username,nickname from users where id=$id and comId=$comId");
$username = $user->username;
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
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <? require('views/system/users/head.php')?>
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_zhanghuanquan">
                        <div class="hyxx_zhanghuanquan_1">
                            <div class="hyxx_zhanghuanquan_1_up">
                                账户信息
                            </div>
                            <div class="hyxx_zhanghuanquan_1_down">
                                <div class="hyxx_zhanghuanquan_1_down_left">
                                    账号名称
                                </div>
                                <div class="hyxx_zhanghuanquan_1_down_right">
                                    <div class="hyxx_zhanghuanquan_1_down_right_01">
                                        <?=$username?>
                                    </div>
                                    <!--<div class="hyxx_zhanghuanquan_1_down_right_02">-->
                                    <!--    <a href="javascript:" onclick="editPass();" class="hyxx_zhanghuanquan_chongzhimima">重置密码</a>-->
                                    <!--</div>-->
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                        </div>
                        <div class="hyxx_zhanghuanquan_1">
                            <div class="hyxx_zhanghuanquan_1_up">
                                支付密码
                            </div>
                            <div class="hyxx_zhanghuanquan_1_down">
                                <div class="hyxx_zhanghuanquan_1_down_left">
                                    支付密码
                                </div>
                                <div class="hyxx_zhanghuanquan_1_down_right">
                                    <a href="javascript:" onclick="editPayPass();" class="hyxx_zhanghuanquan_xiugaimima">修改支付密码</a>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hyxx_zhanghuanquan_chongzhimima_tc" id="edit_mima_div">
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_1">
            重置密码
        </div>
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_2">
            <ul>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 帐号
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="text" value="<?=$username?>" disabled/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 密码
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="password" id="password" style="width:150px;float:left;">
                        <div class="mimaguize">
                            <div class="mimaguize_01" style="display:none" id="yz_password_qd">
                                <span style="background-color:#ff8181;">弱</span><span style="background-color:#e1e1e1;">中</span><span style="background-color:#e1e1e1;">强</span>
                            </div>
                            <div class="mimaguize_02" id="yz_password">
                                密码为6-16位英文字母、数字、下划线组合！
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 重复密码
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="password" id="repass" style="width:150px;float:left;" onblur="checkRepass();">
                        <div id="yz_repass" style="display:inline-block;margin-left:10px;line-height:35px;"></div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_3">
            <a href="javascript:" onclick="updatePass(<?=$id?>);" class="hyxx_jibenziliao_3_01">保 存</a> <a href="javascript:hideDiv('edit_mima_div');" class="hyxx_jibenziliao_3_02">取  消</a>
        </div>
    </div>
    <div class="hyxx_zhanghuanquan_chongzhimima_tc" id="edit_pay_div">
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_1">
            支付密码设置
        </div>
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_2">
            <ul>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 帐号
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="text" value="<?=$username?>" disabled/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 支付密码
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="password" id="password1" style="width:150px;float:left;">
                        <div class="mimaguize">
                            <div class="mimaguize_01" style="display:none" id="yz_password_qd1">
                                <span style="background-color:#ff8181;">弱</span><span style="background-color:#e1e1e1;">中</span><span style="background-color:#e1e1e1;">强</span>
                            </div>
                            <div class="mimaguize_02" id="yz_password1">
                                密码为6-16位英文字母、数字、下划线组合！
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_left">
                        <span>*</span> 重复密码
                    </div>
                    <div class="hyxx_zhanghuanquan_chongzhimima_tc_2_right">
                        <input type="password" id="repass1" style="width:150px;float:left;" onblur="checkRepass1();">
                        <div id="yz_repass1" style="display:inline-block;margin-left:10px;line-height:35px;"></div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="hyxx_zhanghuanquan_chongzhimima_tc_3">
            <a href="javascript:" onclick="updatePass1(<?=$id?>);" class="hyxx_jibenziliao_3_01">保 存</a> <a href="javascript:hideDiv('edit_pay_div');" class="hyxx_jibenziliao_3_02">取  消</a>
        </div>
    </div>
    <script type="text/javascript" src="js/users/safe.js"></script>
    <? require('views/help.html');?>
</body>
</html>