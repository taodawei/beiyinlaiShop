<?
global $db,$request,$adminRole,$db,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"image"=>array("title"=>"商品图片","rowCode"=>"{field:'image',title:'商品图片',width:87,unresize:true}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:87}"),
				"channel"=>array("title"=>"所属分类","rowCode"=>"{field:'channel',title:'所属分类',width:150}"),
				"orders"=>array("title"=>"订单量","rowCode"=>"{field:'orders',title:'订单量',width:100}"),
				"price_market"=>array("title"=>"原价","rowCode"=>"{field:'price_market',title:'原价',width:150}"),
				"price_sale"=>array("title"=>"售价","rowCode"=>"{field:'price_sale',title:'售价',width:100}"),
				"price_sale"=>array("title"=>"售价","rowCode"=>"{field:'price_sale',title:'售价',width:100}"),
				"ordering"=>array("title"=>"排序","rowCode"=>"{field:'ordering',title:'排序',width:80,sort:true}"),
				"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:150,sort:true}")
			);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'<img src=\"images/biao_22.png\">',align:'center', toolbar: '#barDemo'}";
$channelId = (int)$request['channelId'];
$brandId = (int)$request['brandId'];
$status = (int)$request['status'];
$keyword = $request['keyword'];
$tags = $request['tags'];
$cangkus = $request['cangkus'];
$source = (int)$request['source'];
$cuxiao = (int)$request['cuxiao'];
$payType = (int)$request['payType'];
$if_tongbu = (int)$request['if_tongbu'];
$order1 = empty($request['order1'])?'ordering':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['pdtPageNum'])?10:$_COOKIE['pdtPageNum'];
$channels = array();
if(is_file("../cache/channels_pdt_$comId.php")){
	$content = file_get_contents("../cache/channels_pdt_$comId.php");
	$channels = json_decode($content);
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
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:73px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div,td[data-field="com_title"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> 商铺商品列表
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
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus(0,'全部状态');" class="splist_up_01_left_02_down_on">全部状态</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus(1,'上架');">上架</a>
									</li>

									<li>
										<a href="javascript:" onclick="selectStatus(-1,'下架');">下架</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus(2,'待审');">待审</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus(-2,'驳回');">驳回</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_3">
							<a href="?m=system&s=pdts&a=create" class="splist_add">新 增</a>
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
					<div class="splist_up_02_3">
						<? if($_SESSION[TB_PREFIX.'mendian_type']!=1 && $_SESSION[TB_PREFIX.'mendian_type']!=2){?>
							<a href="javascript:" onclick="xiajia();"><img src="images/biao_27.png"/> 下架</a>
							<a href="javascript:" onclick="delAll();"><img src="images/biao_28.png"/> 删除</a>
						<? }?>
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
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<li>
							<a href="javascript:edit_product();"><img src="images/biao_31.png"> 编辑</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="brandId" value="<?=$brandId?>">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="tags" value="<?=$tags?>">
	<input type="hidden" id="cangkus" value="<?=$cangkus?>">
	<input type="hidden" id="source" value="<?=$source?>">
	<input type="hidden" id="payType" value="<?=$payType?>">
	<input type="hidden" id="cuxiao" value="<?=$cuxiao?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var if_tongbu=<?=$if_tongbu?>;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=pdts&a=getList&if_tongbu='+if_tongbu
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	brandId:<?=$brandId?>,
		    	status:<?=$status?>,
		    	payType:<?=$payType?>,
		    	keyword:'<?=$keyword?>',
		    	tags:'<?=$tags?>',
		    	source:<?=$source?>,
		    	cuxiao:<?=$cuxiao?>,
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var brandId = $("#brandId").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var tags = $("#tags").val();
		  	var source = $("#source").val();
		  	var cuxiao = $("#cuxiao").val();
		  	var payType = $("#payType").val();
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
			      ,brandId:brandId
			      ,status:status
			      ,keyword:keyword
			      ,tags:tags
			      ,source:source
			      ,cuxiao:cuxiao
			      ,payType:payType
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
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
		  		$("#cangkus").val('');
		  	}else{
		  		var cangkustr = '';
		  		$("input:checkbox[name='super_cangkus']:checked").each(function(){
		  			cangkustr = cangkustr+','+$(this).val();
		  		});
		  		if(cangkustr.length>0){
		  			cangkustr = cangkustr.substring(1);
		  		}
		  		$("#cangkus").val(cangkustr);
		  	}
		  	$("#cuxiao").val(data.field.super_cuxiao);
		  	$("#status").val(data.field.sunper_status);
		  	$("#source").val(data.field.sunper_source);
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
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  ajaxpost=$.ajax({
		  	type: "POST",
		  	url: "/erp_service.php?action=get_pdts_channels1",
		  	data: "",
		  	dataType:"text",timeout : 20000,
		  	success: function(resdata){
		  		$("#selectChannels").append(resdata);
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
	<script type="text/javascript" src="js/pdts/pdts_list.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
</body>
</html>