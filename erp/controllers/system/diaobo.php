<?php
function index(){}
function add(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		if(is_file("../cache/kucun_set_$comId.php")){
			$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
		}else{
			$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		}
		$fenbiao = getFenbiao($comId,20);
		$storeId = (int)$request['storeId'];
		$store1Id = (int)$request['store1Id'];
		$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
		$store1Name = $db->get_var("select title from demo_kucun_store where id=$store1Id and comId=$comId");
		$dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
		$type = 3;
		$orderInt = getOrderId($comId,$type);
		$orderId = $request['orderId'];
		$status = 1;
		$shenheUser = 0;
		$shenheName = '';
		if($kucun_set->diaobo_shenpi==1){
			$shenheUser = getShenpUser($comId,$type,$storeId);
			$status = 0;
			if($shenheUser==0){
				$status = 1;
			}else{
				$crmdb = getCrmDb();
				$shenheName = $crmdb->get_var("select name from demo_user where id=$shenheUser");
			}
		}
		$type_info = '商品调拨';
		$jingbanren = $request['jingbanren'];
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		$beizhu = $request['beizhu'];
		if(!empty($request['inventoryNum'])){
			$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName,store1Name) value($comId,$type,$storeId,$store1Id,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','$jingbanren',$shenheUser,'$shenheName','$beizhu','$storeName','$store1Name')");
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values";
			$rukuSql1 = '';
			foreach ($request['inventoryNum'] as $key => $num){
				$inventoryId = $request['inventoryId'][$key];
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $request['inventorySn'][$key];
				$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
				$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
				$productId = $request['inventoryPdtId'][$key];
				$units = $request['inventoryUnits'][$key];
				$beizhu = $request['inventoryBeizhu'][$key];
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$k = $db->get_row("select kucun,chengben from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
				$kucun = $k->kucun;
				$kucun-=$num;
				$rukuChengben = $k->chengben*$num;
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				$zongchengben = $lastJilu->zongchengben-$rukuChengben;
				if($status==1){
					$db->query("update demo_kucun set kucun=kucun-$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
					$db->query("update demo_product_inventory set kucun=kucun-$num where id=$inventoryId");
					$db->query("update demo_kucun set zaituNum=zaituNum+$num where inventoryId=$inventoryId and storeId=$store1Id limit 1");
				}
				$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo',$storeId,'$storeName','-$num',$status,'$kucun','$beizhu',2,'调拨出库','$dtTime','$units','$rukuChengben','$zongchengben')";
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			if($status==0){
				$db->query("insert into demo_task$fenbiao(comId,type,infoId,title,userIds,content,dtTime) value($comId,$type,$jiluId,'新的调拨单需要您审批','$shenheUser','有新的调拨单需要您审批，请及时处理','".date("Y-m-d H:i:s")."')");
				//推送消息
				send_message($shenheUser,2,'有新的调拨单需要您审批','有新的调拨单需要您审批，请及时处理');
			}
			redirect("?m=system&s=diaobo");
		}
	}
}
function getJilus(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenBiao($comId,20);
	$storeIds = (int)$request['storeIds'];
	$status = (int)$request['status'];
	$type = $request['type'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('diaoboPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,storeId,store1Id,storeName,store1Name,type_info,username,status,shenheTime from demo_kucun_jilu$fenbiao where comId=$comId and type=3 ";
	if(!empty($storeIds)){
		$sql.=" and storeId=$storeIds";
	}
	if(!empty($status)){
		if($status==3)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($type)){
		$sql.=" and type_info='$type'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,orderId,dtTime,storeId,store1Id,storeName,store1Name,type_info,username,status,shenheTime','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$status = '';
			$j->layclass = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$status = '待审核';
				break;
				case 1:
					$status = '<span style="color:red">在途</span>';
				break;
				case 2:
					$status = '<span style="color:green">已调拨</span>';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			$j->storeName = $db->get_var("select title from demo_kucun_store where id=$j->storeId");
			$j->store1Name = $db->get_var("select title from demo_kucun_store where id=$j->store1Id");
			$j->orderId = '<span onclick="view_jilu(\'diaobo\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}