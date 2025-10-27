var nowIndexTime;
$(document).ready(function(){
	$(document).bind('click',function(){
		$("#riqilan").slideUp(200);
	});
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
function selectTime(startTime,endTime){
	$("#s_time1").html(startTime);
	$("#s_time2").html(endTime);
	$("#startTime").val(startTime);
	$("#endTime").val(endTime);
	reloadTable(0);
}
function tongguo(){
	var jiluId = getPdtId();
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
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=users&a=tixian_deal&status=1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						reloadTable(1);
					}
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定已经打款了吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入备注内容"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function zuofei(){
	var jiluId = getPdtId();
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
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=users&a=tixian_deal&status=-1",
				data: "jiluId="+jiluId+"&cont="+beizhu,
				dataType:'json',timeout:30000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.msg(resdata.message,{icon:5});
					}else{
						reloadTable(1);
					}
				}
			});
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">确定要作废该申请吗？</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入作废原因"></textarea>'+
		'</div>'+
		'</div>'
	});
}
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-100;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+55;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	status = $(".layui-table-main tr[data-index='"+index+"']").eq(0).find("td[data-field='status'] div span").eq(0).html();
	if(status=='待审核'){


		$("#operate_row").css({"top":(top+25)+"px","right":right+"px"});
		if(nowIndex==index){
			$("#operate_row").stop().slideToggle(250);
		}else{
			if($("#operate_row").css("display")=='none'){
				$("#operate_row").stop().slideDown(250);
			}
		}
		$("#nowIndex").val(index);
	}
	return false;
}
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
function reloadTable(curpage){
	layer.load();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var keyword = $("#keyword").val();
  	var type = $("#type option:selected").val();
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
			,type:type
			,keyword:keyword
			,startTime:startTime
			,endTime:endTime
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}