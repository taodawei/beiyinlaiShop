<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id))$cuxiao = $db->get_row("select * from reg_gift where id=$id");
if(!empty($cuxiao->guizes)){
    $guizes = json_decode($cuxiao->guizes,true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
    .sprukuadd_04 ul li{display:inline-block;width:523px;margin-right:20px;}
    .add_other,.add_pay{float:right}
    .add_other{padding:17px 15px 5px 0;font-size:13px;color:#6a6a6a;line-height:34px}
    .add_other div{padding-bottom:5px}
    #shouhuoDiv img,#fapiaoCont img{cursor:pointer;}
    .layui-table-cell{height:36px}
    .sprukuadd_03_tt_input{width:60px;}
    .yx_spcuxiaoadd_2_right_guize_01{margin-left:30px;margin-top:10px;}
    .yx_spcuxiaoadd_2_right_guize_01 input{height:30px;}
    .layui-table-main .layui-table-cell{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
</style>
</head>
<body>
    <div class="right_up">
        <a href="?s=yyyx&a=reg"><img src="images/back.gif"/></a> 新增注册赠送活动
    </div>
    <div class="right_down">
     <div class="sprukuadd">
        <form id="addForm" action="" method="post" class="layui-form">
            <input type="hidden" name="startTime" id="startTime">
            <input type="hidden" name="endTime" id="endTime">
            <div class="dhd_adddinghuodan_3" style="padding-bottom:280px">
             <ul>
                <li>
                    <div class="dhd_adddinghuodan_3_left">活动时间：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="sprukulist_01" style="margin-left:0px;top:0px;">
                            <div class="sprukulist_01_left">
                                <span id="s_time1"><?=empty($startTime)?'&nbsp;&nbsp;&nbsp;&nbsp;选择日期&nbsp;&nbsp;&nbsp;&nbsp;':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'&nbsp;&nbsp;&nbsp;&nbsp;选择日期&nbsp;&nbsp;&nbsp;&nbsp;':$endTime?></span>
                            </div>
                            <div class="sprukulist_01_right">
                                <img src="images/biao_76.png"/>
                            </div>
                            <div class="clearBoth"></div>
                            <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99">
                                <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">活动规则：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="yx_spcuxiaoadd_2_right_guize">
                            <input type="radio" name="type" value="1" lay-filter="type1" <? if(empty($cuxiao)||$cuxiao->type==1){?>checked="true"<? }?> title="注册返现"/><br>
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian1" rows="<?=$cuxiao->type==1?count($guizes):1?>" <? if(!empty($cuxiao)&&$cuxiao->type!=1){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==1){
                                    foreach ($guizes as $key => $g){
                                        ?>
                                        <div id="rows_2_<?=$key+1?>">
                                            注册赠金额<span><input type="number" step="1" name="jian_1_<?=$key+1?>" value="<?=$g['jian']?>"/></span>元
                                            <input type="hidden" name="rows_1[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('1_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_2_1">
                                    注册赠金额<span><input type="number" step="1" name="jian_1_1"/></span>元
                                    <input type="hidden" name="rows_1[]" value="1" >
                                </div>
                                <? }?>
                            </div>
                            <input type="radio" name="type" value="2" lay-filter="type2" <? if($cuxiao->type==2){?>checked="true"<? }?> title="注册返积分" /><br>
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian2" rows="<?=$cuxiao->type==2?count($guizes):1?>" <? if($cuxiao->type!=2){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==2){
                                    foreach ($guizes as $key => $g){
                                        ?>
                                        <div id="rows_2_<?=$key+1?>">
                                            注册赠积分<span><input type="number" step="1" name="jian_2_<?=$key+1?>" value="<?=$g['jian']?>"/></span>
                                            <input type="hidden" name="rows_2[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('2_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_2_1">
                                    注册赠积分<span><input type="number" step="1" name="jian_2_1"/></span>
                                    <input type="hidden" name="rows_2[]" value="1" >
                                </div>
                                <? }?>
                            </div>  
                            <input type="radio" name="type" value="3" lay-filter="type3" <? if($cuxiao->type==3){?>checked="true"<? }?> title="注册赠优惠券" />
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian3" rows="<?=$cuxiao->type==3?count($guizes):1?>" <? if($cuxiao->type!=3){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==3){
                                    foreach ($guizes as $key => $g){
                                        $yhqName = $db->get_var("select title from yhq where id=".$g['yhqId']);
                                        ?>
                                        <div id="rows_3_<?=$key+1?>">
                                            注册赠优惠券<a href="javascript:" onclick="showAllpdts(<?=$key+1?>);" id="pdt_3_<?=$key+1?>" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp"><?=$yhqName?></a><span><input type="number" step="1" name="jian_3_<?=$key+1?>" value="<?=$g['jian']?>"/></span>个
                                            <input type="hidden" name="yhqId_3_<?=$key+1?>" id="yhqId_3_<?=$key+1?>" class="inventory_input" value="<?=$g['yhqId']?>">
                                            <input type="hidden" name="rows_3[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('3_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_1_1">
                                    注册赠优惠券<a href="javascript:" onclick="showAllpdts(1);" id="pdt_3_1" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp">请选择优惠券</a><span><input type="number" step="1" name="jian_3_1"/></span>个
                                    <input type="hidden" name="yhqId_3_1" id="yhqId_3_1" class="inventory_input" value="0">
                                    <input type="hidden" name="rows_3[]" value="1">
                                </div>
                                <? }?>
                                <a href="javascript:" onclick="add_qujian(this,3);">+添加优惠券</a>
                                （PS:优惠券发放完将不再赠送，请注意优惠券的余量）
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
            <div class="sprukuadd_05" >
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
            </div>
        </div>
    </form>
</div>
</div>
<div class="sprkadd_xuanzesp">
    <div class="sprkadd_xuanzesp_01">
        <div class="sprkadd_xuanzesp_01_1">选择优惠券</div>
       <div class="clearBoth"></div>
    </div>
    <div class="sprkadd_xuanzesp_02">
        <table id="product_list" lay-filter="product_list"></table>
    </div>
    <div class="sprkadd_xuanzesp_03">
       <a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
    </div>
</div>
<input type="hidden" id="channelId" value="<?=$channelId?>">
<input type="hidden" id="departs" value="" />
<input type="hidden" id="users" value="" />
<input type="hidden" id="departNames" value=""/>
<input type="hidden" id="userNames" value="" />
<input type="hidden" id="editId" value="0">
<div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<script type="text/javascript">
    var productListTalbe;
    var productListForm;
    layui.use(['laydate', 'laypage','table','form','upload'], function(){
        var laydate = layui.laydate
        ,laypage = layui.laypage
        ,table = layui.table
        ,form = layui.form
        ,upload = layui.upload
    laydate.render({
        elem: '#riqi1'
        ,show: true
        ,position: 'static'
        ,min: '2018-01-01'
        ,type:'date'
        ,format: 'yyyy-MM-dd'
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
        ,min: '2018-01-01'
        ,btns: ['confirm']
        ,type:'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){
            $("#s_time2").html(value);
            $("#endTime").val(value);
        }
    });
    $(".laydate-btns-confirm").click(function(){
        $("#riqilan").slideUp(200);
    });
    productListForm = form;
    //渲染选择产品列表
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-250"
        ,url: '?s=yyyx&a=getYhqList&type=1'
        ,page: true
        ,cols: [[{field:'title',title:'优惠券名称',width:250},{field:'jiazhi',title:'价值',width:150},{field:'time',title:'有效期',width:180},{field:'fanwei',title:'适用商品',width:200},{field:'select',title:'选择',width:80}]]
        ,done: function(res, curr, count){
            $("#page").val(curr);
            layer.closeAll('loading');
        }
    });
    //规则切换
    form.on('radio(type1)',function(){
        $("#qujian1").show();
        $("#qujian2").hide();
        $("#qujian3").hide();
    });
    form.on('radio(type2)',function(){
        $("#qujian2").show();
        $("#qujian1").hide();
        $("#qujian3").hide();
    });
    form.on('radio(type3)',function(){
        $("#qujian3").show();
        $("#qujian1").hide();
        $("#qujian2").hide();
    });
    form.on('submit(tijiao)', function(data){
        var startTime = $("#startTime").val();
        var endTime = $("#endTime").val();
        if(startTime==''||endTime==''){
            layer.msg("请先选择活动时间",function(){});
            return false;
        }
        var type = parseInt($("input[name='type']:checked").val());
        tijiao = 1;
        $("#qujian"+type+" input").each(function(){
            var val = parseFloat($(this).val());
            if(isNaN(val)||val<=0){
                if($(this).attr('type')=='hidden'){
                    layer.msg("请选择要赠送的优惠券",function(){});
                }else{
                    layer.msg("该字段必须是大于0的数字",function(){});
                }
                tijiao = 0;
                return false;
            }
        });
        if(tijiao==0){
            return false;
        }
        layer.load();
        $.ajax({
            type: "POST",
            url: "?s=yyyx&a=create_reg&submit=1",
            data: $("#addForm").serialize(),
            dataType : "json",timeout : 10000,
            success: function(data) {
                layer.closeAll();
                if(data.code!=1){
                    layer.msg(data.message,{icon:5});
                    return false;
                }else{
                    layer.alert('创建成功',function(){
                        location.href='?s=yyyx&a=reg';
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg("请求超时，请检查网络");
            }
        });
        return false;
    });
});
function add_qujian(dom,type){
    var rows = parseInt($("#qujian"+type).attr("rows"));
    rows = rows+1;
    //var accordType = parseInt($("input[name='accordType']:checked").val());
    accord_name = '金额';
    var str = '<div id="rows_'+type+'_'+rows+'">';
        switch(type){
            case 3:
                str = str+'赠优惠券<a href="javascript:" onclick="showAllpdts('+rows+');" id="pdt_'+type+'_'+rows+'" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp">请选择优惠券</a><span><input type="number" step="1" name="jian_'+type+'_'+rows+'"/></span>个'+
                '<input type="hidden" name="yhqId_'+type+'_'+rows+'" id="yhqId_'+type+'_'+rows+'" class="inventory_input" value="0">';
            break;
        }
        str = str+'<input type="hidden" name="rows_'+type+'[]" value="'+rows+'" >&nbsp;<a href="javascript:" onclick="del_rows(\''+type+'_'+rows+'\')"><img src="images/yingxiao_30.png"></a>'+
    '</div>';
    $(dom).before(str);
    $("#qujian"+type).attr("rows",rows);
}
function del_rows(id){
    $("#rows_"+id).remove();
}
</script>
<script type="text/javascript" src="js/yyyx/create_reg.js"></script>
<? require('views/help.html');?>
</body>
</html>