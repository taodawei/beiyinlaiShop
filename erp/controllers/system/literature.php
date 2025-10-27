<?php


//资讯
function gonggao(){}
function delGongao(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_iteratures where id=$id and comId=$comId");
	echo '{"code":1,"message":"ok1"}';
}
function addGonggao(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$gonggao = array();
		$gonggao['id'] = (int)$request['id'];

		$gonggao['comId'] = $comId;
		$gonggao['dtTime'] = date("Y-m-d H:i:s");
		$gonggao['title'] = $request['title'];
	    $gonggao['url'] = $request['url'];
		$gonggao['originalPic'] = $request['originalPic'];

		
		insert_update('demo_iteratures',$gonggao,'id');
		redirect("?m=system&s=literature&a=gonggao");
	}
}

function totop()
{
    global $db,$request;

	$gonggao = array();
	$gonggao['id'] = (int)$request['id'];
    
    $ordering = $db->get_var("select ordering from demo_list order by ordering desc limit 1 ");
    $gonggao['ordering'] = $ordering+1;
	
	insert_update('demo_list',$gonggao,'id');
	
	redirect("?m=system&s=banner&a=gonggao");
}

function viewGonggao(){}
function getGonggaos(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$pageNum = (int)$request["limit"];
	setcookie('gonggaoPageNum',$pageNum,time()+3600*24*30);
	$sql="select * from demo_iteratures where comId=$comId";
	if(!empty($channelId)){
		$sql.=" and channelId=$channelId";
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id asc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>$jilus);

	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}