<?
global $db;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$product_inventory = $db->get_row("select * from demo_pdt_inventory where id=$id and status=1");
if(empty($product_inventory)){
	die("<script>alert('产品已下架');go_prev_page();</script>");
}
$nowSelect = array();
if(!empty($product_inventory->key_ids)){
	$nowSelect = explode('-', $product_inventory->key_ids);
}
$productId = $product_inventory->productId;
$product = $db->get_row("select * from demo_pdt where id=$productId");
$keys = $db->get_results("select id,title,parentId,originalPic from demo_pdt_key where productId=$productId and isnew=0 order by parentId asc,id asc");
$keysArry = array();
$rows = 0;
if(count($keys)>1){
	foreach ($keys as $k){
		$keysArry[$k->parentId][$k->id]['title'] = $k->title;
		$keysArry[$k->parentId][$k->id]['image'] = $k->originalPic;
	}
	$rows = count($keysArry[0]);
}
$originalPics = array();
if(!empty($product->originalPic)){
    $originalPics = explode('|',$product->originalPic);
}
$url = urlencode("/index.php?p=22&a=view&id=$id");
//$zhekou = get_user_zhekou();
$now = date("Y-m-d H:i:s");
$price_sale = $product_inventory->price_sale;
$shezhi = $db->get_row("select com_phone,com_title,com_address,zuobiao,user_bili,shangji_bili from demo_shezhi where comId=$product_inventory->comId");
if(!empty($shezhi)){
    $zuobiaos = explode('|',$shezhi->zuobiao);
    if(!empty($zuobiaos)){
        $zuobiao = $zuobiaos[1].','.$zuobiaos[0];
    }
}
$fanli_pdt = $db->get_row("select min(price_sale) as price_sale,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1 from demo_pdt_inventory where productId=$productId");
if($comId==10){
    $pdt_shezhi = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=10");
    $shezhi->user_bili=$pdt_shezhi->user_bili;
    $shezhi->shangji_bili=$pdt_shezhi->shangji_bili;
    $fanli1 = getXiaoshu(($fanli_pdt->price_sale-$fanli_pdt->price_cost)*$shezhi->user_bili/100,2);
    $fanli2 = getXiaoshu(($fanli_pdt->price_sale1-$fanli_pdt->price_cost1)*$shezhi->user_bili/100,2);
}else{
    $fanli1 = $fanli_pdt->fanli_tuanzhang;
    $fanli2 = $fanli_pdt->fanli_tuanzhang1;
}
$fanli1 = getXiaoshu($fanli1*(100-$shezhi->shangji_bili)/100,2);
$fanli2 = getXiaoshu($fanli2*(100-$shezhi->shangji_bili)/100,2);
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/shangcheng.css">
<link href="/skins/default/styles/bendi.css" rel="stylesheet" type="text/css">
<div class="bendiliebiao">  
    <div class="bendiliebiao_up">
        <div class="bendiliebiao_up_left" onclick="go_prev_page(1);">
            <img src="/skins/default/images/bendi_1.png"/> 直商易购本地
        </div>
        <div class="clearBoth"></div>
    </div>
    <div class="bendixiangqing">
        <div class="bendixiangqing_1">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php
                        foreach($originalPics as $v){
                        ?>
                        <div class="swiper-slide"><a href="<?=$v?>"><img src="<?=$v?>" width="100%" /></a></div>
                        <?
                        }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="bendixiangqing_2">
            <div class="bendixiangqing_2_01">
                【<?=$db->get_var("select title from demo_area where id=$product_inventory->sale_area");?>】<?=$product_inventory->title?>
            </div>
            <div class="bendixiangqing_2_02">
                ￥<?=$product_inventory->price_sale?> <span>门市价￥<?=$product_inventory->price_market?></span>
                <b>返￥<?=$fanli1==$fanli2?$fanli1:$fanli1.'-'.$fanli2?></b><br>
                <span style="color:red;text-decoration:none;">有效期：<?=$product->youxiaoqi_start?> - <?=$product->youxiaoqi_end?></span>
            </div>
            <div class="bendixiangqing_2_03">
                <div class="bendixiangqing_2_03_left">
                    已售：<?=$product->orders?>
                </div>
                <div class="bendixiangqing_2_03_right">
                    库存：<span id="pdt_kucun"><?=$product_inventory->kucun?></span>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <? if(!empty($keysArry)){?>
            <div class="bendixiangqing_3">
                <div class="bendixiangqing_3_01">
                    规格选择
                </div>
                <div class="bendixiangqing_3_02">
                        <?
                        $i=0;
                        foreach ($keysArry[0] as $key => $val) {
                            $i++;
                            if(!empty($keysArry[$key])){
                                echo '<ul id="key-'.$key.'" row="'.$i.'"><div style="display:inline-block;float:left;line-height:1.8rem;">'.$val['title'].'：</div>';
                                foreach($keysArry[$key] as $key1 => $val1){
                                ?>
                                <li class="cp_lijigoumai_3" style="padding:0px;">
                                    <a href="javascript:" <? if(in_array($key1,$nowSelect)){?>class="bendixiangqing_3_02_on"<? }?> data-id="<?=$key1?>" data-row="<?=$i?>" data-key="<?=$key?>" data-img="<?=$val1['image']?>"><?=$val1['title']?></a>
                                    <img src="/skins/default/images/bendi_13.png" <? if(!in_array($key1,$nowSelect)){?>style="display:none"<? }?>>
                                </li>
                                <?
                                }
                                ?>
                                <div class="clearBoth"></div>
                                </ul>
                                <?
                            }
                        }
                    ?>
                </div>
            </div>
        <? }?>
        <div class="bendixiangqing_4">
            <div class="bendixiangqing_3_01">
                商家信息
            </div>
            <div class="bendixiangqing_4_down">
                <div class="bendixiangqing_4_down_1" <? if(!empty($zuobiao)){?>onclick="location.href='http://api.map.baidu.com/geocoder?location=<?=$zuobiao?>&coord_type=bd09ll&output=html&src=<?=$_SERVER['HTTP_HOST']?>'"<? }?>>   
                    <img src="/skins/default/images/bendi_14.png"/>
                </div>
                <div class="bendixiangqing_4_down_2" <? if(!empty($zuobiao)){?>onclick="location.href='http://api.map.baidu.com/geocoder?location=<?=$zuobiao?>&coord_type=bd09ll&output=html&src=<?=$_SERVER['HTTP_HOST']?>'"<? }?>>
                    <h2><?=$shezhi->com_title?></h2>
                    <?=$shezhi->com_address?>
                </div>
                <div class="bendixiangqing_4_down_3">
                    <a href="tel:<?=$shezhi->com_phone?>"><img src="/skins/default/images/bendi_15.png"/></a>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="bendixiangqing_5">
            <div class="bendixiangqing_3_01">
                详细信息
            </div>
            <div class="bendixiangqing_5_02">
                <?
                $cont1 = str_replace('src="', 'class="lazy" data-original="', $product->cont1);
                echo $cont1;
                ?>
            </div>
        </div>
        <div class="bendixiangqing_4">
            <div class="bendixiangqing_3_01">
                商家信息
            </div>
            <div class="bendixiangqing_4_down">
                <div class="bendixiangqing_4_down_1" <? if(!empty($zuobiao)){?>onclick="location.href='http://api.map.baidu.com/geocoder?location=<?=$zuobiao?>&coord_type=bd09ll&output=html&src=<?=$_SERVER['HTTP_HOST']?>'"<? }?>>   
                    <img src="/skins/default/images/bendi_14.png"/>
                </div>
                <div class="bendixiangqing_4_down_2" <? if(!empty($zuobiao)){?>onclick="location.href='http://api.map.baidu.com/geocoder?location=<?=$zuobiao?>&coord_type=bd09ll&output=html&src=<?=$_SERVER['HTTP_HOST']?>'"<? }?>>
                    <h2><?=$shezhi->com_title?></h2>
                    <?=$shezhi->com_address?>
                </div>
                <div class="bendixiangqing_4_down_3">
                    <a href="tel:<?=$shezhi->com_phone?>"><img src="/skins/default/images/bendi_15.png"/></a>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="bendixiangqing_6">
            <div class="bendixiangqing_6_left">
                <ul>
                    <li>
                        <a href="/index.php?p=22"><img src="/skins/default/images/bendi_16.png"/><br>商城首页</a>
                    </li>
                    <li>
                        <a href="javascript:" onclick="$('#cp_kefu_tc').show();"><img src="/skins/default/images/bendi_17.png"/><br>咨询客服</a>
                    </li>
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="bendixiangqing_6_right">
                <a href="javascript:" onclick="$('#cp_lijigoumai_tc').show();">立即购买</a>
            </div>
            <div class="clearBoth"></div>
        </div>
    </div>
</div>
<div class="bendixiangqing_2_fenxiang"> 
    <div class="bendixiangqing_2_fenxiang_up">
        <img src="images/a2020115_1.png" alt="">
    </div>
    <div class="bendixiangqing_2_fenxiang_down">
        <div class="bendixiangqing_2_fenxiang_down_1">
            会员分享
        </div>
        <div class="bendixiangqing_2_fenxiang_down_2">
            返¥<?=$fanli1==$fanli2?$fanli1:$fanli1.'-'.$fanli2?>
        </div>
        <div class="bendixiangqing_2_fenxiang_down_3">
            下单即得佣金
        </div>
        <div class="bendixiangqing_2_fenxiang_down_4">
            <a href="javascript:" onclick="$('#bendixiangqing_fenxiang_tc').show();">立即分享</a>
        </div>
    </div>
</div>
<div class="bendixiangqing_fenxiang_tc" id="bendixiangqing_fenxiang_tc" style="display:none;">
    <div class="bj" onclick="$('#bendixiangqing_fenxiang_tc').hide();">
    </div>
    <div class="bendixx_fenxiang_dingbu">
        <img src="/skins/default/images/a2020115_11.png">
        <br>请点击右上角“...”进行分享
    </div>
    <div class="bendixx_fenxiang">
        <div class="bendixx_fenxiang_up">
            <div class="bendixx_fenxiang_up1" onclick="location.href='/index.php?p=22&a=haibao&id=<?=$id?>&productId=<?=$productId?>';">
                <img src="/skins/default/images/a2020115_12.png" > 产品宣传海报
            </div>
        </div>
        <div class="bendixx_fenxiang_down">
            <a href="javascript:" onclick="$('#bendixiangqing_fenxiang_tc').hide();">取消</a>
        </div>
    </div>
</div>
<!--立即购买-弹出-->
<div class="cp_lijigoumai_tc" id="cp_lijigoumai_tc" style="display:none;">
    <div class="cp_bj" onclick="$('#cp_lijigoumai_tc').hide();">
    </div>
    <div class="cp_lijigoumai">
        <div class="cp_lijigoumai_1" onclick="$('#cp_lijigoumai_tc').hide();">
            <img src="/skins/default/images/shangpinxx_33.png" />
        </div>
        <div class="cp_lijigoumai_2">
            <div class="cp_lijigoumai_2_left">
                <img src="<?=ispic($product_inventory->image)?>" id="fx_lijigoumai_1_img"/>
            </div>
            <div class="cp_lijigoumai_2_right">
                <div class="cp_lijigoumai_2_right_1">
                    <b id="price_sale"><?='￥'.$price_sale?></b><span id="price_market1">门市价：￥<?=number_format($product_inventory->price_market,2)?></span>
                </div>
                <div class="cp_lijigoumai_2_right_2">
                    商品编号：<span id="pdt_sn"><?=$product_inventory->sn?></span>
                </div>
            </div>
            <div class="clearBoth"></div>
        </div>
        <!-- <div style="max-height:18rem;overflow-y:auto;">
        <? if(!empty($keysArry)){
            $i=0;
            foreach ($keysArry[0] as $key => $val) {
                $i++;
                if(!empty($keysArry[$key])){
                    ?>
                    <div class="cp_lijigoumai_3" id="key-<?=$key?>" row="<?=$i?>">
                        <div class="cp_lijigoumai_3_up">
                            <?=$val['title']?>
                        </div>
                        <div class="cp_lijigoumai_3_down">
                            <ul>
                                <? foreach($keysArry[$key] as $key1 => $val1){
                                    ?>
                                    <li><a href="javascript:" <? if(in_array($key1,$nowSelect)){?>class="cp_lijigoumai_3_down_on"<? }?> data-id="<?=$key1?>" data-row="<?=$i?>" data-key="<?=$key?>" data-img="<?=$val1['image']?>" ><?=$val1['title']?></a></li>
                                    <?
                                    }
                                ?>
                                <div class="clearBoth"></div>
                            </ul>
                        </div>
                    </div>
                    <?
                }
            }
        }?>
        </div> -->
        <div class="cp_lijigoumai_4">
            <div class="cp_lijigoumai_4_left">
                数量
            </div>
            <div class="cp_lijigoumai_4_right">
                <a href="javascript:" onclick="num_edit(-1);"><img src="/skins/default/images/shangpinxx_31.png"/></a><input type="number" id="num" value="1" step="1"/><a href="javascript:" onclick="num_edit(1);"><img src="/skins/default/images/shangpinxx_32.png"/></a>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="cp_lijigoumai_5">
            <a href="javascript:" onclick="buy(2);" style="background-color: #ff2700;border-radius:5px;width:100%;height:2.175rem;display:block;text-align:center;line-height:2.175rem;font-size:.6rem;color:#fff;" class="chanpin_9_right_01">立即购买</a>
        </div>
        <div class="cp_lijigoumai_6">
        </div>
    </div>
</div>
<!--客服-弹出-->
<?
$phone = $shezhi->com_phone;
//$zxkefu = empty($shezhi->com_kefu)?'https://kefu.zhishangez.com/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=kefu01&groupid=4':$shezhi->com_kefu;
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
          </ul>
        </div>
      <div class="cp_kefu_2">
          <a href="javascript:" onclick="$('#cp_kefu_tc').hide();">取消</a>
        </div>
    </div>
</div>
<link href="https://www.zhishangez.com/cdn/swiper.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/skins/default/styles/photo.css">
<script type="text/javascript" src="https://www.zhishangez.com/cdn/swiper.min.js"></script>
<script src="/skins/resource/scripts/photo.js"></script>
<script type="text/javascript">
	var show_detail = 0;
	//切换拼团或分享购的参数
	var show_price = 0;
	//是否已经加载过评价了，如果加载过就不进行初始化
	var ifpingjia = 0;
	var productId = <?=$productId?>;
	var inventoryId =<?=$id?>;
	var buy_limit = 0;
    var buy_type = 1;
    var max_num = 999;
    var user_level = <?=(int)$_SESSION[TB_PREFIX.'user_level'];?>;
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '<?=$product_inventory->title?>';
    var share_img = '<?=$originalPics[0]?>';
    var share_desc = '<?=$_SESSION['demo_com_title']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,<?=$id?>);
    });
    var swiper = new Swiper('.swiper-container',{
        pagination: {
            el:'.swiper-pagination',
        }
    });
    baguetteBox.run('.swiper-wrapper');
</script>
<script src="/skins/default/scripts/product/pdts_view.js"></script>