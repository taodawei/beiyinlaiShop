<?php
use Workerman\Worker;
require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/vendor/autoload.php';
// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker("http://0.0.0.0:2345");
// 启动几个进程对外提供服务，可设置为期望的数据库连接池的数量
$http_worker->count = 1;
//避免惊群效应，只有php7才支持，可以让每个task进程均衡的接收任务
$http_worker->reusePort = true;
$http_worker->onWorkerStart = function($http_worker)
{
    // 将db实例存储在全局变量中(也可以存储在某类的静态成员中)
    global $db;
    $db = new \Workerman\MySQL\Connection(DB_HOSTNAME, DB_PORT, DB_USER, DB_PASSWORD, DB_DBNAME);
};
// 接收到发送的数据时触发
$http_worker->onMessage = function($connection, $data)
{
    global $db,$request;
	$request = cleanArrayForMysql($_REQUEST);
	if(!empty($request['action'])){
		$action = explode('_',$request['action']);
		if(count($action)!=2){
			$connection->send('{"code":0,"message":"请求不合法！"}');
		}else{
			$class = '\Zhishang\Controllers\\'.ucfirst($action[0]);
			$method = $action[1];
			if(!class_exists($class)){
				$connection->send('{"code":0,"message":"请求的控制器不存在，请检查action参数！"}');
			}else{
				$controller = new $class;
				if(!method_exists($controller,$method)){
					$connection->send('{"code":0,"message":"请求的方法不存在，请检查action参数！"}');
				}else{
					$connection->send(json_str($controller->$method()));
				}
			}
		}
	}else{
		$connection->send('{"code":0,"message":"action传了吗？山炮~~"}');
	}
};

// 运行worker
Worker::runAll();