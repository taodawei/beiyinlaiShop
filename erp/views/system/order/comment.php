<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"pingjia"=>array("title"=>"评价","rowCode"=>"{field:'pingjia',title:'评价',width:150}"),
	"content"=>array("title"=>"评论内容","rowCode"=>"{field:'content',title:'评论内容',width:400}"),
	"pdtName"=>array("title"=>"评价商品","rowCode"=>"{field:'pdtName',title:'评价商品',width:300}"),
	"name"=>array("title"=>"评价人","rowCode"=>"{field:'name',title:'评价人',width:120}")
);
// $rowsJS = "{type:'checkbox'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";

$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"}";

foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
// $rowsJS .=",{width:49,title:'',align:'center', toolbar: '#barDemo'}";
$status = empty($request['status'])?1:(int)$request['status'];
$star = (int)$request['star'];
$ifJifen = (int)$request['if_jifen'];
$keyword = $request['keyword'];
$pdtName = $request['pdtName'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$username = $request['username'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
//计算各类型订单的数量
$num_sql = "select status,count(*) as num from order_comment$fenbiao where comId=$comId and if_jifen = $ifJifen group by status";
$zongNum = 0;
$numArry = array();
$nums = $db->get_results($num_sql);
if(!empty($nums)){
	foreach ($nums as $n){
		$zongNum+=$n->num;
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
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/clipboard.min.js"></script>
	<style>
		.layui-table-view{margin:10px;}
		td[data-field="pdtName"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:40px;overflow:hidden;cursor:pointer;}
		.layui-anim.layui-icon{font-size:20px;}
		.layui-form-radio{margin-top:0px;line-height:22px;margin-right:0px;}
		.layui-form-radio i{margin-right:3px;}
		.layui-form-radio span{font-size:12px;}
		.layui-form-select .layui-input{height:25px;}
		.ddxx_jibenxinxi_2_01_down_right .layui-form-select{margin-bottom:2px;}
		.layui-table-cell{height: auto;min-height:28px;}
		.layui-form-selected dl{top:25px;min-height:200px;}
	</style>
</head>
<body>
<? require('views/system/order/header.php')?>
<div id="content">
	<div class="right_up">
		<img src="images/biao_109.png"/> 订单评价<?='('.$zongNum.')'?>
		<!-- <div class="bangzhulist_up_right" onclick="showHelp(321);">帮助</div> -->
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up" style="height:118px;">
				<div class="splist_up_addtab">
	            	<ul>
	                    <li>
	                    	<a href="?s=order&a=comment&status=1&if_jifen=<?=$ifJifen?>" <? if($status==1){?>class="splist_up_addtab_on"<? }?>>全部(<?=(int)$numArry[1]?>)</a>
	                    </li>
	                    <!--<li>-->
	                    <!--	<a href="?s=order&a=comment&status=2" <? if($status==2){?>class="splist_up_addtab_on"<? }?>>已审核(<?=(int)$numArry[2]?>)</a>-->
	                    <!--</li>-->
	                    <!--<li>-->
	                    <!--	<a href="?s=order&a=comment&status=3" <? if($status==3){?>class="splist_up_addtab_on"<? }?>>已回复(<?=(int)$numArry[3]?>)</a>-->
	                    <!--</li>-->
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>评价</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStar(0,'全部');">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStar(5,'好评');">好评</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStar(3,'中评');">中评</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStar(1,'差评');">差评</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入订单号/商品名称/评价人"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="keyword=$('#keyword').val();reloadTable(0);"><img src="images/biao_21.gif"/></a>
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
														商品信息
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_pdtName" value="<?=$pdtName?>" class="gaojisousuo_right_input" placeholder="请输入商品名称"/>
													</div>
													<div class="gaojisousuo_left">
														评价时间
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
														订单号
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_orderId" value="<?=$orderId?>" class="gaojisousuo_right_input" placeholder="请输入订单号"/>
													</div>
													<div class="gaojisousuo_left">
														联系人
													</div>
													<div class="gaojisousuo_right">
														<div class="dingdanjine">
															<input type="text" name="super_username" value="<?=$username?>" class="gaojisousuo_right_input" placeholder="输入申请人姓名/联系方式"/>
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
						<? if($status==1){?>
						<a href="javascript:" onclick="pi_shenhe();"><img src="images/dangqiandingdan_1.png"> 批量审核</a>
						<? }
						if($status!=3){?>
							<a href="javascript:" onclick="pi_huifu();"><img src="images/dangqiandingdan_12.png"> 批量回复</a>
						<? }?>
						<a href="javascript:" onclick="pi_delete();"><img src="images/dingdanfuwu_12.png"> 批量删除</a>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list"></table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
					<ul>
						<li id="sheheBtn">
							<a href="javascript:" onclick="comment_shenhe();"><img src="images/shangchengdd_28.png">审核</a>
						</li>
						<li id="huifuBtn">
							<a href="javascript:" onclick="comment_huifu();"><img src="images/shangchengdd_33.png">回复</a>
						</li>
						<li>
							<a href="javascript:" onclick="comment_delete();"><img src="images/shangchengdd_31.png">删除</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="if_jifen" value="<?=$ifJifen?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
	var productListTalbe,lay_date;
	var status = <?=$status?>,
		star = <?=$star?>,
		keyword='<?=$keyword?>',
		pdtName='<?=$pdtName?>',
		orderId='<?=$orderId?>',
		startTime='<?=$startTime?>',
		endTime='<?=$endTime?>',
		if_jifen='<?=$ifJifen?>',
		username='<?=$username?>';
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
	  $(".laydate-btns-confirm").click(function(){
	  	$("#riqilan").slideUp(200);
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-200"
	    ,url: '?s=order&a=getCommentList'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	status:status,
	    	star:star,
	    	keyword:keyword,
	    	if_jifen:if_jifen,
	    	pdtName:pdtName,
	    	orderId:orderId,
	    	startTime:startTime,
	    	endTime:endTime,
	    	username:username
	    },done: function(res, curr, count){
	    	layer.closeAll('loading');
		    $("#page").val(curr);
		    $("th[data-field='id']").hide();
		    $("th[data-field='status']").hide();
		  }
	  });
	  form.on('submit(search)', function(data){
	  	pdtName = data.field.super_pdtName;
	  	startTime = data.field.super_startTime;
	  	endTime = data.field.super_endTime;
	  	orderId = data.field.super_orderId;
	  	username = data.field.super_username;
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
<script type="text/javascript" src="js/order/order_comment.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>