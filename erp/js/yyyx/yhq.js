var nowIndexTime;
$(document).ready(function(){
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
function updataStatus(status,index){
	$("#status").val(status);
	$(".shengriquan_down_1_left_on").removeClass('shengriquan_down_1_left_on');
	$(".shengriquan_down_1_left a").eq(index).addClass('shengriquan_down_1_left_on');
	reloadTable(0);
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-50;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = parseInt(nowTr.find("td[data-field='status'] div").eq(0).text());
	$("#operate_row").css({"top":(top+20)+"px","right":right+"px"});
	if(status==0){
		$("#operate_edit").hide();
		$("#operate_jinyong").hide();
	}else{
		$("#operate_edit").show();
		$("#operate_jinyong").show();
	}
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
function view(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var type = $("#type").val();
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=yyyx&a=yhq&type="+type+"&status="+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=yyyx&a=viewYhq&id="+pdtId+"&returnurl="+url;
}
function edit(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var type = $("#type").val();
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=yyyx&a=yhq&type="+type+"&status="+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	type = type==1?'':type;
	location.href="?s=yyyx&a=add_yhq"+type+"&id="+pdtId+"&type="+type+"&returnurl="+url;
}
function shixiao(){
	pdtId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?s=yyyx&a=yhq_shixiao",
		data: "id="+pdtId,
		dataType:'text',timeout : 8000,
		success: function(resdata){
			layer.closeAll();
			layer.msg("操作成功",{icon:1});
			reloadTable(1);
		},
        error: function() {
        	layer.closeAll();
            layer.msg('超时，请重试', {icon: 5});
        }
	});
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var type = $("#type").val();
	var status = $("#status").val();
	var keyword = $("#keyword").val();
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
			,type:type,
			status:status,
			keyword:keyword,
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}