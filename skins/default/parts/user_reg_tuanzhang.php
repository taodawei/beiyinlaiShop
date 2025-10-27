<?
global $db,$request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
if($_SESSION[TB_PREFIX.'user_level']>1){
    echo '<script>alert("您已经是团长了！");location.href="/index.php";</script>';
    exit;
}
if(empty($_SESSION['tuijianren'])||$request['tuijianren']==$userId){
    echo '<script>alert("链接错误！");location.href="/index.php";</script>';
    exit;
}
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
body,.wode{height:100%;}
</style>
<div class="wode">
	<div class="wode_1">
    	申请团长
        <div class="wode_1_left">
        	<img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
	<div class="shenqingtuanzhang">
    	<div class="shenqingtuanzhang_1">
        	<img src="/skins/default/images/shenqingtuanzhang_1.gif"/>
        </div>
    	<div class="shenqingtuanzhang_2">
        	<ul>
        		<li>
                	<div class="shenqingtuanzhang_2_left">
                    	<img src="/skins/default/images/shenqingtuanzhang_11.png"/>
                    </div>
                	<div class="shenqingtuanzhang_2_right">
                    	<input type="text" id="name" placeholder="请输入您的真实姓名"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? if(empty($userId)){?>
                <li>
                	<div class="shenqingtuanzhang_2_left">
                    	<img src="/skins/default/images/shenqingtuanzhang_12.png"/>
                    </div>
                	<div class="shenqingtuanzhang_2_right">
                    	<input type="text" id="username" placeholder="请输入您的手机号"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="shenqingtuanzhang_2_left">
                    	<img src="/skins/default/images/shenqingtuanzhang_13.png"/>
                    </div>
                	<div class="shenqingtuanzhang_2_right">
                    	<input type="password" id="password" placeholder="6-16位的字母+数字"/>
                    </div>
                    <div class="shenqingtuanzhang_2_mima">
                    	<img src="/skins/default/images/denglu_16.png"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? }else{?>
                <li>
                    <div class="shenqingtuanzhang_2_left">
                        <img src="/skins/default/images/shenqingtuanzhang_12.png"/>
                    </div>
                    <div class="shenqingtuanzhang_2_right">
                        <input type="text" id="phone" placeholder="请输入您的手机号"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <? }?>
                <li>
                	<div class="shenqingtuanzhang_2_left">
                    	<img src="/skins/default/images/shenqingtuanzhang_14.png"/>
                    </div>
                	<div class="shenqingtuanzhang_2_right">
                    	<input type="text" id="wxh" placeholder="请输入您的微信号"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? if(empty($userId)){?>
                <li>
                    <div class="shenqingtuanzhang_2_left">
                        <img src="/skins/default/images/shenqingtuanzhang_18.png"/>
                    </div>
                    <div class="shenqingtuanzhang_2_right">
                        <input type="text" id="select_city" placeholder="选择您所在地区"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                	<div class="shenqingtuanzhang_2_left">
                    	<img src="/skins/default/images/shenqingtuanzhang_15.png"/>
                    </div>
                	<div class="shenqingtuanzhang_2_right">
                    	<input type="text" id="yzm" placeholder="请输入验证码"/>
                    </div>
                    <div class="shenqingtuanzhang_2_yanzhengma">
                    	<a href="javascript:" id="send_btn" onclick="sendSms();">发送验证码</a>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? }?>
        	</ul>
        </div>
    	<div class="shenqingtuanzhang_3">
        	<a href="javascript:<? if(empty($userId)){?>reg();<? }else{?>reg1();<? }?>">立即申请</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var areaId = 0;
    var userId = '<?=$userId?>';
</script>
<script type="text/javascript" src="/skins/demo/scripts/cityJson.js"></script>
<script type="text/javascript" src="/skins/demo/scripts/citySet.js"></script>
<script type="text/javascript" src="/skins/demo/scripts/user/reg_tuanzhang.js"></script>