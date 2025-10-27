<?
global $db,$request;
$url = '';
$comId = (int)$_SESSION['demo_comId'];
if(!empty($request['url']))$url=urldecode($request['url']);
?>
<style type="text/css">
._citys {width:100%; height:100%;display: inline-block; position: relative;}
._citys span {color: #cf2a51; height: 15px; width: 15px; line-height: 15px; text-align: center; border-radius: 3px; position: absolute; right: 1em; top: 10px; border: 1px solid #cf2a51; cursor: pointer;}
._citys0 {width: 100%; height: 34px; display: inline-block; padding: 0; margin: 0;}
._citys0 li {float:left; height:34px;line-height: 34px;overflow:hidden; font-size:.75rem; color: #888; width: 33%; text-align: center; cursor: pointer; }
.citySel {border-bottom: 2px solid #cf2a51; }
._citys1 {width: 100%;height:80%; display: inline-block; padding: 10px 0; overflow: auto;}
._citys1 a {height: 35px; display: block; color: #666; padding-left: 6px; margin-top: 3px; line-height: 35px; cursor: pointer; font-size:.7rem; overflow: hidden;}
._citys1 a:hover { color: #fff; background-color: #cf2a51;}
.ui-content{border: 1px solid #EDEDED;}
</style>
<div id="zhuce">
    <div class="zhuce_1">
        <a href="javascript:" onclick="go_prev_page();"><img src="/skins/default/images/denglu_14.png"/></a>
    </div>
    <div class="zhuce_2">
        手机注册
    </div>
    <div class="zhuce_3">
        <div class="zhuce_3_up">
            手机号
        </div>
        <div class="zhuce_3_down">
            <div class="zhuce_3_down_left">
                <input type="text" id="username" />
            </div>
            <div class="zhuce_3_down_right">
                <img src="/skins/default/images/denglu_15.png" onclick="$('#username').val('');" class="zhuce_3_down_right_img1"/>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
    <div class="zhuce_3">
        <div class="zhuce_3_up">
            密码
        </div>
        <div class="zhuce_3_down">
            <div class="zhuce_3_down_left">
                <input id="password" placeholder="6-16位的字母+数字" type="password"/>
            </div>
            <div class="zhuce_3_down_right">
                <img src="/skins/default/images/denglu_16.png" class="zhuce_3_down_right_img2"/>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
    <div class="zhuce_3">
        <div class="zhuce_3_up">
            验证码
        </div>
        <div class="zhuce_3_down">
            <div class="zhuce_3_down_left">
                <input type="text" id="yzm" value=""/>
            </div>
            <div class="zhuce_3_down_right">
                <a href="javascript:" id="send_btn" onclick="sendSms();">发送</a>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
    <div class="zhuce_4" <? if($comId==1183){?>style="display:none;"<? }?>>
        <div class="zhuce_4_up">
            <div class="zhuce_4_up_left">
                省市区
            </div>
            <div class="zhuce_4_up_right">
                <img src="/skins/default/images/denglu_17.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="zhuce_4_down">
            <input type="text" id="select_city" readonly="true" placeholder="选择您所在地区"/>
        </div>
    </div>
    <div class="zhuce_5">
        <a href="javascript:reg();">注册</a>
    </div>
    <div class="zhuce_6">
        注册代表您已阅读并同意<span onclick="$('#fenxiang_tc').show();">《用户服务协议》</span>的内容 
    </div>
</div>
<div class="bj" style="display:none;"></div>
<div class="fenxiang_tc" id="fenxiang_tc" onclick="$('#fenxiang_tc').hide();" style="display:none;z-index:997">
    <div class="bj" style="background-color:rgba(0,0,0,.8);"></div>
    <div class="fenxiangdiv" style="width:16rem;color: #fff;padding:1rem;text-align:left;">
        <? 
            $comId = (int)$_SESSION['demo_comId'];
            $xieyi = $db->get_var("select xieyi from demo_shezhi where comId=$comId limit 1");
            echo preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$xieyi);
        ?>
    </div>
</div>
<script type="text/javascript">
    var areaId = 0;
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/resource/scripts/cityJson.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/citySet.js"></script>
<script type="text/javascript" src="/skins/default/scripts/user/reg.js?v=1"></script>