<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg&tuijianren='.$userId;
$user = $db->get_row("select image from users where id=$userId");
$share_img =$user->image;
if(empty($share_img)){
    $share_img = 'http://'.$_SERVER['HTTP_HOST'].'/skins/shequ/images/erweima.jpg';
}else{
    if(substr($share_img,0,4)!='http'){
        $share_img = 'http://www.zhishangez.com'.$share_img;
    }
}
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/guangchang.css">
<div class="wodeerweima">
    <div class="wodeerweima_1">	
    	我的二维码
        <div class="wodeerweima_1_left" onclick="go_prev_page();">
        	<img src="/skins/erp_zong/images/a923_1.png"/>
        </div>
        <div class="wodeerweima_1_right" onclick="layer.open({content:'长按图片进行下载或分享',skin: 'msg',time: 2});">
        	<img src="/skins/erp_zong/images/a1015_18.png"/> <img src="/skins/erp_zong/images/a1015_19.png"/> 
        </div>
    </div>
    <div id="zhongqiu">
        <div class="wodeerweima_2" style="background-image:url(/skins/shequ/images/erweima.jpg)">
    		
        </div>
        <div class="wodeerweima_3">
    		<div class="wodeerweima_3_img" id="erweima">
            </div>
    		<div class="wodeerweima_3_tt">
            	邀请好友 赚佣金！
            </div>
    		<div class="clearBoth"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/skins/resource/scripts/html2canvas.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/jquery.qrcode.min.js"></script>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$share_img?>';
    var share_desc = '<?=$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
    var userId = <?=$userId?>;
    $(function(){
        layer.open({type:2,shadeClose:false});
        var html = document.documentElement;
        var htmlWidth = html.getBoundingClientRect().width;
        if(htmlWidth>960)htmlWidth=960;
        qrcode_width =Math.ceil(htmlWidth*4/4.5);
        $('#erweima').qrcode({width:qrcode_width,height:qrcode_width,text:share_url});
        var shareContent = document.getElementById("zhongqiu");
        var width = shareContent.offsetWidth; 
        var height = shareContent.offsetHeight; 
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
    });
</script>