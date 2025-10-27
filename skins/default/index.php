<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta content="telephone=no" name="format-detection" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable = no"/>
  <title><?=$_SESSION['demo_com_title']?></title>
  <link rel="stylesheet" type="text/css" href="/skins/resource/layui/css/layui.mobile.css">
  <link href="/skins/default/styles/common.css" rel="stylesheet" type="text/css">
  <link href="/skins/default/styles/shangcheng.css?v=1" rel="stylesheet" type="text/css">
  <script src="/skins/resource/scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
  <script src="/skins/resource/scripts/jquery.lazyload.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="/skins/resource/scripts/layer.js"></script>
  <script type="text/javascript" src="/skins/resource/layui/layui.js"></script>
  <script src="/skins/resource/scripts/common.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<?
global $db;
$comId = (int)$_SESSION['demo_comId'];
//banner图跟自定义模块
$banners = $db->get_results("select * from banner where comId=$comId and channelId=0 order by ordering desc,id asc");
$channels= $db->get_results("select * from banner_channel where comId=$comId order by ordering desc,id asc");
$share_url = 'http://'.$_SERVER['HTTP_HOST'];
require(ABSPATH.'/skins/default/head.php');
  ?>
    <div class="shouye_4">
      <div class="flexslider" id="flash">
        <ul class="slides">
        <?php 
            if(!empty($banners)){
                foreach($banners as $k =>$v){
                  if(empty($v->url) && !empty($v->inventoryId)){
                      $v->url="/index.php?p=4&a=view&id=$v->inventoryId";
                  }
              ?>
                <li>
                    <div class="img" ><a href="<? echo $v->url?>"><img src="<? echo $v->originalPic?>" border="0" /></a></div>
                </li>
              <?
                }
            }
            ?>
            </ul>
        </div>
    </div>
    <? if(!empty($channels)){
      ?><div class="shouye_7"><?
      foreach ($channels as $channel) {
        $imgs = $db->get_results("select originalPic,inventoryId,url,title from banner where channelId=$channel->id and comId=$comId order by ordering desc");
        if(!empty($imgs)){
          $class = 'shouye_7_02';
          switch ($channel->shuliang) {
            case '2':
              $class = 'shouye_7_03';
            break;
            case '3':
              $class = 'shouye_7_04';
            break;
            case '4':
              $class = 'shouye_7_05';
            break;
            case '5':
              $class = 'shouye_7_06';
            break;
            case '6':
              $class = 'shouye_7_07';
            break;
          }
        ?>
        
          <? if($channel->show_title==1){?>
          <div class="shouye_7_01"><?=$channel->title?> <img src="/skins/default/images/index_06.gif"/> <span><?=$channel->remark?></span></div>
          <? }?>
          <div class="<?=$class?>">
            <?
            if($class=='shouye_7_07'){
              if(empty($imgs[0]->url) && !empty($imgs[0]->inventoryId)){
                $imgs[0]->url="/index.php?p=4&a=view&id=$img->inventoryId";
              }
              if(empty($imgs[1]->url) && !empty($imgs[1]->inventoryId)){
                $imgs[1]->url="/index.php?p=4&a=view&id=$img->inventoryId";
              }
              if(empty($imgs[2]->url) && !empty($imgs[2]->inventoryId)){
                $imgs[2]->url="/index.php?p=4&a=view&id=$img->inventoryId";
              }
              ?>
              <div class="shouye_7_07">
                <div class="shouye_7_07_1">
                  <a href="<?=$imgs[0]->url?>"><img src="<?=$imgs[0]->originalPic?>"/></a>
                </div>
                <div class="shouye_7_07_2">
                  <a href="<?=$imgs[1]->url?>"><img src="<?=$imgs[1]->originalPic?>" style="margin-bottom:0.25rem;"/></a>
                  <a href="<?=$imgs[2]->url?>"><img src="<?=$imgs[2]->originalPic?>"/></a>
                </div>
                <div class="clearBoth"></div>
              </div>
              <?
            }else{
            ?>
            <ul>
              <? foreach($imgs as $i=>$img){
                  if(empty($img->url) && !empty($img->inventoryId)){
                      $img->url="/index.php?p=4&a=view&id=$img->inventoryId";
                  }
              ?>
                <li>
                  <a href="<?=$img->url?>"><img src="<?=$img->originalPic?>"/></a>
                  <? if($channel->show_img_title==1){?>
                    <div class="banner_title"><?=$img->title?></div>
                  <? }?>
                </li>
              <? 
                if($channel->shuliang>1 && ($i+1)%$channel->shuliang==0){
                  ?><div class="clearBoth"></div><?
                }
              }?>
              <div class="clearBoth"></div>
            </ul>
          <? }?>
          </div>
        <?
        }
      }
      ?></div><?
    }?>
    <?
    $yushous = $db->get_results("select pdtId,pdtInfo,price_json from yushou where comId=$comId and startTime<'".date('Y-m-d H:i:s')."' and endTime>'".date('Y-m-d H:i:s')."' and status=1 order by startTime asc limit 3");
    if(!empty($yushous)){
    ?>
    <div class="shouye_4" style="background-color: #ffffff;border-radius: 0.15rem;margin: 0 0.5rem 0.5rem;padding: 0.5rem 0;">
      <div class="shouye_4_up">
          <div class="shouye_4_up_left">
            <i style="background-color:#519600;"></i> 新品预售
          </div>
          <div class="shouye_4_up_right">
            <a href="/index.php?p=4&a=yushou">更多 <img src="/skins/erp_zong/images/jiantou_1.png" /></a>
          </div>
          <div class="clearBoth"></div>
        </div>
      <div class="shouye_4_down">
          <ul>
            <? foreach($yushous as $yushou){
              $price_json = json_decode($yushou->price_json,true);
              $yushou_money = $price_json[0]['price'];
              $inventory = $db->get_row("select title,image from demo_product_inventory where id=$yushou->pdtId");
              ?>
              <li>
                <a href="/index.php?p=4&a=view&id=<?=$yushou->pdtId?>">
                    <div class="shouye_4_down_img">
                        <img src="<?=ispic($inventory->image)?>" >
                      </div>
                    <div class="shouye_4_down_tt">
                        <h2><?=$inventory->title?></h2>
                          ￥<?=$yushou_money?>
                      </div>
                      <div class="shouye_xinpin_biaoqian">
                        NEW
                      </div>
                  </a>
              </li>
            <? }?>
            <div class="clearBoth"></div>
          </ul>
        </div>
    </div>
    <?
    }
    ?>
    <div class="shouye_8">
      <div class="shouye_8_up">
        <ul>
          <li>
            <a href="javascript:" onclick="orderby(0,'ordering','desc')" class="shouye_8_up_on">推荐</a>
          </li>
          <li>
            <a href="javascript:" onclick="orderby(1,'orders','desc')">热门</a>
          </li>
          <li>
            <a href="javascript:" onclick="orderby(2,'inventoryId','asc')">新品</a>
          </li>
          <li>
            <a href="javascript:" onclick="orderby(3,'price_sale','asc')">价格 <img id="price_order_img" src="/skins/default/images/chanpin_11.png"/></a>
          </li>
          <div class="clearBoth"></div>
        </ul>
      </div>
      <div class="shouye_8_down">
        <ul id="flow_ul">
          <div class="clearBoth"></div>
        </ul>
      </div>
    </div>
  </div>
  <div class="fenxiang_tc" id="fenxiang_tc" onclick="$('#fenxiang_tc').hide();" style="display:none;z-index:997">
    <div class="bj"></div>
    <div class="fenxiangdiv">
      <img src="/skins/default/images/share.png" width="90%">
    </div>
  </div>
  <?
  require(ABSPATH.'/skins/default/bottom.php');
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
  <link rel="stylesheet" type="text/css" href="/skins/resource/pro_qh/product_qh.css">
  <script src="/skins/resource/pro_qh/slider.js"></script>
  <script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
    var order1 = '';
    var order2 = '';
  </script>
  <script type="text/javascript" src="/skins/default/scripts/index.js?v=1"></script>
</body>
</html>