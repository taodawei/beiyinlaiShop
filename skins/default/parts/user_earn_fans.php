<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
if($comId==10){
  $db_service = getCrmDb();
  $tuanzhang_id = $db_service->get_var("select tuan_id from demo_user where id=$userId");
  $fans = (int)$db_service->get_var("select count(*) from demo_user where shangji=$userId or tuan_id=$userId");
  if($tuanzhang_id>0){
    $tuanzhang = $db_service->get_row("select image,name as nickname,user_info from demo_user where id=$tuanzhang_id");
  }
}else{
  $tuanzhang_id = $db->get_var("select tuan_id from users where id=$userId");
  $fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
  $fans = (int)$db->get_var("select count(*) from users where comId=$comId and (shangji=$userId or ".($fanli_type==2?'tuan_id':'shangshangji')."=$userId) and id<>$userId") ;
  if($tuanzhang_id>0 && $fanli_type==2){
    $tuanzhang = $db->get_row("select image,nickname,user_info from users where id=$tuanzhang_id");
  }
}
$shangji_phone = '无';
$tuanzhang_phone = '无';
$rst = $db->get_row("select shangji,tuan_id from users where id=$userId");
if(!empty($rst->shangji)){
  $phone = $db->get_var("select username from users where id=$rst->shangji");
  $shangji_phone = substr($phone,0,3).'****'.substr($phone,7,4);
}
if(!empty($rst->tuan_id)){
  $phone = $db->get_var("select username from users where id=$rst->tuan_id");
  $tuanzhang_phone = substr($phone,0,3).'****'.substr($phone,7,4);
}
//$users_yaoqing = $db->get_row("select * from users_yaoqing where userId=$userId");
?>
<link rel="stylesheet" type="text/css" href="/skins/default/styles/yongjin.css">
<div class="wodefensi" style="background:url(/skins/default/images/wodefensi_1.gif) center top no-repeat #f6f6f6; background-size:100%<? if(empty($tuan_id)){?> 12.2rem;<? }?>">
  <div class="wodefensi_1">
      我的粉丝
        <div class="wodefensi_1_left" onclick="go_prev_page();">
          <img src="/skins/default/images/fenlei_1.png" />
        </div>
    </div>
  <div class="wodefensi_2">
      <h2>我的粉丝</h2><?=$fans?>
  </div>
  <?
  if(!empty($tuan_id)){
    $user_info = json_decode($tuanzhang->user_info,true);
  ?>
  <div class="wodefensi_3"> 
      <div class="wodefensi_3_01">
          <img src="/skins/default/images/huiyuanshengji_1.png" />
        </div>
      <div class="wodefensi_3_02">
        <h2>团长：<?=$tuanzhang->nickname?></h2>微信号：<?=$user_info['wxh']?>
      </div>
      <div class="wodefensi_3_03">
        <a href="javascript" id="copy_weixin" data-clipboard-text="<?=$user_info['wxh']?>">复制</a>
      </div>
      <div class="clearBoth"></div>
    </div>
  <? }else{?>
    <div class="wodefensi_tuanzhang" onclick="$('#cp_kefu_tc').show();">
      <span>联系客服</span>
    </div>
  <? }?>
  <div class="wodefensi_4">
      <ul>
        <li>
              <a href="javascript:" onclick="qiehuan_scene(0)" class="wodefensi_4_on">全部</a>
            </li>
            <li>
              <a href="javascript:" onclick="qiehuan_scene(1)">直属粉丝</a>
            </li>
            <li>
              <a href="javascript:" onclick="qiehuan_scene(2)">推荐粉丝</a>
            </li>
            <div class="clearBoth"></div>
      </ul>
    </div>
    <? if($fans==0){?>
      <div class="wodefensi_wu">
      <div class="wodefensi_wu_1">
          <ul>
            <li>
                  <img src="/skins/default/images/wodefensi_11.png" /><br>邀请好友
                </li>
                <li>
                  <img src="/skins/default/images/wodefensi_12.png" /><br>好友注册下单
                </li>
                <li>
                  <img src="/skins/default/images/wodefensi_13.png" /><br>获得奖励
                </li>
                <div class="clearBoth"></div>
          </ul>
      </div>
      <div class="wodefensi_wu_2">
          还没有粉丝呢，快邀请好友使用直商易购吧！
      <br>好友下单，你也有机会获取佣金哦
        </div>
      <div class="wodefensi_wu_3">
          <a href="/index.php?p=8&a=earn_yaoqing">立即邀请</a>
        </div>
        <ul id="flow_ul" style="display:none;"></ul>
    </div>
    <? }else{?>
    <div class="wodefensilist">
      <div style="line-height:1rem;height:2rem;">
        已下单<font id="hasnum" color="red"></font>，未下单<font color="red" id="weinum"></font>
      </div>
      <ul id="flow_ul">
        
      </ul>
    </div>
    <div class="wodefensilist_di">
      <div style="width:49.5%;display:inline-block;text-align:center;">我的邀请人：<?=$shangji_phone?></div>
      <div style="width:49.5%;display:inline-block;text-align:center;">我的团长：<?=$tuanzhang_phone?></div>
    </div>
    <? }?>
</div>
<!--粉丝详细-->
<div class="wodefensi_xiangxi_tc" id="wodefensi_xiangxi_tc" style="display:none;">
  <div class="bj" onclick="$('#wodefensi_xiangxi_tc').hide()">
    </div>
  <div class="wodefensi_xiangxi">
      <div class="wodefensi_xiangxi_1" onclick="$('#wodefensi_xiangxi_tc').hide()">
          <img src="/skins/default/images/huiyuanshengji_1.png" />
        </div>
      <div class="wodefensi_xiangxi_2">
          <div class="wodefensi_xiangxi_2_01" onclick="$('#wodefensi_xiangxi_tc').hide()">
              <img src="/skins/default/images/wodefensi_14.png" />
            </div>
          <div class="wodefensi_xiangxi_2_02">
              
            </div>
          <div class="wodefensi_xiangxi_2_03">
              微信号：未填写
            </div>
        </div>
      <div class="wodefensi_xiangxi_3">
          <h2>0</h2>粉丝数
        </div>
      <div class="wodefensi_xiangxi_4">
          <ul>
            <li class="wodefensi_xiangxi_4_line">
                  <h2>0.0元</h2>上月预估收入
                </li>
                <li>
                  <h2>0.0元</h2>累积收入
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
      <div class="wodefensi_xiangxi_5">
          注册时间：
        </div>
      <div class="wodefensi_xiangxi_6">
        </div>
      <div class="wodefensi_xiangxi_7">
        </div>
    </div>
</div>
<!--客服-弹出-->
<?
$kefu = $db->get_row("select com_phone,com_kefu from demo_shezhi where comId=$comId");
$phone = $kefu->com_phone;
$zxkefu = empty($kefu->com_kefu)?'https://kefu.zhishangez.com/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=kefu01&groupid=4':$kefu->com_kefu;
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
            <a href="<?=$zxkefu?>"><li class="cp_kefu_1_line">
              在线客服
            </li></a>
          </ul>
        </div>
      <div class="cp_kefu_2">
          <a href="javascript:" onclick="$('#cp_kefu_tc').hide();">取消</a>
        </div>
    </div>
</div>
<script type="text/javascript">
  var scene = 0;
  var keyword = '';
</script>
<script type="text/javascript" src="/skins/resource/scripts/clipboard.min.js"></script>
<script type="text/javascript" src="/skins/default/scripts/user/earn_fans.js?v=1.1"></script>
