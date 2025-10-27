<?
global $db,$request;
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$tags = $request['tags'];
$cuxiao_id = (int)$request['cuxiao_id'];
$yhq_id = (int)$request['yhq_id'];
//分类
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("cache/channels_$comId.php")){
  $cache = 1;
  $content = file_get_contents("cache/channels_$comId.php");
  $channels = json_decode($content);
}
if(empty($channels))$channels = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
?>
<link rel="stylesheet" type="text/css" href="/skins/default/styles/shengxuan.css">
<script type="text/javascript" src="/skins/default/js/iscroll.js"></script>
<script type="text/javascript" src="/skins/default/js/navbarscroll.js"></script>
<script type="text/javascript">
$(function(){
  $('.wrapper').navbarscroll();
});
</script>
<div>
    <div class="gouwuche_1">
      积分兑换
        <div class="fenlei_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fenlei_1.png">
        </div>
    </div>
      <div class="shangpinlist">
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
  ?>
<script type="text/javascript">
    var is_jifen  = 1;
    var channelId = '<?=$channelId?>';
    var keyword = '<?=$keyword?>';
    var cuxiao_id = '<?=$cuxiao_id?>';
    var yhq_id = '<?=$yhq_id?>';
    var tags = '<?=$tags?>';
    var order1 = '';
    var order2 = '';
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '积分列表-<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$_SESSION['demo_com_remark']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>
<script type="text/javascript" src="/skins/default/scripts/product/jifen.js"></script>
