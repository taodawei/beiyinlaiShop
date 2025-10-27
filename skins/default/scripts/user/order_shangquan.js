var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	rend_order_list();
});
function qiehuan_scene(index){
	$(".pintuandingdan_up .wokaidetuan_1_on").removeClass('wokaidetuan_1_on');
	$(".pintuandingdan_up ul li").eq(index).find('a').addClass('wokaidetuan_1_on');
	scene = index;
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
        		url: "/index.php?p=19&a=get_shangquan_list&pageNum=10&page="+page,
        		data: "scene="+scene+"&type="+order_type,
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			//console.log(res);
        			$.each(res.data, function(index, item){
        				str = '<li onclick="location.href=\'/index.php?p=19&a=view_shangquan&id='+item.id+'\'">'+
		                    '<div class="wokaidetuan_2_01">'+
		                        '<div class="wokaidetuan_2_01_left">'+
		                            '下单时间：'+item.dtTime+
		                        '</div>'+
		                        '<div class="wokaidetuan_2_01_right">'+
		                            item.statusInfo+
		                        '</div>'+
		                        '<div class="clearBoth"></div>'+
		                    '</div>'+
		                    '<div class="wokaidetuan_2_02">'+
		                        '<div class="wokaidetuan_2_02_img">'+
		                            '<img src="'+item.image+'" />'+
		                        '</div>'+
		                        '<div class="wokaidetuan_2_02_tt">'+
		                            '<div class="wokaidetuan_2_02_tt_01">'+
		                                item.mendian+
		                            '</div>'+
                                '<div class="tongchengdingdan_2_02_tt_02">'+
                                    item.product+
                                '</div>'+
		                            '<div class="tongchengdingdan_2_02_tt_03">'+
		                                '¥<b>'+item.price_sale+'</b>'+
		                            '</div>'+
		                        '</div>'+
		                        '<div class="clearBoth"></div>'+
		                    '</div>'+
		                    '<div class="wokaidetuan_2_03">'+
		                        '<div class="wokaidetuan_2_03_left">'+
		                            '实付：¥'+item.price_payed+' <span>共'+item.num+'张</span>'+
		                        '</div>';
		                        str=str+'<div class="clearBoth"></div>'+
		                    '</div>'+
		                '</li>';
        				lis.push(str);
        				if(item.jishiqi==1){
        					countDown(item.endTime,item.id);
        				}
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
  var end_time1 = new Date(time).getTime();
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