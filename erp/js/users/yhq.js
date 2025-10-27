function select_type(type){
	$("#type").val(type);
	$(".hyxx_youhuiquan_up .hyxx_youhuiquan_up_on").removeClass('hyxx_youhuiquan_up_on');
	$(".hyxx_youhuiquan_up a").eq(type).addClass('hyxx_youhuiquan_up_on');
	reloadTable(0);
}
function show_yhq_info(dom,id){
	$(dom).toggleClass("hyxx_youhuiquan_down_tt_span2");
	if($("#tr_"+id).length>0){
		$("#tr_"+id).toggle();
	}else{
		var str = '<tr id="tr_'+id+'"><td colspan="5" style="padding:0px;"><table class="hyxx_youhuiquan_down_table" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody>'+
		'<tr height="45"><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">券类型：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;">优惠券</div></td><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">每人限领：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td></tr>'+
		'<tr height="45"><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">优惠券标题：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">发放总量：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td></tr>'+
		'<tr height="45"><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">面值：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px; color:#ff0000;"></div></td><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">有效时间：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td></tr>'+
		'<tr height="45"><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">使用门槛：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">推广设置：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td></tr>'+
		'<tr height="45"><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">使用说明：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td><td bgcolor="#fff1b7" width="200" align="right" valign="middle"><div style="padding-right:10px;">适用商品：</div></td><td bgcolor="#fcfaea" align="left" valign="middle"><div style="padding-left:10px;"></div></td></tr></tbody></table></td><td></td></tr>';
		$(dom).parent().parent().parent().after(str);
	}
}
function reloadTable(curpage){
	layer.load();
  	var type = $("#type").val();
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
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
}