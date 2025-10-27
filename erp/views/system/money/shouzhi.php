<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
	"orderId"=>array("title"=>"支付流水号","rowCode"=>"{field:'orderId',title:'支付流水号',width:220}"),
	"dinghuoOrderId"=>array("title"=>"订单号","rowCode"=>"{field:'dinghuoOrderId',title:'订单号',width:200,sort:true}"),
	"dtTime"=>array("title"=>"时间","rowCode"=>"{field:'dtTime',title:'时间',width:150,sort:true}"),
	"kehuName"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'kehuName',title:'".$kehu_title."名称',width:200,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"pay_type"=>array("title"=>"支付方式","rowCode"=>"{field:'pay_type',title:'支付方式',width:150}"),
	"money"=>array("title"=>"金额","rowCode"=>"{field:'money',title:'金额',width:150}"),
	"account"=>array("title"=>"收款账户","rowCode"=>"{field:'account',title:'收款账户',width:250}"),
	"remark"=>array("title"=>"摘要","rowCode"=>"{field:'remark',title:'摘要',width:150}"),
	"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:150,sort:true}")
);
$showRowsArry = array("orderId"=>1,"dinghuoOrderId"=>1,"dtTime"=>1,"kehuName"=>1,"pay_type"=>1,"money"=>1,"account"=>1,"remark"=>1,"status"=>1);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'detail', title: 'detail', width:0,style:\"display:none;\"}";
foreach ($showRowsArry as $row=>$isshow){
	if($isshow==1){
		$rowsJS.=','.$allRows[$row]['rowCode'];
	}
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$keyword = $request['keyword'];
$remark = $request['remark'];
$account = $request['account'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$level = (int)$request['level'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['shouzhiPageNum'])?10:$_COOKIE['shouzhiPageNum'];
$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$banks = $db->get_results("select * from demo_kehu_bank where comId=$comId and status=1 ");
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
	<link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="account"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="chuku_fahuo"] div{line-height:20px;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_105.png"/> 收支明细
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="gaojisousuo_right" style="height:35px;margin-top:15px;">
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
								</div>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/订单号/流水号"/>
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
														关键字
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_keyword" value="<?=$keyword?>" class="gaojisousuo_right_input" placeholder="请输入<?=$kehu_title?>名称/订单号/流水号"/>
													</div>
													<div class="gaojisousuo_left">
														摘要
													</div>
													<div class="gaojisousuo_right" style="height:35px;">
														<select name="super_remark" id="super_remark" lay-search>
															<option value="">全部摘要</option>
															<option value="现金充值">现金充值</option>
															<option value="销售返点">销售返点</option>
															<option value="退款">退款</option>
															<option value="其他充值">其他充值</option>
															<option value="订单付款">订单付款</option>
															<option value="其他付款">其他付款</option>
															<option value="红冲">红冲</option>
															<option value="订单退款">订单退款</option>
															<option value="收款单充值">收款单充值</option>
															<option value="付款单扣款">付款单扣款</option>
															<option value="退货单退款">退货单退款</option>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														收款账户
													</div>
													<div class="gaojisousuo_right">
														<select name="super_account" id="super_account" lay-search>
															<option value="">选择账户</option>
															<? if(!empty($banks)){
																foreach ($banks as $b){?>
																	<option value="<?=$b->bank_account?>"><?=$b->bank_name?>&nbsp;<?=$b->bank_account?></option>	
																<?}
															}?>
														</select>
													</div>
													<div class="gaojisousuo_left">
														<?=$kehu_title?>级别
													</div>
													<div class="gaojisousuo_right">
														<select name="super_level" id="super_level" lay-search>
															<option value="">选择级别</option>
															<? if(!empty($levels)){
																foreach ($levels as $l) {
																	?>
																	<option value="<?=$l->id?>"><?=$l->title?></option>
																	<?
																}
															}?>
														</select>
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
						<div class="splist_up_01_right_3">
							<a href="?m=system&s=money&a=daochuShouzhi" id="daochuA" target="_blank()" onclick="daochu();" class="splist_add">导 出</a>
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
				<div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
					<ul>
						<li id="sheheBtn">
							<a href="javascript:view();"><img src="images/biao_108.png"> 详情</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="shoukuanqueren_xiangqing_tc" id="shoukuanqueren_xiangqing_tc" style="display:none;">
		<div class="bjkh_bj"></div>
		<div class="skqr_xx">
			<div class="bjkh_jebangsjxx_1">
				财务详情
			</div>
			<div class="bjkh_jebangsjxx_2">
				<div class="skqr_xx_01">
					<ul id="detail_ul">
						
					</ul>
				</div>
				<div class="skqr_xx_02">
					流水号：<span id="show_orderId"></span>　　　　　　日期：<span id="show_dtTime"></span><br>
					操作人：<span id="show_userName"></span>　　　　　　 审核人：<span id="show_shenheUser"></span>
				</div>
			</div>
			<div class="bjkh_jebangsjxx_3">
				<a href="javascript:hideInfo();" class="bjkh_jebangsjxx_3_01">确定</a>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="remark" value="<?=$remark?>">
	<input type="hidden" id="account" value="<?=$account?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="kehuName" value="<?=$kehuName?>">
	<input type="hidden" id="level" value="<?=$level?>">
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
		    ,url: '?m=system&s=money&a=getShouzhis'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	remark:'<?=$remark?>',
		    	account:'<?=$account?>',
		    	startTime:'<?=$startTime?>',
		    	keyword:'<?=$keyword?>',
		    	endTime:'<?=$endTime?>',
		    	level:<?=$level?>
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='detail']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var remark = $("#remark").val();
		  	var account = $("#account").val();
		  	var startTime = $("#startTime").val();
		  	var keyword = $("#keyword").val();
		  	var endTime = $("#endTime").val();
		  	var level = $("#level").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
				    order1: obj.field
				    ,order2: obj.type,
				    remark:remark,
			    	account:account,
			    	startTime:startTime,
			    	keyword:keyword,
			    	endTime:endTime,
			    	level:level
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
					$("th[data-field='detail']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		  form.on('submit(search)', function(data){
		  	$("#remark").val(data.field.super_remark);
		  	$("#account").val(data.field.super_account);
		  	$("#level").val(data.field.super_level);
		  	$("#keyword").val(data.field.super_keyword);
		  	hideSearch();
		  	reloadTable(0);
		  	return false;
		  });
		  form.on('submit(quxiao)', function(){
		  	hideSearch();
		  	return false;
		  });
		});
	</script>
	<script type="text/javascript" src="js/money_shouzhi.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>