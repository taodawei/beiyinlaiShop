var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	rend_order_list();
});
//重新渲染flow组件
function rend_order_list(){
	$("#flow_ul").html('');
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
        	layer.closeAll();
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=13&a=get_my_shipin&pageNum=20&page="+page,
        		data: "status="+status,
        		dataType:"json",timeout : 8000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				str = '<li '+(status==0?'onclick="layer.open({content:\'视频审核通过之后才能观看和分享\',skin:\'msg\',time: 2});"':'onclick="location.href=\'/index.php?p=13&a=view&id='+item.id+'\'"')+'>'+
                  '<div class="shipin_3_img">'+
                      '<img src="'+item.image+'"/>'+
                      '<img src="/skins/default/images/zhuye_play.png" class="anniu"/>'+
                    '</div>'+
                  '<div class="shipin_3_01">'+
                      '<span>'+item.title+'</span>'+
                    '</div>'+
                  '<div class="shipin_3_02">'+
                      '<div class="shipin_3_02_left">'+
                          (status==0?'待审核':item.clicks+'次播放')+
                        '</div>'+
                      '<div class="shipin_3_02_right">'+
                          ''+
                        '</div>'+
                      '<div class="clearBoth"></div>'+
                    '</div>'+
                  '<div class="shipin_3_03">'+
                      item.title+
                    '</div>'+
                '</li>';
        				lis.push(str);
        			});
        			next(lis.join(''), page < res.pages);
        		},
        		error: function() {
        			layer.closeAll();
        			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
        		}
        	});
        }
    });
}