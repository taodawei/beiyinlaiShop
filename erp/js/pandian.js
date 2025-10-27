function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#channelId").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
layui.use(['upload','form'], function(){
	var form = layui.form
	,upload = layui.upload;
	upload.render({
	    elem: '#uploadFile'
	    ,url: '?m=system&s=upload&a=uploadXls'
	    ,accept: 'file'
    	,exts: 'xls|xlsx'
	    ,before: function(obj){
	    	obj.preview(function(index, file, result){
	    		$("#uploadMsg").html('已选择：'+file.name);
		      //console.log(file);
		    });
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      if(res.code > 0){
	      	return layer.msg(res.msg);
	      }else{
	      	res.url;
	      	$('#filepath').val(res.url);
	      }
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      $("#uploadMsg").html('');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});

	upload.render({
	    elem: '#uploadFile1'
	    ,url: '?m=system&s=upload&a=uploadXls'
	    ,accept: 'file'
    	,exts: 'xls|xlsx'
	    ,before: function(obj){
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      //导入成功之后
	      $.ajax({
			type:"post",
			url:"?m=system&s=product&a=daorushuo1&type=0",
			data:"filepath="+res.url,
			timeout:"60000",
			dataType:"json",
			async:false,
			success: function(data){
				// reloadTable(0);
				layer.msg(data.content);
				//window.location.reload();
			},
			error:function(){
			    
	           // alert("超时,请刷新");
	        }

	    });
	      //导入成功之后
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});
	
	upload.render({
	    elem: '#uploadFile2'
	    ,url: '?m=system&s=upload&a=uploadXls'
	    ,accept: 'file'
    	,exts: 'xls|xlsx'
	    ,before: function(obj){
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      //导入成功之后
	      $.ajax({
			type:"post",
			url:"?m=system&s=product&a=daorushuo1&type=1",
			data:"filepath="+res.url,
			timeout:"60000",
			dataType:"json",
			async:false,
			success: function(data){
				// reloadTable(0);
				layer.msg(data.content);
				//window.location.reload();
			},
			error:function(){
			    
	           // alert("超时,请刷新");
	        }

	    });
	      //导入成功之后
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});
	
    upload.render({
	    elem: '#uploadFile3'
	    ,url: '?m=system&s=upload&a=uploadPdf'
	    ,accept: 'file'
    	,exts: 'pdf'
	    ,before: function(obj){
	        console.log(1111,obj);
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      //导入成功之后
	      $.ajax({
			type:"post",
			url:"?m=system&s=product&a=setBook&inventoryId=<?=$inventoryId?>&productId=<?=$inventory->productId?>",
			data:"filepath="+res.url,
			timeout:"60000",
			dataType:"json",
			async:false,
			success: function(data){
				// reloadTable(0);
				layer.msg(data.content);
				window.location.reload();
			},
			error:function(){
			    
	           // alert("超时,请刷新");
	        }

	    });
	      //导入成功之后
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});
	
	ajaxpost=$.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_product_channels1",
		data: "",
		dataType:"text",timeout : 10000,
		success: function(resdata){
			$("#selectChannels").append(resdata);
		},
		error: function() {
			layer.msg('数据请求失败', {icon: 5});
		}
	});
	$("#selectChannel").click(function(){
		$(this).parent().toggleClass('layui-form-selected');
	});
});
//导出导入操作
function daochu(){
	var storeId = $("#storeId option:selected").val();
	var channelId = $("#channelId").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&storeId="+storeId+"&channelId="+channelId);
}
function checkForm(){
	if($("#type_info").length>0&&$("#type_info option:selected").val()==''){
		layer.msg('请先选择入库类型',function(){});
		return false;
	}
	if($("#filepath").val()==''){
		layer.msg('请先上传修改后的数据文件',function(){});
		return false;
	}
	$("#pandianForm").submit();
}