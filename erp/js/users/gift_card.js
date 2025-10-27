$(document).ready(function(){
	$('#cardid1').bind('input propertychange', function() {
		var val = $(this).val();
		if(val.length==16){
			$(this).val(val.substr(0,4));
			$('#cardid2').val(val.substr(4,4));
			$('#cardid3').val(val.substr(8,4));
			$('#cardid4').val(val.substr(12,4));
			$('#cardid4').focus();
		}else if(val.length==4){
			$('#cardid2').focus();
		}
	});
	$('#cardid2').bind('input propertychange', function() {
		if($(this).val().length==4){
			$('#cardid3').focus();
		}
	});
	$('#cardid3').bind('input propertychange', function() {
		if($(this).val().length==4){
			$('#cardid4').focus();
		}
	});
});
function bangding(userId){
	var card = $('#cardid1').val()+$('#cardid2').val()+$('#cardid3').val()+$('#cardid4').val();
	if(card.length!=16){
		layer.msg('卡号输入不正确');
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=bind_gift_card",
		data: "cardId="+card+"&userId="+userId,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			if(resdata.code==0){
				layer.closeAll();
				layer.msg(resdata.message,function(){});
				return false;
			}else{
				layer.closeAll();
				layer.msg('绑定成功',{icon: 1});
				reloadTable();
				$('#cardid1').val('');
				$('#cardid2').val('');
				$('#cardid3').val('');
				$('#cardid4').val('');
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}
function reloadTable(){
	productListTalbe.reload();
}