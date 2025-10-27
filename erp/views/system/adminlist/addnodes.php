<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$nodes=$db->get_row("select * from quanxian where id=$id");
$lists=$db->get_results("select * from quanxian where topid=0 and type=0 order by id asc");
foreach($lists as &$v){
    $v->chlid=$db->get_results("select * from quanxian where topid=".$v->id." and type=0 order by id asc");
    foreach($v->chlid as &$sub){
        $sub->chlid=$db->get_results("select * from quanxian where topid=".$sub->id." and type=0 order by id asc");
        foreach($sub->chlid as &$sub1){
            $sub1->chlid=$db->get_results("select * from quanxian where topid=".$sub1->id." and type=0 order by id asc");
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
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 节点<?=empty($id)?'新增':'编辑'?>
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=adminlist&a=addnodes&id=<?=$id?>&tijiao=1" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 节点名称
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="name" style="width:300px" value="<?=$nodes->name?>" lay-verify="required" placeholder="请输入节点名称" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            节点路由
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="url" style="width:300px" value="<?=$nodes->url?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                            <font color="red">填写节点的URL地址，格式：“?m=system&s=product”</font>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            上级节点
                        </div>
                        <div class="yx_guanggaoadd_01_right" style="width:310px;">
                            <select name="topid">
                                <option value="0">顶级节点</option>
                                <?
                                    foreach($lists as $list){
                                ?>
                                <option value="<?=$list->id?>"><?=$list->name?></option>
                                <?
                                  foreach($list->chlid as $list1){ 
                                ?>
                                <option value="<?=$list1->id?>">&nbsp;&nbsp;&nbsp;--<?=$list1->name?></option>
                                <?
                                  foreach($list1->chlid as $list2){
                                ?>
                                <option value="<?=$list2->id?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?=$list2->name?></option>
                                <?
                                  foreach($list2->chlid as $list3){
                                ?>
                                <option value="<?=$list3->id?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?=$list3->name?></option>
                                <?}}}}?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           节点图标
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="imgurl" style="width:300px" value="<?=$nodes->imgurl?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           图标切换
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="imgon" style="width:300px" value="<?=$nodes->url?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            排序
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="sort" style="width:300px" value="<?=$nodes->sort==""?0:$nodes->sort?>" placeholder="" class="yx_guanggaoadd_01_right_input"/>
                            <font color="red">数字越大越排前</font>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            隐藏
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" <?=$nodes->isshow==1?'checked':''?> name="isshow" value="1"  class="layui-input" title="显示">
                            <input type="radio" name="isshow" <?=$nodes->isshow==0?'checked':''?> value="0" class="layui-input" title="隐藏">
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            操作行为
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="radio" <?=$nodes->type==0?'checked':''?> name="type" value="0"  class="layui-input" title="页面">
                            <input type="radio" name="type" <?=$nodes->type==1?'checked':''?> value="1" class="layui-input" title="操作">
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
    <script type="text/javascript" src="js/adminlist/addnodes.js"></script>
<? require('views/help.html');?>
</body>
</html>