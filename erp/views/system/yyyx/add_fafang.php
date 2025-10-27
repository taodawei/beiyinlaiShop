<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$allRows = array(
    "nickname"=>array("title"=>"姓名","rowCode"=>"{field:'nickname',title:'姓名',width:150}"),
    "username"=>array("title"=>"手机号","rowCode"=>"{field:'username',title:'手机号',width:180}"),
    "level"=>array("title"=>"会员等级","rowCode"=>"{field:'level',title:'会员等级',width:100,sort:true}"),
    "renzheng"=>array("title"=>"认证状态","rowCode"=>"{field:'renzheng',title:'认证状态',width:100}"),
    "mendian"=>array("title"=>"所属门店","rowCode"=>"{field:'mendian',title:'所属门店',width:200}"),
    "money"=>array("title"=>"余额","rowCode"=>"{field:'money',title:'余额',width:150,sort:true}"),
    "jifen"=>array("title"=>"积分","rowCode"=>"{field:'jifen',title:'积分',width:150,sort:true}"),
    "yhq"=>array("title"=>"优惠券","rowCode"=>"{field:'yhq',title:'优惠券',width:80}"),
    "gift_card"=>array("title"=>"礼品卡","rowCode"=>"{field:'gift_card',title:'礼品卡',width:150}"),
    "cost"=>array("title"=>"累计消费","rowCode"=>"{field:'cost',title:'累计消费',width:200}"),
    "lastLogin"=>array("title"=>"最后登录时间","rowCode"=>"{field:'lastLogin',title:'最后登录时间',width:180}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0, sort: true,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
    $rowsJS.=','.$isshow['rowCode'];
}
$levels = $db->get_results("select id,title from user_level where comId=$comId order by jifen asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId order by id asc");
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
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div class="mendianguanli">	
		<div class="mendianguanli_up">
        	<a href="javascript:history.go(-1);"><img src="images/users_39.png" alt=""/></a> 发放赠送券
        </div>
        <div class="mendianguanli_down">
            <form action="?s=yyyx&a=add_fafang&submit=1&type=<?=$type?>&id=<?=$id?>" method="post" id="tijiaoForm" class="layui-form">
                <input type="hidden" name="areaIds" id="areaIds" value="">
                <input type="hidden" name="userIds" id="selectedIds" value="">
                <input type="hidden" name="url" value="<?=urlencode($request['returnurl'])?>">
                <div class="zsq_addfafangmingxi">
                    <div class="zsq_addfafangmingxi_1">
                        <div class="zsq_addfafangmingxi_1_left">
                            <span>*</span> 选择区域:
                        </div>
                        <div class="zsq_addfafangmingxi_1_right">
                            <div class="addshengriquan_left_03_right_quyu">
                                <div class="addshengriquan_left_03_right_quyu_1">
                                    <input type="radio" name="if_area" lay-filter="area1" value="0" <? if(empty($yhq->areaIds)){?>checked="true"<? }?> title="全部地区"><br>
                                    <input type="radio" name="if_area" lay-filter="area2" value="1" <? if(!empty($yhq->areaIds)){?>checked="true"<? }?> title="指定地区">
                                </div>
                                <div class="addshengriquan_left_03_right_quyu_2" id="area_div" <? if(empty($yhq->areaIds)){echo 'style="display:none;"';}?>>
                                    <input type="text" id="areaIdsFanwei" placeholder="选择区域" onclick="area_fanwei('areaIds');" readonly="true" value="<?=empty($yhq->areaIds)?'':$db->get_var("select group_concat(title) from demo_area where id in($yhq->areaIds)");?>"/>
                                    <div class="clearBoth"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="zsq_addfafangmingxi_2">
                        <div class="zsq_addfafangmingxi_1_left">
                            <span>*</span> 发放会员：
                        </div>
                        <div class="addshengriquan_left_03_right">
                            <div class="addshengriquan_left_03_right_huiyuan">
                                <input type="radio" name="type" lay-filter="level1" value="1" checked="true" title="全部会员"><br>
                                <input type="radio" name="type" lay-filter="level2" value="2" title="指定会员级别"><br>
                                <div style="margin-left:10px;margin-top:8px;<? if(empty($yhq->levelIds)){echo 'display:none;';}?>" id="level_div">
                                    <? if(!empty($levels)){
                                        foreach ($levels as $l) {
                                            ?><input type="checkbox" name="levels[]" <? if(in_array($l->id,$levelArry)){?>checked="true"<? }?> value="<?=$l->id?>" lay-skin="primary" title="<?=$l->title?>"><?
                                        }
                                    }?>
                                </div>
                                <input type="radio" name="type" lay-filter="level3" value="3" title="指定会员">      <a href="javascript:" id="check_user" style="position:relative;top:5px;display:none;">+ 选择会员</a>
                                <div class="zsq_addfafangmingxi_2_02_down" id="check_user_div" style="margin-top:10px;display:none;">
                                    <table width="600" border="0" cellpadding="0" cellspacing="0">
                                        <tr height="38">
                                            <td bgcolor="#cee1ea" width="44" align="center" valign="middle">
                                                
                                            </td>
                                            <td bgcolor="#cee1ea" align="center" valign="middle">
                                                昵称
                                            </td>
                                            <td bgcolor="#cee1ea" align="center" valign="middle">
                                                手机号
                                            </td>
                                            <td bgcolor="#cee1ea" align="center" valign="middle">
                                                会员等级
                                            </td>
                                            <td bgcolor="#cee1ea" align="center" valign="middle">
                                                累计消费
                                            </td>
                                            <td bgcolor="#cee1ea" align="center" valign="middle">
                                                最后登录时间
                                            </td>
                                        </tr>
                                        <tr height="34" id="rowTrHeji">
                                            <td colspan="12" bgcolor="#fffbf0" align="left" valign="middle">
                                                <div style="color:#656565; padding-left:30px;">已选<span style="color:#3084d9;" id="selectNum"> 0 </span>会员 </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="zsq_addfafangmingxi_3">
                        <ul>
                            <li>
                                <div class="addshengriquan_left_03_left">
                                    <span>*</span> 生效时间：
                                </div>
                                <div class="addshengriquan_left_03_right">
                                    <div class="addshengriquan_left_03_duixiang">
                                        <input type="text" name="startTime" lay-verify="required" autocomplete="off" id="startTime" value="<?=$yhq->startTime?>"/>
                                    </div>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="addshengriquan_left_03_left">
                                    <span>*</span> 过期时间：
                                </div>
                                <div class="addshengriquan_left_03_right">
                                    <div class="addshengriquan_left_03_duixiang">
                                        <input type="text" name="endTime" lay-verify="required" autocomplete="off" id="endTime" value="<?=$yhq->endTime?>"/>
                                    </div>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <li>
                                <div class="zsq_addfafangmingxi_1_left">
                                    <span>*</span> 每人发放：
                                </div>
                                <div class="zsq_addfafangmingxi_3_right">
                                    <input type="text" name="num" lay-verify="shuzi|required" value="1" /> 张
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                        </ul>
                    </div>
                    <div class="addshengriquan_left_04">
                        <button class="layui-btn" lay-submit="" lay-filter="tijiao">保 存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <input type="hidden" id="departs" value="" />
    <input type="hidden" id="users" value="" />
    <input type="hidden" id="departNames" value=""/>
    <input type="hidden" id="userNames" value="" />
    <input type="hidden" id="editId" value="0">
    <input type="hidden" id="nowIndex" value="">
    <input type="hidden" id="level" value="">
    <input type="hidden" id="mendianId" value="">
    <input type="hidden" id="money_start" value="">
    <input type="hidden" id="money_end" value="">
    <input type="hidden" id="jifen_start" value="">
    <input type="hidden" id="jifen_end" value="">
    <input type="hidden" id="dtTime_start" value="">
    <input type="hidden" id="dtTime_end" value="">
    <input type="hidden" id="login_start" value="">
    <input type="hidden" id="login_end" value="">
    <input type="hidden" id="order1" value="id">
    <input type="hidden" id="order2" value="desc">
    <input type="hidden" id="page" value="1">
    
    <div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
    <div class="reveal-modal-bg" style="display:none; cursor: pointer;"></div>
    <!--选择会员-弹出-->
<div class="zsq_xuanzehuiyuan">
    <div class="zsq_xuanzehuiyuan_1">
        <div class="splist_up">
            <div class="splist_up_01">
                <div class="splist_up_01_left">
                    <div class="splist_up_01_left_01">
                        <div class="splist_up_01_left_02_up">
                            <span>全部等级</span> <img src="images/biao_20.png"/>
                        </div>
                        <div class="splist_up_01_left_02_down">
                            <ul>
                                <li>
                                    <a href="javascript:" onclick="selectLevel(0,'全部等级');" class="splist_up_01_left_02_down_on">全部等级</a>
                                </li>
                                <? if(!empty($levels)){
                                    foreach ($levels as $l) {
                                        ?><li>
                                            <a href="javascript:" onclick="selectLevel(<?=$l->id?>,'<?=$l->title?>');"><?=$l->title?></a>
                                        </li><?
                                    }
                                }?>
                            </ul>
                        </div>
                    </div>
                    <div class="splist_up_01_left_02">
                        <div class="splist_up_01_left_02_up">
                            <span>全部门店</span> <img src="images/biao_20.png"/>
                        </div>
                        <div class="splist_up_01_left_02_down">
                            <ul>
                                <li>
                                    <a href="javascript:" onclick="selectMendian(0,'全部门店');" class="splist_up_01_left_02_down_on">全部门店</a>
                                </li>
                                <? if(!empty($mendians)){
                                    foreach ($mendians as $m) {
                                        ?><li>
                                            <a href="javascript:" onclick="selectMendian(<?=$m->id?>,'<?=$m->title?>');"><?=$m->title?></a>
                                        </li><?
                                    }
                                }?>
                            </ul>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="splist_up_01_right">    
                    <div class="splist_up_01_right_1">
                        <div class="splist_up_01_right_1_left">
                            <input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入姓名/手机号"/>
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
                                                    余额区间
                                                </div>
                                                <div class="gaojisousuo_right">
                                                    <div class="huiyuanlist_gjss_yue">
                                                        <input type="number" name="super_money_start" step="1" value="<?=$money_start?>"> - <input type="number" name="super_money_end" step="1" value="<?=$money_end?>">
                                                    </div>
                                                </div>
                                                <div class="gaojisousuo_left">
                                                    积分区间
                                                </div>
                                                <div class="gaojisousuo_right">
                                                    <div class="huiyuanlist_gjss_yue">
                                                        <input type="number" name="super_jifen_start" step="1" value="<?=$jifen_start?>"> - <input type="number" name="super_jifen_end" step="1" value="<?=$jifen_end?>">
                                                    </div>
                                                </div>
                                                <div class="clearBoth"></div>
                                            </li>
                                            <li>
                                                <div class="gaojisousuo_left">
                                                    注册时间
                                                </div>
                                                <div class="gaojisousuo_right">
                                                    <div class="huiyuanlist_gjss_yue">
                                                        <input type="text" readonly="true" id="time1" name="super_dtTime_start" value="<?=$dtTime_start?>"> - <input type="text" id="time2" readonly="true" name="super_dtTime_end" step="1" value="<?=$dtTime_end?>">
                                                    </div>
                                                </div>
                                                <div class="gaojisousuo_left">
                                                    最近登录
                                                </div>
                                                <div class="gaojisousuo_right">
                                                    <div class="huiyuanlist_gjss_yue">
                                                        <input type="text" readonly="true" id="time3" name="super_login_start" value="<?=$login_start?>"> - <input type="text" id="time2" readonly="true" name="super_login_end" step="1" value="<?=$login_end?>">
                                                    </div>
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
                    <div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="splist_up_02">
                <div class="clearBoth"></div>
            </div>
        </div>
    </div>
    <div class="sprkadd_xuanzesp_02" style="height:450px;">
       <table id="product_list" lay-filter="product_list">
       </table>
   </div>
   <div class="sprkadd_xuanzesp_03">
       <a href="javascript:" id="sprkadd_xuanzesp_03_01" class="sprkadd_xuanzesp_03_01">确  认</a><a href="javascript:hideSearch1();" class="sprkadd_xuanzesp_03_02">取  消</a>
   </div>
</div>
<script type="text/javascript">
var layForm,productListTalbe;
layui.use(['laydate','laypage','table','form'], function(){
    var laydate = layui.laydate
    ,laypage = layui.laypage
    ,table = layui.table
    ,layForm = layui.form
    ,active = {
        appendCheckData: function(){
            var checkStatus = table.checkStatus('product_list')
            ,data = checkStatus.data;
            var ids = $("#selectedIds").val();
            if(data.length>0){
                var num = parseInt($("#selectNum").html());
                for (var i = 0; i < data.length; i++) {
                    var inventoryId = data[i].id;
                    if(ids==''){
                        ids = inventoryId;
                    }else{
                        ids = ids+','+inventoryId;
                    }
                    var nickname = data[i].nickname;
                    var username = data[i].username;
                    var level = data[i].level;
                    var cost = data[i].cost;
                    var lastLogin = data[i].lastLogin;
                    num = num+1;
                    var str = '<tr height="37" id="row_'+inventoryId+'"><td bgcolor="#ffffff" align="center" valign="middle"><a href="javascript:" onclick="delRow('+inventoryId+');"><img src="images/yingxiao_32.png" alt=""></a></td><td bgcolor="#ffffff" align="center" valign="middle">'+nickname+'</td><td bgcolor="#ffffff" align="center" valign="middle">'+username+'</td><td bgcolor="#ffffff" align="center" valign="middle"><div>'+level+'</div></td><td bgcolor="#ffffff" align="center" valign="middle">'+cost+'</td><td bgcolor="#ffffff" align="center" valign="middle">'+lastLogin+'</td></tr>';
                    $("#rowTrHeji").before(str);
                }
                $("#selectedIds").val(ids);
                $("#selectNum").html(num);
                hideSearch1();
            }else{
                hideSearch1();
            }
        }
    }
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "420"
        ,url: '?m=system&s=users&a=getList'
        ,page: true
        ,limit:10
        ,cols: [[<?=$rowsJS?>]]
        ,done: function(res, curr, count){
            $("th[data-field='id']").hide();
            layer.closeAll('loading');
            $("#page").val(curr);
        }
    });
    laydate.render({
        elem: '#time1'
        ,min: '2018-01-01'
        ,max: '<?=date("Y-m-d")?>'
        <?=empty($dtTime_start)?'':",value:'$dtTime_start'"?>
        ,done: function(value, date, endDate){
            $("#time1").html(value);
        }
    });
    laydate.render({
        elem: '#time2'
        ,min: '2018-01-01'
        ,max: '<?=date("Y-m-d")?>'
        <?=empty($dtTime_end)?'':",value:'$dtTime_end'"?>
        ,done: function(value, date, endDate){
            $("#time2").html(value);
        }
    });
    laydate.render({
        elem: '#time3'
        ,min: '2018-01-01'
        ,max: '<?=date("Y-m-d")?>'
        <?=empty($login_start)?'':",value:'$login_start'"?>
        ,done: function(value, date, endDate){
            $("#time3").html(value);
        }
    });
    laydate.render({
        elem: '#time4'
        ,min: '2018-01-01'
        ,max: '<?=date("Y-m-d")?>'
        <?=empty($login_end)?'':",value:'$login_end'"?>
        ,done: function(value, date, endDate){
            $("#time4").html(value);
        }
    });
    laydate.render({
        elem: '#startTime'
        ,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
    });
    laydate.render({
        elem: '#endTime'
        ,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
    });
    layForm.verify({
        shuzi:function(value,item){
            value = parseFloat(value);
            if(isNaN(value)||value<=0){
                return '字段不能小于0';
            }
        }
    });
    layForm.on('radio(area1)',function(){
        $("#area_div").hide();
    });
    layForm.on('radio(area2)',function(){
        $("#area_div").show();
    });
    layForm.on('radio(level1)',function(){
        $("#level_div").hide();
        $("#check_user").hide(50);
        $("#check_user_div").hide(50);
    });
    layForm.on('radio(level2)',function(){
        $("#level_div").show();
        $("#check_user").hide(50);
        $("#check_user_div").hide(50);
    });
    layForm.on('radio(level3)',function(){
        $("#level_div").hide();
        $("#check_user").show(50);
        $("#check_user_div").show(50);
    });
    layForm.on('submit(search)', function(data){
        $("#money_start").val(data.field.super_money_start);
        $("#money_end").val(data.field.super_money_end);
        $("#jifen_start").val(data.field.super_jifen_start);
        $("#jifen_end").val(data.field.super_jifen_end);
        $("#dtTime_start").val(data.field.super_dtTime_start);
        $("#dtTime_end").val(data.field.super_dtTime_end);
        $("#login_start").val(data.field.super_login_start);
        $("#login_end").val(data.field.super_login_end);
        hideSearch();
        reloadTable(0);
        return false;
    });
    layForm.on('submit(quxiao)', function(){
        hideSearch();
        return false;
    });
    layForm.on('submit(tijiao)',function(){
        var beginDate=$("#startTime").val();  
        var endDate=$("#endTime").val();
        var d1 = new Date(beginDate.replace(/\-/g, "\/"));
        var d2 = new Date(endDate.replace(/\-/g, "\/"));
        if(d1 >=d2){
            layer.msg("开始时间不能大于结束时间！",function(){});
            return false;
        }
        layer.load();
    });
    $("#check_user").click(function(){
        $(".zsq_xuanzehuiyuan").css({'top':'0','opacity':'1','visibility':'visible'});
        reloadTable(0);
    });
    $("#sprkadd_xuanzesp_03_01").on("click", function(){
      active['appendCheckData'].call(this);
    });
});
</script>
<script type="text/javascript" src="js/yyyx/add_fafang.js"></script>
<? require('views/help.html');?>
</body>
</html>