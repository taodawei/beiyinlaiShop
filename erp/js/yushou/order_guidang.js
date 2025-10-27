var ajaxpost;
var btn;
var lay_form;
layui.use(['form'],function(){
	lay_form = layui.form;
});
function order_show(zindex){
	if(typeof(zindex)=='undefined'){
		var zindex = $("#nowIndex").val();
	}else{
		$("#nowIndex").val(zindex);
	}
	var nowtr = $(".layui-table-main tr[data-index='"+zindex+"']").eq(0);
	var id = parseInt($(nowtr).find("td[data-field='id'] div").text());
	var dataId = parseInt($("#dqddxiangqing").attr("data-id"));
	$(".layui-table-fixed-l tr[data-index!='"+zindex+"']").removeClass("openIcon");
	$(".layui-table-fixed-l tr[data-index='"+zindex+"']").toggleClass("openIcon");
	if(id==dataId){
		if($("#dqddxiangqing").css("display")=='block'){
			$(".layui-table-main").css("overflow","auto");
		}
		$("#dqddxiangqing").toggle(200);
	}else{
		if(ajaxpost){
			ajaxpost.abort();
		}
		var top = $(nowtr).offset().top+50;
		$(".layui-table-main").css("overflow","hidden");
		$("#dqddxiangqing").show().css("top",top+"px").attr("data-id",id).find(".dqddxiangqing_down_01").html('<div class="loading"><img src="images/loading.gif"></div>');
		qiehuan('orderInfo',1,'dqddxiangqing_up_on');
		order_info_index(1);
	}
}
//获取订单基础信息，force是否强制刷新 0否1是
function order_info_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont1 .loading").length>0||force==1){
		if($("#orderInfoCont1 .loading").length==0){
			$("#orderInfoCont1").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=order_info_guidang",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont1").html(data);
				btn = document.getElementById('copy_order_shouhuo');
				var clipboard = new ClipboardJS(btn);
				clipboard.on('success', function(e) {
					layer.msg("复制成功");
				});
				clipboard.on('error', function(e) {
					layer.msg("您的浏览器不支持该操作，请手动复制",{icon:5});
				});
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//获取订单异常信息force是否强制刷新 0否1是
function order_error_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont2 .loading").length>0||force==1){
		if($("#orderInfoCont2 .loading").length==0){
			$("#orderInfoCont2").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=order_error_guidang",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont2").html(data);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//订单操作记录
function order_jilu_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont5 .loading").length>0||force==1){
		if($("#orderInfoCont5 .loading").length==0){
			$("#orderInfoCont5").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=order_jilu_index",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont5").html(data);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//退换货管理首页
function order_tuihuan_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont3 .loading").length>0||force==1){
		if($("#orderInfoCont3 .loading").length==0){
			$("#orderInfoCont3").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=order_tuihuan_index",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont3").html(data);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//订单服务首页
function order_service_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont4 .loading").length>0||force==1){
		if($("#orderInfoCont4 .loading").length==0){
			$("#orderInfoCont4").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=order_service_index",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont4").html(data);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//隐藏收货人信息
function toggle_shouhuo_info(dom){
	var show = $(dom).attr("data-show");
	if(show==0){
		$("#order_shoujianren").html($("#order_shoujianren").attr("data-val"));
		$("#order_shoujihao").html($("#order_shoujihao").attr("data-val"));
		$(dom).attr({"data-show":'1',"src":"images/dingdanxx_13.png"});
	}else{
		$("#order_shoujianren").html($("#order_shoujianren").attr("data-hide"));
		$("#order_shoujihao").html($("#order_shoujihao").attr("data-hide"));
		$(dom).attr({"data-show":'0',"src":"images/dingdanxx_12.png"});
	}
}
//修改tr中td的值
function update_order_tr(obj){
	var index = $("#nowIndex").val();
	var tr = $(".layui-table-main tr[data-index='"+index+"']");
	$.each(obj,function(key,val){
		if(tr.find("td[data-field='"+key+"'] div span").length>0){
			tr.find("td[data-field='"+key+"'] div span").html(val);
		}else{
			tr.find("td[data-field='"+key+"'] div").html(val);
		}
		if(key=='status'&&val=='-1'){
			tr.addClass("deleted");
		}
	});
}