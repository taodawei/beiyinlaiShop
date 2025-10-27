<?
global $db;
?>
<div class="shouye_8">
    <div class="wode_1">
      浏览记录
        <div class="wode_1_left" onclick="location.href='/index.php?p=8'">
          <img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
    <div class="shouye_8_down">
        <ul id="flow_ul">
          <div class="clearBoth"></div>
        </ul>
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
    var tags = '';
    var shoucang = 0;
    var ifhistory = 1;
</script>
<script type="text/javascript" src="/skins/default/scripts/product/shoucang.js"></script>
