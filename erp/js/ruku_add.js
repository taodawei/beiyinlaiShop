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
window.onload = function(e){
	var code = "";
    var lastTime,nextTime;
    var lastCode,nextCode;
	document.onkeypress = function(e) {
        nextCode = e.which;
        nextTime = new Date().getTime();

        if(lastCode != null && lastTime != null && nextTime - lastTime <= 30) {
            code += String.fromCharCode(lastCode); 
        } else if(lastCode != null && lastTime != null && nextTime - lastTime > 100){
            code = "";
        }

        lastCode = nextCode;
        lastTime = nextTime;
    }
    this.onkeypress = function(e){
        if(e.which == 13){
        	console.log(code);
        	if(code=='')return false;
            var storeId = $("#storeId option:selected").val();
		    if(storeId==''){
		    	layer.msg('请先选择仓库！',function(){});
		    	return false;
		    }else{
		    	layer.load();
		    	var hasIds = new Array();
				$("input[name^='inventoryId[']").each(function(){
					hasIds.push($(this).val());
				});
				
		    	$.ajax({
					type: "POST",
					url: "/erp_service.php?action=getPdtByCode&code="+code+"&storeId="+storeId,
					data: "keyword="+keyword+"&hasIds="+hasIds,
					dataType:'json',timeout : 8000,
					success: function(resdata){
						layer.closeAll();
						if(resdata.code=='0'){
							layer.msg(resdata.message,function(){});
		    				return false;
						}else{
							if(hasIds.indexOf(resdata.data.id)>-1){
								val = parseFloat($("#ruku_num_"+resdata.data.id).val())+1;
								$("#ruku_num_"+resdata.data.id).val(val);
							}else{
								var rowId = parseInt($("#dataTable").attr("rows"));
								selectRow(rowId,resdata.data.id,resdata.data.sn,resdata.data.title,resdata.data.key_vals,resdata.data.productId,resdata.data.units,resdata.data.kucun,1);
							}
						}
					}
				});
		    }
        }
    }
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
  	var storeId = $("#storeId option:selected").val();
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
			,storeId:storeId
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
            '</tr>';
    $("#dataTable").attr("rows",num).append(str);
    $('#searchInput'+num).bind('input propertychange', function() {
    	clearTimeout(jishiqi);
        var row = $(this).attr('row');
        var val = $(this).val();
        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
    });
    $('#searchInput'+num).click(function(eve){
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
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,num=''){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="images/biao_66.png"/></a> '+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
	'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="text" lay-verify="required|number|kucun" name="inventoryNum['+id+']" id="ruku_num_'+inventoryId+'" onchange="zong_chengben('+inventoryId+')"  onfocus="showTips(this,\''+kucun+'\');" max="'+kucun+'" value="'+num+'" class="sprukuadd_03_tt_input">'+
		'<input type="hidden" name="inventoryId['+id+']" value="'+inventoryId+'">'+
		'<input type="hidden" name="inventorySn['+id+']" value="'+sn+'">'+
		'<input type="hidden" name="inventoryTitle['+id+']" value="'+title+'">'+
		'<input type="hidden" name="inventoryKey_vals['+id+']" value="'+key_vals+'">'+
		'<input type="hidden" name="inventoryBeizhu['+id+']" id="beizhu'+id+'" value="">'+
		'<input type="hidden" name="inventoryPdtId['+id+']" value="'+productId+'">'+
		'<input type="hidden" name="inventoryUnits['+id+']" value="'+units+'">'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
	'<td bgcolor="#ffffff" width="276" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<span class="sprukuadd_03_tt_addbeizhu" onclick="editBeizhu('+id+')">+</span>'+
	'</td>';
	$("#rowTr"+id).html(str);
	
	addRow();
}
function showTips(dom,kucun){
	layer.tips('剩余库存'+kucun,dom,{tips:[1,'#333']});
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
	var storeId = $("#storeId option:selected").val();
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getPdtList&id="+id+"&storeId="+storeId,
		data: "keyword="+keyword+"&hasIds="+hasIds,
		dataType:'text',timeout : 8000,
		success: function(resdata){
			$("#pdtList"+id+" ul").html(resdata);
		}
	});
}
function showpdtList(id,keyword){
	var storeId = $("#storeId option:selected").val();
    if(storeId==''){
    	layer.msg('请先选择仓库！',function(){});
    	return false;
    }
	$("#pdtList"+id).show();
	getPdtInfo(id,keyword);
}
function hidePdtList(id,keyword){
	$("#pdtList"+id).hide();
}
//显示所有产品列表
function showAllpdts(){
	var storeId = $("#storeId option:selected").val();
    if(storeId==''){
    	layer.msg('请先选择仓库！',function(){});
    	return false;
    }
	$('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
	reloadTable(0);
}
//刷新tr行前边的标号
function readerTr(){
	$("#dataTable tr").each(function(){
		if($(this).index()>0){
			$(this).find("td").eq(0).html($(this).index());
		}
	});
}
function hideSearch(){
	$('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function quxiao(){
	layer.confirm('取消后您输入的信息不能保存，确定要取消吗？', {
		btn: ['确定','取消'],
	},function(){
		history.go(-1);
	});
}
function zong_chengben(invid){
	let num = parseFloat($("#ruku_num_"+invid).val());
	let chengben = parseFloat($("#chengben_"+invid).val());
	if(!isNaN(num) && !isNaN(chengben)){
		zchengben = parseFloat(num*chengben).toFixed(2);
		$("#zchengben_"+invid).val(zchengben);
	}else{
		$("#zchengben_"+invid).val('');
	}
}