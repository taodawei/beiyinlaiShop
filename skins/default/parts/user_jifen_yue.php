<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$yue = $db->get_var("select jifen from users where id=$userId");
$jifen_rule = $db->get_row("select jifen_yue,jifen_yue_num,jifen_yue_limit from user_shezhi where comId=$comId");
?>
<div class="zhifu">
	<div class="zhifu_1">
    	积分兑换余额
        <div class="zhifu_1_left" onclick="location.href='/index.php?p=8&a=jfjl';">
        	<img src="/skins/default/images/fanhui_1.png" />
        </div>
    </div>
    <div class="zhifu_2">
        <div class="zhifu_2_left">
            剩余积分
        </div>
        <div class="zhifu_2_right">
            <?=$yue?>元
        </div>
        <div class="clearBoth"></div>
    </div>
	<div class="zhifu_2">
    	<div class="zhifu_2_left">
        	兑换积分
        </div>
    	<div class="zhifu_2_right">
        	<input type="number" id="money" max="<?=$yue?>" style="width:3rem;height:1.2rem">
        </div>
    	<div class="clearBoth"></div>
    </div>
    <div class="zhifu_2" style="height:auto;">
        <div class="zhifu_2_left">
            兑换规则
        </div>
        <div class="zhifu_2_right" style="font-size:.65rem;line-height:1.3rem;text-align:right;">
            <?=$jifen_rule->jifen_yue_num?>积分兑换1元<br>
            <? if(!empty($jifen_rule->jifen_yue_limit)){?>
                每天最多兑换<?=$jifen_rule->jifen_yue_limit?>积分
            <? }?>
        </div>
        <div class="clearBoth"></div>
    </div>
	<div class="zhifu_4">
    	<a href="javascript:" onclick="pay();">兑换</a>
    </div>
</div>
<script type="text/javascript">
    function pay(){
        var jifen = $("#money").val();
        layer.open({type:2});
        $.ajax({
            type: "POST",
            url: "/index.php?p=8&a=jifen2yue",
            data: "jifen="+jifen,
            dataType:"json",timeout : 10000,
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
</script>