<?php
use Workerman\Worker;
require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/vendor/autoload.php';
// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker("Text://0.0.0.0:2346");

// 启动1个进程对外提供异步任务服务
$http_worker->count = 1;
$http_worker->onWorkerStart = function($http_worker)
{
    // 将db实例存储在全局变量中(也可以存储在某类的静态成员中)
    global $db;
    $db = new \Workerman\MySQL\Connection(DB_HOSTNAME, DB_PORT, DB_USER, DB_PASSWORD, DB_DBNAME);
};
// 接收到发送的数据时触发
$http_worker->onMessage = function($connection, $task_data)
{
    global $db;
    $task_data = json_decode($task_data, true);
	$action = explode('_',$task_data['action']);
	if(count($action)!=2){
		file_put_contents('asy_task.err','任务路由不合法，ation：'.$task_data['action'].PHP_EOL,FILE_APPEND);
		$connection->send('{"code":0,"message":"请求不合法！"}');
	}else{
		$class = '\Zhishang\\'.ucfirst($action[0]);
		$method = $action[1];
		if(!class_exists($class)){
			file_put_contents('asy_task.err','任务控制器不存在，ation：'.$task_data['action'].PHP_EOL,FILE_APPEND);
			$connection->send('{"code":0,"message":"请求的控制器不存在，请检查action参数！"}');
		}else{
			$controller = new $class;
			if(!method_exists($controller,$method)){
				file_put_contents('asy_task.err','任务控制器的方法不存在，ation：'.$task_data['action'].PHP_EOL,FILE_APPEND);
				$connection->send('{"code":0,"message":"请求的方法不存在，请检查action参数！"}');
			}else{
				$connection->send(json_str($controller->$method($task_data['params'])));
			}
		}
	}
};

// 运行worker
Worker::runAll();