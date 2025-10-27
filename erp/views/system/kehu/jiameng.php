<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"title"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'title',title:'".$kehu_title."名称',width:250}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:100}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"address"=>array("title"=>"地址","rowCode"=>"{field:'address',title:'地址',width:200}"),
				"dtTime"=>array("title"=>"申请时间","rowCode"=>"{field:'dtTime',title:'申请时间',width:130,sort:true}"),
				"tuijianren"=>array("title"=>"推荐人","rowCode"=>"{field:'tuijianren',title:'推荐人',width:100}"),
				"beizhu"=>array("title"=>"附言","rowCode"=>"{field:'beizhu',title:'附言',width:250}")
			);
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$keyword = $request['keyword'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['jiamengPageNum'])?10:$_COOKIE['jiamengPageNum'];
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
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> 加盟信息
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div>	
						<div class="splist_up_01_right_1" style="margin-left:10px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
							<a href="?m=system&s=kehu&a=daochuJiameng" id="daochuA" target="_blank" onclick="daochu();" class="splist_daochu">导 出</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
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
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=kehu&a=getJiamengList&id=<?=$id?>'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	keyword:'<?=$keyword?>'
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  $("th[data-field='id']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var keyword = $("#keyword").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	//var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,keyword:keyword
			    },page: {
					curr: 1
				},done:function(){
					//$(".layui-table-header").scrollLeft(scrollLeft);
					//$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		});
	</script>
	<div id="bg" onclick="hideRowset();"></div>
	<script type="text/javascript" src="js/kehu_jiameng.js"></script>
	<? require('views/help.html');?>
</body>
</html>