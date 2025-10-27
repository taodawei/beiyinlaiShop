<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$qiandao = $db->get_row("select if_qiandao,qiandao_rule from user_shezhi where comId=$comId");
if($qiandao->if_qiandao!=1){
  die('签到功能已关闭');
}
$qiandao_rule = $qiandao->qiandao_rule;
if(!empty($qiandao_rule)){
  $rule = json_decode($qiandao_rule,true);
}
$yesterday = (int)$db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='".date("Y-m-d")."' limit 1");
if(empty($yesterday))$yesterday=1;
$today_jifen = $rule['jifen'];
$next_jifen = $rule['jifen'];
$three_jifen = $rule['jifen'];
if($rule['type']==2){
  $first =$rule['first'];
  $maxday = $rule['day'];
  $leijia = $rule['leijia'];
  if($yesterday>$maxday+1){
    $yesterday = $maxday+1;
  }
  $today_jifen = $first+($yesterday-1)*$leijia;
  $tommrow = $yesterday+1;
  if($tommrow>$maxday+1){
    $tommrow = $maxday+1;
  }
  $next_jifen = $first+($tommrow-1)*$leijia;
  $three = $yesterday+2;
  if($three>$maxday+1){
    $three = $maxday+1;
  }
  $three_jifen = $first+($three-1)*$leijia;
}
?>
<link href="/skins/default/styles/jifen.css" rel="stylesheet" type="text/css">
<div class="wdqiandaojilu">
  <div class="wdqiandaojilu_up">  
      <div class="wdqiandaojilu_up_1" onclick="go_prev_page();">
        <img src="/skins/default/images/a925_1.png"/>
      </div>
      <div class="wdqiandaojilu_up_2">
          签到有礼<? if($yesterday>0){?>，奖励到账<? }?>
      </div>
      <div class="wdqiandaojilu_up_3">
          <ul>
            <li>
              <a><? if($yesterday>0){?>已领取<? }else{?>积分+<?echo $today_jifen;}?></a>
            </li>
            <li>
              <a style="color:#ffffff;">积分+<?=$next_jifen?></a>
            </li>
            <li>
              <a style="color:#f9abaa; background-image:url(/skins/default/images/a925_22.png);">积分+<?=$three_jifen?></a>
            </li>
            <div class="clearBoth"></div>
          </ul>
        </div>
      <div class="wdqiandaojilu_up_4">
          <ul>
            <li>
                  <img src="/skins/default/images/a925_23.png"/>
                </li>
                <li>
                  <span>2</span>
                </li>
                <li>
                  <span>3</span>
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
        <div class="wdqiandaojilu_up_5">
          <ul>
            <li>
                  今天
                </li>
                <li>
                  明天
                </li>
                <li>
                  后天
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
  <div class="wdqiandaojilu_down">
      <div class="wdqiandaojilu_down_1">
        签到日历（<?=(int)date('m')?>月）
      </div>
      <div class="wdqiandaojilu_down_2">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <?
                $next_month = date("Y-m-01",strtotime('+1 month'));echo  $next_month;
                $days = date("d",strtotime('-1 day',strtotime($next_month)));
                $today = date("d");
                $month = date("Y-m-");
                for ($i=1; $i <=$days ; $i++) { 
                  $if_qiandao = $db->get_var("select userId from user_qiandao_jilu where userId=$userId and comId=$comId and dtTime='".$month.$i."'");
                  ?>
                  <td width="100" align="center" valign="middle">
                    <?
                    if($today==$i){?><span class="wdqiandaojilu_down_2_jinri"><?=$i?></span><? }else{echo $i;}
                    if($if_qiandao>0){
                    ?>
                    <br><img src="/skins/default/images/a925_25.png"/>
                    <? }?>
                  </td><?
                  if($i%7==0){?></tr><tr><?}
                }
                ?>
              </tr>
            </table>
        </div>
    </div>
</div>