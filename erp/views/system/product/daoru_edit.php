<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$storeId = (int)$request['storeId'];
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
if(is_file("../cache/kucun_set_$comId.php")){
	$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$rukuTypes = explode('@_@',$kucun_set->ruku_types);
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
		<img src="images/biao_77.png"/> 商品导入修改
	</div>
	<div class="right_down">
		<div class="splist_up_addtab">
        	<ul>
        		<li>
                	<a href="?s=product&a=daoru" >导入新增商品</a>
                </li>
                <li>
                	<a href="javascript:" class="splist_up_addtab_on">导入修改商品</a>
                </li>                
                <div class="clearBoth"></div>
        	</ul>
        </div>
		<form action="?m=system&s=product&a=daoru_edit1" method="post" id="pandianForm" class="layui-form">
		<div class="kucunpandian">
			<div class="kucunpandian_01" style="height:196px;">
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
					
				</ul>
				<div class="clearBoth"></div>
				<div style="display:inline-block;float:left;margin-left:40px;">注意事项：</div><div style="text-align:left;color:red;display:inline-block;vertical-align:top;float:left;">1.导入商品以商品编码为识别条件，所以不要修改商品的编码栏；<br>2.务必删掉不修改的商品记录<br>3.单次上传商品记录不要超过1000条<br>4.第一行不能删 <br>5.类目 筛选条件 货号 产品名称 货期 规格 价格 这几个固定字段必有且不能改变顺序</div>
			</div>
			<div class="kucunpandian_02">
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						1、下载商品模板文件
					</div>
					<div class="kucunpandian_02_1_down">
						<div class="kucunpandian_02_1_down_1">
							<div class="layui-form-select">
								<div class="layui-select-title" id="selectChannel"><input type="text" readonly value="全部分类" class="layui-input"><i class="layui-edge"></i></div>
								<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
							</div>
							<input type="hidden" name="channelId" id="channelId" value="0" lay-verify="required">
						</div>
						<div class="kucunpandian_shujuxiazai">
							<a href="?m=system&s=product&a=daochu" id="daochuA" onclick="daochu();"><img src="images/biao_78.png"/> 下载商品数据</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						2、上传商品数据<span style="font-size:13px;color:#999">(上传前请删除不需要修改的商品)</span>
					</div>
					<div class="kucunpandian_02_1_down">
						<button type="button" class="layui-btn" id="uploadFile">上传商品数据文件</button>
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