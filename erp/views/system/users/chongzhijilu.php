<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "orderId"=>array("title"=>"流水号","rowCode"=>"{field:'orderId',title:'流水号',width:250}"),
    "name"=>array("title"=>"会员名称","rowCode"=>"{field:'name',title:'会员名称',width:100}"),
    "username"=>array("title"=>"账号","rowCode"=>"{field:'username',title:'账号',width:150}"),
    "dtTime"=>array("title"=>"充值时间","rowCode"=>"{field:'dtTime',title:'操作时间',width:150}"),
    "money"=>array("title"=>"金额(元)","rowCode"=>"{field:'money',title:'金额(元)',width:100}"),
    "yue"=>array("title"=>"账户余额","rowCode"=>"{field:'yue',title:'账户余额',width:150}"),
    "remark"=>array("title"=>"类型","rowCode"=>"{field:'remark',title:'类型',width:100}"),
    "orderInfo"=>array("title"=>"备注","rowCode"=>"{field:'orderInfo',title:'备注',width:400}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
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
        td[data-field="orderInfo"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <img src="images/users_24.png"/> 充值明细
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">                
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_jifenmingxi">
                        <div class="chongzhimingxi_up">
                            <div class="chongzhimingxi_up_left">
                                <div class="chongzhimingxi_up_left_01">
                                    <div class="chongzhimingxi_up_left_01_left">
                                        <input type="text" id="keyword" placeholder="搜索会员/流水号"/>
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
                                        <select name="type" id="pay_type" lay-filter="type">
                                            <option value="">所有</option>
                                            <!--<option value="1">支付宝</option>-->
                                            <option value="2">微信</option>
                                            <option value="99">后台充值</option>
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
                            <div class="chongzhimingxi_up_right">
                                <? chekurl($arr,'<a href="?m=system&s=users&a=daochu_chongzhi&type=2" style="width:60px;" id="daochuA" onclick="daochu();">导出</a>') ?>
                                <? chekurl($arr,'<a href="?m=system&s=users&a=chongzhi" style="width:80px;">手动充值</a>') ?>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="hyxx_jifenmingxi_down">
                            <table id="product_list" lay-filter="product_list"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="type" value="2">
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
            ,url: '?m=system&s=users&a=get_liushui_jilu&userId=<?=$id?>&source=1'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,where:{
                type:2
            }
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
        //导出导入操作
        function daochu(){
            var pay_type = $("#pay_type option:selected").val();
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            var href = $("#daochuA").attr("href");
            $("#daochuA").attr("href",href+"&pay_type="+pay_type+"&startTime="+startTime+"&endTime="+endTime);
        }
    </script>
    <script type="text/javascript" src="js/users/chongzhijilu.js"></script>
    <? require('views/help.html');?>
</body>
</html>