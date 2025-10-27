<?
global $db,$request;
$id = (int)$request['id'];
$faxian = $db->get_row("select * from demo_product_dapei where id=$id");
if(empty($faxian)){
    die('文章不存在');
}
$db->query("update demo_product_dapei set views=views+1 where id=$faxian->id");
$imgs = explode('|',$faxian->originalPic);
//$userId = (int)$_SESSION['demo_zhishangId'];
//$company = $db->get_row("select com_title,com_logo,com_remark from demo_shezhi where comId=$faxian->shopId");
//$if_guanzhu = (int)$db->get_var("select count(*) from user_shop_collect where userId=$userId and shopId=$faxian->shopId");
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/faxian.css?v=1">
<div class="huodong">
    <div class="huodong_1">
        <?=$faxian->content?>
    </div>
    <div class="huodong_2">
        <div class="huodong_2_01">
            商品购买<?=count(explode(',', $faxian->pdtIds))?>
        </div>
        <div class="huodong_2_02">
            <div class="huodong_2_02_right">                
                <div class="huodong_2_02_left">
                    <img src="/skins/erp_zong/images/huodong_12.png" alt=""/>
                </div>
                <div class="huodong_2_02_up">
                    相关商品
                </div>
                <div class="huodong_2_02_down">
                    <div class="huodong_2_02_down_right">
                        <ul id="pdt_ul">
                        <?
                        if(empty($faxian->pdtIds))$faxian->pdtIds='0';
                        $products = $db->get_results("select id,image,price_sale,title,price_card from demo_product_inventory where id in($faxian->pdtIds)");
                        if(!empty($products)){
                            foreach ($products as $pdt){
                                ?>
                                <li>
                                    <a href="/index.php?p=4&a=view&id=<?=$pdt->id?>">
                                        <div class="huodong_2_02_down_img">
                                            <img src="<?=ispic($pdt->image)?>"/>
                                        </div>
                                        <div class="huodong_2_02_down_tt">
                                            <div class="huodong_2_02_down_tt_01">
                                                <?=$pdt->title?>
                                            </div>
                                            <div class="huodong_2_02_down_tt_02">
                                                <div class="huodong_2_02_down_tt_02_left">
                                                    ￥<?=$pdt->price_sale-$pdt->price_card?>
                                                </div>
                                                <div class="huodong_2_02_down_tt_02_right">
                                                    <a href="/index.php?p=4&a=view&id=<?=$pdt->id?>">去看看 ></a>
                                                </div>
                                                <div class="clearBoth"></div>
                                            </div>
                                        </div>
                                        <div class="clearBoth"></div>
                                    </a>
                                </li>
                                <?
                            }
                        }
                        ?>
                        </ul>
                    </div>
                    <div class="clearBoth"></div>               
                </div>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?p=24&a=view&id=<?=$id?>&tuijianren=<?=(int)$_SESSION[TB_PREFIX.'user_ID']?>';
    var share_title = '<?=$faxian->title?>';
    var share_img = '<?=$imgs[0]?>';
    var share_desc = '';
    $(function(){
        var url = window.location.href;
        url = encodeURIComponent(url);
        WeChat(url,share_url,share_title,share_img,share_desc,1);
    });
    $(function(){
        $(".huodong_2_01").click(function(){
            $(".huodong_2_02").animate({left:"2.875rem"},200);
            $(this).hide();
        });
        $(".huodong_2_02_left img").click(function(){
            $(".huodong_2_02").animate({left:"100%"},200);
            $(".huodong_2_01").show();
        });
    }); 
</script>