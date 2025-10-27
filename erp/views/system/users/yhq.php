<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "title"=>array("title"=>"优惠券标题","rowCode"=>"{field:'title',title:'优惠券标题',width:200}"),
    "jian"=>array("title"=>"金额(元)","rowCode"=>"{field:'jian',title:'金额(元)',width:100}"),
    "dtTime"=>array("title"=>"领取时间","rowCode"=>"{field:'dtTime',title:'领取时间',width:150}"),
    "endTime"=>array("title"=>"过期时间","rowCode"=>"{field:'endTime',title:'过期时间',width:350}"),
    // "caozuo"=>array("title"=>"操作","rowCode"=>"{field:'caozuo',title:'操作',width:100}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname,money from users where id=$id and comId=$comId");
$yhqs = $db->get_results("select status,count(*) as num from user_yhq$fenbiao where comId=$comId and userId=$id group by status");

$weishiyong = 0;
$yishiyong = 0;
if(!empty($yhqs)){
    foreach ($yhqs as $y) {
        if($y->status==0){
            $weishiyong = $y->num;
        }else{
            $yishiyong = $y->num;
        }
    }
}
$guoqi = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$id  and status=0 and endTime<'".date("Y-m-d H:i:s")."'");

if(empty($guoqi))$guoqi=0;
$weishiyong -= $guoqi;
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
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <? require('views/system/users/head.php')?>
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_youhuiquan">
                        <div class="hyxx_youhuiquan_up" style="display:none;">
                            <ul>
                                <li>
                                    <a href="javascript:" onclick="select_type(0);" class="hyxx_youhuiquan_up_on">未使用 <span>( <?=$weishiyong?> )</span></a>
                                </li>
                                <li>
                                    <a href="javascript:" onclick="select_type(1);">已使用 <span>( <?=$yishiyong?> )</span></a>
                                </li>
                                <li>
                                    <a href="javascript:" onclick="select_type(2);">已过期 <span>( <?=$guoqi?> )</span></a>
                                </li>
                                <div class="clearBoth"></div>
                            </ul>
                        </div>
                        <div class="hyxx_youhuiquan_down">
                            <div class="hyxx_youhuiquan_down1">
                                <div class="hyxx_youhuiquan_down_weishiyong">
                                    <table id="product_list" lay-filter="product_list"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="type" value="<?=$type?>">
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
            ,height: "full-270"
            ,url: '?m=system&s=users&a=get_yhq_jilu&userId=<?=$id?>'
            ,page: true
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/yhq.js"></script>
    <? require('views/help.html');?>
</body>
</html>