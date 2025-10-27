var layupload;
layui.use(['upload'], function(){
	layupload = layui.upload
});
function edit_brand(id, pid, title,en_title, imgurl,backimg, is_hot = 0){
    window.location.href="?m=system&s=product_brand&a=addProductBrand&id="+id+"&pid="+pid; //2.当前页面打开URL页面
    if (is_hot == 0){
		var selectHot = '<option value="0" selected>否</option><option value="1">是</option><';
	}else{
		var selectHot = '<option value="0">否</option><option value="1" selected>是</option><';
	}
    
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			return false;
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
		'<form action="?m=system&s=product_brand&a=addProductBrand&id='+id+'&parentId='+parentId+'" method="post" id="brandForm"><div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+
		(id==0?'新增':'修改')+'商品品牌'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02">'+
		'<div class="jiliang_tanchu">'+
		'<span>*</span> 品牌名称 <input type="text" name="title" id="brand_title" value="'+title+'"><br>'+
			'<span>*</span> 英文名称 <input type="text" name="en_title" id="brand_title" value="'+en_title+'"><br>'+
									'<span>*</span> 是否推荐 '+
							'<select name="is_index">' +selectHot+ '</select><br>'+
		'<span>&nbsp;</span> 品牌图标 <img src="'+(imgurl==''?'images/add.jpg':imgurl)+'" style="margin-left:10px;cursor:pointer;height:50px" id="channel_img">'+
// 		'<span>&nbsp;</span> 品牌附图 <img src="'+(backimg==''?'images/add.jpg':backimg)+'" style="margin-left:10px;cursor:pointer;height:50px" id="channel_back">'+
		'</div>'+
		'</div>'+
		'<input type="hidden" name="originalPic" id="channel_imgurl" value="'+imgurl+'" />'+
		'<input type="hidden" name="backimg" id="channel_backimg" value="'+backimg+'" />'+
		'</form></div>'
		,success: function(layero){
			layupload.render({
			    elem: '#channel_img'
			    ,url: '?m=system&s=upload&a=upload&limit_width=no'
			    ,before:function(){
			    	layer.load();
			    }
			    ,done: function(res){
			      layer.closeAll('loading');
			      if(res.code > 0){
			      	return layer.msg(res.msg);
			      }else{
			      	$("#channel_img").attr("src",res.url);
			      	$("#channel_imgurl").val(res.url);
			      }
			  	}
			  	,error: function(){
			  		layer.msg('上传失败，请重试', {icon: 5});
			  	}
			});
			
			layupload.render({
			    elem: '#channel_back'
			    ,url: '?m=system&s=upload&a=upload&limit_width=no'
			    ,before:function(){
			    	layer.load();
			    }
			    ,done: function(res){
			      layer.closeAll('loading');
			      if(res.code > 0){
			      	return layer.msg(res.msg);
			      }else{
			      	$("#channel_back").attr("src",res.url);
			      	$("#channel_backimg").val(res.url);
			      }
			  	}
			  	,error: function(){
			  		layer.msg('上传失败，请重试', {icon: 5});
			  	}
			});
			
			var btn = layero.find('.layui-layer-btn');
			btn.find('.layui-layer-btn0').attr({
				href: 'javascript:checkBrandForm();'
			});
			return false;
		}
	});
}
var submit = 1;
function checkBrandForm(){
	if(submit==1){
		if($("#brand_title").val()==''){
			layer.msg('品牌名称不能为空',{zIndex:99891014,anim:6,time:2000});
			return false;
		}
		submit = 0;
		$("#brandForm").submit();
	}
}
function delBrand(id){
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=product_brand&a=delBrand",
		data: "&id="+id,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll('loading');
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				var ids = resdata.ids.split(',');
				for(i=0;i<ids.length;i++){
					$(".shangpinguanli li[data-id='"+ids[i]+"']").remove();
				}
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});	
}