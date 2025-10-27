<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
    $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
    $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$fenbiao = getFenBiao($comId,20);
$id = $supplierId = (int)$request['id'];
if(empty($id))die('异常访问');
$supplier = $db->get_row("select id,title from demo_supplier where id=$id and comId=$comId");
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$nowPage = empty($request['nowPage'])?1:$request['nowPage'];
$qiankuan = $db->get_var("select sum(price_weikuan) from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=2 and status=1");
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
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript">
        <? if(empty($supplier)){?>
        layui.use(['layer'], function(){
            layer.confirm('供应商不存在或已删除',{
                btn: ['确定'],
            }, function(){
                location.href='?m=system&s=supplier';
            });
        });
        <? }?>
    </script>
    <style>
        body{background:#fff;}
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="back">
        <div><a href="<?=urldecode($request['url'])?>"><img src="images/back.gif" /></a></div>
        <div><?=$supplier->title?></div>
    </div>
    <div class="cont_switch">
        <ul>
            <li>
                <a href="?m=system&s=supplier&a=detail&id=<?=$id?>&url=<?=urlencode($request['url'])?>"><img src="images/switch_1.gif" /></a>
            </li>
            <li>
                <a href="?m=system&s=supplier&a=orders&id=<?=$id?>&url=<?=urlencode($request['url'])?>"><img src="images/switch_2.gif" /></a>
            </li>
            <li>
                <a href=""><img src="images/switch_3_pre.gif" /></a>
            </li>
        </ul>
    </div>
    <div class="cont">
        <div class="operate">
            <div class="sprukulist_01" style="margin-left:0px;">
                <div class="sprukulist_01_left">
                    <span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                </div>
                <div class="sprukulist_01_right">
                    <img src="images/biao_76.png"/>
                </div>
                <div class="clearBoth"></div>
                <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99">
                    <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                </div>
            </div>
            <div class="splist_up_01_right">
                <div class="splist_up_01_right_3">
                    <a href="?m=system&s=supplier&a=daochuWanglais&id=<?=$id?>" onclick="daochu();" target="_blank" id="daochuA" class="splist_daoru">导 出</a>
                    <a <? if($qiankuan>0){?>href="javascript:gotoJiesuan();"<? }else{?>href="javascript:layer.msg('暂无欠款采购订单',function(){});"<? }?> class="splist_add">结 算</a>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="mun1">
            <ul>
                <li style="background-color:#b583c5;" onmouseenter="tips(this,'点击查看所有采购订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price1">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        采购总金额
                    </div>
                </li>
                <li style="background-color:#efbd3a;" onmouseenter="tips(this,'点击查看所有现购采购订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price2">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        现购金额
                    </div>
                </li>
                <li style="background-color:#64bce6;" onmouseenter="tips(this,'点击查看所有赊购采购订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price3">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        预付款金额
                    </div>
                </li>
                <li style="background-color:#6dd7ab;" onmouseenter="tips(this,'点击查看所有结算订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price4">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        已结金额
                    </div>
                </li>
                <li style="background-color:#adb8c4;" onmouseenter="tips(this,'点击查看欠款的采购订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price5">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        欠款金额
                    </div>
                </li>
                <li style="background-color:#f66c6c;" onmouseenter="tips(this,'点击查看所有采购退货订单',1);" onmouseleave="hideTips();">
                    <div class="b_num1" id="price6">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        退货金额
                    </div>
                </li>
                <li style="background-color:#6bc942; margin-right:0px; float:right;">
                    <div class="b_num1" id="price7">
                        <img src="images/loading.gif" width="30">
                    </div>
                    <div class="mun_tt1">
                        实际总金额
                    </div>
                </li>
            </ul>
            <div class="clearBoth"></div>
        </div>
        <div class="purchase_list2" style="position:absolute;top:330px;bottom:0px;min-height:300px;left:23px;right:46px;background:#fff;">
            <iframe src="?m=system&s=supplier&a=wanglais<?=$nowPage?>&id=<?=$supplierId?>&startTime=<?=$startTime?>&endTime=<?=$endTime?>" border="0" frameborder="0" width="100%" height="100%" id="tableFrame"></iframe>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<input type="hidden" id="nowPage" value="<?=$nowPage?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="supplierId" value="<?=$supplierId?>">
<input type="hidden" id="url" value="<?=urlencode($request['url'])?>">
<script type="text/javascript">
    layui.use(['laydate','form'], function(){
      var laydate = layui.laydate
      ,form = layui.form
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
        getWanglais();
        reloadTable();
    });
  });
</script>
<script type="text/javascript" src="js/supplier_wanglai.js"></script>
<? require('views/help.html');?>
</body>
</html>