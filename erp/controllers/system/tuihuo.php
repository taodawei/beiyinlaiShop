<?php
function index(){}
function jilu_detail(){}
function daochu(){}
function add(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$fenbiao = getFenbiao($comId,20);
		$kehuId = (int)$request['kehuId'];
		$k = $db->get_row("select title,departId,storeId from demo_kehu where id=$kehuId and comId=$comId");
		$kehuName = $k->title;
		$departId = $k->departId;
		$orderId = 'DH-R_'.date("Ymd").'_'.rand(100000,999999);
		$iftejia = empty($request['ifxieshang'])?0:1;
		$price = empty($request['xieshangMoney'])?$request['price']:$request['xieshangMoney'];
		$price_weikuan = $price;
		$beizhu = '';
		if(!empty($request['beizhu'])){
			$results = array();
			$fankui = array();
			$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['beizhu']);
			$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
			$fankui['time'] = date('Y-m-d H:i:s');
			$fankui['company'] = $_SESSION[TB_PREFIX.'com_title'];
			$results[]=$fankui;
			$beizhu = json_encode($results,JSON_UNESCAPED_UNICODE);
		}
		$dtTime = date("Y-m-d H:i:s");
		$shoukuanInfo = $request['shouhuoInfo'];
		$fujianInfo = $request['fujianInfo'];
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		if(!empty($request['inventoryNum'])){
			$dinghuo = array();
			$dinghuo['comId'] = $comId;
			$dinghuo['kehuId'] = $kehuId;
			$dinghuo['kehuName'] = $kehuName;
			$dinghuo['orderId'] = $orderId;
			$dinghuo['money'] = $price;
			$dinghuo['money_weikuan'] = $price_weikuan;
			$dinghuo['dtTime'] = $dtTime;
			$dinghuo['shoukuanInfo'] =$shoukuanInfo;
			$dinghuo['fujianInfo'] = $fujianInfo;
			$dinghuo['userId'] = $userId;
			$dinghuo['username'] = $username;
			$dinghuo['beizhu'] = $beizhu;
			$dinghuo['orderType'] = 2;
			insert_update('demo_tuihuo',$dinghuo,'id');
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_tuihuo_detail(comId,jiluId,inventoryId,productId,pdtInfo,num,units,unit_price,price,beizhu) values";
			$rukuSql1 = '';
			foreach ($request['inventoryNum'] as $key => $num){
				$inventoryId = (int)$request['inventoryId'][$key];
				$productId = (int)$request['inventoryPdtId'][$key];
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $request['inventorySn'][$key];
				$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
				$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
				$units = $request['inventoryUnits'][$key];
				$unit_price = str_replace(',','',$request['inventoryPrice'][$key]);
				$price = $unit_price*$num;
				$beizhu = $request['inventoryBeizhu'][$key];
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo','$num','$units','$unit_price','$price','$beizhu')";
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			$content = '已为您代下退货单，单号：'.$orderId;
			add_dinghuo_msg($kehuId,$content,2,$jiluId);
			addTaskMsg(21,$jiluId,'有新的退货单需要您审核，请及时处理！');
			redirect("?m=system&s=tuihuo");
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
	$sql="select * from demo_tuihuo where comId=$comId ";
	if(!empty($status)){
		switch ($status) {
			case 1:
				$sql.=" and status=0";
			break;
			case 2:
				$sql.=" and status in(1,2)";
			break;
			default:
				$sql.=" and status=$status";
			break;
		}
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
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
					$status = '<span style="color:red">已作废</span>';
				break;
				case 0:
					$status = '<span style="color:red">待退单审核</span>';
				break;
				case 1:
					$status = '<span style="color:red">待收货确认</span>';
				break;
				case 2:
					$status = '<span style="color:red">待财务审核</span>';
				break;
				case 4:
					$status = '<span style="color:red">待收款确认</span>';
				break;
				case 3:
					$status = '<span style="color:green">已完成</span>';
				break;
			}
			$j->status = $status;
			$payStatus = '';
			switch ($j->payStatus){
				case 0:
					$payStatus = '未退款';
				break;
				case 1:
					$payStatus = '部分退款';
				break;
				case 2:
					$payStatus = '已退款';
				break;
			}
			$j->payStatus = $payStatus;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			if($j->orderType==2){
				$j->orderId = $j->orderId.'<div class="table-tag"><div class="sub-tag">代下单</div></div>';
			}
			$j->orderId = '<span onclick="view_jilu(\'tuihuo\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
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
	$jilu = $db->get_row("select id,status,orderId,kehuId from demo_tuihuo where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=0){
		echo '{"code":0,"message":"该任务已经处理过了！"}';
		exit;
	}
	$status = 1;$statusType='退货单订单审核';
	$liucheng = getLiucheng();
	if($liucheng['if_shouhuo']==0){
		$status = 2;
		if($liucheng['if_caiwu']==0){
			$status = 4;
			if($liucheng['if_queren']==0)$status = 3;
		}else{
			addTaskMsg(23,$jiluId,'有新的退货单需要您进行退款操作，请及时处理！');
		}
	}else{
		addTaskMsg(22,$jiluId,'有新的退货单需要您进行收货操作，请及时处理！');
	}
	$content = str_replace('退货单','退货单已通过',$statusType);
	if(!empty($cont)){
		$content.='，说明：'.$cont;
	}
	$db->query("update demo_tuihuo set status=$status where id=$jiluId");
	addJilu($jiluId,$statusType,$content);
	$content = '退货单'.$jilu->orderId.'已通过订单审核';
	add_dinghuo_msg($jilu->kehuId,$content,2,$jiluId);
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function caiwu_shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select id,status,orderId,kehuId from demo_tuihuo where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=2){
		echo '{"code":0,"message":"该任务不是待财务审核状态！"}';
		exit;
	}
	$statusType='退货单财务审核';
	$status = 4;
	if($liucheng['if_queren']==0)$status = 3;
	$content = str_replace('退货单','退货单已通过',$statusType);
	$db->query("update demo_tuihuo set status=$status where id=$jiluId");
	addJilu($jiluId,$statusType,$content);
	$content = '退货单'.$jilu->orderId.'已通过财务审核';
	add_dinghuo_msg($jilu->kehuId,$content,2,$jiluId);
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select id,status,orderId,kehuId from demo_tuihuo where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=0){
		echo '{"code":0,"message":"该任务已经处理过了！"}';
		exit;
	}
	$status = -1;$statusType='退货单审核不通过';
	$content=$statusType.',原因：'.$cont;
	if(!empty($cont)){
		$content.='，说明：'.$cont;
	}
	$db->query("update demo_tuihuo set status=$status where id=$jiluId");
	addJilu($jiluId,$statusType,$content);
	$content = '退货单'.$jilu->orderId.'已作废，原因：'.$cont;
	add_dinghuo_msg($jilu->kehuId,$content,2,$jiluId);
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function ruku(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$tuihuoId = $jiluId = (int)$request['jiluId'];
	$storeId = (int)$request['storeId'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select id,status from demo_tuihuo where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	if($jilu->status!=1){
		echo '{"code":0,"message":"该任务不是待入库审核状态，无法入库！"}';
		exit;
	}
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId");
	$dtTime = date("Y-m-d H:i:s",strtotime($request['dtTime']));
	$type = 1;
	$orderInt = getOrderId($comId,$type);
	$orderId = $kucun_set->ruku_pre.'_'.date("Ymd").'_'.$orderInt;
	$status = 1;
	$shenheUser = 0;
	$shenheName = '';
	$type_info = '销售退货';
	$jingbanren = '';
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,$type,$storeId,0,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','$jingbanren',$shenheUser,'$shenheName','','$storeName')");
	$jiluId = $db->get_var("select last_insert_id();");
	$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values";
	$rukuSql1 = '';
	$jilus = $db->get_results("select * from demo_tuihuo_detail where jiluId=$tuihuoId");
	foreach ($jilus as $key => $detail){
		$num = $detail->num;
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
		$rukuChengben = $detail->price;
		$zongchengben = $rukuChengben+$lastJilu->zongchengben;
		$zongNum = $lastJilu->kucun+$num;
		$chengben = getXiaoshu($zongchengben/$zongNum,4);
		if($chengben<0)$chengben=0;
		$db->query("update demo_kucun set kucun=kucun+$num,chengben='$chengben' where inventoryId=$inventoryId and storeId=$storeId limit 1");
		$db->query("update demo_product_inventory set kucun=kucun+$num where id=$inventoryId");
		$rukuSql1.=",($comId,$jiluId,$inventoryId,$productId,'$pdtInfo',$storeId,'$storeName','$num',$status,'$kucun','',$type,'$type_info','$dtTime','$units','$rukuChengben','$zongchengben')";
	}
	$rukuSql1 = substr($rukuSql1,1);
	$db->query($rukuSql.$rukuSql1);
	$status = 2;$statusType='退货单入库审核';
	$liucheng = getLiucheng();
	if($liucheng['if_caiwu']==0){
		$status = 4;
		if($liucheng['if_queren']==0)$status = 3;
	}else{
		addTaskMsg(23,$tuihuoId,'有新的退货单需要您进行退款操作，请及时处理！');
	}
	$content = str_replace('退货单','退货单已通过',$statusType);
	$content.=',退货仓库：'.$storeName;
	$db->query("update demo_tuihuo set status=$status where id=$tuihuoId");
	addJilu($tuihuoId,$statusType,$content);
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
	$kehuId = (int)$request['kehuId'];
	if(empty($kehuId)){
		echo '{"code":0,"msg":"","count":0,"data":[]}';
		exit;
	}
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
	$sql="select id,sn,title,key_vals,productId,weight from demo_product_inventory where comId=$comId and id not in($hasIds)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId,weight','count(*)',$sql));
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
			$price = getKehuPrice($pdt->id,$kehuId);
			if(!empty($price)){
			$price = getXiaoshu($price,$product_set->price_num);
				$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","shuliang":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" id=\"shuliang_'.$pdt->id.'\">","price":"'.$price.'","units":"'.$unitstr.'","weight":"'.$pdt->weight.'"}';
			}
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
//获取客户的银行
function getKehuInfo(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fapiao = $db->get_row("select name,phone,caiwu from demo_kehu where id=$id and comId=$comId");
	if(!empty($fapiao)){
		$arry = array();
		$caiwu = json_decode($fapiao->caiwu,true);
		$caiwu['name'] = $fapiao->name;
		$caiwu['phone'] = $fapiao->phone;
		$arry['code'] = 1;
		$arry['message'] = '成功';
		$arry['fapiao']=$caiwu;
		echo json_encode($arry,JSON_UNESCAPED_UNICODE);
	}else{
		echo '{"code":0,"message":"客户不存在！"}';
	}
	exit;
}
//根据客户id，产品id获取订货价格
function getKehuPrice($inventoryId,$kehuId){
	global $db;
	$dinghuo = $db->get_row("select ifsale,price_sale from demo_product_dinghuo where inventoryId=$inventoryId and kehuId=$kehuId limit 1");
	if(!empty($dinghuo)){
		if($dinghuo->ifsale==1){
			return $dinghuo->price_sale;
		}else{
			return 0;
		}
	}else{
		$level = $db->get_var("select level from demo_kehu where id=$kehuId");
		$dinghuo = $db->get_row("select ifsale,price_sale from demo_product_dinghuo where inventoryId=$inventoryId and levelId=$level limit 1");
		if(!empty($dinghuo)){
			if($dinghuo->ifsale==1){
				return $dinghuo->price_sale;
			}else{
				return 0;
			}
		}else{
			$pdtPrice = $db->get_var("select price_sale from demo_product_inventory where id=$inventoryId");
			$zhekou = $db->get_var("select zhekou from demo_kehu_level where id=$level");
			return $pdtPrice*100/$zhekou;
		}
	}
}
function getCzJilus(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluOrder = $db->get_row("select username,kehuId,kehuName,orderType,dtTime from demo_tuihuo where id=$jiluId and comId=$comId limit 1");
	if(empty($jiluOrder)){
		die("订单不存在！");
	}
	$jilus = $db->get_results("select * from demo_tuihuo_jilu where jiluId=$jiluId order by id desc limit 100");
	$str = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$str.='<tr height="43">
	                	<td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->company.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->username.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                     	'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->statusType.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->content.'
	                    </td>
	                </tr>';
		}
	}
	$str.='<tr height="43">
            	<td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	'.($jiluOrder->orderType==1?$jiluOrder->kehuName:$_SESSION[TB_PREFIX.'com_title']).'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	'.$jiluOrder->username.'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                 	'.date("Y-m-d H:i",strtotime($jiluOrder->dtTime)).'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	创建退货单
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	已提交退货单，等待退货单审核
                </td>
            </tr>';
    echo $str;exit;
}
function addBeizhu(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluOrder = $db->get_row("select id,beizhu from demo_tuihuo where id=$jiluId and comId=$comId limit 1");
	if(!empty($jiluOrder)){
		$results = array();
		if(!empty($jiluOrder->beizhu)){
			$results = json_decode($jiluOrder->beizhu,true);
		}
		$fankui = array();
		$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['cont']);
		$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
		$fankui['time'] = date('Y-m-d H:i:s');
		$fankui['company'] = $_SESSION[TB_PREFIX.'com_title'];
		array_unshift($results,$fankui);
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update demo_tuihuo set beizhu='$resultstr' where id=$jiluId");
		$fankui['content'] = str_replace('\n','<br>',$fankui['content']);
		$fankui['content'] = str_replace('"','',$fankui['content']);
		$fankui['content'] = str_replace("'",'',$fankui['content']);
		echo '{"code":1,"message":"<div style=\"padding-bottom:10px;\">'.$fankui['content'].'【'.$fankui['name'].'&nbsp;/&nbsp;'.$fankui['company'].'&nbsp;&nbsp;'.$fankui['time'].'】</div>"}';
	}else{
		echo '{"code":0,"message":"记录不存在"}';
	}
	exit;
}
//插入订单操作记录
function addJilu($jiluId,$statusType,$content){
	$jilu = array();
	$jilu['jiluId'] = $jiluId;
	$jilu['username'] = $_SESSION[TB_PREFIX.'name'];
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['statusType'] = $statusType;
	$jilu['content'] = $content;
	$jilu['company'] = $_SESSION[TB_PREFIX.'com_title'];
	insert_update('demo_tuihuo_jilu',$jilu,'id');
}
function shoukuan(){}
function getShoukuanInfo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['dinghuoId'];
	$return = array();
	$jilu = $db->get_row("select * from demo_tuihuo where id=$id and comId=$comId");
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在，请刷新重试。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$return['code'] = 1;
	$return['message'] = '成功';
	$return['data']['money'] = $jilu->money;
	$return['data']['payed'] = $jilu->money-$jilu->money_weikuan;
	$daizhifu = $return['data']['daizhifu'] = $jilu->money_weikuan;
	if($return['data']['daizhifu']<=0){
		$return['code']=0;
		$return['message']='已收回所有款项，不需要再添加。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$return['data']['orderId'] = $jilu->orderId;
	$return['data']['yue_account1'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=1 limit 1");
	$return['data']['yue_account2'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=2 limit 1");
	$return['data']['yue_account3'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=3 limit 1");
	if(empty($return['data']['yue_account1']))$return['data']['yue_account1']=0;
	if(empty($return['data']['yue_account2']))$return['data']['yue_account2']=0;
	if(empty($return['data']['yue_account3']))$return['data']['yue_account3']=0;
	$return['data']['account1'] = 0;
	$return['data']['account2'] = 0;
	$return['data']['account3'] = 0;
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function add_shoukuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['dinghuoId'];
	$return = array();
	$jilu = $db->get_row("select * from demo_tuihuo where id=$id and comId=$comId");
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在，请刷新重试。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$account1 = $request['a_account1'];
	$account2 = $request['a_account2'];
	$account3 = $request['a_account3'];
	if($account1+$account2+$account3>$jilu->money_weikuan){
		$return['code']=0;
		$return['message']='退款金额大于待退款金额！';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$zongMoney = $account1+$account2+$account3;
	$price_weikuan = $jilu->money_weikuan;
	$jingbanren = $request['jingbanren'];
	$remark = $request['remark'];
	if($account1>0){
		add_dinghuo_money($jilu,$account1,1,$jingbanren,$remark);
	}
	if($account2>0){
		add_dinghuo_money($jilu,$account2,2,$jingbanren,$remark);
	}
	if($account3>0){
		add_dinghuo_money($jilu,$account3,3,$jingbanren,$remark);
	}
	$payStatus = 1;
	if($zongMoney==$price_weikuan)$payStatus=2;
	$db->query("update demo_tuihuo set payStatus=$payStatus,money_weikuan=money_weikuan-$zongMoney where id=$id");
	$content = '退货单已退款：￥'.$zongMoney;
	add_dinghuo_msg($jilu->kehuId,$content,2,$id);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//添加账户支付记录
function add_dinghuo_money($jilu,$money,$type,$jingbanren,$beizhu){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$dinghuo_money = array();
	$dinghuo_money['comId'] = $comId;
	$dinghuo_money['kehuId'] = $jilu->kehuId;
	$dinghuo_money['kehuName'] = $jilu->kehuName;
	$dinghuo_money['jiluId'] = $jilu->id;
	$dinghuo_money['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$dinghuo_money['money'] = $money;
	$dinghuo_money['dtTime'] = date("Y-m-d H:i:s");
	$dinghuo_money['status'] = 1;
	$dinghuo_money['pay_type'] = $type;
	$dinghuo_money['pay_info'] = '退货单退款';
	$dinghuo_money['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_money['userName'] = $_SESSION[TB_PREFIX.'name'];
	$dinghuo_money['jingbanren'] = $jingbanren;
	$dinghuo_money['remark'] = $remark;
	insert_update('demo_tuihuo_money',$dinghuo_money,'id');
	$db->query("update demo_kehu_account set money=money+$money where kehuId=$jilu->kehuId and type=$type limit 1");
	$liushui = array();
	$liushui['comId'] = $comId;
	$liushui['orderId'] = $dinghuo_money['orderId'];
	$liushui['order_type'] = 2;
	$liushui['dinghuoId'] = $jilu->id;
	$liushui['dinghuoOrderId'] = $jilu->orderId;
	$liushui['kehuId'] = $jilu->kehuId;
	$liushui['type'] = 1;
	$liushui['accountType'] =$type;
	$liushui['typeInfo'] = getPayType($type);
	$liushui['dtTime'] = date("Y-m-d H:i:s");
	$liushui['remark'] = '退货单退款';
	$liushui['money'] = $money;
	$liushui['status'] = 1;
	$liushui['userName'] = $dinghuo_money['userName'];
	$liushui['shenheUser'] = '系统自动';
	insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
}
function getLiucheng(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$liucheng = array('if_shouhuo'=>1,'if_caiwu'=>1,'if_queren'=>0);
	$liuchengContent = $db->get_var("select content from demo_liucheng where comId=$comId and type=2");
	if(!empty($liuchengContent)){
		$liucheng = json_decode($liuchengContent,true);
	}
	return $liucheng;
}