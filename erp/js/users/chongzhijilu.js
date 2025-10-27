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
function chongzhi(eve,userId){
	location.href="?m=system&s=users&a=chongzhi&id="+userId;
	stopPropagation(eve);
}
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
  	var type = 2;
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var keyword = $("#keyword").val();
  	var pay_type = $("#pay_type option:selected").val();
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
			,type:type
			,pay_type:pay_type
			,keyword:keyword
			,startTime:startTime
			,endTime:endTime
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}