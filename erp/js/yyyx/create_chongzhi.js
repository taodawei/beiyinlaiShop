var nowIndexTime;
$(function(){
	$(document).bind('click',function(){
		$(".sprukuadd_03_tt_addsp_erji").hide();
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	$('.splist_up_01_left_01_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_01_down').slideToggle(200);
		stopPropagation(eve);
	});
	 $(".sprukulist_01").click(function(eve){
	 	$("#riqilan").slideToggle(200);
	 	stopPropagation(eve);
	 });
});
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//选择分类
function selectChannel(channelId,title){
	$("#channelId").val(channelId);
	$(".splist_up_01_left_01_up span").html(title);
	reloadTable(0);
}
//加载子分类
function loadZiChannels(menuId,ceng,hasnext){
	var channelDiv = $(".splist_up_01_left_01_down");
	if($("#ziChannels"+ceng).length==0&&hasnext==1){
		var ulstr = '<ul id="ziChannels'+ceng+'"><div style="text-align:center;"><img src="images/loading.gif"></div></ul>';
		var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
		channelDiv.css("width",(nowWidth+200)+"px");
		channelDiv.append(ulstr);
	}else{
		if(ceng<4&&$("#ziChannels4").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels4").remove();
		}
		if(ceng<3&&$("#ziChannels3").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels3").remove();
		}
		if($("#ziChannels"+ceng).length>0&&hasnext==0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels"+ceng).remove();
		}else{
			$("#ziChannels"+ceng).html('<div style="text-align:center;"><img src="images/loading.gif"></div>');
		}
	}
	if(hasnext==1){
		ajaxpost=$.ajax({
			type: "POST",
			url: "/erp_service.php?action=get_zi_channels",
			data: "id="+menuId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				var listr = '';
				for(var i=0;i<resdata.items.length;i++){
					if(ceng<4){
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" onmouseenter="loadZiChannels('+resdata.items[i].id+','+(ceng+1)+','+resdata.items[i].hasNext+');" class="allsort_01_tlte">'+resdata.items[i].title+(resdata.items[i].hasNext==1?' <span><img src="images/biao_24.png"></span>':'')+' </a></li>';
					}else{
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" class="allsort_01_tlte">'+resdata.items[i].title+'</a></li>';
					}
				}
				$("#ziChannels"+ceng).html(listr);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}
}
function reloadTable(curpage){
	layer.load();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
	$("input[name^='inventoryId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	var page = 1;
	productListTalbe.reload({
		where: {
			startTime:startTime
			,endTime:endTime
		},page: {
			curr: page
		}
	});
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="/erp/images/biao_66.png"/></a>'+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+units+
	'<input type="hidden" name="inventoryId['+id+']" value="'+inventoryId+'">'+
	'<input type="hidden" name="inventoryPdtId['+id+']" value="'+productId+'">'+
	'</td>';
	$("#rowTr"+id).html(str);
	addRow();
}
//输入获取产品列表
function getPdtInfo(id,keyword){
	$("#pdtList"+id+" ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
	var hasIds = '0';
	$("input[name^='inventoryId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getGonghuoList&id="+id,
		data: "keyword="+keyword+"&hasIds="+hasIds+"&cuxiao=1",
		dataType:'text',timeout : 10000,
		success: function(resdata){
			$("#pdtList"+id+" ul").html(resdata);
		}
	});
}
function showpdtList(id,keyword){
	$("#pdtList"+id).show();
	getPdtInfo(id,keyword);
}
function hidePdtList(id,keyword){
	$("#pdtList"+id).hide();
}
//显示所有产品列表
function showAllpdts(key){
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	if(startTime==''||endTime==''){
		layer.msg("请先选择活动时间");
		return false;
	}
    $("#editId").val(key);
	$('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
	reloadTable(0);
}
function hideSearch(){
	$('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function add_qujian(dom,type){
	var rows = parseInt($("#qujian"+type).attr("rows"));
	rows = rows+1;
	//var accordType = parseInt($("input[name='accordType']:checked").val());
	accord_name = '金额';
	var str = '<div id="rows_'+type+'_'+rows+'">'+
        '充值<font class="accord_name">'+accord_name+'</font>每满<span><input type="number" onchange="checkman(\''+type+'_'+rows+'\');" step="1" id="man_'+type+'_'+rows+'" name="man_'+type+'_'+rows+'" /></span>，';
        switch(type){
        	case 1:
                str = str+'返金额<span><input type="number" step="1" min="0" name="jian_'+type+'_'+rows+'" onchange="checkjian(\''+type+'_'+rows+'\');" id="jian_'+type+'_'+rows+'"/></span>元';
        	break;
        	case 2:
        		str = str+'返积分<span><input type="number" step="1" name="jian_'+type+'_'+rows+'" onchange="checkjian(\''+type+'_'+rows+'\');" id="jian_'+type+'_'+rows+'"/></span>';
        	break;
        	case 3:
                str = str+'赠优惠券<a href="javascript:" onclick="showAllpdts('+rows+');" id="pdt_'+type+'_'+rows+'" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp">请选择优惠券</a><span><input type="number" step="1" name="jian_'+type+'_'+rows+'"/></span>个'+
                '<input type="hidden" name="yhqId_'+type+'_'+rows+'" id="yhqId_'+type+'_'+rows+'" class="inventory_input" value="0">';
        	break;
        }
        str = str+'<input type="hidden" name="rows_'+type+'[]" value="'+rows+'" >&nbsp;<a href="javascript:" onclick="del_rows(\''+type+'_'+rows+'\')"><img src="images/yingxiao_30.png"></a>'+
    '</div>';
    $(dom).before(str);
    $("#qujian"+type).attr("rows",rows);
}
function checkman(id){
	if($("#rows_"+id).prev().length>0){
		var pre = parseInt($("#rows_"+id).prev().find('input').eq(0).val());
		var val = parseInt($("#man_"+id).val());
		if(val<=pre){
			layer.msg('该值不能小于或等于上一范围的值！',function(){});
			$("#man_"+id).focus();
		}
	}
}
function checkjian(id){
	if($("#rows_"+id).prev().length>0){
		var pre = parseInt($("#rows_"+id).prev().find('input').eq(1).val());
		var val = parseInt($("#jian_"+id).val());
		if(val<=pre){
			layer.msg('该值不能小于或等于上一范围的值！',function(){});
			$("#jian_"+id).focus();
		}
	}
}
function checkzhe(id){
	if($("#rows_"+id).prev().length>0){
		var pre = parseFloat($("#rows_"+id).prev().find('input').eq(1).val());
		var val = parseFloat($("#jian_"+id).val());
        if(val>=100||val<=0){
            layer.msg('折扣值必须是0-100之间的数字！',function(){});
            $("#jian_"+id).focus();
        }else if(val>=pre){
			layer.msg('折扣值不能大于或等于上一范围的值！',function(){});
			$("#jian_"+id).focus();
		}
	}
}
function del_rows(id){
	$("#rows_"+id).remove();
}
function select_yhq(id,title){
    var editId = $("#editId").val();
    $("#pdt_3_"+editId).html(title);
    $("#yhqId_3_"+editId).val(id);
    $("#editId").val(0);
    hideSearch();
}