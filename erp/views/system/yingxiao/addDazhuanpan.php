<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id))$dazhuanpan = $db->get_row("select * from demo_dazhuanpan where id=$id");
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
    .sprukulist_01_left span{padding:0px;}
</style>
</head>
<body>
    <div class="right_up">
        <a href="?s=yingxiao&a=dazhuanpan"><img src="images/back.gif"/></a> 新增大转盘活动
    </div>
    <div class="right_down">
     <div class="sprukuadd">
        <form id="addForm" action="" method="post" class="layui-form">
            <input type="hidden" name="startTime" id="startTime">
            <input type="hidden" name="endTime" id="endTime">
            <div class="dhd_adddinghuodan_3">
             <ul>
                <li>
                    <div class="dhd_adddinghuodan_3_left">活动主题：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" name="title" style="width: 297px;" lay-verify="required" value="<?=$cuxiao->title?>" class="layui-input" placeholder="输入活动主题">
                    </div>
                    <div class="clearBoth"></div>
                </li>
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
                    <div class="dhd_adddinghuodan_3_left">抽奖次数：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" name="per_num" style="width: 100px;" lay-verify="required|number" value="1" class="layui-input" placeholder="输入抽奖次数">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">抽奖限制：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="yx_spcuxiaoadd_2_right_guize">
                            <input type="radio" name="per_type" value="1" title="按抽奖次数抽完为止" checked="true" /><br>
                            <input type="radio" name="per_type" value="2" title="每天都可以重新抽奖" />
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">消耗积分/次：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="text" name="per_jifen" style="width: 100px;" lay-verify="required|number" value="0" class="layui-input" placeholder="输入每次消耗积分" style="display:inline-block;">
                        <span style="color:red;">0代表免费抽奖</span>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dhd_adddinghuodan_3_left">活动说明：</div>
                    <div class="dhd_adddinghuodan_3_right" style="width:600px">
                        <textarea name="content" class="layui-textarea"></textarea>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="sprukuadd_05">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
        </div>
    </form>
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
    layui.use(['laydate','form'], function(){
        var laydate = layui.laydate
        ,form = layui.form
        $(".sprukulist_01").click(function(eve){
            $("#riqilan").slideToggle(200);
            stopPropagation(eve);
        });
        laydate.render({
            elem: '#riqi1'
            ,show: true
            ,position: 'static'
            ,min: '2018-01-01'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
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
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
            ,done: function(value, date, endDate){
                $("#s_time2").html(value);
                $("#endTime").val(value);
            }
        });
        $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
        });
        productListForm = form;
        form.on('submit(tijiao)', function(data){
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            if(startTime==''||endTime==''){
                layer.msg("请先选择促销时间",function(){});
                return false;
            }
            layer.load();
            $.ajax({
                type: "POST",
                url: "?s=yingxiao&a=addDazhuanpan&submit=1",
                data: $("#addForm").serialize(),
                dataType : "json",timeout : 10000,
                success: function(data) {
                    layer.closeAll();
                    if(data.code!=1){
                        layer.msg(data.message,{icon:5});
                        return false;
                    }else{
                        layer.alert('创建成功',function(){
                            location.href='?s=yingxiao&a=dazhuanpan';
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