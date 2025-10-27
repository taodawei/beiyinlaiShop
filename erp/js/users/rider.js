var nowIndexTime;
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
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
function selectStatus(status,title){
	$("#status").val(status);
	$(".splist_up_01_left_01_up").eq(0).find("span").html(title);
	reloadTable(0);
}
function reloadTable(curpage){
	var keyword = $("#keyword").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var status = $("#status").val();
	productListTalbe.reload({
		where: {
			order1: order1
			,order2: order2
			,status: status
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
//导出导入操作
function daochu(){
	var keyword = $("#keyword").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&keyword="+keyword);
}
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
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
	status = nowTr.find("td[data-field='status'] div").eq(0).html();
	if(status=='-1'){
		$("#tongguo_btn").show();
		$("#bohui_btn").hide();
	}else{
		$("#tongguo_btn").hide();
		$("#bohui_btn").show();
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
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
function bohui(){
	layer.load();
	jiluId = getPdtId();
	$.ajax({
		type: "POST",
		url: "?s=peisongyuan&a=bohuiRider",
		data: "id="+jiluId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function shenhe(){
	layer.load();
	jiluId = getPdtId();
	$.ajax({
		type: "POST",
		url: "?s=peisongyuan&a=sheheRider",
		data: "id="+jiluId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function del(){
	layer.load();
	jiluId = getPdtId();
	$.ajax({
		type: "POST",
		url: "?s=peisongyuan&a=delRider",
		data: "id="+jiluId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function edit(){
	jiluId = getPdtId();
	location.href="?s=peisongyuan&a=add_rider&id="+jiluId;
}