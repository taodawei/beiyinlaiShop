<?
global $db,$request,$adminRole,$db,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"logo"=>array("title"=>"兑换卡图片","rowCode"=>"{field:'logo',title:'兑换卡图片',width:150}"),
				"title"=>array("title"=>"兑换卡名称","rowCode"=>"{field:'title',title:'兑换卡名称',width:150}"),
				"change_time"=>array("title"=>"可兑数量","rowCode"=>"{field:'change_time',title:'可兑数量',width:150}"),
				"totalNum"=>array("title"=>"总商品","rowCode"=>"{field:'totalNum',title:'总商品',width:150}"),
				"todayNum"=>array("title"=>"可兑商品","rowCode"=>"{field:'totalNum',title:'可兑商品',width:150}"),
				"weekNum"=>array("title"=>"预警下线商品","rowCode"=>"{field:'weekNum',title:'预警下线商品',width:150}"),
                "channelTitle"=>array("title"=>"分类","rowCode"=>"{field:'channelTitle',title:'分类',width:120}"),
                "beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:180}"),
				"ordering"=>array("title"=>"排序","rowCode"=>"{field:'ordering',title:'排序',width:80,sort:true}")
			);

$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:100,title:'操作',align:'center', toolbar: '#barDemo'}";

$channelId = !empty($request['channelId'])?$request['channelId']:0;
$brandId = !empty($request['brandId'])?$request['brandId']:0;
$status = !empty($request['status'])?$request['status']:0;
$keyword = !empty($request['keyword'])?$request['keyword']:'';
$tags = !empty($request['tags'])?$request['tags']:'';
$cangkus = !empty($request['cangkus'])?$request['cangkus']:'';
$source = !empty($request['source'])?$request['source']:0;
$cuxiao = !empty($request['cuxiao'])?$request['cuxiao']:0;

$order1 = empty($request['order1'])?'ordering':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['pdtPageNum'])?10:$_COOKIE['pdtPageNum'];
$channels = array();
$channels = $db->get_results("select id,title from demo_change_channel where comId=$comId and parentId = 0 order by ordering desc,id asc");

foreach ($channels as $k => $channel){
    $channel->channels = [];
    $childs = $db->get_results("select id,title from demo_change_channel where comId=$comId and parentId = $channel->id order by ordering desc,id asc");
    if($childs){
        foreach ($childs as $ck => $child){
            $childs[$ck]->channels = [];
            $childd = $db->get_results("select id,title from demo_change_channel where comId=$comId and parentId = $child->id order by ordering desc,id asc"); 
            if($childd){
                $childs[$ck]->channels = $childd;
            }
            
        }
        // $childs[$ck]->channels = $childs;
        
    }
    if($childs){
        $channel->channels = $childs;
    }
    
    $channels[$k] = $channel;
}

// echo '<pre>';
// var_dump($channels);
$tagsarry = array();
if(!empty($product_set->tags)){
	$tagsarry = explode('@_@',$product_set->tags);
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
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:73px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="logo"] div{height:auto;text-align:center;}
		td[data-field="logo"] img{border:#abd3e7 1px solid;height:60px;width:120px;}
		.yuandian_xx{width:105px;}
		.cangkugl_xiugai_02_left{width:180px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_19.png"/> 兑换卡列表
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
												<a href="javascript:" onclick="selectChannel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChangeChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
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
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<!--<div class="splist_up_01_left_kucun layui-form">-->
     <!--               	<input type="checkbox" name="hebing" lay-filter="hebing" lay-skin="primary" checked="true" title="将兑换卡合并">-->
     <!--               </div>-->
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入兑换卡名称"/>
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
														<input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入兑换卡名称/编码/规格/关键字/条形码"/>
													</div>
													<div class="gaojisousuo_left">
														兑换卡分类
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
														兑换卡品牌
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
														兑换卡标签
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
												<!-- <li>
													<div class="gaojisousuo_left">
														所属仓库
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_cangkus_all" lay-skin="primary" lay-filter="cangkus" title="全选" checked />
														<input type="checkbox" name="super_cangkus" pid="cangkus" lay-skin="primary" lay-filter="nocangkus" value="仓库1" title="仓库1" />
														<input type="checkbox" name="super_cangkus" pid="cangkus" lay-skin="primary" lay-filter="nocangkus" value="仓库2" title="仓库2" />
													</div>
													<div class="clearBoth"></div>
												</li> -->
												<li>
													<div class="gaojisousuo_left">
														促销状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="super_cuxiao" value="0" checked title="全部"/><input type="radio" name="super_cuxiao" value="1" title="促销中" /><input type="radio" name="super_cuxiao" value="-1" title="正常"/> 
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														兑换卡状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_status" value="0" title="全部" checked /><input type="radio" name="sunper_status" value="1" title="上架" /><input type="radio" name="sunper_status" value="-1" title="下架"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														兑换卡来源
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="sunper_source" value="0" title="全部" checked/><input type="radio" name="sunper_source" value="1" title="手动新增"/><input type="radio" name="sunper_source" value="2" title="批量导入"/>
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
						    <? chekurl($arr,'<a href="?&m=system&s=change&a=add" class="splist_add">新 增</a>') ?>
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
					    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=shangjia" onclick="shangjia();"><img src="images/biao_26.png"/> 上架</a>') ?>
					    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=xiajia" onclick="xiajia();"><img src="images/biao_27.png"/> 下架</a>') ?>
						<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=change&a=del" onclick="delChange();"><img src="images/biao_28.png"/> 删除</a>') ?>
						<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product&a=setTags" id="setTags" ><img src="images/biao_29.png"/> 设置标签</a>') ?>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext1(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<? chekurl($arr,'<li><a href="javascript:" _href="?&m=system&s=change&a=add" onclick="edit_anli()"><img src="images/biao_31.png"> 编辑</a></li>') ?>
			            <?chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=change&a=product" onclick="bind_gift()"><img src="images/biao_117.png">绑定商品</a></li>') ?>
			            <?chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=change&a=card" onclick="showCard()"><img src="images/biao_96.png">卡列表</a></li>') ?>
			            
						<? chekurl($arr,'<li><a href="javascript:" _href="?&m=system&s=change&a=del" onclick="z_confirm(\'确定要删除该兑换卡吗？\',del_anli,\'\');"><img src="images/biao_32.png"> 删除</a></li>') ?>
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
	<input type="hidden" id="cuxiao" value="<?=$cuxiao?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="hebing" value="1">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  ,active = {
		  	setTags:function(){
				layer.open({
					type: 1
					,title: false
					,closeBtn: false
					,area: '530px;'
					,shade: 0.3
					,id: 'LAY_layuipro'
					,btn: ['提交', '取消']
					,yes: function(index, layero){
						return false;
					}
					,btnAlign: 'r'
					,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
					'<form action="#" class="layui-form" method="post" id="setTagsForm"><div class="spxx_shanchu_tanchu_01">'+
					'<div class="spxx_shanchu_tanchu_01_left">'+'设置兑换卡标签'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
					'</div>'+
					'<div class="clearBoth"></div>'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_02">'+
					'<div class="jiliang_tanchu">'+
					'<div style="float:left;width:80px;line-height:42px;">设置标签：</div><div style="float:left;width:350px;line-height:40px;">'+
					<? if(!empty($tagsarry)){
						foreach ($tagsarry as $tag){
						echo '\'<input type="checkbox" name="piliang_tags" lay-skin="primary" title="'.$tag.'" value="'.$tag.'" />\'+';	
						}
					}?>
					'</div>'+
					'</div>'+
					'</form></div>'
					,success: function(layero){
						form.render('checkbox');
						var btn = layero.find('.layui-layer-btn');
						btn.find('.layui-layer-btn0').attr({
							href: 'javascript:setAllTags();'
						});
						return false;
					}
				});
			}
		  }
		  laydate.render({elem: '#e_baozhiqi'});
		  form.on('submit(tijiao)',function(data){
		  	var pdtId = getPdtId();
		  	var baozhiqi = $("#e_baozhiqi").val();
		  	var baozhiqi_days = $("#e_days").val();
		  	var fanwei = $("#e_fanwei option:selected").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=setBaozhiqi",
				data: "&id="+pdtId+"&baozhiqi="+baozhiqi+"&baozhiqi_days="+baozhiqi_days+"&fanwei="+fanwei,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#baozhiqi_xiugai").hide();
						reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('submit(tijiao1)',function(data){
		  	var pdtId = getPdtId();
		  	var orders = $("#e_orders").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=setorders",
				data: "id="+pdtId+"&orders="+orders,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#orders_xiugai").hide();
						reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('submit(tijiao2)',function(data){
		  	var pdtId = getPdtId();
		  	var views = $("#e_views").val();
		  	layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=setviews",
				data: "id="+pdtId+"&views="+views,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					if(resdata.code==1){
						layer.closeAll('loading');
						$("#orders_xiugai1").hide();
						//reloadTable(1);
						layer.msg('操作成功');
					}else{
						layer.closeAll('loading');
						layer.msg(resdata.message, {icon: 5});
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		  	return false;
		  });
		  form.on('checkbox(hebing)',function(data){
		  	if(data.elem.checked==false){
		  		$("#hebing").val(0);
		  	}else{
		  		$("#hebing").val(1);
		  	}
		  	reloadTable();
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=change&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	channelId:<?=$channelId?>,
		    	brandId:<?=$brandId?>,
		    	status:<?=$status?>,
		    	keyword:'<?=$keyword?>',
		    	tags:'<?=$tags?>',
		    	source:<?=$source?>,
		    	cuxiao:<?=$cuxiao?>,
		    	hebing:1
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  $("th[data-field='id']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var channelId = $("#channelId").val();
		  	var brandId = $("#brandId").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var tags = $("#tags").val();
		  	var source = $("#source").val();
		  	var cuxiao = $("#cuxiao").val();
		  	var hebing = $("#hebing").val();
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
			      ,hebing:hebing
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
		  	url: "/erp_service.php?action=get_product_channels1",
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
		
		function edit_anli(params){
        	var pdtId = getPdtId();
        	var channelId = $("#channelId").val();
        	var brandId = $("#brandId").val();
        	var status = $("#s_status").val();
        	var keyword = $("#keyword").val();
        	var tags = $("#tags").val();
        	var source = $("#source").val();
        	var cuxiao = $("#cuxiao").val();
        	var page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = '?m=system&s=product&a=occasion&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	location.href="?m=system&s=change&a=add&id="+pdtId+"&url="+url;
        }
        
        function showCard(params)
        {
            if(params>0){
        		pdtId = params;
        	}else{
        		pdtId = getPdtId();
        	}
        	var channelId = $("#channelId").val();
        	var brandId = $("#brandId").val();
        	var status = $("#s_status").val();
        	var keyword = $("#keyword").val();
        	var tags = $("#tags").val();
        	var source = $("#source").val();
        	var cuxiao = $("#cuxiao").val();
        	var page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = '?m=system&s=change&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	
        	location.href="?m=system&s=change&a=card&id="+pdtId+"&url="+url;
        }
        
        function bind_gift(params){
        	if(params>0){
        		pdtId = params;
        	}else{
        		pdtId = getPdtId();
        	}
        	var channelId = $("#channelId").val();
        	var brandId = $("#brandId").val();
        	var status = $("#s_status").val();
        	var keyword = $("#keyword").val();
        	var tags = $("#tags").val();
        	var source = $("#source").val();
        	var cuxiao = $("#cuxiao").val();
        	var page = $("#page").val();
        	var order1 = $("#order1").val();
        	var order2 = $("#order2").val();
        	var url = '?m=system&s=change&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
        	url = encodeURIComponent(url);
        	
        	location.href="?m=system&s=change&a=product&id="+pdtId+"&url="+url;
        }
        
        function del_anli(params){
        	var pdtId = getPdtId();
        	layer.load();
        	ajaxpost=$.ajax({
        		type: "POST",
        		url: "?&m=system&s=change&a=del",
        		data: "&ids="+pdtId,
        		dataType:"json",timeout : 8000,
        		success: function(resdata){
        			if(resdata.code==1){
        				layer.closeAll('loading');
        				layer.msg('操作成功');
        				reloadTable(1);
        			}else{
        				layer.closeAll('loading');
        				layer.msg(resdata.message, {icon: 5});
        			}
        		},
        		error: function() {
        			layer.closeAll();
        			layer.msg('数据请求失败', {icon: 5});
        		}
        	});
        }
        
function loadZiChangeChannels(menuId,ceng,hasnext){
	var channelDiv = $(".splist_up_01_left_01_down");
	if($("#ziChannels"+ceng).length==0&&hasnext==1){
		var ulstr = '<ul id="ziChannels'+ceng+'"><div style="text-align:center;"><img src="images/loading.gif"></div></ul>';
		var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
		channelDiv.css("width",(nowWidth+200)+"px");
		channelDiv.append(ulstr);
	}else{
		if(ceng<4&&$("#ziChannels4").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels4").remove();
		}
		if(ceng<3&&$("#ziChannels3").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels3").remove();
		}
		if($("#ziChannels"+ceng).length>0&&hasnext==0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels"+ceng).remove();
		}else{
			$("#ziChannels"+ceng).html('<div style="text-align:center;"><img src="images/loading.gif"></div>');
		}
	}
	if(hasnext==1){
		ajaxpost=$.ajax({
			type: "POST",
			url: "/erp_service.php?action=get_zichange_channels",
			data: "&id="+menuId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				var listr = '';
				for(var i=0;i<resdata.items.length;i++){
					if(ceng<4){
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" onmouseenter="loadZiChangeChannels('+resdata.items[i].id+','+(ceng+1)+','+resdata.items[i].hasNext+');" class="allsort_01_tlte">'+resdata.items[i].title+(resdata.items[i].hasNext==1?' <span><img src="images/biao_24.png"></span>':'')+' </a></li>';
					}else{
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" class="allsort_01_tlte">'+resdata.items[i].title+'</a></li>';
					}
				}
				$("#ziChannels"+ceng).html(listr);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}
}

function delChange(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要删除选中的'+num+'个兑换卡吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=change&a=del",
			data: "&ids="+ids,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				if(resdata.code==1){
					layer.closeAll('loading');
					layer.msg('操作成功');
					reloadTable(1);
				}else{
					layer.closeAll('loading');
					layer.msg(resdata.message, {icon: 5});
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
        
        function showNext1(dom){
        	var top = $(dom).offset().top;
        	if(top+250>document.body.clientHeight){
        		top=top-100;
        	}else if(top+330>document.body.clientHeight){
        		top=top-130;
        	}
        	var width = parseInt($(dom).css("width"));
        	var right = (width/2)+35;
        	var nowIndex = $("#nowIndex").val();
        	var index = $(dom).parent().parent().parent().attr("data-index");
        	$("#operate_row").css({"top":(top-90)+"px","right":right+"px"});
        	if(nowIndex==index){
        		$("#operate_row").stop().slideToggle(250);
        	}else{
        		if($("#operate_row").css("display")=='none'){
        			$("#operate_row").stop().slideDown(250);
        		}
        	}
        	$("#nowIndex").val(index);
        	return false;
        }
	</script>
	<script type="text/javascript" src="js/product_list.js?v=1.2"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>