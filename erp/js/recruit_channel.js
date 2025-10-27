var layupload;
layui.use(['upload'], function(){
	layupload = layui.upload
});
$(function(){
	$(".shangpinguanli_01_left").click(function(){
		var iconspan = $(this).find("span");
		if(iconspan.attr("class")=='shangpinguanli_01_left_03'){
			iconspan.attr("class","shangpinguanli_01_left_01");
		}else if(iconspan.attr("class")=='shangpinguanli_01_left_01'){
			iconspan.attr("class","shangpinguanli_01_left_03");
		} 
		var nowli = $(this).parent().parent();
		var id = nowli.attr("data-id");
		nowli.find("div[pid='"+id+"']").slideToggle(100);
	});
});
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#channelId").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}

function edit_channel(id,pid,title, en_title, imgurl,backimg,is_hot = 0, type=0, remark =''){
	if (type == 0){
		var selectType = '<option value="0" selected>默认</option><option value="1">首页列表</option>';
	}else if(type == 1){
		var selectType = '<option value="0">默认</option><option value="1" selected>首页列表</option>';
	}else{
		var selectType = '<option value="0">默认</option><option value="1">首页列表</option>';
	}
	if (is_hot == 0){
		var selectHot = '<option value="0" selected>否</option><option value="1">是</option>';
	}else{
		var selectHot = '<option value="0">否</option><option value="1" selected>是</option>';
	}

	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_recruit_channels",
		data: "&id="+id+"&pid="+pid,
		dataType:"text",timeout : 8000,
		success: function(resdata) {
			layer.closeAll('loading');
			layer.open({
		        type: 1
		        ,title: false //不显示标题栏
		        ,closeBtn: false
		        ,area: '530px;'
		        ,shade: 0.3
		        ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
		        ,btn: ['提交', '取消']
		        ,yes: function(index, layero){
		        	return false;
				}
		        ,btnAlign: 'r'
		        ,zIndex: layer.zIndex
		        ,success: function(layero){
		        	layer.setTop(layero);
		        }
		        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;height: 300px;">'+
					'<form action="?m=system&s=recruit_channel&a=addProductChannel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
				    	'<div class="spxx_shanchu_tanchu_01_left">'+
				        	(id==0?'新增':'修改')+'招聘分类'+
				        '</div>'+
				    	'<div class="spxx_shanchu_tanchu_01_right">'+
				        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				        '</div>'+
				    	'<div class="clearBoth"></div>'+
				    '</div>'+
					'<div class="spxx_shanchu_tanchu_02" style="height:160px;">'+
				    	'<div class="jiliang_tanchu">'+
				        	'<span>*</span> 所属分类 '+
				            '<select name="parentId"><option value="0">顶级分类</option>'+resdata+'</select>'+
				            '<br>'+
				            '<span>*</span> 中文分类 <input type="text" name="title" id="channel_title" value="'+title+'"><br>'+
				            '<span>*</span> 英文分类 <input type="text" name="en_title" value="'+en_title+'"><br>'+
				// 			'<span>*</span> 分类补充 <input type="text" name="remark" id="channel_remark" value="'+remark+'"><br>'+
				// 			'<span>*</span> 所在位置 '+
				// 			'<select name="type">' +selectType+ '</select><br>'+
							'<span>*</span> 是否展示 '+
							'<select name="is_hot">' +selectHot+ '</select>'+
				            // '<span>&nbsp;</span> 分类图标 <img src="'+(imgurl==''?'images/add.jpg':imgurl)+'" style="margin-left:10px;cursor:pointer;height:50px" id="channel_img"> &nbsp;<a href="javascript:" onclick="del_channel_img();">删除</a><br>'+
				            // '<span>&nbsp;</span> 背景图 <img src="'+(backimg==''?'images/add.jpg':backimg)+'" style="margin-left:10px;cursor:pointer;height:50px" id="channel_backimg"> &nbsp;<a href="javascript:" onclick="del_channel_backimg();">删除</a><br>'+
				            // 	'<span>说明：图片需处理大小比例1：1，不超过1M，图片类型处理成png。</span>'+
				        '</div>'+
				    '</div>'+
				    '<input type="hidden" name="originalPic" id="channel_imgurl" value="'+imgurl+'" />'+
				    '<input type="hidden" name="backimg" id="backimg" value="'+backimg+'" />'+
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
					    elem: '#channel_backimg'
					    ,url: '?m=system&s=upload&a=upload&limit_width=no'
					    ,before:function(){
					    	layer.load();
					    }
					    ,done: function(res){
					      layer.closeAll('loading');
					      if(res.code > 0){
					      	return layer.msg(res.msg);
					      }else{
					      	$("#channel_backimg").attr("src",res.url);
					      	$("#backimg").val(res.url);
					      }
					  	}
					  	,error: function(){
					  		layer.msg('上传失败，请重试', {icon: 5});
					  	}
					});
		          var btn = layero.find('.layui-layer-btn');
		          btn.find('.layui-layer-btn0').attr({
		            href: 'javascript:checkChannelForm();'
		          });
		          return false;
		        }
		      });
		},
		error: function() {
			layer.closeAll('loading');
			layer.msg('数据请求失败', {icon: 5});
		}
	});	
}

function del_channel_backimg(){
	$("#channel_backimg").attr("src",'images/add.jpg');
	$("#backimg").val('');
}

function del_channel_img(){
	$("#channel_img").attr("src",'images/add.jpg');
	$("#channel_imgurl").val('');
}

var submit = 1;
function checkChannelForm(){
	if(submit==1){
		if($("#channel_title").val()==''){
			layer.msg('分类名称不能为空',{zIndex:99891014,anim:6,time:2000});
			$("#channel_title").focus();
			return false;
		}
		submit = 0;
		$("#channelForm").submit();
	}
}
function delChannel(id){
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=recruit_channel&a=delChannel",
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