<?
global $db,$request;
$scene = (int)$request['scene'];
?>
<div class="wode">
    <div class="wode_1">
        订单
        <div class="wode_1_left" onclick="location.href='/index.php?p=8';">
            <img src="/skins/default/images/sousuo_1.png"/>
        </div>
        <div style="position:absolute;right:1rem;top:-0.5rem">
            <a href="javascript:" onclick="search_order();"><img src="/skins/default/images/sou_12.png" style="margin-top:.3rem;height:auto;width:1rem"></a>
        </div>
    </div>
    <div class="pintuandingdan">
        <div class="pintuandingdan_up">
            <ul>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(0)" <? if($scene==0){?>class="wokaidetuan_1_on"<? }?>>全部</a>
                </li>
                <li> 
                    <a href="javascript:" onclick="qiehuan_scene(1)" <? if($scene==1){?>class="wokaidetuan_1_on"<? }?>>待付款</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(2)" <? if($scene==2){?>class="wokaidetuan_1_on"<? }?>>待发货</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(3)" <? if($scene==3){?>class="wokaidetuan_1_on"<? }?>>已发货</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(4)" <? if($scene==4){?>class="wokaidetuan_1_on"<? }?>>待评价</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(5)" <? if($scene==5){?>class="wokaidetuan_1_on"<? }?>>已评价</a>
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
    var keyword = '';
</script>
<script type="text/javascript" src="/skins/default/scripts/user/order_index.js"></script>
