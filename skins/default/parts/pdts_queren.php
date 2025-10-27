<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$address_id = (int)$request['address_id'];
$url = urlencode("/index.php?p=22&a=queren");
$gouwuche = array();
$content = $db->get_var("select content2 from demo_gouwuche where userId=$userId and comId=$comId");
if(!empty($content))$gouwuche=json_decode($content,true);
if(!empty($gouwuche)){
    reset($gouwuche);
    $inventoryId = key($gouwuche);
    $if_kuaidi = (int)$db->get_var("select if_kuaidi from demo_pdt_inventory where id=".$inventoryId);
}
$address_sql = "select * from user_address where userId=$userId and comId=$comId order by moren desc,id desc limit 50";
$addresss = $db->get_results($address_sql);
if(!empty($addresss) && empty($address_id)){
    $address_id = (int)$addresss[0]->id;
}
if(!empty($address_id)){
    $address = $db->get_row("select * from user_address where id=$address_id");
    $areaId = $address->areaId;
    $_SESSION[TB_PREFIX.'sale_area'] = (int)$areaId;
    $_SESSION[TB_PREFIX.'address_id'] = $address_id;
}
?>
<link href="/skins/default/styles/wode.css" rel="stylesheet" type="text/css">
<link href="/skins/default/styles/bendi.css" rel="stylesheet" type="text/css">
<div class="querendingdan">
    <div class="querendingdan_1">
        订单结算
        <div class="querendingdan_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fanhui_1.png"/>
        </div>
    </div>
    <? 
    if($if_kuaidi==1){
        if(empty($addresss)){?>
        <div class="querendingdan_2" onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'">
            <img src="/skins/default/images/querendingdan_11.png"/> 添加收货地址
            <div class="querendingdan_2_right"><img src="/skins/default/images/querendingdan_12.png"/></div>
        </div>
        <? }else{?>
        <div class="querendingdan_22" onclick="show_address();">
            <div class="querendingdan_2_01">
                <img src="/skins/default/images/querendingdan_13.png"/>
            </div>
            <div class="querendingdan_2_02">
                <h2><?=$address->name?>  <?=$address->phone?> <span>默认</span></h2>
                <?=$address->areaName?> <?=$address->address?>
            </div>
            <div class="querendingdan_2_03">
                <img src="/skins/default/images/querendingdan_12.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <? }
    }?>
</div>
<div class="bdquerendingdan" style="background-color:#f6f6f6;">
    <div class="bdquerendingdan_2">
        <div class="bdquerendingdan_2_01">
            商品信息
        </div>
        <?
            $nowProductId = 0;
            $shuliang = 0;
            $num = 0;
            $zong_price = 0;
            $kedi = 0;
            $pdtstr = '';
            foreach ($gouwuche as $i=>$g) {
                $nowProductId = $g['productId'];
                $inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,weight,image,status,comId from demo_pdt_inventory where id=".$g['inventoryId']);
                $if_user_info = $db->get_var("select if_user_info from demo_pdt where id=$nowProductId");
                if($inventory->status!=1)continue;
                //$yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
                $price = $inventory->price_sale;
                //$pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'","comId":'.$inventory->comId.'}}';
                $shuliang++;
                $num+=$g['num'];
                $zong_price+=$price*$g['num'];
                $kedi += 0;
                ?>
                <div class="bdquerendingdan_2_02">
                    <div class="bdquerendingdan_2_02_left">
                        <img src="<?=ispic($inventory->image)?>">
                    </div>
                    <div class="bdquerendingdan_2_02_right">
                        <?=$inventory->title?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="bdquerendingdan_2_03">
                    <div class="bdquerendingdan_2_03_left">
                        <?=$inventory->key_vals=='无'?'':$inventory->key_vals?>
                    </div>
                    <div class="bdquerendingdan_2_03_right">
                        ￥<?=$inventory->price_sale?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="bdquerendingdan_2_04">  
                    <div class="bdquerendingdan_2_04_left">
                        购买数量： <?=$g['num']?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <?
            }
        ?>
    </div>
    <div class="bdquerendingdan_1"> 
        <ul>
            <?
            if($if_user_info==1){
                if($comId==10){
                    $db_service = getCrmDb();
                    $user_info = $db_service->get_var("select user_info from demo_user where id=$userId");
                }else{
                    $user_info = $db->get_var("select user_info from users where id=$userId");
                }
                $user_arr = array();
                if(!empty($user_info))$user_arr=json_decode($user_info);
            ?>
                <li>    
                    <div class="bdquerendingdan_1_left">
                        <img src="/skins/default/images/a2020115_14.png" alt=""> 姓      名：
                    </div>
                    <div class="bdquerendingdan_1_right">
                        <input type="text" id="name" value="<?=$user_arr->name?>" placeholder="请输入姓名">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>    
                    <div class="bdquerendingdan_1_left">
                        <img src="/skins/default/images/a2020115_15.png" alt=""> 手机号码：
                    </div>
                    <div class="bdquerendingdan_1_right">
                        <input type="text" id="phone" value="<?=$user_arr->phone?>" placeholder="请输入手机号码">
                    </div>
                    <div class="clearBoth"></div>
                </li>
            <? }?>
            <li>    
                <div class="bdquerendingdan_1_left">
                    <img src="/skins/default/images/a2020115_16.png" alt=""> 备       注：
                </div>
                <div class="bdquerendingdan_1_right">
                    <input type="text" id="remark" placeholder="请输入备注">
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
    </div>
    <div class="bdquerendingdan_3">
        下单代表您已阅读，并同意<span onclick="$('#fenxiang_tc').show();">《平台用户服务协议》</span>
    </div>
    <?
    $zong_price = getXiaoshu($zong_price,2);
    ?>
    <div class="bdquerendingdan_4">
        <div class="bdquerendingdan_4_left">
            小计：<span>￥<?=$zong_price?></span>
        </div>
        <div class="bdquerendingdan_4_right">   
            <a href="javascript:xiadan();">立即购买</a>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="fenxiang_tc" id="fenxiang_tc" onclick="$('#fenxiang_tc').hide();" style="display:none;z-index:997">
    <div class="bj" style="background-color:rgba(0,0,0,.8);"></div>
    <div class="fenxiangdiv" style="width:16rem;color: #fff;padding:1rem;text-align:left;height:100%;overflow-y:scroll;">
        <? 
            $xieyi = $db->get_row("select xieyi,xieyi2 from demo_shezhi where comId=$comId limit 1");
            $content = $comId==10?$xieyi->xieyi2:$xieyi->xieyi;
            echo preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$content);
        ?>
    </div>
</div>
<div class="shouhuodizhi_queren_tc" id="shouhuodizhi_queren_tc" style="display:none;">
    <div class="shouhuodizhi_queren_bj" onclick="$('#shouhuodizhi_queren_tc').hide();"></div>
    <div class="shouhuodizhi_queren">
        <div class="wode_1" style="background:none;display:none;">
            <div class="shouye_1_left" style="height:auto;">
                <div class="shouye_1_left_01">
                  <img src="/skins/default/images/sou_1.png" style="margin-top:.65rem">
              </div>
              <div class="shouye_1_left_02">
                <input type="text" id="search_addr" style="background:#fff;width:12.75rem;padding-left:.3rem" placeholder="选择或搜索收货地址">
              </div>
              <div class="clearBoth"></div>
          </div>
        </div>
        <? if(!empty($addresss)){
            foreach ($addresss as $addr) {
                $addr->address = preg_replace('/((\s)*(\n)+(\s)*)/','',$addr->address);
                ?>
                <div class="shouhuodizhi_queren_1" onclick="location.href='/index.php?p=22&a=queren&address_id=<?=$addr->id?>';">
                    <h2><?=$addr->name?> <span><?=$addr->phone?></span></h2>
                    <?=$addr->areaName.$addr->address?>
                </div>
                <?
            }
        }?>
        <div class="shouhuodizhi_queren_1" onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'" style="text-align:center;color:red;">新增收货地址</div>
    </div>
</div>
<script type="text/javascript">
    var if_user_info = <?=$if_user_info?>;
    var money_zong = <?=$zong_price?>;
    var if_kuaidi = <?=$if_kuaidi?>;
    var address_id = <?=$address_id?>;
</script>
<script type="text/javascript" src="/skins/default/scripts/product/pdts_queren.js"></script>