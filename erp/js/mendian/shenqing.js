function del_shenqing(jiluId){
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=mendian&a=delShenqing",
		data: "id="+jiluId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.alert('操作成功!',function(){
				location.href=returnurl;
			});
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function bohui(jiluId){
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=mendian&a=bohuiShenqing",
		data: "id="+jiluId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.alert('操作成功!',function(){
				location.href=returnurl;
			});
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}