<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$share_url = 'https://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg_tuanzhang&tuijianren='.$userId;
$fenbiao = getYzFenbiao($userId,20);
$nums = $db->get_var("select count(*) from users where shangji_id=$userId");
$money = $db->get_var("select sum(money) from user_liushui$fenbiao where userId=$userId and type=2 and (remark='团队奖励' or remark='自营收入')");
$user = $db->get_row("select nickname,image from users where id=$userId");
?>
<style type="text/css">
    html,body,.sousuo{height:100%}
</style>
<div class="sousuo" style="background:url(/skins/default/images/tuanzhangyaoqing_13.gif) center top no-repeat; background-size:cover; background-color:#f34f4d;">
    <div class="wode_1">
        邀请返利
        <div class="wode_1_left" onclick="history.go(-1);">
            <img src="/skins/default/images/sousuo_1.png"/>
        </div>
        <div class="wode_1_tuanzhangyaoqing" onclick="location.href='/index.php?p=8&a=yaoqing';">
            <img src="/skins/default/images/pintuanshangpinye_14.png"/>
        </div>
    </div>
    <div class="tuanzhangyaoqing">
        <div class="tuanzhangyaoqing_1">
            <ul>
                <li>
                    <a>
                        <div class="tuanzhangyaoqing_1_img">
                            <img src="/skins/default/images/tuanzhangyaoqing_1.png"/> 
                        </div>
                        <div class="tuanzhangyaoqing_1_tt">
                            <h2>成功邀请人数</h2><?=$nums?>人
                        </div>
                        <div class="clearBoth"></div>
                    </a>
                </li>
                <li>
                    <a>
                        <div class="tuanzhangyaoqing_1_img">
                            <img src="/skins/default/images/tuanzhangyaoqing_11.png"/> 
                        </div>
                        <div class="tuanzhangyaoqing_1_tt">
                            <h2>累计赚取返利</h2><?=empty($money)?0:$money?>元
                        </div>
                        <div class="clearBoth"></div>
                    </a>
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="tuanzhangyaoqing_2">
            <img src="/skins/default/images/tuanzhangyaoqing_12.png"/>
        </div>
        <div class="tuanzhangyaoqing_3">
            <div class="tuanzhangyaoqing_3_up">
                <a>返利规则</a>
            </div>
            <div class="tuanzhangyaoqing_3_down">
                1.邀请好友申请团长<br><br>
                2.审核通过后您的下级团长升级至总监，你将获得
  888/1288元奖励，升级至总监，你将获得2588元奖励。<br><br>
                3.享受下级团长的购物返利。
            </div>
        </div>
        <div class="tuanzhangyaoqing_4">
            <a href="/index.php?p=8&a=yaoqing">立即邀请</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$user->nickname?>邀请你成为团长！';
    var share_img = '<?=ispic($user->image,'/skins/default/images/wode_1.png')?>';
    var share_desc = '';
    var userId = <?=$userId?>;
    $(function(){
        var url = window.location.href;
        url = encodeURIComponent(url);
        WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
    function share(){
        $('#fenxiang_tc').show();
    }
</script>