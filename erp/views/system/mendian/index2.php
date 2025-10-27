<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"title"=>array("title"=>"商家名称","rowCode"=>"{field:'title',title:'商家名称',width:250}"),
				"username"=>array("title"=>"账号","rowCode"=>"{field:'username',title:'账号',width:130}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:100}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"address"=>array("title"=>"地址","rowCode"=>"{field:'address',title:'地址',width:200}"),
				"product_type"=>array("title"=>"经营品类","rowCode"=>"{field:'product_type',title:'经营品类',width:130}"),
				"shouyi"=>array("title"=>"商家收益","rowCode"=>"{field:'shouyi',title:'商家收益',width:150}"),
				"baozhengjin"=>array("title"=>"保证金","rowCode"=>"{field:'baozhengjin',title:'保证金',width:130}"),
				"dtTime"=>array("title"=>"添加时间","rowCode"=>"{field:'dtTime',title:'添加时间',width:150,sort:true}"),
				"tuijianren"=>array("title"=>"推荐人","rowCode"=>"{field:'tuijianren',title:'推荐人',width:100}"),
				"statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}")
			);
$rowsJS = "{field: 'status', title: 'status', width:0,style:\"display:none;\"},{field:'supplierId',title:'supplierId',width:0,style:\"display:none;\"},{field: 'id', title: '序号', width:80, sort: true}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$keyword = $request['keyword'];
$status = (int)$request['status'];
$type = $request['type'];
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
		td[data-field="title"] div,td[data-field="address"] div,td[data-field="title"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> 同城商铺列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1" style="margin-left:10px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商家名称"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
                            <a href="?s=mendian&a=add_mendian" class="splist_add">新 增</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list"></table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<li>
							<a href="javascript:mendian_view();"><img src="images/biao_30.png"> 详情</a>
						</li>
						<li>
							<a href="javascript:mendian_caiwu();"><img src="images/caiwu.png"> 财务</a>
						</li>
						<li>
							<a href="javascript:mendian_pdt1();"><img src="images/shangpin.png"> 商家商品</a>
						</li>
						<li class="btn_qiyong">
							<a href="javascript:" onclick="edit();"><img src="images/shangchengdd_32.png"> 编辑</a>
						</li>
						<li id="btn_jinyong">
							<a href="javascript:z_confirm('确定要禁用该商家吗？',jinyong,'');"><img src="images/biao_120.png"> 禁用</a>
						</li>
						<li id="btn_qiyong">
							<a href="javascript:z_confirm('确定要启用该商家吗？',qiyong,'');"><img src="images/biao_888.png"> 启用</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="type" value="<?=$type?>">
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
		    ,url: '?m=system&s=mendian&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	keyword:'<?=$keyword?>',
		    	status:<?=$status?>,
		    	type:'<?=$type?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='status']").hide();
		    	$("th[data-field='supplierId']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var keyword = $("#keyword").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var status = $("#status").val();
		  	var type = $("#type").val();
		  	var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,status:status
			      ,keyword:keyword
			      ,type:type
			    },page: {
					curr: 1
				},done:function(){
					$("th[data-field='status']").hide();
					$("th[data-field='supplierId']").hide();
					layer.closeAll('loading');
					$(".layui-table-body").scrollLeft(scrollLeft);
				}
			  });
		  });
		});
	</script>
	<div id="bg" onclick="hideRowset();"></div>
	<script type="text/javascript" src="js/mendian/index.js"></script>
	<? require('views/help.html');?>
</body>
</html>