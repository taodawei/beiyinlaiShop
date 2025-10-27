var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	rend_order_list();
});
function qiehuan_scene(index){
	$(".shouhouliebiao_up .shouhouliebiao_up_on").removeClass('shouhouliebiao_up_on');
	$(".shouhouliebiao_up ul li").eq(index).find('a').addClass('shouhouliebiao_up_on');
	status = index;
	if(index==2)status=-1;
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
        		url: "/index.php?p=21&a=get_list&pageNum=10&page="+page,
        		data: "status="+status,
        		dataType:"json",timeout : 20000,
        		success: function(res){
              console.log(res);
        			$.each(res.data, function(index, item){
        				str = '<li onclick="location.href=\'/index.php?p=21&a=view&id='+item.id+'\'">'+
                  '<div class="shouhouliebiao_down_up">'+
                    '退换货编号：'+item.sn+
                  '</div>';
                  $.each(item.products,function(key,val){
                    str+='<div class="shouhouliebiao_down_down">'+
                    '<div class="shouhouliebiao_down_down_left">'+
                        '<img src="'+val.image+'"/>'+
                      '</div>'+
                    '<div class="shouhouliebiao_down_down_right">'+
                        '<h2>'+val.title+'</h2>'+
                          '¥'+val.price_sale+' &nbsp;&nbsp; 数量：'+val.num+
                      '</div>'+
                    '<div class="clearBoth"></div>'+
                  '</div>';
                  });
                  str+='<div class="wokaidetuan_2_03">'+
                      '<div class="wokaidetuan_2_03_left">'+
                          '申请类型：<span style="color:#cf2950">'+item.type_info+'</span>'+
                      '</div>';
                      if(item.type<3){
                        str=str+'<div class="wokaidetuan_2_03_right">'+
                            '<span style="background:none;color:#cf2950">退款金额：'+item.money+'</span>'+
                        '</div>';
                      }
                      str=str+'<div class="clearBoth"></div>'+
                  '</div>'+
                  '<div class="wokaidetuan_2_03">'+
                      '<div class="wokaidetuan_2_03_left">'+
                          '当前进度：<span style="color:#cf2950">'+item.status_info+'</span>'+
                      '</div>'+
                    '<div class="clearBoth"></div>'+
                  '</div>';
                  if(item.status==-1){
                    str = str+'</div>'+
                      '<div class="wokaidetuan_2_03">'+
                          '<div class="wokaidetuan_2_03_left">'+
                              '驳回原因：<span style="color:#cf2950">'+item.dealCont+'</span>'+
                          '</div>'+
                          '<div class="clearBoth"></div>'+
                      '</div>';
                  }
                str =str+'</li>';
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
function countDown(time,id){
  var end_time1 = time;
  var sys_second1 = (end_time1-new Date().getTime())/1000;
  setInterval(function(){
    if(sys_second1>1) {
      sys_second1 -= 1;
      var day = Math.floor((sys_second1 / 3600) / 24);
      var hour = Math.floor((sys_second1 / 3600) % 24);
      var minute = Math.floor((sys_second1 / 60) % 60);
      var second = Math.floor(sys_second1 % 60);
      if(day>0){
        hour = day*24+hour;
      }
      if(minute<10){
      	minute = '0'+minute;
      }
      if(second<10){
      	second = '0'+second;
      }
      $("#jishiqi"+id).html("剩余 "+hour+":"+minute+":"+second);
    }else{
      $("#jishiqi"+id).html("无效");
    }
  }, 1000);
}