$(function(){
	$('#password').bind('input propertychange', function() {
		var a=$("#password").val();
		if(a.length<6){
			$("#yz_password_qd").hide();
			$("#yz_password").show();
		}else if(a.length>=6&&a.length<=16){
			$("#yz_password_qd").show();
			$("#yz_password").hide();
			var reg=/^[0-9]{6,16}$|^[a-zA-Z]{6,16}$/;
			var reg1=/^[A-Za-z0-9]{6,16}$/;
			var reg2=/^\w{6,16}$/;
			if(a.match(reg)){
				$("#yz_password_qd span").eq(0).css("backgroundColor","#ff8181");
				$("#yz_password_qd span").eq(1).css("backgroundColor","#e1e1e1");
				$("#yz_password_qd span").eq(2).css("backgroundColor","#e1e1e1");
			}
			else if(a.match(reg1)){
				$("#yz_password_qd span").eq(0).css("backgroundColor","#f9c842");
				$("#yz_password_qd span").eq(1).css("backgroundColor","#f9c842");
				$("#yz_password_qd span").eq(2).css("backgroundColor","#e1e1e1");
			}
			else if(a.match(reg2)){
				$("#yz_password_qd span").eq(0).css("backgroundColor","#00c07a");
				$("#yz_password_qd span").eq(1).css("backgroundColor","#00c07a");
				$("#yz_password_qd span").eq(2).css("backgroundColor","#00c07a");
			}else{
				$("#yz_password_qd").hide();
				$("#yz_password").show();
			}
		}else if(a.length>16){
			$("#yz_password_qd").hide();
			$("#yz_password").show();
		}
	});
	$('#password1').bind('input propertychange', function() {
		var a=$("#password1").val();
		if(a.length<6){
			$("#yz_password_qd1").hide();
			$("#yz_password1").show();
		}else if(a.length>=6&&a.length<=16){
			$("#yz_password_qd1").show();
			$("#yz_password1").hide();
			var reg=/^[0-9]{6,16}$|^[a-zA-Z]{6,16}$/;
			var reg1=/^[A-Za-z0-9]{6,16}$/;
			var reg2=/^\w{6,16}$/;
			if(a.match(reg)){
				$("#yz_password_qd1 span").eq(0).css("backgroundColor","#ff8181");
				$("#yz_password_qd1 span").eq(1).css("backgroundColor","#e1e1e1");
				$("#yz_password_qd1 span").eq(2).css("backgroundColor","#e1e1e1");
			}
			else if(a.match(reg1)){
				$("#yz_password_qd1 span").eq(0).css("backgroundColor","#f9c842");
				$("#yz_password_qd1 span").eq(1).css("backgroundColor","#f9c842");
				$("#yz_password_qd1 span").eq(2).css("backgroundColor","#e1e1e1");
			}
			else if(a.match(reg2)){
				$("#yz_password_qd1 span").eq(0).css("backgroundColor","#00c07a");
				$("#yz_password_qd1 span").eq(1).css("backgroundColor","#00c07a");
				$("#yz_password_qd1 span").eq(2).css("backgroundColor","#00c07a");
			}else{
				$("#yz_password_qd1").hide();
				$("#yz_password1").show();
			}
		}else if(a.length>16){
			$("#yz_password_qd1").hide();
			$("#yz_password1").show();
		}
	});
});
function checkRepass(){
	if($("#password").val()!=$("#repass").val()){
		$("#yz_repass").css("color","#ff6d6d").html('两次输入的密码不一致！');
	}else if($("#repass").val()!=""){
		$("#yz_repass").html('<img src="images/r.png">');
	}
}
function checkRepass1(){
	if($("#password1").val()!=$("#repass1").val()){
		$("#yz_repass1").css("color","#ff6d6d").html('两次输入的密码不一致！');
	}else if($("#repass1").val()!=""){
		$("#yz_repass1").html('<img src="images/r.png">');
	}
}
function editPass(){
	$("#edit_mima_div").css({'top':'0','opacity':'1','visibility':'visible'});
}
function editPayPass(){
	$("#edit_pay_div").css({'top':'0','opacity':'1','visibility':'visible'});
}
function hideDiv(id){
	$("#"+id).css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function updatePass(userId){
	var newPass = $("#password").val();
	var renewPass = $("#repass").val();
	var reg2=/^\w{6,16}$/;
	if(!newPass.match(reg2)){
		layer.msg('密码必须是6-16位英文字母、数字、下划线的组合！',function(){});
		return false;
	}
	if(newPass!=renewPass){
		layer.msg('密码与确认密码不一致！',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=updatePass&id="+userId,
		data: "password="+newPass,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			if(resdata.code==0){
				layer.msg(resdata.message,function(){});
				return false;
			}else{
				layer.closeAll();
				layer.msg('修改成功',{icon: 1});
				hideDiv('edit_mima_div');
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}
function updatePass1(userId){
	var newPass = $("#password1").val();
	var renewPass = $("#repass1").val();
	var reg2=/^\w{6,16}$/;
	if(!newPass.match(reg2)){
		layer.msg('支付密码必须是6-16位英文字母、数字、下划线的组合！',function(){});
		return false;
	}
	if(newPass!=renewPass){
		layer.msg('支付密码与确认密码不一致！',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=updatePaypass&id="+userId,
		data: "password="+newPass,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			if(resdata.code==0){
				layer.msg(resdata.message,function(){});
				return false;
			}else{
				layer.closeAll();
				layer.msg('修改成功',{icon: 1});
				hideDiv('edit_pay_div');
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}