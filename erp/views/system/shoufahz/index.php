<?php
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$cangkuSql = "select id,title from demo_kucun_store where comId=$comId";
if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
    $cangkuSql .= " and id in(".$qx_arry['kucun']['storeIds'].")";
}
$cangkuSql .= " order by id asc";
$cangkus = $db->get_results($cangkuSql);
//$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId order by id asc");
$channelId = (int)$request['channelId'];
$brandId = (int)$request['brandId'];
$storeIds = $request['storeIds'];
$keyword = $request['keyword'];
$startTime = empty($request['startTime'])?date("Y-m-01"):$request['startTime'];
$endTime = empty($request['endTime'])?date("Y-m-d"):$request['endTime'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['shoufaPageNum'])?10:$_COOKIE['shoufaPageNum'];
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
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
        td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
        th[data-field="7"],th[data-field="9"],th[data-field="num_qichu"],th[data-field="price_qichu"],th[data-field="price_qichu_per"],th[data-field="num_chuku"],th[data-field="price_chuku"]{background:#b8d5e3}
    </style>
</head>
<body>
    <div class="back">
        <div><img src="images/biao_82.png"></div>
        <div>商品收发汇总表</div>
    </div>
    <div class="cont">
        <div class="operate">
            <div class="splist_up_01_left_01">
                <div class="splist_up_01_left_02_up">
                    <span><?=empty($storeName)?'全部仓库':$storeName?></span> <img src="images/biao_20.png"/>
                </div>
                <div class="splist_up_01_left_02_down">
                    <ul>
                        <li>
                            <a href="javascript:" onclick="selectStatus(0,'全部仓库');">全部仓库</a>
                        </li>
                        <? foreach($cangkus as $cangku){?>
                        <li>
                            <a href="javascript:" onclick="selectStatus(<?=$cangku->id?>,'<?=$cangku->title?>');"><?=$cangku->title?></a>
                        </li>
                        <?}?>
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
                        <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入商品名称/编码/规格"/>
                    </div>
                    <div class="splist_up_01_right_1_right">
                        <a href="javascript:" onclick="rerenderPrice();reloadTable(0);"><img src="images/biao_21.gif"/></a>
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
                                                关键词
                                            </div>
                                            <div class="gaojisousuo_right">
                                                <input type="text" name="super_keyword" class="gaojisousuo_right_input" placeholder="请输入商品名称/编码/规格"/>
                                            </div>
                                            <div class="gaojisousuo_left">
                                                商品分类
                                            </div>
                                            <div class="gaojisousuo_right">
                                                <div class="layui-form-select">
                                                    <div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="" class="layui-input"><i class="layui-edge"></i></div>
                                                    <dl class="layui-anim layui-anim-upbit" id="selectChannels"></dl>
                                                </div>
                                                <input type="hidden" name="super_channel" id="super_channel">
                                            </div>
                                            <div class="clearBoth"></div>
                                        </li>
                                        <li>
                                            <div class="gaojisousuo_left">
                                                商品品牌
                                            </div>
                                            <div class="gaojisousuo_right">
                                                <select name="super_brand" id="super_brand" lay-search>
                                                    <option value="">选择品牌或输入搜索</option>
                                                    <? if(!empty($brands)){
                                                        foreach ($brands as $b) {
                                                            ?>
                                                            <option value="<?=$b->id?>"><?=$b->title?></option>
                                                            <?
                                                        }
                                                    }?>
                                                </select>
                                            </div>
                                            <div class="clearBoth"></div>
                                        </li>
                                        <li>
                                            <div class="gaojisousuo_left">
                                                所属仓库
                                            </div>
                                            <div class="gaojisousuo_right">
                                                <input type="checkbox" name="super_cangkus_all" lay-skin="primary" lay-filter="cangkus" title="全选" checked />
                                                <? 
                                                foreach($cangkus as $i=>$t) {
                                                    ?>
                                                    <input type="checkbox" name="super_cangkus" pid="cangkus" lay-filter="nocangkus" lay-skin="primary" lay-skin="primary" title="<?=$t->title?>" value="<?=$t->id?>" />
                                                    <?
                                                }
                                                ?>
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
                <div class="export">
                    <a href="?m=system&s=shoufahz&a=daochu" onclick="daochu();" target="_blank" id="daochuA">导出</a>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="spshoufahuizong_1">
            <ul>
                <li class="spshoufahuizong_1_bj1">
                    <h2 id="price1"><img src="images/loading.gif" width="30"></h2>期初成本金额
                </li>
                <li class="spshoufahuizong_1_bj2">
                    <h2 id="price2"><img src="images/loading.gif" width="30"></h2>入库成本金额
                </li>
                <li class="spshoufahuizong_1_bj3">
                    <h2 id="price3"><img src="images/loading.gif" width="30"></h2>出库成本金额
                </li>
                <li class="spshoufahuizong_1_bj4">
                    <h2 id="price4"><img src="images/loading.gif" width="30"></h2>期末成本金额
                </li>
                <div class="clearBoth"></div>
            </ul>
        </div>
        <div class="purchase_list2" style="width:100%;position:relative;">
            <table id="product_list" lay-filter="product_list"></table>
            <script type="text/html" id="barDemo">
                <div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
                    <span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
                </div>
            </script>
            <div class="yuandian_xx" id="operate_row" data-id="0">
                <ul>
                    <li>
                        <a href="javascript:jilu_detail();"><img src="images/biao_30.png"> 明细</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="channelId" value="<?=$channelId?>">
    <input type="hidden" id="storeIds" value="<?=$storeIds?>">
    <input type="hidden" id="brandId" value="<?=$brandId?>">
    <input type="hidden" id="startTime" value="<?=$startTime?>">
    <input type="hidden" id="endTime" value="<?=$endTime?>">
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
            ,url: '?m=system&s=shoufahz&a=getjilus'
            ,page: {curr:<?=$page?>}
            ,limit:<?=$limit?>
            ,cols: [
                [{
                    field: 'id',
                    title: 'id',
                    width: 0,
                    rowspan: 2,
                    style: 'display:none'
                }, {
                    field: 'storeId',
                    title: 'storeId',
                    width: 0,
                    rowspan: 2,
                    style: 'display:none'
                }, {
                    field: 'sn',
                    title: '商品编码',
                    width: 200,
                    rowspan: 2
                }, {
                    field: 'title',
                    title: '商品名称',
                    width: 250,
                    rowspan: 2
                }, {
                    field: 'key_vals',
                    title: '规格',
                    width: 250,
                    rowspan: 2
                }, {
                    field: 'storeName',
                    title: '所属仓库',
                    width: 150,
                    rowspan: 2
                }, {
                    field: 'units',
                    title: '单位',
                    width: 80,
                    rowspan: 2
                }, {
                    title: '期初',
                    width: 200,
                    colspan: 3,
                    align: 'center'
                }, {
                    title: '入库',
                    width: 200,
                    colspan: 2,
                    align: 'center'
                }, {
                    title: '出库',
                    width: 200,
                    colspan: 2,
                    align: 'center'
                }, {
                    title: '期末',
                    width: 200,
                    colspan: 3,
                    align: 'center'
                }, {
                    fixed: 'right',
                    width: 49,
                    title: '',
                    align: 'center',
                    toolbar: '#barDemo',
                    rowspan: 2
                }],
                [{
                    field: 'num_qichu',
                    title: '数量',
                    width: 150
                }, {
                    field: 'price_qichu',
                    title: '成本金额',
                    width: 150
                }, {
                    field: 'price_qichu_per',
                    title: '平均成本',
                    width: 150
                }, {
                    field: 'num_ruku',
                    title: '数量',
                    width: 150
                }, {
                    field: 'price_ruku',
                    title: '成本金额',
                    width: 150
                }, {
                    field: 'num_chuku',
                    title: '数量',
                    width: 150
                }, {
                    field: 'price_chuku',
                    title: '成本金额',
                    width: 150
                }, {
                    field: 'num_qimo',
                    title: '数量',
                    width: 150
                }, {
                    field: 'price_qimo',
                    title: '成本金额',
                    width: 150
                }, {
                    field: 'price_qimo_per',
                    title: '平均成本',
                    width: 150
                }]
            ]
            ,where:{
                keyword:'<?=$keyword?>',
                channelId:'<?=$channelId?>',
                brandId:'<?=$brandId?>',
                startTime:'<?=$startTime?>',
                endTime:'<?=$endTime?>',
                storeIds:'<?=$storeIds?>'
            },done: function(res, curr, count){
                $("#page").val(curr);
                layer.closeAll('loading');
              }
          });
          $("th[data-field='id']").hide();
          $("th[data-field='storeId']").hide();
          form.on('checkbox(cangkus)', function(data){
            if(data.elem.checked){
                $("input[pid='cangkus']").prop("checked",false);
            }
            form.render('checkbox');
          });
          form.on('checkbox(nocangkus)', function(data){
            $("input[name='super_cangkus_all']").prop("checked",false);
            form.render('checkbox');
          });
          form.on('submit(search)', function(data){
            $("#keyword").val(data.field.super_keyword);
            $("#channelId").val(data.field.super_channel);
            $("#brandId").val(data.field.super_brand);
            if(data.field.super_cangkus_all=="on"){
                $("#storeIds").val('');
            }else{
                var cangkustr = '';
                $("input:checkbox[name='super_cangkus']:checked").each(function(){
                    cangkustr = cangkustr+','+$(this).val();
                });
                if(cangkustr.length>0){
                    cangkustr = cangkustr.substring(1);
                }
                $("#storeIds").val(cangkustr);
            }
            hideSearch();
            rerenderPrice();
            reloadTable(0);
            return false;
          });
          form.on('submit(quxiao)', function(){
            hideSearch();
            return false;
          });
          ajaxpost=$.ajax({
            type: "POST",
            url: "/erp_service.php?action=get_product_channels1",
            data: "",
            dataType:"text",timeout : 10000,
            beforeSend:function(){
                <? if($request['page']>1){?>
                reloadTable(1);
                <? }?>
            },
            success: function(resdata){
                $("#selectChannels").append(resdata);
                
            },
            error: function() {
                layer.msg('数据请求失败1', {icon: 5});
            }
          });
          $("#selectChannel").click(function(){
            $(this).parent().toggleClass('layui-form-selected');
          });
        });
    </script>
    <script type="text/javascript" src="js/shoufahz.js"></script>
    <? require('views/help.html');?>
</body>
</html>