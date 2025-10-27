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
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_02_down').slideToggle(200);
		stopPropagation(eve); 
	});
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
function selectLevel(status,title){
	$("#isnew").val(status);
	$(".splist_up_01_left_02_up span").html(title);
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
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	status = nowTr.find("td[data-field='status'] div").eq(0).html();
	if(status==1){
		$("#operate_row li.operate2").show();
		$("#operate_row li.operate1").hide();
	}else{
		$("#operate_row li.operate2").hide();
		$("#operate_row li.operate1").show();
	}
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
//删除单个产品
function del_fankui(params){
	var pdtId = getPdtId();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=kehu&a=delFankui",
		data: "&id="+pdtId,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function huifu(){
	var zindex = $("#nowIndex").val();
	var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']").eq(0);
	var pdtId = nowTr.find("td[data-field='id'] div").text();
	var title = nowTr.find("td[data-field='title'] div span").text();
	var status = nowTr.find("td[data-field='status'] div").eq(0).text();
	$("#khfk_imglist").html('');
	$("#khfk_imgs").val('');
	$("#khfk_id").val(pdtId);
	$("#khfk_content").val('');
	$("#khfk_ljhuifu").css({'top':'0px','opacity':'1','visibility':'visible'});
	$("#khfk_ljhuifu .khfk_ljhuifu_02").remove();
	$("#khfk_ljhuifu .khfk_ljhuifu_01").html('回复'+title);
	if(status=='1'){
		$("#khfk_ljhuifu_03").hide();
		$(".khfk_ljhuifu_04_1").hide();
	}else{
		$("#khfk_ljhuifu_03").show();
		$(".khfk_ljhuifu_04_1").show();
	}
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=kehu&a=getFankuiList",
		data: "&id="+pdtId,
		dataType:"text",timeout : 20000,
		success: function(resdata){
			layer.closeAll();
			$("#khfk_ljhuifu_03").before(resdata);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function khfk_send(){
	var id = $('#khfk_id').val();
	var images = $("#khfk_imgs").val();
	var content = $("#khfk_content").val();
	if(content==''){
		layer.msg('反馈内容不能为空',function(){});
		return false;
	}
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=kehu&a=addFankui",
		data: "&id="+id+"&images="+images+"&content="+content,
		dataType:"text",timeout : 20000,
		success: function(resdata){
			huifu();
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function khfk_cancel(){
	$("#khfk_ljhuifu").css({'top':'-10px','opacity':'0','visibility':'hidden'});
	reloadTable(1);
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var isnew = $("#isnew").val();
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
			,isnew:isnew
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
	$("th[data-field='id']").hide();
	$("th[data-field='status']").hide();
}
