<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$order = $db->get_row("select * from order0 where id=$id");
if(empty($order)){
    die("<script>alert('订单不存在');history.go(-1);</script>");
}
if($order->status!=-5){
    die("<script>alert('订单当前不是待支付状态');location.href='/index.php?p=8';</script>");
}
$pay_end = strtotime($order->pay_endtime);
$now = time();
if($pay_end<$now){
    die("<script>alert('该订单已超过支付时间');history.go(-1);</script>");
}
if(!empty($order->address_id)){
    $address = $db->get_row("select * from user_address where id=$order->address_id");
}
$product_json = json_decode($order->product_json);
$address_sql = "select * from user_address where userId=$userId ";
$address_sql .="order by moren desc,id desc";
$addresss = $db->get_results($address_sql);
$url = urlencode("/index.php?p=19&a=queren&id=$id");
$money = $db->get_var("select money from users where id=$userId");
$if_yushou = (int)$request['if_yushou'];
?>
<div class="querendingdan">
    <div class="querendingdan_1">
        订单支付
        <div class="querendingdan_1_left">
            <a href="javascript:" onclick="go_prev_page();"><img src="/skins/default/images/sousuo_1.png"/></a>
        </div>
    </div>
    <? if($order->tuan_id==0){?>
    <div class="querendingdan_2" <? if(!empty($addresss)){?>onclick="show_address();"<? }else{?>onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'"<? }?>>
        <div class="querendingdan_2_01">
            <img src="/skins/default/images/querendingdan_1.png"/>
        </div>
        <div class="querendingdan_2_02">
            <? if(empty($address)){echo '【选择收货地址】';}else{?>
            <h2><?=$address->name?> <b><?=$address->phone?></b></h2>
            <?=$address->areaName?> <?=$address->address?>
            <? }?>
        </div>
        <div class="querendingdan_2_03">
            <img src="/skins/default/images/querendingdan_11.png"/>
        </div>
        <div class="clearBoth"></div>
    </div>
    <? }else{?>
    <div class="querendingdan_2">
        <div class="querendingdan_2_01">
            <img src="/skins/default/images/querendingdan_1.png"/>
        </div>
        <div class="querendingdan_2_02">
            <h2><?=$address->name?>（团长） <b><?=$address->phone?></b></h2>
            <?=$address->areaName?> <?=$address->address?>
        </div>
        <div class="clearBoth"></div>
    </div>
    <?}?>
    <div class="querendingdan_3">
        <div class="querendingdan_3_img">
            <img src="<?=ispic($product_json->image)?>"/>
        </div>
        <div class="querendingdan_3_tt">
            <div class="querendingdan_3_tt_01">
                <?=$product_json->title?>
                <? if($product_json->key_vals!='无'){?>【<?=$product_json->key_vals?>】<? }?> * <?=$product_json->num?>
            </div>
            <div class="querendingdan_3_tt_02">
                <div class="querendingdan_3_tt_02_left">
                    ¥ <b><?=$product_json->price_sale?></b> <span>￥<?=$product_json->price_market?></span>
                </div>
                <div class="querendingdan_3_tt_02_right">
                    <span><? switch($order->type){
                            case 1:
                                echo '单独购';
                            break;
                            case 2:
                                echo '分享购';
                            break;
                            case 3:
                                echo '0元购';
                            break;
                            case 4:
                                echo '拼团购';
                            break;
                        }?></span>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="querendingdan_3_tt_03">
                <div class="querendingdan_3_tt_03_left">
                    订单号：<?=$order->orderId?>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="clearBoth"></div>
    </div>
    <div class="querendingdan_4">
        <ul>
            <li>    
                <div class="querendingdan_4_left">
                    订单金额
                </div>
                <div class="querendingdan_4_right">
                    ¥<?=$order->price?>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
    </div>
    <div class="querendingdan_5">
        <div class="querendingdan_5_left">
            支付方式
        </div>
        <div class="querendingdan_5_right">
            <ul>
                <li>
                    <a href="javascript:" onclick="change_pay(0,'weixin');" class="querendingdan_5_right_on"><img src="/skins/default/images/querendingdan_15.png"/> 微信</a>
                </li>
                <li>
                    <a href="javascript:" <? if($money>=$order->price){?>onclick="change_pay(1,'yue');"<? }else{?>onclick="layer.open({content:'余额不足',skin: 'msg',time: 2});" style="opacity:.7;"<? }?>><img src="/skins/default/images/querendingdan_16.png"/> 钱包</a>
                </li>
            </ul>
        </div>
        <div class="clearBoth"></div>
    </div>
    <div class="querendingdan_6">
        <h2><span>*</span>备注</h2>
        <textarea name="beizhu" id="beizhu" cols="30" rows="10" placeholder="请填写备注"></textarea>
    </div>
    <div class="querendingdan_7">
        <div class="querendingdan_7_left">
            总计：<span>¥<?=$order->price?></span>
        </div>
        <div class="querendingdan_7_right" onclick="pay();">
            支付 <img src="/skins/default/images/querendingdan_17.png"/> <span id="jishiqi1">00:00:00</span>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="shouhuodizhi_queren_tc" id="shouhuodizhi_queren_tc" style="display:none;">
    <div class="shouhuodizhi_queren_bj" onclick="$('#shouhuodizhi_queren_tc').hide();"></div>
    <div class="shouhuodizhi_queren">
        <div class="wode_1" style="background:none;">
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
                <div class="shouhuodizhi_queren_1" onclick="select_address(<?=$addr->id?>,'<?=$addr->name?>','<?=$addr->phone?>','<?=$addr->areaName.$addr->address?>');">
                    <h2><?=$addr->name?> <span><?=$addr->phone?></span></h2>
                    <?=$addr->areaName.$addr->address?>
                </div>
                <?
            }
        }?>
        <div class="shouhuodizhi_queren_1" onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'" style="text-align:center;color:red;">新增收货地址</div>
    </div>
</div>
<div class="shouhuodizhi_queren_tc" id="zhifu_div" style="display:none;">
    <div class="shouhuodizhi_queren_bj" onclick="$('#zhifu_div').hide();"></div>
    <div class="shouhuodizhi_queren" style="height:12rem;">
        <div class="wode_1" style="background:none;">输入支付密码</div>
        <div class="duanxinyanzheng">
            <div class="duanxinyanzheng_2">
                <span>
                    <input type="text" id="zhifumm" maxlength="8">
                    <i></i><i></i><i></i><i></i><i></i><i></i>
                </span>
            </div>
            <div style="height:1rem;"></div>
            <div class="duanxinyanzheng_4">
                <a href="javascript:yue_pay();">确认</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var order_id = <?=$id?>;
    var address_id = <?=$order->address_id?>;
    var pay_type = 'weixin';
    var endTime = '<?=strtotime($order->pay_endtime)*1000?>';
</script>
<script type="text/javascript" src="/skins/demo/scripts/user/order_queren.js?v=1.1"></script>