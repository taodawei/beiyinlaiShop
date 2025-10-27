var lay_page;
layui.use(['laypage'],function(){
	lay_page = layui.laypage;
	render_pdt_list();
});
function render_pdt_list(){
	var page = $("#page").val();
	var status = $("#status").val();
	var keyword = $("#keyword").val();
	layer.load();
	$.ajax({
		type: "POST",
		url: "?s=yingxiao&a=get_shipin_list",
		data: "keyword="+keyword+"&status="+status+"&page="+page,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			$("#fankui_list").html('');
			$("#wei_num").text(resdata.wei_num);
			$("#yi_num").text(resdata.yi_num);
			$.each(resdata.data,function(key,val){
				listr = '<li>';
					if(val.shipin==''){
						listr +='<img src="'+val.originalPic+'"  width="412" height="240">';
					}else{
						listr +='<video src="'+val.shipin+'" controls="controls" preload="none" width="412" height="240"></video>';
					}
                    listr +='<div class="video_btm">'+
                            '<div class="video_btm_left">'+
                                val.title+'<br><span style="font-size:16px;">'+val.mendian+'</span>'+
                            '</div>'+
                            '<div class="video_btm_right">'+
                                '<a href="javascript:" onclick="del_shipin('+val.id+',\''+val.shipin+'\');"><img src="images/video_delete.png" /></a>'+
                                '<a href="?s=yingxiao&a=addShipin&id='+val.id+'"><span>修改</span></a>';
                            listr = listr+'</div>'+
                        '</div>'+
                    '</li>';
                $("#fankui_list").append(listr);
			});
			lay_page.render({
   				elem: 'fenye'
   				,limit:6
   				,curr:page
    			,count: resdata.count
    			,theme: '#1E9FFF'
    			,layout: ['prev', 'page', 'next', 'skip','count']
    			,jump: function(obj, first){
    				if(!first){
					    $("#page").val(obj.curr);
					    render_pdt_list();
					}
				}
			});
			layer.closeAll();
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请检查网络', {icon: 5});
		}
	});
}
function pass_shipin(id){
	layer.confirm('确定审核通过该视频吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST", 
			url: "?s=yingxiao&a=shenhe_shipin",
			data: "id="+id,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				render_pdt_list();
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请检查网络', {icon: 5});
			}
		});
	});
}
function del_shipin(id,url){
	layer.confirm('确定要删除该视频吗？', {
		btn: ['确定','取消'],
	},function(){
		layer.load();
		$.ajax({
			type: "POST", 
			url: "?s=yingxiao&a=del_shipin",
			data: "id="+id,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				render_pdt_list();
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败，请检查网络', {icon: 5});
			}
		});
		$.ajax({type:"POST",url:"?s=upload&a=delImg",data:"img="+url,dataType:"string",timeout:1000});
	});
}