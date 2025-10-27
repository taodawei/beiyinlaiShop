<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$id = (int)$request['id'];
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/lpk.css">
<div class="wode">
    <div class="lpk_1">
        <div class="lpk_1_1">流水记录</div>
        <div class="lpk_1_2" onclick="history.go(-1);"><img src="/skins/erp_zong/images/lpk_icon1.png"></div>
        <div class="lpk_1_3"></div>
    </div>
    <div class="lsjl">
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
            url: "/index.php?p=8&a=get_card_liushui&pageNum=20&page="+page,
            data: "id=<?=$id?>",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                str = '<li>'+
                  '<div class="lsjl_1"><img src="/skins/erp_zong/images/lpk_icon6.png"></div>'+
                  '<div class="lsjl_2">'+
                    '<div class="lsjl_2_1"><span>'+item.remark+'</span><br>'+item.orderInfo+'</div>'+
                    '<div class="lsjl_2_2">'+item.dtTime+'</div>'+
                  '</div>'+
                  '<div class="lsjl_3"><span>'+item.money+'</span><br>余额：'+item.yue+'</div>'+
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