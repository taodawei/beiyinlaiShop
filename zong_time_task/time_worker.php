<?php
use Workerman\Worker;
use \Workerman\Lib\Timer;
require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/vendor/autoload.php';
$task = new Worker();
// 开启多少个进程运行定时任务，注意业务是否在多进程有并发问题
$task->count = 1;
$task->onWorkerStart = function($task)
{
	global $db,$db_service,$time_nums;
	
    // 每3秒执行一次
	$time_interval = 3;
    //每次执行多少个任务 
	$time_nums = 10;
	$db = new \Workerman\MySQL\Connection(DB_HOSTNAME, DB_PORT, DB_USER, DB_PASSWORD,DB_DBNAME);
	$db_service = new \Workerman\MySQL\Connection(SERVICE_HOSTNAME, SERVICE_PORT, SERVICE_USER, SERVICE_PASSWORD,SERVICE_DBNAME);
	Timer::add($time_interval, function()
	{
		global $db,$db_service,$time_nums;
		$now = time();
		$tasks = $db->get_results("select * from demo_timed_task order by dtTime asc limit $time_nums");
		if(!empty($tasks)){
			foreach ($tasks as $task) {
				if($task->dtTime>$now)break;
				$action = explode('_',$task->router);
				if(count($action)!=2){
					file_put_contents('timed_task.err','任务路由不合法，任务id：'.$task->id.PHP_EOL,FILE_APPEND);
					$db->query("update demo_timed_task set dtTime=2147483647 where id=".$task->id);
				}else{
					$class = '\Zhishang\\'.ucfirst($action[0]);
					$method = $action[1];
					if(!class_exists($class)){
						$controller = new $class;
						file_put_contents('timed_task.err','任务类'.'\Zhishang\\'.ucfirst($action[0]).'不存在，任务id：'.$task->id.PHP_EOL,FILE_APPEND);
						$db->query("update demo_timed_task set dtTime=2147483647 where id=".$task->id);
					}else{
						$controller = new $class;
						if(!method_exists($controller,$method)){
							file_put_contents('timed_task.err','任务类的方法不存在，任务id：'.$task->id.PHP_EOL,FILE_APPEND);
							$db->query("update demo_timed_task set dtTime=2147483647 where id=".$task->id);
						}else{
							$params = json_decode($task->params,true);
							$params['fenbiao'] = $task->comId%20;
							//调用类的方法，将params作为参数传递过去
							$controller->$method($params);
							//执行完成后删除记录
							$db->query("delete from demo_timed_task where id=".$task->id);
						}
					}
				}
			}
		}
	});
};
Worker::runAll();