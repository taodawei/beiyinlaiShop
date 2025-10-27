function acc_chongzhi(kehuId,accountType,kehuName){
	$("#acc_kehuName").val(kehuName);
	$("#acc_chongzhi_kehuId").val(kehuId);
	$("#chongzhi_div").css({'top':'0','opacity':'1','visibility':'visible'});
	$("#bg").show();
}
function acc_koukuan(kehuId,accountType,kehuName){
	$("#acct_kehuName").val(kehuName);
	$("#acc_koukuan_kehuId").val(kehuId);
	$("#koukuan_div").css({'top':'0','opacity':'1','visibility':'visible'});
	$("#bg").show();
}
function hide_acc_chongzhi(){
	$("#chongzhi_div").css({'top':'-10px','opacity':'0','visibility':'hidden'});
	$("#bg").hide();
}
function hide_acc_koukuan(){
	$("#koukuan_div").css({'top':'-10px','opacity':'0','visibility':'hidden'});
	$("#bg").hide();
}
function tijiaoChongzhi(){
	var money = parseFloat($("#acc_chongzhi_money").val());
	if(money<=0||isNaN(money)){
		layer.msg('充值金额不能为空或小于0！',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url:$("#chongzhiForm").attr('action'),
		data: $("#chongzhiForm").serialize(),
		dataType:"json",timeout : 20000,
		success: function(resdata){
			//console.log(resdata);
			if(resdata.code==0){
				layer.closeAll();
				layer.msg(resdata.message,{icon: 5});
			}else{
				location.reload();
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}
function tijiaoKoukuan(){
	var money = parseFloat($("#acc_koukuan_money").val());
	if(money<=0||isNaN(money)){
		layer.msg('扣款金额不能为空或小于0！',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url:$("#koukuanForm").attr('action'),
		data: $("#koukuanForm").serialize(),
		dataType:"json",timeout : 20000,
		success: function(resdata){
			//console.log(resdata);
			if(resdata.code==0){
				layer.closeAll();
				layer.msg(resdata.message,{icon: 5});
			}else{
				location.reload();
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
}