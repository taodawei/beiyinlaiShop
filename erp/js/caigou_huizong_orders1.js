var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
		$("#riqilan").slideUp(200);
	});
	//分类
	$('.splist_up_01_left_01_up').click(function(eve){
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
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
//选择类型
function selectType(suppId,id){
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	if(id==1){
		location.href='?m=system&s=caigou_huizong&a=index&startTime='+startTime+'&endTime='+endTime;
	}else if(id==2){
		location.href='?m=system&s=caigou_huizong&a=orders2&startTime='+startTime+'&endTime='+endTime;
	}else{
		location.href='?m=system&s=caigou_huizong&a=orders3&startTime='+startTime+'&endTime='+endTime;
	}
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
		top=top-25;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	$("#operate_row").css({"top":(top-310)+"px","right":right+"px"});
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
function detail(action){
	var jiluId = getPdtId();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var supplierId = $("#supplierId").val();
	var returnurl = "?m=system&s=supplier&a="+action+"&id="+supplierId+"&startTime="+startTime+"&endTime="+endTime+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	var url = $("#url").val();
	location.href="?m=system&s=caigou&a=detail&id="+jiluId+"&returnurl="+returnurl+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
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
			,startTime:startTime
			,endTime:endTime
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
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&startTime="+startTime+"&endTime="+endTime);
}
//重新获取价格
function rerenderPrice(){
	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	for(var i=1;i<4;i++){
		$("#price"+i).html('<img src="images/loading.gif" width="30">');
	}
	$.ajax({
        type: "POST",
        url: "/erp_service.php?action=get_caigou_huizong",
        data: "startTime="+startTime+"&endTime="+endTime,
        dataType:"json",timeout : 30000,
        success: function(resdata){
            $("#price1").html(resdata.price1);
            $("#price2").html(resdata.price2);
            $("#price3").html(resdata.price3);
        },
        error: function() {
            layer.msg('超时，请重试', {icon: 5});
        }
    });
}