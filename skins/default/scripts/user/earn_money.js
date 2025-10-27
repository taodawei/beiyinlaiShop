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
	$(".dingdanmingxi_2 .dingdanmingxi_2_on").removeClass('dingdanmingxi_2_on');
	$(".dingdanmingxi_2 ul li").eq(index).find('a').addClass('dingdanmingxi_2_on');
	scene = index;
  //初始化数据
  clearCacheDate();
	rend_order_list();
}
function search_order(){
  layer.open({
    btn: ['搜索', '取消'],
    title: [
      '搜索订单',
      'background-color: #FF4351; color:#fff;'
    ]
    ,content: '<div style="text-align:center"><input type="text" class="search_keyword" style="height:1.2rem;width:100%;padding-left:.5rem" placeholder="订单号/会员姓名/手机号" /></div>'
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
        		url: "/index.php?p=19&a=get_earnorder_list&pageNum=20&page="+page,
        		data: "scene="+scene+"&keyword="+keyword,
        		dataType:"json",timeout : 10000,
        		success: function(res){
        			$.each(res.data, function(index, item){
                initItems.push(item);
        				str = '<div class="dingdanmingxi_3">'+
                        '<div class="dingdanmingxi_3_01">'+
                            '<div class="dingdanmingxi_3_01_left">'+
                                '创建：'+item.dtTime+
                            '</div>'+
                            '<div class="dingdanmingxi_3_01_right" '+(item.status==1?'':'style="color:#ff4800;"')+'>'+
                                item.statusInfo+
                            '</div>'+
                            '<div class="clearBoth"></div>'+
                        '</div>';
                        if(item.products.length>0){
                          $.each(item.products,function(key,val){
                          str+='<div class="dingdanmingxi_3_02">'+
                            '<div class="dingdanmingxi_3_02_img">'+
                                '<img src="'+val.image+'"/>'+
                            '</div>'+
                            '<div class="dingdanmingxi_3_02_tt">'+
                                '<div class="dingdanmingxi_3_02_tt_1">'+
                                    val.title+
                                '</div>'+
                                '<div class="dingdanmingxi_3_02_tt_2">'+
                                    '<div class="dingdanmingxi_3_02_tt_2_left">'+
                                        val.price_sale+
                                    '</div>';
                                    if(key==item.products.length-1){
                                      str+='<div class="dingdanmingxi_3_02_tt_2_right">'+
                                        '赚佣'+item.yongjin+'元'+
                                    '</div>';
                                    }
                                    str+='<div class="clearBoth"></div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="clearBoth"></div>'+
                        '</div>';
                          });
                        }
                        str+='<div class="dingdanmingxi_3_03">'+
                            '<div class="dingdanmingxi_3_03_left">'+
                                '订单单号：'+item.orderId+
                            '</div>'+
                            '<div class="dingdanmingxi_3_03_right">'+
                                '<span class="copy_order" data-clipboard-text="'+item.orderId+'">复制</span>'+
                            '</div>'+
                            '<div class="clearBoth"></div>'+
                        '</div>'+
                    '</div>';
        				lis.push(str);
        			});
        			next(lis.join(''), page < res.pages);
              //sessionStorage.setItem('initPage',page);
              //sessionStorage.setItem('initItems',JSON.stringify(initItems));
              //console.log(initItems);
              
              var clipboard1 = new ClipboardJS(".copy_order");
                clipboard1.on('success', function(e) {
                    layer.open({
                        content: '已复制'
                        ,skin: 'msg'
                        ,time: 2
                    });
                });
                clipboard1.on('error', function(e) {
                    layer.open({
                        content: '您的浏览器不支持复制，请自行选择复制！'
                        ,skin: 'msg'
                        ,time: 2
                    });
                });
        		},
        		error: function() {
        			layer.closeAll();
        			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
        		}
        	});
        }
    });
}