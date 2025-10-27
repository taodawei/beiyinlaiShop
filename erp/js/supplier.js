var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
		hideTanchu("splist_up_01_left_03");
	});
	$('.splist_up_01_left_03_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$(this).next().slideToggle(200);
		stopPropagation(eve); 
	});
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
//选择状态
function selectZt(status,title){
	$("#s_status").val(status);
	$(".splist_up_01_left_03_up span").html(title);
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
function edit(){
	location.href='?m=system&s=supplier&a=add&id='+getPdtId();
}
function detail(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var url = '?m=system&s=supplier&a=index&status='+status+"&keyword="+keyword;
	url = encodeURIComponent(url);
	location.href='?m=system&s=supplier&a=detail&id='+getPdtId()+'&url='+url;
}
function wanglai(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var url = '?m=system&s=supplier&a=index&status='+status+"&keyword="+keyword;
	url = encodeURIComponent(url);
	location.href='?m=system&s=supplier&a=wanglais&id='+getPdtId()+'&url='+url;
}
function del(){
	var id = getPdtId();
	layer.confirm('确定删除该供应商吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=supplier&a=delete",
			data: "&id="+id,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==1){
					layer.msg('操作成功');
					reloadTable(1);
				}else{
					layer.msg(resdata.message,function(){});
				}				
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function gonghuo(){
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var url = '?m=system&s=supplier&a=index&status='+status+"&keyword="+keyword;
	url = encodeURIComponent(url);
	location.href='?m=system&s=supplier&a=gonghuo&id='+getPdtId()+'&url='+url;
}
//获取当前选中的经销商Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
  	var status = $("#s_status").val();
  	var keyword = $("#keyword").val();
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
			,status:status
			,keyword:keyword
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
function view_detail(nowPage,jiluId){
  	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var page = $("#page").val();
	var url = '?m=system&s=supplier&a=index&status='+status+"&keyword="+keyword;
	url = encodeURIComponent(url);
	location.href='?m=system&s=supplier&a=detail&id='+jiluId+'&url='+url;
}