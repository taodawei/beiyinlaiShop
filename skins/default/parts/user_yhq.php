<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}
$fenbiao = getFenbiao($comId,20);
$num1 = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
$num2 = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and status=1");
$num3 = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime<'".date("Y-m-d H:i:s")."'");
?>
<link href="/skins/default/styles/youhuiquan.css" rel="stylesheet" type="text/css">
<div class="zsyhq" style="background-color:#f6f6f6;">
  <div class="zsyhq_1">
    优惠券
    <div class="zsyhq_1_left" onclick="go_prev_page();">
      <img src="/skins/default/images/a923_1.png"/>
    </div>
    <div class="zsyhq_1_right" onclick="location.href='/index.php?p=8&a=yhqList'">
      领券中心
    </div>
  </div>
  <div class="zsyhq_2">
      <ul>
        <li>
          <a href="javascript:" onclick="qiehuan_yhq(1)" class="zsyhq_2_on">未使用(<font id="wei_num"><?=$num1?></font>)</a>
        </li>
        <li>
          <a href="javascript:" onclick="qiehuan_yhq(2)">已使用(<?=$num2?>)</a>
        </li>
        <li>
          <a href="javascript:" onclick="qiehuan_yhq(3)">已过期(<?=$num3?>)</a>
        </li>
        <div class="clearBoth"></div>
      </ul>
  </div>
  <div class="zsyhq_3">
      <ul id="flow_ul">
        
      </ul>
    </div>
</div>
<!--转增-弹出-->
<div class="zsyhq_zhuanzeng_tc" id="zsyhq_zhuanzeng_tc" style="display:none;">
  <div class="bj" onclick="$('#zsyhq_zhuanzeng_tc').hide();">
    </div>
  <div class="zsyhq_zhuanzeng">
      <div class="zsyhq_zhuanzeng_1">
          赠送给好友
          <div class="zsyhq_zhuanzeng_1_right" onclick="$('#zsyhq_zhuanzeng_tc').hide();">
            <img src="/skins/default/images/miaoshaxx_youhuiquan_1.png"/>
          </div>
      </div>
      <div class="zsyhq_zhuanzeng_2">
          <div class="zsyhq_3_left" id="e_color" style="background-color:#3c8bec;">  
                <h2>￥<b id="e_jian">0</b></h2>满<i id="e_man">0</i>元可用
            </div>
            <div class="zsyhq_3_right">
                <div class="zsyhq_3_right_1" id="e_tiaojian">
                    仅可购买本店产品全部商品使用
                </div>
                <div class="zsyhq_3_right_2" id="e_time">
                    2019.03.09-2019.03.30
                </div>
                <div class="zsyhq_3_right_3">
                    <div class="zsyhq_3_right_3_left">
                        <img src="/skins/default/images/a928_12.png"/> 可赠送
                    </div>
                    <div class="zsyhq_3_right_3_right">
                       
                    </div>
                    <div class="clearBoth"></div>
                </div>
            </div>
            <div class="clearBoth"></div>
        </div>
      <div class="zsyhq_zhuanzeng_3">
          赠送出的券在您的帐户不能使用了哦！
        </div>
      <div class="zsyhq_zhuanzeng_4" onclick="location.href='/index.php?p=5';">
          优惠券赠送规则 >
        </div>
      <div class="zsyhq_zhuanzeng_5">
          赠送账号: <input id="add_user" placeholder="请输入赠送账号" onblur="document.body.scrollTop = document.body.scrollTop+1;" type="text"/>
        </div>
      <div class="zsyhq_zhuanzeng_6">
          <a href="javascript:" onclick="zengsong();"><img src="/skins/default/images/a921_02.png"/></a>
        </div>
    </div>
</div>

<script type="text/javascript" src="/skins/default/scripts/user/yhq.js"></script>