var nowIndexTime;
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#super_channel").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//分类
	$('.splist_up_01_left_01_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_01_down').slideToggle(200);
		stopPropagation(eve); 
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$(this).next().slideToggle(200);
		stopPropagation(eve); 
	});
	//高级搜索
	$('.splist_up_01_right_2_up').click(function(){
		$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});
	});
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
	$(".splist_up_02_1").click(function(){
		$(".splist_up_02").hide();
		$(".splist_up_01").show();
	});
	
});
function xiajia(){
	var id = getPdtId();
	layer.confirm('确定要下架该产品吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=zhuisu&a=xiajia",
			data: "&id="+id,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				layer.msg('操作成功');
				reloadTable(1);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function shangjia(){
	var id = getPdtId();
	layer.confirm('确定要上架该产品吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=zhuisu&a=shangjia",
			data: "&id="+id,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				layer.msg('操作成功');
				reloadTable(1);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
//选择分类
function selectChannel(channelId,title){
	$("#channelId").val(channelId);
	$(".splist_up_01_left_01_up span").html(title);
	reloadTable(0);
}
//选择上下架状态
function selectStatus(status,title){
	$("#s_status").val(status);
	$(".splist_up_01_left_02_up").eq(0).find("span").html(title);
	reloadTable(0);
}
//选择销售类型
function selectPaytype(status,title){
	$("#payType").val(status);
	$(".splist_up_01_left_02_up").eq(1).find("span").html(title);
	reloadTable(0);
}
//选择促销类型
function selectCuxiao(status,title){
	$("#cuxiao").val(status);
	$(".splist_up_01_left_02_up").eq(2).find("span").html(title);
	reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-50;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	$("#operate_row").css({"top":(top-90)+"px","right":right+"px"});
	if(nowIndex==index){
		$("#operate_row").stop().slideToggle(250);
	}else{
		if($("#operate_row").css("display")=='none'){
			$("#operate_row").stop().slideDown(250);
		}
	}
	$("#nowIndex").val(index);
	return false;
}
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
//编辑产品
function edit_product(params){
	var zindex = $("#nowIndex").val();
	var pdtId = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
	var key_vals = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='key_vals'] div").text();
	var channelId = $("#channelId").val();
	var brandId = $("#brandId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=zhuisu&a=pdts&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=zhuisu&a=editPdts&id="+pdtId+"&url="+url;	
}
function view_product(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var key_vals = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='key_vals'] div").text();
	var channelId = $("#channelId").val();
	var brandId = $("#brandId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=pdts&channelId='+channelId+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=pdts&a=edit&id="+pdtId+"&url="+url;	
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var channelId = $("#channelId").val();
	var brandId = $("#brandId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var payType = $("#payType").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	productListTalbe.reload({
		where: {
			order1: order1
			,order2: order2
			,channelId:channelId
			,brandId:brandId
			,status:status
			,keyword:keyword
			,tags:tags
			,source:source
			,cuxiao:cuxiao
			,payType:payType
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
//自定义字段相关
function showRowset(){
	$("#bg").show();
	$("#xianshiziduan").show().animate({"right":"0px"},300);
}
function hideRowset(){
	$("#xianshiziduan").animate({"right":"-259px","display":"none"},300);
	$("#bg").hide();
}
//导出导入操作
function daochu(){
	var channelId = $("#channelId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&channelId="+channelId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao);
}