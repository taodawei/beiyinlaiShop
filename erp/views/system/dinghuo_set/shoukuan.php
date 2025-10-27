<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$weixin = $db->get_row("select * from demo_kehu_pay where comId=$comId and type=1 limit 1");
if(!empty($weixin->info)){
    $weixinInfo = json_decode($weixin->info,true);
}
$alipay = $db->get_row("select * from demo_kehu_pay where comId=$comId and type=2 limit 1");
if(!empty($alipay->info)){
    $alipayInfo = json_decode($alipay->info,true);
}
$weixin1 = $db->get_row("select * from demo_kehu_pay where comId=$comId and type=3 limit 1");
if(!empty($weixin1->info)){
    $weixinInfo1 = json_decode($weixin1->info,true);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/duanxin.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/dinghuo_level.js"></script>
</head>
<body>
    <div class="kehushezhi">
        <div class="kehushezhi_01">
            <img src="images/duanxin_20.png"/> 收款帐户设置
        </div>
        <div class="shoukuanzhanghushezhi">
            <div class="shoukuanzhanghushezhi_01">
                <ul>
                    <li>
                        <a href="javascript:" class="shoukuanzhanghushezhi_01_on">在线支付</a>
                    </li>
                    <!--<li>-->
                    <!--    <a href="?m=system&s=dinghuo_set&a=xianxia">线下支付</a>-->
                    <!--</li>-->
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="shoukuanzhanghushezhi_02">
                <div class="onlinepay">
                    <div class="onlinepay1">
                        <div class="onlinepay_01 onlinepay_01_wx">
                            <div class="onlinepay_01_left">
                                <img src="images/duanxin_23.png"/>
                            </div>
                            <div class="onlinepay_01_right">
                                <a href="javascript:" class="onlinepay_01_right_wx">设置收款帐号</a>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="onlinepay_02">
                            需您开通微信服务号，并与腾讯公司签约收款服务；交易手续费由腾讯公司向收款方收取。
                        </div>
                        <div class="onlinepay_03">
                            <div class="onlinepay_03_left">
                                <a href="https://kf.qq.com/faq/170830jimmaa170830B7F7NJ.html" target="_blank"><img src="images/duanxin_25.png"/> 开通向导</a>
                            </div>
                            <div class="onlinepay_03_right">
                                <? if(empty($weixin)){?>未开通<? }else if($weixin->status==1){echo '<span style="color:green">已启用</span>';}else{echo '<span style="color:red">已禁用</span>';}?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                    </div>
                    <div class="onlinepay1" style="display:none;">
                        <div class="onlinepay_01 onlinepay_01_wx">
                            <div class="onlinepay_01_left">
                                <img src="images/duanxin_223.png"/>
                            </div>
                            <div class="onlinepay_01_right">
                                <a href="javascript:" class="onlinepay_01_right_xcx">设置收款帐号</a>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="onlinepay_02">
                            需您开通微信小程序，并与腾讯公司签约收款服务；交易手续费由腾讯公司向收款方收取。
                        </div>
                        <div class="onlinepay_03">
                            <div class="onlinepay_03_left">
                                <a href="https://kf.qq.com/faq/170830jimmaa170830B7F7NJ.html" target="_blank"><img src="images/duanxin_25.png"/> 开通向导</a>
                            </div>
                            <div class="onlinepay_03_right">
                                <? if(empty($weixin1)){?>未开通<? }else if($weixin1->status==1){echo '<span style="color:green">已启用</span>';}else{echo '<span style="color:red">已禁用</span>';}?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                    </div>
                    <div class="onlinepay1">
                        <div class="onlinepay_01 onlinepay_01_zfb">
                            <div class="onlinepay_01_left">
                                <img src="images/duanxin_24.png"/>
                            </div>
                            <div class="onlinepay_01_right">
                                <a href="javascript:" class="onlinepay_01_right_zfb">设置收款帐号</a>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="onlinepay_02">
                            需开通企业支付宝账号，仅支持以企业对公账户作为收款账户。交易手续费由支付宝公司向收款方收取。
                        </div>
                        <div class="onlinepay_03">
                            <div class="onlinepay_03_left">
                                <a href="https://mrchportalweb.alipay.com/settling/selfhelp/accessGuide.htm" target="_blank"><img src="images/duanxin_25.png"/> 开通向导</a>
                            </div>
                            <div class="onlinepay_03_right">
                                <? if(empty($alipay)){?>未开通<? }else if($alipay->status==1){echo '<span style="color:green">已启用</span>';}else{echo '<span style="color:red">已禁用</span>';}?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            </div>
        </div>
    </div>
    <!--微信支付设置-->
    <div class="weixinzhifu">
        <div class="weixinzhifu_01">
            微信签约信息
        </div>
        <div class="weixinzhifu_02">
            <img src="images/duanxin_26.png"/> 设置账号前，请确保您已在微信平台完成相关设置。 <a href="javascript:">查看帮助</a>
        </div>
        <form action="?m=system&s=dinghuo_set&a=setWeixin" method="post" class="layui-form">
        <div class="weixinzhifu_03">
            <ul>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> AppID（应用ID）
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="appid" value="<?=$weixinInfo['appid']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：公众平台-开发者中心
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> （Mchid）商户号   
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="mch_id" value="<?=$weixinInfo['mch_id']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：商户平台-API安全-API密钥
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> （Key）商户支付密钥
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="key" value="<?=$weixinInfo['key']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：商户平台-API安全-API密钥
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> AppSecret（应用密钥）
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="appsecret" value="<?=$weixinInfo['appsecret']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：公众平台-开发者中心
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        KEY证书
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" readonly="true" name="sslkey" value="<?=$weixinInfo['sslkey']?>" id="sslkey" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        退款时使用，获取路径：商户平台-api安全
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        CERT证书
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" readonly="true" name="sslcert" value="<?=$weixinInfo['sslcert']?>" id="sslcert" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        退款时使用，获取路径：商户平台-api安全
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left" style="line-height:45px;height:38px;">
                        状态
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="radio" name="status" value="1" <? if($weixin->status==1){?>checked<? }?> title="启用"/>&nbsp;&nbsp; <input type="radio" name="status" <? if(!empty($weixin)&&$weixin->status==0){?>checked<? }?> value="0" title="停用" />
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="kehujibieshezhi_add_03">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
            <button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
        </div>
    </form>
    </div>
    <!--微信支付设置结束-->
    <!--微信小程序支付设置-->
    <div class="weixinzhifu1">
        <div class="weixinzhifu_01">
            微信小程序签约信息
        </div>
        <div class="weixinzhifu_02">
            <img src="images/duanxin_26.png"/> 设置账号前，请确保您已在微信平台完成相关设置。 <a href="javascript:">查看帮助</a>
        </div>
        <form action="?m=system&s=dinghuo_set&a=setWeixin1" method="post" class="layui-form">
        <div class="weixinzhifu_03">
            <ul>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> AppID（应用ID）
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="appid" value="<?=$weixinInfo1['appid']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：公众平台-开发者中心
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> （Mchid）商户号   
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="mch_id" value="<?=$weixinInfo1['mch_id']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：商户平台-API安全-API密钥
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> （Key）商户支付密钥
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="key" value="<?=$weixinInfo1['key']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：商户平台-API安全-API密钥
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        <span>*</span> AppSecret（应用密钥）
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" name="appsecret" value="<?=$weixinInfo1['appsecret']?>" lay-verify="required" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        获取路径：公众平台-开发者中心
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        KEY证书
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" readonly="true" name="sslkey" value="<?=$weixinInfo1['sslkey']?>" id="sslkey1" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        退款时使用，获取路径：商户平台-api安全
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left">
                        CERT证书
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="text" readonly="true" name="sslcert" value="<?=$weixinInfo1['sslcert']?>" id="sslcert1" class="layui-input"/>
                    </div>
                    <div class="weixinzhifu_03_right">
                        退款时使用，获取路径：商户平台-api安全
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="weixinzhifu_03_left" style="line-height:45px;height:38px;">
                        状态
                    </div>
                    <div class="weixinzhifu_03_right">
                        <input type="radio" name="status" value="1" <? if($weixin1->status==1){?>checked<? }?> title="启用"/>&nbsp;&nbsp; <input type="radio" name="status" <? if(!empty($weixin1)&&$weixin1->status==0){?>checked<? }?> value="0" title="停用" />
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="kehujibieshezhi_add_03">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao2">立即提交</button>
            <button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
        </div>
    </form>
    </div>
    <!--微信支付设置结束-->
    <!--支付宝支付设置-->
    <div class="zhifubaozhifu">
        <div class="weixinzhifu_01">
            支付宝签约信息
        </div>
        <form action="?m=system&s=dinghuo_set&a=setAlipay" method="post" class="layui-form">
        <div class="zhifubaozhifu01">
            <div class="zhifubaozhifu01_left">
                <div class="weixinzhifu_02">
                    <img src="images/duanxin_26.png"/> 设置账号前，请确保您已在支付宝平台完成相关设置。 <a href="#">查看帮助</a>
                </div>
                <div class="weixinzhifu_03">
                    <ul>
                        <li>
                            <div class="weixinzhifu_03_left1">
                                <span>*</span> 支付宝企业账户
                            </div>
                            <div class="weixinzhifu_03_right1">
                                <input type="text" name="account" value="<?=$alipayInfo['account']?>" lay-verify="required" class="layui-input"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="weixinzhifu_03_left1">
                                <span>*</span> 合作者身份（Partner ID）
                            </div>
                            <div class="weixinzhifu_03_right1">
                                <input type="text" name="partnerId" value="<?=$alipayInfo['partnerId']?>" lay-verify="required" class="layui-input"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="weixinzhifu_03_left1">
                                <span>*</span> 安全校验码（Key）
                            </div>
                            <div class="weixinzhifu_03_right1">
                                <input type="text" name="key" value="<?=$alipayInfo['key']?>" lay-verify="required" class="layui-input"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="weixinzhifu_03_left1">
                                <span>*</span> 商户RSA私钥
                            </div>
                            <div class="weixinzhifu_03_right1">
                                <textarea name="private_key" lay-verify="required" class="layui-textarea"><?=$alipayInfo['private_key']?></textarea>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="weixinzhifu_03_left1">
                                <span>*</span> 支付宝RSA公钥
                            </div>
                            <div class="weixinzhifu_03_right1">
                                <textarea name="alipay_public_key" lay-verify="required" class="layui-textarea"><?=$alipayInfo['alipay_public_key']?></textarea>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="weixinzhifu_03_left">
                                状态
                            </div>
                            <div class="weixinzhifu_03_right">
                                <input type="radio" name="status" value="1" <? if($alipay->status==1){?>checked<? }?> title="启用"/>&nbsp;&nbsp;<input type="radio" name="status" <? if(!empty($alipay)&&$alipay->status==0){?>checked<? }?> value="0" title="停用" />
                            </div>                
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="zhifubaozhifu01_right">
                <div class="zhifubaozhifu01_right_01">
                    如何设置支付宝签约信息?
                    <br>1、访问支付宝商户服务中心（b.alipay.com），用您的签约支付宝账号登陆。
                    <br>2、在“我的商家服务”中，点击“查询 PID、Key” ，将查询的相应信息填写到上面相应输入框中。
                    <br>3、RSA秘钥请联系客服帮助生成
                </div>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="kehujibieshezhi_add_03">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao1">立即提交</button>
            <button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
        </div>
        </form>
    </div>
    <!--支付宝支付设置结束-->
    <script type="text/javascript">
        layui.use(['form','upload'], function(){
            var form = layui.form,upload=layui.upload;
            upload.render({
                elem: '#sslkey'
                ,url: '?m=system&s=upload&a=upload_sslkey&type=key'
                ,accept:'file'
                ,exts:'pem'
                ,before:function(){
                  layer.load();
                }
                ,done: function(res){
                  layer.closeAll('loading');
                  if(res.code > 0){
                    return layer.msg(res.msg);
                  }else{
                    $("#sslkey").val(res.url);
                  }
                }
                ,error: function(){
                  layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            upload.render({
                elem: '#sslcert'
                ,url: '?m=system&s=upload&a=upload_sslkey&type=cert'
                ,accept:'file'
                ,exts:'pem'
                ,before:function(){
                  layer.load();
                }
                ,done: function(res){
                  layer.closeAll('loading');
                  if(res.code > 0){
                    return layer.msg(res.msg);
                  }else{
                    $("#sslcert").val(res.url);
                  }
                }
                ,error: function(){
                  layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            upload.render({
                elem: '#sslkey1'
                ,url: '?m=system&s=upload&a=upload_sslkey&type=key1'
                ,accept:'file'
                ,exts:'pem'
                ,before:function(){
                  layer.load();
                }
                ,done: function(res){
                  layer.closeAll('loading');
                  if(res.code > 0){
                    return layer.msg(res.msg);
                  }else{
                    $("#sslkey1").val(res.url);
                  }
                }
                ,error: function(){
                  layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            upload.render({
                elem: '#sslcert1'
                ,url: '?m=system&s=upload&a=upload_sslkey&type=cert1'
                ,accept:'file'
                ,exts:'pem'
                ,before:function(){
                  layer.load();
                }
                ,done: function(res){
                  layer.closeAll('loading');
                  if(res.code > 0){
                    return layer.msg(res.msg);
                  }else{
                    $("#sslcert1").val(res.url);
                  }
                }
                ,error: function(){
                  layer.msg('上传失败，请重试', {icon: 5});
                }
            });
        });
        $(function(){
            $(".onlinepay_01_right_wx").click(function(){
                $(".weixinzhifu").css({"top":"129px","opacity":1,"visibility":"visible"});
            });
            $(".onlinepay_01_right_xcx").click(function(){
                $(".weixinzhifu1").css({"top":"129px","opacity":1,"visibility":"visible"});
            });
            $(".onlinepay_01_right_zfb").click(function(){
                $(".zhifubaozhifu").css({"top":"129px","opacity":1,"visibility":"visible"});
            });
        });
        function quxiao(){
            $(".weixinzhifu").css({"top":"119px","opacity":0,"visibility":"hidden"});
            $(".weixinzhifu1").css({"top":"119px","opacity":0,"visibility":"hidden"});
            $(".zhifubaozhifu").css({"top":"119px","opacity":0,"visibility":"hidden"});
        }
    </script>
    <? require('views/help.html');?>
</body>
</html>