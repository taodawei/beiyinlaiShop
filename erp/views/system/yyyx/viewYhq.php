<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$type = (int)$request['type'];
if($id>0){
    $yhq = $db->get_row("select title,money,man,mendianIds,useType from yhq where id=$id and comId=$comId");
}else{
    redirect(urldecode($request['returnurl']));
}
$allRows = array(
    "dtTime"=>array("title"=>"发放时间","rowCode"=>"{field:'dtTime',title:'发放时间',width:180}"),
    // "areas"=>array("title"=>"发放区域","rowCode"=>"{field:'areas',title:'发放区域',width:250}"),
    "time"=>array("title"=>"有效期","rowCode"=>"{field:'time',title:'有效期',width:220}"),
    "statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}"),
    "userInfo"=>array("title"=>"领取信息","rowCode"=>"{field:'userInfo',title:'领取人信息',width:220}"),
    "useInfo"=>array("title"=>"使用信息","rowCode"=>"{field:'useInfo',title:'使用信息',width:150}")
);
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
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
        .layui-table-main .layui-table-cell{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="shengriquan_up">
            <div class="shengriquan_up_left">
                <ul>
                    <li><a href="?s=yyyx&a=yhq&type=1" class="shengriquan_up_left_on">优惠券</a></li>
                    <!--<li><a href="?s=yyyx&a=yhq&type=2" >赠送券</a></li>-->
                    <!--<li><a href="?s=yyyx&a=yhq&type=3">生日券</a></li>-->
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="mendianguanli_down">
            <div class="zsq_fafangmingxi">
                <div class="zsq_fafangmingxi_1">
                    <div class="zsq_fafangmingxi_1_left">
                        <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"/></a> 发放明细
                    </div>
                    <!--<div class="zsq_fafangmingxi_1_right">-->
                    <!--    <a href="?s=yyyx&a=add_fafang&id=<?=$id?>&returnurl=<?=urlencode($request['returnurl'])?>">+ 新增发放</a>-->
                    <!--</div>-->
                    <div class="clearBoth"></div>
                </div>
                <div class="zsq_fafangmingxi_2">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr height="69">
                            <td bgcolor="#fff5d9" width="300" align="center" valign="middle">
                                <h2>优惠券名称</h2><?=$yhq->title?>
                            </td>
                            <td bgcolor="#fff5d9" width="300" align="center" valign="middle">
                                <h2>面额</h2> ¥<?=$yhq->money?>
                            </td>
                            <td bgcolor="#fff5d9" width="300" align="center" valign="middle">
                                <h2>使用限制</h2><?=$yhq->man>0?'满'.$yhq->man.'可用':'无限制'?>
                            </td>
                            <td bgcolor="#fff5d9" width="300" align="center" valign="middle">
                                <h2>适用门店</h2><?=empty($yhq->mendianIds)?'所有门店':$db->get_var("select group_concat(title) from mendian where id in($yhq->mendianIds)")?>
                            </td>
                            <td bgcolor="#fff5d9" width="430" align="center" valign="middle">
                                <h2>适用商品</h2><?=$yhq->useType==2?'部分商品':'全部商品'?>
                            </td>
                            
                        </tr>
                    </table>
                </div>
                <div class="zsq_fafangmingxi_3">
                    <table id="product_list" lay-filter="product_list"></table>
                </div>
            </div>
        </div>
    </div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="jiluId" value="<?=$id?>">
<input type="hidden" id="page" value="<?=$page?>">
<script type="text/javascript">
    var productListTalbe;
    layui.use(['laypage','table'], function(){
      var laypage = layui.laypage
      ,table = layui.table
      ,load = layer.load()
      productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-155"
        ,url: '?m=system&s=yyyx&a=getYhqFafang'
        ,page: true
        ,limit:10
        ,cols: [[<?=$rowsJS?>]]
        ,where:{
            jiluId:'<?=$id?>'
        },done: function(res, curr, count){
            layer.closeAll('loading');
            $("#page").val(curr);
        }
    });
  });
</script>
<script type="text/javascript" src="js/yyyx/yhq.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>