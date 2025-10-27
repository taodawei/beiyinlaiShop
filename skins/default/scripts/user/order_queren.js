$(function(){
  countDown(endTime,1);
  $("#zhifumm").bind('input propertychange', function() {
    if($(this).val().length==6){
      $(this).blur();
    }
  });
  $("#search_addr").bind('input propertychange', function() {
    var keyword = $(this).val();
    $("#shouhuodizhi_queren_tc .shouhuodizhi_queren_1").each(function(){
      if($(this).html().indexOf(keyword)>-1){
        $(this).show();
      }else{
        $(this).hide();
      }
    });
  });
});
function change_pay(index,type){
  pay_type = type;
  $(".querendingdan_5_right .querendingdan_5_right_on").removeClass('querendingdan_5_right_on');
  $(".querendingdan_5_right li").eq(index).find("a").addClass('querendingdan_5_right_on');
}
function show_address(){
  $("#shouhuodizhi_queren_tc").show();
}
function select_address(addressId,name,phone,address){
  layer.open({type:2});
  $.ajax({
    type: "POST",
    url: "/index.php?p=19&a=change_address",
    data: "addressId="+addressId+"&order_id="+order_id,
    dataType:"json",timeout : 10000,
    success: function(resdata){
      layer.closeAll();
      var str = '<h2>'+name+' <b>'+phone+'</b></h2>'+address;
      $(".querendingdan_2_02").html(str);
      $("#shouhuodizhi_queren_tc").hide();
      address_id = addressId;
    }
  });
}
function pay(){
  if(address_id==0){
    layer.open({content:'请先选择收货地址',skin:'msg',time:2});
    return false;
  }
  if(pay_type=='yue'){
    $("#zhifu_div").show();
    $("#zhifumm").focus();
  }else if(pay_type=='weixin'){
    location.href='/index.php?p=19&a=weixin_pay&order_id='+order_id;
  }
}
function yue_pay(){
  var zhifumm = $("#zhifumm").val();
  var beizhu = $("#beizhu").val();
  layer.open({type:2});
  $.ajax({
    type: "POST",
    url: "/index.php?p=19&a=yue_pay",
    data: "order_id="+order_id+"&zhifumm="+zhifumm+"&beizhu="+beizhu,
    dataType:"json",timeout : 10000,
    success: function(resdata){
      layer.closeAll();
      layer.open({content:resdata.message,skin:'msg',time:2});
      if(resdata.code==1){
        if(resdata.tuanId>0){
          url = '/index.php?p=19&a=view_tuan&id='+resdata.tuanId;
        }else if(resdata.buy_type==2){
          url = '/index.php?p=19&a=view&id='+order_id+"&share=1";
        }else{
          url = '/index.php?p=8';
        }
        setTimeout(function(){location.href=url;},1900);
      }
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
      $("#jishiqi"+id).html(hour+":"+minute+":"+second);
    }else{
      $("#jishiqi"+id).html("无效");
    }
  }, 1000);
}