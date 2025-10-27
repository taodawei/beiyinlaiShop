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
		url: "?m=system&s=tuihuo&a=getKehuInfo",
		data: "id="+id,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			//console.log(resdata);
			layer.closeAll('loading');
			renderShouhuoInfo(resdata.fapiao.name,resdata.fapiao.phone,resdata.fapiao.kaihuming,resdata.fapiao.kaihuhang,resdata.fapiao.kaihubank);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
//收货地址相关
//修改收货信息
function renderShouhuoInfo(name,phone,kaihuming,kaihuhang,kaihubank){
	hideAddress();
	shouhuoInfo = '{"name":"'+name+'","phone":"'+phone+'","kaihuming":"'+kaihuming+'","kaihuhang":"'+kaihuhang+'","kaihubank":"'+kaihubank+'"}';
	$("#shouhuoInfo").val(shouhuoInfo);
	$("#shouhuoDiv").html('<img src="images/biao_116.png" onclick="selectAddress();">退款人：'+name+'，联系电话：'+phone+',  开户名称：'+kaihuming+',  开户银行：'+kaihuhang+',  开户账户：'+kaihubank);
}
//渲染收货列表
function renderShouhuoList(arry){
	var html = '';
	if(arry!=null){
		for (var i=0; i<arry.length; i++){
			var html=html+'<li><div style="display:inline-block;float:left;"><input type="radio" name="s" data-title="'+arry[i].title+'" data-name="'+arry[i].name+'" data-phone="'+arry[i].phone+'" data-areaName="'+arry[i].areaName+'" data-address="'+arry[i].address+'"/> '+arry[i].title+',   '+arry[i].name+' ,  '+arry[i].phone+' ,  '+arry[i].areaName+arry[i].address;
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
	renderShouhuoInfo(a.attr('data-title'),a.attr('data-name'),a.attr('data-phone'),a.attr('data-areaName'),a.attr('data-address'));
}
function selectAddress(){
	var shouhuoInfo = $("#shouhuoInfo").val();
	var arry = eval('(' + shouhuoInfo + ')');
	$.each(arry,function(key,val){
		$("#e_"+key).val(val);
	});
	$('.adddinghuodan_bianjidizhi').css({'top':'0','opacity':'1','visibility':'visible'});
	$("#bg").show();
}
function hideAddress(){
	$("#bg").hide();
	$('.adddinghuodan_bianjidizhi').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function hideEditAddress(){
	$('.adddinghuodan_addjidizhi').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}

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
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price,weight){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="images/biao_66.png"/></a>'+ 
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
		'<input type="hidden" name="inventoryPrice['+id+']" id="inventoryPrice_'+id+'" value="'+price+'">'+
		'<input type="hidden" name="inventoryWeight['+id+']" id="inventoryWeight_'+id+'" value="'+weight+'">'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+price+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="price_xiaoji'+id+'"></td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle" id="weight_xiaoji'+id+'"></td>'+
	'<td bgcolor="#ffffff" width="276" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<span class="sprukuadd_03_tt_addbeizhu" onclick="editBeizhu('+id+')">+</span>'+
	'</td>';
	$("#rowTr"+id).html(str);
	addRow();
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
	var num = $("#rowTr"+i+" input[type='text']").eq(0).val();
	var price = $("#inventoryPrice_"+i).val();
	price = parseFloat(price.replace(/,/g, ""));
	var weight = $("#inventoryWeight_"+i).val();
	if(!isNaN(num)&&!isNaN(price)){
		var xiaoji = Math.round(num*price*price_xiaoshu)/price_xiaoshu;
		var weightXiaoji = Math.round(num*weight*100)/100;
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
function updateFapiao(){
	var name = $("#e_name").val();
	var phone = $("#e_phone").val();
	var kaihuming = $("#e_kaihuming").val();
	var kaihuhang = $("#e_kaihuhang").val();
	var kaihubank = $("#e_kaihubank").val();
	renderShouhuoInfo(name,phone,kaihuming,kaihuhang,kaihubank);
}