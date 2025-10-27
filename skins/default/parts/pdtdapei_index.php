<link href="/skins/fushi/styles/guoshenqun.css" rel="stylesheet" type="text/css">
<div class="chuandatuijian">
	<div class="chuandatuijian_1">
    	搭配推荐
        <div class="chuandatuijian_1_left" onclick="go_prev_page();">
        	<img src="/skins/fushi/images/chuanda_1.png"/>
        </div>
    </div>
	<div class="chuandatuijian_2">
    	<ul id="flow_ul">
    		
    	</ul>
    </div>
</div>
<script type="text/javascript">
  var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
  var share_title = '搭配推荐-<?=$_SESSION['demo_com_title']?>';
  var share_img = 'http://buy.zhishangez.com/skins/erp_zong/images/share_logo.png';
  var share_desc = '<?=$_SESSION['demo_com_remark']?>';
  $(function(){
    var url = window.location.href;
    url = encodeURIComponent(url);
    WeChat(url,share_url,share_title,share_img,share_desc,0);
  });
</script>
<script type="text/javascript" src="/skins/erp_zong/scripts/product/dapei.js"></script>