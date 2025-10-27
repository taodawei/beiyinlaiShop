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
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function selectStar(id,cont){
	star = id;
	$(".splist_up_01_left_02_up span").html(cont);
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
	if(status==1){
		$("#sheheBtn").show();
		$("#huifuBtn").show();
	}else if(status==2){
		$("#sheheBtn").hide();
		$("#huifuBtn").show();
	}else if(status==3){
		$("#sheheBtn").hide();
		$("#huifuBtn").hide();
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
			,status:status,
	    	star:star,
	    	keyword:keyword,
	    	pdtName:pdtName,
	    	orderId:orderId,
	    	startTime:startTime,
	    	endTime:endTime,
	    	username:username
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}
function comment_shenhe(){
	var ids = getPdtId();
	layer.confirm('确定要审核该评论吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=comment_shenhe",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功');
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
}
function pi_shenhe(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择评论！",function(){});
		return false;
	}
	layer.confirm('确定要审核选中的评论吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=comment_shenhe",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功');
					$("#selectedIds").val('');
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
}
function pi_delete(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择要删除的评论！",function(){});
		return false;
	}
	layer.confirm('确定要删除选中的评论吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=comment_delete",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功');
					$("#selectedIds").val('');
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
}
function comment_delete(){
	var ids = getPdtId();
	layer.confirm('确定要删除该评论吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=comment_delete",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message,{icon:5});
				}else{
					layer.msg('操作成功');
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
}
function pi_huifu(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择要回复的评价！",function(){});
		return false;
	}
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $.trim($("#e_beizhu").val());
			if(beizhu==''){
				layer.msg("请输入回复内容",function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order&a=comment_huifu",
				data: "ids="+ids+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						$("#selectedIds").val('');
						$(".splist_up_01").show();
						$(".splist_up_02").hide();
						reloadTable(1);
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">评价回复</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="请输入回复内容"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function comment_huifu(){
	var ids = getPdtId();
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $.trim($("#e_beizhu").val());
			if(beizhu==''){
				layer.msg("请输入回复内容",function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order&a=comment_huifu",
				data: "ids="+ids+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						layer.msg('操作成功');
						reloadTable(1);
					}
				},
				error: function(){
					layer.closeAll();
					layer.msg('网络错误，请检查网络',{icon:5});
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">评价回复</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="请输入回复内容"></textarea>'+
		'</div>'+
		'</div>'
	});
}