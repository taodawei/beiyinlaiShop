<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"time"=>array("title"=>"时间段","rowCode"=>"{field:'time',title:'时间段',width:200}"),
	"z_num"=>array("title"=>"订单数量","rowCode"=>"{field:'z_num',title:'订单数量',width:250}"),
	"z_price"=>array("title"=>"订单总金额","rowCode"=>"{field:'z_price',title:'订单总金额',width:250}")
);
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
//$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$startTime = date("Y-m-01");
$endTime = date("Y-m-d");
$time = $request['time'];
$order1 = empty($request['order1'])?'z_num':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 20;
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$step = 1;
if($product_set->number_num>0){
	$chushu = pow(10,$product_set->number_num);
	$step = 1/$chushu;
}
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
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> <?=$request['time']?>时间段订单统计
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
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<!-- <input type="hidden" id="cuxiao" value="<?=$cuxiao?>"> -->
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var step = <?=$step?>; 
		var productListTalbe;
		var productListForm;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListForm = form;
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
		  	let startTime = $("#startTime").val();
		  	let endTime = $("#endTime").val();
		  	let days = getDaysBetween(startTime,endTime);
		  	if(days>31){
		  		layer.msg('统计周期不能大于一个月', function(){});
		  		return false;
		  	}else{
		  		reloadTable(0);
		  	}
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=tongji&a=get_day_tongji'
		    ,page: false
		    ,limit:40
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	time:'<?=$time?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		  		$("th[data-field='storeId']").hide();
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		});
	function  getDaysBetween(dateString1,dateString2){
	   var  startDate = Date.parse(dateString1);
	   var  endDate = Date.parse(dateString2);
	   var days=(endDate - startDate)/(1*24*60*60*1000);
	   // alert(days);
	   return  days;
	}
	</script>
	<script type="text/javascript" src="js/tongji/index.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>