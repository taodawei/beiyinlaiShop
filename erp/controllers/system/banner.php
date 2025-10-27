<?php
function index(){}
function channel(){}
function banner(){}
function addBanner(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$banner = array();
		$banner['id'] = (int)$request['id'];
		$banner['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$banner['channelId'] = (int)$request['channelId'];
		$banner['title'] = $request['title'];
		$banner['en_title'] = $request['en_title'];
		$banner['position'] = $request['position'];
        $banner['originalPic'] = $request['originalPic'];
		$banner['inventoryId'] = (int)$request['inventoryId'];
		$banner['url'] = $request['url'];
		if($banner['id']==0){
			$banner['dtTime'] = date("Y-m-d H:i:s");
		}
		$db->insert_update('banner',$banner,'id');
		if($request['channelId']==0){
			redirect("?m=system&s=banner");
		}
		redirect("?m=system&s=banner&a=banner&channelId=".$request['channelId']);
	}
}
function topBanner(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$channelId = (int)$request['channelId'];
	$maxOrdering = $db->get_var("select max(ordering) from banner where comId=$comId");
	$maxOrdering+=1;
	$db->query("update banner set ordering=$maxOrdering where id=$id");
	if($channelId==0){
		redirect("?m=system&s=banner");
	}
	redirect("?m=system&s=banner&a=banner&channelId=$channelId");
}
function del_banner(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['channelId'];
	$db->query("delete from banner where id=$id and comId=$comId");
	if($channelId==0){
		redirect("?m=system&s=banner");
	}
	redirect("?m=system&s=banner&a=banner&channelId=$channelId");
}
function ordering_channel(){
	global $db,$request;
	$ordering = $request['ordering'];
	foreach($ordering as $key=>$value)
	{
		if(empty($value))$value=0;
		$sql ='UPDATE banner_channel SET ordering='.$value.' WHERE id='.$key;
		$db->query($sql);
	}
	redirect("?s=banner&a=channel");
}
function addChannel(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$banner = array();
		$banner['id'] = (int)$request['id'];
		$banner['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$banner['title'] = $request['title'];
		$banner['remark'] = $request['remark'];
		$banner['show_title'] = (int)$request['show_title'];
		$banner['shuliang'] = (int)$request['shuliang'];
		$banner['show_img_title'] = (int)$request['show_img_title'];
		$banner['if_banner_show'] = (int)$request['if_banner_show'];
		$db->insert_update('banner_channel',$banner,'id');
		redirect("?m=system&s=banner&a=channel");
	}
}
function del_banner_channel(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from banner_channel where id=$id and comId=$comId");
	$db->query("delete from banner where channelId=$id and comId=$comId");
	redirect("?m=system&s=banner&a=channel");
}
//资讯
function gonggao(){}
function delGongao(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_list where id=$id and comId=$comId");
	echo '{"code":1,"message":"ok"}';
}
function addGonggao(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$gonggao = array();
		$gonggao['id'] = (int)$request['id'];
		$gonggao['channelId'] = (int)$request['channelId'];
		$gonggao['comId'] = $comId;
		$gonggao['dtTime'] = !empty($request['dtTime']) ? $request['dtTime'] : date("Y-m-d H:i:s");
		$gonggao['title'] = $request['title'];
	    $gonggao['language'] = (int)$request['language'];
		$gonggao['originalPic'] = $request['originalPic'];
		$gonggao['content'] = $request['content'];
		$gonggao['video'] = $request['video'] ? $request['video'] : '';
		$gonggao['video_img'] = $request['video_img'] ? $request['video_img'] : '';
		$gonggao['path'] = $request['path'] ? $request['path'] : '';
		$gonggao['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$gonggao['jianjie'] = $request['jianjie'];
		$gonggao['ordering'] = (int)$request['ordering'];
		$gonggao['if_show'] = (int)$request['if_show'];
		$gonggao['if_index'] = (int)$request['if_index'];
		$gonggao['product_channel'] = $request['product_channel'];
        $status = insert_update('demo_list',$gonggao,'id');

		redirect("?m=system&s=banner&a=gonggao");
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
	$sql="select id,dtTime,title,channelId,ordering,if_index,language,if_show from demo_list where comId=$comId";
	if(!empty($channelId)){
	    $channelIds = $db->get_var("select group_concat(id) from demo_list_channel where id = $channelId or parentId = $channelId");
		$sql.=" and channelId in ($channelIds) ";
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
	$count = $db->get_var(str_replace('id,dtTime,title,channelId,ordering,if_index,language','count(*)',$sql));
	$sql.=" order by ordering desc,id asc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->url = '/index.php?p=5&a=view&id='.$j->id;
			$j->if_index = $j->if_index ? '<span style="color:green;">是</span>' : '<span style="color:gray;">否</span>';
			$j->if_show = $j->if_show ? '<span style="color:green;">是</span>' : '<span style="color:gray;">否</span>';
			$j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->totop = '<a href="?m=system&s=banner&a=totop&id='.$j->id.'" style="color:green;">置顶</a>';
			$j->channel = $db->get_var("select title from demo_list_channel where id=$j->channelId");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function gonggaoChannel(){}
function addGonggaoChannel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$parentId = (int)$request['parentId'];
	$title = $request['title'];
	$enTitle = $request['en_title'];
	$originalPic = $request['originalPic'];
	if(empty($id)){
		$ifhas = $db->get_var("select id from demo_list_channel where comId=$comId and parentId=$parentId and title='$title'");
		if(!empty($ifhas)){
			echo '<script>alert("您已经创建过这个分类了！");history.go(-1);</script>';
			exit;
		}
		$db->query("insert into demo_list_channel(comId,title,en_title,parentId,originalPic) value($comId,'$title','$enTitle',$parentId,'$originalPic')");
		$id = $db->get_var("select last_insert_id();");
	}else{
		$db->query("update demo_list_channel set title='$title',en_title='$enTitle',parentId=$parentId,originalPic='$originalPic' where id=$id and comId=$comId");
	}
	redirect("?m=system&s=banner&a=gonggaoChannel");
}
function dellistChannel(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($id>0){
		$ifhas = $db->get_var("select id from demo_list where channelId=$id limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"删除分类前请先删除分类下的资讯！"}';
		}else{
			$db->query("delete from demo_list_channel where id =$id");
			echo '{"code":1,"message":"删除成功！","ids":"'.$id.'"}';
		}
	}else{
		echo '{"code":0,"message":"分类选择有误!"}';
	}
	exit;
}