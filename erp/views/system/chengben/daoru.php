<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$storeId = (int)$request['storeId'];
$cangkuSql = "select id,title from demo_kucun_store where comId=$comId";
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
	$cangkuSql .= " and id in(".$qx_arry['kucun']['storeIds'].")";
}
$cangkuSql .= " order by id asc";
$cangkus = $db->get_results($cangkuSql);
//$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
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
		<img src="images/biao_77.png"/> 商品成本调整导入
	</div>
	<div class="right_down">
		<form action="?m=system&s=chengben&a=pandian1" method="post" id="pandianForm" class="layui-form">
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
						1、选择调整仓库
					</div>
					<div class="kucunpandian_02_1_down">
						<div style="width:250px">
							<select name="storeId" id="storeId" lay-verify="required">
								<? foreach($cangkus as $cangku){
									?><option value="<?=$cangku->id?>" <? if($storeId==$cangku->id){?>selected="selected"<? }?>><?=$cangku->title?></option><?
								}?>
							</select>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						2、商品库存数据下载
					</div>
					<div class="kucunpandian_02_1_down">
						<div class="kucunpandian_02_1_down_1">
							<select name="channelId" id="channelId" lay-verify="required" lay-search>
								<option value="0">全部分类</option>
							</select>
						</div>
						<div class="kucunpandian_shujuxiazai">
							<a href="?m=system&s=chengben&a=daochuPandian" id="daochuA" onclick="daochu();"><img src="images/biao_78.png"/> 下载商品库存数据</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						3、上传成本调整数据<span style="font-size:13px;color:#999">(上传前请删除不需要修改的产品)</span>
					</div>
					<div class="kucunpandian_02_1_down">
						<button type="button" class="layui-btn" id="uploadFile">上传成本调整数据文件</button>
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