<?php
function index(){}
function view(){}
function lists(){}
function get_shipin_list(){
	global $db,$request;
	$channelId = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;

	$sql="select * from demo_shipin where status=1";
	if(!empty($channelId)){
		$sql.=" and channelId=$channelId";
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	//file_put_contents('request.txt',$sql);
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$now = time();
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$data = array();
			$data['id'] = $pdt->id;
			$data['image'] = $pdt->url.'?x-oss-process=video/snapshot,t_0000,f_jpg,w_720,m_fast';
			$data['title'] = $pdt->title;
			$data['clicks'] = $pdt->clicks;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//团长发布的视频
function get_my_shipin(){
	global $db,$request;
	$status = (int)$request['status'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;

	$sql="select * from demo_shipin where userId=$userId and status=$status";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$now = time();
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$data = array();
			$data['id'] = $pdt->id;
			$data['image'] = $pdt->url.'?x-oss-process=video/snapshot,t_0000,f_jpg,w_720,m_fast';
			$data['title'] = $pdt->title;
			$data['clicks'] = $pdt->clicks;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function myshipin(){}
function upload_shipin(){
	global $db,$request;
	$file_name=$request['file_name'];
	$file_type=1;
	$file_suffix=$request['file_suffix'];
	$file_size=$request['file_size'];
	$file_parentId=$request['file_parentId'];
	$oss_fileId=$request['oss_fileId'];
	$oss_fileName=$request['oss_fileName'];
	$file_url="https://yzwc.oss-cn-zhangjiakou.aliyuncs.com/$oss_fileName";
	$file_name_arr=explode(".", $file_name);
	$size_arr=explode(" ",$file_size);
	$shipin = array();
	$shipin['channelId'] = 2;
	$shipin['mendianId'] = 0;
	$shipin['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$shipin['title'] = $request['title'];
	$shipin['url'] = $file_url;
	$shipin['dtTime'] = date("Y-m-d H:i:s");
	$shipin['status'] = 0;
	$shipin['inventoryId'] = (int)$request['inventoryId'];
	$shipin['link_url'] = '';
	$db->insert_update('demo_shipin',$shipin,'id');
	echo '{"code":1,"message": "上传成功","file_url":"'.$file_url.'"}';
	exit;
}