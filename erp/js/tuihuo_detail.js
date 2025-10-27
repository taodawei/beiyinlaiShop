var layForm;
layui.use(['form','upload','laydate'], function(){
	var form = layui.form
	,upload = layui.upload
	,laydate =layui.laydate
	layForm = form;
	var uploadInit = upload.render({
		elem: '#uploadPdtImage'
		,url: '?m=system&s=upload&a=upload'
		,before:function(){
			var jiluId = $("#jiluId").val();
			uploadInit.config.data.type = 'tuihuo';
			uploadInit.config.data.jiluId = jiluId;
			layer.load();
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
			}else{
				var nums = parseInt($('#uploadImages').attr("data-num"))+1;
				$('#uploadImages').before('<li id="image_li'+nums+'"><a><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
				$('#uploadImages').attr("data-num",nums);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	$(function(){
		$(".dhd_dingdanxiangqing_5_up").click(function(){
			$(this).toggleClass("openIcon");
			$(this).next().slideToggle(200);
		});
		renderJilus();
	});
});
function doPrint() {   
	bdhtml=window.document.body.innerHTML;   
	sprnstr="<!--startprint-->";   
	eprnstr="<!--endprint-->";   
	prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);   
	prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));   
	window.document.body.innerHTML=prnhtml;  
	window.print();
}
function tongguo(jiluId){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=tuihuo&a=shenhe&status=1",
				data: "jiluId="+jiluId,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						location.reload();
					}
				},error:function(){
					layer.closeAll();
					layer.msg('超时，请刷新重试',{icon:5});
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
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;line-height:220px;text-align:center;padding:0px;margin-top:10px;font-size:16px;">'+
		'确定要审核通过该退货单吗？'+
		'</div>'+
		'</div>'
	});
}
function zuofei(jiluId){
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
				url: "?m=system&s=tuihuo&a=zuofei&status=-1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						location.reload();
					}
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要驳回吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入驳回原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function ruku(jiluId){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var storeId = $("#r_cangkuId option:selected").val();
			if(isNaN(storeId)||storeId==''){
				layer.msg('请选择要入库的仓库',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=tuihuo&a=ruku",
				data: "jiluId="+jiluId+"&storeId="+storeId,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						location.reload();
					}
				},error:function(){
					layer.closeAll();
					layer.msg('超时，请刷新重试',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">收货确认</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02 layui-form" style="height:220px;line-height:220px;text-align:center;padding:0px;margin-top:10px;">'+
		'确认将退货商品退回到：<div style="display:inline-block;width:150px"><select id="r_cangkuId">'+cangku_options+'</select></div>'+
		'</div>'+
		'</div>'
		,success:function(){
			layForm.render('select');
		}
	});
}
function caiwu_tongguo(jiluId){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=tuihuo&a=caiwu_shenhe&status=1",
				data: "jiluId="+jiluId,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						location.reload();
					}
				},error:function(){
					layer.closeAll();
					layer.msg('超时，请刷新重试',{icon:5});
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
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;line-height:220px;text-align:center;padding:0px;margin-top:10px;font-size:16px;">'+
		'确定要通过财务审核通过吗？'+
		'</div>'+
		'</div>'
	});
}
function del_image(id){
	layer.load();
	var img = $("#image_li"+id+" img").eq(0).attr("src");
	$("#image_li"+id).remove();
	img = img.replace('?x-oss-process=image/resize,w_122','');
	$.ajax({
		type: "POST",
		url: "?m=system&s=upload&a=delImg",
		data: "img="+img,
		dataType:'text',timeout : 5000,
		success: function(resdata){
			layer.closeAll('loading');
		},
		error: function() {
			layer.closeAll('loading');
		}
	});
}
function renderJilus(){
	var jiluId = $("#jiluId").val();
	layer.load();
	$.ajax({
		type: "POST", 
		url: "?m=system&s=tuihuo&a=getCzJilus",
		data: "jiluId="+jiluId,
		dataType:'text',timeout :30000,
		success: function(resdata){
			$("#jiluHeader").siblings().remove();
			$("#jiluHeader").after(resdata);
			layer.closeAll('loading');
		},
		error: function() {
			layer.msg('数据请求超时，请刷新重试');
			layer.closeAll('loading');
		}
	});
}
function addBeizhu(){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var jiluId = $("#jiluId").val();
			var beizhu = $("#e_beizhu").val();
			if(beizhu==''){
				layer.msg('请输入备注内容',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=tuihuo&a=addBeizhu",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						$("#addBeizhu").before(resdata.message);
					}
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">添加备注</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入备注信息"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function editBeizhu(id){
	var content = $("#beizhu"+id).val();
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			$("#beizhu"+id).val(beizhu);
			if(beizhu==''){
				$("#rowTr"+id+" .sprukuadd_03_tt_addbeizhu").css("fontSize","36px").html('+');
			}else{
				$("#rowTr"+id+" .sprukuadd_03_tt_addbeizhu").css("fontSize","12px").html('<img src="images/biao_31.png"/>'+beizhu);
			}
			layer.closeAll();
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
			'<div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">备注说明</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
				'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入备注信息">'+content+'</textarea>'+
			'</div>'+
		'</div>'
	});
}