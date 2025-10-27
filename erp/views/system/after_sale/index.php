<?
global $db,$request,$adminRole,$qx_arry;

$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
    "view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:57,fixed: 'left'}"),
    "sn"=>array("title"=>"退换货编号","rowCode"=>"{field:'sn',title:'退换货编号',width:200}"),
    "order_orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
    "title"=>array("title"=>"商品信息","rowCode"=>"{field:'title',title:'商品信息',width:300}"),
    "reason"=>array("title"=>"退换货原因","rowCode"=>"{field:'reason',title:'退换货原因',width:200}"),
    "money"=>array("title"=>"退款总额","rowCode"=>"{field:'refund_amount',title:'退款总额',width:100,sort:true}"),
    "username"=>array("title"=>"申请人","rowCode"=>"{field:'nickname',title:'申请人',width:180}"),
    "dtTime"=>array("title"=>"申请时间","rowCode"=>"{field:'dtTime',title:'申请时间',width:150}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"},{field: 'tuihuanId', title: 'tuihuanId', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$type = (int)$request['type'];
$limit = empty($_COOKIE['tuihuanPageNum'])?10:$_COOKIE['tuihuanPageNum'];

$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field: 'status', title: 'status', width:0,style:\"display:none;\"},{field: 'tuihuanId', title: 'tuihuanId', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$status = empty($request['status'])?1:(int)$request['status'];
$keyword = $request['keyword'];
$reason = $request['reason'];
$sn = $request['sn'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$pdtInfo = $request['pdtInfo'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
//计算各类型订单的数量
$num_sql = "select status,count(*) as num from tuihuan where comId=$comId and type=$type group by status";
$zongNum = 0;
$numArry = array();
$nums = $db->get_results($num_sql);
if(!empty($nums)){
    foreach ($nums as $n){
        $zongNum+=$n->num;
        $numArry[$n->status] = $n->num;
    }
}
//$reasons = get_tuihuan_reasons();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="styles/tuihuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript"  src="layui/layui.all.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/clipboard.min.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="pdtInfo"] div,td[data-field="address"]{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:40px;overflow:hidden;cursor:pointer;}
        .layui-anim.layui-icon{font-size:20px;}
        .layui-form-radio{margin-top:0px;line-height:22px;margin-right:0px;}
        .layui-form-radio i{margin-right:3px;}
        .layui-form-radio span{font-size:12px;}
        .layui-form-select .layui-input{height:25px;}
        .ddxx_jibenxinxi_2_01_down_right .layui-form-select{margin-bottom:2px;}
        .layui-form-selected dl{top:25px;min-height:200px;}
    </style>
</head>
<body>
<? require('views/system/after_sale/header.php')?>
<div id="content">
    <!--    <div class="right_up">-->
    <!--        <img src="images/biao_109.png"/> --><?//=$type==1?'退款':($type==2?'退货':'换货')?><!--订单--><?//='('.$zongNum.')'?>
    <!--    </div>-->
    <div class="right_down" style="padding-bottom:0px;">
        <div class="splist">
            <div class="splist_up" style="height:118px;">
                <div class="splist_up_addtab">
                    <ul>
                        <li>
                            <a href="?s=after_sale&a=index&status=1&type=<?=$type?>" <? if($status==1){?>class="splist_up_addtab_on"<? }?>>待审核(<?=(int)$numArry[1]?>)</a>
                        </li>
                        <? if($type==2 || $type==1){?>
                            <li>
                                <a href="?s=after_sale&a=index&status=2&type=<?=$type?>" <? if($status==2){?>class="splist_up_addtab_on"<? }?>>待收货(<?=(int)$numArry[2]?>)</a>
                            </li>
                        <? }
                        if($type==1){
                            ?>
                            <li>
                                <a href="?s=after_sale&a=index&status=3&type=<?=$type?>" <? if($status==3){?>class="splist_up_addtab_on"<? }?>>待退款(<?=(int)$numArry[3]?>)</a>
                            </li>
                            <?
                        }

                        if($type==2){?>
                            <li>
                                <a href="?s=after_sale&a=index&status=5&type=<?=$type?>" <? if($status==5){?>class="splist_up_addtab_on"<? }?>>换货发货(<?=(int)$numArry[4]?>)</a>
                            </li>
                            <li>
                                <a href="?s=after_sale&a=index&status=6&type=<?=$type?>" <? if($status==6){?>class="splist_up_addtab_on"<? }?>>待客户收货(<?=(int)$numArry[5]?>)</a>
                            </li>
                        <? }?>
                        <? if($type==3){?>
                            <li>
                                <a href="?s=after_sale&a=index&status=7&type=<?=$type?>" <? if($status==7){?>class="splist_up_addtab_on"<? }?>>待发货(<?=(int)$numArry[7]?>)</a>
                            </li>
                            <li>
                                <a href="?s=after_sale&a=index&status=6&type=<?=$type?>" <? if($status==6){?>class="splist_up_addtab_on"<? }?>>待客户收货(<?=(int)$numArry[5]?>)</a>
                            </li>
                        <? }?>
                        <li>
                            <a href="?s=after_sale&a=index&status=4&type=<?=$type?>" <? if($status==4){?>class="splist_up_addtab_on"<? }?>>已完成(<?=(int)$numArry[4]?>)</a>
                        </li>
                        <li>
                            <a href="?s=after_sale&a=index&status=-1&type=<?=$type?>" <? if($status==-1){?>class="splist_up_addtab_on"<? }?>>已驳回(<?=(int)$numArry[-1]?>)</a>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="splist_up_01">
                    <div class="splist_up_01_left">
                        <div class="splist_up_01_left_02">
                            <div class="splist_up_01_left_02_up">
                                <span>退换货原因</span> <img src="images/biao_20.png"/>
                            </div>
                            <div class="splist_up_01_left_02_down">
                                <ul>
                                    <li>
                                        <a href="javascript:" onclick="selectReason('全部');">全部</a>
                                    </li>
                                    <? foreach($reasons as $re){?>
                                        <li>
                                            <a href="javascript:" onclick="selectReason('<?=$re?>');"><?=$re?></a>
                                        </li>
                                    <? }?>
                                </ul>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="splist_up_01_right">
                        <div class="splist_up_01_right_1">
                            <div class="splist_up_01_right_1_left">
                                <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入订单号/退款单号/申请人"/>
                            </div>
                            <div class="splist_up_01_right_1_right">
                                <a href="javascript:" onclick="keyword=$('#keyword').val();reloadTable(0);"><img src="images/biao_21.gif"/></a>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="splist_up_01_right_2">
                            <div class="splist_up_01_right_2_up">
                                高级搜索
                            </div>
                            <div class="splist_up_01_right_2_down">
                                <div class="splist_up_01_right_2_down1">
                                    <div class="splist_up_01_right_2_down1_01">
                                        高级搜索
                                    </div>
                                    <div class="splist_up_01_right_2_down1_02">
                                        <form id="searchForm" class="layui-form">
                                            <ul>
                                                <li>
                                                    <div class="gaojisousuo_left">
                                                        退换货编号
                                                    </div>
                                                    <div class="gaojisousuo_right">
                                                        <input type="text" name="super_sn" value="<?=$sn?>" class="gaojisousuo_right_input" placeholder="请输入退换货编号"/>
                                                    </div>
                                                    <div class="gaojisousuo_left">
                                                        申请时间
                                                    </div>
                                                    <div class="gaojisousuo_right" style="height:35px;">
                                                        <div class="sprukulist_01" style="top:0px;margin-left:0px;z-index:999;">
                                                            <div class="sprukulist_01_left">
                                                                <span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                                                            </div>
                                                            <div class="sprukulist_01_right">
                                                                <img src="images/biao_76.png"/>
                                                            </div>
                                                            <div class="clearBoth"></div>
                                                            <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;">
                                                                <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                                                            </div>
                                                            <input type="hidden" name="super_startTime" id="super_startTime" value="<?=$startTime?>">
                                                            <input type="hidden" name="super_endTime" id="super_endTime" value="<?=$endTime?>">
                                                        </div>
                                                    </div>
                                                    <div class="clearBoth"></div>
                                                </li>
                                                <li>
                                                    <div class="gaojisousuo_left">
                                                        订单号
                                                    </div>
                                                    <div class="gaojisousuo_right">
                                                        <input type="text" name="super_orderId" value="<?=$orderId?>" class="gaojisousuo_right_input" placeholder="请输入订单号"/>
                                                    </div>
                                                    <div class="gaojisousuo_left">
                                                        申请人
                                                    </div>
                                                    <div class="gaojisousuo_right">
                                                        <div class="dingdanjine">
                                                            <input type="text" name="super_kehuName" value="<?=$kehuName?>" class="gaojisousuo_right_input" placeholder="输入申请人姓名/联系方式"/>
                                                        </div>
                                                    </div>
                                                    <div class="clearBoth"></div>
                                                </li>
                                                <li>
                                                    <div class="gaojisousuo_left">
                                                        商品信息
                                                    </div>
                                                    <div class="gaojisousuo_right">
                                                        <input type="text" name="super_pdtInfo" value="<?=$pdtInfo?>" class="gaojisousuo_right_input" placeholder="输入商品名称/编码"/>
                                                    </div>
                                                    <div class="clearBoth"></div>
                                                </li>
                                                <li>
                                                    <div class="gaojisousuo_tijiao">
                                                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search" > 确 定 </button>
                                                        <button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
                                                        <button type="reset" class="layui-btn layui-btn-primary"> 重 置 </button>
                                                    </div>
                                                </li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="splist_up_02">
                    <div class="splist_up_02_1">
                        <img src="images/biao_25.png"/>
                    </div>
                    <div class="splist_up_02_2">
                        已选择 <span id="selectedNum">0</span> 项
                    </div>
                    <div class="dangqiandd_2_down_3">
                        <? if($status==1){?>
                            <a href="javascript:" onclick="pi_shenhe();"><img src="images/dangqiandingdan_1.png"> 批量审核</a>
                        <? }else if($status==2){?>
                            <a href="javascript:" onclick="pi_shenhe();"><img src="images/dangqiandingdan_1.png"> 确认收货</a>
                        <? }else if($status==3){?>
                            <a href="javascript:" onclick="pi_tuikuan();"><img src="images/dangqiandingdan_1.png"> 批量设为已退款</a>
                        <? }?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            </div>
            <div class="splist_down1">
                <table id="product_list" lay-filter="product_list">
                </table>
                <script type="text/html" id="barDemo">
                    <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
                        <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
                    </div>
                </script>
                <div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
                    <ul>
                        <li id="sheheBtn">
                            <a href="javascript:" onclick="tuihuan_shenhe(<?=$status==2?'1':''?>);"><img src="images/shangchengdd_22.png">审核通过</a>
                        </li>
                        <li id="cancelBtn">
                            <a href="javascript:" onclick="tuihuan_quxiao();" class="yuandian_tc_quxiaodd"><img src="images/shangchengdd_25.png">驳回申请</a>
                        </li>
                        <li id="tuikuanBtn">
                            <a href="javascript:" onclick="tuihuan_wancheng();" class="yuandian_tc_quxiaodd"><img src="images/shangchengdd_28.png">退款完成</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;">
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
<!--批量服务分配-->
<div class="ddfw_piliangfenpei_tc" id="ddfw_piliangfenpei_tc" data-type="0" data-id="0" style="display:none;">
    <div class="bj"></div>
    <div class="ddfw_adddingdangfuwu">
        <div class="dqpiliangshenhe_01">
            <div class="dqpiliangshenhe_01_left">
                服务分配
            </div>
            <div class="dqpiliangshenhe_01_right" onclick="$('#ddfw_piliangfenpei_tc').hide();">
                <img src="images/close_1.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="ddfw_piliangfenpei1">
            <ul>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        <span>*</span> 服务人员：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="fanwei_1" readonly="true" onclick="fanwei(1);" placeholder="选择服务人员" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        联系电话：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_phone" placeholder="请输入服务人员电话" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        预约服务时间：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_time" placeholder="请选择预约服务时间" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="dqpiliangshenhe_03">
            <a href="javascript:" onclick="service_fenpei();">立即分配</a>
        </div>
        <input type="hidden" id="editId" value="0">
        <input type="hidden" id="users" value="0">
        <input type="hidden" id="userNames" value="">
    </div>
</div>
<!--批量服务分配结束-->
<div id="myModal" class="reveal-modal"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
    var productListTalbe,lay_date;
    var type = <?=$type?>,
        status = <?=$status?>,
        keyword='<?=$keyword?>',
        reason='<?=$reason?>',
        sn='<?=$sn?>',
        orderId='<?=$orderId?>',
        startTime='<?=$startTime?>',
        endTime='<?=$endTime?>',
        kehuName='<?=$kehuName?>',
        pdtInfo='<?=$pdtInfo?>';
    layui.use(['laydate', 'laypage','table','form'], function(){
        var laydate = layui.laydate
            ,laypage = layui.laypage
            ,table = layui.table
            ,form = layui.form;
        lay_date = laydate;
        //,load = layer.load()
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
                $("#super_startTime").val(value);
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
                $("#super_endTime").val(value);
            }
        });
        laydate.render({
            elem: '#service_time'
            ,min: '<?=date("Y-m-d",strtotime("-1 days"))?>'
            ,type: 'datetime'
            ,format:'yyyy-MM-dd HH:mm'
        });
        $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
        });
        productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-200"
            ,url: '?s=after_sale&a=getSalesReturnList&type='+type
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,where:{
                status:status,
                keyword:keyword,
                reason:reason,
                sn:sn,
                orderId:orderId,
                startTime:startTime,
                endTime:endTime,
                kehuName:kehuName,
                pdtInfo:pdtInfo
            },done: function(res, curr, count){
                layer.closeAll('loading');
                $("#page").val(curr);
                $("th[data-field='id']").hide();
                $("th[data-field='status']").hide();
                $("th[data-field='tuihuanId']").hide();
            }
        });
        table.on('sort(product_list)', function(obj){
            $("#order1").val(obj.field);
            $("#order2").val(obj.type);
            var scrollLeft = $(".layui-table-body").scrollLeft();
            layer.load();
            table.reload('product_list', {
                initSort: obj
                ,height: "full-200"
                ,where: {
                    order1: obj.field
                    ,order2: obj.type
                    ,status:status,
                    keyword:keyword,
                    reason:reason,
                    sn:sn,
                    orderId:orderId,
                    startTime:startTime,
                    endTime:endTime,
                    kehuName:kehuName,
                    pdtInfo:pdtInfo
                },page: {
                    curr: 1
                },done:function(){
                    $(".layui-table-header").scrollLeft(scrollLeft);
                    $(".layui-table-body").scrollLeft(scrollLeft);
                    layer.closeAll('loading');
                }
            });
        });
        form.on('submit(search)', function(data){
            sn = data.field.super_sn;
            startTime = data.field.super_startTime;
            endTime = data.field.super_endTime;
            orderId = data.field.super_orderId;
            kehuName = data.field.super_kehuName;
            pdtInfo = data.field.super_pdtInfo;
            hideSearch();
            reloadTable(0);
            return false;
        });
        form.on('submit(quxiao)', function(){
            hideSearch();
            return false;
        });
        table.on('checkbox(product_list)', function(obj){
            var checkStatus = table.checkStatus('product_list')
                ,data = checkStatus.data;
            if(data.length>0){
                var ids = '';
                for (var i = 0; i < data.length; i++) {
                    if(i==0){
                        ids = data[i].tuihuanId;
                    }else{
                        ids = ids+','+data[i].tuihuanId;
                    }
                }
                $("#selectedIds").val(ids);
                $(".splist_up_01").hide();
                $(".splist_up_02").show().find(".splist_up_02_2 span").html(data.length);
            }else{
                $(".splist_up_02").hide();
                $(".splist_up_01").show();
            }
        });
    });
</script>
<script type="text/javascript" src="js/order/order_tuihuan.js"></script>
<script type="text/javascript" src="js/order/order_info.js"></script>
<script type="text/javascript" src="js/tuihuan.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>