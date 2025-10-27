var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	$("#zanting_fahuo").click(function(){
		layer.confirm('确定要将选中发货单暂停吗？', {
		  btn: ['确定','取消'],
		},function(){
			layer.load();
			var ids = $("#selectedIds").val();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?s=fahuo&a=zanting_fahuo",
				data: "&ids="+ids,
				dataType:"json",timeout : 10000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						$(".splist_up_01").show();
						$(".splist_up_02").hide();
						reloadTable(1);
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
			return true;
		});
	});
	$("#huifu_fahuo").click(function(){
		layer.confirm('确定要将选中发货单恢复吗？', {
		  btn: ['确定','取消'],
		},function(){
			layer.load();
			var ids = $("#selectedIds").val();
			ajaxpost=$.ajax({
				type: "POST",
				url: "?s=fahuo&a=huifu_fahuo",
				data: "&ids="+ids,
				dataType:"json",timeout : 10000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						$(".splist_up_01").show();
						$(".splist_up_02").hide();
						reloadTable(1);
					}
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
			return true;
		});
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon').next().slideToggle(200);
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
function selectStore(status,title){
	$("#storeId").val(status);
	$(".splist_up_01_left_02_up").eq(0).find('span').html(title);
	reloadTable(0);
}
function selectMendian(status,title){
	$("#mendian").val(status);
	$(".splist_up_01_left_02_up").eq(1).find('span').html(title);
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
	var height = parseInt($("#operate_row").css("height"));
	if(top+height>document.body.clientHeight){
		top=top-height;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	var status = parseInt(nowTr.find("td[data-field='status'] div").eq(0).text());
	if(status<0){
		$("#sheheBtn").hide();
		$("#errorBtn").hide();
		$("#tuihuanBtn").hide();
		$("#cancelBtn").hide();
	}else if(status==0||status==1){
		$("#sheheBtn").show();
		$("#errorBtn").show();
		$("#tuihuanBtn").show();
		$("#cancelBtn").show();
	}else if(status==2||status==3){
		$("#sheheBtn").hide();
		$("#errorBtn").show();
		$("#tuihuanBtn").show();
		$("#cancelBtn").hide();
	}else if(status==2||status==4){
		$("#sheheBtn").hide();
		$("#errorBtn").hide();
		$("#tuihuanBtn").show();
		$("#cancelBtn").hide();
	}
	$("#operate_row").css({"top":(top-146)+"px","right":right+"px"});
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
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var scene = $("#scene").val();
	var status = $("#status").val();
	var type = $("#type").val();
	var orderId = $("#orderId").val();
	var startTime = $("#startTime").val();
	var keyword = $("#keyword").val();
	var mendian = $("#mendian").val();
	var storeId = $("#storeId").val();
	var endTime = $("#endTime").val();
	var kehuName = $("#kehuName").val();
	var moneystart = $("#moneystart").val();
	var moneyend = $("#moneyend").val();
	var shouhuoInfo = $("#shouhuoInfo").val();
	var pdtInfo = $("#pdtInfo").val();
	var payStatus = $("#payStatus").val();
	var kaipiao = $("#kaipiao").val();
	var fahuoTime = '';
	if($("#fahuo_time").length>0){
		fahuoTime = $("#fahuo_time").val();
	}
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
			,scene:scene
			,status:status
			,type:type
			,orderId:orderId
			,startTime:startTime
			,keyword:keyword
			,mendian:mendian
			,storeId:storeId
			,endTime:endTime
			,kehuName:kehuName
			,moneystart:moneystart
			,moneyend:moneyend
			,shouhuoInfo:shouhuoInfo
			,kaipiao:kaipiao
			,pdtInfo:pdtInfo
			,payStatus:payStatus
			,fahuoTime:fahuoTime
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}
