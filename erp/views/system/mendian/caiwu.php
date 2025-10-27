<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "orderId"=>array("title"=>"流水号","rowCode"=>"{field:'orderId',title:'流水号',width:230}"),
    "money"=>array("title"=>"金额(元)","rowCode"=>"{field:'money',title:'金额(元)',width:150}"),
    "yue"=>array("title"=>"账户余额","rowCode"=>"{field:'yue',title:'账户余额',width:150}"),
    "dtTime"=>array("title"=>"操作时间","rowCode"=>"{field:'dtTime',title:'操作时间',width:150}"),
    "typeInfo"=>array("title"=>"类型","rowCode"=>"{field:'typeInfo',title:'类型',width:120}"),
    "remark"=>array("title"=>"备注","rowCode"=>"{field:'remark',title:'备注',width:400}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$id = (int)$request['id'];
$yzFenbiao = getFenbiao(10,20);
$mendian = $db->get_row("select money from demo_shops where comId=$comId");
$liushuis = $db->get_results("select type,sum(money) as money from demo_mendian_liushui$yzFenbiao where mendianId=$id group by type");
$shouyi_money = 0;
$tixian_money = 0;
$chongzhi_money = 0;
if(!empty($liushuis)){
    foreach ($liushuis as $liushui){
        switch ($liushui->type){
            case 1:
                $shouyi_money = $liushui->money;
            break;
            case 2:
                $tixian_money = $liushui->money;
            break;
            case 3:
                $chongzhi_money = $liushui->money;
            break;
        }
    }
}
$qx_tixian_money = $db->get_var("select sum(money) as money from demo_mendian_liushui$yzFenbiao where mendianId=$id and typeInfo='提现作废'");
$chongzhi_money = $db->get_var("select sum(money) as money from demo_mendian_liushui$yzFenbiao where mendianId=$id and type=3 and typeInfo!='订单退货快递费用' and typeInfo!='提现作废'");
$qx_tixian_money = empty($qx_tixian_money)?0:$qx_tixian_money;
$chongzhi_money = empty($chongzhi_money)?0:$chongzhi_money;
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
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
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
                            <li style="background-color:#77a9da;" onclick="select_type(0);" onmouseenter="tips(this,'点击查看所有流水记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥<?=$mendian->money?></h2>余额&nbsp;<a href="javascript:" style="display:inline-block;width:44px;height: 26px;background-color: rgba(255,255,255,.2);text-align: center;line-height: 26px;font-size: 13px;color: #fff;" onclick="$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});">提现</a>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#efbd3a;" onclick="select_type(4);" onmouseenter="tips(this,'点击查看所有保证金记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥0</h2>保证金
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#7acad1;" onclick="select_type(1);" onmouseenter="tips(this,'点击查看所有收益记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥ <?=$shouyi_money?></h2>总收益金额
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#b19ecb;" onclick="select_type(2);" onmouseenter="tips(this,'点击查看所有提现记录',1);" onmouseleave="hideTips();">
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥ <?=abs($tixian_money+$qx_tixian_money)?></h2>累计提现金额
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li style="background-color:#f98e88;" onclick="select_type(3);" onmouseenter="tips(this,'点击查看所有充值记录',1);" onmouseleave="hideTips();">                                
                                <div class="hyxx_yuemingxi_up_left">
                                    <h2>¥ <?=$chongzhi_money?></h2>累计充值金额
                                </div>
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
                                <div class="hyxx_yxmx_yuemingxi_down">
                                    <table id="product_list" lay-filter="product_list"></table>
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
                    <!-- <li style="width:100%">
                        <div class="gaojisousuo_left">
                            开户行：
                        </div>
                        <div class="gaojisousuo_right">
                            <select name="kaihu" lay-filter="kaihu">
                                <option value="0">请选择</option>
                                <option value="1002">工商银行</option> 
                                <option value="1005">农业银行</option> 
                                <option value="1026">中国银行</option> 
                                <option value="1003">建设银行</option> 
                                <option value="1001">招商银行</option> 
                                <option value="1066">邮储银行</option> 
                                <option value="1020">交通银行</option> 
                                <option value="1004">浦发银行</option> 
                                <option value="1006">民生银行</option> 
                                <option value="1009">兴业银行</option>                                                                   
                                <option value="1010">平安银行</option>                               
                                <option value="1021">中信银行</option> 
                                <option value="1025">华夏银行</option> 
                                <option value="1027">广发银行</option> 
                                <option value="1022">光大银行</option> 
                                <option value="1032">北京银行</option> 
                                <option value="1056">宁波银行</option>
                            </select>
                            <input type="hidden" id="kaihu" value="0">
                        </div>
                        <div class="clearBoth"></div>
                    </li> -->
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
    <script type="text/javascript" src="js/mendian/liushui.js"></script>
</body>
</html>