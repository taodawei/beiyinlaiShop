<?
global $db,$request;
$status = empty($request['scene'])?0:1;
?>
<div class="wode" style="background-color:#f6f6f6;">
	<div class="wode_1">
    	我的团购
        <div class="wode_1_left" onclick="history.go(-1);">
        	<img src="/skins/default/images/sousuo_1.png" />
        </div>
    </div>
	<div class="wokaidetuan">
    	<div class="wokaidetuan_1">
        	<ul>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(0)" <? if($status==0){?>class="wokaidetuan_1_on"<? }?>>全部</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan_scene(1)" <? if($status==1){?>class="wokaidetuan_1_on"<? }?>>我开的团</a>
                </li>
        		<li>
                	<a href="javascript:" onclick="qiehuan_scene(2)" <? if($status==2){?>class="wokaidetuan_1_on"<? }?>>待成团</a>
                </li>
                <li>
                	<a href="javascript:" onclick="qiehuan_scene(3)" <? if($status==3){?>class="wokaidetuan_1_on"<? }?>>已成功</a>
                </li>
                <li>
                	<a href="javascript:" onclick="qiehuan_scene(4)" <? if($status==4){?>class="wokaidetuan_1_on"<? }?>>已失败</a>
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
    var status = <?=$status?>;
</script>
<script type="text/javascript" src="/skins/default/scripts/user/order_mytuan.js"></script>