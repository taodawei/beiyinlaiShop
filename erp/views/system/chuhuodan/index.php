<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"name"=>array("title"=>"收件姓名","rowCode"=>"{field:'name',title:'收件姓名',width:100}"),
				"product"=>array("title"=>"品种名称","rowCode"=>"{field:'product',title:'品种名称',width:150}"),
				"penjing"=>array("title"=>"盆径","rowCode"=>"{field:'penjing',title:'盆径',width:100}"),
				"guige"=>array("title"=>"规格","rowCode"=>"{field:'guige',title:'规格',width:150}"),
				"toushu"=>array("title"=>"头数","rowCode"=>"{field:'toushu',title:'头数',width:150}"),
				"num"=>array("title"=>"件数","rowCode"=>"{field:'num',title:'件数',width:150}"),
				"shuliang"=>array("title"=>"数量","rowCode"=>"{field:'shuliang',title:'数量',width:150}"),
				"remark"=>array("title"=>"备注","rowCode"=>"{field:'remark',title:'备注',width:150}"),
				"print"=>array("title"=>"打印","rowCode"=>"{field:'print',title:'打印',width:80}")
			);
$rowsJS = "";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$keyword = $request['keyword'];
$status = (int)$request['status'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
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
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="address"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> 出货单列表（<font color="red">PS：导出出货单前请先进行发货操作</font>）
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01" style="margin-top: 15px;">
							发货日期：<input type="text" autocomplete="off" style="height:35px;padding-left:5px;background: 0 0;font-size: 12px;color: grey;border: 1px solid #ccc;border-radius: 3px;" id="fahuo_time" value="" placeholder="请选择发货日期"/>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_3">
							<a href="?s=chuhuodan&a=daochu" id="daochuA" onclick="daochu();" class="splist_add">导 出</a>
							<a href="?s=chuhuodan&a=printall" id="daochuB" onclick="daochu1();" class="splist_add">打 印</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var fahuoTime = '<?=date("Y-m-d")?>';
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form;
		  laydate.render({
		  	elem: '#fahuo_time'
            ,value:'<?=date("Y-m-d")?>',
            done: function(value, date, endDate){
            	fahuoTime = value;
            	reloadTable(0);
            }
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=chuhuodan&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	fahuoTime:fahuoTime
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		});
	</script>
	<div id="bg" onclick="hideRowset();"></div>
	<script type="text/javascript" src="js/fahuo/chuhuodan.js?v=1"></script>
	<? require('views/help.html');?>
</body>
</html>