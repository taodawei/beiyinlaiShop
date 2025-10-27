<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
    "money"=>array("title"=>"金额(元)","rowCode"=>"{field:'money',title:'金额(元)',width:150}"),
    "shouxufei"=>array("title"=>"手续费(元)","rowCode"=>"{field:'shouxufei',title:'手续费(元)',width:150}"),
    "shiji_money"=>array("title"=>"实际收入金额(元)","rowCode"=>"{field:'shiji_money',title:'实际收入金额(元)',width:150}"),
    "dtTime"=>array("title"=>"时间","rowCode"=>"{field:'dtTime',title:'时间',width:150}"),
    "orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:250}"),
    "typeInfo"=>array("title"=>"类型","rowCode"=>"{field:'typeInfo',title:'类型',width:120}"),
    "income_type"=>array("title"=>"收入账户","rowCode"=>"{field:'income_type',title:'收入账户',width:120}"),
    "statusInfo"=>array("title"=>"当前状态","rowCode"=>"{field:'statusInfo',title:'当前状态',width:120}"),
    "remark"=>array("title"=>"备注","rowCode"=>"{field:'remark',title:'备注',width:400}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
$rowsJS = substr($rowsJS,1);
$id = (int)$request['id'];
$yzFenbiao = getFenbiao(10,20);
$mendian = $db->get_row("select money,pay_info from demo_shops where comId=$comId");
$tixian_money = $db->get_var("select sum(money) as money from demo_mendian_liushui$yzFenbiao where mendianId=$id and (typeInfo='提现' or typeInfo='提现作废')");
if(empty($tixian_money))$tixian_money=0;
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
if(!empty($mendian->pay_info)){
    $pay_info = json_decode($mendian->pay_info);
    /*include '../yop-api/conf.php';
    require_once ("../yop-api/lib/YopRsaClient1.php");
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $pay_info->merchantNo);
    $response = YopRsaClient::post("/rest/v1.0/sys/merchant/balancequery", $request);
    $data=object_array($response);
    $yibao_yue = number_format($data['result']['merBalance'],2);*/
}
$daijiesuan = $db->get_var("select sum(money) from demo_yibao_fenzhang where comId=$id and status=1");
if(empty($daijiesuan))$daijiesuan = 0;
$yibao_daijiesuan = $db->get_var("select sum(money) from demo_yibao_fenzhang where comId=$id and income_type=1 and status=1");
if(empty($yibao_daijiesuan))$yibao_daijiesuan = 0;
$zong_yibao = 0;$zong_shop = 0;
$yues = $db->get_results("select sum(money) as money,income_type from demo_yibao_fenzhang where comId=$id and status>0 group by income_type");
if(!empty($yues)){
    foreach ($yues as $y) {
        if($y->income_type==1){
            $zong_yibao = $y->money;
        }else if($y->income_type==2){
            $zong_shop = $y->money;
        }
    }
}
if(empty($zong_yibao))$zong_yibao=0;
if(empty($zong_shop))$zong_shop=0;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript" src="js/clipboard.min.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="orderInfo"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
        .hyxx_yuemingxi_up ul li{width:19%}
    </style>
</head>
<body>
    <div class="back">
        <div><a><img src="images/back.gif" /></a></div>
        <div><?=$db->get_var("select com_title from demo_shezhi where comId=$comId")?></div>
    </div>
    <div class="cont_switch">
        <ul>
            <li>
                <a href="?s=mendian&a=add_mendian&id=<?=$id?>"><img src="images/switch_1.gif" /></a>
            </li>
            <li>
                <a href="javascript:"><img src="images/switch_4_pre.gif" /></a>
            </li>
        </ul>
    </div>
    <div class="mendianguanli"> 
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_yuemingxi_up">
                        <ul>
                            <li style="background-color:#77a9da;" onclick="qiehuan_down(0,0,0);" onmouseenter="tips(this,'点击查看订单流水记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left" style="float:none;width:100%;padding-top: 12px;">
                                    <h2 id="zong_yue">￥<?=$zong_yibao+$zong_shop?></h2>总收入
                                </div>
                                <div style="margin-left:10px;color:#fff">易宝:<span id="zong_yibao">￥<?=$zong_yibao?></span>&nbsp;&nbsp;&nbsp;&nbsp;余额:<span id="zong_shop">￥<?=$zong_shop?></span></div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#77a9da;" onclick="qiehuan_down(0,2,2);" onmouseenter="tips(this,'点击查看易宝订单流水记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left" style="float:none;width:100%;padding-top: 12px;">
                                    <h2 id="yibao_yue"></h2>易宝账户余额
                                </div>
                                <div style="margin-left:10px;color:#fff">总解冻金额:<span>￥<?=$zong_yibao-$yibao_daijiesuan?></span></div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#7acad1;" onclick="qiehuan_down(0,0,1);" onmouseenter="tips(this,'点击查看冻结资金记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left" style="width:100%">
                                    <h2>¥<?=$daijiesuan?></h2>冻结资金（客户确认收货后到账）
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#efbd3a;" onclick="qiehuan_down(1,0,0);" onmouseenter="tips(this,'点击查看所有店铺流水记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left"  style="float:none;width:100%;padding-top: 12px;">
                                    <h2>¥<?=$mendian->money?></h2>店铺余额&nbsp;<a href="javascript:" style="display:inline-block;width:44px;height: 26px;background-color: rgba(255,255,255,.2);text-align: center;line-height: 26px;font-size: 13px;color: #fff;" onclick="$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});">提现</a>
                                </div>
                                <div style="margin-left:10px;color:#fff">已提现:<span id="zong_yitixian">￥<?=$tixian_money?></span></div>
                                <div class="clearBoth"></div>
                            </li>
                            <div class="clearBoth"></div>
                        </ul>
                    </div>
                    <div class="hyxx_yuemingxi_down">
                        <div class="hyxx_yuemingxi">
                            <div class="hyxx_yxmx_yuemingxi">
                                <div class="hyxx_yxmx_yuemingxi_up">
                                    <div class="hyxx_yxmx_yuemingxi_up_left">
                                        流水记录
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
                                <div class="hyxx_yxmx_yuemingxi_down" id="down_0">
                                    <table id="product_list1" lay-filter="product_list1"></table>
                                </div>
                                <div class="hyxx_yxmx_yuemingxi_down" id="down_1" style="display:none;">
                                    <table id="product_list2" lay-filter="product_list2"></table>
                                </div>
                            </div>
                            <div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;left:35px;right:35px">
                                <div class="dqddxiangqing_up" id="orderInfoMenu">
                                    <ul>
                                        <li>
                                            <a href="javascript:" id="orderInfoMenu1" onclick="qiehuan('orderInfo',1,'dqddxiangqing_up_on');" class="dqddxiangqing_up_on">基本信息</a>
                                        </li>
                                        <li>
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
                                        </li>
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
            </div>
        </div>
     </div>
     <div class="splist_up_01_right_2_down">
    <div class="splist_up_01_right_2_down1">
        <div class="splist_up_01_right_2_down1_01">
            申请提现
        </div>
        <div class="splist_up_01_right_2_down1_02" style="margin-top:20px;">
            <form id="searchForm" class="layui-form">
                <ul>
                    <li style="width:100%">
                        <div class="gaojisousuo_left">
                            当前余额：
                        </div>
                        <div class="gaojisousuo_right">
                            <?=$mendian->money?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li style="width:100%">
                        <div class="gaojisousuo_left">
                            提现金额：
                        </div>
                        <div class="gaojisousuo_right">
                            <input type="number" class="gaojisousuo_right_input" style="width:150px;" step="1" min="200" id="add_tixian_money" placeholder="最小提现金额200">
                            <br><span>PS:如果收款信息更改请从基本信息中修改 <a href="?s=mendian&a=add_mendian&id=<?=$_SESSION[TB_PREFIX.'mendianId']?>">前去修改</a></span>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="gaojisousuo_tijiao">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search"> 确 定 </button>
                            <button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
                        </div>
                    </li>
                </ul>
            </form>
        </div>                                    
    </div>
</div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="type" value="<?=$type?>">
    <input type="hidden" id="status" value="<?=$status?>">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <script type="text/javascript">
        var productListTalbe1;
        var productListTalbe2;
        var productListForm;
        var type = 0;
        var merchantNo = '<?=$pay_info->merchantNo?>';
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
          productListTalbe1 = table.render({
            elem: '#product_list1'
            ,height: "full-340"
            ,url: '?s=mendian&a=get_liushui_jilu1&id=<?=$id?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          productListTalbe2 = table.render({
            elem: '#product_list2'
            ,height: "full-340"
            ,url: '?s=mendian&a=get_liushui_jilu&id=<?=$id?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          form.on('select(kaihu)', function(data){
            $("#kaihu").val(data.value);
            return false;
          }); 
          form.on('submit(search)', function(){
            var money = parseFloat($("#add_tixian_money").val());
            if(isNaN(money)||money<=0){
                layer.msg("请认真填写信息",function(){});
                return false;
            }else if(money<200){
                layer.msg("提现金额必须大于200",function(){});
                return false;
            }else if(money>20000){
                layer.msg("提现金额必须小于于20000",function(){});
                return false;
            }
            layer.load();
            $.ajax({
                type: "POST",
                url: "?s=mendian&a=add_tixian",
                data: "money="+money,
                dataType:"json",timeout : 30000,
                success: function(resdata){
                    layer.closeAll();
                    if(resdata.code==0){
                        layer.msg(resdata.message);
                    }else{
                        location.reload();
                    }
                },
                error: function() {
                    layer.closeAll();
                    layer.msg('数据请求失败', {icon: 5});
                }
            });
            return false;
        });
        form.on('submit(quxiao)', function(){
            $('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
            return false;
        });
    });
    </script>
    <script type="text/javascript" src="js/mendian/liushui1.js"></script>
    <script type="text/javascript" src="js/order/order_info.js"></script>
</body>
</html>