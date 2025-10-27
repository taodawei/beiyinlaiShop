<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];

$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];
$allRows = array(
				"title"=>array("title"=>"经销商名称","rowCode"=>"{field:'title',title:'经销商名称',width:250}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:150}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"weixin"=>array("title"=>"邮箱","rowCode"=>"{field:'weixin',title:'邮箱',width:200}"),
				// "bili"=>array("title"=>"抽成","rowCode"=>"{field:'bili',title:'抽成',width:150,sort:true}"),
				"area_info"=>array("title"=>"所在地区","rowCode"=>"{field:'area_info',title:'所在地区',width:200}"),
				"address"=>array("title"=>"详细地址","rowCode"=>"{field:'address',title:'详细地址',width:200}"),
				"dtTime"=>array("title"=>"开通时间","rowCode"=>"{field:'dtTime',title:'开通时间',width:150,sort:true}"),
				"statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}")
			);
if($mendianId > 0){
    $allRows = array(
				"title"=>array("title"=>"经销商名称","rowCode"=>"{field:'title',title:'经销商名称',width:250}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:150}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"weixin"=>array("title"=>"邮箱","rowCode"=>"{field:'weixin',title:'邮箱',width:200}"),
				// "bili"=>array("title"=>"抽成","rowCode"=>"{field:'bili',title:'抽成',width:150,sort:true}"),
				"area_info"=>array("title"=>"所在地区","rowCode"=>"{field:'area_info',title:'所在地区',width:200}"),
				"address"=>array("title"=>"详细地址","rowCode"=>"{field:'address',title:'详细地址',width:200}"),
				"dtTime"=>array("title"=>"开通时间","rowCode"=>"{field:'dtTime',title:'开通时间',width:150,sort:true}"),
				// "statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}")
			);
}
			
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
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
		<img src="images/biao_94.png"/> 经销商列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left" style="display:none;">
						<div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
								<span>状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_01_down">
								<ul style="border-left:0px">
									<li class="allsort_01">
										<a href="javascript:selectStatus(1,'正常');">正常</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:selectStatus(-1,'暂停');">暂停</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1" style="margin-left:10px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入经销商名称/联系人/手机号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
						    <? if($mendianId == 0) chekurl($arr,'<a href="?m=system&s=users&a=add_shequ" class="splist_add">新 增</a>') ?>
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
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
					    <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=users&a=view_shequ" onclick="view();"><img src="images/biao_30.png"> 详情</a></li>') ?>
					    <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=users&a=add_shequ" onclick="edit();"><img src="images/biao_30.png"> 编辑</a></li>') ?>
					    <? chekurl($arr,'<li id="tongguo_btn"><a href="javascript:" _href="?m=system&s=users&a=sheheShequ" onclick="shenhe();"><img src="images/shangchengdd_32.png"> 开通</a></li>') ?>
						<? chekurl($arr,'<li id="bohui_btn"><a href="javascript:" _href="?m=system&s=users&a=bohuiShequ" onclick="z_confirm(\'确定要暂停该经销商吗？\',bohui,\'\');"><img src="images/shangchengdd_25.png"> 暂停</a></li>') ?>
					</ul>
				</div>
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
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=users&a=getShequList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	keyword:'<?=$keyword?>',
		    	status:<?=$status?>
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var keyword = $("#keyword").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var status = $("#status").val();
		  	//var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,status:status
			      ,keyword:keyword
			    },page: {
					curr: 1
				},done:function(){
					layer.closeAll('loading');
					$("th[data-field='id']").hide();
		    		$("th[data-field='status']").hide();
				}
			  });
		  });
		});
	</script>
	<div id="bg" onclick="hideRowset();"></div>
	<script type="text/javascript" src="js/users/shequ.js"></script>
	<? require('views/help.html');?>
</body>
</html>