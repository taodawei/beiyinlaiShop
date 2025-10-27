var tuijian_page = 0;
var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
	rend_pdt_list();
  var url = window.location.href;
  url = encodeURIComponent(url);
  WeChat(url,share_url,share_title,share_img,share_desc,0);
});
function rend_pdt_list(){
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
        	layer.closeAll();
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=4&a=get_pdt_list&pageNum=20&page="+page,
        		data: "xinren=1",
        		dataType:"json",timeout : 10000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				str = '<li>'+
        					'<a href="/index.php?p=4&a=view&xinren=1&id='+item.inventoryId+'">'+
			                	'<div class="xianshiqianggou_3_02_img">'+
			                    	'<img src="'+item.img+'" />'+
			                    '</div>'+
			                	'<div class="xianshiqianggou_3_02_tt" style="width:10rem">'+
			                    	'<div class="xianshiqianggou_3_02_tt_1">'+
			                        	'<h2>'+item.title+'</h2>'+
			                        '</div>'+
			                    	'<div class="xianshiqianggou_3_02_tt_2" >'+
			                        	'<div class="xianshiqianggou_3_02_tt_2_left">'+
			                            	'<h2>'+((item.key_vals==''||item.key_vals=='无')?'规格：无':item.key_vals)+'</h2>'+
			                                '<font style="font-size:.5rem;">新人专享：</font>¥'+item.price_user+'<br>'+
			                                '<span>¥'+item.price_market+'</span>'+
			                            '</div>'+
			                        	'<div class="xianshiqianggou_3_02_tt_2_qiangouzhong">'+
			                            	'<h2>购买</h2>'+
			                            '</div>'+
			                        	'<div class="clearBoth"></div>'+
			                        '</div>'+
			                    '</div>'+
			                	'<div class="clearBoth"></div>'+
			                '</a></li>';
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
  var end_time1 = time*1000;
  console.log(end_time1);
  console.log(new Date().getTime());
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
      $("#jishiqi"+id).html('距结束 <span>'+hour+'</span> : <span>'+minute+'</span> : <span>'+second+'</span>');
    }else{
      $("#jishiqi"+id).html("无效");
    }
  }, 1000);
}