<?
global $db,$request;
$id = (int)$request['id'];
if(!empty($id)){
    $supplier = $db->get_row("select * from demo_supplier where id=$id");
}
$areaId = (int)$supplier->areaId;
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
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div>添加供应商</div>
    </div>
    <div class="cont">
        <form action="?m=system&s=supplier&a=add&tijiao=1&id=<?=$id?>" method="post" id="submitForm" class="layui-form">
            <div class="provider_cont">
                <div class="cont_h"> 
                    供应商信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="title" class="layui-input" lay-verify="required" type="text" value="<?=$supplier->title?>" placeholder="请输入供应商名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                编码
                            </div>
                            <div class="cont_left_input1">
                                <input name="sn" class="layui-input" type="text" value="<?=$supplier->sn?>" placeholder="请输入供应商编码"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>

                    </tr>
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                区域
                            </div>
                            <div class="cont_left_input1">
                                <div style="width:32%;display:inline-block;">
                                    <input type="hidden" name="psarea" id="psarea" value="<?=$supplier->areaId?>" />
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
                                <input name="address" class="layui-input" type="text" value="<?=$supplier->address?>" placeholder="请输入详细地址"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                <span class="must">*</span>状态
                            </div>
                            <div class="add_pur_way" id="purchase_choice4">
                                <input type="radio" name="status" value="1" title="启用" <? if(empty($supplier)||$supplier->status==1){?>checked<? }?>>
                                <input type="radio" name="status" value="-1" title="禁用" <? if(!empty($supplier)&&$supplier->status==-1){?>checked<? }?>>
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
                                <input name="name" class="layui-input" lay-verify="required" type="text" value="<?=$supplier->name?>" placeholder="请输入姓名"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>手机
                            </div>
                            <div class="cont_left_input1">
                                <input name="phone" class="layui-input" lay-verify="required|phone" type="text" value="<?=$supplier->phone?>" placeholder="请输入手机"/>
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
                                <input name="phone1" class="layui-input" type="text" value="<?=$supplier->phone1?>" placeholder="请输入电话"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                E-mail
                            </div>
                            <div class="cont_left_input1">
                                <input name="email" class="layui-input" lay-verify="email" type="text" value="<?=$supplier->email?>" placeholder="请输入Email"/>
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
                                <input name="position" class="layui-input" type="text" value="<?=$supplier->position?>" placeholder="请输入职位"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
                <div class="cont_h"> 
                    财务信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                开户名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="kaihu_title" class="layui-input" type="text" value="<?=$supplier->kaihu_title?>" placeholder="请输入开户名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                开户银行
                            </div>
                            <div class="cont_left_input1">
                                <input name="kaihu_bank" class="layui-input" type="text" value="<?=$supplier->kaihu_bank?>" placeholder="请输入开户银行"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>

                    </tr>
                    <tr >
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                银行账户
                            </div>
                            <div class="cont_left_input1">
                                <input name="kaihu_user" class="layui-input" type="text" value="<?=$supplier->kaihu_user?>" placeholder="请输入银行账户"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                发票抬头
                            </div>
                            <div class="cont_left_input1">
                                <input name="kaihu_fapiao" class="layui-input" type="text" value="<?=$supplier->kaihu_fapiao?>" placeholder="请输入发票抬头"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
                <div class="cont_h"> 
                    其他信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                备注
                            </div>
                            <div class="cont_left_input1">
                                <textarea name="beizhu" class="layui-textarea" placeholder="请输入备注"><?=$supplier->beizhu?></textarea>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="purchase_affirm3">
                <div class="relevance_affirm">
                    <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                    <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
                </div>
                <div class="clearBoth"></div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
      layui.use(['form'], function(){
        var form = layui.form
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
<? require('views/help.html');?>
</body>
</html>