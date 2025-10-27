<?
global $db,$request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$order_comId = $comId = (int)$_SESSION['demo_comId'];
/*if($_SESSION['if_tongbu']==1){
    $db_service = getCrmDb();
    $comId = 10;
    $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}*/
$order_fenbiao = getFenbiao($order_comId,20);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$order = $db->get_row("select price,price_payed,status,price_dingjin,product_json,pay_json,price_json,yushouId,tuan_id from order$order_fenbiao where id=$id");
if($order->status!=-5){
    die('<script>alert("该订单状态不能进行支付");location.href="/index.php?p=19&a=alone";</script>');
}
if(!empty($order->yushouId) && $order->price_dingjin==0){
    $yushou = $db->get_row("select * from yushou where id=$order->yushouId");
    if($yushou->paytype==2){
        $now = time();
        $startTime1 = strtotime($yushou->startTime1);
        $endTime1 = strtotime($yushou->endTime1);
        if($now<$startTime1){
            die('<script>alert("该订单未到支付尾款时间！");location.href="/index.php?p=19&a=alone";</script>');
        }else if($now>$endTime1){
            die('<script>alert("该订单已超出尾款支付时间！");location.href="/index.php?p=19&a=alone";</script>');
        }
    }
    if($yushou->type==2){
        $price_json = json_decode($yushou->price_json,true);
        $price = $price_json[0]['price'];
        $columns = array_column($price_json,'num');
        array_multisort($columns,SORT_DESC,$price_json);
        foreach ($price_json as $val) {
            if($yushou->num_saled>=$val['num']){
                $order->price = $val['price'];
                $db->query("update order$order_fenbiao set price=$order->price where id=$id");
                break;
            }
        }
    }
}
$daizhifu = getXiaoshu($order->price-$order->price_payed,2);
if($order->price_dingjin>0){
    $daizhifu = getXiaoshu($order->price_dingjin-$order->price_payed,2);
}
$u = $db->get_row("select money,jifen,payPass from users where id=$userId");
if($_SESSION['if_tongbu']==1){
    $db_service = getCrmDb();
    if($_SESSION['demo_comId']==1009){
        $u = $db_service->get_row("select money,jifen,payPass from demo_user where id=$userId");
    }
    $u->payPass = $db_service->get_var("select payPass from demo_user where id=".$_SESSION[TB_PREFIX.'zhishangId']);
}
$yue = $u->money;
$ifweixin = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=1 limit 1");
$ifalipay = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=2 limit 1");
$jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
if(!empty($order->pay_json)){
    $pay_json = json_decode($order->pay_json,true);
}
$price_json = array();
if(!empty($order->price_json)){
    $price_json = json_decode($order->price_json,true);
}
if(empty($pay_json['lipinka']['price']) && $_SESSION['if_tongbu']!=1){
    $gift_cards = $db->get_results("select id,cardId,yue,bili from gift_card$fenbiao where comId=$comId and userId=$userId and yue>=0 and (endTime>='".date("Y-m-d")."' or endTime is NULL)");
}else{
    $gift_cards = array();
}
$need_jifen = 0;
$max_money = 0;
if($jifen_pay->if_jifen_pay==1 && !empty($jifen_pay->jifen_pay_rule) && !empty($u->jifen)){
    $jifen_rule = json_decode($jifen_pay->jifen_pay_rule);
    if($jifen_rule->if_man==1 && $daizhifu>=$jifen_rule->man){
        if($jifen_rule->if_bili==1 && !empty($jifen_rule->bili)){
            $max_money = (int)($daizhifu*$jifen_rule->bili*100)/10000;
        }else{
            $max_money = $daizhifu;
        }
        if($jifen_rule->if_shangxian==1 && $max_money>$jifen_rule->shangxian){
            $max_money = $jifen_rule->shangxian;
        }
        if($need_jifen>$u->jifen){
            $max_money = (int)($u->jifen*100/$jifen_rule->jifen)/100;
            //$need_jifen = $max_money*$jifen_rule->jifen;
        }
        if($max_money>$daizhifu){
            $max_money = $daizhifu;
        }
        $need_jifen = $max_money*$jifen_rule->jifen;
    }
}

$kedi = 0;
if($_SESSION['if_tongbu']==1){
    $product_json = json_decode($order->product_json);
    if(!empty($product_json)){
        foreach ($product_json as $pdt) {
            $kedi+=$pdt->price_card*$pdt->num;
        }
    }
}
$url = urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?'.$_SERVER["QUERY_STRING"]);
$if_yibao = $db->get_var("select if_yibao from demo_shezhi where comId=$comId");
?>
<div class="zhifu">
	<div class="zhifu_1">
    	支付方式
        <div class="zhifu_1_left" onclick="location.href='/index.php?p=19&a=alone&scene=0';">
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
                if($_SESSION['if_tongbu']==1 || $if_yibao==1){
                    ?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" onclick="show_lipinka();" value="yibao" name="pay_type" checked="true" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/default/images/zhifu_12.png" /> 微信支付
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" onclick="show_lipinka();" value="yibao_k" name="pay_type" >
                        </div>
                        <div class="zhifu_3_down_02">
                            <img src="/skins/erp_zong/images/yinlian.png" /> 银行卡快捷支付(仅首次支付需填写银行卡信息)
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                }
                if($ifweixin==1 && $_SESSION['if_tongbu']!=1 && $if_yibao!=1){?>
                    <li>
                        <div class="zhifu_3_down_01">
                            <input type="radio" value="weixin" onclick="show_lipinka();" name="pay_type" checked="true" >
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
                            <input type="radio" onclick="show_lipinka();" value="alipay" name="pay_type" >
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
    <? if($order->price_dingjin>0){?>
        <div class="zhifu_2">
            <div class="zhifu_2_left">
                支付定金
            </div>
            <div class="zhifu_2_right">
                <?=$daizhifu?>元
            </div>
            <div class="clearBoth"></div>
        </div>
    <? }else{?>
	<div class="zhifu_2">
    	<div class="zhifu_2_left">
        	订单金额
        </div>
    	<div class="zhifu_2_right">
        	<?=$daizhifu?>元
        </div>
    	<div class="clearBoth"></div>
    </div>
    <? 
    }
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
    }?>
	
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
            var cardId =parseInt($("#gift_id option:selected").val());
            //var if_jifen = $("#if_jifen").is(':checked');
            if(cardId>0){
                var kedi = parseFloat($("#gift_id option:selected").attr('data-kedi'));
                var pay_type = $("input[name='pay_type']:checked").val();
                if(pay_type=='yue' && kedi+yue<daizhifu){
                    layer.open({content:'余额不足',skin: 'msg',time: 2});
                    return false;
                 }
                layer.open({type:2});
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=19&a=card_pay",
                    data: "order_id=<?=$id?>&cardId="+cardId+"&money="+kedi+"&comId=<?=$order_comId?>",
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
                                location.href='/index.php?p=19&a=weixin_pay&order_id=<?=$id?>';
                            }else if(pay_type=='alipay'){
                                location.href='/index.php?p=19&a=alipay_pay&order_id=<?=$id?>';
                            }else if(pay_type=='yibao'){
                                location.href='http://buy.zhishangez.com/yop-api/submit_order.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
                            }else if(pay_type=='yibao_k'){
                                location.href='http://buy.zhishangez.com/yop-api/submit_order_yibao.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
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
            }else{
                var pay_type = $("input[name='pay_type']:checked").val();
                if(pay_type=='yue'){
                    $("#zhifu_div").show();
                }else if(pay_type=='gift_card'){
                    $("#gift_div").show();
                }else if(pay_type=='weixin'){
                    location.href='/index.php?p=19&a=weixin_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
                }else if(pay_type=='alipay'){
                    location.href='/index.php?p=19&a=alipay_pay&order_id=<?=$id?>&comId=<?=$order_comId?>';
                }else if(pay_type=='yibao'){
                    location.href='http://buy.zhishangez.com/yop-api/submit_order.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
                }else if(pay_type=='yibao_k'){
                    location.href='http://buy.zhishangez.com/yop-api/submit_order_yibao.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
                }
            }
        }
    <? }else{?>
    function pay(){
        var if_jifen = $("#if_jifen").is(':checked');
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
                        }else if(pay_type=='yibao'){
                            location.href='http://buy.zhishangez.com/yop-api/submit_order.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
                        }else if(pay_type=='yibao_k'){
                            location.href='http://buy.zhishangez.com/yop-api/submit_order_yibao.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
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
        }else{
            var pay_type = $("input[name='pay_type']:checked").val();
            if(pay_type=='yue'){
                $("#zhifu_div").show();
            }else if(pay_type=='gift_card'){
                $("#gift_div").show();
            }else if(pay_type=='weixin'){
                location.href='/index.php?p=19&a=weixin_pay&order_id=<?=$id?>';
            }else if(pay_type=='alipay'){
                location.href='/index.php?p=19&a=alipay_pay&order_id=<?=$id?>';
            }else if(pay_type=='yibao'){
                location.href='http://buy.zhishangez.com/yop-api/submit_order.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
            }else if(pay_type=='yibao_k'){
                location.href='http://buy.zhishangez.com/yop-api/submit_order_yibao.php?id=<?=$id?>&comId=<?=$order_comId?>&userId=<?=$userId?>';
            }
        }
    }
    <? }?>
    function yue_pay(){
        layer.open({type:2});
        var zhifumm = $("#zhifumm").val();
        $.ajax({
            type: "POST",
            url: "/index.php?p=19&a=yue_pay&comId=<?=$order_comId?>",
            data: "order_id=<?=$id?>&zhifumm="+zhifumm,
            dataType:"json",
            timeout : 5000,
            success: function(resdata){
                layer.closeAll();
                layer.open({content:resdata.message,skin:'msg',time:2});
                if(resdata.code==1){
                    setTimeout(function(){location.href='<?=$order->tuan_id>0?'/index.php?p=19&a=view_tuan&id='.$order->tuan_id:'/index.php?p=19&a=alone'?>';}, 1800);
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
    function gift_pay(){
        layer.open({type:2});
        var cardId = $("#gift_id option:selected").val();
        var zhifumm = $("#gift_mm").val();
        $.ajax({
            type: "POST",
            url: "/index.php?p=19&a=gift_pay",
            data: "order_id=<?=$id?>&card_id="+cardId+"&zhifumm="+zhifumm,
            dataType:"json",
            timeout : 5000,
            success: function(resdata){
                layer.closeAll();
                layer.open({content:resdata.message,skin:'msg',time:2});
                if(resdata.code==1){
                    setTimeout(function(){location.href='/index.php?p=19&a=alone';}, 1800);
                    return false;
                }
            },
            error: function() {
                layer.closeAll('loading');
                layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
            }
        });
    }
    function show_lipinka(){
         $("#lipink_div").show();
    }
    function hide_lipinka(){
        $("#lipink_div").hide();
        $("#gift_id option").eq(0).attr("selected",true);
    }
    function show_tishi(){
        $("#weizhifu_tishi").show();
    }
</script>