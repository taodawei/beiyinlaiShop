<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$gonggao = $db->get_row("select * from dinghuo_gonggao where id=$id and comId=$comId");
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
        <a href="<?=urldecode($request['url'])?>"><img src="images/biao_63.png"/></a> 公告详情
    </div>
    <div class="right_down">
        <div class="yx_tongzhixiangqing">   
            <div class="yx_tongzhixiangqing_01">
                <h2><?=$gonggao->title?></h2>
                <span>时间：<?=date("Y-m-d H:i",strtotime($gonggao->dtTime))?></span>
            </div>
            <div class="yx_tongzhixiangqing_02">
                <?=$gonggao->content?>
            </div>
        </div>
    </div>
</body>
</html>