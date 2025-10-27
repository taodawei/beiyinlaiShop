<?
global $db,$request;
$id = (int)$request['id'];
$tuanId = (int)$request['tuanId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$product_inventory = $db->get_row("select * from demo_product_inventory where id=$id and status=1");
if(empty($product_inventory)){
	die("<script>alert('产品已下架');location.href='/index.php';</script>");
}
$tuan = $db->get_row("select * from demo_tuan where id=$tuanId");
switch ($tuan->status) {
    case 0:
    $pay_end = strtotime($tuan->endTime);
    if($pay_end>$now){
        $statusInfo = '待成团';
        $dai_chengtuan = 1;
    }else{
        $statusInfo = '拼团失败';
    }
    break;
    case 1:
        $statusInfo = '拼团成功';
    break;
    case -1:
        $statusInfo = '拼团失败';
    break;
}
if($dai_chengtuan!=1){
    die("<script>alert('该团已结束！');location.href='/index.php';</script>");
}
$nowSelect = array();
if(!empty($product_inventory->key_ids)){
    $nowSelect = explode('-', $product_inventory->key_ids);
}
$productId = $product_inventory->productId;
$product = $db->get_row("select * from demo_product where id=$productId");
$keys = $db->get_results("select id,title,parentId,originalPic from demo_product_key where productId=$productId and isnew=0 order by parentId asc,id asc");
$inventory_keys = $db->get_var("select group_concat(key_ids) from demo_product_inventory where productId=$productId");
$keysArry = array();
$rows = 0;
if(count($keys)>1){
    foreach ($keys as $k){
        $keysArry[$k->parentId][$k->id]['title'] = $k->title;
        $keysArry[$k->parentId][$k->id]['image'] = $k->originalPic;
    }
    $rows = count($keysArry[0]);
}
$originalPics = array();
if(!empty($product_inventory->originalPic)){
    $originalPics = explode('|',$product_inventory->originalPic);
}else if(!empty($product->originalPic)){
    $originalPics = explode('|',$product->originalPic);
}
$kucun = get_product_kucun($id);
$max_num = $kucun;
if(!empty($userId)){
    $address_sql = "select id,areaName,name,phone,address,title from user_address where userId=$userId ";
    $address_sql .="order by moren desc,id desc";
    $addresss = $db->get_results($address_sql);
}
$addressId = (int)$_SESSION[TB_PREFIX.'address_id'];
if(empty($addressId)&&!empty($addresss)){
    $addressId = $addresss[0]->id;
    $_SESSION[TB_PREFIX.'address_id'] = $addressId;
    $_SESSION[TB_PREFIX.'sale_area'] = $addresss[0]->areaId;
}
$url = urlencode("/index.php?p=4&a=view&id=$id");
//$zhekou = get_user_zhekou();
$ifshoucang = $db->get_var("select inventoryId from user_pdt_collect where userId=$userId and inventoryId=$id");
$ifshoucang = empty($ifshoucang)?0:1;
$now = date("Y-m-d H:i:s");
/*$yushou = $db->get_row("select * from yushou where pdtId=$id and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
if(!empty($yushou)){
    $max_num = $yushou->num_limit;
    $left = $yushou->num - $yushou->num_saled;
    if(empty($max_num) || $max_num>$left){
        $max_num = $left;
    }
    $price_json = json_decode($yushou->price_json,true);
    $yushou_money = $price_json[0]['price'];
    if($yushou->type==2){
        $columns = array_column($price_json,'num');
        array_multisort($columns,SORT_DESC,$price_json);
        foreach ($price_json as $val) {
            if($yushou->num_saled>=$val['num']){
                $yushou_money = $val['price'];
                break;
            }
        }
    }
}*/
$price_sale = get_user_zhekou($id,$product_inventory->price_sale);
//是否促销活动
$cuxiao_pdt = $db->get_row("select id,startTime,endTime,guizes,accordType,type from cuxiao_pdt where comId=".($_SESSION['if_tongbu']==1?'10':$comId)." and find_in_set($id,pdtIds) and status=1 and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' order by startTime asc limit 1");
if(!empty($cuxiao_pdt)){
    $cuxiao_xiangou = $db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiao_pdt->id and inventoryId=$id");
    if($cuxiao_xiangou>0){
        $buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiao_pdt->id and inventoryId=$id and userId=$userId");
        $cuxiao_xiangou-=$buy_num;
        $max_num = $max_num>($cuxiao_xiangou-$buy_num)?($cuxiao_xiangou-$buy_num):$max_num;
    }

}
if($product_inventory->fenleiId==387){
    $lingyuangou=1;
}
/*if($lingyuangou == 1){
    $userId = (int)$_SESSION['demo_zhishangId'];
    $users_yaoqing = $db->get_row("select * from users_yaoqing where userId=$userId");
    $buy_ids = array();
    if(!empty($users_yaoqing->buy_ids))$buy_ids = explode(',',$users_yaoqing->buy_ids);
    $yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
    $yaoqing_rules = json_decode($yaoqing_rule);
    $guizes = $yaoqing_rules->guizes;
    foreach ($guizes as $guize){
        if($id==$guize->inventoryId){
            if($users_yaoqing->nums<$guize->man){
                echo '<script>alert("您还没有达到购买此商品助力购的资格，赶快去邀请好友吧！");location.href="/index.php?p=8&a=yaoqing"</script>';
                exit;
            }else if(in_array($guize->id,$buy_ids)){
                echo '<script>alert("您已经使用过助力购的资格买过这个产品了！");location.href="/index.php?p=8&a=yaoqing"</script>';
                exit;
            }
        }
    }
}*/
$shezhi = $db->get_row("select com_phone,com_kefu,time_tuan from demo_shezhi where comId=$comId");
?>

<link rel="stylesheet" type="text/css" href="/skins/default/styles/youhuiquan.css">
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/pintuan.css">
<div id="chanpin">
    <div class="chanpin_1">
        <ul id="pdtMenu">
            <li>
                <a href="javascript:" onclick="qiehuan('pdt',1,'chanpin_1_on');" id="pdtMenu1" class="chanpin_1_on">商品</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan('pdt',2,'chanpin_1_on');show_imgs();" id="pdtMenu2">详情</a>
            </li>
            <li>
                <a href="javascript:" onclick="qiehuan('pdt',3,'chanpin_1_on');init_pingjia();" id="pdtMenu3">评价</a>
            </li>
            <div class="clearBoth"></div>
        </ul>
        <div class="chanpin_1_01" onclick="go_prev_page(1);">
            <img src="/skins/default/images/fenlei_1.png"/>
        </div>
        <div class="chanpin_1_02" onclick="$('#fenxiang_tc').show();">
            <img src="/skins/default/images/shangpinxx_11.png"/>
        </div>
        <div class="chanpin_1_03" onclick="$('#cp_qita_tc').toggle();">
            <img src="/skins/default/images/shangpinxx_12.png"/>
        </div>
    </div>
    <div class="pdtCont" id="pdtCont1">
        <div class="chanpin_2">
            <div class="chanpin_2_01" style="position:relative;">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php
                            foreach($originalPics as $v){
                            ?>
                            <div class="swiper-slide"><a href="<?=$v?>"><img src="<?=$v?>" width="100%" /></a></div>
                            <?
                            }
                        ?>
                        <div class="tittup" id="tittup">
                            <span class="inner">
                                <em class="arrow"></em>
                                <span class="txt">滑动查看详情</span>
                            </span>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <? if(!empty($brand_img)){?>
                    <div class="chanpin_2_01_logo"><img src="<?=$brand_img?>"/></div>
                <? }?>
            </div>
            <? if(!empty($cuxiao_pdt)){
                $startTime = strtotime($cuxiao_pdt->startTime);
                $now = time();
                if($startTime>$now){
                    ?>
                    <div class="miaoshaxx_3_weikaishi">
                        <div class="miaoshaxx_3_01">
                            ¥<span><?=$price_sale?></span>
                        </div>
                        <div class="miaoshaxx_3_weikaishi_02">
                            <span>¥<?=$product_inventory->price_market?></span>
                        </div>
                        <div class="miaoshaxx_3_weikaishi_03">
                            距开抢 <font id="jishiqi1">00:00:00</font><br>
                            <span><? 
                            $today = date("Y-m-d");
                            if(strstr($cuxiao_pdt->startTime,$today)){echo '今天';}else{echo date("m-d",strtotime($cuxiao_pdt->startTime));}?> <?=date("H:i",strtotime($cuxiao_pdt->startTime))?>开抢</span>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <script type="text/javascript">
                        $(function(){
                            countDown('<?=strtotime($cuxiao_pdt->startTime)?>',1);
                        });
                    </script>
                    <?
                }else{
                    $zongNum = $kucun + $product_inventory->orders;
                    $width = intval($product_inventory->orders*10000/$zongNum)/100;
                    ?>
                    <div class="miaoshaxx_3">
                        <div class="miaoshaxx_3_01">
                            ¥<span><?=$price_sale?></span>
                        </div>
                        <div class="miaoshaxx_3_02">
                            <span>¥<?=$product_inventory->price_market?></span><br>
                            已抢<?=$product_inventory->orders?>件
                        </div>
                        <div class="miaoshaxx_3_03">
                            距结束 <font id="jishiqi1">00:00:00</font><br>
                            <span><i style="width:<?=$width?>%">已抢<?=$width?>%</i></span>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <script type="text/javascript">
                        $(function(){
                            countDown('<?=strtotime($cuxiao_pdt->endTime)?>',1);
                        });
                    </script>
                    <?
                }
            }
            if(!empty($yushou)){
            ?>
            <!-- <div class="detail_decoration bg_red2" id="priceSpec">
                <div class="price">¥<em><?=$yushou_money?></em></div>
                <div class="msg">
                    <div class="text" <? if($yushou->paytype==1){?>style="display: none;"<? }?>>
                        定金：<span class="old_price">¥<?=$yushou->dingjin?></span>
                    </div>
                    <div class="text">
                        <span class="logo type_seckill">预售</span>
                        <span class="participant">已预定<?=$yushou->num_saled?>件</span>
                    </div>
                </div>
                <div class="countdown" id="specTimer" style="display:none;"></div>
            </div> -->
            <? }?>
            <div class="chanpin_2_02">
                <div class="chanpin_2_02_left">
                    <?=$product_inventory->title?>
                </div>
                <div class="chanpin_2_02_right">
                    <a href="javascript:" id="shoucang_btn" onclick="shoucang(<?=$id?>);"><img src="/skins/default/images/pintuanshangpinye_<? if($ifshoucang==1){echo '18';}else{echo '19';}?>.png"/><br><? if($ifshoucang==1){echo '已';}?>关注</a>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="chanpin_2_03">
                <?=$product->remark?>
            </div>
            <? if(empty($yushou)){?>
                <div class="chanpin_2_04">  
                    ¥<?=$price_sale?> <? if($comId!=1009){?><span>￥<?=number_format($product_inventory->price_market,2)?></span><? }if($cuxiao_xiangou>0){?><span style="margin-left:1rem;">每人限购：<font style="font-weight:bold;color:red"><?=$cuxiao_xiangou?></font>份</span><? }?>
                </div>
                <? if(!empty($product_inventory->price_card)){?>
                    <div class="chanpin_2_05"><span>会员价</span> ¥<font id="price_user"><?=getXiaoshu($price_sale-$product_inventory->price_card,2)?></font></div>
                <? }?>
            <? }else{
                ?>
                <div class="de_buy_tip" id="buyNoticeArea">
                    <ul class="buy_tip_v2">
                        <li class="buy_tip_v2_line">
                            <span class="buy_tip_v2_title">流程</span>
                            <? if($yushou->paytype==1){?>
                                1.支付全款 2.发货
                            <? }else{?>
                                1.支付定金 2.支付尾款 3.发货
                            <? }?>
                        </li>
                        <li class="buy_tip_v2_line">
                            <? if($yushou->paytype==1){?>
                                <span class="buy_tip_v2_title">全款付款时段</span><?=date("Y-m-d H:i",strtotime($yushou->startTime))?>-<?=date("Y-m-d H:i",strtotime($yushou->endTime))?>
                            <? }else{?>
                                <span class="buy_tip_v2_title">定金付款时段</span><?=date("Y-m-d H:i",strtotime($yushou->startTime))?>-<?=date("Y-m-d H:i",strtotime($yushou->endTime))?><br>
                                <span class="buy_tip_v2_title">尾款付款时段</span><?=date("Y-m-d H:i",strtotime($yushou->startTime1))?>-<?=date("Y-m-d H:i",strtotime($yushou->endTime1))?>
                            <? }
                            if($yushou->type==2){
                                $price_json = json_decode($yushou->price_json);
                                echo '<br>预售价:<span style="color:#ff0000;">￥'.$price_json[0]->price.'元</span>';
                                foreach ($price_json as $i => $price) {
                                    if($i>0){
                                        echo '<br>满'.$price->num.'份:<span style="color:#ff0000;">￥'.$price->price.'元</span>';
                                    }
                                }
                            }
                            ?>
                        </li>
                        <li class="buy_tip_v2_line"><span class="buy_tip_v2_title">预计发货时间</span><?=date("Y-m-d",strtotime($yushou->fahuoTime))?></li>
                    </ul>
                </div>
                <?
            }?>
        </div>
        <div class="chanpin_3" id="pdt_yhq" onclick="$('#cp_youhuiquan_tc').show();">
            <ul>
                <li>
                    <div class="chanpin_3_left" id="pdt_yhq_list">
                        领券 
                    </div>
                    <div class="chanpin_3_right">
                        <img src="/skins/default/images/shangpinxx_15.png"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <? if($product_inventory->price_card>0){?>
            <div class="chanpin_3">
                <ul>
                    <li>
                        <div class="chanpin_3_left" style="width:auto;display:inline-block;">
                            抵扣金 <span>可抵￥<?=$product_inventory->price_card?></span>
                        </div>
                        <div class="chanpin_3_right" onclick="location.href='/index.php?p=5&a=view&id=3'" style="width:auto;display:inline-block;line-height:1.9rem;padding-top:0px;">
                            如何获得抵扣金？<img src="/skins/default/images/shangpinxx_25.png" style="height:.8rem;width:.45rem;vertical-align:middle;margin-left:.3rem;" />
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
        <?}
        if(!empty($cuxiao_pdt)){
            $content = '';
            $type1 = $cuxiao_pdt->accordType == '1'?'':'元';
            $type2 = $cuxiao_pdt->type==1?'赠':($cuxiao_pdt->type==2?'减':'享');
            $contents = json_decode($cuxiao_pdt->guizes);
            if(!empty($contents)){
                foreach ($contents as $i=>$rule){
                    $content .='满'.$rule->man.$type1.$type2.$rule->jian;
                    switch ($cuxiao_pdt->type) {
                        case 1:
                            $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
                            $content .=$inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
                        break;
                        case 2:
                            $content .='元';
                        break;
                        case 3:
                            $content .='%';
                        break;
                    }
                    if($i<(count($contents)-1))$content.='；';
                }
                ?>
                <div class="chanpin_3">
                    <ul>
                        <li>
                            <div class="chanpin_3_left" style="width:auto;display:inline-block;">
                                促销
                            </div>
                            <div class="chanpin_3_right" onclick="$('#cp_cuxiao_tc').show();" style="width:auto;display:inline-block;line-height:1.9rem;padding-top:0px;">
                                <?=sys_substr($content,15,true);?><img src="/skins/default/images/shangpinxx_15.png" style="vertical-align:middle;margin-left:.3rem;">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            <? }
        }
        if($product_inventory->sale_tuan==1 && $product_inventory->tuan_num){
            //$tuangous = $db->get_results("select id,user_num,nums,tuanzhang,endTime from demo_tuan where productId=$productId and comId=$comId and type=1 and status=0 and nums>0 order by nums desc limit 10");
            //if(!empty($tuangous)){
            ?>
            <div class="pintuanxx_4">
                <div class="pintuanxx_4_up">    
                    <div class="pintuanxx_4_up_left">
                        拼团玩法
                    </div>
                    <div class="pintuanxx_4_up_right">
                        <a href="javascript:" onclick="$('#pintuanxx_guize_tc').show();">拼团规则 ></a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="pintuanxx_4_down">
                    <img src="/skins/erp_zong/images/pintuan_23.gif"/>
                </div>
            </div>
            <?
        }
        if(count($keys)>0){?>
        <div class="chanpin_4" onclick="$('#cp_lijigoumai_tc').show();">
            <div class="chanpin_4_left">
                已选 <span id="sn_title"><?=$product_inventory->key_vals?></span>
            </div>
            <div class="chanpin_4_right">
                <img src="/skins/default/images/shangpinxx_15.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <? }
        if(!empty($addresss) || !empty($product_inventory->weight)){
        ?>
        <div class="chanpin_5"> 
            <div class="chanpin_5_left">
                <? if(!empty($addressId)){
                    $address = $db->get_row("select areaName,address from user_address where id=$addressId");
                    $add = $address->areaName.$address->address;
                ?>
                    送至 <span id="address_div"><img src="/skins/default/images/shangpinxx_16.png"/> <?=sys_substr($add,20,true)?></span><br>
                <? }?>
                <? if(!empty($product_inventory->weight)){?>
                    重量 <span><?=$product_inventory->weight?><?=$db->get_var("select weight from demo_product_set where comId=$comId")?></span><br>
                <? }?>
            </div>
            <div class="chanpin_5_right" onclick="$('#cp_peisong_tc').show();">
                <img src="/skins/default/images/shangpinxx_15.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <? }
        if(empty($product->yunfei_moban)){
            ?>
            <div class="chanpin_51">    
                <div class="chanpin_51_left">
                    运费 <span>包邮</span>
                </div>
                <div class="chanpin_51_right">
                    <img src="/skins/default/images/shangpinxx_15.png" alt=""/>
                </div>
                <div class="clearBoth"></div>
            </div>
            <?
        }else{
        ?>
        <div class="chanpin_51">    
            <div class="chanpin_51_left">
                运费 <span> <a href="javascript:" onclick="$('#yunfei_moban_div').show();">运费规则</a><img src="/skins/default/images/yfgz.png"></span>
            </div>
            <div class="chanpin_51_right" onclick="$('#yunfei_moban_div').show();">
                <img src="/skins/default/images/shangpinxx_15.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <?
        }
        ?>
        <div class="chanpin_6">
            <div class="chanpin_6_01">
                <div class="chanpin_6_01_left">
                    <img src="/skins/default/images/shangpinxx_17.gif"/> 商品评价
                </div>
                <div class="chanpin_6_01_right" onclick="qiehuan('pdt',3,'chanpin_1_on');init_pingjia();">
                    <span id="comment_num">0</span>条评价 <img src="/skins/default/images/shangpinxx_25.png"/>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="chanpin_6_02 pingjia" style="padding:0px;">
                <ul id="comment_list"></ul>
            </div>
            <div class="chanpin_6_03">
                <a href="javascript:" onclick="qiehuan('pdt',3,'chanpin_1_on');init_pingjia();">查看全部评价</a>
            </div>
        </div>
        <div class="chanpin_7">
            <div class="chanpin_7_01">
                <img src="/skins/default/images/shangpinxx_17.gif"/> 猜你喜欢
            </div>
            <div class="chanpin_7_02">
                <ul id="tuijian_list">
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="chanpin_7_03">
                <a href="/index.php?p=4">查看更多推荐</a>
            </div>
        </div>
        <div class="chanpin_8">
            <div class="pintuanshangpinye_6" onclick="qiehuan('pdt',2,'chanpin_1_on');show_imgs();">
                点击查看图文详情
            </div>
        </div>
    </div>
    <div class="shangpinjieshao pdtCont" id="pdtCont2" style="display:none">
        <div class="shangpinjieshao_up">
            <ul id="pdtContMenu">
                <li class="shangpinjieshao_up_line">
                    <a href="javascript:" onclick="qiehuan('pdtCont',1,'shangpinjieshao_up_on');" id="pdtContMenu1" class="shangpinjieshao_up_on">商品介绍</a>
                </li>
                <li class="shangpinjieshao_up_line">
                    <a href="javascript:" onclick="qiehuan('pdtCont',2,'shangpinjieshao_up_on');" id="pdtContMenu2">规格参数</a>
                </li>
                <li>
                    <a href="javascript:" onclick="qiehuan('pdtCont',3,'shangpinjieshao_up_on');" id="pdtContMenu3">包装售后</a>
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="shangpinjieshao_down pdtContCont" id="pdtContCont1">
            <?
            $cont1 = empty($product_inventory->cont1)?$product->cont1:$product_inventory->cont1;
            $cont1 = str_replace('src="', 'class="lazy" data-original="', $cont1);
            echo $cont1;
            ?>
        </div>
        <div class="shangpinjieshao_down pdtContCont" id="pdtContCont2" style="display:none;">
            <?
            $cont2 = empty($product_inventory->cont2)?$product->cont2:$product_inventory->cont2;
            $cont2 = str_replace('src="', 'class="lazy" data-original="', $cont2);
            echo $cont2;
            ?>
        </div>
        <div class="shangpinjieshao_down pdtContCont" id="pdtContCont3" style="display:none;">
            <?
            $cont3 = empty($product_inventory->cont3)?$product->cont3:$product_inventory->cont3;
            $cont3 = str_replace('src="', 'class="lazy" data-original="', $cont3);
            echo $cont3;
            ?>
        </div>
    </div>
    <div id="pdtCont3" class="pintuanshangpinye pdtCont" style="background:#fff;display:none;">
        <div class="pingjia">
            <ul id="flow_ul"></ul>
        </div>
    </div>
    <div class="chanpin_9">
        <div class="chanpin_9_left">
            <ul>
                <li>
                    <a href="javascript:" onclick="$('#cp_kefu_tc').show();"><img src="/skins/default/images/shangpinxx_22.png"/><br>联系客服</a>
                </li>
                <li>
                    <a href="/" ><img src="/skins/default/images/shangpinxx_23.png"/><br>首页</a>
                </li>
                <li>
                    <a href="/index.php?p=4&a=gouwuche"><img src="/skins/default/images/shangpinxx_24.png"/><br>购物车 <span id="gwc_num">0</span></a>
                </li>
            </ul>
        </div>
        <div class="chanpin_9_right">
            <ul>
                <? if(empty($yushou) && empty($lingyuangou)){
                    //是否拼团
                    if($product_inventory->sale_tuan==1 && $product_inventory->tuan_num){?>
                        <li>
                            <a href="javascript:" onclick="show_buy_div(1,1);" class="chanpin_9_right_01" style="line-height:.875rem;padding-top:.3rem;">
                                ￥<?=$price_sale?><br>单独购买</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="show_buy_div(2,1);" class="chanpin_9_right_02" style="line-height:.875rem;padding-top:.3rem;">
                                ￥<?=$tuan->type==2?$product_inventory->price_shequ_tuan:$product_inventory->price_tuan?><br>我要参团</a>
                        </li>
                    <? }else{?>
                        <li>
                            <a href="javascript:" onclick="show_buy_div(1,2);" class="chanpin_9_right_01">加入购物车</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="show_buy_div(1,1);" class="chanpin_9_right_02">立即购买</a>
                        </li>
                    <? 
                    }
                }else{?>
                     <li style="width:100%">
                        <a href="javascript:" onclick="show_buy_div(1,1);" class="chanpin_9_right_01">立即购买</a>
                    </li>
                <? }?>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<!--拼团规则-->
<div class="pintuanxx_guize_tc" id="pintuanxx_guize_tc" style="display:none;">
    <div class="bj" onclick="$('#pintuanxx_guize_tc').hide();">
    </div>
    <div class="pintuanxx_guize">
        <div class="pintuanxx_guize_1">
            拼团规则
            <div class="pintuanxx_guize_1_right" onclick="$('#pintuanxx_guize_tc').hide();">
                <img src="/skins/erp_zong/images/pintuan_26.png"/>
            </div>
        </div>
        <div class="pintuanxx_guize_2">
            <div class="pintuanxx_guize_21">
                <h2>1. 拼团有效期</h2>
                拼团有效期是自开团时刻起得<?=$shezhi->time_tuan?>小时内，如果距离活动失效时间小于<?=$shezhi->time_tuan?>小时，则以活动的结束时间为准
            </div>
            <div class="pintuanxx_guize_21">
                <h2>2. 拼团失败</h2>
                超过成团有效期<?=$shezhi->time_tuan?>小时，未达到相应参团人数的团，则该团失败。<br>
                在团有效期<?=$shezhi->time_tuan?>小时内，商品已提前售完，若还未拼团成功，则该团失败。
            </div>
        </div>
        <div class="pintuanxx_guize_3">
            <a href="javascript:" onclick="$('#pintuanxx_guize_tc').hide();">确定</a>
        </div>
    </div>
</div>
<!--顶部分享-弹出-->
<div class="fenxiang_tc" id="fenxiang_tc" onclick="$('#fenxiang_tc').hide();" style="display:none;z-index:997">
    <div class="bj"></div>
    <div class="fenxiangdiv">
      <img src="/skins/default/images/share.png" width="90%">
    </div>
</div>
<!--顶部其他-弹出-->
<div class="cp_qita_tc" id="cp_qita_tc" style="display:none;">
    <ul>
        <li>
            <a href="/index.php?p=8&a=msg"><img src="/skins/default/images/shangpinxx_39.png"/> 消息</a>
        </li>
        <li>
            <a href="/"><img src="/skins/default/images/shangpinxx_40.png"/> 首页</a>
        </li>
        <li>
            <a href="/index.php?p=4"><img src="/skins/default/images/shangpinxx_41.png"/> 搜索</a>
        </li>
        <li>
            <a href="/index.php?p=8&a=shoucang"><img src="/skins/default/images/shangpinxx_42.png"/> 我的收藏</a>
        </li>
    </ul>
</div>
<!--优惠券-弹出-->
<div class="cp_youhuiquan_tc" id="cp_youhuiquan_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_youhuiquan_tc').hide();"></div>
    <div class="cp_youhuiquan">
        <div class="cp_youhuiquan_1">
            优惠券
            <div class="cp_youhuiquan_1_right"> 
                <a href="javascript:" onclick="$('#cp_youhuiquan_tc').hide();"><img src="/skins/default/images/shangpinxx_33.png"/></a>
            </div>
        </div>
        <div class="cp_youhuiquan_2">
            可领取优惠券
        </div>
        <div class="zslingquanzhongxin">
          <ul>
          </ul>
        </div>
    </div>
</div>
<!--配送-弹出-->
<div class="cp_peisong_tc" id="cp_peisong_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_peisong_tc').hide();">
    </div>
    <div class="cp_peisong">
        <div class="cp_peisong_1">
            配送至 
            <div class="cp_peisong_1_right" onclick="$('#cp_peisong_tc').hide();">    
                <img src="/skins/default/images/shangpinxx_33.png"/>
            </div>
        </div>
        <div class="cp_peisong_2">
            <ul>
                <? if(!empty($addresss)){
                    foreach ($addresss as $i=>$addr) {
                        $addr->address = preg_replace('/((\s)*(\n)+(\s)*)/','',$addr->address);
                        ?>
                        <li <? if($i==0){?>class="addressOn"<? }?> id="address_<?=$i?>" onclick="select_address(this,<?=$addr->id?>,'<?=$addr->name?>','<?=$addr->phone?>','<?=$addr->areaName.$addr->address?>');">
                            <div class="cp_peisong_2_left">
                                <img src="/skins/default/images/shangpinxx_35.png"/> <?=$addr->areaName.$addr->address?> 
                            </div>
                            <div class="cp_peisong_2_right">
                                <img src="/skins/default/images/shangpinxx_36.png"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <?
                    }
                }?>
            </ul>
        </div>
        <div class="cp_peisong_3">
            <a href="/index.php?p=8&a=shouhuoEdit&url=<?=$url?>"><img src="/skins/default/images/shangpinxx_44.gif"/></a>
        </div>
    </div>
</div>
<!--立即购买-弹出-->
<div class="cp_lijigoumai_tc" id="cp_lijigoumai_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_lijigoumai_tc').hide();">
    </div>
    <div class="cp_lijigoumai">
        <div class="cp_lijigoumai_1" onclick="$('#cp_lijigoumai_tc').hide();">
            <img src="/skins/default/images/shangpinxx_33.png" />
        </div>
        <div class="cp_lijigoumai_2">
            <div class="cp_lijigoumai_2_left">
                <img src="<?=ispic($product_inventory->image)?>" id="fx_lijigoumai_1_img"/>
            </div>
            <div class="cp_lijigoumai_2_right">
                <div class="cp_lijigoumai_2_right_1">
                    <b id="price_sale"><?=empty($yushou)?'￥'.$price_sale:'预售价：￥'.$yushou_money?></b>
                    <b id="price_tuan" style="display:none"><?='团购价：￥'.$product_inventory->price_tuan?></b>
                    <b id="price_shequ_tuan" style="display:none"><?='团购价：￥'.$product_inventory->price_shequ_tuan?></b> <span id="price_market1"><?=$price_name?>：￥<?=number_format($product_inventory->price_market,2)?></span>
                </div>
                <div class="cp_lijigoumai_2_right_2">
                    商品编号：<span id="pdt_sn"><?=$product_inventory->sn?></span>
                </div>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div style="max-height:18rem;overflow-y:auto;">
        <? if(!empty($keysArry)){
            $i=0;
            foreach ($keysArry[0] as $key => $val) {
                $i++;
                if(!empty($keysArry[$key])){
                    ?>
                    <div class="cp_lijigoumai_3" id="key-<?=$key?>" row="<?=$i?>">
                        <div class="cp_lijigoumai_3_up">
                            <?=$val['title']?>
                        </div>
                        <div class="cp_lijigoumai_3_down">
                            <ul>
                                <? foreach($keysArry[$key] as $key1 => $val1){
                                    ?>
                                    <li><a href="javascript:" <? if(in_array($key1,$nowSelect)){?>class="cp_lijigoumai_3_down_on"<? }?> data-id="<?=$key1?>" data-row="<?=$i?>" data-key="<?=$key?>" data-img="<?=$val1['image']?>" ><?=$val1['title']?></a></li>
                                    <?
                                    }
                                ?>
                                <div class="clearBoth"></div>
                            </ul>
                        </div>
                    </div>
                    <?
                }
            }
        }?>
        </div>
        <div class="cp_lijigoumai_4">
            <div class="cp_lijigoumai_4_left">
                数量
            </div>
            <div class="cp_lijigoumai_4_right">
                <a href="javascript:" onclick="num_edit(-1);"><img src="/skins/default/images/shangpinxx_31.png"/></a><input type="number" id="num" value="1" step="1"/><a href="javascript:" onclick="num_edit(1);"><img src="/skins/default/images/shangpinxx_32.png"/></a>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="cp_lijigoumai_5" id="gouwu_div" style="display:none">
            <? if(empty($yushou)){?>
                <a href="javascript:" onclick="buy(1);"><img src="/skins/default/images/shangpinxx_46.gif"/></a>
                <a href="javascript:" onclick="buy(0);"><img src="/skins/default/images/shangpinxx_45.gif"/></a>
            <? }else{?>
                <a href="javascript:" onclick="buy(2);" style="background-color: #ff2700;border-radius:5px;width:100%;height:2.175rem;display:block;text-align:center;line-height:2.175rem;font-size:.6rem;color:#fff;" class="chanpin_9_right_01">立即购买</a>
            <? }?>
        </div>
        <div class="cp_lijigoumai_5" id="liji_div">
            <? if(empty($yushou)){?>
                <a href="javascript:" onclick="buy(0);"><img src="/skins/default/images/shangpinxx_45.gif"/></a>
                <a href="javascript:" onclick="buy(1);"><img src="/skins/default/images/shangpinxx_46.gif"/></a>
            <? }else{?>
                <a href="javascript:" onclick="buy(2);" style="background-color: #ff2700;border-radius:5px;width:100%;height:2.175rem;display:block;text-align:center;line-height:2.175rem;font-size:.6rem;color:#fff;" class="chanpin_9_right_01">立即购买</a>
            <? }?>
        </div>
        <div class="pintuanxx_kaituan_5" id="kaituan_div" style="display:none;">
            <ul>
                <li onclick="kaituan();" style="background-color:#f61a08;width:96%;margin-left:2%;">
                    <a href="javascript:">立即参团</a>
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="cp_lijigoumai_6">
        </div>
    </div>
</div>
<!--客服-弹出-->
<?
$phone = $shezhi->com_phone;
//$zxkefu = empty($shezhi->com_kefu)?'https://kefu.zhishangez.com/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=kefu01&groupid=4':$shezhi->com_kefu;
?>
<div class="cp_kefu_tc" id="cp_kefu_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_kefu_tc').hide();">
    </div>
    <div class="cp_kefu">
      <div class="cp_kefu_1">
          <ul>
            <? if(!empty($phone)){?>
                <a href="tel:<?=$phone?>"><li>
                  客服热线:<?=$phone?>
                </li></a>
            <? }?>
          </ul>
        </div>
      <div class="cp_kefu_2">
          <a href="javascript:" onclick="$('#cp_kefu_tc').hide();">取消</a>
        </div>
    </div>
</div>
<!--促销-弹出-->
<div class="cp_cuxiao_tc" id="cp_cuxiao_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_cuxiao_tc').hide();">
    </div>
    <div class="cp_cuxiao">
        <div class="cp_cuxiao_1">
            促销
            <div class="cp_cuxiao_1_right" onclick="$('#cp_cuxiao_tc').hide();">
                <img src="/skins/default/images/shangpinxx_33.png"/>
            </div>
        </div>
        <div class="cp_cuxiao_2">
            <h2>促销</h2>
            活动时间：<?=date("Y-m-d H:i",strtotime($cuxiao_pdt->startTime))?>-<?=date("Y-m-d H:i",strtotime($cuxiao_pdt->endTime))?>
        </div>
        <div class="cp_cuxiao_3">
            <ul>
                <?
                if(!empty($cuxiao_pdt)){
                    $type1 = $cuxiao_pdt->accordType == '1'?'':'元';
                    $type2 = $cuxiao_pdt->type==1?'赠':($cuxiao_pdt->type==2?'减':'享');
                    $contents = json_decode($cuxiao_pdt->guizes);
                    if(!empty($contents)){
                        foreach ($contents as $rule){
                            $content = '';
                            $content .='满'.$rule->man.$type1.$type2.$rule->jian;
                            switch ($cuxiao_pdt->type) {
                                case 1:
                                    $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
                                    $content .= '<a href="/index.php?p=4&a=view&id='.$rule->inventoryId.'">'.$inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】').'</a>';
                                break;
                                case 2:
                                    $content .='元';
                                break;
                                case 3:
                                    $content .='%';
                                break;
                            }
                            ?>
                            <li>
                                <div class="cp_cuxiao_3_01">
                                    <span>满<?=$type2=='享'?'折':$type2;?></span>
                                </div>
                                <div class="cp_cuxiao_3_02">
                                    <?=$content?>
                                </div>
                                <div class="cp_cuxiao_3_03">
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <?
                        }
                    }
                }?>
            </ul>
        </div>
    </div>
</div>
<? if(!empty($product->yunfei_moban)){
    $yunfei_moban = $db->get_row("select * from yunfei_moban where id=$product->yunfei_moban");
    $yunfei_rules = $db->get_results("select * from yunfei_moban_rule where mobanId=$product->yunfei_moban order by id");
?>
<!--运费规则-弹出-->
<div class="cp_cuxiao_tc" id="yunfei_moban_div" style="display:none;">
    <div class="cp_bj" onclick="$('#yunfei_moban_div').hide();">
    </div>
    <div class="cp_yunfei">
        <div class="cp_yunfei_1">
            运费规则
            <div class="cp_yunfei_1_right" onclick="$('#yunfei_moban_div').hide();">
                <img src="/skins/default/images/shangpinxx_33.png"/>
            </div>
        </div>
        <div class="cp_yunfei_3">
            <ul>
                <? if($yunfei_moban->if_man==1){?>
                <li>
                    <div class="cp_yunfei_3_01">
                        <img src="/skins/default/images/yfgz.png">订单满<?=$yunfei_moban->man?>元免<?=$yunfei_moban->mantype==2?'基础':''?>运费
                    </div>
                </li>
                <?
                }
                if(!empty($yunfei_rules)){
                    foreach ($yunfei_rules as $rule) {
                        if($rule->areaNames=='通用'){
                            $rule->areaNames = '';
                        }else{
                            $rule->areaNames .= '：';
                        }
                        if($yunfei_moban->accordby==1){
                            $accordby1 = '数量';
                            $accordby2 = '件';
                        }else{
                            $accordby1 = '重量';
                            $accordby2 = 'KG';
                        }
                        ?>
                        <li>
                            <div class="cp_yunfei_3_01">
                                <img src="/skins/default/images/yfgz.png"><?=$rule->areaNames.$accordby1?>在<?=$rule->base.$accordby2?>以内，运费<?=$rule->base_price?>元，超出<?=$accordby1?>按照每<?=$rule->add_num.$accordby2?>收取<?=$rule->add_price?>元运费
                            </div>
                        </li>
                        <?
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<? }?>
<link href="https://www.zhishangez.com/cdn/swiper.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/skins/default/styles/photo.css">
<script type="text/javascript" src="https://www.zhishangez.com/cdn/swiper.min.js"></script>
<script src="/skins/resource/scripts/photo.js"></script>
<script type="text/javascript">
    var show_detail = 0;
    //切换拼团或分享购的参数
    var show_price = 0;
    //是否已经加载过评价了，如果加载过就不进行初始化
    var ifpingjia = 0;
    var ifshoucang = <?=$ifshoucang?>;
    var productId = <?=$productId?>;
    var inventoryId =<?=$id?>;
    //当前所切换的标签标识2详情 3评价
    var nwo_page = 1;
    //购买方式type:1.单独购  2.分享购  3.0元购  4.拼团购 
    var buy_type = 0;
     //开团类型
    var tuan_type = <?=$tuan->type?>;
    var tuan_id = <?=$tuan->id?>;
    var tuan_limit = <?=$product_inventory->tuan_num>$max_num?$max_num:$product_inventory->tuan_num?>;
    //不能购买的原因,1.价格为0 2.库存不足
    var buy_limit = 0;
    var inventoryId = <?=$id?>;
    var addressId = <?=(int)$addressId?>;
    var tuanId = <?=$tuanId?>;
    var max_num = <?=(int)$max_num?>;
    var user_level = <?=(int)$_SESSION[TB_PREFIX.'user_level'];?>;
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '<?=$product_inventory->title?>';
    var share_img = '<?=$originalPics[0]?>';
    var share_desc = '<?=$_SESSION['demo_com_title']?>';
    var if_yushou = <?=empty($yushou)?0:1?>;
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,<?=$id?>);
    });
    var swiper = new Swiper('.swiper-container',{
        pagination: {
            el:'.swiper-pagination',
        },
        on: {
            touchEnd: function(event){
                if(this.realIndex+1==$(".swiper-wrapper .swiper-slide").length){
                    if(this.touches.diff<-150){
                        qiehuan('pdt',2,'chanpin_1_on');show_imgs();
                    }
                }
            },
            touchMove:function(event){
                var width = -500 - this.realIndex*this.width;
                $("#tittup").css("right",width+'px');
                if(this.touches.diff<-150){
                    $("#tittup .txt").text('释放查看详情');
                    $("#tittup .arrow").addClass('rotate');
                }else{
                    $("#tittup .txt").text('滑动查看详情');
                    $("#tittup .arrow").removeClass('rotate');
                }
            }
        },
    });
    baguetteBox.run('.swiper-wrapper');
</script>
<script src="/skins/default/scripts/product/product_view.js?v=1.3"></script>