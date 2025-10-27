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
	$(".wodefensi_4 .wodefensi_4_on").removeClass('wodefensi_4_on');
	$(".wodefensi_4 ul li").eq(index).find('a').addClass('wodefensi_4_on');
	scene = index;
  //初始化数据
  clearCacheDate();
	rend_order_list();
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
        		url: "/index.php?p=8&a=get_earn_fans&pageNum=20&page="+page,
        		data: "scene="+scene+"&keyword="+keyword,
        		dataType:"json",timeout : 10000,
        		success: function(res){
              $("#nums").html(res.count);
              $("#hasnum").html(res.hasnum);
              $("#weinum").html(res.weinum);
        			$.each(res.data, function(index, item){
        				str = '<li>'+
                        '<div class="wodefensilist_img">'+
                            '<img src="'+item.image+'" />'+
                          '</div>'+
                        '<div class="wodefensilist_tt">'+
                            item.phone+' <span>'+item.level+'</span>'+
                              '<h2>'+item.dtTime+' <span '+(item.hasbuy==1?'style="background:#FED904;color:#000;"':'style="background:#ccc;color:#666;"')+'>'+(item.hasbuy==1?'已下单':'未下单')+'</span></h2>'+
                          '</div>'+
                        '<div class="wodefensilist_tt2">'+
                            '<a href="javascript:" onclick="show_info('+item.id+',\''+item.name+'\',\''+item.image+'\',\''+item.wxh+'\',\''+item.fans+'\',\''+item.dtTime+'\')">已推'+item.fans+'人 ></a>'+
                          '</div>'+
                        '<div class="clearBoth"></div>'+
                      '</li>';
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
function show_info(id,name,image,wxh,fans,dtTime){
  $.ajax({
    type: "POST",
    url: "/index.php?p=8&a=get_fans_yugu",
    data: "id="+id,
    dataType:"json",timeout : 10000,
    success: function(res){
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_1 img").prop("src",image);
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_2_02").text(name);
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_2_03").text('微信号：'+wxh);
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_3 h2").text(fans);
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_4 h2").eq(0).text(res.last_shouru+'元');
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_4 h2").eq(1).text(res.zong_shouru+'元');
      $("#wodefensi_xiangxi_tc .wodefensi_xiangxi_5").text('注册时间：'+dtTime);
      $("#wodefensi_xiangxi_tc").show();
    },
    error: function() {
      layer.closeAll();
      layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
    }
  });
}