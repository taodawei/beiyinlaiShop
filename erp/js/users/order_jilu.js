var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){
		$("#riqilan").slideUp(200);
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
function select_type(type){
	$("#type").val(type);
	reloadTable(0);
}
function selectTime(startTime,endTime){
	$("#s_time1").html(startTime);
	$("#s_time2").html(endTime);
	$("#startTime").val(startTime);
	$("#endTime").val(endTime);
	reloadTable(0);
}
function reloadTable(curpage){
	layer.load();
  	var keyword = $("#keyword").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var money_start = $("#money_start").val();
  	var money_end = $("#money_end").val();
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
			,keyword:keyword
			,startTime:startTime
			,endTime:endTime
			,money_start:money_start
			,money_end:money_end
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}
function view_order(id,userId){
  	var keyword = $("#keyword").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var money_start = $("#money_start").val();
  	var money_end = $("#money_end").val();
	var page = $("#page").val();
	var returnurl = "?m=system&s=users&a=order_jilu&id="+id+"&keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime+"&money_start="+money_start+"&money_end="+money_end+"&page="+page;
	returnurl = encodeURIComponent(returnurl);
	var url = $("#url").val();
	location.href="?m=system&s=order&a=view&id="+id+"&returnurl="+returnurl+"&url="+url;
}