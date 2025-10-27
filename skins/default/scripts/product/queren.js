function show_address(){
	$("#shouhuodizhi_queren_tc").show();
}
function xiadan(){
	var str = '';
	if(address_id==0){
		layer.open({content:'请先选择收货地址~~',skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2});
	var remark = $("#remark").val();
	var fapiao_id = $("#fapiao_id").val();
	var if_fapiao = $("#if_fapiao").val();
	var fapiao_type = $("#fapiao_type").val();
	var fapiao_leixing = $("#fapiao_leixing").val();
	var fapiao_cont = $("#fapiao_cont").val();
	var fapiao_com_title = $("#fapiao_com_title").val();
	var fapiao_shibiema = $("#fapiao_shibiema").val();
	var shoupiao_phone = $("#shoupiao_phone").val();
	var shoupiao_email = $("#shoupiao_email").val();
	var fapiao_address = $("#fapiao_address").val();
	var fapiao_phone = $("#fapiao_phone").val();
	var fapiao_bank_name = $("#fapiao_bank_name").val();
	var fapiao_bank_card = $("#fapiao_bank_card").val();
	var toaction = yushouId>0?'create':'create_zong';
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=create",
		data: "tuan_type="+tuan_type+"&tuan_id="+tuan_id+"&address_id="+address_id+"&yhq_id="+yhq_id+"&remark="+remark+"&yushouId="+yushouId+"&xinren="+xinren+"&lpk_id="+lpk_id+"&lpk_kedi="+lpk_kedi+"&fapiao_id="+fapiao_id+"&if_fapiao="+if_fapiao+"&fapiao_type="+fapiao_type+"&fapiao_leixing="+fapiao_leixing+"&fapiao_cont="+fapiao_cont+"&fapiao_com_title="+fapiao_com_title+"&fapiao_shibiema="+fapiao_shibiema+"&shoupiao_phone="+shoupiao_phone+"&shoupiao_email="+shoupiao_email+"&fapiao_address="+fapiao_address+"&fapiao_phone="+fapiao_phone+"&fapiao_bank_name="+fapiao_bank_name+"&fapiao_bank_card="+fapiao_bank_card,
		dataType:"json",timeout:8000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.open({content:resdata.message,skin: 'msg',time: 2});
				return false;
			}
			location.href='/index.php?p=19&a=pay&id='+resdata.order_id;
		},
		error: function() {
			layer.open({content:'网络错误，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}
function select_yhq(id,jian,title){
	if(lpk_id>0){
		layer.open({content:'抵扣金与优惠券不能同时使用',skin: 'msg',time: 2});
		return false;
	}
	$("#yhq_cont").html(title+'(<font color="red">-'+jian+'</font>)<img src="/skins/default/images/querendingdan_12.png"/>');
	$('#cp_youhuiquan_tc').hide();
	money_zong = parseFloat(money_zong) + parseFloat(yhq_money) - parseFloat(jian);
	money_zong = money_zong.toFixed(2);
	$("#money_zong").text(money_zong);
	yhq_id = id;
	yhq_money = jian;
}
function select_lpk(id,jian){
	lpk_id = id;
	money_zong = parseFloat(money_zong) + parseFloat(yhq_money) + parseFloat(lpk_kedi) - parseFloat(jian);//加上原来减去的钱
	money_zong = money_zong.toFixed(2);
	$("#money_zong").text(money_zong);
	lpk_kedi = jian;
	$("#lpk_cont font").html('-'+jian);
	$('#cp_lpk_tc').hide();
	yhq_id = 0;yhq_money=0;
	$("#yhq_cont").html('不使用');
}

function select_fapiao_type(index){
	$("#fapiao_type").val(index);
	$(".qddd_fapiao_4_02 ul li .qddd_fapiao_4_02_on").removeClass('qddd_fapiao_4_02_on');
	$(".qddd_fapiao_4_02 ul li").eq(index-1).find('a').addClass('qddd_fapiao_4_02_on');
	if(index==1){
		$("#qddd_fapiao_4_03").hide();
		$("#fapiao_com_title").val('');
		$("#fapiao_shibiema").val('');
	}else{
		$("#qddd_fapiao_4_03").show();
	}
}
function select_fapiao_leixing(index){
	$(".qddd_fapiao_3 ul li .qddd_fapiao_3_on").removeClass('qddd_fapiao_3_on');
	$(".qddd_fapiao_3 ul li").eq(index-1).find('a').addClass('qddd_fapiao_3_on');
	switch(index){
		case 1:
			$("#fapiao_leixing").val('电子普通发票');
		break;
		case 2:
			$("#fapiao_leixing").val('普通发票');
		break;
		case 3:
			$("#fapiao_leixing").val('增值税专用发票');
		break;
	}
}
function qiehua_fp_cont(index){
	$(".qddd_fapiao_6_02 .qddd_fapiao_6_02_on").removeClass('qddd_fapiao_6_02_on');
	$(".qddd_fapiao_6_02 li").eq(index).find('a').addClass('qddd_fapiao_6_02_on');
	if(index==1){
		$("#fapiao_cont").val('商品类别');
	}else{
		$("#fapiao_cont").val('商品明细');
	}
}