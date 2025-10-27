<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"image"=>array("title"=>"商品图片","rowCode"=>"{field:'image',title:'商品图片',width:87,unresize:true}"),
	"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true}"),
	"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"fukuan_time"=>array("title"=>"付款时间","rowCode"=>"{field:'fukuan_time',title:'付款时间',width:400}"),
	"fahuoTime"=>array("title"=>"开始发货时间","rowCode"=>"{field:'fahuoTime',title:'开始发货时间',width:180}"),
	"price"=>array("title"=>"预售价格","rowCode"=>"{field:'price',title:'预售价格',width:180}"),
	"dingjin_weikuan"=>array("title"=>"定金/尾款","rowCode"=>"{field:'dingjin_weikuan',title:'定金/尾款',width:150}"),
	"nums"=>array("title"=>"预售/已售","rowCode"=>"{field:'nums',title:'预售/已售',width:150}"),
	"orders"=>array("title"=>"人数/订单数","rowCode"=>"{field:'orders',title:'人数/订单数',width:150}"),
	"money"=>array("title"=>"销售额","rowCode"=>"{field:'money',title:'销售额',width:100}"),
	"status_info"=>array("title"=>"当前状态","rowCode"=>"{field:'status_info',title:'当前状态',width:100}"),
	"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:180}")
);
$rowsJS = "{type:'checkbox'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{width:49,title:'',align:'center', toolbar: '#barDemo'}";
$type = (int)$request['type'];
$status = (int)$request['status'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
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
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:73px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="dingjin_weikuan"] div,td[data-field="price"] div,td[data-field="fukuan_time"] div,td[data-field="time"] div,td[data-field="content"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.layui-layer-tips .layui-layer-content{width:250px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="?s=yyyx"><img src="images/users_39.png"/></a> 预售管理 (前台链接地址：/index.php?p=4&a=yushou)
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>预售类型</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectScene('0','全部');">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectScene('1','一口价');">一口价</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectScene('2','阶梯价');">阶梯价</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="margin-left:20px;">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus('0','全部状态');">全部状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('1','未开始');">未开始</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('2','进行中');">进行中</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('3','已结束');">已结束</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('-1','已作废');">已作废</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="sprukulist_01">
							<div class="sprukulist_01_left">
								<span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
							</div>
							<div class="sprukulist_01_right">
								<img src="images/biao_76.png"/>
							</div>
							<div class="clearBoth"></div>
							<div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;">
								<div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="yx_cuxiaoshuoming">
                        	预售规则说明 <img onmouseover="tips(this,'1、每个预售只能选择单规格商品或多规格中的某一个规格；<br>2、每个商品同一时间点内只能创建一个预售活动；<br>',3);" onmouseout="hideTips();" src="images/yingxiao_21.png" alt="">
                        </div>
						<div class="splist_up_01_right_3">
							<!-- <a href="?m=system&s=product&a=daoru" class="splist_daoru">导 入</a> -->
							<a href="?s=yyyx&a=create_yushou" class="splist_add">新 增</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
				<div class="splist_up_02">
					<div class="splist_up_02_1">
						<img src="images/biao_25.png"/>
					</div>
					<div class="splist_up_02_2">
						已选择 <span id="selectedNum">0</span> 项
					</div>
					<div class="splist_up_02_3">
						<a href="javascript:" onclick="piliang_zuofei();"><img src="images/yingxiao_34.png"> 批量作废</a>
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
				<div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
					<ul>
						<li>
							<a href="javascript:view_jilu();"><img src="images/yingxiao_22.png"> 查看订单</a>
						</li>
						<li>
							<a href="javascript:view_fahuo();"><img src="images/yingxiao_22.png"> 发货管理</a>
						</li>
						<li id="zuofeibtn">
							<a href="javascript:z_confirm('确定要作废该预售吗？',zuofei,'');"><img src="images/yingxiao_23.png"> 作废</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="type" value="<?=$type?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var nowpage = '?s=yyyx&a=yushou';
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  //,load = layer.load()
		  laydate.render({
		  	elem: '#riqi1'
		  	,show: true
		  	,position: 'static'
		  	,min: '2018-01-01'
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
		  	,min: '2018-01-01'
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
		    ,url: '?s=yyyx&a=getYushouList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:'<?=$status?>',
		    	type:'<?=$type?>',
		    	startTime:'<?=$startTime?>',
		    	keyword:'<?=$keyword?>',
		    	endTime:'<?=$endTime?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  table.on('checkbox(product_list)', function(obj){
			var checkStatus = table.checkStatus('product_list')
		    ,data = checkStatus.data;
		    if(data.length>0){
		    	var ids = '';
		    	for (var i = 0; i < data.length; i++) {
		    		if(i==0){
		    			ids = data[i].id;
		    		}else{
		    			ids = ids+','+data[i].id;
		    		}
		    	}
		    	$("#selectedIds").val(ids);
		    	$(".splist_up_01").hide();
		    	$(".splist_up_02").show().find(".splist_up_02_2 span").html(data.length);
		    }else{
		    	$(".splist_up_02").hide();
		    	$(".splist_up_01").show();
		    }
		  });
		});
	</script>
	<script type="text/javascript" src="js/yyyx/yushou.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>