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
	}
}
var productListForm;
layui.use(['form','upload'], function(){
	var form = layui.form
	,upload = layui.upload
	productListForm = form
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
			guigeTable1();
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
	var uploadInit2 = upload.render({
	    elem: '#uploadPdtImage'
	    ,multiple: true
		,drag: false
	    ,url: '?m=system&s=upload&a=upload'
	    ,before:function(){
			uploadInit.config.data.parentId = 0;
        	uploadInit.config.data.keyId = 0;
	    	layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      if(res.code > 0){
	      	return layer.msg(res.msg);
	      }else{
	      	var nums = parseInt($('#uploadImages').attr("data-num"))+1;
	      	$('#uploadImages').before('<li  class="gallery-item" draggable ="true" id="image_li'+nums+'"><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
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
	var uploadInit = upload.render({
	    elem: '#uploadSnImg'
	    ,url: '?m=system&s=upload&a=upload'
	    ,before:function(){
	    	var parentId = $("#pdtKeyId"+$("#snId1").val()).val();
	    	var keyId = $("#snId2").val();
        	uploadInit.config.data.parentId = parentId;
        	uploadInit.config.data.keyId = keyId;
	    	layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      if(res.code > 0){
	      	return layer.msg(res.msg);
	      }else{
	      	$("#zhutu1").attr("src",res.url+"?x-oss-process=image/resize,w_350");
	      	$("#zhutu2").attr("src",res.url+"?x-oss-process=image/resize,w_350");
	      	$("#zhutu3").attr("src",res.url+"?x-oss-process=image/resize,w_350");
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
	form.on('checkbox(ifmoresn)', function(data){
		if(data.elem.checked){
			$(".table1_tb").hide();
			$(".table2_tb").show();
			$("#moreGuige").show();
		}else{
			$(".table1_tb").show();
			$(".table2_tb").hide();
			$("#moreGuige").hide();
		}
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
		$("#khjg_table_duo").slideToggle(200);
	});
	form.on('submit(tijiao)', function(data){
		layer.load();
		var tijiao = true;
		$("#moreGuige input[mustrow]").each(function(){
			if($(this).val()==''){
				layer.msg('产品规格的内容不能留空',{icon: 5,time:2000});
				$(this).focus();
				tijiao = false;
				layer.closeAll('loading');
				return false;
			}
		});
		$("#dinghuo_moresn input[mustrow]").each(function(){
			if($(this).val()==''){
				layer.msg('请给所有级别设置订货价格',{icon: 5,time:2000});
				$(this).focus();
				tijiao = false;
				layer.closeAll('loading');
				return false;
			}
		});
		$("#khjg_table_duo input[mustrow]").each(function(){
			if($(this).val()==''){
				layer.msg('订货价格不能为空',{icon: 5,time:2000});
				$(this).focus();
				tijiao = false;
				layer.closeAll('loading');
				return false;
			}
		});
		var hasArry = new Array();
		$("#moreGuige input[name^='sn']").each(function(){
			var val = $(this).val();
			if($.inArray(val,hasArry)==-1){
				hasArry.push(val);
			}else{
				$(this).focus();
				tijiao = false;
				layer.msg('商品的编码必须是唯一的！',{icon: 5,time:5000});
				layer.closeAll('loading');
			}
		});
		return tijiao;
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
	$(".guigezhi_tt").on('input',function(){
		$(this).next().next().val(($(this).text()));
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
//多规格相关操作
function addMoreGuige(){
	var duoguigeTable= $("#duoguigeTable");
	var nowId = parseInt(duoguigeTable.attr("rowNums"));
	var nums = parseInt(duoguigeTable.attr("nums"));
	nowId = nowId+1;
	nums = nums+1;
	var trstr = '<tr id="moreGuigeTr'+nowId+'" data-id="'+nowId+'" snNums="0">'+
					'<td class="td1"><a href="javascript:" onclick="delDuoTr('+nowId+');"><img src="images/reduce2.png" /></a></td>'+
					'<td class="td1"><input type="text" name="gg['+nowId+']" onblur="updateGGName(this);" placeholder="规格名称" maxlength="10" style="width:116px;" /></td>'+
					'<td class="td2">'+
						'<div class="guigezhi">'+
							'<ul></ul>'+
							'<div class="ggz_add">'+
								'<a href="javascript:" onclick="addGuige('+nowId+');">+ 添加</a>'+
							'</div>'+
							'<div class="clearBoth"></div>'+
						'</div>'+
					'</td><input type="hidden" name="pdtKeyId'+nowId+'" id="pdtKeyId'+nowId+'" value="0">'+
				'</tr>';
	$("#addGuigeTr").before(trstr);
	duoguigeTable.attr("rowNums",nowId).attr("nums",nums);
	if(nums>2){
		$("#addGuigeTr").hide();
	}
}
function updateGGName(dom){
	if($(dom).parent().next().find("ul li").length>0){
		guigeTable();
	}
}
function delDuoTr(nowId){
	layer.confirm('确定要删除该规格吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		var duoguigeTable= $("#duoguigeTable");
		var nums = parseInt(duoguigeTable.attr("nums"));
		nums = nums-1;
		if(nums<1){
			layer.msg("请至少保留一个规格",{time:2000,icon:5});
		}else{
			$("#moreGuigeTr"+nowId).remove();
			duoguigeTable.attr("nums",nums);
			$("#addGuigeTr").show();
			guigeTable();
		}
		
	});
}
function addGuige(dataid){
	var width = ($(document).width()-530)/2;
	$("#addSndiv").attr("data-id",dataid).css({"top":"200px","left":width+'px'}).show();
	$("#bg").show();
}
function closeAddSn(){
	$("#addSndiv").hide();
	$("#bg").hide();
	$("#guigesInput").val('');
}
function addSn(){
	var rowId = $("#addSndiv").attr("data-id");
	var nowTr = $("#moreGuigeTr"+rowId);
	var startNum = parseInt(nowTr.attr("snnums"))+1;
	var guiges = $("#guigesInput").val();
	if(guiges==''){
		layer.msg("请输入规格值，多个用，分开",{time:2000,icon:5});
	}
	re = new RegExp("，","g");
	guiges = guiges.replace(re,",");
	guigeArr = guiges.split(',');
	var ul = nowTr.find(".td2 ul").eq(0);
	var hasSn = new Array();
	$(ul).find('li').each(function(){
		var str = $(this).find(".guigezhi_tt").eq(0).html();
		hasSn.push(str);
	});
	for (var i = 0; i < guigeArr.length; i++){
		guigeArr[i] = $.trim(guigeArr[i]);
		if($.inArray(guigeArr[i],hasSn)==-1&&guigeArr[i]!=''){
			startNum = startNum+i;
			var listr = '<li id="pdtKey_'+rowId+'_'+startNum+'">'+
							'<div class="guigezhi_tt" onmouseenter="tips(this,\'点击可修改，修改之后需要点击下方的“重新生成所有规格”\',1)" onmouseout="hideTips();" contenteditable="true">'+guigeArr[i]+
							'</div>'+
							'<div class="uploadSnImg1" onclick="upload_img('+rowId+','+startNum+');">'+
								'<img src="images/mrtp.gif">'+
							'</div>'+
							'<input type="hidden" name="ggseci'+rowId+'['+startNum+']" value="'+guigeArr[i]+'" id="ggseci_'+rowId+'_'+startNum+'">'+
							'<input type="hidden" name="image'+rowId+'['+startNum+']" id="image_'+rowId+'_'+startNum+'">'+
							'<div class="close-modal small js-remove-sku-atom" onclick="del_guigezhi('+rowId+','+startNum+');">×</div>'+
						'</li>';
			ul.append(listr);
			$("#snImg_"+rowId+"_"+startNum).wrap("<form id='myupload_"+rowId+"_"+startNum+"' action='?m=system&s=upload&a=upload' method='post' enctype='multipart/form-data'></form>");
			if((i+1)%9==0){
				ul.append('<div class="clearBoth"></div>');
			}
			hasSn.push(guigeArr[i]);
		}
	}
	nowTr.attr("snnums",startNum);
	closeAddSn();
	guigeTable();
}
function del_guigezhi(sn1,sn2){
	layer.confirm('确定要删除该规格值吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		//layer.load();
		var img = $("#image_"+sn1+"_"+sn2).val();
		$("#pdtKey_"+sn1+"_"+sn2).remove();
		guigeTable();
		/*$.ajax({
			type: "POST",
			url: "?m=system&s=upload&a=delImg",
			data: "img="+img,
			dataType:'text',timeout : 5000,
			success: function(resdata){
				layer.closeAll();
			}
		});*/
	});
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
function guigeTable(){
	layer.load();
	var data = $("#createPdtForm").serialize();
	$.ajax({
		type: "POST",
		url: "?m=system&s=product&a=getPricesTabel&if_create=0",
		data: data,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			dinghuoHtml = resdata.table_level;
			layer.closeAll('loading');
			$("#productId").val(resdata.productId);
			for (var i = 0; i < resdata.newIdstr.length; i++) {
				$("#duoguigeTable #pdtKeyId"+resdata.newIdstr[i].index).val(resdata.newIdstr[i].val);
				//$("#duoguigeTable tr").eq(resdata.newIdstr[i].index).find("input").eq(0).attr("name","gg[id"+resdata.newIdstr[i].val+"]");
			}
			re = new RegExp("{lipinka_str}","g");
			resdata.table = resdata.table.replace(re,lipinka_str);
			$("#moreGuige").html(resdata.table);
			$(".lipinka_str_val").each(function(){
				var val = $(this).val();
				if(val!=''&&val!='0'){
					$(this).prev().val(val);
				}
			});
			//渲染级别定价表
			$("#dinghuo_moresn .jibieCont").each(function(){
				var level = $(this).attr("data-id");
				var zhekou = $(this).attr("data-zhekou");
				re = new RegExp("{levelId}","g");
				re1 = new RegExp("{zhekou}","g");
				tablestr = resdata.table_level.replace(re,level);
				tablestr = tablestr.replace(re1,zhekou);
				$(this).find(".jiebie2_table").html(tablestr);
			});
			$("#jiage_kehu_xiang").html('');
			renderLevels();
			pendKehus();
			renderKehus();
			productListForm.render();
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}
function guigeTable1(){
	layer.load();
	var productId = $("#productId").val();
	$.ajax({
		type: "POST",
		url: "?m=system&s=product&a=getPricesTabel1",
		data: "productId="+productId,
		dataType:"json",timeout : 20000,
		success: function(resdata){
			dinghuoHtml = resdata.table_level;
			layer.closeAll('loading');
			$("#productId").val(resdata.productId);
			for (var i = 0; i < resdata.newIdstr.length; i++) {
				$("#duoguigeTable #pdtKeyId"+resdata.newIdstr[i].index).val(resdata.newIdstr[i].val);
				//$("#duoguigeTable tr").eq(resdata.newIdstr[i].index).find("input").eq(0).attr("name","gg[id"+resdata.newIdstr[i].val+"]");
			}
			re = new RegExp("{lipinka_str}","g");
			resdata.table = resdata.table.replace(re,lipinka_str);
			$("#moreGuige").html(resdata.table);
			$(".lipinka_str_val").each(function(){
				var val = $(this).val();
				if(val!=''&&val!='0'){
					$(this).prev().val(val);
				}
			});
			//渲染级别定价表
			$("#dinghuo_moresn .jibieCont").each(function(){
				var level = $(this).attr("data-id");
				var zhekou = $(this).attr("data-zhekou");
				re = new RegExp("{levelId}","g");
				re1 = new RegExp("{zhekou}","g");
				tablestr = resdata.table_level.replace(re,level);
				tablestr = tablestr.replace(re1,zhekou);
				$(this).find(".jiebie2_table").html(tablestr);
			});
			$("#jiage_kehu_xiang").html('');
			renderLevels();
			pendKehus();
			renderKehus();
			productListForm.render();
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});
}
//赋值级别订货列表
function renderLevels(){
	for(var i=0;i<levelPrices.length;i++){
		var rowInputs = $("#d_row_"+levelPrices[i][1]+"_"+levelPrices[i][2]).find("input");
		if(levelPrices[i][3]==0){
			rowInputs.eq(0).removeAttr("checked");
			$("#d_row_"+levelPrices[i][1]+"_"+levelPrices[i][2]).find("input[type='number']").prop("readonly",true).addClass('disabled');
		}
		rowInputs.eq(1).val(levelPrices[i][4]);
		rowInputs.eq(2).val(levelPrices[i][5]);
		rowInputs.eq(3).val(levelPrices[i][6]);
		rowInputs.eq(4).val(levelPrices[i][0]);
	}
	productListForm.render('checkbox');
}
//渲染客户订货列表并赋值
function pendKehus(){
	if(kehuPrices.length>0){
		kehuIds = new Array();
		for(var i=0;i<kehuPrices.length;i++){
			id = kehuPrices[i][1];
			if(kehuIds.indexOf(id)==-1){
				kehuIds.push(id);
				title = kehuPrices[i][7];
				level = kehuPrices[i][8];
				re = new RegExp("d_","g");
				re3 = new RegExp("\'d{levelId}","g");
				re1 = new RegExp("piliang_d","g");
				re2 = new RegExp("{levelId}","g");
				tablestr = dinghuoHtml.replace(re3,"'k{levelId}");
				tablestr = tablestr.replace(re,'k_');
				tablestr = tablestr.replace(re1,'piliang_k');
				tablestr = tablestr.replace(re2,id);
				var html = '<div id="k_dinghuo_row'+id+'" style="margin-bottom:20px;"><input type="hidden" name="moreKehuId['+id+']" value="'+id+'" />'+
				'<div style="background:#3caac5;height:47px;">'+
				'<div class="kehu_xiang_name">'+
				'<div class="kehu_xiang_name_left">'+
				'<span>'+title+
				'</span>'+
				'客户级别：'+level+
				'</div>'+
				'<div class="kehu_xiang_name_right">'+
				'<a href="javascript:" onclick="zhedieKehu('+id+',this);"><img src="images/zhedie.png"></a>'+
				'<a href="javascript:" onclick="delKehu('+id+',\''+title+'\');"><img src="images/close.png"></a>'+
				'</div>'+
				'</div>'+
				'</div>'+tablestr+'</div>';
				$("#jiage_kehu_xiang").prepend(html);
			}
		}
	}
}
function renderKehus(){
	if(kehuPrices.length>0){
		for(var i=0;i<kehuPrices.length;i++){
			var rowInputs = $("#k_row_"+kehuPrices[i][1]+"_"+kehuPrices[i][2]).find("input");
			if(kehuPrices[i][3]==0){
				rowInputs.eq(0).removeAttr("checked");
				$("#k_row_"+kehuPrices[i][1]+"_"+kehuPrices[i][2]).find("input[type='number']").prop("readonly",true).addClass('disabled');
			}
			rowInputs.eq(1).val(kehuPrices[i][4]);
			rowInputs.eq(2).val(kehuPrices[i][5]);
			rowInputs.eq(3).val(kehuPrices[i][6]);
			rowInputs.eq(4).val(kehuPrices[i][0]);
		}
		productListForm.render('checkbox');
	}
}
function uploadImg(rowId,startNum){
	if($("#snImg_"+rowId+"_"+startNum).val()!=""){
		var pdtKeyId = $("#pdtKeyId"+rowId).val();
		$("#myupload_"+rowId+"_"+startNum).ajaxSubmit({
			dataType:  "json",
			data: {"parentId":pdtKeyId,"keyId":startNum},
			beforeSend: function() {
				layer.load();
			},
			success: function(data) {
				layer.closeAll('loading');
				if(data.code==1){
					layer.msg(data.msg,{icon:5,time:2000});
				}else{
					$("#image_"+rowId+"_"+startNum).val(data.url);
					$("#myupload_"+rowId+"_"+startNum).prev().attr('src',data.url+'?x-oss-process=image/resize,w_54');
				}
			},
			error:function(xhr){
				layer.closeAll('loading');
				layer.msg('上传失败，请重试',{icon:5,time:2000});
			}
		});
	}
}
function upload_img(rowId,startNum){
	var img = $("#image_"+rowId+"_"+startNum).val();
	if(img==""){
		img = "/inc/img/nopic.svg";
	}
	$("#zhutu1").attr("src",img+"?x-oss-process=image/resize,w_350");
	$("#zhutu2").attr("src",img+"?x-oss-process=image/resize,w_350");
	$("#zhutu3").attr("src",img+"?x-oss-process=image/resize,w_350");
	$("#snId1").val(rowId);
	$("#snId2").val(startNum);
	$("#bg").show();
	$("#zhutu").css({'top':'10px','opacity':'1','visibility':'visible'});
}
function hide_zhutu(){
	$("#bg").hide();
	$('#zhutu').css({'top':'0px','opacity':'0','visibility':'hidden'});
}
function select_zhutu(){
	var snId = $("#snId1").val()+'_'+$("#snId2").val();
	var img = $("#zhutu1").attr("src");
	if(img!="/inc/img/nopic.svg"){
		img = img.replace("?x-oss-process=image/resize,w_350","");
		$("#image_"+snId).val(img).prev().prev().find("img").eq(0).attr("src",img+"?x-oss-process=image/resize,w_350");
	}
	$("#bg").hide();
	$('#zhutu').css({'top':'0px','opacity':'0','visibility':'hidden'});
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
function getKehuList(keyword){
	$("#kehuId").val(0);
	$("#kehuList ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
	var hasIds = '0';
	$("input[name^='moreKehuId[']").each(function(){
		hasIds+=','+$(this).val();
	});
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=getKehuList",
		data: "keyword="+keyword+"&hasIds="+hasIds,
		dataType:'text',timeout : 10000,
		success: function(resdata){
			$("#kehuList ul").html(resdata);
		}
	});
}
function selectKehu(id,title,level){
	//console.log(dinghuoHtml);
	re = new RegExp("d_","g");
	re3 = new RegExp("\'d{levelId}","g");
	re1 = new RegExp("piliang_d","g");
	re2 = new RegExp("{levelId}","g");
	tablestr = dinghuoHtml.replace(re3,"'k{levelId}");
	tablestr = tablestr.replace(re,'k_');
	tablestr = tablestr.replace(re1,'piliang_k');
	tablestr = tablestr.replace(re2,id);
	var html = '<div id="k_dinghuo_row'+id+'" style="margin-bottom:20px;"><input type="hidden" name="moreKehuId['+id+']" value="'+id+'" />'+
		'<div style="background:#3caac5;height:47px;">'+
		'<div class="kehu_xiang_name">'+
			'<div class="kehu_xiang_name_left">'+
				'<span>'+title+
				'</span>'+
				'客户级别：'+level+
			'</div>'+
			'<div class="kehu_xiang_name_right">'+
				'<a href="javascript:" onclick="zhedieKehu('+id+',this);"><img src="images/zhedie.png"></a>'+
				'<a href="javascript:" onclick="delKehu('+id+',\''+title+'\');"><img src="images/close.png"></a>'+
			'</div>'+
		'</div>'+
	'</div>'+tablestr+'</div>';
	$("#jiage_kehu_xiang").prepend(html);
	productListForm.render('checkbox');
}
function zhedieKehu(id,dom){
	$("#k_dinghuo_row"+id+" table").eq(0).slideToggle(200);
	$(dom).toggleClass('openIcon');
}
function delKehu(id,title){
	layer.confirm('确定要删除'+title+'的设置吗？', {
		btn: ['确定','取消'],
	},function(){
		$("#k_dinghuo_row"+id).remove();
		layer.closeAll();
	});
}
function checkPrice(val1,val2){
	if(val1!=val2){
		$("#pdt_status").val(0);
	}
}