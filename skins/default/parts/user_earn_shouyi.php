<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if($comId==10){
    $db_service = getCrmDb();
    $user = $db_service->get_row("select yongjin as money,earn from demo_user where id=$userId");
}else{
    $user = $db->get_row("select money,earn from users where id=$userId");
}
$if_yue_tixian = $db->get_var("select if_yue_tixian from user_shezhi where comId=$comId");
?>
<link href="/skins/default/styles/yongjin.css" rel="stylesheet" type="text/css">
<div class="wodeshouyi" style="background:url(/skins/default/images/wodefensi_1.gif) center top no-repeat #f6f6f6; background-size:100% 8.5rem;">
	<div class="wodeshouyi_1">
    	我的收益
        <div class="wodeshouyi_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/fenlei_1.png" />
        </div>
    </div>
	<div class="wodeshouyi_2">
    	<div class="wodeshouyi_2_left">
        	账户余额（元）
        	<h2>¥<?=$user->money?></h2>
            <a href="/index.php?p=8&a=earn_money">账单详情</a>
        </div>
    	<div class="wodeshouyi_2_right">
        	<a <? if($if_yue_tixian==1){?>href="/index.php?p=8&a=tixian"<? }else{?>href="javascript:" onclick="layer.open({content:'未开放提现功能',skin: 'msg',time: 2});"<? }?>>提现</a>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="wodeshouyi_3">
    	<div class="wodeshouyi_3_up">	
        	<div class="wodeshouyi_3_up_left">
            	日预估收入
            </div>
        	<div class="wodeshouyi_3_up_right" onclick="$('#wodeshouyi_ri_tc').show();">
            	说明 <img src="/skins/default/images/wodeshouyi_12.png" />
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="wodeshouyi_3_down">
        	<ul>
        		<li class="wodeshouyi_3_down_line">
                	<h2>付款笔数</h2>
                    <span id="today_orders"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></span>
                </li>
                <li class="wodeshouyi_3_down_line">
                	<h2>成交预估收入</h2>
                    <txt id="today_chengjiao"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></txt>
                </li>
                <li>
                	<h2>结算预估收入</h2>
                    <txt id="today_jiesuan"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></txt>
                </li>
                <div class="clearBoth"></div>
        	</ul>
            <div class="wodeshouyi_3_down_img">
            	<img src="/skins/default/images/wodeshouyi_1.png" />
            </div>
        </div>
        <div class="wodeshouyi_3_down">
        	<ul>
        		<li class="wodeshouyi_3_down_line">
                	<h2>付款笔数</h2>
                    <span id="yestday_orders"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></span>
                </li>
                <li class="wodeshouyi_3_down_line">
                	<h2>成交预估收入</h2>
                    <txt id="yestday_chengjiao"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></txt>
                </li>
                <li>
                	<h2>结算预估收入</h2>
                    <txt id="yestday_jiesuan"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></txt>
                </li>
                <div class="clearBoth"></div>
        	</ul>
            <div class="wodeshouyi_3_down_img">
            	<img src="/skins/default/images/wodeshouyi_11.png" />
            </div>
        </div>
    </div>
	<div class="wodeshouyi_4">
    	<div class="wodeshouyi_3_up">	
        	<div class="wodeshouyi_3_up_left">
            	月预估收入
            </div>
        	<div class="wodeshouyi_3_up_right" onclick="$('#wodeshouyi_ri_tc1').show();">
            	说明 <img src="/skins/default/images/wodeshouyi_12.png" />
            </div>
        	<div class="clearBoth"></div>
        </div>
        <div class="wodeshouyi_4_down">
        	<ul>
        		<li>
                	本月消费预估收入
                    <h2 id="month_chengjiao"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></h2>
                </li>
                <li>
                	本月消费结算预估收入
                    <h2 id="month_jiesuan"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></h2>
                </li>
                <li>
                	上月消费预估收入
                    <h2 id="last_month_chengjiao"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></h2>
                </li>
                <li>
                	上月消费结算预估收入
                    <h2 id="last_month_jiesuan"><img src="/skins/default/images/loading.gif" style="width:.75rem;"></h2>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    </div>
	<div class="wodeshouyi_5" onclick="$('#wodeshouyi_guize_tc').show();">
    	规则说明
    </div>
</div>
<!--日预估收入说明-->
<div class="wodeshouyi_ri_tc" id="wodeshouyi_ri_tc" style="display:none;">
	<div class="bj" onclick="$('#wodeshouyi_ri_tc').hide();">
    </div>
	<div class="wodeshouyi_ri">
    	<div class="wodeshouyi_ri_1">
        	日预估收入说明
        </div>
    	<div class="wodeshouyi_ri_2">
        	<span>1. 付款笔数：</span>今/昨当日付款笔数<br>
            <span>2. 成交预估收入：</span>今/昨订单还没有确认收货的佣金<br>
            <span>3. 结算预估收入：</span>今/昨确认收货的订单，报货上月的订单在今/昨确定
        </div>
    	<div class="wodeshouyi_ri_3" onclick="$('#wodeshouyi_ri_tc').hide();">
        	<span>知道了</span>
        </div>
    </div>
</div>
<!--月预估收入说明-->
<div class="wodeshouyi_ri_tc" id="wodeshouyi_ri_tc1" style="display:none;">
	<div class="bj" onclick="$('#wodeshouyi_ri_tc1').hide();">
    </div>
	<div class="wodeshouyi_ri">
    	<div class="wodeshouyi_ri_1">
        	月预估收入说明
        </div>
    	<div class="wodeshouyi_ri_2">
        	<span>1. 月消费预估收入：</span>本月/上月卖出去的所有佣金<br>
            <span>2. 本月消费结算预估收入：</span>在本月内确认收货的订单，包括上月的订单在本月内确认的<br>
            <span>3. 上月消费结算预估收入：</span>本月25号可提现金额。（如果该数据为0，即本月不可体现）
        </div>
    	<div class="wodeshouyi_ri_3" onclick="$('#wodeshouyi_ri_tc1').hide();">
        	<span>知道了</span>
        </div>
    </div>
</div>
<!--规则说明-->
<div class="wodeshouyi_guize_tc" id="wodeshouyi_guize_tc" style="display:none;">
	<div class="bj" onclick="$('#wodeshouyi_guize_tc').hide();">
    </div>
	<div class="wodeshouyi_guize">
    	<div class="wodeshouyi_guize_1">
        	规则说明
        </div>
    	<div class="wodeshouyi_guize_2">
        	1、下单支付后会在预估收入里查看（会有不定期的延时）
            <br>2、订单在确认收货（结算）后才会呈现在结算预估收入里。
            <br>3、当申请售后（维权）成功后会从预估收入及结算预估收入中剔除。
            <br>4、取消订单、退款退货、申请售后维权都会产生预估收入和结算收入的数据变动

        </div>
    	<div class="wodeshouyi_guize_3" onclick="$('#wodeshouyi_guize_tc').hide();">
        	朕知道啦
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $.ajax({
        type: "POST",
        url: "/index.php?p=8&a=get_shouyi_info",
        data: "",
        dataType:"json",timeout : 20000,
        success: function(res){
            $("#today_orders").html(res.data.today_orders);
            $("#today_chengjiao").html(res.data.today_chengjiao);
            $("#today_jiesuan").html(res.data.today_jiesuan);
            $("#yestday_orders").html(res.data.yestday_orders);
            $("#yestday_chengjiao").html(res.data.yestday_chengjiao);
            $("#yestday_jiesuan").html(res.data.yestday_jiesuan);
            $("#month_chengjiao").html(res.data.month_chengjiao);
            $("#month_jiesuan").html(res.data.month_jiesuan);
            $("#last_month_chengjiao").html(res.data.last_month_chengjiao);
            $("#last_month_jiesuan").html(res.data.last_month_jiesuan);
        }
    });
});
</script>