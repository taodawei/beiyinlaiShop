<?php
function index(){}
function shoukuan(){}
function shoukuan1(){}
function tuikuan(){}
function account(){}
function shouzhi(){}
function daochuTongji(){}
function daochuAccount(){}
function getShoukuanOrder(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$keyword = $request['keyword'];
	$type = (int)$request['type'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('skdPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,kehuId,price,price_weikuan,dtTime from demo_dinghuo_order where comId=$comId and status>0 ";
	if($type==1){
		$sql.=" and price_weikuan>0";
		$a = 'shoukuan';
	}else{
		$sql.=" and price_weikuan=0";
		$a = 'shoukuan1';
	}
	if(!empty($keyword)){
		$sql.=" and (orderId='$keyword' or kehuName='$keyword')";
	}
	$countsql = str_replace('id,orderId,kehuId,price,price_weikuan,dtTime','count(*)',$sql);
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->kehuName = $db->get_var("select title from demo_kehu where id=$j->kehuId");
			$j->orderId = '<span onclick="view_jilu(\''.$a.'\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
			$j->price_daiqueren = $db->get_var("select sum(money) from demo_dinghuo_money where jiluId=$j->id and status=0 and type=0");
			if(empty($j->price_daiqueren)){
				$j->price_daiqueren=0;
			}else{
				$j->price_daiqueren = '<span style="color:red">'.$j->price_daiqueren.'</span>';
			}
			$j->qrTime = $db->get_var("select shenheTime from demo_dinghuo_money where jiluId=$j->id order by id desc limit 1");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getShoukuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$account = $request['account'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('skdPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_dinghuo_money where comId=$comId and status=0 and type=0";
	if(!empty($account)){
		$sql.=" and shoukuan_info like '%$account%'";
	}
	if(!empty($keyword)){
		$dinghuoId = (int)$db->get_var("select id from demo_dinghuo_order where comId=$comId and orderId='$keyword' limit 1");
		$sql.=" and (kehuName='$keyword' or orderId='$keyword' or username='$keyword' or jiluId=$dinghuoId)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->pay_type = getPayType($j->pay_type);
			$j->account = $j->pay_type;
			if($j->account=='银行转账'){
				$account = json_decode($j->shoukuan_info);
				$j->account = $account->bank_name.'<br>'.$account->bank_account;
			}
			$dinghuoOrder = $db->get_var("select orderId from demo_dinghuo_order where id=$j->jiluId");
			$j->detail = 'dinghuoId|'.$dinghuoOrder.',money|'.$j->money.',pay_type|'.$j->pay_type.',beizhu|'.$j->beizhu.',orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheCont.',fujian|'.$j->files;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getTuikuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$account = $request['account'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('skdPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_dinghuo_money where comId=$comId and status=0 and type=1";
	if(!empty($account)){
		$sql.=" and shoukuan_info like '%$account%'";
	}
	if(!empty($keyword)){
		$dinghuoId = (int)$db->get_var("select id from demo_dinghuo_order where comId=$comId and orderId='$keyword' limit 1");
		$sql.=" and (kehuName='$keyword' or orderId='$keyword' or username='$keyword' or jiluId=$dinghuoId)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->pay_type = getPayType($j->pay_type);
			$j->account = $j->pay_type;
			if($j->account=='银行转账'){
				$account = json_decode($j->shoukuan_info);
				$j->account = $account->bank_name.'<br>'.$account->bank_account;
			}
			$dinghuoOrder = $db->get_var("select orderId from demo_dinghuo_order where id=$j->jiluId");
			$j->detail = 'dinghuoId|'.$dinghuoOrder.',money|'.$j->money.',pay_type|'.$j->pay_type.',beizhu|'.$j->beizhu.',orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheCont.',fujian|'.$j->files;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getShouzhis(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$remark = $request['remark'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$account = $request['account'];
	$keyword = $request['keyword'];
	$level = (int)$request['level'];
	$page = (int)$request['page'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('shouzhiPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_kehu_liushui$fenbiao where comId=$comId";
	if(!empty($account)){
		$sql.=" and typeInfo like '%$account%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($remark)){
		$sql.=" and remark='$remark'";
	}
	if(!empty($keyword)){
		$dinghuoId = (int)$db->get_var("select id from demo_dinghuo_order where comId=$comId and orderId='$keyword' limit 1");
		$tuihuoId = (int)$db->get_var("select id from demo_tuihuo where comId=$comId and orderId='$keyword' limit 1");
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and title like '%$keyword%'");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and (kehuId in($kehuIds) or orderId like '%$keyword%' or dinghuoId=$dinghuoId)";
	}
	if($level>0){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->pay_type = getPayType($j->accountType);
			$j->account = $j->pay_type;
			if($j->account=='银行转账'){
				$account = json_decode($j->typeInfo);
				$j->account = $account->bank_name.'<br>'.$account->bank_account;
			}
			$status = '';
			$j->layclass = '';
			if($j->status==-1){
				$j->layclass = 'deleted';
				$status = '已作废';
			}else{
				$status = '已完成';
			}
			$j->status = $status;
			$j->detail = 'orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheUser;
			$payType = getPayType($j->accountType);
			if(!empty($j->dinghuoId)){
				if($j->order_type==1){
					$j->detail .=',订货单号|'.$j->dinghuoOrderId;
					if($j->type==1){
						$j->detail .=',充值资金账户|'.$payType;
					}else{
						$j->detail .=',支付方式|'.$j->dinghuoOrderId;
					}
				}else{
					$j->detail .=',退货单号|'.$j->dinghuoOrderId;
					$j->detail .=',充值资金账户|'.$payType;
				}
			}else{
				if($j->type==1){
					$j->detail .=',充值资金账户|'.$payType;
				}else{
					$j->detail .=',支付方式|'.$j->dinghuoOrderId;
				}
			}
			$j->detail .=',金额|'.$j->money;
			$j->detail .=',备注|'.$j->remark;
			if($j->type==1){
				$j->money = '<span style="color:green">'.$j->money.'</span>';
			}else{
				$j->money = '<span style="color:red">'.$j->money.'</span>';
			}
			if(!empty($j->dinghuoId)&&$j->order_type==1){
				$j->dinghuoOrderId = '<span onclick="view_dinghuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
			}else if(!empty($j->dinghuoId)&&$j->order_type==2){
				$j->dinghuoOrderId = '<span onclick="view_tuihuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
			}
			$j->kehuName = $db->get_var("select title from demo_kehu where id=$j->kehuId");
			$j->remark = sys_substr($j->remark,10,true);
			
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_acc_detail(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$remark = $request['remark'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$account = $request['account'];
	$accountType = $request['accountType'];
	$keyword = $request['keyword'];
	$level = (int)$request['level'];
	$page = (int)$request['page'];
	$id = (int)$request['id'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('shouzhiPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_kehu_liushui$fenbiao where comId=$comId and kehuId=$id";
	if(!empty($account)){
		$sql.=" and typeInfo like '%$account%'";
	}
	if(!empty($accountType)){
		$sql.=" and accountType=$accountType";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($remark)){
		$sql.=" and remark='$remark'";
	}
	if(!empty($keyword)){
		$dinghuoId = (int)$db->get_var("select id from demo_dinghuo_order where comId=$comId and orderId='$keyword' limit 1");
		$tuihuoId = (int)$db->get_var("select id from demo_tuihuo where comId=$comId and orderId='$keyword' limit 1");
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and title like '%$keyword%'");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and (kehuId in($kehuIds) or orderId like '%$keyword%' or dinghuoId=$dinghuoId)";
	}
	if($level>0){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->pay_type = getPayType($j->accountType);
			$j->account = $j->pay_type;
			if($j->account=='银行转账'){
				$account = json_decode($j->typeInfo);
				$j->account = $account->bank_name.'<br>'.$account->bank_account;
			}
			$status = '';
			$j->layclass = '';
			if($j->status==-1){
				$j->layclass = 'deleted';
				$status = '已作废';
			}else{
				$status = '已完成';
			}
			$j->status = $status;
			$j->detail = 'orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheUser;
			$payType = getPayType($j->accountType);
			if(!empty($j->dinghuoId)){
				if($j->order_type==1){
					$j->detail .=',订货单号|'.$j->dinghuoOrderId;
					if($j->type==1){
						$j->detail .=',充值资金账户|'.$payType;
					}else{
						$j->detail .=',支付方式|'.$payType;
					}
				}else{
					$j->detail .=',退货单号|'.$j->dinghuoOrderId;
					$j->detail .=',充值资金账户|'.$payType;
				}
			}else{
				if($j->type==1){
					$j->detail .=',充值资金账户|'.$payType;
				}else{
					$j->detail .=',支付方式|'.$j->dinghuoOrderId;
				}
			}
			$j->detail .=',金额|'.$j->money;
			$j->detail .=',备注|'.$j->remark;
			if($j->type==1){
				$j->money = '<span style="color:green">'.$j->money.'</span>';
			}else{
				$j->money = '<span style="color:red">'.$j->money.'</span>';
			}
			if(!empty($j->dinghuoId)&&$j->order_type==1){
				$j->dinghuoOrderId = '<span onclick="view_dinghuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
			}else if(!empty($j->dinghuoId)&&$j->order_type==2){
				$j->dinghuoOrderId = '<span onclick="view_tuihuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
			}
			$j->kehuName = $db->get_var("select title from demo_kehu where id=$j->kehuId");
			$j->remark = sys_substr($j->remark,10,true);
			
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getTongjis(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$areaId = $request['areaId'];
	$paystatus = $request['paystatus'];
	$level = $request['level'];
	$kehuStatus = $request['kehuStatus'];
	$page = (int)$request['page'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('m_tongjiPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_dinghuo_order where comId=$comId and status>-1";
	if(!empty($keyword)){
		$sql.=" and (kehuName like '%$keyword%' or orderId like '%$keyword%')";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($kehuName)){
		$sql.=" and kehuName like '%$kehuName%'";
	}
	if(!empty($areaId)){
		$areaIds = $areaId.getZiAreas($areaId);
		$sql.=" and areaId in($areaIds)";
	}
	if(!empty($paystatus)){
		if($paystatus==1){
			$sql.=" and payStatus<4";
		}else{
			$sql.=" and payStatus=4";
		}
	}
	if(!empty($level)){
		if($level>0){
			$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
			if(empty($kehuIds))$kehuIds='0';
			$sql.=" and kehuId in($kehuIds)";
		}
	}
	if(!empty($kehuStatus)){
		if($kehuStatus==2)$kehuStatus=0;
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and status=$kehuStatus");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->price_dinghuo = $j->price-$j->price_wuliu;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getAccounts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$keyword = $request['keyword'];
	$areaId = $request['areaId'];
	$level = $request['level'];
	$kehuStatus = $request['kehuStatus'];
	$page = (int)$request['page'];
	$pageNum = empty($request["limit"])?10:(int)$request["limit"];
	setcookie('m_accPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,title from demo_kehu where comId=$comId";
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($areaId)){
		$areaIds = $areaId.getZiAreas($areaId);
		$sql.=" and areaId in($areaIds)";
	}
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($kehuStatus)){
		if($kehuStatus==2)$kehuStatus=0;
		$sql.=" and status=$kehuStatus";
	}
	$count = $db->get_var(str_replace('id,title','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->account1 = 0;
			$j->account2 = 0;
			$j->account3 = 0;
			$j->account4 = 0;
			$accounts = $db->get_results("select type,money from demo_kehu_account where kehuId=$j->id and type in(1,2,3,4) limit 4");
			if(!empty($accounts)){
				foreach ($accounts as $a){
					switch ($a->type) {
						case 1:
							$j->account1 = $a->money;
						break;
						case 2:
							$j->account2 = $a->money;
						break;
						case 3:
							$j->account3 = $a->money;
						break;
						case 4:
							$j->account4 = $a->money;
						break;
					}
				}
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_tongji_money(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$areaId = $request['areaId'];
	$paystatus = $request['paystatus'];
	$level = $request['level'];
	$kehuStatus = $request['kehuStatus'];
	$sql="select sum(price) as price,sum(price_wuliu) as price_wuliu,sum(price_payed) as price_payed,sum(price_weikuan) as price_weikuan from demo_dinghuo_order where comId=$comId and status>-1";
	if(!empty($keyword)){
		$sql.=" and (kehuName like '%$keyword%' or orderId like '%$keyword%')";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($kehuName)){
		$sql.=" and kehuName like '%$kehuName%'";
	}
	if(!empty($areaId)){
		$areaIds = $areaId.getZiAreas($areaId);
		$sql.=" and areaId in($areaIds)";
	}
	if(!empty($paystatus)){
		if($paystatus==1){
			$sql.=" and payStatus<4";
		}else{
			$sql.=" and payStatus=4";
		}
	}
	if(!empty($level)){
		if($level>0){
			$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
			if(empty($kehuIds))$kehuIds='0';
			$sql.=" and kehuId in($kehuIds)";
		}
	}
	$jilu = $db->get_row($sql);
	$dataJson = array("code"=>1,"msg"=>'成功');
	$dataJson['price1'] = empty($jilu->price)?'0.00':$jilu->price;
	$dataJson['price2'] = empty($jilu->price_payed)?'0.00':$jilu->price_payed;
	$dataJson['price3'] = empty($jilu->price_weikuan)?'0.00':$jilu->price_weikuan;
	$dataJson['price5'] = empty($jilu->price_wuliu)?'0.00':$jilu->price_wuliu;
	$dataJson['price4'] = $jilu->price-$jilu->price_wuliu;
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function quren(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jilu = $db->get_row("select * from demo_dinghuo_money where id=$id and comId=$comId");
	$return = array();
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	if($jilu->status!=0){
		$return['code']=0;
		$return['message']='该记录已处理，不需要再次确认';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$dinghuo_money = array();
	$dinghuo_money['id'] = $id;
	$dinghuo_money['shenheTime'] = date("Y-m-d H:i:s");
	$dinghuo_money['status'] = 1;
	$dinghuo_money['shenheUser'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_money['shenheCont'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_money',$dinghuo_money,'id');
	if($jilu->pay_type>0&&$jilu->pay_type<5){
		$db->query("update demo_kehu_account set money=money-$jilu->money where kehuId=$jilu->kehuId and type=$jilu->pay_type limit 1");
	}
	$liushui = array();
	$liushui['comId'] = $comId;
	$liushui['orderId'] = $jilu->orderId;
	$liushui['order_type'] = 1;
	$liushui['dinghuoId'] = $jilu->jiluId;
	$liushui['dinghuoOrderId'] = $db->get_var("select orderId from demo_dinghuo_order where id=$jilu->jiluId");
	$liushui['kehuId'] = $jilu->kehuId;
	$liushui['type'] = 2;
	$liushui['accountType'] =$jilu->pay_type;
	$liushui['typeInfo'] = getPayType($jilu->pay_type);
	if($jilu->pay_type==6){
		$liushui['typeInfo'] = $jilu->shoukuan_info;
	}
	$liushui['dtTime'] = date("Y-m-d H:i:s");
	$liushui['money'] = $jilu->money;
	$liushui['status'] = 1;
	$liushui['userName'] = $jilu->userName;
	$liushui['remark'] = '订单付款';
	$liushui['shenheUser'] = $dinghuo_money['shenheCont'];
	insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	$payStatus = 3;
	$daiqueren = $db->get_var("select sum(money) from demo_dinghuo_money where jiluId=$jilu->jiluId and status=0");
	$dinghuo_order = $db->get_row("select price_weikuan,orderId,status from demo_dinghuo_order where id=$jilu->jiluId");
	$price_weikuan = $dinghuo_order->price_weikuan;
	if($jilu->money==$price_weikuan+$daiqueren)$payStatus=4;
	$new_status = $dinghuo_order->status;
	if($new_status==1&&$payStatus==4){
		$new_status = 2;
		$liucheng = getLiucheng();
		if($liucheng['if_chuku']==0&&$liucheng['if_fahuo']==0){
			if($liucheng['if_shouhuo']==0){
				$new_status = 6;
			}else{
				$new_status = 5;
			}
		}else{
			addTaskMsg(13,$jilu->jiluId,'有新的订货单需要您进行出库\发货操作，请及时处理！');
		}
	}
	$db->query("update demo_dinghuo_order set status=$new_status,payStatus=$payStatus,price_payed=price_payed+$jilu->money,price_weikuan=price_weikuan-$jilu->money where id=$jilu->jiluId");
	$orderId = $dinghuo_order->orderId;
	$content = '订货单：'.$orderId.'收款记录'.$jilu->orderId.'已审核';
	add_dinghuo_msg($jilu->kehuId,$content,1,$jilu->jiluId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function z_quren(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jilu = $db->get_row("select * from demo_dinghuo_money where id=$id and comId=$comId");
	$return = array();
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	if($jilu->status!=0){
		$return['code']=0;
		$return['message']='该记录已处理，不需要再次确认';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$dinghuo_money = array();
	$dinghuo_money['id'] = $id;
	$dinghuo_money['shenheTime'] = date("Y-m-d H:i:s");
	$dinghuo_money['status'] = 1;
	$dinghuo_money['shenheUser'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_money['shenheCont'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_money',$dinghuo_money,'id');
	$liushui = array();
	$liushui['comId'] = $comId;
	$liushui['orderId'] = $jilu->orderId;
	$liushui['order_type'] = 1;
	$liushui['dinghuoId'] = $jilu->jiluId;
	$liushui['dinghuoOrderId'] = $db->get_var("select orderId from demo_dinghuo_order where id=$jilu->jiluId");
	$liushui['kehuId'] = $jilu->kehuId;
	$liushui['type'] = 1;
	$liushui['accountType'] =$jilu->pay_type;
	$liushui['typeInfo'] = getPayType($jilu->pay_type);
	if($jilu->pay_type==6){
		$liushui['typeInfo'] = $jilu->shoukuan_info;
	}
	$liushui['dtTime'] = date("Y-m-d H:i:s");
	$liushui['money'] = $jilu->money;
	$liushui['status'] = 1;
	$liushui['remark'] = '订单退款';
	$liushui['userName'] = $jilu->userName;
	$liushui['shenheUser'] = $dinghuo_money['shenheCont'];
	insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	$ifhas = $db->get_row("select id,money from demo_kehu_account where kehuId=$jilu->kehuId and type=$jilu->pay_type limit 1");
	$kehu_account = array();
	$kehu_account['id'] = (int)$ifhas->id;
	$kehu_account['comId'] = $comId;
	$kehu_account['kehuId'] = $jilu->kehuId;
	$kehu_account['type'] = $jilu->pay_type;
	$kehu_account['money'] = $jilu->money+$ifhas->money;
	insert_update('demo_kehu_account',$kehu_account,'id');
	$orderId = $liushui['dinghuoOrderId'];
	$content = '订货单：'.$orderId.'退款记录'.$jilu->orderId.'已审核';
	add_dinghuo_msg($jilu->kehuId,$content,1,$jilu->jiluId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function zuofei(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jilu = $db->get_row("select * from demo_dinghuo_money where id=$id and comId=$comId");
	$return = array();
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	if($jilu->status!=0){
		$return['code']=0;
		$return['message']='该记录已处理，不需要再次确认';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$dinghuo_money = array();
	$dinghuo_money['id'] = $id;
	$dinghuo_money['shenheTime'] = date("Y-m-d H:i:s");
	$dinghuo_money['status'] = -1;
	$dinghuo_money['shenheUser'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_money['shenheCont'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_money',$dinghuo_money,'id');
	$orderId = $db->get_var("select orderId from demo_dinghuo_order where id=$jilu->jiluId");
	$content = '订货单：'.$orderId.'收款记录'.$jilu->orderId.'被作废';
	add_dinghuo_msg($jilu->kehuId,$content,1,$jilu->jiluId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function daochuShouzhi(){}
function tongji(){}
function acc_chongzhi(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(empty($request['kehuId'])||empty($request['money'])){
		$return['code']=0;
		$return['message']='系统错误，请刷新重试';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$liushui = array();
	$liushui['comId'] = $comId;
	$liushui['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$liushui['order_type'] = 0;
	$liushui['dinghuoId'] = 0;
	$liushui['dinghuoOrderId'] = '';
	$liushui['kehuId'] = (int)$request['kehuId'];
	$liushui['type'] = 1;
	$liushui['accountType'] =(int)$request['type'];
	$liushui['typeInfo'] = getPayType($liushui['accountType']);
	$liushui['dtTime'] = date("Y-m-d H:i:s",strtotime($request['dtTime']));
	$liushui['money'] = $request['money'];
	$liushui['status'] = 1;
	$liushui['remark'] = $request['remark'];
	$liushui['beizhu'] = $request['beizhu'];;
	$liushui['userName'] = $_SESSION[TB_PREFIX.'name'];
	$liushui['shenheUser'] = '系统自动';
	insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	$liushuiId = $db->get_var("select last_insert_id();");
	$ifhas = $db->get_row("select id,money from demo_kehu_account where kehuId=".$liushui['kehuId']." and type=".$liushui['accountType']." limit 1");
	$kehu_account = array();
	$kehu_account['id'] = (int)$ifhas->id;
	$kehu_account['comId'] = $comId;
	$kehu_account['kehuId'] = $liushui['kehuId'];
	$kehu_account['type'] = $liushui['accountType'];
	$kehu_account['money'] = $liushui['money']+$ifhas->money;
	insert_update('demo_kehu_account',$kehu_account,'id');
	$content = getPayType($liushui['accountType']).'代充值'.$liushui['money'].'，摘要：'.$liushui['remark'];
	add_dinghuo_msg($liushui['kehuId'],$content,3,$liushuiId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function acc_koukuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(empty($request['kehuId'])||empty($request['money'])){
		$return['code']=0;
		$return['message']='系统错误，请刷新重试';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$kehuId = (int)$request['kehuId'];
	$type = (int)$request['type'];
	$ifhas = $db->get_row("select id,money from demo_kehu_account where kehuId=$kehuId and type=$type limit 1");
	if($ifhas->money<$request['money']){
		$return['code']=0;
		$return['message']='扣款失败！原因：账户余额不足。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$liushui = array();
	$liushui['comId'] = $comId;
	$liushui['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$liushui['order_type'] = 0;
	$liushui['dinghuoId'] = 0;
	$liushui['dinghuoOrderId'] = '';
	$liushui['kehuId'] = (int)$request['kehuId'];
	$liushui['type'] = 2;
	$liushui['accountType'] =(int)$request['type'];
	$liushui['typeInfo'] = getPayType($liushui['accountType']);
	$liushui['dtTime'] = date("Y-m-d H:i:s",strtotime($request['dtTime']));
	$liushui['money'] = $request['money'];
	$liushui['status'] = 1;
	$liushui['remark'] = $request['remark'];
	$liushui['beizhu'] = $request['beizhu'];
	$liushui['userName'] = $_SESSION[TB_PREFIX.'name'];
	$liushui['shenheUser'] = '系统自动';
	insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	$liushuiId = $db->get_var("select last_insert_id();");
	$kehu_account = array();
	$kehu_account['id'] = (int)$ifhas->id;
	$kehu_account['comId'] = $comId;
	$kehu_account['kehuId'] = $liushui['kehuId'];
	$kehu_account['type'] = $liushui['accountType'];
	$kehu_account['money'] = $ifhas->money-$liushui['money'];
	insert_update('demo_kehu_account',$kehu_account,'id');
	$content = getPayType($liushui['accountType']).'扣款￥'.$liushui['money'].'，摘要：'.$liushui['remark'];
	add_dinghuo_msg($liushui['kehuId'],$content,3,$liushuiId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function acc_detail(){}
function getLiucheng(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$liucheng = array('if_caiwu'=>0,'if_chuku'=>1,'if_fahuo'=>1,'if_shouhuo'=>0);
	$liuchengContent = $db->get_var("select content from demo_liucheng where comId=$comId and type=1");
	if(!empty($liuchengContent)){
		$liucheng = json_decode($liuchengContent,true);
	}
	return $liucheng;
}