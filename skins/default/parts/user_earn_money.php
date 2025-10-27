<?
global $db,$request;
$scene = (int)$request['scene'];
?>
<link href="/skins/default/styles/yongjin.css" rel="stylesheet" type="text/css">
<div class="dingdanmingxi">
    <div class="dingdanmingxi_1">
        订单明细
        <div class="dingdanmingxi_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fenlei_1.png"/>
        </div>
        <div class="dingdanmingxi_1_right" onclick="search_order();">
            <img src="/skins/default/images/sou_12.png"/>
        </div>
    </div>
    <div class="dingdanmingxi_2">
        <ul>
            <li>
                <a href="javascript:" onclick="qiehuan_scene(0)" <? if($scene==0){?>class="dingdanmingxi_2_on"<? }?>>全部</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan_scene(1)" <? if($scene==1){?>class="dingdanmingxi_2_on"<? }?>>已付款</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan_scene(2)" <? if($scene==2){?>class="dingdanmingxi_2_on"<? }?>>已结算</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan_scene(3)" <? if($scene==3){?>class="dingdanmingxi_2_on"<? }?>>已失效</a>
            </li>
            <div class="clearBoth"></div>
        </ul>
    </div>
    <div id="flow_ul"></div>
</div>
<script type="text/javascript">
    var scene = <?=$scene?>;
    var keyword = '';
</script>
<script type="text/javascript" src="/skins/resource/scripts/clipboard.min.js"></script>
<script type="text/javascript" src="/skins/default/scripts/user/earn_money.js"></script>
