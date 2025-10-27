<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if($_SESSION['if_tongbu']==1){
    $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
    $comId = 10;
}
$fenbiao = getFenbiao($comId,20);
$gift_cards = $db->get_results("select * from gift_card$fenbiao where comId=$comId and userId=$userId and ((endTime is not NULL and endTime<'".date("Y-m-d")."') or yue='0.00') and yue>0 order by id desc");
?>
<link rel="stylesheet" href="/skins/lipinka/styles/lipinka.css"></link>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/lpk.css">
<div class="lpk">
	<div class="lpk_1">
        <div class="lpk_1_1">已过期(使用)抵扣金</div>
        <div class="lpk_1_2" onclick="history.go(-1);"><img src="/skins/erp_zong/images/lpk_icon1.png"></div>
        <div class="lpk_1_3"><a href="javascript:" onclick="$('#add_card').show();$('body').css({'height':'100%','overflow':'hidden'});">绑定抵扣卡</a></div>
    </div>
    <div class="lpk_4" onclick="location.href='/index.php?p=5&a=view&id=3'"><img src="/skins/erp_zong/images/lpk_icon7.png">如何获得抵扣金？</div>
	<div class="lpk_2">
        <ul>
            <? 
            $now = time();
            if(!empty($gift_cards)){
                foreach ($gift_cards as $i=>$card) {
                    $endTime = strtotime($card->endTime);
                    if(empty($card->endTime))$endTime='无限制';
                    ?>
                    <li>
                        <div class="lpk_2_1">
                            <div class="lpk_2_1_1"><span><?=$card->yue?></span>元<br>抵扣金</div>
                            <div class="lpk_2_1_2">
                                <div class="lpk_2_1_2_1"><span>购物抵扣金</span><br>有效期至：<?=empty($card->endTime)?'永久有效':$card->endTime?></div>
                                <div class="lpk_2_1_2_2" onclick="show_rule(this);">使用规则<img src="/skins/erp_zong/images/lpk_icon3.png"></div>
                            </div>
                            <div class="lpk_2_1_3">
                                <a href="?p=8&a=card_liushui&id=<?=$card->id?>" class="lsjl" style="border-top:0px;">流水记录</a>
                                <? if($_SESSION['if_tongbu']==1 && $card->yue>0 && ($endTime>$now || $endTime=='无限制')){?>
                                    <a href="javascript:" onclick="zeng(<?=$card->id?>,<?=$card->yue?>);" class="lpkzs">抵扣金赠送</a>
                                <? }?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="lpk_2_2" style="display:none;">1.不可用其他优惠活动叠加使用。2.选择余额支付时不可用</div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                }
            }else{?>
                <li>
                    <div class="lpk_2_1">暂无抵扣金</div>
                </li>
            <? }?>
        </ul>
    </div>
</div>
<div class="wdlipinka_2" onclick="location.href='/index.php?p=8&a=lipinka'">
    <div class="wdlipinka_2_left">
        <img src="/skins/lipinka/images/wodelipinka_12.png"/> 可用抵扣金
    </div>
    <div class="wdlipinka_2_right">
        <img src="/skins/lipinka/images/wodelipinka_13.png"/>
    </div>
    <div class="clearBoth"></div>
</div>
<!--签到弹出-->
<!-- <div class="tanchu" id="add_card" style="display:none;">
    <div class="bj" onclick="$('#add_card').hide();"></div>
    <div class="bdhy">
        <div class="bdhy_1">绑定抵扣卡<img  onclick="$('#add_card').hide();" src="/skins/erp_zong/images/lpk_icon4.png"></div>
        <div class="bdhy_2">
            <div class="bdhy_2_1">卡号:</div>
            <div class="bdhy_2_2"><input id="add_card_id" type="text"></div>
            <div class="clearBoth"></div>
        </div>
        <div class="bdhy_2">
            <div class="bdhy_2_1">密码:</div>
            <div class="bdhy_2_2"><input id="add_card_pwd" type="text"></div>
            <div class="clearBoth"></div>
        </div>
        <div class="bdhy_3"><a href="javascript:" onclick="bangding()">立即绑定</a></div>
  </div>
</div> -->
<div class="lpk_6 tanchu" id="add_card" style="display:none;">
    <div class="bj" onclick="$('#add_card').hide();"></div>
    <div class="bdhy">
        <div class="lpk_6_01">
            <div class="lpk_6_01_left"> 
                绑定抵扣卡
            </div>
            <div class="lpk_6_01_right" onclick="$('#add_card').hide();">
                <img src="/skins/erp_zong/images/lpk_icon4.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="lpk_6_02">
            <ul>
                <li>
                    卡号:<input type="text" id="add_card_id" placeholder="请输入卡号" onblur="document.body.scrollTop = document.body.scrollTop+1;" />
                </li>
                <li>
                    密码:<input type="text" id="add_card_pwd" placeholder="请输入密码" onblur="document.body.scrollTop = document.body.scrollTop+1;" />
                </li>
            </ul>
        </div>
        <div class="lpk_6_03">
            <a href="javascript:" onclick="bangding();">确 定</a>
        </div>
    </div>
</div>
<div class="lpk_6 tanchu" id="add_zeng" style="display: none;">
    <div class="bj" onclick="$('#add_zeng').hide();"></div>
    <div class="bdhy">
        <div class="lpk_6_01">
            <div class="lpk_6_01_left"> 
                抵扣金赠送
            </div>
            <div class="lpk_6_01_right" onclick="$('#add_zeng').hide();">
                <img src="/skins/erp_zong/images/lpk_icon4.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="lpk_6_02">
            <ul>
                <li>
                    赠送金额：<input type="text" id="add_money" placeholder="请输入赠送金额 " onblur="document.body.scrollTop = document.body.scrollTop+1;"/>
                </li>
                <li>
                    赠送账号：<input type="text" id="add_user" placeholder="请输入赠送账号 " onblur="document.body.scrollTop = document.body.scrollTop+1;"/>
                </li>
            </ul>
        </div>
        <div class="lpk_6_03">
            <a href="javascript:" onclick="zengsong();">确 定</a>
        </div>
    </div>
</div>

<!-- <div class="tanchu" id="add_zeng" style="display:none;">
    <div class="bj" onclick="$('#add_zeng').hide();"></div>
    <div class="bdhy">
        <div class="bdhy_1">抵扣金赠送<img onclick="$('#add_zeng').hide();" src="/skins/erp_zong/images/lpk_icon4.png"></div>
        <div class="bdhy_2">
            <div class="bdhy_2_1">赠送金额:</div>
            <div class="bdhy_2_2"><input id="add_money" type="text"></div>
            <div class="clearBoth"></div>
        </div>
        <div class="bdhy_2">
            <div class="bdhy_2_1">赠送账号:</div>
            <div class="bdhy_2_2"><input id="add_user" type="text"></div>
            <div class="clearBoth"></div>
        </div>
        <div class="bdhy_3"><a href="javascript:" onclick="zengsong();">确定</a></div>
  </div>
</div> -->
<script type="text/javascript">
    var zeng_card_id = 0;
    var zeng_card_money = 0;
    var zeng_user_id = 0;
</script>
<script type="text/javascript" src="/skins/erp_zong/scripts/user/lipinka.js"></script>