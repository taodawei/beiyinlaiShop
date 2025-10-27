var nowIndexTime,map;
$(document).ready(function(){
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+69>document.body.clientHeight){
		top=top-50;
	}

	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	
	$("#operate_row").css({"top":(top-30)+"px","right":right+"px"});
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
function edit_store(params){
	$("#cangkugl_xiugai").show();
	var kuaidiId = 0;
	var kuaidi_company = '';
	var kuaidi_title = '';
	var CustomerName = '';
	var CustomerPwd = '';
	var MonthCode = '';
	var EBusinessID = '';
	var AppKey = '';
	var e_fahuo_user = '';
	var e_fahuo_phone = '';
	var e_print_name = '';
	var e_SendSite = '';
	if(params==1){
		$("#cangkugl_xiugai .cangkugl_xiugai_01").html('修改快递');
		var zindex = $("#nowIndex").val();
		kuaidiId = getPdtId();
		var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']").eq(0);
		EBusinessID = nowTr.find("td[data-field='EBusinessID'] div").eq(0).html();
		AppKey = nowTr.find("td[data-field='AppKey'] div").eq(0).html();
		kuaidi_company = nowTr.find("td[data-field='kuaidi_company'] div").eq(0).html();
		kuaidi_title = nowTr.find("td[data-field='kuaidi_title'] div").eq(0).html();
		CustomerName = nowTr.find("td[data-field='CustomerName'] div").eq(0).html();
		CustomerPwd = nowTr.find("td[data-field='CustomerPwd'] div").eq(0).html();
		MonthCode = nowTr.find("td[data-field='MonthCode'] div").eq(0).html();
		e_fahuo_user = nowTr.find("td[data-field='fahuo_user'] div").eq(0).html();
		e_fahuo_phone = nowTr.find("td[data-field='fahuo_phone'] div").eq(0).html();
		e_print_name = nowTr.find("td[data-field='print_name'] div").eq(0).html();
		e_SendSite = nowTr.find("td[data-field='SendSite'] div").eq(0).html();
		$("#kuaidi_company option").each(function(){
			if($(this).val()==kuaidi_company){
				$(this).attr("selected","selected");
				return;
			}
		});
	}
	console.log(e_SendSite);
	$("#kuaidiId").val(kuaidiId);
	$("#EBusinessID").val(EBusinessID);
	$("#AppKey").val(AppKey);
	$("#kuaidi_title").val(kuaidi_title);
	$("#CustomerName").val(CustomerName);
	$("#CustomerPwd").val(CustomerPwd);
	$("#MonthCode").val(MonthCode);
	$("#fahuo_user").val(e_fahuo_user);
	$("#fahuo_phone").val(e_fahuo_phone);
	$("#print_name").val(e_print_name);
	$("#SendSite").val(e_SendSite);
	$("#bg").show();
	productListForm.render('select');
}
function del_store(){
	layer.confirm('确定要删除该记录吗？', {
	  btn: ['确定','取消'],
	}, function(){
		layer.load();
		id = getPdtId();
		$.ajax({
			type:"POST",
			url:"?s=store&a=del_kuaidi",
			data:"id="+id,
			timeout:"8000",
			dataType:"text",
			success: function(html){
				layer.closeAll();
				layer.msg('删除成功！');
				location.reload();
			},
			error:function(){
				layer.closeAll();
				layer.msg("超时，请刷新后重试");
			}
		});
	});
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function quxiao(){
	$("#bg").hide();
	$("#cangkugl_xiugai").hide();
}