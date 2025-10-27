<?php
function index(){}

function addProductChannel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$parentId = (int)$request['parentId'];
	$id = (int)$request['id'];
	$submit = (int)$request['submit'];
	if($submit){
    	$title = $request['title'];
    	$enTitle = $request['en_title'];
    	$miaoshu = $request['miaoshu'];
    	$enMiaoshu = $request['en_miaoshu'];
    	$originalPic = $request['originalPic'];
    	$backimg = $request['backimg'];
    	$isHot = (int)$request['is_hot'];
    	$type = (int)$request['type'];
    	if(empty($id)){
    		$ifhas = $db->get_var("select id from demo_study_channel where comId=$comId and parentId=$parentId and title='$title'");
    		if(!empty($ifhas)){
    			echo '<script>alert("您已经创建过这个分类了！");history.go(-1);</script>';
    			exit;
    		}
    		$db->query("insert into demo_study_channel(comId,title,parentId,originalPic,backimg,is_hot,en_title,miaoshu,en_miaoshu,type) value($comId,'$title',$parentId,'$originalPic','$backimg', $isHot,'$enTitle', '$miaoshu','$enMiaoshu',$type)");
    		$id = $db->get_var("select last_insert_id();");
    		
    		$db->query("update demo_study_channel set ordering = $id where id = $id");
    	}else{
    		$db->query("update demo_study_channel set title='$title',en_title='$enTitle',miaoshu='$miaoshu', en_miaoshu='$enMiaoshu' ,parentId=$parentId,originalPic='$originalPic',backimg='$backimg',is_hot=$isHot,type=$type where id=$id and comId=$comId");
    	}
    	
    	redirect("?m=system&s=study_channel&id=$id");
	}
}
function totop(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$maxOrdering = $db->get_var("select ordering from demo_study_channel where comId=$comId order by ordering desc limit 1");
	$maxOrdering+=1;
	$db->query("update demo_study_channel set ordering=$maxOrdering where id=$id");
// 	cache_channel();
	redirect("?m=system&s=study_channel&id=$id");
}

function move()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$type = (int)$request['type'];//类型：0-向下  1-向上
	
	$currentChannel = $db->get_row("select * from demo_study_channel where id = $id");
	if($type){//1-向上
	    $move = $db->get_row("select * from demo_study_channel where parentId = $currentChannel->parentId and ordering > $currentChannel->ordering order by ordering asc ");
	}else{
	    $move = $db->get_row("select * from demo_study_channel where parentId = $currentChannel->parentId and ordering < $currentChannel->ordering order by ordering desc ");
	}
	if($move){
	    $db->query("update demo_study_channel set ordering = $move->ordering where id = $id");
	    $db->query("update demo_study_channel set ordering = $currentChannel->ordering where id = $move->id");
	}

	redirect("?m=system&s=study_channel&id=$id");
}

function delChannel(){
	global $db,$request;
	$id = (int)$request['id'];
	if($id>0){
		$ids = $id.getZiIds($id);
		$ifhas = $db->get_var("select id from demo_study where channelId in($ids) and is_del =0  limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"删除分类前请先转移分类下的学习资料！"}';
		}else{
			$db->query("delete from demo_study_channel where id in($ids)");
// 			cache_channel();
			echo '{"code":1,"message":"删除成功！","ids":"'.$ids.'"}';
		}
	}else{
		echo '{"code":0,"message":"分类选择有误!"}';
	}
	exit;
}
//获取所有上级分类，用,分开
function getParentIds($id){
	global $db;
	$pid = $db->get_var("select parentId from demo_study_channel where id=$id");
	if($pid>0){
		return ','.$pid.getParentIds($pid);
	}
}