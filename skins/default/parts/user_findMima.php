<?
global $db,$request;
$url = '';
if(!empty($request['url']))$url=urldecode($request['url']);
?>
<div id="zhuce">
    <div class="zhuce_1">
        <a href="javascript:" onclick="go_prev_page();"><img src="/skins/default/images/denglu_14.png"/></a>
    </div>
    <div class="zhuce_2">
        找回密码
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
    <div class="zhuce_3">
        <div class="zhuce_3_up">
            新密码
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
    <div class="zhuce_5">
        <a href="javascript:reg();">重置</a>
    </div>
</div>
<script type="text/javascript">
    var areaId = 0;
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/default/scripts/user/findMima.js"></script>