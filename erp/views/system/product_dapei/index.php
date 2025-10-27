<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$status = (int)$request['status'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>商品组合推荐管理</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/yingxiaoguanli.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="root">
        <div class="video">
            <div class="video_h">
                商品组合推荐(链接地址：/index.php?p=24)
            </div>
            <div class="video_fenlei">
                <ul>
                    <li class="video_fenlei_pre">
                        <a href="?s=product_dapei">组合列表</a>
                    </li>
                    <li>
                        <a href="?s=product_dapei&a=addShipin">新建</a>
                    </li>

                </ul>
                <div class="clearBoth"></div>
            </div>
            <div class="video_search">
                <input type="text" id="keyword" placeholder="组合名称" class="video_search_input" />
                <a href="javascript:" onclick="render_pdt_list();"><img src="images/search.png" /></a>
            </div>
            <div class="video_cont">
                <ul id="fankui_list"></ul>
                <div class="clearBoth"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="page" value="<?=$page?>">
    <input type="hidden" id="status" value="<?=$status?>">
    <script type="text/javascript" src="js/product_dapei/index.js"></script>
</body>
</html>