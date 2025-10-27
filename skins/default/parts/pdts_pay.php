<?
global $db,$request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
/*if($_SESSION['if_tongbu']==1){
    $db_service = getCrmDb();
    $comId = 10;
    $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}*/
$id = (int)$request['id'];
$order = $db->get_row("select * from demo_pdt_order where id=$id");
$order_comId = $order->comId;
if($order->status!=-5){
    die('<script>alert("该订单状态不能进行支付");location.href="/index.php?p=22&a=orders";</script>');
}
$daizhifu = getXiaoshu($order->price-$order->price_payed,2);
$u = $db->get_row("select money,jifen,payPass from users where id=$userId");
if($comId==10){
    $db_service = getCrmDb();
    $u = $db_service->get_row("select money,jifen,payPass from demo_user where id=$userId");
}
$yue = $u->money;
$ifweixin = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=1 limit 1");
$ifalipay = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=2 limit 1");
$url = urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?'.$_SERVER["QUERY_STRING"]);
?>
<link href="/skins/default/styles/wode.css" rel="stylesheet" type="text/css">
<div class="zhifu">
	<div class="zhifu_1">
    	支付方式
        <div class="zhifu_1_left" onclick="location.href='/index.php?p=22&a=orders&scene=0';">
        	<img src="/skins/default/images/fanhui_1.png" />
        </div>
    </div>
    <div class="zhifu_3">
        <div class="zhifu_3_up">
            支付方式
        </div>
        <div class="zhifu_3_down">
            <ul>
                <?
                if($_SESSION['if_tongbu']==1){
                    ?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="yibao" name="pay_type" checked="true" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_12.png" /> 微信支付
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="yibao_k" name="pay_type" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/erp_zong/images/yinlian.png" /> 银行卡快捷支付(仅首次支付需填写银行卡信息)
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                }
                if($ifweixin==1 && $_SESSION['if_tongbu']!=1){?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="weixin" name="pay_type" checked="true" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_12.png" /> 微信支付
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <? }
                if($ifalipay && $_SESSION['if_tongbu']!=1){?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="alipay" name="pay_type" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_13.png" /> 支付宝支付
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <? }
                if(empty($pay_json['lipinka']['price']) && empty($price_json['yhq']) && empty($price_json['lingyuangou'])){
                ?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="yue" onclick="hide_lipinka();<? if(empty($u->payPass)){?>show_tishi();<? }?>" <? if($yue<$order->price-$order->price_payed){?>disabled="true"<? }?> name="pay_type" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_14.png" /> 余额支付
                        </div>
                        <div class="zhifu_3_down_right">
                            账户余额：￥<?=$yue?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <? 
                }
                if(!empty($gift_cards) && $_SESSION['if_tongbu']!=1){
                    ?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="gift_card" name="pay_type" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_15.png" /> 礼品卡支付
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                }?>
            </ul>
        </div>
    </div>
	<div class="zhifu_2">
    	<div class="zhifu_2_left">
        	订单金额
        </div>
    	<div class="zhifu_2_right">
        	<?=$daizhifu?>元
        </div>
    	<div class="clearBoth"></div>
    </div>
    <!-- <? 
    if($_SESSION['if_tongbu']==1 && !empty($gift_cards) && $kedi>0){
        ?>
        <div class="zhifu_2">
            <div class="zhifu_2_left">
                礼品卡抵现
            </div>
            <div class="zhifu_2_right" style="font-size:.75rem">
                <select id="gift_id" style="border:1px solid #ccc;border-radius:3px;-webkit-appearance:menulist;height:2.375rem;width:12rem;">
                    <option value="0">不使用</option>
                    <? if(!empty($gift_cards)){
                        $order_price = $order->price-$order->price_payed;
                        foreach ($gift_cards as $card) {
                            $ke_dixian = $kedi>$card->yue?$card->yue:$kedi;
                            ?><option value="<?=$card->id?>" data-kedi="<?=$ke_dixian?>"><?=$card->cardId?>(可抵：<?=$kedi?> 余额:<?=$card->yue?> )</option><?
                        }
                    }?>
                </select>
            </div>
            <div class="clearBoth"></div>
        </div>
        <?
    }else if($_SESSION['if_tongbu']!=1){
        if($max_money>0 && $order->price_payed==0){?>
        <div class="zhifu_2">
            <div class="zhifu_2_left">
                <input type="checkbox" value="1" id="if_jifen" name="if_jifen" > 积分抵现
            </div>
            <div class="zhifu_2_right" style="font-size:.75rem">￥<?=$max_money?>(消耗<?=$need_jifen?>积分)</div>
            <div class="clearBoth"></div>
        </div>
        <? }
    }?> -->
	
	<div class="zhifu_4">
    	<a href="javascript:" onclick="pay();">确认</a>
    </div>
</div>
<div class="shouhuodizhi_queren_tc" id="zhifu_div" style="display:none;">
    <div class="shouhuodizhi_queren_bj" onclick="$('#zhifu_div').hide();"></div>
    <div class="shouhuodizhi_queren" style="height:13.5rem;">
        <div class="wode_1" style="background:none;">输入支付密码</div>
        <div class="zhifufangshi_zhifu_2">
            支付金额<h2>¥<?=$daizhifu?></h2>
        </div>
        <div class="duanxinyanzheng" style="padding-top:.25rem;">
            <div class="duanxinyanzheng_2">
                <span>
                    <input type="text" id="zhifumm" maxlength="6">
                    <i></i><i></i><i></i><i></i><i></i><i></i>
                </span>
            </div>
            <div style="height:1rem;"></div>
            <div class="duanxinyanzheng_4">
                <a href="javascript:yue_pay();">确认</a>
            </div>
        </div>
        <div class="zhifufangshi_zhifu_5">
            <a href="/index.php?p=8&a=editzfpwd&url=<?=$url?>">忘记密码？</a>
        </div>
    </div>
</div>
<div class="zhifufangshi_weishezhimima_tc" id="weizhifu_tishi" style="display:none;">
    <div class="bj">
    </div>
    <div class="zhifufangshi_weishezhimima">
        <div class="zhifufangshi_weishezhimima_up">
            <img src="/skins/default/images/a921.png"/><br>尚未设置支付密码
        </div>
        <div class="zhifufangshi_weishezhimima_down">
            <ul>
                <li>
                    <a href="javascript:" onclick="$('#weizhifu_tishi').hide();">取消</a>
                </li>
                <li>
                    <a href="/index.php?p=8&a=editzfpwd&url=<?=$url?>" class="zhifufangshi_weishezhimima_down_on">设置</a>
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
    </div>
</div>
<? if($_SESSION['if_tongbu']!=1){?>
<div class="shouhuodizhi_queren_tc" id="gift_div" style="display:none;">
    <div class="shouhuodizhi_queren_bj" onclick="$('#gift_div').hide();"></div>
    <div class="shouhuodizhi_queren" style="height:14rem;">
        <div class="wode_1" style="background:none;text-align:left;padding-left: .65rem;">选择礼品卡：</div>
        <div class="duanxinyanzheng" style="padding-top:.3rem;text-align:center;">
            <select id="gift_id" style="border:1px solid #ccc;border-radius:3px;-webkit-appearance:menulist;height:2.375rem;width:14rem;">
                <? if(!empty($gift_cards)){
                    foreach ($gift_cards as $card) {
                        ?><option value="<?=$card->id?>"><?=$card->cardId?>(余额:<?=$card->yue?>)</option><?
                    }
                }?>
            </select>
        </div>
        <div class="wode_1" style="background:none;text-align:left;padding-left: .65rem;">输入礼品卡密码：</div>
        <div class="duanxinyanzheng" style="padding-top:.3rem;">
            <div class="duanxinyanzheng_2">
                <span>
                    <input type="text" id="gift_mm" maxlength="8">
                    <i></i><i></i><i></i><i></i><i></i><i></i>
                </span>
            </div>
            <div style="height:1rem;"></div>
            <div class="duanxinyanzheng_4">
                <a href="javascript:gift_pay();">确认</a>
            </div>
        </div>
    </div>
</div>
<? }?>
<script type="text/javascript">
    var yue = <?=$yue?>;
    var daizhifu = <?=$daizhifu?>;
    $(function(){
        $('#zhifumm').bind('input propertychange', function() {
            if($(this).val().length==6){
                document.getElementById("zhifumm").blur();
                //yue_pay();
            }
        });
    });
    <? if($_SESSION['if_tongbu']==1){?>
        function pay(){
            var pay_type = $("input[name='pay_type']:checked").val();
            if(pay_type=='yue'){
                $("#zhifu_div").show();
            }else if(pay_type=='gift_card'){
                $("#gift_div").show();
            }else if(pay_type=='weixin'){
                location.href='/index.php?p=22&a=weixin_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
            }else if(pay_type=='alipay'){
                location.href='/index.php?p=22&a=alipay_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
            }else if(pay_type=='yibao'){
                location.href='http://buy.zhishangez.com/yop-api/submit_pdtorder.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
            }else if(pay_type=='yibao_k'){
                location.href='http://buy.zhishangez.com/yop-api/submit_pdtorder_yibao.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
            }
        }
    <? }else{?>
    function pay(){
        /*var if_jifen = $("#if_jifen").is(':checked');
        if(if_jifen){
            layer.open({type:2});
            $.ajax({
                type: "POST",
                url: "/index.php?p=19&a=jifen_pay&comId=<?=$order_comId?>",
                data: "order_id=<?=$id?>&jifen=<?=$need_jifen?>",
                dataType:"json",
                timeout : 5000,
                success: function(resdata){
                    layer.closeAll();
                    if(resdata.code==0){
                        layer.open({content:resdata.message,skin:'msg',time:2});
                    }else if(resdata.code==1){
                        var pay_type = $("input[name='pay_type']:checked").val();
                        if(pay_type=='yue'){
                            $("#zhifu_div").show();
                        }else if(pay_type=='gift_card'){
                            $("#gift_div").show();
                        }else if(pay_type=='weixin'){
                            location.href='/index.php?p=19&a=weixin_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
                        }else if(pay_type=='alipay'){
                            location.href='/index.php?p=19&a=alipay_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
                        }
                    }else if(resdata.code==2){
                        layer.open({content:resdata.message,skin:'msg',time:2});
                        setTimeout(function(){location.href='/index.php?p=19&a=alone';}, 1800);
                        return false;
                    }
                },
                error: function() {
                    layer.closeAll('loading');
                    layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
                }
            });
        }else{*/
        var pay_type = $("input[name='pay_type']:checked").val();
        if(pay_type=='yue'){
            $("#zhifu_div").show();
        }else if(pay_type=='gift_card'){
            $("#gift_div").show();
        }else if(pay_type=='weixin'){
            location.href='/index.php?p=22&a=weixin_pay&order_id=<?=$id?>';
        }else if(pay_type=='alipay'){
            location.href='/index.php?p=22&a=alipay_pay&order_id=<?=$id?>';
        }
    }
    //}
    <? }?>
    function yue_pay(){
        layer.open({type:2});
        var zhifumm = $("#zhifumm").val();
        $.ajax({
            type: "POST",
            url: "/index.php?p=22&a=yue_pay&comId=<?=$order_comId?>",
            data: "order_id=<?=$id?>&zhifumm="+zhifumm,
            dataType:"json",
            timeout : 5000,
            success: function(resdata){
                layer.closeAll();
                layer.open({content:resdata.message,skin:'msg',time:2});
                if(resdata.code==1){
                    setTimeout(function(){location.href='/index.php?p=22&a=orders';}, 1800);
                    return false;
                }else{
                    $('#zhifumm').val('');
                    document.getElementById("zhifumm").focus();
                }
            },
            error: function() {
                layer.closeAll('loading');
                layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
            }
        });
    }
    function show_tishi(){
        $("#weizhifu_tishi").show();
    }
</script>