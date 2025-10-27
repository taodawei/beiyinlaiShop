var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_02_down').slideToggle(200);
		stopPropagation(eve); 
	});
	//高级搜索
	$('.splist_up_01_right_2_up').click(function(){
		$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});
	});
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
	$(".splist_up_02_1").click(function(){
		$(".splist_up_02").hide();
		$(".splist_up_01").show();
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
//批量操作
function shenhe_order(){
	var ids = $("#selectedIds").val();
	
}
function shenhe_caiwu(){
	var ids = $("#selectedIds").val();
	
}
function shenhe_chuku(){
	var ids = $("#selectedIds").val();
	
}
function shenhe_fahuo(){
	var ids = $("#selectedIds").val();
	
}
function daochu(){
	var ids = $("#selectedIds").val();
}
//自定义字段点击上下箭头调整
function rowToUp(row){
	var nowli = $(row).parent();
	nowli.prev().before(nowli);
	if(nowli.index()==1){
		nowli.find(".rowtoup").remove();
		nowli.next().append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
	}
}
function rowToDown(row){
	var nowli = $(row).parent();
	nowli.next().after(nowli);
	if(nowli.index()==2){
		nowli.append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
		nowli.prev().find(".rowtoup").remove();
	}
}
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectStatus(status,title){
	$("#s_status").val(status);
	$(".splist_up_01_left_02_up span").html(title);
	reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-80;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = nowTr.find("td[data-field='status'] div span").eq(0).text();
	var payStatus = nowTr.find("td[data-field='payStatus'] div").eq(0).text();
	if(status.indexOf("审核")>0){
		$("#sheheBtn").show();
	}else{
		$("#sheheBtn").hide();
	}
	if(payStatus=='已付款'){
		$("#shoukuanBtn").hide();
	}else{
		$("#shoukuanBtn").show();
	}
	if(status=='已驳回'){
		$("#sheheBtn").hide();
		$("#shoukuanBtn").hide();
	}
	$("#operate_row").css({"top":(top-146)+"px","right":right+"px"});
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
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
//浏览详情
function view(){
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var level = $("#level").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var departId = $("#departId").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var tags = $("#tags").val();
	var orderType = $("#orderType").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=dinghuo&status="+status+"&orderId="+orderId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&kehuName="+kehuName+"&level="+level+"&shouhuoInfo="+shouhuoInfo+"&departId="+departId+"&pdtInfo="+pdtInfo+"&payStatus="+payStatus+"&tags="+tags+"&orderType="+orderType+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=dinghuo&a=detail&id="+getPdtId()+"&returnurl="+url;
}
function fahuoInfo(jiluId){
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var level = $("#level").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var departId = $("#departId").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var tags = $("#tags").val();
	var orderType = $("#orderType").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=dinghuo&status="+status+"&orderId="+orderId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&kehuName="+kehuName+"&level="+level+"&shouhuoInfo="+shouhuoInfo+"&departId="+departId+"&pdtInfo="+pdtInfo+"&payStatus="+payStatus+"&tags="+tags+"&orderType="+orderType+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=dinghuo&a=chuku&id="+jiluId+"&returnurl="+url;
}
//添加收款记录
function addShoukuan(){
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var level = $("#level").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var departId = $("#departId").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var tags = $("#tags").val();
	var orderType = $("#orderType").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=dinghuo&status="+status+"&orderId="+orderId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&kehuName="+kehuName+"&level="+level+"&shouhuoInfo="+shouhuoInfo+"&departId="+departId+"&pdtInfo="+pdtInfo+"&payStatus="+payStatus+"&tags="+tags+"&orderType="+orderType+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=dinghuo&a=shoukuan&id="+getPdtId()+"&returnurl="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var level = $("#level").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var departId = $("#departId").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var tags = $("#tags").val();
	var orderType = $("#orderType").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	productListTalbe.reload({
		where: {
			order1: order1
			,order2: order2
			,status:status
			,orderId:orderId
			,startTime:startTime
			,keyword:keyword
			,endTime:endTime
			,kehuName:kehuName
			,level:level
			,shouhuoInfo:shouhuoInfo
			,departId:departId
			,pdtInfo:pdtInfo
			,payStatus:payStatus
			,tags:tags
			,orderType:orderType
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
//自定义字段相关
function showRowset(){
	$("#bg").show();
	$("#xianshiziduan").show().animate({"right":"0px"},300);
}
function hideRowset(){
	$("#xianshiziduan").animate({"right":"-259px","display":"none"},300);
	$("#bg").hide();
}
function view_jilu(nowPage,jiluId){
  	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var level = $("#level").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var departId = $("#departId").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var tags = $("#tags").val();
	var orderType = $("#orderType").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=dinghuo&status="+status+"&orderId="+orderId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&kehuName="+kehuName+"&level="+level+"&shouhuoInfo="+shouhuoInfo+"&departId="+departId+"&pdtInfo="+pdtInfo+"&payStatus="+payStatus+"&tags="+tags+"&orderType="+orderType+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=dinghuo&a=detail&id="+jiluId+"&returnurl="+url;
}
function shenhe_order(){
	var ids = $("#selectedIds").val();
	layer.confirm('确定要审核通过选中的订货单吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=dinghuo&a=piliang_shenhe",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功');
					$("#selectedIds").val('');
					$(".splist_up_01").show();
					$(".splist_up_02").hide();
					reloadTable(1);
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
function shenhe_caiwu(){
	var ids = $("#selectedIds").val();
	layer.confirm('确定要审核通过选中的订货单吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=dinghuo&a=piliang_caiwu",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功');
					$("#selectedIds").val('');
					$(".splist_up_01").show();
					$(".splist_up_02").hide();
					reloadTable(1);
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