$(function(){
	$(".dhd_chukufahuojl_dianzifahuo").click(function(){
		$(".dhd_chukufahuojl_dianzifahuo_tc").css("display","block");
	});
	$(".bjkh_jebangsjxx_3_02").click(function(){
		$(".dhd_chukufahuojl_dianzifahuo_tc").css("display","none");
	});

	$(".dhd_chukufahuojl_02_up_right_01").click(function(){
		$(this).parent().parent().next().slideToggle(200);
		var oSpan=$(this).attr('class'); 		
		if(oSpan == 'dhd_chukufahuojl_02_up_right_01'){
			$(this).attr('class', 'dhd_chukufahuojl_02_up_right_02'); 
		}
		else if(oSpan == 'dhd_chukufahuojl_02_up_right_02'){
			$(this).attr('class', 'dhd_chukufahuojl_02_up_right_01'); 
		}
	});
	$(".dhd_chukufahuojl_03_down_01_fahuo").click(function(){
		$(this).next().slideToggle(100);
	});
	$(".dd_addshoukuan_04_down_left_02_up").click(function(){
		$(this).next().toggle();
	});
});
function checkMax(dom){
	var e = $(dom);
	if(parseFloat(e.val())>parseFloat(e.attr("max"))||parseFloat(e.val())<0){
		layer.msg("出库数量不能大于订购数量或库存数量！");
		e.addClass("borderRed");
	}else{
		e.removeClass("borderRed");
	}
}
function reloadTable(){
	layer.load();
  	var jiluId = $("#jiluId").val();
  	var storeId = $("#storeId option:selected").val();
  	var ifkucun = $('#ifkucun').is(":checked")?1:0;
	productListTalbe.reload({
		where: {
			jiluId:jiluId
			,storeId:storeId
			,ifkucun:ifkucun
		}
	});
}

function chuku(){
	var tijiao = true;
	var zongNum = 0;
	var categorys = 0;
	$("#chukuForm input.sprkadd_xuanzesp_02_tt_input").each(function(){
		var e = $(this);
		if(parseFloat(e.val())>parseFloat(e.attr("max"))||parseFloat(e.val())<0){
			layer.msg("出库数量不能大于订购数量或库存数量！");
			e.focus();
			tijiao = false;
			return false;
		}
		categorys++;
		zongNum = zongNum+parseFloat(e.val());
	});
	if(tijiao){
		if(zongNum<=0){
			layer.msg("请先输入本次要出库的数量！");
			return false;
		}
		$("#dhd_chukufahuojl_chuku_tc").show();
		$("#chuku_storeName").text($("#storeId option:selected").text());
		$("#chuku_categorys").text(categorys);
		$("#chuku_nums").text(zongNum);
	}else{
		return false;
	}
}
//点击确定出库
function dochuku(){
	var tijiao = true;
	var zongNum = 0;
	$("#chukuForm input").each(function(){
		var e = $(this);
		if(parseFloat(e.val())>parseFloat(e.attr("max"))||parseFloat(e.val())<0){
			layer.msg("出库数量不能大于订购数量或库存数量！");
			e.focus();
			tijiao = false;
			return false;
		}
		zongNum = zongNum+parseFloat(e.val());
	});
	if(tijiao){
		if(zongNum<=0){
			layer.msg("请先输入本次要出库的数量！");
			return false;
		}
		layer.load();
		var action = $("#chukuForm").attr('action');
		$.ajax({
			type: "POST",
			url:action,
			data: $("#chukuForm").serialize(),
			dataType:"json",timeout : 10000,
			success: function(resdata){
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}else{
		return false;
	}
}
function zuofei(id){
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
			var dinghuoId = $("#jiluId").val();
			layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=dinghuo&a=zuofei&dinghuoId="+dinghuoId,
				data: "&id="+id+"&beizhu="+beizhu,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					layer.closeAll('loading');
					location.reload();
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要作废该出库记录吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入作废原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function zuofei_fahuo(id){
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
			var dinghuoId = $("#jiluId").val();
			layer.load();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?m=system&s=dinghuo&a=zuofei_fahuo&dinghuoId="+dinghuoId,
				data: "&id="+id+"&beizhu="+beizhu,
				dataType:"json",timeout : 8000,
				success: function(resdata){
					layer.closeAll('loading');
					location.reload();
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要作废该发货记录吗？作废后将变回待发货状态。</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入作废原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function chuku_fahuo(type,jiluId,address,name,phone){
	var tijiao = true;
	var zongNum = 0;
	var categorys = 0;
	$("#chukuForm input.sprkadd_xuanzesp_02_tt_input").each(function(){
		var e = $(this);
		if(parseFloat(e.val())>parseFloat(e.attr("max"))||parseFloat(e.val())<0){
			layer.msg("出库数量不能大于订购数量或库存数量！");
			e.focus();
			tijiao = false;
			return false;
		}
		categorys++;
		zongNum = zongNum+parseFloat(e.val());
	});
	if(tijiao){
		if(zongNum<=0){
			layer.msg("请先输入本次要出库的数量！");
			return false;
		}
	}else{
		return false;
	}
	if(type==1){
		$('#dhd_chukufahuojl_fahuoxinxi').attr("data-id",jiluId).attr("data-fahuo",0).css({'top':'0','opacity':'1','visibility':'visible'});
		$("#bg").show();
	}else if(type==2){
		$("#k_expressDesc").val($("#row_tr1 td").eq(1).html());
		$("#k_address").html(address);
		$("#k_name").val(name);
		$("#k_phone").val(phone);
		$('#dhd_chukufahuojl_dianzifahuo_tc').attr("data-id",jiluId).attr("data-fahuo",0).css({'top':'0','opacity':'1','visibility':'visible'});
	}
	$(".dhd_chukufahuojl_03_down_01_fahuo_erji").hide();
}
function fahuo(type,jiluId,address,name,phone){
	if(type==1){
		$('#dhd_chukufahuojl_fahuoxinxi').attr("data-id",jiluId).attr("data-fahuo",0).css({'top':'0','opacity':'1','visibility':'visible'});
		$("#bg").show();
	}else if(type==2){
		$("#k_expressDesc").val($("#row_tr1 td").eq(1).html());
		$("#k_address").html(address);
		$("#k_name").val(name);
		$("#k_phone").val(phone);
		$('#dhd_chukufahuojl_dianzifahuo_tc').attr("data-id",jiluId).attr("data-fahuo",0).css({'top':'0','opacity':'1','visibility':'visible'});
	}
	$(".dhd_chukufahuojl_03_down_01_fahuo_erji").hide();
}
function editFahuo(jiluId,fahuoId,fahuo_time,fahuo_company,fahuo_order,fahuo_beizhu){
	$("#fahuo_time").val(fahuo_time);
	$("#fahuo_company").val(fahuo_company);
	$("#fahuo_order").val(fahuo_order);
	$("#fahuo_beizhu").val(fahuo_beizhu);
	$('#dhd_chukufahuojl_fahuoxinxi').attr("data-id",jiluId).attr("data-fahuo",fahuoId).css({'top':'0','opacity':'1','visibility':'visible'});
	$("#bg").show();
}
function hidefahuo(type){
	if(type==1){
		$("#dhd_chukufahuojl_fahuoxinxi").css({'top':'-10px','opacity':'0','visibility':'hidden'});
		$("#bg").hide();
	}else{

	}
}
function dofahuo(type){
	var jiluId = $('#dhd_chukufahuojl_fahuoxinxi').attr("data-id");
	if(jiluId==0){
		layer.load();
		var action = $("#chukuForm").attr('action');
		$.ajax({
			type: "POST",
			url:action,
			data: $("#chukuForm").serialize(),
			dataType:"json",timeout : 10000,
			success: function(resdata){
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					jiluId = resdata.jiluId;
					var fahuoId = $('#dhd_chukufahuojl_fahuoxinxi').attr("data-fahuo");
					var dinghuoId = $("#jiluId").val();
					var action = $("#fahuoForm").attr('action')+'&jiluId='+jiluId+"&fahuoId="+fahuoId+'&dinghuoId='+dinghuoId+'&type='+type;
					$.ajax({
						type: "POST",
						url:action,
						data: $("#fahuoForm").serialize(),
						dataType:"json",timeout : 10000,
						success: function(resdata){
							if(resdata.code==0){
								layer.closeAll();
								layer.msg(resdata.message,{icon: 5});
							}else{
								location.reload();
							}
						},
						error: function() {
							layer.closeAll();
							layer.msg('数据请求失败', {icon: 5});
						}
					});
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}else{
		var fahuoId = $('#dhd_chukufahuojl_fahuoxinxi').attr("data-fahuo");
		var dinghuoId = $("#jiluId").val();
		layer.load();
		var action = $("#fahuoForm").attr('action')+'&jiluId='+jiluId+"&fahuoId="+fahuoId+'&dinghuoId='+dinghuoId+'&type='+type;
		$.ajax({
			type: "POST",
			url:action,
			data: $("#fahuoForm").serialize(),
			dataType:"json",timeout : 10000,
			success: function(resdata){
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}
}
//快递鸟电子面单
function fahuo_kuaidiniao(){
	var jiluId = $('#dhd_chukufahuojl_dianzifahuo_tc').attr("data-id");
	var fahuoId = $('#dhd_chukufahuojl_dianzifahuo_tc').attr("data-fahuo");
	var dinghuoId = $("#jiluId").val();
	var name = $("#k_name").val();
	var phone = $("#k_phone").val();
	if(name==''){
		layer.msg("发件人姓名不能为空！",function(){});
		return false;
	}
	if(phone.length!=11){
		layer.msg("请输入正确的发件人手机号码！",function(){});
		return false;
	}
	if(jiluId==0){
		layer.load();
		var action = $("#chukuForm").attr('action');
		$.ajax({
			type: "POST",
			url:action,
			data: $("#chukuForm").serialize(),
			dataType:"json",timeout : 10000,
			success: function(resdata){
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					jiluId = resdata.jiluId;
					var action = $("#fahuo_kuaidiniao_form").attr('action')+'&jiluId='+jiluId+"&fahuoId="+fahuoId+'&dinghuoId='+dinghuoId;
					$.ajax({
						type: "POST",
						url:action,
						data: $("#fahuo_kuaidiniao_form").serialize(),
						dataType:"json",timeout : 10000,
						success: function(resdata){
							if(resdata.code==0){
								layer.closeAll();
								layer.msg(resdata.message,{icon: 5});
							}else{
								location.reload();
							}
						},
						error: function() {
							layer.closeAll();
							layer.msg('数据请求失败', {icon: 5});
						}
					});
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}else{
		layer.load();
		var action = $("#fahuo_kuaidiniao_form").attr('action')+'&jiluId='+jiluId+"&fahuoId="+fahuoId+'&dinghuoId='+dinghuoId;
		$.ajax({
			type: "POST",
			url:action,
			data: $("#fahuo_kuaidiniao_form").serialize(),
			dataType:"json",timeout : 10000,
			success: function(resdata){
				//console.log(resdata);
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}
}
function viewWuliu(type,kuaidi_com,kuaidi_order){
	layer.load();
	$.ajax({
		type: "POST",
		url:"?m=system&s=dinghuo&a=getWuliu",
		data: "type="+type+"&kuaidi_com="+kuaidi_com+"&kuaidi_order="+kuaidi_order,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			$("#dhd_chukufahuojl_wuliu_tc").show().find(".dh_ckfhjl_chuku_zu").html(resdata.message);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}