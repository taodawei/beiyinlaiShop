<?php
$comId = (int)$_SESSION['demo_comId'];
if(empty($comId))$comId=(int)$_REQUEST['com_id'];
if(empty($comId))$comId=(int)$_REQUEST['comId'];
if(empty($comId))$comId=(int)$_REQUEST['shopId'];
define('HTTP_URL','');

// define('DB_HOSTNAME','127.0.0.1');
define('DB_HOSTNAME','localhost:3306');
// define('DB_USER','fbsc1');
// define('DB_PASSWORD','fbsc1');
define('DB_USER','root');
define('DB_PASSWORD','root');
define('DB_DBNAME','fbsc1');
define('TB_PREFIX','demo_');
define('SITENAME','武汉贝茵莱');
define('SITEKEYWORDS','');
define('SITESUMMARY','');

define('UPLOADPATH','/upload/');
define('URLREWRITE',0);
// define('EDITORSTYLE','kindeditor');
define('EDITORSTYLE','ueditor');//kindeditor
define('ABSPATH',dirname(__FILE__).'/../');
define('ROOTPATH','');
define('VERSION','1.0');

define('FILEDISK','oss');  //oss|local
define('OSS_KEYID','LTAI5tGoUQzcPa17Cn3BAbg5');
define('OSS_KEYSECRET','mXE4WHD4Af3hpFOeAL6LA8EoLM3Ikw');
define('OSS_ENDPOINT','http://oss-cn-nanjing.aliyuncs.com');
define('OSS_BUCKET','bio-swamp');
define('OSS_BUCKET_URL','https://bio-swamp.oss-cn-nanjing.aliyuncs.com/');

$fileIndex 	= 'index.html';
$fileCommon = 'common.html';
date_default_timezone_set("Asia/Shanghai");
function get_root_path()
{
	return ROOTPATH;
}
?>