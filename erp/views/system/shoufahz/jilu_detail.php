<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$inventoryId = (int)$request['inventoryId'];
$storeId = (int)$request['storeId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$pdtInfo = $db->get_row("select title,sn,key_vals from demo_product_inventory where id=$inventoryId");
$storeName = $db->get_var("select title from demo_kucun_store where id=".$storeId);
$page = empty($request['page'])?1:(int)$request['page'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$supplier->title?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
    </style>
</head>
<body>
    <div class="back" style="cursor:pointer;" onclick="location.href='<?=urldecode($request['url'])?>'">
        <div><img src="images/biao_63.png"></div>
        <div>商品收发汇总-明细</div>
    </div>
    <div class="cont">
        <div class="shoufahuizongxx">
            <div class="shoufahuizongxx_01">
                <div class="shoufahuizongxx_01_1">
                    <a href="?m=system&s=chengben">成本调整</a>
                </div>
                <div class="shoufahuizongxx_01_2">
                    <div class="shoufahuizongxx_01_2_up">
                        成本核算规则 <img src="images/biao_83.png"/>
                    </div>
                    <div class="shoufahuizongxx_01_2_down"> 
                        1、商品收发成本采用“移动加权平均法”计算；
                        <br>2、商品初始库存成本不正确或入库成本录入不正确均可能导致成本数据有误，可使用库存盘点及成本调整功能初始化或校正库存数量及库存成本金额；
                    </div>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="shoufahuizongxx_02">
                商品编码：<?=$pdtInfo->sn?> &nbsp;&nbsp; 商品名称：<?=$pdtInfo->title?>    &nbsp;&nbsp;    规格：<?=$pdtInfo->key_vals?> &nbsp; 仓库：<?=$storeName?>  &nbsp;&nbsp;    时段：<?=$startTime?>—<?=$endTime?>
            </div>
        </div>
        <div class="shoufahuizongxx_03">
            <table id="product_list" lay-filter="product_list"></table>
        </div>
    </div>
<input type="hidden" id="nowIndex" value="">
    <script type="text/javascript">
        var productListTalbe;
        layui.use(['laydate', 'laypage','table','form'], function(){
          var laydate = layui.laydate
          ,laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-180"
            ,url: '?m=system&s=shoufahz&a=getPdtJilus'
            ,page: {curr:<?=$page?>}
            ,cols: [[{field: 'orderId',title:'单号',width:200,rowspan:2},{field:'dtTime',title:'出/入库时间',width:150,rowspan:2},{field:'typeInfo',title:'交易类型',width:250,rowspan:2},{title:'入库',width:300,colspan:3,align:'center'},{title:'出库',width:300,colspan:3,align:'center'},{title:'结余',width:300,colspan:3,align:'center'}],[{field:'num_ruku',title:'数量',width:100},{field:'price_ruku',title:'单位成本',width:100},{field:'chengben_ruku',title:'成本金额',width:100},{field:'num_chuku',title:'数量',width:100},{field:'price_chuku',title:'单位成本',width:100},{field:'chengben_chuku',title:'成本金额',width:100},{field:'num_jieyu',title:'数量',width:100},{field:'price_jieyu',title:'单位成本',width:100},{field:'chengben_jieyu',title:'成本金额',width:100}]]
            ,where:{
                inventoryId:'<?=$inventoryId?>',
                storeId:'<?=$storeId?>',
                startTime:'<?=$startTime?>',
                endTime:'<?=$endTime?>'
            },done: function(res, curr, count){
                layer.closeAll('loading');
            }
          });
        });
        $(function(){
            $('.shoufahuizongxx_01_2_up img').hover(function(){
                $('.shoufahuizongxx_01_2_down').stop().slideToggle(200);
            });
        });
    </script>
    <? require('views/help.html');?>
</body>
</html>