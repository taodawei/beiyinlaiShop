<?
global $db,$request;
$id = (int)$request['id'];
$inventoryId = (int)$request['inventoryId'];
$comId = (int)$request['comId'];
if(empty($comId))$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$order = $db->get_row("select * from order$fenbiao where id=$id");
if(empty($order)){
    die("<script>alert('订单不存在');history.go(-1);</script>");
}
$inventory = $db->get_row("select id,title,image from demo_product_inventory where id=$inventoryId");
if(empty($inventory)){
    die("<script>alert('该产品已下架');history.go(-1);</script>");
}
$now = time();
?>
<link rel="stylesheet" href="/skins/resource/layui/css/layui.css" />
<div class="wode">
    <div class="wode_1">
        发表评价
        <div class="wode_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
    <div class="fabiaopingjia">
        <div class="fabiaopingjia_1">
            <div class="fabiaopingjia_1_left">
                <img src="<?=ispic($inventory->image)?>"/>
            </div>
            <div class="fabiaopingjia_1_right">
                描述相符 <span id="pingfen"></span>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="fabiaopingjia_2">
            <div class="fabiaopingjia_2_up">
                <textarea id="content" cols="30" rows="10" placeholder="输入评价内容~"></textarea>
            </div>
            <div class="fabiaopingjia_2_down">
                <div id="upload_img_div" style="position:relative;display:inline-block;">
                    <input name="uploadfile" id="uploadfile" multiple="multiple" type="file" style="position:absolute;opacity:0;width:100%;height:100%;z-index:9;" onchange="showPic(this);">
                    <img src="/skins/default/images/fabiaopingjia_1.gif"/>
                </div>
            </div>
        </div>
        <div class="fabiaopingjia_3">
            <a href="javascript:" onclick="pingjia();">确认发布</a>
        </div>
        <div class="fabiaopingjia_4"></div>
    </div>
</div>
<script type="text/javascript">
    var orderId = <?=$id?>;
    var inventoryId = <?=$inventoryId?>;
    var star = 5;
    var imgs = '';
    var comId = <?=$comId?>;
</script>
<script type="text/javascript" src="/skins/resource/scripts/MegaPixImage.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/exif.js"></script>
<script type="text/javascript" src="/skins/default/scripts/user/order_pingjia.js"></script>