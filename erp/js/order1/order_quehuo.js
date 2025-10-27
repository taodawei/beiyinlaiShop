var nowIndexTime;
$(document).ready(function(){
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
	$("#operate_row").css({"top":(top-96)+"px","right":right+"px"});
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
			order1: order1,
			order2: order2,
			keyword:keyword,
	    	pdtChanelOpt:pdtChanelOpt,
	    	pdtChanelNum:pdtChanelNum,
	    	pdtNumsOpt:pdtNumsOpt,
	    	pdtNumsNum:pdtNumsNum
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		}
	});
}
function pi_shenhe(){
	var ids = $("#selectedIds").val();
	if(ids==''){
		layer.msg("请先选择订单！",function(){});
		return false;
	}
	layer.confirm('确定要将该订单转为正常状态吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=order&a=quehuo_huifu",
			data: "&ids="+ids,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功，共审核'+resdata.num+"个订单");
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
function search_order(){
	keyword = $("#keyword").val();
	pdtChanelOpt = $("#pdtChanelOpt option:selected").val();
	pdtChanelNum = $("#pdtChanelNum").val();
	pdtNumsOpt = $("#pdtNumsOpt option:selected").val();
	pdtNumsNum = $("#pdtNumsNum").val();
	reloadTable(0);
}
function order_huifu(){
	var ids = getPdtId();
	layer.confirm('确定要将该订单转为正常状态吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?s=order&a=quehuo_huifu",
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
function quehuo_quxiao(){
	jiluId = getPdtId();
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['确定', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			if(beizhu==''){
				layer.msg('请输入取消订单原因',function(){});
				return false;
			}
			layer.load();
			$.ajax({
				type: "POST",
				url: "?s=order&a=shenhe&status=-1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
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
		'<div class="spxx_shanchu_tanchu_01_left">确定要取消订单吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入取消原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}