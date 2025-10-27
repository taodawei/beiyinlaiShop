var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon').next().slideToggle(200);
		stopPropagation(eve); 
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
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
function hideSearch(){
	$('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectStatus(status,title){
	$("#status").val(status);
	$(".splist_up_01_left_02_up").eq(0).find("span").html(title);
	reloadTable(0);
}
function bind(){
	var key = getPdtId();
	$("#editId").val(key);
	$('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
}
function select_user(uid,uname,uphone){
	var nowIndex = $("#nowIndex").val();
	var nowTr = $(".splist_down1 .layui-table-main tr[data-index='"+nowIndex+"']").eq(0);
	var cardNum = nowTr.find("td[data-field='cardId'] div").text();
	var cardId = nowTr.find("td[data-field='id'] div").text();
	layer.confirm('确定要将卡“'+cardNum+'”绑定给“'+uname+'('+uphone+')”吗？', {
		btn: ['确定','取消'],
	},function(){
		$.ajax({
			type: "POST",
			url: "?s=users&a=bind_card",
			data: "cardId="+cardId+"&userId="+uid,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				if(resdata.code==0){
					layer.msg(resdata.message,function(){});
					return false;
				}else{
					layer.msg('操作成功',{icon:1});
					hideSearch();
					reloadTable(1);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
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
		top=top-80;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+25;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".splist_down1 .layui-table-main tr[data-index='"+index+"']").eq(0);
	var binduser = nowTr.find("td[data-field='binduser'] div").text();
	if(binduser==''){
		if($("#mingxiBtn").next().length==0){
			return false;
		}
		$("#mingxiBtn").hide();
		$("#bindBtn").show();
	}else{
		$("#mingxiBtn").show();
		$("#bindBtn").hide();
	}
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
//浏览详情
function view(){
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var jiluId = $("#jiluId").val();
	var returnurl = $("#returnurl").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?s=yyyx&a=viewGiftCardJilu&id="+jiluId+"&status="+status+"&keyword="+keyword+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?s=users&a=gift_card_luishui&id="+getPdtId()+"&returnurl="+returnurl+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".splist_down1 .layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	var jiluId = $("#jiluId").val();
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
			,jiluId:jiluId
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}
function reloadTable1(curpage){
	var keyword = $("#keyword_user").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	userListTable.reload({
		where: {
			keyword:keyword
		},page: {
			curr: page
		}
	});
}