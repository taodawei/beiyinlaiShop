<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "money"=>array("title"=>"消费金额( 元 )","rowCode"=>"{field:'money',title:'消费金额( 元 )',width:100}"),
    "yue"=>array("title"=>"余额","rowCode"=>"{field:'yue',title:'余额',width:150}"),
    "dtTime"=>array("title"=>"消费时间","rowCode"=>"{field:'dtTime',title:'消费时间',width:150}"),
    "remark"=>array("title"=>"类型","rowCode"=>"{field:'remark',title:'类型',width:100}"),
    "orderInfo"=>array("title"=>"备注","rowCode"=>"{field:'orderInfo',title:'备注',width:400}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$card = $db->get_row("select * from gift_card$fenbiao where id=$id");
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
$returnurl = urlencode($request['returnurl']);
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
            <a href="<? if(!empty($request['url'])){echo urldecode($request['url'].'&returnurl='.urlencode($returnurl));}else{?>javascript:history.go(-1);<? }?>"><img src="images/users_39.png" alt=""/></a> 礼品卡消费明细
        </div>
        <div class="mendianguanli_down">
            <div class="yx_lipinkaxiaofei">
                <div class="yx_lipinkaxiaofei_up">
                    <div class="yx_lipinkaxiaofei_up_left">
                        <div class="yx_lipinkaxiaofei_up_left_02">
                            金额：<input type="number" id="money_start" placeholder=""/> - <input type="number" id="money_end" placeholder=""/>
                        </div>
                        <div class="yx_lipinkaxiaofei_up_left_03">
                            <div class="yx_lipinkaxiaofei_up_left_03_left">
                                时间：
                            </div>
                            <div class="yx_lipinkaxiaofei_up_left_03_right">
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
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="yx_lipinkaxiaofei_up_left_04">
                            <a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-7 day'))?>','<?=date("Y-m-d")?>');">最近7天</a><a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-30 day'))?>','<?=date("Y-m-d")?>');">最近30天</a>
                        </div>
                        <div class="yx_lipinkaxiaofei_up_left_05">
                            <a href="javascript:reloadTable(0)">筛选</a>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="yx_lipinkaxiaofei_up_right">
                        礼品卡金额：<span style="color:#3ab2ee;">￥<?=$card->money?></span> 余额：<span style="color:#ff0f0f;">￥<?=$card->yue?></span>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="yx_lipinkaxiaofei_down">
                    <table id="product_list" lay-filter="product_list"></table>
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
            ,height: "full-180"
            ,url: '?m=system&s=users&a=get_giftcard_liushui&id=<?=$id?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/gift_card_luishui.js"></script>
    <? require('views/help.html');?>
</body>
</html>