<?php 
global $db,$request;
if(!empty($request['p'])){
  if($request['p']==4){
    if($request['a']=='view'||$request['a']=='cantuan'){
      $seotitle = $db->get_var("select title from demo_product_inventory where id=".$request['id']);
    }else if($request['a']=='channels'){
      $seotitle = '商品分类';
    }else if(!empty($request['channelId'])){
      $channel = $db->get_row("select parentId,title,share_title,share_desc,originalPic from demo_product_channel where id=".$request['channelId']);
      /*if(empty($channel->share_title) && $channel->parentId>0){
        $channel = $db->get_row("select title,share_title,share_desc,originalPic from demo_product_channel where id=".$channel->parentId);
      }*/
      $seotitle = empty($channel->share_title)?$channel->title:$channel->share_title;
      $share_desc = $channel->share_desc;
      $channel_img = $channel->originalPic;
    }
  }else if($request['p']==22){
    if($request['a']=='view'){
      $seotitle = $db->get_var("select title from demo_pdt_inventory where id=".$request['id']);
    }else{
      $seotitle = '直商易购本地';
    }
  }else if($request['p']==24){
    if($request['a']=='view'){
      $seotitle = $db->get_var("select title from demo_product_dapei where id=".$request['id']);
    }else{
      $seotitle = '推荐搭配';
    }
  }else if($request['p']==5&&$request['a']=='view'){
    $seotitle = $db->get_var("select title from demo_list where id=".(int)$request['id']);
  }else if($request['a']=='search'){
    $seotitle = '搜索商品';
  }else if($request['a']=='shops'){
    $seotitle = '商家推荐';
  }else{
    $seotitle = $db->get_var("select title from demo_menu where id=".$request['p']);
  }
  
}?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta content="telephone=no" name="format-detection" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable = no"/>
  <title><?=$seotitle?>_<?=$_SESSION['demo_com_title']?></title>
  <link rel="stylesheet" type="text/css" href="/skins/resource/layui/css/layui.mobile.css">
  <link href="/skins/default/styles/common.css" rel="stylesheet" type="text/css">
  <link href="/skins/default/styles/shangcheng.css?v=1" rel="stylesheet" type="text/css">
  <script src="/skins/resource/scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
  <script src="/skins/resource/scripts/jquery.lazyload.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="/skins/resource/scripts/layer.js"></script>
  <script type="text/javascript" src="/skins/resource/layui/layui.js"></script>
  <script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="/skins/resource/scripts/common.js"></script>
</head>
<?
  /*$arrs = array('channels','view','cantuan','gouwuche','queren','weixin_pay','shop','yushou','shenqing_tuan','shenqing_tuanzhang','shenqing_shequ','shenqing1113');
  if($request['p']==24 || $request['p']==5 || in_array($request['a'],$arrs)){*/
?>
<body>
  <?php
    if($request['p']==4 && ($request['a']=='index' || $request['a']=='huodong' || $request['a']=='shangxin' || empty($request['a']))){
      require(ABSPATH.'/skins/default/head.php');
    }

    sys_layout_part();
    if($_SESSION['if_tongbu']==1){?>
    <div class="index_piao">
      <div class="index_piao_1">
          <ul>
            <li>
                  <a href="/toshop.php?comId=10"><img src="/skins/default/images/piao_1.png"/></a>
                    <i class="index_piao_1_line"></i>
                </li>
                <li>
                  <a href="javascript:" onclick="$('#cp_kefu_tc').show();"><img src="/skins/default/images/piao_11.png"/></a>
                    <i class="index_piao_1_line"></i>
                </li>
                <li>
                  <a href="javascript:" onclick="$('#cp_kefu_tc').show();"><img src="/skins/default/images/piao_12.png"/></a>
                    <i class="index_piao_1_line"></i>
                </li>
                <li>
                  <a href="#"><img src="/skins/default/images/piao_13.png"/></a>
                    <i class="index_piao_1_line"></i>
                </li>
                <li>
                  <a href="javascript:"><img src="/skins/default/images/piao_14.png" class="index_piao_1_moren"/></a>
                </li>
          </ul> 
        </div>
      <div class="index_piao_2">
        <img src="/skins/default/images/piao_15.png"/>
      </div>
    </div>
    <script>
      $(function(){
        $(".index_piao_1_moren").click(function(){
          $(".index_piao_1").animate({right:"-1.6rem"},100);
          $(".index_piao_2").animate({right:"0"},100);
        });
        $(".index_piao_2").click(function(){
          $(".index_piao_1").animate({right:"0"},100);
          $(".index_piao_2").animate({right:"-1.6rem"},100);
        });
      });
    </script>
  <?
  $comId = (int)$_SESSION['demo_comId'];
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
  <?
  }
  ?>
</body>
</html>