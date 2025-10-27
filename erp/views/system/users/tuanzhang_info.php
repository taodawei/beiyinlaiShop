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
$rowsJS1 = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"},{field: 'status', title: '状态', width:0, sort: false,style:\"display:none;\"},{field: 'zhishangId', title: 'zhishangId', width:0,style:\"display:none;\"}";
$allRows1 = array(
    "nickname"=>array("title"=>"姓名","rowCode"=>"{field:'nickname',title:'姓名',width:150}"),
    "username"=>array("title"=>"手机号","rowCode"=>"{field:'username',title:'手机号',width:180}"),
    "level"=>array("title"=>"会员等级","rowCode"=>"{field:'level',title:'会员等级',width:100,sort:true}"),
    "money"=>array("title"=>"余额","rowCode"=>"{field:'money',title:'余额',width:150,sort:true}"),
    "jifen"=>array("title"=>"积分","rowCode"=>"{field:'jifen',title:'积分',width:150,sort:true}"),
    "yhq"=>array("title"=>"优惠券","rowCode"=>"{field:'yhq',title:'优惠券',width:80}"),
    "gift_card"=>array("title"=>"礼品(抵扣)卡","rowCode"=>"{field:'gift_card',title:'礼品(抵扣)卡',width:150}"),
    "lastLogin"=>array("title"=>"最后登录时间","rowCode"=>"{field:'lastLogin',title:'最后登录时间',width:180}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
foreach ($allRows1 as $row=>$isshow){
    $rowsJS1.=','.$isshow['rowCode'];
}
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname,earn,money from users where id=$id and comId=$comId");
$xiaji_num = $db->get_var("select count(*) from users where shangji=$id");
$team_num = $db->get_var("select count(*) from users where tuan_id=$id");
$yiti = $db->get_var("select sum(money) from user_liushui$fenbiao where comId=$comId and userId=$id and (remark='提现' or remark='提现作废')");
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
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 团长详情
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
                            <li style="background-color:#b19ecb;" onclick="select_type(1,1);" onmouseenter="tips(this,'点击查看下级会员',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2><?=$xiaji_num?></h2>下级数量
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#f98e88;" onclick="select_type(1,2);" onmouseenter="tips(this,'点击查看所有团队人员',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2><?=$team_num?></h2>团队人数
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
                                        会员列表
                                    </div>
                                    <div class="hyxx_yxmx_yuemingxi_up_right">
                                        <div style="display:inline-block;position:relative;top:-12px;">
                                            <input type="text" id="keyword" style="width:300px" placeholder="会员名称/手机号" class="hyxx_yxmx_yuemingxi_up_right_input2"/><a href="javascript:reloadTable1(0);" class="hyxx_yxmx_yuemingxi_up_right_a">筛选</a>
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
          $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
            reloadTable(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-340"
            ,url: '?m=system&s=users&a=get_liushui_jilu&userId=<?=$id?>'
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
            ,url: '?m=system&s=users&a=getList'
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
    <script type="text/javascript" src="js/users/tuanzhang_info.js"></script>
    <? require('views/help.html');?>
</body>
</html>