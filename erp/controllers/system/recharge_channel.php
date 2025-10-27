<?php
function index(){}
function cache_channel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(empty($comId)){
		return false;
	}
	$channels = array();
	$departments = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
	$departs = array();
	if(!empty($departments)){
		foreach($departments as $department){
			$departments1 = $db->get_results("select * from demo_recharge_channel where parentId=".$department->id."  order by ordering desc,id asc");
			$departs1=array();
			if(!empty($departments1)){
				foreach($departments1 as $department1){
					$departments2 = $db->get_results("select * from demo_recharge_channel where parentId=".$department1->id." order by ordering desc,id asc");
					$departs2 = array();
					if(!empty($departments2)){
						foreach($departments2 as $department2){
							$departments3 = $db->get_results("select * from demo_recharge_channel where parentId=".$department2->id." order by ordering desc,id asc");
							if(!empty($departments3)){
								$department2->channels = $departments3;
							}else{
								$department2->channels = array();
							}
							$departs2[]=$department2;
						}
					}
					if(!empty($departs2)){
						$department1->channels = $departs2;
					}else{
						$department1->channels = array();
					}
					$departs1[]=$department1;
				}
			}
			if(!empty($departs1)){
				$department->channels = $departs1;
			}else{
				$department->channels = array();
			}
			$departs[] = $department;
		}
	}
	$content = json_encode($departs,JSON_UNESCAPED_UNICODE);
	file_put_contents("../cache/channels_".$comId.".php",$content);
	return true;
}
function addProductChannel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$parentId = (int)$request['parentId'];
	$title = $request['title'];
	$originalPic = $request['originalPic'];
	$backimg = $request['backimg'];
	$isHot = (int)$request['is_hot'];
	if(empty($id)){
		$ifhas = $db->get_var("select id from demo_recharge_channel where comId=$comId and parentId=$parentId and title='$title'");
		if(!empty($ifhas)){
			echo '<script>alert("您已经创建过这个分类了！");history.go(-1);</script>';
			exit;
		}
		$db->query("insert into demo_recharge_channel(comId,title,parentId,originalPic,backimg,is_hot) value($comId,'$title',$parentId,'$originalPic','$backimg', $isHot)");
		$id = $db->get_var("select last_insert_id();");
		
		$db->query("update demo_recharge_channel set ordering = $id where id = $id");
	}else{
		$db->query("update demo_recharge_channel set title='$title',parentId=$parentId,originalPic='$originalPic',backimg='$backimg',is_hot=$isHot where id=$id and comId=$comId");
	}
// 	cache_channel();
	redirect("?m=system&s=recharge_channel&id=$id");
}
function totop(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$maxOrdering = $db->get_var("select ordering from demo_recharge_channel where comId=$comId order by ordering desc limit 1");
	$maxOrdering+=1;
	$db->query("update demo_recharge_channel set ordering=$maxOrdering where id=$id");
// 	cache_channel();
	redirect("?m=system&s=product_channel&id=$id");
}

function move()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$type = (int)$request['type'];//类型：0-向下  1-向上
	
	$currentChannel = $db->get_row("select * from demo_recharge_channel where id = $id");
	if($type){//1-向上
	    $move = $db->get_row("select * from demo_recharge_channel where parentId = $currentChannel->parentId and ordering > $currentChannel->ordering order by ordering asc ");
	}else{
	    $move = $db->get_row("select * from demo_recharge_channel where parentId = $currentChannel->parentId and ordering < $currentChannel->ordering order by ordering desc ");
	}
	if($move){
	    $db->query("update demo_recharge_channel set ordering = $move->ordering where id = $id");
	    $db->query("update demo_recharge_channel set ordering = $currentChannel->ordering where id = $move->id");
	}

	redirect("?m=system&s=product_channel&id=$id");
}

function delChannel(){
	global $db,$request;
	$id = (int)$request['id'];
	if($id>0){

        $db->query("delete from demo_recharge_channel where id in($id)");
 
        echo '{"code":1,"message":"删除成功！","ids":"'.$id.'"}';

	}else{
		echo '{"code":0,"message":"分类选择有误!"}';
	}
	exit;
}
//获取所有上级分类，用,分开
function getParentIds($id){
	global $db;
	$pid = $db->get_var("select parentId from demo_recharge_channel where id=$id");
	if($pid>0){
		return ','.$pid.getParentIds($pid);
	}
}