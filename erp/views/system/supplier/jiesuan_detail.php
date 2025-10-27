<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(empty($id))die('异常访问');
$jilu = $db->get_row("select * from demo_caigou_repay where id=$id and comId=$comId");
$supplierId = (int)$request['supplierId'];
$supplier = $db->get_row("select id,title from demo_supplier where id=$supplierId and comId=$comId");
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
            <? if(empty($jilu)){?>
                layer.confirm('记录不存在！',{
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
        td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;}
        .cont_left_input{width:350px;}
    </style>
</head>
<body>
    <div class="back">
        <div><a href="<?=urldecode($request['returnurl'])?>"><img src="images/back.gif" /></a></div>
        <div><?=$supplier->title?></div>
    </div>
    <div class="cont_switch">
        <ul>
            <li>
                <a href="?m=system&s=supplier&a=detail&id=<?=$supplierId?>&url=<?=urlencode($request['url'])?>"><img src="images/switch_1.gif" /></a>
            </li>
            <li>
                <a href="?m=system&s=supplier&a=orders&id=<?=$supplierId?>&url=<?=urlencode($request['url'])?>"><img src="images/switch_2.gif" /></a>
            </li>
            <li>
                <a href="javascript:"><img src="images/switch_3_pre.gif" /></a>
            </li>
        </ul>
    </div>
    <div class="cont">
     <div class="add_account">
        <div class="account_h">
            <span class="acnt_back"><a href="<?=urldecode($request['returnurl'])?>"><img src="images/back.gif"></a></span>
            <span class="acnt_tt">结算明细</span>
        </div>
        <div class="jieshuanmx">
            <div class="jieshuanmx_1">
                <div>结算方式：<? if($jilu->type==1){?>按订单结算<? }else{?>按欠款额结算<? }?></div>
                <div>结算单号：<?=$jilu->orderId?> </div>
                <div>结算时间：<?=date("Y-m-d",strtotime($jilu->dtTime))?></div>
            </div>
            <div class="clearBoth"></div>
            <div class="relevance_list" style="margin-top:35px;<? if($jilu->type==2){?>display:none;<? }?>">
                <table id="product_list" lay-filter="product_list"></table>
            </div>
            <ul>
                <li>
                    <span class="jieshuanmx_left">结算金额：</span>
                    <span style="font-size:18px; color:#ff0000;"><?=$jilu->money?>元</span>
                </li>
                <li>
                    <span class="jieshuanmx_left">经办人： </span>
                    <span><?=$jilu->username?></span>
                </li>
                <li>
                    <span class="jieshuanmx_left">支付方式：</span>
                    <span><?=$jilu->payType?></span>
                </li>
                <li>
                    <span class="jieshuanmx_left">支付账号：</span>
                    <span><?=$jilu->payAccount?></span>
                </li>
                <li>
                    <span class="jieshuanmx_left">支付单据：</span>
                    <span><?=$jilu->payOrder?></span>
                </li>
            </ul>
        </div>
    </div>
    </div>
    <input type="hidden" id="url" value="<?=urlencode($request['url'])?>">
    <script type="text/javascript">
        var productListTalbe;
        layui.use(['laydate','laypage','table','form'], function(){
          var laydate = layui.laydate
          ,laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          productListTalbe = table.render({
            elem: '#product_list'
            ,url: '?m=system&s=supplier&a=getJiesuanOrders&id=<?=$id?>'
            ,page: false
            ,limit:90
            ,cols: [[{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field: 'orderId', title: '采购单号'},{field:'dtTime',title:'采购时间'},{field:'price',title:'采购金额'}]]
            ,done: function(res, curr, count){
                layer.closeAll('loading');
            }
          });
          $("th[data-field='id']").hide();
        });
    </script>
    <? require('views/help.html');?>
</body>
</html>