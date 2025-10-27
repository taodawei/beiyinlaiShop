<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$fenbiao = getFenBiao($comId,20);
$id = (int)$request['id'];
$kehu = $db->get_row("select * from demo_kehu where id=$id and comId=$comId limit 1");
if(empty($kehu)){
    echo '<script>alert("客户不存在！");history.go(-1);</script>';
}
$moneys = $db->get_results("select type,sum(money) as money from demo_kehu_liushui$fenbiao where kehuId=$id and status=1 group by type");
$chongzhi = 0.00;
$koukuan = 0.00;
if(!empty($moneys)){
    foreach ($moneys as $m) {
        if($m->type==1){
            $chongzhi = $m->money;
        }else{
            $koukuan = $m->money;
        }
    }
}
$zongs = $db->get_results("select type,sum(money) as money from demo_kehu_account where comId=$comId and kehuId=$id group by type");
$zong1 = 0.00;
$zong2 = 0.00;
$zong3 = 0.00;
$zong4 = 0.00;
if(!empty($zongs)){
    foreach ($zongs as $z) {
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
$zong = $zong1+$zong2+$zong3+$zong4;
$kehu_shezhi = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
$dai_money = $db->get_var("select sum(money) from demo_dinghuo_money where comId=$comId and kehuId=$id and status=0 and type=0");
$keyword = $request['keyword'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$accountType = (int)$request['accountType'];
$limit = empty($_COOKIE['m_accdPageNum'])?10:$_COOKIE['m_accdPageNum'];
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
        <div><a href="<?=urldecode($request['returnurl'])?>"><img src="images/biao_63.png"/></a></div>
        <div>资金账户明细</div>
    </div>
    <div class="cont">
        <div class="zj_zijinzhmingxi">
            <div class="zj_zijinzhmingxi_01">
                客户名称：<span><?=$kehu->title?></span>客户级别：<span><?=$db->get_var("select title from demo_kehu_level where id=$kehu->level");?></span>总金额：<span><?=$zong?></span> 待确认收款：<span style="color:#ff2d2d;"><?=empty($zong_money)?'0.00':$zong_money?></span>
            </div>
            <div class="zj_zijinzhmingxi_02">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tbody><tr height="47">
                        <td bgcolor="f4f7f9" align="center" valign="middle">
                            账户
                        </td>
                        <td bgcolor="f4f7f9" align="center" valign="middle">
                            余额
                        </td>
                        <td bgcolor="f4f7f9" align="center" valign="middle">
                            操作
                        </td>
                    </tr>
                    <tr height="47">
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$kehu_shezhi->acc_xianjin_name?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$zong1?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <a href="javascript:" onclick="acc_chongzhi(<?=$id?>,1,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_chongzhi">充值</a><a href="javascript:" onclick="acc_koukuan(<?=$id?>,1,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_koukuan">扣款</a>
                        </td>
                    </tr>
                    <tr height="47">
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$kehu_shezhi->acc_yufu_name?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$zong2?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <a href="javascript:" onclick="acc_chongzhi(<?=$id?>,2,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_chongzhi">充值</a><a href="javascript:" onclick="acc_koukuan(<?=$id?>,2,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_koukuan">扣款</a>
                        </td>
                    </tr>
                    <tr height="47">
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$kehu_shezhi->acc_fandian_name?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$zong3?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <a href="javascript:" onclick="acc_chongzhi(<?=$id?>,3,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_chongzhi">充值</a><a href="javascript:" onclick="acc_koukuan(<?=$id?>,3,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_koukuan">扣款</a>
                        </td>
                    </tr>
                    <tr height="47">
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$kehu_shezhi->acc_baozheng_name?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$zong4?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <a href="javascript:" onclick="acc_koukuan(<?=$id?>,2,'<?=$kehu->title?>');" class="zj_zijinzhmingxi_02_koukuan">扣款</a>
                        </td>
                    </tr>
                    <tr height="47">
                        <td bgcolor="ffffff" align="center" valign="middle">
                            总计  
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">
                            <?=$zong?>
                        </td>
                        <td bgcolor="ffffff" align="center" valign="middle">

                        </td>
                    </tr>
                </tbody></table>
            </div>
        </div>
        <div class="operate">
            <div class="splist_up_01_right" style="width: 97%;">
                <div class="splist_up_01_left_02" style="float:left;margin-right:20px;">
                    <div class="splist_up_01_left_02_up">
                        <span>全部资金帐号</span> <img src="images/biao_20.png"/>
                    </div>
                    <div class="splist_up_01_left_02_down">
                        <ul>
                            <li>
                                <a href="javascript:" onclick="selectStatus('0','全部资金帐号');" class="splist_up_01_left_02_down_on">全部资金帐号</a>
                            </li>
                            <li>
                                <a href="javascript:" onclick="selectStatus('1','<?=$kehu_shezhi->acc_xianjin_name?>');" class="splist_up_01_left_02_down_on"><?=$kehu_shezhi->acc_xianjin_name?></a>
                            </li>
                            <li>
                                <a href="javascript:" onclick="selectStatus('2','<?=$kehu_shezhi->acc_yufu_name?>');" class="splist_up_01_left_02_down_on"><?=$kehu_shezhi->acc_yufu_name?></a>
                            </li>
                            <li>
                                <a href="javascript:" onclick="selectStatus('3','<?=$kehu_shezhi->acc_fandian_name?>');" class="splist_up_01_left_02_down_on"><?=$kehu_shezhi->acc_fandian_name?></a>
                            </li>
                            <li>
                                <a href="javascript:" onclick="selectStatus('4','<?=$kehu_shezhi->acc_baozheng_name?>');" class="splist_up_01_left_02_down_on"><?=$kehu_shezhi->acc_baozheng_name?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="splist_up_01_left_02" style="float:left;margin-right:20px;">
                    <div class="gaojisousuo_right" style="height:35px;margin-top:15px;">
                        <div class="sprukulist_01" style="top:0px;margin-left:0px;z-index:999;">
                            <div class="sprukulist_01_left">
                                <span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                            </div>
                            <div class="sprukulist_01_right">
                                <img src="images/biao_76.png"/>
                            </div>
                            <div class="clearBoth"></div>
                            <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;">
                                <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="splist_up_01_right_1">
                    <div class="splist_up_01_right_1_left">
                        <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入流水号/单号/摘要"/>
                    </div>
                    <div class="splist_up_01_right_1_right">
                        <a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
        <div class="mun">
            <ul>
                <li style="background-color:#52ade6;position:relative;width:47.8%;">
                    <div class="mun_tt">
                        充值
                    </div>
                    <div class="b_num" id="price1">
                        <?=$chongzhi?>
                    </div>
                </li>
                <li style="background-color:#ff8382;width:47.8%;">
                    <div class="mun_tt">
                        扣款
                    </div>
                    <div class="b_num" id="price2">
                        <?=$koukuan?>
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
<div class="shoukuanqueren_xiangqing_tc" id="shoukuanqueren_xiangqing_tc" style="display:none;">
    <div class="bjkh_bj"></div>
    <div class="skqr_xx">
        <div class="bjkh_jebangsjxx_1">
            财务详情
        </div>
        <div class="bjkh_jebangsjxx_2">
            <div class="skqr_xx_01">
                <ul id="detail_ul">
                    
                </ul>
            </div>
            <div class="skqr_xx_02">
                流水号：<span id="show_orderId"></span>　　　　　　日期：<span id="show_dtTime"></span><br>
                操作人：<span id="show_userName"></span>　　　　　　 审核人：<span id="show_shenheUser"></span>
            </div>
        </div>
        <div class="bjkh_jebangsjxx_3">
            <a href="javascript:hideInfo();" class="bjkh_jebangsjxx_3_01">确定</a>
        </div>
    </div>
</div>
<!--扣款弹出结束-->
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="accountType" value="<?=$accountType?>">
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
        reloadTable(0);
    });
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
        ,url: '?m=system&s=money&a=get_acc_detail&id=<?=$id?>'
        ,page: true
        ,limit:<?=$limit?>
        ,cols: [[{field:'id',title:'id',width:0,style:'display:none'},{field:'detail',title:'detail',width:0,style:'display:none'},{field:'orderId',title:'流水号',width:220},{field:'dtTime',title:'时间',width:180},{field:'pay_type',title:'资金账户',width:180},{field:'remark',title:'摘要',width:180},{field:'money',title:'金额',width:180},{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}]]
        ,where:{
            startTime:'<?=$startTime?>',
            endTime:'<?=$endTime?>',
            keyword:'<?=$keyword?>',
            accountType:'<?=$accountType?>'
        },done: function(res, curr, count){
            $("#page").val(curr);
            layer.closeAll('loading');
            $("th[data-field='id']").hide();
            $("th[data-field='detail']").hide();
        }
    });
  });
</script>
<script type="text/javascript" src="js/money_acc_detail.js"></script>
<script type="text/javascript" src="js/money_acc_opt.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>