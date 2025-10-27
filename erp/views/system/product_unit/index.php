<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$no_units = $db->get_var("select no_units from demo_product_set where comId=$comId");
$sql = "select * from demo_product_unit where comId in(0,$comId) ";
if(!empty($no_units)){
	$sql.=" and id not in($no_units)";
}
$sql.=" order by id asc";
$units = $db->get_results($sql);
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
	<script type="text/javascript" src="js/product_unit.js"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 计量单位管理
			</div>
			<div class="jiliangdanwei_up_right">
			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product_unit&a=addProductUnit" onclick="edit_unit(0,\'\');">+ 新 增</a>') ?>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="shangpinguanli" style="padding-top:20px">
			<ul>
				<?
				if(!empty($units)){
					foreach ($units as $c){
						?>
						<li data-id="<?=$c->id?>" data-pid="0">
							<div class="shangpinguanli_01">
								<div class="shangpinguanli_01_left">
									<?=$c->title?>
								</div>
								<div class="shangpinguanli_01_right">
								    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product_unit&a=delUnit" onclick="z_confirm(\'确定要删除“'.$c->title.'”吗？\',delUnit,'.$c->id.');"><img src="images/biao_48.png"/> 删除</a>') ?>
								</div>
								<div class="clearBoth"></div>
							</div>
						</li>
					<?
					}
				}?>
			</ul>
		</div>
	</div>
	<? require('views/help.html');?>
</body>
</html>