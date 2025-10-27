<?
global $db;
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
    var channelId = '<?=$channelId?>';
    var keyword = '<?=$keyword?>';
    var cuxiao_id = '<?=$cuxiao_id?>';
    var tags = '<?=$tags?>';
    var order1 = '';
    var order2 = '';
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '商品展示-<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$_SESSION['demo_com_remark']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>
<script type="text/javascript" src="/skins/default/scripts/product/index.js"></script>
