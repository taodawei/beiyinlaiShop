<?php
function index(){}
function daoru(){}
function daochuPandian(){}
function pandian1(){}
function pandian2(){}
function daochuExcel(){
	global $db,$request;
	require_once ABSPATH.'inc/excel.php';
	$pandianJsonData = stripcslashes($request['pandianJsonData']);
	$jilus = json_decode($pandianJsonData,true);
	$indexKey = array('商品编码','商品名称','商品规格','成本调整');
	exportExcel($jilus,'成本调整失败记录',$indexKey);
	exit;
}
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
		$dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
		$type = 6;
		$orderInt = getOrderId($comId,$type);
		$orderId = $request['orderId'];
		$status = 1;
		$shenheUser = 0;
		$shenheName = '';
		$type_info = '成本调整';
		$jingbanren = $request['jingbanren'];
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		$beizhu = $request['beizhu'];
		if(!empty($request['inventoryChengben'])){
			$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,6,$storeId,0,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','$jingbanren',$shenheUser,'$shenheName','$beizhu','$storeName')");
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values";
			$rukuSql1 = '';
			foreach ($request['inventoryChengben'] as $key => $rukuChengben){
				$inventoryId = $request['inventoryId'][$key];
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $request['inventorySn'][$key];
				$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
				$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
				$productId = $request['inventoryPdtId'][$key];
				$units = $request['inventoryUnits'][$key];
				$beizhu = $request['inventoryBeizhu'][$key];
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				$zongchengben = $rukuChengben+$lastJilu->zongchengben;
				$zongNum = $kucun;
				$chengben = getXiaoshu($zongchengben/$zongNum,4);
				if($chengben<0)$chengben=0;
				$db->query("update demo_kucun set chengben='$chengben' where inventoryId=$inventoryId and storeId=$storeId limit 1");
				$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo',$storeId,'$storeName','0',$status,'$kucun','$beizhu',$type,'$type_info','$dtTime','$units','$rukuChengben','$zongchengben')";
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			redirect("?m=system&s=chengben");
		}
	}
}
function getJilus(){
	global $db,$request,$adminRole,$qx_arry;
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
	setcookie('chengbenPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,orderId,dtTime,storeId,storeName,store1Name,type_info,username,status,shenheTime from demo_kucun_jilu$fenbiao where comId=$comId and type=6 ";
	if($adminRole<7&&!strstr($qx_arry['kucun']['storeIds'],'all')){
		$sql.=" and (storeId in(".$qx_arry['kucun']['storeIds'].") or store1Id in(".$qx_arry['kucun']['storeIds']."))";
	}
	if(!empty($storeIds)){
		$sql.=" and (storeId=$storeIds or store1Id=$storeIds)";
	}
	if(!empty($status)){
		if($status==2)$status=0;
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
	$count = $db->get_var(str_replace('id,orderId,dtTime,storeId,storeName,store1Name,type_info,username,status,shenheTime','count(*)',$sql));
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
				case -2:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已作废</span>';
				break;
			}
			$j->status = $status;
			$j->storeName = $db->get_var("select title from demo_kucun_store where id=$j->storeId");
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shenheTime = empty($j->shenheTime)?'':date("Y-m-d H:i",strtotime($j->shenheTime));
			if($storeIds==0||$storeIds==$j->storeId){
				$j->orderId = '<span onclick="view_jilu(\'chengben\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
				$dataJson['data'][] = $j;
			}
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}