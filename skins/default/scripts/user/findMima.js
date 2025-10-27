$(function(){
	$(".zhuce_3_down_right_img2").click(function(){
		if($(this).attr('src')=='/skins/default/images/denglu_16.png'){
			$(this).attr('src','/skins/default/images/denglu_18.png');
			$("#password").attr("type",'text');
		}else{
			$(this).attr('src','/skins/default/images/denglu_16.png');
			$("#password").attr("type",'password');
		}
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
		url:"/index.php?p=8&a=sendSms1&phone="+phone,
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
	if(username.length!=11){
		layer.open({content:'请输入正确的手机号码',skin: 'msg',time: 2});
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
	$.ajax({
		type:"POST",
		url:"/index.php?p=8&a=findMima&tijiao=1",
		data:"username="+username+"&password="+password+"&yzm="+yzm,
		timeout:"30000",
		dataType:"json",
		success: function(res){
			layer.open({content:res.message,skin: 'msg',time: 2});
		 	if(res.code==1){
		 		location.href = '/index.php?p=8&a=login';
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}