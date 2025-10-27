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
		order_dianzi_info(1);
	}
}
//获取订单基础信息，force是否强制刷新 0否1是
function order_dianzi_info(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont1 .loading").length>0||force==1){
		if($("#orderInfoCont1 .loading").length==0){
			$("#orderInfoCont1").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=change_log&a=order_dianzi_info",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont1").html(data);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
//获取订单异常信息force是否强制刷新 0否1是
function order_xiangqing_index(force){
	var orderId = parseInt($("#dqddxiangqing").attr("data-id"));
	if($("#orderInfoCont2 .loading").length>0||force==1){
		if($("#orderInfoCont2 .loading").length==0){
			$("#orderInfoCont2").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=change_log&a=order_xiangqing_index",
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
			url: "?s=fahuo&a=order_jilu_index",
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
//弹出快递
function fahuo_show(id){
	$(".putongfh_fahuo_tc").show();
	$('#orderId').val(id);
}
function fahuo_hide(){
	$(".putongfh_fahuo_tc").hide();
}
//单个订单发货
function order_fahuo(id){
	if(id != '' || id != 0){
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=fahuo&a=order_fahuo",
			data: "id="+id,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	}
}
function fahuo_tuikuan(orderId){
	layer.confirm('确定要执行订单退款操作吗？取消后所有的订单将会退款并变为无效！', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST",
			url: "/index.php?p=19&a=qx_fahuo",
			data: "ids="+orderId,
			dataType:'json',timeout:30000,
			success: function(resdata){
				layer.closeAll();
				layer.msg(resdata.message);
				if(resdata.code==1){
					$("#dqddxiangqing").hide();
					reloadTable(1);
				}
			},
			error: function(){
				layer.closeAll();
				layer.msg('网络错误，请检查网络',{icon:5});
			}
		});
	});
}