<?php

function index(){}

function add()
{
    global $db,$request;
    
    if($request['submit'] == 1){
        
        $addrows = [];
		if($request['addRowKey']){
		    $rowNum = count($request['addRowKey']);
		    for($i=0; $i < $rowNum; $i++){
		        $addrows[$request['addRowKey'][$i]] = $request['addRowValue'][$i];    
		    }
		}
		$custom_json = '';
		if($addrows){
    	    $custom_json = json_encode($addrows,JSON_UNESCAPED_UNICODE);
		}
        
        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['channelId'] = (int)$request['channelId'];
        $recruit['file_info'] = $custom_json;
        $recruit['status'] = (int)$request['status'];

        $recruit['content'] = $request['content'];
        $recruit['solution'] = $request['solution'];
        $recruit['process_type'] = (int)$request['process_type'];
        $recruit['language'] = (int)$request['language'];
       
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_skill', $recruit, 'id');

        redirect("?s=skill");
    }
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_skill set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function getList()
{
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc';
	}
	$sql = "select * from demo_skill where 1=1 and is_del = 0 ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->channelTitle = $db->get_var("select title from demo_skill_channel where id = $j->channelId");
            $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function process(){}

function getProcessList(){
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc';
	}
	
	$skillId = (int)$request['skillId'];
	$sql = "select * from demo_skill_process where 1=1 and is_del = 0 and type = 0 and skillId = $skillId ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%' or jianjie like '%$keyword%' ) ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->channelTitle = $db->get_var("select title from demo_skill where id = $j->skillId");
            // $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            $j->image = $j->originalPic ? '<img src="'.ispic($j->originalPic).'" width="50" height="50">' : '';
			
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function addProcess(){
    global $db,$request;
    
    if($request['submit'] == 1 && $request['title']){

        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['skillId'] = $skillId = (int)$request['skillId'];
        $recruit['status'] = (int)$request['status'];
        $recruit['content'] = $request['content'];
        $recruit['originalPic'] = $request['originalPic'];
        $recruit['jianjie'] = $request['jianjie'];
        
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_skill_process', $recruit, 'id');

        redirect("?s=skill&a=process&skillId=$skillId");
    }
}

function delAnli(){
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_skill_process set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function delFangan(){
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_skill_process set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function delProcess(){
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_skill_process set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function fangan(){}

function anli(){}

function getFanganList()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc';
	}
	$skillId = (int)$request['skillId'];
	$sql = "select * from demo_skill_process where 1=1 and is_del = 0 and type = 1 and skillId = $skillId ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%' or jianjie like '%$keyword%' ) ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->channelTitle = $db->get_var("select title from demo_skill where id = $j->skillId");
            // $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function getAnliList()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc';
	}
	
	$skillId = (int)$request['skillId'];
	$sql = "select * from demo_skill_process where 1=1 and is_del = 0 and type = 2 and skillId = $skillId ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%' or jianjie like '%$keyword%' ) ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->channelTitle = $db->get_var("select title from demo_skill where id = $j->skillId");
            // $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function addFangan(){
    global $db,$request;
    
    if($request['submit'] == 1 && $request['title']){

        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['skillId'] = $skillId = (int)$request['skillId'];
        $recruit['status'] = (int)$request['status'];
        $recruit['content'] = $request['content'];
        $recruit['jianjie'] = $request['jianjie'];
        $recruit['type'] = 1;
        
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_skill_process', $recruit, 'id');

        redirect("?s=skill&a=fangan&skillId=$skillId");
    }
}

function addAnli(){
    global $db,$request;
    
    if($request['submit'] == 1 && $request['title']){

        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['subtitle'] = $request['subtitle'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['skillId'] = $skillId = (int)$request['skillId'];
        $recruit['status'] = (int)$request['status'];
        $recruit['originalPic'] = $request['originalPic'];
        $recruit['content'] = $request['content'];
        $recruit['jianjie'] = $request['jianjie'];
        $recruit['type'] = 2;
        
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_skill_process', $recruit, 'id');

        redirect("?s=skill&a=anli&skillId=$skillId");
    }
}

function consult(){}

function consultInfo(){}

function getConsultList()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
// 	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
// 	}
	$sql = "select * from demo_skill_consult where 1=1 and is_del = 0  ";
    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (name like '%$keyword%' or content like '%$keyword%' or phone like '%$keyword%' or email like '%$keyword%' or institution like '%$keyword%' ) ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->channelTitle = $db->get_var("select title from demo_skill where id = $j->skill_id");
            // $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            // $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function delConsult()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_skill_consult set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}











