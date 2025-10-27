<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "orderId"=>array("title"=>"流水号","rowCode"=>"{field:'orderId',title:'流水号',width:200}"),
    "money"=>array("title"=>"金额(元)","rowCode"=>"{field:'money',title:'金额(元)',width:100}"),
    "yue"=>array("title"=>"账户余额","rowCode"=>"{field:'yue',title:'账户余额',width:150}"),
    "dtTime"=>array("title"=>"操作时间","rowCode"=>"{field:'dtTime',title:'操作时间',width:150}"),
    "remark"=>array("title"=>"类型","rowCode"=>"{field:'remark',title:'类型',width:100}"),
    "orderInfo"=>array("title"=>"备注","rowCode"=>"{field:'orderInfo',title:'备注',width:400}")
);
$allRows1 = array(
    "orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
    "shequ"=>array("title"=>"所属社区","rowCode"=>"{field:'shequ',title:'所属社区',width:240}"),
    "type"=>array("title"=>"订单类型","rowCode"=>"{field:'type',title:'订单类型',width:100}"),
    "dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
    "price"=>array("title"=>"订单总额","rowCode"=>"{field:'price',title:'订单总额',width:100,sort:true}"),
    "status_info"=>array("title"=>"订单状态","rowCode"=>"{field:'status_info',title:'订单状态',width:120}"),
    "payStatus"=>array("title"=>"付款状态","rowCode"=>"{field:'payStatus',title:'付款状态',width:90}"),
    "fapiao"=>array("title"=>"是否开票","rowCode"=>"{field:'fapiao',title:'是否开票',width:90}"),
    "kaipiao_status"=>array("title"=>"开票状态","rowCode"=>"{field:'kaipiao_status',title:'开票状态',width:90}"),
    "fahuoStatus"=>array("title"=>"发货状态","rowCode"=>"{field:'fahuoStatus',title:'发货状态',width:150}"),
    "username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}"),
    "address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
    "shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}"),
    "beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$rowsJS1 = "";
foreach ($allRows1 as $row=>$isshow){
    $rowsJS1.=','.$isshow['rowCode'];
}
$rowsJS1 .=",{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$rowsJS1 = substr($rowsJS1,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$shequ = $db->get_row("select * from demo_shequ where id=$id");
$user = $db->get_row("select nickname,earn,money from users where id=$shequ->userId and comId=$comId");
$order_num = (int)$db->get_var("select count(*) from order$fenbiao where shequ_id=$id");
$yiti = $db->get_var("select sum(money) from user_liushui$fenbiao where comId=$comId and userId=$shequ->userId and (remark='提现' or remark='提现作废')");
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
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="orderInfo"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 社区详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_yuemingxi_up">
                        <ul>
                            <li style="background-color:#77a9da;" onclick="select_type(0,1);" onmouseenter="tips(this,'点击查看所有收入记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥<?=$user->earn?></h2>总佣金金额<br>
                                    当前余额：<?=$user->money?>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#7acad1;" onclick="select_type(0,3);" onmouseenter="tips(this,'点击查看所有提现记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥ <?=abs($yiti)?></h2>已提佣金
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#b19ecb;" onclick="select_type(1,1);" onmouseenter="tips(this,'点击查看社区订单',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2><?=$order_num?></h2>订单数量
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <div class="clearBoth"></div>
                        </ul>
                    </div>
                    <div class="hyxx_yuemingxi_down" id="cont1">                       
                        <div class="hyxx_yuemingxi">
                            <div class="hyxx_yxmx_yuemingxi">
                                <div class="hyxx_yxmx_yuemingxi_up">
                                    <div class="hyxx_yxmx_yuemingxi_up_left">
                                        佣金明细
                                    </div>
                                    <div class="hyxx_yxmx_yuemingxi_up_right">
                                        <div class="sprukulist_01" style="top:0px;">
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
                                        <div style="display:inline-block;position:relative;top:-12px;">
                                            金额：<input type="text" id="money_start" class="hyxx_yxmx_yuemingxi_up_right_input2"/> - <input id="money_end" type="text" class="hyxx_yxmx_yuemingxi_up_right_input2"/>
                                            <a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-7 day'))?>','<?=date("Y-m-d")?>');">最近7天</a><a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-30 day'))?>','<?=date("Y-m-d")?>');">最30天</a><a href="javascript:reloadTable(0);" class="hyxx_yxmx_yuemingxi_up_right_a">筛选</a>
                                        </div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_yxmx_yuemingxi_down">
                                    <table id="product_list" lay-filter="product_list"></table>
                                </div>                          
                            </div>
                        </div>
                    </div>
                    <div class="hyxx_yuemingxi_down" id="cont2" style="display:none">                       
                        <div class="hyxx_yuemingxi">
                            <div class="hyxx_yxmx_yuemingxi">
                                <div class="hyxx_yxmx_yuemingxi_up">
                                    <div class="hyxx_yxmx_yuemingxi_up_left">
                                        订单列表
                                    </div>
                                    <div class="hyxx_yxmx_yuemingxi_up_right">
                                        <div class="sprukulist_01" style="top:0px;">
                                            <div class="sprukulist_01_left">
                                                <span id="s_time11"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time22"><?=empty($endTime)?'选择日期':$endTime?></span>
                                            </div>
                                            <div class="sprukulist_01_right">
                                                <img src="images/biao_76.png"/>
                                            </div>
                                            <div class="clearBoth"></div>
                                            <div id="riqilan1" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99;">
                                                <div id="riqi11" style="float:left;width:272px;"></div><div id="riqi22" style="float:left;width:272px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="hyxx_yxmx_yuemingxi_down">
                                    <table id="product_list1" lay-filter="product_list1"></table>
                                </div>                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="type" value="<?=$type?>">
    <input type="hidden" id="shangji_type" value="<?=$shangji_type?>">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <script type="text/javascript">
        var productListTalbe,productListTalbe1;
        var productListForm;
        var shangji = <?=$id?>;
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
          laydate.render({
            elem: '#riqi11'
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
            elem: '#riqi22'
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
            $("#riqilan1").slideUp(200);
            reloadTable(0);
            reloadTable1(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-340"
            ,url: '?m=system&s=users&a=get_liushui_jilu&userId=<?=$shequ->userId?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          productListTalbe1 = table.render({
            elem: '#product_list1'
            ,height: "full-340"
            ,url: '?m=system&s=order&a=getList&shequ_id=<?=$id?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS1?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                $("th[data-field='id']").hide();
                $("th[data-field='status']").hide();
                $("th[data-field='zhishangId']").hide();
                layer.closeAll('loading');
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/view_shequ.js"></script>
    <? require('views/help.html');?>
</body>
</html>