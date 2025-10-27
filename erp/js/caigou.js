var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
		hideTanchu("splist_up_01_left_03");
	});
	$('.splist_up_01_left_03_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$(this).next().slideToggle(200);
		stopPropagation(eve); 
	});
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
//选择状态
function selectZt(status,title){
	$("#s_status").val(status);
	$(".splist_up_01_left_03_up span").html(title);
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
		top=top-90;
	}

	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	status = nowTr.find("td[data-field='rukuStatus'] div span").eq(0).html();
	if(status=='已入库'){
		$("#operate_row li.ruku").hide();
		$("#operate_row li.tuihuo").show();
	}else if(status=='待入库'){
		$("#operate_row li.ruku").show();
		$("#operate_row li.tuihuo").hide();
	}else if(status=='部分入库'){
		$("#operate_row li.ruku").show();
		$("#operate_row li.tuihuo").show();
	}else{
		$("#operate_row li.ruku").hide();
		$("#operate_row li.tuihuo").hide();
	}
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
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
function edit(){
	location.href='?m=system&s=supplier&a=add&id='+getPdtId();
}
function detail(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = '?m=system&s=caigou&a=index&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	location.href='?m=system&s=caigou&a=detail&id='+getPdtId()+'&returnurl='+returnurl;
}
function tuihuo(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = '?m=system&s=caigou&a=index&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	location.href='?m=system&s=caigou_tuihuo&a=add&caigouId='+getPdtId()+'&returnurl='+returnurl;
}
function ruku(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = '?m=system&s=caigou&a=index&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	location.href='?m=system&s=caigou&a=ruku&id='+getPdtId()+'&returnurl='+returnurl;
}
function dayin(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = '?m=system&s=caigou&a=index&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	location.href='?m=system&s=caigou&a=detail&print=1&id='+getPdtId()+'&returnurl='+returnurl;
}
//获取当前选中的经销商Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
  	var status = $("#s_status").val();
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
			,status:status
			,keyword:keyword
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
function view_jilu(nowPage,jiluId){
  	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = '?m=system&s=caigou&a=index&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	location.href='?m=system&s=caigou&a=detail&id='+jiluId+'&returnurl='+returnurl;
}