<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
$remark = trim($request['remark']);
$orderInfo = trim($request['orderInfo']);
$sd = trim($request['sd']);
$ed = trim($request['ed']);
$yzFenbiao = getYzFenbiao($userId,20);
$qbMoney = $db->get_var("select sum(money) from user_liushui$yzFenbiao where userId=$userId and remark='$remark'");
?>
<div class="wode">
  <div class="wode_1">
      我的收益
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=qianbao'">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
    </div>
  <div class="wodeshouyi">
      <div class="wodeshouyi_1">
          <ul>
            <li>
                  <a href="/index.php?p=8&a=shouyi&remark=自营收入" <?php if($remark =='自营收入'){?> class="wodeshouyi_1_on" <?php }?>>自营收入</a>
                </li>
                <li>
                  <a href="/index.php?p=8&a=shouyi&remark=团队奖励" <?php if($remark =='团队奖励'){?> class="wodeshouyi_1_on" <?php }?>>团队奖励</a>
                </li>
                <li>
                  <a href="/index.php?p=8&a=shouyi&remark=盟商奖励" <?php if($remark =='盟商奖励'){?> class="wodeshouyi_1_on" <?php }?>>盟商奖励</a>
                </li>
                <li style="display:none;">
                  <a href="/index.php?p=8&a=shouyi&remark=待收益" <?php if($remark =='待收益'){?> class="wodeshouyi_1_on" <?php }?>>待收益</a>
                </li>
                <div class="clearBoth"></div>
          </ul>
        </div>
      <div class="wodeshouyi_2">
          <div class="wodeshouyi_2_left">
              <h2>
              <? 
              if($sd || $ed){ 
                echo empty($sd)?'~':$sd;
                echo ' 至 ';
                echo empty($ed)?'~':$ed;
              }else{
                echo '全部';
              }
              ?>
              </h2>
                <?=$remark?> ¥<?=$qbMoney?>
            </div>
          <div class="wodeshouyi_2_right">
              <a href="#" class="wodeshouyi_2_right_01"><img src="/skins/default/images/wodeshouyi_1.png" alt=""/></a>
                <a href="#" class="wodeshouyi_2_right_02"><img src="/skins/default/images/wodeshouyi_11.png" alt=""/></a>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="wodeshouyi_3">
          <ul id="flow_ul">            
          </ul>
        </div>
    </div>
</div>
<!--搜索弹出-->
<div class="shouyi_sousuo_tc" style="display:none;">
  <div class="shouyi_sousuo">
      <form id="ordForm" action="/index.php?p=8&a=shouyi" method="post">
        <input type="hidden" name="remark" value="<?=$remark?>">
        <div class="shouyi_sousuo_left">
          <div class="shouyi_sousuo_left_01"> 
              <img src="/skins/default/images/sou_1.png" alt=""/>
            </div>
          <div class="shouyi_sousuo_left_02">
              <input type="text" name="orderInfo" value="<?=$orderInfo?>" placeholder="输入收益相关信息"/>
            </div>
          <div class="clearBoth"></div>
        </div>
        <div class="shouyi_sousuo_right">
          <a href="javascript:$('#ordForm').submit();">搜索</a>
        </div>
      </form>
      <div class="clearBoth"></div>
    </div>
</div>
<!--余额记录-筛选-->
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
<div class="yuejilu_shaixuan_tc" style="display:none;">
  <div class="yuejilu_shaixuan_bj">
    </div>
  <div class="yuejilu_shaixuan">
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
              
            </div>
        </div>
      <div class="yuejilu_shaixuan_3">
          <a href="#" class="yuejilu_shaixuan_3_01">取消</a><a href="#" id="qd">确定</a>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(function(){
    $(".wodeshouyi_2_right_02").click(function(){
      $(".yuejilu_shaixuan_tc").show();
    });
    $(".wodeshouyi_2_right_01").click(function(){
      $(".shouyi_sousuo_tc").show();
    });
    $(".yuejilu_shaixuan_3_01").click(function(){
      $(".yuejilu_shaixuan_tc").hide();
    });
    $("#qd").click(function(){
      location.href="/index.php?p=8&a=shouyi&remark=<?=$remark?>&sd="+$("#startDate").val()+"&ed="+$("#endDate").val();
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
            url: "/index.php?p=8&a=get_shouyi_list&pageNum=10&page="+page,
            data: "remark=<?=$remark?>&orderInfo=<?=$orderInfo?>&sd=<?=$sd?>&ed=<?=$ed?>",
            dataType:"json",timeout : 10000,
            success: function(res){
              $.each(res.data, function(index, item){
                <?php if($remark == '盟商奖励'){?>
                  str = '<li>' + 
                  '<div class="wodeshouyi_3_02">' +
                      '<h2>'+ item.from_user_nickname +'</h2>' +
                       item.dtTime +
                    '</div>' + 
                  '<div class="wodeshouyi_3_03">' + 
                      item.money + ' <img src="/skins/default/images/querendingdan_11.png" alt=""/>' + 
                    '</div>' + 
                  '<div class="clearBoth"></div>' + 
                  '</li>';
                <?php }else{?>
                  str = '<li>' + 
                  '<div class="wodeshouyi_3_01">' +
                      '<img src="' + item.from_user_image +'" alt=""/>' +
                    '</div>' + 
                  '<div class="wodeshouyi_3_02">' +
                      '<h2>'+item.from_user_nickname+' <span>('+item.from_user_level+')</span></h2>' + item.dtTime +
                    '</div>' + 
                  '<div class="wodeshouyi_3_03">' + 
                      item.money + ' <img src="/skins/default/images/querendingdan_11.png" alt=""/>' + 
                    '</div>' + 
                  '<div class="clearBoth"></div>' + 
                '</li>';
                <?php }?>
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
  });
</script>


<script src="/skins/demo/scripts/calendar/js/mobiscroll_002.js" type="text/javascript"></script>
<script src="/skins/demo/scripts/calendar/js/mobiscroll_004.js" type="text/javascript"></script>
<link href="/skins/demo/scripts/calendar/css/mobiscroll_002.css" rel="stylesheet" type="text/css">
<link href="/skins/demo/scripts/calendar/css/mobiscroll.css" rel="stylesheet" type="text/css">
<script src="/skins/demo/scripts/calendar/js/mobiscroll.js" type="text/javascript"></script>
<script src="/skins/demo/scripts/calendar/js/mobiscroll_003.js" type="text/javascript"></script>
<script src="/skins/demo/scripts/calendar/js/mobiscroll_005.js" type="text/javascript"></script>
<link href="/skins/demo/scripts/calendar/css/mobiscroll_003.css" rel="stylesheet" type="text/css">
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
