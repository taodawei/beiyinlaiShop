<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$_SESSION['demo_comId'];
$tuan = $db->get_row("select * from demo_tuan where id=$id");
if(empty($tuan)){
    echo "<script>alert('团购不存在');history.go(-1);</script>";
    exit;
}
$shouhuo_json = json_decode($tuan->shouhuo_json,true);
$product_json = $db->get_row("select title,image,price_sale,price_market,productId from demo_product_inventory where id=$tuan->inventoryId");
$userIds = array();
if(!empty($tuan->userIds))$userIds = explode(',',$tuan->userIds);
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user_level = (int)$_SESSION[TB_PREFIX.'user_level'];
if(!in_array($userId,$userIds)){
    $noin = 1;
    $shouhuo_json['手机号'] = substr($shouhuo_json['手机号'],0,3).'****'.substr($shouhuo_json['手机号'],7);
}
$num_yi = 0;
if(!empty($tuan->orderIds)){
    $num_yi = count(explode(',',$tuan->orderIds));
}
$num_cha = $tuan->user_num-$num_yi;
switch ($tuan->status) {
    case 0:
    $pay_end = strtotime($tuan->endTime);
    if($pay_end>$now){
        $statusInfo = '待成团';
        $dai_chengtuan = 1;
    }else{
        $statusInfo = '拼团失败';
    }
    break;
    case 1:
        $statusInfo = '拼团成功';
    break;
    case -1:
        $statusInfo = '拼团失败';
    break;
}
$xiaoqu = $db->get_var("select title from user_address where id=$tuan->addressId");
$tuanzhang_level = 2;
if(!empty($tuan->orderIds)){
    $ids = explode(',', $tuan->orderIds);
    $firstId = $ids[0];
    $order_type = $db->get_var("select type from order0 where id=$firstId");
    if($order_type==3){
        $tuanzhang_level = 1;
    }
}
$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=19&a=view_tuan&id='.$id.'&tuijianren='.$userId;
?>
<link href="/skins/default/styles/tuan.css" rel="stylesheet" type="text/css">
<div class="querendingdan">
    <div class="querendingdan_1">
        拼团
        <div class="querendingdan_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/sousuo_1.png" style="width:.5rem;" />
        </div>
        <div class="querendingdan_1_right">
            <a href="/index.php"><img src="/skins/default/images/pintuan_1.png"/></a><a href="javascript:" onclick="share();"><img src="/skins/default/images/pintuan_11.png"/></a>
        </div>
    </div>
    <div class="pintuan_1">
        <div class="pintuan_1_01">  
            <?=$shouhuo_json['收件人']?> <b><?=$shouhuo_json['手机号']?></b> <span>团长</span>
        </div>
        <? if($tuan->type==2){?>
            <div class="pintuan_1_02">  
                <img src="/skins/default/images/querendingdan_1.png"/>  取货地址
            </div>
            <div class="pintuan_1_03">
                【<?=$xiaoqu?>】
                <?=$shouhuo_json['所在地区'].$shouhuo_json['详细地址']?>
            </div>
            <div style="color:red;font-weight:bold;padding-top:.5rem;">该团为社区团，参团之前请与团长联系好取货方式！</div>
        <? }?>
    </div>
    <div class="pintuan_2">
        <div class="querendingdan_3_img">
            <img src="<?=ispic($product_json->image)?>"/>
        </div>
        <div class="querendingdan_3_tt">
            <div class="querendingdan_3_tt_01">
                <?=$product_json->title?>
            </div>
            <div class="querendingdan_3_tt_02">
                <div class="querendingdan_3_tt_02_left">
                    ¥ <b><?=$product_json->price_sale?></b> <span>¥<?=$product_json->price_market?></span>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="clearBoth"></div>
    </div>
    <? if($dai_chengtuan==1){?>
    <div class="pintuan_3">
        <div class="pintuan_3_01">
            <span>
                老铁还差<b><?=$num_cha?></b>组，赶快邀请好友来拼单吧！
                <div class="pintuanshangpinye_3_right" onclick="$('#fenxiang_tc').show();" style="position:relative;top:-8px;margin-left:.5rem;">
                    <a href="javascript:"><img src="/skins/default/images/pintuanshangpinye_14.png"><br>分享</a>
                </div>
            </span>
        </div>
        <div class="pintuan_3_02">
            剩余 <div id="jishiqi1" style="display:inline-block;"><span>00</span> :  <span>00</span> :  <span>00</span></div> 结束
        </div>
        <div class="pintuan_3_03">
            <a <? if($tuanzhang_level>1){?>
                href="/index.php?p=4&a=cantuan&id=<?=$tuan->inventoryId?>&tuanId=<?=$id?>"
            <? }else{
             if($user_level==1 && $noin==1){?>
                href="/index.php?p=4&a=cantuan&id=<?=$tuan->inventoryId?>&tuanId=<?=$id?>"
            <? }else if($noin!=1){?>
                href="javascript:" onclick="layer.open({content:'您已经参加过这个团了~~',skin: 'msg',time: 2});"
            <? }else if($user_level>1){?>
                href="javascript:" onclick="layer.open({content:'只有普通会员能参与零元购！',skin: 'msg',time: 2});"
            <?}

        }?>>立即参团</a>
        </div>
    </div>
    <? }else{?>
        <div class="pintuan_3">
            <div class="pintuan_3_01">
                <span><?=$statusInfo?></span>
            </div>
        </div>
    <? }
    if($comId==10){
        $db_service = getCrmDb();
        $tuanzhang = $db_service->get_row("select image,name as nickname from demo_user where id=$tuan->tuanzhang");
    }else{
        $tuanzhang = $db->get_row("select image,nickname from users where id=$tuan->tuanzhang");
    }
    
    ?>
    <div class="pintuan_4">
        <ul>
            <li>
                <a>
                    <img src="<?=ispic($tuanzhang->image,'/skins/default/images/wode_1.png')?>"/><br><?=sys_substr($tuanzhang->nickname,4,true)?>
                    <span>团长</span>
                </a>
            </li>
            <?
            if(!empty($userIds)){
                foreach ($userIds as $uid){
                    if($uid>0){
                        if($comId==10){
                            $u = $db_service->get_row("select image,name as nickname,phone from demo_user where id=$uid");
                        }else{
                            $u = $db->get_row("select image,nickname,phone from users where id=$uid");
                        }
                        ?>
                        <li>
                            <a>
                                <img src="<?=ispic($u->image,'/skins/default/images/wode_1.png')?>"/><br><?=sys_substr($u->nickname,4,true)?>
                                <? if($userId==$tuan->tuanzhang){?><Br><? echo $u->phone; }?>
                            </a>
                        </li>
                        <?
                    }
                }
            }
            ?>
            <div class="clearBoth"></div>
        </ul>
    </div>
    <div class="pintuan_7">
        <div class="pintuan_7_up">
            - <span>猜你喜欢</span> -
        </div>
        <div class="chanpinlist_3 chanpin_7_02">
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
<script type="text/javascript">
    var dai_chengtuan = <?=(int)$dai_chengtuan?>;
    var endTime = '<?=strtotime($tuan->endTime)*1000?>';
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$product_json->title?>';
    var share_img = '<?=$product_json->image?>';
    var share_desc = '<?=$_SESSION['demo_com_title']?>';
    var inventoryId = <?=$id?>;
    $(function(){
        var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,inventoryId);
    });
</script>
<script type="text/javascript" src="/skins/default/scripts/user/view_tuan.js?v=1"></script>