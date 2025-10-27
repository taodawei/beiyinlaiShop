<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if($comId==10){
  $db_service = getCrmDb();
  $user = $db_service->get_row("select id,jifen from demo_user where id=$userId");
}else{
  $user = $db->get_row("select id,jifen from users where id=$userId");
}
$fenbiao = getFenbiao($comId,20);
$jifens = $db->get_results("select sum(jifen) as jifen,type from user_jifen$fenbiao where userId=$userId and comId=$comId group by type");
$jifen1 = 0;
$jifen2 = 0;
if(!empty($jifens)){
  foreach ($jifens as $j) {
    if($j->type==1){
      $jifen1 = $j->jifen;
    }else{
      $jifen2=$j->jifen;
    }
  }
}
?>
<link href="/skins/default/styles/jifen.css" rel="stylesheet" type="text/css">
<div class="wdjifenmingxi">
  <div class="wdjifenmingxi_1">
      积分明细
      <div class="wdjifenmingxi_1_left" onclick="go_prev_page();">
        <img src="/skins/default/images/a923_14.png"/>
      </div>
  </div>
  <div class="wdjifenmingxi_2">
      当前<b><?=$user->jifen?></b>积分
        <a href="/index.php?p=8&a=jifen_rule">积分规则</a>
    </div>
  <div class="wdjifenmingxi_3">
      <div class="wdjifenmingxi_3_left">
          积分明细
        </div>
      <div class="wdjifenmingxi_3_right">
          累计获得<span><?=$jifen1?></span> &nbsp; 累计使用<b><?=$jifen2?></b>
        </div>
      <div class="clearBoth"></div>
    </div>
  <div class="wdjifenmingxi_4"> 
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
            url: "/index.php?p=8&a=get_jfjl_list&pageNum=10&page="+page,
            data: "",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                str = '<li>'+
                    '<div class="wdjifenmingxi_4_left">'+
                        '<h2>' + item.remark + '</h2>'+
                          item.dtTime+
                    '</div>'+
                    '<div class="wdjifenmingxi_4_right">'+
                      item.jifen+
                    '</div>'+
                    '<div class="clearBoth"></div>'+
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
  </script>