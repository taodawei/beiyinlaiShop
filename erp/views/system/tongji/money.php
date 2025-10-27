<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"shequ"=>array("title"=>"所属门店","rowCode"=>"{field:'shequ',title:'所属社区',width:240}"),
	"type"=>array("title"=>"订单类型","rowCode"=>"{field:'type',title:'订单类型',width:100}"),
	"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
	"price"=>array("title"=>"订单总额","rowCode"=>"{field:'price',title:'订单总额',width:100,sort:true}"),
	"status_info"=>array("title"=>"订单状态","rowCode"=>"{field:'status_info',title:'订单状态',width:120}"),
	"payStatus"=>array("title"=>"付款状态","rowCode"=>"{field:'payStatus',title:'付款状态',width:90}"),
	"fapiao"=>array("title"=>"是否开票","rowCode"=>"{field:'fapiao',title:'是否开票',width:90}"),
	"kaipiao_status"=>array("title"=>"开票状态","rowCode"=>"{field:'kaipiao_status',title:'开票状态',width:90}"),
	"fahuoStatus"=>array("title"=>"发货状态","rowCode"=>"{field:'fahuoStatus',title:'发货状态',width:150}"),
	"username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
	"shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}"),
	"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}")
);
$rowsJS = "";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$rowsJS .=",{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
//0当前订单 1.货到付款订单 2.未支付订单 3.异常订单 4.退换货订单 5.缺货 6预售订单  7完成订单 8无效订单 
$scene = 9;
$status = $request['status'];
$keyword = $request['keyword'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$shouhuoInfo = $request['shouhuoInfo'];
$moneystart = $request['moneystart'];
$moneyend = $request['moneyend'];
$payStatus = $request['payStatus'];
$pdtInfo = $request['pdtInfo'];
$kaipiao = (int)$request['kaipiao'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['orderPageNum'])?10:$_COOKIE['orderPageNum'];
//计算各类型订单的数量
$num_sql = "select status,count(*) as num from order$fenbiao where comId=$comId ";
switch ($scene){
	case 0:
		$title = '当前订单';
		//$num_sql .= " and status in(0,1,2,3)";
	break;
	case 1:
		$title = '货到付款订单';
		$num_sql .= " and type=2";
	break;
	case 2:
		//未支付的
		$title = '未支付订单';
		$last_time = date("Y-m-d H:i:s");
		$num_sql .= " and status=-5 and pay_endtime>'$last_time'";
	break;
	case 3:
		$title = '异常订单';
		$num_sql .= " and status=-2";
	break;
	case 4:
		$title = '退换货订单';
		$num_sql .= " and status=-3";
	break;
	case 5:
		$title = '缺货订单';
		$num_sql .= " and status=-4";
	break;
	case 6:
		$title = '预售订单';
		$num_sql .= " and type=4";
	break;
	case 7:
		$title = '完成订单';
		$num_sql .= " and status=4";
	break;
	case 8:
		$title = '无效订单';
		$last_time = date("Y-m-d H:i:s");
		$num_sql .= " and (status=-1 or (status=-5 and pay_endtime<'$last_time'))";
	break;
}
$zongNum = 0;
$numArry = array();
if(!strstr($num_sql,'status=')){
	$num_sql.=" group by status";
	$nums = $db->get_results($num_sql);
	if(!empty($nums)){
		foreach ($nums as $n){
			$zongNum+=$n->num;
			$numArry[$n->status] = $n->num;
		}
	}
}else{
	$zongNum = $db->get_var(str_replace('status,','',$num_sql));
}
$daikaipiao_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and status=4 and ifkaipiao=1 and kaipiao_status=1");
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
		.hyxx_yuemingxi_up{height:91px}
		.hyxx_yuemingxi_up ul li{float:left;width:24%;margin:0 .5%;height:91px;border-radius:7px;cursor:pointer}
		.hyxx_yuemingxi_up ul li a{display:block}
		.hyxx_yuemingxi_up_left{width:90%;float:left;margin-left:5%;padding-top:20px;font-size:14px;color:#fff}
		.hyxx_yuemingxi_up_left h2{font-size:24px;color:#fff;font-weight:700;padding-bottom:12px}
		.hyxx_yuemingxi_up_right{width:84px;height:26px;float:right;margin-right:5%;padding-top:55px}
		.hyxx_yuemingxi_up_right span{display:inline-block;width:84px;height:26px;background-color:rgba(255,255,255,.2);text-align:center;line-height:26px;font-size:13px;color:#fff}
		.sprukulist_01_left span{padding:0px;}
	</style>
</head>
<body>
<div id="content" style="left:0px;">
	<div class="right_up">
		<img src="images/biao_109.png"/> 订单金额统计
	</div>
	<div class="right_down" style="padding-bottom:0px;padding-top:10px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
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
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		<div class="hyxx_yuemingxi_up">
	        <ul>
	            <li style="background-color:#77a9da;">
	                <div class="hyxx_yuemingxi_up_left">
	                    <h2 id="total_money1"></h2>订单总金额
	                </div>
	                <div class="clearBoth"></div>
	            </li>
	            <li style="background-color:#7acad1;">
	                <div class="hyxx_yuemingxi_up_left">
	                    <h2 id="total_money2"></h2>待发货订单总金额
	                </div>
	                <div class="clearBoth"></div>
	            </li>
	            <li style="background-color:#b19ecb;">
	                <div class="hyxx_yuemingxi_up_left">
	                    <h2 id="total_money3"></h2>待收货订单总金额
	                </div>
	                <div class="clearBoth"></div>
	            </li>
	            <li style="background-color:#f98e88;">                                
	                <div class="hyxx_yuemingxi_up_left">
	                    <h2 id="total_money4"></h2>已完成订单总金额
	                </div>
	                <div class="clearBoth"></div>
	            </li>
	            <div class="clearBoth"></div>
	        </ul>
	    </div>
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_addtab">
	            	<ul>
	            		<li>
	                    	<a href="javascript:" onclick="qiehuan_sence(0,0)" class="splist_up_addtab_on">全部</a>
	                    </li>
	                    <li>
	                    	<a href="javascript:" onclick="qiehuan_sence(1,2)">待发货</a>
	                    </li>
	                    <li>
	                    	<a href="javascript:" onclick="qiehuan_sence(2,3)">待收货</a>
	                    </li>
	                    <li>
	                    	<a href="javascript:" onclick="qiehuan_sence(3,4)">已完成</a>
	                    </li>
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_02">
					<div class="splist_up_02_1">
						<img src="images/biao_25.png"/>
					</div>
					<div class="splist_up_02_2">
						已选择 <span id="selectedNum">0</span> 项
					</div>
					<div class="dangqiandd_2_down_3">
						<a href="javascript:" onclick="pi_shenhe();"><img src="images/dangqiandingdan_1.png"> 批量审核</a>
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
						<li id="sheheBtn">
							<a href="javascript:" onclick="order_shenhe();"><img src="images/shangchengdd_22.png">订单审核</a>
						</li>
						<li id="errorBtn">
							<a href="javascript:" onclick="order_show();qiehuan('orderInfo',2,'dqddxiangqing_up_on');order_error_index(0);" class="yuandian_tc_yichangcaozuo"><img src="images/shangchengdd_23.png">订单异常操作</a>
						</li>
						<li id="tuihuanBtn">
							<a href="javascript:" onclick="order_show();qiehuan('orderInfo',3,'dqddxiangqing_up_on');order_tuihuan_index(0);" class="yuandian_tc_tuihuanhuo"><img src="images/shangchengdd_24.png">退换货申请</a>
						</li>
						<li id="cancelBtn">
							<a href="javascript:" onclick="quxiao();" class="yuandian_tc_quxiaodd"><img src="images/shangchengdd_25.png">取消订单</a>
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
<input type="hidden" id="status" value="<?=$status?>">
<input type="hidden" id="orderId" value="<?=$orderId?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="kehuName" value="<?=$kehuName?>">
<input type="hidden" id="shouhuoInfo" value="<?=$shouhuoInfo?>">
<input type="hidden" id="pdtInfo" value="<?=$pdtInfo?>">
<input type="hidden" id="payStatus" value="<?=$payStatus?>">
<input type="hidden" id="moneystart" value="<?=$moneystart?>">
<input type="hidden" id="moneyend" value="<?=$moneyend?>">
<input type="hidden" id="kaipiao" value="<?=$kaipiao?>">
<input type="hidden" id="if_beizhu" value="0">
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
	  /*$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	  });*/
	  get_tongjis();
	  laydate.render({
	  	elem: '#riqi1'
	  	,show: true
	  	,position: 'static'
	  	,min: '2018-01-01'
	  	,type: 'datetime'
		,format: 'yyyy-MM-dd HH:mm'
	  	<?=empty($startTime)?'':",value:'$startTime'"?>
	  	,btns: ['confirm']
	  	,done: function(value, date, endDate){
	  		$("#s_time1").html(value);
	  		$("#startTime").val(value);
	  	}
	  });
	  laydate.render({
	  	elem: '#riqi2'
	  	,show: true
	  	,position: 'static'
	  	,type: 'datetime'
		,format: 'yyyy-MM-dd HH:mm'
	  	<?=empty($endTime)?'':",value:'$endTime'"?>
		,btns: ['confirm']
		,done: function(value, date, endDate){
			$("#s_time2").html(value);
			$("#endTime").val(value);
		}
	  });
	  laydate.render({
	  	elem: '#service_time'
	  	,min: '<?=date("Y-m-d",strtotime("-1 days"))?>'
	  	,type: 'datetime'
	  	,format:'yyyy-MM-dd HH:mm'
	  });
	  $(".laydate-btns-confirm").eq(1).click(function(){
	  	$("#riqilan").slideUp(200);
	  	get_tongjis();
	  	reloadTable(0);
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-280"
	    ,url: '?s=order&a=getList'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	scene:'<?=$scene?>',
	    	status:'<?=$status?>',
	    	orderId:'<?=$orderId?>',
	    	startTime:'<?=$startTime?>',
	    	keyword:'<?=$keyword?>',
	    	endTime:'<?=$endTime?>',
	    	kehuName:'<?=$kehuName?>',
	    	shouhuoInfo:'<?=$shouhuoInfo?>',
	    	moneystart:'<?=$moneystart?>',
	    	moneyend:'<?=$moneyend?>',
	    	payStatus:'<?=$payStatus?>',
	    	pdtInfo:'<?=$pdtInfo?>',
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
	  	var status = $("#status").val();
	  	var orderId = $("#orderId").val();
	  	var startTime = $("#startTime").val();
	  	var keyword = $("#keyword").val();
	  	var endTime = $("#endTime").val();
	  	var kehuName = $("#kehuName").val();
	  	var moneystart = $("#moneystart").val();
	  	var moneyend = $("#moneyend").val();
	  	var shouhuoInfo = $("#shouhuoInfo").val();
	  	var pdtInfo = $("#pdtInfo").val();
	  	var payStatus = $("#payStatus").val();
	  	var kaipiao = $("#kaipiao").val();
	  	var if_beizhu = $("#if_beizhu").val();
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
		      ,status:status
		      ,orderId:orderId
		      ,startTime:startTime
		      ,keyword:keyword
		      ,endTime:endTime
		      ,kehuName:kehuName
		      ,moneystart:moneystart
		      ,moneyend:moneyend
		      ,shouhuoInfo:shouhuoInfo
		      ,kaipiao:kaipiao
		      ,pdtInfo:pdtInfo
		      ,payStatus:payStatus
		      ,if_beizhu:if_beizhu
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
	  	$("#kehuName").val(data.field.super_kehuName);
	  	$("#moneystart").val(data.field.super_moneystart);
	  	$("#moneyend").val(data.field.super_moneyend);
	  	$("#shouhuoInfo").val(data.field.super_shouhuoInfo);
	  	$("#pdtInfo").val(data.field.super_pdtInfo);
	  	$("#if_beizhu").val(data.field.super_if_beizhu);
	  	if(data.field.super_status_all=="on"){
	  		$("#status").val('');
	  	}else{
	  		var cangkustr = '';
	  		$("input:checkbox[name='super_status']:checked").each(function(){
	  			cangkustr = cangkustr+','+$(this).val();
	  		});
	  		if(cangkustr.length>0){
	  			cangkustr = cangkustr.substring(1);
	  		}
	  		$("#status").val(cangkustr);
	  	}
	  	$("#payStatus").val(data.field.super_payStatus);
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
function get_tongjis(){
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	$.ajax({
		type: "POST",
		url: "?s=tongji&a=get_tongjis",
		data: "startTime="+startTime+"&endTime="+endTime,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			$("#total_money1").html('￥'+resdata.total_money1+'<span style="font-size:12px;">(订单量:'+resdata.total_num1+')</span>');
			$("#total_money2").html('￥'+resdata.total_money2+'<span style="font-size:12px;">(订单量:'+resdata.total_num2+')</span>');
			$("#total_money3").html('￥'+resdata.total_money3+'<span style="font-size:12px;">(订单量:'+resdata.total_num3+')</span>');
			$("#total_money4").html('￥'+resdata.total_money4+'<span style="font-size:12px;">(订单量:'+resdata.total_num4+')</span>');
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function qiehuan_sence(index,status){
	$(".splist_up_addtab .splist_up_addtab_on").removeClass('splist_up_addtab_on');
	$(".splist_up_addtab ul li").eq(index).find("a").addClass('splist_up_addtab_on');
	$("#status").val(status);
	reloadTable(0);
}
</script>
<script type="text/javascript" src="js/order/order_list.js"></script>
<script type="text/javascript" src="js/order/order_info.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>