$(function(){
	$(".shangpinguanli_01_left").click(function(){
		var iconspan = $(this).find("span");
		if(iconspan.attr("class")=='shangpinguanli_01_left_03'){
			iconspan.attr("class","shangpinguanli_01_left_01");
		}else if(iconspan.attr("class")=='shangpinguanli_01_left_01'){
			iconspan.attr("class","shangpinguanli_01_left_03");
		} 
		var nowli = $(this).parent().parent();
		var id = nowli.attr("data-id");
		nowli.find("div[pid='"+id+"']").slideToggle(100);
	});
});
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#channelId").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
function edit_channel(id,user,name,account){
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro' 
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			return false;
		}
		,btnAlign: 'r'
		,zIndex: layer.zIndex
		,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
		'<form action="?m=system&s=dinghuo_set&a=addBank&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+
		(id==0?'新增':'修改')+'收款账户'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:166px">'+
		'<div class="jiliang_tanchu" style="line-height:46px;">'+
		'<span>*</span> 账户名称 <input type="text" name="bank_user" id="bank_user" value="'+user+'"><br>'+
		'<span>*</span> 开户银行 <input type="text" name="bank_name" id="bank_name" value="'+name+'"><br>'+
		'<span>*</span> 银行账号 <input type="text" name="bank_account" id="bank_account" value="'+account+'"><br>'+
		'</div>'+
		'</div>'+
		'</form></div>'
		,success: function(layero){
			var btn = layero.find('.layui-layer-btn');
			btn.find('.layui-layer-btn0').attr({
				href: 'javascript:checkChannelForm();'
			});
			return false;
		}
	});
}
function checkChannelForm(){
	if($("#bank_user").val()==''){
		layer.msg('账户名称不能为空',{zIndex:99891014,anim:6,time:2000});
		$("#bank_user").focus();
		return false;
	}
	if($("#bank_name").val()==''){
		layer.msg('开户银行不能为空',{zIndex:99891014,anim:6,time:2000});
		$("#bank_name").focus();
		return false;
	}
	if($("#bank_account").val()==''){
		layer.msg('银行账号不能为空',{zIndex:99891014,anim:6,time:2000});
		$("#bank_account").focus();
		return false;
	}
	layer.load();
	$("#channelForm").submit();
}
function delChannel(id){
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo_set&a=delBank",
		data: "&id="+id,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll('loading');
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				$(".xianxiapay_02 tr[data-id='"+id+"']").remove();
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});	
}