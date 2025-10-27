<?
global $db;
$comId = (int)$_SESSION['demo_comId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$if_tuanzhang = $db->get_var("select if_tuanzhang from users where id=$userId");
if($if_tuanzhang==1){
    echo '<script>alert("您已经是团长了");history.go(-1);</script>';
    exit;
}
$tuanzhang_rule = $db->get_var("select tuanzhang_rule from demo_shezhi where comId=$comId");
$rule = array();
if(!empty($tuanzhang_rule)){
    $rule = json_decode($tuanzhang_rule,true);
}
$userNums = (int)$db->get_var("select count(*) from users where comId=$comId and shangji=$userId");
$earn = $db->get_var("select earn from users where id=$userId");
$shengji = 1;$msg = '';
$rule['yaoqing_num'] = empty($rule['yaoqing_num'])?0:$rule['yaoqing_num'];
$rule['yaoqing_yongjin'] = empty($rule['yaoqing_yongjin'])?0:$rule['yaoqing_yongjin'];
if($userNums<$rule['yaoqing_num']){
    $shengji = 0;
    $msg = '邀请人数不足'.$rule['yaoqing_num'].'，不能升级成为团长';
}else if($earn<$rule['yaoqing_yongjin']){
    $shengji = 0;
    $msg = '佣金不足'.$rule['yaoqing_yongjin'].'，不能升级成为团长';
}
?>
<link rel="stylesheet" type="text/css" href="/skins/shequ/styles/index.css">
<div class="zhaomutuanzhang">
	<div class="zhaomutuanzhang_fanhui" onclick="go_prev_page();">
    	<img src="/skins/shequ/images/fanhui_1.png" />
    </div>
	<div class="zhaomutuanzhang_1">
    	<img src="/skins/shequ/images/a1212_01.gif" />
    </div>
	<div class="zhaomutuanzhang_2">	
    	<div class="zhaomutuanzhang_2_up">
        	成为团长<br><img src="/skins/shequ/images/a1212_02.gif" />
        </div>
    	<div class="zhaomutuanzhang_2_down">
        	<ul>
        		<li>
                	<div class="zhaomutuanzhang_2_down_img">
                    	<img src="/skins/shequ/images/a1212_03.png" />
                    </div>
                	<div class="zhaomutuanzhang_2_down_tt">
                    	<h2>会员数</h2><?=$userNums?>/<?=$rule['yaoqing_num']?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="zhaomutuanzhang_2_down_img">
                    	<img src="/skins/shequ/images/a1212_04.png" />
                    </div>
                	<div class="zhaomutuanzhang_2_down_tt">
                    	<h2>佣金</h2><?=$earn?>/<?=$rule['yaoqing_yongjin']?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    </div>
	<div class="zhaomutuanzhang_3">
    	<a href="javascript:" onclick="shengji();">申请成为团长</a>
    </div>
</div>
<script type="text/javascript">
    function shengji(){
        var shengji = <?=$shengji?>;
        var msg = '<?=$msg?>';
        if(shengji==0){
            layer.open({content:msg,skin: 'msg',time: 2});
            setTimeout(function(){
                location.href='/index.php?p=8&a=earn_yaoqing';
            },2000);
            return false;
        }
        location.href="/index.php?p=8&a=to_tuanzhang";
        /*$.ajax({
            type:"POST",
            url:"/index.php?p=1&a=shenqing_tuan",
            data:"tijiao=1",
            timeout:"10000",
            dataType:"json",
            success: function(res){
                layer.closeAll();
                layer.open({content:res.message,skin: 'msg',time: 2});
                setTimeout(function(){
                    location.href='/index.php?p=8';
                },2000);
            },
            error:function(){
                alert("超时,请重试");
            }
        });*/
    }
</script>