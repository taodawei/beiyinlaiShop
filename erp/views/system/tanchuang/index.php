<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"image"=>array("title"=>"图片","rowCode"=>"{field:'image',title:'图片',width:150}"),
	"url"=>array("title"=>"链接","rowCode"=>"{field:'url',title:'链接',width:150}"),
	"time"=>array("title"=>"时间","rowCode"=>"{field:'time',title:'时间',width:250}"),
);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$scene = (int)$request['scene'];
$status = (int)$request['status'];
$keyword = $request['keyword'];
$pdtName = $request['pdtName'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$mendianIds = $request['mendianIds'];
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
		.layui-table-body tr{height:80px}
		.layui-table-cell{height:50px;}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="time"] div,td[data-field="content"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		.layui-layer-tips .layui-layer-content{width:250px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="?s=yyyx"><img src="images/users_39.png"/></a> 首页弹窗
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02" style="margin-left:20px;">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus('0','全部');" class="splist_up_01_left_02_down_on">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('1','未开始');">未开始</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('2','活动中');">活动中</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('3','已过期');">已过期</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('-1','已作废');">已作废</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="yx_cuxiaoshuoming">
                        	首页弹窗说明 <img onmouseover="tips(this,'1、同一时间点只能有一条规则生效；<br>2、目前只有小程序端支持',3);" onmouseout="hideTips();" src="images/yingxiao_21.png" alt="">
                            <div class="yx_cuxiaoshuoming_tanchu" style="display: none;">
                            	
                        	</div>
                        </div>
						<div class="splist_up_01_right_3">
							<!-- <a href="?m=system&s=product&a=daoru" class="splist_daoru">导 入</a> -->
							<a href="?s=tanchuang&a=create" class="splist_add">新 增</a>
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
						<a href="javascript:" onclick="piliang_zuofei();"><img src="images/yingxiao_34.png"> 批量删除</a>
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
						<li id="zuofeibtn">
							<a href="javascript:z_confirm('确定要删除吗？',zuofei,'');"><img src="images/yingxiao_23.png"> 删除</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="scene" value="<?=$scene?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="pdtName" value="<?=$pdtName?>">
	<input type="hidden" id="mendianIds" value="<?=$mendianIds?>">
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
		  //,load = layer.load()
		  /*laydate.render({
		  	elem: '#riqi1'
		  	,show: true
		  	,position: 'static'
		  	,min: '2018-01-01'
  			,max: '<?=date("Y-m-d")?>'
  			<?=empty($startTime)?'':",value:'$startTime'"?>
  			,btns: []
  			,done: function(value, date, endDate){
  				$("#s_time1").html(value);
  				$("#super_startTime").val(value);
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
  				$("#super_endTime").val(value);
  			}
		  });
		  $(".laydate-btns-confirm").click(function(){
		  	$("#riqilan").slideUp(200);
		  });*/
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=tanchuang&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:'<?=$status?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		});
	</script>
	<script type="text/javascript" src="js/yyyx/tanchuang.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>