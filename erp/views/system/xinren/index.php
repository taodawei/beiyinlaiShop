<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:200}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:180}"),
				"sn"=>array("title"=>"商品编号","rowCode"=>"{field:'sn',title:'商品编号',width:180}"),
				"money"=>array("title"=>"专享价格","rowCode"=>"{field:'money',title:'专享价格',width:180}")
			);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{field: 'status', title: 'status', width:0,style:\"display:none;\"},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 20;
//$menus = $db->get_results("select id,title from demo_kecheng_channel where comId=$comId order by id");
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
		<img src="images/biao_131.png"/> 新人专享商品管理（前台链接地址： /index.php?p=10）
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div style="color:red;padding:20px;padding-bottom: 0px;">规则：1:只有没有下过订单的用户可买；2:每个用户只能买其中一个商品</div>
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectFenlei(0,'全部状态');" class="splist_up_01_left_02_down_on">全部状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectFenlei(-1,'已禁用');">已禁用</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectFenlei(1,'已启用');">已启用</a>
									</li>
								</ul>
							</div>
						</div>
						<!-- <div class="splist_up_01_left_02" style="margin-left:10px;">
							<div class="splist_up_01_left_02_up">
								<span>全部类型</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectLevel(0,'全部类型');" class="splist_up_01_left_02_down_on">全部类型</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectLevel(1,'视频新人专享');">视频新人专享</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectLevel(2,'文字新人专享');">文字新人专享</a>
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
                        </div>-->
						<div class="clearBoth"></div>
					</div> 
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
							<a href="?m=system&s=xinren&a=create" class="splist_add">新 增</a>
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
							<a href="javascript:;" onclick="view()"><img src="images/biao_136.png"> 修改</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要删除吗？',del_gonggao,'');"><img src="images/biao_32.png"> 删除</a>
						</li>
						<li id="operate_jinyong">
							<a href="javascript:z_confirm('确定要禁用吗？',jin_user,'');"><img src="images/biao_120.png"> 禁用</a>
						</li>
						<li id="operate_qiyong">
							<a href="javascript:z_confirm('确定要启用吗？',qiyong_user,'');"><img src="images/biao_888.png"> 启用</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="type" value="0">
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
		    ,height: "full-160"
		    ,url: '?m=system&s=xinren&a=getList'
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
			    $("th[data-field='status']").hide();
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var type = $("#type").val();
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
			      ,type:type
			      ,keyword:keyword
			      ,startTime:startTime
			      ,endTime:endTime
			    },page: {
					curr: 1
				},done:function(){
					$("th[data-field='id']").hide();
					$("th[data-field='status']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		});
	</script>
	<script type="text/javascript" src="js/xinren/index.js"></script>
	<? require('views/help.html');?>
</body>
</html>