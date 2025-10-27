<?php
global $db,$request;
$comId = (int)$_SESSION['demo_comId'];
$shop = $db->get_row("select com_phone,com_address,com_desc,com_honor from demo_shezhi where comId=$comId");
$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=1&a=shop';
?>
<div class="dianpujianjie" style="background:#eee">
	<div class="dianpujianjie_1">
    	店铺简介
        <div class="dianpujianjie_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/fenlei_21.png"/>
        </div>
    </div>
	<div class="dianpujianjie_2">
    	<div class="dianpujianjie_2_01">
        	<img src="<?=$_SESSION['demo_com_logo']?>"/>
        </div>
    	<div class="dianpujianjie_2_02">
        	<h2><?=$_SESSION['demo_com_title']?></h2>
            <?=$_SESSION['demo_com_remark']?> <img src="/skins/default/images/add_13.png"/>
        </div>
    	<div class="dianpujianjie_2_03" onclick="guanzhu();">
        	<? $ifguanzhu = (int)$db->get_var("select userId from user_shop_collect where userId=".(int)$_SESSION['demo_zhishangId']." and shopId=$comId");?>
        	<img src="/skins/muying/images/dianpu_guanzhu<? if($ifguanzhu>1){echo '1';}?>.png" style="width:3rem">
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="dianpujianjie_3">
    	<ul>
    		<li class="dianpujianjie_3_line">
            	服务热线 <span><?=$shop->com_phone?></span>
            </li>
            <li>
            	所在地区 <span><?=$shop->com_address?></span>
            </li>
    	</ul>
    </div>
	<div class="dianpujianjie_4">
    	<div class="dianpujianjie_4_up">
        	店铺简介
        </div>
    	<div class="dianpujianjie_4_down">
        	<?=$shop->com_desc?>
        </div>
    </div>
	<div class="dianpujianjie_5">
    	<div class="dianpujianjie_4_up">
        	资质荣誉
        </div>
        <div class="dianpujianjie_5_down">
        	<?=$shop->com_honor?>
        </div>
    </div>
	<div class="dianpujianjie_6">
    	<a href="/index.php?p=4">查看全部产品&gt;</a>
    </div>
    <div style="text-align:center;padding-bottom:1rem;font-size: .65rem;">经营方：<?=$_SESSION['demo_com_title']?><br>技术支持：直商易购-企业自主电商平台</div>
</div>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
    $(function(){
        var url = window.location.href;
        url = encodeURIComponent(url);
        WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>