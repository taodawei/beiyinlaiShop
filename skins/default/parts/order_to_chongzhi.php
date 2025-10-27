<?
global $db,$request;
$money = $request['money'];
$order_comId = $comId = (int)$_SESSION['demo_comId'];
?>
<div class="zhifu">
	<div class="zhifu_1">
    	支付方式
        <div class="zhifu_1_left" onclick="go_prev_page();">
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
                <? }else{
                    $ifweixin = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=1 limit 1");
                    $ifalipay = $db->get_var("select status from demo_kehu_pay where comId=$comId and type=2 limit 1");
                    if($ifweixin==1){
                        ?>
                        <li>
                            <div class="zhifu_3_down_01">
                                <input type="radio" value="weixin" name="pay_type" checked="true" >
                            </div>
                            <div class="zhifu_3_down_02">
                                <img src="/skins/default/images/zhifu_12.png" /> 微信支付
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <?
                    }
                    if($ifalipay==1){
                        ?>
                        <li>
                            <div class="zhifu_3_down_01">
                                <input type="radio" value="alipay" name="pay_type" >
                            </div>
                            <div class="zhifu_3_down_02">
                                <img src="/skins/default/images/zhifu_13.png" /> 支付宝支付
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <?
                    }
                }?>
            </ul>
        </div>
    </div>
	<div class="zhifu_2">
    	<div class="zhifu_2_left">
        	充值金额
        </div>
    	<div class="zhifu_2_right">
        	<?=$money?>元
        </div>
    	<div class="clearBoth"></div>
    </div>
    <div class="zhifu_4">
        <a href="javascript:" onclick="pay();">确认</a>
    </div>
</div>
<script type="text/javascript">
    function pay(){
        var pay_type = $("input[name='pay_type']:checked").val();
        if(pay_type=='weixin'){
            location.href='/index.php?p=19&a=weixin_chongzhi&money=<?=$money?>';
        }else if(pay_type=='alipay'){
            location.href='/index.php?p=19&a=alipay_chongzhi&money=<?=$money?>';
        }else if(pay_type=='yibao'){
            location.href='http://buy.zhishangez.com/yop-api/submit_chongzhi_order.php?money=<?=$money?>&comId=<?=$order_comId?>&userId=<?=(int)$_SESSION['demo_user_ID']?>';
        }else if(pay_type=='yibao_k'){
            location.href='http://buy.zhishangez.com/yop-api/submit_chongzhi_yibao.php?money=<?=$money?>&comId=<?=$order_comId?>&userId=<?=(int)$_SESSION['demo_user_ID']?>';
        }
    }
</script>