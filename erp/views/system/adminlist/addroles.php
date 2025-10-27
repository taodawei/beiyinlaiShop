<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $admin= $db->get_row("select name,roles,mark,dtTime from roles as a,roles_group as b where b.rolesId=a.id and a.id=$id");
    $quanxian=explode(',',$admin->roles);
}
$lists=$db->get_results("select * from quanxian where topid=0 and isshow=1 order by id asc");
foreach($lists as &$v){
    $v->chlid=$db->get_results("select * from quanxian where topid=".$v->id." and isshow=1 order by id asc");
    foreach($v->chlid as &$sub){
        $sub->chlid=$db->get_results("select * from quanxian where topid=".$sub->id." and isshow=1 order by type asc,id asc");
        foreach($sub->chlid as &$sub1){
        $sub1->chlid=$db->get_results("select * from quanxian where topid=".$sub1->id." and isshow=1 order by type asc, id asc");
    }
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
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css">
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        #quanxian dl{border:1px solid #eee;}
        #quanxian dl dt{background:#d7ebf5;padding:10px 20px;}
        #quanxian .layui-form-checkbox i{width:20px;font-size:16px;right:2px;}
        #quanxian .layui-form-checkbox{width:20px;height:20px;line-height:20px;padding-right:0;margin-right:5px;}
        #quanxian dl dd{padding:20px 40px;}
        #quanxian dl dd ul{display:flex;flex-wrap:wrap;}
        #quanxian dl dd ul li{margin: 0 10px;width:100%;border-top:1px solid #ccc;padding-top:15px;padding-bottom:15px;}
        #quanxian dl dd ul>li:first-child{border-top:0;}
        #quanxian dl dd ul li div.p1{width:120px;float:left;font-weight:bold;}
        #quanxian dl dd ul li div.p2{display:flex;flex-wrap:wrap;margin-left:120px;}
        #quanxian dl dd ul li div.p3.duo{display:flex;flex-wrap:wrap;}
        #quanxian dl dd ul li div.p3.da >label{font-weight:bold;margin-right:15px;}
        #quanxian dl dd ul li div.p3{margin: 0 10px 5px;}
        #quanxian dl dd ul li div.p3>label{margin-top:5px;}
        #quanxian dl dd ul li div.p4{margin-top:5px;}
        #quanxian dl dd ul li div.p3.duo div.p4{margin-right:15px;}
    </style>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 角色<?=empty($id)?'新增':'编辑'?>
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=adminlist&a=addroles&id=<?=$id?>&type=1" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 角色名称
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="name" style="width:300px" value="<?=$admin->name?>" lay-verify="required" placeholder="请输入角色名称" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            角色备注
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="mark" style="width:300px" value="<?=$admin->mark?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="right_up" style="border-bottom:1px solid #eee;margin-top:20px;font-weight:bold;">权限设置</div>
            <div class="yx_guanggaoadd_01" style="padding:20px 0;" id="quanxian">
                <? 
                   foreach($lists as $list){
                ?>
                <dl>
                    <dt class="dt"><label><input type="checkbox" <? if(in_array($list->id,$quanxian)){ ?> checked="true" <? } ?> value="<?=$list->id?>" name="quanxian1"><?=$list->name?></label></dt>
                    <dd>
                        <ul>
                            <? 
                               foreach($list->chlid as $list1){
                            ?>
                            <li>
                                <div class="p1" <? if(count($list1->chlid)==0){ ?>style="font-weight:normal;"<? } ?>><label><input type="checkbox" <? if(in_array($list1->id,$quanxian)){ ?> checked="true" <? } ?> value="<?=$list1->id?>" name="quanxian1"><?=$list1->name?></label>
                                </div>
                                <div class="p2">
                                    <? 
                                       foreach($list1->chlid as $list2){
                                    ?>
                                    <div class="p3<?=count($list1->chlid)<=1?' duo':''?><?=count($list2->chlid)>0?' da':''?>"><label><input type="checkbox" <? if(in_array($list2->id,$quanxian)){ ?> checked="true" <? } ?> value="<?=$list2->id?>" name="quanxian1"><?=$list2->name?></label>
                                        <? 
                                           foreach($list2->chlid as $list3){
                                        ?>
                                        <div class="p4"><label><input type="checkbox" <? if(in_array($list3->id,$quanxian)){ ?> checked="true" <? } ?> value="<?=$list3->id?>" name="quanxian1"><?=$list3->name?></label></div>
                                        <?
                                        }
                                        ?>
                                    </div>
                                   <?
                                        }
                                    ?>
                                </div>
                            </li>
                            <?
                                }
                            ?>
                        </ul>
                    </dd>
                </dl>
                <?
                    }
                ?>
            </div>
            <input type="hidden" name="ids" value="<?=$id?>" id="ids" />
            <input type="hidden" name="quanxian2" value="<?=$admin->roles?>" id="quanxian2" />
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            </form>
        </div>
    </div>
    <script>
        $(function(){
            $("input:checkbox[name='quanxian1']").click(function(){
                var erji=$(this).parent().parent().attr("class");
                console.log(erji);
                let arr=[];
                if($("#quanxian2").val()!=""){
                    arr=$("#quanxian2").val().split(",");
                }
                if($(this).is(":checked")){
                    $(this).prop("checked",false);
                    var index = arr.indexOf($(this).val());
                    if (index > -1) {
                        arr.splice(index, 1);
                    }
                }else{
                    $(this).prop("checked",true);
                    arr.push($(this).val());
                }
                var ediv="";
                if(erji=="dt"){
                    ediv="dd";
                }else if(erji=="p1"){
                    ediv="div.p2";
                }else if(erji=="p3"||erji=="p3 duo"||erji=="p3 da"){
                    ediv="div.p4";
                    $(this).parent().parent().find(ediv+" input:checkbox").prop("checked",$(this).prop("checked"));
                    if($(this).is(":checked")){
                        $(this).parent().parent().find(ediv+" .layui-unselect.layui-form-checkbox").addClass("layui-form-checked");
                        $(this).parent().parent().find(ediv+" input:checkbox").each(function(index,item){
                            var index = arr.indexOf($(this).val());
                            if(index<0){
                                arr.push($(this).val());
                            }
                        })
                    }else{
                        $(this).parent().parent().find(ediv+" .layui-unselect.layui-form-checkbox").removeClass("layui-form-checked");
                        $(this).parent().parent().find(ediv+" input:checkbox").each(function(index,item){
                            var index = arr.indexOf($(this).val());
                            if (index > -1) {
                                arr.splice(index, 1);
                            }
                        })
                    }
                    $("#quanxian2").val(arr.join());
                    return false;
                }else if(erji=="p3 duo da"){
                    ediv="div.p3";
                }else{
                    ediv=$(this).parent().attr("class");
                }
                $(this).parent().parent().parent().find(ediv+" input:checkbox").prop("checked",$(this).prop("checked"));
                if($(this).is(":checked")){
                    $(this).parent().parent().parent().find(ediv+" .layui-unselect.layui-form-checkbox").addClass("layui-form-checked");
                    $(this).parent().parent().parent().find(ediv+" input:checkbox").each(function(index,item){
                        var index = arr.indexOf($(this).val());
                        if(index<0){
                            arr.push($(this).val());
                        }
                    })
                }else{
                    $(this).parent().parent().parent().find(ediv+" .layui-unselect.layui-form-checkbox").removeClass("layui-form-checked");
                    $(this).parent().parent().parent().find(ediv+" input:checkbox").each(function(index,item){
                        var index = arr.indexOf($(this).val());
                        if (index > -1) {
                            arr.splice(index, 1);
                        }
                    })
                }
                $("#quanxian2").val(arr.join());
            })
        })
    </script>
<script type="text/javascript" src="js/adminlist/addAdmin.js"></script>
<? require('views/help.html');?>
</body>
</html>