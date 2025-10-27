var ajaxpost;
$(function(){
	$(".right_content_caozuo .add").click(function(){
		$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
		$('#myModal').reveal();
		var url = $("#url").val();
		ajaxpost=$.ajax({
			type: "POST",
			url: "/crm_service.php",
			data: "action=addCustomer&url="+url,
	        dataType : "text",timeout : 8000,
			success: function(data) {
				$('#myModal').html(data);
			},
			error: function() {
               alert('超时，请重新获取');
            }
		});
	});
	$(".right_content_caozuo .addCj").click(function(){
		$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
		$('#myModal').reveal();
		var url = $("#url").val();
		ajaxpost=$.ajax({
			type: "POST",
			url: "/crm_service.php",
			data: "action=addCustomer&cj=1&url="+url,
	        dataType : "text",timeout : 8000,
			success: function(data) {
				$('#myModal').html(data);
				$('#com_title').bind('input propertychange', function() {searchKehu();}); 
			},
			error: function() {
               alert('超时，请重新获取');
            }
		});
	});
	$(".right_content_caozuo .enter").click(function(){
		$("#myModal").html('<form id="myform" method="post" name="FormAdd" action="?m=system&s=index&a=daoruKehu" enctype="multipart/form-data">'+
		'<div id="add_container">'+
			'<div id="new_title">'+
				'<div class="new_title_01">导入客户</div>'+
				'<div class="new_title_02" onclick="hide_myModal();"></div>'+
				'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="add_container1" style="height:200px;">'+
				'<div class="add_cont_title">导入客户</div>'+
				'<div id="add_cont">'+
					'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
						'<tbody><tr height="122">'+
							'<td width="16%" height="42" valign="top"> <span class="add_cont_zhuyi">*</span> Excel文件：</td>'+
							'<td width="84%">'+
								'<input type="file" name="MyFile"><Br>'+
								'<span style="color:red">PS：1.必须严格按照示例文件格式导入,<a href="/inc/客户Demo.xls" style="color:blue;text-decoration:underline;" target="_blank">下载示例</a><Br>2.每次导入量不能超过一百个客户，如果过多请分批导入</span>'+
								'</td>'+
							'</tr>'+
						'</tbody>'+
					'</table>'+
				'</div>'+
			'</div>'+
			'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
				'<tbody><tr>'+
				'<td></td>'+
				'<td align="right" style="padding-right:40px; padding-top:20px;">'+
				'<input type="submit" value="确定" class="new_cont_button2 submitButton">'+
				'<input type="button" value="取消" onclick="hide_myModal();" class="new_cont_button3">'+
				'</td>'+
				'</tr>'+
				'</tbody>'+
			'</table>'+
			'</div></form>');
		$('#myModal').reveal();
	});
	$(".right_content_caozuo .exit").click(function(){
		$("#myModal").html('<form id="myform" method="post" name="FormAdd" action="?m=system&s=index&a=daochuKehu">'+
		'<div id="add_container">'+
			'<div id="new_title">'+
				'<div class="new_title_01">导出客户</div>'+
				'<div class="new_title_02" onclick="hide_myModal();"></div>'+
				'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="add_container1" style="height:200px;">'+
				'<div class="add_cont_title">导出客户</div>'+
				'<div id="add_cont">'+
					'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
						'<tbody><tr>'+
							'<td width="16%" valign="top"> <span class="add_cont_zhuyi"></span> 时间范围：</td>'+
							'<td width="84%">'+
								'<input type="text" name="startTime" value="" onclick="WdatePicker();" class="input0">&nbsp;至<input type="text" name="endTime" value="" onclick="WdatePicker();" class="input0"></td>'+
							'</tr>'+
						'<tr>'+
							'<td width="16%" valign="top"> <span class="add_cont_zhuyi"></span> 选择类型：</td>'+
							'<td width="84%">'+
								'<select name="type"><option value="0">全部</option><option value="1">拜访客户</option><option value="2">保护客户</option><option value="3">成交客户</option></select>'+
							'</tr></tbody>'+
					'</table>'+
				'</div>'+
			'</div>'+
			'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
				'<tbody><tr>'+
				'<td></td>'+
				'<td align="right" style="padding-right:40px; padding-top:20px;">'+
				'<input type="submit" value="确定" class="new_cont_button2 submitButton">'+
				'<input type="button" value="取消" onclick="hide_myModal();" class="new_cont_button3">'+
				'</td>'+
				'</tr>'+
				'</tbody>'+
			'</table>'+
			'</div></form>');
		$('#myModal').reveal();
	});
	$(".right_content_caozuo .shaixuan").click(function(){
		$("#searchTr").stop().fadeToggle(200);
		if(ajaxpost){
			ajaxpost.abort();
		}
	});
	//弹出客户信息
	$("#kh_table tr td.com_title").click(function(){
		kehuTr = $(this).parent();
		$kehuId = parseInt(kehuTr.attr("kehu_id"));
		if($kehuId>0){
			$com_title = kehuTr.find(".com_title").html();
			$com_sn = kehuTr.attr("kehu_sn");
			if(typeof($com_sn)=='undefined'){$com_sn='';}
			$fzr = kehuTr.find(".fzr").html();
			$iforder = kehuTr.find(".iforder").html();
			$level = kehuTr.find(".level").html();
			$source = kehuTr.find(".source").html();
			$hangye = kehuTr.find(".hangye").html();
			$protect = kehuTr.attr(".kehu_protect");
			$("#show_kehu_info").attr("kehu_id",$kehuId);
			$protectstr = '';
			if($protect==1){
				$protectstr='<img src="images/bh_biao.png">';
			}
			$baifangTime = kehuTr.find(".baifangTime").html();
			titlestr = '<input type="hidden" id="kehu_fzr" value="'+$fzr+'" /><input type="hidden" id="kehu_level" value="'+$level+'" /><input type="hidden" id="kehu_source" value="'+$source+'" /><input type="hidden" id="kehu_hangye" value="'+$hangye+'" /><div class="khxx_title_01">'+$com_title+'</div>'+
				'<div class="khxx_title_02">['+$level+']</div>'+
				'<div class="khxx_title_03">'+$protectstr+'</div>'+
				'<div class="khxx_title_04">'+
				   '<div class="khxx_title_04_01"><a href="#">设置提醒</a></div>'+
				'</div>'+
				'<div class="clearBoth"></div>';
			aboutstr = '<table width="100%" border="0" cellspacing="0" cellpadding="0">'+
		         '<tbody><tr>'+
		           '<td width="18%">客户编号: '+$com_sn+'</td>'+
		           '<td width="82%">负责人: '+$fzr+'</td>'+
		         '</tr>'+
		         '<tr>'+
		           '<td colspan="2">最后跟进时间: '+$baifangTime+'</td>'+
		         '</tr>'+
		       '</tbody></table>';
		    constr='<div id="hotnews_caption">'+
			        '<ul>'+
			        '<li class="current" onclick="secBoard(\'hotnews_caption\',\'list\',1);">&nbsp;<a href="javascript:void(0)">概况</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',2);loadKehuInfo('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">客户信息</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',3);loadSaleTeam('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">服务团队</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',4);loadService('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">服务记录</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',5);loadHetongs('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">成交合同</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',6);loadPdts('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">产品信息</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',7);loadMoneys('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">费用</a>&nbsp; </li>'+
					'<li class="normal" onclick="secBoard(\'hotnews_caption\',\'list\',8);loadOthers('+$kehuId+',0);">&nbsp;<a href="javascript:void(0)">其它</a>&nbsp; </li>'+
					'<li class="normal" style="display:none"></li>'+
			        '</ul>'+
			      '</div>'+
				  '<div id="hotnews_content">'+
					'<div id="list_1" class="current"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_2" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_3" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_4" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_5" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_6" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_7" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_8" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
					'<div id="list_9" class="normal"><div class="loading"><img src="images/loading.gif"></div></div>'+
			    '</div>';
			$("#show_kehu_info #khxx_container .khxx_title").html(titlestr);
			$("#show_kehu_info #khxx_container .khxx_about").html(aboutstr);
			$("#show_kehu_info #khxx_container .khxx_con").html(constr);
			$("#show_kehu_info").show();
			$("#show_kehu_info").animate({left:"0px"},300);
			ajaxpost=$.ajax({
				type: "POST",
				url: "/crm_service.php",
				data: "action=getKehuInfo&id="+$kehuId,
		        dataType : "text",timeout : 20000,
				success: function(data) {
					$("#show_kehu_info #list_1").html(data);
				},
				error: function() {
	               alert('超时，请重新获取');
	            }
			});
		}
	});
	$("#searchTr input").change(function(){
		$("#ifsearch").val(1);
		$("#searchForm").submit();
	});
	$("#searchTr select").change(function(){
		$("#ifsearch").val(1);
		$("#searchForm").submit();
	});
});
/*function hide_myModal(){
	if(ajaxpost){
		ajaxpost.abort();
	}
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal").css({"width":"702px","left":"50%","margin-left":"-350px","opacity":"0","display":"none"});
	$(".reveal-modal-bg").fadeOut(200);
}*/
function hide_kehu_info(){
	if(ajaxpost){
		ajaxpost.abort();
	}
	$("#show_kehu_info").animate({left:"100%"},300);
	$("#show_kehu_info").hide();
	$("#show_kehu_info .khxx_title").html("");
	$("#show_kehu_info .khxx_about").html("<img src='images/loading.gif' />");
	$("#show_kehu_info .khxx_con").html("");
}
function checkEditKehu(kehuId,isKehu){
	if($("#com_title").length>0&&$("#com_title").val()==''){
		alert('客户名称不能为空');
		$("#com_title").focus();
		return false;
	}
	if($("#lxr_name").length>0&&$("#lxr_name").val()==''){
		alert('联系人姓名不能为空');
		$("#lxr_name").focus();
		return false;
	}
	$yz = 0;
	$("#myform input[required]").each(function(){
		if($(this).val()==''){
			alert($(this).attr("placeholder"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}
	$(".submitButton").attr("disabled","ture");
	var str_data=$("#myform input").map(function(){
		if($(this).attr("type")=='checkbox'){
			if($(this).is(':checked')){
				return ($(this).attr("name")+'=1');
			}else{
				return ($(this).attr("name")+'=0');
			}
		}else{
			return ($(this).attr("name")+'='+$(this).val());
		}
	}).get().join("&");
	str_data=str_data+'&'+$("#myform select").map(function(){
	  return ($(this).attr("name")+'='+$(this).val());
	}).get().join("&");
	str_data=str_data+'&'+$("#myform textarea").map(function(){
	  return ($(this).attr("name")+'='+$(this).val());
	}).get().join("&");
	var url = $("#myform").attr("action");
	ajaxpost=$.ajax({
		type: "POST",
		url: url,
		data: str_data,
		dataType : "text",timeout : 20000,
		success: function(data) {
			hide_myModal();
			if(isKehu==0){
				loadKehuInfo(kehuId,1);
			}else if(isKehu==1){
				if(kehuId>0){
					$("#show_kehu_info #list_2 .khxx_intro_con_contact ul #lxrli_"+kehuId).html(data);
				}else{
					$("#show_kehu_info #list_2 .khxx_intro_con_contact ul ").append(data);
				}
			}else if(isKehu==2){
				if(kehuId>0){
					$("#show_kehu_info #list_2 #addressTables #addressTable_"+kehuId).html(data);
				}else{
					$("#show_kehu_info #list_2 #addressTables").append(data);
				}
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});

}
function checkKehu(){
	if($("#com_title").val()==''){
		alert('客户名称不能为空');
		$("#com_title").focus();
		return false;
	}
	if($("#lxr_name").length>0&&$("#lxr_name").val()==''){
		alert('联系人姓名不能为空');
		$("#lxr_name").focus();
		return false;
	}
	$yz = 0;
	$("#myform input[required]").each(function(){
		if($(this).val()==''){
			alert($(this).attr("placeholder"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}
	$("#myform select[required]").each(function(){
		if($(this).find("option:selected").val()=='0'){
			alert($(this).attr("title"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}
	$(".submitButton").attr("disabled","ture");
	$("#myform").submit();
}
//获取客户信息标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
function loadKehuInfo(kehuId,force){
	if($("#show_kehu_info #list_2 .loading").length>0||force==1){
		if($("#show_kehu_info #list_2 .loading").length==0){
			$("#show_kehu_info #list_2").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		level = $("#kehu_level").val();
		source = $("#kehu_source").val();
		hangye = $("#kehu_hangye").val();
		ajaxpost=$.ajax({
			type: "POST",
			url: "/crm_service.php",
			data: "action=kehuInfo&id="+$kehuId+'&level='+level+'&source='+source+'&hangye='+hangye,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_2").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
//获取销售团队标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
function loadSaleTeam(kehuId,force){
	if($("#show_kehu_info #list_3 .loading").length>0||force==1){
		if($("#show_kehu_info #list_3 .loading").length==0){
			$("#show_kehu_info #list_3").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/crm_service.php",
			data: "action=kehuSaleTeam&id="+$kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_3").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
//获取服务记录标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
var genjinPage = 1;
var gongdanPage = 1;
var tixingPage = 1;
function loadService(kehuId,force){
	if($("#show_kehu_info #list_4 .loading").length>0||force==1){
		genjinPage = 1;
		gongdanPage = 1;
		tixingPage = 1;
		if($("#show_kehu_info #list_4 .loading").length==0){
			$("#show_kehu_info #list_4").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/gongdan_service.php",
			data: "action=kehuServices&kehuId="+kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_4").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
//获取产品信息标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
var pdtsPage = 1;
function loadPdts(kehuId,force){
	if($("#show_kehu_info #list_6 .loading").length>0||force==1){
		pdtsPage = 1;
		if($("#show_kehu_info #list_6 .loading").length==0){
			$("#show_kehu_info #list_6").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/hetong_service.php",
			data: "action=kehuProducts&kehuId="+kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_6").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
function loadMorePdts(kehuId){
	pdtsPage = pdtsPage+1;
	$("#loadMorePdts").html("<img src='images/loading.gif'>");
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=moreKehuProducts&kehuId="+kehuId+"&page="+pdtsPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#loadMorePdts").html("<a href=\"javascript:loadMorePdts("+kehuId+");\">加载更多</a>");
			if(data==""){
				$("#loadMorePdts").hide();
			}else{
				$("#show_kehu_info #list_5 ul").append(data);
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
//查看磁盘详情
function viewProductInfo(id,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=viewProductInfo&id="+id+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
//添加/编辑客户产品信息,kehuPdtId:编辑客户产品id  productId添加新产品的id
function addKehuProduct(kehuPdtId,productId,kehuId){
	var url = $("#url").val();
	var hetongInfo = $("#pdt_hetong option:selected").val();
	$("#myModal").css("top","50px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addKehuProduct&id="+kehuPdtId+"&productId="+productId+"&kehuId="+kehuId+"&hetongInfo="+hetongInfo,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
//获取成交合同标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
function loadHetongs(kehuId,force){
	if($("#show_kehu_info #list_5 .loading").length>0||force==1){
		if($("#show_kehu_info #list_5 .loading").length==0){
			$("#show_kehu_info #list_5").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/hetong_service.php",
			data: "action=kehuChengjiaos&kehuId="+kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_5").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
//获取费用标签页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
function loadMoneys(kehuId,force){
	if($("#show_kehu_info #list_7 .loading").length>0||force==1){
		if($("#show_kehu_info #list_7 .loading").length==0){
			$("#show_kehu_info #list_7").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/hetong_service.php",
			data: "action=kehuMoneys&kehuId="+kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_7").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
		$("#show_kehu_info #list_7").html('<div class="khxx_intro"><div class="khxx_intro_title">功能开发中...</div></div>');
	}
}
function add_baoxiao(kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php?action=addKehuBaoxiao",
		data: "kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data){
			$("#myModal").html(data);
		}
	});
}
//获取其它页信息，kehuId客户id,force是否强制刷新，用于新建信息后刷新
function loadOthers(kehuId,force){
	if($("#show_kehu_info #list_8 .loading").length>0||force==1){
		if($("#show_kehu_info #list_8 .loading").length==0){
			$("#show_kehu_info #list_8").html('<div class="loading"><img src="images/loading.gif"></div>');
		}
		ajaxpost=$.ajax({
			type: "POST",
			url: "/hetong_service.php",
			data: "action=kehuOthers&kehuId="+kehuId,
		    dataType : "text",timeout : 20000,
			success: function(data) {
				$("#show_kehu_info #list_8").html(data);
			},
			error: function() {
	            alert('超时，请重新获取');
	        }
		});
	}
}
//获取客户拜访信息，kehuId客户id
function loadBaifang(kehuId){
	if($("#show_kehu_info #list_9 .loading").length==0){
		$("#show_kehu_info #list_9").html('<div class="loading"><img src="images/loading.gif"></div>');
	}
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=getWaiqins&id="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#show_kehu_info #list_9").html(data);
		},
		error: function() {
	        alert('超时，请重新获取');
	    }
	});
}
//获取客户修改记录
function loadChanges(kehuId){
	if($("#show_kehu_info #list_9 .loading").length==0){
		$("#show_kehu_info #list_9").html('<div class="loading"><img src="images/loading.gif"></div>');
	}
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getChanges&id="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#show_kehu_info #list_9").html(data);
		},
		error: function() {
	        alert('超时，请重新获取');
	    }
	});
}
//获取客户附件列表
function loadFiles(kehuId){
	if($("#show_kehu_info #list_9 .loading").length==0){
		$("#show_kehu_info #list_9").html('<div class="loading"><img src="images/loading.gif"></div>');
	}
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getFiles&id="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#show_kehu_info #list_9").html(data);
		},
		error: function() {
	        alert('超时，请重新获取');
	    }
	});
}
changePage = 1;
function loadMoreChanges(kehuId){
	changePage = changePage+1;
	$("#loadMoreChanges").html("<img src='images/loading.gif'>");
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=moreKehuChanges&kehuId="+kehuId+"&page="+changePage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#loadMoreChanges").html("<a href=\"javascript:loadMoreChanges("+kehuId+");\">加载更多</a>");
			if(data==""){
				$("#loadMoreChanges").hide();
			}else{
				$("#list_9 .khxx_qita_xiugaick_con2 ul").append(data);
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function loadMoreFiles($kehuId){
	var allPages = parseInt($("#filePages").val());
	var nowPage = parseInt($("#fileNowPage").val());
	var nextPage = nowPage+1;
	$("#show_kehu_info #list_9 .loadNextPage").hide();
	$("#show_kehu_info #list_9 .loading").show();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getMoreFiles&id="+$kehuId+"&page="+nextPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#fileNowPage").val(nextPage);
			$("#show_kehu_info #list_9 .loading").hide();
			$("#show_kehu_info #list_9 .khxx_qita_fujianck_con1 ul").append(data);
			if(nextPage<allPages){
				$("#show_kehu_info #list_9 .loadNextPage").show();
			}
		},
		error: function() {
	        alert('超时，请重新获取');
	    }
	});
}
//分页获取拜访记录
function loadMoreWaiqins($kehuId){
	var allPages = parseInt($("#waiqinPages").val());
	var nowPage = parseInt($("#waiqinNowPage").val());
	var nextPage = nowPage+1;
	$("#show_kehu_info #list_9 .loadNextPage").hide();
	$("#show_kehu_info #list_9 .loading").show();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=getMoreWaiqins&id="+$kehuId+"&page="+nextPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#waiqinNowPage").val(nextPage);
			$("#show_kehu_info #list_9 .loading").hide();
			$("#show_kehu_info #list_9 .khxx_qita_baifangck_con2 ul").append(data);
			if(nextPage<allPages){
				$("#show_kehu_info #list_9 .loadNextPage").show();
			}
		},
		error: function() {
	        alert('超时，请重新获取');
	    }
	});
}
//编辑客户基本信息
function editKehu(kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=editCustomer&id="+kehuId+"&url="+url,
		dataType : "text",timeout : 8000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function viewLxrInfo(lxrId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=viewLxrInfo&id="+lxrId,
		dataType : "text",timeout : 8000,
		success: function(data){
			$("#myModal").html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function delAddressInfo(addressId,kehuId){
	if(window.confirm('确定要删除吗？')){
		$("#show_kehu_info #list_2 #addressTables #addressTable_"+addressId).remove();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=index&a=delAddress",
			data: "id="+addressId,
			dataType : "text",timeout : 8000,
			success: function(data){}
		});
	}
}
function delLxrInfo(lxrId,kehuId){
	$("#lxrli_"+lxrId).addClass("unlink");
	$("#lxrli_"+lxrId+" .khxx_intro_contact_06_02").html('<a href="javascript:linkLxrInfo('+lxrId+','+kehuId+')">重新关联</a>');
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=index&a=updateLxrStatus",
		data: "id="+lxrId+"&status=0",
		dataType : "text",timeout : 8000,
		success: function(data){}
	});
}
function linkLxrInfo(lxrId,kehuId){
	$("#lxrli_"+lxrId).removeClass("unlink");
	$("#lxrli_"+lxrId+" .khxx_intro_contact_06_02").html('<a href="javascript:editLxrInfo('+lxrId+','+kehuId+')">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:delLxrInfo('+lxrId+','+kehuId+')">解除关联</a>');
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=index&a=updateLxrStatus",
		data: "id="+lxrId+"&status=1",
		dataType : "text",timeout : 8000,
		success: function(data){}
	});
}
function editAddressInfo(addressId,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=editAddressInfo&id="+addressId+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data){
			$("#myModal").html(data);
		}
	});
}
function addFujian(kehuId){
	$("#myModal").html('<div id="add_container">'+
		'<div id="new_title">'+
			'<div class="new_title_01">新建客户附件</div>'+
			'<div class="new_title_02" onclick="hide_myModal();"></div>'+
			'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="add_container1" style="height:250px">'+
			'<div class="add_cont_title">基本信息</div>'+
			'<div id="add_cont">'+
				'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
					'<tbody><tr>'+
						'<td width="16%" height="42"> <span class="add_cont_zhuyi">*</span> 附件名称：</td>'+
						'<td width="84%">'+
							'<input name="lxr_name" value="" type="text" id="file_name" class="new_cont_input"/></td>'+
						'</tr>'+
						'<input type="hidden" name="originalPics" id="originalPics" value="">'+
						'<input type="hidden" id="fileSize" value="">'+
						'<tr>'+
						'<td valign="top"><span class="add_cont_zhuyi">&nbsp;</span> 上传附件：</td>'+
						'<td>'+
							'<div class="btn">'+
								'<span>上传附件</span>'+
								'<input id="fileupload" type="file" name="originalPic">'+
							'</div>'+
							'<span style="color:red;">支持gif,jpg,png,txt,zip,rar,pdf,doc,xls格式（不超过1M）</span>'+
							'<br>'+
							'<div class="progress">'+
								'<span class="bar"></span><span class="percent">0%</span>'+
							'</div>'+
						'</td>'+
					'</tr>'+
				'</tbody>'+
			'</table>'+
		'</div>'+
	'</div>'+
	'<table width="96%" border="0" align="center" cellpadding="0" cellspacing="10" style=" margin:0 auto;">'+
		'<tbody><tr>'+
			'<td></td>'+
			'<td align="right" style="padding-right:40px; padding-top:20px;">'+
				'<input type="button" value="确定" onclick="addKehuFujian('+kehuId+');" class="new_cont_button2 submitButton">'+
				'<input type="button" value="取消" onclick="hide_myModal();" class="new_cont_button3">'+
			'</td>'+
		'</tr>'+
	'</tbody></table>'+
	'</div>');
	$('#myModal').reveal();
	var bar = $('.bar');
	var percent = $('.percent');
	var progress = $(".progress");
	var files = $(".files");
	var btn = $(".btn span");
	$("#fileupload").wrap("<form id='myupload' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
	$("#fileupload").change(function(){
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			beforeSend: function() {
		        		//showimg.empty();
		        		progress.show();
		        		var percentVal = '0%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		        		btn.html("上传中...");
		        	},
		        	uploadProgress: function(event, position, total, percentComplete) {
		        		var percentVal = percentComplete + '%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		        	},
		        	success: function(data) {
		        		files.html("<b>"+data.name+"("+data.size+"k)</b> <span class='delimg' rel='"+data.pic+"'>删除</span>");
		        		var img = "/upload/"+data.pic;
		        		$("#yulan_img").attr("src",img);
		        		$("#originalPics").val(img);
		        		$("#fileSize").val(data.size+"K");
						//showimg.html("<img src='"+img+"'>");
						btn.html("上传成功");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
					}
				});
	});
	$(".delimg").live('click',function(){
		var pic = $(this).attr("rel");
		$.post("action.php?act=delimg",{imagename:pic},function(msg){
			if(msg==1){
				files.html("删除成功.");
						//showimg.empty();
						progress.hide();
					}else{
						alert(msg);
					}
				});
	});
}
function addKehuFujian(kehuId){
	var fileName = $("#file_name").val();
	var fileUrl = $("#originalPics").val();
	var index1=fileName.lastIndexOf(".");
	var index2=fileName.length;
	var fileType=fileName.substring(index1+1,index2);
	var fileSize = $("#fileSize").val();
	if(fileName==''){
		alert("文件名称不能为空");
		return false;
	}
	if(fileUrl==''){
		alert("文件未上传或上传失败，请重新上传");
		return false;
	}
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var ajaxData = {"title":fileName,"type":fileType,"size":fileSize,"url":fileUrl}
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php?action=addKehuFile&kehuId="+kehuId,
		data: ajaxData,
		dataType : "json",timeout : 8000,
		success: function(data){
			if(data.code==1){
				var html = '<li id="kehuFile'+data.fileId+'">'+
				'<div class="khxx_qita_con1_01"><img src="images/fjgs_2.png"></div>'+
				'<div class="khxx_qita_con1_02">'+fileName+'<br>'+
				'<span class="text_hui">文件大小：'+fileSize+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;上传时间：'+data.time+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;负责人：'+data.uname+'</span>'+
				'</div><div class="khxx_qita_con1_03"><a href="'+fileUrl+'" target="_blank"><img src="images/down_1.png" border="0"></a></div>'+
				'<div class="clearBoth"></div>'+
				'</li>';
				$("#list_9 .khxx_qita_fujianck_con1 ul").prepend(html);
			}else{
				alert(data.message);
				return false;
			}
			hide_myModal();
		}
	});
}
function editLxrInfo(lxrId,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=editLxrInfo&id="+lxrId+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data){
			$("#myModal").html(data);
			var bar = $('.bar');
			var percent = $('.percent');
			var progress = $(".progress");
			var files = $(".files");
			var btn = $(".btn span");
			$("#fileupload").wrap("<form id='myupload' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
		    $("#fileupload").change(function(){
				$("#myupload").ajaxSubmit({
					dataType:  'json',
					beforeSend: function() {
		        		//showimg.empty();
						progress.show();
		        		var percentVal = '0%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
						btn.html("上传中...");
		    		},
		    		uploadProgress: function(event, position, total, percentComplete) {
		        		var percentVal = percentComplete + '%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		    		},
					success: function(data) {
						files.html("<b>"+data.name+"("+data.size+"k)</b> <span class='delimg' rel='"+data.pic+"'>删除</span>");
						var img = "/upload/"+data.pic;
						$("#yulan_img").attr("src",img);
						$("#originalPics").val(img);
						//showimg.html("<img src='"+img+"'>");
						btn.html("上传成功");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
					}
				});
			});
			$(".delimg").live('click',function(){
				var pic = $(this).attr("rel");
				$.post("action.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						files.html("删除成功.");
						//showimg.empty();
						progress.hide();
					}else{
						alert(msg);
					}
				});
			});
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
//添加跟进人
function add_genjin_user(kehuId,nousers){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=getDeparts&kehuId="+kehuId+"&nousers="+nousers,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
//添加跟进人
function add_shouhou_user(kehuId,nousers){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=getDeparts1&kehuId="+kehuId+"&nousers="+nousers+"&shouhou=1",
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addShouhouUser(userId,kehuId,uname){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	ajaxpost=$.ajax({
			type:"POST",
			url:"?m=system&s=index&a=addShouhouUser",
			data:"kehuId="+kehuId+"&userId="+userId+"&uname="+uname,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				$("#list_1 .khxx_gk_tuandui ul > .clearBoth").before(html);
				html = html.replace('genjinrens_','genjinren_');
				html = html.replace('<a>'+uname+'</a>','<a>'+uname+'</a>&nbsp;&nbsp;&nbsp;<a href="javascript:delShouhou('+userId+','+kehuId+')"><img src="images/dele.png"></a>');
				$("#list_3 #shouhouTeams ul > .clearBoth").before(html);
				hide_myModal();
			},
			error:function(){
				alert("系统错误，请刷新重试");
			}
		});
}
function editChargeUser(kehuId,nousers){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php",
		data: "action=getDeparts&edit=1&kehuId="+kehuId+"&nousers="+nousers,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function viewImg(src){
	$("#myModal").html('<div id="add_container"><div id="new_title"><div class="new_title_01">查看图片</div><div class="new_title_02" onclick="hide_myModal();"></div><div class="clearBoth"></div></div><div style="text-align:center;padding:20px 0px;"><img src="'+src+'" style="max-width:700px;" /></div>');
	$('#myModal').reveal();
}
//显示部门下员工
function showDepartUsers(departId,renshu,kehuId,edit){
	if(renshu>0&&$("#users"+departId).html()==""){
		$nousers = $("#noGenjinUsers").val();
		ajaxpost=$.ajax({
			type:"POST",
			url:"/crm_service.php?action=get_shenpi_users",
			data:"edit="+edit+"&kehuId="+kehuId+"&id="+departId+"&nousers="+$nousers,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("系统错误，请刷新重试");
			}
		});
	}
	$("#users"+departId).toggle();
}
function showDepartUsers1(departId,renshu,edit,type){
	if(renshu>0&&$("#users"+departId).html()==""){
		$nousers = $("#noGenjinUsers").val();
		ajaxpost=$.ajax({
			type:"POST",
			url:"/hetong_service.php?action=get_shenpi_users",
			data:"edit="+edit+"&id="+departId+"&nousers="+$nousers+"&type="+type+"&ifgongdan=1",
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("超时，请重新操作");
			}
		});
	}
	$("#users"+departId).toggle();
}
function showDepartUsers2(departId,renshu,kehuId,type){
	if(renshu>0&&$("#users"+departId).html()==""){
		$nousers = $("#noGenjinUsers").val();
		ajaxpost=$.ajax({
			type:"POST",
			url:"/hetong_service.php?action=get_shouhou_users",
			data:"kehuId="+kehuId+"&id="+departId+"&nousers="+$nousers+"&type="+type+"&ifgongdan=1",
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("超时，请重新操作");
			}
		});
	}
	$("#users"+departId).toggle();
}
function showDepartUsers3(departId,renshu,kehuIds,type){
	if(renshu>0&&$("#users"+departId).html()==""){
		$url = $("#url").val();
		$nousers = $("#noGenjinUsers").val();
		ajaxpost=$.ajax({
			type:"POST",
			url:"/hetong_service.php?action=get_charge_users",
			data:"kehuIds="+kehuIds+"&id="+departId+"&nousers="+$nousers+"&type="+type+"&url="+$url,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("超时，请重新操作");
			}
		});
	}
	$("#users"+departId).toggle();
}
//修改负责人
function updateChargeUser(userId,kehuId,uname){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	ajaxpost=$.ajax({
			type:"POST",
			url:"?m=system&s=index&a=updateChargeUser",
			data:"kehuId="+kehuId+"&userId="+userId+"&uname="+uname,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				$("#list_3 #chargeTeams ul li").html(html);
				$("#kh_table tr[kehu_id="+kehuId+"] td.fzr").html(uname);
				hide_myModal();
			},
			error:function(){
				alert("系统错误，请刷新重试");
			}
		});
}
//添加跟进人
function addGenjinUser(userId,kehuId,uname){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	ajaxpost=$.ajax({
			type:"POST",
			url:"?m=system&s=index&a=addGenjinUser",
			data:"kehuId="+kehuId+"&userId="+userId+"&uname="+uname,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				$("#list_1 .khxx_gk_tuandui ul > .clearBoth").before(html);
				html = html.replace('genjinrens_','genjinren_');
				html = html.replace('<a>'+uname+'</a>','<a>'+uname+'</a>&nbsp;&nbsp;&nbsp;<a href="javascript:delGenjin('+userId+','+$kehuId+')"><img src="images/dele.png"></a>');
				$("#list_3 #genjinTeams ul > .clearBoth").before(html);
				hide_myModal();
			},
			error:function(){
				alert("系统错误，请刷新重试");
			}
		});
}
//删除跟进人
function delGenjin(uid,kehuId){
	$("#list_3 #genjinTeams ul li#genjinren_"+uid).remove();
	$("#list_1 .khxx_gk_tuandui ul li#genjinrens_"+uid).remove();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=index&a=delGenjin",
		data: "id="+uid+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data){}
	});
}
function delShouhou(uid,kehuId){
	$("#list_3 #shouhouTeams ul li#genjinren_"+uid).remove();
	$("#list_1 .khxx_gk_tuandui ul li#genjinrens_"+uid).remove();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=index&a=delShouhou",
		data: "id="+uid+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data){}
	});
}
function changeHangye1(){
	pid = $("#hangye1").find("option:selected").val();
	$("#hangye").val(pid);
	ajaxpost=$.ajax({
		type:"POST",
		url:"/crm_service.php?action=getMenuOptions",
		data:"pid="+pid,
		timeout:"4000",
		dataType:"text",
		success: function(html){
			$("#hangye2").html(html);
		}
	});
}
function changeHangye2(){
	pid = $("#hangye2").find("option:selected").val();
	$("#hangye").val(pid);
}
function changeArea1(){
	var id =$("#ps1").children('option:selected').val();
	ajaxpost=$.ajax({
		type:"POST",
		url:"/pay.php?a=getAreas",
		data:"id="+id,
		timeout:"4000",
		dataType:"text",
		success: function(html){
			if(html!=""){
				$("#ps2").html(html);
				$("#psarea").val(id);
			}else{
				$("#psarea").val(id);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function changeArea2(){
	var id =$("#ps2").children('option:selected').val();
	ajaxpost=$.ajax({
		type:"POST",
		url:"/pay.php?a=getAreas",
		data:"id="+id,
		timeout:"4000",
		dataType:"text",                                 
		success: function(html){
			if(html!=""){
				$("#ps3").html(html);
				$("#psarea").val(id);
			}else{
				$("#psarea").val(id);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function changeArea3(){
	var id =$("#ps3").children('option:selected').val();
	$("#psarea").val(id);
}
function changeSearchArea1(){
	var id =$("#search_ps1").children('option:selected').val();
	ajaxpost=$.ajax({
		type:"POST",
		url:"/pay.php?a=getAreas",
		data:"id="+id,
		timeout:"4000",
		dataType:"text",
		success: function(html){
			if(html!=""){
				$("#search_ps2").html(html);
				$("#search_areaid").val(id);
			}else{
				$("#search_areaid").val(id);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function changeSearchArea2(){
	var id =$("#search_ps2").children('option:selected').val();
	ajaxpost=$.ajax({
		type:"POST",
		url:"/pay.php?a=getAreas",
		data:"id="+id,
		timeout:"4000",
		dataType:"text",                                 
		success: function(html){
			if(html!=""){
				$("#search_ps3").html(html);
				$("#search_areaid").val(id);
			}else{
				$("#search_areaid").val(id);
			}
		},
		error:function(){
			alert("超时,请重试");
		}
	});
}
function changeSearchArea3(){
	var id =$("#search_ps3").children('option:selected').val();
	$("#search_areaid").val(id);
}
function chachongKehu(chengjiao){
	var title = $("#add_container #com_title").val();
	$("#add_container #chachongKehu").attr("disabled","disabled");
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/crm_service.php?action=chachongKehu",
		data: "title="+title+"&cj="+chengjiao,
		dataType : "text",timeout : 8000,
		success: function(data){
			if(data=='1'||data>1){				
				if(chengjiao==1){
					addkehuHetong(data,url);
				}else{
					ajaxpost=$.ajax({
						type: "POST",
						url: "/crm_service.php",
						data: 'action=addCustomer&title='+title+"&url="+url,
				        dataType : "text",timeout : 8000,
						success: function(data) {
							$('#myModal').html(data);
						},
						error: function() {
			               alert('超时，请重新获取');
			            }
					});
				}
			}else{
				if(chengjiao==1){
					alert("客户不存在，请输入完整的客户名称");
					$("#add_container #chachongKehu").removeAttr("disabled");
				}else{
					alert(data);
					$("#add_container #chachongKehu").removeAttr("disabled");
				}
			}
		}
	});
}
function checkAddPdtForm(kehuId){
	var startTime = $("#pdt_starttime").val();
	var endTime = $("#pdt_endtime").val();
	var hetongInfo = $("#pdt_hetongInfo").val();
	if(startTime==""){
		alert("请选择开始日期");
		$("#pdt_starttime").focus();
		return false;
	}
	if(endTime==""){
		alert("请选择结束日期");
		$("#pdt_endtime").focus();
		return false;
	}
	$yz = 0;
	$("#addKehuPdtForm input[required]").each(function(){
		if($(this).val()==''){
			alert($(this).attr("placeholder"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}
	var productId = $("#pdt_select option[selected]").val();
	var productName = $("#pdt_select option[selected]").html();
	var dataForm = new Object();
	var url = $("#addKehuPdtForm").attr("action");
	$("#addKehuPdtForm input[name^='com_added']").each(function(){
		dataForm[$(this).attr('name')]  = $(this).val();
	});
	$("#addKehuPdtForm select[name^='com_added']").each(function(){
		dataForm[$(this).attr('name')]  = $(this).find("option:selected").val();
	});
	$("#addKehuPdtForm textarea[name^='com_added']").each(function(){
		dataForm[$(this).attr('name')]  = $(this).val();
	});
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$.ajax({
		type: "POST",
		url: url+"&startTime="+startTime+"&endTime="+endTime+"&productId="+productId+"&productName="+productName+"&hetongInfo="+hetongInfo,
		data: dataForm,
		dataType : "text",timeout : 8000,
		success: function(data){
			hide_myModal();
			alert(data);
			loadPdts(kehuId,1);
		}
	});
}
function addKehuFapiao(chengjiaoId,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addKehuFapiao&chengjiaoId="+chengjiaoId+"&kehuId="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
			var bar = $('.bar');
			var percent = $('.percent');
			var progress = $(".progress");
			var showimg = $('#showimg');
			var files = $(".files");
			var btn = $(".btn span");
			$("#fileupload").wrap("<form id='myupload' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
		    $("#fileupload").change(function(){
				$("#myupload").ajaxSubmit({
					dataType:  'json',
					beforeSend: function() {
		        		//showimg.empty();
						progress.show();
		        		var percentVal = '0%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
						btn.html("上传中...");
		    		},
		    		uploadProgress: function(event, position, total, percentComplete) {
		        		var percentVal = percentComplete + '%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		    		},
					success: function(data) {
						var useid= data.pic.replace(".","");
						var useid = useid.replace("/","");
						files.html(files.html()+"<b id='B"+useid+"'>"+data.name+"("+data.size+"k)</b> <span class='delimg' id='D"+useid+"' rel='"+data.pic+"'>删除</span>&nbsp;&nbsp;&nbsp;&nbsp;");
						var img = "/upload/"+data.pic;
						$("#originalPics").val($("#originalPics").val()+"|"+img);
						showimg.html(showimg.html()+"<a href='"+img+"' target='_blank' id='A"+useid+"'><img src='"+img+"' width=80 height=80 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;");
						btn.html("添加附件");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
					}
				});
			});
			$(".delimg").live('click',function(){
				var pic = $(this).attr("rel");
				var useid = pic.replace(".","");
				var useid = useid.replace("/","");
				$.post("imgupload.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						$("#A"+useid).remove();
						$("#B"+useid).remove();
						$("#D"+useid).remove();
						var newpics = $("#originalPics").val().replace("|/upload/"+pic,"");
						$("#originalPics").val(newpics);
						//showimg.empty();
						progress.hide();
					}else{
						alert(msg);
					}
				});
			});
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addKehuTuikuan(chengjiaoId,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addKehuTuikuan&chengjiaoId="+chengjiaoId+"&kehuId="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addKehuWeikuan(chengjiaoId,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addKehuWeikuan&chengjiaoId="+chengjiaoId+"&kehuId="+kehuId,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addkehuHetong(kehuId,url){
	$("#myModal").css("top","50px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	var com_title = $("#khxx_container .khxx_title_01").html();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addkehuHetong&kehuId="+kehuId+"&com_title="+com_title+"&url="+url,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
			$('#cj_bianhao').bind('input propertychange', function() {searchHetong();}); 
			var bar = $('.bar');
			var percent = $('.percent');
			var progress = $(".progress");
			var showimg = $('#showimg');
			var files = $(".files");
			var btn = $(".btn span");
			$("#fileupload").wrap("<form id='myupload' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
		    $("#fileupload").change(function(){
				$("#myupload").ajaxSubmit({
					dataType:  'json',
					beforeSend: function() {
		        		//showimg.empty();
						progress.show();
		        		var percentVal = '0%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
						btn.html("上传中...");
		    		},
		    		uploadProgress: function(event, position, total, percentComplete) {
		        		var percentVal = percentComplete + '%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		    		},
					success: function(data) {
						var useid= data.pic.replace(".","");
						var useid = useid.replace("/","");
						files.html(files.html()+"<b id='B"+useid+"'>"+data.name+"("+data.size+"k)</b> <span class='delimg' id='D"+useid+"' rel='"+data.pic+"'>删除</span>&nbsp;&nbsp;&nbsp;&nbsp;");
						var img = "/upload/"+data.pic;
						$("#originalPics").val($("#originalPics").val()+"|"+img);
						showimg.html(showimg.html()+"<a href='"+img+"' target='_blank' id='A"+useid+"'><img src='"+img+"' width=80 height=80 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;");
						btn.html("添加附件");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
					}
				});
			});
			$(".delimg").live('click',function(){
				var pic = $(this).attr("rel");
				var useid = pic.replace(".","");
				var useid = useid.replace("/","");
				$.post("imgupload.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						$("#A"+useid).remove();
						$("#B"+useid).remove();
						$("#D"+useid).remove();
						var newpics = $("#originalPics").val().replace("|/upload/"+pic,"");
						$("#originalPics").val(newpics);
						//showimg.empty();
						progress.hide();
					}else{
						alert(msg);
					}
				});
			});
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addkehuXufei(kehuId,chengjiaoId){
	$("#myModal").css("top","50px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	var com_title = $("#khxx_container .khxx_title_01").html();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=addkehuXufei&kehuId="+kehuId+"&chengjiaoId="+chengjiaoId+"&com_title="+com_title+"&url="+url,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$('#myModal').html(data);
			$('#cj_bianhao').bind('input propertychange', function() {searchHetong();}); 
			var bar = $('.bar');
			var percent = $('.percent');
			var progress = $(".progress");
			var showimg = $('#showimg');
			var files = $(".files");
			var btn = $(".btn span");
			$("#fileupload").wrap("<form id='myupload' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
		    $("#fileupload").change(function(){
				$("#myupload").ajaxSubmit({
					dataType:  'json',
					beforeSend: function() {
		        		//showimg.empty();
						progress.show();
		        		var percentVal = '0%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
						btn.html("上传中...");
		    		},
		    		uploadProgress: function(event, position, total, percentComplete) {
		        		var percentVal = percentComplete + '%';
		        		bar.width(percentVal);
		        		percent.html(percentVal);
		    		},
					success: function(data) {
						var useid= data.pic.replace(".","");
						var useid = useid.replace("/","");
						files.html(files.html()+"<b id='B"+useid+"'>"+data.name+"("+data.size+"k)</b> <span class='delimg' id='D"+useid+"' rel='"+data.pic+"'>删除</span>&nbsp;&nbsp;&nbsp;&nbsp;");
						var img = "/upload/"+data.pic;
						$("#originalPics").val($("#originalPics").val()+"|"+img);
						showimg.html(showimg.html()+"<a href='"+img+"' target='_blank' id='A"+useid+"'><img src='"+img+"' width=80 height=80 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;");
						btn.html("添加附件");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
					}
				});
			});
			$(".delimg").live('click',function(){
				var pic = $(this).attr("rel");
				var useid = pic.replace(".","");
				var useid = useid.replace("/","");
				$.post("imgupload.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						$("#A"+useid).remove();
						$("#B"+useid).remove();
						$("#D"+useid).remove();
						var newpics = $("#originalPics").val().replace("|/upload/"+pic,"");
						$("#originalPics").val(newpics);
						//showimg.empty();
						progress.hide();
					}else{
						alert(msg);
					}
				});
			});
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function changeWeikuan(){
	var money_zong = $("#cj_money_zong").val();
	var money_daozhang = $("#cj_money_daozhang").val();
	if(money_zong==""){
		money_zong = 0;
	}
	if(money_daozhang==""){
		money_daozhang = 0;
	}
	var weikuan = money_zong-money_daozhang;
	if(weikuan<0){
		weikuan = 0;
	}
	$("#cj_money_weikuan").html(weikuan);
}
function changeEndTime(){
	var startTime = $("#cj_hetong_startTime").val();
	var years = $("#cj_years").val();
	if(startTime!=""){
		$years = 0;
		$months = 0;
		timeArr = startTime.split('-');
		$newYear = parseInt(timeArr[0]);
		$newMonth = parseInt(timeArr[1]);
		$newDay = parseInt(timeArr[2]);
		if(years.indexOf('.')>=0){
			$yeararr = years.split('.');
			$years = parseInt($yeararr[0]);
			$newYear = $newYear+$years;
			$months = parseInt($yeararr[1]);
			$newMonth = $newMonth+$months;
			if($newMonth>12){
				$newMonth = $newMonth-12;
				$newYear = $newYear+1;
			}
			if($newMonth<10){
				$newMonth='0'+$newMonth;
			}
		}else{
			$years = parseInt(years);
			$newYear = $newYear+$years;
		}
		var endTime = new Date();
		$("#cj_hetong_endTime").val($newYear+"-"+$newMonth+"-"+$newDay);
	}
}
var tijiao = 1;
function checkAddHetong(kehuId){
	if(tijiao==1){
		var tourl = $("#addChengjiaoForm").attr("action");
		var money_zong = parseFloat($("#cj_money_zong").val());
		var money_daozhang = parseFloat($("#cj_money_daozhang").val());
		var money_type = $("#cj_money_type option:selected").val();
		var paytype = $("#cj_money_paytype option:selected").val();
		var zhekou = $("#cj_zhekou").val();
		var beizhu = $("#cj_beizhu").val();
		var bianhao = $("#cj_bianhao").val();
		var title = $("#cj_hetong_title").val();
		var startTime = $("#cj_hetong_startTime").val();
		var endTime = $("#cj_hetong_endTime").val();
		var years = $("#cj_years").val();
		var originalFile = $("#originalPics").val();
		var marketPrice = $("#cj_money_market").val();
		var type = $("#type").val();
		var chengjiaoId = $("#chengjiaoId").val();
		if($("#cj_money_zong").val()==""){
			alert("成交金额不能为空");
			$("#cj_money_zong").focus();
			return false;
		}
		if($("#cj_money_daozhang").val()==""){
			alert("到账金额不能为空");
			$("#cj_money_daozhang").focus();
			return false;
		}
		if(money_daozhang>money_zong){
			alert("到账金额不能大于总金额");
			return false;
		}
		if(money_type==""&&$("#cj_money_type").attr("required")=='required'){
			alert("请选择收入类型");
			return false;
		}
		if(paytype==""&&$("#cj_money_paytype").attr("required")=='required'){
			alert("请选择付款方式");
			return false;
		}
		if($("#cj_money_market").val()==""&&$("#cj_money_market").attr("required")=='required'){
			alert("请输入市场价格");
			return false;
		}
		if(bianhao==""){
			alert("合同编号不能为空");
			$("#cj_bianhao").focus();
			return false;
		}
		if(title==""){
			alert("合同名称不能为空");
			$("#cj_hetong_title").focus();
			return false;
		}
		if(startTime==""){
			alert("签单日期不能为空");
			$("#cj_hetong_startTime").focus();
			return false;
		}
		if(years==""){
			alert("服务年限不能为空");
			$("#cj_years").focus();
			return false;
		}
		if(endTime==""){
			alert("到期日期不能为空");
			$("#cj_hetong_endTime").focus();
			return false;
		}
		var pdtIds = '';
		$("#addChengjiaoForm .xufei_pdt").each(function(){
			if($(this).is(':checked')){
				pdtIds =pdtIds+','+$(this).val();
			}
		});
		if(pdtIds.length>0){
			pdtIds = pdtIds.substring(1);
		}
		tijiao = 0;
		var dataForm = new Object();
		dataForm['money_zong'] = money_zong;
		dataForm['money_daozhang'] = money_daozhang;
		dataForm['money_type'] = money_type;
		dataForm['paytype'] = paytype;
		dataForm['zhekou'] = zhekou;
		dataForm['beizhu'] = beizhu;
		dataForm['bianhao'] = bianhao;
		dataForm['title'] = title;
		dataForm['startTime'] = startTime;
		dataForm['endTime'] = endTime;
		dataForm['years'] = years;
		dataForm['originalFile'] = originalFile;
		dataForm['marketPrice'] = marketPrice;
		dataForm['type'] = type;
		dataForm['chengjiaoId'] = chengjiaoId;
		dataForm['pdtIds'] = pdtIds;
		ajaxpost=$.ajax({
			type: "POST",
			url: tourl,
			data: dataForm,
			dataType : "text",timeout : 20000,
			success: function(data) {
				alert(data);
				tijiao = 1;
				if(data=='创建成功，请等待财务审核'||data=='创建成功'){
					hide_myModal();
					loadHetongs(kehuId,1);
				}
			},
			error: function() {
				tijiao = 1;
				alert('超时，请重新获取');
			}
		});
	}else{
		alert('请等待当前程序执行完成，不要重复提交！');
	}
}
function viewChengjiaoInfo(id,kehuId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=viewChengjiaoInfo&id="+id+"&kehuId="+kehuId,
		dataType : "text",timeout : 8000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function checkAddKehuFapiao(){
	if(tijiao==1){
		var tourl = $("#addFapiaoForm").attr("action");
		var fapiao_time = $("#fapiao_time").val();
		var fapiao_money = parseFloat($("#fapiao_money").val());
		var maxMoney = parseFloat($("#fapiao_money").attr("maxMoney"));
		var fapiao_type = $("#fapiao_type").val();
		var fapiao_beizhu = $("#fapiao_beizhu").val();
		var imgs = $("#originalPics").val();
		if(fapiao_time==""){
			alert("开票日期不能为空");
			$("#fapiao_time").focus();
			return false;
		}
		if(isNaN(fapiao_money)||fapiao_money<1){
			alert("开票金额不能为空");
			$("#fapiao_money").focus();
			return false;
		}
		if(fapiao_money>maxMoney){
			alert("可开票金额最多为"+maxMoney);
			$("#fapiao_money").focus();
			return false;
		}
		if(fapiao_type==""){
			alert("开票类型不能为空");
			$("#fapiao_type").focus();
			return false;
		}
		tijiao = 0;
		var dataForm = new Object();
		dataForm['fapiao_time'] = fapiao_time;
		dataForm['fapiao_money'] = fapiao_money;
		dataForm['fapiao_type'] = fapiao_type;
		dataForm['fapiao_beizhu'] = fapiao_beizhu;
		dataForm['imgs'] = imgs;
		ajaxpost=$.ajax({
			type: "POST",
			url: tourl,
			data: dataForm,
			dataType : "text",timeout : 20000,
			success: function(data) {
				alert(data);
				tijiao = 1;
				hide_myModal();
			},
			error: function() {
				tijiao = 1;
				alert('超时，请重新获取');
			}
		});
	}else{
		alert('请等待当前程序执行完成，不要重复提交！');
	}
}
function checkAddKehuTuikuan(){
	if(tijiao==1){
		var tourl = $("#addTuikuanForm").attr("action");
		var tuikuan_time = $("#tuikuan_time").val();
		var tuikuan_money = parseFloat($("#tuikuan_money").val());
		var maxMoney = parseFloat($("#tuikuan_money").attr("maxMoney"));
		var tuikuan_type = $("#tuikuan_type").val();
		var tuikuan_beizhu = $("#tuikuan_beizhu").val();
		
		if(tuikuan_time==""){
			alert("退款日期不能为空");
			$("#tuikuan_time").focus();
			return false;
		}
		if(isNaN(tuikuan_money)||tuikuan_money<1){
			alert("退款金额不能为空");
			$("#tuikuan_money").focus();
			return false;
		}
		if(tuikuan_money>maxMoney){
			alert("退款金额最多为"+maxMoney);
			$("#tuikuan_money").focus();
			return false;
		}
		if(tuikuan_type==""){
			alert("退款方式不能为空");
			$("#tuikuan_type").focus();
			return false;
		}
		tijiao = 0;
		var dataForm = new Object();
		dataForm['tuikuan_time'] = tuikuan_time;
		dataForm['tuikuan_money'] = tuikuan_money;
		dataForm['tuikuan_type'] = tuikuan_type;
		dataForm['tuikuan_beizhu'] = tuikuan_beizhu;
		ajaxpost=$.ajax({
			type: "POST",
			url: tourl,
			data: dataForm,
			dataType : "text",timeout : 20000,
			success: function(data) {
				alert(data);
				tijiao = 1;
				hide_myModal();
			},
			error: function() {
				tijiao = 1;
				alert('超时，请重新获取');
			}
		});
	}else{
		alert('请等待当前程序执行完成，不要重复提交！');
	}
}
function checkAddKehuWeikuan(kehuId){
	if(tijiao==1){
		var tourl = $("#addWeikuanForm").attr("action");
		var weikuan_money = parseFloat($("#weikuan_money").val());
		var weikuan_total = parseFloat($("#money_weikuan_hidden").val());
		var weikuan_paytype = $("#weikuan_paytype option:selected").val();
		var weikuan_time = $("#weikuan_time").val();
		var weikuan_beizhu = $("#weikuan_beizhu").val();
		
		if(isNaN(weikuan_money)||weikuan_money<1){
			alert("收回金额不能为空");
			$("#weikuan_money").focus();
			return false;
		}
		if(weikuan_money>weikuan_total){
			alert("收回金额不能大于总尾款金额");
			$("#weikuan_money").focus();
			return false;
		}
		if(weikuan_time==""){
			alert("收回日期不能为空");
			$("#weikuan_time").focus();
			return false;
		}
		if(weikuan_paytype==""){
			alert("收回方式不能为空");
			$("#weikuan_paytype").focus();
			return false;
		}
		tijiao = 0;
		var dataForm = new Object();
		dataForm['weikuan_time'] = weikuan_time;
		dataForm['weikuan_money'] = weikuan_money;
		dataForm['weikuan_paytype'] = weikuan_paytype;
		dataForm['weikuan_beizhu'] = weikuan_beizhu;
		ajaxpost=$.ajax({
			type: "POST",
			url: tourl,
			data: dataForm,
			dataType : "text",timeout : 20000,
			success: function(data) {
				alert(data);
				tijiao = 1;
				hide_myModal();
				loadHetongs(kehuId,1);
			},
			error: function() {
				tijiao = 1;
				alert('超时，请重新获取');
			}
		});
	}else{
		alert('请等待当前程序执行完成，不要重复提交！');
	}
}
function changeWeikuan1(){
	var money_zong = parseFloat($("#cj_money_zong").val());
	var money_market = parseFloat($("#cj_money_market").val());
	if(!isNaN(money_zong)&&!isNaN(money_market)){
		zhekou = (money_zong*100/money_market).toFixed(2);
		$("#cj_zhekou").val(zhekou);
	}
}
function searchKehu(){
	var com_title = $("#com_title").val();
	if(com_title==""){
		$("#search_kehus").hide();
	}else{
		$("#search_kehus").show().html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
		ajaxpost=$.ajax({
			type: "POST",
			url: "/crm_service.php?action=searchKehu",
			data: "com_title="+com_title,
			dataType : "text",timeout : 20000,
			success: function(data){
				$("#search_kehus").html(data);
			}
		});
	}
}
function setKehuTitle(title){
	$("#com_title").val(title);
	$("#search_kehus").hide();
}
function searchHetong(){
	var bianhao = $("#cj_bianhao").val();
	if(bianhao==""){
		$("#search_hetongs").hide();
	}else{
		//$("#search_hetongs").show().html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
		ajaxpost=$.ajax({
			type: "POST",
			url: "/hetong_service.php?action=searchHetong",
			data: "bianhao="+bianhao,
			dataType : "text",timeout : 20000,
			success: function(data){
				if(data!='no'){
					$("#search_hetongs").show().html(data);
				}else{
					$("#search_hetongs").hide();
				}
			}
		});
	}
}
function setKehuBianhao(title){
	$("#cj_bianhao").val(title);
	$("#search_hetongs").hide();
}
function delProductInfo(liId,id,kehuId){
	ajaxpost=$.ajax({url: "/hetong_service.php?action=delKehuPdt&kehuId="+kehuId+"&pdtId="+id,success: function(data){
		if(data!='ok'){
			alert(data);
		}else{
			$("#lehuPdt"+liId).remove();
		}
	}});
}
function qiehuanService(id){
	$("#kehuServiceHead li").attr("class","normal1");
	$("#kehuServiceHead #kehuServiceHead"+id).attr("class","current1");
	$("#kehuServiceCont > div").hide();
	$("#kehuServiceCont #kehuServiceCont"+id).show();
}
function loadMoreGenjins(kehuId){
	genjinPage = genjinPage+1;
	$("#loadMoreGenjins").html("<img src='images/loading.gif'>");
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=moreKehuGenjins&kehuId="+kehuId+"&page="+genjinPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#loadMoreGenjins").html("<a href=\"javascript:loadMoreGenjins("+kehuId+");\">加载更多</a>");
			if(data==""){
				$("#loadMoreGenjins").hide();
			}else{
				$("#kehuServiceCont1 .xiaoshougenjin2 ul").append(data);
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function loadMoreGongdans(kehuId){
	gongdanPage = gongdanPage+1;
	$("#loadMoreGongdans").html("<img src='images/loading.gif'>");
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=moreKehuGongdans&kehuId="+kehuId+"&page="+gongdanPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#loadMoreGongdans").html("<a href=\"javascript:loadMoreGongdans("+kehuId+");\">加载更多</a>");
			if(data==""){
				$("#loadMoreGongdans").hide();
			}else{
				$("#kehuServiceCont2 .shengchanshouhou2 ul").append(data);
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function loadMoreTixings(kehuId){
	tixingPage = tixingPage+1;
	$("#loadMoreTixings").html("<img src='images/loading.gif'>");
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=moreKehuTixings&kehuId="+kehuId+"&page="+tixingPage,
		dataType : "text",timeout : 20000,
		success: function(data) {
			$("#loadMoreTixings").html("<a href=\"javascript:loadMoreTixings("+kehuId+");\">加载更多</a>");
			if(data==""){
				$("#loadMoreTixings").hide();
			}else{
				$("#kehuServiceCont3 .tixingjilu ul").append(data);
			}
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function add_kehu_genjin(kehuId){
	$("#myModal").css({"width":"618px","left":"50%","margin-left":"-309px","top":"50px"}).html('<div class="xiaoshougenjin_new1">'+
		'<div class="xiaoshougenjin_new1_01">'+
		'<div class="xiaoshougenjin_new1_01_left">'+
		'新建销售跟进'+
		'</div>'+
		'<div class="xiaoshougenjin_new1_01_right" style="cursor:pointer" onclick="hide_myModal();">'+
		'<img src="images/fuwu_12.png">'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="xiaoshougenjin_new1_02">'+
		'<textarea id="addGenjinContent" cols="30" rows="10" placeholder="请输入销售跟进记录内容……"></textarea>'+
		'</div>'+
		'<div class="xiaoshougenjin_new1_03">'+
		'<a href="javascript:tijiao_kehu_genjin('+kehuId+');"><img src="images/fuwu_13.gif"></a><a href="javascript:hide_myModal();"><img src="images/fuwu_14.gif"></a>'+
		'</div>'+
	'</div>');
	$('#myModal').reveal();
}
function tijiao_kehu_genjin(kehuId){
	var content = $("#addGenjinContent").val();
	if(content == ''){
		alert('跟进内容不能为空');
		return;
	}
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=index&a=add_kehu_genjin",
		data: "kehuId="+kehuId+"&content="+content,
		dataType : "text",timeout : 20000,
		success: function(data) {
			alert('提交成功');
			hide_myModal();
			loadService(kehuId,1);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
var iftijiao = 1;
String.prototype.replaceAll = function(s1,s2){
　return this.replace(new RegExp(s1,"gm"),s2);
}
function addGongdan(id,kehuId,pdtId){
	$(".qbgd_1_right_03_down").hide();
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=addGongdan&id="+id+"&kehuId="+kehuId+"&pdtId="+pdtId+"&url="+url,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"600px","left":"50%","margin-left":"-300px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function tongbuAddr(id){
	var address = $("#addrSelect"+id+"_1 option:selected").val()+$("#addrSelect"+id+"_2 option:selected").val()+$("#addrSelect"+id+"_3 option:selected").val();
	$("#addrSelect"+id).val(address);
}
function hide_myModal(){
	if(ajaxpost){
		ajaxpost.abort();
	}
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal").css({"opacity":"0","display":"none"});
	$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal1").css({"opacity":"0","display":"none"});
	$(".reveal-modal-bg").fadeOut(200);
}
function submitForm(){
	$yz = 0;
	$("#submitForm input[required]").each(function(){
		if($(this).val()==''){
			alert($(this).attr("placeholder"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}else{
		$("#submitForm").submit();
	}
}
function submitBaoxiaoForm(kehuId){
	$yz = 0;
	$("#submitBaoxiaoForm input[required]").each(function(){
		if($(this).val()==''){
			alert($(this).attr("placeholder"));
			$(this).focus();
			$yz = 1;
			return false;
		}
	});
	if($yz==1){
		return false;
	}
	var datas = "1=1";
	$("#submitBaoxiaoForm .xjgd_down_4_right input").each(function(){
		datas = datas+"&"+$(this).attr("name")+"="+$(this).val();
	});
	$("#submitBaoxiaoForm .xjgd_down_4_right select").each(function(){
		datas = datas+"&"+$(this).attr("name")+"="+$(this).find("option:selected").val();
	});
	$("#submitBaoxiaoForm .xjgd_down_4_right textarea").each(function(){
		datas = datas+"&"+$(this).attr("name")+"="+$(this).val();
	});
	var submitUrl = $("#submitBaoxiaoForm").attr("action");
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	ajaxpost=$.ajax({
		type: "POST",
		url: submitUrl,
		data: datas,
		dataType : "json",timeout : 8000,
		success: function(data) {
			alert(data.message);
			hide_myModal();
			loadMoneys(kehuId,1);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function gongdanInfo(id){
	$("#myModal").css({"width":"598px","margin-left":"-299px","top":"50px"}).html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	var url = $("#url").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=getKehuGongdanInfo&id="+id+"&url="+url,
		dataType : "text",timeout : 8000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function gongdan_jieshu(id){
	if(window.confirm("工单流程尚未执行完成，确定要直接结束吗？")){
		ajaxpost=$.ajax({
			type: "POST",
			url: "/gongdan_service.php",
			data: "action=gongdan_finish&id="+id,
			dataType : "text",timeout : 30000,
			success: function(data) {
				hide_myModal();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert('超时，请重新操作');
			}
		});
	}
}
function gongdan_fankui(id){
	iftijiao = 1;
	var data = '<form action="?m=system&s=mygongdan&a=fankui&id='+id+'" id="fankui_form"><div class="zxwc">'+
	'<div class="zxwc_1">工单反馈</div>'+
	'<div class="zxwc_2">'+
    	'<div class="zxwc_2_left">内容反馈：</div>'+
    	'<div class="zxwc_2_right">'+
        	'<div class="zxwc_2_right_01">'+
            	'<textarea id="fankui_content" cols="30" rows="10" placeholder="请输入内容……"></textarea>'+
            '</div>'+
        '</div>'+
    	'<div class="clearBoth"></div>'+
    '</div>'+
    '<div class="zxwc_2">'+
    	'<div class="zxwc_2_left">设置提醒：</div>'+
    	'<div class="zxwc_2_right">'+
        	'<div class="zxwc_2_right_02">'+
        		'<input type="hidden" id="fankui_users">'+
        		'<span id="tixingUsers"></span>'+
            	' <span style="cursor:pointer;" onclick="selectTixingUser('+id+');">+ @谁关注</span>'+
            '</div>'+
        '</div>'+
    	'<div class="clearBoth"></div>'+
    '</div>'+
    '<div class="zxwc_2">'+
    	'<div class="zxwc_2_left">上传图片：</div>'+
    	'<div class="zxwc_2_right">'+
        	'<div class="zxwc_2_right_02">'+
        		'<input type="hidden"  id="originalPicsFankui">'+
        		'<div class="btn" id="btnFankui">'+
        		'<span style="color:#fff;">上传图片</span>'+
        		'<input id="fileuploadFankui" type="file" name="originalPic">'+
        		'</div>'+
        		'<span style="color:red;">支持gif,jpg,png格式（1M以内）</span>'+
        		'<br>'+
        		'<div class="progress" id="progressFankui">'+
        		'<span class="bar" id="barFankui"></span><span class="percent">0%</span>'+
        		'</div>'+
        		'<div class="files" id="filesFankui"></div>'+
        		'<div id="showimgFankui"></div>'+
            '</div>'+
        '</div>'+
    	'<div class="clearBoth"></div>'+
    '</div>'+
	'<div class="zxwc_4">'+
    	'<a href="javascript:submitFankui('+id+');" class="zxwc_4_01">提交</a><a href="javascript:hide_myModal();" class="zxwc_4_02">取消</a>'+
    '</div>'+
'</div></form>';
	$('#myModal').css({"width":"600px","left":"50%","margin-left":"-300px"}).html(data).reveal();
	var barFankui = $("#barFankui");
	var percentFankui = $("#percentFankui");
	var progressFankui = $("#progressFankui");
	var showimgFankui = $("#showimgFankui");
	var filesFankui = $("#filesFankui");
	var btnFankui = $("#btnFankui span");
	$("#fileuploadFankui").wrap("<form id='myuploadFankui' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
	$("#fileuploadFankui").change(function(){
		$("#myuploadFankui").ajaxSubmit({
			dataType:  "json",
			beforeSend: function() {
				progressFankui.show();
				var percentVal = "0%";
				barFankui.width(percentVal);
				percentFankui.html(percentVal);
				btnFankui.html("上传中...");
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + "%";
				barFankui.width(percentVal);
				percentFankui.html(percentVal);
			},
			success: function(data) {
				var useid= data.pic.replace(".","");
				var useid = useid.replace("/","");
				filesFankui.html(filesFankui.html()+"<b id='BFankui"+useid+"'>"+data.name+"("+data.size+"k)</b> <span class='delimg' id='DFankui"+useid+"' rel='"+data.pic+"'>删除</span>&nbsp;&nbsp;&nbsp;&nbsp;");
				var img = "/upload/"+data.pic;
				$("#originalPicsFankui").val($("#originalPicsFankui").val()+"|"+img);
				showimgFankui.html(showimgFankui.html()+"<a href='"+img+"' target='_blank' id='AFankui"+useid+"'><img src='"+img+"' width=80 height=80 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;");
				btnFankui.html("添加附件");
			},
			error:function(xhr){
				btnFankui.html("上传失败");
				barFankui.width("0");
				filesFankui.html(xhr.responseText);
			}
		});
	});
	$(".delimg").live('click',function(){
		var pic = $(this).attr("rel");
		var useid = pic.replace(".","");
		var useid = useid.replace("/","");
		$.post("imgupload.php?act=delimg",{imagename:pic},function(msg){
			if(msg==1){
				$("#AFankui"+useid).remove();
				$("#BFankui"+useid).remove();
				$("#DFankui"+useid).remove();
				var newpics = $("#originalPicsFankui").val().replace("|/upload/"+pic,"");
				$("#originalPicsFankui").val(newpics);
				progressFankui.hide();
			}else{
				alert(msg);
			}
		});
	});
}
function submitFankui(id){
	if(iftijiao == 1){
		var content = $("#fankui_content").val();
		if(content==''){
			alert('反馈内容不能为空');
			return false;
		}
		iftijiao = 0;
		fankui_users = $("#fankui_users").val();
		imgs = $("#originalPicsFankui").val();
		var url = $("#fankui_form").attr("action");
		var data = {"content":content,"tixing_users":fankui_users,"imgs":imgs}
		ajaxpost=$.ajax({
			type:"POST",
			url:url,
			data:data,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				alert('反馈成功');
				hide_myModal();
				gongdanInfo(id);
			},
			error:function(){
				alert("超时，请重新操作");
			}
		});
	}
}
function selectTixingUser(gongId){
	$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal1').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=getGongdanUsers&ifgongdan=1&id="+gongId,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal1').css({"width":"401px","left":"50%","margin-left":"-230px","top":($(document).scrollTop()+100)+'px'}).html(data);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus);
		}
	});
}
function setZhixingUser(liuchengId){
	$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal1').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getDeparts1&ifgongdan=2&kehuId="+liuchengId+"&type=1",
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal1').css({"width":"401px","left":"50%","margin-left":"-230px","top":($(document).scrollTop()+100)+'px'}).html(data);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus);
		}
	});
}
function selectUser(userId,name,type,departId,bianhao,title){
	var nowIds = $("#fankui_users").val();
	if(!isNaN(userId)){
		if(nowIds!=""){
			ids = nowIds.split(",");
			for (var i = 0; i < ids.length; i++) {  
				if (ids[i] == userId){
					alert('您已经选择过'+name+"了，不需要再次选择");
					$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
					$("#myModal1").css({"opacity":"0","display":"none"});
					return;
				}  
			}
		}
	}else{
		nowIds = '';
		$("#fankui_users").val('');
		$("#tixingUsers").html('');
	}
	if(nowIds==''){
		$("#fankui_users").val(userId);
	}else{
		$("#fankui_users").val(nowIds+','+userId);
	}
	var udivId = userId.replaceAll(',','_');
	var html = '<div class="khxx_add_people1_01" id="addedUser'+udivId+'">'+name+'&nbsp;<a href="javascript:delFankuiUser(\''+userId+'\');"><img src="images/close2.png" border="0"></a></div>';
	$("#tixingUsers").append(html);
	$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal1").css({"opacity":"0","display":"none"});
}
function delFankuiUser(userId){
	var nowIds = $("#fankui_users").val();
	if(isNaN(userId)){
		nowIds = nowIds.replace(userId,'');
		$("#fankui_users").val(nowIds);
	}else{
		if(nowIds!=""){
			ids = nowIds.split(",");
			for (var i = 0; i < ids.length; i++) {  
				if (ids[i] == userId){
					ids.splice(i,1);
					break;
				}  
			}
			users = ids.join(",");
			$("#fankui_users").val(users);
		}
	}
	$("#addedUser"+(userId.replaceAll(',','_'))).remove();
}
function gongdan_wancheng(id){
	iftijiao = 1;
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=gongdan_deal_liucheng&id="+id,
		dataType : "text",timeout : 30000,
		success: function(data) {
			var html = '<form action="?m=system&s=mygongdan&a=wancheng&id='+id+'" id="wancheng_form"><div class="zxwc">'+
				'<div class="zxwc_1">工单执行</div>'+data+
				'<div class="zxwc_2">'+
			    	'<div class="zxwc_2_left">内容反馈：</div>'+
			    	'<div class="zxwc_2_right">'+
			        	'<div class="zxwc_2_right_01">'+
			            	'<textarea id="fankui_content" cols="30" rows="10" placeholder="请输入内容……"></textarea>'+
			            '</div>'+
			        '</div>'+
			    	'<div class="clearBoth"></div>'+
			    '</div>'+
			    '<div class="zxwc_2">'+
			    	'<div class="zxwc_2_left">设置提醒：</div>'+
			    	'<div class="zxwc_2_right">'+
			        	'<div class="zxwc_2_right_02">'+
			        		'<input type="hidden" id="fankui_users">'+
			        		'<span id="tixingUsers"></span>'+
			            	' <span style="cursor:pointer;" onclick="selectTixingUser('+id+');">+ @谁关注</span>'+
			            '</div>'+
			        '</div>'+
			    	'<div class="clearBoth"></div>'+
			    '</div>'+
			    '<div class="zxwc_2">'+
			    	'<div class="zxwc_2_left">上传图片：</div>'+
			    	'<div class="zxwc_2_right">'+
			        	'<div class="zxwc_2_right_02">'+
			        		'<input type="hidden"  id="originalPicsFankui">'+
			        		'<div class="btn" id="btnFankui">'+
			        		'<span style="color:#fff;">上传图片</span>'+
			        		'<input id="fileuploadFankui" type="file" name="originalPic">'+
			        		'</div>'+
			        		'<span style="color:red;">支持gif,jpg,png格式（1M以内）</span>'+
			        		'<br>'+
			        		'<div class="progress" id="progressFankui">'+
			        		'<span class="bar" id="barFankui"></span><span class="percent">0%</span>'+
			        		'</div>'+
			        		'<div class="files" id="filesFankui"></div>'+
			        		'<div id="showimgFankui"></div>'+
			            '</div>'+
			        '</div>'+
			    	'<div class="clearBoth"></div>'+
			    '</div>'+
				'<div class="zxwc_4">'+
			    	'<a href="javascript:submitWancheng('+id+');" class="zxwc_4_01">提交</a><a href="javascript:hide_myModal();" class="zxwc_4_02">取消</a>'+
			    '</div>'+
			'</div></form>';
			$('#myModal').css({"width":"600px","left":"50%","margin-left":"-300px"}).html(html).reveal();
			var barFankui = $("#barFankui");
			var percentFankui = $("#percentFankui");
			var progressFankui = $("#progressFankui");
			var showimgFankui = $("#showimgFankui");
			var filesFankui = $("#filesFankui");
			var btnFankui = $("#btnFankui span");
			$("#fileuploadFankui").wrap("<form id='myuploadFankui' action='imgupload.php' method='post' enctype='multipart/form-data'></form>");
			$("#fileuploadFankui").change(function(){
				$("#myuploadFankui").ajaxSubmit({
					dataType:  "json",
					beforeSend: function() {
						progressFankui.show();
						var percentVal = "0%";
						barFankui.width(percentVal);
						percentFankui.html(percentVal);
						btnFankui.html("上传中...");
					},
					uploadProgress: function(event, position, total, percentComplete) {
						var percentVal = percentComplete + "%";
						barFankui.width(percentVal);
						percentFankui.html(percentVal);
					},
					success: function(data) {
						var useid= data.pic.replace(".","");
						var useid = useid.replace("/","");
						filesFankui.html(filesFankui.html()+"<b id='BFankui"+useid+"'>"+data.name+"("+data.size+"k)</b> <span class='delimg' id='DFankui"+useid+"' rel='"+data.pic+"'>删除</span>&nbsp;&nbsp;&nbsp;&nbsp;");
						var img = "/upload/"+data.pic;
						$("#originalPicsFankui").val($("#originalPicsFankui").val()+"|"+img);
						showimgFankui.html(showimgFankui.html()+"<a href='"+img+"' target='_blank' id='AFankui"+useid+"'><img src='"+img+"' width=80 height=80 border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;");
						btnFankui.html("添加附件");
					},
					error:function(xhr){
						btnFankui.html("上传失败");
						barFankui.width("0");
						filesFankui.html(xhr.responseText);
					}
				});
			});
			$(".delimg").live('click',function(){
				var pic = $(this).attr("rel");
				var useid = pic.replace(".","");
				var useid = useid.replace("/","");
				$.post("imgupload.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						$("#AFankui"+useid).remove();
						$("#BFankui"+useid).remove();
						$("#DFankui"+useid).remove();
						var newpics = $("#originalPicsFankui").val().replace("|/upload/"+pic,"");
						$("#originalPicsFankui").val(newpics);
						progressFankui.hide();
					}else{
						alert(msg);
					}
				});
			});
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function selectZhixingUser(userId,name,type,departId,liuchengId,title){
	var nowIds = $("#execut_users_"+liuchengId).val();
	if(nowIds=='0'||nowIds==''){
		$("#execut_users_"+liuchengId).val(userId);
		$("#liucheng_"+liuchengId+"_users").html('');
	}else{
		$("#execut_users_"+liuchengId).val(nowIds+','+userId);
	}

	var html = '<span id="zhixingUser'+userId+'">'+(name.slice(-2))+'<a href="javascript:delZhixingUser('+userId+','+liuchengId+');" style="position:absolute;top:18px;right:-17px;"><img src="images/dele.png" border="0" style="padding:0px;"></a></span>';
	$("#liucheng_"+liuchengId+"_users").append(html);
	$("#myModal1").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$("#myModal1").css({"opacity":"0","display":"none"});
}
function delZhixingUser(userId,liuchengId){
	var nowIds = $("#execut_users_"+liuchengId).val();
	if(nowIds!=""){
		ids = nowIds.split(",");
		for (var i = 0; i < ids.length; i++) {
			if (ids[i] == userId){
				ids.splice(i,1);
				break;
			}
		}
		users = ids.join(",");
		$("#execut_users_"+liuchengId).val(users);
	}
	$("#zhixingUser"+userId).remove();
}
function submitWancheng(id){
	if(iftijiao == 1){
		var tijiao = 1;
		var execut_users = '';
		var ustr = '';
		$("#wancheng_form input[required]").each(function(){
			var uid = $(this).val();
			var lid = $(this).attr("data-id");
			ustr = ustr+',"'+lid+'":"'+uid+'"';
			if(uid==0||uid==''){
				tijiao = 0;
			}
		});
		if(tijiao==0){
			alert("有未分配负责人的节点，请先分配");
			return false;
		}
		if(ustr.length>0){
			ustr = ustr.substring(1);
			execut_users = '{'+ustr+'}';
		}
		iftijiao = 0;
		var content = $("#fankui_content").val();
		fankui_users = $("#fankui_users").val();
		imgs = $("#originalPicsFankui").val();
		var url = $("#wancheng_form").attr("action");
		var data = {"content":content,"tixing_users":fankui_users,"imgs":imgs,"execut_users":execut_users}
		ajaxpost=$.ajax({
			type:"POST",
			url:url,
			data:data,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				alert(html);
				hide_myModal();
				kehuId = $("#show_kehu_info").attr("kehu_id");
				loadService(kehuId,1);
				qiehuanService(2);
			},
			error:function(){
				alert("超时，请重新操作");
			}
		});
	}
}
function viewShenpi(shenpiId){
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/gongdan_service.php",
		data: "action=viewLiuchent&id="+shenpiId,
		dataType : "text",timeout : 8000,
		success: function(data) {
			$('#myModal').html(data);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus);
		}
	});
}
function fenpei(){
	kehuIds = '';
	$("#kh_table .checkbox1 input").each(function(){
		kehuId = $(this).attr("name").replace('kehuId-','');
		kehuIds+=','+kehuId;
	});
	if(kehuIds.length<2){
		alert("请先选择要分配的客户");
		return false;
	}
	kehuIds = kehuIds.substring(1);
	var url = $("#url").val();
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getDeparts2&kehuIds="+kehuIds+"&url="+url,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function addShouhou(){
	kehuIds = '';
	$("#kh_table .checkbox1 input").each(function(){
		kehuId = $(this).attr("name").replace('kehuId-','');
		kehuIds+=','+kehuId;
	});
	if(kehuIds.length<2){
		alert("请先选择要添加售后服务人员的客户");
		return false;
	}
	kehuIds = kehuIds.substring(1);
	var url = $("#url").val();
	$("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/hetong_service.php",
		data: "action=getDeparts3&kehuIds="+kehuIds+"&url="+url,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function() {
			alert('超时，请重新获取');
		}
	});
}
function changeChargeUser(userId,kehuIds,uname,url){
	location.href='?m=system&s=index&a=changeChargeUser&kehuIds='+kehuIds+'&userId='+userId+'&uname='+uname+'&url='+encodeURIComponent(url);
}
function addShouhouU(userId,kehuIds,uname,url){
	location.href='?m=system&s=index&a=addShouhouU&kehuIds='+kehuIds+'&userId='+userId+'&uname='+uname+'&url='+encodeURIComponent(url);
}