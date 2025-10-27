var nowIndexTime;
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//分类
	$('.splist_up_01_left_01_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_01_down').slideToggle(200);
		stopPropagation(eve); 
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
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
	$(".splist_up_02_1").click(function(){
		$(".splist_up_02").hide();
		$(".splist_up_01").show();
	});
	
});
//选择上下架状态
function selectStatus(status,title){
	$("#s_status").val(status);
	$(".splist_up_01_left_02_up").eq(0).find("span").html(title);
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
		top=top-50;
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
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}

function edit_product(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var zindex = $("#nowIndex").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=yushou&status='+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=yushou&a=fahuo&inventoryId="+pdtId+"&kaipiao=1&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
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