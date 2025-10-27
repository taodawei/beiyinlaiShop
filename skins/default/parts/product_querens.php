<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
if($_SESSION['if_tongbu']==1){
    $comId = 10;
    $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}
$address_id = (int)$request['address_id'];
$if_yushou = (int)$request['if_yushou'];
$areaId = 0;//收货地址的区域，获取促销
$tuan_type = (int)$request['tuan_type'];
$tuan_id = (int)$request['tuan_id'];
if($tuan_id>0){
    $tuan = $db->get_row("select * from demo_tuan where id=$tuan_id");
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
    if($tuan->type==2){
        $address_id = $tuan->addressId;
    }
}
if($tuan_type==2 && empty($tuan_id) && $_SESSION['if_shequ_tuan']!=1){
    echo '<script>alert("您不是社区团长，不能开社区团~~");history.go(-1);"</script>';
    exit;
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
$url = urlencode("/index.php?p=4&a=querens");
$gouwuche = array();
$content = $db->get_var("select content2 from demo_gouwuche where userId=$userId and comId=$comId");
if(!empty($content))$gouwuche=json_decode($content,true);
//$zhekou = get_user_zhekou();
if($_SESSION['if_tongbu']==1){
    $gift_cards = $db->get_results("select id,cardId,yue,bili from gift_card10 where comId=10 and userId=$userId and yue>=0 and (endTime>='".date("Y-m-d")."' or endTime is NULL) order by yue desc");
}
$shop_shezhi = $db->get_row("select com_title,kaipiao_type,com_logo,if_dianzi_fapiao from demo_shezhi where comId=".$_SESSION['demo_comId']);
$kaipiao_type = $shop_shezhi->kaipiao_type;
$if_dianzi_fapiao = $shop_shezhi->if_dianzi_fapiao;
?>
<div class="querendingdan">
    <div class="querendingdan_1">
        积分下单
        <div class="querendingdan_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fanhui_1.png"/>
        </div>
    </div>
    <? if(empty($addresss)){?>
    <div class="querendingdan_2" onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'">
        <img src="/skins/default/images/querendingdan_11.png"/> 添加收货地址
        <div class="querendingdan_2_right"><img src="/skins/default/images/querendingdan_12.png"/></div>
    </div>
    <? }else{?>
    <div class="querendingdan_22" <? if($tuan->type==2){?>style="height:4.8rem" <? } if($tuan_id>0&&$tuan->type==2){}else{?>onclick="show_address();"<? }?>>
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
        <? if($tuan->type==2){?>
            <div style="color:red;font-weight:bold;padding-top:.5rem;text-align:center;font-size:.7rem;">该团为社区团，参团之前请与团长联系好取货方式！</div>
        <? }?>
    </div>
    <? }?>
    <div class="querendingdan_3">
        <ul>
            <?
            $nowProductId = 0;
            $shuliang = 0;
            $num = 0;
            $zong_price = 0;
            $kedi = 0;
            $pdtstr = '';
            foreach ($gouwuche as $i=>$g) {
                $nowProductId = $g['productId'];
                $inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,weight,image,status,comId,price_card,price_tuan,price_shequ_tuan from demo_product_inventory where id=".$g['inventoryId']);
                if($if_yushou==1){
                    $now = date("Y-m-d H:i:s");
                    $yushou = $db->get_row("select * from yushou where pdtId=".$g['inventoryId']." and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
                    if(empty($yushou)){
                        die('<script>alert("预售活动已结束!");history.go(-1);</script>');
                    }
                    $left = $yushou->num - $yushou->num_saled;
                    if($g['num']>$left){
                        die('<script>alert("库存不足，下单失败!");history.go(-1);</script>');
                    }
                    $price_json = json_decode($yushou->price_json,true);
                    $price = $yushou_money = $price_json[0]['price'];
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
                    $num+=$g['num'];
                    $zong_price += $yushou_money*$g['num'];
                    $kedi += $inventory->price_card*$g['num'];
                    $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
                    $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$yushou_money.'","comId":'.$inventory->comId.'}}';
                }else{
                    if($inventory->status!=1)continue;
                    $kucun = get_product_kucun($g['inventoryId']);
                    if($g['num']>$kucun)$g['num']=$kucun;
                    if($kucun<=0)continue;
                    $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
                    if($tuan_type==1){
                        $price = $inventory->price_tuan;
                    }else if($tuan_type==2){
                        $price = $inventory->price_shequ_tuan;
                    }else{
                        $price = get_user_zhekou($g['inventoryId'],$inventory->price_sale);
                    }
                    $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'","comId":'.$inventory->comId.'}}';
                    $shuliang++;
                    $num+=$g['num'];
                    $zong_price+=$price*$g['num'];
                    $kedi += $inventory->price_card*$g['num'];
                    $jifen = $db->get_var("select jifen from demo_jifenList where inventoryId=".$g['inventoryId']." limit 1");
                    $jifens = $jifen*$g['num'];
                }
                ?>
                <li>
                    <div class="querendingdan_3_01">
                        <img src="<?=ispic($inventory->image)?>"/>
                    </div>
                    <div class="querendingdan_3_02">
                        <h2><?=$inventory->title?>【<?=$inventory->key_vals?>】</h2>×<?=$g['num']?>
                    </div>
                    <div class="querendingdan_3_03">
                        <?=$jifens;?>积分
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <?
            }
            ?>
        </ul>
    </div>
    <?
    if(!empty($pdtstr)){
        $pdtstr = substr($pdtstr,1);
        $pdt_arr = json_decode('['.$pdtstr.']');
    }
    if($_SESSION['if_tongbu']==1){
        $max_kedi = empty($gift_cards)?0:$gift_cards[0]->yue;
        $kedi = $kedi>$max_kedi?$max_kedi:$kedi;
        $kedi = getXiaoshu($kedi,2);
        $zong_price-=$kedi;
        $zong_price = getXiaoshu($zong_price,2);
    }else{
        $kedi = 0;
    }
    //获取商品促销信息和订单促销信息
    $cuxiao_title = '';
    $zengpin = '';
    if($areaId>0){
        $pdt_cuxiao = get_pdt_cuxiao($pdt_arr,$areaId,$zong_price);
        if($pdt_cuxiao['jian']>0){
            $zong_price-=$pdt_cuxiao['jian'];
        }
        if(!empty($pdt_cuxiao['cuxiao_title'])){
            $cuxiao_title = $pdt_cuxiao['cuxiao_title'];
        }
        if(!empty($pdt_cuxiao['zengpin'])){
            foreach ($pdt_cuxiao['zengpin'] as $pdt) {
                $title= $db->get_var("select title from demo_product_inventory where id=".$pdt['id']);
                $zengpin.=','.$title.' * '.$pdt['num'];
            }
        }
        //订单促销
        $order_cuxiao = get_order_cuxiao($zong_price,$areaId);
        if($order_cuxiao['jian']>0){
            $zong_price-=$order_cuxiao['jian'];
        }
        if(!empty($order_cuxiao['cuxiao_title'])){
            $cuxiao_title .= empty($cuxiao_title)?$order_cuxiao['cuxiao_title']:','.$order_cuxiao['cuxiao_title'];
        }
        if(!empty($order_cuxiao['zengpin'])){
            foreach ($order_cuxiao['zengpin'] as $pdt) {
                $title= $db->get_var("select title from demo_product_inventory where id=".$pdt['id']);
                $zengpin.=','.$title.' * '.$pdt['num'];
            }
        }
        //获取运费
        $yunfei = 0;
        //获取优惠券
        $yhqs = get_yhqs($pdt_arr,$zong_price);
        if(!empty($yhqs)&&$_SESSION['if_tongbu']!=1){
            $zong_price-=$yhqs[0]['jian'];
        }
        if(!empty($yunfei)){
            $zong_price+=$yunfei;
        }
    }
    if(!empty($zengpin))$zengpin=substr($zengpin,1);
    $jifen = get_order_jifen($pdt_arr,$zong_price);
    $zong_price = getXiaoshu($zong_price,2);
    ?>
    <div class="querendingdan_4">
        <ul>
            <!-- <li>
                <div class="querendingdan_4_left">
                    邮费
                </div>
                <div class="querendingdan_4_right">
                    <? if($yunfei==0){echo '包邮';}else{?>
                        ￥<?=$yunfei?>
                    <? }?>
                </div>
                <div class="clearBoth"></div>
            </li> -->
            <?
            if(!empty($cuxiao_title)){
            ?>
            <li>
                <div class="querendingdan_4_left">
                    参与的促销
                </div>
                <div class="querendingdan_4_right" onclick="layer.open({content:'<?=str_replace(',','<br>', $cuxiao_title)?>',btn: '我知道了'});">
                    <?=sys_substr($cuxiao_title,10,true)?>
                    <img src="/skins/default/images/querendingdan_12.png"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <? }
            if(!empty($zengpin)){
            ?>
            <li>
                <div class="querendingdan_4_left">
                    赠品
                </div>
                <div class="querendingdan_4_right" onclick="layer.open({content:'<?=str_replace(',','<br>', $zengpin)?>',btn: '我知道了'});">
                    <?=sys_substr($zengpin,10,true)?>
                    <img src="/skins/default/images/querendingdan_12.png"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <? }?>
            
            <? if(!empty($gift_cards) && $kedi>0){
                $kedi = $kedi>$gift_cards[0]->yue?$gift_cards[0]->yue:$kedi;
                ?>
                <li style="height:auto">
                    <div class="querendingdan_4_left">
                        抵扣金抵扣
                    </div>
                    <div class="querendingdan_4_right" id="lpk_cont" onclick="$('#cp_lpk_tc').show();">
                        <font color="red">-<?=$kedi?></font>
                        <img src="/skins/default/images/querendingdan_12.png"/>
                    </div>
                    <div class="clearBoth"></div>
                    <div style="color:red;width:100%;text-align:right;margin-bottom:.3rem">注：使用抵扣金后不能使用账户余额支付</div>
                    <div class="clearBoth"></div>
                </li>
            <? }
            if(!empty($yhqs)){?>
            <li>
                <div class="querendingdan_4_left">
                    选择优惠券
                </div>
                <div class="querendingdan_4_right" id="yhq_cont" onclick="$('#cp_youhuiquan_tc').show();">
                    <? if($_SESSION['if_tongbu']==1){echo '不使用';}else{?>
                        <?=sys_substr($yhqs[0]['title'],10,true)?>(<font color="red">-<?=$yhqs[0]['jian']?></font>)
                        <img src="/skins/default/images/querendingdan_12.png"/>
                    <? }?>
                </div>
                <div class="clearBoth"></div>
            </li>
            <? }
            if($kaipiao_type>0||$if_dianzi_fapiao==1){
            ?>
            <li>
                <div class="querendingdan_4_left">
                    发票
                </div>
                <div class="querendingdan_4_right" id="fapiao_cont_div" onclick="$('#cp_fapiao_tc').show();">
                    <font>本次不开具发票</font> <img src="/skins/default/images/querendingdan_12.png" />
                </div>
                <div class="clearBoth"></div>
            </li>
            <? }?>
        </ul>
    </div>
    <div class="querendingdan_6">
        <h2><span>*</span>备注</h2>
        <textarea name="beizhu" id="remark" cols="30" rows="10" placeholder="请填写备注"></textarea>
    </div>
    <div class="querendingdan_5">
        <div class="querendingdan_5_left">
            合计：<span id="money_zong"><? echo $jifens;?>积分</span>
        </div>
        <div class="querendingdan_5_right">
            <a href="javascript:xiadans();">立即下单</a>
        </div>
        <div class="clearBoth"></div>
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
                <div class="shouhuodizhi_queren_1" onclick="location.href='/index.php?p=4&a=querens&address_id=<?=$addr->id?>&if_yushou=<?=$if_yushou?>';">
                    <h2><?=$addr->name?> <span><?=$addr->phone?></span></h2>
                    <?=$addr->areaName.$addr->address?>
                </div>
                <?
            }
        }?>
        <div class="shouhuodizhi_queren_1" onclick="location.href='/index.php?p=8&a=shouhuoEdit&url=<?=$url?>'" style="text-align:center;color:red;">新增收货地址</div>
    </div>
</div>
<!--优惠券-弹出-->

<!--抵扣金选择-->

<!--确认订单-发票-->
<?
$fapiao = $db->get_row("select * from user_fapiao where userId=$userId and comId=$comId limit 1");
?>
<input type="hidden" id="fapiao_id" value="<?=$fapiao->id?>">
<input type="hidden" id="if_fapiao" value="0">
<input type="hidden" id="fapiao_type" value="1">
<input type="hidden" id="fapiao_leixing" value="<?=$kaipiao_type>0?'普通发票':'电子普通发票'?>">
<input type="hidden" id="fapiao_cont" value="商品明细">
<div class="qddd_fapiao_tc" id="cp_fapiao_tc" style="display:none;">
    <div class="bj" onclick="$('#cp_fapiao_tc').hide();">
    </div>
    <div class="qddd_fapiao">
        <div class="qddd_fapiao_1">
            <div class="qddd_fapiao_1_left">
                发票
            </div>
            <div class="qddd_fapiao_1_right">
                <a href="javascript:" onclick="$('#fapiao_xuzhi').show();">发票须知</a> <img src="/skins/erp_zong/images/miaoshaxx_youhuiquan_1.png" onclick="$('#cp_fapiao_tc').hide();" />
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="qddd_fapiao_2">
            <div class="qddd_fapiao_2_left">
                <img src="/skins/default/images/a921_01.png"/>
            </div>
            <div class="qddd_fapiao_2_right">
                订单完成后24小时内开具，点击“我的订单”查看和下载
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="qddd_fapiao_3">
            <h2>发票类型</h2>
            <ul>
                <? if($if_dianzi_fapiao==1){?>
                <li>
                    <a href="javascript:" onclick="select_fapiao_leixing(1);" <? if($kaipiao_type==0){?>class="qddd_fapiao_3_on"<? }?>>电子普通发票</a>
                </li>
                <? }
                if($kaipiao_type>0){?>
                <li>
                    <a href="javascript:" onclick="select_fapiao_leixing(2);" class="qddd_fapiao_3_on">普通发票</a>
                </li>
                <? 
                }if($kaipiao_type==2){?>
                <li>
                    <a href="javascript:" onclick="select_fapiao_leixing(3);">增值税专用发票</a>
                </li>
                <? }?>
                <div class="clearBoth"></div>
            </ul>
            电子普通发票与纸质普通发票具备同等法律效力，可支持报销入账。
        </div>
        <div class="qddd_fapiao_4">
            <div class="qddd_fapiao_4_01">
                发票抬头
            </div>
            <div class="qddd_fapiao_4_02">
                <ul>
                    <li>
                        <a href="javascript:" onclick="select_fapiao_type(1);" class="qddd_fapiao_4_02_on">个人</a>
                    </li>
                    <li>
                        <a href="javascript:" onclick="select_fapiao_type(2);">单位</a>
                    </li>
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="qddd_fapiao_4_03" id="qddd_fapiao_4_03" style="display:none">
                <ul>
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            单位名称
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text" id="fapiao_com_title" value="<?=$fapiao->com_title?>" placeholder="请输入单位全称"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            纳税人识别码
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text" id="fapiao_shibiema" value="<?=$fapiao->shibiema?>" placeholder="请输入纳税人识别码"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            注册地址
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text" id="fapiao_address" value="<?=$fapiao->address?>" placeholder="请输入注册地址"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            注册电话
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text" id="fapiao_phone" value="<?=$fapiao->phone?>" placeholder="请输入注册电话"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>  
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            开户银行
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text" id="fapiao_bank_name" value="<?=$fapiao->bank_name?>" placeholder="请输入开户银行"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="qddd_fapiao_4_03_left">
                            银行账号
                        </div>
                        <div class="qddd_fapiao_4_03_right">
                            <input type="text"  id="fapiao_bank_card" value="<?=$fapiao->bank_card?>" placeholder="请输入银行账号"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="qddd_fapiao_5">
            <div class="qddd_fapiao_5_up">
                收票人信息
            </div>
            <div class="qddd_fapiao_5_down">
                <ul>
                    <li>
                        <div class="qddd_fapiao_5_down_left">
                            收票人手机
                        </div>
                        <div class="qddd_fapiao_5_down_right">
                            <input type="text" id="shoupiao_phone" value="<?=$fapiao->shoupiao_phone?>" placeholder="请输入收票人手机"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="qddd_fapiao_5_down_left">
                            收票人邮箱
                        </div>
                        <div class="qddd_fapiao_5_down_right">
                            <input type="text" id="shoupiao_email" value="<?=$fapiao->shoupiao_email?>" placeholder="用来接收电子发票邮件"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="qddd_fapiao_6">
            <div class="qddd_fapiao_6_01">
                发票内容 <span>发票内容选项已根据税法调整，具体以展示为准</span>
            </div>
            <div class="qddd_fapiao_6_02">
                <ul>
                    <li>
                        <a href="javascript:" onclick="qiehua_fp_cont(0);" class="qddd_fapiao_6_02_on">商品明细</a>
                    </li>
                    <li>
                        <a href="javascript:" onclick="qiehua_fp_cont(1);">商品类别</a>
                    </li>
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="qddd_fapiao_6_03">
                发票内容将显示 像是商品名称与价格信息，发票金额为实际支付金额，不含虚拟资产、优惠等 扣减金额。
            </div>
        </div>
        <div class="qddd_fapiao_7" onclick="$('#if_fapiao').val(1);$('#fapiao_cont_div font').html($('#fapiao_leixing').val());$('#cp_fapiao_tc').hide();">
            <img src="/skins/default/images/a921_02.png"/>
        </div>
    </div>
</div>
<!--确认订单-发票须知-->
<div class="qddd_fapiaoxuzhi_tc" id="fapiao_xuzhi" style="display:none;">
    <div class="bj" onclick="$('#fapiao_xuzhi').hide();">
    </div>
    <div class="qddd_fapiaoxuzhi">
        <div class="qddd_fapiaoxuzhi_1">
            发票须知
        </div>
        <div class="qddd_fapiaoxuzhi_2">
            1、发票金额不含知商优惠券、抵扣金、代金券、银行卡支付有礼等优惠扣减金额。
            <br>2、电子普通发票
            <br>（1）电子普通发票是税局认可的有效收付款凭证，其法律效力、基本用途及使用规定同纸质发票，如需纸质发票可自行下载打印；
            <br>（2）知商新零售全面启用电子普通发票，用户可点击“我的订单-查看发票”查询和下载。            
            <br>3、第三方卖家销售商品/服务的发票有卖家自行出具、提供，发票类型 和内容由卖家根据实际商品、服务情况决定。
        </div>
        <div class="qddd_fapiaoxuzhi_3" onclick="$('#fapiao_xuzhi').hide();">
            <img src="/skins/default/images/a921_03.png"/>
        </div>
    </div>
</div>
<script type="text/javascript">
     var address_id = <?=$address_id?>;
     var yhq_id = <?=$_SESSION['if_tongbu']==1?'0':(int)$yhqs[0]['id']?>;
     var yhq_money = <?=$_SESSION['if_tongbu']==1?'0':(empty($yhqs[0]['jian'])?0:$yhqs[0]['jian'])?>;
     var money_zong = <?=$zong_price?>;
     var yushouId = <?=(int)$yushou->id?>;
     var lpk_id = <?=$kedi>0?(int)$gift_cards[0]->id:'0'?>;
     var lpk_kedi = <?=$kedi?>;
     var tuan_type = <?=$tuan_type?>;
     var tuan_id = <?=$tuan_id?>;
     //积分下单
    function xiadans(){
        var str = '';
        if(address_id==0){
            layer.open({content:'请先选择收货地址~~',skin: 'msg',time: 2});
            return false;
        }
        layer.open({type:2});
        var remark = $("#remark").val();
        var fapiao_id = $("#fapiao_id").val();
        var if_fapiao = $("#if_fapiao").val();
        var fapiao_type = $("#fapiao_type").val();
        var fapiao_leixing = $("#fapiao_leixing").val();
        var fapiao_cont = $("#fapiao_cont").val();
        var fapiao_com_title = $("#fapiao_com_title").val();
        var fapiao_shibiema = $("#fapiao_shibiema").val();
        var shoupiao_phone = $("#shoupiao_phone").val();
        var shoupiao_email = $("#shoupiao_email").val();
        var fapiao_address = $("#fapiao_address").val();
        var fapiao_phone = $("#fapiao_phone").val();
        var fapiao_bank_name = $("#fapiao_bank_name").val();
        var fapiao_bank_card = $("#fapiao_bank_card").val();
        var toaction = yushouId>0?'create':'create_zong';
        $.ajax({
            type: "POST",
            url: "/index.php?p=4&a=creates",
            data: "tuan_type="+tuan_type+"&tuan_id="+tuan_id+"&address_id="+address_id+"&yhq_id="+yhq_id+"&remark="+remark+"&yushouId="+yushouId+"&lpk_id="+lpk_id+"&lpk_kedi="+lpk_kedi+"&fapiao_id="+fapiao_id+"&if_fapiao="+if_fapiao+"&fapiao_type="+fapiao_type+"&fapiao_leixing="+fapiao_leixing+"&fapiao_cont="+fapiao_cont+"&fapiao_com_title="+fapiao_com_title+"&fapiao_shibiema="+fapiao_shibiema+"&shoupiao_phone="+shoupiao_phone+"&shoupiao_email="+shoupiao_email+"&fapiao_address="+fapiao_address+"&fapiao_phone="+fapiao_phone+"&fapiao_bank_name="+fapiao_bank_name+"&fapiao_bank_card="+fapiao_bank_card,
            dataType:"json",timeout:8000,
            success: function(resdata){
                layer.closeAll();
                if(resdata.code==0){
                    layer.open({content:resdata.message,skin: 'msg',time: 2});
                    return false;
                }
                location.href='/index.php?p=19&a=jifen_pays&order_id='+resdata.order_id;
            },
            error: function() {
                layer.open({content:'网络错误，请刷新页面重试',skin: 'msg',time: 2});
            }
        });
    }
</script>
<script type="text/javascript" src="/skins/default/scripts/product/queren.js"></script>