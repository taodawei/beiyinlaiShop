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
	//高级搜索
	$('.splist_up_01_right_2_up').click(function(){
		$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});
		$("#bg").show();
	});
});
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
	$("#bg").hide();
}
//选择类型
function selectType(title,id){
	$(".splist_up_01_left_01_up span").text(title);
	$("#paystatus").val(id);
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
		top=top-25;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	$("#operate_row").css({"top":(top-245)+"px","right":right+"px"});
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
function detail(){
	var jiluId = getPdtId();
  	var keyword = $("#keyword").val();
  	var areaId = $("#areaId").val();
  	var level = $("#level").val();
  	var kehuStatus = $("#kehuStatus").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var returnurl = "?m=system&s=money&a=account&keyword="+keyword+"&areaId="+areaId+"&level="+level+"&kehuStatus="+kehuStatus+"&page="+page+"&order1="+order1+"&order2="+order2;
	returnurl = encodeURIComponent(returnurl);
	var url = $("#url").val();
	location.href="?m=system&s=money&a=acc_detail&id="+jiluId+"&returnurl="+returnurl+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
  	var keyword = $("#keyword").val();
  	var areaId = $("#areaId").val();
  	var level = $("#level").val();
  	var kehuStatus = $("#kehuStatus").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	productListTalbe.reload({
		where: {
			order1: order1,
			order2: order2,
			keyword:keyword,
            areaId:areaId,
            level:level,
            kehuStatus:kehuStatus
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
  	var areaId = $("#areaId").val();
  	var level = $("#level").val();
  	var kehuStatus = $("#kehuStatus").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&keyword="+keyword+"&areaId="+areaId+"&level="+level+"&kehuStatus="+kehuStatus);
}
function chongzhi(){
	var zindex = $("#nowIndex").val();
	var kehuId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").text();
	var kehuName = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='title'] div").text();
	acc_chongzhi(kehuId,0,kehuName);
}
function koukuan(){
	var zindex = $("#nowIndex").val();
	var kehuId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").text();
	var kehuName = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='title'] div").text();
	acc_koukuan(kehuId,0,kehuName);
}