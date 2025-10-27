function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	if($(dom).find('.menuLeft').length>0){
		var id = $(dom).attr("lay-value");
		showNextMenus(eve,$(dom).find('span').eq(0),id);
	}else{
		$("#channelId").val($(dom).attr("lay-value"));
		$("#selectChannel").find('input').val($(dom).html());
		console.log("111111");
	}
}
var productListForm;
layui.use(['form','upload'], function(){
	var form = layui.form
	,upload = layui.upload
	productListForm = form;
	ajaxpost = $.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_product_channels1",
		data: "pid="+channelId,
		dataType:"text",timeout : 8000,
		success: function(resdata){
			$("#selectChannels").append(resdata);
			if(unit_type==1){
				$("#danunit").next().hide();
			}
		},
		error: function() {
			layer.msg('数据请求失败', {icon: 5});
		}
	});
	
	ajaxpost = $.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_change_channels1",
		data: "pid="+channelId,
		dataType:"text",timeout : 8000,
		success: function(resdata){
			$("#selectChangeChannels").append(resdata);
			if(unit_type==1){
				$("#danunit").next().hide();
			}
		},
		error: function() {
			layer.msg('数据请求失败', {icon: 5});
		}
	});
	
	upload.render({
        elem: '#upload1'
        ,url: '?m=system&s=upload&a=upload'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#share_img").val(res.url);
                $("#haibao_img").attr('src',res.url).parent().show().attr("href",res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
    });
	upload.render({
	    elem: '#uploadPdtImage'
	    ,drag: false
	    ,url: '?m=system&s=upload&a=upload'
	    ,before:function(){
	    	layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      if(res.code > 0){
	      	return layer.msg(res.msg);
	      }else{
	      	var nums = parseInt($('#uploadImages').attr("data-num"))+1;
	      	$('#uploadImages').before('<li class="gallery-item" draggable ="true" id="image_li'+nums+'"><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
	      	var originalPic = $("#originalPic").val();
	      	if(originalPic==''){
	      		originalPic = res.url;
	      	}else{
	      		originalPic = originalPic+'|'+res.url;
	      	}
	      	$("#originalPic").val(originalPic);
	      	$('#uploadImages').attr("data-num",nums);
	      }
	  	}
	  	,error: function(){
	  		layer.msg('上传失败，请重试', {icon: 5});
	  	}
	});
	form.on('checkbox(moreUnit)', function(data){
		if(data.elem.checked){
			var units = $("#units").val();
			var dinghuo_units = $("#dinghuo_units").val();
			var selectstr1 = $unitOptions;
			var selectstr2 = $unitOptions;
			var selectstr3 = $unitOptions;
			var number1 = '';
			var number2 = '';
			dinghuo_str = '';
			if(units!=''){
				unitarr = units.split(',');
				dinghuoarr = new Array();
				if(dinghuo_units!=''){
					dinghuoarr = dinghuo_units.split(',');
				}
				for (var i = 0; i < unitarr.length; i++){
					unit = unitarr[i].split('|');
					if(i==0){
						selectstr1 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
					}else if(i==1){
						selectstr2 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
						number1 = unit[1];
					}else if(i==2){
						selectstr3 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
						number2 = unit[1];
					}
					dinghuo_str=dinghuo_str+'<input type="checkbox" pid="dinghuo_units" '+($.inArray(unit[0],dinghuoarr)?'checked':'')+' lay-skin="primary" value="'+unit[0]+'" title="'+unit[0]+'" />';
				}
			}
			layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '530px;'
				,shade: 0.3
				,id: 'LAY_layuipro'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					return false;
				},btn2: function(){
					$("input[lay-filter='moreUnit']").attr("checked",false);
					form.render("checkbox");
				}
				,btnAlign: 'r'
				,content:'<div class="spxx_shanchu_tanchu">'+
				'<form action="#" class="layui-form" method="post" id="setTagsForm"><div class="spxx_shanchu_tanchu_01">'+
				'<div class="spxx_shanchu_tanchu_01_left">'+'设置商品单位'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_01_right">'+
				'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				'</div>'+
				'<div class="clearBoth"></div>'+
				'</div>'+
				'<div class="spxx_shanchu_tanchu_02" style="height:230px;">'+
				'<div class="jiliang_tanchu">'+
				'<div class="moreunitLeft mustRow">最小单位：</div>'+
				'<div style="float:left;width:350px;line-height:40px;">'+
				'<select id="unit1" lay-filter="unit1"><option value="">选择单位</option>'+selectstr1+'</select>'+
				'</div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
				'<div class="moreunitLeft mustRow">副单位1：1</div>'+
				'<div style="float:left;width:150px;line-height:40px;">'+
				'<select id="unit2" lay-filter="unit2"><option value="">选择单位</option>'+selectstr2+'</select>'+
				'</div><div style="float:left;width:20px;line-height:40px;text-align:center;">=</div><div style="float:left;width:150px;line-height:40px;">'+
				'<input type="number" min="1" id="number1" value="'+number1+'" class="layui-input">'+
				'</div><div style="float:left;width:20px;line-height:40px;text-align:center;" class="ziunit"></div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
				'<div class="moreunitLeft" style="margin-left:9px;width:79px;">副单位2：1</div>'+
				'<div style="float:left;width:150px;line-height:40px;">'+
				'<select id="unit3" lay-filter="unit3"><option value="">选择单位</option>'+selectstr3+'</select>'+
				'</div><div style="float:left;width:20px;line-height:40px;text-align:center;">=</div><div style="float:left;width:150px;line-height:40px;">'+
				'<input type="number" min="1" id="number2" value="'+number2+'" class="layui-input">'+
				'</div><div style="float:left;width:20px;line-height:40px;text-align:center;" class="ziunit"></div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
				'<div class="moreunitLeft mustRow">可订货单位：</div>'+
				'<div style="float:left;width:350px;line-height:40px;" id="dinghuoUnits">'+dinghuo_str+			
				'</div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
				'</div>'+
				'</form></div>'
				,success: function(layero){
					form.render();
					var btn = layero.find('.layui-layer-btn');
					btn.find('.layui-layer-btn0').attr({
						href: 'javascript:setUnits();'
					});
					return false;
				}
			});
		}else{
			layer.open({
				type: 1
				,title: '确定要取消多单位吗？'
				,shade: 0.3
				,area: '390px;'
				,id: 'LAY_layuipro1'
				,btn: ['确定', '取消']
				,closeBtn: false
				,yes: function(index, layero){
					$("#duounit").hide();
					$("#danunit").next().show();
					$("#unit_type").val('0');
					$("#unit_type").val('');
					$("#dinghuo_units").val('');
					layer.close(index);
				},btn2: function(){
					$("input[lay-filter='moreUnit']").prop("checked",true);
					form.render("checkbox");
				}
				,btnAlign: 'r'
				,content:'<div style="margin:12px 15px;text-align:center;width:360px;">确定要取消多单位吗？（修改多请点击左侧的输入框）</div>'
			});
		}
	});
	$("#duounit").click(function(){
		var units = $("#units").val();
		var dinghuo_units = $("#dinghuo_units").val();
		var selectstr1 = $unitOptions;
		var selectstr2 = $unitOptions;
		var selectstr3 = $unitOptions;
		var number1 = '';
		var number2 = '';
		dinghuo_str = '';
		if(units!=''){
			unitarr = units.split(',');
			dinghuoarr = new Array();
			if(dinghuo_units!=''){
				dinghuoarr = dinghuo_units.split(',');
			}
			for (var i = 0; i < unitarr.length; i++){
				unit = unitarr[i].split('|');
				if(i==0){
					selectstr1 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
				}else if(i==1){
					selectstr2 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
					number1 = unit[1];
				}else if(i==2){
					selectstr3 = $unitOptions.replace(unit[0]+'"',unit[0]+'" selected="true"');
					number2 = unit[1];
				}
				dinghuo_str=dinghuo_str+'<input type="checkbox" pid="dinghuo_units" '+($.inArray(unit[0],dinghuoarr)>-1?'checked':'')+' lay-skin="primary" value="'+unit[0]+'" title="'+unit[0]+'" />';
			}
		}
		layer.open({
			type: 1
			,title: false
			,closeBtn: false
			,area: '530px;'
			,shade: 0.3
			,id: 'LAY_layuipro'
			,btn: ['确定']
			,yes: function(index, layero){
				return false;
			}
			,btnAlign: 'r'
			,content: '<div class="spxx_shanchu_tanchu">'+
			'<form action="#" class="layui-form" method="post" id="setTagsForm"><div class="spxx_shanchu_tanchu_01">'+
			'<div class="spxx_shanchu_tanchu_01_left">'+'设置商品单位'+
			'</div>'+
			'<div class="spxx_shanchu_tanchu_01_right">'+
			'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
			'</div>'+
			'<div class="clearBoth"></div>'+
			'</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:230px;">'+
			'<div class="jiliang_tanchu">'+
			'<div class="moreunitLeft mustRow">最小单位：</div>'+
			'<div style="float:left;width:350px;line-height:40px;">'+
			'<select id="unit1" lay-filter="unit1"><option value="">选择单位</option>'+selectstr1+'</select>'+
			'</div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
			'<div class="moreunitLeft mustRow">副单位1：1</div>'+
			'<div style="float:left;width:150px;line-height:40px;">'+
			'<select id="unit2" lay-filter="unit2"><option value="">选择单位</option>'+selectstr2+'</select>'+
			'</div><div style="float:left;width:20px;line-height:40px;text-align:center;">=</div><div style="float:left;width:150px;line-height:40px;">'+
			'<input type="number" min="1" id="number1" value="'+number1+'" class="layui-input">'+
			'</div><div style="float:left;width:20px;line-height:40px;text-align:center;" class="ziunit"></div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
			'<div class="moreunitLeft" style="margin-left:9px;width:79px;">副单位2：1</div>'+
			'<div style="float:left;width:150px;line-height:40px;">'+
			'<select id="unit3" lay-filter="unit3"><option value="">选择单位</option>'+selectstr3+'</select>'+
			'</div><div style="float:left;width:20px;line-height:40px;text-align:center;">=</div><div style="float:left;width:150px;line-height:40px;">'+
			'<input type="number" min="1" id="number2" value="'+number2+'" class="layui-input">'+
			'</div><div style="float:left;width:20px;line-height:40px;text-align:center;" class="ziunit"></div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
			'<div class="moreunitLeft mustRow">可订货单位：</div>'+
			'<div style="float:left;width:350px;line-height:40px;" id="dinghuoUnits">'+dinghuo_str+			
			'</div><div class="clearBoth" style="margin-bottom:10px;"></div>'+
			'</div>'+
			'</form></div>'
			,success: function(layero){
				form.render();
				$("#danunit").next().hide();
				var btn = layero.find('.layui-layer-btn');
				btn.find('.layui-layer-btn0').attr({
					href: 'javascript:setUnits();'
				});
				return false;
			}
		});
	});
	form.on('select(unit1)', function(data){
		$(".ziunit").html(data.value);
		dinghuoUnits();
		form.render("checkbox");
	});
	form.on('select(unit2)', function(data){
		dinghuoUnits();
		form.render("checkbox");
	});
	form.on('select(unit3)', function(data){
		dinghuoUnits();
		form.render("checkbox");
	});
	form.on('checkbox(ifsale)',function(data){
		var rowtr =$(data.elem).parent().parent();
		if(data.elem.checked){
			rowtr.find('input[type="number"]').removeClass('disabled').removeAttr('readonly');
		}else{
			rowtr.find('input[type="number"]').addClass('disabled').val(0).prop('readonly',true);
		}
	});
	form.on('checkbox(dinghuo_bykehu)',function(data){
		var ifmoresn = $("#ifmoresn").is(':checked');
		if(ifmoresn){
			$("#khjg_table_dan").hide();
			$("#khjg_table_duo").slideToggle(200);
		}else{
			$("#khjg_table_duo").hide();
			$("#khjg_table_dan").slideToggle(200);
		}
		
	});
	form.on('checkbox(ifmoresn)',function(){
		location.href='?m=system&s=product&a=editProduct&id='+productId;
	});
	form.on('submit(tijiao)', function(data){
		layer.load();
		var tijiao = true;
		var ifmoresn = $("#ifmoresn").is(':checked');
		if(ifmoresn){
			$("#moreGuige input[mustrow]").each(function(){
				if($(this).val()==''){
					layer.msg('产品规格的内容不能留空',{icon: 5,time:2000});
					$(this).focus();
					tijiao = false;
					layer.closeAll('loading');
					return false;
				}
			});
			return tijiao;
		}else{
			$(".table1_tb input[mustrow]").each(function(){
				if($(this).val()==''){
					layer.msg('产品规格的内容不能留空',{icon: 5,time:2000});
					$(this).focus();
					tijiao = false;
					layer.closeAll('loading');
					return false;
				}
			});
			$("#dinghuo_dansn input[mustrow]").each(function(){
				if($(this).val()==''){
					layer.msg('订货价格不能为空',{icon: 5,time:2000});
					$(this).focus();
					tijiao = false;
					layer.closeAll('loading');
					return false;
				}
			});
			$("#khjg_table_dan input[mustrow]").each(function(){
				if($(this).val()==''){
					layer.msg('订货价格不能为空',{icon: 5,time:2000});
					$(this).focus();
					tijiao = false;
					layer.closeAll('loading');
					return false;
				}
			});
			return tijiao;
		}
	});
	$("#selectChannel").click(function(){
		$(this).parent().toggleClass('layui-form-selected');
	});
	$("#ordering").bind('input propertychange',function(){
		var val = $(this).val();
		if((val.length>1&&val.substring(0,1)=='0')||isNaN(val)){
			$(this).val(0);
		}
	});
	$(document).bind('click',function(){
		$(".sprukuadd_03_tt_addsp_erji").hide();
	});
});
function quxiao(){
	layer.confirm('取消后您输入的信息不能保存，确定要取消吗？', {
		btn: ['确定','取消'],
	},function(){
		history.go(-1);
	});
}
function dinghuoUnits(){
	var unit1 = $("#unit1 option:selected").val();
	var unit2 = $("#unit2 option:selected").val();
	var unit3 = $("#unit3 option:selected").val();
	if(unit1!=''){
		if(unit1==unit2||unit1==unit3){
			layer.msg('副单位与主单位不能相同',{icon: 5,time:2000});
		}
	}
	if(unit2!=''&&unit2==unit3){
		layer.msg('两个副单位不能相同',{icon: 5,time:2000});
	}
	str = '';
	if(unit1!=''){
		str=str+'<input type="checkbox" pid="dinghuo_units" checked lay-skin="primary" value="'+unit1+'" title="'+unit1+'" />';
	}
	if(unit2!=''){
		str=str+'<input type="checkbox" pid="dinghuo_units" checked lay-skin="primary" value="'+unit2+'" title="'+unit2+'" />';
	}
	if(unit3!=''){
		str=str+'<input type="checkbox" pid="dinghuo_units" checked lay-skin="primary" value="'+unit3+'" title="'+unit3+'" />';
	}
	$("#dinghuoUnits").html(str);
}
function setUnits(){
	var unit_type = 1;
	var units = '';
	var dinghuo_units = '';
	var duounit = '';
	var unit1 = $("#unit1 option:selected").val();
	var unit2 = $("#unit2 option:selected").val();
	var unit3 = $("#unit3 option:selected").val();
	var number1 = $("#number1").val();
	var number2 = $("#number2").val();
	if(unit1==""||unit2==""){
		layer.msg('至少需要选择两个单位',{icon: 5,time:2000});
		return false;
	}
	if(unit1==unit2||unit1==unit3||unit2==unit3){
		layer.msg('不能使用相同的单位',{icon: 5,time:2000});
		return false;
	}
	if(number1<1){
		layer.msg('副单位1比例值必须大于1',{icon: 5,time:2000});
		return false;
	}
	if(unit3!=''&&number2<1){
		layer.msg('副单位2比例值必须大于1',{icon: 5,time:2000});
		return false;
	}
	$("input[pid='dinghuo_units']:checked").each(function(){
		dinghuo_units=dinghuo_units+','+$(this).val();
	});
	//$("#danunit").next().hide();
	if(dinghuo_units!=''){
		dinghuo_units = dinghuo_units.substring(1);
	}
	units = unit1+'|1,'+unit2+'|'+number1;
	duounit = unit1+' / '+unit2+'（'+number1+unit1+'）';
	if(unit3!=''){
		units = units+','+unit3+'|'+number2;
		duounit = duounit+' / '+unit3+'（'+number2+unit1+'）';
	}
	$("#unit_type").val(unit_type);
	$("#units").val(units);
	$("#dinghuo_units").val(dinghuo_units);
	$("#duounit").val(duounit).show();
	$("#danunit").hide().next().hide();
	layer.closeAll();
}
function del_image(id){
	//layer.load();
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
	/*$.ajax({
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
	});*/
}
function checkPdtTitle(id){
	var title = $("#title").val();
	if(title!=''){
		$.ajax({
			type: "POST",
			url: "/erp_service.php?action=checkPdtTitle",
			data: "id="+id+"&title="+title,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				if(resdata.code==0){
					layer.msg(resdata.message,function(){});
					$("#title").addClass('layui-form-danger').focus();
				}else{
					$("#title").removeClass('layui-form-danger');
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请重试', {icon: 5});
			}
		});
	}
}
//订货相关
//输入获取客户列表
function getPdtInfo(id,keyword){
	$("#pdtList"+id+" ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
	var hasIds = '0';
	$("input[name^='kehuId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getRowKehuList&id="+id,
		data: "keyword="+keyword+"&hasIds="+hasIds,
		dataType:'text',timeout : 8000,
		success: function(resdata){
			$("#pdtList"+id+" ul").html(resdata);
		}
	});
}
function selectRow(id,kehuId,title,level){
	var str = '<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<div style="width:95px;"><div class="kehu_set1"><a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a></div><div class="kehu_set2"><a href="javascript:" onclick="delRow('+id+');"><img src="images/reduce.png"></a></div></div>'+ 
	'</td>'+
	'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
	'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+level+'</td>'+
	'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input name="k_ifsale_0['+id+']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td>'+
	'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="number" step="'+step+'" mustrow name="k_price_sale0['+id+']" min="0" style="width:102px;" />'+
		'<input type="hidden" name="kehuId['+id+']" value="'+kehuId+'" />'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="number" step="'+step1+'" value="0" name="k_dinghuo_min0['+id+']" min="0" style="width:102px;" />'+
	'</td>'+
	'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
		'<input type="number" step="'+step1+'" value="0" name="k_dinghuo_max0['+id+']" onmouseover="tips(this,\'0或空代表不限制\',1)" onmouseout="hideTips();" min="0" style="width:102px;" />'+
	'</td>';
	$("#rowTr"+id).html(str);
	productListForm.render('checkbox');
	//addRow();
}
//添加新行
function addRow(){
	var num = parseInt($("#dataTable").attr("rows"));
	num = num+1;
	var str='<tr height="48" id="rowTr'+num+'">'+
                '<td>'+
					'<div style="width:95px;"><div class="kehu_set1">'+
						'<a href="javascript:" onclick="addRow()"><img src="images/plus.png"></a>'+
					'</div>'+
					'<div class="kehu_set2">'+
						'<a href="javascript:" onclick="delRow('+num+');"><img src="images/reduce.png"></a>'+
					'</div></div>'+
				'</td>'+
				'<td colspan="6">'+
					'<div class="sprukuadd_03_tt_addsp">'+
                    	'<div class="sprukuadd_03_tt_addsp_left">'+
                        	'<input type="text" class="layui-input addRowtr" id="searchInput'+num+'" row="'+num+'" placeholder="输入'+kehu_title+'名称/编码/联系人/手机" >'+
                        '</div>'+
                    	'<div class="sprukuadd_03_tt_addsp_right" onclick="showKehus(event,'+num+');">'+
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
            '</tr>';
    $("#dataTable").attr("rows",num);
    $("#dataTable").append(str);
    $('#searchInput'+num).bind('input propertychange', function() {
    	clearTimeout(jishiqi);
    	var row = $(this).attr('row');
    	var val = $(this).val();
    	jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
    });
    $('#searchInput'+num).click(function(eve){
    	var nowRow = $(this).attr("row");
    	if($("#pdtList"+nowRow).css("display")=="none"){
    		$("#pdtList"+nowRow).show();
			getPdtInfo(nowRow,$(this).val());
    	}
    	stopPropagation(eve); 
    });
}
function showKehus(eve,nowRow){
	if($("#pdtList"+nowRow).css("display")=="none"){
		$("#pdtList"+nowRow).show();
		getPdtInfo(nowRow,$('#searchInput'+nowRow).val());
	}
	stopPropagation(eve);
}
function delRow(nowId){
	$("#rowTr"+nowId).remove();
	if($("#dataTable tr").length==1){
		addRow();
	}
}
function checkPrice(val1,val2){
	if(val1!=val2){
		$("#pdt_status").val(0);
	}
}