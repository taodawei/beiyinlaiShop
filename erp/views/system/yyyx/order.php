<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"title"=>array("title"=>"促销主题","rowCode"=>"{field:'title',title:'促销主题',width:240}"),
	"scene"=>array("title"=>"应用场景","rowCode"=>"{field:'scene',title:'应用场景',width:150}"),
	"time"=>array("title"=>"时间","rowCode"=>"{field:'time',title:'时间',width:250}"),
	"mendian"=>array("title"=>"门店","rowCode"=>"{field:'mendian',title:'门店',width:200}"),
	"content"=>array("title"=>"促销内容","rowCode"=>"{field:'content',title:'促销内容',width:250}"),
	"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:180}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";
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
$mendians = $db->get_results("select id,title from mendian where comId=$comId");
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
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="time"] div,td[data-field="content"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		.layui-layer-tips .layui-layer-content{width:250px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="?s=yyyx"><img src="images/users_39.png"/></a> 订单促销
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>应用场景</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectScene('0','全部');" class="splist_up_01_left_02_down_on">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectScene('1','线上商城');">线上商城</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectScene('2','订货平台');">订货平台</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectScene('3','线下门店');">线下门店</a>
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
										<a href="javascript:" onclick="selectStatus('0','全部');" class="splist_up_01_left_02_down_on">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('1','未开始');">未开始</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('2','促销中');">促销中</a>
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
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入促销主题"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_2">
							<div class="splist_up_01_right_2_up">
								高级搜索
							</div>
							<div class="splist_up_01_right_2_down">
								<div class="splist_up_01_right_2_down1">
									<div class="splist_up_01_right_2_down1_01">
										高级搜索
									</div>
									<div class="splist_up_01_right_2_down1_02">
										<form id="searchForm" class="layui-form">
											<ul>
												<li>
													<div class="gaojisousuo_left">
														促销门店
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_mendian_all" lay-skin="primary" lay-filter="mendian" title="全选" checked />
														<? if(!empty($mendians)){
															foreach ($mendians as $m){
																?>
																<input type="checkbox" name="super_mendian" pid="mendian" lay-skin="primary" lay-filter="nomendian" value="<?=$m->id?>" title="<?=$m->title?>" />
																<?
															}
														}?>
														
													</div>
													<div class="gaojisousuo_left">
														下单时间
													</div>
													<div class="gaojisousuo_right" style="height:35px;">
														<div class="sprukulist_01" style="top:0px;margin-left:0px;z-index:999;">
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
															<input type="hidden" name="super_startTime" id="super_startTime" value="<?=$startTime?>">
															<input type="hidden" name="super_endTime" id="super_endTime" value="<?=$endTime?>">
														</div>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_tijiao">
														<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search" > 确 定 </button>
														<button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
														<button type="reset" class="layui-btn layui-btn-primary"> 重 置 </button>
													</div>
												</li>
											</ul>
										</form>
									</div>                                    
								</div>
							</div>
						</div>
						<div class="yx_cuxiaoshuoming">
                        	促销规则说明 <img onmouseover="tips(this,'1、商品促销、订单促销的优惠政策同时生效；<br>2、同一时间点，多个促销不能有重复商品；<br>3、最多支持10条组合促销同时生效。<br>',3);" onmouseout="hideTips();" src="images/yingxiao_21.png" alt="">
                            <div class="yx_cuxiaoshuoming_tanchu" style="display: none;">
                            	
                        	</div>
                        </div>
						<div class="splist_up_01_right_3">
							<!-- <a href="?m=system&s=product&a=daoru" class="splist_daoru">导 入</a> -->
							<a href="?s=yyyx&a=create_order" class="splist_add">新 增</a>
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
							<a href="javascript:view();"><img src="images/yingxiao_22.png"> 查看</a>
						</li>
						<li id="zuofeibtn">
							<a href="javascript:z_confirm('确定要作废该促销吗？',zuofei,'');"><img src="images/yingxiao_23.png"> 作废</a>
						</li>
						<li>
							<a href="javascript:copy();"><img src="images/yingxiao_24.png"> 复制</a>
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
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=yyyx&a=getOrderList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:'<?=$status?>',
		    	scene:'<?=$scene?>',
		    	startTime:'<?=$startTime?>',
		    	keyword:'<?=$keyword?>',
		    	endTime:'<?=$endTime?>',
		    	pdtName:'<?=$pdtName?>',
		    	mendianIds:'<?=$mendianIds?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  form.on('checkbox(mendian)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='mendian']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(nomendian)', function(data){
		  	$("input[name='super_mendian_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('submit(search)', function(data){
		  	$("#pdtName").val(data.field.super_pdtName);
		  	$("#startTime").val(data.field.super_startTime);
		  	$("#endTime").val(data.field.super_endTime);
		  	if(data.field.super_mendian_all=="on"){
		  		$("#mendianIds").val('0');
		  	}else{
		  		var cangkustr = '';
		  		$("input:checkbox[name='super_mendian']:checked").each(function(){
		  			cangkustr = cangkustr+','+$(this).val();
		  		});
		  		if(cangkustr.length>0){
		  			cangkustr = cangkustr.substring(1);
		  		}
		  		$("#mendianIds").val(cangkustr);
		  	}
		  	hideSearch();
		  	reloadTable(0);
		  	return false;
		  });
		  form.on('submit(quxiao)', function(){
		  	hideSearch();
		  	return false;
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
	<script type="text/javascript" src="js/yyyx/order.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>