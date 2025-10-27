layui.use(['form'], function(){
	var form = layui.form;
	form.on('submit(tijiao)', function(data){
		layer.load();
	});
	guigeTable1();
});
function guigeTable1(){
	layer.load();
	var productId = $("#productId").val();
	$.ajax({
		type: "POST",
		url: "?m=system&s=product&a=getLevelPrices",
		data: "productId="+productId,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			layer.closeAll('loading');
			$("#moreGuige").html(resdata.table);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}