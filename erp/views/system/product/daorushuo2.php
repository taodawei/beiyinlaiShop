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

$type = (int)$request['type'];
$title = $type ? '中文说明书导入':'英文说明书导入';
$up = $type ? 'uploadFile2' : 'uploadFile1';

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
		<img src="images/biao_77.png"/> <?=$title?>
	</div>
	<div class="right_down">
		<div class="splist_up_addtab">
        	<ul>
        		<li>
                	<a href="javascript:" class="splist_up_addtab_on"><?=$title?></a>
                </li>
                <li>
                	<!--<a href="?s=product&a=daoru_edit">导入修改商品</a>-->
                </li>                
                <div class="clearBoth"></div>
        	</ul>
        </div>
    
		<form action="?m=system&s=product&a=daorushuo1&type=<?=$type?>" method="post" id="pandianForm" class="layui-form">
		    
		<input type="hidden" id="type" value="<?=$type?>">
		<div class="kucunpandian">
			<div class="kucunpandian_01" style="height: 270px;">
				<ul>
					<li class="kucunpandian_01_right">
						<a class="kucunpandian_01_bj1">上传导入文件</a>
					</li>
					<!--<li class="kucunpandian_01_right">-->
					<!--	<a class="kucunpandian_01_bj2">导入文件预览</a>-->
					<!--</li>-->
					<li>
						<a class="kucunpandian_01_bj2">导入完成</a>
					</li>
					<div class="clearBoth"></div>
				</ul>
				<div class="clearBoth"></div>
				<div style="display:inline-block;float:left;margin-left:40px;height:200px;">说明书导入注意事项：</div><div style="text-align:left;color:red;display:inline-block;vertical-align:top;float:left;">
				<br>
                1、第一列填写产品货号<br>
                2、每次最多批量导入1000条数据，分批导入<br>
                3、Excel表格底部不能出现多余的空白行，需要删除后再导入<br>
                4、字段需要和说明书一一对应，如出现少字段问题，所有字段数据将会出现问题<br>
                				</div>
			</div>
			<div class="kucunpandian_02">
				<div class="kucunpandian_02_1" style="display:none;">
					<div class="kucunpandian_02_1_up">
						1、下载商品模板文件
					</div>
					<div class="kucunpandian_02_1_down">
						<div class="kucunpandian_shujuxiazai">
							<a href="images/商品导入模板.xlsx" target="_blank"><img src="images/biao_78.png"/> 下载商品模板文件</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						上传说明书文件并且导入
					</div>
					<div class="kucunpandian_02_1_down">
						<button type="button" class="layui-btn" id="<?=$up?>">上传说明书文件并完成导入</button>
						<input type="hidden" name="filepath" id="filepath">
						<span id="uploadMsg"></span>
					</div>
				</div>
			</div>
			<div class="kucunpandian_03">
				<!--<a href="" id="uploadFile1" class="kucunpandian_03_1">确定导入</a><a href="javascript:history.go(-1);" class="kucunpandian_03_2">取 消</a>-->
			</div>
		</div>
	</form>
	</div>
	
	<script type="text/javascript" src="js/pandian.js"></script>
	<? require('views/help.html');?>
</body>
</html>