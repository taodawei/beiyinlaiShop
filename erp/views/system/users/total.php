<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "money"=>array("title"=>"业绩/佣金","rowCode"=>"{field:'money',title:'业绩/佣金',width:150}"),
    "userInfo"=>array("title"=>"账号信息","rowCode"=>"{field:'userInfo',title:'账号信息',width:200}"),
    "order_price"=>array("title"=>"订单总额","rowCode"=>"{field:'order_price',title:'订单总额',width:100}"),

    "fromUser"=>array("title"=>"来源","rowCode"=>"{field:'fromUser',title:'来源',width:200}"),
    "remark"=>array("title"=>"类型","rowCode"=>"{field:'remark',title:'类型',width:100}"),
    "orderInfo"=>array("title"=>"简介","rowCode"=>"{field:'orderInfo',title:'简介',width:200}")
);
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
// $rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$fenbiao = getFenbiao($comId,20);

$type = (int)$request['type'] ? $request['type']: 0;
$keyword = $request['keyword'] ? $request['keyword'] : '';
$startTime = $request['startTime'] ? $request['startTime'] : '';
$endTime = $request['endTime'] ? $request['endTime'] : '';
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
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="info"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <img src="images/users_24.png"/> 业绩明细
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">                
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_jifenmingxi">
                        <div class="chongzhimingxi_up">
                            <div class="chongzhimingxi_up_left">
                                <div class="chongzhimingxi_up_left_01">
                                    <div class="chongzhimingxi_up_left_01_left">
                                        <input type="text" id="keyword" value="<?=$keyword?>" placeholder="搜索姓名/账号"/>
                                    </div>
                                    <div class="chongzhimingxi_up_left_01_right" onclick="reloadTable(0);">
                                        <a href="javascript:"><img src="images/sou_1.png"/></a>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="chongzhimingxi_up_left_02">
                                    <div class="chongzhimingxi_up_left_02_left">
                                        类型：
                                    </div>
                                    <div class="chongzhimingxi_up_left_02_right layui-form">
                                        <select name="type" id="type" lay-filter="type">
                                            <option value="0" <?=$type==0 ?'selected':''?> >所有</option>
                                            <option value="1" <?=$type==1 ?'selected':''?>>个人业绩</option>
                                            <option value="2" <?=$type==2 ?'selected':''?>>团队业绩</option>
                                            <option value="3" <?=$type==3 ?'selected':''?>>直推业绩</option>
                                            <option value="4" <?=$type==4 ?'selected':''?>>间推业绩</option>
                                        </select>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="chongzhimingxi_up_left_03">
                                    <div class="chongzhimingxi_up_left_02_left">
                                        时间：
                                    </div>
                                    <div class="chongzhimingxi_up_left_03_right">
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
                                <div class="chongzhimingxi_up_left_04">
                                    <a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-7 day'))?>','<?=date("Y-m-d")?>');">最近7天</a><a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-30 day'))?>','<?=date("Y-m-d")?>');">最30天</a>
                                </div>
                                <div class="clearBoth"></div>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="hyxx_jifenmingxi_down">
                            <table id="product_list" lay-filter="product_list"></table>
                            <script type="text/html" id="barDemo">
                                <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
                                    <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
                                </div>
                            </script>
                            <div class="yuandian_xx" id="operate_row" data-id="0">
                                <ul>
                                    <li>
                                        <a href="javascript:tongguo();"><img src="images/users_46.png"> 已打款</a>
                                    </li>
                                    <li>
                                        <a href="javascript:zuofei();"><img src="images/users_47.png"> 作废</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
          form.on('select(type)',function(){
            reloadTable(0);
          });
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-150"
            ,url: '?m=system&s=users&a=getTotalList&type=<?=$type?>&keyword=<?=$keyword?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("th[data-field='id']").hide();
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/tixian.js"></script>
    <? require('views/help.html');?>
</body>
</html>