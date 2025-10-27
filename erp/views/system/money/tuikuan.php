<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"orderId"=>array("title"=>"流水号","rowCode"=>"{field:'orderId',title:'流水号',width:220}"),
				"dtTime"=>array("title"=>"退款时间","rowCode"=>"{field:'dtTime',title:'退款时间',width:150,sort:true}"),
				"kehuName"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'kehuName',title:'".$kehu_title."名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"pay_type"=>array("title"=>"退款账户","rowCode"=>"{field:'pay_type',title:'退款账户',width:100}"),
				"money"=>array("title"=>"退款金额","rowCode"=>"{field:'money',title:'退款金额',width:120}"),
				"pay_info"=>array("title"=>"退款原因","rowCode"=>"{field:'pay_info',title:'退款原因',width:150}")
			);
$showRowsArry = array("orderId"=>1,"dtTime"=>1,"kehuName"=>1,"pay_type"=>1,"money"=>1,"pay_info"=>1);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'detail', title: 'detail', width:0,style:\"display:none;\"}";
foreach ($showRowsArry as $row=>$isshow){
	if($isshow==1){
		$rowsJS.=','.$allRows[$row]['rowCode'];
	}
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$account = $request['account'];
$keyword = $request['keyword'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['skdPageNum'])?10:$_COOKIE['skdPageNum'];
$zong_money = $db->get_var("select sum(money) from demo_dinghuo_money where comId=$comId and status=0 and type=1");
if(empty($zong_money))$zong_money=0;
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
		td[data-field="title"] div,td[data-field="account"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="chuku_fahuo"] div{line-height:20px;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.splist_up_01_left_02_down ul li a {width: 300px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_105.png"/> 退款确认
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_right_1" style="display:inline-block;float:none;margin-top:0px;position:relative;top:13px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/订单号/流水号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_3">
							<div class="splist_up_01_right_zj">
                        		待确认退款合计：<span id="zong_money"><?=$zong_money?></span>
                        	</div>
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
						<li>
							<a href="javascript:detail();"><img src="images/biao_30.png"> 订单详情</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要执行退款确认操作吗？',t_queren,'');"><img src="images/biao_95.png"> 确认退款</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="shoukuanqueren_xiangqing_tc" id="shoukuanqueren_xiangqing_tc" style="display:none;">
		<div class="bjkh_bj"></div>
		<div class="skqr_xx" style="top:5%;">
			<div class="bjkh_jebangsjxx_1">
				订单退款详情
			</div>
			<div class="bjkh_jebangsjxx_2">
				<div class="skqr_xx_01">
					<ul>
						<li>
							<div class="skqr_xx_01_left">
								订单号：
							</div>
							<div class="skqr_xx_01_right" id="show_dinghuoId">

							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="skqr_xx_01_left">
								金额：
							</div>
							<div class="skqr_xx_01_right" id="show_money">

							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="skqr_xx_01_left">
								支付方式：
							</div>
							<div class="skqr_xx_01_right" id="show_pay_type">

							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
							<div class="skqr_xx_01_left">
								备注：
							</div>
							<div class="skqr_xx_01_right" id="show_beizhu">

							</div>
							<div class="clearBoth"></div>
						</li>
						<li>
	                        <div class="skqr_xx_01_left">
	                            附件：
	                        </div>
	                        <div class="skqr_xx_01_right" id="show_fujian">
	                            
	                        </div>
	                        <div class="clearBoth"></div>
	                    </li>
					</ul>
				</div>
				<div class="skqr_xx_02">
					流水号：<span id="show_orderId"></span>　　　　　　日期：<span id="show_dtTime"></span><br>
					操作人：<span id="show_userName"></span>　　　　　　 
				</div>
			</div>
			<div class="bjkh_jebangsjxx_3">
				<a href="javascript:hideInfo();" class="bjkh_jebangsjxx_3_01">确定</a>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="account" value="<?=$account?>">
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
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=money&a=getTuikuan'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	account:'<?=$account?>',
		    	keyword:'<?=$keyword?>'
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='detail']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var account = $("#account").val();
		  	var keyword = $("#keyword").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,account:account
			      ,keyword:keyword
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
		});
	</script>
	<script type="text/javascript" src="js/money_shoukuan.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>