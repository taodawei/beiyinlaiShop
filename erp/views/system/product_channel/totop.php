<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/departs_$comId.php")){
	$cache = 1;
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content,true);
}
if(empty($channels))$channels = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/product_channel.js"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 商品分类管理
			</div>
			<div class="jiliangdanwei_up_right">
				<a href="javascript:" onclick="edit_channel(0,0,'');">+ 新 增</a>
			</div>
			<div class="clearBoth"></div>
		</div>
	</div>

</body>
</html>