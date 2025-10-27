<?
global $db,$request;
$url = '';
if(!empty($request['url']))$url=urldecode($request['url']);
$_SESSION['errors'] = 1;
$comId = (int)$_SESSION['demo_comId'];
$ifweixin = 0;
if($_SESSION['if_tongbu']==1 && strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false){
    $ifweixin = 1;
}
if($_SESSION['if_tongbu']==0 && strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false){
    $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
    if(!empty($weixin_set->info)){
        $ifweixin = 1;
        if($comId==1113){
            redirect('/index.php?p=8&a=wxlogin_com&urltob='.urlencode($url));
        }
    }
}
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/shouquan.css">
<div class="shouquan" id="shouquan_div" <? if($ifweixin!=1){?>style="display:none"<? }?>>
    <div class="shouquan_1">
        第三方授权登录
    </div>
    <div class="shouquan_2">    
        <a href="/index.php?p=8&a=<?=$_SESSION['if_tongbu']==1?'wxlogin':'wxlogin_com'?>&urltob=<?=urlencode($url)?>"><img src="/skins/erp_zong/images/add_1.png"/></a>
    </div>
    <div class="shouquan_3">
        授权说明您已阅读并同意<span onclick="$('#fenxiang_tc').show();">《用户服务协议》</span>的内容
    </div>
    <div class="shouquan_4">
        <a href="/index.php?p=8&a=<?=$_SESSION['if_tongbu']==1?'wxlogin':'wxlogin_com'?>&urltob=<?=urlencode($url)?>">直接授权登录</a>
        <a href="javascript:" onclick="$('#shouquan_div').hide();$('#zhuce').show();" class="shouquan_4_02">账号密码登录</a>
    </div>
    <!-- <div class="shouquan_5">
        绑定手机号，即可获取588元抵扣金、88元优惠券
    </div> 
    <div class="shouquan_6">
        <a href="javascript:" onclick="$('#shouquan_div').hide();$('#zhuce').show();">知商账号登录</a>
    </div>-->
</div>
<div id="zhuce" class="denglu" style="background:#fff;padding: 5rem 0;<? if($ifweixin==1){?>display:none<? }?>">
    <div class="denglu_1">
        <img src="<?=$_SESSION['demo_com_logo']?>"/>
    </div>
    <div class="denglu_2" style="background:none;">
        <ul>
            <li>
                <a href="javascript:" onclick="qiehuan_login(0);" class="denglu_2_on">账号密码登录</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan_login(1);">手机验证码登录</a>
            </li>
        </ul>
    </div>
    <div class="denglu_3">
        <ul>
            <li>
                <div class="denglu_3_left">
                    <img src="/skins/erp_zong/images/add_12.png"/>
                </div>
                <div class="denglu_3_right">
                    <input type="text" id="username" placeholder="请输入手机号"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="denglu_3_left">
                    <img src="/skins/erp_zong/images/add_13.png"/>
                </div>
                <div class="denglu_3_right">
                    <input type="password" id="password" placeholder="请输入密码"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li style="display:none;">
                <div class="denglu_3_left">
                    <img src="/skins/erp_zong/images/add_13.png"/>
                </div>
                <div class="denglu_3_right">
                    <input type="text" id="yzm" placeholder="请输入验证码"/>
                    <span id="send_btn" onclick="send_login_msg();">获取验证码</span>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
    </div>
    <div class="denglu_4">
        <a href="javascript:" onclick="login();"><img src="/skins/erp_zong/images/add_15.png"/></a>
    </div>
    <div class="denglu_4" style="display:none;">
        <a href="javascript:" onclick="msg_login();"><img src="/skins/erp_zong/images/add_15.png"/></a>
    </div>
    <div class="denglu_5">
        <div class="denglu_5_left">
            <a href="/?p=8&a=findMima">忘记密码?</a>
        </div>
        <div class="denglu_5_right">
            <a href="/index.php?p=8&a=reg&url=<?=urlencode($url)?>">新用户注册</a>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="fenxiang_tc" id="fenxiang_tc" onclick="$('#fenxiang_tc').hide();" style="display:none;z-index:997">
    <div class="bj" style="background-color:rgba(0,0,0,.8);"></div>
    <div class="fenxiangdiv" style="width:16rem;color: #fff;padding:1rem;text-align:left;">
        <? 
            $comId = $_SESSION['if_tongbu']==1?10:(int)$_SESSION['demo_comId'];
            $xieyi = $db->get_var("select xieyi from demo_shezhi where comId=$comId limit 1");
            echo preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$xieyi);
        ?>
    </div>
</div>
<script type="text/javascript">
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/default/scripts/user/login.js"></script>