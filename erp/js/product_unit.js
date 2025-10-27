function edit_unit(id,title){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			return false;
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
		'<form action="?m=system&s=product_unit&a=addProductUnit" method="post" id="unitForm"><div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+
		(id==0?'新增':'修改')+'计量单位'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02">'+
		'<div class="jiliang_tanchu">'+
		'<span>*</span> 名称 <input type="text" name="title" id="unit_title" value="'+title+'"><br>'+
		'</div>'+
		'</div>'+
		'</form></div>'
		,success: function(layero){
			var btn = layero.find('.layui-layer-btn');
			btn.find('.layui-layer-btn0').attr({
				href: 'javascript:checkBrandForm();'
			});
			return false;
		}
	});
}
var submit = 1;
function checkBrandForm(){
	if(submit==1){
		if($("#unit_title").val()==''){
			layer.msg('名称不能为空',{zIndex:99891014,anim:6,time:2000,icon: 5});
			return false;
		}
		submit = 0;
		$("#unitForm").submit();
	}
}
function delUnit(id){
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=product_unit&a=delUnit",
		data: "&id="+id,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll('loading');
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				var ids = resdata.ids.split(',');
				for(i=0;i<ids.length;i++){
					$(".shangpinguanli li[data-id='"+ids[i]+"']").remove();
				}
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});	
}