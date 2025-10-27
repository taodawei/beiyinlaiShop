var lay_flow;
var initPage = 0;
var initScroll = 0;
var initItems = [];
layui.use('flow', function(){
	lay_flow = layui.flow;
  if(sessionStorage.getItem("init")==1){
    //init_order_list();
  }
  rend_order_list();
});
function qiehuan_scene(index){
	$(".bddd_cont_1 .bddd_cont_1_on").removeClass('bddd_cont_1_on');
	$(".bddd_cont_1 ul li").eq(index).find('a').addClass('bddd_cont_1_on');
	scene = index;
  //初始化数据
  clearCacheDate();
	rend_order_list();
}
function search_order(){
  layer.open({
    btn: ['搜索', '取消'],
    title: [
      '收货信息或产品信息',
      'background-color: #FF4351; color:#fff;'
    ]
    ,content: '<div style="text-align:center"><input type="text" class="search_keyword" style="height:1.2rem;width:100%;padding-left:.5rem" placeholder="收货人/手机号/产品" /></div>'
    ,yes:function(){
      keyword = $(".search_keyword").eq(0).val();
      clearCacheDate();
      rend_order_list();
    }
  });
}
function setInitScroll(){
  sessionStorage.setItem("initScroll",$(document).scrollTop());
}
function del_order(id,comId){
  layer.open({
    content: '是否删除此订单？订单删除后将不再显示，如有需要请联系客服恢复订单'
    ,btn: ['确定', '不要']
    ,yes: function(index){
      $.ajax({
          type: "POST",
          url: "/index.php?p=22&a=del_order",
          data: "id="+id+"&comId="+comId,
          dataType:"json",timeout : 10000,
          success: function(res){
            $('#order_li_'+id).remove();
          },error: function() {
              layer.closeAll();
              layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
            }
      });
      layer.close(index);
    }
  });
}
//重新渲染flow组件
function rend_order_list(){
	//$("#flow_ul").html('');
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
          page = parseInt(page+initPage);
        	layer.closeAll();
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=22&a=get_order_list&pageNum=10&page="+page,
        		data: "scene="+scene+"&keyword="+keyword,
        		dataType:"json",timeout : 10000,
        		success: function(res){
        			$.each(res.data, function(index, item){
                initItems.push(item);
        				str = '<li>'+
                    '<div class="bddd_cont_2_1" onclick="setInitScroll();location.href=\'/index.php?p=22&a=view_order&id='+item.id+'\'">'+
                        '<div class="bddd_cont_2_1_left">'+
                            '订单编号：'+item.orderId+
                        '</div>'+
                        '<div class="bddd_cont_2_1_right">'+
                          item.statusInfo+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                    '</div>';
                    $.each(item.products,function(ind,val){
                      str += '<div class="bddd_cont_2_2" onclick="setInitScroll();location.href=\'/index.php?p=22&a=view_order&id='+item.id+'\'">'+
                        '<div class="bddd_cont_2_2_left">'+
                            '<img src="'+val.image+'"/>'+
                        '</div>'+
                        '<div class="bddd_cont_2_2_right">'+
                          '<div class="bddd_cont_2_2_right_01">'+
                            val.title+
                          '</div>'+
                          '<div class="bddd_cont_2_2_right_02">'+
                              '规格: '+val.key_vals+'<br>数量: ×'+val.num+
                          '</div>'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                      '</div>';
                    });
                    str+='<div class="bddd_cont_2_4">'+
                        '下单时间：'+item.dtTime+' <br>'+
                        '合计金额：￥'+item.price+'（共'+item.num+'件商品）'+
                      '</div>'+
                      '<div class="bddd_cont_2_3">'+
                          '<div class="bddd_cont_2_3_right">';
                            if(item.status==5||item.status==-1){
                              str = str+'<a href="javascript:" onclick="del_order('+item.id+','+item.comId+')" class="bddd_cont_2_3_right_01">删除订单</a>';
                            }
                            if(item.status==4){
                              str = str+'<a href="javascript:" onclick="hexiaoma(\''+item.hexiaoma+'\')" class="bddd_cont_2_3_right_02">核销码</a>';
                            }
                            if(item.jishiqi==1){
                              str+='<a href="/index.php?p=22&a=pay&id='+item.id+'" class="bddd_cont_2_3_right_02">立即支付</a>';
                            }
                            str = str+'</div>'+
                          '<div class="clearBoth"></div>'+
                        '</div>'+
                    '</li>';
        				lis.push(str);
        				if(item.jishiqi==1){
        					countDown(item.endTime,item.id);
        				}
        			});
        			next(lis.join(''), page < res.pages);
              sessionStorage.setItem('initPage',page);
              sessionStorage.setItem('initItems',JSON.stringify(initItems));
              //console.log(initItems);
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
function hexiaoma(ma){
    layer.open({
        title: [
          '提货码',
          'background-color: #FF4351; color:#fff;'
        ]
        ,content: ma
    });
}