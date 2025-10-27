<?
global $request;
$url = '';
if(!empty($request['url']))$url=urldecode($request['url']);
$_SESSION['errors'] = 1;
?>
<div id="zhuce">
    <div class="zhuce_1">
        <a href="javascript:" onclick="go_prev_page();"><img src="/skins/default/images/denglu_14.png"/></a>
    </div>
    <div class="zhuce_2">
        登录
    </div>
    <div class="zhuce_3">
        <div class="zhuce_3_up">
            手机号
        </div>
        <div class="zhuce_3_down">
            <div class="zhuce_3_down_left">
                <input id="username" type="text"/>
            </div>
            <div class="zhuce_3_down_right">
                
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
                <input id="password" type="password"/>
            </div>
            <div class="zhuce_3_down_right">
                <img src="/skins/default/images/denglu_16.png" class="zhuce_3_down_right_img2"/>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
    <div class="denglu_tijiao">
        <a href="javascript:" onclick="login();">登录</a>
    </div>
</div>
<script type="text/javascript">
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/demo/scripts/user/login.js"></script>