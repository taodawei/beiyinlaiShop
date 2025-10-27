<?php
function index(){}
function jilu_detail(){}
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
		$caigouId = (int)$request['caigouId'];
		$storeId = (int)$request['storeId'];
		if(empty($caigouId)||empty($storeId)){
			die('error');
		}		
		$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
		$supplier = $db->get_row("select supplierId,supplierName from demo_caigou where id=$caigouId");
		$dtTime = date("Y-m-d H:i:s");
		$status = 1;
		$shenheUser = 0;
		$shenheName = '';
		if($kucun_set->caigou_tuihuo_shenpi==1){
			$shenheUser = getShenpUser($comId,$type,$storeId);
			$status = 0;
			if($shenheUser==0){
				$status = 1;
			}else{
				$crmdb = getCrmDb();
				$shenheName = $crmdb->get_var("select name from demo_user where id=$shenheUser");
			}
		}
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		$jingbanren = $request['jingbanren'];
		if(!empty($request['inventoryNum'])){
			$type = 2;
			$orderInt = getOrderId($comId,$type);
			$orderId1 = $kucun_set->chuku_pre.'_'.date("Ymd").'_'.$orderInt;
			$type_info = '采购退货';
			$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,$type,$storeId,0,'$orderId1',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','$jingbanren',$shenheUser,'$shenheName','','$storeName')");
			$jiluId = $db->get_var("select last_insert_id();");
			$type = 5;
			$orderInt = getOrderId($comId,$type);
			$orderId = $request['orderId'];
			$ifxieshang = empty($request['ifxieshang'])?0:1;
			$money = $ifxieshang==0?$request['price']:$request['xieshangMoney'];
			$hejiPrice = $request['hejiPrice'];
			$beizhu = $request['beizhu'];
			$caigou_tuihuo = array();
			$caigou_tuihuo['comId']=$comId;
			$caigou_tuihuo['caigouId']=$caigouId;
			$caigou_tuihuo['jiluId']=$jiluId;
			$caigou_tuihuo['supplierId']=$supplier->supplierId;
			$caigou_tuihuo['supplierName']=$supplier->supplierName;
			$caigou_tuihuo['storeId']=$storeId;
			$caigou_tuihuo['storeName']=$storeName;
			$caigou_tuihuo['orderId']=$orderId;
			$caigou_tuihuo['orderInt']=$orderInt;
			$caigou_tuihuo['dtTime']=$dtTime;
			$caigou_tuihuo['status']=$status;
			$caigou_tuihuo['userId']=$userId;
			$caigou_tuihuo['username']=$username;
			$caigou_tuihuo['jingbanren']=$jingbanren;
			$caigou_tuihuo['shenheUser']=$shenheUser;
			$caigou_tuihuo['shenheName']=$shenheName;
			$caigou_tuihuo['ifxieshang']=$ifxieshang;
			$caigou_tuihuo['money']=$money;
			$caigou_tuihuo['hejiPrice']=$hejiPrice;
			$caigou_tuihuo['beizhu']=$beizhu;
			insert_update('demo_caigou_tuikuan',$caigou_tuihuo,'id');
			$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,price,caigouId,chengben,zongchengben) values";
			$rukuSql1 = '';
			foreach ($request['inventoryNum'] as $key => $num){
				if($num>0){
					$inventoryId = $request['inventoryId'][$key];
					$pdtInfoArry = array();
					$pdtInfoArry['sn'] = $request['inventorySn'][$key];
					$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
					$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
					$price = $request['inventoryPrice'][$key];
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
					//$zongNum = $kucun;
					//$chengben = getXiaoshu($zongchengben/$zongNum,4);
					if($chengben<0)$chengben=0;
					if($status==1){
						$db->query("update demo_kucun set kucun=kucun-$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
						$db->query("update demo_product_inventory set kucun=kucun-$num where id=$inventoryId");
					}else{
						//$db->query("update demo_kucun set yugouNum=yugouNum+$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
					}
					$db->query("update demo_caigou_detail$fenbiao set tuihuoNum=tuihuoNum+$num where jiluId=$caigouId and inventoryId=$inventoryId limit 1");
					$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo',$storeId,'$storeName','-$num',$status,'$kucun','$beizhu',2,'$type_info','$dtTime','$units','$price',-1,'$rukuChengben','$zongchengben')";
				}
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			if($status==0){
				$db->query("insert into demo_task$fenbiao(comId,type,infoId,title,userIds,content,dtTime) value($comId,$type,$jiluId,'新的采购退货单需要您审批','$shenheUser','有新的采购退货单需要您审批，请及时处理','".date("Y-m-d H:i:s")."')");
				//推送消息
				send_message($shenheUser,2,'有新的采购退货单需要您审批','有新的采购退货单需要您审批，请及时处理');
			}
			redirect("?m=system&s=caigou_tuihuo");
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
	setcookie('caigoutPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,supplierName,storeId,storeName,money,username,status,shenheTime,dtTime from demo_caigou_tuikuan where comId=$comId ";
	if(!empty($storeIds)){
		$sql.=" and storeId=$storeIds";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,orderId,supplierName,storeId,storeName,money,username,status,shenheTime,dtTime','count(*)',$sql));
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
					$status = '<span style="color:green">已审核</span>';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			$j->storeName = $db->get_var("select title from demo_kucun_store where id=$j->storeId");
			$j->orderId = '<span onclick="view_jilu(\'caigou_tuihuo\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
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
	$jilu = $db->get_row("select shenheUser,status from demo_caigou_tuikuan where id=$jiluId and comId=$comId");
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
	$jiluDetails = $db->get_results("select id,inventoryId,storeId,num,type,caigouId,chengben from demo_kucun_jiludetail$fenbiao where jiluId=$jiluId and status=0 and caigouId=-1");
	$dtTime = date("Y-m-d H:i:s");
	if(!empty($jiluDetails)){
		if($status==1){
			foreach ($jiluDetails as $j){
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and status=1 order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				$zongchengben = $lastJilu->zongchengben-$j->chengben;
				$zongNum = $lastJilu->kucun+$j->num;
				$chengben = getXiaoshu($zongchengben/$zongNum,4);
				if($chengben<0)$chengben=0;
				$sql = "update demo_kucun set kucun=kucun+".$j->num.",chengben='".$chengben."'";
				//$sql.=",yugouNum=yugouNum-".abs($j->num);
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);
				$db->query("update demo_product_inventory set kucun=kucun+$j->num where id=$j->inventoryId");
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1");
				$db->query("update demo_kucun_jiludetail$fenbiao set status=1,kucun='$kucun',shenheTime='$dtTime',zongchengben='$zongchengben' where id=".$j->id);
			}
		}else{
			foreach ($jiluDetails as $j){
				/*$sql = "update demo_kucun set ";
				$sql.="yugouNum=yugouNum-".abs($j->num);
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);*/
				$db->query("update demo_caigou_detail$fenbiao set tuihuoNum=tuihuoNum-".abs($j->num)." where jiluId=$jiluId and inventoryId=".$j->inventoryId." limit 1");
			}
			$db->query("update demo_kucun_jiludetail$fenbiao set status=$status,shenheTime='$dtTime' where jiluId=$jiluId and status=0 and caigouId=-1");
		}
		$db->query("update demo_caigou_tuikuan set status=$status,shenheTime='$dtTime',shenheCont='$cont' where id=$jiluId");
		$db->query("update demo_kucun_jilu$fenbiao set status=$status,shenheTime='$dtTime',shenheCont='$cont' where id=$jiluId");
	}
	echo '{"code":1,"message":"操作成功"}';
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
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$caigouId = (int)$request['caigouId'];
	$storeId = (int)$request['storeId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$hasIds = $request['hasIds'];
	if(empty($hasIds))$hasIds='0';
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql = "select * from demo_caigou_detail$fenbiao where jiluId=$caigouId and hasNum>0 and inventoryId not in($hasIds)";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$kucun = 99999999;
			if($kucun_set->chuku_limit==1){
				$kucun = $db->get_var("select kucun-yugouNum from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
				if(empty($kucun))continue;
			}
			if($kucun>$pdt->hasNum)$kucun = $pdt->hasNum;
			$kucun = $kucun-$pdt->tuihuoNum;
			if($kucun<0)$kucun=0;
			$pdtInfo = json_decode($pdt->pdtInfo);
			$price = getXiaoshu($pdt->unit_price,$product_set->price_num);
			$pdtstr.=',{"id":'.$pdt->inventoryId.',"productId":'.$pdt->productId.',"sn":"'.$pdtInfo->sn.'","title":"'.$pdtInfo->title.'","key_vals":"'.$pdtInfo->key_vals.'","units":"'.$pdt->units.'","nums":"'.$pdt->num.'","kucun":"'.$kucun.'","shuliang":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" max=\"'.$kucun.'\" value=\"'.$kucun.'\" onmouseenter=\"tips(this,\'最多可退'.$kucun.'\',1)\" onmouseout=\"hideTips();\" readonly=\"true\" id=\"shuliang_'.$pdt->inventoryId.'\">","price":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" value=\"'.$price.'\" id=\"price_'.$pdt->inventoryId.'\">"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}