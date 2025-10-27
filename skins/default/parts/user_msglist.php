<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
if($_SESSION['if_tongbu']==1){
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $comId = 10;
}
$fenbiao = getFenbiao($comId,20);
$lastId = (int)$db->get_var("select id from user_msg$fenbiao order by id desc limit 1");
$ifhas = $db->get_var("select userId from user_msg_read where userId=$userId and comId=$comId");
if(empty($ifhas)){
  $db->query("insert into user_msg_read(userId,comId,msgId) value($userId,$comId,$lastId)");
}else{
  $db->query("update user_msg_read set msgId=$lastId where userId=$userId and comId=$comId");
}
?>
<div class="zhifu">
  <div class="zhifu_1">
    消息中心
    <div class="zhifu_1_left" onclick="go_prev_page();">
      <img src="/skins/default/images/fanhui_1.png"/>
    </div>
  </div>
  <div class="xiaoxixiangqing">
    <ul id="flow_ul"></ul>
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
            url: "/index.php?p=8&a=get_msg_list&pageNum=20&page="+page,
            data: "",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                str = '<li onclick="yidu(this,'+item.id+');'+((item.url=='')?'':'location.href=\''+item.url+'\'')+'">'+
                    '<div class="xiaoxixiangqing_up">'+
                      '<div class="xiaoxixiangqing_up_left">'+
                          item.content+
                      '</div>'+
                      '<div class="xiaoxixiangqing_up_right">'+
                          item.dtTime+
                      '</div>'+
                      '<div class="clearBoth"></div>'+
                    '</div>';
                    if(item.ifread==0){
                      str +='<div class="xiaoxixiangqing_weidu"></div>';
                    }
                str += '</li>';
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
    function yidu(dom,id){
      $(dom).find('.xiaoxixiangqing_weidu').hide();
      $.ajax({
        type: "POST",
        url: "/index.php?p=8&a=read_msg",
        data: "id="+id,
        dataType:"json",timeout :100,
        success: function(res){}
      });
    }
  </script>