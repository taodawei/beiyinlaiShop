function viewInfo(id,status,str){
	var infos = new Array();
	var arry = str.split(',');
	for (var i = arry.length - 1; i >= 0; i--) {
		var info = arry[i].split('|');
		infos[info[0]] = info[1];
	}
	for (info in infos) {
		if(info=='fujian'){
			if(infos[info].length>0){
				$("#show_"+info).html('');
				imgs = infos[info].split('~');
				for (var i = 0; i < imgs.length; i++) {
					$("#show_"+info).append('<a href="'+imgs[i]+'" target="_blank" style="margin-right:10px;"><img src="'+imgs[i]+'" width="120"></a>');
				}
			}else{
				$("#show_"+info).html('无');
			}
		}else{
			$("#show_"+info).text(infos[info]);
		}
	}
	if(status==0){
		$("#qrbtn").show();
	}else{
		$("#qrbtn").hide();
	}
	$("#shoukuanqueren_xiangqing_tc").attr('data-id',id).show();
}
function hideInfo(){
	$("#shoukuanqueren_xiangqing_tc").hide();
}
function addShoukuan(id){
	add_shoukuan(id);
}
function zuofei(id,dinghuoId){
	layer.confirm('是否作废该笔收款记录？', {
		btn: ['确定','取消'],
	},function(){
		$.ajax({
			type: "POST",
			url:'?m=system&s=dinghuo&a=zuofeiShoukuan',
			data: 'id='+id+"&dinghuoId="+dinghuoId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				if(resdata.code==0){
					layer.closeAll();
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.closeAll();
					reloadTable(1);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
}
function reloadTable(type){
	location.reload();
}
function z_tongguo(id){
	layer.confirm('是否通过审核该退款记录？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=money&a=z_quren",
			data: "&id="+id,
			dataType:"json",timeout : 20000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
}
function t_queren(){
	layer.confirm('确定要执行收款确认吗？', {
		btn: ['确定','取消'],
	},function(){
		var pdtId = parseInt($("#shoukuanqueren_xiangqing_tc").attr('data-id'));
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=money&a=quren",
			data: "&id="+pdtId,
			dataType:"json",timeout : 20000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
}