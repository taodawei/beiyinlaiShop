var nowIndexTime;
$(function(){
	$(document).bind('click',function(){
		$(".sprukuadd_03_tt_addsp_erji").hide();
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
		if($("#kehuId").val()==0){
			$("#searchKehuInput").val('');
		}
	});
	$('.splist_up_01_left_01_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_01_down').slideToggle(200);
		stopPropagation(eve);
	});
	$('#searchKehuInput').bind('input propertychange', function() {
		$("#kehuId").val(0);
		clearTimeout(jishiqi);
		var val = $(this).val();
		jishiqi=setTimeout(function(){getKehuList(val);},500);
	});
	$('.dhd_adddinghuodan_1_right_02').click(function(eve){
		$("#kehuList").show();
		var keyword = $("#searchKehuInput").val();
		getKehuList(keyword);
		stopPropagation(eve);
	});
	$('#searchKehuInput').click(function(eve){
		if($("#kehuList").css("display")=="none"){
			$("#kehuList").show();
			getKehuList('');
		}
		stopPropagation(eve);
	});
});
function selectKehu(id,title){
	layer.load();
	$("#kehuId").val(id);
	$("#searchKehuInput").val(title);
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=getKehuInfo",
		data: "id="+id,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll('loading');
			renderShouhuoInfo(resdata.shouhuos[0].id,resdata.shouhuos[0].title,resdata.shouhuos[0].name,resdata.shouhuos[0].phone,resdata.shouhuos[0].areaName,resdata.shouhuos[0].address);
			renderShouhuoList(resdata.shouhuos);
			renderFapiaoInfo(resdata.fapiao,0,'商品明细');
			
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
//收货地址相关
//修改收货信息
function renderShouhuoInfo(areaId,title,name,phone,areaName,address){
	hideAddress();
	shouhuoInfo = '{"areaId":"'+areaId+'","company":"'+title+'","name":"'+name+'","phone":"'+phone+'","address":"'+areaName+address+'"}';
	$("#shouhuoInfo").val(shouhuoInfo);
	$("#shouhuoDiv").html('<img src="images/biao_116.png" onclick="selectAddress();">公司名称：'+title+'，收货人：'+name+',  联系电话：'+phone+',  收货地址：'+areaName+address);
}
//渲染收货列表
function renderShouhuoList(arry){
	var html = '';
	if(arry!=null){
		for (var i=0; i<arry.length; i++){
			var html=html+'<li><div style="display:inline-block;float:left;"><input type="radio" name="s" data-areaId="'+arry[i].areaId+'" data-title="'+arry[i].title+'" data-name="'+arry[i].name+'" data-phone="'+arry[i].phone+'" data-areaName="'+arry[i].areaName+'" data-address="'+arry[i].address+'"/> '+arry[i].title+',   '+arry[i].name+' ,  '+arry[i].phone+' ,  '+arry[i].areaName+arry[i].address;
			if(arry[i].moren==1){html=html+'  默认';}
			html=html+'</div><div class="editAddress">';
			if(arry[i].moren==0){html=html+'<a href="javascript:" onclick="setMorenAddress('+arry[i].id+');"><img src="images/biao_80.png"> 设为默认</a>&nbsp;&nbsp;&nbsp;';}
			html=html+'<a href="javascript:" onclick="edit_address('+arry[i].id+',\''+arry[i].title+'\',\''+arry[i].name+'\',\''+arry[i].phone+'\','+arry[i].areaId+',\''+arry[i].address+'\');"><img src="images/biao_31.png"> 修改</a>&nbsp;&nbsp;&nbsp;<a href="javascript:" onclick="del_address('+arry[i].id+');"><img src="images/biao_32.png"> 删除</a>';
			html=html+'</div>';
			html=html+'<div class="clearBoth"></div></li>';
		}
	}
	$("#shouhuoList ul").html(html);
}
function searchShouhuo(keyword){
	layer.load();
	var id = $("#kehuId").val();
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=getKehuInfo",
		data: "id="+id+"&keyword="+keyword,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll('loading');
			renderShouhuoList(resdata.shouhuos);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function selectAddr(){
	var a = $("input[name='s']:checked");
	if(a.length==0){
		layer.msg("请先选择收货地址！",function(){});
		return false;
	}
	renderShouhuoInfo(a.attr('data-areaId'),a.attr('data-title'),a.attr('data-name'),a.attr('data-phone'),a.attr('data-areaName'),a.attr('data-address'));
}
function selectAddress(){
	$('.adddinghuodan_bianjidizhi').css({'top':'0','opacity':'1','visibility':'visible'});
}
function hideAddress(){
	$('.adddinghuodan_bianjidizhi').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function hideEditAddress(){
	$('.adddinghuodan_addjidizhi').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function del_address(id){
	var kehuId=$("#kehuId").val();
	layer.confirm('确定要删除该收货地址吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?m=system&s=dinghuo&a=delAddress",
			data: "id="+id+"&kehuId="+kehuId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				searchShouhuo('');
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请检查网络', {icon: 5});
			}
		});
	});
}
function setMorenAddress(id){
	var kehuId=$("#kehuId").val();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=setMorenAddress",
		data: "id="+id+"&kehuId="+kehuId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			searchShouhuo('');
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
//新增/修改收货地址
function edit_address(id,title,name,phone,areaId,address){
	$("#e_address_id").val(id);
	$("#e_address_areaId").val(areaId);
	$("#e_address_title").val(title);
	$("#e_address_name").val(name);
	$("#e_address_phone").val(phone);
	$("#e_address_address").val(address);
	$('.adddinghuodan_addjidizhi').css({'top':'0','opacity':'1','visibility':'visible'});
	if(areaId>0){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?m=system&s=dinghuo&a=getAreaInfo",
			data: "id="+areaId,
			dataType:"json",timeout:10000,
			success: function(resdata){
				layer.closeAll();
				$("#ps1").html(resdata.areas1);
				$("#ps2").html(resdata.areas2);
				$("#ps3").html(resdata.areas3);
				productListForm.render('select');
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请检查网络', {icon: 5});
			}
		});
	}
}
function updateAddress(){
	var id = $("#e_address_id").val();
	var areaId = $("#e_address_areaId").val();
	var title = $("#e_address_title").val();
	var name = $("#e_address_name").val();
	var phone = $("#e_address_phone").val();
	var address = $("#e_address_address").val();
	var kehuId = $("#kehuId").val();
	if(title==''){
		layer.msg('公司名称不能为空',function(){});
		return false;
	}
	if(name==''){
		layer.msg('收货人不能为空',function(){});
		return false;
	}
	if(phone.length!=11){
		layer.msg('请输入正确的手机号码',function(){});
		return false;
	}

	if(areaId==''||areaId==0){
		layer.msg('请选择所在区域',function(){});
		return false;
	}
	if(address==''){
		layer.msg('请输入详细地址',function(){});
		return false;
	}
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=updateAddress",
		data: "id="+id+"&kehuId="+kehuId+"&areaId="+areaId+"&title="+title+"&name="+name+"&phone="+phone+"&address="+address,
		dataType:"json",timeout:10000,
		success: function(resdata){
			layer.closeAll();
			hideEditAddress();
			searchShouhuo('');
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
//收货地址完

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
  	var channelId = $("#channelId").val();
  	var keyword = $("#keyword").val();
  	var kehuId = $("#kehuId").val();
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
			,kehuId:kehuId
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
    	var kehuId = $("#kehuId").val();
    	if(kehuId==''||kehuId==0){
    		layer.msg('请先选择'+kehu_title+'！',function(){});
    		return false;
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
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price,weight,min,max,duoUnits){
	var tipstr = '';
	if(kucun_type==2){
		if(kucun>0){
			tipstr = '库存：有<br>';
		}else{
			tipstr = '库存：无<br>';
		}
	}else if(kucun_type==3){
		tipstr = '库存：'+kucun+units+'<br>';
	}
	if(min>0){
		tipstr = tipstr+'起订量：'+min+units+'<br>';
	}
	if(max>0){
		tipstr = tipstr+'限购量：'+max+units;
	}
	unitsArry = duoUnits.split(',');
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="/erp/images/biao_66.png"/></a>'+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>';
	var unitPrice = price;
	if(unitsArry.length==1){
		str = str+'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>';
	}else{
		var selectstr = '<div style="width:80%;margin:auto;display:inline-block;"><select id="inventoryUnit_'+id+'" data-id="'+id+'" lay-filter="unit" name="inventoryUnit['+id+']">';
		for (var i = 0; i < unitsArry.length; i++) {
			$un = unitsArry[i].split('|');
			selectstr = selectstr + '<option value="'+unitsArry[i]+'" data-num="'+$un[1]+'">'+$un[0]+'('+$un[1]+units+')'+'</option>';
			if(i==0){
				unitPrice = Math.round(parseFloat(price) * parseFloat($un[1])*price_xiaoshu)/price_xiaoshu;
			}
		}
		selectstr = selectstr+'</select></div>';
		str = str+'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+selectstr+'</td>';
	}
	str = str+'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="text" lay-verify="required|number|kucun" name="inventoryNum['+id+']" data-min="'+min+'" data-max="'+max+'" data-kucun="'+kucun+'"';
		if(tipstr!=''){
			str = str + ' onmouseover="tips(this,\''+tipstr+'\',1);" onmouseout="hideTips();"';
		}
		str = str+' onchange="renderPrice('+id+');checkKucun(this);" class="sprukuadd_03_tt_input">'+
		'<input type="hidden" name="inventoryId['+id+']" value="'+inventoryId+'">'+
		'<input type="hidden" name="inventorySn['+id+']" value="'+sn+'">'+
		'<input type="hidden" name="inventoryTitle['+id+']" value="'+title+'">'+
		'<input type="hidden" name="inventoryKey_vals['+id+']" value="'+key_vals+'">'+
		'<input type="hidden" name="inventoryBeizhu['+id+']" id="beizhu'+id+'" value="">'+
		'<input type="hidden" name="inventoryPdtId['+id+']" value="'+productId+'">'+
		'<input type="hidden" name="inventoryUnits['+id+']" value="'+units+'">'+
		'<input type="hidden" name="inventoryPrice['+id+']" id="inventoryPrice_'+id+'" value="'+price+'">'+
		'<input type="hidden" name="inventoryWeight['+id+']" id="inventoryWeight_'+id+'" value="'+weight+'">'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="price_danjia'+id+'">'+unitPrice+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="price_xiaoji'+id+'"></td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="weight_xiaoji'+id+'"></td>'+
	'<td bgcolor="#ffffff" width="276" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<span class="sprukuadd_03_tt_addbeizhu" onclick="editBeizhu('+id+')">+</span>'+
	'</td>';
	$("#rowTr"+id).html(str);
	addRow();
	productListForm.render('select');
}
function editBeizhu(id){
	var content = $("#beizhu"+id).val();
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			var beizhu = $("#e_beizhu").val();
			$("#beizhu"+id).val(beizhu);
			if(beizhu==''){
				$("#rowTr"+id+" .sprukuadd_03_tt_addbeizhu").css("fontSize","36px").html('+');
			}else{
				$("#rowTr"+id+" .sprukuadd_03_tt_addbeizhu").css("fontSize","12px").html('<img src="images/biao_31.png"/>'+beizhu);
			}
			layer.closeAll();
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
			'<div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">备注说明</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
				'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入备注信息">'+content+'</textarea>'+
			'</div>'+
		'</div>'
	});
}
//输入获取产品列表
function getPdtInfo(id,keyword){
	$("#pdtList"+id+" ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
	var hasIds = '0';
	$("input[name^='inventoryId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	var kehuId = $("#kehuId").val();
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=getPdtList&id="+id+"&kehuId="+kehuId,
		data: "keyword="+keyword+"&hasIds="+hasIds,
		dataType:'text',timeout : 10000,
		success: function(resdata){
			$("#pdtList"+id+" ul").html(resdata);
		}
	});
}
function getKehuList(keyword){
	$("#kehuList ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getKehuList",
		data: "keyword="+keyword,
		dataType:'text',timeout : 10000,
		success: function(resdata){
			$("#kehuList ul").html(resdata);
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
	var kehuId = $("#kehuId").val();
	if(kehuId==''||kehuId==0){
		layer.msg('请先选择'+kehu_title+'！',function(){});
		return false;
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
	var num = $("#rowTr"+i+" input[name^='inventoryNum[']").eq(0).val();
	var price = $("#inventoryPrice_"+i).val();
	price = parseFloat(price.replace(/,/g, ""));
	var weight = $("#inventoryWeight_"+i).val();
	var unit_num = 1;
	if($("#inventoryUnit_"+i).length>0){
		unit_num = parseFloat($("#inventoryUnit_"+i+" option:selected").attr('data-num'));
	}
	if(!isNaN(num)&&!isNaN(price)){
		var xiaoji = Math.round(parseFloat(num*price*unit_num)*price_xiaoshu)/price_xiaoshu;
		var weightXiaoji = Math.round(parseFloat(num*weight*unit_num)*price_xiaoshu)/price_xiaoshu;
		$("#price_xiaoji"+i).html(xiaoji);
		$("#weight_xiaoji"+i).html(weightXiaoji);
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
	var weight = 0;
	var weight_all = 0;
	$("input[name^='inventoryNum[']").each(function(){
		num = $(this).val();
		if(!isNaN(num)){
			nums = nums+parseFloat(num);
		}
	});
	$("#dataTable td[id^='price_xiaoji']").each(function(){
		price = $(this).text();
		if(!isNaN(price)){
			prices = prices+parseFloat(price);
		}
	});
	$("#dataTable td[id^='weight_xiaoji']").each(function(){
		weight = $(this).text();
		if(!isNaN(weight)){
			weight_all = Math.round((weight_all+parseFloat(weight))*100)/100;
			$("#z_weight").val(weight_all);
		}
	});
	var yunfei = $("#price_wuliu").html();
	prices = Math.round(prices*price_xiaoshu)/price_xiaoshu;
	$("#rowTrHeji td").eq(5).html(nums);
	$("#rowTrHeji td").eq(7).html(prices);
	$("#rowTrHeji td").eq(8).html(weight_all);
	price_all = prices;
	if(!isNaN(yunfei)){
		price_all = prices+parseFloat(yunfei);
	}
	price_all = Math.round(price_all*price_xiaoshu)/price_xiaoshu;
	$("#price_all").html(price_all);
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
//发票相关
//渲染发票列表
function renderFapiaoInfo(fapiao,type,content){
	var html='<option value="type|0">不开发票</option>';
	html = html+'<option value="type|1,taitou|'+fapiao.taitou+',content|'+content+',shibie|'+fapiao.shibie+'" '+(type==1?'selected="true"':'')+'>普通发票（3%税点）</option>';
	html = html+'<option value="type|2,taitou|'+fapiao.taitou+',content|'+content+',shibie|'+fapiao.shibie+',address|'+fapiao.address+',phone|'+fapiao.phone+',kaihuming|'+fapiao.kaihuming+',kaihuhang|'+fapiao.kaihuhang+',kaihubank|'+fapiao.kaihubank+'" '+(type==2?'selected="true"':'')+'>增值税发票（17%税点）</option>';
	$("#fapiaoInfo").html(html);
	productListForm.render('select');
}
function editFapiao(type,content){
	$('.editFapiaoDiv').css({'top':'0','opacity':'1','visibility':'visible'});
	arry = content.split(',');
	for (var i = 0; i < arry.length; i++){
      var info = arry[i].split('|');
      $("#e_fapiao_"+info[0]).val(info[1]);
    }
	if(type==1){
		$(".zengzhishui").hide();
	}else{
		$(".zengzhishui").show();
	}
}
function hideEditFapiao(){
	$('.editFapiaoDiv').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function updateFapiao(){
	var kehuId = $("#kehuId").val();
	var type = $("#e_fapiao_type").val();
	var taitou = $("#e_fapiao_taitou").val();
	var content = $("#e_fapiao_content").val();
	var shibie = $("#e_fapiao_shibie").val();
	var address = $("#e_fapiao_address").val();
	var phone = $("#e_fapiao_phone").val();
	var kaihuming = $("#e_fapiao_kaihuming").val();
	var kaihuhang = $("#e_fapiao_kaihuhang").val();
	var kaihubank = $("#e_fapiao_kaihubank").val();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?m=system&s=dinghuo&a=updateKehuFapiao",
		data: "kehuId="+kehuId+"&type="+type+"&taitou="+taitou+"&shibie="+shibie+"&address="+address+"&phone="+phone+"&kaihuming="+kaihuming+"&kaihuhang="+kaihuhang+"&kaihubank="+kaihubank,
		dataType:"json",timeout:10000,
		success: function(resdata){
			layer.closeAll();
			hideEditFapiao();
			editStr = 'type|'+type+',taitou|'+taitou+',content|'+content+',shibie|'+shibie;
			editCont = '发票抬头：'+taitou+'&nbsp;&nbsp;&nbsp;发票内容：'+content+'&nbsp;&nbsp;&nbsp;纳税人识别号：'+shibie;
			if(type==2){
				editStr=editStr+',address|'+address+',phone|'+phone+',kaihuming|'+kaihuming+',kaihuhang|'+kaihuhang+',kaihubank|'+kaihubank;
				editCont=editCont+'&nbsp;&nbsp;&nbsp;地址：'+address+'&nbsp;&nbsp;&nbsp;电话：'+phone+'&nbsp;&nbsp;&nbsp;开户名称：'+kaihuming+'&nbsp;&nbsp;&nbsp;开户银行：'+kaihuhang+'&nbsp;&nbsp;&nbsp;银行账号：'+kaihubank;
			}
			var html = '<img src="images/biao_116.png" onclick="editFapiao('+types[1]+',\''+editStr+'\');"> '+editCont;
		    $("#fapiaoCont").html(html);
		    renderFapiaoInfo(resdata.fapiao,type,content);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function checkKucun(dom){
	var rowId = $(dom).parent().parent().attr("id").toString();
    rowId = rowId.replace('rowTr','');
    var value = parseFloat($(dom).val());
    if(value<=0){
    	layer.msg("字段不能小于或等于0",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    var min=parseFloat($(dom).attr("data-min"));
    var max = parseFloat($(dom).attr("data-max"));
    var kucun = parseFloat($(dom).attr("data-kucun"));
    var unit_num = 1;
    if($("#inventoryUnit_"+rowId).length>0){
      unit_num = parseFloat($("#inventoryUnit_"+rowId+" option:selected").attr('data-num'));
    }
    value = Math.round(value*unit_num*price_xiaoshu)/price_xiaoshu;
    if(value<min){
    	layer.msg("数量不能小于起订量！",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    if(max>0&&value>max){
    	layer.msg("数量不能大于限购量！",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    if(dinghuo_limit==1&&value>kucun){
    	layer.msg("此商品库存不足",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
}
function checkKucun1(dom){
	var rowId = $(dom).parent().parent().parent().find("td[data-field='id'] div").text();
    var value = parseFloat($(dom).val());
    if(value<=0){
    	layer.msg("字段不能小于或等于0",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    var min=parseFloat($(dom).attr("data-min"));
    var max = parseFloat($(dom).attr("data-max"));
    var kucun = parseFloat($(dom).attr("data-kucun"));
    var unit_num = 1;
    if($("#add_unit_"+rowId).length>0){
      unit_num = parseFloat($("#add_unit_"+rowId+" option:selected").attr('data-num'));
    }
    value = Math.round(value*unit_num*price_xiaoshu)/price_xiaoshu;
    if(value<min){
    	layer.msg("数量不能小于起订量！",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    if(max>0&&value>max){
    	layer.msg("数量不能大于限购量！",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
    if(dinghuo_limit==1&&value>kucun){
    	layer.msg("此商品库存不足",function(){});
    	$(dom).addClass("layui-form-danger").focus();
      	return false;
    }
}