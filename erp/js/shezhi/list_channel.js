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
					'<form action="?m=system&s=banner&a=addGonggaoChannel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
				    	'<div class="spxx_shanchu_tanchu_01_left">'+
				        	(id==0?'新增':'修改')+'资讯分类'+
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
		url: "?m=system&s=banner&a=dellistChannel",
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