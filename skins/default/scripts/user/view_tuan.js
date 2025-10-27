var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	if(dai_chengtuan==1){
		countDown(endTime,1);
	}
	rend_pdt_list();
});
function rend_pdt_list(){
	$("#flow_ul").html('');
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
        	layer.closeAll();
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=4&a=get_pdt_list&pageNum=10&page="+page,
        		data: '',
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				str = '<li class="chanpin_7_02_rightline chanpin_7_02_bottomline">'+
                    '<a href="/index.php?p=4&a=view&id='+item.inventoryId+'">'+
                        '<div class="chanpin_7_02_img">'+
                            '<img src="'+(item.img==''?'/inc/img/nopic.svg':item.img)+'"/>'+
                          '</div>'+
                        '<div class="chanpin_7_02_tt">'+
                            item.title+
                          '</div>'+
                        '<div class="chanpin_7_02_price">'+
                            '￥<span>'+(Math.floor(item.price_sale*100)/100)+'</span>'+
                          '</div>'+
                      '</a>'+
                  '</li>';
                  if((index+1)%3==0){
                    str+='<div class="clearBoth"></div>';
                  }
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
function share(){
  $("#fenxiang_tc").show();
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
      $("#jishiqi"+id).html("<span>"+hour+"</span>：<span>"+minute+"</span>：<span>"+second+"</span>");
    }else{
      $("#jishiqi"+id).html("已结束");
    }
  }, 1000);
}