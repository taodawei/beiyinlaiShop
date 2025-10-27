var zeng_yhq_id=0;
$(function(){
  qiehuan_yhq(1);
});
function show_next(dom){
  $(dom).parent().next().next().toggle();
  $(dom).find(".zsyhq_3_right_3_right img").toggleClass("zsyhq_3_right_3_right_img");
}
function zeng(id,jian,man,tiaojian,startTime,endTime,color){
  zeng_yhq_id = id;
  $("#zsyhq_zhuanzeng_tc").show();
  $("#e_color").css("background-color",color);
  $("#e_jian").text(jian);
  $("#e_man").text(man);
  $("#e_tiaojian").html(tiaojian);
  $("#e_time").html(startTime+'-'+endTime);
}
function zengsong(){
  var user = $("#add_user").val();
  if(user==''){
    layer.open({content:'会员账号不能为空',skin: 'msg',time: 2});
    return false;
  }
  layer.open({type:2});
  $.ajax({
    type: "POST",
    url: "/index.php?p=8&a=check_zeng",
    data: "username="+user,
    dataType:"json",timeout : 5000,
    success: function(resdata){
      layer.closeAll();
      if(resdata.code==1){
        layer.open({
          content: '确定要将该优惠券赠送给'+user+'('+resdata.name+')'+'吗？'
          ,btn: ['确定', '取消']
          ,yes: function(index){
            $.ajax({
              type: "POST",
              url: "/index.php?p=8&a=zeng_yhq",
              data: "yhq_id="+zeng_yhq_id+"&user_id="+resdata.userId+"&username="+user,
              dataType:"json",timeout : 5000,
              success: function(res){
                layer.closeAll();
                layer.open({content:res.message,skin:'msg',time:2});
                if(res.code==1){
                  var num = parseInt($("#wei_num").text());
                  $("#wei_num").text(num-1);
                  $("#yhq_li_"+zeng_yhq_id).remove();
                  $("#zsyhq_zhuanzeng_tc").hide();
                }
              },
              error: function() {
                layer.closeAll('loading');
                layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
              }
            });
          }
        });
      }else{
        layer.open({content:resdata.message,skin:'msg',time:2});
      }
    },
    error: function() {
      layer.closeAll('loading');
      layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
    }
  });
}
function qiehuan_yhq(index){
  $(".zsyhq_2 .zsyhq_2_on").removeClass('zsyhq_2_on');
  $(".zsyhq_2 ul li").eq(index-1).find('a').addClass('zsyhq_2_on');
  $("#flow_ul").html('');
  layui.use('flow', function(){
    lay_flow = layui.flow;
    layer.open({type:2,content:'加载中'});
    lay_flow.load({
      elem: '#flow_ul'
      ,done: function(page, next){
        layer.closeAll();
        var lis = [];
        $.ajax({
          type: "POST",
          url: "/index.php?p=8&a=get_myyhq_list&pageNum=20&page="+page,
          data: "scene="+index,
          dataType:"json",timeout : 8000,
          success: function(res){
            $.each(res.data, function(index, item){
              str = '<li id="yhq_li_'+item.id+'">'+
                '<div class="zsyhq_3_left" style="background-color:'+(item.status==0?item.color:'#c2c2c2')+'">'+
                    '<h2>￥<b>'+item.jian+'</b></h2>满'+item.man+'元可用'+
                '</div>'+
                '<div class="zsyhq_3_right">'+
                    '<div class="zsyhq_3_right_1">'+
                        item.tiaojian+
                    '</div>'+
                    '<div class="zsyhq_3_right_2">'+
                        item.startTime+'-'+item.endTime+
                    '</div>';
                    if(item.status==0){
                      str = str+'<div class="zsyhq_3_right_3" onclick="show_next(this);">'+
                          '<div class="zsyhq_3_right_3_left">'+
                              '<img src="/skins/default/images/a928_12.png"/> 可赠送'+
                          '</div>'+
                          '<div class="zsyhq_3_right_3_right">'+
                              '<img src="/skins/default/images/a928_13.png"/>'+
                          '</div>'+
                          '<div class="clearBoth"></div>'+
                        '</div>'+
                      '<div class="zsyhq_3_right_4">'+(item.image==''?'':'<img src="/skins/default/images/'+item.image+'.png"/>')+
                      '</div>'+
                      '<div class="zsyhq_3_right_5" onclick="location.href=\'/index.php?p=4&yhq_id='+item.id+'\'">'+
                          '立即使用'+
                      '</div>';
                    }else{
                      str = str+'<div class="zsyhq_3_right_3"></div>';
                    }
                  str = str+'</div><div class="clearBoth"></div>';
                  if(item.status==0){
                    str = str+'<div class="zsyhq_3_zhuanzeng" onclick="zeng('+item.id+',\''+item.jian+'\',\''+item.man+'\',\''+item.tiaojian+'\',\''+item.startTime+'\',\''+item.endTime+'\',\''+item.color+'\')">'+
                      '<img src="/skins/default/images/a928_15.png"/> 赠送朋友'+
                    '</div>';
                  }else if(item.status==1){
                    str = str+'<div class="zsyhq_3_yishiyong">'+
                      '<img src="/skins/default/images/a928_16.png">'+
                    '</div>';
                  }
              '</li>';
              lis.push(str);
            });
            next(lis.join(''), page < res.pages);
            $("#flow_ul").append('<div class="clearBoth"></div>');
          },
          error: function() {
            layer.closeAll();
            layer.msg('数据请求失败', {icon: 5});
          }
        });
      }
    });
  });
}