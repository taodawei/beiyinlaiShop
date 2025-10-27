<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
	"kehuName"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'kehuName',title:'".$kehu_title."名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"price"=>array("title"=>"订货金额","rowCode"=>"{field:'price',title:'订货金额',width:150}"),
	"price_weikuan"=>array("title"=>"未确认金额","rowCode"=>"{field:'price_weikuan',title:'未确认金额',width:150}"),
	"price_daiqueren"=>array("title"=>"待确认金额","rowCode"=>"{field:'price_daiqueren',title:'待确认金额',width:150}")
);
$showRowsArry = array("orderId"=>1,"dtTime"=>1,"kehuName"=>1,"price"=>1,"price_weikuan"=>1,"price_daiqueren"=>1);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
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
$zong = $db->get_row("select count(*) as num,sum(price_weikuan) as money from demo_dinghuo_order where comId=$comId and status>0 and price_weikuan>0");
$yiqueren = $db->get_var("select count(*) from demo_dinghuo_order where comId=$comId and status>0 and price_weikuan=0");
if(empty($yiquern))$yiquern=0;
//$accounts = $db->get_results("select bank_name,bank_user,bank_account from demo_kehu_bank where comId=$comId and status=1");
//$zong_money = $db->get_var("select sum(money) from demo_dinghuo_money where comId=$comId and status=0 and type=0");
//if(empty($zong_money))$zong_money=0;
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
		<img src="images/biao_105.png"/> 收款确认
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up" style="height:118px;">
				<div class="splist_up_addtab">
	            	<ul>
	            		<li>
	                    	<a href="javascript:" class="splist_up_addtab_on">待收款（<?=$zong->num?>）</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=money&a=shoukuan1">已收款（<?=$yiqueren?>）</a>
	                    </li>
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_01">
					<div class="splist_up_01_left" style="">
						<!-- <div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部收款帐号</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus('0','全部收款帐号');" class="splist_up_01_left_02_down_on">全部收款帐号</a>
									</li>
									<? if(!empty($accounts)){
										foreach ($accounts as $a){?>
											<li><a href="javascript:" onclick="selectStatus('<?=$a->bank_account?>','<?=$a->bank_name?> <?=$a->bank_account?>');"><?=$a->bank_name?> <?=$a->bank_account?></a></li>
										<?}
									}?>
								</ul>
							</div>
						</div> -->
						<div class="splist_up_01_right_1" style="display:inline-block;float:none;margin-left:40px;margin-top:0px;position:relative;top:13px;">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/订单号"/>
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
                        		待确认收款合计：<span id="zong_money"><?=$zong->money?></span>
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
							<a href="javascript:detail('shoukuan');"><img src="images/biao_30.png"> 订单详情</a>
						</li>
						<li>
							<a href="javascript:detail_shoukuan('shoukuan');"><img src="images/biao_95.png"> 确认收款</a>
						</li>
					</ul>
				</div>
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
		    ,height: "full-190"
		    ,url: '?m=system&s=money&a=getShoukuanOrder&type=1'
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
			    ,height: "full-190"
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