<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$fenbiao = getFenBiao($comId,20);
$keyword = $request['keyword'];
$areaId = (int)$request['areaId'];
$level = (int)$request['level'];
$kehuStatus =(int)$request['kehuStatus'];
$limit = empty($_COOKIE['m_accPageNum'])?10:$_COOKIE['m_accPageNum'];
$areas = $db->get_results("select * from demo_area where parentId=0");
$zong = $db->get_results("select type,sum(money) as money from demo_kehu_account where comId=$comId group by type");
$zong1 = 0.00;
$zong2 = 0.00;
$zong3 = 0.00;
$zong4 = 0.00;
if(!empty($zong)){
    foreach ($zong as $z) {
        if($z->type==1){
            $zong1 = $z->money;
        }else if($z->type==2){
            $zong2 = $z->money;
        }else if($z->type==3){
            $zong3 = $z->money;
        }else if($z->type==4){
            $zong4 = $z->money;
        }
    }
}
$page = empty($request['page'])?1:(int)$request['page'];
$kehu_shezhi = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
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
    .mun ul li {width:23%;}
    .b_num{padding-top:20px;}
</style>
</head>
<body>
    <div class="back">
        <div><img src="images/biao_105.png" /></div>
        <div>资金账户</div>
    </div>
    <div class="cont">
        <div class="operate">
            <div class="splist_up_01_right" style="width: 97%;">    
                <div class="splist_up_01_right_1">
                    <div class="splist_up_01_right_1_left">
                        <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称"/>
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
                                                <input type="text" name="super_keyword" value="<?=$keyword?>" class="gaojisousuo_right_input" placeholder="请输入<?=$kehu_title?>名称"/>
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
            <a href="?m=system&s=money&a=daochuAccount" id="daochuA" target="_blank()" onclick="daochu();" class="splist_add">导 出</a>
        </div>
        <div class="clearBoth"></div>
    </div>
</div>
<div class="mun">
    <ul>
        <li style="background-color:#ff8382;position:relative;">
            <div class="mun_tt">
                <?=$kehu_shezhi->acc_xianjin_name?> 余额总计
            </div>
            <div class="b_num" id="price1">
                <?=$zong1?>
            </div>
        </li>
        <li style="background-color:#52ade6;">
            <div class="mun_tt">
                <?=$kehu_shezhi->acc_yufu_name?> 余额总计
            </div>
            <div class="b_num" id="price2">
                <?=$zong2?>
            </div>
        </li>
        <li style="background-color:#af99e8;">
            <div class="mun_tt">
                <?=$kehu_shezhi->acc_fandian_name?> 余额总计
            </div>
            <div class="b_num" id="price3">
                <?=$zong3?>
            </div>
        </li>
        <li style="background-color:#feaaa1; margin-right:0px;">
            <div class="mun_tt">
                <?=$kehu_shezhi->acc_baozheng_name?> 余额总计
            </div>
            <div class="b_num" id="price3">
                <?=$zong4?>
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
            <li>
                <a href="javascript:detail();"><img src="images/biao_108.png"> 明细</a>
            </li>
            <li>
                <a href="javascript:chongzhi();"><img src="images/biao_95.png"> 充值</a>
            </li>
            <li>
                <a href="javascript:koukuan();"><img src="images/biao_106.png"> 扣款</a>
            </li>
        </ul>
    </div>
</div>
<div class="clearBoth"></div>
</div>
</div>
<!--充值弹出-->
<div class="zjzhmx_chongzhi" id="chongzhi_div">
    <div class="kh_gjsousuo_01">
        充值
    </div>
    <div class="kh_gjsousuo_03">
        <form action="?m=system&s=money&a=acc_chongzhi" id="chongzhiForm" method="post" class="layui-form">
        <ul>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 充值客户 
                </div>
                <div class="kh_gjsousuo_03_right">
                    <input type="text" value="" id="acc_kehuName" readonly="true" class="kh_gjsousuo_03_right_input" disabled/>
                </div>
                <div class="kh_gjsousuo_03_left">
                    充值日期
                </div>
                <div class="kh_gjsousuo_03_right">
                    <div class="kh_gjsousuo_03_right_yewu">
                        <input type="text" value="" name="dtTime" readonly="true" style="width:340px;border:0px;height:34px;padding-left:10px;" id="acc_chongzhi_dtTime"/>
                        <div class="clearBoth"></div>
                    </div>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                    充值账户
                </div>
                <div class="kh_gjsousuo_03_right" style="width:362px;">
                    <select id="acc_chongzhi_type" name="type">
                        <option value="1"><?=$kehu_shezhi->acc_xianjin_name?></option>
                        <option value="2"><?=$kehu_shezhi->acc_yufu_name?></option>
                        <option value="3"><?=$kehu_shezhi->acc_fandian_name?></option>
                        <option value="4"><?=$kehu_shezhi->acc_baozheng_name?></option>
                    </select>
                </div>
                <div class="kh_gjsousuo_03_left">
                    备注 
                </div>
                <div class="kh_gjsousuo_03_right">
                    <div class="kh_gjsousuo_03_right_zjbeizhu">
                        <textarea name="beizhu"></textarea>
                    </div>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 金额
                </div>
                <div class="kh_gjsousuo_03_right">
                    <input type="number" name="money" id="acc_chongzhi_money" min="0" step="0.01" class="kh_gjsousuo_03_right_input"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 充值摘要
                </div>
                <div class="kh_gjsousuo_03_right" style="width:362px;">
                    <select id="acc_chongzhi_remark" name="remark">
                        <option value="现金充值">现金充值</option>
                        <option value="销售返点">销售返点</option>
                        <option value="退款">退款</option>
                        <option value="其他充值">其他充值</option>
                        <option value="订单退款">订单退款</option>
                    </select>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
        <input type="hidden" name="kehuId" id="acc_chongzhi_kehuId" value="0">
    </form>
    </div>  
    <div class="kh_gjsousuo_04">
        <a href="javascript:tijiaoChongzhi();" class="kh_gjsousuo_04_1">确定</a><a href="javascript:hide_acc_chongzhi();" class="kh_gjsousuo_04_2">取消</a> <a href="javascript:" onclick="$('#chongzhiForm').reset();">清空</a>
    </div>
</div>
<!--充值弹出结束-->
<!--扣款弹出-->
<div class="zjzhmx_koukuan" id="koukuan_div">
    <div class="kh_gjsousuo_01">
        扣款
    </div>
    <div class="kh_gjsousuo_03">
        <form action="?m=system&s=money&a=acc_koukuan" id="koukuanForm" method="post" class="layui-form">
        <ul>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 扣款客户
                </div>
                <div class="kh_gjsousuo_03_right" style="width:362px;">
                    <input type="text" value="" id="acct_kehuName" readonly="true" class="kh_gjsousuo_03_right_input" disabled/>
                </div>
                <div class="kh_gjsousuo_03_left">
                    扣款日期 
                </div>
                <div class="kh_gjsousuo_03_right">
                    <div class="kh_gjsousuo_03_right_yewu">
                        <input type="text" value="" name="dtTime" readonly="true" style="width:340px;border:0px;height:34px;padding-left:10px;" id="acc_koukuan_dtTime"/>
                        <div class="clearBoth"></div>
                    </div>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                     扣款账户
                </div>
                <div class="kh_gjsousuo_03_right" style="width:362px;">
                    <select id="acc_koukuan_type" name="type">
                        <option value="1"><?=$kehu_shezhi->acc_xianjin_name?></option>
                        <option value="2"><?=$kehu_shezhi->acc_yufu_name?></option>
                        <option value="3"><?=$kehu_shezhi->acc_fandian_name?></option>
                        <option value="4"><?=$kehu_shezhi->acc_baozheng_name?></option>
                    </select>
                </div>
                <div class="kh_gjsousuo_03_left">
                    备注 
                </div>
                <div class="kh_gjsousuo_03_right">
                    <div class="kh_gjsousuo_03_right_zjbeizhu">
                        <textarea name="beizhu"></textarea>
                    </div>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 金额
                </div>
                <div class="kh_gjsousuo_03_right">
                    <input type="number" name="money" id="acc_koukuan_money" min="0" step="0.01" class="kh_gjsousuo_03_right_input"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kh_gjsousuo_03_left">
                    <span>*</span> 扣款摘要 
                </div>
                <div class="kh_gjsousuo_03_right">
                    <select id="acc_koukuan_remark" name="remark">
                        <option value="订单付款">订单付款</option>
                        <option value="其他扣款">其他扣款</option>
                        <option value="付款单扣款">付款单扣款</option>
                    </select>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
        <input type="hidden" name="kehuId" id="acc_koukuan_kehuId" value="0">
    </form>
    </div>  
    <div class="kh_gjsousuo_04">
        <a href="javascript:tijiaoKoukuan();" class="kh_gjsousuo_04_1">确定</a><a href="javascript:hide_acc_koukuan();" class="kh_gjsousuo_04_2">取消</a> <a href="javascript:" onclick="$('#koukuanForm').reset();">清空</a>
    </div>
</div>
<!--扣款弹出结束-->
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="areaId" value="<?=$areaId?>">
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
        elem: '#acc_chongzhi_dtTime'
        ,max:'<?=date("Y-m-d H:i:s")?>'
        ,value:'<?=date("Y-m-d H:i")?>'
        ,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm'
    });
    laydate.render({
        elem: '#acc_koukuan_dtTime'
        ,max:'<?=date("Y-m-d H:i:s")?>'
        ,value:'<?=date("Y-m-d H:i")?>'
        ,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm'
    });
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-300"
        ,url: '?m=system&s=money&a=getAccounts'
        ,page: {curr:<?=$page?>}
        ,limit:<?=$limit?>
        ,cols: [[{field:'id',title:'id',width:0,style:'display:none'},{field:'title',title:'客户名称',width:150},{field:'account1',title:'<?=$kehu_shezhi->acc_xianjin_name?>',width:180},{field:'account2',title:'<?=$kehu_shezhi->acc_yufu_name?>',width:180},{field:'account3',title:'<?=$kehu_shezhi->acc_fandian_name?>',width:180},{field:'account4',title:'<?=$kehu_shezhi->acc_baozheng_name?>',width:180},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}]]
        ,where:{
            startTime:'<?=$startTime?>',
            endTime:'<?=$endTime?>',
            keyword:'<?=$keyword?>',
            areaId:'<?=$areaId?>',
            level:'<?=$level?>',
            kehuStatus:'<?=$kehuStatus?>'
        },done: function(res, curr, count){
            $("#page").val(curr);
            layer.closeAll('loading');
            $("th[data-field='id']").hide();
        }
    });
    form.on('submit(search)', function(data){
        $("#keyword").val(data.field.super_keyword);
        $("#areaId").val(data.field.super_areaId);
        $("#level").val(data.field.super_level);
        $("#kehuStatus").val(data.field.super_kehuStatus);
        hideSearch();
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
<script type="text/javascript" src="js/money_account.js"></script>
<script type="text/javascript" src="js/money_acc_opt.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>