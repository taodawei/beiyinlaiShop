<?php
function index(){}

function main(){}

function add()
{
    global $db,$request;
    
    if($request['tijiao'] == 1){
        $change = array();
        $change['id'] = (int)$request['id'];
        $change['title'] = $request['title'];
        $change['ordering'] = (int)$request['ordering'];
        $change['channelId'] = (int)$request['channelId'];
        $change['beizhu'] = $request['beizhu'] ? $request['beizhu'] : '';
        $change['price'] = $request['price'] ? $request['price'] : 0;
        $change['theme_color'] = $request['theme_color'] ? $request['theme_color'] : '';
        $change['story_img'] = $request['logo'];
        if($change['id'] == 0){
            $change['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('kmd_recharge', $change, 'id');
        
        redirect("?s=recharge&a=main");
    }
}

function getCardList()
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
	$sql = "select * from kmd_recharge where 1=1 and is_del = 0 ";
	
	if($channelId > 0){
	    $ziIds = getZiDataByTable($channelId, 'demo_recharge_channel');
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
    $now = date("Y-m-d H:i:s");
    $nextWeek = date("Y-m-d H:i:s", strtotime("+1 week"));
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->logo = '<img src="'.ispic($j->story_img).'?x-oss-process=image/resize,w_54" width="50" height="50">';
		    $j->subtitle = '<span onmouseover="tips(this,\''.stripcslashes($j->subtitle).'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($j->subtitle),100,true).'</span>';
		    
            
            $j->channelTitle = $db->get_var("select title from demo_recharge_channel where id = $j->channelId");
            
            if($j->theme_color){
	            $j->title = '<span style="color:'.$j->theme_color.'">'.$j->title.'</span>';
	        }
            
            $j->title = '<a href="?s=recharge&id='.$j->id.'">'.$j->title.'</a>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$isOpend = (int)$request['is_open'];
	$ids = $request['ids'];
	$cards = $db->get_results("select id,status from recharge_card where id in($ids) and status = 0 ");
	
	$statusInfo = "开通";
    if($isOpend == 1){
        $statusInfo = "关闭";
    }
	
	if(!$cards){
	    die('{"code":0,"message":"没有未兑换的充值卡设置为'.$statusInfo.'！"}');
	}
	
	$now = date("Y-m-d H:i:s");
	if($isOpend){//1-开通  0-关闭
	    $db->query("update recharge_card set is_open = $isOpend,openTime = '$now',closeTime=null where id in ($ids) and status = 0 ");
	}else{
	    $db->query("update recharge_card set is_open = $isOpend,closeTime = '$now',openTime=null where id in ($ids) and status = 0 "); 
	}
	
// 	if(!empty($fahuos)){
// 		foreach ($fahuos as $fahuo) {
// 			if($fahuo->status==0){
// 				$db->query("update order_fahuo$fenbiao set status=-2 where id=$fahuo->id");
// 				addJilu($fahuo->id,$fenbiao,1,'发货暂停','发货暂停');
// 			}
// 		}
// 	}
	die('{"code":1,"message":"批量'.$statusInfo.'完成！"}');
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
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	
	$jiluId = $request['jiluId'];
	
	$sql = "select * from recharge_card where 1=1 and jiluId = $jiluId ";
	
	$channelId = (int)$request['channelId'];
	if($channelId > 0){
	    $channelIds = getZiDataByTable($channelId, "demo_recharge_channel");
	    $sql .= " and channelId in ($channelId.$channelIds) ";
	}
	
    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (card_no like '%$keyword%' ) ";
    }
    $type = (int)$request['type'];
    if($type > 0){
        if($type == 1){
            $sql .= " and status = 0";
        }elseif ($type == 2) {
            $sql .= " and status = 1";
        }
    }
    
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());

	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
            $j->startTime = date('Y-m-d H:i', strtotime($j->startTime));
            $j->endTime = date('Y-m-d H:i', strtotime($j->endTime));
            $j->dtTime = date('Y-m-d H:i', strtotime($j->dtTime));
            
            if($j->bindTime){
                $j->bindTime = date('Y-m-d H:i', strtotime($j->bindTime));
                $j->userInfo = $db->get_var("select concat(nickname, '(', phone,')') from users where id = $j->userId"  );
            }
            
            switch ($j->status) {//状态：0-未兑换  1-已兑换 -1-已失效
                case 0:
                    $status_info = '<span style="color:red;">未兑换</span>';
                    break;
                case 1:
                    $status_info = '<span style="color:green;">已兑换</span>';
                    break;
                case -1:
                    $status_info = '<span style="color:gray;">已失效</span>';
                    break;
            }
            $j->status_info = $status_info;
            
            switch ($j->is_open) {//状态：0-未兑换  1-已兑换 -1-已失效
                case 0:
                    $open_info = '<span style="color:red;">未开通</span>';
                    break;
                case 1:
                    $open_info = '<span style="color:green;">已开通</span>';
                    break;
            }
            $j->open_info = $open_info;
            
            $j->changeTitle = '';
            if($j->channelId > 0){
                $j->channelTitle = $db->get_var("select title from demo_recharge_channel where id = $j->channelId");
            }
            
            if($j->openTime){
                $j->openTime = date("Y-m-d H:i", strtotime($j->openTime));
            }
            
            if($j->theme_color){
	            $j->title = '<span style="color:'.$j->theme_color.'">'.$j->title.'</span>';
	        }
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update kmd_recharge set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function create()
{
    global $db,$request;
    
    $comId = (int)$_SESSION[TB_PREFIX.'comId'];
    if($request['tijiao']==1){
        $num = (int)$request['num'];
        $money = floatval($request['money']);
        
        if(!$num || !$money){
            echo '<script>alert("请输入生成充值卡数量和充值卡面值");location.href="?m=system&s=recharge&a=create";</script>';
            exit;
        }
        
        $last_id = $db->get_var("select id from recharge_card where id >0 order by id desc limit 1");
        $str = 'CZ';
        $s = 889000+$last_id;
        $e = 889000+$last_id+$num;
       
        $startTime = $request['startTime'] ? $request['startTime'] : date('Y-m-d H:i:s');
        $endTime = $request['endTime'] ? $request['endTime'] : date('Y-m-d H:i:s', strtotime("+1 month"));
        for ($i = $s ;$i < $e; $i++){
            $data = array();
            
            $data['card_no'] = $str.$i;
            $data['money'] = $money;
            $data['card_pass'] = substr(md5($data['card_no']),10,6);
            $data['dtTime'] = date('Y-m-d H:i:s');
            $data['status'] = 0;
            $data['userId'] = 0;
            $data['startTime'] = $startTime;
            $data['endTime'] = $endTime;
            //todo 产生二维码

            $db->insert_update('recharge_card', $data,'id');
        }

        redirect("?m=system&s=recharge");
        exit;
    }
}

function batchExport(){}

function importCards()
{
    global $db,$request;
    
	$return = array();
	$return['code'] = 1;
	$return['message'] = '上传成功';
	$reurn['data'] = array();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$filepath = $request['filepath'];
	$filepath = ABSPATH.str_replace('../','',$filepath);
	require_once ABSPATH.'inc/excel.php';
	$pandians = excelToArray($filepath);
	$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
	//file_put_contents('request.txt',$pandianJsonData);
	$pandianJsonData = str_replace("'","\'",$pandianJsonData);
	$pandianJsonData = preg_replace('/((\s)*(\n)+(\s)*)/','',$pandianJsonData);
	$pandianJsonData = stripcslashes($pandianJsonData);
	$jilus = json_decode($pandianJsonData,true);
	
	$errorJilus = array();
	$success_num = 0;
	$fail_num = 0;
	$dtTime = date("Y-m-d H:i:s");
	$fahuoIds = '';
	$jiluId = intval($request['jiluId']);
	$rechargeInfo = $db->get_row("select * from kmd_recharge where id = $jiluId");

    $errorNo = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$card = $db->get_row("select * from recharge_card where card_no='".$jilu[0]."' ");
			if(empty($card)){
			    $success_num++;

                $data = array();
                
                $data['jiluId'] = $rechargeInfo->id;
                $data['image'] = $rechargeInfo->story_img ? $rechargeInfo->story_img : "";
                $data['card_no'] = $jilu[0];
                $data['money'] = floatval($rechargeInfo->price);
                $data['card_pass'] = $jilu[1];
                $data['dtTime'] = date('Y-m-d H:i:s');
                $data['theme_color'] = $rechargeInfo->theme_color;
                $data['status'] = 0;
                $data['userId'] = 0;
                $data['startTime'] = date("Y-m-d 00:00:00", strtotime($jilu[2]));
                $data['endTime'] = date("Y-m-d 23:59:59", strtotime($jilu[3]));
                $data['channelId'] = (int)$rechargeInfo->channelId;
                //todo 产生二维码
    
                $db->insert_update('recharge_card', $data,'id');
			}else{
				$fail_num++;
				$errorJilus[] = $jilu;
				$errorNo .= $jilu[0]."<br>";
			}
		}
		
		if(empty($fail_num)){
			$res = '导入成功';
			$content = '实际导入充值卡'.$success_num.'条，全部导入成功！';
		}else{
			$res = '部分导入成功';
			$content = '实际导入兑换卡'.$success_num.'条，'.$fail_num.'个失败！卡号<br>'.$errorNo.'已经存在！';
		}
		
		echo '{"code":1,"message":"上传成功,'.$content.'","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);
	}
	exit;
}