$(document).ready(function(){
	getWanglais();
	$(document).bind('click',function(){
		$("#riqilan").slideUp(200);
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
	$(".mun1 li").click(function(){
		var nowId = parseInt($(this).find(".b_num1").eq(0).attr("id").replace('price',''));
		if(nowId>0&&nowId<7){
			$("#nowPage").val(nowId);
			reloadTable();
		}
	});
});
//获取往来账汇总
function getWanglais(){
	var supplierId = $("#supplierId").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	for(var i=1;i<8;i++){
		$("#price"+i).html('<img src="images/loading.gif" width="30">');
	}
	$.ajax({
        type: "POST",
        url: "/erp_service.php?action=get_supplier_wanglai",
        data: "id="+supplierId+"&startTime="+startTime+"&endTime="+endTime,
        dataType:"json",timeout : 30000,
        success: function(resdata){
            $("#price1").html(resdata.price1);
            $("#price2").html(resdata.price2);
            $("#price3").html(resdata.price3);
            $("#price4").html(resdata.price4);
            $("#price5").html(resdata.price5);
            $("#price6").html(resdata.price6);
            $("#price7").html(resdata.price7);
        },
        error: function() {
            layer.msg('数据请求失败', {icon: 5});
        }
    });
}
//重新加载iframe
function reloadTable(){
	var nowId = $("#nowPage").val();
	var supplierId = $("#supplierId").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var url = $("#url").val();
	$("#tableFrame").attr("src",'?m=system&s=supplier&a=wanglais'+nowId+'&id='+supplierId+'&startTime='+startTime+'&endTime='+endTime+'&url='+url);
}
function daochu(){
	var nowPage = $("#nowPage").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
	var href = $("#daochuA").attr("href");
	href = href.replace('a=daochuWanglais','1=');
	href = href+'&a=daochuWanglais'+nowPage;
	$("#daochuA").attr("href",href+"&startTime="+startTime+"&endTime="+endTime);
}
function gotoJiesuan(){
	var id = $("#supplierId").val();
	var nowPage = $("#nowPage").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var url = $("#url").val();
  	var returnurl = "?m=system&s=supplier&a=wanglais&id="+id+"&nowPage="+nowPage+"&startTime="+startTime+"&endTime="+endTime;
    returnurl = encodeURIComponent(returnurl);
    location.href="?m=system&s=supplier&a=jiesuan&id="+id+"&returnurl="+returnurl+"&url="+url;
}