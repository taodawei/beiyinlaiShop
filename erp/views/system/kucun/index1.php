<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"untis"=>array("title"=>"单位","rowCode"=>"{field:'units',title:'单位',width:100}"),
				"yugouNum"=>array("title"=>"预购量","rowCode"=>"{field:'yugouNum',title:'预购量',width:100}"),
				"kucun"=>array("title"=>"库存量","rowCode"=>"{field:'kucun',title:'库存量',width:100,sort:true}"),
				"kucun_keyong"=>array("title"=>"可用库存量","rowCode"=>"{field:'kucun_keyong',title:'可用库存量',width:100}")
			);
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',title:'',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$brandId = (int)$request['brandId'];
$status = (int)$request['status'];
$keyword = $request['keyword'];
$tags = $request['tags'];
$storeIds = $request['storeIds'];
$source = (int)$request['source'];
$cuxiao = (int)$request['cuxiao'];
$kczt = $request['kczt'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['kucunPageNum1'])?10:$_COOKIE['kucunPageNum1'];
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
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId order by id asc");
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
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> 商品库存
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
						<div class="splist_up_01_left_kucun">	
                        	<form class="layui-form"><input type="checkbox" name="hebing" lay-filter="hebing" lay-skin="primary" title="将商品合并" checked="checked"></form>
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
														关键词
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入商品名称/编码/规格/关键字"/>
													</div>
													<div class="gaojisousuo_left">
														商品分类
													</div>
													<div class="gaojisousuo_right">
														<div class="layui-form-select">
															<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
															<dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
														</div>
														<input type="hidden" name="super_channel" id="super_channel">
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
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
														商品状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_status" value="0" title="全部" checked /><input type="radio" name="sunper_status" value="1" title="上架" /><input type="radio" name="sunper_status" value="-1" title="下架"/>
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
							<a href="?m=system&s=kucun&a=pandian" class="splist_add">库存盘点</a>
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
						<li>
							<a href="javascript:detail_kucun();"><img src="images/biao_30.png"> 明细</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="storeIds" value="<?=$storeIds?>">
	<input type="hidden" id="brandId" value="<?=$brandId?>">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="tags" value="<?=$tags?>">
	<input type="hidden" id="kczt" value="<?=$kczt?>">
	<input type="hidden" id="cuxiao" value="<?=$cuxiao?>">
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
		  ,load = layer.load()
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=kucun&a=getList1'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	storeIds:'<?=$storeIds?>',
		    	brandId:<?=$brandId?>,
		    	status:<?=$status?>,
		    	keyword:'<?=$keyword?>',
		    	tags:'<?=$tags?>',
		    	source:<?=$source?>,
		    	cuxiao:<?=$cuxiao?>,
		    	kczt:'<?=$kczt?>'
		    },done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='storeId']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var storeIds = $("#storeIds").val();
		  	var brandId = $("#brandId").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var tags = $("#tags").val();
		  	var source = $("#source").val();
		  	var cuxiao = $("#cuxiao").val();
		  	var kczt = $("#kczt").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,channelId:channelId
			      ,brandId:brandId
			      ,status:status
			      ,keyword:keyword
			      ,tags:tags
			      ,kczt:kczt
			      ,source:source
			      ,cuxiao:cuxiao
			    },page: {
					curr: 1
				}
			  });
			$("th[data-field='id']").hide();
			$("th[data-field='storeId']").hide();
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
		  	location.href="?m=system&s=kucun&a=index";
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
		  	dataType:"text",timeout : 8000,
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
		  		layer.msg('数据请求失败', {icon: 5});
		  	}
		  });
		  $("#selectChannel").click(function(){
		  	$(this).parent().toggleClass('layui-form-selected');
		  });
		});

	</script>
	<script type="text/javascript" src="js/kucun_list.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>