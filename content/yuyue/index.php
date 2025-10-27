<?php
global $request;
$no_login = array('qx_order');
if( !in_array($request['a'], $no_login) && empty($_SESSION[TB_PREFIX.'user_ID'])){
	redirect('/index.php?p=8&a=login');
}
function index(){}
function orders(){}
function view(){}
function get_yuyue_tables(){
	global $db,$request;
	$shequId = (int)$request['shequId'];
	$yuyue_date = date("Y-m-d",strtotime($request['yuyue_date']));
	$yuyue_time_type = (int)$request['time_type'];
	$yuyue_time = $db->get_var("select title from demo_shequ_yuyuetimes where id=$yuyue_time_type");
	$table_divs = array();
	$table_types = $db->get_results("select id,title,money from demo_shequ_tabletype where shequId=$shequId and status=1 order by id asc");
	$tables = $db->get_results("select * from demo_shequ_table where shequId=$shequId and status=1 and if_yuding=1");
	$yuyues = $db->get_results("select * from demo_shequ_yuyue where shequId=$shequId and yuyue_date='$yuyue_date' and yuyue_time_type=$yuyue_time_type and status>-1");
	$yuyue_array = array();
	if(!empty($yuyues)){
		foreach ($yuyues as $yuyue) {
			$yuyue_array[$yuyue->tableId] = $yuyue;
		}
	}
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = count($yuyues);
	$return['data'] = array();
	if(!empty($table_types)){
		foreach ($table_types as $val) {
			$div = array();
			$div['id'] = $val->id;
			$div['title'] = $val->title;
			$div['money'] = $val->money;
			$div['tables'] = array();
			foreach ($tables as $table) {
				if($table->type==$val->id){
					$table->if_yuyue = 0;
					if(!empty($yuyue_array[$table->id])){
						$table->if_yuyue = 1;
						$table->yuyue_info = $yuyue_array[$table->id];
						unset($yuyue_array[$table->id]);
					}
					$div['tables'][] = $table;
				}
			}
			$return['data'][] = $div;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function add(){
	global $db,$request;
	if($_SESSION['tijiao']==1){
		unset($_SESSION['tijiao']);
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$shequId = (int)$request['shequId'];
		$tableId = (int)$request['tableId'];
		$yuyue_date = $request['yuyue_date'];
		$yuyue_time_type = (int)$request['yuyue_time_type'];
		$ifhas = $db->get_var("select id from demo_shequ_yuyue where comId=$comId and shequId=$shequId and tableId=$tableId and yuyue_date='$yuyue_date' and $yuyue_time_type=$yuyue_time_type and status!=-1 limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"该餐桌已经被预约了，请预约其他餐桌"}';
			exit;
		}
		$yuyue = array();
		$yuyue['comId'] = $comId;
		$yuyue['shequId'] = $shequId;
		$yuyue['tableId'] = $tableId;
		$yuyue['yuyue_date'] = $yuyue_date;
		$yuyue['yuyue_time_type'] = $yuyue_time_type;
		$yuyue['yuyue_time'] = $request['yuyue_time'];
		$yuyue['arrive_time'] = $request['arrive_time'];
		$yuyue['userNum'] = $request['userNum'];
		$yuyue['table_type'] = $db->get_var("select type from demo_shequ_table where id=$tableId");
		$yuyue['table_title'] = $db->get_var("select title from demo_shequ_table where id=$tableId");
		$yuyue['uname'] = $request['uname'];
		$yuyue['uphone'] = $request['uphone'];
		$yuyue['money'] = $db->get_var("select money from demo_shequ_tabletype where id=".$yuyue['table_type']);
		$yuyue['userId'] = $userId;
		$yuyue['status'] = $yuyue['money']>0?0:1;
		$yuyue['dtTime'] = date("Y-m-d H:i:s");
		$id = $db->insert_update('demo_shequ_yuyue',$yuyue,'id');
		if($yuyue['money']>0){
			$timed_task = array();
			$timed_task['comId'] = $comId;
			$timed_task['dtTime'] = time()+900;//15分钟不支持就作废
			$timed_task['router'] = 'order_checkYuyue';
			$timed_task['params'] = '{"order_id":'.$id.'}';
			$db->insert_update('demo_timed_task',$timed_task,'id');
		}
		echo '{"code":1,"message":"预约成功","money":'.$yuyue['money'].',"id":'.$id.'}';
	}
	exit;
}
function pay(){}
function yue_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$zhifumm = $request['zhifumm'];
	$u = $db->get_row("select payPass,money from users where id=$userId");
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$shlencryption = new shlEncryption($zhifumm);
	if($u->payPass!=$shlencryption->to_string()){
		die('{"code":0,"message":"支付密码不正确"}');
	}
	$order = $db->get_row("select * from demo_shequ_yuyue where id=$orderId and userId=$userId");
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=0){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->money<$order->money){
		die('{"code":0,"message":"余额不足！请选择其他支付方式"}');
	}
	$db->query("update users set money=money-$order->money where id=$userId");
	$liushui = array();
	$liushui['userId']=$userId;
	$liushui['comId']=$comId;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=-$order->money;
	$liushui['yue']=$u->money-$order->money;
	$liushui['type']=1;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='预约支付';
	$liushui['orderInfo']='预约支付，预约餐桌：'.$order->table_title;
	$liushui['order_id']=0;
	insert_update('user_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	$o['status'] = 1;
	$pay_json = array();
	if(!empty($order->payed_json)){
		$pay_json = json_decode($order->payed_json,true);
	}
	$pay_json['yue']['price'] = $order->money;
	$pay_json['yue']['if_zong'] = $comId==10?1:0;//是否是总平台的余额,退款时要按这个字段来退款
	$o['payed_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	
	$db->insert_update('demo_shequ_yuyue',$o,'id');
	die('{"code":1,"message":"支付成功"}');
}
function weixin_pay(){
	if(is_weixin()){
		global $db,$request,$order;
		$orderId = (int)$request['order_id'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION['demo_comId'];
		$order = $db->get_row("select * from demo_shequ_yuyue where id=$orderId and userId=$userId");
		if(empty($order)){
			die('<script>alert("订单不存在");location.href="/index.php?p=8";</script>');
		}
		if($order->status!=0){
			die('<script>location.href="/index.php?p=8";</script>');
		}
		require('inc/pay/WxpayAPI_php_v3/example/jsapi_yuyue.php');
		exit;
	}
}
function is_weixin(){
	if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false){
		return true;
	}
	return false;
}
function qx_order(){
	global $db,$request;
	$orderId = (int)$request['orderId'];
	$comId = (int)$_SESSION['demo_comId'];
	if(!empty($request['comId'])){
		$comId = (int)$request['comId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select * from demo_shequ_yuyue where id=$orderId");
	if($order->status==0||$order->status==1){
		$db->query("update demo_shequ_yuyue set status=-1 where id=$orderId");
		if($order->money>0 && $order->order_id==0){
			tuikuan($order);
		}
	}else{
		die('{"code":0,"message":"订单当前状态不支持取消"}');
	}
	die('{"code":"1","message":"取消成功"}');
}

function tuikuan($order){
	global $db;
	$userId = $order->userId;
	$comId = $order->comId;
	$orderId = $order->id;
	$fenbiao = getFenbiao($comId,20);
	
	$pay_json = json_decode($order->payed_json,true);
	//余额支付
	if(!empty($pay_json['yue']['price'])){
		$money = $pay_json['yue']['price'];
		$db->query("update users set money=money+$money where id=".$order->userId);
		$yue = $db->get_var('select money from users where id='.$order->userId);
		$liushui = array();
		$liushui['userId']=$order->userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$money;
		$liushui['yue']=$yue;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='预约取消';
		$liushui['orderInfo']='餐位预约取消';
		$liushui['order_id']=0;
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
	}
	//微信支付返余额
	if(!empty($pay_json['weixin']['price'])){
		$money = $pay_json['weixin']['price'];
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 and status=1 limit 1");
		if(!empty($weixin_set->info)){
			$weixin_arr = json_decode($weixin_set->info);
		}
		if(!empty($weixin_arr->sslkey) && !empty($weixin_arr->sslcert)){
			define('WX_APPID',$weixin_arr->appid);
			define('WX_MCHID',$weixin_arr->mch_id);
			define('WX_KEY',$weixin_arr->key);
			define('WX_APPSECRET',$weixin_arr->appsecret);
			define('WX_SSLKEY',ABSPATH.$weixin_arr->sslkey);
			define('WX_SSLCERT',ABSPATH.$weixin_arr->sslcert);
			require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
			require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
			$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
			$log = Log::Init($logHandler, 15);
			$transaction_id = $pay_json['weixin']['desc'][0];
			$total_fee = $money*100;
			$refund_fee = $total_fee;
			$input = new WxPayRefund();
			$input->SetTransaction_id($transaction_id);
			$input->SetTotal_fee($total_fee);
			$input->SetRefund_fee($refund_fee);
			$input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
			$input->SetOp_user_id(WX_MCHID);
			//file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE));
			$result = WxPayApi::refund($input);
			if($result['result_code'] != "SUCCESS"){
				file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
			}
		}else{
			
			$db->query("update users set money=money+$money where id=$userId");
			$yue = $db->get_var('select money from users where id='.$userId);
			$liushui = array();
			$liushui['userId']=$userId;
			$liushui['comId']=$comId;
			$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
			$liushui['money']=$money;
			$liushui['yue']=$yue;
			$liushui['type']=2;
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']='预约取消';
			$liushui['orderInfo']='餐位预约取消';
			$liushui['order_id']=0;
			$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
		}		
	}
	//支付宝返余额
	if(!empty($pay_json['alipay']['price'])){
		$money = $pay_json['alipay']['price'];
		if($_SESSION['if_tongbu']==1){
			$db_service->query("update demo_user set money=money+$money where id=$userId");
			$yue = $db_service->get_var('select money from demo_user where id='.$userId);
		}else{
			$db->query("update users set money=money+$money where id=$userId");
			$yue = $db->get_var('select money from users where id='.$userId);
		}
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$money;
		$liushui['yue']=$yue;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='预约取消';
		$liushui['orderInfo']='餐位预约取消';
		$liushui['order_id']=0;
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
	}
	/*if(!empty($pay_json['yibao']['price'])){
		$money = $pay_json['yibao']['price'];
		$yibao_orderId = $pay_json['yibao']['desc'];
		$verify = md5(substr($yibao_orderId.$money,0,10));
		//是否已经分过账
		$fenzhang = $db->get_row("select id,payId,ledgerNo,ledgerName from demo_yibao_fenzhang where orderId=$order->id and income_type=1 limit 1");
		if(!empty($fenzhang) && $fenzhang->status==2){
			file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
			if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
				$money = $pay_json['dingjin']['price'];
				$yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
				$verify = md5(substr($yibao_orderId.$money,0,10));
				file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
			}
		}else{
			$db->query("update demo_yibao_fenzhang set status=-1 where orderId=$order->id and income_type=1 and status=1 limit 1");
			file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
			if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
				$money = $pay_json['dingjin']['price'];
				$yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
				$verify = md5(substr($yibao_orderId.$money,0,10));
				file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
			}
		}
	}*/
}
function get_order_list(){
	global $db,$request;
	$scene = (int)$request['scene'];
	$type = $request['type'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	
	$sql="select * from demo_shequ_yuyue where comId=$comId and userId=$userId ";
	if(!empty($scene)){
		switch($scene){
			case 1:
				$sql.=" and status=1";
			break;
			case 2:
				$sql.=" and status=1 and order_id=0";
			break;
			case 3:
				$sql.=" and status=1 and order_id>0";
			break;
		}
	}
	//file_put_contents('request.txt',$sql);
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	
	$sql.=" order by id desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$now = time();
	$now_str = strtotime(date("Y-m-d"));
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$table_type = $db->get_var("select title from demo_shequ_tabletype where id=$pdt->table_type");
			$data = array();
			$data['id'] = $pdt->id;
			$data['orderId'] = $pdt->comId.'_'.$pdt->id;
			$data['status'] = $pdt->status;
			$data['order_id'] = $pdt->order_id;
			switch ($pdt->status) {
				case 0:
					$data['statusInfo'] = '<span style="color:#cf2950;">待付款</span>';
				break;
				case 1:
					if($pdt->order_id>0){
						$data['statusInfo'] = '<span style="color:#cf2950;">已使用</span>';
					}else{
						$yuyue_str = strtotime($pdt->yuyue_date);
                        if($yuyue_str>=$now){
							$data['statusInfo'] = '<span style="color:#cf2950;">待使用</span>';
						}else{
							$data['statusInfo'] = '<span style="color:#cf2950;">已过期</span>';
							$data['status'] = -2;
						}

					}
				break;
				case -1:
					$data['statusInfo'] = '<span style="color:#cf2950;">无效</span>';
				break;
			}
			$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$data['yuyeTime'] = $pdt->yuyue_date.' '.$pdt->yuyue_time;
			$data['shequ_title'] = $db->get_var("select title from demo_shequ where id=$pdt->shequId");
			$data['table_id'] = $pdt->tableId;
			$data['table_title'] = $table_type.'-'.$pdt->table_title;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//微信相关的方法
function createNoncestr( $length = 32 ){
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}
function postXmlCurl($xml,$url,$second = 30){
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //设置 header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post 提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行 curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    }else{
        $error = curl_errno($ch);
        curl_close($ch);
        echo "curl 出错，错误码:$error"."<br>";
    }
}
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    $ip         =   'unknown';
    if ($ip !== 'unknown') return $ip[$type];
    if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实 IP
        $ip=$_SERVER['HTTP_X_REAL_IP'];
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的 ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的 ip 地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP 地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}