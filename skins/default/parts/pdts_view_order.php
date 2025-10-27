<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$order = $db->get_row("select * from demo_pdt_order where id=$id");
if(empty($order)){
	die("<script>alert('订单不存在');history.back();</script>");
}
//$shouhuo_json = json_decode($order->shuohuo_json,true);
$product_json = json_decode($order->product_json);
$now = time();
$shezhi = $db->get_row("select com_address,com_phone,zuobiao from demo_shezhi where comId=$order->comId");
if(!empty($shezhi)){
    $zuobiaos = explode('|',$shezhi->zuobiao);
    if(!empty($zuobiaos)){
        $zuobiao = $zuobiaos[1].','.$zuobiaos[0];
    }
}
switch ($order->status) {
	case 0:
		$statusInfo = '<span style="color:#cf2950;">待审核</span>';
	break;
    case 2:
        $statusInfo = '<span style="color:#cf2950;">待发货</span>';
    break;
    case 3:
        $statusInfo = '<span style="color:#cf2950;">待收货</span>';
    break;
	case 4:
		if($order->iehexiao>=$order->hexiaos){
			$statusInfo = '<span style="color:green;">已完成</span>';
			$order->status = 5;
		}else{
			$statusInfo = '<span style="color:green;">待核销</span>';
		}
	break;
	case -5:
		$statusInfo = '<span style="color:#cf2950;">待支付</span>';
	break;
	case -1:
		$statusInfo = '<span>无效</span>';
	break;
}

?>
<link href="/skins/default/styles/bddd.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.layui-m-layercont{font-size:1rem;}
</style>
<div id="bddd_top" style="background:url(/skins/default/images/bddd_top_bg.jpg) top center no-repeat;background-size:100%;background-color:#f6f6f6;">
	<div class="bddd_top_1">
    	本地订单
        <div class="bddd_top_1_left" onclick="location.href='/index.php?p=22&a=orders';">
        	<img src="/skins/default/images/biao_20.png"/>
        </div>
    </div>
    <? if(!empty($product_json)){
        foreach ($product_json as $val) {
	   	?>
		<div class="bdddxx_cont1">
	    	<div class="bdddxx_cont1_1" onclick="location.href='/index.php?p=22&a=view&id=<?=$val->id?>';"><?=$val->title?></div>
	        <div class="bdddxx_cont1_2">
	        	<div class="bdddxx_cont1_2_1"><? if(!empty($val->key_vals) || $val->key_vals!='无'){echo $val->key_vals;}?>  ×<?=$val->num?></div>
	            <div class="bdddxx_cont1_2_2">￥<?=$val->price_sale?></div>
	            <div class="clearBoth"></div>
	        </div>
	        <div class="bdddxx_cont1_3"><img src="/skins/default/images/bddd_icon1.jpg"/></div>
	        <div class="bdddxx_cont1_4">
	        	<ul>
	            	<li><a <? if(!empty($zuobiao)){?>href="http://api.map.baidu.com/geocoder?location=<?=$zuobiao?>&coord_type=bd09ll&output=html&src=<?=$_SERVER['HTTP_HOST']?>"<? }?>><img src="/skins/default/images/bdddxx_icon1.png"/><?=$shezhi->com_address?></a></li>
	                <li><a href="tel:<?=$shezhi->com_phone?>"><img src="/skins/default/images/bdddxx_icon2.png"/><?=$shezhi->com_phone?></a></li>
	            </ul>
	        </div>
	    </div>
	    <?
    	}
    }
    ?>
    <div class="bdddxx_cont2">
    	<ul>
        	<li>
            	<div class="bdddxx_cont2_1">订单状态</div>
                <div class="bdddxx_cont2_2"><?=$statusInfo?></div>
                <div class="clearBoth"></div>
            </li>
            <?
            if(!empty($order->fahuoId)){
                $fahuo = $db->get_row("select kuaidi_title,kuaidi_order from pdt_order_fahuo where id=$order->fahuoId");
                if(!empty($fahuo->kuaidi_title)){
                ?>
                 <li>
                    <div class="bdddxx_cont2_1">物流信息</div>
                    <div class="bdddxx_cont2_2"><?=$fahuo->kuaidi_order.'('.$fahuo->kuaidi_title.')'?>&nbsp;<a href="/index.php?p=19&a=getwlinfo&id=<?=$order->fahuoId?>&type=pdt" style="color:red">查看物流</a></div>
                    <div class="clearBoth"></div>
                </li>
                <? 
                }
            }
            ?>
            <li>
            	<div class="bdddxx_cont2_1">订单编号</div>
                <div class="bdddxx_cont2_2"><?=$order->orderId?></div>
                <div class="clearBoth"></div>
            </li>
            <? if(!empty($order->userInfo)){
                $userInfo = explode(' ', $order->userInfo);
                ?>
                <li>
                    <div class="bdddxx_cont2_1">用户姓名</div>
                    <div class="bdddxx_cont2_2"><?=str_replace('姓名：','',$userInfo[0])?></div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="bdddxx_cont2_1">联系电话</div>
                    <div class="bdddxx_cont2_2"><?=str_replace('电话：','',$userInfo[1])?></div>
                    <div class="clearBoth"></div>
                </li>
                <?
            }?>
            <li style="border-bottom:none;">
            	<div class="bdddxx_cont2_1">有效期</div>
                <div class="bdddxx_cont2_2"><?=$order->youxiaoqi_start?> - <?=$order->youxiaoqi_end?></div>
                <div class="clearBoth"></div>
            </li>
            <? if($order->status==4){?>
            	<div class="bdddxx_cont_ma" onclick="hexiaoma('<?=get_36id($order->id)?>')"><img src="/skins/default/images/bddd_tijiao.jpg"/></div>
        	<? }?>
        </ul>
    </div>
    <?
    if($order->status==-5){
    ?>
    <div class="bdddxx_cont3">
    	<div class="bdddxx_cont3_1">应付款：￥<?=$order->price-$order->price_payed?></div>
        <div class="bdddxx_cont3_2">
        	<a href="/index.php?p=22&a=pay&id=<?=$order->id?>" class="bdddxx_cont3_2_2">立即支付</a>
        </div>
        <div class="clearBoth"></div>
    </div>
    <? 
	}else if($order->status==3){
        ?>
        <div class="bdddxx_cont3">
            <div class="bdddxx_cont3_2">
                <a href="javascript:" onclick="qr_shouhuo(<?=$order->id?>);" class="bdddxx_cont3_2_2">确认收货</a>
            </div>
            <div class="clearBoth"></div>
        </div>
        <?
    }?>
</div>
<script type="text/javascript">
	function hexiaoma(ma){
	    layer.open({
	        title: [
	          '核销码',
	          'background-color: #FF4351; color:#fff;'
	        ]
	        ,content: ma
	    });
	}
    function qr_shouhuo(orderId){
        layer.open({
            content: '亲  货物收到后请仔细查看试用   确认无质量问题点击确认收货。'
            ,btn: ['确认收货', '取消']
            ,yes: function(index){
                layer.open({type:2});
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=22&a=qr_shouhuo",
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