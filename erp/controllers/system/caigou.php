<?php
function index(){}
function detail(){}
function ruku(){}
function daochu(){}
function daochuRuku(){}
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
		$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
		$supplierId = (int)$request['supplierId'];
		if($supplierId==0){
			$supplierName = $request['supplierName'];
			if(!empty($request['ifAddSupp'])){
				$supplier = array();
				$supplier['comId'] = $comId;
				$supplier['title'] = $supplierName;
				insert_update('demo_supplier',$supplier,'id');
				$supplierId = $db->get_var("select last_insert_id();");
			}
		}else{
			$supplierName = $db->get_var("select title from demo_supplier where id=$supplierId");
		}
		$ifjiaji = empty($request['ifjiaji'])?0:1;
		$price_other = $request['price_other'];
		$price_wuliu = $request['price_wuliu'];
		$price = $request['price'];
		$price_payed = $request['price_payed'];
		$price_weikuan = $price-$price_payed;
		$price_type = $request['price_type'];
		$caigouyuan = $request['caigouyuan'];
		$dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
		$type = 4;
		$orderInt = $db->get_var("select orderInt from demo_caigou where comId=$comId order by id desc limit 1");
		$orderInt = $orderInt+1;
		$orderId = $request['orderId'];
		$status = 1;
		$shenheUser = 0;
		$shenheName = '';
		if($kucun_set->caigou_shenpi==1){
			$shenheUser = getShenpUser($comId,$type,$storeId);
			$status = 0;
			if($shenheUser==0){
				$status = 1;
			}else{
				$crmdb = getCrmDb();
				$shenheName = $crmdb->get_var("select name from demo_user where id=$shenheUser");
			}
		}
		$type_info = $request['type_info'];
		$jingbanren = $request['jingbanren'];
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		$beizhu = $request['beizhu'];
		if(!empty($request['inventoryNum'])){
			$caigou = array();
			$caigou['comId'] = $comId;
			$caigou['storeId'] = $storeId;
			$caigou['storeName'] = $storeName;
			$caigou['supplierId'] = $supplierId;
			$caigou['supplierName'] = $supplierName;
			$caigou['ifjiaji'] = $ifjiaji;
			$caigou['price_other'] = $price_other;
			$caigou['price_wuliu'] = $price_wuliu;
			$caigou['price'] = $price;
			$caigou['price_payed'] = $price_payed;
			$caigou['price_weikuan'] = $price_weikuan;
			$caigou['price_type'] = $price_type;
			$caigou['caigouyuan'] = $caigouyuan;
			$caigou['orderId'] = $orderId;
			$caigou['orderInt'] = $orderInt;
			$caigou['dtTime'] = $dtTime;
			$caigou['status'] = $status;
			$caigou['userId'] = $userId;
			$caigou['username'] = $username;
			$caigou['shenheUser'] = $shenheUser;
			$caigou['shenheName'] = $shenheName;
			$caigou['beizhu'] = $beizhu;
			insert_update('demo_caigou',$caigou,'id');
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_caigou_detail$fenbiao(comId,supplierId,jiluId,inventoryId,productId,pdtInfo,num,status,price,unit_price,units,dtTime) values";
			$rukuSql1 = '';
			foreach ($request['inventoryNum'] as $key => $num){
				$inventoryId = $request['inventoryId'][$key];
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $request['inventorySn'][$key];
				$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
				$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
				$productId = $request['inventoryPdtId'][$key];
				$units = $request['inventoryUnits'][$key];
				$unit_price = $request['inventoryPrice'][$key];
				$price = $request['inventoryHeji'][$key];
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$rukuSql1.=",($comId,$supplierId,$jiluId,$inventoryId,$productId,'$pdtInfo','$num','$status','$price',$unit_price,'$units','$dtTime')";
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			if($status==0){
				$db->query("insert into demo_task$fenbiao(comId,type,infoId,title,userIds,content,dtTime) value($comId,$type,$jiluId,'新的采购单需要您审批','$shenheUser','有新的采购单需要您审批，请及时处理','".date("Y-m-d H:i:s")."')");
				//推送消息
				send_message($shenheUser,2,'有新的采购单需要您审批','有新的采购单需要您审批，请及时处理');
			}
			redirect("?m=system&s=caigou");
		}
	}
}
function getJilus(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('caigouPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,supplierId,supplierName,ifjiaji,price,username,caigouyuan,dtTime,status,shenheName,shenheTime,rukuStatus from demo_caigou where comId=$comId";
	if(!empty($status)){
		if($status>10){
			$rukuStatus = $status-10;
			$sql.=" and status=1 and rukuStatus=$rukuStatus";
		}else if($status==2){
			$sql.=" and status=0";
		}else{
			$sql.=" and status=$status";
		}
	}
	if(!empty($keyword)){
		$sql.=" and (supplierName like '%$keyword%' or orderId like '%$keyword%' or username like '%$keyword%' or caigouyuan like '%$keyword%')";
	}
	$count = $db->get_var(str_replace('id,orderId,supplierId,supplierName,ifjiaji,price,username,caigouyuan,dtTime,status,shenheName,shenheTime,rukuStatus','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$ifjiaji = '';
			if($j->ifjiaji==0){
				$j->ifjiaji = '否';
			}else{
				$j->ifjiaji = '<span style="color:red">是</span>';
			}
			$status = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已驳回</span>';
				break;
				case 0:
					$j->layclass = '';
					$status = '<span>待审核</span>';
				break;
				case 1:
					$j->layclass = '';
					$status = '<span style="color:green">已审核</span>';
				break;
			}
			if($j->status==1){
				switch ($j->rukuStatus){
					case 0:
						$j->rukuStatus = '<span style="color:red">待入库</span>';
					break;
					case 1:
						$j->rukuStatus = '<span style="color:red">部分入库</span>';
					break;
					case 2:
						$j->rukuStatus = '<span style="color:green">已入库</span>';
					break;
				}
			}else{
				$j->rukuStatus = $status;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			if($j->supplierId==0){
				$j->supplierName .='<img src="images/temp.png">';
			}
			$j->orderId = '<span onclick="view_jilu(\'caigou\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getpdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$supplierId = (int)$request['supplierId'];
	$channelId = (int)$request['channelId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$hasIds = $request['hasIds'];
	if(empty($hasIds))$hasIds='0';

	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql="select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId and id not in($hasIds)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	//是否是促销调用
	$cuxiao = (int)$request['cuxiao'];
	/*if($cuxiao==1){
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
		if(!empty($startTime)&&!empty($endTime)){
			$ids = $db->get_var("select group_concat(pdtIds) from cuxiao_pdt where comId=$comId and status=1 and ((startTime<'$startTime' and endTime>'$startTime') or (startTime<'$endTime' and endTime>'$endTime') or (startTime>'$startTime' and endTime<'$endTime'))");
		}
		if(empty($ids))$ids='0';
		$sql.=" and id not in($ids)";
	}*/
	if(!empty($supplierId)){
		$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
		if(empty($pdts))$pdts='0';
		$sql.=" and id in($pdts)";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			if(!empty($supplierId)){
				$price = $db->get_var("select price from demo_supplier_gonghuo where supplierId=$supplierId and inventoryId=".$pdt->id.' limit 1');
			}
			if(empty($price))$price = 0;
			$price = getXiaoshu($price,$product_set->price_num);
			$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","shuliang":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" id=\"shuliang_'.$pdt->id.'\">","price":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" value=\"'.$price.'\" id=\"price_'.$pdt->id.'\">","units":"'.$unitstr.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select shenheUser,status,userId from demo_caigou where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=0){
		echo '{"code":0,"message":"该任务已经处理过了！"}';
		exit;
	}
	if($jilu->shenheUser!=$userId){
		echo '{"code":0,"message":"您没有权限处理该审批！"}';
		exit;
	}
	$db->query("update demo_caigou set status=$status,shenheTime='".date("Y-m-d H:i:s")."',shenheCont='$cont' where id=$jiluId");
	$db->query("update demo_caigou_detail$fenbiao set status=$status where id=$jiluId");
	if($status==1){
		send_message($jilu->userId,2,'您的采购单已审核通过，请尽快执行入库操作','您的采购单已审核通过，请尽快执行入库操作');
	}else{
		send_message($jilu->userId,2,'您的采购单被驳回，请及时查看','您的采购单被驳回，请及时查看');
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function addRuku(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$lock = (int)$request['lock'];
	$id = $request['id'];
	$caigou = $db->get_row("select * from demo_caigou where id=$id and comId=$comId");
	$caigouLock = $caigou->locked;
	if($lock!=$caigouLock){
		echo '{"code":0,"message":"该采购目前目前处于锁定状态，请刷新页面后重新操作"}';
		exit;
	}
	if(empty($request['rukuNum'])){
		echo '{"code":0,"message":"没有要入库的商品！"}';
		exit;
	}
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$caigouNum = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where jiluId=$id");
	$storeId = (int)$request['storeId'];
	$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
	$dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
	$type = 1;
	$orderInt = getOrderId($comId,$type);
	$orderId = $request['orderId'];
	$status = 1;
	$shenheUser = 0;
	$shenheName = '';
	/*if($kucun_set->ruku_shenpi==1){
		$shenheUser = getShenpUser($comId,$type,$storeId);
		$status = 0;
		if($shenheUser==0){
			$status = 1;
		}else{
			$crmdb = getCrmDb();
			$shenheName = $crmdb->get_var("select name from demo_user where id=$shenheUser");
		}
	}*/
	$type_info = '采购入库';
	$jingbanren = $request['jingbanren'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$beizhu = $request['beizhu'];
	$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName,caigouId) value($comId,$type,$storeId,0,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','$jingbanren',$shenheUser,'$shenheName','$beizhu','$storeName',$id)");
	$jiluId = $db->get_var("select last_insert_id();");
	$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,caigouId,chengben,zongchengben) values";
	$rukuSql1 = '';
	foreach ($request['rukuNum'] as $key => $num){
		$detail = $db->get_row("select * from demo_caigou_detail$fenbiao where id=$key and jiluId=$id");
		if($num<=0||$num>($detail->num-$detail->hasNum)){
			continue;
		}
		$inventoryId = $detail->inventoryId;
		$productId = $detail->productId;
		$units = $detail->units;
		$pdtInfo = $detail->pdtInfo;
		$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
		$kucun+=$num;
		$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 order by id desc limit 1");
		if(empty($lastJilu)){
			$lastJilu->zongchengben = 0;
			$lastJilu->kucun = 0;
		}
		$rukuChengben = $detail->unit_price*$num;
		if($caigou->price_other>0||$caigou->price_wuliu>0){
			$rukuChengben=$rukuChengben+(($caigou->price_other+$caigou->price_wuliu)/$caigouNum*$num);
		}
		$zongchengben = $rukuChengben+$lastJilu->zongchengben;
		
		$zongNum = $lastJilu->kucun+$num;
		$chengben = getXiaoshu($zongchengben/$zongNum,4);
		if($chengben<0)$chengben=0;
		if($status==1){
			$db->query("update demo_kucun set kucun=kucun+$num,chengben='$chengben' where inventoryId=$inventoryId and storeId=$storeId limit 1");
			$db->query("update demo_product_inventory set kucun=kucun+$num where id=$inventoryId");
		}else{
			//$db->query("update demo_kucun set zaituNum=zaituNum+$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
		}
		$db->query("update demo_caigou_detail$fenbiao set hasNum=hasNum+$num where id=$key");
		$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo',$storeId,'$storeName','$num',$status,'$kucun','',$type,'$type_info','$dtTime','$units',".$detail->id.",'$rukuChengben','$zongchengben')";
	}
	$rukuSql1 = substr($rukuSql1,1);
	$db->query($rukuSql.$rukuSql1);
	$ifhas = $db->get_var("select id from demo_caigou_detail$fenbiao where jiluId=$id and num>hasNum limit 1");
	if(empty($ifhas)){
		$rukuStatus = 2;
	}else{
		$rukuStatus = 1;
	}
	$db->query("update demo_caigou set rukuStatus=$rukuStatus,locked=locked+1 where id=$id");
	if($status==0){
		$db->query("insert into demo_task$fenbiao(comId,type,infoId,title,userIds,content,dtTime) value($comId,$type,$jiluId,'新的入库需要您审批','$shenheUser','有新的入库需要您审批，请及时处理','".date("Y-m-d H:i:s")."')");
				//推送消息
		send_message($shenheUser,2,'有新的入库单需要您审批','有新的入库单需要您审批，请及时处理');
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$caigouId = (int)$request['caigouId'];
	$jiluId = (int)$request['id'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$shenheName = $_SESSION[TB_PREFIX.'name'];
	$cont = $request['beizhu'];
	$jilu = $db->get_row("select shenheUser,userId,storeId,status,caigouId from demo_kucun_jilu$fenbiao where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"记录不存在"}';
		exit;
	}
	if($jilu->status<0){
		echo '{"code":0,"message":"该记录不能进行作废操作！"}';
		exit;
	}
	if($jilu->shenheUser!=$userId&&$jilu->userId!=$userId){
		echo '{"code":0,"message":"您没有权限作废该记录！"}';
		exit;
	}
	$iftuihuo = $db->get_var("select id from demo_caigou_tuikuan where comId=$comId and caigouId=$jilu->caigouId and status>-1 limit 1");
	if(!empty($iftuihuo)){
		echo '{"code":0,"message":"已发生退货行为，不允许作废！"}';
		exit;
	}
	$jiluDetails = $db->get_results("select id,inventoryId,storeId,num,type,caigouId,chengben from demo_kucun_jiludetail$fenbiao where jiluId=$jiluId");
	$dtTime = date("Y-m-d H:i:s");
	if(!empty($jiluDetails)){
		if($jilu->status==1){
			$kucunArry = array();
			if($kucun_set->chuku_limit==1){
				foreach ($jiluDetails as $j){
					$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1");
					if($kucun<$l->num){
						echo '{"code":0,"message":"库存不足！无法作废"}';
						exit;
					}
					$kucunArry[$j->id] = $kucun;
				}
			}
			foreach ($jiluDetails as $j) {
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and status=1 and id!=$j->id order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				$zongchengben = $lastJilu->zongchengben-$j->chengben;
				$zongNum = $lastJilu->kucun-$j->num;
				if($zongNum<=0){
					$chengben = 0;
				}else{
					$chengben = getXiaoshu($zongchengben/$zongNum,4);
				}
				if($chengben<0)$chengben=0;
				$sql = "update demo_kucun set kucun=kucun-".$j->num.",chengben='".$chengben."'";
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);
				$db->query("update demo_product_inventory set kucun=kucun-$j->num where id=$j->inventoryId");
				$db->query("update demo_kucun_jiludetail$fenbiao set status=-2,kucun='".($kucunArry[$j->id]-$j->num)."',shenheTime='$dtTime' where id=".$j->id);
				$db->query("update demo_caigou_detail$fenbiao set hasNum=hasNum-".$j->num." where id=".$j->caigouId);
			}
		}else if($jilu->status==0){
			foreach ($jiluDetails as $j){
				/*$sql = "update demo_kucun set zaituNum=zaituNum-".abs($j->num);
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);*/
				$db->query("update demo_kucun_jiludetail$fenbiao set status=-2,shenheTime='$dtTime' where id=".$j->id);
				$db->query("update demo_caigou_detail$fenbiao set hasNum=hasNum-".$j->num." where id=".$j->caigouId);
			}
		}
		$db->query("update demo_kucun_jilu$fenbiao set status=-2,shenheTime='$dtTime',shenheCont='$cont',shenheUser=$userId,shenheName='$shenheName' where id=$jiluId");
		if($jilu->caigouId>0){
			$ifhas = $db->get_var("select id from demo_caigou_detail$fenbiao where jiluId=$jilu->caigouId and hasNum>0 limit 1");
			if($ifhas>0){
				$rukuStatus = 1;
			}else{
				$rukuStatus = 0;
			}
			$db->query("update demo_caigou set rukuStatus=$rukuStatus where id=".$jilu->caigouId);
		}
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}