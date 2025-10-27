<?php
function index(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$fenbiao = getFenbiao($comId,20);
	$lastId = $db->get_var("select id from demo_task$fenbiao order by id desc limit 1");
	$ifhas = $db->get_var("select msgId from demo_task_read where userId=$userId");
	if($lastId > 0){
	    if(empty($ifhas)){
    		$db->query("insert into demo_task_read(userId,msgId) value($userId,$lastId)");
    	}else{
    		$db->query("update demo_task_read set msgId=$lastId where userId=$userId");
    	}
	}

}
function getJilus(){
	global $db,$request,$adminRole;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('yewuPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql = "select id,type,infoId,content,dtTime,results from demo_task$fenbiao where comId=$comId";
	if($adminRole<7){
		$sql.=" and find_in_set($userId,userIds)";
	}
	$count = $db->get_var(str_replace('id,type,infoId,content,dtTime,results','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->layclass = '';
			$ifread = 0;
			if(!empty($j->results)){
				$reads = explode(',',$j->results);
				if(in_array($userId,$reads)){
					$j->layclass = 'deleted';
					$ifread = 1;
				}
			}
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$url = getMsgUrl($j->type,$j->infoId,$page, $j->content);
			$j->content = '<span onclick="view_jilu(\''.$url.'\','.$j->id.','.$ifread.','.$type.');" style="cursor:pointer;">'.($ifread==0?'<i></i>':'').$j->content.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getMsgUrl($type,$infoId,$page,$content){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$returnurl = urlencode('?m=system&s=msg&page='.$page);
	switch ($type) {
		case 1:
			$s = "?m=system&s=kucun&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 2:
			$s = "?m=system&s=kucun&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 3:
			$s = "?m=system&s=kucun&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 4:
			$s = "?m=system&s=caigou&a=detail&id=$infoId&returnurl=".$returnurl;
		break;
		case 5:
			$s = "?m=system&s=caigou_tuihuo&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 11:
			$s = "?m=system&s=dinghuo&a=detail&id=$infoId&returnurl=".$returnurl;
		break;
		case 12:
			$s = "?m=system&s=dinghuo&a=shoukuan&id=$infoId&returnurl=".$returnurl;
		break;
		case 13:
			$s = "?m=system&s=dinghuo&a=chuku&id=$infoId&returnurl=".$returnurl;
		break;
		case 14:
			$s = "?m=system&s=dinghuo&a=chuku&id=$infoId&returnurl=".$returnurl;
		break;
		case 15:
			$s = "?m=system&s=dinghuo&a=detail&id=$infoId&returnurl=".$returnurl;
		break;
		case 21:
			$s = "?m=system&s=tuihuo&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 22:
			$s = "?m=system&s=tuihuo&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 23:
			$s = "?m=system&s=tuihuo&a=shoukuan&id=$infoId&url=".$returnurl;
		break;
		case 24:
			$s = "?m=system&s=tuihuo&a=jilu_detail&id=$infoId&url=".$returnurl;
		break;
		case 31:
			$s = "?m=system&s=order&url".$returnurl;
		break;
        case 34:
            $type = $db->get_var("select type from order_tuihuan where id = $infoId ");
            switch($content){
                case '有新的退货退款订单需要进行审核，请及时处理':
                    $status = 1;
                    break;
                case '有新的退换货订单需要进行退款操作，请及时处理！':
                    $status = 3;
                    break;
                default:
                    $status = 0;
                    break;
            }
			$s = "?s=order&a=tuikuan_order&status=$status&type=".$type."&if_jifen=0&url".$returnurl;
		    break;
		case 41:
			$s = "?m=system&s=users&url".$returnurl;
		break;
		default:
			$s = '';
		break;
	}
	return $s;
}
function setYidu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	if($id > 0 ){
	    $results = $db->get_var("select results from demo_task$fenbiao where id=$id");
    	if(empty($results)){
    		$db->query("update demo_task$fenbiao set results='$userId' where id=$id");
    	}else{
    		$readarr = explode(',',$results);
    		if(!in_array($userId,$readarr)){
    			$results = $results.','.$userId;
    			$db->query("update demo_task$fenbiao set results='$results' where id=$id");
    		}
    	}
	}

	echo '{"code":1}';
	exit;
}
function xitong(){}
function shengji(){}
function getXitongs(){
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>0,"data"=>array());
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getShengjis(){
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>0,"data"=>array());
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}