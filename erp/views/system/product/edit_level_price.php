<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_inventory = $db->get_row("select productId from demo_product_inventory where id=$id limit 1");
$productId = $product_inventory->productId;

$inventorys = $db->get_results("select * from demo_product_inventory where productId = $productId");
foreach ($inventorys as $inventory){
    $kucun = $db->get_row("select * from demo_kucun where inventoryId = $inventory->id");
    if(!$kucun){
        $addKucun = array(
            'comId' => $comId,
            'storeId' => 5,
            'productId' => $productId,
            'inventoryId' => $inventory->id,
            'kucun' => 0,
            'entitle' => 'Z'
        );
        
        $db->insert_update("demo_kucun", $addKucun, "id");
    }
}

$product = $db->get_row("select * from demo_product where id=$productId");
if(empty($product)){
	die("<script>alert('产品不存在或已删除');history.go(-1);</script>");
}
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$chushu = pow(10,$product_set->price_num);
$step = 1/$chushu;
$chushu1 = pow(10,$product_set->number_num);
$step1 = 1/$chushu1;
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spgl.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style type="text/css">html,body,form,.content_edit{height:100%;}</style>
</head>
<body>
	<form action="?m=system&s=product&a=edit_level_price&tijiao=1&id=<?=$productId?>" method="post" id="createPdtForm" class="layui-form">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit" style="display:table;">
			<div class="edit_h">
				<a href="javascript:history.go(-1);"><img src="images/back.jpg" /></a>
				<span>修改商品库存</span>
			</div>
		
			<div class="guige_set" id="moreGuige" style="margin-bottom:80px;">

			</div>
			<div class="edit_save">
				<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
				<a class="layui-btn layui-btn-primary" onclick="history.go(-1);" href="javascript:;">取 消</a>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="productId" id="productId" value="<?=$productId?>">
</form>
<div id="bg"></div>
<script type="text/javascript" src="js/product_editPrice.js"></script>
<? require('views/help.html');?>
</body>
</html>