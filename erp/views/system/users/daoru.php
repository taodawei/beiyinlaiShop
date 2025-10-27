<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_77.png"/> 会员导入
	</div>
	<div class="right_down">
		<form action="?s=users&a=daoru1" method="post" id="pandianForm" class="layui-form">
		<div class="kucunpandian">
			<div class="kucunpandian_01">
				<ul>
					<li class="kucunpandian_01_right">
						<a class="kucunpandian_01_bj1">上传导入文件</a>
					</li>
					<li class="kucunpandian_01_right">
						<a class="kucunpandian_01_bj2">导入文件预览</a>
					</li>
					<li>
						<a class="kucunpandian_01_bj2">导入完成</a>
					</li>
					<div class="clearBoth"></div>
				</ul>
			</div>
			<div class="kucunpandian_02">
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						1、下载模板文件
					</div>
					<div class="kucunpandian_02_1_down">
						<div class="kucunpandian_shujuxiazai">
							<a href="?s=users&a=downMoban" target="_blank"><img src="images/biao_78.png"/> 下载模板文件</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						2、上传会员数据
					</div>
					<div class="kucunpandian_02_1_down">
						<button type="button" class="layui-btn" id="uploadFile">上传会员数据文件</button>
						<input type="hidden" name="filepath" id="filepath">
						<span id="uploadMsg"></span>
					</div>
				</div>
			</div>
			<div class="kucunpandian_03">
				<a href="javascript:checkForm();" class="kucunpandian_03_1">下一步</a><a href="javascript:history.go(-1);" class="kucunpandian_03_2">取 消</a>
			</div>
		</div>
	</form>
	</div>
	<script type="text/javascript" src="js/pandian.js"></script>
	<? require('views/help.html');?>
</body>
</html>