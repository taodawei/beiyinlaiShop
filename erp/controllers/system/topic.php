<?php

function refund()
{
    global $db,$request;
    
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$db->query("delete from demo_product_refund ");
		
		$addrows = [];
		if($request['addRowKey']){
		    $rowNum = count($request['addRowKey']);
		    for($i=0; $i < $rowNum; $i++){
		        $addrows[] = array(
		            'title' => $request['addRowKey'][$i],
		            'download' => $request['addRowValue'][$i]
		        );
		    }
		}
		
		if($addrows){
            foreach ($addrows as $q => $ans){
                $detail = array();
                $detail['title'] = $ans['title'];
                $detail['download'] = $ans['download'];
                $detail['dtTime'] = date("Y-m-d H:i:s");
                
                $db->insert_update("demo_product_refund", $detail, "id");
            } 
        }
		
		redirect("?s=topic&a=refund");
	}
}

function think(){}

function addThink()
{
    global $db,$request;
    
    if($request['submit'] == 1){
        
        $topic = array();
        $topic['id'] = (int)$request['id'];
        $topic['title'] = $request['title'];
        $topic['en_title'] = $request['en_title'];
        $topic['originalPic'] = $request['originalPic'];
        $topic['video_img'] = $request['video_img'];
        $topic['topicId'] = (int)$request['topicId'];

        $topic['ordering'] = (int)$request['ordering'];
        $topic['status'] = (int)$request['status'];
        $topic['download'] = $request['download'];

        $topic['content'] = $request['content'];
        $topic['en_content'] = $request['en_content'];
       
        if($topic['id'] == 0){
            $topic['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_product_think', $topic, 'id');

        redirect("?s=topic&a=think");
    }
}

function getThinkList()
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
	$sql = "select * from demo_product_think where 1=1 and is_del = 0 ";
	
	if($channelId > 0){
	    $sql .= " and channelId in ($channelId) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%' or en_title like '%$keyword%' or en_content like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->image = $j->originalPic ? '<img src="'.ispic($j->originalPic).'" width="50" height="50">' : '';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            $j->topic = $db->get_var("select title from demo_product_topic where id = $j->topicId");
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function index(){}

function add()
{
    global $db,$request;
    
    if($request['submit'] == 1){
        
        $topic = array();
        $topic['id'] = (int)$request['id'];
        $topic['title'] = $request['title'];
        $topic['en_title'] = $request['en_title'];
        $topic['originalPic'] = $request['originalPic'];


        $topic['ordering'] = (int)$request['ordering'];
        $topic['status'] = (int)$request['status'];

        $topic['content'] = $request['content'];
        $topic['en_content'] = $request['en_content'];
       
        if($topic['id'] == 0){
            $topic['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_product_topic', $topic, 'id');

        redirect("?s=topic");
    }
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_product_topic set is_del = 1 where id in ($id) ");
	
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
	$sql = "select * from demo_product_topic where 1=1 and is_del = 0 ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%' or en_title like '%$keyword%' or en_content like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->image = $j->originalPic ? '<img src="'.ispic($j->originalPic).'" width="50" height="50">' : '';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}