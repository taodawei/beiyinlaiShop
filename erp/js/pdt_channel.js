var lay_upload,now_title;
layui.use(['upload'],function(){
	lay_upload = layui.upload;
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
function edit_channel(id,pid,title,img){
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_pdts_channels",
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
		        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
					'<form action="?m=system&s=pdts_channel&a=addProductChannel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
				    	'<div class="spxx_shanchu_tanchu_01_left">'+
				        	(id==0?'新增':'修改')+'商品分类'+
				        '</div>'+
				    	'<div class="spxx_shanchu_tanchu_01_right">'+
				        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
				        '</div>'+
				    	'<div class="clearBoth"></div>'+
				    '</div>'+
					'<div class="spxx_shanchu_tanchu_02" style="height:186px">'+
				    	'<div class="jiliang_tanchu">'+
				        	'<span>*</span> 所属分类 '+
				            '<select name="parentId"><option value="0">顶级分类</option>'+resdata+'</select>'+
				            '<br>'+
				            '<span>*</span> 分类名称 <input type="text" name="title" id="channel_title" value="'+title+'"><br>'+
				            '<span>&nbsp;</span> 分类图片 <img id="upload_channel_img" src="'+(img.length>5?img:'images/mrtp.gif')+'" width="70" height="70" style="margin-left:12px;margin-top:10px;cursor:pointer">&nbsp;&nbsp;<font color="red">建议上传尺寸120*120，大小不超过200K</font><br>'+
				        '</div>'+
				    '<input type="hidden" name="originalPic" value="'+img+'" id="upload_originalPic"></div>'+
				'</form></div>'
		        ,success: function(layero){
		        	lay_upload.render({
		        		elem: '#upload_channel_img'
		        		,url: '?m=system&s=upload&a=upload&width=120&height=120'
		        		,before:function(){
		        			layer.load();
		        		}
		        		,done: function(res){
		        			layer.closeAll('loading');
		        			if(res.code > 0){
		        				return layer.msg(res.msg);
		        			}else{
		        				$("#upload_originalPic").val(res.url);
		        				$("#upload_channel_img").attr("src",res.url);
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
		url: "?m=system&s=pdts_channel&a=delChannel",
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
function pull_down(channelId,channel_title,force){
	now_title = channel_title;
	var tags_div = $("#channel_tags_"+channelId);
	if(tags_div.css("display")=='none'||force==1){
		if(force==1||tags_div.find('ul').length==0){
			load_channel_tags(channelId,channel_title);
		}
		if(tags_div.css("display")=='none'){
			tags_div.slideDown(200);
			$("#channel_menu_"+channelId).addClass("openIcon");
		}
	}else{
		$("#channel_menu_"+channelId).removeClass("openIcon");
		tags_div.slideUp(200);
	}
}
function load_channel_tags(channelId,channel_title){
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=pdts_channel&a=get_Channel_tags",
		data: "&id="+channelId,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				if(resdata.items.length>0){
					html = '<ul>';
					$.each(resdata.items,function(key,val){
						if(val.tags.length>0){
							html=html+'<li>'+
								'<div class="shangpinguanli_01">'+
									'<div class="shangpinguanli_01_left">'+
										'<div class="tags_left">'+
											'<a href="javascript:" onclick="edit_tag('+channelId+',\''+channel_title+'\','+val.id+',\''+val.title+'\');" onmouseover="tips(this,\'点击修改名称\',1)" onmouseout="hideTips();" >'+val.title+'</a>'+
											'<i class="close-modal small js-remove-sku-atom"  onclick="del_tags('+channelId+',\''+channel_title+'\','+val.id+',\''+val.title+'\');">×</i>'+
										'</div>';
										$.each(val.tags,function(key1,val1){
											html=html+'<div class="tags_right">'+
												'<a href="javascript:" onclick="edit_tag('+channelId+',\''+channel_title+'\','+val1.id+',\''+val1.title+'\');" onmouseover="tips(this,\'点击修改名称\',1)" onmouseout="hideTips();">'+val1.title+'</a>'+
												'<i class="close-modal small js-remove-sku-atom" onclick="del_tags('+channelId+',\''+channel_title+'\','+val1.id+',\''+val1.title+'\');">×</i>'+
											'</div>';
											if(key1+1<val.tags.length){
												html=html+'、';
											}
										});
										html=html+'<div class="tags_right" id="add_tags_'+channelId+'"><a href="javascript:" onclick="add_tags('+channelId+',\''+channel_title+'\','+val.id+',\''+val.title+'\');"  style="color:#00d2ff;margin-left:20px;" ><img src="images/biao_57.png"/> 新增标签</a></div>'+
										'<div class="clearBoth"></div>'+
									'</div>'+
								'</div>'+
							'</li>';
						}
					});
					html = html+'</ul>';
				}else{
					html = '<ul><li><div class="shangpinguanli_01"><div class="shangpinguanli_01_left">暂无标签</div></div></li></ul>';
				}
				$("#channel_tags_"+channelId).html(html);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});
}
function del_tags(channelId,channel_title,tagId,tag_title){
	layer.confirm('确定要删除该标签吗？', {
	  btn: ['确定','取消'],
	}, function(){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=pdts_channel&a=delTag",
			data: "&id="+tagId,
			dataType:"json",
			timeout : 20000,
			success: function(resdata) {
				layer.closeAll();
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					pull_down(channelId,channel_title,1);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('超时，数据请求失败', {icon: 5});
			}
		});	
		return true;
	});
}
function edit_tag(channelId,channel_title,tagId,tag_title){
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
        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
			'<form action="?m=system&s=pdts_channel&a=edit_tag&id='+tagId+'" method="post" id="tagForm"><div class="spxx_shanchu_tanchu_01">'+
		    	'<div class="spxx_shanchu_tanchu_01_left">'+
		        	'修改商品标签'+
		        '</div>'+
		    	'<div class="spxx_shanchu_tanchu_01_right">'+
		        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		        '</div>'+
		    	'<div class="clearBoth"></div>'+
		    '</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:106px">'+
		    	'<div class="jiliang_tanchu">'+
		            '<span>*</span> 标签名称 <input type="text" name="title" id="tag_title" value="'+tag_title+'"'+
		        '</div>'+
		'</form></div>'
        ,success: function(layero){
        	var btn = layero.find('.layui-layer-btn');
        	btn.find('.layui-layer-btn0').attr({
        		href: 'javascript:checkTagForm('+channelId+',\''+channel_title+'\','+tagId+');'
        	});
        	return false;
        }
    });
}
function checkTagForm(channelId,channel_title,tagId){
	var title = $("#tag_title").val();
	if(title==''){
		layer.msg('标题不能为空',function(){});
		return false;
	}
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=pdts_channel&a=editTag",
		data: "&id="+tagId+"&title="+title,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				pull_down(channelId,channel_title,1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});
}
function add_tags(channelId,channel_title,tagId,tag_title){
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
        ,zIndex: layer.zIndex
        ,success: function(layero){
        	layer.setTop(layero);
        }
        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
			'<form action="?m=system&s=pdts_channel&a=edit_tag&id='+tagId+'" method="post" id="tagForm"><div class="spxx_shanchu_tanchu_01">'+
		    	'<div class="spxx_shanchu_tanchu_01_left">'+
		        	'添加商品标签'+
		        '</div>'+
		    	'<div class="spxx_shanchu_tanchu_01_right">'+
		        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		        '</div>'+
		    	'<div class="clearBoth"></div>'+
		    '</div>'+
			'<div class="spxx_shanchu_tanchu_02" style="height:196px">'+
		    	'<div class="jiliang_tanchu" style="line-height:46px;">'+
		            '<span>&nbsp;</span> 所属分类 <input type="text" value="'+channel_title+'" readonly="true" class="disabled"><br>'+
		            '<span>*</span> 标签名称 <input type="text" id="tag_ptitle" value="'+tag_title+'" '+(tagId>0?'readonly="true" class="disabled"':'')+'><br>'+
		            '<span>*</span> 　类型值 <input type="text" id="tag_ztitle" value=""><br><span style="color:red;padding-left:80px">PS:多个类型值用，分开</span>'+
		        '</div>'+
		'</form></div>'
        ,success: function(layero){
        	var btn = layero.find('.layui-layer-btn');
        	btn.find('.layui-layer-btn0').attr({
        		href: 'javascript:addTags('+channelId+',\''+channel_title+'\','+tagId+');'
        	});
        	return false;
        }
    });
}
function addTags(channelId,channel_title,tagId){
	var ptitle = $("#tag_ptitle").val();
	var ztitle = $("#tag_ztitle").val();
	if(ptitle==''||ztitle==''){
		layer.msg('标签不能为空',function(){});
		return false;
	}
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=pdts_channel&a=addTags",
		data: "channelId="+channelId+"&pId="+tagId+"&ptitle="+ptitle+"&ztitle="+ztitle,
		dataType:"json",
		timeout : 20000,
		success: function(resdata) {
			layer.closeAll();
			if(resdata.code==0){
				layer.msg(resdata.message, {icon: 5});
			}else{
				pull_down(channelId,channel_title,1);
			}
		},
		error: function() {
			layer.closeAll();
			layer.msg('超时，数据请求失败', {icon: 5});
		}
	});
}