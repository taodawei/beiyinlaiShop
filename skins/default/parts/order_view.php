<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$order = $db->get_row("select * from order$fenbiao where id=$id");
if(empty($order)){
	die("<script>alert('订单不存在');history.back();</script>");
}
$shouhuo_json = json_decode($order->shuohuo_json,true);
$product_json = json_decode($order->product_json);
$now = time();
$share = (int)$request['share'];
$share_url = 'https://'.$_SERVER['HTTP_HOST'].'/index.php?p=4&a=view&id='.$order->inventoryId;
?>
<link href="/skins/erp_zong/styles/dingdan.css" rel="stylesheet" type="text/css">
<div class="querendingdan">
    <div class="querendingdan_1">
        订单详情
        <div class="querendingdan_1_left" onclick="go_prev_page(1);">
            <img src="/skins/erp_zong/images/fanhui_1.png"/>
        </div>
    </div>
    <div class="querendingdan_2">
        <div class="querendingdan_2_01">
            <img src="/skins/erp_zong/images/querendingdan_1.png"/>
        </div>
        <div class="querendingdan_2_02">
            <h2><?=$shouhuo_json['收件人']?>  <?=$shouhuo_json['手机号']?> </h2>
            <?=$shouhuo_json['所在地区'].$shouhuo_json['详细地址']?>
        </div>
        <div class="clearBoth"></div>
    </div>
    <div class="querendingdan_3">
        <div class="wodedingdan_2_01">
            <div class="wodedingdan_2_01_left">
                <img src="/skins/erp_zong/images/wodedingdan_1.png" class="wodedingdan_2_01_left_logo"/> <?=$db->get_var("select com_title from demo_shezhi where comId=$order->comId")?>  <img src="/skins/erp_zong/images/miaoshaxx_14.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <? if(!empty($product_json)){
            foreach ($product_json as $val) {
                if($comId==1142&&$_SESSION[TB_PREFIX.'user_level']!=118){
                    $val->title = '';
                }
                ?>
                <div class="wodedingdan_2_02">
                    <div class="wodedingdan_2_02_1">
                        <a href="/index.php?p=4&a=view&id=<?=$val->id?>"><img src="<?=ispic($val->image)?>"/></a>
                    </div>
                    <div class="wodedingdan_2_02_2">
                        <div class="wodedingdan_2_02_2_01">
                            <a href="/index.php?p=4&a=view&id=<?=$val->id?>"><?=$val->title?></a>
                        </div>
                        <div class="wodedingdan_2_02_2_02">
                            <?=$val->key_vals?>
                        </div>
                        <? if($order->status==4){
                            $ifpingjia = $db->get_var("select id from order_comment$fenbiao where orderId=$order->id and inventoryId=$val->id limit 1");
                            if(empty($ifpingjia)){
                                ?><a href="/index.php?p=19&a=pingjia&id=<?=$order->id?>&comId=<?=$order->comId?>&inventoryId=<?=$val->id?>" style="color:red;margin-top:5px;display:inline-block;">评价</a><?
                            }
                        }?>
                    </div>
                    <div class="wodedingdan_2_02_3">
                        <h2>¥<span><?=$val->price_sale?></span></h2>
                        ×<?=$val->num.$val->unit?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <?
            }
        }?>
        </div>
    <? if(!empty($order->fahuoId)){
        $fahuo = $db->get_row("select kuaidi_title,kuaidi_order from order_fahuo$fenbiao where id=$order->fahuoId");
        if(!empty($fahuo->kuaidi_title)){
    ?>
    <div class="dingdanxx_2">
        <div class="dingdanxx_2_up">
            物流信息
        </div>
        <div class="dingdanxx_2_down">
             快递公司：<?=$fahuo->kuaidi_title?><br>
             物流单号：<?=$fahuo->kuaidi_order?><br>
             <a href="/index.php?p=19&a=getwlinfo&id=<?=$id?>" style="color:red">查看物流</a>
        </div>
    </div>
    <? 
    }
    }
    if(!empty($order->tuan_id)){
        $tuan = $db->get_row("select * from demo_tuan where id=$order->tuan_id");
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
        if($order->if_zong==1){
            $db_service = getCrmDb();
            $tuanzhang = $db_service->get_row("select name as nickname from demo_user where id=$tuan->tuanzhang");
        }else{
            $tuanzhang = $db->get_row("select nickname from users where id=$tuan->tuanzhang");
        }
        ?>
        <div class="dingdanxx_2">
            <div class="dingdanxx_2_up">
                团购信息
            </div>
            <div class="dingdanxx_2_down">
                团长：<?=$tuanzhang->nickname?><br>
                团购状态：<?=$statusInfo?>
                <a href="/index.php?p=19&a=view_tuan&id=<?=$order->tuan_id?>" style="color:#dc152e;float:right;">查看</a>
            </div>
        </div>
        <?
    }
    ?>
    <div class="dingdanxx_2">
        <div class="dingdanxx_2_up">
            订单信息
        </div>
        <div class="dingdanxx_2_down">
            订单状态：<b style="color:red"><?
            switch ($order->status) {
                case 0:
                    echo '待成团';
                break;
                case 2:
                    echo '待发货';
                break;
                case 3:
                    echo '待收货';
                break;
                case 4:
                    echo '已完成';
                break;
                case -3:
                    echo '售后服务中';
                break;
                case -5:
                    $pay_end = strtotime($order->pay_endtime);
                    if($pay_end>$now){
                        echo '待支付';
                        $dai_pay = 1;
                    }else{
                        echo '无效';
                    }
                break;
                case -1:
                    $qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
                    if(in_array($order->remark,$qx_remarks)){
                        echo $order->remark;
                        if(!empty($order->qx_time)){echo '&nbsp;&nbsp;(取消时间：'.date('Y-m-d H:i',strtotime($order->qx_time)).')';}
                    }else{
                        echo '无效';
                    }
                break;
            }
            ?></b><br>
            订单备注：<?=$order->remark?><br>
            订单总金额：￥<b><?=$order->price?></b><br>
            <? if(!empty($order->pay_json)){
                $pay_json = json_decode($order->pay_json,true);
                if(!empty($pay_json['jifen'])){
                    echo '积分抵现： <b>￥'.$pay_json['jifen']['price'].'</b>('.$pay_json['jifen']['desc'].'积分)<br>';
                }
                if(!empty($pay_json['yue'])){
                    echo '余额支付： <b>￥'.$pay_json['yue']['price'].'</b><br>';
                }
                if(!empty($pay_json['weixin'])){
                    echo '微信支付： <b>￥'.$pay_json['weixin']['price'].'</b><br>';
                }
                if(!empty($pay_json['applet'])){
                    $str.='小程序支付 <b>￥'.$pay_json['applet']['price'].'</b><br>';
                }
                if(!empty($pay_json['alipay'])){
                    echo '支付宝支付： <b>￥'.$pay_json['alipay']['price'].'</b><br>';
                }
                if(!empty($pay_json['cash'])){
                    echo '现金支付： <b>￥'.$pay_json['cash']['price'].'</b><br>';
                }
                if(!empty($pay_json['paypal'])){
                    echo '银联支付： <b>￥'.$pay_json['paypal']['price'].'</b><br>';
                }
                if(!empty($pay_json['lipinka'])){
                    echo '抵扣金： <b>￥'.$pay_json['lipinka']['price'].'</b><br>';
                }
                if(!empty($pay_json['lipinka1'])){
                    echo '礼品卡支付： <b>￥'.$pay_json['lipinka1']['price'].'</b><br>';
                }
                if(!empty($pay_json['other'])){
                    echo '其他支付 ：<b>￥'.$pay_json['other']['price'].'</b>('.$pay_json['ohter']['desc'].')<br>';
                }
                if(!empty($pay_json['dingjin'])){
                    echo '定金 ：<b>￥'.$pay_json['dingjin']['price'].'<br>';
                }
                if(!empty($pay_json['yibao'])){
                    $pay_way = $pay_json['yibao']['pay_way']=='NCPAY'?'快捷支付(银行卡)':'微信支付';
                    echo $pay_way.' ：<b>￥'.$pay_json['yibao']['price'].'</b>';
                }
            }?>
            订单编号：<?=$order->orderId?><br>
            下单时间：<?=date("Y-m-d H:i",strtotime($order->dtTime))?><br>
            <? 
            if($dai_pay==1){
                $show_pay = 1;
                if(!empty($order->yushouId) && $order->price_dingjin==0){
                    $yushou = $db->get_row("select * from yushou where id=$order->yushouId");
                    if($yushou->paytype==2){
                        $now = time();
                        $startTime1 = strtotime($yushou->startTime1);
                        $endTime1 = strtotime($yushou->endTime1);
                        if($now<$startTime1){
                            $show_pay = 0;
                        }else if($now>$endTime1){
                            $show_pay = 2;
                        }
                    }
                    if($yushou->type==2){
                        $price_json = json_decode($yushou->price_json,true);
                        $price = $price_json[0]['price'];
                        $columns = array_column($price_json,'num');
                        array_multisort($columns,SORT_DESC,$price_json);
                        foreach ($price_json as $val) {
                            if($yushou->num_saled>=$val['num']){
                                $order->price = $val['price'];
                                break;
                            }
                        }
                    }
                }
            }
            if($show_pay==0 && $dai_pay==1){
                ?>
                尾款支付时间：<?=date("Y-m-d H:i",strtotime($yushou->startTime1)).'-'.date("Y-m-d H:i",strtotime($yushou->endTime1))?><br>
                尾款：<?=$order->price-$order->price_payed?><br>
                <?
            }
            if($order->status==2&&!empty($order->yushouId)){
                $fahuoTime = $db->get_row("select fahuoTime from yushou where id=$order->yushouId");
                ?>
                预计发货日期：<?=date("Y-m-d",strtotime($fahuoTime->fahuoTime))?><br>
                <?
            }
            $ifshouhou = $db->get_var("select id from order_tuihuan where orderId=$id and status>-1 limit 1");
            if(!empty($ifshouhou)){
                ?>
                售后记录：<a href="/index.php?p=21&a=view&id=<?=$ifshouhou?>&comId=<?=$order->comId?>" style="color:#dc152e">查看</a><br>
                <?
            }?>
        </div>
    </div>
    <? if($order->ifkaipiao==1){
        $fapiao_json = json_decode($order->fapiao_json,true);
        if(!empty($fapiao_json)){
    ?>
    <div class="dingdanxx_2">
        <div class="dingdanxx_2_up">
            发票信息
        </div>
        <div class="dingdanxx_2_down">
            <?
            foreach ($fapiao_json as $key => $val){
                echo $key.'：<b>'.$val.'</b><br>';
            }
            ?>
        </div>
        <div class="dingdanxx_2_03">
            <img src="/skins/default/images/a923_13.png" style="width:1rem" /> 电子发票将在订单完成后1-2天开具
        </div>
    </div>
    <? }
    }
    ?>
    <div class="querendingdan_5" <? if($order->status==-1){?>style="display:none"<? }?>>
        <? if($dai_pay==1){
                if($show_pay==1){
                ?>
                    共<?=$order->pdtNums?>件，待支付：<span>￥</span><b><?=$order->price-$order->price_payed?></b><a href="/index.php?p=19&a=pay&id=<?=$order->id?>&comId=<?=$order->comId?>">付款</a>
                <? 
                }else if($show_pay==0){
                    ?>
                    <a href="javascript:" onclick="layer.open({content:'未到支付尾款时间',skin: 'msg',time: 2});">付款</a>
                    <?
                }else if($show_pay==2){
                    ?>
                    <a href="javascript:" onclick="layer.open({content:'已超出尾款支付时间',skin: 'msg',time: 2});">付款</a>
                    <?
                }
            }else if($order->status==0 || ($order->status==2 && $order->tuan_id==0)){
                if($order->is_choujiang==0 && $order->yushouId==0){
                ?>
                    <a href="javascript:" onclick="qx_order(<?=$order->id?>);">取消订单</a>
                <? 
                }
            }else if($order->status==3){?>
                <a href="/index.php?p=21&a=add&id=<?=$order->id?>&comId=<?=$order->comId?>">申请售后</a>
                <a href="javascript:" onclick="qr_shouhuo(<?=$order->id?>);">确认收货</a>
            <? }?>
    </div>
</div>
<!--客服-弹出-->
<?
$kefu = $db->get_row("select com_phone,com_kefu from demo_shezhi where comId=$order->comId");
$phone = $kefu->com_phone;
$zxkefu = empty($kefu->com_kefu)?'https://kefu.zhishangez.com/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=kefu01&groupid=4':$kefu->com_kefu;
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
            <a href="<?=$zxkefu?>"><li class="cp_kefu_1_line">
              在线客服
            </li></a>
          </ul>
        </div>
      <div class="cp_kefu_2">
          <a href="javascript:" onclick="$('#cp_kefu_tc').hide();">取消</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    function qr_shouhuo(orderId){
        layer.open({
            content: '亲  货物收到后请仔细查看试用   确认无质量问题点击确认收货  确认收货后将不再支持退换货  请您悉知。'
            ,btn: ['确认收货', '取消']
            ,yes: function(index){
                layer.open({type:2});
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=19&a=qr_shouhuo",
                    data: "orderId="+orderId,
                    dataType:"json",timeout : 20000,
                    success: function(res){
                        layer.closeAll();
                        layer.open({content:res.message,skin: 'msg',time: 2});
                        if(res.code==1){
                            setTimeout(function(){
                                location.reload();
                            },1800);
                        }
                    },
                    error: function() {
                        layer.closeAll();
                        layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
                    }
                });
            }
        });
    }
    function qx_order(orderId){
        layer.open({
            content: '亲  确定要取消订单吗？取消订单后资金将在一个工作日内返还！'
            ,btn: ['确认取消', '我再想想']
            ,yes: function(index){
                layer.open({type:2});
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=19&a=qx_order",
                    data: "orderId="+orderId,
                    dataType:"json",timeout : 20000,
                    success: function(res){
                        layer.closeAll();
                        layer.open({content:res.message,skin: 'msg',time: 2});
                        if(res.code==1){
                            setTimeout(function(){
                                location.reload();
                            },1800);
                        }
                    },
                    error: function() {
                        layer.closeAll();
                        layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
                    }
                });
            }
      });
    }
</script>