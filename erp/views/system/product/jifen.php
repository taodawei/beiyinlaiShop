<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"title"=>array("title"=>"标题","rowCode"=>"{field:'title',title:'标题',width:300}"),
				"jifen"=>array("title"=>"所需积分","rowCode"=>"{field:'jifen',title:'所需积分',width:300}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:300}"),
				"dtTime"=>array("title"=>"发布时间","rowCode"=>"{field:'dtTime',title:'发布时间',width:200,sort:true}")
			);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['gonggaoPageNum'])?10:$_COOKIE['gonggaoPageNum'];
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
	<link href="styles/kehu_fankui.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_131.png"/> 积分商品兑换列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1" style="display: none;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入通知标题关键词"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=product&a=create_jifen" class="splist_add">新 增</a>') ?>
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
					    <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=create_jifen" onclick="view()"><img src="images/biao_136.png"> 修改</a></li>') ?>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=product&a=del_jifen" onclick="z_confirm(\'确定要删除吗？\',del_jifen,\'\')"><img src="images/biao_32.png"> 删除</a></li>') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="keyword" value="<?=$keyword?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form','upload'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,upload = layui.upload
		  ,load = layer.load()
		  laydate.render({
		  	elem: '#riqi1'
		  	,show: true
		  	,position: 'static'
		  	,min: '2017-12-1'
  			,max: '<?=date("Y-m-d")?>'
  			<?=empty($startTime)?'':",value:'$startTime'"?>
  			,btns: []
  			,done: function(value, date, endDate){
  				$("#s_time1").html(value);
  				$("#startTime").val(value);
  			}
		  });
		  laydate.render({
		  	elem: '#riqi2'
		  	,show: true
		  	,position: 'static'
		  	<?=empty($endTime)?'':",value:'$endTime'"?>
		  	,min: '2017-12-1'
  			,max: '<?=date("Y-m-d")?>'
  			,btns: ['confirm']
  			,done: function(value, date, endDate){
  				$("#s_time2").html(value);
  				$("#endTime").val(value);
  			}
		  });
		  $(".laydate-btns-confirm").click(function(){
		  	$("#riqilan").slideUp(200);
		  	reloadTable(0);
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=product&a=get_jifen_list'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	keyword:'<?=$keyword?>',
		    	startTime:'<?=$startTime?>',
		    	endTime:'<?=$endTime?>'
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			    $("th[data-field='id']").hide();
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var keyword = $("#keyword").val();
		  	var startTime = $("#startTime").val();
		  	var endTime = $("#endTime").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,channelId:channelId
			      ,keyword:keyword
			      ,startTime:startTime
			      ,endTime:endTime
			    },page: {
					curr: 1
				},done:function(){
					$("th[data-field='id']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		});
	</script>
	<script type="text/javascript" src="js/pdts/jifen_list.js"></script>
	<? require('views/help.html');?>
</body>
</html>