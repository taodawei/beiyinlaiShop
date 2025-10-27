<link href="/skins/default/styles/youhuiquan.css" rel="stylesheet" type="text/css">
<div class="zsyhq" style="background-color:#f6f6f6;">
  <div class="zsyhq_1">
      领券中心
        <div class="zsyhq_1_left" onclick="go_prev_page();">
          <img src="/skins/default/images/a923_1.png"/>
        </div>
    </div>
    <div class="zslingquanzhongxin">
      <ul id="flow_ul">
      </ul>
    </div>
</div>
<script type="text/javascript">
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
            url: "/index.php?p=8&a=get_yhq_list&pageNum=20&page="+page,
            data: "",
            dataType:"json",timeout : 8000,
            success: function(res){
              $.each(res.data, function(index, item){
                str = '<li>'+
                      '<div class="zsyhq_3_left" style="background-color:'+item.color+';">'+
                          '<h2>￥<b>'+item.money+'</b></h2>满'+item.man+'元可用'+
                        '</div>'+
                        '<div class="zslingquanzhongxin_right">'+
                          '<h2>'+item.tiaojian+'</h2>'+
                            item.startTime+'-'+item.endTime+
                        '</div>';
                        if(item.if_lingqu==1){
                          str = str+'<div class="zslingquanzhongxin_biao_1">'+
                            '<img src="/skins/default/images/a928_17.png"/>'+
                          '</div>'+
                          '<div class="zslingquanzhongxin_biao_2" onclick="location.href=\'/index.php?p=4&yhq_id='+item.lingqu_id+'\'">'+
                            '<img src="/skins/default/images/a928_19.png"/>'+
                          '</div>';
                          if(item.if_ke_lingqu==1){
                            str = str+'<div class="zslingquanzhongxin_biao_2" style="top:3.2rem" onclick="lingqu('+item.id+');">'+
                              '<img src="/skins/default/images/a928_18.png"/>'+
                            '</div>';
                          }
                        }else{
                          str = str+'<div class="zslingquanzhongxin_biao_1" style="display:none">'+
                              '<img src="/skins/default/images/a928_17.png"/>'+
                            '</div><div class="zslingquanzhongxin_biao_2" style="display:none" onclick="">'+
                              '<img src="/skins/default/images/a928_19.png"/>'+
                            '</div>'+
                            '<div class="zslingquanzhongxin_biao_2" onclick="lingqu('+item.id+',this);">'+
                              '<img src="/skins/default/images/a928_18.png"/>'+
                            '</div>';
                        }
                        str = str+'<div class="clearBoth"></div>'+
                    '</li>';
                lis.push(str);
              });
              next(lis.join(''), page < res.pages);
              $("#flow_ul").append('<div class="clearBoth"></div>');
            },
            error: function() {
              layer.closeAll();
            }
          });
        }
      });
    });
    function lingqu(id,dom){
      layer.open({type:2});
      $.ajax({
        type: "POST",
        url: "/index.php?p=8&a=yhq_lingqu",
        data: "id="+id,
        dataType:"json",timeout :10000,
        success: function(res){
          layer.closeAll();
          layer.open({content:res.message,skin: 'msg',time: 2});
          if(res.code==1){
            $(dom).hide().prev().show();
            $(dom).prev().prev().show();
            $(dom).prev().attr('onclick','location.href="/index.php?p=4&yhq_id='+res.yhq_id+'"');
          }
        }
      });
    }
  </script>