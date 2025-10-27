<?php
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
if(is_file("../cache/product_set_$comId.php")){
  $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
  $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
if(is_file("../cache/kucun_set_$comId.php")){
  $kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
  $kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$gouwuche = array();
if($_SESSION['if_tongbu']==1){
    $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
    $content = $db->get_var("select content from demo_gouwuche where userId=$userId and comId=10");
}else{
    $content = $db->get_var("select content from demo_gouwuche where userId=$userId and comId=$comId");
}
if(!empty($content))$gouwuche=json_decode($content,true);
?>
<div class="gouwuche" style="background:#f7f7f7">
	<div class="gouwuche_1">
    	购物车
        <div class="fenlei_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fenlei_1.png">
        </div>
        <div class="gouwuche_1_right" onclick="qingkong_gouwuche();">
        	清空
        </div>
    </div>
	<div class="gouwuche_2">
    	共<font id="gouwuche_zongnum">0</font>件商品
    </div>
	<div class="gouwuche_3">
        <? if(empty($gouwuche)){?>
          <a href="/index.php?p=4" style="padding:2rem 0rem;text-align:center;display: block;">购物车还是空的，来挑几件中意的商品吧~~~</a>
        <? }else{
            echo '<ul>';
            //$zhekou = get_user_zhekou();
            $nowProductId = 0;
            $shuliang = 0;
            $num = 0;
            $zong_price = 0;
            foreach ($gouwuche as $i=>$g) {
                if($g['comId']==$comId){
                    $nowProductId = $g['productId'];
                    $inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$g['inventoryId']);
                    if($comId==1142&&$_SESSION[TB_PREFIX.'user_level']!=118){
                        $inventory->title = '';
                    }
                    //if($inventory->status!=1)continue;
                    $kucun = get_product_kucun($g['inventoryId']);
                    if($g['num']>$kucun)$g['num']=$kucun;
                    //if($kucun<=0)continue;
                    $price = get_user_zhekou($inventory->id,$inventory->price_sale);
                    $shuliang++;
                    $num+=$g['num'];
                    $zong_price+=$price*$g['num'];
                    $cuxiao_pdt = $db->get_row("select startTime,endTime,guizes,accordType,type from cuxiao_pdt where comId=".($_SESSION['if_tongbu']==1?'10':$comId)." and find_in_set(".$g['inventoryId'].",pdtIds) and status=1 and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' order by startTime asc limit 1");
                    ?>
                    <li id="g_pdt_<?=$g['inventoryId']?>" <? if($inventory->status!=1 || $kucun<=0){?> style="opacity:.7" data-select="0"<?}else{?>data-select="1"<? }?>>
                        <div class="gouwuche_3_biao">
                            <input type="checkbox" id="gouwuche_checkbox_<?=$g['inventoryId']?>" onclick="select_gouwuche_item(<?=$g['inventoryId']?>);" name="my-checkbox" value="<?=$g['inventoryId']?>" <? if($inventory->status!=1 || $kucun<=0){?>disabled="true"<? }else{?>checked="true"<? }?>>
                        </div>
                        <div class="gouwuche_3_img">
                            <img src="<?=ispic($inventory->image)?>"/>
                        </div>
                        <div class="gouwuche_3_tt">
                            <div class="gouwuche_3_tt_1">
                                <h2><?=$inventory->title?><? if($inventory->status!=1){?><span style="color:red">[已下架]</span><? }else if($kucun<=0){?><span style="color:red">[无货]</span><? }?></h2>
                                <? if($inventory->key_vals!='无'){?>规格:<?=$inventory->key_vals?><br><? }?>
                                <a href="javascript:" onclick="del_product(<?=$g['inventoryId']?>,0)"><img src="/skins/default/images/shanchu.png" style="width:1.5rem;" /></a>
                            </div>
                            <div class="gouwuche_3_tt_2">
                                <div class="gouwuche_3_tt_2_left">
                                    ¥<?=$price?>
                                </div>
                                <div class="gouwuche_3_tt_2_right">
                                    <div class="gouwuche_3_tt_2_right_01" onclick="num_edit(-1,<?=$g['inventoryId']?>);">
                                        <img src="/skins/default/images/chanpinxx_26.png"/>
                                    </div>
                                    <div class="gouwuche_3_tt_2_right_02">
                                        <input type="number" class="gouwuche_input" id="gouwuche_input_<?=$g['inventoryId']?>" data-id="<?=$g['inventoryId']?>" data-pid="<?=$g['productId']?>" data-comId="<?=$g['comId']?>" data-price="<?=$price?>" value="<?=$g['num']?>" max-num="<?=$kucun?>" />
                                    </div>
                                    <div class="gouwuche_3_tt_2_right_03" onclick="num_edit(1,<?=$g['inventoryId']?>);">
                                        <img src="/skins/default/images/chanpinxx_27.png"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                            </div>
                        <div class="clearBoth"></div>
                        <? if(!empty($cuxiao_pdt)){
                                $content = '';
                                $type1 = $cuxiao_pdt->accordType == '1'?'':'元';
                                $type2 = $cuxiao_pdt->type==1?'赠':($cuxiao_pdt->type==2?'减':'享');
                                $contents = json_decode($cuxiao_pdt->guizes);
                                if(!empty($contents)){
                                    foreach ($contents as $i=>$rule){
                                        switch ($cuxiao_pdt->type) {
                                            case 1:
                                                $content .='满'.$rule->man.$type1.$type2.',';
                                                $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
                                                $shuoming =sys_substr($inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】'),16,true).'&nbsp;<span>*'.$rule->jian.'</span>';
                                                $content .= '<a href="/index.php?p=4&a=view&id='.$rule->inventoryId.'">'.$shuoming.'</a>';
                                            break;
                                            case 2:
                                                $content .='满'.$rule->man.$type1.$type2.$rule->jian;
                                                $content .='元';
                                            break;
                                            case 3:
                                                $content .='满'.$rule->man.$type1.$type2.$rule->jian;
                                                $content .='%';
                                            break;
                                        }
                                        if($i<(count($contents)-1))$content.='<br>';
                                    }
                                    ?>
                                    <div class="gouwuche_3_03_3_05">    
                                        <div class="gouwuche_3_03_3_05_left">
                                            促销
                                        </div>
                                        <div class="gouwuche_3_03_3_05_right" style="width:14.5rem">
                                              <?=$content?>
                                        </div>
                                        <div class="clearBoth"></div>
                                    </div>
                                <? }
                            }
                            ?>
                    </li>
                    <?
                }
            }
        ?>
    		
    	</ul>
        <? }?>
    </div>
	<div class="gouwuche_4" style="bottom:2.75rem">
    	<div class="gouwuche_4_left">
        	<input type="checkbox" id="select_all" > 全部 <span>合计：<font id="gouwuche_zongprice">￥0</font></span>
        </div>
    	<div class="gouwuche_4_right">
        	<a href="javascript:" onclick="tijiao_gouwuche();">去结算</a>
        </div>
    	<div class="clearBoth"></div>
    </div>
</div>
<?
  require(ABSPATH.'/skins/default/bottom.php');
  ?>
<script type="text/javascript" src="/skins/default/scripts/product/gouwuche.js"></script>