layui.use(['form'], function(){
	form = layui.form;
	form.verify({
		shuzi:function(value,item){
			value = parseInt(value);
			if(isNaN(value)||value<0){
				return '请输入有效的数字';
			}
		}
	});
	form.on('select(accordby)',function(data){
		if(parseInt(data.value)==2){
			$(".weight").text(weight);
			showWeight = weight
		}else{
			$(".weight").text('');
			showWeight = '';
		}
	});
	form.on('checkbox(if_man)',function(data){
		if(data.elem.checked){
			$("#man").removeClass("disabled").attr("readonly",false);
			$("#mantype input").removeClass("disabled");
		}else{
			$("#man").addClass("disabled").attr("readonly",true);
			$("#mantype input").addClass("disabled");
		}
	});
	form.on('submit(tijiao)',function(){
		var tijiao = true;
		$("input.area_input").each(function(){
			if($(this).val()==""){
				tijiao = false;
			}
		});
		if(tijiao){
			layer.load();
		}else{
			layer.msg("区域必须选择",function(){});
			return false;
		}
	});
});
function addRow(){
  var rows = parseInt($("#row_table").attr("data-row"));
  rows = rows+1;
  var content = '<tr height="43" id="row_'+rows+'">'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+rows+'</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '<a href="javascript:addRow();"><img src="images/yingyong_21.png" alt=""></a>'+'&nbsp;&nbsp;'+
                        '<a href="javascript:delRow('+rows+');"><img src="images/yingyong_22.png" alt=""></a>'+
                    '</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '<span style="color:#1f87eb;cursor:pointer;" id="areaFanwei_'+rows+'" onclick="area_fanwei('+rows+');">选择区域</span>'+
                    '</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '<input type="text" name="base_'+rows+'" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight">'+showWeight+'</span>'+
                    '</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '<input type="text" name="base_price_'+rows+'" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">'+
                    '</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '每 <input type="text" name="add_num_'+rows+'" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight">'+showWeight+'</span>'+
                    '</td>'+
                    '<td bgcolor="#ffffff" align="center" valign="middle">'+
                        '<input type="text" name="add_price_'+rows+'" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">'+
                        '<input type="hidden" class="area_input" name="areaIds_'+rows+'" id="areaIds_'+rows+'" value="">'+
                        '<input type="hidden" name="areaNames_'+rows+'" id="areaNames_'+rows+'" value="">'+
                        '<input type="hidden" name="rows[]" value="'+rows+'">'+
                    '</td>'+
                '</tr>';
  $("#row_table").attr("data-row",rows).append(content);
}
function delRow(delrow){
  $("#row_"+delrow).remove();
}
function show_detail(dom,id){
	if($(dom).hasClass('on')){
		$("#tr_detail_"+id).hide();
	}else{
		if($("#tr_detail_"+id).length>0){
			$("#tr_detail_"+id).show();
		}else{
			layer.load();
			$.ajax({
				type: "POST",
				url: "?m=system&s=product&a=get_moban_detail",
				data: "&id="+id,
				dataType:"text",timeout : 10000,
				success: function(resdata){
					layer.closeAll('loading');
					$("#tr_"+id).after(resdata);
				},
				error: function() {
					layer.closeAll();
					layer.msg('数据请求失败', {icon: 5});
				}
			});
		}
	}
	$(dom).toggleClass('on');
}
function delete_moban(id){
	layer.confirm('确定要删除选中的运费模板吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?m=system&s=product&a=delete_moban",
			data: "&id="+id,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message,{icon: 5});
				}else{
					layer.msg('操作成功');
					$("#tr_"+id).remove();
					$("#tr_detail_"+id).remove();
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	});
}


function area_fanwei(modelId){
	$("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    var departs = $("#areaIds_"+modelId).val();
    var departNames = $("#areaNames_"+modelId).val();
    $("#departs").val(departs);
    $("#departNames").val(departNames);
    hasIds = '';
    $("input[name^=areaIds_]").each(function(){
    	var val = $(this).val();
    	if($(this).attr('name')!='areaIds_'+modelId&&val!=''){
    		hasIds = hasIds+','+val;
    	}
    });
    if(hasIds.length>0)hasIds = hasIds.substring(1);
    console.log(hasIds);
    ajaxpost=$.ajax({
        type: "POST",
        url: "?m=system&s=users&a=getAreas",
        data: "departs="+departs+"&departNames="+departNames+"&hasIds="+hasIds,
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
    $("#areaIds_"+modelId).val(departs);
    var fanwei = departNames;
    $("#areaNames_"+modelId).val(fanwei);
    $("#areaFanwei_"+modelId).html(fanwei);
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
            url:"?m=system&s=users&a=getAreasByPid",
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