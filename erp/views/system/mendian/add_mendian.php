<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$dtTime = date("Y-m-d");
$userInfo = array();
$caiwu = array();
if(!empty($id)){
    $kehu = $db->get_row("select * from demo_shops where comId=$id");
    if(!empty($kehu->caiwu)){
        $caiwu = json_decode($kehu->caiwu,true);
    }
}
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
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .redselect .layui-form-checkbox span{color:#ff0101;}
    </style>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div><? if(empty($kehu)){?>添加<? }else{ echo '修改';}?>商家</div>
    </div>
    <div class="cont_switch">
        <ul>
            <li>
                <a href="javascript:"><img src="images/switch_1_pre.gif" /></a>
            </li>
            <li>
                <a href="?s=mendian&a=caiwus&id=<?=$id?>"><img src="images/switch_4.gif" /></a>
            </li>
        </ul>
    </div>
    <div class="cont">
        <form action="?s=mendian&a=add_mendian&tijiao=1&id=<?=$id?>&shenqing_id=<?=$shenqing_id?>" method="post" id="submitForm" class="layui-form">
            <input type="hidden" name="url" value="<?=urlencode($request['returnurl'])?>">
            <div class="provider_cont">
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
    <div id="myModal" class="reveal-modal">
      <div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>
    </div>
    <script type="text/javascript">
      layui.use(['laydate','form','upload'], function(){
        var form = layui.form,
        upload = layui.upload,
        laydate = layui.laydate;
        upload.render({
            elem: '#upload1'
            ,url: '?m=system&s=upload&a=upload'
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_zhizhao").val(res.url);
                    $("#img_zhizhao_img").attr('src',res.url).parent().show().attr("href",res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
        });
        upload.render({
            elem: '#upload2'
            ,url: '?m=system&s=upload&a=upload'
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_shenfenzheng").val(res.url);
                    $("#img_shenfenzheng_img").attr('src',res.url).parent().show().attr("href",res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
        });
        upload.render({
            elem: '#upload3'
            ,url: '?m=system&s=upload&a=upload&limit_width=no'
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_xingxiang").val(res.url);
                    $("#img_xingxiang_img").attr('src',res.url).parent().show().attr("href",res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
        });
        upload.render({
            elem: '#upload_wxkefu1'
            ,url: '?m=system&s=upload&a=upload&limit_width=no'
            ,size:500
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_wxkefu1").val(res.url);
                    $("#upload_wxkefu1").attr('src',res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
        });
        upload.render({
            elem: '#upload_wxkefu2'
            ,url: '?m=system&s=upload&a=upload&limit_width=no'
            ,size:500
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_wxkefu2").val(res.url);
                    $("#upload_wxkefu2").attr('src',res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
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
                            $("#shiId").val(id);
                        }else{
                            $("#psarea").val(id);
                            $("#shiId").val(id);
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
        form.on('submit(tijiao)', function(data){
           layer.load();
       });
    });
</script>
<script type="text/javascript" src="js/mendian/add_mendian.js"></script>
</body>
</html>