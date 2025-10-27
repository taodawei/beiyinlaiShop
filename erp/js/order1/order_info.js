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
			url: "?s=order1&a=order_info_index",
			data: "id="+orderId,
		    dataType : "text",
		    timeout : 20000,
			success: function(data) {
				$("#orderInfoCont1").html(data);
				/*btn = document.getElementById('copy_order_shouhuo');
				var clipboard = new ClipboardJS(btn);
				clipboard.on('success', function(e) {
					layer.msg("复制成功");
				});
				clipboard.on('error', function(e) {
					layer.msg("您的浏览器不支持该操作，请手动复制",{icon:5});
				});*/
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
			url: "?s=order1&a=order_error_index",
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
			url: "?s=order1&a=order_jilu_index",
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
			url: "?s=order1&a=order_tuihuan_index",
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
			url: "?s=order1&a=order_service_index",
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
function order_add_error(orderId){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $.trim($("#e_beizhu").val());
			if(biezhu==''){
				layer.msg("请输入异常原因",function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order1&a=add_error",
				data: "id="+orderId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						update_order_tr({"status":-2,"status_info":"异常"});
						order_error_index(1);
						layer.msg('操作成功',{icon:1});
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">新增异常</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入异常原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
//跟进异常，is_done是否完成
function add_error_beizhu(errorId,is_done){
	var beizhu = $.trim($("#error_beizhu_input").val());
	if(beizhu==''){
		layer.msg("请填写跟进说明！",function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?s=order1&a=add_error_beizhu",
		data: "id="+errorId+"&is_done="+is_done+"&cont="+beizhu,
		dataType:'json',timeout:30000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message,{icon:5});
			}else{
				order_error_index(1);
				layer.msg('操作成功',{icon:1});
			}
		},
		error: function(){
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
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
function add_order_beizhu(id){
	var content = $.trim($("#add_order_beizhu_content").val());
	if(content==''){
		layer.msg('请填写备注内容',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?s=order1&a=add_order_beizhu",
		data: "id="+id+"&cont="+content,
		dataType:'json',timeout:30000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message,{icon:5});
			}else{
				$("#add_order_beizhu_content").parent().prepend(resdata.message);
				$("#add_order_beizhu_content").val('');
			}
		},
		error: function(){
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
}
function order_shenhe(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order1&a=shenhe&status=1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						order_info_index(1);
						update_order_tr({"status":resdata.status,"status_info":resdata.status_info});
						layer.msg('操作成功',{icon:1});
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要审核通过吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入审批内容"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function quxiao(jiluId){
	if(typeof(jiluId)=='undefined'){
		jiluId = getPdtId();
	}
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			if(beizhu==''){
				layer.msg('请输入取消订单原因',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order1&a=shenhe&status=-1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						order_info_index(1);
						update_order_tr({"status":resdata.status,"status_info":resdata.status_info});
						layer.msg('取消成功',{icon:1});
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要取消订单吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入取消原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
//编辑订单信息
function order_edit(orderId){
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=order1&a=order_info_getedit",
		data: "id="+orderId,
		dataType : "json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll();
			$("#order_info_price").html(resdata.price_str);
			$("#order_info_fapiao").html(resdata.fapiao_str);
			$("#order_info_shouhuo").html(resdata.shouhuo_str);
			lay_form.render();
			$("#order_operate").attr("class","weizhifu_jibenxinxi_bianji_1").html('<a href="javascript:" onclick="order_edit_save('+orderId+');" class="weizhifu_jibenxinxi_bianji_1_01">确认并保存</a><a href="javascript:" onclick="order_info_index(1);" class="weizhifu_jibenxinxi_bianji_1_02">取消</a>');
			lay_form.on('select(order_ps1)',function(data){
				if(!isNaN(data.value)){
					layer.load();
					id = data.value;
					ajaxpost=$.ajax({
						type:"POST",
						url:"/erp_service.php?action=getAreas",
						data:"id="+id,
						timeout:"4000",
						dataType:"text",
						success: function(html){
							$("#order_ps3").html('<option value="">请先选择市</option>');
							if(html!=""){
								$("#order_ps2").html(html);
								$("#order_areaId").val(id);
							}else{
								$("#order_areaId").val(id);
							}
							lay_form.render('select');
							layer.closeAll('loading');
						},
						error:function(){
							layer.closeAll();
							layer.msg('网络错误，请检查网络',{icon:5});
						}
					});
				} 
			});
			lay_form.on('select(order_ps2)',function(data){
				if(!isNaN(data.value)){
					layer.load();
					id = data.value;
					ajaxpost=$.ajax({
						type:"POST",
						url:"/erp_service.php?action=getAreas",
						data:"id="+id,
						timeout:"4000",
						dataType:"text",
						success: function(html){
							if(html!=""){
								$("#order_ps3").html(html);
								$("#order_areaId").val(id);
							}else{
								$("#order_areaId").val(id);
							}
							lay_form.render('select');
							layer.closeAll('loading');
						},
						error:function(){
							layer.closeAll();
							layer.msg('网络错误，请检查网络',{icon:5});
						}
					});
				}
			});
			lay_form.on('select(order_ps3)',function(data){
				if(!isNaN(data.value)){
					$("#order_areaId").val(data.value);
				}
			});
		},
		error: function() {
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
}
//编辑保存操作
function order_edit_save(orderId){
	var order_edit_price =$("#order_edit_price").val();
	var araeId = parseInt($("#order_areaId").val());
	var shoujianren = $.trim($("#order_edit_shoujianren").val());
	var phone = $.trim($("#order_edit_phone").val());
	var address = $.trim($("#order_edit_address").val());
	if(order_edit_price<=0){
		layer.msg('待付款金额不能小于或等于0!',function(){});
		return false;
	}
	if(shoujianren==''){
		layer.msg('收件人不能为空!',function(){});
		return false;
	}
	if(phone==''){
		layer.msg('联系电话不能为空!',function(){});
		return false;
	}
	if(araeId==0||typeof(araeId)=='undefined'){
		layer.msg('请先选择所在地区!',function(){});
		return false;
	}
	if(address==''){
		layer.msg('详细地址不能为空!',function(){});
		return false;
	}

	ajaxpost=$.ajax({
		type:"POST",
		url:"?s=order1&a=order_edit_save",
		data:"id="+orderId+"&order_edit_price="+order_edit_price+'&'+$("#order_fapiao_form").serialize()+'&'+$("#order_shouhuo_form").serialize(),
		timeout:"20000",
		dataType:"json",
		success: function(html){
			layer.closeAll('loading');
			layer.msg("操作成功",{icon:1});
			order_info_index(1);
		},
		error:function(){
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
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
function tuihuan_shenhe(){
	reload = true;
	if(typeof(arguments[1])=='undefined'){
		var zindex = $("#nowIndex").val();
		var tuihuanId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='tuihuanId'] div").html();
	}else{
		tuihuanId = arguments[1];
		reload = false;
	}
	var tishi = arguments[0]==1?'确定已收到买家退回的商品吗？':'确定要审核通过该退换货请求吗？';
	layer.confirm(tishi, {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order1&a=shenhe_tuihuan",
			data: "&tuihuanId="+tuihuanId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功');
					if(reload){
						reloadTable(1);
					}else{
						order_tuihuan_index(1);
					}
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function tuihuan_quxiao(){
	reload = true;
	if(typeof(arguments[0])=='undefined'){
		var zindex = $("#nowIndex").val();
		var tuihuanId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='tuihuanId'] div").html();
	}else{
		tuihuanId = arguments[0];
		reload = false;
	}
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			if(beizhu==''){
				layer.msg('请输入驳回原因',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order1&a=bohui_tuihuan",
				data: "tuihuanId="+tuihuanId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						if(reload){
							reloadTable(1);
						}else{
							order_tuihuan_index(1);
						}
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要驳回该退换货请求吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="请输入驳回原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function tuihuan_wancheng(){
	reload = true;
	if(typeof(arguments[0])=='undefined'){
		var zindex = $("#nowIndex").val();
		var tuihuanId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='tuihuanId'] div").html();
	}else{
		tuihuanId = arguments[0];
		reload = false;
	}
	layer.confirm('确定要将该订单设置为已退款吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order1&a=wancheng_tuihuan",
			data: "&tuihuanId="+tuihuanId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功');
					if(reload){
						reloadTable(1);
					}else{
						order_tuihuan_index(1);
					}
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function service_shenhe(){
	if(typeof(arguments[0])=='undefined'){
		$("#ddfw_piliangfenpei_tc").attr("data-type","2").attr("data-id",'0').show();
	}else{
		$("#ddfw_piliangfenpei_tc").attr("data-type","2").attr("data-id",arguments[0]).show();
	}
}
function service_zuofei(){
	reload = true;
	if(typeof(arguments[0])=='undefined'){
		var zindex = $("#nowIndex").val();
		var serviceId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='serviceId'] div").html();
	}else{
		serviceId = arguments[0];
		reload = false;
	}
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			if(beizhu==''){
				layer.msg('请输入作废原因',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order1&a=service_zuofei",
				data: "ids="+serviceId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						if(reload){
							reloadTable(1);
						}else{
							order_tuihuan_index(1);
						}
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要作废该服务吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="请输入作废原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function service_done(){
	reload = true;
	if(typeof(arguments[0])=='undefined'){
		var zindex = $("#nowIndex").val();
		var serviceId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='serviceId'] div").html();
	}else{
		serviceId = arguments[0];
		reload = false;
	}
	layer.confirm('确定要将该订单设置为已完成吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order1&a=service_wancheng",
			data: "&ids="+serviceId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				layer.msg('操作成功');
				if(reload){
					reloadTable(1);
				}else{
					order_service_index(1);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
}
function service_fenpei(){
	var type = parseInt($("#ddfw_piliangfenpei_tc").attr("data-type"));
	var serviceId = parseInt($("#ddfw_piliangfenpei_tc").attr("data-id"));
	var ids = '';
	reload = true;
	if(type==1){
		ids = $("#selectedIds").val();
	}else{
		if(serviceId==0){
			var zindex = $("#nowIndex").val();
			ids = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='tuihuanId'] div").html();
		}else{
			ids = serviceId;
			reload = false;
		}
	}
	var worker_id = $("#users").val();
	var worker_name=$("#userNames").val();
	var service_time = $("#service_time").val();
	var worker_phone = $("#service_phone").val();
	if(worker_id==0){
		layer.msg("请先选择服务人员",function(){});
		return false;
	}
	if(service_time==''){
		layer.msg("请先选择服务时间",function(){});
		return false;
	}
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?s=order1&a=service_fenpei",
		data: "&ids="+ids+"&worker_id="+worker_id+"&worker_name="+worker_name+"&service_time="+service_time+"&worker_phone="+worker_phone,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll('loading');
			if(resdata.code==0){
				layer.msg(resdata.message,{icon: 5});
			}else{
				layer.msg('操作成功');
				if(reload){
					reloadTable(1);
				}else{
					order_service_index(1);
				}
				$("#users").val('0');
				$("#userNames").val('');
				$("#service_time").val('');
				$("#fanwei_1").val('');
				$("#ddfw_piliangfenpei_tc").hide();
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function service_edit(orderId,serviceId,reload){
	if($("#ddfw_adddingdangfuwu_tc").length==0){
		$("body").append('<div class="ddfw_adddingdangfuwu_tc" id="ddfw_adddingdangfuwu_tc" style=""><div class="bj"></div><div class="ddfw_adddingdangfuwu"></div></div>');
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?s=order1&a=order_edit_service",
		data: "orderId="+orderId+"&serviceId="+serviceId+"&reload="+reload,
		dataType : "text",
		timeout : 10000,
		success: function(data) {
			layer.closeAll();
			$("#ddfw_adddingdangfuwu_tc").show().find(".ddfw_adddingdangfuwu").html(data);
			lay_date.render({
				elem: '#service_edit_time'
				,type: 'datetime'
				,format:'yyyy-MM-dd HH:mm'
			});
		},
		error: function() {
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
}
function hide_service_edit(){
	$("#ddfw_adddingdangfuwu_tc").hide().find(".ddfw_adddingdangfuwu").html("");
}
function submit_service_edit(reload){
	var edit_form = $("#service_edit_form");
	tijiao = true;
	edit_form.find("input[required='required']").each(function(){
		if($.trim($(this).val())==''){
			layer.msg($(this).attr('placeholder'),{icon:5});
			tijiao = false;
			return false;
		}
	});
	if(!tijiao){
		return false;
	}
	layer.load();
	var worker_id = $("#users").val();
	var worker_name=$("#userNames").val();
	var reqData = edit_form.serialize();
	$.ajax({
		type: "POST",
		url: edit_form.attr('action'),
		data: reqData+"&worker_id="+worker_id+"&worker_name="+worker_name,
		dataType : "text",
		timeout : 10000,
		success: function(data) {
			layer.closeAll();
			layer.msg('操作成功');
			hide_service_edit();
			if(reload==0){
				order_service_index(1);
			}else{
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('网络错误，请检查网络',{icon:5});
		}
	});
}




function fanwei(modelId){
    $("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    ajaxpost=$.ajax({
        type: "POST",
        url: "/erp_service.php",
        data: "action=getDinghuoFanwei",
        dataType : "text",timeout : 8000,
        success: function(data) {
            $('#myModal').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
}
function get_users(id){
    var img = $("img.depart_select_img[data-id="+id+"]").attr("src");
    if(img=='images/tree_bg2.jpg'){
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg1.jpg");
    }else{
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg2.jpg");
    }
    $("#departUsers"+id).slideToggle(100);
    if($("#departUsers"+id).html()==""){
        $("#departUsers"+id).html("<li><img src='images/loading.gif'></li>");
        ajaxpost=$.ajax({
            type:"POST",
            url:"/erp_service.php?action=get_fanwei_users",
            data:"id="+id,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#departUsers"+id).html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function search_users(keyword){
    if(keyword==''){
        $("#depart_users").show();
        $("#search_users").hide();
    }else{
        $("#depart_users").hide();
        $("#search_users").html("<li><img src='images/loading.gif'></li>").show();
        ajaxpost=$.ajax({
            type:"POST",
            url:"/erp_service.php?action=get_fanwei_users",
            data:"keyword="+keyword,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#search_users").html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function add_depart(id,name){
	get_users(id);
}
function add_user(id,name){
    $("#users").val(id);
    $("#userNames").val(name);
    var editId = $("#editId").val();
    $("#fanwei_"+editId).val(name);
    hide_myModal();
}
function hide_myModal(){
    if(ajaxpost){
        ajaxpost.abort();
    }
    $("#myModal").css("opacity","0").css("display","none").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $(".reveal-modal-bg").fadeOut(200);
}