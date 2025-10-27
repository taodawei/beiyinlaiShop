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
layui.use(['form','upload','laydate'], function(){
	var form = layui.form
	,upload = layui.upload,laydate = layui.laydate,
	productListForm = form;
	guigeTable1();
	/*ajaxpost = $.ajax({
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
	});*/
	laydate.render({
        elem: '#youxiaoqi_start'
    });
    laydate.render({
        elem: '#youxiaoqi_end'
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
    laydate.render({
        elem: '#endTime'
        ,min: '2019-01-01'
        ,btns: ['confirm']
        ,type:'datetime'
        ,format: 'yyyy-MM-dd HH:mm'
    });
	form.on('select(ps1)',function(data){
        if(!isNaN(data.value)){
            layer.load();
            id = data.value;
            ajaxpost=$.ajax({
                type:"POST",
                url:"/erp_service.php?action=getAreas",
                data:"id="+id,
                timeout:"4000",
                dataType:"text",
                success: function(html){
                    $("#ps3").html('<option value="">请先选择市</option>');
                    if(html!=""){
                        $("#ps2").html(html);
                    }
                    form.render('select');
                    layer.closeAll('loading');
                },
                error:function(){
                    alert("超时,请重试");
                }
            });
        }            
    });
    form.on('select(ps2)',function(data){
        if(!isNaN(data.value)){
            layer.load();
            id = data.value;
            ajaxpost=$.ajax({
                type:"POST",
                url:"/erp_service.php?action=getAreas",
                data:"id="+id,
                timeout:"4000",
                dataType:"text",
                success: function(html){
                    if(html!=""){
                        $("#ps3").html(html);
                        $("#shiId").val(id);
                    }else{
                        $("#shiId").val(id);
                    }
                    form.render('select');
                    layer.closeAll('loading');
                },
                error:function(){
                    alert("超时,请重试");
                }
            });
        }
    });
    form.on('select(ps3)',function(data){
        if(!isNaN(data.value)){
            $("#psarea").val(data.value);
        }
    });
	upload.render({
	    elem: '#uploadPdtImage'
	    ,url: '?m=system&s=upload&a=upload&width=800&height=600'
	    ,before:function(){
	    	layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      if(res.code > 0){
	      	return layer.msg(res.msg);
	      }else{
	      	var nums = parseInt($('#uploadImages').attr("data-num"))+1;
	      	$('#uploadImages').before('<li id="image_li'+nums+'"><a><img src="'+res.url+'?x-oss-process=image/resize,w_122" width="122" height="122"></a><div class="close-modal small js-remove-sku-atom" onclick="del_image('+nums+');">×</div></li>');
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
	    ,url: '?m=system&s=upload&a=upload&width=800&height=600'
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
});
function quxiao(){
	layer.confirm('取消后您输入的信息不能保存，确定要取消吗？', {
		btn: ['确定','取消'],
	},function(){
		history.go(-1);
	});
}
function checkPrice(val1,val2){
	if(val1!=val2){
		$("#pdt_status").val(0);
	}
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
							'<div class="guigezhi_tt">'+guigeArr[i]+
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
		url: "?m=system&s=pdts&a=getPricesTabel",
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
			$("#moreGuige").html(resdata.table);
			//渲染级别定价表
			/*$("#dinghuo_moresn .jibieCont").each(function(){
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
			renderKehus();*/
			productListForm.render('checkbox');
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
		url: "?m=system&s=pdts&a=getPricesTabel1",
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
			$("#moreGuige").html(resdata.table);
			/*//渲染级别定价表
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
			renderKehus();*/
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
function jisuan_choucheng(rowid){
	var price_sale = $("#price_sale_"+rowid).val();
	var price_cost = $("#price_cost_"+rowid).val();
	var price_card = $("#price_card_"+rowid).val();
	if(price_cost!='' && price_sale!='' && price_card!=''){
		var money = (price_sale-price_cost-price_card).toFixed(2);
		$("#price_gonghuo_"+rowid).val(money);
	}
}
function piliang_choucheng(){
	var price_sale = $("#piliang_sale").val();
	var price_cost = $("#piliang_cost").val();
	var price_card = $("#piliang_card").val();
	if(price_cost!='' && price_sale!='' && price_card!=''){
		var money = (price_sale-price_cost-price_card).toFixed(2);
		$(".piliang_gonghuo").val(money);
	}
}