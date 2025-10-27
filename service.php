<?php
ini_set('date.timezone','Asia/Shanghai');
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials:true');
header("Access-Control-Allow-Headers:X-PINGOTHER,X-Requested-With,Content-Type,Accept");
header("Access-Control-Allow-Methods:GET,POST");
error_reporting(E_ERROR);

require_once __DIR__ . '/vendor/autoload.php';
define('ABSPATH',dirname(__FILE__));
global $db,$request,$comId;
$request = cleanArrayForMysql($_REQUEST);
$comId = 888;

$db = new \Workerman\MySQL\Connection(DB_HOSTNAME, '3306', DB_USER, DB_PASSWORD, DB_DBNAME);
// die('david nihao 3');
if(!empty($request['action'])){
	$action = explode('_',$request['action']);
	if(count($action)!=2){
		die('{"code":0,"message":"请求不合法！"}');
	}
	$class = '\Zhishang\\'.ucfirst($action[0]);

	$method = $action[1];
	if(!class_exists($class)){
		die('{"code":0,"message":"请求的控制器不存在，请检查action参数！"}');
	}

	$controller = new $class;
	if(!method_exists($controller,$method)){
		die('{"code":0,"message":"请求的方法不存在，请检查action参数！"}');
	}
	//验证token是否合法
	$no_login_class = array('\Zhishang\Pdts','\Zhishang\Index','\Zhishang\Product','\Zhishang\Skill','\Zhishang\Brand','\Zhishang\Paper');
	$no_login_funcs = array('login', 'wxLogin', 'miniLogin', 'douyinLogin', 'appletReg', 'shareOrder', 'showCode', 'phoneRegister', 'phoneLogin', 'sendSms', 'changePass', 'pjTask','tdTask', 'lists', 'getAreaList', 'getCodeUrl','getLiteratures');
	if(!in_array($class, $no_login_class) && !in_array($method, $no_login_funcs)){
		\Zhishang\Users::verify_token((int)$request['user_id'],$request['token']);
	}
	echostr($controller->$method());
}else{
	die('{"code":0,"message":"action不能为空"}');
}