<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"untis"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"dtTime"=>array("title"=>"出入库日期","rowCode"=>"{field:'dtTime',title:'出入库日期',width:200,sort:true}"),
				"num"=>array("title"=>"出入库数量","rowCode"=>"{field:'num',title:'出入库数量',width:100}"),
				"kucun"=>array("title"=>"库存量","rowCode"=>"{field:'kucun',title:'库存量',width:100}")
			);
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");

	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$chukuTypes = str_replace('@_@',',',$kucun_set->chuku_types);
$chukuTypes.=',调拨出库,采购退货,盘亏出库';
$rukuTypes = str_replace('@_@',',',$kucun_set->ruku_types);
$rukuTypes.=',调拨入库,采购入库,盘赢入库';
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
// $rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$inventoryId = (int)$request['inventoryId'];
if(!empty($inventoryId)){
	$sn = $db->get_var("select sn from demo_product_inventory where id=$inventoryId");
	if(!empty($request['storeIds'])&&is_numeric($request['storeIds'])){
		$storeName = $db->get_var("select title from demo_kucun_store where id=".(int)$request['storeIds']);
	}
}
$channelId = (int)$request['channelId'];
$brandId = (int)$request['brandId'];
$keyword = $request['keyword'];
$tags = $request['tags'];
$type = $request['type'];
$status = (int)$request['status'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$storeIds = $request['storeIds'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['jiluPageNum'])?10:$_COOKIE['jiluPageNum'];
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$brands = $db->get_results("select id,title from demo_product_brand where comId=$comId order by ordering desc,id asc");
$tagsarry = array();
if(!empty($product_set->tags)){
	$tagsarry = explode('@_@',$product_set->tags);
}
$cangkuSql = "select id,title from demo_kucun_store where comId=$comId";
// if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
// 	$cangkuSql .= " and id in(".$qx_arry['kucun']['storeIds'].")";
// }
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
		<img src="images/biao_19.png"/><?=empty($sn)?'':$sn.' -'?> 出入库明细
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01">
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
						</div>
						<div class="splist_up_01_left_02" style="display:none;">
							<div class="splist_up_01_left_02_up">
								<span><?=empty($storeName)?'全部仓库':$storeName?></span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus(0,'全部仓库');" class="splist_up_01_left_02_down_on">全部仓库</a>
									</li>
									<? foreach($cangkus as $cangku){?>
										<li>
											<a href="javascript:" onclick="selectStatus(<?=$cangku->id?>,'<?=$cangku->title?>');"><?=$cangku->title?></a>
										</li>
									<?}?>
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
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=empty($sn)?$keyword:$sn ?>" placeholder="请输入商品名称/编码/规格/关键字"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_2" style="display:none;">
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
														关键词
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入商品名称/编码/规格/关键字"/>
													</div>
													<div class="gaojisousuo_left">
														出入库类型
													</div>
													<div class="gaojisousuo_right">
														<select name="super_type" id="super_type" lay-search>
															<option value="">全部类型</option>
															<option value="<?=$chukuTypes?>">全部出库</option>
															<? 
																$chukuArry = explode(',',$chukuTypes);
																foreach ($chukuArry as $chuku) {
																	?>
																	<option value="<?=$chuku?>">----<?=$chuku?></option>
																	<?
																}
															?>
															<option value="<?=$rukuTypes?>">全部入库</option>
															<? 
																$rukuArry = explode(',',$rukuTypes);
																foreach ($rukuArry as $ruku) {
																	?>
																	<option value="<?=$ruku?>">----<?=$ruku?></option>
																	<?
																}
															?>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品分类
													</div>
													<div class="gaojisousuo_right" style="width:364px;">
														<div class="layui-form-select">
															<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
															<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
														</div>
														<input type="hidden" name="super_channel" id="super_channel">
													</div>
													<div class="gaojisousuo_left">
														商品品牌
													</div>
													<div class="gaojisousuo_right">
														<select name="super_brand" id="super_brand" lay-search>
															<option value="">选择品牌或输入搜索</option>
															<? if(!empty($brands)){
																foreach ($brands as $b) {
																	?>
																	<option value="<?=$b->id?>"><?=$b->title?></option>
																	<?
																}
															}?>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品标签
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_tags_all" lay-filter="tags" lay-skin="primary" lay-skin="primary" title="全选" checked/>
														<? if(!empty($tagsarry)){
															foreach ($tagsarry as $i=>$t) {
																?>
																<input type="checkbox" name="super_tags_<?=$i?>" pid="tags" lay-filter="notags" lay-skin="primary" lay-skin="primary" title="<?=$t?>" value="<?=$t?>" />
																<?
															}
														}?>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														所属仓库
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_cangkus_all" lay-skin="primary" lay-filter="cangkus" title="全选" checked />
														<? 
															foreach($cangkus as $i=>$t) {
																?>
																<input type="checkbox" name="super_cangkus" pid="cangkus" lay-filter="nocangkus" lay-skin="primary" lay-skin="primary" title="<?=$t->title?>" value="<?=$t->id?>" />
																<?
															}
														?>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														审批状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_status" value="0" title="全部" checked /><input type="radio" name="sunper_status" value="1" title="审批通过" /><input type="radio" name="sunper_status" value="-1" title="审批驳回"/><input type="radio" name="sunper_status" value="2" title="审批中"/>
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
						    <? chekurl($arr,'<a href="?m=system&s=kucun&a=daochuJilus" target="_blank" onclick="daochu();" id="daochuA" class="splist_add">导出</a>') ?>
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
	<input type="hidden" id="inventoryId" value="<?=$inventoryId?>">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="storeIds" value="<?=$storeIds?>">
	<input type="hidden" id="brandId" value="<?=$brandId?>">
	<input type="hidden" id="tags" value="<?=$tags?>">
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
		    ,url: '?m=system&s=kucun&a=getJilus'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	inventoryId:<?=$inventoryId?>,
		    	channelId:<?=$channelId?>,
		    	storeIds:'<?=$storeIds?>',
		    	brandId:<?=$brandId?>,
		    	status:<?=$status?>,
		    	keyword:'<?=$keyword?>',
		    	tags:'<?=$tags?>',
		    	type:'<?=$type?>',
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
		  	var inventoryId = $("#inventoryId").val();
		  	var channelId = $("#channelId").val();
		  	var storeIds = $("#storeIds").val();
		  	var brandId = $("#brandId").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var tags = $("#tags").val();
		  	var type = $("#type").val();
		  	var startTime = $("#startTime").val();
		  	var endTime = $("#endTime").val();
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
			      ,inventoryId:inventoryId
			      ,channelId:channelId
			      ,storeIds:storeIds
			      ,brandId:brandId
			      ,status:status
			      ,keyword:keyword
			      ,tags:tags
			      ,type:type
			      ,startTime:startTime
			      ,endTime:endTime
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
		  form.on('checkbox(tags)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='tags']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(notags)', function(data){
		  	$("input[name='super_tags_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('checkbox(cangkus)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='cangkus']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(nocangkus)', function(data){
		  	$("input[name='super_cangkus_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('checkbox(kczt)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='kczt']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(nokczt)', function(data){
		  	$("input[name='super_kczt_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('submit(search)', function(data){
		  	$("#keyword").val(data.field.super_keyword);
		  	$("#channelId").val(data.field.super_channel);
		  	$("#brandId").val(data.field.super_brand);
		  	$("#type").val(data.field.super_type);
		  	if(data.field.super_tags_all=="on"){
		  		$("#tags").val('');
		  	}else{
		  		var tagstr = '';
		  		if(typeof(data.field.super_tags_0)!='undefined'){
		  			tagstr=','+data.field.super_tags_0;
		  		}
		  		if(typeof(data.field.super_tags_1)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_1;
		  		}
		  		if(typeof(data.field.super_tags_2)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_2;
		  		}
		  		if(typeof(data.field.super_tags_3)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_3;
		  		}
		  		if(typeof(data.field.super_tags_4)!='undefined'){
		  			tagstr=tagstr+','+data.field.super_tags_4;
		  		}
		  		if(tagstr.length>0){
		  			tagstr = tagstr.substring(1);
		  		}
		  		$("#tags").val(tagstr);
		  	}
		  	if(data.field.super_cangkus_all=="on"){
		  		$("#storeIds").val('');
		  	}else{
		  		var cangkustr = '';
		  		$("input:checkbox[name='super_cangkus']:checked").each(function(){
		  			cangkustr = cangkustr+','+$(this).val();
		  		});
		  		if(cangkustr.length>0){
		  			cangkustr = cangkustr.substring(1);
		  		}
		  		$("#storeIds").val(cangkustr);
		  	}
		  	if(data.field.super_kczt_all=="on"){
		  		$("#kczt").val('');
		  	}else{
		  		var kcztstr = '';
		  		$("input:checkbox[name='super_kczt']:checked").each(function(){
		  			kcztstr = kcztstr+','+$(this).val();
		  		});
		  		if(kcztstr.length>0){
		  			kcztstr = kcztstr.substring(1);
		  		}
		  		$("#kczt").val(kcztstr);
		  	}
		  	$("#s_status").val(data.field.sunper_status);
		  	hideSearch();
		  	reloadTable(0);
		  	return false;
		  });
		  form.on('submit(quxiao)', function(){
		  	hideSearch();
		  	return false;
		  });
		  form.on('checkbox(hebing)',function(){
		  	location.href="?m=system&s=kucun&a=index1";
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
		  		form.render('select');
		  		
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
	<script type="text/javascript" src="js/kucun_jilus.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>