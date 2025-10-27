var lay_flow;
var initPage = 0;
var initScroll = 0;
var initItems = [];
layui.use('flow', function(){
	lay_flow = layui.flow;
	/*if(sessionStorage.getItem("init")==1){
	    init_pdt_list();
	}*/
	rend_pdt_list();
});
//重新渲染flow组件
function init_pdt_list(){
  initPage = parseInt(sessionStorage.getItem("initPage"));
  initScroll = sessionStorage.getItem("initScroll");
  initItems = JSON.parse(sessionStorage.getItem("initItems"));
  if(initItems.length>0){
  $.each(initItems, function(index, item){
      var str = '<li>'+
                  '<a onclick="setInitScroll();" href="/index.php?p=4&a=views&id='+item.inventoryId+'">'+
                      '<div class="shangpinlist_img">'+
                          '<img src="'+item.img+'"/>'+
                      '</div>'+
                      '<div class="shangpinlist_tt">'+
                        '<div class="shangpinlist_tt_01">  '+
                            item.title+
                        '</div>'+
                        '<div class="shangpinlist_tt_02">  '+
                          item.jifen+'积分 <span><img src="/skins/default/images/jifenduihuan_1.png" alt=""/></span>'+
                        '</div>'+
                      '</div>'+
                      '<div class="clearBoth"></div>'+
                    '</a>'+
                  '</li>';
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
        		data: "is_jifen=1",
        		dataType:"json",timeout : 20000,
        		success: function(res){
        			$.each(res.data, function(index, item){
        				initItems.push(item);
        				str = '<li>'+
                    '<a onclick="setInitScroll();" href="/index.php?p=4&a=views&id='+item.inventoryId+'">'+
                        '<div class="shangpinlist_img">'+
                            '<img src="'+item.img+'"/>'+
                        '</div>'+
                        '<div class="shangpinlist_tt">'+
                          '<div class="shangpinlist_tt_01">  '+
                              item.title+
                          '</div>'+
                          '<div class="shangpinlist_tt_02">  '+
                            item.jifen+'积分 <span><img src="/skins/default/images/jifenduihuan_1.png" alt=""/></span>'+
                          '</div>'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
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
        			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
        		}
        	});
        }
    });
}
function setInitScroll(){
  sessionStorage.setItem("initScroll",$(document).scrollTop());
}
