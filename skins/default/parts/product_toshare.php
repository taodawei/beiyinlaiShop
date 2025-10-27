<?
global $db;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$product_inventory = $db->get_row("select price_sale,price_market,productId,tuan_num,title,image,sale_tuan,price_json,sale_lingyuangou,sale_sharegou from demo_product_inventory where id=$id and status=1");
if(empty($product_inventory)){
	die("<script>alert('产品已下架');go_prev_page();</script>");
}
$product = $db->get_row("select share_img,share_title from demo_product where id=$product_inventory->productId");
$user = $db->get_row("select nickname,level,image from users where id=$userId");
switch ($user->level) {
    case 1:
        $level = '会员';
    break;
    case 2:
        $level = '团长';
    break;
    case 3:
        $level = '总监';
    break;
    case 4:
        $level = '联创';
    break;
}
$price_json = json_decode($product_inventory->price_json);
if($product_inventory->sale_tuan==0 && $product_inventory->sale_lingyuangou==0){
    if($product_inventory->sale_sharegou==1){
        $product_inventory->price_sale = $price_json->price_share;
    }else{
        $product_inventory->price_sale = $price_json->price_alone;
    }
}
$share_url = 'https://'.$_SERVER['HTTP_HOST'].'/index.php?p=4&a=view&id='.$id.'&tuijianren='.$userId;
?>
<style type="text/css">
    #erweima canvas{width:100%;}
</style>
<div class="fenxiang" id="zhongqiu">
    <div class="fenxiang_1">
        <img src="<?=empty($user->image)?'/skins/default/images/wode_1.png':$user->image?>" />
        <span><b><?=$user->nickname?></b><?=$level?></span>
    </div>
    <div class="fenxiang_2">
        <img src="<?=empty($product->share_img)?$product_inventory->image:$product->share_img?>" crossorigin="anonymous"/>
    </div>
    <div class="fenxiang_3">
        <div class="fenxiang_3_left">
            <div id="erweima"></div>
            <span>[长按立即购买]</span>
        </div>
        <div class="fenxiang_3_right">
            <div class="fenxiang_3_right_1" style="height:auto">
                <?=empty($product->share_title)?$product_inventory->title:$product->share_title?>
            </div>
            <div class="fenxiang_3_right_2">
                ¥<b><?=$product_inventory->price_sale?></b> <i>¥<?=$product_inventory->price_market?></i> <? if($product_inventory->sale_tuan==1){?><span>拼<?=$product_inventory->tuan_num?>件成团</span><? }?>
            </div>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="fenxiang_4">
    <a href="javascript:" onclick="layer.open({content:'长按上方图片保存，或点击右上角进行链接分享',skin: 'msg',time: 2});">分享</a>
</div>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '即品汇：<?=empty($product->share_title)?$product_inventory->title:$product->share_title?>';
    var share_img = '<?=empty($product->share_img)?$product_inventory->image:$product->share_img?>';
    var share_desc = '<?=empty($product->share_title)?$product_inventory->title:$product->share_title?>';
    var inventoryId = <?=$id?>;
</script>
<script type="text/javascript" src="/skins/demo/scripts/html2canvas.js"></script>
<script type="text/javascript" src="/skins/demo/scripts/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/skins/demo/scripts/product/product_share.js"></script>