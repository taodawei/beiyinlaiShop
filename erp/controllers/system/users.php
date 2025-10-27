<?php
function yongjin(){}
function index(){}
function tuanzhang(){}
function basic(){}
function edit_basic(){}
function safe(){}
function liushui(){}
function jifen_jilu(){}
function gift_card(){}
function yhq(){}
function order_jilu(){}
function gift(){}
function fans(){}
function guanzhu_pdt(){}
function guanzhu_history(){}
function guanzhu_shop(){}
function guanzhu_list(){}
function gift_card_luishui(){}
function tuanzhang_info(){}
function operate(){}
function shuju(){}
function chongzhijilu(){}
function tixian(){}
function daochu(){}
function daoru(){}
function daoru1(){}
function daoru2(){}
function report(){}

function yongjinInfo(){}

function getYongJinList()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$pay_type = (int)$request['pay_type'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_yongjin8 where comId=$comId ";
	if(!empty($userId)){
		$sql.=" and userId=$userId";
	}
	if(!empty($type) && in_array($type, [3,1])){//3-提现  1-收入
	    if($type == 1){
		    $sql.=" and type in (0,1,2) ";
	    }else{
            $sql.=" and type in (3,4,5) ";
	    }
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and nickname='$keyword' or username='$keyword'");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId='$keyword' or userId in($userIds))";
	}
	if(!empty($pay_type)){
		switch ($pay_type) {
			case 1:
				$sql.=" and orderInfo like '支付宝充值，支付宝单号%'";
			break;
			case 2:
				$sql.=" and orderInfo like '微信充值，微信单号%'";
			break;
			case 99:
				$sql.=" and remark='后台充值'";
			break;
		}
	}
	if(!empty($money_start)){
		$sql.=" and money>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and money<='$money_end'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$u = $db->get_row("select nickname,username from users where id=$j->userId");
			$j->name = $u->nickname;
			$j->username = $u->username;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->money = $j->money>0?'<span style="color:green">+'.$j->money.'</span>':'<span style="color:red">'.$j->money.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function card(){}

function getCardList()
{
    global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from  user_card where userId = $userId ";
	
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime'";
	}
	if(!empty($userId)){
		$sql.=" and userId='$userId'";
	}
	$count = $db->get_var(str_replace('*','count(id)',$sql));
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->caozuo = '<a href="?m=system&s=users&a=cardLiuShui&cardId='.$j->id.'" style="color:#1f9cd9;">查看消费记录</a>';
		    
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function cardLiuShui(){}

function getCardLiuShui()
{
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$pay_type = (int)$request['pay_type'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	
	$cardId = (int)$request['cardId'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_liushui$fenbiao where comId=$comId and cardId = $cardId ";
	
	if(!empty($userId)){
		$sql.=" and userId=$userId";
	}
	if(!empty($type)){
		$sql.=" and type=$type";
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and nickname='$keyword' or username='$keyword'");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId='$keyword' or userId in($userIds))";
	}
	if(!empty($pay_type)){
		switch ($pay_type) {
			case 1:
				$sql.=" and orderInfo like '支付宝充值，支付宝单号%'";
			break;
			case 2:
				$sql.=" and orderInfo like '微信充值，微信单号%'";
			break;
			case 99:
				$sql.=" and remark='后台充值'";
			break;
		}
	}
	if(!empty($money_start)){
		$sql.=" and money>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and money<='$money_end'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$u = $db->get_row("select nickname,username from users where id=$j->userId");
			$j->name = $u->nickname;
			$j->username = $u->username;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->money = $j->money>0?'<span style="color:green">+'.$j->money.'</span>':'<span style="color:red">'.$j->money.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function history(){}
function getHistoryList()
{
	global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select startTime,endTime,SUM(self_total) self_total, SUM(self_fenhong) self_fenhong, SUM(team_total) team_total, SUM(team_fenhong) team_fenhong, SUM(direct_total) direct_total, SUM(direct_fenhong) direct_fenhong, SUM(indirect_total) indirect_total, SUM(indirect_fenhong) indirect_fenhong,SUM(total_fenhong) total_fenhong  from  user_month_fenhong where 1 = 1 and startTime is not null ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or phone like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and userId in($pdtId)";
	}
	if(!empty($startTime)){
		$sql.=" and startTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and endTime<='$endTime'";
	}

	$count = $db->get_var(str_replace('startTime,endTime,SUM(self_total) self_total, SUM(self_fenhong) self_fenhong, SUM(team_total) team_total, SUM(team_fenhong) team_fenhong, SUM(direct_total) direct_total, SUM(direct_fenhong) direct_fenhong, SUM(indirect_total) indirect_total, SUM(indirect_fenhong) indirect_fenhong,SUM(total_fenhong) total_fenhong','count(startTime)',$sql));
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);

	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
	        if(!$j->startTime){
	            continue;
	        }
// 			$user = $db->get_row("select * from users where id = $j->userId");

			$j->month = date('Y年m月', strtotime($j->startTime));
		    $j->month = '<a href="?m=system&s=users&a=dabiao&startTime='.date('Y-m-d H:i', strtotime($j->startTime)).'&endTime='.date('Y-m-d H:i', strtotime($j->endTime)).'" style="color:#1f9cd9;">'.$j->month.'</a>';
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function total(){}
function getTotalList(){
     global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from  user_tuan_price where 1 = 1  ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or phone like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and userId in($pdtId)";
	}
	
	if($type > 0){
	    switch($type){//1-个人业绩  2-团队业绩 3-直推业绩 4-间推业绩
	        case 1:
	            $sql .= " and remark = '个人业绩累计' ";
	            break;
	        case 2:
	            $sql .= " and remark = '下级业绩累计' ";
	            break;
	        case 3:
	            $sql .= " and remark = '直推返利' ";
	            break;
	        case 4:
	            $sql .= " and remark = '间推返利' ";
	            break;         
	    }
	}
	
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($userId)){
		$sql.=" and b.userId='$userId'";
	}
	$count = $db->get_var(str_replace('*','count(id)',$sql));
	$config = $db->get_results("select * from zc_release where id > 0");
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$startTime = date('Y-m-01 00:00:00');
	$endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    
		    $user = $db->get_row("select * from users where id = $j->userId");
			$j->userInfo = $user->nickname.'('.$user->phone.')';
		
            $fromUser = $db->get_row("select * from users where id = $j->from_user");
            if($fromUser){
                $j->fromUser = $fromUser->nickname.'('.$fromUser->phone.')';
            }
			
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function yeji(){}
function getYejiList()
{
    global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'order_price':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from  users where 1 = 1 and level = 74 ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or phone like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and id in($pdtId)";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime'";
	}
	if(!empty($userId)){
		$sql.=" and b.userId='$userId'";
	}
	$count = $db->get_var(str_replace('*','count(id)',$sql));
	$config = $db->get_results("select * from zc_release where id > 0");
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$startTime = date('Y-m-01 00:00:00');
	$endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->userInfo = $j->nickname.'('.$j->phone.')';
		
			$j->month = date('Y年m月', strtotime($j->dtTime));
			
			$userId = $v->id = $j->id;
            $selfTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $userId and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $userId ");
            $selfTotal = $selfTotal ? $selfTotal : 0;
            // var_dump($selfTotal);
            $bili = $selfFenhong = 0;
            if($selfTotal > 0){
                foreach($config as $kk=>$vv){ 
                    if($selfTotal >= $vv->min && $selfTotal < $vv->max){
                         $bili = $vv->bili ;
                    }
                }
                $selfFenhong = bcmul($selfTotal, $bili, 2);
            }

            $user_price = $xiaji_price = $xiajisub_price = $bili = 0;
            //查询所有下级  大于10万用户
            $user_xiaji = $db->get_results("select id,nickname,level from users where  shangji =$v->id ");
            $userXiajiData = [];
            foreach($user_xiaji as $user_xiaji_k=>$user_xiaji_v){
                //当前用户 返利金额减去所有下级 返利金额
                $childTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $user_xiaji_v->id and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $user_xiaji_v->id ");
                $childTotal = $childTotal ? $childTotal : 0;
                $xiaji_price = bcadd($xiaji_price, $childTotal, 2);
                foreach($config as $kk=>$vv){
                    if($childTotal >= $vv->min && $childTotal < $vv->max){
                        $childFenhong = bcmul($vv->bili, $childTotal, 2);
                        $userXiajiData[] = array(
                            'total' => $childTotal,
                            'bili' => $vv->bili,
                            'fenhong' => $childFenhong
                        );
                        $xiajisub_price = bcadd($xiajisub_price, $childFenhong, 2);
                    }
                }        
            }
            
            foreach($config as $kk=>$vv){ 
                //当前用户返利金额及比例
                if($xiaji_price >= $vv->min && $xiaji_price < $vv->max){
                     $bili = $vv->bili ;
                }
            }
   
            $user_price1 = bcmul($xiaji_price, $bili, 2);
            $user_price = bcsub($user_price1, $xiajisub_price, 2);
            
            $directTotal = $directFenhong = $indirectTotal = $indirectFenhong = 0;
            $directTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $directTotal = $directTotal ? $directTotal : 0;
            if($directTotal > 0){
                $directFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
                $directFenhong = $directFenhong ? $directFenhong : 0;
            }
            
            $indirectTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $indirectTotal = $indirectTotal ? $indirectTotal : 0;
            if($indirectTotal > 0){
                $indirectFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
                $indirectFenhong = $indirectFenhong ? $indirectFenhong : 0;
            }
            
            $totalFenhong = bcadd($selfFenhong, $user_price, 2);
            $totalFenhong = bcadd($totalFenhong, $directFenhong, 2);
            $totalFenhong = bcadd($totalFenhong, $indirectFenhong, 2);
            $j->userInfo = '<a href="?m=system&s=users&a=total&keyword='.$j->nickname.'&type=0&startTime='.date('Y-m-d', strtotime($startTime)).'&endTime='.date('Y-m-d', strtotime($endTime)).'" style="color:#1f9cd9;">'.$j->userInfo.'</a>';
            $fenhongLog = array(
                'id' => 0,
                'userId' => $userId,
                'userInfo' => $j->userInfo,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'self_total' => $selfTotal,
                'self_fenhong' => $selfFenhong,
                'team_total' => $xiaji_price,
                'team_fenhong' => $user_price,
                'direct_total' => $directTotal,//直推业绩
                'direct_fenhong' => $directFenhong,//直推分红
                'indirect_total' => $indirectTotal,//间推业绩
                'indirect_fenhong' => $indirectFenhong,//间推分红
                'total_fenhong' => $totalFenhong,
                'xiajiFenhong_info' => $userXiajiData,
                'dtTime' => date('Y-m-d H:i:s')
            );
			
			$dataJson['data'][] = $fenhongLog;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function getDabiaoListBak()
{
	global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id, userId, total_order_num, dtTime from  user_tuan_fenhong where comId=$comId and  is_dabiao = 1 ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or phone like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and userId in($pdtId)";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime'";
	}
	if(!empty($userId)){
		$sql.=" and b.userId='$userId'";
	}
	$count = $db->get_var(str_replace('id, userId, total_order_num, dtTime','count(id)',$sql));
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$data = array();
			$data['id'] = $j->id;
			$data['userId'] = $j->userId;
			$user = $db->get_row("select * from users where id = $j->userId");
			$data['userInfo'] = '<a href="?m=system&s=users&a=order_jilu&id='.$j->userId.'&returnurl=%3Fm%3Dsystem%26s%3Dusers%26a%3Ddabiao" style="color:#1f9cd9;">'.$user->nickname.'('.$user->phone.')</a>';;
			$data['total_order_num'] = getXiaoshu($j->total_order_num,2);
			$data['month'] = date('Y年m月', strtotime($j->dtTime));
		    
			$dataJson['data'][] = $data;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function getDabiaoList()
{
	global $db,$request;
	
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from  user_month_fenhong where 1 = 1 ";
	if(!empty($keyword)){
		$pdtId = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or phone like '%$keyword%') limit 20");
		if(empty($pdtId))$pdtId=0;
		$sql.=" and userId in($pdtId)";
	}
	if(!empty($startTime)){
	    $startTime = date('Y-m-d H:i:00', strtotime($startTime));
		$sql.=" and startTime>='$startTime'";
	}
	if(!empty($endTime)){
	    $endTime = date('Y-m-d H:i:59', strtotime($endTime));
		$sql.=" and endTime<='$endTime'";
	}
	if(!empty($userId)){
		$sql.=" and b.userId='$userId'";
	}
	$count = $db->get_var(str_replace('*','count(id)',$sql));
	
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
	
			$user = $db->get_row("select * from users where id = $j->userId");
			$j->userInfo = $user->nickname.'('.$user->phone.')';
		    
		    $j->userInfo = '<a href="?m=system&s=users&a=total&keyword='.$user->nickname.'&type=0&startTime='.date('Y-m-d', strtotime($j->startTime)).'&endTime='.date('Y-m-d', strtotime($j->endTime)).'" style="color:#1f9cd9;">'.$j->userInfo.'</a>';
			$j->month = date('Y年m月', strtotime($j->startTime));
		    
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function shenhe1()
{
    global $db,$request;
    
    $id = (int)$request['jiluId'];
    $content = $request['cont'];
    $status = (int)$request['status'];
    $comId = (int)$_SESSION[TB_PREFIX . 'comId'];
    $fenbiao = getFenbiao($comId,20);
    $db->query("update users set renzheng_msg = '$content',renzheng = $status where id = $id");
    
    echo '{"code":1,"message":"操作成功"}';
    exit;
}

function feedbackDetail(){}
function getFeedbackList()
{
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$channelId = $request['channelId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from feedback_log where 1=1 ";
	
	if($channelId){
	   // $channelStr = $db->get_var("select group_concat(distinct(feed_type)) from feedback_log where feed_type <> '' ");

    //     $channels = explode(',', $channelStr);
    //     $feedType = $channels[($channelId -1)];
        
        $sql .= " and feed_type = '$channelId' ";
	}
	
    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (name like '%$keyword%' or phone like '%$keyword%') ";
    }
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());

	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
	        $user = $db->get_row("select * from users where id =".$j->userId);
	       
	        $j->content = '<span onmouseover="tips(this,\''.stripcslashes($j->content).'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($j->content),100,true).'</span>';
	        $temp = array(
	            'id' => $j->id,
	            'userId' => $j->userId,
	            'name' => $j->name,
	            'phone' => $j->phone,
	            'feed_type' => $j->feed_type,
	            'content' => $j->content,
	            'dtTime' => $j->dtTime,
	           // 'action' => '<a href="javascript:" onclick="z_confirm(\'确定要该消息吗？\',del_file,'.$j->id.');"><img src="images/biao_137.png"/> 删除</a>'
	        );
			$dataJson['data'][] = $temp;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function shequ(){}
function shenqing(){}
function daochu_shenqing(){}
function daochuExcel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	require_once ABSPATH.'inc/excel.php';
	$pandianJsonData = stripcslashes($request['pandianJsonData']);
	$jilus = json_decode($pandianJsonData,true);
	$indexKey = array('会员编号','姓名','手机号','密码','会员等级','所属门店','性别','出生日期');
	$addRows = $db->get_var("select addRows from user_shezhi where comId=$comId");
	if(!empty($addRows)){
		$arry = unserialize($addRows);
		foreach ($arry as $v){
			array_push($indexKey,$v['name']);
		}
	}
	exportExcel($jilus,'导入会员失败记录',$indexKey);
	exit;
}
function downMoban(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	require_once ABSPATH.'inc/excel.php';
	$jilus = array();
	$indexKey = array('会员编号','姓名','手机号','密码','会员等级','所属门店','性别','出生日期');
	$addRows = $db->get_var("select addRows from user_shezhi where comId=$comId");
	if(!empty($addRows)){
		$arry = unserialize($addRows);
		foreach ($arry as $v){
			array_push($indexKey,$v['name']);
		}
	}
	exportExcel($jilus,'会员上传模板',$indexKey);
	exit;
}
function create(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$user = array();
		$user['id'] = (int)$request['id'];
		$user['comId'] = $comId;
		$user['nickname'] = $request['nickname'];
		$user['phone'] = $request['phone'];
		$user['sn'] = $request['sn'];
		$password = $request['password'];
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		if(!empty($password)){
			$shlencryption = new shlEncryption($password);
			$user['password'] = $shlencryption->to_string();
		}
		$user['level'] = (int)$request['level'];
		$user['status'] = 1;
		$user['mendianId'] = (int)$request['mendianId'];
		$user['sex'] = (int)$request['sex'];
		$user['birthday'] = $request['birthday'];
		if($user['id']>0){
			$user['dtTime'] = date("Y-m-d H:i:s");
		}
		if(!empty($request['com_added']))$user['addRows'] = json_encode($request['com_added'],JSON_UNESCAPED_UNICODE);
		insert_update('users',$user,'id');
		if(empty($user['id'])){
			$userId = $db->get_var("select last_insert_id();");
// 			$zhishangId = reg_zhishang($userId,$user['username'],$user['password'],$user['nickname']);
// 			$db->query("update users set zhishangId=$zhishangId where id=$userId");
		}

		redirect("?s=users");
	}
}
function check_username(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$username = $request['username'];
	$ifhas = $db->get_var("select id from users where comId=$comId and username='$username' limit 1");
	if(empty($ifhas)){
		echo '{"code":1}';
	}else{
		echo '{"code":0,"message":"该手机号已经注册过会员啦！"}';
	}
	exit;
}
function tixian_deal(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];

	$id = (int)$request['jiluId'];
	$tixian = $db->get_row("select id,status,money,userId from user_tixian where id=$id and comId=$comId");
	if(empty($tixian)||$tixian->status!=0){
		echo '{"code":0,"message":"该申请已经被处理过了，请不要重复处理！"}';
		exit;
	}
	$status = (int)$request['status'];
	$shenheCont = $request['cont'];
	$shenheName = $_SESSION[TB_PREFIX.'name'];
	$shenheTime = date("Y-m-d H:i:s");
	$db->query("update user_tixian set status=$status,shenheTime='$shenheTime',shenheName='$shenheName',shenheCont='$shenheCont' where id=$id");
	if($status==-1){
		$fenbiao = getFenbiao($comId,20);
		$db->query("update users set money=money+$tixian->money where id=$tixian->userId");
		$yue = $db->get_var("select money from users where id=$tixian->userId");
		$liushui = array();
		$liushui['userId']=$tixian->userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$tixian->money;
		$liushui['yue']=$yue;
		$liushui['type']=3;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='提现作废';
		$liushui['orderInfo']='提现申请被作废';
		insert_update('user_liushui'.$fenbiao,$liushui,'id');
	}
	echo '{"code":1,"message":"操作成功"}';
}
function chongzhi(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$fenbiao = getFenbiao($comId,20);
		$userId = (int)$request['userId'];
		$money = $request['money'];
		$beizhu = $request['beizhu'];
		$uname = $_SESSION[TB_PREFIX.'name'];
		$yue = $db->get_var("select money from users where id=$userId and comId=$comId");
		if($money+$yue<0){
			echo '{"code":0,"message":"会员余额不足，扣款失败！"}';
			exit;
		}
		$db->query("update users set money=money+$money where id=$userId and comId=$comId");
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$money;
		$liushui['yue']=$money+$yue;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='后台充值';
		$liushui['orderInfo']='操作者：'.$uname.'，备注：'.$beizhu;
		insert_update('user_liushui'.$fenbiao,$liushui,'id');
		echo '{"code":1,"message":"充值成功"}';
		exit;
	}
}
function getcitylist(){
    global $db,$request;
    $province=$request["province"];
    if($province==0){
        $citylist=[];
    }else{
        $citylist=$db->get_results("select * from demo_area where parentId=$province order by id asc");
    }
	$dataJson['code']=1;
	$dataJson['list']=$citylist;
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function dabiao(){}

function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$level = (int)$request['level'];
	$mendianId = (int)$request['mendianId'];
	$shangji = (int)$request['shangji'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$jifen_start = (int)$request['jifen_start'];
	$jifen_end = (int)$request['jifen_end'];
	$dtTime_start = $request['dtTime_start'];
	$dtTime_end = $request['dtTime_end'];
	$login_start = $request['login_start'];
	$login_end = $request['login_end'];
	$selectedIds = $request['selectedIds'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('userPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$province=$request["province"];
	$city=$request["city"];
	$tijiao="";
	if($province>0){
	    $tijiao.=" and province=$province";
	}
	if($city>0){
	    $tijiao.=" and city=$city";
	}
	$sql = "select id,nickname,username,level,province,city,renzheng,mendianId,money,jifen,cost,lastLogin,status,dtTime,zhishangId,if_shequ_tuan,if_tuanzhang,shangji,yongjin,yongjins,phone,order_price,is_dabiao,wx_money from users where comId=$comId $tijiao ";
	
	$isdabiao = (int)$request['is_dabiao'];
	if($isdabiao){
	    $sql .= " and is_dabiao = 1 ";
	}
	
// 	$orderPrice = $request['order_price'];
// 	if($orderPrice){
// 	    $sql .= " and order_price >= ".$orderPrice;
// 	}
	
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($keyword)){
		$sql.=" and (nickname like '%$keyword%' or username like '%$keyword%')";
	}
	if(!empty($mendianId)){
		$sql.=" and mendianId=$mendianId";
	}
	if(!empty($money_start)){
		$sql.=" and money>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and money<='$money_end'";
	}
	if(!empty($jifen_start)){
		$sql.=" and jifen>=$jifen_start";
	}
	if(!empty($jifen_end)){
		$sql.=" and jifen<=$jifen_end";
	}
	if(!empty($dtTime_start)){
		$sql.=" and dtTime>='$dtTime_start'";
	}
	if(!empty($dtTime_end)){
		$sql.=" and dtTime<='$dtTime_end'";
	}
	if(!empty($selectedIds)){
		$sql.=" and id not in($selectedIds)";
	}
	if(!empty($shangji)){
		$type = (int)$request['type'];
		if($type==1){
			$sql.=" and shangji=$shangji";	
		}else{
			$sql.=" and tuan_id=$shangji";
		}
		
	}
	$countsql = str_replace('id,nickname,username,level,province,city,renzheng,mendianId,money,jifen,cost,lastLogin,status,dtTime,zhishangId,if_shequ_tuan,if_tuanzhang,shangji,yongjin,yongjins,phone,order_price,is_dabiao,wx_money','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	$order_fenbiao = $fenbiao;
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$fenbiao = 10;
	}
	$fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
	if(!empty($jilus)){
		foreach ($jilus as $i=>&$j) {
			$j->layclass= '';
			if($j->status!=1){
				$j->layclass= 'deleted';
			}
			$if_order = $db->get_var("select id from order$order_fenbiao where userId=$j->id and status <> -1 limit 1");
			if($if_order>0){
				$j->nickname = $j->nickname.'(<font color="red">买过</font>)';
			}
			$j->username = strlen($j->username)>11?sys_substr($j->username,11,true):$j->username;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->level = empty($j->level)?'无':$db->get_var("select title from user_level where id=$j->level");
			if($j->if_shequ_tuan==1){
				$j->level = '社区站长';
			}else if($j->if_tuanzhang){
				$j->level = '分销团长';
			}
			$fans_num = $db->get_var("select count(*) from users where comId=$comId and shangji=$j->id");
			$j->fans_num = '<a href="javascript:" onclick="user_info(\'fans\','.$j->id.');">'.$fans_num.'</a>';
			if($fanli_type==2){
				$j->fans_num1 = $db->get_var("select count(*) from users where comId=$comId and tuan_id=$j->id and id<>$j->id");
			}else{
				$j->fans_num1 = $db->get_var("select count(*) from users where comId=$comId and shangshangji=$j->id");
			}
			
			$j->fans_num1 = $db->get_var("select count(*) from users where comId=$comId and (shangshangji=$j->id or shangji = $j->id)");
			$j->renzheng = $j->renzheng==1?'已认证':'未认证';
			$j->status1 = $j->status==0?'已禁用':'已开启';
			
			$j->dabiao = $j->is_dabiao==0?'未达标':'已达标';
			
			$j->mendian  =$db->get_var("select title from mendian where id=$j->mendianId");
			$j->yhq = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$j->id");
			$j->gift_card = $db->get_var("select sum(yue) from gift_card$fenbiao where comId=$comId and userId=$j->id");
			if(empty($j->gift_card))$j->gift_card='0';
			$j->nickname = '<span onclick="user_info(\'basic\','.$j->id.')" style="cursor:pointer;">'.$j->nickname.'</span>';
			$j->money = '<span onclick="user_info(\'liushui\','.$j->id.')" style="cursor:pointer;color:#f00">'.$j->wx_money.'</span>';
			
			$j->yongjin = '<span onclick="user_info(\'yongjinInfo\','.$j->id.')" style="cursor:pointer;color:#f00">'.$j->yongjin.'</span>';
			
			$j->jifen = '<span onclick="user_info(\'jifen_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->jifen.'</span>';
			$j->yhq = '<span onclick="user_info(\'yhq\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->yhq.'</span>';
			$j->gift_card = '<span onclick="user_info(\'gift_card\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->gift_card.'</span>';
			$j->cost = '<span onclick="user_info(\'order_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->cost.'</span>';
			$j->lastLogin = empty($j->lastLogin)?'':date("Y-m-d H:i",strtotime($j->lastLogin));
			if(!empty($j->shangji)){
				$shangji = $db->get_row("select nickname,username from users where id=$j->shangji");
				$j->shangji = $shangji->nickname.'('.$shangji->username.')';
			}else{
				$j->shangji = '无';
			}
			$province=$db->get_var("select title from demo_area where id=$j->province");
			$city=$db->get_var("select title from demo_area where id=$j->city");
			$j->cityname="";
			if(!empty($province)&&!empty($city)){
			    $j->cityname=$province."-".$city;
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getTuanzhangList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$level = (int)$request['level'];
	$mendianId = (int)$request['mendianId'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$jifen_start = (int)$request['jifen_start'];
	$jifen_end = (int)$request['jifen_end'];
	$dtTime_start = $request['dtTime_start'];
	$dtTime_end = $request['dtTime_end'];
	$login_start = $request['login_start'];
	$login_end = $request['login_end'];
	$selectedIds = $request['selectedIds'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('userPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,nickname,username,money,jifen,earn,lastLogin,status,dtTime,zhishangId,if_tuanzhang from users where comId=$comId and if_tuanzhang in(-1,1) ";
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($keyword)){
		$sql.=" and (id='$keyword' or nickname like '%$keyword%' or username like '%$keyword%')";
	}
	if(!empty($mendianId)){
		$sql.=" and mendianId=$mendianId";
	}
	if(!empty($money_start)){
		$sql.=" and money>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and money<='$money_end'";
	}
	if(!empty($jifen_start)){
		$sql.=" and jifen>=$jifen_start";
	}
	if(!empty($jifen_end)){
		$sql.=" and jifen<=$jifen_end";
	}
	if(!empty($dtTime_start)){
		$sql.=" and dtTime>='$dtTime_start'";
	}
	if(!empty($dtTime_end)){
		$sql.=" and dtTime<='$dtTime_end'";
	}
	if(!empty($selectedIds)){
		$sql.=" and id not in($selectedIds)";
	}
	$countsql = str_replace('id,nickname,username,money,jifen,earn,lastLogin,status,dtTime,zhishangId,if_tuanzhang','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	$order_fenbiao = $fenbiao;
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$fenbiao = 10;
	}
	$fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->layclass= '';
			if($j->if_tuanzhang!=1){
				$j->layclass= 'deleted';
				$j->status = -1;
			}
			/*$if_order = $db->get_var("select id from order$order_fenbiao where zhishangId=$j->zhishangId limit 1");
			if($if_order>0){
				$j->nickname = $j->nickname.'(<font color="red">买过</font>)';
			}*/
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			/*$j->nickname = '<span onclick="user_info(\'basic\','.$j->id.')" style="cursor:pointer;">'.$j->nickname.'</span>';
			$j->money = '<span onclick="user_info(\'money_jilu\','.$j->id.')" style="cursor:pointer;color:#f00">'.$j->money.'</span>';
			$j->jifen = '<span onclick="user_info(\'jifen_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->jifen.'</span>';
			$j->yhq = '<span onclick="user_info(\'yhq\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->yhq.'</span>';
			$j->gift_card = '<span onclick="user_info(\'gift_card\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->gift_card.'</span>';
			$j->cost = '<span onclick="user_info(\'order_jilu\','.$j->id.')" style="cursor:pointer;color:#3084d9;">'.$j->cost.'</span>';*/
			$j->xiaji_num = $db->get_var("select count(*) from users where shangji=$j->id");
			$j->team_num = $db->get_var("select count(*) from users where ".($fanli_type==1?'shangshangji':'tuan_id')."=$j->id");
			$j->lastLogin = empty($j->lastLogin)?'':date("Y-m-d H:i",strtotime($j->lastLogin));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_select_users(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$level = (int)$request['level'];
	$keyword = $request['keyword'];
	$order1 = 'id';
	$order2 = 'desc';
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select id,nickname,username,level from users where comId=$comId ";
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($keyword)){
		$sql.=" and (nickname like '%$keyword%' or username like '%$keyword%')";
	}
	$countsql = str_replace('id,nickname,username,level','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->level = empty($j->level)?'无':$db->get_var("select title from user_level where id=$j->level");
			$j->select = '<a href="javascript:" onclick="select_user('.$j->id.',\''.$j->nickname.'\',\''.$j->username.'\');" style="color:#31baf3">选择</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function jinyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update users set status=0 where id=$id and comId=$comId");
	echo '{"code":1,"message":"禁用成功！"}';
	exit;
}
function qiyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update users set status=1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"启用成功！"}';
	exit;
}
//禁用团长
function jin_tuanzhang(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update users set if_tuanzhang=-1 where id=$id and comId=$comId");
	$db->query("update users set tuan_id=0 where comId=$comId and tuan_id=$id");
	add_user_oprate('管理员撤销团长！',2,$id);
	echo '{"code":1,"message":"禁用成功！"}';
	exit;
}
function qi_tuanzhang(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update users set if_tuanzhang=0 where id=$id and comId=$comId");
	echo '{"code":1,"message":"启用成功！"}';
	exit;
}
function delete(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$u = $db->get_row("select money,username from users where id=$id and comId=$comId");
	if($u->money>0){
		echo '{"code":0,"message":"该账户余额大于0，不能删除！"}';
		exit;
	}
	$ifhas = $db->get_var("select id from order$fenbiao where comId=$comId and userId=$id limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"该会员名下已存在订单，不能删除！"}';
		exit;
	}
	$db->query("delete from users where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function updatePass(){
	global $db,$request;
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$shlencryption = new shlEncryption($request['password']);
	$password = $shlencryption->to_string();
	$db->query("update users set password='$password' where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function updatePaypass(){
	global $db,$request;
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$shlencryption = new shlEncryption($request['password']);
	$password = $shlencryption->to_string();
	$db->query("update users set payPass='$password' where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function daochu_chongzhi(){}
function get_liushui_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$pay_type = (int)$request['pay_type'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_liushui$fenbiao where comId=$comId ";
	
	$source = (int)$request['source'];
	$cardId = (int)$request['cardId'];
	if($source > 0 && $cardId == 0){
	    switch($source){
	        case 1:
	            $sql .= " and cardId = 0 and remark like '%充值%' ";
	            break;
	        case 2:
	            $sql .= " and cardId = 0 ";
	            break;
	    }
	}
	
// 	echo  $sql;die;
	if(!empty($userId)){
		$sql.=" and userId=$userId";
	}
	
	if($cardId > 0){
	    $sql .= " and cardId = $cardId ";    
	}
	
	if(!empty($type)){
		$sql.=" and type=$type";
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and nickname='$keyword' or username='$keyword'");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId='$keyword' or userId in($userIds))";
	}
	if(!empty($pay_type)){
		switch ($pay_type) {
			case 1:
				$sql.=" and orderInfo like '支付宝充值，支付宝单号%'";
			break;
			case 2:
				$sql.=" and orderInfo like '微信充值，微信单号%'";
			break;
			case 99:
				$sql.=" and remark='后台充值'";
			break;
		}
	}
	if(!empty($money_start)){
		$sql.=" and money>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and money<='$money_end'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$u = $db->get_row("select nickname,username from users where id=$j->userId");
			$j->name = $u->nickname;
			$j->username = $u->username;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->money = $j->money>0?'<span style="color:green">+'.$j->money.'</span>':'<span style="color:red">'.$j->money.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_tixian_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$type = (int)$request['type'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_tixian where comId=$comId ";
	if(!empty($type)){
		if($type==99)$type=0;
		$sql.=" and status=$type";
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and nickname='$keyword' or username='$keyword'");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$u = $db->get_row("select nickname,username,money from users where id=$j->userId");
			$j->name = $u->nickname;
			$j->username = $u->username;
			$j->usermoney = $u->money;
			$status = '';
			$j->layclass = '';
			switch ($j->status){
				case 0:
					$status = '<span style="color:red">待审核</span>';
				break;
				case 1:
					$status = '<span style="color:green">已审核</span>';
				break;
				case -1:
					$status = '已拒绝';
					$j->layclass = 'deleted';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			$info = $db->get_row("select * from user_bank where userId=$j->userId");
			$j->info = $info->bank_card.'('.$info->bank_name.')<br>开户人：'.$info->name;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_jifen_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_jifen$fenbiao where comId=$comId and userId=$userId ";
	if(!empty($type)){
		$sql.=" and type=$type";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->jifen = $j->jifen>0?'<span style="color:green">+'.$j->jifen.'</span>':'<span style="color:red">'.$j->jifen.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_giftcard_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from gift_card$fenbiao where comId=$comId and userId=$userId ";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->caozuo = '<a href="?m=system&s=users&a=gift_card_luishui&id='.$j->id.'" style="color:#1f9cd9;">查看消费记录</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_giftcard_liushui(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from gift_card_liushui$fenbiao where cardId=$id";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//根据id绑定卡
function bind_card(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$cardId = (int)$request['cardId'];
	$userId = (int)$request['userId'];
	$fenbiao = getFenbiao($comId,20);
	$card = $db->get_row("select id,userId,endTime,jiluId from gift_card$fenbiao where id=$cardId and comId=$comId");
	if(empty($card)){
		echo '{"code":0,"message":"无效的礼品卡卡号"}';
		exit;
	}else if($card->userId>0){
		echo '{"code":0,"message":"该礼品卡已经绑定过了！"}';
		exit;
	}else{
		$jiluStatus = $db->get_var("select status from gift_card_jilu where id=$card->jiluId");
		if($jiluStatus!=1){
			echo '{"code":0,"message":"该礼品卡已经作废了，不能进行绑定！"}';
			exit;
		}
		$time1 = time();
		$time2 = strtotime($card->endTime.' 23:59:59');
		if($time1>$time2){
			echo '{"code":0,"message":"该礼品卡已经过期了！"}';
			exit;
		}
		$db->query("update gift_card$fenbiao set userId=$userId,bind_time='".date("Y-m-d H:i:s")."',bind_user=".$_SESSION[TB_PREFIX.'admin_userID']." where id=$card->id");
		$db->query("update gift_card_jilu set bind_num=bind_num+1 where id=$card->jiluId");
		echo '{"code":1,"message":"绑定成功！"}';
	}
}
//根据卡号绑定卡
function bind_gift_card(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$cardId = $request['cardId'];
	$userId = (int)$request['userId'];
	$fenbiao = getFenbiao($comId,20);
	$card = $db->get_row("select id,userId,endTime,jiluId from gift_card$fenbiao where comId=$comId and cardId='$cardId' limit 1");
	if(empty($card)){
		echo '{"code":0,"message":"无效的礼品卡卡号"}';
		exit;
	}else if($card->userId>0){
		echo '{"code":0,"message":"该礼品卡已经绑定过了！"}';
		exit;
	}else{
		$jiluStatus = $db->get_var("select status from gift_card_jilu where id=$card->jiluId");
		if($jiluStatus!=1){
			echo '{"code":0,"message":"该礼品卡已经作废了，不能进行绑定！"}';
			exit;
		}
		$time1 = time();
		$time2 = strtotime($card->endTime.' 23:59:59');
		if($time1>$time2){
			echo '{"code":0,"message":"该礼品卡已经过期了！"}';
			exit;
		}
		$db->query("update gift_card$fenbiao set userId=$userId,bind_time='".date("Y-m-d H:i:s")."',bind_user=".$_SESSION[TB_PREFIX.'admin_userID']." where id=$card->id");
		$db->query("update gift_card_jilu set bind_num=bind_num+1 where id=$card->jiluId");
		echo '{"code":1,"message":"绑定成功！"}';
	}
}
function get_yhq_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$type = (int)$request['type'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_yhq$fenbiao where comId=$comId and userId=$userId";
	switch ($type){
		case 0:
			$sql.=" and status=0 and endTime>'".date("Y-m-d H:i:s")."'";
		break;
		case 1:
			$sql.=" and status=1";
		break;
		case 2:
			$sql.=" and status=0 and endTime<='".date("Y-m-d H:i:s")."'";
		break;
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->endTime = $j->startTime.' ~ '.$j->endTime;
			$j->caozuo = '查看 <span class="hyxx_youhuiquan_down_tt_span1" onclick="show_yhq_info(this,'.$j->id.');"></span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_gift_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_gift where userId=$userId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pdtInfo = json_decode($j->pdtInfo);
			$j->image = '<img src="/inc/img/nopic.svg">';
			if(!empty($pdtInfo->image))$j->image = '<img src="'.$pdtInfo->image.'">';
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->mendian = '无';
			if($j->mendianId>0)$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_pdt_collect(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$request['userId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = 'dtTime';
	$order2 = 'desc';
	$sql = "select * from user_pdt_collect where userId=$userId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pdtInfo = $db->get_row("select sn,title,key_vals from demo_product_inventory where id=$j->inventoryId");
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_pdt_history(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$request['userId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = 'dtTime';
	$order2 = 'desc';
	$sql = "select * from user_pdt_history where userId=$userId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pdtInfo = $db->get_row("select sn,title,key_vals from demo_product_inventory where id=$j->inventoryId");
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->key_vals = $pdtInfo->key_vals;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_shop_collect(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$request['userId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = 'dtTime';
	$order2 = 'desc';
	$sql = "select * from user_shop_collect where userId=$userId";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pdtInfo = $db->get_row("select sn,title from mendian where id=$j->shopId");
			$j->sn = $pdtInfo->sn;
			$j->title = $pdtInfo->title;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_order_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	if($_SESSION['if_tongbu']==1){
		$zhishangId = $db->get_var("select zhishangId from users where id=$userId");
	}
	$sql = "select id,orderId,price_payed,mendianId,pay_type,dtTime,price,status,comId from order$fenbiao where comId=$comId ";
	if($_SESSION['if_tongbu']==1){
		$sql.=" and zhishangId=$zhishangId";
	}else{
		$sql.=" and userId=$userId";
	}
	if(!empty($keyword)){
		$sql.=" and orderId='$keyword'";
	}
	if(!empty($money_start)){
		$sql.=" and price_payed>='$money_start'";
	}
	if(!empty($money_end)){
		$sql.=" and price_payed<='$money_end'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('id,orderId,price_payed,mendianId,pay_type,dtTime,price,status','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->caozuo = '<a href="javascript:" onclick="view_order('.$j->id.','.$userId.','.$j->comId.');" style="color:#3ab2ee;">查看订单</a>';
			switch ($j->status) {
				case 0:
					$j->statusInfo = '<span style="color:#cf2950;">待审核</span>';
				break;
				case 2:
					$j->statusInfo = '<span style="color:#cf2950;">待发货</span>';
				break;
				case 3:
					$j->statusInfo = '<span style="color:#cf2950;">待收货</span>';
				break;
				case 4:
					$j->statusInfo = '<span style="color:green;">已完成</span>';
				break;
				case -1:
					$j->statusInfo = '<span style="color:#f00;">无效</span>';
				break;
				case -3:
					$j->statusInfo = '<span style="color:#f00;">退换货</span>';
				break;
			}
			$j->mendian = $db->get_var("select com_title from demo_shezhi where comId=$j->comId");
			$j->view = '<a href="javascript:" onclick="order_show('.$i.','.$j->comId.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_operate_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$request['userId'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from user_oprate$fenbiao where userId=$userId and comId=$comId";
	if(!empty($keyword)){
		$sql.=" and content like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$terminal = '';
			switch ($j->terminal){
				case 1:
					$terminal = 'PC';
				break;
				case 2:
					$terminal = '手机端';
				break;
				case 3:
					$terminal = '客户端';
				break;
				default:
					$terminal = '未知';
				break;
			}
			$j->terminal = $terminal;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function searchUsers(){
	global $db,$request;
	$keyword = $request['keyword'];
	$jifen = (int)$request['jifen'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$sql = "select id,username,nickname,money,jifen from users where comId=$comId and status=1";
	if(!empty($keyword)){
		$sql.=" and (username like '%$keyword%' or nickname like '%$keyword%')";
	}
	$sql.=" order by id desc limit 8";
	$pdts = $db->get_results($sql);
	$str = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt){
			$str.='<li onclick="selectKehu('.$pdt->id.',\''.$pdt->username.'('.$pdt->nickname.')'.'\',\''.($jifen==1?$pdt->jifen:$pdt->money).'\')"><a href="javascript:" >'.$pdt->username.'('.$pdt->nickname.')'.'</a></li>';
		}
	}else{
		$str='<li style="padding:20px;text-align:center;">未找到会员信息</li>';
	}
	echo $str;
	exit;
}
function searchZhishangUsers(){
	global $request;
	$db_service = getCrmDb();
	$keyword = $request['keyword'];
	$sql = "select id,username,name from demo_user where 1=1";
	if(!empty($keyword)){
		$sql.=" and (username like '%$keyword%' or name like '%$keyword%')";
	}
	$sql.=" order by id desc limit 8";
	$pdts = $db_service->get_results($sql);
	$str = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt){
			$str.='<li onclick="selectKehu('.$pdt->id.',\''.$pdt->username.'('.$pdt->name.')'.'\',\'0\')"><a href="javascript:" >'.$pdt->username.'('.$pdt->name.')'.'</a></li>';
		}
	}else{
		$str='<li style="padding:20px;text-align:center;">未找到会员信息</li>';
	}
	echo $str;
	exit;
}


function getyewuMsgNum(){
	global $db,$request,$adminRole;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$erpMaxId = (int)$request['erpMaxId'];
	if($erpMaxId==0){
		$erpMaxId = $db->get_var("select msgId from demo_task_read where userId=$userId");
	}
	if(empty($erpMaxId))$erpMaxId=0;
	$fenbiao = getFenbiao($comId,20);
	$sql = "select count(*) from demo_task$fenbiao where id>$erpMaxId and comId=$comId";
	if($adminRole<7){
		$sql.=" and find_in_set($userId,userIds)";
	}
	$weidu_num = (int)$db->get_var($sql);
	$max_id = (int)$db->get_var("select id from demo_task$fenbiao order by id desc");
	echo '{"code":1,"new_msg_num":'.$weidu_num.',"max_msg_id":'.$max_id.'}';
	exit;
}
function get_helps(){
	global $request,$crmDb;
	$crmDb = getCrmDb();
	$menuId = (int)$request['id'];
	$keyword = $request['keyword'];
	if(!empty($keyword))$menuId=254;
	$channels = $menuId.getMenustr($menuId);
	$m = $crmDb->get_row("select title,parentId from demo_menu where id=$menuId");
	$menuTitle = $m->title;
	$channels.=','.$m->parentId;
	$sql = "select id,title from demo_list where channelId in($channels)";
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	$count = (int)$crmDb->get_var(str_replace('id,title','count(*)',$sql));
	$sql.=" order by ordering desc,id desc";
	$lists = $crmDb->get_results($sql);	
	$dataJson = array("code"=>1,"menuTitle"=>$menuTitle,"count"=>$count,"data"=>array());
	if(!empty($lists)){
		$dataJson['data'] = $lists;
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getMenustr($id){
	global $crmDb;
	$menus = $crmDb->get_results("select * from demo_menu where parentId=$id");
	$str = '';
	if(!empty($menus)){
		foreach($menus as $menu){
			$str.=','.$menu->id;
			$str.=getMenustr($menu->id);
		}
		return $str;
	}
}
function get_help_info(){
	global $request;
	$crmDb = getCrmDb();
	$id = (int)$request['id'];
	$list = $crmDb->get_row("select id,title,content from demo_list where id=$id");
	$list->content = preg_replace('/((\s)*(\n)+(\s)*)/','',$list->content);
	$list->content = str_replace('src="/','src="https://www.zhishangez.com/',$list->content);
	$list->content = str_replace('<img ','<img onclick="show_helo_img(this);" ',$list->content);
	$dataJson = array("code"=>1,"title"=>$list->title,"content"=>$list->content);
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit; 
}
function add_fankui(){
	global $request;
	$crmDb = getCrmDb();
	$content = $request['content'];
	$userId = $_SESSION[TB_PREFIX.'admin_name'];
	$listId = (int)$request['id'];
	$title = $request['title'];
	if(!empty($title))$content = $title.'。'.$content;
	$check = $crmDb->get_var("select count(*) from demo_list_feedback where listId=$listId and userId='$userId' and dtTime like '".date("Y-m-d")."%'");
	if($check>2){
		echo '{"code":0,"message":"请不要重复留言"}';
		exit;
	}
	if(!empty($listId)){
		$channelId = $crmDb->get_var("select channelId from demo_list where id=$listId");
		$crmDb->query("insert into demo_list_feedback(listId,userId,content,dtTime,status,channelId) value($listId,'$userId','$content','".date("Y-m-d H:i:s")."',0,$channelId)");
	}
	echo '{"code":1,"message":""}';
}
//同步知商账号
function reg_zhishang($userId,$username,$password,$name){
	$db_service = getCrmDb();
	$comId = (int)$_SESSION['demo_comId'];
	$company = $_SESSION['demo_com_title'];
	$ifhas = $db_service->get_var("select id from demo_user where username='$username' limit 1");
	if(empty($ifhas)){
		$user = array();
		$user['nickname'] = '';
		$user['email'] = '';
		$user['username'] = $username;
		$user['pwd'] = $password;
		$user['role'] = 1;
		$user['dtTime'] = date("Y-m-d H:i:s");
		$user['ip'] = '';
		$user['qq'] = '';
		$user['msn'] = '';
		$user['name'] = $name;
		$user['mtel'] = $username;
		$user['phone'] = $username;
		$db_service->insert_update('demo_user',$user,'id');
		$ifhas = $db_service->get_var("select last_insert_id();");
	}
	$if_re = $db_service->get_var("select id from demo_user_relation where userId=$ifhas and comId=$comId limit 1");
	if(empty($if_re)){
		$db_service->query("insert into demo_user_relation(comId,dtTime,userId,company) value($comId,'".date("Y-m-d H:i:s")."',$ifhas,'$company')");
	}
	return $ifhas;
}
function chongzhi_jifen(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$userId = (int)$request['userId'];
		$yzFenbiao = getFenbiao($comId,20);
		$money = $request['money'];
		$beizhu = $request['beizhu'];
		$uname = $_SESSION[TB_PREFIX.'name'];
		$yue = $db->get_var("select jifen from users where id=$userId");
		if($money+$yue<0){
			echo '{"code":0,"message":"会员积分不足，操作失败！"}';
			exit;
		}
		
		$db->query("update users set jifen=jifen+$money where id=$userId");
		
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = $money;
		$jifen_jilu['yue'] = $money+$yue;
		$jifen_jilu['type'] = $money > 0 ? 1 : 2;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '后台操作';
		$jifen_jilu['orderInfo']='操作者：'.$uname.'，备注：'.$beizhu;
		
		$db->insert_update('user_jifen'.$yzFenbiao,$jifen_jilu,'id');
		
		echo '{"code":1,"message":"操作成功"}';
		exit;
	}
}

function salesman(){}

function getsalesmanList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_salesman where 1=1 ";
	
	$keyword = $request['keyword'];
	if(!empty($keyword)){
		$sql.=" and (area like '%$keyword%' or name like '%$keyword%' or phone like '%$keyword%' )";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by ordering desc,id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			
			$j->move = '<span onclick="moveSaleman('.$j->id.',0)" style="cursor:pointer;color:#3084d9;">向下↓</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="moveSaleman('.$j->id.',1)" style="cursor:pointer;color:#3084d9;">向上↑</span>';
		   
			
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function add_salesman()
{
    global $db,$request;
	if($request['tijiao']==1){
	    $salesman = array();
	    
	    $salesman['id'] = (int)$request['id'];
	    $salesman['name'] = $request['name'];
	    $salesman['area'] = $request['area'];
	    $salesman['entitle'] = getFirstCharter($request['area']);
	    $salesman['email'] = $request['email'];
	    $salesman['phone'] = $request['phone'];
	    $salesman['ordering'] = (int)$request['ordering'];
		
		if(empty($salesman['id'])){
			$salesman['dtTime'] = date("Y-m-d H:i:s");
		}
		
		$id = $db->insert_update('demo_salesman', $salesman,'id');
		if(empty($salesman['id'])){
		    $id = $db->get_var("select last_insert_id();");
    		
    		$db->query("update demo_salesman set ordering = $id where id = $id");
		}

		redirect("?s=users&a=salesman");
	}
}

function moveSalesman()
{
    global $db,$request;

	$id = (int)$request['id'];
	$type = (int)$request['state'];//类型：0-向下  1-向上
	
	$currentChannel = $db->get_row("select * from demo_salesman where id = $id");
	if($type){//1-向上
	    $move = $db->get_row("select * from demo_salesman where ordering > $currentChannel->ordering order by ordering asc ");
	}else{
	    $move = $db->get_row("select * from demo_salesman where ordering < $currentChannel->ordering order by ordering desc ");
	}
	if($move){
	    $db->query("update demo_salesman set ordering = $move->ordering where id = $id");
	    $db->query("update demo_salesman set ordering = $currentChannel->ordering where id = $move->id");
	}
	
	echo '{"code":1,"message":"移动成功！"}';
	exit;
}

function delSalesman()
{
    global $db,$request;
	$id = (int)$request['ids'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	
	$db->query("delete from demo_salesman where id=$id ");
	
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}

//社区相关
function view_shenqing(){}
function view_shequ(){}
function getShequList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_shequ where comId=$comId ";
	if(!empty($status)){
		$sql.=" and status=$status";
	}

	$mendianId = $_SESSION['mendianId'];
	if($mendianId > 0){
	    $sql .= " and id = $mendianId ";
	}
	
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 1:
					$statusInfo = '<font color="green">已开通</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>已停用</font>';
				break;
			}
			$j->area_info = getAreaName($j->areaId);
			$j->statusInfo = $statusInfo;
			$j->user_info = '会员ID：'.$j->userId;
			$j->remark = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->remark = str_replace('"','',$j->remark);
			$j->remark = str_replace("'",'',$j->remark);
			$j->remark = '<span onmouseover="tips(this,\''.$j->remark.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->remark),20,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getShengqingList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_shequ_shenqing where comId=$comId ";
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$statusInfo = '<font color="red">待审核</font>';
				break;
				case 1:
					$statusInfo = '<font color="green">已审核</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>未通过</font>';
				break;
			}
			$j->area_info = getAreaName($j->areaId);
			$j->statusInfo = $statusInfo;
			$j->user_info = '会员ID：'.$j->userId;
			$j->remark = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->remark = str_replace('"','',$j->remark);
			$j->remark = str_replace("'",'',$j->remark);
			$j->remark = '<span onmouseover="tips(this,\''.$j->remark.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->remark),20,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function delShenqing(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_shequ_shenqing where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function bohuiShenqing(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_shequ_shenqing set status=-1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function sheheShequ(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("update demo_shequ set status=1 where id=$id");
	$db->query("update users set if_shequ_tuan=1 where id=(select userId from demo_shequ where id=$id)");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function bohuiShequ(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("update demo_shequ set status=-1 where id=$id");
	$db->query("update users set if_shequ_tuan=0 where id=(select userId from demo_shequ where id=$id)");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function add_shequ(){
	global $db,$request;
	if($request['tijiao']==1){
		$shequ = array();
		$shequ['id'] = (int)$request['id'];
		$shequ['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$shequ['title'] = $request['title'];
		$shequ['name'] = $request['name'];
		$shequ['phone'] = $request['phone'];
		$shequ['weixin'] = $request['weixin'];
		$shequ['address'] = $request['address'];
		$shequ['peisong_area'] = $request['peisong_area'];
		$shequ['originalPic'] = $request['originalPic'];
		$shequ['areaId'] = (int)$request['psarea'];
		
		if($request['psarea'] > 0){
		    $peisongArea = '';
		    $area = $db->get_row("select * from demo_area where id = ".$request['psarea']);
		    if($area){
		        $peisongArea = $area->title;
		        if($area->parentId){
		            $shi = $db->get_row("select * from demo_area where id =".$area->parentId);
		            if($shi){
		                $peisongArea = $shi->title."".$peisongArea;
		                $province = $db->get_row("select * from demo_area where id = $shi->parentId");
		                if($province){
		                    $peisongArea = $province->title."".$peisongArea;
		                }
		            }
		        }
		        
		        $shequ['peisong_area'] = $peisongArea;
		    }
		}
		
	    $shequ['longitude'] = $request['hengzuobiao'];
	    if(isset($request['bili'])){
	        $shequ['bili'] = $request['bili'];
	    }
	    $shequ['bili'] = 0.00;
	    
		$shequ['Latitude'] = $request['zongzuobiao'];
		$shequ['shiId'] = $db->get_var("select parentId from demo_area where id=".$shequ['areaId']);
		
		if(empty($shequ['id'])){
			$shequ['dtTime'] = date("Y-m-d H:i:s");
		}
		$shequId = $db->insert_update('demo_shequ',$shequ,'id');

		if(!empty($request['shenqing_id'])){
			$db->query("update demo_shequ_shenqing set status=1 where id=".(int)$request['shenqing_id']);
			redirect("?s=users&a=shenqing");
		}else{
			redirect("?s=users&a=shequ");
		}
	}
}
function get_user_info(){
	global $db,$request;
	$uid = $request['uid'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$shequId = (int)$request['shequId'];
	if(strlen($uid)==11){
		$uid = $db->get_var("select id from users where comId=$comId and username='$uid' limit 1");
	}
	$ifhas = $db->get_var("select id from demo_shequ where comId=$comId and userId=$uid and id<>$shequId");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"该会员已经是其他社区的团长了！请重新填写"}';
		exit;
	}
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$user = $db->get_row("select nickname,username from users where id=$uid and comId=$comId");
	if(empty($user)){
		echo '{"code":0,"message":"会员不存在！"}';
	}else{
		echo '{"code":1,"user_info":"'.$user->nickname.'('.$user->username.')'.'"}';
	}
	exit;
}
function level_shenqing(){}
function getLevelsqs(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$keyword = $request['keyword'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_level_shenqing where comId=$comId ";
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and content like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$statusInfo = '<font color="red">待审核</font>';
				break;
				case 1:
					$statusInfo = '<font color="green">已审核</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>未通过</font>';
				break;
			}
			$j->statusInfo = $statusInfo;
			$content = json_decode($j->content);
			$j->nickname = $content->name;
			$j->username = $content->phone;
			$j->address = $content->address;
			
			$j->shenfenzheng ='<a href="'.$content->img_id.'" target="_blank">查看</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function delLevelsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_level_shenqing where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function bohuiLevelsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_level_shenqing set status=-1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function shenheLevelsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update users set level=88 where id=(select userId from demo_level_shenqing where id=$id)");
	$db->query("update demo_level_shenqing set status=1 where id=$id");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function add_user_oprate($content,$type,$uid=0){
	global $db;
	$user_oprate = array();
	$user_oprate['comId'] = (int)$_SESSION['demo_comId'];
	$user_oprate['userId'] = $uid==0?(int)$_SESSION[TB_PREFIX.'user_ID']:$uid;
	$user_oprate['dtTime'] = date("Y-m-d H:i:s");
	$user_oprate['ip'] = getip();
	$user_oprate['terminal'] = 1;
	$user_oprate['content'] = $content;
	$user_oprate['type'] = $type;
	$fenbiao = getFenbiao($user_oprate['comId'],20);
	$db->insert_update('user_oprate'.$fenbiao,$user_oprate,'id');
}

//分销团长申请
function tuanzhangsq(){}
function delTuanzhangsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_tuanzhang_shenq where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function bohuiTuanzhangsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_tuanzhang_shenq set status=-1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
function shenheTuanzhangsq(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$shenqing = $db->get_row("select * from demo_tuanzhang_shenq where id=$id");
	if(!empty($shenqing) && $shenqing->status==0){
		$db->query("update demo_tuanzhang_shenq set status=1 where id=$id");
		$userId = $shenqing->userId;
		$u = $db->get_row("select user_info,username,tuan_id from users where id=$userId");
		$user_info_str = $u->user_info;
		if(!empty($user_info_str)){
			$user_info = json_decode($user_info_str,true);				
		}
		$user_info['wxh'] = $wxh;
		$user_info['wx_img'] = $wx_img;
		$user = array();
		$user['id'] = $userId;
		$user['if_tuanzhang'] = 1;
		$user['user_info'] = json_encode($user_info,JSON_UNESCAPED_UNICODE);
		$user['nickname'] = $shenqing->name;
		if(strlen($u->username)!=11){
			$ifhas = $db->get_var("select id from users where username='$shenqing->phone' and comId=$comId limit 1");
			if(empty($ifhas)){
				$user['username'] = $shenqing->phone;
			}
		}
		$db->insert_update('users',$user,'id');
		update_user_tuanid($userId,$u->tuan_id,$userId);
	}
	
	echo '{"code":1,"message":"操作成功！"}';
	exit;
}
//修改会员的团id
function update_user_tuanid($uid,$old_tuanid,$new_tuanid){
	$comId = (int)$_SESSION['demo_comId'];
	global $db;
	$db->query("update users set tuan_id=$new_tuanid where id=$uid and tuan_id=$old_tuanid");
	add_user_oprate('所属团队由'.$old_tuanid.'变更为'.$new_tuanid,2,$uid);
	$xiajistr = $db->get_var("select group_concat(id) from users where comId=$comId and shangji=$uid and tuan_id=$old_tuanid");
	if(!empty($xiajistr)){
		$xiajis = explode(',',$xiajistr);
		foreach ($xiajis as $userid) {
			update_user_tuanid($userid,$old_tuanid,$new_tuanid);
		}
	}
}
function getTuanzhangsqList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from demo_tuanzhang_shenq where comId=$comId ";
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$statusInfo = '<font color="red">待审核</font>';
				break;
				case 1:
					$statusInfo = '<font color="green">已审核</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>未通过</font>';
				break;
			}
			$j->wx_img = '<a href="'.$j->wx_img.'" target="_blank">查看</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//修改上级
function edit_shangji(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$shangji = (int)$request['username'];
	$u = $db->get_row("select shangji,tuan_id,nickname,id from users where (id=$shangji or phone = $shangji) and comId=$comId");
	if(empty($u)){
		echo '{"code":0,"message":"会员不存在！"}';
		exit;
	}
    $shangji = $u->id;
	$db->query("update users set shangji=$shangji,shangshangji=$u->shangji,tuan_id=$u->tuan_id where id=$id");

	add_user_oprate('后台绑定上级会员('.$shangji.')！',2,$id);
	//修改下级会员的上上级
	$db->query("update users set shangshangji=$shangji where comId=$comId and shangji=$id");
	//修改下级、下下级会员的团队id
	$db->query("update users set tuan_id=$u->tuan_id where comId=$comId and (shangji=$id or shangshangji=$id) and tuan_id=0");
	echo '{"code":1,"message":"操作成功"}';
}

function getuserbyid(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$u = $db->get_row("select nickname,username from users where (id=$id or phone = $id) and comId=$comId");
	if(empty($u)){
		echo '{"code":0,"message":"会员不存在！"}';
	}else{
		echo '{"code":1,"message":"","nickname":"'.$u->nickname.'","username":"'.$u->nickname.'"}';
	}
	exit;
}

function getAreas(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$ds = $request['departs'];
	$dNames = $request['departNames'];
	if(!empty($ds)){
		$departs = explode(',',$ds);
		$departNames = explode(',',$dNames);
	}
	$str = '<div id="add_container">
			<div id="new_title">
				<div class="new_title_01">选择地区</div>
				<div class="new_title_02" onclick="hide_myModal();"></div>
				<div class="clearBoth"></div>
			</div>
		  <div id="splc_cont">
			<div class="splc_cont_left">
				<div class="splc_cont_left_title">已选择以下地区</div>
				<div class="splc_cont_left_con">
					<ul>';
					if(!empty($departs)){
						$i=0;
						foreach($departs as $depart){
							$str.='<li id="left_depart'.$depart.'">
								<div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_area_depart('.$depart.',\''.$departNames[$i].'\')"><img src="images/close1.png" border="0" /></a></div>
								<div class="clearBoth"></div>
								<div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png" /></div>'.$departNames[$i].'</div>
							</li>';
							$i++;
						}
					}
					$str.='</ul>
				</div>
			</div>
			<div class="splc_cont_right">
				<div class="splc_cont_right_title">所有地区</div>
				<div class="splc_cont_right_search"><input type="text" stlye="border:0px;" onchange="search_areas(this.value);" placeholder="请输入地区名称"></div>
				<div class="splc_cont_right_con">
					<div class="sp_nav1">
						   <ul id="depart_users">
						   	<li class="sp_nav_01">
							<ul>
						   ';
						$departs = $db->get_results("select * from demo_area where parentId=0 order by id asc");
						if(!empty($departs)){
							foreach($departs as $v){
								$str .='<li class="sp_nav_01_zimenu">
										  <img src="images/tree_bg2.jpg" data-id="'.$v->id.'" class="depart_select_img" />
										  <a href="javascript:add_area_depart('.$v->id.',\''.$v->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$v->title.'">'.sys_substr($v->title,10,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$v->id.'" style="display:none;"></ul>
										  <ul>';
								$departs1 = $db->get_results("select * from demo_area where parentId=".$v->id." order by id asc");
								if(!empty($departs1)){
									foreach($departs1 as $list){
										$str .='<li class="sp_nav_01_zimenu1">
										  <img src="images/tree_bg2.jpg" onclick="get_areas('.$list->id.')" data-id="'.$list->id.'" class="depart_select_img" />
										  <a href="javascript:add_area_depart('.$list->id.',\''.$list->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$list->title.'">'.sys_substr($list->title,9,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$list->id.'" style="display:none;"></ul>
										  </li>';
									}
								}
								$str .='</ul></li>';
							}
						}  
				$str .='</ul></li></ul>
					<ul id="search_users"></ul>
					 </div>
				</div>
				
			</div>
			<div class="clearBoth"></div>
			<div class="splc_cont_bottom">
			<input type="button" onclick="area_baocun();" value="保存" />
			<input type="button" onclick="hide_myModal();" value="取消" />
			</div>
		  </div>
		</div>';
	echo $str;
	exit;	
}

//获取子区域列表
function getAreasByPid(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['id'];
	$keyword = $request['keyword'];
	if(!empty($channelId)){
		$sql="SELECT id,title FROM demo_area WHERE parentId=$channelId";
	}else{
		$sql="SELECT id,title FROM demo_area WHERE title like '%$keyword%' limit 20";
	}
	$users=$db->get_results($sql);
	$str = "";
	if(!empty($users)){
		foreach($users as $user){
			$str.='<li class="sp_nav_02" onclick="add_area_depart('.$user->id.',\''.$user->title.'\')" title="'.$user->title.'"><div class="gg_people_show_3_1" style="float:left; margin-right:5px;">分类</div>'.sys_substr($user->title,7,true).'</li>';
		}
	}else{
		if(!empty($department)){
			$str.='<li class="sp_nav_02">该分类下没有地区</li>';
		}else{
			$str.='<li class="sp_nav_02">没有搜索到相关地区</li>';
		}
	}
	echo $str;
	exit;
}

