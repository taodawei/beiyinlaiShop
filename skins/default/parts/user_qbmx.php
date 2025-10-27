<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
/*if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $db_service = getCrmDb();
  $user = $db_service->get_row("select id,username,name as nickname,image,level,money from demo_user where id=$userId");
}else{
  $user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
}*/
$type = (int)$request['type'];
$remark = trim($request['remark']);
$sd = trim($request['sd']);
$ed = trim($request['ed']);
?>
<div class="wode">
  <div class="wode_1">
      余额记录
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=qianbao'">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
    </div>
  <div class="yuejilu">
      <div class="yuejilu_up">
          <div class="yuejilu_up_left">
              <h2>余额</h2>¥<?=$user->money?>
            </div>
          <div class="yuejilu_up_right">
              <img src="/skins/default/images/shaixuan_1.png" alt=""/>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="yuejilu_down">
          <ul id="flow_ul"></ul>
        </div>
    </div>
</div>
<!--余额记录-筛选-->
<div class="yuejilu_shaixuan_tc" style="display:none;">
  <div class="yuejilu_shaixuan_bj">
    </div>
  <div class="yuejilu_shaixuan">
      <div class="yuejilu_shaixuan_1">
          <ul>
            <li>
                  <a href="/index.php?p=8&a=qbmx">全部</a>
                </li>
                <li>
                  <a href="/index.php?p=8&a=qbmx&type=1">消费</a>
                </li>
                <li>
                  <a href="/index.php?p=8&a=qbmx&type=3">提现</a>
                </li>
                <li>
                  <a href="/index.php?p=8&a=qbmx&type=2">充值</a>
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
<style>
.yuejilu_shaixuan_2_up{
  position: relative;
  height: 200px;
}
#startDate{
  width: 80px;
  height: 2.5rem;
  border: none;
  background: 0 0;
  outline: 0;
  line-height: 2.5rem;
  font-size: .7rem;
  color: #cf2950 ;
  float: left;
  margin:0;
  padding: 0 5px 0 15px;
  border-bottom: #cf2950 .05rem solid;
  position: absolute;
  left:1.2rem;
}
#endDate{
  width: 80px;
  height: 2.5rem;
  border: none;
  background: 0 0;
  outline: 0;
  line-height: 2.5rem;
  font-size: .7rem;
  color: #cf2950 ;
  float: left;
  margin:0;
  padding: 0 5px 0 15px;
  border-bottom: #cf2950 .05rem solid;
  position: absolute;
  right: 1.2rem;
}
.yuejilu_shaixuan_2_up .yuejilu_shaixuan_2_up_01{
  width: 100px;
  border: 0;
  text-align: center;
  margin-left: 20px;
}
.yuejilu_shaixuan_2_up #z{
  position: absolute;
  left: 48%;
  width: 2%;
  padding: 0;
  border:0;
  height: 2.5rem;
  line-height: 2.5rem;
}
.yuejilu_shaixuan_2_up span{
  padding: 0;
  float: left;
  height: auto;
  border: 0;
}
.dw-trans{
  position: absolute;
  top: 2.5rem;
  left: 1.2rem;
}
</style>
      <div class="yuejilu_shaixuan_2">
          <div class="yuejilu_shaixuan_2_up">
              <span class="yuejilu_shaixuan_2_up_01" id="startSpan">
                  <input value="<?=$sd?>" class="" readonly="readonly" name="startDate" id="startDate" type="text" placeholder="开始时间">
                </span>
                <span id="z">至</span>
                <span id="endSpan">
                  <input value="<?=$ed?>" class="" readonly="readonly" name="endDate" id="endDate" type="text" placeholder="结束时间">
                </span>
            </div>
          <div class="yuejilu_shaixuan_2_down">
              <!-- <img src="/skins/default/images/yuejilu_1.gif" alt=""/> -->
            </div>
        </div><div class="clearBoth"></div>
      <div class="yuejilu_shaixuan_3">
          <a href="javascript:;" id="qd">确定</a>
        </div>
        <div class="clearBoth"></div>
    </div>
    <div class="clearBoth"></div>
</div>
<script type="text/javascript">
  $(function(){
    $(".yuejilu_up_right img").click(function(){
      $(".yuejilu_shaixuan_tc").toggle();
    });
  });
  $("#qd").click(function(){

    location.href="/index.php?p=8&a=qbmx&type=<?=$type?>&remark=<?=$remark?>&sd="+$("#startDate").val()+"&ed="+$("#endDate").val();
  });

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
            url: "/index.php?p=8&a=get_yejl_list&pageNum=10&page="+page,
            data: "type="+"<?=$type?>"+"&remark="+"<?=$remark?>"+"&sd="+"<?=$sd?>"+"&ed="+"<?=$ed?>",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                
                str = '<li>' + 
                  '<div class="yuejilu_down_left">' + 
                      '<h2>' + item.remark + '</h2>' + item.dtTime +
                    '</div>' +
                  '<div class="yuejilu_down_right">' + 
                      item.money +
                    '</div><div class="clearBoth"></div><div style="font-size:.55rem;">'+item.orderInfo+'</div>' +
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

<script src="/skins/resource/scripts/calendar/js/mobiscroll_002.js" type="text/javascript"></script>
<script src="/skins/resource/scripts/calendar/js/mobiscroll_004.js" type="text/javascript"></script>
<link href="/skins/resource/scripts/calendar/css/mobiscroll_002.css" rel="stylesheet" type="text/css">
<link href="/skins/resource/scripts/calendar/css/mobiscroll.css" rel="stylesheet" type="text/css">
<script src="/skins/resource/scripts/calendar/js/mobiscroll.js" type="text/javascript"></script>
<script src="/skins/resource/scripts/calendar/js/mobiscroll_003.js" type="text/javascript"></script>
<script src="/skins/resource/scripts/calendar/js/mobiscroll_005.js" type="text/javascript"></script>
<link href="/skins/resource/scripts/calendar/css/mobiscroll_003.css" rel="stylesheet" type="text/css">


<script type="text/javascript">
    $(function () {
        var currYear = (new Date()).getFullYear();  
        var opt={};
        opt.date = {preset : 'date'};
        opt.datetime = {preset : 'datetime'};
        opt.time = {preset : 'time'};
        opt.default = {
          theme: 'android-ics light', //皮肤样式
              display: 'inline', //显示方式 inline默认显示显示
              mode: 'scroller', //日期选择模式
          dateFormat: 'yy-mm-dd',
          lang: 'zh',
          showNow: true,
          nowText: "今天",
              startYear: currYear - 2, //开始年份
              endYear: currYear,  //结束年份
              row: 3
        };

        $("#startDate").mobiscroll($.extend(opt['date'], opt['default']));
        $("#endDate").mobiscroll($.extend(opt['date'], opt['default']));        
        $("#startSpan .dw-trans").css("z-index",'2');
        $("#endSpan .dw-trans").css("z-index",'1');

        $("#startDate").click(function(){
          var optDate = $.extend(opt['date'], opt['default']);
          var date = $("#startDate").val();
          $("#startDate").val('').scroller('destroy');
          $("#startDate").val(date).scroller($.extend(optDate, opt['default']));
          $("#endSpan .dw-trans").css("z-index",'1');
          $("#startSpan .dw-trans").css("z-index",'2');
        });

        $("#endDate").click(function(){
          var optDate = $.extend(opt['date'], opt['default']);
          var date = $("#endDate").val();
          $("#endDate").val('').scroller('destroy');
          $("#endDate").val(date).scroller($.extend(optDate, opt['default']));
          $("#startSpan .dw-trans").css("z-index",'1');
          $("#endSpan .dw-trans").css("z-index",'2');
        });

    });
</script>
