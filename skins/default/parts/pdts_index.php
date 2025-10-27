<?
global $db,$request,$seotitle,$share_desc;
$comId = (int)$_SESSION['demo_comId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$shi_id = empty($request['shi_id'])?(int)$_SESSION['shi_id']:(int)$request['shi_id'];
if(empty($shi_id)){
	if($comId==10){
		$db_service = getCrmDb();
		$area_id = (int)$db_service->get_var("select areaid from demo_user where id=$userId");
		if($area_id>0)$shi_id = $db->get_var("select parentId from demo_area where id=$area_id");
	}else{
		$shi_id = (int)$db->get_var("select city from users where id=$userId");
	}
}else{
	if($comId!=10){
		$db->query("update users set city=$shi_id where id=$userId");
	}
	$_SESSION['shi_id'] = $shi_id;
}
$shi_title = '选择城市';
if(!empty($shi_id)){
	$shi_title = $db->get_var("select title from demo_area where id=$shi_id");
}
?>
<link href="/skins/default/styles/bendi.css" rel="stylesheet" type="text/css">
<div class="bendiliebiao">	
	<div class="bendiliebiao_up">
    	<div class="bendiliebiao_up_left" onclick="location.href='/index.php'">
        	<img src="/skins/default/images/bendi_1.png"/> 直商易购本地
        </div>
    	<div class="bendiliebiao_up_right" onclick="location.href='/index.php?p=22&a=select_shi'">
        	<span id="select_shi"><?=$shi_title?></span> <img src="/skins/default/images/bendi_11.png"/>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="bendiliebiao_down">	
    	<ul id="flow_ul">
    		
    	</ul>
    </div>
</div>
<script type="text/javascript">
	var shi_id = <?=$shi_id?>;
    <?
      if(empty($shi_id) && strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false){
        require_once "wxshare.php";
        $jssdk = new JSSDK("wx7a91a4f2eccb30db", "368a5e47cb481c6aebfe0376ef71a463");
        $signPackage = $jssdk->GetSignPackage();
        ?>
          wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <?php echo $signPackage["timestamp"];?>,
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
            'getLocation'
            ]
          });
          wx.ready(function () {
            layer.open({type:2,content:'定位中...'});
            wx.getLocation({
              type: 'wgs84',
              success: function (res) {
                var latitude = res.latitude;
                var longitude = res.longitude;
                $.ajax({
                  url: '/index.php?p=1&a=dingwei&longitude='+longitude+'&latitude='+latitude,  
                  type:'post',
                  data: '',
                  dataType : "json",timeout : 10000,
                  error: function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.closeAll();
                  },
                  success:function(data){
                    layer.closeAll();
                    if(data.code ==1){
                      $('#select_shi').text(data.title);
                      shi_id = data.city;
                      $("#flow_ul").html('');
                      rend_pdt_list();
                    }
                  }
                });         
              }
            })
          });
        <?
      }
    ?>
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '<?=$seotitle?>';
    var share_img = 'http://<?=$_SERVER['HTTP_HOST']?>/skins/erp_zong/images/share22.png';
    var share_desc = '<?=$share_desc?>';
    var if_yushou = <?=empty($yushou)?0:1?>;
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>
<script type="text/javascript" src="/skins/default/scripts/product/pdt_index.js"></script>