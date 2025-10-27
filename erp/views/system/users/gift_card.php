<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
    "typeInfo"=>array("title"=>"礼品卡名称","rowCode"=>"{field:'typeInfo',title:'礼品卡名称',width:200}"),
    "cardId"=>array("title"=>"礼品卡号","rowCode"=>"{field:'cardId',title:'礼品卡号',width:150}"),
    "money"=>array("title"=>"面值","rowCode"=>"{field:'money',title:'面值',width:150}"),
    "yue"=>array("title"=>"余额","rowCode"=>"{field:'yue',title:'余额',width:150}"),
    "endTime"=>array("title"=>"有效期","rowCode"=>"{field:'endTime',title:'有效期',width:100}"),
    "caozuo"=>array("title"=>"操作","rowCode"=>"{field:'caozuo',title:'操作',width:100}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$id = (int)$request['id'];
$user = $db->get_row("select nickname from users where id=$id and comId=$comId");
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
                    <div class="hyxx_lipinka">
                        <div class="hyxx_lipinka_up">
                            <div class="hyxx_lipinka_up_1">
                                礼品卡绑定
                            </div>
                            <div class="hyxx_lipinka_up_2">
                                <ul>
                                    <li>
                                        <div class="hyxx_lipinka_up_2_left">
                                             请输入礼品卡卡号：
                                        </div>
                                        <div class="hyxx_lipinka_up_2_right">
                                            <input type="text" id="cardid1" /> - <input type="text" id="cardid2"  maxlength="4"/> - <input type="text" id="cardid3"  maxlength="4"/> - <input type="text" id="cardid4"  maxlength="4"/>
                                        </div>
                                        <div class="hyxx_lipinka_up_2_right">
                                            卡密不区分大小写
                                        </div>
                                        <div class="clearBoth"></div>
                                    </li>
                                    <li>
                                        <div class="hyxx_lipinka_up_2_tijiao">
                                            <a href="javascript:" onclick="bangding(<?=$id?>);">绑 定</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="hyxx_lipinka_down">
                            已绑定礼品卡
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
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <script type="text/javascript">
        var productListTalbe;
        layui.use(['laypage','table','form'], function(){
          var laypage = layui.laypage
          ,table = layui.table
          ,form = layui.form
          ,load = layer.load()
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-420"
            ,url: '?m=system&s=users&a=get_giftcard_jilu&userId=<?=$id?>'
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
    <script type="text/javascript" src="js/users/gift_card.js"></script>
    <? require('views/help.html');?>
</body>
</html>