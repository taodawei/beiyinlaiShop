<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
	"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:200,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:150,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"z_num"=>array("title"=>"销售数量","rowCode"=>"{field:'z_num',title:'销售数量',width:150,sort:true}"),
	"z_price"=>array("title"=>"销售总金额","rowCode"=>"{field:'z_price',title:'销售总金额',width:150,sort:true}")
);
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
//$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$startTime = (int)$request['startTime'];
$endTime = (int)$request['endTime'];
$keyword = $request['keyword'];
$userId = (int)$request['userId'];
$order1 = empty($request['order1'])?'z_num':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 20;
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$step = 1;
if($product_set->number_num>0){
	$chushu = pow(10,$product_set->number_num);
	$step = 1/$chushu;
}
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
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.sprukulist_01_left span{padding:0px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> 商品销量统计
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<!-- <div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
								<span>全部分类</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_01_down">
								<ul style="border-left:0px" id="ziChannels1">
									<li class="allsort_01">
										<a href="javascript:selectChannel(0,'全部分类');">全部分类</a>
									</li>
									<? if(!empty($channels)){
										foreach ($channels as $c) {
											?>
											<li class="allsort_01">
												<a href="javascript:" onclick="selectChannel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
											</li>
											<?
										}
										?><?
									}?>
								</ul>
							</div>
						</div> -->
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
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码/规格/关键字"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=tongji&a=daochu" id="daochuA" onclick="daochu();" class="splist_add">导出</a>') ?>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<!-- <script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<? if($adminRole>=7||strstr($qx_arry['kucun']['storeIds'],'all')||strstr($qx_arry['kucun']['storeIds'],'edit')){?>
						<li>
							<a href="javascript:edit_kucun();"><img src="images/biao_31.png"> 修改</a>
						</li>
						<? }?>
						<li>
							<a href="javascript:detail_kucun();"><img src="images/biao_30.png"> 明细</a>
						</li>
					</ul>
				</div> -->
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<!-- <input type="hidden" id="cuxiao" value="<?=$cuxiao?>"> -->
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var step = <?=$step?>; 
		var productListTalbe;
		var productListForm;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListForm = form;
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
		  $(".laydate-btns-confirm").eq(1).click(function(){
		  	$("#riqilan").slideUp(200);
		  	reloadTable(0);
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=tongji&a=get_inventorys&userId=<?=$userId?>'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,autoSort:false
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	startTime:'<?=$startTime?>',
		    	endTime:'<?=$endTime?>',
		    	keyword:'<?=$keyword?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		  		$("th[data-field='storeId']").hide();
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var startTime = $("#startTime").val();
		  	var endTime = $("#endTime").val();
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
			      ,channelId:channelId
			      ,startTime:startTime
			      ,endTime:endTime
			      ,keyword:keyword
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
					$("th[data-field='storeId']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  ajaxpost=$.ajax({
		  	type: "POST",
		  	url: "/erp_service.php?action=get_product_channels1",
		  	data: "",
		  	dataType:"text",timeout : 10000,
		  	beforeSend:function(){
		  		<? if($request['page']>1){?>
		  		reloadTable(1);
		  		<? }?>
		  	},
		  	success: function(resdata){
		  		$("#selectChannels").append(resdata);
		  		
		  	},
		  	error: function() {
		  		layer.msg('数据请求失败1', {icon: 5});
		  	}
		  });
		  $("#selectChannel").click(function(){
		  	$(this).parent().toggleClass('layui-form-selected');
		  });
		});
	</script>
	<script type="text/javascript" src="js/tongji/index.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>