<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money,shangji_id, shangshangji from users where id=$userId");
$level = array(1=>'普通', 2=>'团长',3=>'总监',4=>'联创');
$keyword = trim($request['keyword']);
?>
<script type="text/javascript" src="/skins/demo/scripts/clipboard.min.js"></script>
<div class="sousuo">
  <div class="sousuo_1">
      <div class="sousuo_1_01" onclick="location.href='/index.php?p=8'">
          <img src="/skins/default/images/sousuo_1.png" />
        </div>
        <form id="sssForm" action="/index.php" method="get">
          <input type="hidden" name="p" value="8">
          <input type="hidden" name="a" value="ziyuan">
      <div class="sousuo_1_02">
          <div class="sousuo_1_02_left">
              <img src="/skins/default/images/sou_1.png" onclick="$('#sssForm').submit();" />
            </div>
          <div class="sousuo_1_02_right">
              <input name="keyword" type="text" placeholder="搜索姓名/手机号"/>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="sousuo_1_03">
          
        </div>
      <div class="clearBoth"></div>
    </div>
  <div class="wodeziyuan">
      <div class="wodeziyuan_1">
          <div class="wodeziyuan_1_up">
              我的上级
            </div>
            <?php 
            if($user->shangji_id){
              $shangshangji = $db->get_row("select id,username,nickname,image,level,money,shangji_id, shangshangji from users where id=".$user->shangji_id);
              
            ?>
          <div class="wodeziyuan_1_down">
              <img src="<?=ispic($shangshangji->image);?>" />  <?=$shangshangji->nickname;?> <span>(<?=$level[$shangji_id->level];?>)</span>
            </div>
            <?php }else{?>
            无
            <?php }?>
        </div>
      <div class="wodeziyuan_2">
          <div class="wodeziyuan_2_left">
          <?php 
          $gk = 0;
          $tz = 0;
          $xiajis = $db->get_results("select count(*) as num,level from users where shangji_id=$userId group by level");
          if($xiajis){
            foreach ($xiajis as $xj) {
              if($xj->level==1){
                $gk = $xj->num;
              }else{
                $tz+=$xj->num;
              }
            }
          }
          ?>
              我的顾客 <?=$gk?>人
            </div>
          <div class="wodeziyuan_2_right">
              <img src="/skins/default/images/wodeziyuan_1.png" /> 返利总金额：<span id="money_1">--</span>元
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="wodeziyuan_3">
          <div class="wodeziyuan_3_up">
              <div class="wodeziyuan_3_up_left">
                  我的下级 <?=$tz?>人
                </div>
              <div class="wodeziyuan_3_up_right">
                  <img src="/skins/default/images/wodeziyuan_1.png" /> 返利总金额：<span id="money_2">--</span>元
                </div>
              <div class="clearBoth"></div>
            </div>
            <div class="wodeziyuan_3_down">
              <ul id="flow_ul"></ul>
            </div>
        </div>
    </div>
</div>
<!--联系团长-->
<div class="huiyuan_tuanzhang_tc" style="display:none;">
  <div class="bj" style="background-color:rgba(0,0,0,0.6);">
    </div>
  <div class="huiyuan_tuanzhang">
      <div class="huiyuan_tuanzhang_close">
          <img src="/skins/default/images/yin_12.png" />
        </div>
      <div class="huiyuan_tuanzhang1">
          <div class="huiyuan_tuanzhang1_01">
              联系方式
            </div>
          <div class="huiyuan_tuanzhang1_02">
              <div class="huiyuan_tuanzhang1_02_left">
                  <br><span>()</span>
                </div>
              <div class="huiyuan_tuanzhang1_02_right">
                  手机 <span id="tel"></span><br>
                    微信 <span id="wxh">要复制的文字</span>
                </div>
              <div class="clearBoth"></div>
            </div>
          <div class="huiyuan_tuanzhang1_03">
              <a href="#" class="huiyuan_tuanzhang1_03_01" id="copywxh"><img src="/skins/default/images/shengji_11.png" /> 复制微信号</a>
                <a href="#" class="huiyuan_tuanzhang1_03_02" id="bdtel"><img src="/skins/default/images/shengji_12.png" /> 拨打电话</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/skins/demo/scripts/user/ziyuan.js"></script>
