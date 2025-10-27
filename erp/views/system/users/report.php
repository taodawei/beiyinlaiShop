<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
// 	"id"=>array("title"=>"反馈ID","rowCode"=>"{field:'id',title:'反馈ID',width:80}"),
	"name"=>array("title"=>"姓名","rowCode"=>"{field:'name',title:'姓名',width:100}"),
	"phone"=>array("title"=>"手机号","rowCode"=>"{field:'phone',title:'手机号',width:100}"),
	"content"=>array("title"=>"内容","rowCode"=>"{field:'content',title:'内容',width:580}"),
	"feed_type"=>array("title"=>"类型","rowCode"=>"{field:'feed_type',title:'类型',width:150}"),
	"dtTime"=>array("title"=>"提交时间","rowCode"=>"{field:'dtTime',title:'提交时间',width:150}")
);

$rowsJS = "{field: 'id', title: 'id', width:20, sort: false,style:\"display:none;\"}";
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
$channelStr = $db->get_var("select group_concat(distinct(feed_type)) from feedback_log where feed_type <> '' ");

$channels = explode(',', $channelStr);
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
		<img src="images/biao_131.png"/> 反馈信息列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部类别</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectLevel1(0,'全部类别');" class="splist_up_01_left_02_down_on">全部类别</a>
									</li>
									<?
									if(!empty($channels)){
										foreach ($channels as $key => $channel) {
											?>
											<li class="allsort_01"><a href="javascript:" onclick="selectLevel1('<?=$channel?>','<?=$channel?>');" class="allsort_01_tlte"><?=$channel?></a></li>
											<?
										}
									}
									?>
								</ul>
							</div>
						</div>
						<div class="sprukulist_01" style="display:none;">
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
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入内容关键词"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
						    <? //chekurl($arr,'<a href="?m=system&s=banner&a=addGonggao" class="splist_add">新 增</a>') ?>
							<? //chekurl($arr,'<a href="?m=system&s=banner&a=gonggaoChannel" class="splist_add">分类管理</a>') ?>
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
							<a href="javascript:view1();"><img src="images/biao_30.png"> 详情</a>
						</li>
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
		    ,url: '?m=system&s=users&a=getFeedBackList'
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
		
		function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}

function selectLevel1(status,title){
	$("#channelId").val(status);
	$(".splist_up_01_left_02_up span").html(title);
	reloadTable(0);
}

function view1(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=users&a=report";
	url = encodeURIComponent(url);
	location.href="?m=system&s=users&a=feedbackDetail&id="+pdtId+"&returnurl="+url;
}
	</script>
	<script type="text/javascript" src="js/shezhi/banner_list.js"></script>
	<? require('views/help.html');?>
</body>
</html>