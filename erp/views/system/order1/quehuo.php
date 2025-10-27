<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
	"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}"),
	"time"=>array("title"=>"缺货时间","rowCode"=>"{field:'time',title:'缺货时间',width:150,sort:true,style:\"text-align:center\"}"),
	"weight"=>array("title"=>"商品重量","rowCode"=>"{field:'weight',title:'商品重量',width:100}"),
	"pdtNums"=>array("title"=>"商品数量","rowCode"=>"{field:'pdtNums',title:'商品重量',width:100}"),
	"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
	"price"=>array("title"=>"订单总额","rowCode"=>"{field:'price',title:'订单总额',width:100,sort:true}"),
	"fapiao"=>array("title"=>"是否开票","rowCode"=>"{field:'fapiao',title:'是否开票',width:90}"),
	"username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$status = 1;
$keyword = $request['keyword'];
$pdtChanelOpt = $request['pdtChanelOpt'];
$pdtChanelNum = $request['pdtChanelNum'];
$pdtNumsOpt = $request['pdtNumsOpt'];
$pdtNumsNum = $request['pdtNumsNum'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['quehuoPageNum'])?10:$_COOKIE['quehuoPageNum'];
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
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
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
		<img src="images/biao_109.png"/> 缺货订单
		<!-- <div class="bangzhulist_up_right" onclick="showHelp(321);">帮助</div> -->
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist_up">
			<div class="splist_up_01">
				<div class="dqdd_kehebingdd_2">	
					<div class="dqdd_kechaifendd_2_01">
						商品种类
						<select id="pdtChanelOpt">
							<option value="=">=</option>
							<option value=">">></option>
							<option value="<"><</option>
						</select>
						<input type="number" id="pdtChanelNum">
					</div>
					<div class="dqdd_kechaifendd_2_01">
						商品总重量
						<select id="pdtNumsOpt">
							<option value="=">=</option>
							<option value=">">></option>
							<option value="<"><</option>
						</select>
						<input type="number" id="pdtNumsNum">
					</div>
					<div class="dqdd_kehebingdd_2_02">
						<div class="dqdd_kehebingdd_2_02_1">
							<input type="text" id="keyword" placeholder="搜索订单号/商品编码/会员账号">
						</div>
						<div class="dqdd_kehebingdd_2_02_2">
							<a href="javascript:" onclick="search_order();"><img src="images/shangchengdd_12.png"> 搜索</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_up_02">
				<div class="splist_up_02_1">
					<img src="images/biao_25.png"/>
				</div>
				<div class="splist_up_02_2">
					已选择 <span id="selectedNum">0</span> 项
				</div>
				<div class="qhdd_yibuhuo_2">
					<a href="javascript:" onclick="pi_shenhe();"><img src="images/quhuodd_1.png"> 恢复正常发货</a>
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
						<a href="javascript:" onclick="order_huifu();"><img src="images/shangchengdd_22.png">恢复正常</a>
					</li>
					<li>
						<a href="javascript:" onclick="quehuo_quxiao();" class="yuandian_tc_quxiaodd"><img src="images/shangchengdd_25.png">取消订单</a>
					</li>
				</ul>
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
<div id="myModal" class="reveal-modal"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
	var keyword='<?=$keyword?>',
		pdtChanelOpt='<?=$pdtChanelOpt?>',
		pdtChanelNum='<?=$pdtChanelNum?>',
		pdtNumsOpt='<?=$pdtNumsOpt?>',
		pdtNumsNum='<?=$pdtNumsNum?>';
	var productListTalbe,lay_date;
	layui.use(['laydate', 'laypage','table','form'], function(){
	  var laydate = layui.laydate
	  ,laypage = layui.laypage
	  ,table = layui.table
	  ,form = layui.form;
	  lay_date = laydate;
	  //,load = layer.load()
	  laydate.render({
	  	elem: '#service_time'
	  	,min: '<?=date("Y-m-d",strtotime("-1 days"))?>'
	  	,type: 'datetime'
	  	,format:'yyyy-MM-dd HH:mm'
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-140"
	    ,url: '?s=order&a=getQuehuoList'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	keyword:keyword,
	    	pdtChanelOpt:pdtChanelOpt,
	    	pdtChanelNum:pdtChanelNum,
	    	pdtNumsOpt:pdtNumsOpt,
	    	pdtNumsNum:pdtNumsNum
	    },done: function(res, curr, count){
	    	layer.closeAll('loading');
		    $("#page").val(curr);
		    $("th[data-field='id']").hide();
		    $("th[data-field='status']").hide();
		  }
	  });
	  table.on('sort(product_list)', function(obj){
	  	$("#order1").val(obj.field);
	  	$("#order2").val(obj.type);
	  	var scrollLeft = $(".layui-table-body").scrollLeft();
	  	layer.load();
		table.reload('product_list', {
		    initSort: obj
		    ,height: "full-200"
		    ,where: {
		      order1: obj.field
		      ,order2: obj.type,
		      keyword:keyword,
		      pdtChanelOpt:pdtChanelOpt,
		      pdtChanelNum:pdtChanelNum,
		      pdtNumsOpt:pdtNumsOpt,
		      pdtNumsNum:pdtNumsNum
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
<script type="text/javascript" src="js/order/order_quehuo.js"></script>
<script type="text/javascript" src="js/order/order_info.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>