<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
    $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
    $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$fenbiao = getFenBiao($comId,20);
$id = (int)$request['id'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
if(empty($id))die('异常访问');
$supplier = $db->get_row("select id,title from demo_supplier where id=$id");
$orderNum = $db->get_row("select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and supplierId=$id and status>-1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
$pdtNum = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and supplierId=$id and status>-1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
$orderNum->orderNum=empty($orderNum->orderNum)?0:$orderNum->orderNum;
$orderNum->priceNum=empty($orderNum->priceNum)?0:$orderNum->priceNum;
$orderNum->priceNum = getXiaoshu($orderNum->priceNum,2);
if(empty($pdtNum))$pdtNum=0;
$pdtNum = getXiaoshu($pdtNum,$product_set->number_num);
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
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript">
        layui.use(['layer'], function(){
            <? if(empty($supplier)){?>
                layer.confirm('供应商不存在或已删除',{
                  btn: ['确定'],
              }, function(){
                location.href='?m=system&s=supplier';
            });
                <? }?>
            });
    </script>
    <style>
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
                <a href="javascript:"><img src="images/switch_2_pre.gif" /></a>
            </li>
            <li>
                <a href="?m=system&s=supplier&a=wanglais&id=<?=$id?>&url=<?=urlencode($request['url'])?>"><img src="images/switch_3.gif" /></a>
            </li>
        </ul>
    </div>
    <div class="cont">
        <div class="operate">
            <div class="splist_up_01_left_02">
                <div class="splist_up_01_left_01_up">
                    <span>按订单</span> <img src="images/biao_20.png"/>
                </div>
                <div class="splist_up_01_left_02_down">
                    <ul>
                        <li>
                            <a href="javascript:" onclick="selectType(<?=$id?>,1);">按订单</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="selectType(<?=$id?>,2);">按明细</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="selectType(<?=$id?>,3);">按商品</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="sprukulist_01">
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
            <div class="export">
                <a href="?m=system&s=supplier&a=daochuOrders1&id=<?=$id?>" onclick="daochu();" target="_blank" id="daochuA">导出</a>
            </div>
        </div>
        <div class="mun">
            <ul>
                <li style="background-color:#ff8382;">
                    <div class="b_num" id="price1">
                        <?=$orderNum->orderNum?>
                    </div>
                    <div class="mun_tt">
                        采购单总数
                    </div>
                </li>
                <li style="background-color:#52ade6;">
                    <div class="b_num" id="price2">
                        <?=$pdtNum?>
                    </div>
                    <div class="mun_tt">
                        采购商品数
                    </div>
                </li>
                <li style="background-color:#af99e8; margin-right:0px; float:right;">
                    <div class="b_num" id="price3">
                        <?=$orderNum->priceNum?>
                    </div>
                    <div class="mun_tt">
                        采购总金额
                    </div>
                </li>
            </ul>
            <div class="clearBoth"></div>
        </div>
        <div class="purchase_list2" style="width:100%;position:relative;">
            <table id="product_list" lay-filter="product_list"></table>
            <script type="text/html" id="barDemo">
                <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
                    <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
                </div>
            </script>
            <div class="yuandian_xx" id="operate_row" data-id="0">
                <ul>
                    <li>
                        <a href="javascript:detail('orders');"><img src="images/biao_30.png"> 明细</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <input type="hidden" id="supplierId" value="<?=$id?>">
    <input type="hidden" id="url" value="<?=urlencode($request['url'])?>">
    <script type="text/javascript">
        var productListTalbe;
        layui.use(['laydate', 'laypage','table','form'], function(){
          var laydate = layui.laydate
          ,laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          ,load = layer.load()
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
            rerenderPrice();
            reloadTable(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-360"
            ,url: '?m=system&s=supplier&a=getOrders1&id=<?=$id?>'
            ,page: {curr:<?=$page?>}
            ,cols: [[{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field: 'orderId', title: '采购单号', width:150},{field:'dtTime',title:'采购时间',width:150,sort:true},{field:'num',title:'采购数量',width:90},{field:'username',title:'制单人',width:100},{field:'price_type',title:'采购方式',width:100},{field:'price',title:'采购金额',width:100},{field:'price_weikuan',title:'欠款金额',width:100},{field:'status',title:'状态',width:100},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}]]
            ,where:{
                startTime:'<?=$startTime?>',
                endTime:'<?=$endTime?>'
            },done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          $("th[data-field='id']").hide();
          table.on('sort(product_list)', function(obj){
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            $("#order1").val(obj.field);
            $("#order2").val(obj.type);
            layer.load();
            table.reload('product_list', {
                initSort: obj
                ,height: "full-140"
                ,where: {
                  order1: obj.field
                  ,order2: obj.type
                  ,startTime:startTime
                  ,endTime:endTime
                },page: {
                    curr: 1
                }
              });
            $("th[data-field='id']").hide();
          });
        });
    </script>
    <script type="text/javascript" src="js/supplier_orders1.js"></script>
    <? require('views/help.html');?>
</body>
</html>