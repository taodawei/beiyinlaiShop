<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"name"=>array("title"=>"姓名","rowCode"=>"{field:'name',title:'姓名',width:100}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"wxh"=>array("title"=>"微信号","rowCode"=>"{field:'wxh',title:'微信号',width:150}"),
				"wx_img"=>array("title"=>"微信图片","rowCode"=>"{field:'wx_img',title:'微信图片',width:150}"),
				"dtTime"=>array("title"=>"申请时间","rowCode"=>"{field:'dtTime',title:'申请时间',width:150,sort:true}"),
				"statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}")
			);
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
		td[data-field="title"] div,td[data-field="address"] div,td[data-field="remark"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> 分销团长申请
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
								<span>审核状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_01_down">
								<ul style="border-left:0px">
									<li class="allsort_01">
										<a href="javascript:selectStatus(0,'审核状态');">审核状态</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:selectStatus(2,'待审核');">待审核</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:selectStatus(1,'审核通过');">审核通过</a>
									</li>
									<li class="allsort_01">
										<a href="javascript:selectStatus(-1,'审核通过');">未通过</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1" style="margin-left:10px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入姓名或手机号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
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
						<li id="tongguo_btn">
							<a href="javascript:" onclick="z_confirm('确定要通过该团长申请吗？',shenhe,'');"><img src="images/shangchengdd_32.png"> 通过</a>
						</li>
						<li id="bohui_btn">
							<a href="javascript:" onclick="z_confirm('确定要驳回该申请吗？',bohui,'');"><img src="images/shangchengdd_25.png"> 驳回</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要删除该申请吗？',del_shenqing,'');"><img src="images/biao_32.png"> 删除</a>
						</li>
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
		    ,url: '?m=system&s=users&a=getTuanzhangsqList'
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
	<script type="text/javascript" src="js/users/tuanzhangsq.js"></script>
	<? require('views/help.html');?>
</body>
</html>