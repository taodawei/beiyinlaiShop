<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"orderId"=>array("title"=>"采购单号","rowCode"=>"{field:'orderId',title:'采购单号',width:170}"),
	"supplierName"=>array("title"=>"供应商","rowCode"=>"{field:'supplierName',title:'供应商',width:200}"),
	"ifjiaji"=>array("title"=>"是否紧急","rowCode"=>"{field:'ifjiaji',title:'是否紧急',width:80}"),
	"price"=>array("title"=>"采购金额","rowCode"=>"{field:'price',title:'采购金额',width:100}"),
	"username"=>array("title"=>"制单人","rowCode"=>"{field:'username',title:'制单人',width:70}"),
	"caigouyuan"=>array("title"=>"采购员","rowCode"=>"{field:'caigouyuan',title:'采购员',width:80}"),
	"dtTime"=>array("title"=>"采购日期","rowCode"=>"{field:'dtTime',title:'采购日期',width:140}"),
	"status"=>array("title"=>"审核状态","rowCode"=>"{field:'status',title:'审核状态',width:80}"),
	"shenheName"=>array("title"=>"审批人","rowCode"=>"{field:'shenheName',title:'审批人',width:70}"),
	"shenheTime"=>array("title"=>"审核时间","rowCode"=>"{field:'shenheTime',title:'审核时间',width:150}"),
	"rukuStatus"=>array("title"=>"状态","rowCode"=>"{field:'rukuStatus',title:'状态',width:70}"),
);
$rowsJS = "{field: 'id',title:'id',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$status = (int)$request['status'];
$keyword = $request['keyword'];
$page = empty($request['page'])?1:(int)$request['page'];
$limit = empty($_COOKIE['caigouPageNum'])?10:$_COOKIE['caigouPageNum'];
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
		td[data-field="supplierName"] div,td[data-field="sn"] div,td[data-field="address"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		#riqi1 .layui-laydate{border-right:0px;}
		#riqi2 .layui-laydate{border-left:0px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/purchase.gif"/>采购单管理
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02" style="margin-left:20px">
							<div class="splist_up_01_left_03_up">
								<span>所有状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectZt(0,'所有状态');">所有状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(1,'已审核');">已审核</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(-1,'已驳回');">已驳回</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(2,'待审核');">待审核</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(10,'待入库');">待入库</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(11,'部分入库');">部分入库</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(12,'已入库');">已入库</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入采购单号/供应商名称/制表人/采购人"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
                            <a href="?m=system&s=caigou&a=add" class="splist_add">新 增</a>
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
						<li>
							<a href="javascript:detail();"><img src="images/xingqing.png"> 详情</a>
						</li>
						<li class="ruku">
							<a href="javascript:ruku();"><img src="images/ruku.png"> 入库</a>
						</li>
						<li class="tuihuo">
							<a href="javascript:tuihuo();"><img src="images/tuihuo.png"> 退货</a>
						</li>
						<li>
							<a href="javascript:dayin();"><img src="images/dayin.png"> 打印</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var productListForm;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListForm = form;
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=caigou&a=getJilus'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:<?=$status?>,
		    	keyword:'<?=$keyword?>'
		    },done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		});
	</script>
	<script type="text/javascript" src="js/caigou.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>