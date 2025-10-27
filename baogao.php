<?php
session_start();
error_reporting(E_ERROR);
header('Content-Type: text/html; charset=utf-8');
$dirName=dirname(__FILE__);
$shlConfig=$dirName.'/config/dt-config.php';
require($shlConfig);
function_exists('date_default_timezone_set') && @date_default_timezone_set('Etc/GMT-'.TIMEZONENAME);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$_REQUEST = cleanArrayForMysql($_REQUEST);
$request  = $_REQUEST;
$id = (int)$request['id'];
$baogao = $db->get_row("select jiance_name,jiance_content from zhuisu_pdt where id=$id");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<title>检测报告</title>
<link href="/skins/default/styles/common.css" rel="stylesheet" type="text/css">
<link href="/skins/default/styles/zhuisu.css" rel="stylesheet" type="text/css">
<script>
	var win = document.getElementsByTagName('html')[0];
	window.onload=(function(){
		var html = document.documentElement;
		var htmlWidth = html.getBoundingClientRect().width;
        if(htmlWidth>960){
            htmlWidth = 960;
        }
		html.style.fontSize = htmlWidth/18+"px";
	})()
	window.onresize = function() {
		var html = document.documentElement;
		var htmlWidth = html.getBoundingClientRect().width;
        if(htmlWidth>960){
            htmlWidth = 960;
        }
		html.style.fontSize = htmlWidth/18+"px";
	}
</script>
<style type="text/css">
    .zhuisu img{max-width:100%;height:auto;}
</style>
</head>
<body style="background:none">
<div class="zhuisu" style="padding-top:0px;">
    <?=$baogao->jiance_content?>
</div>
</body>
</html>
