layui.use(['form'], function(){
	form = layui.form;
	form.on('checkbox(yufu)',function(data){
		if(data.elem.checked){
			$(".acc_yufu").removeClass('disabled').prop('readonly',false);
			$("#acc_yufu_queren").removeAttr('disabled');
			form.render("checkbox");
		}else{
			var title = $(data.elem).attr('dtitle');
			layer.open({
				type: 1
				,title: '确定要关闭'+title+'吗？'
				,shade: 0.3
				,area: '390px;'
				,id: 'LAY_layuipro1'
				,btn: ['确定', '取消']
				,closeBtn: false
				,yes: function(index, layero){
					$(".acc_yufu").addClass('disabled').prop('readonly',true);
					$("#acc_yufu_queren").prop('checked',false).prop('disabled',true);
					form.render("checkbox");
					layer.closeAll();
				},btn2: function(){
					$("input[lay-filter='yufu']").prop("checked",true);
					$("#acc_yufu_queren").removeAttr('disabled');
					form.render("checkbox");
					layer.closeAll();
				}
				,btnAlign: 'r'
				,content:'<div style="margin:12px 15px;text-align:center;width:360px;">您确定要关闭'+title+'吗？</div>'
			});
		}
	});
	form.on('checkbox(fandian)',function(data){
		if(data.elem.checked){
			$(".acc_fandian").removeClass('disabled').prop('readonly',false);
			$("#acc_fandian_queren").removeAttr('disabled');
			form.render("checkbox");
		}else{
			var title = $(data.elem).attr('dtitle');
			layer.open({
				type: 1
				,title: '确定要关闭'+title+'吗？'
				,shade: 0.3
				,area: '390px;'
				,id: 'LAY_layuipro1'
				,btn: ['确定', '取消']
				,closeBtn: false
				,yes: function(index, layero){
					$(".acc_fandian").addClass('disabled').prop('readonly',true);
					$("#acc_fandian_queren").prop('checked',false).prop('disabled',true);
					form.render("checkbox");
					layer.closeAll();
				},btn2: function(){
					$("input[lay-filter='fandian']").prop("checked",true);
					$("#acc_fandian_queren").removeAttr('disabled');
					form.render("checkbox");
					layer.closeAll();
				}
				,btnAlign: 'r'
				,content:'<div style="margin:12px 15px;text-align:center;width:360px;">您确定要关闭'+title+'吗？</div>'
			});
		}
	});
	form.on('checkbox(baozheng)',function(data){
		if(data.elem.checked){
			$(".acc_baozheng").removeClass('disabled').prop('readonly',false);
			$("#acc_baozheng_queren").removeAttr('disabled');
			form.render("checkbox");
		}else{
			var title = $(data.elem).attr('dtitle');
			layer.open({
				type: 1
				,title: '确定要关闭'+title+'吗？'
				,shade: 0.3
				,area: '390px;'
				,id: 'LAY_layuipro1'
				,btn: ['确定', '取消']
				,closeBtn: false
				,yes: function(index, layero){
					$(".acc_baozheng").addClass('disabled').prop('readonly',true);
					$("#acc_baozheng_queren").prop('checked',false).prop('disabled',true);
					form.render("checkbox");
					layer.closeAll();
				},btn2: function(){
					$("input[lay-filter='baozheng']").prop("checked",true);
					$("#acc_baozheng_queren").removeAttr('disabled');
					form.render("checkbox");
					layer.closeAll();
				}
				,btnAlign: 'r'
				,content:'<div style="margin:12px 15px;text-align:center;width:360px;">您确定要关闭'+title+'吗？</div>'
			});
		}
	});
	form.on('submit(tijiao)',function(){
		layer.load();
	});
});