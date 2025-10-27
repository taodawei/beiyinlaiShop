<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $kehu = $db->get_row("select * from demo_kehu where id=$id and comId=$comId");
    if(!empty($kehu->caiwu)){
        $caiwu = json_decode($kehu->caiwu,true);
    }
    $addresss = $db->get_results("select * from demo_kehu_address where kehuId=$kehu->id order by moren desc,id asc");
}
$areaId = (int)$kehu->areaId;
$firstId=0;
$secondId=0;
$thirdId=0;
if($areaId>0){
    $area = $db->get_row("select * from demo_area where id=".$areaId);
    if($area->parentId==0){
        $firstId = $area->id;
    }else{
        $firstId = $area->parentId;
        $secondId = $area->id;
        $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
        if($farea->parentId!=0){
            $firstId = $farea->parentId;
            $secondId = $farea->id;
            $thirdId=$area->id;
        }
    }
}
$areas = $db->get_results("select * from demo_area where parentId=0");
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="styles/selectDparts.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .redselect .layui-form-checkbox span{color:#ff0101;}
    </style>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div><? if(empty($kehu)){?>添加<? }else{ echo '修改';}?><?=$kehu_title?></div>
    </div>
    <div class="cont">
        <form action="?m=system&s=kehu&a=edit&tijiao=1&id=<?=$kehu->id?>" method="post" id="submitForm" class="layui-form">
            <div class="provider_cont">
                <div class="cont_h"> 
                    <?=$kehu_title?>信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="title" id="title" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->title?>" placeholder="请输入<?=$kehu_title?>名称" onblur="checkKehuTitle(<?=(int)$kehu->id?>);"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                <span class="must">*</span><?=$kehu_title?>级别
                            </div>
                            <div class="cont_left_input1">
                                <select name="level" lay-verify="required">
                                    <option value="">请选择级别</option>
                                    <?foreach($levels as $l){ ?>
                                       <option value="<?=$l->id?>" <? if($l->id==$kehu->level){?>selected="true"<? }?>><?=$l->title?></option> 
                                    <? }?>
                                </select>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <?=$kehu_title?>编码
                            </div>
                            <div class="cont_left_input1">
                                <input name="sn" class="layui-input" type="text" value="<?=$kehu->sn?>" placeholder="请输入<?=$kehu_title?>编码"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                <span class="must">*</span>默认发货仓库
                            </div>
                            <div class="cont_left_input1">
                                <select name="storeId" lay-verify="required">
                                    <option value="">请选择仓库</option>
                                    <? foreach($cangkus as $c){ ?>
                                       <option value="<?=$c->id?>" <? if($c->id==$kehu->storeId){?>selected="true"<? }?>><?=$c->title?></option> 
                                    <? }?>
                                </select>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>所在部门
                            </div>
                            <div class="cont_left_input1">
                                <select name="departId" id="departId" lay-verify="required">
                                    <option value="">请选择部门</option>
                                </select>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                业务员
                            </div>
                            <div class="cont_left_input1">
                                <input type="text" name="uname" id="uname" readonly="readonly" value="<?=$kehu->uname?>" class="layui-input" onclick="selectUser();">
                                <input type="hidden" name="userId" id="userId" value="<?=$kehu->userId?>">
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                签约时间
                            </div>
                            <div class="cont_left_input1 sprukulist_01" style="border:#dfdfdf 1px solid;margin:0px;top:0px">
                                <div class="sprukulist_01_left">
                                    <span id="s_time1"><?=empty($kehu->startTime)?'选择日期':$kehu->startTime?></span> <span>~</span> <span id="s_time2"><?=empty($kehu->endTime)?'选择日期':$kehu->endTime?></span>
                                </div>
                                <div class="sprukulist_01_right">
                                    <img src="images/biao_76.png"/>
                                </div>
                                <div class="clearBoth"></div>
                                <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:9;">
                                    <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                邮编
                            </div>
                            <div class="cont_left_input1">
                                <input name="youbian" class="layui-input" type="text" value="<?=$kehu->youbian?>" placeholder="请输入编码" style="width:150px;margin-right:20px;display:inline-block;"/>
                                传真<input name="chuanzhen" class="layui-input" type="text" value="<?=$kehu->chuanzhen?>" placeholder="请输入传真" style="width:150px;display:inline-block;margin-left:10px;"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                区域
                            </div>
                            <div class="cont_left_input1">
                                <div style="width:32%;display:inline-block;">
                                    <input type="hidden" name="psarea" id="psarea" value="<?=$kehu->areaId?>" />
                                    <select id="ps1" lay-filter="ps1">
                                        <option value="">选择省份</option>
                                        <?if(!empty($areas)){
                                            foreach ($areas as $hangye) {
                                                ?><option value="<?=$hangye->id?>" <?=($hangye->id==$firstId?'selected="selected"':'')?>><?=$hangye->title?></option><?
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps2" lay-filter="ps2"><option value="">请先选择省</option>
                                        <?
                                        if($firstId>0){
                                            $areas = $db->get_results("select id,title from demo_area where parentId=$firstId");
                                            if(!empty($areas)){
                                                foreach ($areas as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$secondId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>
                                        <? if($secondId>0){
                                            $areas = $db->get_results("select id,title from demo_area where parentId=$secondId");
                                            if(!empty($areas)){
                                                foreach ($areas as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$thirdId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                详细地址
                            </div>
                            <div class="cont_left_input1">
                                <input name="address" class="layui-input" type="text" value="<?=$kehu->address?>" placeholder="请输入详细地址"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                物流编码
                            </div>
                            <div class="cont_left_input1">
                                <input name="wuliuCode" class="layui-input" type="text" value="<?=$kehu->wuliuCode?>" placeholder="请输入物流编码"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                备用信息
                            </div>
                            <div class="cont_left_input1">
                                <input name="beizhu" class="layui-input" type="text" value="<?=$kehu->beizhu?>" maxlength="200" placeholder="请输入备用信息"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
                <div class="cont_h"> 
                    个人信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                <span class="must">*</span>姓名
                            </div>
                            <div class="cont_left_input1">
                                <input name="name" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->name?>" placeholder="请输入姓名"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>手机
                            </div>
                            <div class="cont_left_input1">
                                <input name="phone" class="layui-input" lay-verify="required|phone" type="text" value="<?=$kehu->phone?>" placeholder="请输入手机"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                电话
                            </div>
                            <div class="cont_left_input1">
                                <input name="phone1" class="layui-input" type="text" value="<?=$kehu->phone1?>" placeholder="请输入电话"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                E-mail
                            </div>
                            <div class="cont_left_input1">
                                <input name="email" class="layui-input" lay-verify="email" type="text" value="<?=$kehu->email?>" placeholder="请输入Email"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>

                    </tr>
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                职位
                            </div>
                            <div class="cont_left_input1">
                                <input name="job" class="layui-input" type="text" value="<?=$kehu->job?>" placeholder="请输入职位"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                QQ
                            </div>
                            <div class="cont_left_input1">
                                <input name="qq" class="layui-input" type="text" value="<?=$kehu->qq?>" placeholder="请输入QQ"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
                <div class="cont_h redselect"> 
                    帐号信息<? if($kehu->status==0){?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="status" title="开通订货帐号" lay-skin="primary" lay-filter="status"><span style="color:#a0a0a0; font-size:13px;">（开通订货账号，<?=$kehu_title?>才能进入知商订货系统订货） </span><? }?>
                </div>
                <? if($kehu->status==0){?>
                <div class="kh_addkehu_1_down">
                    <ul>
                        <li>
                            <div class="kh_addkehu_1_down_title">
                                <span></span> 帐号
                            </div>
                            <div class="kh_addkehu_1_down_right">
                                <div class="kh_addkehu_1_down_right1">
                                    <input type="text" name="username" id="username" onblur="checkUsername();" maxlength="20" class="layui-input disabled" readonly="true">
                                </div>
                            </div>
                            <div class="kh_addkehu_1_down_right" id="yz_username">
                                账号为4-20位英文字母与数字组合！
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="kh_addkehu_1_down_title">
                                <span></span> 密码
                            </div>
                            <div class="kh_addkehu_1_down_right">
                                <div class="kh_addkehu_1_down_right1">
                                    <input type="password" name="password" onchange="checkUsername();" id="password" class="layui-input disabled" readonly="true">
                                </div>
                            </div>
                            <div class="kh_addkehu_1_down_right">
                                <div class="mimaguize">
                                    <div class="mimaguize_01" style="display:none" id="yz_password_qd">
                                        <span style="background-color:#ff8181;">弱</span><span style="background-color:#e1e1e1;">中</span><span style="background-color:#e1e1e1;">强</span>
                                    </div>
                                    <div class="mimaguize_02" id="yz_password">
                                        密码为6-16位英文字母、数字、下划线组合！
                                    </div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="kh_addkehu_1_down_title">
                                <span></span> 确认密码
                            </div>
                            <div class="kh_addkehu_1_down_right">
                                <div class="kh_addkehu_1_down_right1">
                                    <input type="password" name="repass" id="repass" class="layui-input disabled" onblur="checkRepass();" readonly="true">
                                </div>
                            </div>
                            <div class="kh_addkehu_1_down_right" id="yz_repass">
                                
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <? }else{?>
                <div class="kh_bianjikehu_1_down">
                    <ul>
                        <li>
                            <div class="kh_bianjikehu_1_down_left">
                                <span>*</span> 帐号
                            </div>
                            <div class="kh_bianjikehu_1_down_right">
                               <input class="layui-input disabled" name="username" value="<?=$kehu->username?>" readonly="true">
                               <div class="kh_bianjikehu_zhanghao">
                                    <a href="javascript:" onclick="repass(<?=$kehu->id?>,'<?=$kehu->username?>');" class="kh_bianjikehu_zhanghao_mima">重置密码</a><? if($kehu->linkPhone==1){?><a href="javascript:" onclick="jiebang(<?=$kehu->id?>,this);" class="kh_bianjikehu_zhanghao_jiebangshouji">解绑手机</a><? }?>
                               </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <div class="clearBoth"></div>
                        <li>
                            <div class="kh_bianjikehu_1_down_left">
                                <span>*</span>  帐号状态
                            </div>
                            <div class="kh_bianjikehu_1_down_right">
                               <input type="radio" name="status" value="1" title="开通" <? if($kehu->status==1){?>checked="true"<? }?>><input type="radio" type="radio" name="status" value="-1" title="禁用" <? if($kehu->status==-1){?>checked="true"<? }?>>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <? }?>
                <div class="cont_h"> 
                    财务信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                发票抬头
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[taitou]" class="layui-input" type="text" value="<?=$caiwu['taitou']?>" placeholder="请输入发票抬头"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                纳税人识别号
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[shibie]" class="layui-input" type="text" value="<?=$caiwu['shibie']?>" placeholder="请输入纳税人识别号"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>

                    </tr>
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                地址
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[address]" class="layui-input" type="text" value="<?=$caiwu['address']?>" placeholder="请输入地址"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                电话
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[phone]" class="layui-input" type="text" value="<?=$caiwu['phone']?>" placeholder="请输入电话"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                开户名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[kaihuming]" class="layui-input" type="text" value="<?=$caiwu['kaihuming']?>" placeholder="请输入开户名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                开户银行
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[kaihuhang]" class="layui-input" type="text" value="<?=$caiwu['kaihuhang']?>" placeholder="请输入开户银行"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                银行账号
                            </div>
                            <div class="cont_left_input1">
                                <input name="caiwu[kaihubank]" class="layui-input" type="text" value="<?=$caiwu['kaihubank']?>" placeholder="请输入银行账号"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
                <? if(!empty($addresss)){?>
                <div class="kh_bianjikehu_3" style="padding-bottom:30px;">
                    <div class="cont_h">
                        收货地址
                    </div>
                    <div class="kh_bianjikehu_3_down" style="padding-left:4%;line-height:40px;font-size:13px;color:#555">
                        <?
                        foreach ($addresss as $a) {
                            ?>
                            <?=$a->title?>　　　　　<?=$a->name?>　　　　　<?=$a->phone?>　　　　　　<?=$a->areaName.$a->address?>  <? if($a->moren==1){?><span>（默认）</span><? }?><br>
                            <?
                        }
                        ?>
                    </div>
                </div>
                <? }?>
            </div>
            <div class="purchase_affirm3">
                <div class="relevance_affirm">
                    <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                    <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
                </div>
                <div class="clearBoth"></div>
            </div>
            <input type="hidden" name="startTime" id="startTime" value="<?=$kehu->startTime?>">
            <input type="hidden" name="endTime" id="endTime" value="<?=$kehu->endTime?>">
        </form>
    </div>
    <div id="myModal" class="reveal-modal">
      <div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>
    </div>
    <script type="text/javascript">
      layui.use(['laydate','form'], function(){
        var form = layui.form,
        laydate = layui.laydate;
        laydate.render({
            elem: '#riqi1'
            ,show: true
            ,position: 'static'
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
            ,btns: ['confirm']
            ,done: function(value, date, endDate){
                $("#s_time2").html(value);
                $("#endTime").val(value);
            }
        });
        $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
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
                            $("#psarea").val(id);
                        }else{
                            $("#psarea").val(id);
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
                            $("#psarea").val(id);
                        }else{
                            $("#psarea").val(id);
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
        $.ajax({
            type: "POST",
            url: "/erp_service.php?action=getComDeparts",
            data: "pid=<?=$kehu->departId?>",
            dataType:"text",
            timeout : 30000,
            success: function(resdata){
                $("#departId").html(resdata);
                form.render('select');
            },
            error: function() {
                layer.msg('数据请求失败', {icon: 5});
            }
        });
        form.on('checkbox(status)',function(data){
            if(data.elem.checked){
                $(".kh_addkehu_1_down .kh_addkehu_1_down_title span").html('*');
                $("#username").attr("lay-verify",'required|username').removeAttr('readonly').removeClass('disabled');
                $("#password").attr("lay-verify",'required|password').removeAttr('readonly').removeClass('disabled');
                $("#repass").attr("lay-verify",'required|repass').removeAttr('readonly').removeClass('disabled');
            }else{
                $(".kh_addkehu_1_down .kh_addkehu_1_down_title span").html('');
                $("#username").removeAttr("lay-verify").prop('readonly',true).addClass('disabled');
                $("#password").removeAttr("lay-verify").prop('readonly',true).addClass('disabled');
                $("#repass").removeAttr("lay-verify").prop('readonly',true).addClass('disabled');
            }
        });
        form.verify({
            username:function(value,item){
                if(value.length<4||!isNaN(value)){
                    return '用户名必须是4-20位的字母与数字的组合！';
                }
            },
            password:function(value,item){
                if(value.length<6||value.length>16){
                    return '密码长度必须在6-16之间！';
                }
                var reg2=/^\w{6,16}$/;
                if(!value.match(reg2)){
                    return '密码必须是6-16位英文字母、数字、下划线的组合！';
                }
            },
            repass:function(value,item){
                if(value!=$("#password").val()){
                    return '密码与确认密码不一致！';
                }
            }
        });
        form.on('select(ps3)',function(data){
            if(!isNaN(data.value)){
                $("#psarea").val(data.value);
            }
        });
        form.on('submit(tijiao)', function(data){
           layer.load();
       });
    });
</script>
<script type="text/javascript" src="js/kehu_edit.js"></script>
<? require('views/help.html');?>
</body>
</html>