<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"title"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'title',title:'".$kehu_title."名称',width:200,sort:true}"),
				"sn"=>array("title"=>$kehu_title."编码","rowCode"=>"{field:'sn',title:'".$kehu_title."编码',width:200,sort:true}"),
				"username"=>array("title"=>"登录账号","rowCode"=>"{field:'username',title:'登录账号',width:200}"),
				"areaName"=>array("title"=>"地区","rowCode"=>"{field:'areaName',title:'地区',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"level"=>array("title"=>"级别","rowCode"=>"{field:'level',title:'级别',width:100}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:100,sort:true}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:120,sort:true}"),
				"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:140,sort:true}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:100}")
			);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$level = (int)$request['level'];
$keyword = $request['keyword'];
$uname = $request['uname'];
$areaId = (int)$request['areaId'];
$status = (int)$request['status'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['kehuPageNum'])?10:$_COOKIE['kehuPageNum'];
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$firstId=0;
$secondId=0;
$thirdId=0;
if($areaId>0){
    $area = $db->get_row("select * from demo_area where id=".$areaId);
    if($area->parentId==0){
        $firstId = $area->id;
    }else{
        $firstId = $area->parentId;
        $secondId = $area->id;
        $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
        if($farea->parentId!=0){
            $firstId = $farea->parentId;
            $secondId = $farea->id;
            $thirdId=$area->id;
        }
    }
}
$areas = $db->get_results("select * from demo_area where parentId=0");
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
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> <?=$kehu_title?>列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部级别</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectLevel(0,'全部级别');" class="splist_up_01_left_02_down_on">全部级别</a>
									</li>
									<? if(!empty($levels)){
										foreach ($levels as $c) {
											?>
											<li class="allsort_01">
												<a href="javascript:" onclick="selectLevel(<?=$c->id?>,'<?=$c->title?>');" class="allsort_01_tlte"><?=$c->title?></a>
											</li>
											<?
										}
										?><?
									}?>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/编码/联系人/手机"/>
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
														<input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入<?=$kehu_title?>名称/编码/联系人/手机"/>
													</div>
													<div class="gaojisousuo_left">
														业务员
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_uname" class="gaojisousuo_right_input" placeholder="请输入业务员姓名"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														<?=$kehu_title?>级别
													</div>
													<div class="gaojisousuo_right">
														<select name="super_level" id="super_level" lay-search>
															<option value="">选择级别</option>
															<? if(!empty($levels)){
																foreach ($levels as $l) {
																	?>
																	<option value="<?=$l->id?>"><?=$l->title?></option>
																	<?
																}
															}?>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														区域
													</div>
													<div class="gaojisousuo_right">
														<div style="width:32%;display:inline-block;">
															<input type="hidden" name="psarea" id="psarea" value="<?=$supplier->areaId?>" />
															<select id="ps1" lay-filter="ps1">
																<option value="">选择省份</option>
																<?if(!empty($areas)){
																	foreach ($areas as $hangye) {
																		?><option value="<?=$hangye->id?>" <?=($hangye->id==$firstId?'selected="selected"':'')?>><?=$hangye->title?></option><?
																	}
																}?>
															</select>
														</div>
														<div style="width:32%;display:inline-block;">
															<select id="ps2" lay-filter="ps2"><option value="">请先选择省</option>
																<?
																if($firstId>0){
																	$areas = $db->get_results("select id,title from demo_area where parentId=$firstId");
																	if(!empty($areas)){
																		foreach ($areas as $hangye) {?>
																		<option value="<?=$hangye->id?>" <?=($hangye->id==$secondId?'selected="selected"':'')?> ><?=$hangye->title?></option>
																		<?}
																	}
																}?>
															</select>
														</div>
														<div style="width:32%;display:inline-block;">
															<select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>
																<? if($secondId>0){
																	$areas = $db->get_results("select id,title from demo_area where parentId=$secondId");
																	if(!empty($areas)){
																		foreach ($areas as $hangye) {?>
																		<option value="<?=$hangye->id?>" <?=($hangye->id==$thirdId?'selected="selected"':'')?> ><?=$hangye->title?></option>
																		<?}
																	}
																}?>
															</select>
															<input type="hidden" name="super_areaId" id="super_areaId" value="<?=$areaId?>">
														</div>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														<?=$kehu_title?>状态
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="super_status" value="0" checked title="全部"/><input type="radio" name="super_status" value="1" title="已开通" /><input type="radio" name="super_status" value="2" title="未开通"/><input type="radio" name="super_status" value="-1" title="禁用"/> 
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
							<a href="?m=system&s=kehu&a=daochu" id="daochuA" target="_blank" onclick="daochu();" class="splist_daochu">导 出</a>
							<a href="?m=system&s=kehu&a=edit" class="splist_add">新 增</a>
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
						<a href="javascript:" onclick="shangjia();"><img src="images/biao_26.png"/> 批量开通</a>
						<a href="javascript:" onclick="xiajia();"><img src="images/biao_27.png"/> 批量禁用</a>
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
							<a href="javascript:edit_kehu();"><img src="images/biao_30.png"> 修改</a>
						</li>
						<li>
							<a href="javascript:baojiadan();"><img src="images/biao_95.png"> 报价单</a>
						</li>
						<li>
							<a href="javascript:zhanghu();"><img src="images/biao_96.png"> 账户</a>
						</li>
						<li>
							<a href="javascript:z_confirm('确定要删除该<?=$kehu_title?>吗？',del_kehu,'');"><img src="images/biao_32.png"> 删除</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="level" value="<?=$level?>">
	<input type="hidden" id="uname" value="<?=$uname?>">
	<input type="hidden" id="s_status" value="<?=$status?>">
	<input type="hidden" id="areaId" value="<?=$areaId?>">
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
		  form.on('select(ps1)',function(data){
		  	if(!isNaN(data.value)){
		  		layer.load();
		  		id = data.value;
		  		ajaxpost=$.ajax({
		  			type:"POST",
		  			url:"/erp_service.php?action=getAreas",
		  			data:"id="+id,
		  			timeout:"4000",
		  			dataType:"text",
		  			success: function(html){
		  				$("#ps3").html('<option value="">请先选择市</option>');
		  				if(html!=""){
		  					$("#ps2").html(html);
		  					$("#super_areaId").val(id);
		  				}else{
		  					$("#super_areaId").val(id);
		  				}
		  				form.render('select');
		  				layer.closeAll('loading');
		  			},
		  			error:function(){
		  				alert("超时,请重试");
		  			}
		  		});
		  	}            
		  });
		  form.on('select(ps2)',function(data){
		  	if(!isNaN(data.value)){
		  		layer.load();
		  		id = data.value;
		  		ajaxpost=$.ajax({
		  			type:"POST",
		  			url:"/erp_service.php?action=getAreas",
		  			data:"id="+id,
		  			timeout:"4000",
		  			dataType:"text",
		  			success: function(html){
		  				if(html!=""){
		  					$("#ps3").html(html);
		  					$("#super_areaId").val(id);
		  				}else{
		  					$("#super_areaId").val(id);
		  				}
		  				form.render('select');
		  				layer.closeAll('loading');
		  			},
		  			error:function(){
		  				alert("超时,请重试");
		  			}
		  		});
		  	}
		  });
		  form.on('select(ps3)',function(data){
		  	if(!isNaN(data.value)){
		  		$("#super_areaId").val(data.value);
		  	}
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=kehu&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	level:<?=$level?>,
		    	keyword:'<?=$keyword?>',
		    	uname:'<?=$uname?>',
		    	areaId:<?=$areaId?>,
		    	status:<?=$status?>
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  $("th[data-field='id']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var level = $("#level").val();
		  	var status = $("#s_status").val();
		  	var keyword = $("#keyword").val();
		  	var uname = $("#uname").val();
		  	var areaId = $("#areaId").val();
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
			      ,level:level
			      ,status:status
			      ,keyword:keyword
			      ,uname:uname
			      ,areaId:areaId
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
		  form.on('submit(search)', function(data){
		  	$("#keyword").val(data.field.super_keyword);
		  	$("#uname").val(data.field.super_uname);
		  	$("#level").val(data.field.super_level);
		  	if($("#ps1 option:selected").val()==''){
		  		$("#areaId").val('0');
		  	}else{
		  		$("#areaId").val(data.field.super_areaId);
		  	}
		  	$("#s_status").val(data.field.super_status);
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
		  $(function(){
		  	<? if($page>1){?>
		  	reloadTable(1);
		  	<? }?>
		  });
		});
	</script>
	<script type="text/javascript" src="js/kehu_list.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>