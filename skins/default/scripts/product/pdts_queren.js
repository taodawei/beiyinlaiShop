function xiadan(){
	var str = '';
	var name = $.trim($("#name").val());
	var phone = $.trim($("#phone").val());
	if(if_user_info==1 && (name==''||phone.length!=11)){
		layer.open({content:'信息填写有误，请检查您输入的姓名和手机号',skin: 'msg',time: 2});
		return false;
	}
	if(if_kuaidi==1 && address_id==0){
		layer.open({content:'请先选择收货地址',skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2});
	var remark = $.trim($("#remark").val());
	$.ajax({
		type: "POST",
		url: "/index.php?p=22&a=create",
		data: "remark="+remark+"&name="+name+"&phone="+phone+"&if_kuaidi="+if_kuaidi+"&address_id="+address_id,
		dataType:"json",timeout:8000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.open({content:resdata.message,skin: 'msg',time: 2});
				return false;
			}
			location.href='/index.php?p=22&a=pay&id='+resdata.order_id;
		},
		error: function() {
			layer.open({content:'网络错误，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}
function show_address(){
	$("#shouhuodizhi_queren_tc").show();
}