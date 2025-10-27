<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg_tuanzhang&tuijianren='.$userId;
$user = $db->get_row("select nickname,image from users where id=$userId");
?>
<style type="text/css">
    html,body,#zhongqiu{height:100%}
</style>
<div class="yaoqing_1" onclick="history.go(-1);">
    <img src="/skins/default/images/denglu_14.png" /> 邀请
</div>
<div class="yaoqing" id="zhongqiu" style="position:relative;padding-top:2.175rem">
	<div class="yaoqing_2">
    	<img src="/skins/default/images/yaoqing_1.gif" />
    </div>
	<div class="yaoqing_3">
    	<div class="yaoqing_3_left" id="erweima">
        </div>
    	<div class="yaoqing_3_right">
        	长按识别图中二维码
            <h2>立即申请“团长”</h2>
        </div>
    	<div class="clearBoth"></div>
    </div>
    <img src="/skins/default/images/yaoqing_12.gif" style="position: absolute;left:0px;top:0px;right:0px;bottom:0px;height:100%;z-index:-1;">
</div>
<script type="text/javascript" src="/skins/demo/scripts/html2canvas.js"></script>
<script type="text/javascript" src="/skins/demo/scripts/jquery.qrcode.min.js"></script>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$user->nickname?>邀请你成为团长！';
    var share_img = '<?=ispic($user->image,'/skins/default/images/yaoqing_1.gif')?>';
    var share_desc = '';
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