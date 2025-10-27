<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $admin= $db->get_row("select * from demo_user where id=$id");
    $admin->rid=$db->get_var("select rolesId from roles_group where userId=$id order by id desc limit 1");
}
$roles=$db->get_results("select id,name from roles order by id asc");

$mendians = $db->get_results("select id, title from demo_shequ where 1=1 ");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css">
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 管理员<?=empty($id)?'新增':'编辑'?>
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=adminlist&a=addAdmin&id=<?=$id?>&type=1" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 管理员账号
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="username" <?=$id>0?'readonly':''?> id="username" style="width:300px" value="<?=$admin->username?>" lay-verify="required" placeholder="请输入管理员账号" class="yx_guanggaoadd_01_right_input"/>
                            <font color="red">管理员账号由5-16位字母或数字组成</font>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 管理员密码
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="pwd" id="pwd" style="width:300px" <? if($id==0){ ?> lay-verify="required"<? } ?> value="" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                            <font color="red">密码由5-16位字母或数字组成<?=$id>0?'（填写此处为修改密码）':''?></font>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 管理员姓名
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="name" id="name" style="width:300px" lay-verify="required" value="<?=$admin->name?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 管理员角色
                        </div>
                        <div class="yx_guanggaoadd_01_right" style="width:310px;">
                            <select name="rolesid">
                                <option value="0">请选择管理员角色</option>
                                <?
                                    foreach($roles as $list){
                                ?>
                                <option value="<?=$list->id?>" <?=$list->id==$admin->rid?'selected':''?>><?=$list->name?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                    <li style="display:none;">
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 所属供应商
                        </div>
                        <div class="yx_guanggaoadd_01_right" style="width:310px;">
                            <select name="mendianId">
                                <option value="0">请选择供应商</option>
                                <?
                                    foreach($mendians as $mendian){
                                        
                                ?>
                                <option value="<?=$mendian->id?>" <?=$mendian->id==$admin->mendianId?'selected':''?>><?=$mendian->title?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            </form>
        </div>
    </div>
<script type="text/javascript" src="js/adminlist/addAdmin.js"></script>
<script>
    $(function(){
        $("#username").change(function(){
            var username=$(this).val();
            $.ajax({
                type:'post',
                url:'/erp_service.php?action=chekadmin',
                data:{username:username},
                dataType:'json',
                success:function(res){
                    if(res.code==0){
                        return layer.msg(res.msg);
                    }
                }
            })
        })
    })
</script>
<? require('views/help.html');?>
</body>
</html>