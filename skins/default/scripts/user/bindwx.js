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
	$("#select_city").click(function (e) {
		SelCity(this,e);
		$(".bj").show();
	});
	$(".zhuce_4_up_right").click(function(e){
		var dom = $("#select_city");
		SelCity(dom,e);
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
		url:"/index.php?p=8&a=sendSms2&phone="+phone,
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
function reg_wx(){
	var username = $("#username").val();
	var yzm = $("#yzm").val();
	if(username.length!=11){
		layer.open({content:'请输入正确的手机号码',skin: 'msg',time: 2});
		return false;
	}
	if(yzm==''){
		layer.open({content:'请输入验证码',skin: 'msg',time: 2});
		return false;
	}
	$.ajax({
		type:"POST",
		url:"/index.php?p=8&a=bindwx&tijiao=1",
		data:"username="+username+"&yzm="+yzm,
		timeout:"30000",
		dataType:"json",
		success: function(res){
			layer.open({content:res.message,skin: 'msg',time: 2});
		 	if(res.code==1){
		 		location.href = url;
			}else if(res.code==2){
				setTimeout(function(){
					location.href = '/index.php';
				},2000);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}