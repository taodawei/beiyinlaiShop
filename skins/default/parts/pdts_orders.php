<?
global $db,$request;
$scene = (int)$request['scene'];
?>
<link href="/skins/default/styles/bddd.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.layui-m-layercont{font-size:1rem;}
</style>
<div id="bddd_top" style="background:url(/skins/default/images/bddd_top_bg.jpg) top center no-repeat;background-size:100%;background-color:#f6f6f6;">
	<div class="bddd_top_1">
    	本地订单
        <div class="bddd_top_1_left" onclick="location.href='/index.php?p=8'">
        	<img src="/skins/default/images/biao_20.png"/>
        </div>
    </div>
	<div class="bddd_cont">
    	<div class="bddd_cont_1">
        	<ul>
        		<li>
                	<a href="javascript:" onclick="qiehuan_scene(0)" <? if($scene==0){?>class="bddd_cont_1_on"<? }?>>全部</a>
                </li>
                <li>
                	<a href="javascript:" onclick="qiehuan_scene(1)" <? if($scene==1){?>class="bddd_cont_1_on"<? }?>>待付款</a>
                </li>
                <li>
                	<a href="javascript:" onclick="qiehuan_scene(2)" <? if($scene==2){?>class="bddd_cont_1_on"<? }?>>待核销</a>
                </li>
                <li>
                	<a href="javascript:" onclick="qiehuan_scene(3)" <? if($scene==3){?>class="bddd_cont_1_on"<? }?>>已核销</a>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    	<div class="bddd_cont_2">
        	<ul id="flow_ul"></ul>
        	</ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var scene = <?=$scene?>;
    var keyword = '';
</script>
<script type="text/javascript" src="/skins/default/scripts/product/pdts_order.js"></script>