<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
if(is_file("../cache/product_set_$comId.php")){
    $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
    $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$fenbiao = getFenBiao($comId,20);
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$keyword = $request['keyword'];
$kehuName = $request['kehuName'];
$areaId = (int)$request['areaId'];
$paystatus = (int)$request['paystatus'];
$level = (int)$request['level'];
$kehuStatus = (int)$request['kehuStatus'];
$limit = empty($_COOKIE['m_tongjiPageNum'])?10:$_COOKIE['m_tongjiPageNum'];
$areas = $db->get_results("select * from demo_area where parentId=0");
$page = empty($request['page'])?1:(int)$request['page'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$supplier->title?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
    .layui-table-body tr{height:50px}
    .layui-table-view{margin:10px;}
    td[data-field="title"] div,td[data-field="kehuName"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
</style>
</head>
<body>
    <div class="back">
        <div><img src="images/biao_105.png" /></div>
        <div>订单收款统计</div>
    </div>
    <div class="cont">
        <div class="operate">
            <div class="splist_up_01_left_02">
                <div class="splist_up_01_left_01_up">
                    <span>全部付款状态</span> <img src="images/biao_20.png"/>
                </div>
                <div class="splist_up_01_left_02_down">
                    <ul>
                        <li>
                            <a href="javascript:" onclick="selectType('全部付款状态',0);">全部付款状态</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="selectType('未全部付款',1);">未全部付款</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="selectType('已全部付款',2);">已全部付款</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="sprukulist_01">
                <div class="sprukulist_01_left">
                    <span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                </div>
                <div class="sprukulist_01_right">
                    <img src="images/biao_76.png"/>
                </div>
                <div class="clearBoth"></div>
                <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99">
                    <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                </div>
            </div>
            <div class="splist_up_01_right">    
                <div class="splist_up_01_right_1">
                    <div class="splist_up_01_right_1_left">
                        <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/订单号"/>
                    </div>
                    <div class="splist_up_01_right_1_right">
                        <a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="splist_up_01_right_2">
                    <div class="splist_up_01_right_2_up">
                        高级搜索
                    </div>
                    <div class="splist_up_01_right_2_down">
                        <div class="splist_up_01_right_2_down1">
                            <div class="splist_up_01_right_2_down1_01">
                                高级搜索
                            </div>
                            <div class="splist_up_01_right_2_down1_02">
                                <form id="searchForm" class="layui-form">
                                    <ul>
                                        <li>
                                            <div class="gaojisousuo_left">
                                                客户名称
                                            </div>
                                            <div class="gaojisousuo_right">
                                                <input type="text" name="super_kehuName" value="<?=$kehuName?>" class="gaojisousuo_right_input" placeholder="请输入<?=$kehu_title?>名称"/>
                                            </div>
                                            <div class="gaojisousuo_left">
                                                区域
                                            </div>
                                            <div class="gaojisousuo_right" style="height:35px;">
                                                <div style="width:32%;display:inline-block;">
                                                    <select id="ps1" lay-filter="ps1">
                                                      <option value="">选择省份</option>
                                                      <?if(!empty($areas)){
                                                        foreach ($areas as $hangye) {
                                                          ?><option value="<?=$hangye->id?>" <?=($hangye->id==$firstId?'selected="selected"':'')?>><?=$hangye->title?></option>
                                                          <?
                                                      }
                                                  }?>
                                              </select>
                                          </div>
                                          <div style="width:32%;display:inline-block;">
                                            <select id="ps2" lay-filter="ps2"><option value="">请先选择省</option>

                                            </select>
                                        </div>
                                        <div style="width:32%;display:inline-block;">
                                            <select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>

                                            </select>
                                        </div>
                                        <input type="hidden" name="super_areaId" id="super_areaId">
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <li>
                                    <div class="gaojisousuo_left">
                                        <?=$kehu_title?>级别
                                    </div>
                                    <div class="gaojisousuo_right" style="width:364px;">
                                        <select name="super_level" id="super_level" lay-search>
                                            <option value="">选择级别</option>
                                            <? if(!empty($levels)){
                                                foreach ($levels as $l) {
                                                    ?>
                                                    <option value="<?=$l->id?>"><?=$l->title?></option>
                                                    <?
                                                }
                                            }?>
                                        </select>
                                    </div>
                                    <div class="gaojisousuo_left">
                                        <?=$kehu_title?>状态
                                    </div>
                                    <div class="gaojisousuo_right" style="width:364px;">
                                        <select name="kehuStatus" id="kehuStatus" lay-search>
                                            <option value="0">全部状态</option>
                                            <option value="2">未开通</option>
                                            <option value="1">已开通</option>
                                        </select>
                                    </div>
                                    <div class="clearBoth"></div>
                                </li>
                                <li>
                                    <div class="gaojisousuo_tijiao">
                                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search" > 确 定 </button>
                                        <button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
                                        <button type="reset" class="layui-btn layui-btn-primary"> 重 置 </button>
                                    </div>
                                </li>
                            </ul>
                        </form>
                    </div>                                    
                </div>
            </div>
        </div>
        <div class="splist_up_01_right_3">
            <a href="?m=system&s=money&a=daochuTongji" id="daochuA" target="_blank()" onclick="daochu();" class="splist_add">导 出</a>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="mun">
    <ul>
        <li style="background-color:#ff8382;position:relative;">
            <div class="b_num" id="price1" style="padding-top:25px;">
                0.00
            </div>
            <div class="mun_tt" style="padding-top:0px;">
                应收金额总计
            </div>
            <div class="zj_ddshoukuan_1_01">
                订货金额：<span id="price4">0.00</span>　　　运费：<span id="price5">0.00</span>
            </div>
        </li>
        <li style="background-color:#52ade6;">
            <div class="b_num" id="price2">
                0.00
            </div>
            <div class="mun_tt">
                已收金额总计
            </div>
        </li>
        <li style="background-color:#af99e8; margin-right:0px; float:right;">
            <div class="b_num" id="price3">
                0.00
            </div>
            <div class="mun_tt">
                待收金额总计
            </div>
        </li>
    </ul>
    <div class="clearBoth"></div>
</div>
<div class="purchase_list2" style="width:100%;position:relative;">
    <table id="product_list" lay-filter="product_list"></table>
    <script type="text/html" id="barDemo">
        <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
            <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
        </div>
    </script>
    <div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
        <ul>
            <li id="sheheBtn">
                <a href="javascript:detail();"><img src="images/biao_108.png"> 详情</a>
            </li>
        </ul>
    </div>
</div>
<div class="clearBoth"></div>
</div>
</div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="kehuName" value="<?=$kehuName?>">
<input type="hidden" id="areaId" value="<?=$areaId?>">
<input type="hidden" id="paystatus" value="<?=$paystatus?>">
<input type="hidden" id="level" value="<?=$level?>">
<input type="hidden" id="kehuStatus" value="<?=$kehuStatus?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<input type="hidden" id="url" value="<?=urlencode($request['url'])?>">
<script type="text/javascript">
    var productListTalbe;
    layui.use(['laydate', 'laypage','table','form'], function(){
      var laydate = layui.laydate
      ,laypage = layui.laypage
      ,table = layui.table
      ,form = layui.form
      ,load = layer.load()
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
        rerenderPrice();
        reloadTable(0);
    });
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-300"
        ,url: '?m=system&s=money&a=getTongjis&id=<?=$id?>'
        ,page: {curr:<?=$page?>}
        ,limit:<?=$limit?>
        ,cols: [[{field:'id',title:'id',width:0,style:'display:none'},{field:'dtTime',title:'下单时间',width:150},{field:'kehuName',title:'客户名称',width:250},{field:'orderId',title:'单号',width:220},{field:'price_dinghuo',title:'订货金额',width:180},{field:'price_wuliu',title:'运费',width:180},{field:'price',title:'应收金额',width:180},{field:'price_payed',title:'已收金额',width:180},{field:'price_weikuan',title:'待收金额',width:180},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}]]
        ,where:{
            startTime:'<?=$startTime?>',
            endTime:'<?=$endTime?>',
            keyword:'<?=$keyword?>',
            kehuName:'<?=$kehuName?>',
            areaId:'<?=$areaId?>',
            paystatus:'<?=$paystatus?>',
            level:'<?=$level?>',
            kehuStatus:'<?=$kehuStatus?>'
        },done: function(res, curr, count){
            $("#page").val(curr);
            layer.closeAll('loading');
            $("th[data-field='id']").hide();
        }
    });
    form.on('submit(search)', function(data){
        $("#kehuName").val(data.field.super_kehuName);
        $("#areaId").val(data.field.super_areaId);
        $("#level").val(data.field.super_level);
        $("#kehuStatus").val(data.field.super_kehuStatus);
        hideSearch();
        rerenderPrice();
        reloadTable(0);
        return false;
    });
    form.on('submit(quxiao)', function(){
        hideSearch();
        return false;
    });
    form.on('select(ps1)',function(data){
      if(!isNaN(data.value)){
            layer.load();
            id = data.value;
            ajaxpost=$.ajax({
              type:"POST",
              url:"/erp_service.php?action=getAreas",
              data:"id="+id,
              timeout:"4000",
              dataType:"text",
              success: function(html){
                $("#ps3").html('<option value="">请先选择市</option>');
                if(html!=""){
                  $("#ps2").html(html);
                  $("#super_areaId").val(id);
              }else{
                  $("#super_areaId").val(id);
              }
              form.render('select');
              layer.closeAll('loading');
          },
          error:function(){
            alert("超时,请重试");
            }
        });
        }            
    });
    form.on('select(ps2)',function(data){
      if(!isNaN(data.value)){
        layer.load();
        id = data.value;
        ajaxpost=$.ajax({
              type:"POST",
              url:"/erp_service.php?action=getAreas",
              data:"id="+id,
              timeout:"4000",
              dataType:"text",
              success: function(html){
                if(html!=""){
                  $("#ps3").html(html);
                  $("#super_areaId").val(id);
              }else{
                  $("#super_areaId").val(id);
              }
              form.render('select');
              layer.closeAll('loading');
          },
            error:function(){
                alert("超时,请重试");
            }
        });
        }
    });
    form.on('select(ps3)',function(data){
        if(!isNaN(data.value)){
            $("#super_areaId").val(data.value);
        }
    });
  });
</script>
<script type="text/javascript" src="js/money_tongji.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>