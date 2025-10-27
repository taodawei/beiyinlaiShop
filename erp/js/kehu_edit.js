$(function(){
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
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
});
function selectedUser(id,name){
	$("#userId").val(id);
	$("#uname").val(name);
	hide_myModal();
}
function checkKehuTitle(id){
	var title = $("#title").val();
	if(title!=''){
		$.ajax({
			type: "POST",
			url: "/erp_service.php?action=checkKehuTitle",
			data: "id="+id+"&title="+title,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				if(resdata.code==0){
					layer.msg(resdata.message,function(){});
					$("#title").addClass('layui-form-danger').focus();
				}else{
					$("#title").removeClass('layui-form-danger');
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请重试', {icon: 5});
			}
		});
	}
}
function checkUsername(){
	var username = $("#username").val();
	var reg1=/^[A-Za-z0-9]{4,20}$/;
	if(username.match(reg1)){
		$.ajax({
			type: "POST",
			url: "/erp_service.php?action=checkKehuUsername",
			data: "username="+username,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				if(resdata.code==0){
					layer.msg(resdata.message,function(){});
					$("#username").addClass('layui-form-danger').focus();
				}else{
					$("#yz_username").css("color","green").html('账号可以使用。');
					$("#username").removeClass('layui-form-danger');
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请重试', {icon: 5});
			}
		});
	}else{
		$("#yz_username").css("color","#ff6d6d").html('账号为4-20位英文字母与数字组合！');
	}
}
function checkRepass(){
	if($("#password").val()!=$("#repass").val()){
		$("#yz_repass").css("color","#ff6d6d").html('两次输入的密码不一致！');
	}else if($("#repass").val()!=""){
		$("#yz_repass").html('<img src="images/r.png">');
	}
}
function repass(id,username){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '730px;'
		,shade: 0.3
		,id: 'LAY_layuipro' 
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			return false;
		}
		,btnAlign: 'r'
		,zIndex: layer.zIndex
		,content: '<div class="spxx_shanchu_tanchu" style="display:block;width:730px">'+
		'<form action="?m=system&s=dinghuo_set&a=addLevel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+
		'修改客户登录密码'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:166px;">'+
		'<div class="jiliang_tanchu" style="line-height:46px;">'+
		'　　<span>*</span> 账号 <input type="text" class="layui-input disabled" style="width:250px;display:inline-block;" readonly="true" value="'+username+'"><br>'+
		'　　<span>*</span> 密码 <input type="password" class="layui-input" style="width:250px;display:inline-block;" name="newPass" onblur="checkNewpass();" id="newPass"><div style="display:inline-block;padding-left:15px;color:#ff6d6d" id="yz_newpass">密码为6-16位英文字母、数字、下划线组合！</div><br>'+
		'<span>*</span> 重复密码 <input type="password" class="layui-input" style="width:250px;display:inline-block;" name="renewPass" onblur="checkReNewpass();" id="renewPass"><div style="display:inline-block;padding-left:15px;color:#ff6d6d" id="yz_renewpass"></div><br>'+
		'</div>'+
		'</div>'+
		'</form></div>'
		,success: function(layero){
			var btn = layero.find('.layui-layer-btn');
			btn.find('.layui-layer-btn0').attr({
				href: 'javascript:checkPassForm('+id+',\''+username+'\');'
			});
			return false;
		}
	});
}
function jiebang(id,dom){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro' 
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			$.ajax({
				type: "POST",
				url: "?m=system&s=kehu&a=jiebang&id="+id,
				data: "",
				dataType:"json",timeout : 20000,
				success: function(resdata){
					if(resdata.code==0){
						layer.msg(resdata.message,function(){});
						return false;
					}else{
						layer.closeAll();
						layer.msg('解绑成功',{icon: 1});
						$(dom).remove();
						return false;
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败，请重试', {icon: 5});
				}
			});
		}
		,btnAlign: 'r'
		,zIndex: layer.zIndex
		,content: '<div class="spxx_shanchu_tanchu" style="display:block;">'+
		'<form action="?m=system&s=dinghuo_set&a=addLevel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+
		'确定要解绑手机号吗？'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:126px;">'+
		'<div class="jiliang_tanchu" style="line-height:46px;">请确认是否解绑该手机号码？解绑后需要订货端重新绑定。</div>'+
		'</div>'+
		'</form></div>'
	});
}
function checkNewpass(){
	var repass = $("#newPass").val();
	var reg2=/^\w{6,16}$/;
	if(repass.match(reg2)){
		$("#yz_newpass").html('');
	}else{
		$("#yz_newpass").html('密码为6-16位英文字母、数字、下划线组合！');
	}
}
function checkReNewpass(){
	if($("#newPass").val()!=$("#renewPass").val()){
		$("#yz_renewpass").html('两次输入的密码不一致！');
	}else{
		$("#yz_renewpass").html('');
	}
}
function checkPassForm(id){
	var newPass = $("#newPass").val();
	var renewPass = $("#renewPass").val();
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
		url: "?m=system&s=kehu&a=updatePass&id="+id,
		data: "password="+newPass,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			if(resdata.code==0){
				layer.msg(resdata.message,function(){});
				return false;
			}else{
				layer.closeAll();
				layer.msg('修改成功',{icon: 1});
				return false;
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}