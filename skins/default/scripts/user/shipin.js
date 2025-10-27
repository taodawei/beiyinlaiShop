var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	rend_order_list();
});
function qiehuan_scene(index){
	$(".shipin_2 .shipin_2_on").removeClass('shipin_2_on');
	$(".shipin_2 ul li").eq(index).find('a').addClass('shipin_2_on');
	channelId = index+1;
  keyword = '';
  $("#keyword").val('');
	rend_order_list();
}
function search_shipin(){
      keyword = $("#keyword").val();
      rend_order_list();
}
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
        		url: "/index.php?p=13&a=get_shipin_list&pageNum=20&page="+page,
        		data: "channelId="+channelId+"&keyword="+keyword,
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				str = '<li onclick="location.href=\'/index.php?p=13&a=view&id='+item.id+'\'">'+
                  '<div class="shipin_3_img">'+
                      '<img src="'+item.image+'"/>'+
                      '<img src="/skins/demo/images/zhuye_play.png" class="anniu"/>'+
                    '</div>'+
                  '<div class="shipin_3_01">'+
                      '<span>'+item.title+'</span>'+
                    '</div>'+
                  '<div class="shipin_3_02">'+
                      '<div class="shipin_3_02_left">'+
                          item.clicks+'次播放'+
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