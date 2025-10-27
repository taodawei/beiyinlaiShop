<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
	"id"=>array("title"=>"反馈ID","rowCode"=>"{field:'id',title:'反馈ID',width:80}"),
	"name"=>array("title"=>"姓名","rowCode"=>"{field:'name',title:'姓名',width:100}"),
	"phone"=>array("title"=>"手机号","rowCode"=>"{field:'phone',title:'手机号',width:100}"),
	"content"=>array("title"=>"内容","rowCode"=>"{field:'content',title:'内容',width:580}"),
	"feed_type"=>array("title"=>"类型","rowCode"=>"{field:'feed_type',title:'类型',width:150}"),
	"dtTime"=>array("title"=>"提交时间","rowCode"=>"{field:'dtTime',title:'提交时间',width:150}")
);
$rowsJS = "{field: 'status', title: '状态', width:0, sort: false,style:\"display:none;\"},{field: 'zhishangId', title: 'zhishangId', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$level = (int)$request['level'];
$mendianId = (int)$request['mendianId'];
$keyword = $request['keyword'];
$money_start = $request['money_start'];
$money_end = $request['money_end'];
$jifen_start = $request['jifen_start'];
$jifen_end = $request['jifen_end'];
$dtTime_start = $request['dtTime_start'];
$dtTime_end = $request['dtTime_end'];
$login_start = $request['login_start'];
$login_end = $request['login_end'];
$channelId = $request['channel_id'] ? $request['channel_id'] : '';
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['userPageNum'])?10:$_COOKIE['userPageNum'];
$channelStr = $db->get_var("select group_concat(feed_type) from feedback_log where feed_type <> '' ");

$channels = explode(',', $channelStr);
//$levels = $db->get_results("select id,title from user_level where comId=$comId order by ordering desc,id asc");
//$mendians = $db->get_results("select id,title from mendian where comId=$comId order by id asc");
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
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_m.png"/> 反馈信息列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
				    	<div class="splist_up_01_left">
						<div class="splist_up_01_left_01">
							<div class="splist_up_01_left_01_up">
								<span>全部类型</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_01_down">
								<ul style="border-left:0px" id="ziChannels1">
									<li class="allsort_01">
										<a href="javascript:selectChannel(0,'全部类型');">全部类型</a>
									</li>
								    <?
									if(!empty($channels)){
										foreach ($channels as $channel) {
											?>
											<li class="allsort_01"><a href="javascript:" onclick="selectChannel('<?=$channel?>','<?=$channel?>');" class="allsort_01_tlte"><?=$channel?></a></li>
											<?
										}
									}
									?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="display:none;">
							<div class="splist_up_01_left_02_up">
								<span>全部仓库</span> <img src="images/biao_20.png"/>
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
						<div class="splist_up_01_left_kucun layui-form" style="display:none;">
                        	<input type="checkbox" name="hebing" lay-filter="hebing" lay-skin="primary" title="将商品合并">
                        </div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入手机号"/>
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
				<!-- <div class="splist_up_02">
					<div class="splist_up_02_1">
						<img src="images/biao_25.png"/>
					</div>
					<div class="splist_up_02_2">
						已选择 <span id="selectedNum">0</span> 项
					</div>
					<div class="splist_up_02_3">
					
					</div>
					<div class="clearBoth"></div>
				</div> -->
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
							<a href="javascript:view1();"><img src="images/biao_30.png"> 详情</a>
						</li>
						<!--<li id="huifuBtn">-->
						<!--	<a href="javascript:" onclick="comment_huifu();"><img src="images/biao_31.png">标记回复</a>-->
						<!--</li>-->
						<!--<li id="operate_jinyong">-->
						<!--	<a href="javascript:z_confirm('禁用后该总代理名下的成员都将归属到平台，并且该总代理不能再申请成为总代理，确定要禁用该总代理吗？',jin_user,'');"><img src="images/biao_120.png"> 禁用</a>-->
						<!--</li>-->
						<!--<li id="operate_qiyong">-->
						<!--	<a href="javascript:z_confirm('确定要允许该会员重新申请总代理吗？',qiyong_user,'');"><img src="images/biao_888.png"> 允许申请</a>-->
						<!--</li>-->
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="level" value="<?=$level?>">
	<input type="hidden" id="mendianId" value="<?=$mendianId?>">
	<input type="hidden" id="money_start" value="<?=$money_start?>">
	<input type="hidden" id="money_end" value="<?=$money_end?>">
	<input type="hidden" id="jifen_start" value="<?=$jifen_start?>">
	<input type="hidden" id="jifen_end" value="<?=$jifen_end?>">
	<input type="hidden" id="dtTime_start" value="<?=$dtTime_start?>">
	<input type="hidden" id="dtTime_end" value="<?=$dtTime_end?>">
	<input type="hidden" id="login_start" value="<?=$login_start?>">
	<input type="hidden" id="login_end" value="<?=$login_end?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="type" value="<?=$type?>">
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
					'<div class="spxx_shanchu_tanchu_01_left">'+'发放抵扣金'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
					'</div>'+
					'<div class="clearBoth"></div>'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_02">'+
					'<div class="jiliang_tanchu">'+
					'<div style="float:left;width:80px;line-height:42px;">发放金额：</div><div style="float:left;width:350px;line-height:40px;">'+
					'<input type="number" id="money" style="width:60px;height:25px;padding-left:5px;">'+
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
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  laydate.render({
            elem: '#time1'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($dtTime_start)?'':",value:'$dtTime_start'"?>
            ,done: function(value, date, endDate){
                $("#time1").html(value);
            }
          });
          laydate.render({
            elem: '#time2'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($dtTime_end)?'':",value:'$dtTime_end'"?>
            ,done: function(value, date, endDate){
                $("#time2").html(value);
            }
          });
          laydate.render({
            elem: '#time3'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($login_start)?'':",value:'$login_start'"?>
            ,done: function(value, date, endDate){
                $("#time3").html(value);
            }
          });
          laydate.render({
            elem: '#time4'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($login_end)?'':",value:'$login_end'"?>
            ,done: function(value, date, endDate){
                $("#time4").html(value);
            }
          });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=users&a=getFeedBackList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	level:'<?=$level?>',
		    	mendianId:'<?=$mendianId?>',
		    	keyword:'<?=$keyword?>',
		    	money_start:'<?=$money_start?>',
		    	money_end:'<?=$money_end?>',
		    	jifen_start:'<?=$jifen_start?>',
		    	jifen_end:'<?=$jifen_end?>',
		    	dtTime_start:'<?=$dtTime_start?>',
		    	dtTime_end:'<?=$dtTime_end?>',
		    	login_start:'<?=$login_start?>',
		    	login_end:'<?=$login_end?>',
		    	type:'<?=$type?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='status']").hide();
		    	$("th[data-field='zhishangId']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  table.on('sort(product_list)', function(obj){
		  	var level = $("#level").val();
		  	var mendianId = $("#mendianId").val();
		  	var keyword = $("#keyword").val();
		  	var money_start = $("#money_start").val();
		  	var money_end = $("#money_end").val();
		  	var jifen_start = $("#jifen_start").val();
		  	var jifen_end = $("#jifen_end").val();
		  	var dtTime_start = $("#dtTime_start").val();
		  	var dtTime_end = $("#dtTime_end").val();
		  	var login_start = $("#login_start").val();
		  	var login_end = $("#login_end").val();
		  	var type = $("#type").val();
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
			      ,level:level,
			      mendianId:mendianId,
			      keyword:keyword,
			      money_start:money_start,
			      money_end:money_end,
			      jifen_start:jifen_start,
			      jifen_end:jifen_end,
			      dtTime_start:dtTime_start,
			      dtTime_end:dtTime_end,
			      login_start:login_start,
			      login_end:login_end,
			      type:type
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='status']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		  form.on('submit(search)', function(data){
		  	$("#money_start").val(data.field.super_money_start);
		  	$("#money_end").val(data.field.super_money_end);
		  	$("#jifen_start").val(data.field.super_jifen_start);
		  	$("#jifen_end").val(data.field.super_jifen_end);
		  	$("#dtTime_start").val(data.field.super_dtTime_start);
		  	$("#dtTime_end").val(data.field.super_dtTime_end);
		  	$("#login_start").val(data.field.super_login_start);
		  	$("#login_end").val(data.field.super_login_end);
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
		    			ids = data[i].zhishangId;
		    		}else{
		    			ids = ids+','+data[i].zhishangId;
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
		
		//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function view1(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=users&a=report";
	url = encodeURIComponent(url);
	location.href="?m=system&s=users&a=feedbackDetail&id="+pdtId+"&returnurl="+url;
}

function comment_huifu(){
	var ids = getPdtId();
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $.trim($("#e_beizhu").val());
			if(beizhu==''){
				layer.msg("请输入回复内容",function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=users&a=feedback_huifui",
				data: "ids="+ids+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						reloadTable(1);
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">备注</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="请输入回复内容"></textarea>'+
		'</div>'+
		'</div>'
	});
}

	</script>
	<script type="text/javascript" src="js/users_tuanzhang.js?v=1.1"></script>
		<script type="text/javascript" src="js/kucun_list.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>