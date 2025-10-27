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
	$('#password').bind('input propertychange', function() {
		if($(this).val().length>3){
			$(".denglu_tijiao a").css("background",'#cf2950');
		}else{
			$(".denglu_tijiao a").css("background",'#d6d6d6');
		}
	});
});
function login(){
	var username = $("#username").val();
	var password = $("#password").val();
	if(username.length<6||password.length<2){
		layer.open({content:'请输入正确的手机号和密码',skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2,content:'登录中'});
	$.ajax({
		type: "POST",
		url: "/index.php?p=8&a=login&tijiao=1",
		data: "username="+username+"&password="+password,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.open({content:resdata.message,skin: 'msg',time: 2});
			if(resdata.code==1){
				location.href=url;
			}
		}
	});
}
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
function send_login_msg(){
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
		url:"/index.php?p=8&a=send_login_msg&phone="+phone,
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
function msg_login(){
	var username = $("#username").val();
	var yzm = $("#yzm").val();
	if(username.length<11||yzm.length<4){
		layer.open({content:'请输入正确的手机号和密码',skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2,content:'登录中'});
	$.ajax({
		type: "POST",
		url: "/index.php?p=8&a=msg_login&tijiao=1",
		data: "username="+username+"&yzm="+yzm,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.open({content:resdata.message,skin: 'msg',time: 2});
			if(resdata.code==1){
				location.href=url;
			}
		}
	});
}
function qiehuan_login(index){
	$(".denglu_2 li a").removeClass('denglu_2_on');
	$(".denglu_2 li").eq(index).find('a').addClass('denglu_2_on');
	if(index==1){
		$(".denglu_3 li").eq(1).hide();
		$(".denglu_3 li").eq(2).show();
		$(".denglu_4").eq(1).show();
		$(".denglu_4").eq(0).hide();
	}else{
		$(".denglu_3 li").eq(1).show();
		$(".denglu_3 li").eq(2).hide();
		$(".denglu_4").eq(1).hide();
		$(".denglu_4").eq(0).show();
	}
	
}