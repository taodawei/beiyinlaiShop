layui.use(['laydate','upload','form'], function(){
	var laydate = layui.laydate
	,form = layui.form
	,upload = layui.upload
	var date = new Date();
	var seperator1 = "-";
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var strDate = date.getDate();
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	var currentdate = year + seperator1 + month + seperator1 + strDate;
	laydate.render({
		elem: '#a_dtTime'
		,max:currentdate
		,value:currentdate
	});
	upload.render({
		elem: '#uploadPdtImage'
		,url: '?m=system&s=upload&a=upload'
		,before:function(){
			layer.load();
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
			}else{
				var nums = parseInt($('#uploadImages').attr("data-num"))+1;
				$('#uploadImages').before('<li id="image_li'+nums+'"><a><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
				$('#uploadImages').attr("data-num",nums);
				var originalPic = $("#originalPic").val();
				if(originalPic==''){
					originalPic = res.url;
				}else{
					originalPic = originalPic+'|'+res.url;
				}
				$("#originalPic").val(originalPic);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	form.on('radio(pay_type1)',function(){
		$("#a_bankDiv").hide();
	});
	form.on('radio(pay_type2)',function(){
		$("#a_bankDiv").show();
	});
});
function add_shoukuan(dinghuoId){
	layer.load();
	$.ajax({
		type: "POST",
		url:'?m=system&s=dinghuo&a=getShoukuanInfo',
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
	var account1_yue = parseFloat($("#a_yue_account1").text());
	var account2 = parseFloat($("#a_account2").val());
	var account2_yue = parseFloat($("#a_yue_account2").text());
	var account3 = parseFloat($("#a_account3").val());
	var account3_yue = parseFloat($("#a_yue_account3").text());
	if(account1>account1_yue){
		layer.msg('现金账户余额不足！',function(){});
		$("#a_account1").focus();
		return false;
	}
	if(account2>account2_yue){
		layer.msg('预付款账户余额不足！',function(){});
		$("#a_account2").focus();
		return false;
	}
	if(account3>account3_yue){
		layer.msg('返点账户余额不足！',function(){});
		$("#a_account3").focus();
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url:$("#addShoukuanForm").attr('action'),
		data: $("#addShoukuanForm").serialize(),
		dataType:"json",timeout : 20000,
		success: function(resdata){
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