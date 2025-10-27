<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "title"=>array("title"=>"门店名称","rowCode"=>"{field:'title',title:'门店名称',width:400}"),
    "sn"=>array("title"=>"门店编号","rowCode"=>"{field:'sn',title:'门店编号',width:100}"),
    "dtTime"=>array("title"=>"关注时间","rowCode"=>"{field:'dtTime',title:'关注时间',width:150}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$user = $db->get_row("select nickname from users where id=$id and comId=$comId");
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
$pdt_collect_num = $db->get_var("select count(*) from user_pdt_collect where userId=$id");
$shop_collect_num = $db->get_var("select count(*) from user_shop_collect where userId=$id");
$list_collect_num = $db->get_var("select count(*) from user_list_collect where userId=$id");
$pdt_history_num = $db->get_var("select count(*) from user_pdt_history where userId=$id");
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
        td[data-field="title"] div,td[data-field="key_vals"] div,td[data-field="remark"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
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
                    <div class="hyxx_guanzhudongtai">
                        <div class="hyxx_guanzhudongtai_up">
                            <ul>
                                <li>
                                    <a href="?m=system&s=users&a=guanzhu_pdt&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>">商品关注 <span>( <?=$pdt_collect_num?> )</span></a>
                                </li>
                                <li>
                                    <a href="javascript:" class="hyxx_guanzhudongtai_up_on">店铺关注 <span>( <?=$shop_collect_num?> )</span></a>
                                </li>
                                <li>
                                    <a href="?m=system&s=users&a=guanzhu_history&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>">浏览记录 <span>( <?=$pdt_history_num?> )</span></a>
                                </li>
                                <div class="clearBoth"></div>
                            </ul>
                        </div>
                        <div class="hyxx_guanzhudongtai_down">
                            <div class="hyxx_guanzhudongtai_down1">
                                <div class="hyxx_guanzhudongtai_shangpinguanzhu">
                                    <div class="hyxx_shangpinguanzhu_up">
                                        <div class="hyxx_shangpinguanzhu_up_left">
                                            <div class="hyxx_shangpinguanzhu_up_right_01">
                                                <div class="sprukulist_01" style="top:0px;margin-left:0px;">
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
                                            <div class="hyxx_shangpinguanzhu_up_right_02">
                                                <a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-7 day'))?>','<?=date("Y-m-d")?>');">最近7天</a><a href="javascript:selectTime('<?=date("Y-m-d",strtotime('-30 day'))?>','<?=date("Y-m-d")?>');">最30天</a>
                                            </div>
                                            <div class="clearBoth"></div>
                                        </div>
                                        <div class="clearBoth"></div>
                                    </div>
                                    <div class="hyxx_shangpinguanzhu_down">
                                        <table id="product_list" lay-filter="product_list"></table>
                                    </div>
                                </div>
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
            ,height: "full-265"
            ,url: '?m=system&s=users&a=get_shop_collect&userId=<?=$id?>'
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [[<?=$rowsJS?>]]
            ,done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
            }
          });
        });
    </script>
    <script type="text/javascript" src="js/users/gift.js"></script>
    <? require('views/help.html');?>
</body>
</html>