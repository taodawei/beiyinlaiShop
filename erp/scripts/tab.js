// JavaScript Document

function qiehuanPdt(id){
	//class是jiebie_fenlei 底下的li a 消除class为xinwen_1_left_on  removeClass：是给某个标签去掉class
	$(".jiebie_fenlei li a").removeClass("jiebie_fenlei_on");
	//a标签加上class 选中效果的样式  addClass：是给某个标签添加class
	$("#pdtMenu"+id).addClass("jiebie_fenlei_on");  
	//一共几组 当前是3组
	var counts=3;
	for(var i=0;i<=counts;i++){
		//将所有的Tab 先隐藏掉 hide()是隐藏方法
		$("#Tab_"+i).hide();
	}
	//根据id 显示相应的Tab show()是显示方法
	$("#Tab_"+id).show();
}

function selectTab(id){
	$(".sp_name_tt li a").removeClass("sp_name_tt_on");
	$("#biao"+id).addClass("sp_name_tt_on");
	
}