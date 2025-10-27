<?php

function index(){}

function add()
{
    global $db,$request;
    
    if($request['submit'] == 1){
        
        $study = array();
        $study['id'] = (int)$request['id'];
        $study['title'] = $request['title'];
        $study['jianjie'] = $request['jianjie'];
        $study['originalPic'] = $request['originalPic'];
        $study['video'] = $request['video'];
        $study['download'] = $request['download'];
        $study['ordering'] = (int)$request['ordering'];
        $study['channelId'] = (int)$request['channelId'];
        $study['status'] = (int)$request['status'];

        $study['content'] = $request['content'];
        $study['language'] = (int)$request['language'];
       
        if($study['id'] == 0){
            $study['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_study', $study, 'id');

        redirect("?s=study");
    }
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_study set is_del = 1 where id in ($id) ");
	
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
	$sql = "select * from demo_study where 1=1 and is_del = 0 ";
	
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
		    $j->image = $j->originalPic ? '<img src="'.ispic($j->originalPic).'" width="50" height="50">' : '';
            $j->channel = $db->get_var("select title from demo_study_channel where id = $j->channelId");
            $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function faq(){}

function addFaq()
{
    global $db,$request;
    
    if($request['submit'] == 1){
        
        $addrows = [];
		if($request['addRowKey']){
		    $rowNum = count($request['addRowKey']);
		    for($i=0; $i < $rowNum; $i++){
		        $addrows[] = array(
		            'question' => $request['addRowKey'][$i],
		            'answer' => $request['addRowValue'][$i]
		        );
		    }
		}
        
        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['status'] = (int)$request['status'];

        $recruit['language'] = (int)$request['language'];
       
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_faq', $recruit, 'id');
        if($recruit['id'] == 0){
            $recruit['id'] = $db->get_var("select last_insert_id();");
        }
        $db->query("delete from demo_faq_detail where faqId = ".$recruit['id']);
        
        if($addrows){
            foreach ($addrows as $q => $ans){
                $detail = array();
                $detail['faqId'] = $recruit['id'];
                $detail['question'] = $ans['question'];
                $detail['answer'] = $ans['answer'];
                $detail['dtTime'] = date("Y-m-d H:i:s");
                
                $db->insert_update("demo_faq_detail", $detail, "id");
            } 
        }
        

        redirect("?s=study&a=faq");
    }
}

function getFaqList()
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
	$sql = "select * from demo_faq where 1=1 and is_del = 0 ";

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or faq_info like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
            $j->language = $j->language ? '<span style="color:green;">英文</span>' : '<span style="color:red;">中文</span>';
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function delFaq()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_faq set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function step(){}

function addStep()
{
    global $db,$request;
    
    if($request['submit'] == 1){
       
        $recruit = array();
        $recruit['id'] = (int)$request['id'];
        $recruit['title'] = $request['title'];
        $recruit['en_title'] = $request['en_title'];
        $recruit['ordering'] = (int)$request['ordering'];
        $recruit['status'] = (int)$request['status'];
        $recruit['download'] = $request['download'];
        $recruit['faqId'] = (int)$request['faqId'];
        if($recruit['id'] == 0){
            $recruit['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('demo_study_step', $recruit, 'id');

        redirect("?s=study&a=step");
    }    
}

function getStepList()
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
	$sql = "select * from demo_study_step where 1=1 and is_del = 0 ";

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or faq_info like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
            $j->statusInfo = $j->status ? '<span style="color:green;">展示</span>' : '<span style="color:red;">隐藏</span>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function delStep()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update demo_study_step set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}


