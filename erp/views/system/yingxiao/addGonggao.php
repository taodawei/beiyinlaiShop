<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if(!empty($id)){
    $gonggao = $db->get_row("select * from dinghuo_gonggao where id=$id and comId=$comId");
}
$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><? echo SITENAME;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="/keditor/kindeditor.js"></script>
</head>
<body>
    <div class="right_up">
        <a href="javascript:history.go(-1);"><img src="images/biao_63.png"/></a> 公告新增
    </div>
    <div class="right_down">
        <div class="yx_guanggaoadd">
            <form method="post" action="?m=system&s=yingxiao&a=addGonggao&id=<?=$id?>" class="layui-form">
            <div class="yx_guanggaoadd_01">
                <ul>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                            <span>*</span> 标题
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <input type="text" name="title" value="<?=$gonggao->title?>" lay-verify="required" placeholder="请输入广告标题" class="yx_guanggaoadd_01_right_input"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 类型
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="type">
                                <option value="1" <? if($gonggao->type==1){?>selected="true"<? }?>>公司公告</option>
                                <option value="2" <? if($gonggao->type==2){?>selected="true"<? }?>>政策发文</option>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 发送对象
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <select name="level">
                                <option value="0">所有客户</option>
                                <?foreach($levels as $l){ ?>
                                <option value="<?=$l->id?>" <? if($l->id==$gonggao->level){?>selected="true"<? }?>><?=$l->title?></option> 
                                <? }?>
                            </select>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="yx_guanggaoadd_01_left">
                           <span>*</span> 内容
                        </div>
                        <div class="yx_guanggaoadd_01_right">
                            <?php
                            ewebeditor(EDITORSTYLE,'content',$gonggao->content,'800');
                            ?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                </ul>
            </div>
            <div class="yx_guanggaoadd_02">
                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
            <input type="hidden" name="originalPic" id="originalPic" value="<?=$banner->originalPic?>">
            </form>
        </div>
    </div>
<script type="text/javascript">
  layui.use(['laydate','form'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    form.on('submit(tijiao)', function(data){
        layer.load();
    });
});
</script>
<? require('views/help.html');?>
</body>
</html>