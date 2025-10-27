<?php

function index(){}

function add()
{
    global $db,$request;
    
    if($request['tijiao'] == 1){
        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['channelId'] = (int)$request['channelId'];
       
        $recruit['num'] = $request['num'] ? $request['num'] : 1;
        $recruit['content'] = $request['content'];
        $recruit['address'] = $request['address'];
        $recruit['language'] = (int)$request['language'];
       
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('recruit', $recruit, 'id');
        
        redirect("?s=recruit");
    }
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update recruit set is_del = 1 where id in ($id) ");
	
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
	$sql = "select * from recruit where 1=1 and is_del = 0 ";
	
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
		    
            $j->channelTitle = $db->get_var("select title from demo_recruit_channel where id = $j->channelId");
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}