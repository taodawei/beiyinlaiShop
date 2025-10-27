<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
    "view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:44,fixed: 'left'}"),
    "orderId"=>array("title"=>"批次号","rowCode"=>"{field:'orderId',title:'批次号',width:230}"),
    "type"=>array("title"=>"类型","rowCode"=>"{field:'type',title:'类型',width:100}"),
    "num"=>array("title"=>"发货单数量","rowCode"=>"{field:'num',title:'发货单数量',width:100}"),
    "dtTime"=>array("title"=>"生成时间","rowCode"=>"{field:'dtTime',title:'生成时间',width:150}"),
    "is_print"=>array("title"=>"电子面单打印次数","rowCode"=>"{field:'is_print',title:'电子面单打印次数',width:130}"),
    "print"=>array("title"=>"打印","rowCode"=>"{field:'print',title:'打印',width:300}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
//0当前订单 1.未打印 2.已打印
$scene = (int)$request['scene'];
$type = (int)$request['type'];
$status = $request['status'];
$keyword = $request['keyword'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$shouhuoInfo = $request['shouhuoInfo'];
$moneystart = $request['moneystart'];
$moneyend = $request['moneyend'];
$mendian = $request['mendian'];
$payStatus = $request['payStatus'];
$pdtInfo = $request['pdtInfo'];
$kaipiao = (int)$request['kaipiao'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['orderPageNum'])?10:$_COOKIE['orderPageNum'];
//$storeId = (int)$_SESSION[TB_PREFIX.'storeId'];
//计算各类型订单的数量
$num_sql = "select count(*) as num,type from fahuo_pici$fenbiao where comId=$comId and (type=2 or need_peihuo=0) group by type";
$title = '发货批次';
$zongNum = 0;
$numArry = array();
//$num_sql.=" group by id";
$nums = $db->get_results($num_sql);
if(!empty($nums)){
    foreach ($nums as $n){
        $zongNum+=$n->num;
        $numArry[$n->type] = $n->num;
    }
}
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
    <link href="styles/dianzimiandan.css" rel="stylesheet" type="text/css">
    <link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/clipboard.min.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="beizhu"] div,td[data-field="address"],td[data-field="mendian"],td[data-field="pdt_info"]{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
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
<? require('views/system/fahuo/header.php')?>
<div id="content">
    <div class="right_up">
        <img src="images/biao_109.png"/> <?=$title.'('.$zongNum.')'?>
    </div>
    <div class="right_down" style="padding-bottom:0px;">
        <div class="splist">
            <div class="splist_up" style="height:118px;">
                <div class="splist_up_addtab">
                    <ul>
                        <li>
                            <a href="?s=fahuo&type=0&a=pici" <? if($type==0){?>class="splist_up_addtab_on"<? }?>>全部(<?=$zongNum?>)</a>
                        </li>
                        <li>
                            <a href="?s=fahuo&type=1&a=pici" <? if($type==1){?>class="splist_up_addtab_on"<? }?>>普通发货(<?=(int)$numArry[1]?>)</a>
                        </li>
                        <li>
                            <a href="?s=fahuo&type=2&a=pici" <? if($type==2){?>class="splist_up_addtab_on"<? }?>>电子面单(<?=(int)$numArry[2]?>)</a>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="splist_up_01">
                    <div class="splist_up_01_right">
                        <div class="splist_up_01_right_1">
                            <div class="splist_up_01_right_1_left">
                                <input type="text" id="keyword" value="<?=$keyword?>" placeholder="发货单号/订单号"/>
                            </div>
                            <div class="splist_up_01_right_1_right">
                                <a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
                            </div>
                            <div class="clearBoth"></div>
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
                    <div class="clearBoth"></div>
                </div>
            </div>
            <div class="splist_down1">
                <table id="product_list" lay-filter="product_list">
                </table>
            </div>
        </div>
    </div>
    <div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;">
        <div class="dqddxiangqing_up" id="orderInfoMenu">
            <ul>
                <li>
                    <a href="javascript:" id="orderInfoMenu1" onclick="qiehuan('orderInfo',1,'dqddxiangqing_up_on');" class="dqddxiangqing_up_on">发货单列表</a>
                </li>
                <li>
                    <a href="javascript:" id="orderInfoMenu2" onclick="qiehuan('orderInfo',2,'dqddxiangqing_up_on');fahuo_order_index(0);">货品详情</a>
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
        </div>
    </div>
</div>
<!--更换物流弹出-->
<div class="damx_genghuanwuliu_tc" style="display:none;">
    <div class="bj">
    </div>
    <div class="damx_genghuanwuliu">
        <div class="damx_genghuanwuliu_1">
            <div class="damx_genghuanwuliu_1_left">
                选择物流
            </div>
            <div class="damx_genghuanwuliu_1_right">
                <img src="images/miandan_13.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="damx_genghuanwuliu_2">
            <div class="damx_genghuanwuliu_2_2" style="text-align: center;">
                <select id="kuaidi_company" style="width: 75%;height: 35px">
                    <option value="0">请选择</option>
                    <option value="SF">顺丰</option>
                    <option value="EMS">EMS</option>
                </select>
            </div>
        </div>
        <div class="damx_genghuanwuliu_3">
            <a href="javascript:;" onclick="print_fahuo();">确定</a>
        </div>
    </div>
</div>
<!--更换物流弹出结束-->
<!--是否打印-->
<div class="print_tc" style="display:none;">
    <div class="bj">
    </div>
    <div class="damx_genghuanwuliu">
        <div class="damx_genghuanwuliu_1">
            <div class="damx_genghuanwuliu_1_left">
                打印详情
            </div>
            <div class="damx_genghuanwuliu_1_right">
                <img src="images/miandan_13.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="damx_genghuanwuliu_2">
            <div class="damx_genghuanwuliu_2_2" id="shuju" style="text-align: center;">
                成功：0条，失败：0条
            </div>
        </div>
        <div class="damx_genghuanwuliu_3">
            <a href="javascript:;" id="prints" target="_blank" onclick="xiaoshi();">打印</a>
        </div>
    </div>
</div>
<!--是否打印-->
<div id="myModal" class="reveal-modal"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="scene" value="<?=$scene?>">
<input type="hidden" id="status" value="<?=$status?>">
<input type="hidden" id="type" value="<?=$type?>">
<input type="hidden" id="orderId" value="<?=$orderId?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="kehuName" value="<?=$kehuName?>">
<input type="hidden" id="shouhuoInfo" value="<?=$shouhuoInfo?>">
<input type="hidden" id="pdtInfo" value="<?=$pdtInfo?>">
<input type="hidden" id="payStatus" value="<?=$payStatus?>">
<input type="hidden" id="moneystart" value="<?=$moneystart?>">
<input type="hidden" id="moneyend" value="<?=$moneyend?>">
<input type="hidden" id="kaipiao" value="<?=$kaipiao?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
    function xiaoshi(){
        $(".print_tc").hide();
        reloadTable(0);
    }
    //验证表单
    layui.use('form', function(){
      var form = layui.form;
      
      //监听提交
      form.on('submit(formDemo)', function(data){
        var content = $("#forms").serialize();
        ajaxpost=$.ajax({
            type: "POST",
            url: "?s=fahuo&a=order_fahuo",
            data: content,
            dataType : "json",
            timeout : 20000,
            success: function(data) {
                order_dianzi_info(1);
                $("#forms")[0].reset();
                $(".putongfh_fahuo_tc").hide();
                layer.msg(data.message);
            },
            error: function() {
                layer.msg('网络错误，请检查网络',{icon:5});
            }
        });
      });
    });
    //验证表单
    var productListTalbe,lay_date;
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
        ,url: '?s=fahuo&a=getPiciLists'
        ,page: {curr:<?=$page?>}
        ,limit:<?=$limit?>
        ,cols: [[<?=$rowsJS?>]]
        ,where:{
            scene:'<?=$scene?>',
            status:'<?=$status?>',
            type:'<?=$type?>',
            orderId:'<?=$orderId?>',
            startTime:'<?=$startTime?>',
            keyword:'<?=$keyword?>',
            mendian:'<?=$mendian?>',
            endTime:'<?=$endTime?>',
            kehuName:'<?=$kehuName?>',
            shouhuoInfo:'<?=$shouhuoInfo?>',
            moneystart:'<?=$moneystart?>',
            moneyend:'<?=$moneyend?>',
            payStatus:'<?=$payStatus?>',
            pdtInfo:'<?=$pdtInfo?>',
            kaipiao:'<?=$kaipiao?>'
        },done: function(res, curr, count){
            layer.closeAll('loading');
            $("#page").val(curr);
            $("th[data-field='id']").hide();
            $("th[data-field='status']").hide();
          }
      });
      table.on('sort(product_list)', function(obj){
        var scene = $("#scene").val();
        var status = $("#status").val();
        var type = $("#type").val();
        var orderId = $("#orderId").val();
        var startTime = $("#startTime").val();
        var keyword = $("#keyword").val();
        var mendian = $("#mendian").val();
        var endTime = $("#endTime").val();
        var kehuName = $("#kehuName").val();
        var moneystart = $("#moneystart").val();
        var moneyend = $("#moneyend").val();
        var shouhuoInfo = $("#shouhuoInfo").val();
        var pdtInfo = $("#pdtInfo").val();
        var payStatus = $("#payStatus").val();
        var kaipiao = $("#kaipiao").val();
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
              ,scene:scene
              ,status:status
              ,type:type
              ,orderId:orderId
              ,startTime:startTime
              ,keyword:keyword
              ,mendian:mendian
              ,endTime:endTime
              ,kehuName:kehuName
              ,moneystart:moneystart
              ,moneyend:moneyend
              ,shouhuoInfo:shouhuoInfo
              ,kaipiao:kaipiao
              ,pdtInfo:pdtInfo
              ,payStatus:payStatus
            },page: {
                curr: 1
            },done:function(){
                $(".layui-table-header").scrollLeft(scrollLeft);
                $(".layui-table-body").scrollLeft(scrollLeft);
                $("th[data-field='id']").hide();
                $("th[data-field='status']").hide();
                layer.closeAll('loading');
            }
          });
      });
      form.on('checkbox(status)', function(data){
        if(data.elem.checked){
            $("input[pid='status']").prop("checked",false);
        }
        form.render('checkbox');
      });
      form.on('checkbox(nostatus)', function(data){
        $("input[name='super_status_all']").prop("checked",false);
        form.render('checkbox');
      });
      form.on('submit(search)', function(data){
        $("#orderId").val(data.field.super_orderId);
        $("#startTime").val(data.field.super_startTime);
        $("#endTime").val(data.field.super_endTime);
        $("#kehuName").val(data.field.super_kehuName);
        $("#moneystart").val(data.field.super_moneystart);
        $("#moneyend").val(data.field.super_moneyend);
        $("#shouhuoInfo").val(data.field.super_shouhuoInfo);
        $("#pdtInfo").val(data.field.super_pdtInfo);
        if(data.field.super_status_all=="on"){
            $("#status").val('');
        }else{
            var cangkustr = '';
            $("input:checkbox[name='super_status']:checked").each(function(){
                cangkustr = cangkustr+','+$(this).val();
            });
            if(cangkustr.length>0){
                cangkustr = cangkustr.substring(1);
            }
            $("#status").val(cangkustr);
        }
        $("#payStatus").val(data.field.super_payStatus);
        $("#kaipiao").val(data.field.super_kaipiao);
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
                    ids = data[i].id;
                }else{
                    ids = ids+','+data[i].id;
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
function peihuo_wancheng(id){
    layer.confirm('确定已完成配货吗？', {
      btn: ['确定','取消'],
    },function(){
        layer.load();
        ajaxpost=$.ajax({
            type: "POST",
            url: "?s=fahuo&a=peihuo_wancheng",
            data: "&ids="+id,
            dataType:"json",timeout : 8000,
            success: function(resdata){
                layer.closeAll();
                if(resdata.code==0){
                    layer.msg(resdata.message,{icon:5});
                }else{
                    layer.msg('操作成功');
                    reloadTable(1);
                }
            },
            error: function() {
                layer.closeAll();
                layer.msg('数据请求失败', {icon: 5});
            }
        });
        return true;
    });
}
function piliang_wancheng(){
    var ids = $("#selectedIds").val();
    layer.confirm('确定已完成配货吗？', {
      btn: ['确定','取消'],
    },function(){
        layer.load();
        ajaxpost=$.ajax({
            type: "POST",
            url: "?s=fahuo&a=peihuo_wancheng",
            data: "&ids="+ids,
            dataType:"json",timeout : 8000,
            success: function(resdata){
                layer.closeAll();
                if(resdata.code==0){
                    layer.msg(resdata.message,{icon:5});
                }else{
                    layer.msg('操作成功');
                    $(".splist_up_02").hide();
                    $(".splist_up_01").show();
                    reloadTable(1);
                }
            },
            error: function() {
                layer.closeAll();
                layer.msg('数据请求失败', {icon: 5});
            }
        });
        return true;
    });
}
</script>
<script type="text/javascript" src="js/fahuo/fahuo_list.js"></script>
<div id="bg" onclick="hideRowset();"></div>
</body>
</html>