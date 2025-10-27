$(function(){
	$(".shenqingtuanzhang_2_mima img").click(function(){
		if($(this).attr('src')=='/skins/demo/images/denglu_16.png'){
			$(this).attr('src','/skins/demo/images/denglu_18.png');
			$("#password").attr("type",'text');
		}else{
			$(this).attr('src','/skins/demo/images/denglu_16.png');
			$("#password").attr("type",'password');
		}
	});
	$("#select_city").click(function (e) {
		SelCity(this,e);
		$(".bj").show();
	});
	$(".bj").click(function(){
		$(this).hide();
	});
});
var wait=60;
function time() {
	if (wait == 0) {
		$("#send_btn").text('重新获取');
		wait = 60;
	} else {
		$("#send_btn").text(wait+'秒');
		wait--;
		setTimeout(function() {
			time();
		},1000)
	}
}
function sendSms(){
	var phone = $("#username").val();
	if(phone.length!=11){
		layer.open({content:'请输入正确的手机号码',skin: 'msg',time: 2});
		return false;
	}
	if(wait!=60){
		return false;
	}
	layer.open({type:2});
	$.ajax({
		type:"POST",
		url:"/index.php?p=8&a=sendSms&phone="+phone,
		data:"type=reg",
		timeout:"30000",
		dataType:"json",
		success: function(res){
			layer.closeAll();
			layer.open({content:res.message,skin: 'msg',time: 2});
		 	if(res.code==1){
		 		time();
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function reg(){
	var username = $("#username").val();
	var password = $("#password").val();
	var yzm = $("#yzm").val();
	var name = $("#name").val();
	var wxh = $("#wxh").val();
	if(username.length!=11){
		layer.open({content:'请输入正确的手机号码',skin: 'msg',time: 2});
		return false;
	}
	if(name==''){
		layer.open({content:'请输入真实姓名',skin: 'msg',time: 2});
		return false;
	}
	if(wxh==''){
		layer.open({content:'请输入微信号',skin: 'msg',time: 2});
		return false;
	}
	var regu = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;
	var re = new RegExp(regu)
	if (!re.test(password)) { 
		layer.open({content:'密码必须6-16位的字母+数字的组合',skin: 'msg',time: 2});
		return false;
	}
	if(yzm==''){
		layer.open({content:'请输入验证码',skin: 'msg',time: 2});
		return false;
	}
	if(areaId==0){
		layer.open({content:'请选择所在地区',skin: 'msg',time: 2});
		return false;
	}
	$.ajax({
		type:"POST",
		url:"/index.php?p=8&a=reg_tuanzhang&tijiao=1",
		data:"username="+username+"&name="+name+"&wxh="+wxh+"&password="+password+"&yzm="+yzm+"&areaId="+areaId,
		timeout:"30000",
		dataType:"json",
		success: function(res){
			layer.open({content:res.message,skin: 'msg',time: 2});
		 	if(res.code==1){
		 		location.href = '/index.php';
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function reg1(){
	var name = $("#name").val();
	var wxh = $("#wxh").val();
	var phone = $("#phone").val();
	if(name==''){
		layer.open({content:'请输入真实姓名',skin: 'msg',time: 2});
		return false;
	}
	if(phone.length!=11){
		layer.open({content:'请输入正确的手机号码',skin: 'msg',time: 2});
		return false;
	}
	if(wxh==''){
		layer.open({content:'请输入微信号',skin: 'msg',time: 2});
		return false;
	}
	$.ajax({
		type:"POST",
		url:"/index.php?p=8&a=to_tuanzhang&tijiao=1",
		data:"name="+name+"&wxh="+wxh+"&phone="+phone,
		timeout:"30000",
		dataType:"json",
		success: function(res){
			layer.open({content:res.message,skin: 'msg',time: 2});
		 	if(res.code==1){
		 		setTimeout(function(){
		 			location.href = '/index.php';
		 		},1800);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}