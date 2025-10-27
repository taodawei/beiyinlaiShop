<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"orderId"=>array("title"=>"单号","rowCode"=>"{field:'orderId',title:'单号',width:200}"),
				"dtTime"=>array("title"=>"出库日期","rowCode"=>"{field:'dtTime',title:'出库日期',width:150,sort:true}"),
				"username"=>array("title"=>"制单人","rowCode"=>"{field:'username',title:'制单人',width:100}")
			);
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");

	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$chukuTypes = str_replace('@_@',',',$kucun_set->chuku_types);
$chukuTypes.=',销售出库,调拨出库,采购退货,盘亏出库';
$rukuTypes = str_replace('@_@',',',$kucun_set->ruku_types);
$rukuTypes.=',调拨入库,采购入库,盘赢入库';
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$storeIds = (int)$request['storeIds'];
if(!empty($storeIds))$storeName = $db->get_var("select title from demo_kucun_store where id=$storeIds");
$type = $request['type'];
$status = (int)$request['status'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['chukuPageNum'])?10:$_COOKIE['chukuPageNum'];
$cangkuSql = "select id,title from demo_kucun_store where comId=$comId";
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
	$cangkuSql .= " and id in(".$qx_arry['kucun']['storeIds'].")";
}
$cangkuSql .= " order by id asc";
$cangkus = $db->get_results($cangkuSql);
//$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId order by id asc");
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
		#riqi1 .layui-laydate{border-right:0px;}
		#riqi2 .layui-laydate{border-left:0px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_58.png"/>商品出库
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01" style="display:none;">
							<div class="splist_up_01_left_02_up">
								<span><?=empty($storeName)?'全部仓库':$storeName?></span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus(0,'全部仓库');">全部仓库</a>
									</li>
									<? foreach($cangkus as $cangku){?>
										<li>
											<a href="javascript:" onclick="selectStatus(<?=$cangku->id?>,'<?=$cangku->title?>');"><?=$cangku->title?></a>
										</li>
									<?}?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="display:none;">
							<div class="splist_up_01_left_01_up">
								<span>全部类型</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectType('');">全部类型</a>
									</li>
									<? 
									$chukuTypes = explode(',',$chukuTypes);
									foreach($chukuTypes as $type1){?>
										<li>
											<a href="javascript:" onclick="selectType('<?=$type1?>');"><?=$type1?></a>
										</li>
									<?}?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="margin-left:20px;display:none">
							<div class="splist_up_01_left_03_up">
								<span>审批状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectZt(0,'所有状态');">所有状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(1,'已审批');">已审批</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(-1,'已驳回');">已驳回</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectZt(2,'待审批');">待审批</a>
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
                        </div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=chuku&a=add" class="splist_add">新 增</a>') ?>
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
					    <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=kucun&a=jilu_detail" onclick="jilu_detail(\'chuku\')"><img src="images/biao_30.png"> 明细</a></li>') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="storeIds" value="<?=$storeIds?>">
	<input type="hidden" id="type" value="<?=$type?>">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
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
		    ,height: "full-140"
		    ,url: '?m=system&s=chuku&a=getJilus'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	storeIds:'<?=$storeIds?>',
		    	type:'<?=$type?>',
		    	status:<?=$status?>,
		    	startTime:'<?=$startTime?>',
		    	endTime:'<?=$endTime?>'
		    },done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='storeId']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var storeIds = $("#storeIds").val();
		  	var status = $("#s_status").val();
		  	var type = $("#type").val();
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
			      ,storeIds:storeIds
			      ,status:status
			      ,type:type
			      ,startTime:startTime
			      ,endTime:endTime
			    },page: {
					curr: 1
				}
			  });
			$("th[data-field='id']").hide();
		  });
		});

	</script>
	<script type="text/javascript" src="js/ruku.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>