<?php
function shenqing(){}
function view_shenqing(){}
function view(){}
function caiwu(){}
function dikoujin(){}
function caiwus(){}
function fafang(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$userId = (int)$request['userId'];
		$money = $request['money'];
		$db_service = getCrmDb();
		$u = $db_service->get_row("select name,username from demo_user where id=$userId");
		$db->query("update demo_shops set lipinka_money=lipinka_money-$money where comId=$comId");
		$liushui = array();
		$liushui['comId']=$comId;
		$liushui['userId']=$userId;
		$liushui['money']=$money;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['username']=$u->username;
		$liushui['name']=$u->name;
		insert_update('gift_card_fafang',$liushui,'id');
		add_linpinka_money($userId,$money,'商家赠送','来自商家“'.$_SESSION[TB_PREFIX.'com_title'].'”的赠送',0);
		echo '{"code":1,"message":"发放成功"}';
		exit;
	}
}
function get_fafang_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
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
	$sql = "select * from gift_card_fafang where comId=$comId ";
	if(!empty($keyword)){
		$sql.=" and (username like '%$keyword%' or name like '%$keyword%')";
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
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function add_mendian(){
	global $db,$request;
	if($request['tijiao']==1){
		$id = (int)$request['id'];
		$caiwu = json_encode($request['caiwu'],JSON_UNESCAPED_UNICODE);
		//file_put_contents('request.txt',json_encode($mendian,JSON_UNESCAPED_UNICODE));
		$db->query("update demo_shops set caiwu='$caiwu' where comId=$id");
	}
}
function add_tixian(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$money = $request['money'];
	$kaihu = $request['kaihu'];
	$shouxufei= 0;
	/*$shouxufei = 1;
	if($money>1000){
		$shouxufei = ((int)$money/10)/100;
		if($shouxufei>25){
			$shouxufei = 25;
		}
	}*/
	$mendianId = (int)$_SESSION[TB_PREFIX.'comId'];
	$yzFenbiao = getFenbiao(10,20);
	$yue = $db->get_var("select money from demo_shops where comId=$mendianId");
	if($money>$yue){
		die('{"code":0,"message":"余额不足"}');
	}
	$caiwu = $db->get_var("select caiwu from demo_shops where comId=".$mendianId." limit 1");
	$c = json_decode($caiwu);
	if($c->kaihubank==''){
		die('{"code":0,"message":"请先添加银行帐号！"}');
	}
	$ifhas = $db->get_row("select id,status from user_tixian where mendianId=$mendianId order by id desc limit 1");
	if(!empty($ifhas) && $ifhas->status==0){
		die('{"code":0,"message":"您有尚未处理的提现请求，请等待管理员审核过后再进行提现操作"}');
	}
	$db->query("update demo_shops set money=money-$money where comId=$mendianId");
	$tixian = array();
	$tixian['comId'] = 10;
	$tixian['money'] = $money-$shouxufei;
	$tixian['shouxufei'] = $shouxufei;
	$tixian['dtTime'] = date("Y-m-d H:i:s");
	$tixian['mendianId'] = $mendianId;
	$tixian['yue'] = $db->get_var("select money from demo_shops where comId=$mendianId");
	insert_update('user_tixian',$tixian,'id');
	$liushui = array();
	$liushui['mendianId']=$mendianId;
	$liushui['comId']=10;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=-$money;
	$liushui['yue']=$yue-$money;
	$liushui['type']=2;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['typeInfo']='提现';
	$liushui['remark']='申请提现';
	insert_update('demo_mendian_liushui'.$yzFenbiao,$liushui,'id');
	die('{"code":1}');
}
function get_liushui_jilu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$yzFenbiao = getFenbiao(10,20);
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
	$sql = "select * from demo_mendian_liushui$yzFenbiao where mendianId=$id ";
	if(!empty($type)){
		if($type==4){
			$sql.=" and (type=$type or typeInfo='订单退货快递费用')";
		}elseif($type==2){
			$sql.=" and (type=$type or typeInfo='提现作废')";
		}else{
			$sql.=" and type=$type and typeInfo!='订单退货快递费用' and typeInfo!='提现作废'";
		}
	}else{
		$sql.=" and type<4";
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
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->money = $j->money>0?'<span style="color:green">+'.$j->money.'</span>':'<span style="color:red">'.$j->money.'</span>';
			/*if($j->type==4){
				$j->money = '<i class="mark back_zi">保</i>'.$j->money;
				$j->yue = '<i class="mark back_zi">保</i>'.$j->yue;
			}*/
			$j->income_type = '店铺余额';
			$j->statusInfo = '已确认';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_liushui_jilu1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$fenbiao = getFenbiao($comId,20);
	$type = (int)$request['type'];
	$status = (int)$request['status'];
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
	$sql = "select * from demo_yibao_fenzhang where comId=$id ";
	if(!empty($type)){
		$sql.=" and income_type=$type";
	}
	if(!empty($status)){
		$sql.=" and status=$status";
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
			$j->id = $j->orderId;
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->shouxufei = 0;
			$j->shiji_money = $j->money;
			if($j->income_type==1){
				$j->shouxufei = round($j->money*7/1000,2);
				$j->shiji_money = $j->money-$j->shouxufei;
			}
			$j->money = $j->money>0?'<span style="color:green">+'.$j->money.'</span>':'<span style="color:red">'.$j->money.'</span>';
			$j->typeInfo = $j->type==1?'订单收益':'支出';
			$j->income_type = $j->income_type==1?'易宝':'店铺余额';
			$j->statusInfo = $j->status==1?'<span style="color:red">冻结中</span>':'<span style="color:green">已到账</span>';
			$j->orderId = $db->get_var("select orderId from order$fenbiao where id=$j->orderId");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function fafang_dikoujin(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$yue = $db->get_var("select lipinka_money from demo_shops where comId=$comId");
	$ids = explode(',',$request['ids']);
	$money = $request['money'];
	$fafang_money = $money*count($ids);
	if($fafang_money>$yue){
		die('{"code":1,"message":"抵扣金可发放余额不足，最多还可发放：'.$yue.'，请联系客服续费"}');
	}
	if(!empty($ids)){
		foreach ($ids as $uid) {
			add_linpinka_money($uid,$money,'商家赠送','来自商家“'.$_SESSION[TB_PREFIX.'com_title'].'”的赠送',0);
		}
	}
	die('{"code":1,"message":"发放成功"}');
}
//给会员永久抵扣卡充值
function add_linpinka_money($userId,$money,$remark,$info,$daili_id){
	global $db;
	$fenbiao = 10;
	$card = $db->get_row("select * from gift_card10 where userId=$userId and (endTime is NULL or endTime='0000-00-00') order by id asc limit 1");
	if(empty($card)){
		$sql = "insert into gift_card$fenbiao(comId,cardId,password,money,yue,jiluId,typeInfo,userId,bind_time,bili,from_id,daili_id) values";
		$sql1 = '';
		$cardId = $userId;
		$length = 16-strlen($cardId);
		for($j = 0; $j < $length; $j++) {
			$cardId .= rand(0,9);
		}
		$password = rand(100000,999999);
		$sql1.=" (10,'$cardId','$password','$money','$money',1,'抵扣卡',$userId,'".date("Y-m-d H:i:s")."','100.00',0,$daili_id)";
		$db->query($sql.$sql1);
		$card_id = $db->get_var("select last_insert_id();");
		$liushui = array();
		$liushui['cardId']=$card_id;
		$liushui['money']=$money;
		$liushui['yue']=$money;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']=$remark;
		$liushui['orderInfo']=$info;
		$liushui['orderId']=0;
		insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	}else{
		$db->query("update gift_card$fenbiao set yue=yue+$money where id=$card->id");
		$liushui = array();
		$liushui['cardId']=$card->id;
		$liushui['money']=$money;
		$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$card->id");
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']=$remark;
		$liushui['orderInfo']=$info;
		$liushui['orderId']=0;
		insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	}	
}