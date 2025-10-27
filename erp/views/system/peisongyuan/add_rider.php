<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $rider = $db->get_row("select * from demo_peisong_rider where id=$id");
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
    <link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .redselect .layui-form-checkbox span{color:#ff0101;}
        .cangkugl_xiugai_02_right .layui-select-title input{width:100%}
    </style>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div><? if(empty($rider)){?>添加<? }else{ echo '修改';}?>配送信息</div>
    </div>
    <div class="cont" style="height:100%">
        <form action="?s=peisongyuan&a=add_rider&tijiao=1&id=<?=$id?>" method="post" id="submitForm" class="layui-form">
            <input type="hidden" name="url" value="<?=urlencode($request['returnurl'])?>">
            <div class="provider_cont">
                <div class="cont_h"> 
                    配送信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>配送车型
                            </div>
                            <div class="cont_left_input1">
                                <input name="row1" class="layui-input" lay-verify="required" type="text" value="<?=$rider->row1?>" placeholder="配送车型"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                <span class="must">*</span>物流方式
                            </div>
                            <div class="cont_left_input1">
                                <input name="row2" class="layui-input" lay-verify="required" type="text" value="<?=$rider->row2?>" placeholder="配送车型"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr><tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>配送姓名
                            </div>
                            <div class="cont_left_input1">
                                <input name="name" class="layui-input" lay-verify="required" type="text" value="<?=$rider->name?>" placeholder="配送员姓名"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                <span class="must">*</span>联系电话
                            </div>
                            <div class="cont_left_input1">
                                <input name="phone" class="layui-input" lay-verify="required|phone" type="text" value="<?=$rider->phone?>" placeholder="请输入联系电话"/>
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
    layui.use(['laydate','form','upload'], function(){
        var form = layui.form,
        upload = layui.upload,
        laydate = layui.laydate;
        form.on('submit(tijiao)', function(data){
           layer.load();
        });
    });
</script>
</body>
</html>