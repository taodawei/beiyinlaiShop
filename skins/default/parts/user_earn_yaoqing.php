<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg&tuijianren='.$userId;
if($comId==10){
    $db_service = getCrmDb();
    $user = $db_service->get_row("select image from demo_user where id=$userId");
}else{
    $user = $db->get_row("select image from users where id=$userId");
}
$share_img =$user->image;
if(empty($share_img)){
    $share_img = 'http://'.$_SERVER['HTTP_HOST'].'/skins/shequ/images/erweima.jpg';
}
$tuanzhang_rule = $db->get_var("select tuanzhang_rule from demo_shezhi where comId=$comId");
if(!empty($tuanzhang_rule)){
    $rules = json_decode($tuanzhang_rule,true);
    $back_img = $rules['yaoqing_back'];
}
if(empty($back_img))$back_img='/skins/default/images/fenxianghaoyou_1.png';
$share_title = $_SESSION['demo_com_title'];
if($comId==1135 && !empty($_SESSION[TB_PREFIX.'user_ID'])){
  $share_title = $db->get_var("select nickname from users where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
  $share_title = $share_title.'的商城';
}
?>
<link rel="stylesheet" type="text/css" href="/skins/default/styles/yongjin.css">
<style type="text/css">
.wodeerweima_3_img{width: 5rem;position: absolute;left:5rem;bottom:<?=$comId==1099?'13.1':'3.1'?>rem;}
.wodeerweima_3_img canvas{width:100%;}
</style>
<div class="fenxianghaoyou">
    <div class="fenxianghaoyou_1">
        分享好友
        <div class="fenxianghaoyou_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/fenlei_1.png"/>
        </div>
        <div class="fenxianghaoyou_1_right">
            奖励规则
        </div>
    </div>
    <div class="fenxianghaoyou_2" id="zhongqiu" style="position:relative;width:15rem;margin:auto;margin-top:1.9rem;">  
        <img src="<?=$comId==10?'/skins/erp_zong/images/fenxianghaoyou_1.png':$back_img?>" crossOrigin="anonymous"/>
        <div class="wodeerweima_3_img" id="erweima">
        </div>
        <? if($comId==10){?>
            <div style="position:absolute;width:3rem;text-align:center;left:6rem;bottom: .85rem;font-size: .7rem;"><?=$userId?></div>
        <? }?>
    </div>
    <div class="fenxianghaoyou_3">
        <ul>
            <li>
                <a href="javascript:" id="copy_weixin" data-clipboard-text="<?=$share_url?>"><img src="/skins/default/images/fenxianghaoyou_12.png"/> 分享链接</a>
            </li>
            <li>
                <a href="javascript:" onclick="layer.open({content:'长按图片进行下载或分享',skin: 'msg',time: 2});"><img src="/skins/default/images/fenxianghaoyou_13.png"/> 分享海报</a>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript" src="/skins/resource/scripts/html2canvas.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/clipboard.min.js"></script>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$comId==10?'直商易购—承包你的吃喝玩乐':$share_title?>';
    var share_img = '<?=$share_img?>';
    var share_desc = '<?=$comId==10?'企业自主电商普及工程指定商城，品牌企业入驻、厂家直供正品保证，底价购买。':$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
    var userId = <?=$userId?>;
    window.onload =function(){
        layer.open({type:2,shadeClose:false});
        var html = document.documentElement;
        var htmlWidth = html.getBoundingClientRect().width;
        if(htmlWidth>960)htmlWidth=960;
        qrcode_width =Math.ceil(htmlWidth*4/4.5);
        $('#erweima').qrcode({width:qrcode_width,height:qrcode_width,text:share_url});
        var shareContent = document.getElementById("zhongqiu");
        var width = shareContent.offsetWidth; 
        var height = shareContent.offsetHeight; 
        //console.log(height);
        var canvas = document.createElement("canvas"); 
        var scale = 2;
        canvas.width = width * scale;
        canvas.height = height * scale;
        canvas.getContext("2d").scale(scale, scale);
        html2canvas(shareContent,{
            useCORS:true,
            scale: scale,
            canvas: canvas,
            width: width,
            height: height
        }
        ).then(canvas => {
            canvas.getContext("2d").scale(scale, scale);
            layer.closeAll();
            var img_data1 = canvas.toDataURL();
            $("#zhongqiu").html('<img src="'+img_data1+'" width="100%">').css("padding-top","0px");
            layer.open({content:'海报生成成功，请长按图片下载或分享',skin: 'msg',time: 2});
        });
        var url = window.location.href;
        url = encodeURIComponent(url);
        WeChat(url,share_url,share_title,share_img,share_desc,0);
        btn1 = document.getElementById('copy_weixin');
        var clipboard1 = new ClipboardJS(btn1);
        clipboard1.on('success', function(e) {
            layer.open({
                content: '已复制'
                ,skin: 'msg'
                ,time: 2
            });
        });
    }
</script>