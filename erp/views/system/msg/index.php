<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"content"=>array("title"=>"消息内容","rowCode"=>"{field:'content',title:'消息内容',width:'85%'}"),
	"dtTime"=>array("title"=>"时间","rowCode"=>"{field:'dtTime',title:'时间',width:'15%'}")
);
$rowsJS = "{field: 'id',title:'id',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$limit = empty($_COOKIE['msgPageNum'])?10:$_COOKIE['msgPageNum'];
$page = empty($request['page'])?1:$request['page'];
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
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		.layui-table-cell i{width: 6px;height: 6px;display: inline-block;background-color: red;border-radius: 100%;vertical-align: middle;margin-left: 6px;margin-right:10px;}
		tr.deleted span{padding-left:20px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/bangzhu_1.png"/>业务消息
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var productListForm;
		layui.use(['laypage','table','form'], function(){
		  laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListForm = form;
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=msg&a=getJilus'
		    ,page:  {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		});
		function view_jilu(url,msgId,ifread,types){
			if(ifread==0){
				layer.load();
				$.ajax({
					type: "POST",
					url: "?m=system&s=msg&a=setYidu",
					data: "&id="+msgId,
					dataType:"json",timeout : 10000,
					success: function(resdata){}
				});
			}
			location.href=url;
			return false;
		}
	</script>
	<div id="bg" onclick="hideRowset();"></div>
</body>
</html>