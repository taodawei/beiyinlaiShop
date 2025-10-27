var form,ajaxpost;
layui.use(['form'], function(){
	form = layui.form
	form.on('checkbox(ruku_shenpi)', function(data){
		if(data.elem.checked){
			$("#ruku_shenpi_cont").slideDown(100);
			$("#ruku_shenpi_cont").next().slideDown(100);
			if($("#ruku_shenpi_cont").attr("rows")=='0'){
				addShenpiRow('ruku');
			}
		}else{
			$("#ruku_shenpi_cont").slideUp(100);
			$("#ruku_shenpi_cont").next().slideUp(100);
		}
	});
	form.on('checkbox(chuku_shenpi)', function(data){
		if(data.elem.checked){
			$("#chuku_shenpi_cont").slideDown(100);
			$("#chuku_shenpi_cont").next().slideDown(100);
			if($("#chuku_shenpi_cont").attr("rows")=='0'){
				addShenpiRow('chuku');
			}
		}else{
			$("#chuku_shenpi_cont").slideUp(100);
			$("#chuku_shenpi_cont").next().slideUp(100);
		}
	});
	form.on('checkbox(diaobo_shenpi)', function(data){
		if(data.elem.checked){
			$("#diaobo_shenpi_cont").slideDown(100);
			$("#diaobo_shenpi_cont").next().slideDown(100);
			if($("#diaobo_shenpi_cont").attr("rows")=='0'){
				addShenpiRow('diaobo');
			}
		}else{
			$("#diaobo_shenpi_cont").slideUp(100);
			$("#diaobo_shenpi_cont").next().slideUp(100);
		}
	});
	form.on('checkbox(caigou_shenpi)', function(data){
		if(data.elem.checked){
			$("#caigou_shenpi_cont").slideDown(100);
			$("#caigou_shenpi_cont").next().slideDown(100);
			if($("#caigou_shenpi_cont").attr("rows")=='0'){
				addShenpiRow('caigou');
			}
		}else{
			$("#caigou_shenpi_cont").slideUp(100);
			$("#caigou_shenpi_cont").next().slideUp(100);
		}
	});
	form.on('checkbox(caigou_tuihuo_shenpi)', function(data){
		if(data.elem.checked){
			$("#caigou_tuihuo_shenpi_cont").slideDown(100);
			$("#caigou_tuihuo_shenpi_cont").next().slideDown(100);
			if($("#caigou_tuihuo_shenpi_cont").attr("rows")=='0'){
				addShenpiRow('caigou_tuihuo');
			}
		}else{
			$("#caigou_tuihuo_shenpi_cont").slideUp(100);
			$("#caigou_tuihuo_shenpi_cont").next().slideUp(100);
		}
	});
	form.on('submit(tijiao)', function(data){
		var tijiao = true;
		$("input[name*='_shenpi_user']").each(function(){
			if($(this).val()=='0'){
				layer.msg('所有审批必须设置审批人！',function(){});
				tijiao = false;
				return false;
			}
		});
		return tijiao;
	});
});
function addRow(type){
	var appendDiv = $("#"+type+"_rows");
	var num = parseInt(appendDiv.attr("rows"));
	num = num+1;
	var str = '<div id="'+type+'_rows'+num+'"><input type="text" name="'+type+'_types['+num+']" value="" lay-verify="required" class="layui-input" placeholder="填写类型"/> <a href="javascript:" onclick="delRow(\''+type+'\','+num+');"><img src="images/chukushezhi_12.gif"/></a><a href="javascript:" onclick="addRow(\''+type+'\');"><img src="images/chukushezhi_13.gif"/></a></div>';
	appendDiv.attr("rows",num).append(str);
}
function delRow(type,id){
	if($("#"+type+"_rows div").length==1){
		layer.msg("请至少保留一个类型",function(){});
		return false;
	}
	$("#"+type+"_rows"+id).remove();
}
function addShenpiRow(type){
	var appendDiv = $("#"+type+"_shenpi_cont");
	var num = parseInt(appendDiv.attr("rows"));
	num = num+1;
	var str = '<div id="'+type+'_shenpi'+num+'">'+
					'<div class="churukushezhi_03_down_3_01">'+
						'设置审批人'+
					'</div>'+
					'<div class="churukushezhi_03_down_3_02">'+
						'<select name="'+type+'_shenpi_store['+num+']"><option value="0">所有仓库</option>'+optionstr+'</select>'+
					'</div>'+
					'<div class="churukushezhi_03_down_3_03">'+
						'<div class="churukushezhi_03_down_3_03_up" id="'+type+'_shenpi_user'+num+'" onclick="selectSpUser(\''+type+'\','+num+');">'+
							'+ 选择人员'+
						'</div>'+
					'</div>'+
					'<div class="churukushezhi_03_down_3_04">'+
						'<a href="javascript:" onclick="addShenpiRow(\''+type+'\');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow(\''+type+'\','+num+');"><img src="images/biao_66.png"/></a>'+
					'</div>'+
					'<div class="clearBoth"></div>'+
					'<input type="hidden" name="'+type+'_shenpi_user['+num+']" id="'+type+'_shenpi_user_'+num+'" value="0">'+
					'<input type="hidden" name="'+type+'_shenpi_id['+num+']" id="'+type+'_shenpi_id_'+num+'" value="0">'+
				'</div>';
	appendDiv.attr("rows",num).append(str);
	form.render('select');
}
function delShenpiRow(type,id){
	if($("#"+type+"_shenpi_cont > div").length==1){
		layer.msg("请至少保留一个审批",function(){});
		return false;
	}
	$("#"+type+"_shenpi"+id).remove();
}
function selectSpUser(type,id){	
	$("#myModal").css("top","50px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
	$('#myModal').reveal();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/erp_service.php",
		data: "action=getDeparts&edit=1&type="+type+"&id="+id,
		dataType : "text",timeout : 30000,
		success: function(data) {
			$('#myModal').css({"width":"401px","left":"50%","margin-left":"-200px"}).html(data);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(textStatus);
		}
	});
}
function showDepartUsers(departId,renshu,id,type){
	if(renshu>0&&$("#users"+departId).html()==""){
		ajaxpost=$.ajax({
			type:"POST",
			url:"/erp_service.php?action=get_shenpi_users",
			data:"edit=1&id="+departId+"&type="+type+"&typeId="+id,
			timeout:"10000",
			dataType:"text",
			success: function(html){
				if(html==""){
					
				}else{
					$("#users"+departId).html(html);
				}
			},
			error:function(){
				alert("超时，请重试");
			}
		});
	}
	$("#users"+departId).toggle();
}
function selectUser(userId,name,type,id){
	$("#"+type+"_shenpi_user"+id).html(name);
	$("#"+type+"_shenpi_user_"+id).val(userId+'|'+name);
	hide_myModal();
}