var nowIndexTime;
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#super_channel").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
$(document).ready(function(){
	rerenderPrice();
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
		$("#riqilan").slideUp(200);
	});
	//分类
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$(this).next().slideToggle(200);
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
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
//选择仓库
function selectStatus(status,title){
	$("#storeIds").val(status);
	$(".splist_up_01_left_02_up span").html(title);
	reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
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
	$("#operate_row").css({"top":(top-250)+"px","right":right+"px"});
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
function jilu_detail(){
	var zindex = $("#nowIndex").val();
	var inventoryId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").text();
	var storeId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='storeId'] div").text();
  	var keyword = $("#keyword").val();
    var channelId = $("#channelId").val();
    var brandId = $("#brandId").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var storeIds = $("#storeIds").val();
	var page = $("#page").val();
	var url = '?m=system&s=shoufahz&channelId='+channelId+"&storeIds="+storeIds+"&brandId="+brandId+"&keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime+"&page="+page;
	url = encodeURIComponent(url);
	location.href="?m=system&s=shoufahz&a=jilu_detail&inventoryId="+inventoryId+"&storeId="+storeId+"&startTime="+startTime+"&endTime="+endTime+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
	var keyword = $("#keyword").val();
    var channelId = $("#channelId").val();
    var brandId = $("#brandId").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var storeIds = $("#storeIds").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	productListTalbe.reload({
		where: {
			keyword:keyword,
			channelId:channelId,
			brandId:brandId,
			storeIds:storeIds,
			startTime:startTime,
			endTime:endTime
		},page: {
			curr: page
		}
	});
	$("th[data-field='id']").hide();
	$("th[data-field='storeId']").hide();
}
//导出导入操作
function daochu(){
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var keyword = $("#keyword").val();
    var channelId = $("#channelId").val();
    var brandId = $("#brandId").val();
  	var storeIds = $("#storeIds").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&startTime="+startTime+"&endTime="+endTime+"&keyword="+keyword+"&channelId="+channelId+"&brandId="+brandId+"&storeIds="+storeIds);
}
//重新获取价格
function rerenderPrice(){
	var keyword = $("#keyword").val();
    var channelId = $("#channelId").val();
    var brandId = $("#brandId").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var storeIds = $("#storeIds").val();
  	for(var i=1;i<5;i++){
		$("#price"+i).html('<img src="images/loading.gif" width="30">');
	}
	$.ajax({
        type: "POST",
        url: "/erp_service.php?action=get_shoufa_huizong",
        data: "startTime="+startTime+"&endTime="+endTime+'&storeIds='+storeIds+'&keyword='+keyword+'&channelId='+channelId+'&brandId='+brandId,
        dataType:"json",timeout : 30000,
        success: function(resdata){
            $("#price1").html(resdata.price1);
            $("#price2").html(resdata.price2);
            $("#price3").html(resdata.price3);
            $("#price4").html(resdata.price4);
        },
        error: function() {
            layer.msg('超时，请重试', {icon: 5});
        }
    });
}