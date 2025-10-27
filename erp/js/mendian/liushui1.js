var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){
		$("#riqilan").slideUp(200);
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
	if(merchantNo!=''){
		$.ajax({
			type: "POST",
			url: "/yop-api/sendBalancequery.php",
			data: "merchantNo="+merchantNo,
			dataType : "text",timeout : 30000,
			success: function(data) {
				$("#yibao_yue").html('￥'+data);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	}else{
		$("#yibao_yue").html('￥0');
	}
	
});
function chongzhi(eve,userId){
	location.href="?s=mendian&a=chongzhi&id="+userId;
	stopPropagation(eve);
}
function chongzhi1(eve,userId){
	location.href="?s=mendian&a=chongzhi1&id="+userId;
	stopPropagation(eve);
}
/*function select_type(type){
	$("#type").val(type);
	reloadTable(0);
}*/
function selectTime(startTime,endTime){
	$("#s_time1").html(startTime);
	$("#s_time2").html(endTime);
	$("#startTime").val(startTime);
	$("#endTime").val(endTime);
	reloadTable(0);
}
function qiehuan_down(index1,index2,status){
	$("#type").val(index2);
	$("#status").val(status);
	$("#down_0").hide();
	$("#down_1").hide();
	$("#down_"+index1).show();
	reloadTable();
}
function reloadTable(curpage){
	layer.load();
  	var type = $("#type").val();
  	var status = $("#status").val();
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
	productListTalbe1.reload({
		where: {
			order1: order1
			,order2: order2
			,type:type
			,status:status
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
	productListTalbe2.reload({
		where: {
			order1: order1
			,order2: order2
			,type:type
			,status:status
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