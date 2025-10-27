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
        <a href="?s=yyyx&a=gift_card"><img src="images/back.gif"/></a> 生成礼品卡
    </div>
    <div class="right_down">
     <div class="sprukuadd">
        <form id="addForm" action="" method="post" class="layui-form">
            <div class="dhd_adddinghuodan_3" style="padding-bottom:100px">
             <ul>
                <li>
                    <div class="dhd_adddinghuodan_3_left">礼品卡名称：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" class="layui-input" placeholder="请输入礼品卡名称，最多30个字" name="title" lay-verify="required" style="width:300px" maxlength="30">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">应用场景</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="radio" name="type" value="1" <? if($request['type']==1){?>checked="true"<? }?> title="线下制卡">
                        <input type="radio" name="type" value="2" <? if($request['type']==2){?>checked="true"<? }?> title="线上销售">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">面额：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" class="layui-input" name="money" placeholder="输入面额" lay-verify="required|shuzi" style="width:100px" >
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">售价：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" class="layui-input" name="price" placeholder="输入售价" lay-verify="required|shuzi" style="width:100px" >
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">生成数量：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" class="layui-input" name="num" id="num" placeholder="生成数量" lay-verify="required|shuzi" style="width:100px" >
                    </div>
                    <div class="dhd_adddinghuodan_3_right" style="padding-left:20px;line-height:40px;color:red">PS:每次最多生成1000张礼品卡</div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">过期时间：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" name="endTime" id="endTime" readonly="true" class="layui-input" placeholder="不限制请留空" style="width:200px" >
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
            <div class="sprukuadd_05" style="position: relative;">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
            </div>
        </div>
    </form>
</div>
</div>
<script type="text/javascript">
    var productListForm;
    layui.use(['laydate','form'], function(){
        var laydate = layui.laydate
        ,form = layui.form
        laydate.render({
            elem: '#endTime'
        });
        form.verify({
            shuzi:function(value,item){
                value = parseFloat(value);
                if(isNaN(value)||value<=0){
                    return '字段不能小于0';
                }
            }
        });
        form.on('submit(tijiao)', function(data){
            var num = parseInt($("#num").val());
            if(num<1||num>1000){
                layer.msg("生成数量必须在1-1000之间",function(){});
                return false;
            }
            layer.msg('礼品卡生成中,请勿关闭页面...',{icon:16,shade:0.1});
            var type = $("input[name='type']:checked").val();
            $.ajax({
                type: "POST",
                url: "?s=yyyx&a=create_card&submit=1",
                data: $("#addForm").serialize(),
                dataType : "json",timeout : 10000,
                success: function(data) {
                    layer.closeAll();
                    if(data.code!=1){
                        layer.msg(data.message,{icon:5});
                        return false;
                    }else{
                        layer.alert('礼品卡创建成功',function(){
                            location.href='?s=yyyx&a=gift_card&type='+type;
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
</script>
<? require('views/help.html');?>
</body>
</html>