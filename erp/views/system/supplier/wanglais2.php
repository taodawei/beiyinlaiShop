<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$page = empty($request['page'])?1:(int)$request['page'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript">
        var noUrl = 1;
    </script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        body{background:#fff;}
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
    </style>
</head>
<body>
    <table id="product_list" lay-filter="product_list"></table>
    <script type="text/html" id="barDemo">
        <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
            <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
        </div>
    </script>
    <div class="yuandian_xx" id="operate_row" data-id="0">
        <ul>
            <li>
                <a href="javascript:detail('orders');"><img src="images/xingqing.png"> 订单明细</a>
            </li>
        </ul>
    </div>
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
    <input type="hidden" id="order1" value="<?=$order1?>">
    <input type="hidden" id="order2" value="<?=$order2?>">
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="selectedIds" value="">
    <input type="hidden" id="url" value="<?=urlencode($request['url'])?>">
    <script type="text/javascript">
        var productListTalbe;
        layui.use(['laypage','table'], function(){
          var laypage = layui.laypage
          ,table = layui.table
          ,load = layer.load()
          productListTalbe = table.render({
            elem: '#product_list'
            ,height: "full-20"
            ,url: '?m=system&s=supplier&a=getWanglai1&price_type=1&id=<?=$id?>'
            ,page: {curr:<?=$page?>}
            ,cols: [[{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field: 'orderId', title: '采购单号'},{field:'dtTime',title:'采购时间',sort:true},{field:'price',title:'采购金额'},{field:'username',title:'经办人'},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}]]
            ,where:{
                startTime:'<?=$startTime?>',
                endTime:'<?=$endTime?>'
            },done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          $("th[data-field='id']").hide();
          table.on('sort(product_list)', function(obj){
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            $("#order1").val(obj.field);
            $("#order2").val(obj.type);
            layer.load();
            table.reload('product_list', {
                initSort: obj
                ,height: "full-20"
                ,where: {
                  order1: obj.field
                  ,order2: obj.type
                  ,startTime:startTime
                  ,endTime:endTime
                },page: {
                    curr: 1
                }
              });
            $("th[data-field='id']").hide();
          });
        });
        $(document).ready(function(){
            //点击。。。弹窗滑过清除自动隐藏倒计时
            $("#operate_row").hover(function(){
                clearTimeout(nowIndexTime);
            },function(){
                $("#operate_row").hide();
            });
            $(".sprukulist_01").click(function(eve){
                $("#riqilan").slideToggle(200);
                stopPropagation(eve);
            });
        });
        //显示右侧点击。。。的弹窗
        function showNext(dom){
            var top = $(dom).offset().top;
            if(top+129>document.body.clientHeight){
                top=top-25;
            }
            var width = parseInt($(dom).css("width"));
            var right = (width/2)+45;
            var nowIndex = $("#nowIndex").val();
            var index = $(dom).parent().parent().parent().attr("data-index");
            $("#operate_row").css({"top":(top+18)+"px","right":right+"px"});
            if(nowIndex==index){
                $("#operate_row").stop().slideToggle(250);
            }else{
                if($("#operate_row").css("display")=='none'){
                    $("#operate_row").stop().slideDown(250);
                }
            }
            $("#nowIndex").val(index);
            return false;
        }
        //定时隐藏点击。。。出来的弹窗
        function hideNext(){
            nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
        }
        function detail(action){
            var jiluId = getPdtId();
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            var url = $("#url").val();
            var returnurl = "?m=system&s=supplier&a=wanglais&id=<?=$id?>&nowPage=2&startTime="+startTime+"&endTime="+endTime;
            returnurl = encodeURIComponent(returnurl);
            parent.location.href="?m=system&s=caigou&a=detail&id="+jiluId+"&returnurl="+returnurl+"&url="+url;
        }
        //获取当前选中的产品Id
        function getPdtId(){
            var zindex = $("#nowIndex").val();
            return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
        }
    </script>
</body>
</html>