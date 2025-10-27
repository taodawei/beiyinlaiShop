var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
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
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
//自定义字段点击上下箭头调整
function rowToUp(row){
	var nowli = $(row).parent();
	nowli.prev().before(nowli);
	if(nowli.index()==1){
		nowli.find(".rowtoup").remove();
		nowli.next().append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
	}
}
function rowToDown(row){
	var nowli = $(row).parent();
	nowli.next().after(nowli);
	if(nowli.index()==2){
		nowli.append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
		nowli.prev().find(".rowtoup").remove();
	}
}
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectStatus(status,title){
	$("#s_status").val(status);
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
//浏览详情
function view(){
	var zindex = $("#nowIndex").val();
	var str = $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='detail'] div").html();
	var infos = new Array();
	var arry = str.split(',');
	for (var i = 0;i < arry.length; i++) {
		var info = arry[i].split('|');
		infos[info[0]] = info[1];
	}
	$("#detail_ul").html('');
	var i = 0 ;
	for (info in infos) {
		i++;
		if(i<5){
			$("#show_"+info).html(infos[info]);
		}else{
			str = '<li><div class="skqr_xx_01_left">'+info+'：</div><div class="skqr_xx_01_right">'+infos[info]+'</div><div class="clearBoth"></div></li>';
			$("#detail_ul").append(str);
		}
	}
	$("#shoukuanqueren_xiangqing_tc").show();
}
function hideInfo(){
	$("#shoukuanqueren_xiangqing_tc").hide();
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function view_dinghuo(jiluId){
	var remark = $("#remark").val();
	var account = $("#account").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var level = $("#level").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=money&a=shouzhi&remark="+remark+"&account="+account+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&level="+level+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=dinghuo&a=shoukuan&id="+jiluId+"&returnurl="+url;
}
function view_tuihuo(jiluId){
	var remark = $("#remark").val();
	var account = $("#account").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var level = $("#level").val();
	var	page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=money&a=shouzhi&remark="+remark+"&account="+account+"&startTime="+startTime+"&keyword="+keyword+"&endTime="+endTime+"&level="+level+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=tuihuo&a=shoukuan&id="+jiluId+"&url="+url;
}
function reloadTable(curpage){
	var remark = $("#remark").val();
	var account = $("#account").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var level = $("#level").val();
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	productListTalbe.reload({
		where: {
			order1: order1,
			order2: order2,
			remark:remark,
			account:account,
			startTime:startTime,
			keyword:keyword,
			endTime:endTime,
			level:level
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
	$("th[data-field='detail']").hide();
}
function daochu(){
	var remark = $("#remark").val();
	var account = $("#account").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var endTime = $("#endTime").val();
	var level = $("#level").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&remark="+remark+"&account="+account+"&keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime+"&level="+level);
}