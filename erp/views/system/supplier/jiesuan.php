<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$supplierId = $id = (int)$request['id'];
if(empty($id))die('异常访问');
$supplier = $db->get_row("select id,title from demo_supplier where id=$id and comId=$comId");
$orderId = 'JS_'.date('Ymd').'_'.rand(1000,9999);
$money_zong = $db->get_var("select sum(price_weikuan) from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=2 and status=1");
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
        td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;}
        .cont_left_input{width:350px;}
    </style>
</head>
<body>
    <div class="back">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
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
        <form action="?m=system&s=supplier&a=jiesuan&id=<?=$id?>" class="layui-form" id="jiesuanForm">
        <div class="add_account">
            <div class="account_h">
                <span class="acnt_back"><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></span>
                <span  class="acnt_tt">新增结算</span>
            </div>
            <div class="account_way" id="purchase_choice5">
                <div class="account_way1">
                    <input type="radio" name="type" value="1" lay-filter="order" title="按订单结算" checked>
                    <div class="way_tt">此方法结算为选择订单进行金额结算</div>
                    <div class="clearBoth"></div>
                </div>
                <div class="account_way2">
                    <input type="radio" name="type" lay-filter="qiankuan" value="2" title="按欠款额结算">
                    <div class="way_tt">此方法结算为直接对总欠款金额进行金额结算</div>
                    <div class="clearBoth"></div>
                </div>
            </div>
        </div>
        <div class="order_choice">
            <span>选择订单</span>
            <div class="choice_table">
                <table id="product_list" lay-filter="product_list"></table>
            </div>
            <div class="account_total">
                <div class="account_total1">
                    已选择<span style="color:#29a4e0;"> 0 </span>个订单进行结算&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#4b4b4b;font-size:15px"> 合计：</span><span style="color:#ff4242;font-size:24px"> 0元</span>
                </div>
            </div>
        </div>
        <div class="account_cont">
            <div class="account_cont_left">
                <ul>
                    <li>
                        <div class="cont_left_tt">
                            <span class="must">*</span>结算单号：
                        </div>
                        <div class="cont_left_input">
                            <input name="orderId" class="layui-input" id="orderId" lay-verify="required" value="<?=$orderId?>" type="text" placeholder="请输入结算单号"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cont_left_tt">
                            时间：
                        </div>
                        <div class="cont_left_input">
                            <input type="text" name="dtTime" id="dtTime" class="layui-input" value="<?=date("Y-m-d H:i")?>" lay-verify="required">
                        </div>
                        <div class="time"><a href="/"><img src="images/calendar.gif" /></a></div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cont_left_tt">
                            <span class="must">*</span>结算金额：
                        </div>
                        <div class="cont_left_input" style="width:200px">
                            <input name="money" id="money" readonly="true" value="<?=empty($money_zong)?0:$money_zong?>" class="layui-input disabled" lay-verify="required|number" type="text" />
                        </div>
                        <div class="qiankuan">总欠款金额<span style="color:#fe3131" id="money_zong"><?=empty($money_zong)?0:$money_zong?></span></div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cont_left_tt">
                            支付账号：
                        </div>
                        <div class="cont_left_input">
                            <input name="payAccount" maxlength="30" class="layui-input" type="text" placeholder="请输入支付账号" />
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="account_cont_right">
                <ul>
                    <li>
                        <div class="cont_left_tt">
                            经办人：
                        </div>
                        <div class="cont_left_input">
                            <input type="text" placeholder="<?=$_SESSION[TB_PREFIX.'name']?>" readonly="true" class="layui-input disabled" />
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cont_left_tt">
                            <span class="must">*</span>支付方式：
                        </div>
                        <div class="cont_left_input">
                            <select name="payType" lay-verify="required">
                                <option value="">选择支付方式</option>
                                <option value="现金">现金</option>
                                <option value="转账">转账</option>
                                <option value="支付宝">支付宝</option>
                                <option value="微信">微信</option>
                                <option value="其他">其他</option>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cont_left_tt">
                            <span class="must">*</span>单据号：
                        </div>
                        <div class="cont_left_input">
                            <input name="payOrder" maxlength="30" class="layui-input" lay-verify="required" type="text" placeholder="请输入单据号"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="purchase_affirm3">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
        </div>
        <input type="hidden" name="selectedIds" id="selectedIds" value="">
    </form>
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
          ,load = layer.load()
          laydate.render({
            elem: '#dtTime'
            ,max:'<?=date("Y-m-d H:i:s")?>'
            ,value:'<?=date("Y-m-d H:i")?>'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,url: '?m=system&s=supplier&a=getWanglai1&id=<?=$id?>&price_type=2&weikuan=1'
            ,page: false
            ,limit:90
            ,cols: [[{type:'checkbox'},{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field: 'orderId', title: '采购单号'},{field:'dtTime',title:'采购时间'},{field:'price',title:'采购金额'},{field:'price_weikuan',title:'欠款金额'}]]
            ,done: function(res, curr, count){
                layer.closeAll('loading');
                var checkStatus = table.checkStatus('product_list')
                ,data = checkStatus.data;
                if(data.length>0){
                    var ids = '';
                    var money = 0;
                    for (var i = 0; i < data.length; i++) {
                        if(i==0){
                            ids = data[i].id;
                        }else{
                            ids = ids+','+data[i].id;
                        }
                        money = money+parseFloat(data[i].price_weikuan);
                    }
                    $("#selectedIds").val(ids);
                    $(".account_total span").eq(0).html(data.length);
                    $(".account_total span").eq(2).html(money+'元');
                    $("#money").val(money);
                }else{
                    $("#selectedIds").val('');
                    $(".account_total span").eq(0).html('0');
                    $(".account_total span").eq(2).html('0元');
                    $("#money").val('0');
                }
            }
          });
          $("th[data-field='id']").hide();
          table.on('checkbox(product_list)', function(obj){
            var checkStatus = table.checkStatus('product_list')
            ,data = checkStatus.data;
            if(data.length>0){
                var ids = '';
                var money = 0;
                for (var i = 0; i < data.length; i++) {
                    if(i==0){
                        ids = data[i].id;
                    }else{
                        ids = ids+','+data[i].id;
                    }
                    money = money+parseFloat(data[i].price_weikuan);
                }
                $("#selectedIds").val(ids);
                $(".account_total span").eq(0).html(data.length);
                $(".account_total span").eq(2).html(money+'元');
                $("#money").val(money);
            }else{
                $("#selectedIds").val('');
                $(".account_total span").eq(0).html('0');
                $(".account_total span").eq(2).html('0元');
                $("#money").val('0');
            }
          });
          form.on('radio(qiankuan)',function(){
            layer.load();
            $(".order_choice").hide();
            productListTalbe.reload();
          });
          form.on('radio(order)',function(){
            $(".order_choice").show();
          });
          form.on('submit(tijiao)',function(data){
            var money = parseFloat($("#money").val());
            if(money<=0){
                layer.msg('结算金额不能小于0',function(){});
            }
            layer.load();
            $.ajax({
                type: "POST",
                url: "?m=system&s=supplier&a=addJiesuan&id=<?=$id?>",
                data: data.field,
                dataType:"json",timeout : 30000,
                success: function(resdata){
                    if(resdata.code==1){
                        location.href='<?=urldecode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>';
                    }else{
                        layer.closeAll();
                        layer.msg(resdata.message,function(){});
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.closeAll();
                    layer.msg('数据请求失败,请重试', {icon: 5});
                }
            });
            return false;
          });
        });
    </script>
    <? require('views/help.html');?>
</body>
</html>