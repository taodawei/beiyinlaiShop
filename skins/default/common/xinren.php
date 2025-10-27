<?php 
global $db;
$comId = (int)$_SESSION['demo_comId'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta content="telephone=no" name="format-detection" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable = no"/>
  <title>新人专享_<?=$_SESSION['demo_com_title']?></title>
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
    <div class="xianshiqianggou_1" style="height:2.5rem;">
      <div class="xianshiqianggou_1_up">
        新人专享
        <div class="xianshiqianggou_1_up_left" onclick="go_prev_page();">
          <img src="/skins/erp_zong/images/fanhui_1.png"/>
        </div>
      </div>
    </div>
    <div class="xianshiqianggou_2">
      <div class="xianshiqianggou_3">
        <div class="xianshiqianggou_3_02">
          <ul id="flow_ul"></ul>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    var miaoshaId = <?=(int)$miaoshaId?>;
    var endTime = '<?=empty($miaoshas)?'':strtotime($miaoshas[0]->endTime)?>';
  </script>
  <script type="text/javascript" src="/skins/default/scripts/product/xinren.js"></script>
</body>
</html>