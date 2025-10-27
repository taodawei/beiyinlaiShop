<?
global $db;
$comId = (int)$_SESSION['demo_comId'];
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/shangpin.css">
<div class="yushoulist">
	<div class="yushoulist_1">
    	新品预售
        <div class="yushoulist_1_left" onclick="go_prev_page();">
        	<img src="/skins/erp_zong/images/fanhui_1.png" alt=""/>
        </div>
    </div>
	<div class="yushoulist_2">
    	<ul>
             <?
            $yushous = $db->get_results("select pdtId,price_json,num_saled from yushou where comId=$comId and startTime<'".date('Y-m-d H:i:s')."' and endTime>'".date('Y-m-d H:i:s')."' and status=1 order by startTime asc limit 100");
            if(!empty($yushous)){
                foreach($yushous as $yushou){
                    $price_json = json_decode($yushou->price_json,true);
                    $yushou_money = $price_json[0]['price'];
                    $inventory = $db->get_row("select title,image,comId,status from demo_product_inventory where id=$yushou->pdtId");
                    //$com_title = $db->get_var("select com_title from demo_shezhi where comId=$inventory->comId");
                    if($inventory->status==1){
                    ?>
                    <li>
                        <div class="yushoulist_2_img">
                            <a href="/index.php?p=4&a=view&id=<?=$yushou->pdtId?>"><img src="<?=ispic($inventory->image)?>"/></a>
                        </div>
                        <div class="yushoulist_2_tt">
                            <div class="yushoulist_2_tt_1">
                                <a href="/index.php?p=4&a=view&id=<?=$yushou->pdtId?>"><span>预售</span><?=$inventory->title?></a>
                            </div>
                            <div class="yushoulist_2_tt_2">
                                <div class="yushoulist_2_tt_2_left">
                                    <div class="yushoulist_2_tt_2_left_1">
                                        ¥<b><?=$yushou_money?></b> <span><?=$yushou->num_saled?>人付款</span>
                                    </div>
                                    <div class="yushoulist_2_tt_2_left_2">
                                        
                                    </div>
                                </div>
                                <div class="yushoulist_2_tt_2_right">
                                    <a href="/index.php?p=4&a=view&id=<?=$yushou->pdtId?>">去预订</a>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <? }
                }
            }
            ?>
    	</ul>
    </div>
</div>