<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}"),
	"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
	"pdtNums"=>array("title"=>"订购数量","rowCode"=>"{field:'pdtNums',title:'订购数量',width:100,sort:true}"),
	"price"=>array("title"=>"订单总额","rowCode"=>"{field:'price',title:'订单总额',width:100,sort:true}"),
	"status_info"=>array("title"=>"订单状态","rowCode"=>"{field:'status_info',title:'订单状态',width:120}"),
	"fapiao"=>array("title"=>"是否开票","rowCode"=>"{field:'fapiao',title:'是否开票',width:90}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
	"shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}"),
	"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$id = (int)$request['id'];
$yushou = $db->get_row("select * from yushou where id=$id and comId=$comId");
if(empty($yushou))die("预售不存在，请重新操作");
$pdtInfo = json_decode($yushou->pdtInfo);
$scene = (int)$request['scene'];
$status = $request['status'];
$keyword = $request['keyword'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['orderPageNum'])?10:$_COOKIE['orderPageNum'];
$zongNum = $db->get_var("select count(*) from order$fenbiao where yushouId=$id and status!=-1");
$numArry = array();
$num_sql = "select status,count(*) as num from order$fenbiao where yushouId=$id and comId=$comId group by status";
$nums = $db->get_results($num_sql);
if(!empty($nums)){
	foreach ($nums as $n){
		$numArry[$n->status] = $n->num;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/clipboard.min.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="beizhu"] div,td[data-field="address"]{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		.layui-anim.layui-icon{font-size:20px;}
		.layui-form-radio{margin-top:0px;line-height:22px;margin-right:0px;}
		.layui-form-radio i{margin-right:3px;}
		.layui-form-radio span{font-size:12px;}
		.layui-form-select .layui-input{height:25px;}
		.ddxx_jibenxinxi_2_01_down_right .layui-form-select{margin-bottom:2px;}
		.layui-form-selected dl{top:25px;min-height:200px;}
	</style>
</head>
<body>
<? require('views/system/order/header.php')?>
<div id="content">
	<div class="right_up">
		<img src="images/biao_109.png"/> <?=$pdtInfo->title?>
		<!-- <div class="bangzhulist_up_right" onclick="showHelp(321);">帮助</div> -->
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up" style="height:118px;">
				<div class="splist_up_addtab">
	            	<ul>
	            		<li>
	                    	<a href="?s=order&a=yushou_order&id=<?=$id?>" <? if(empty($scene)){?>class="splist_up_addtab_on"<? }?>>全部(<?=$zongNum?>)</a>
	                    </li>
	                    <? if($yushou->paytype==2){?>
		                    <li>
		                    	<a href="?s=order&a=yushou_order&id=<?=$id?>&scene=1" <? if($scene==1){?>class="splist_up_addtab_on"<? }?>>待付尾款(<?=(int)$db->get_var("select count(*) from order$fenbiao where yushouId=$id and ispay=0 and status!=-1");?>)</a>
		                    </li>
		                <? }?>
	                    <li>
	                    	<a href="?s=order&a=yushou_order&id=<?=$id?>&scene=2" <? if($scene==2){?>class="splist_up_addtab_on"<? }?>>待发货(<?=(int)$numArry[2];?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?s=order&a=yushou_order&id=<?=$id?>&scene=3" <? if($scene==3){?>class="splist_up_addtab_on"<? }?>>待收货(<?=(int)$numArry[3];?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?s=order&a=yushou_order&id=<?=$id?>&scene=4" <? if($scene==4){?>class="splist_up_addtab_on"<? }?>>已完成(<?=(int)$numArry[4];?>)</a>
	                    </li>
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_01">
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入收货人名称/手机号/订单号"/>
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
														订单号
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_orderId" value="<?=$orderId?>" class="gaojisousuo_right_input" placeholder="请输入订货单号"/>
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
													<div class="gaojisousuo_left">
														收货信息
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_kehuName" value="<?=$kehuName?>" class="gaojisousuo_right_input" placeholder="请输入收货人名称/手机号"/>
													</div>
													<div class="gaojisousuo_left">
														是否开票
													</div>
													<div class="gaojisousuo_right" style="padding-top: 8px;">
														<input type="radio" name="super_kaipiao" value="0" title="全部" checked/><input type="radio" name="super_kaipiao" value="1" title="开票"/><input type="radio" name="super_kaipiao" value="2" title="不开票"/>
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
					<div class="dangqiandd_2_down_3">
						<a href="javascript:" onclick="pi_yichang();"><img src="images/dangqiandingdan_11.png"> 批量异常</a>
						<a href="javascript:" onclick="pi_beizhu();"><img src="images/dangqiandingdan_12.png"> 批量设置备注</a>
						<a href="javascript:" onclick="pi_fapiao(0);"><img src="images/dangqiandingdan_14.png"> 设置为不开票</a>
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
							<a href="javascript:" onclick="order_show();"><img src="images/shangchengdd_21.png">查看订单</a>
						</li>
						<li id="errorBtn">
							<a href="javascript:" onclick="order_show();qiehuan('orderInfo',2,'dqddxiangqing_up_on');order_error_index(0);" class="yuandian_tc_yichangcaozuo"><img src="images/shangchengdd_23.png">订单异常操作</a>
						</li>
						<li id="tuihuanBtn">
							<a href="javascript:" onclick="order_show();qiehuan('orderInfo',3,'dqddxiangqing_up_on');order_tuihuan_index(0);" class="yuandian_tc_tuihuanhuo"><img src="images/shangchengdd_24.png">退换货申请</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;">
		<div class="dqddxiangqing_up" id="orderInfoMenu">
			<ul>
				<li>
					<a href="javascript:" id="orderInfoMenu1" onclick="qiehuan('orderInfo',1,'dqddxiangqing_up_on');" class="dqddxiangqing_up_on">基本信息</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu2" onclick="qiehuan('orderInfo',2,'dqddxiangqing_up_on');order_error_index(0);">异常处理</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu3" onclick="qiehuan('orderInfo',3,'dqddxiangqing_up_on');order_tuihuan_index(0);">退换货管理</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu4" onclick="qiehuan('orderInfo',4,'dqddxiangqing_up_on');order_service_index(0);">订单服务</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu5" onclick="qiehuan('orderInfo',5,'dqddxiangqing_up_on');order_jilu_index(0);">操作记录</a>
				</li>
				<div class="clearBoth"></div>
			</ul>
		</div>
		<div class="dqddxiangqing_down">
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont1">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont2" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont3" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont4" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont5" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
		</div>
	</div>
</div>
<!--批量服务分配-->
<div class="ddfw_piliangfenpei_tc" id="ddfw_piliangfenpei_tc" data-type="0" data-id="0" style="display:none;">
	<div class="bj"></div>
    <div class="ddfw_adddingdangfuwu">
    	<div class="dqpiliangshenhe_01">
        	<div class="dqpiliangshenhe_01_left">
            	服务分配
            </div>
        	<div class="dqpiliangshenhe_01_right" onclick="$('#ddfw_piliangfenpei_tc').hide();">
            	<img src="images/close_1.png" alt=""/>
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="ddfw_piliangfenpei1">
        	<ul>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        <span>*</span> 服务人员：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="fanwei_1" readonly="true" onclick="fanwei(1);" placeholder="选择服务人员" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        联系电话：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_phone" placeholder="请输入服务人员电话" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        预约服务时间：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_time" placeholder="请选择预约服务时间" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
        	</ul>
        </div>
    	<div class="dqpiliangshenhe_03">
        	<a href="javascript:" onclick="service_fenpei();">立即分配</a>
        </div>
        <input type="hidden" id="editId" value="0">
        <input type="hidden" id="users" value="0">
        <input type="hidden" id="userNames" value="">
    </div>
</div>
<!--批量服务分配结束-->
<div id="myModal" class="reveal-modal"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>

<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="scene" value="<?=$scene?>">
<input type="hidden" id="orderId" value="<?=$orderId?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="shouhuoInfo" value="<?=$shouhuoInfo?>">
<input type="hidden" id="kaipiao" value="<?=$kaipiao?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
	var productListTalbe,lay_date;
	layui.use(['laydate', 'laypage','table','form'], function(){
	  var laydate = layui.laydate
	  ,laypage = layui.laypage
	  ,table = layui.table
	  ,form = layui.form;
	  lay_date = laydate;
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
	  laydate.render({
	  	elem: '#service_time'
	  	,min: '<?=date("Y-m-d",strtotime("-1 days"))?>'
	  	,type: 'datetime'
	  	,format:'yyyy-MM-dd HH:mm'
	  });
	  $(".laydate-btns-confirm").click(function(){
	  	$("#riqilan").slideUp(200);
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-200"
	    ,url: '?s=order&a=getYushouOrders&id=<?=$id?>'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	scene:'<?=$scene?>',
	    	orderId:'<?=$orderId?>',
	    	startTime:'<?=$startTime?>',
	    	keyword:'<?=$keyword?>',
	    	endTime:'<?=$endTime?>',
	    	shouhuoInfo:'<?=$shouhuoInfo?>',
	    	kaipiao:'<?=$kaipiao?>'
	    },done: function(res, curr, count){
	    	layer.closeAll('loading');
		    $("#page").val(curr);
		    $("th[data-field='id']").hide();
		    $("th[data-field='status']").hide();
		  }
	  });
	  table.on('sort(product_list)', function(obj){
	  	var scene = $("#scene").val();
	  	var orderId = $("#orderId").val();
	  	var startTime = $("#startTime").val();
	  	var keyword = $("#keyword").val();
	  	var endTime = $("#endTime").val();
	  	var shouhuoInfo = $("#shouhuoInfo").val();
	  	var kaipiao = $("#kaipiao").val();
	  	$("#order1").val(obj.field);
	  	$("#order2").val(obj.type);
	  	var scrollLeft = $(".layui-table-body").scrollLeft();
	  	layer.load();
		table.reload('product_list', {
		    initSort: obj
		    ,height: "full-200"
		    ,where: {
		      order1: obj.field
		      ,order2: obj.type
		      ,scene:scene
		      ,orderId:orderId
		      ,startTime:startTime
		      ,keyword:keyword
		      ,endTime:endTime
		      ,shouhuoInfo:shouhuoInfo
		      ,kaipiao:kaipiao
		    },page: {
				curr: 1
			},done:function(){
				$(".layui-table-header").scrollLeft(scrollLeft);
				$(".layui-table-body").scrollLeft(scrollLeft);
				$("th[data-field='id']").hide();
				$("th[data-field='status']").hide();
				layer.closeAll('loading');
			}
		  });
	  });
	  form.on('checkbox(status)', function(data){
	  	if(data.elem.checked){
	  		$("input[pid='status']").prop("checked",false);
	  	}
	  	form.render('checkbox');
	  });
	  form.on('checkbox(nostatus)', function(data){
	  	$("input[name='super_status_all']").prop("checked",false);
	  	form.render('checkbox');
	  });
	  form.on('submit(search)', function(data){
	  	$("#orderId").val(data.field.super_orderId);
	  	$("#startTime").val(data.field.super_startTime);
	  	$("#endTime").val(data.field.super_endTime);
	  	$("#shouhuoInfo").val(data.field.super_shouhuoInfo);
	  	$("#kaipiao").val(data.field.super_kaipiao);
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
<script type="text/javascript" src="js/order/yushou_order.js"></script>
<script type="text/javascript" src="js/order/order_info.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>