<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$inventory = $db->get_results("select id,title from demo_product_inventory where comId=$comId order by id");
$changId = (int)$request['changeId'];
$chang = $db->get_row("select * from kmd_change where id = $changId");
$inventoryOptions = '';
if(!empty($inventory)){
    foreach ($inventory as $u) {
        $inventoryOptions.='<option value="'.$u->id.'">'.$u->title.'</option>';
    }
}
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
	<script type="text/javascript" src="/keditor/kindeditor1.js"></script>
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		var $unitOptions = '<?=$unitOptions?>';
		var lipinka_str = '<?=$lipinka_str?>';
		var step = <?=$step?>;
		var step1 = <?=$step1?>;
	</script>
	<style type="text/css">
		.edit_guige .layui-form-select,#moreGuige .layui-form-select{width:80%;margin:0px auto;}
		.guige_set table tr td .layui-select-title input{width:100%;margin:0px auto;height:32px;}
	</style>
</head>
<body>
	<form action="?m=system&s=change&a=batchAdd&tijiao=1" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<div class="content_edit">
			<div class="edit_h">
				<a href="javascript:history.go(-1);"><img src="images/back.jpg" /></a>
				<span><?=$chang->title?> 批量生成兑换卡</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_message">
					<ul>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>生成数量
							</div>
							<div class="gaojisousuo_right">
								<input type="text" class="layui-input" name="num" id="num" value="" lay-verify="required" placeholder="请输入生成卡密数量">
							</div>
						</li>
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>生效日期
							</div>
							<div class="gaojisousuo_right">
								<input type="text" name="startTime" value="<?=empty($list->startTime)?'':($list->startTime=='0000-00-00'?'':$list->startTime)?>" id="startTime" placeholder="请选择生效日期" class="addhuiyuan_2_02_input"/>
							</div>
						</li>
						
						<li>
							<div class="gaojisousuo_left">
								<span>*</span>失效日期
							</div>
							<div class="gaojisousuo_right">
							    <input type="text" name="endTime" value="<?=empty($list->endTime)?'':($list->endTime=='0000-00-00'?'':$list->endTime)?>" id="endTime" placeholder="请选择失效日期" class="addhuiyuan_2_02_input"/>
							</div>
						</li>
						<div class="clearBoth"></div>
					</ul>
				</div>
			</div>
			<div class="clearBoth"></div>
		<div class="edit_save">
            <input type="hidden" name="tijiao" value="1">
            <input type="hidden" name="changeId" value="<?=$chang->id?>">
			<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
			<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
		</div>
	</div>
</div>

</form>
<div id="bg"></div>

<script type="text/javascript">
	var jishiqi;
	var kehu_title = '<?=$kehu_title?>';
	var dinghuoHtml = '';
	$("#shichangjia0").bind('input propertychange', function(){
		var val = parseFloat($(this).val());
		if(!isNaN(val)){
			$("#shichangjia").val(val);
			$(".dinghuo_money").each(function(){
				var zhekou = parseFloat($(this).attr("data-zhekou"))/100;
				var price = parseInt(val*zhekou*100)/100;
				$(this).val(price);
			});
		}
	});
	$("#shichangjia").bind('input propertychange', function(){
		var val = parseFloat($(this).val());
		if(!isNaN(val)){
			$(".dinghuo_money").each(function(){
				var zhekou = parseFloat($(this).attr("data-zhekou"))/100;
				var price = parseInt(val*zhekou*100)/100;
				$(this).val(price);
			});
		}
	});
	$('#searchInput1').bind('input propertychange', function() {
		clearTimeout(jishiqi);
		var row = $(this).attr('row');
		var val = $(this).val();
		jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
	});
	$('#searchInput1').click(function(eve){
		var nowRow = $(this).attr("row");
		if($("#pdtList"+nowRow).css("display")=="none"){
			$("#pdtList"+nowRow).show();
			getPdtInfo(nowRow,$(this).val());
		}
		stopPropagation(eve);
	});
	$('#searchKehuInput').bind('input propertychange', function() {
		clearTimeout(jishiqi);
		var row = $(this).attr('row');
		var val = $(this).val();
		jishiqi=setTimeout(function(){getKehuList(val);},500);
	});
	$('#searchKehuInput').click(function(eve){
		var nowRow = $(this).attr("row");
		if($("#kehuList").css("display")=="none"){
			$("#kehuList").show();
			getKehuList($(this).val());
		}
		stopPropagation(eve);
	});
</script>

<script>
	layui.use(['laydate','form'], function(){
		  var laydate = layui.laydate
		  ,form = layui.form
		  laydate.render({
		  	elem: '#startTime'
		  	,min:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
		  });
		  
		  laydate.render({
		  	elem: '#endTime'
		  	,min:'<?=date("Y-m-d H:i:s")?>'
            <? if(!empty($user->birthday)&&$user->birthday!='0000-00-00'){?>,value:'<?=$user->birthday?>'<?}?>
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
		  });
		 
		});
		
</script>

<script type="text/javascript" src="js/product_create.js"></script>
<? require('views/help.html');?>
</body>
</html>