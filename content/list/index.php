<?php
function index(){}
function view(){}
function get_news_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$channelId = (int)$request['channelId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;

	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}

	$sql="select id,title,originalPic,dtTime from demo_list where comId=$comId";
	if($channelId>5){
		$sql.=" and parentId=$channelId";
	}
	$count = $db->get_var(str_replace('id,title,originalPic','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$pdt->title = sys_substr($pdt->title,16,true);
			$pdt->dtTime = date("Y-m-d",strtotime($pdt->dtTime));
			$return['data'][] = $pdt;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}