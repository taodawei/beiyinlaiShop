<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
    "orderId"=>array("title"=>"单号","rowCode"=>"{field:'orderId',title:'单号',width:200}"),
    "price"=>array("title"=>"金额(元)","rowCode"=>"{field:'price',title:'金额(元)',width:100}"),
    "mendian"=>array("title"=>"所属商家","rowCode"=>"{field:'mendian',title:'所属商家',width:200}"),
    "dtTime"=>array("title"=>"交易时间","rowCode"=>"{field:'dtTime',title:'交易时间',width:150}"),
    "statusInfo"=>array("title"=>"订单状态","rowCode"=>"{field:'statusInfo',title:'订单状态',width:120}")
);
$rowsJS .=",{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname,zhishangId from users where id=$id and comId=$comId");
$money = $db->get_var("select sum(price_payed) from order$fenbiao where comId=$comId and ".($_SESSION['if_tongbu']==1?"zhishangId=$id":"userId=$id")." and status>-1");
if(empty($money))$money=0;
$keyword = $request['keyword'];
$money_start = $request['money_start'];
$money_end = $request['money_end'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/clipboard.min.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="orderInfo"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <? require('views/system/users/head.php')?>
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_jifenmingxi">
                        <div class="hyxx_jifenmingxi_up">
                            <div class="hyxx_jifenmingxi_up_left">
                                <div class="hyxx_jifenmingxi_up_left_01">
                                    <div class="hyxx_jifenmingxi_up_left_01_left">
                                        单号：
                                    </div>
                                    <div class="hyxx_jifenmingxi_up_left_01_right">
                                        <input type="text" id="keyword" placeholder="请输入单号" class="hyxx_xiaofeimingxi_up_left_01_right"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_jifenmingxi_up_left_01">
                                    <div class="hyxx_jifenmingxi_up_left_01_left">
                                        金额：
                                    </div>
                                    <div class="hyxx_jifenmingxi_up_left_01_right">
                                        <input type="text" id="money_start" class="hyxx_xiaofeimingxi_up_left_01_right" style="width:60px;"/> - <input type="text" id="money_end" class="hyxx_xiaofeimingxi_up_left_01_right" style="width:60px;"/>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_jifenmingxi_up_left_02" style="margin-right:0px;">
                                    <div class="hyxx_jifenmingxi_up_left_01_left">
                                        时间：
                                    </div>
                                    <div class="hyxx_jifenmingxi_up_left_02_right">
                                        <div class="sprukulist_01" style="top:0px;margin-left:0px;">
                                            <div class="sprukulist_01_left">
                                                <span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                                            </div>
                                            <div class="sprukulist_01_right">
                                                <img src="images/biao_76.png"/>
                                            </div>
                                            <div class="clearBoth"></div>
                                            <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99;">
                                                <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_jifenmingxi_up_left_03">
                                    <a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-7 day'))?>','<?=date("Y-m-d")?>');">最近7天</a><a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-30 day'))?>','<?=date("Y-m-d")?>');">最30天</a>
                                </div>
                                <div class="hyxx_jifenmingxi_up_left_04">
                                    <a href="javascript:reloadTable(0);" class="hyxx_yxmx_yuemingxi_up_right_a">筛选</a>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                            <div class="hyxx_jifenmingxi_up_right">
                                累计消费:<span>￥<?=$money?></span>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="hyxx_jifenmingxi_down">
                            <table id="product_list" lay-filter="product_list"></table>
                        </div>
                    </div>
                </div>
                <div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;left:35px;right:35px">
                    <div class="dqddxiangqing_up" id="orderInfoMenu">
                        <ul>
                            <li>
                                <a href="javascript:" id="orderInfoMenu1" onclick="qiehuan('orderInfo',1,'dqddxiangqing_up_on');" class="dqddxiangqing_up_on">基本信息</a>
                            </li>
                            <!-- <li>
                                <a href="javascript:" id="orderInfoMenu2" onclick="qiehuan('orderInfo',2,'dqddxiangqing_up_on');order_error_index(0);">异常处理</a>
                            </li>
                            <li>
                                <a href="javascript:" id="orderInfoMenu3" onclick="qiehuan('orderInfo',3,'dqddxiangqing_up_on');order_tuihuan_index(0);">退换货管理</a>
                            </li>
                            <li>
                                <a href="javascript:" id="orderInfoMenu4" onclick="qiehuan('orderInfo',4,'dqddxiangqing_up_on');order_service_index(0);">订单服务</a>
                            </li>
                            <li>
                                <a href="javascript:" id="orderInfoMenu5" onclick="qiehuan('orderInfo',5,'dqddxiangqing_up_on');order_jilu_index(0);">操作记录</a>
                            </li> -->
                            <div class="clearBoth"></div>
                        </ul>
                    </div>
                    <div class="dqddxiangqing_down">
                        <div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont1">
                            <div class="loading"><img src="images/loading.gif"></div>
                        </div>
                        <div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont2" style="display:none;">
                            <div class="loading"><img src="images/loading.gif"></div>
                        </div>
                        <div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont3" style="display:none;">
                            <div class="loading"><img src="images/loading.gif"></div>
                        </div>
                        <div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont4" style="display:none;">
                            <div class="loading"><img src="images/loading.gif"></div>
                        </div>
                        <div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont5" style="display:none;">
                            <div class="loading"><img src="images/loading.gif"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="type" value="<?=$type?>">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="url" value="<?=urlencode($request['returnurl'])?>">
    <input type="hidden" id="selectedIds" value="">
    <script type="text/javascript">
        var productListTalbe;
        var productListForm;
        layui.use(['laydate', 'laypage','table','form'], function(){
          var laydate = layui.laydate
          ,laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          ,load = layer.load()
          productListForm = form;
          laydate.render({
            elem: '#riqi1'
            ,show: true
            ,position: 'static'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($startTime)?'':",value:'$startTime'"?>
            ,btns: []
            ,done: function(value, date, endDate){
                $("#s_time1").html(value);
                $("#startTime").val(value);
            }
          });
          laydate.render({
            elem: '#riqi2'
            ,show: true
            ,position: 'static'
            <?=empty($endTime)?'':",value:'$endTime'"?>
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            ,btns: ['confirm']
            ,done: function(value, date, endDate){
                $("#s_time2").html(value);
                $("#endTime").val(value);
            }
          });
          $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
            reloadTable(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-215"
            ,url: '?m=system&s=users&a=get_order_jilu&userId=<?=$id?>'
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,where:{
                keyword:'<?=$keyword?>',
                money_start:'<?=$money_start?>',
                money_end:'<?=$money_end?>',
                startTime:'<?=$startTime?>',
                endTime:'<?=$endTime?>'
            },done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
                $("th[data-field='id']").hide();
                $("th[data-field='status']").hide();
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/order_jilu.js"></script>
    <script type="text/javascript" src="js/order/order_info.js"></script>
    <? require('views/help.html');?>
</body>
</html>