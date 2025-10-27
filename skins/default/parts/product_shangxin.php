<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$lasttime = date("Y-m-d",strtotime("-7 days"));
$products = $db->get_results("select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market from demo_product_inventory where comId=$comId and if_lingshou=1 and status=1 and dtTime>'$lasttime' group by productId order by dtTime desc limit 50");
if(empty($products)){
  echo '<div class="shangxin_1" style="padding:1rem 0;"><span>暂无新品！</span></div>';
}else{
  $nowData = '';
  foreach ($products as $key => $product) {
    $product->image = empty($product->image)?'/inc/img/nopic.svg':$product->image;
    $product->price_sale = number_format($product->price_sale,2);
    $product->price_market = number_format($product->price_market,2);
    if($nowData==date("m月d日",strtotime($product->dtTime))){
      ?>
      <li>
        <a href="/index.php?p=4&a=view&id=<?=$product->inventoryId?>">
          <div class="shouye_8_down_img">
            <img src="<?=ispic($product->image)?>"/>
          </div>
          <div class="shouye_8_down_tt1">
            <?=$product->title?>
          </div>
          <div class="shouye_8_down_tt2">
            ￥<?=$product->price_sale?> <span>原价：￥<?=$product->price_market?></span>
          </div>
        </a>
      </li>
      <?
    }else{
      $nowData=date("m月d日",strtotime($product->dtTime));
      ?>
      <div class="shangxin_1">
        <span><?=$nowData?>本店上新</span>
      </div>
      <div class="shangxin_2">
        <div class="shouye_8_down">
          <ul>
            <li>
              <a href="/index.php?p=4&a=view&id=<?=$product->inventoryId?>">
                <div class="shouye_8_down_img">
                  <img src="<?=ispic($product->image)?>"/>
                </div>
                <div class="shouye_8_down_tt1">
                  <?=$product->title?>
                </div>
                <div class="shouye_8_down_tt2">
                  ￥<?=$product->price_sale?> <span>原价：￥<?=$product->price_market?></span>
                </div>
              </a>
            </li>
        <?
        }
        if(($key==count($products)-1) || $nowData!=date("m月d日",strtotime($products[$key+1]->dtTime))){
          ?>
            <div class="clearBoth"></div>
            </ul>
          </div>
        </div>
        <?
        }
    }
  }
  require(ABSPATH.'/skins/default/bottom.php');
?>
<script type="text/javascript">
  var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '上新-<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$_SESSION['demo_com_remark']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>