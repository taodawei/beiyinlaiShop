<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$yaoqing_num = $db->get_var("select count(*) from users where shangji=$userId");
$yongjin = $db->get_var("select sum(money) from user_liushui$fenbiao where userId=$userId and type=2");
//$users_yaoqing = $db->get_row("select * from users_yaoqing where userId=$userId");
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/guangchang.css">
<div class="wodeerweima">
    <div class="wodeerweima_1">	
    	我的好友
        <div class="wodeerweima_1_left" onclick="go_prev_page();">
        	<img src="/skins/erp_zong/images/a923_1.png" />
        </div>
    </div>
    <div class="wodehaoyou">
    	<div class="wodehaoyou_1">
        	<ul>
        		<li>
                	<h2><?=(int)$users_yaoqing->nums?></h2>我的好友
                </li>
                <li>
                	<h2><?=(int)$users_yaoqing->dikoujin?></h2>总收入
                </li>
                <div class="clearBoth"></div>
        	</ul>
        </div>
    	<div class="wodehaoyou_2">
        	<ul id="flow_ul">
        		
        	</ul>
        </div>
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
            url: "/index.php?p=8&a=get_friends&pageNum=20&page="+page,
            data: "",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                str = '<li>'+
                    '<div class="wodehaoyou_2_left">'+
                        '<h2>'+item.username+'</h2>'+
                        item.dtTime+
                    '</div>'+
                    '<div class="wodehaoyou_2_right">'+
                        
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