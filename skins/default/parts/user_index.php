<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user = $db->get_row("select username,nickname,image,level,money,jifen,payPass,if_tuanzhang,earn from users where id=$userId");
$level = '会员';
if(!empty($user->level)){
  $level = $db->get_var("select title from user_level where id=$user->level");
}
if($_SESSION['if_tongbu']==1){
  $db_service = getCrmDb();
  $user->payPass = $db_service->get_var("select payPass from demo_user where id=".(int)$_SESSION['demo_zhishangId']);
}
$shezhi = $db->get_row("select fanli_type,if_shequ_tuan from demo_shezhi where comId=$comId");
$fanli_type = $shezhi->fanli_type;
/*if($_SESSION['if_tongbu']==1){
  $db_service = getCrmDb();
  $userId = $_SESSION[TB_PREFIX.'zhishangId'];
  $comId = 10;
  $user = $db_service->get_row("select username,name as nickname,image,level,money,jifen,payPass from demo_user where id=$userId");
  if(empty($user->level)){
    $level = '会员';
  }else{
    $level = $db->get_var("select title from user_level where id=$user->level");
  }
}else{
  $user = $db->get_row("select username,nickname,image,level,money,jifen,payPass from users where id=$userId");
  $level = $db->get_var("select title from user_level where id=$user->level");
}*/
$kefu_id = $db->get_var("select id from demo_list where comId=$comId and title like '%客服%' limit 1");
?>
<style type="text/css">
<? if($_SESSION['if_tongbu']==1){?>
.wode_1_down ul li{width:20%;}
<?}else{?>
.wode_1_down ul li{width:25%;}
<?}
?>
.wode_4_down{margin-top:.5rem;}
.wode_4_down ul li{width:<?=$shezhi->if_shequ_tuan==1?'25%':'33%'?>;float:left;text-align: center;}
.wode_4_down ul li img{width:1.5rem}
.wode_2_pintuan{padding-top:1rem}
.wode_2_pintuan ul li{float:left;width:25%;text-align:center}
.wode_2_pintuan ul li a{font-size:0.6rem;color:#333333}
.wode_2_pintuan ul li a img{vertical-align:top;width:1.5rem;margin-bottom:0.3rem}
</style>
<div id="shouye">
  <div class="wode_11">
      <div class="wode_1_up">
          <div class="wode_1_up_left" onclick="location.href='/index.php?p=8&a=zhgl';">
              <div class="wode_1_up_left_img">
                  <img src="<? if(empty($user->image)){?>/skins/default/images/wode_1.png<? }else{echo $user->image;}?>"/>
                </div>
              <div class="wode_1_up_left_tt">
                  <div><?=sys_substr($user->nickname,9,true);?> <img src="/skins/default/images/wode_11.png"/></div>
                    <span><?=$level?></span>
                    <?
                    if($comId==1113 && $user->level==78){
                      ?><a href="/index.php?p=1&a=shenqing1113" style="color: #ff0;font-weight: bold;margin-left: .5rem;">申请经销商</a><?
                    }
                    ?>
                </div>
              <div class="clearBoth"></div>
            </div>
          <div class="wode_1_up_right" style="text-align:right;">
              <a href="/index.php?p=8&a=zhgl"><img src="/skins/default/images/wode_12.png"/></a>
              <a href="/index.php?p=8&a=msg" style="position:relative;">
                <img src="/skins/default/images/wode_13.png"/>
                <span id="msg_num">0</span>
              </a><br>
              <? 
              $if_qiandao = $db->get_var("select if_qiandao from user_shezhi where comId=$comId");
              if($if_qiandao==1){
                $if_qiandao1 = $db->get_var("select days from user_qiandao where userId=$userId and dtTime='".date("Y-m-d")."' limit 1");
                if(empty($if_qiandao1)){
                  ?>
                  <a href="javascript:" onclick="qiandao(this);"><img src="/skins/default/images/wode_29.png" style="width:4.5rem;margin-top:.5rem"></a>
                  <?
                }else{
                  ?>
                  <a href="javascript:"><img src="/skins/default/images/wode_28.png" style="width:4.5rem;margin-top:.5rem"></a>
                  <?
                }
              }?>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="wode_1_down">
          <ul>
            <?
            if($_SESSION['if_tongbu']==1){
              ?>
              <li onclick="location.href='index.php?p=8&a=lipinka'">
                <h2 id="lipinka_num"></h2>抵扣金
              </li>
              <?
            }
            ?>
            <li onclick="location.href='index.php?p=8&a=yhq'">
              <h2 id="yhq_num"></h2>可用礼券
            </li>
            <li onclick="location.href='index.php?p=8&a=jifen'">
              <h2><?=$user->jifen?></h2>积分
            </li>
            <li>
              <a href="index.php?p=8&a=qianbao" style="color:#fff"><h2><?=$user->money?></h2>余额</a> <? if(empty($user->payPass)){?><a href="/index.php?p=8&a=editzfpwd"><img src="/skins/default/images/yuetanhao.png" style="width:.8rem;"></a><? }?>
            </li>
            <li>
              <a href="/index.php?p=8&a=earn_shouyi" style="color:#fff"><h2><?=$user->earn?></h2>佣金</a>
            </li>
            <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
  <div class="wode_2">
      <div class="wode_2_up">
        <div class="wode_2_up_left">
          我的订单
        </div>
        <div class="wode_2_up_right">
          <a href="/index.php?p=19&a=alone">查看全部订单 <img src="/skins/default/images/wode_14.png"/></a>
        </div>
        <div class="clearBoth"></div>
      </div>
      <div class="wode_2_down">
          <ul>
            <li>
              <a href="/index.php?p=19&a=alone&scene=1"><img src="/skins/default/images/wode_15.png"/><span></span><br>待付款</a>
            </li>
            <li>
              <a href="/index.php?p=19&a=alone&scene=2"><img src="/skins/default/images/wode_16.png"/><span></span><br>待发货</a>
            </li>
            <li>
              <a href="/index.php?p=19&a=alone&scene=3"><img src="/skins/default/images/wode_17.png"/><span></span><br>待收货</a>
            </li>
            <li>
              <a href="/index.php?p=19&a=alone&scene=4"><img src="/skins/default/images/wode_18.png"/><span></span><br>待评价</a>
            </li>
            <li>
              <a href="/index.php?p=21&a=shouhou"><img src="/skins/default/images/wode_19.png"/><span></span><br>退款/售后 </a>
            </li>
            <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
    <div class="wode_yongjin">
      <div class="wode_yongjin_up">
        <a href="/index.php?p=8&a=earn_shouyi">
          <div class="wode_yongjin_up_1">
            <div class="wode_yongjin_up_1_left">
              <img src="/skins/default/images/wode_earn_29.png"/> 累计收益 >
            </div>
            <div class="wode_yongjin_up_1_right">
              <?=$user->earn?>
            </div>
            <div class="clearBoth"></div>
          </div>
        </a>
        <a <? if($fanli_type==2){?>href="/index.php?p=8&a=earn_shengji"<? }?>>
          <div class="wode_yongjin_up_2">
            <div class="wode_yongjin_up_2_left">
              <img src="/skins/default/images/wode_30.png" /> 分享赚钱，赚取高额佣金
            </div>
            <div class="wode_yongjin_up_2_right">
              <? if($fanli_type==2){?>去升级 ><? }?>
            </div>
            <div class="clearBoth"></div>
          </div>
        </a>
      </div>
      <div class="wode_yongjin_down">
        <ul>
          <li>
            <a href="/index.php?p=8&a=earn_money"><img src="/skins/default/images/wode_31.png"/><br>订单明细</a>
          </li>
          <li>
            <a href="/index.php?p=8&a=earn_fans"><img src="/skins/default/images/wode_32.png"/><br>我的粉丝</a>
          </li>
          <li>
            <a href="/index.php?p=8&a=earn_yaoqing"><img src="/skins/default/images/wode_33.png"/><br>分享好友</a>
          </li>
          <!-- <li>
            <a href="/index.php?p=8&a=xinrenfuli"><img src="/skins/default/images/wode_34.png"/><br>新人福利社</a>
          </li> -->
          <div class="clearBoth"></div>
        </ul>
      </div>
    </div> 
    <div class="wode_2">
      <div class="wode_2_up">
          <div class="wode_2_up_left">
              我的团购
            </div>
          <div class="wode_2_up_right">
              <a href="/index.php?p=19&a=mytuan">查看全部团购 <img src="/skins/default/images/wode_14.png"></a>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="wode_2_pintuan">  
          <ul>
            <li>
                  <a href="/index.php?p=19&a=mytuan&scene=1"><img src="/skins/default/images/pintuan_36.png"><br>我开的团</a>
                </li>
                <li>
                  <a href="/index.php?p=19&a=mytuan&scene=2"><img src="/skins/default/images/pintuan_37.png"><br>待成团</a>
                </li>
                <li>
                  <a href="/index.php?p=19&a=mytuan&scene=3"><img src="/skins/default/images/pintuan_38.png"><br>拼团成功</a>
                </li>
                <li>
                  <a href="/index.php?p=19&a=mytuan&scene=4"><img src="/skins/default/images/pintuan_39.png"><br>拼团失败</a>
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
    <div class="wode_2">
      <div class="wode_2_up">
        <div class="wode_2_up_left">
          活动订单
        </div>
        <div class="clearBoth"></div>
      </div>
      <div class="wode_2_down">
          <ul>
            <li>
              <a href="/index.php?p=22&a=orders"><img src="/skins/default/images/bddd.png"/><span></span><br>本地订单</a>
            </li>
            <li>
              <a href="/index.php?p=19&a=mytuan"><img src="/skins/default/images/ptdd.png"/><span></span><br>团购订单</a>
            </li>
            <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
    <div class="wode_3">
      <ul>
        <li>
          <a href="/index.php?p=8&a=shoucang"><img src="/skins/default/images/wode_20.png"/><br>收藏的商品</a>
        </li>
        <li>
          <a href="/index.php?p=8&a=yhqList"><img src="/skins/default/images/wode_21.png"/><br>领券中心</a>
        </li>
        <li>
          <a href="/index.php?p=8&a=qianbao"><img src="/skins/default/images/wode_22.png"/><br>我的钱包</a>
        </li>
        <li>
          <a href="<?=empty($kefu_id)?'javascript:':'/index.php?p=5&a=view&id='.$kefu_id?>" onclick="$('#cp_kefu_tc').show();"><img src="/skins/default/images/wode_23.png"/><br>客服帮助</a>
        </li>
        <li>
          <a href="/index.php?p=4&a=jifen"><img src="/skins/default/images/wode_24.png"/><br>积分商城</a>
        </li>
        <li>
          <a href="/index.php?p=8&a=shouhuo"><img src="/skins/default/images/wode_25.png"/><br>收货地址</a>
        </li>
        <li>
          <a href="/index.php?p=19&a=alone&scene=5"><img src="/skins/default/images/wode_26.png"/><br>我的评论</a>
        </li>
        <li>
          <a href="/index.php?p=8&a=history"><img src="/skins/default/images/wode_27.png"/><br>浏览纪录</a>
        </li>
        <div class="clearBoth"></div>
      </ul>
    </div>
</div>
<?
$kefu = $db->get_row("select com_phone,com_kefu from demo_shezhi where comId=$comId");
$phone = $kefu->com_phone;
//$zxkefu = empty($kefu->com_kefu)?'https://kefu.zhishangez.com/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=kefu01&groupid=4':$kefu->com_kefu;
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
            <!-- <a href="<?=$zxkefu?>"><li class="cp_kefu_1_line">
              在线客服
            </li></a> -->
          </ul>
        </div>
      <div class="cp_kefu_2">
          <a href="javascript:" onclick="$('#cp_kefu_tc').hide();">取消</a>
        </div>
    </div>
</div>
<? require_once(ABSPATH.'/skins/default/bottom.php');?>
<script type="text/javascript" src="/skins/default/scripts/user/index.js"></script>