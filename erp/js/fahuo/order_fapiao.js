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
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectkaipiao(status,title){
	$("#kaipiao").val(status);
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
	var height = parseInt($("#operate_row").css("height"));
	if(top+height>document.body.clientHeight){
		top=top-height;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = parseInt(nowTr.find("td[data-field='status'] div").eq(0).text());
	if(status==2){
		return false;
	}
	$("#operate_row").css({"top":(top-92)+"px","right":right+"px"});
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
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function kaipiao(){
	jiluId = getPdtId();
	layer.confirm('确定将该订单设置为已开票吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?s=order&a=kaipiao",
			data: "jiluId="+jiluId,
			dataType:'json',timeout:30000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功',{icon:1});
					reloadTable(1);
				}
			},
			error: function(){
				layer.closeAll();
				layer.msg('网络错误，请检查网络',{icon:5});
			}
		});
	});
}
function bukaipiao(){
	jiluId = getPdtId();
	layer.confirm('确定将该订单设置为不开发票吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?s=order&a=bukaipiao",
			data: "jiluId="+jiluId,
			dataType:'json',timeout:30000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功',{icon:1});
					reloadTable(1);
				}
			},
			error: function(){
				layer.closeAll();
				layer.msg('网络错误，请检查网络',{icon:5});
			}
		});
	});
}
function daochu(){
	var scene = $("#scene").val();
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var moneystart = $("#moneystart").val();
	var moneyend = $("#moneyend").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var kaipiao = $("#kaipiao").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&scene="+scene+"&status="+status+"&orderId="+orderId+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&kehuName="+kehuName+"&moneystart="+moneystart+"&moneyend="+moneyend+"&shouhuoInfo="+shouhuoInfo+"&pdtInfo="+pdtInfo+"&payStatus="+payStatus+"&kaipiao="+kaipiao);
}
function reloadTable(curpage){
	var scene = $("#scene").val();
	var status = $("#status").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var moneystart = $("#moneystart").val();
	var moneyend = $("#moneyend").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var kaipiao = $("#kaipiao").val();
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
			,scene:scene
			,status:status
			,orderId:orderId
			,startTime:startTime
			,keyword:keyword
			,endTime:endTime
			,kehuName:kehuName
			,moneystart:moneystart
			,moneyend:moneyend
			,shouhuoInfo:shouhuoInfo
			,kaipiao:kaipiao
			,pdtInfo:pdtInfo
			,payStatus:payStatus
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}
function pi_shenhe(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择订单！",function(){});
		return false;
	}
	layer.confirm('确定要将选中订单设为已开票吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=order&a=pi_yikaipiao",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功，共修改'+resdata.num+"个订单");
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
function pi_fapiao(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择订单！",function(){});
		return false;
	}
	layer.confirm('确定要将选中的订单设置为不开发票吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=order&a=pi_fapiao",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功，共修改'+resdata.num+"个订单");
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