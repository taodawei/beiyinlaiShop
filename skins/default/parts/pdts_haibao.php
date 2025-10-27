<?
global $db,$request;
$id = (int)$request['id'];
$productId = (int)$request['productId'];
$product = $db->get_row("select title,share_img,originalpic from demo_pdt where id=$productId");
$originalPics = array();
if(!empty($product->originalpic)){
    $originalPics = explode('|',$product->originalpic);
}
?>
<link href="/skins/default/styles/bendi.css" rel="stylesheet" type="text/css">
<div class="haibao" style="background-color:#f6f6f6;">
	<img src="<?=ispic($product->share_img)?>"/>
</div>
<script type="text/javascript">
    var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?p=22&a=view&id=<?=$id?>';
    var share_title = '<?=$product->title?>';
    var share_img = '<?=$originalPics[0]?>';
    var share_desc = '<?=$_SESSION['demo_com_title']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,<?=$id?>);
    });
</script>