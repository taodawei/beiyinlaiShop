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
			data: "&id="+menuId,
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
  	var channelId = $("#channelId").val();
  	var keyword = $("#keyword").val();
  	var supplierId = $("#supplierId option:selected").val();
  	var hasIds = '0';
	$("input[name^='inventoryId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	var page = 1;
	productListTalbe.reload({
		where: {
			channelId:channelId
			,keyword:keyword
			,hasIds:hasIds
			,supplierId:supplierId
		},page: {
			curr: page
		}
	});
	$("th[data-field='id']").hide();
	$("th[data-field='productId']").hide();
}
//添加新行
function addRow(){
	var num = parseInt($("#dataTable").attr("rows"));
	num = num+1;
	var str='<tr height="48" id="rowTr'+num+'">'+
                '<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a>'+ 
                '</td>'+
                '<td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<div class="sprukuadd_03_tt_addsp">'+
                        '<div class="sprukuadd_03_tt_addsp_left">'+
                            '<input type="text" class="layui-input addRowtr" id="searchInput'+num+'" row="'+num+'" placeholder="输入编码/商品名称">'+
                        '</div>'+
                        '<div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">'+
                            '●●●'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                        '<div class="sprukuadd_03_tt_addsp_erji" id="pdtList'+num+'">'+
                            '<ul>'+
                                '<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>'+
                            '</ul>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
            '</tr>';
    $("#dataTable").attr("rows",num);
    $("#rowTrHeji").before(str);
    $('#searchInput'+num).bind('input propertychange', function() {
    	clearTimeout(jishiqi);
        var row = $(this).attr('row');
        var val = $(this).val();
        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
    });
    $('#searchInput'+num).click(function(eve){
    	var supplierType = $("input[name='ifsupplier']:checked").val();
    	var supplierId = $("#supplierId option:selected").val();
    	if(supplierType==1&&supplierId==''){
    		layer.msg('请先选择供应商！',function(){});
    		return false
    	}
    	var nowRow = $(this).attr("row");
    	if($("#pdtList"+nowRow).css("display")=="none"){
    		showpdtList(nowRow,$(this).val());
    	}
    	stopPropagation(eve); 
    });
	readerTr();
}
function delRow(nowId){
	if($("#dataTable tr").length<3){
		layer.msg("请至少保留一个产品",function(){});
		return false;
	}
	$("#rowTr"+nowId).remove();
	readerTr();
	renderAllPrice();
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="images/biao_66.png"/></a> '+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
	'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="text" lay-verify="required|number|kucun" name="inventoryNum['+id+']" onchange="renderPrice('+id+');" class="sprukuadd_03_tt_input">'+
		'<input type="hidden" name="inventoryId['+id+']" value="'+inventoryId+'">'+
		'<input type="hidden" name="inventorySn['+id+']" value="'+sn+'">'+
		'<input type="hidden" name="inventoryTitle['+id+']" value="'+title+'">'+
		'<input type="hidden" name="inventoryKey_vals['+id+']" value="'+key_vals+'">'+
		'<input type="hidden" name="inventoryBeizhu['+id+']" id="beizhu'+id+'" value="">'+
		'<input type="hidden" name="inventoryPdtId['+id+']" value="'+productId+'">'+
		'<input type="hidden" name="inventoryUnits['+id+']" value="'+units+'">'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="text" lay-verify="required|number|kucun" value="'+price+'" onchange="renderPrice('+id+');" name="inventoryPrice['+id+']" class="sprukuadd_03_tt_input">'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="text" lay-verify="required|number|kucun" name="inventoryHeji['+id+']" onchange="renderHeji('+id+');" class="sprukuadd_03_tt_input">'+
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
	var supplierId = $("#supplierId option:selected").val();
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getGonghuoList&id="+id+"&supplierId="+supplierId,
		data: "keyword="+keyword+"&hasIds="+hasIds,
		dataType:'text',timeout : 8000,
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
function showAllpdts(){
	var supplierType = $("input[name='ifsupplier']:checked").val();
	var supplierId = $("#supplierId option:selected").val();
	if(supplierType==1&&supplierId==''){
		layer.msg('请先选择供应商！',function(){});
		return false
	}
	$('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
	reloadTable(0);
}
//刷新tr行前边的标号
function readerTr(){
	var trs = $("#dataTable tr");
	var length = trs.length;
	trs.each(function(){
		var i = $(this).index();
		if(i>0&&i<length-2){
			$(this).find("td").eq(0).html(i);
		}
	});
}
function hideSearch(){
	$('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//修改数量和单价
function renderPrice(i){
	var num = $("#rowTr"+i+" input[type='text']").eq(0).val();
	var price = $("#rowTr"+i+" input[type='text']").eq(1).val();
	if(!isNaN(num)&&!isNaN(price)){
		var xiaoji = Math.round(num*price*price_xiaoshu)/price_xiaoshu;
		$("#rowTr"+i+" input[type='text']").eq(2).val(xiaoji);
		renderAllPrice();
	}
}
//修改小计值
function renderHeji(i){
	var num = $("#rowTr"+i+" input[type='text']").eq(0).val();
	var xiaoji = $("#rowTr"+i+" input[type='text']").eq(2).val();
	if(!isNaN(num)&&!isNaN(xiaoji)){
		var chengshu = 1/step;
		var price = xiaoji/num;
		price = Math.round(price*chengshu)/chengshu;
		$("#rowTr"+i+" input[type='text']").eq(1).val(price);
		renderAllPrice();
	}
}
//计算总价并渲染
function renderAllPrice(){
	var nums = 0;
	var prices = 0;
	var num = 0;
	var price = 0;
	var price_all = 0;
	$("input[name^='inventoryNum[']").each(function(){
		num = $(this).val();
		if(!isNaN(num)){
			nums = nums+parseFloat(num);
		}
	});
	$("input[name^='inventoryHeji[']").each(function(){
		price = $(this).val();
		if(!isNaN(price)){
			prices = prices+parseFloat(price);
		}
	});
	var price_other = $("#price_other").val();
	prices = Math.round(prices*price_xiaoshu)/price_xiaoshu;
	$("#rowTrHeji td").eq(5).html(nums);
	$("#rowTrHeji td").eq(7).html(prices);
	price_all = prices;
	if(!isNaN(price_other)){
		price_all = prices+parseFloat(price_other);
	}
	price_all = Math.round(price_all*price_xiaoshu)/price_xiaoshu;
	$("#price_all").html(price_all);
	$("#price_payed").val(price_all);
	$("#price").val(price_all);
}
function quxiao(){
	layer.confirm('取消后您输入的信息不能保存，确定要取消吗？', {
		btn: ['确定','取消'],
	},function(){
		history.go(-1);
	});
}
function setGonghuo(id){
	var url = '?m=system&s=caigou&a=add&supplierId='+id;
	url = encodeURIComponent(url);
	location.href='?m=system&s=supplier&a=gonghuo&id='+id+'&url='+url;
}