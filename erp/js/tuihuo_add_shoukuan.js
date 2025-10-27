layui.use(['laydate','form'], function(){
	var laydate = layui.laydate
	,form = layui.form
	var date = new Date();
	var seperator1 = "-";
});
function add_shoukuan(dinghuoId){
	layer.load();
	$.ajax({
		type: "POST",
		url:'?m=system&s=tuihuo&a=getShoukuanInfo',
		data: 'dinghuoId='+dinghuoId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			if(resdata.code==0){
				layer.closeAll();
				layer.msg(resdata.message,{icon: 5});
			}else{
				layer.closeAll();
				$.each(resdata.data,function(key,val){
					if(typeof($("#a_"+key).attr('value'))=="undefined"){
						$("#a_"+key).html(val);
					}else{
						$("#a_"+key).val(val);
					}
				});
				$("#a_dinghuoId").val(dinghuoId);
				$("#shoukuanDiv").css({'top':'0px','opacity':'1','visibility':'visible'});
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function hideShoukuan(){
	$("#shoukuanDiv").css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function shoukuanTijiao(){
	var account1 = parseFloat($("#a_account1").val());
	var daizhifu = parseFloat($("#a_daizhifu").text());
	var account2 = parseFloat($("#a_account2").val());
	var account3 = parseFloat($("#a_account3").val());
	if(account1+account2+account3>daizhifu){
		layer.msg('退款金额大于待退款金额！',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url:$("#addShoukuanForm").attr('action'),
		data: $("#addShoukuanForm").serialize(),
		dataType:"json",timeout : 20000,
		success: function(resdata){
			//console.log(resdata);
			if(resdata.code==0){
				layer.closeAll();
				layer.msg(resdata.message,{icon: 5});
			}else{
				reloadTable(1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function del_image(id){
	layer.load();
	var img = $("#image_li"+id+" img").eq(0).attr("src");
	$("#image_li"+id).remove();
	img = img.replace('?x-oss-process=image/resize,w_122','');
	var originalPic = $("#originalPic").val();
	pics = originalPic.split('|');
	for (var i = 0; i < pics.length; i++) {  
		if (pics[i] == img){
			pics.splice(i,1);
			break;
		}
	}
	originalPic = pics.join("|");
	$("#originalPic").val(originalPic);
	$.ajax({
		type: "POST",
		url: "?m=system&s=upload&a=delImg",
		data: "img="+img,
		dataType:'text',timeout : 5000,
		success: function(resdata){
			layer.closeAll('loading');
		},
		error: function() {
			layer.closeAll('loading');
		}
	});
}