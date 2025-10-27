layui.use(['form'], function(){
	var form = layui.form
	form.on('checkbox(if_weight)', function(data){
		if(data.elem.checked){
			$("#if_weight_unit").show();
		}else{
			$("#if_weight_unit").hide();
		}
	});
	form.on('checkbox(if_addrows)', function(data){
		if(data.elem.checked){
			$("#if_addrows_unit").slideDown(100);
		}else{
			$("#if_addrows_unit").slideUp(100);
		}
	});
	form.on('checkbox(if_tags)', function(data){
		if(data.elem.checked){
			$("#if_tags_unit").slideDown(100);
		}else{
			$("#if_tags_unit").slideUp(100);
		}
	});
	form.on('checkbox(if_dinghuo)', function(data){
		if(data.elem.checked){
			$(".if_dinghuo_info").slideDown(100);
		}else{
			$(".if_dinghuo_info").slideUp(100);
		}
	});
	form.on('submit(tijiao)', function(data){
		if(priceNum!=data.field.price_num||numNum!=data.field.number_num){
		    layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '530px;'
				,shade: 0.3
				,id: 'LAY_layuipro'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					$("#productSetForm").submit();
				}
				,btnAlign: 'r'
				,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
					'<div class="spxx_shanchu_tanchu_01">'+
						'<div class="spxx_shanchu_tanchu_01_left">提示</div>'+
						'<div class="spxx_shanchu_tanchu_01_right">'+
							'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
						'</div>'+
						'<div class="clearBoth"></div>'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_02" style="text-align:center;padding:20px 0px;">'+
						'您是否确认将数量小数位设置为'+data.field.number_num+'，价格小数位设置为'+data.field.price_num+'？<div style="color:red;width:330px;margin:auto;margin-top:20px;">特别提醒：数字精度一旦设置则仅可改大不可改小，以避免数据精度丢失造成的数据错误！</div>'+
					'</div>'+
				'</div>'
			});
		}else{
			$("#productSetForm").submit();
		}
		return false;
	});
});