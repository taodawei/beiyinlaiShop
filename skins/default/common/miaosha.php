<?php 
global $db;
$comId = (int)$_SESSION['demo_comId'];
$today = date("Y-m-d 00:00:00");
$today_time = strtotime($today);
$tomrrow = date("Y-m-d 00:00:00",strtotime('+1 day'));
$now = date("Y-m-d H:i:s");
$now_time = time();
$miaoshas = $db->get_results("select * from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$tomrrow' limit 5");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta content="telephone=no" name="format-detection" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable = no"/>
  <title>限时秒杀_<?=$_SESSION['demo_com_title']?></title>
  <link rel="stylesheet" type="text/css" href="/skins/resource/layui/css/layui.mobile.css">
  <link href="/skins/erp_zong/styles/common.css" rel="stylesheet" type="text/css">
  <link href="/skins/erp_zong/styles/shangpin.css" rel="stylesheet" type="text/css">
  <script src="/skins/resource/scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
  <script src="/skins/resource/scripts/jquery.lazyload.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="/skins/resource/scripts/layer.js"></script>
  <script type="text/javascript" src="/skins/resource/layui/layui.js"></script>
  <script src="/skins/resource/scripts/common.js"></script>
  <script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body style="background-color:#eeeeee;">
  <div class="xianshiqianggou">
    <div class="xianshiqianggou_1" <? if(count($miaoshas)<2){?>style="height:2.5rem;"<? }?>>
      <div class="xianshiqianggou_1_up">
        限时秒杀
        <div class="xianshiqianggou_1_up_left" onclick="go_prev_page();">
          <img src="/skins/erp_zong/images/fanhui_1.png"/>
        </div>
      </div>
      <?
      if(empty($miaoshas)){
        ?>
        </div>
          <a href="/index.php" style="padding:2rem 0rem;text-align:center;display: block;">当前没有秒杀活动~~~</a>
        <?
      }else{
        $miaoshaId = $miaoshas[0]->id;
        if(count($miaoshas)>1){
        ?>
        <div class="xianshiqianggou_1_down">
          <ul>
            <?
            foreach ($miaoshas as $i=>$miaosha) {
              $time = strtotime($miaosha->startTime)>$today_time?date("H:i",strtotime($miaosha->startTime)):'00:00';
              $status = strtotime($miaosha->startTime)>$now_time?'即将开始':'抢购中';
              ?>
              <li>
                <a href="javascript:" onclick="qiehuan_miaosha(<?=$i?>,<?=$miaosha->id?>,'<?=strtotime($miaosha->endTime)?>');" <? if($i==0){?>class="xianshiqianggou_1_down_on"<? }?>><h2><?=$time?></h2><?=$status?></a>
              </li>
              <?
            }
            ?>
            <div class="clearBoth"></div>
          </ul>
        </div>
        <? }?>
      </div>
      <div class="xianshiqianggou_2">
        <div class="xianshiqianggou_2_up">
          <div class="xianshiqianggou_2_up_left">
            特别推荐
          </div>
          <div class="xianshiqianggou_2_up_right">
            <a href="javascript:" onclick="qiehuan_tuijian();"><img src="/skins/erp_zong/images/sousuo_11.png"/> 换一换</a>
          </div>
          <div class="clearBoth"></div>
        </div>
        <div class="xianshiqianggou_2_down">
          <ul id="tuijian_ul">
            <div class="clearBoth"></div>
          </ul>
        </div>
      </div>
      <div class="xianshiqianggou_3">
        <div class="xianshiqianggou_3_01">
          <div class="xianshiqianggou_3_01_left">
            先下单先得哦
          </div>
          <div class="xianshiqianggou_3_01_right" id="jishiqi1">
            距结束 <span>00</span> : <span>00</span> : <span>00</span>
          </div>
          <div class="clearBoth"></div>
        </div>
        <div class="xianshiqianggou_3_02">
          <ul id="flow_ul"></ul>
        </div>
        <?
      }
      ?>
  </div>
  <script type="text/javascript">
    var miaoshaId = <?=(int)$miaoshaId?>;
    var endTime = '<?=empty($miaoshas)?'':strtotime($miaoshas[0]->endTime)?>';
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?p=7&tuijianren=<?=$_SESSION['demo_user_ID']?>';
    var share_title = '限时秒杀_<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
  </script>
  <script type="text/javascript" src="/skins/erp_zong/scripts/product/miaosha.js?v=1"></script>
</body>
</html>