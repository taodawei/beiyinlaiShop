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
  	var channelId = $("#channelId").val();
  	var keyword = $("#keyword").val();
  	var kehuId = $("#kehuId").val();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
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
			,startTime:startTime
			,endTime:endTime
		},page: {
			curr: page
		}
	});
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
            '</tr>';
    $("#dataTable").append(str).attr("rows",num);
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
}
function delRow(nowId){
	if($("#dataTable tr").length<3){
		layer.msg("请至少保留一个产品",function(){});
		return false;
	}
	$("#rowTr"+nowId).remove();
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,units,kucun,price){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+id+');"><img src="/erp/images/biao_66.png"/></a>'+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
    '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input type="number" step="1" name="xiangou['+inventoryId+']" placeholder="0或空代表不限购" class="num"></td>'+
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
function showAllpdts(){
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	if(startTime==''||endTime==''){
		layer.msg("请先选择促销时间");
		return false;
	}
	var kehuId = $("#kehuId").val();
	if(kehuId==''||kehuId==0){
		layer.msg('请先选择'+kehu_title+'！',function(){});
		return false;
	}
	$('.sprkadd_xuanzesp').css({'top':'0','opacity':'1','visibility':'visible'});
	reloadTable(0);
}
function hideSearch(){
	$('.sprkadd_xuanzesp').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function add_qujian(dom,type){
	var rows = parseInt($("#qujian"+type).attr("rows"));
	rows = rows+1;
	var accordType = parseInt($("input[name='accordType']:checked").val());
	accord_name = accordType==1?'数量':'金额';
	var str = '<div id="rows_'+type+'_'+rows+'">'+
        '订购<font class="accord_name">'+accord_name+'</font>每满<span><input type="number" onchange="checkman(\''+type+'_'+rows+'\');" step="1" id="man_'+type+'_'+rows+'" name="man_'+type+'_'+rows+'" /></span>，';
        switch(type){
        	case 1:
        		str = str+'获赠品<a href="javascript:" onclick="fanwei('+rows+');" id="pdt_'+type+'_'+rows+'" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp">请选择商品</a><span><input type="number" step="1" name="jian_'+type+'_'+rows+'"/></span>个'+
        		'<input type="hidden" name="inventoryId_'+type+'_'+rows+'" id="inventoryId_'+type+'_'+rows+'" class="inventory_input" value="0">';
        	break;
        	case 2:
        		str = str+'订单金额减<span><input type="number" step="1" name="jian_'+type+'_'+rows+'" onchange="checkjian(\''+type+'_'+rows+'\');" id="jian_'+type+'_'+rows+'"/></span>元';
        	break;
        	case 3:
        		str = str+'订单金额打折<span><input type="number" step="0.01" name="jian_'+type+'_'+rows+'" onchange="checkzhe(\''+type+'_'+rows+'\');" id="jian_'+type+'_'+rows+'"/></span>%';
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




function area_fanwei(modelId){
	$("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    var departs = $("#"+modelId).val();
    var departNames = $("#"+modelId+"Fanwei").val();
    $("#departs").val(departs);
    $("#departNames").val(departNames);
    ajaxpost=$.ajax({
        type: "POST",
        url: "?s=yyyx&a=getAreas",
        data: "departs="+departs+"&departNames="+departNames,
        dataType : "text",timeout : 10000,
        success: function(data) {
            $('#myModal').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            layer.msg("请求超时，请检查网络");
            hide_myModal();
        }
    });
}
function area_baocun(){
    var modelId = $("#editId").val();
    var departs = $("#departs").val();
    var departNames = $("#departNames").val();
    $("#"+modelId).val(departs);
    var fanwei = departNames;
    $("#"+modelId+"Fanwei").val(fanwei);
    $("#editId").val('0');
    $("#departNames").val('');
    $("#users").val('');
    hide_myModal();
}
function get_areas(id){
    var img = $("img.depart_select_img[data-id="+id+"]").attr("src");
    if(img=='images/tree_bg2.jpg'){
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg1.jpg");
    }else{
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg2.jpg");
    }
    $("#departUsers"+id).slideToggle(100);
    if($("#departUsers"+id).html()==""){
        $("#departUsers"+id).html("<li><img src='images/loading.gif'></li>");
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=yyyx&a=getAreasByPid",
            data:"id="+id,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#departUsers"+id).html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function search_areas(keyword){
    if(keyword==''){
        $("#depart_users").show();
        $("#search_users").hide();
    }else{
        $("#depart_users").hide();
        $("#search_users").html("<li><img src='images/loading.gif'></li>").show();
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=yyyx&a=getAreasByPid",
            data:"keyword="+keyword,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#search_users").html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function add_area_depart(id,name){
    var ids = $("#departs").val();
    var idarray = ids.split(",");
    if(idarray.indexOf(""+id)>-1){
        alert(name+"已经在范围内了");
    }else{
        if(ids.length>0){
            $("#departs").val($("#departs").val()+","+id);
            $("#departNames").val($("#departNames").val()+","+name);
        }else{
            $("#departs").val(id);
            $("#departNames").val(name);
        }
        $(".splc_cont_left_con ul").append('<li id="left_depart'+id+'"><div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_area_depart('+id+',\''+name+'\')"><img src="images/close1.png" border="0"></a></div><div class="clearBoth"></div><div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png"></div>'+name+'</div></li>');
    }
}
function del_area_depart(id,name){
    nowValue = $("#departs").val();nowValue1 = $("#departNames").val();
    departArray = nowValue.split(",");departArray1 = nowValue1.split(",");
    var index = departArray.indexOf(""+id);
    if (index > -1) {
        departArray.splice(index, 1);
        departArray1.splice(index, 1);
        nowValue = departArray.join();nowValue1 = departArray1.join();
        $("#departs").val(nowValue);$("#departNames").val(nowValue1);
        $("#left_depart"+id).remove();
    }
}
function hide_myModal(){
    if(ajaxpost){
        ajaxpost.abort();
    }
    $("#myModal").css("opacity","0").css("display","none");
    $(".reveal-modal-bg").fadeOut(200);
    $("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
}
function fanwei(modelId){
    $("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    ajaxpost=$.ajax({
        type: "POST",
        url: "?s=mendian_set&a=getPdtFanwei",
        data: '',
        dataType : "text",timeout : 10000,
        success: function(data) {
            $('#myModal').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            layer.msg("请求超时，请检查网络");
            hide_myModal();
        }
    });
}
function baocun(){
    var modelId = $("#editId").val();
    var users = $("#users").val();
    $("#pdt_1_"+modelId).text($("#userNames").val());
    $("#inventoryId_1_"+modelId).val(users);
    $("#editId").val('0');
    $("#departNames").val('');
    $("#users").val('');
    $("#userNames").val('');
    $("#departs").val('');
    hide_myModal();
}
function get_users(id){
    var img = $("img.depart_select_img[data-id="+id+"]").attr("src");
    if(img=='images/tree_bg2.jpg'){
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg1.jpg");
    }else{
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg2.jpg");
    }
    $("#departUsers"+id).slideToggle(100);
    if($("#departUsers"+id).html()==""){
        $("#departUsers"+id).html("<li><img src='images/loading.gif'></li>");
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=mendian_set&a=getPdtsByChannel",
            data:"id="+id,
            timeout:"10000",
            dataType:"text",
            success: function(html){
                if(html==""){
                    
                }else{
                    $("#departUsers"+id).html(html);
                }
            },
            error:function(){
                alert("系统错误，请刷新重试");
            }
        });
    }
}
function search_users(keyword){
    if(keyword==''){
        $("#depart_users").show();
        $("#search_users").hide();
    }else{
        $("#depart_users").hide();
        $("#search_users").html("<li><img src='images/loading.gif'></li>").show();
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=mendian_set&a=getPdtsByChannel",
            data:"keyword="+keyword,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#search_users").html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function add_depart(id,name){
	get_users(id);
}
function add_user(id,name){
	$("#users").val(id);
    $("#userNames").val(name);
    baocun();
}