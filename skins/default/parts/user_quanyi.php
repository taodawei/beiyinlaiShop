<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money,jifen from users where id=$userId");
switch ($user->level) {
    case 1:
    $level = '会员';
    break;
    case 2:
    $level = '团长';
    break;
    case 3:
    $level = '总监';
    break;
    case 4:
    $level = '联创';
    break;
}
$levels = $db->get_results("select level_jifen,level_money from user_level order by id asc");
$yzFenbiao = getYzFenbiao($userId,20);
$zyMoney = $db->get_var("select sum(money) from user_liushui$yzFenbiao where userId=$userId and remark='自营收入'");
$reason = '请联系您的上级团长邀请您成为团长';
if($user->level>1 && $user->level<4){
  $next_money = $levels[$level]->level_money;
  $next_jifen = $levels[$level]->level_jifen;
  if($user->jifen<$next_jifen){
    $kesheng = 0;
    $reason = '积分需要达到'.$levels[$level]->level_jifen.'能进行升级';
  }else if($zyMoney<$next_money){
    $kesheng = 0;
    $reason = '自营收入需要到达'.$levels[$level]->level_money.'才能升级';
  }else{
    $kesheng = 1;
  }
}
?>
<div class="dengjiquanyi">
  <div class="dengjiquanyi_1">
      等级权益
        <div class="dengjiquanyi_1_left" onclick="location.href='/index.php?p=8'">
          <img src="/skins/default/images/sousuo_1.png"/>
        </div>
        <div class="dengjiquanyi_1_right">
          <img src="/skins/default/images/dengjiquanyi_34.png"/> 升级秘籍
        </div>
    </div>
  <div class="dengjiquanyi_2">
      <img src="<? if(empty($user->image)){?>/skins/default/images/wode_1.png<? }else{echo $user->image;}?>"/>  <?=substr($user->username,0,3).'****'.substr($user->username,7); ?> <span><?=$level?></span>
    </div>
  <div class="dengjiquanyi_3">
      <ul>
        <li><a href="/index.php?p=8&a=jfjl">
          我的积分（分）
          <h2><?=$user->jifen?></h2></a>
        </li>
        <li>
          自营收入（元）
          <h2><?=empty($zyMoney)?0:$zyMoney?></h2>          
        </li>
        <div class="clearBoth"></div>
      </ul>
    </div>
    <? if($user->level<4){?>
    <div class="dengjiquanyi_4">
      <a href="javascript:" <? if($kesheng==1){?>onclick="shengji();"<?}else{?>onclick="layer.open({content:'<?=$reason?>',skin: 'msg',time: 2});"<? }?>>立即升级 <img src="/skins/default/images/dengjiquanyi_11.png"/></a>
        <span></span>
    </div>
    <? }?>
    <div class="dengjiquanyi_5" id="quanyiMenu">
      <ul>
        <li>
          <a href="javascript:" id="quanyiMenu1" onclick="qiehuan('quanyi',1,'dengjiquanyi_5_on')" <? if($user->level==1){?>class="dengjiquanyi_5_on"<? }?>>
            <img src="/skins/default/images/dengjiquanyi_12.png"/><br>会员 
          </a>
        </li>
        <li>
          <a href="javascript:" id="quanyiMenu2" onclick="qiehuan('quanyi',2,'dengjiquanyi_5_on')" <? if($user->level==2){?>class="dengjiquanyi_5_on"<? }?>>
            <img src="/skins/default/images/dengjiquanyi_1<? if($user->level>1){echo '2';}else{echo '3';}?>.png"/><br>团长 
          </a>
        </li>
        <li>
          <a href="javascript:" id="quanyiMenu3" onclick="qiehuan('quanyi',3,'dengjiquanyi_5_on')" <? if($user->level==3){?>class="dengjiquanyi_5_on"<? }?>>
            <img src="/skins/default/images/dengjiquanyi_1<? if($user->level>2){echo '2';}else{echo '3';}?>.png"/><br>总监 
          </a>
        </li>
        <li>
          <a href="javascript:" id="quanyiMenu4" onclick="qiehuan('quanyi',4,'dengjiquanyi_5_on')" <? if($user->level==4){?>class="dengjiquanyi_5_on"<? }?>>
            <img src="/skins/default/images/dengjiquanyi_1<? if($user->level>3){echo '2';}else{echo '3';}?>.png"/><br>联创 
          </a>
        </li>
        <div class="clearBoth"></div>
      </ul>
    </div>
    <div class="dengjiquanyi_6 quanyiCont" id="quanyiCont1" <? if($user->level>1){?>style="display:none"<? }?>>
      <div class="dengji_huiyuan">
          <div class="dengjihuiyuan_1">
              注册一指围城即可成为普通会员
            </div>
          <div class="dengjihuiyuan_2"> 
              <span>等级权益</span>
            </div>
          <div class="dengjihuiyuan_3">
              <ul>
                <li>
                  <a href="javascript:"><img src="/skins/default/images/dengjiquanyi_26.png"/><br>开团</a>
                </li>
                <div class="clearBoth"></div>
              </ul>
            </div>
        </div>
    </div>
    <div class="dengjiquanyi_6 quanyiCont" id="quanyiCont2" <? if($user->level!=2){?>style="display:none"<? }?>>
      <div class="dengji_huiyuan">
          <div class="dengjihuiyuan_1">
              积分清零<br><span>根据提示联系上级团长邀请你申请团长！</span>
            </div>
          <div class="dengjihuiyuan_2"> 
              <span>等级权益</span>
            </div>
          <div class="dengjihuiyuan_3">
              <ul>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>1){echo 26;}else{echo 18;}?>.png"/><br>开团</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>1){echo 27;}else{echo 19;}?>.png"/><br>获取返利</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>1){echo 28;}else{echo 20;}?>.png"/><br>邀请商家入驻</a></li>
                <div class="clearBoth"></div>
              </ul>
            </div>
        </div>
    </div>
    <div class="dengjiquanyi_6 quanyiCont" id="quanyiCont3" <? if($user->level!=3){?>style="display:none"<? }?>>
      <div class="dengji_huiyuan">
          <div class="dengjihuiyuan_1">
            消耗<?=$levels[2]->level_jifen?>积分<br><span>并且自营收入达<?=$levels[2]->level_money?>元</span>
          </div>
          <div class="dengjihuiyuan_2"> 
              <span>等级权益</span>
            </div>
          <div class="dengjihuiyuan_3">
              <ul>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>2){echo 26;}else{echo 18;}?>.png"/><br>开团</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>2){echo 27;}else{echo 19;}?>.png"/><br>获取返利</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>2){echo 28;}else{echo 20;}?>.png"/><br>邀请商家入驻</a></li>
                <div class="clearBoth"></div>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>2){echo 29;}else{echo 21;}?>.png"/><br>升级礼品<br><span>（一套沙发）</span></a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>2){echo 30;}else{echo 22;}?>.png"/><br>奖励888元<br><span>（若下级团长升级至总监）</span></a></li>
                <div class="clearBoth"></div>
              </ul>
            </div>
        </div>
    </div>
    <div class="dengjiquanyi_6 quanyiCont" id="quanyiCont4" <? if($user->level!=4){?>style="display:none"<? }?>>
      <div class="dengji_huiyuan">
          <div class="dengjihuiyuan_1">
            消耗<?=$levels[3]->level_jifen?>积分<br><span>并且自营收入达<?=$levels[3]->level_money?>元</span>
          </div>
          <div class="dengjihuiyuan_2"> 
              <span>等级权益</span>
            </div>
          <div class="dengjihuiyuan_3">
              <ul>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 26;}else{echo 18;}?>.png"/><br>开团</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 27;}else{echo 19;}?>.png"/><br>获取返利</a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 28;}else{echo 20;}?>.png"/><br>邀请商家入驻</a></li>
                <div class="clearBoth"></div>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 29;}else{echo 21;}?>.png"/><br>升级礼品<br><span>（价值1万元家电）</span></a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 30;}else{echo 22;}?>.png"/><br>奖励888元/2588元<br><span>（若下级团长升级至总监/联创）</span></a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 31;}else{echo 23;}?>.png"/><br>积分兑换</a></li>
                <div class="clearBoth"></div>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 32;}else{echo 24;}?>.png"/><br>免费参加年会<br><span>（高规格接待）</span></a></li>
                <li><a href="javascript:"><img src="/skins/default/images/dengjiquanyi_<? if($user->level>3){echo 33;}else{echo 25;}?>.png"/><br>免费旅游<br><span>（每月收入总额超过5000元，连续3个月）</span></a></li>
                <div class="clearBoth"></div>
              </ul>
            </div>
        </div>
    </div>
</div>
<!--升级秘籍弹出-->
<div class="quanyishengji_tc" style="display:none;">
  <div class="bj">
    </div>
  <div class="quanyishengji">
      <div class="quanyishengji_1">
          升级秘籍
        </div>
      <div class="quanyishengji_2">
          <div class="quanyishengji_21">
              <div class="quanyishengji_21_up">
                  如何获得积分？
                </div>
              <div class="quanyishengji_21_down">
                  <ul>
                    <li>
                          <div class="quanyishengji_21_down_left">
                              <img src="/skins/default/images/dengjiquanyi_35.png"/>
                            </div>
                          <div class="quanyishengji_21_down_right">
                              1.分享平台产品给好友或微信群朋友圈（仅限普通会员等级）
                            </div>
                          <div class="clearBoth"></div>
                        </li>
                        <li>
                          <div class="quanyishengji_21_down_left">
                              <img src="/skins/default/images/dengjiquanyi_35.png"/>
                            </div>
                          <div class="quanyishengji_21_down_right">
                              2.每日签到
                            </div>
                          <div class="clearBoth"></div>
                        </li>
                        <li>
                          <div class="quanyishengji_21_down_left">
                              <img src="/skins/default/images/dengjiquanyi_35.png"/>
                            </div>
                          <div class="quanyishengji_21_down_right">
                              3.下级团长每收入一元钱，上级团长会得到1积分。
                            </div>
                          <div class="clearBoth"></div>
                        </li>
                  </ul>
                </div>
            </div>
            <div class="quanyishengji_21">
              <div class="quanyishengji_21_up">
                  如何获得自营收入？
                </div>
              <div class="quanyishengji_21_down">
                  <ul>
                    <li>
                          <div class="quanyishengji_21_down_left">
                              <img src="/skins/default/images/dengjiquanyi_35.png"/>
                            </div>
                          <div class="quanyishengji_21_down_right">
                              1.自己购物获得返利
                            </div>
                          <div class="clearBoth"></div>
                        </li>
                        <li>
                          <div class="quanyishengji_21_down_left">
                              <img src="/skins/default/images/dengjiquanyi_35.png"/>
                            </div>
                          <div class="quanyishengji_21_down_right">
                              2.锁定客户购物获得返利
                            </div>
                          <div class="clearBoth"></div>
                        </li>
                  </ul>
                </div>
            </div>
        </div>
      <div class="quanyishengji_3">
          <a href="javascript:">知道了</a>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(function(){
    $(".dengjiquanyi_1_right").click(function(){
      $(".quanyishengji_tc").show();
    });
    $(".quanyishengji_3 a").click(function(){
      $(".quanyishengji_tc").hide();
    });
  });
  function shengji(){
    layer.open({type:2});
    $.ajax({
      type: "POST",
      url: "/index.php?p=8&a=shengji",
      data: "",
      dataType:"json",timeout : 8000,
      success: function(res){
        layer.closeAll();
        layer.open({content:res.message,skin: 'msg',time: 2});
        if(res.code==1){
          setTimeout(function(){
            location.reload();
          },1500);
        }
      },
      error: function() {
        layer.closeAll();
        layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
      }
    });
  }
</script>
