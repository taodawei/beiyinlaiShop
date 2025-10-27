<?
global $db,$request;
$scene = (int)$request['scene'];
?>
<div class="wode">
    <div class="wode_1">
        中奖订单
        <div class="wode_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
    </div>
    <div class="pintuandingdan">
        <div class="pintuandingdan_up">
            <ul>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(0)" <? if($scene==0){?>class="wokaidetuan_1_on"<? }?>>全部</a>
                </li>
                <li> 
                    <a href="javascript:" onclick="qiehuan_scene(1)" <? if($scene==1){?>class="wokaidetuan_1_on"<? }?>>待发货</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(2)" <? if($scene==2){?>class="wokaidetuan_1_on"<? }?>>已发货</a>
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="wokaidetuan_2">
            <ul id="flow_ul"></ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var scene = <?=$scene?>;
    var order_type = '1,2';
</script>
<script type="text/javascript" src="/skins/demo/scripts/user/order_zhongjiang.js"></script>
