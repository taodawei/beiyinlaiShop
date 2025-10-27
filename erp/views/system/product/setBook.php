<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$inventoryId = (int)$request['id'];
$inventory = $db->get_row("select * from demo_product_inventory where id = $inventoryId");

$product = $db->get_row("select * from demo_product where id = $inventory->productId");
$type = (int)$request['type'];
$title = $type ? '商品PDF说明书导入':'商品PDF说明书导入';
$up = 'uploadFile3';

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
                1、附件类型必须为PDF。<br>
                2、附件尽量压缩，不要超过2M。<br>
                3、附件名称中不要使用符号。<br>
                				</div>
			</div>
			<div class="kucunpandian_02">
			    <? if(!empty($product->book_url)){ ?>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						1、下载当前商品的PDF说明书
					</div>
					<div class="kucunpandian_02_1_down">
						<div class="kucunpandian_shujuxiazai">
							<a href="<?=$product->book_url?>" target="_blank"><img src="images/biao_78.png"/>下载商品的PDF说明书</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>
				<? }else{?>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						1、当前商品尚未上传PDF说明书
					</div>
				
				</div>
				<? } ?>
				<div class="kucunpandian_02_1">
					<div class="kucunpandian_02_1_up">
						2、上传说明书文件并且绑定
					</div>
					<div class="kucunpandian_02_1_down">
						<button type="button" class="layui-btn" id="<?=$up?>">上传PDF说明书并且绑定</button>
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
	<!--<script type="text/javascript" src="js/pandian.js"></script>-->
    <script>
layui.use(['upload','form'], function(){
	var form = layui.form
	,upload = layui.upload;
	
    upload.render({
	    elem: '#uploadFile3'
	    ,url: '?m=system&s=upload&a=uploadPdf'
	    ,accept: 'file'
    	,exts: 'pdf'
	    ,before: function(obj){
	        console.log(1111,obj);
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      //导入成功之后
	      $.ajax({
			type:"post",
			url:"?m=system&s=product&a=setBook&submit=1&inventoryId=<?=$inventoryId?>&productId=<?=$inventory->productId?>",
			data:"filepath="+res.url,
			timeout:"60000",
			dataType:"json",
			async:false,
			success: function(data){
				// reloadTable(0);
				layer.msg(data.content,{time:2000},function(){
				    
				    window.location.reload();
				});
			},
			error:function(){
			    
	           // alert("超时,请刷新");
	        }

	    });
	      //导入成功之后
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});
});
    </script>
	<? require('views/help.html');?>
</body>
</html>