<?
global $db,$request;
$url = '';
if(!empty($request['url']))$url=urldecode($request['url']);
$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
$yaoqing_rules = json_decode($yaoqing_rule);
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
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/shouquan.css">
<div class="bangding">
    <div class="bangding_1">
        绑定手机号
        <div class="bangding_1_left">
            <img src="/skins/erp_zong/images/add_16.png" alt=""/>
        </div>
    </div>
    <? if($_SESSION['if_tongbu']==1){?>
    <div class="bangding_2">
        注册绑定手机号赠送<?=$yaoqing_rules->z_dikoujin?>元购物抵现卡
    </div>
    <? }?>
    <div class="bangding_3">
        <ul>
            <li>
                <div class="bangding_3_left">
                    手机号
                </div>
                <div class="bangding_3_right">
                    <input type="text" id="username" placeholder="请输入手机号"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="bangding_3_left">
                    验证码
                </div>
                <div class="bangding_3_right">
                    <input type="text" id="yzm" placeholder="短信验证码"/>
                    <span id="send_btn" onclick="sendSms();">获取验证码</span>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
    </div>
    <div class="bangding_4">
        绑定代表您已阅读并同意<span onclick="$('#fenxiang_tc').show();">《用户服务协议》</span>的内容
    </div>
    <div class="bangding_5">
        <a href="javascript:" onclick="reg_wx();"><img src="/skins/erp_zong/images/add_17.png"/></a>
    </div>
    <div class="bangding_6">
        <a href="/index.php?p=8&a=reg_nobind&url=<?=urlencode($url)?>">暂不绑定</a>
    </div>
</div>
<div class="bj" style="display:none;"></div>
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
    var areaId = 0;
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/resource/scripts/cityJson.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/citySet.js"></script>
<script type="text/javascript" src="/skins/default/scripts/user/bindwx.js?v=1"></script>