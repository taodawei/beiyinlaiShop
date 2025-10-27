<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$type = empty($request['type'])?1:(int)$request['type'];
$allRows = array(
    "title"=>array("title"=>"礼品卡名称","rowCode"=>"{field:'title',title:'礼品卡名称',width:250}"),
    "money"=>array("title"=>"面额（元）","rowCode"=>"{field:'money',title:'面额（元）',width:120}"),
    "price"=>array("title"=>"售价","rowCode"=>"{field:'price',title:'售价',width:100}"),
    "num"=>array("title"=>"数量","rowCode"=>"{field:'num',title:'数量',width:100}"),
    "bind_num"=>array("title"=>"绑定数量","rowCode"=>"{field:'bind_num',title:'绑定数量',width:100}"),
    "endTime"=>array("title"=>"有效期","rowCode"=>"{field:'endTime',title:'有效期',width:180}"),
    "dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:180}")
);
if($type==1){
    $allRows['daochuTime'] = array("title"=>"最近导出时间","rowCode"=>"{field:'daochuTime',title:'最近导出时间',width:180}");
}
$rowsJS = "{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"},{field: 'status', title: '状态', width:0, sort: false,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";

$status = (int)$request['status'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$keyword = $request['keyword'];
$limit = 10;
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
        .yuandian_xx{width:100px;}
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        .layui-table-main .layui-table-cell{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
    </style>
</head>
<body>
    <div class="right_up">
        <a href="?s=yyyx"><img src="images/back.gif"/></a> 礼品卡管理
    </div>
    <div class="mendianguanli">
    <div class="mendianguanli_down">
       <div class="shengriquan_down">
            <div class="shengriquan_down_1">
                <div class="shengriquan_down_1_left">
                    <ul>
                        <li><a href="?s=yyyx&a=gift_card&type=1" <? if($type==1){?>class="shengriquan_down_1_left_on"<? }?>>线下礼品卡</a></li>
                        <li><a href="?s=yyyx&a=gift_card&type=2" <? if($type==2){?>class="shengriquan_down_1_left_on"<? }?>>线上礼品卡</a></li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="shengriquan_down_1_right">
                    <a href="?s=yyyx&a=create_card&type=<?=$type?>">+  新建礼品卡</a>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="shengriquan_down_2">
                <table id="product_list" lay-filter="product_list"></table>
                <script type="text/html" id="barDemo">
                    <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
                        <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
                    </div>
                </script>
                <div class="yuandian_xx" id="operate_row" data-id="0" <? if($type==2){echo 'style="width:100px"';}?>>
                    <ul>
                        <li>
                            <a href="javascript:view();"><img src="images/biao_109.png"> 领用记录</a>
                        </li>
                        <? if($type==1){?>
                        <li>
                            <a href="?s=yyyx&a=daochu_gift_card" id="daochu" onclick="daochu();"><img src="images/biao_81.png"> 导出</a>
                        </li>
                        <? }
                        ?>
                        <li id="zuofeiBtn">
                            <a href="javascript:z_confirm('礼品卡作废后不能再销售和绑定，但不影响已绑定的礼品卡。确定要作废吗？',zuofei,'');"><img src="images/biao_28.png"> 作废</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="type" value="<?=$type?>">
<input type="hidden" id="status" value="<?=$status?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
    var productListTalbe;
    layui.use(['laydate', 'laypage','table','form'], function(){
      var laydate = layui.laydate
      ,laypage = layui.laypage
      ,table = layui.table
      ,form = layui.form
      ,load = layer.load()
      productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-155"
        ,url: '?&s=yyyx&a=getGiftCardList'
        ,page: {curr:<?=$page?>}
        ,limit:<?=$limit?>
        ,cols: [[<?=$rowsJS?>]]
        ,where:{
            type:'<?=$type?>'
        },done: function(res, curr, count){
            $("th[data-field='id']").hide();
            $("th[data-field='status']").hide();
            layer.closeAll('loading');
            $("#page").val(curr);
        }
    });
  });
</script>
<script type="text/javascript" src="js/yyyx/gift_card.js"></script>
<div id="bg" onclick="hideRowset();"></div>
<? require('views/help.html');?>
</body>
</html>