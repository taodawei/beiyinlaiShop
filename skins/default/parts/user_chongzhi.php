<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$yue = $db->get_var("select money from users where id=$userId");
/*$ifweixin = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=1 limit 1");
$ifalipay = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=2 limit 1");*/
$huodong = $db->get_row("select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1");
?>
<link rel="stylesheet" type="text/css" href="/skins/default/styles/chongzhi.css">
<div class="zhifu">
    <div class="zhifu_1">
        余额充值
        <div class="zhifu_1_yuechongzhi" onclick="location.href='/index.php?p=8&a=qianbao';">
            <img src="/skins/default/images/a923_14.png"/>
        </div>
    </div>
    <div class="yuechongzhi_1"> 
        <img src="/skins/default/images/a923_15.gif"/><br>
        <span><?=$yue?></span>元
    </div>
    <div class="yuechongzhi_2">
        <ul>
            <? if(!empty($huodong)){
                $guizes = json_decode($huodong->guizes);
            ?>
            <div class="zhifu_2" style="height:auto;">
                <div class="zhifu_2_left">
                    充值活动
                </div>
                <div class="zhifu_2_right" style="font-size:.65rem;line-height:1.3rem;">
                    <? switch($huodong->type){
                        case 1:
                            foreach ($guizes as $guize) {
                                ?>
                                <li onclick="chongzhi(<?=$guize->man?>);">
                                    <div class="yuechongzhi_2_left">
                                        <b><?=$guize->man?></b>元<br>
                                        <span>送<?=$guize->jian?>元</span>
                                    </div>
                                    <div class="yuechongzhi_2_right">
                                        <img src="/skins/default/images/a923_16.png"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <?
                            }
                        break;
                        case 2:
                            foreach ($guizes as $guize) {
                                ?>
                                <li onclick="chongzhi(<?=$guize->man?>);">
                                    <div class="yuechongzhi_2_left">
                                        <b><?=$guize->man?></b>元<br>
                                        <span>送<?=$guize->jian?>积分</span>
                                    </div>
                                    <div class="yuechongzhi_2_right">
                                        <img src="/skins/default/images/a923_16.png"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <?
                            }
                        break;
                        case 3:
                            foreach ($guizes as $guize) {
                                $yhqId = $guize->yhqId;
                                $yhq = $db->get_row("select title,man,money from yhq where id=$yhqId");
                                ?>
                                <li onclick="chongzhi(<?=$guize->man?>);">
                                    <div class="yuechongzhi_2_left">
                                        <b><?=$guize->man?></b>元<br>
                                        <span>送优惠券<?=$yhq->title.'(满'.$yhq->man.'减'.$yhq->money.')'.$guize->jian.'个'?></span>
                                    </div>
                                    <div class="yuechongzhi_2_right">
                                        <img src="/skins/default/images/a923_16.png"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <?
                            }
                        break;
                    }?>
                </div>
                <div class="clearBoth"></div>
            </div>
            <? }?>
            <div class="clearBoth"></div>
        </ul>
    </div>
    <div class="yuechongzhi_3">
        <div class="yuechongzhi_3_up">
            <div class="yuechongzhi_3_up_left">
                充值金额
            </div>
            <div class="yuechongzhi_3_up_right">
                <input type="text" id="other_money" placeholder="请输入充值金额" onblur="document.body.scrollTop = document.body.scrollTop+1;" /> 元
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="yuechongzhi_3_down">
            <a href="javascript:" onclick="chongzhi(0)">立即充值</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    function chongzhi(money){
        if(money==0){
            money = $("#other_money").val();
        }
        money = parseFloat(money).toFixed(2);
        if(money<=0){
            alert('充值金额不能小于或等于0元');
            $("#other_money").val('');
            return false;
        }
        location.href="/index.php?p=19&a=to_chongzhi&money="+money;
    }
    function pay(){
        var pay_type = $("input[name='pay_type']:checked").val();
        var money = $("#money").val();
        if(pay_type=='weixin'){
            location.href='/index.php?p=19&a=weixin_chongzhi&money='+money;
        }else if(pay_type=='alipay'){
            location.href='/index.php?p=19&a=alipay_chongzhi&money='+money;
        }
    }
</script>