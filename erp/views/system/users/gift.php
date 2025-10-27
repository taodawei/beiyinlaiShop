<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "image"=>array("title"=>"赠品图片","rowCode"=>"{field:'image',title:'赠品图片',width:80}"),
    "sn"=>array("title"=>"赠品编号","rowCode"=>"{field:'sn',title:'赠品编号',width:100}"),
    "title"=>array("title"=>"赠品名称","rowCode"=>"{field:'title',title:'赠品名称',width:200}"),
    "key_vals"=>array("title"=>"规格","rowCode"=>"{field:'key_vals',title:'规格',width:150}"),
    "num"=>array("title"=>"数量","rowCode"=>"{field:'num',title:'数量',width:80}"),
    "orderId"=>array("title"=>"交易订单号","rowCode"=>"{field:'dtTime',title:'交易订单号',width:150}"),
    "mendian"=>array("title"=>"领取门店","rowCode"=>"{field:'mendian',title:'领取门店',width:150}"),
    "dtTime"=>array("title"=>"领取时间","rowCode"=>"{field:'dtTime',title:'领取时间',width:150}"),
    "remark"=>array("title"=>"参与活动类别","rowCode"=>"{field:'remark',title:'参与活动类别',width:200}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname from users where id=$id and comId=$comId");
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
        .layui-table-body tr{height:70px}
        .layui-table-view{margin:10px;}
        td[data-field="image"] div{height:50px;}
        td[data-field="image"] img{border:#abd3e7 1px solid;width:50px;}
        td[data-field="title"] div,td[data-field="key_vals"] div,td[data-field="remark"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
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
                                <div class="hyxx_jifenmingxi_up_left_02">
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
                                <div class="clearBoth"></div>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="hyxx_jifenmingxi_down">
                            <table id="product_list" lay-filter="product_list"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
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
            ,url: '?m=system&s=users&a=get_gift_jilu&userId=<?=$id?>'
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/gift.js"></script>
    <? require('views/help.html');?>
</body>
</html>