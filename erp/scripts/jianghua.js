$(document).ready(function () {//判断当前复选框状态，并让label背景图与复选框同步勾选或取消
	$(".checkbox").click(function (){//判断复选框勾选状态
		if ($(this).is(':checked')) {
			$(this).parent().removeClass("iCheck");    //删除未勾选选背景图
			$(this).parent().addClass("iCheck-ed");    //添加勾选态背景图
		}
		else{
			$(this).parent().removeClass("iCheck-ed"); //删除勾选选背景图
			$(this).parent().addClass("iCheck");       //添加未勾选选背景图
		}
	});
});
