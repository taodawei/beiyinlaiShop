var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon').next().slideToggle(200);
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
	$(".splist_up_02_1").click(function(){
		$(".splist_up_02").hide();
		$(".splist_up_01").show();
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectStatus(status,title){
	$("#status").val(status);
	$(".splist_up_01_left_02_up").eq(1).find("span").html(title);
	reloadTable(0);
}
function selectScene(status,title){
	$("#type").val(status);
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
		top=top-80;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+25;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = nowTr.find("td[data-field='status'] div").text();
	if(status=='-1'){
		$("#zuofeibtn").hide();
	}else{
		$("#zuofeibtn").show();
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
function zuofei(param){
	ids = getPdtId();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=yingxiao&a=zuofei_dzp",
		data: "&ids="+ids,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll('loading');
			if(resdata.code==0){
				layer.msg(resdata.message,{icon: 5});
			}else{
				layer.msg('操作成功');
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var status = $("#status").val();
	var type = $("#type").val();
	var keyword = $("#keyword").val();
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
			,status:status
			,type:type
			,startTime:startTime
			,keyword:keyword
			,endTime:endTime
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}
function view_jilu(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	var status = $("#status").val();
	var type = $("#type").val();
	var keyword = $("#keyword").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=yingxiao&a=dazhuanpan&status="+status+"&type="+type+"&keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=yingxiao&a=prizes&dazhuanpan_id="+jiluId;
}
function view_record(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	var status = $("#status").val();
	var type = $("#type").val();
	var keyword = $("#keyword").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=yingxiao&a=dazhuanpan&status="+status+"&type="+type+"&keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=yingxiao&a=records&dazhuanpan_id="+jiluId;
}