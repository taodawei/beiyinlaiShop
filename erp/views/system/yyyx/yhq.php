<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$type = (int)$request['type'];
if(empty($type))$type=1;
switch ($type){
    case 1:
        $allRows = array(
            "title"=>array("title"=>"优惠券名称","rowCode"=>"{field:'title',title:'优惠券名称',width:250}"),
            "jiazhi"=>array("title"=>"价值","rowCode"=>"{field:'jiazhi',title:'价值',width:150}"),
            "xianzhi"=>array("title"=>"领取限制","rowCode"=>"{field:'xianzhi',title:'领取限制',width:150}"),
            "time"=>array("title"=>"有效期","rowCode"=>"{field:'time',title:'有效期',width:180}"),
            "fanwei"=>array("title"=>"适用商品","rowCode"=>"{field:'fanwei',title:'适用商品',width:200}"),
            "dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:150}"),
            // "areas"=>array("title"=>"发放区域","rowCode"=>"{field:'areas',title:'发放区域',width:200}"),
            // "levels"=>array("title"=>"可领级别","rowCode"=>"{field:'levels',title:'可领级别',width:200}"),
            "lingqus"=>array("title"=>"领取人/次","rowCode"=>"{field:'lingqus',title:'领取人/次',width:100}"),
            "usenum"=>array("title"=>"已使用","rowCode"=>"{field:'usenum',title:'已使用',width:100}")
        );
    break;
    case 2:
        $allRows = array(
            "title"=>array("title"=>"赠送券名称","rowCode"=>"{field:'title',title:'赠送券名称',width:250}"),
            "jiazhi"=>array("title"=>"价值","rowCode"=>"{field:'jiazhi',title:'价值',width:150}"),
            "fanwei"=>array("title"=>"适用商品","rowCode"=>"{field:'fanwei',title:'适用商品',width:200}"),
            "dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:150}"),
            "lingqus"=>array("title"=>"领取次数","rowCode"=>"{field:'lingqus',title:'领取次数',width:100}"),
            "usenum"=>array("title"=>"已使用","rowCode"=>"{field:'usenum',title:'已使用',width:100}")
        );
    break;
    case 3:
        $allRows = array(
            "title"=>array("title"=>"生日券名称","rowCode"=>"{field:'title',title:'生日券名称',width:250}"),
            "jiazhi"=>array("title"=>"价值","rowCode"=>"{field:'jiazhi',title:'价值',width:150}"),
            "num_day"=>array("title"=>"使用期限","rowCode"=>"{field:'num_day',title:'使用期限',width:150}"),
            "fanwei"=>array("title"=>"适用商品","rowCode"=>"{field:'fanwei',title:'适用商品',width:200}"),
            "levels"=>array("title"=>"发放级别","rowCode"=>"{field:'levels',title:'发放级别',width:200}"),
            "lingqus"=>array("title"=>"领取次数","rowCode"=>"{field:'lingqus',title:'领取次数',width:100}"),
            "usenum"=>array("title"=>"已使用","rowCode"=>"{field:'usenum',title:'已使用',width:100}")
        );
    break;
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
$title = '优惠券';
if($type==2){
    $title = '赠送券';
}else if($type==3){
    $title = '生日券';
}
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
                    <li><a href="?s=yyyx&a=yhq&type=1" <? if($type==1){?>class="shengriquan_up_left_on"<? }?>>优惠券</a></li>
                    <!-- <li><a href="?s=yyyx&a=yhq&type=2" <? if($type==2){?>class="shengriquan_up_left_on"<? }?>>赠送券</a></li>
                    <li><a href="?s=yyyx&a=yhq&type=3" <? if($type==3){?>class="shengriquan_up_left_on"<? }?>>生日券</a></li> -->
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="shengriquan_up_right">
                <div class="shengriquan_up_right_left">
                    <input type="text" id="keyword" value="<?=$keyword?>" placeholder="搜索优惠券名称"/>
                </div>
                <div class="shengriquan_up_right_right">
                    <a href="javascript:" onclick="reloadTable(1);"><img src="images/sou_1.png"/></a>
                </div>
                <div class="clearBoth"></div>
            </div>

            <div class="clearBoth"></div>
        </div>
    <div class="mendianguanli_down">
       <div class="shengriquan_down">
            <div class="shengriquan_down_1">
                <div class="shengriquan_down_1_left">
                    <ul>
                        <li><a href="javascript:" onclick="updataStatus(0,0);" <? if($status==0){?>class="shengriquan_down_1_left_on"<? }?>>所有优惠券</a></li>
                        <? if($type==1){?>
                            <li><a href="javascript:" onclick="updataStatus(1,1);" <? if($status==1){?>class="shengriquan_down_1_left_on"<? }?>>未开始</a></li>
                            <li><a href="javascript:" onclick="updataStatus(2,2);" <? if($status==2){?>class="shengriquan_down_1_left_on"<? }?>>进行中</a></li>
                            <li><a href="javascript:" onclick="updataStatus(3,3);" <? if($status==3){?>class="shengriquan_down_1_left_on"<? }?>>已结束</a></li>
                        <? }else{?>
                            <li><a href="javascript:" onclick="updataStatus(5,1);" <? if($status==1){?>class="shengriquan_down_1_left_on"<? }?>>可用</a></li>
                        <? }?>
                        <li><a href="javascript:" onclick="updataStatus(4,<?=$type==1?4:2?>);" <? if($status==4){?>class="shengriquan_down_1_left_on"<? }?>>已失效</a></li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="shengriquan_down_1_right">
                    <? chekurl($arr,'<a href="?m=system&s=yyyx&a=add_yhq&type='.$type.'&returnurl='.urlencode('?s=yyyx&a=yhq&type='.$type).'">+  新建'.$title.'</a>') ?>
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
                <div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px">
                    <ul>
                        <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=yyyx&a=viewYhq" onclick="view()"><img src="images/users_27.png"> 发放明细</a></li>') ?>
                        <? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=yyyx&a=yhq_shixiao" onclick="z_confirm(\'确定让这组优惠券失效？失效后：<br>&nbsp;&nbsp;1.买家无法再领取该优惠券；<br>&nbsp;&nbsp;2.不能再编辑优惠内容；<br>&nbsp;&nbsp;3.买家之前已领到的优惠券还能正常使用。\',shixiao,\'\')"><img src="images/users_37.png"> 失效</a></li>') ?>
                        
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
        ,url: '?&s=yyyx&a=getYhqList'
        ,page: {curr:<?=$page?>}
        ,limit:<?=$limit?>
        ,cols: [[<?=$rowsJS?>]]
        ,where:{
            type:'<?=$type?>',
            status:'<?=$status?>',
            keyword:'<?=$keyword?>'
        },done: function(res, curr, count){
            $("th[data-field='id']").hide();
            $("th[data-field='status']").hide();
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