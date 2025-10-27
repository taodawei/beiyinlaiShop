var lay_flow;
layui.use('flow', function(){
  lay_flow = layui.flow;
  lay_flow.load({
      elem: '#flow_ul'
      ,done: function(page, next){
        layer.closeAll();
        var lis = [];
        $.ajax({
          type: "POST",
          url: "/index.php?p=8&a=get_xiaji_list&pageNum=10&page="+page,
          data: "",
          dataType:"json",timeout : 20000,
          success: function(res){
            console.log(res);
            $.each(res.data, function(index, item){
              str = '<li>'+
                  '<div class="wodeziyuan_3_down_01">'+
                      '<img src="'+item.image+'"/>'+
                    '</div>'+
                  '<div class="wodeziyuan_3_down_02" onclick="show_info('+item.id+')" data-id="'+item.id+'">'+
                      '<h2>'+item.name+' <span>('+item.level+')</span></h2>'+
                        '<img src="/skins/demo/images/wodeziyuan_11.png"/> '+item.phone+
                    '</div>'+
                  '<div class="wodeziyuan_3_down_03">'+
                      '¥'+item.fanli+
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
});
$(function(){
  $.ajax({
    type:"POST",
    url:"/index.php?p=8&a=get_fanli_money",
    data:"",
    timeout:"30000",
    dataType:"json",
    success: function(res){
      if(res.code==1){
        $("#money_1").text(res.money1);
        $("#money_2").text(res.money2);
      }
    },
    error:function(e){
      alert("超时,请重试");
    }
  });
  $(".wodeziyuan_3_down_02").click(function(){
    var id = $(this).attr('data-id');
    $.ajax({
      type:"POST",
      url:"/index.php?p=8&a=gttzxx",
      data:"id="+id,
      timeout:"30000",
      dataType:"json",
      success: function(res){
        //layer.open({content:res.message,skin: 'msg',time: 2});
        if(res.code==1){
          $(".huiyuan_tuanzhang1_02_left").html(res.xx1);
          $(".huiyuan_tuanzhang1_02_right").html(res.xx2);
          $("#copywxh").attr('data-clipboard-text', $('#wxh').text());
          $("#bdtel").attr('href', 'tel:'+$('#tel').text());
          $(".huiyuan_tuanzhang_tc").show();
        }
      },
      error:function(e){
        alert("超时,请重试");
      }
    });
  });
  $(".huiyuan_tuanzhang_close").click(function(){
    $(".huiyuan_tuanzhang_tc").hide();
  });
  
  //初始化复制功能
  if($("#copywxh").length>0){
      btn1 = document.getElementById('copywxh');//$("#wxh")[0];
      var clipboard1 = new ClipboardJS(btn1);
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
  }
});
function show_info(id){
  $.ajax({
      type:"POST",
      url:"/index.php?p=8&a=gttzxx",
      data:"id="+id,
      timeout:"30000",
      dataType:"json",
      success: function(res){
        if(res.code==1){
          $(".huiyuan_tuanzhang1_02_left").html(res.xx1);
          $(".huiyuan_tuanzhang1_02_right").html(res.xx2);
          $("#copywxh").attr('data-clipboard-text', $('#wxh').text());
          $("#bdtel").attr('href', 'tel:'+$('#tel').text());
          $(".huiyuan_tuanzhang_tc").show();
        }
      },
      error:function(e){
        alert("超时,请重试");
      }
    });
}