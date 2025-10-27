var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
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
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//选择上下架状态
function selectLevel(status,title){
	$("#level").val(status);
	$(".splist_up_01_left_02_up").eq(0).find('span').html(title);
	reloadTable(0);
}
function selectMendian(status,title){
	$("#mendianId").val(status);
	$(".splist_up_01_left_02_up").eq(1).find('span').html(title);
	reloadTable(0);
}
function selectCity(id,title){
	$("#city").val(id);
	$(".splist_up_01_left_02_up").eq(2).find('span').html(title);
	reloadTable(0);
}

function selectProvince(id,title){
    var html='<li><a href="javascript:" onclick="selectCity(0,\'全部城市\');" class="splist_up_01_left_02_down_on">全部城市</a></li>';
	$("#province").val(id);
	$(".splist_up_01_left_02_up").eq(1).find('span').html(title);
    $("#city").val(0);
    $(".splist_up_01_left_02_up").eq(2).find('span').html('全部城市');
	$.ajax({
        type: "post",
        url: "?m=system&s=users&a=getcitylist",
        data: {province:id},
        dataType: "json",
        success: function (data) {
            let list=data.list;
            for(let i=0;i<list.length;i++){
                html+='<li><a href="javascript:" onclick="selectCity('+list[i].id+',\''+list[i].title+'\');" class="splist_up_01_left_02_down_on">'+list[i].title+'</a></li>';
            }
            $("#citydata").html(html);
        }
    })
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
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = parseInt(nowTr.find("td[data-field='status'] div").eq(0).text());
	$("#operate_row").css({"top":(top-90)+"px","right":right+"px"});
	if(status==1){
		$("#operate_qiyong").hide();
		$("#operate_jinyong").show();
	}else{
		$("#operate_qiyong").show();
		$("#operate_jinyong").hide();
	}
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
function del_product(params){
	var pdtId = getPdtId();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=product&a=delete",
		data: "&ids="+pdtId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			if(resdata.code==1){
				layer.closeAll('loading');
				layer.msg('操作成功');
				reloadTable(1);
			}else{
				layer.closeAll('loading');
				layer.msg(resdata.message, {icon: 5});
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function view(params){
	if(params>0){
		pdtId = params;
	}else{
		pdtId = getPdtId();
	}
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=users&level="+level+"&mendianId="+mendianId+"&keyword="+keyword+"&money_start="+money_start+"&money_end="+money_end+"&jifen_start="+jifen_start+"&jifen_end="+jifen_end+"&dtTime_start="+dtTime_start+"&dtTime_end="+dtTime_end+"&login_start="+login_start+"&login_end="+login_end+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=users&a=basic&id="+pdtId+"&returnurl="+url;
}
function user_info(action,userId){
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = "?m=system&s=users&level="+level+"&mendianId="+mendianId+"&keyword="+keyword+"&money_start="+money_start+"&money_end="+money_end+"&jifen_start="+jifen_start+"&jifen_end="+jifen_end+"&dtTime_start="+dtTime_start+"&dtTime_end="+dtTime_end+"&login_start="+login_start+"&login_end="+login_end+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=users&a="+action+"&id="+userId+"&returnurl="+url;
}
function jin_user(){
	userId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=jinyong",
		data: "&id="+userId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			layer.msg(resdata.message);
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function qiyong_user(){
	userId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=qiyong",
		data: "&id="+userId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			layer.msg(resdata.message);
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function del_user(){
	userId = getPdtId();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=users&a=delete",
		data: "&id="+userId,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll('loading');
			layer.msg(resdata.message);
			if(resdata.code==1){
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var province = $("#province").val();
	var city = $("#city").val();
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
			,level:level,
			mendianId:mendianId,
			keyword:keyword,
			money_start:money_start,
			money_end:money_end,
			jifen_start:jifen_start,
			jifen_end:jifen_end,
			dtTime_start:dtTime_start,
			dtTime_end:dtTime_end,
			login_start:login_start,
			login_end:login_end,
			province:province,
			city:city
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}
//导出导入操作
function daochu(){
	var level = $("#level").val();
	var mendianId = $("#mendianId").val();
	var keyword = $("#keyword").val();
	var money_start = $("#money_start").val();
	var money_end = $("#money_end").val();
	var jifen_start = $("#jifen_start").val();
	var jifen_end = $("#jifen_end").val();
	var dtTime_start = $("#dtTime_start").val();
	var dtTime_end = $("#dtTime_end").val();
	var login_start = $("#login_start").val();
	var login_end = $("#login_end").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&level="+level+"&mendianId="+mendianId+"&keyword="+keyword+"&money_start="+money_start+"&money_end="+money_end+"&jifen_start="+jifen_start+"&jifen_end="+jifen_end+"&dtTime_start="+dtTime_start+"&dtTime_end="+dtTime_end+"&login_start="+login_start+"&login_end="+login_end);
}
//批量发放抵扣金
function setAllTags(){
	var ids = $("#selectedIds").val();
	var money = parseFloat($("#money").val());
	if(isNaN(money) || money<1 || money>1000){
		layer.msg('发放金额在1-1000之内',function(){});
	}
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=mendian&a=fafang_dikoujin",
		data: "&ids="+ids+"&money="+money,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll();
			layer.msg(resdata.message);
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
	return true;
}