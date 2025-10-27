<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
/*if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $db_service = getCrmDb();
  $user = $db_service->get_row("select id,username,name as nickname,image,level,money from demo_user where id=$userId");
}else{
  $user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
}*/

$ifktx = $db->get_var("select id from user_tixian where userId=$userId and comId=$comId and status=0 limit 1");
$ktx = $user->money;
$if_yue_tixian = $db->get_var("select if_yue_tixian from user_shezhi where comId=$comId");
?>
<div class="wode">
  <div class="wode_1">
      我的钱包
        <div class="wode_1_left" onclick="location.href='/index.php?p=8'">
          <img src="/skins/default/images/sousuo_1.png"/>
        </div>
        <div class="wode_1_qianbao">
          <a href="/index.php?p=8&a=qbmx">钱包明细&gt;</a>
        </div>
    </div>
  <div class="wodeqianbao">
      <div class="wodeqianbao_2">
          <img src="/skins/default/images/wodeqianbao_12.png"/>
            <h2>钱包余额</h2>
            <?=$user->money?> <span>元</span>
        </div>
      <div class="wodeqianbao_4">
          <a href="/index.php?p=8&a=yhk"><img src="/skins/default/images/wodeqianbao_13.png"/> 银行卡</a>
        </div>
      <div class="wodeqianbao_5">
          <a href="/index.php?p=8&a=chongzhi" >充值</a>
          <? if($ktx>0 && empty($ifktx)){
              if($if_yue_tixian==0){
                ?>
                <a href="javascript:layer.open({content:'未开放提现功能',skin: 'msg',time: 2});" style="background: #dadbdd">提现</a>
                <?
              }else{
            ?>
            <a href="/index.php?p=8&a=tixian">提现</a>
            <? 
            }
          }else if($ifktx){?>
            <a href="javascript:layer.open({content:'您的提现申请正在审核中，请耐心等待。',skin: 'msg',time: 2});">提现</a>
          <? }else{?>
            <a href="javascript:;" style="background: #dadbdd">提现</a>
          <? }?>
        </div>
    </div>
</div>