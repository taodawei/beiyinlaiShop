<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"sn"=>array("title"=>"商品编码","rowCode"=>"{field:'sn',title:'商品编码',width:200}"),
				"title"=>array("title"=>"商品名称","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"key_vals"=>array("title"=>"商品规格","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}")
			);
$rowsJS = "{type:'checkbox'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$id = (int)$request['id'];
$supplier = $db->get_row("select id,title,name,phone,pdts from demo_supplier where id=$id and comId=$comId");
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
		body{background:#fff;}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		.splist_up_02_3 img{position:relative;top:-3px;}
		.white-paper-wrapper {margin: 0 auto;width: 300px;text-align: center;padding-top: 100px;padding-bottom: 100px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="?m=system&s=supplier&a=gonghuo&id=<?=$id?>&url=<?=urlencode($request['url'])?>"><img src="images/back.gif" /></a>&nbsp;&nbsp;<?=$supplier->title?>
		<div style="display:inline-block;margin-left:50px;"><span style="padding:0 23px 0;color:#333"><?=$supplier->name?></span><span style="color:#333"><?=$supplier->phone?></span></div>
	</div>
	<div class="right_down" style="padding-bottom:0px;<? if(empty($supplier->pdts)){?>border-top:2px solid #d7ebf5<? }?>">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01" style="float:left;">
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
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码/规格/关键字"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
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
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<div class="sprukuadd_05" style="margin-left:10px;">
					<form class="layui-form">
						<button class="layui-btn" lay-submit="" lay-filter="tijiao">添 加</button>
						<button class="layui-btn layui-btn-primary" onclick="location.href='?m=system&s=supplier&a=gonghuo&id=<?=$id?>&url=<?=urlencode($request['url'])?>';return false;">取 消</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="channelId" value="<?=$channelId?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laypage','table','form'], function(){
		  var laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-190"
		    ,url: '?m=system&s=supplier&a=getGonghuoList1&id=<?=$id?>'
		    ,page: true
		    ,limit:30
		    ,cols: [[<?=$rowsJS?>]]
		    ,done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  $("th[data-field='id']").hide();
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
		  form.on("submit(tijiao)",function(){
		  	var selectedIds = $("#selectedIds").val();
		  	if(selectedIds==''){
		  		layer.msg("请先选择任何产品！",function(){});
		  		return false;
		  	}
		  	layer.load();
            $.ajax({
                type: "POST",
                url: "?m=system&s=supplier&a=addGonghuoPdts&id=<?=$id?>",
                data: "selectedIds="+selectedIds,
                dataType:"json",timeout : 30000,
                success: function(resdata){
                    if(resdata.code==1){
                        location.href='?m=system&s=supplier&a=gonghuo&id=<?=$id?>&url=<?=urlencode($request['url'])?>';
                    }else{
                        layer.closeAll();
                        layer.msg(resdata.message,function(){});
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.closeAll();
                    layer.msg('数据请求失败,请重试', {icon: 5});
                }
            });
		  });
		});
	</script>
	<script type="text/javascript" src="js/supplier_gonghuo.js"></script>
	<? require('views/help.html');?>
</body>
</html>