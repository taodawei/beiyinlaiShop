<?
global $request;
if($_SESSION['demo_tongbu_menu']==1){?>
<style type="text/css">
.bottom{width: 100%; height: 2.875rem; position: fixed; z-index: 99; left: 0; bottom: 0; background-color: #ffffff; box-shadow: 0 -0.075rem 0.2rem #cdcdcd;}
.bottom ul li{float: left; width: 20%; text-align: center; padding-top: 0.25rem;}
.bottom ul li a{font-size: 0.6rem; color: #6b6b6b;}
.bottom ul li img{vertical-align: top; width: 1.25rem; height: 1.25rem; margin-bottom: 0.2rem;}
.bottom ul li .bottom_on{color: #fa3c17;}
</style>
<div class="bottom">
  <ul>
    <li>
      <a href="http://buy.zhishangez.com" onclick="clearCacheDate();">
        <img src="/skins/erp_zong/images/bottom_1.png" /><br>首页
      </a>
    </li>
    <li>
      <a href="http://buy.zhishangez.com/index.php?p=4&a=channels">
        <img src="/skins/erp_zong/images/bottom_11.png" /><br>分类
      </a>
    </li>
    <li>
      <a href="http://buy.zhishangez.com/index.php?p=9">
        <img src="/skins/erp_zong/images/bottom_12.png" /><br>广场
      </a>
    </li>
    <li>
      <a href="http://buy.zhishangez.com/index.php?p=4&a=gouwuche">
        <img src="/skins/erp_zong/images/bottom_13.png" /><br>购物车
      </a>
    </li>
    <li>
      <a href="http://buy.zhishangez.com/index.php?p=8">
        <img src="/skins/erp_zong/images/bottom_14.png" /><br>我的
      </a>
    </li>
    <div class="clearBoth"></div>
  </ul>
</div>
<?
}else{
  $gouwuche_num = 0;
  $userId = (int)$_SESSION['demo_user_ID'];
  $comId = (int)$_SESSION['demo_comId'];
  if(!empty($userId)){
    $content = $db->get_var("select content from demo_gouwuche where comId=$comId and userId=$userId");
    if(!empty($content))$gouwuche=json_decode($content,true);
    foreach ($gouwuche as $g){
      $gouwuche_num+=$g['num'];
    }
  }
  if($comId==1121){
    ?>
    <style type="text/css">
      .footer{width:100%;height:60px;margin:auto;margin-top:35px;display:flex;justify-content:space-around;position:fixed;bottom:0px;background:#fff}
      .footer_bar{width:30px;height:30px;margin-top:8px}
      .footer_bar img{width:100%;height:100%}
      .footbar_text{font-size:14px;color:#aaa;width:50px}
      .footbar_text:hover{color:#54cd88}
    </style>
    <div class="footer">
        <div class="footer_bar" onclick="location.href='/'">
            <img src="/skins/sakulun/images/first.png">
            <div class="footbar_text">首页</div>
        </div>
        <div class="footer_bar" onclick="location.href='/index.php?p=4&a=gouwuche'">
            <img src="/skins/sakulun/images/shop.png">
            <div class="footbar_text">购物车</div>
        </div>
        <div class="footer_bar" onclick="location.href='/index.php?p=8'">
            <img src="/skins/sakulun/images/my.png">
            <div class="footbar_text">我的</div>
        </div>
    </div>
    <?
  }else{
  $templete = 'default';
  if($_SESSION['demo_comId']==1142){
    $templete = 'xinlv';
  }
?>
<div id="bottom">
  <ul>
    <li>
      <a href="/" onclick="clearCacheDate();" <? if(empty($request['p'])){?>class="bottom_on"<? }?>>
        <img src="/skins/<?=$templete?>/images/bottom_1<? if(empty($request['p'])){echo 4;}else{echo '';}?>.png"><br>首页
      </a>
    </li>
    <li>
      <a href="/index.php?p=4&a=channels" <? if($request['a']=='channels'){?>class="bottom_on"<? }?>>
        <img src="/skins/<?=$templete?>/images/bottom_1<? if($request['a']=='channels'){echo 5;}else{echo 1;}?>.png"><br>分类
      </a>
    </li>
    <li>
      <a href="/index.php?p=4&a=gouwuche" <? if($request['a']=='gouwuche'){?>class="bottom_on"<? }?>>
        <img src="/skins/<?=$templete?>/images/bottom_1<? if($request['a']=='gouwuche'){echo 6;}else{echo 2;}?>.png"><br>购物车
      </a>
      <span id="gwc_num" <? if($gouwuche_num>0){?>style="display:block;"<?}?>><?=$gouwuche_num?></span>
    </li>
    <li>
      <a href="/index.php?p=8" <? if($request['p']=='8'){?>class="bottom_on"<? }?>>
        <img src="/skins/<?=$templete?>/images/bottom_1<? if($request['p']=='8'){echo 7;}else{echo 3;}?>.png"><br>我的
      </a>
    </li>
    <div class="clearBoth"></div>
  </ul>
</div>
<?
  }
}
?>