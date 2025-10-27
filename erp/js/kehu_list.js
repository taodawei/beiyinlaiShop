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
		$('.splist_up_01_left_02_down').slideToggle(200);
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
	$("td[data-field='title']").click(function(){
		alert(1);
	});
});
//批量操作
function shangjia(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要开通选中的'+num+'个客户吗？注：只有创建了账号的客户才能开通', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=kehu&a=shangjia",
			data: "&ids="+ids,
			dataType:"json",timeout : 20000,
			success: function(resdata){
				layer.closeAll('loading');
				$("#selectedNum").html('0');
				$("#selectedIds").val('');
				$(".splist_up_01").show();
				$(".splist_up_02").hide();
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
function xiajia(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要禁用选中的'+num+'个客户吗？注：只有创建了账号的客户才能禁用', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=kehu&a=xiajia",
			data: "&ids="+ids,
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
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectLevel(status,title){
	$("#level").val(status);
	$(".splist_up_01_left_02_up span").html(title);
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
//删除单个产品
function del_kehu(params){
	var pdtId = getPdtId();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=kehu&a=delete",
		data: "&id="+pdtId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function baojiadan(){
	var pdtId = getPdtId();
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=kehu&level='+level+"&status="+status+"&keyword="+keyword+"&uname="+uname+"&areaId="+areaId+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=kehu&a=baojiadan&id="+pdtId+"&url="+url;
}
function zhanghu(){
	var pdtId = getPdtId();
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=kehu&level='+level+"&status="+status+"&keyword="+keyword+"&uname="+uname+"&areaId="+areaId+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=money&a=acc_detail&id="+pdtId+"&returnurl="+url;
}
//编辑产品
function edit_kehu(params){
	var pdtId = getPdtId();
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=kehu&level='+level+"&status="+status+"&keyword="+keyword+"&uname="+uname+"&areaId="+areaId+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=kehu&a=edit&id="+pdtId+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
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
			,level:level
			,status:status
			,keyword:keyword
			,uname:uname
			,areaId:areaId
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
//导出导入操作
function daochu(){
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&level="+level+"&status="+status+"&keyword="+keyword+"&uname="+uname+"&areaId="+areaId);
}
//编辑产品
function view_jilu(nowPage,jiluId){
	var level = $("#level").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var uname = $("#uname").val();
	var areaId = $("#areaId").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=kehu&level='+level+"&status="+status+"&keyword="+keyword+"&uname="+uname+"&areaId="+areaId+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=kehu&a=edit&id="+jiluId+"&url="+url;
}