<?php
namespace Zhishang;

class Money{
    
    public function bind()
    {
        global $db,$request,$comId;

        $userId = (int)$request['user_id'];
        $fenbiao = getFenbiao($comId,20);
        $code = $request['code'];
        $pass = $request['pass'];
        $card = $db->get_row("select * from recharge_card where card_no = '$code' and card_pass = '$pass' ");
        if(!$card){
            return '{"code":0,"message":"未找到对应充值卡信息"}';
        }
        
        if($card->status != 0){
            return '{"code":0,"message":"当前充值卡不是待兑换状态，请核实"}';
        }
        
        if($card->userId != 0){
            return '{"code":0,"message":"当前充值卡券已经有归属用户'.$card->userId.'"}';
        }
        
        if($card->is_open != 1){
            return '{"code":0,"message":"当前储值卡处于未开通状态"}';
        }
        
        if(!$pass || $pass != $card->card_pass){
            return '{"code":0,"message":"密码不正确"}';
        }
       
        $cardData = array(
            'id' => $card->id,
            'userId' => $userId,
            'status' => 1,
            'bindTime' => date("Y-m-d H:i:s")
        );
  
        $db->insert_update('recharge_card', $cardData, 'id');
        
        //充值动作
        $total_fee = $card->money;
        $db->query("update users set money=money+$total_fee where id= $userId");
		
		$newCard = array(
		    'userId' => $userId,
		    'card_no' => $code,
		    'earn' => $total_fee,
		    'yue' => $total_fee,
		    'theme_color' => $card->theme_color,
		    'image' => $card->image,
		    'dtTime' => date('Y-m-d H:i:s'),
		    'updateTime' => date('Y-m-d H:i:s')
		);
		
		$db->insert_update("user_card", $newCard, "id");
		$cardId = $db->get_var("select last_insert_id();");
		
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=$total_fee;
		$liushui['yue']= $total_fee;
		$liushui['cardId'] = $cardId;
		$liushui['type']=2;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='充值卡';
		$liushui['orderInfo']='充值卡充值，充值卡单号：'.$order->card_no;
		$db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
        
        return json_encode(array("code"=>1,"message"=>"绑定成功" ),JSON_UNESCAPED_UNICODE);
    }
    
    public function storeCards()
    {
        global $db,$request,$comId;
        
		$userId = (int)$request['user_id'];
	    
		$cards = $db->get_results("select id,earn,yue,card_no,image,theme_color from user_card where userId = $userId  order by yue asc");
		
// 		$cards = $db->get_results("select id,earn,yue,card_no from user_card where 1=1  order by yue asc");
		
		$backs = $db->get_results("select id, title, originalPic from banner where channelId = 176  ");
    	
    	$banners = [];
		foreach ($backs as $k => $b){
		    $temp['img'] = $b->originalPic;
		    $temp['money'] = intval($b->title);
		    $banners[] = $temp;
		}

    	$columns = array_column($banners, 'money');
		array_multisort($columns,SORT_DESC,  $banners);

	    foreach ($cards as $k => $val){
	        if($val->image){
	            $cards[$k]->backimg = $val->image;
	            continue;
	        }
	        $cards[$k]->backimg = $banners[0]['img'];
	        foreach($banners as $bv){
	            if($val->earn > $bv['money']){
	                $cards[$k]->backimg = $bv['img'];
	                break;
	            }
	        }
	        
	    }
	    
	   // echo '<pre>';
	   // var_dump($cards);die;
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '获取成功';
		$return['data'] = $cards;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
	//绑定银行卡
	public function updateBankcard(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$name = trim($request['name']);
		$msn = trim($request['cardId']);
		$bank_name = trim($request['bank_name']);
		$bank_card = trim($request['bank_cardId']);
		$ifhas = (int)$db->get_var("select id from user_bank where userId=$userId and comId=$comId limit 1");
		if(empty($ifhas)){
			$db->query("insert into user_bank (name, msn, bank_name, bank_card, userId,comId) values ('$name', '$msn', '$bank_name', '$bank_card', $userId,$comId)");
		}else{
			$db->query("update user_bank set name='$name', msn='$msn', bank_name='$bank_name', bank_card='$bank_card' where id=$ifhas");
		}
		return '{"code":1,"message":"成功"}';
	}
	//获取银行卡信息
	public function getBankcrad(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$bank = $db->get_row("select * from user_bank where userId=$userId and comId=$comId limit 1");
		if(empty($bank)){
			return '{"code":0,"message":"请先添加银行卡"}';
		}
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['name'] = $bank->name;
		$return['data']['cardId'] = $bank->msn;
		$return['data']['bank_name'] = $bank->bank_name;
		$return['data']['bank_cardId'] = $bank->bank_card;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	//提现
	public function tixian1(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		if($comId==10){
			$db_service = get_zhishang_db();
		}
		$ifktx = $db->get_var("select id from user_tixian where userId=$userId and comId=$comId and status=0 limit 1");
		if(!empty($ifktx)){
			return '{"code":0,"message":"您的提现申请正在审核中，需要审核完成才能再次申请提现。"}';
		}
		$money = trim($request['money']);
		if($money<1 || $money>5000){
			return '{"code":0,"message":"提现金额必须大于1元，小于5000元！"}';
		}
		if($comId==10){
			$ktx = $db_service->get_var("select yongjin from demo_user where id=$userId limit 1");
		}else{
			$ktx = $db->get_var("select money from users where id=$userId limit 1");
		}
		if($ktx < $money || $money<=0){
			return '{"code":0,"message":"余额不足，无法提现！"}';
		}
		$ifhas = (int)$db->get_var("select id from user_bank where userId=$userId and comId=$comId limit 1");
		if(empty($ifhas)){
			return '{"code":0,"message":"请先绑定银行卡！"}';
		}
		//$db->query("insert into user_tixian (comId, userId, money, dtTime, status) values ($comId, $userId, '$money', '".date("Y-m-d H:i:s")."', 0)");
		if($comId==10){
			$db_service->query("update demo_user set yongjin=yongjin-$money where id=$userId");
		}else{
			$db->query("update users set money=money-$money where id=$userId");
		}
		$yue = $ktx-$money;
		$tixian = array();
		$tixian['comId'] = $comId;
		$tixian['money'] = $money;
		$tixian['dtTime'] = date("Y-m-d H:i:s");
		$tixian['userId'] = $userId;
		$tixian['yue'] = $yue;
		$db->insert_update('user_tixian',$tixian,'id');
		//$tixianId = mysql_insert_id();
		$yzFenbiao = $fenbiao = getFenbiao($comId,20);
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=-$money;
		$liushui['yue']=$db->get_var("select money from users where id=$userId");
		$liushui['type']=3;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='提现';
		$liushui['orderInfo']='';
		if($comId==10){
			$db->insert_update('user_yongjin10',$liushui,'id');
		}else{
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		return '{"code":1,"message":"提交成功，请等待审核。"}';
	}
	public function tixian(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
        $type = (int)$request['type']; // 0 默认提现银行卡 1提现到零钱
		if($comId==10){
			$db_service = get_zhishang_db();
		}
		$tixianjine = $db->get_var("select tixianjine from user_shezhi where comId=$comId");
		$ifktx = $db->get_var("select id from user_tixian where userId=$userId and comId=$comId and status=0 limit 1");
		if(!empty($ifktx)){
			return '{"code":0,"message":"您的提现申请正在审核中，需要审核完成才能再次申请提现。"}';
		}
		$money = trim($request['money']);

		if($money<1 || $money>$tixianjine){
			return '{"code":0,"message":"提现金额必须大于1元，小于'.$tixianjine.'元！"}';
		}
		
		$user = $db->get_row("select money,openid from users where id=$userId limit 1");
        $ktx = $user->money;
	

		if($ktx < $money || $money<=0){
			return '{"code":0,"message":"余额不足，无法提现！"}';
		}
		if(empty($type)){
            $ifhas = (int)$db->get_var("select id from user_bank where userId=$userId and comId=$comId limit 1");
            if(empty($ifhas)){
                return '{"code":0,"message":"请先绑定银行卡！"}';
            }
        }else{
            $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 limit 1");
            if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
                return '{"code":0,"message":"微信配置信息有误"}';
            }
            //$re_openid = 'oMHk85I2O3UoNBaGNrkw0lpocupY';
            $re_openid = $db->get_var("select unionid from users where id=$userId");
            $weixin_arr = json_decode($weixin_set->info);
		    //提现到零钱
            require_once("inc/wechat_paymentchange.class.php");
            // $appid = 'wx7c90438372c9aede1';
            // $mchid ='1592827191';
            // $secrectKey ='ABCDEFGHabcdefgh1234567887654321';
            
            $appid = $weixin_arr->appid;
            $mchid = $weixin_arr->mch_id;
            $secrectKey = $weixin_arr->key;
            
       
            $ip = $_SERVER['REMOTE_ADDR'];
            $wechat_paymentchange = new \wechat_paymentchange($appid,$mchid,$secrectKey,$ip);
            $res = $wechat_paymentchange->sendMoney($money,$re_openid,$desc='提现');
            //var_dump($res);die;
            if($res['result_code'] != 'SUCCESS'){ 
                return '{"code":0,"message":'.$res['return_msg'].$res['err_code_des'].'}';
            }
		}

		//$db->query("insert into user_tixian (comId, userId, money, dtTime, status) values ($comId, $userId, '$money', '".date("Y-m-d H:i:s")."', 0)");
		if($comId==10){
			$db_service->query("update demo_user set yongjin=yongjin-$money where id=$userId");
		}else{
			$db->query("update users set money=money-$money where id=$userId");
		}
		$yue = $ktx-$money;
		$tixian = array();
		$tixian['comId'] = $comId;
		$tixian['money'] = $money;
        $tixian['type'] = $type;
		$tixian['dtTime'] = date("Y-m-d H:i:s");
		$tixian['userId'] = $userId;
		$tixian['yue'] = $yue;
		$tixian['status'] = !empty($type) ? 1: 0;
		$tixian['shenheName'] = !empty($type) ? '微信提现': '';
		$db->insert_update('user_tixian',$tixian,'id');
		//$tixianId = mysql_insert_id();
		$yzFenbiao = $fenbiao = getFenbiao($comId,20);
		$liushui = array();
		$liushui['userId']=$userId;
		$liushui['comId']=$comId;
		$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
		$liushui['money']=-$money;
		$liushui['yue']=$db->get_var("select money from users where id=$userId");
		$liushui['type']=3; #提现
		$liushui['dtTime'] = date("Y-m-d H:i:s");
		$liushui['remark'] = '提现';
		$liushui['orderInfo']='';
		if($comId==10){
			$db->insert_update('user_yongjin10',$liushui,'id');
		}else{
			$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
		}
		
		return '{"code":1,"message":"提交成功，请等待审核。"}';
	}
	
	
	public function investActivity(){
		global $db,$request,$comId;
		$huodong = $db->get_row("select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1");
		
// 		echo "select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1";die;
// 		var_dump($huodong);die;
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		if(!empty($huodong)){
			$guizes = json_decode($huodong->guizes);
			$return['data']['type'] = $huodong->type;
			$return['data']['rules'] = array();
			switch ($huodong->type) {
				case 1:
					foreach ($guizes as $guize) {
						$rule = array();
						$rule['man'] = $guize->man;
						$rule['zeng'] = $guize->jian;
						$rule['jifen'] = 0;
						$rule['yhq'] = '';
						$return['data']['rules'][] = $rule;
					}
				break;
				case 2:
					foreach ($guizes as $guize) {
						$rule = array();
						$rule['man'] = $guize->man;
						$rule['zeng'] = 0;
						$rule['jifen'] = $guize->jian;
						$rule['yhq'] = '';
						$return['data']['rules'][] = $rule;
					}
				break;
				case 3:
					foreach ($guizes as $guize) {
						$yhqId = $guize->yhqId;
                        $yhq = $db->get_row("select title,man,money from yhq where id=$yhqId");
						$rule = array();
						$rule['man'] = $guize->man;
						$rule['zeng'] = 0;
						$rule['jifen'] = 0;
						$rule['yhq'] = $yhq->title.'(满'.$yhq->man.'减'.$yhq->money.') * '.$guize->jian;
						$return['data']['rules'][] = $rule;
					}
				break;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public  function checkScan()
	{
	    global $db,$request,$comId;
		
		$userId = (int)$request['user_id'];
		
		$orderId = $request['orderId'];
		
		$payLog = $db->get_row("select payNo, transaction_id, status, payTime, dtTime from pay_log where payNo = '$orderId' and type = 1 and userId = $userId ");
		if(!$payLog){
	        return '{"code":0,"message":"无效的订单编号！"}';
		}
		
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = $payLog;
	
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function scanChongzhi()
	{
	    global $db,$request,$comId;
		
		$money = trim($request['money']);
		$userId = (int)$request['user_id'];
		$type = (int)$request['type'];
		
		$type = !empty($type) ? $type : 3;  //默认小程序
// 		if($money<1 || $money>5000){
// 			return '{"code":0,"message":"充值金额必须大于1元，小于5000元！"}';
// 		}

        $notifyUrl = "http://".$_SERVER['HTTP_HOST']."/notify_chongzhi.php";
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
		
		require_once ABSPATH.'/inc/pay/WxpayAPI_php_v3/example/log.php';  
        require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");  
		require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php"); 
		require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
		
		if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置信息有误"}';
		}
		
		$weixin_arr = json_decode($weixin_set->info);
		define('WX_APPID',$weixin_arr->appid);
		define('WX_MCHID',$weixin_arr->mch_id);
		define('WX_KEY',$weixin_arr->key);
		define('WX_APPSECRET',$weixin_arr->appsecret);
		
		$orderId = date("YmdHis").rand(100000,999999);
		$chongzhi = array();
		$chongzhi['comId'] = $comId;
		$chongzhi['userId'] = $userId;
		$chongzhi['type'] = 2;
		$chongzhi['money'] = $money;
		$chongzhi['orderId'] = $orderId;
		$db->insert_update('user_chongzhi',$chongzhi,'id');
		
		//记录支付表日志 产生新的支付订单ID
		$chongzhiId = $db->get_var("select last_insert_id();");
		$payLog = array(
		    'userId' => $userId,
		    'type' => 1,
		    'source' => $type,
		    'typeId' => $chongzhiId,
		    'payNo' => $orderId,
		    'dtTime' => date('Y-m-d H:i:s') 
		);
		
		$db->insert_update("pay_log", $payLog, "id");
		$orderId = $payLog['payNo'];
		$subject = '微信余额充值';
		
		$money = bcmul($money, 100, 0);
		$dtTime = date("YmdHis");
		$expireTime = date("YmdHis", time() + 60*60*24);
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($subject);
		$input->SetAttach(1);
		$input->SetOut_trade_no($payLog['payNo']);
		$input->SetTotal_fee("$money");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url($notifyUrl);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($orderId);

		require_once "inc/pay/WxpayAPI_php_v3/example/WxPay.NativePay.php";	  
		$notify = new \NativePay();
		$result = $notify->GetPayUrl($input);
		
		if(isset($result['return_code']) && $result['return_code'] == 'FAIL'){
		    return '{"code":0,"message":"发起支付失败,原因：'.$result['return_msg'].'！"}';
		}

		$share_url = $result["code_url"];
		$share_file = 'cache/wx_scan/'.$orderId.date('YmdHis').'.png';
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		$return['data']['qrcode'] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$share_file;
		$return['data']['timeStamp'] = time();
		$return['data']['orderId'] = $payLog['payNo'];
	
		if(!is_file(ABSPATH.$share_file)){
		    require_once ABSPATH."/inc/pay/WxpayAPI_php_v3/example/phpqrcode/phpqrcode.php"; 
			\QRcode::png($share_url,$share_file,'L',8);
		}
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	
	
	
	public function chongzhi(){
		global $db,$request,$comId;
		
		$money = trim($request['money']);
		$userId = (int)$request['user_id'];
		$type = (int)$request['type'];
		
		$type = !empty($type) ? $type : 3;  //默认小程序
// 		if($money<1 || $money>5000){
// 			return '{"code":0,"message":"充值金额必须大于1元，小于5000元！"}';
// 		}
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=$type limit 1");
		if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置信息有误"}';
		}
		$orderId = date("YmdHis").rand(100000,999999);
		$chongzhi = array();
		$chongzhi['comId'] = $comId;
		$chongzhi['userId'] = $userId;
		$chongzhi['type'] = 2;
		$chongzhi['money'] = $money;
		$chongzhi['orderId'] = $orderId;
		$db->insert_update('user_chongzhi',$chongzhi,'id');
		
		//记录支付表日志 产生新的支付订单ID
		$chongzhiId = $db->get_var("select last_insert_id();");
		$payLog = array(
		    'userId' => $userId,
		    'type' => 1,
		    'source' => $type,
		    'typeId' => $chongzhiId,
		    'payNo' => "Y".date("YmdHis").rand(100000,999999),
		    'dtTime' => date('Y-m-d H:i:s') 
		);
		
		$db->insert_update("pay_log", $payLog, "id");
		$orderId = $payLog['payNo'];
		
		require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
		require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
		require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
		$weixin_arr = json_decode($weixin_set->info);
		define('WX_APPID',$weixin_arr->appid);
		define('WX_MCHID',$weixin_arr->mch_id);
		define('WX_KEY',$weixin_arr->key);
		define('WX_APPSECRET',$weixin_arr->appsecret);
		//初始化日志
		$logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
		$log = \Log::Init($logHandler, 15);

		//echo 3333;
		//①、获取用户openid
		$tools = new \JsApiPay();
		$field = 'openId';
        if($type == 3){
            $field = 'mini_openId';
        }

        $openId = $db->get_var("select $field from users where id=$userId");
        if(empty($openId)){
            return '{"code":0,"message":"获取不到会员的openId"}';
        }
		$body = '账号充值';
		$subject = '账号充值';
		$pay_price = round($money*100);
		$expireTime = date("YmdHis", time() + 60*60*24);

		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($subject);
		$input->SetAttach($type);//自定义数据
		$input->SetOut_trade_no($orderId);
		$input->SetTotal_fee($pay_price);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire($expireTime);
		$input->SetGoods_tag($subject);
		$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify_applet_chongzhi.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$orders = \WxPayApi::unifiedOrder($input);
		file_put_contents('wxpay.txt',json_encode($orders,JSON_UNESCAPED_UNICODE));
		if($orders['appid']==NULL){
			return '{"code":0,"message":"获取支付信息失败，请联系技术人员"}';
		}
		$resultData = json_decode($tools->GetJsApiParameters($orders));
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		$return['data']['appId'] = $resultData->appId;
		$return['data']['timeStamp'] = $resultData->timeStamp;
		$return['data']['nonceStr'] = $resultData->nonceStr;
		$return['data']['package'] = $resultData->package;
		$return['data']['signType'] = $resultData->signType;
		$return['data']['paySign'] = $resultData->paySign;
		$return['data']['orderId'] = $orderId;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function jifenPdts(){
		global $db,$request,$comId;
		$sql = "select a.inventoryId,a.jifen,b.title,b.key_vals,b.image from demo_jifenlist a left join demo_product_inventory b on a.inventoryId=b.id where a.comId=$comId and a.status=1";
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=20;
		$count = $db->get_var("select count(*) from demo_jifenlist where comId=$comId and status=1");
		$sql.=" order by a.inventoryId desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		$now = time();
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$data = array();
				$data['inventoryId'] = $pdt->inventoryId;
				$data['title'] = $pdt->title;
				$data['key_vals'] = $pdt->key_vals;
				$data['jifen'] = $pdt->jifen;
				$data['image'] = $pdt->image;
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function jifenOrder(){
		global $db,$request,$comId;
		$order_comId = $comId;
		$userId = (int)$request['user_id'];
		$inventoryId = (int)$request['inventoryId'];
		$address_id = (int)$request['address_id'];
		$num = (int)$request['num'];
		if($num<1){
			return '{"code":0,"message":"数量不正确！"}';
		}
		$jifen = $db->get_var("select jifen from demo_jifenlist where inventoryId=$inventoryId and status=1 limit 1");
		if(empty($jifen)){
			return '{"code":0,"message":"商品已下架！"}';
		}
		$jifen = $jifen*$num;
		$u = $db->get_row("select jifen from users where id=$userId");
		if($u->jifen<$jifen){
			return '{"code":0,"message":"积分不足！"}';
		}

		$inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=$inventoryId");
		$pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $inventory->productId;
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['weight'] = $inventory->weight;
        $pdt['num'] = $num;
        $pdt['jifen'] = $jifen;
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $inventory->price_sale;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);

        //$pdt['price_card'] = $inventory->price_card;
        $units = $db->get_var("select untis from demo_product where id=$inventory->productId");
        $units_arr = json_decode($units);
        $pdt['unit'] = $units_arr[0]->title;
        $product_json_arry[] = $pdt;
        $address = $db->get_row("select * from user_address where id=$address_id");
        if(empty($address)){
        	return '{"code":0,"message":"收货地址不存在！"}';
        }
		$areaId = (int)$address->areaId;
		$storeId = \Zhishang\Product::get_fahuo_store($areaId,$order_comId);
		$shouhuo_json = array();
		if(!empty($address)){
			$shouhuo_json['收件人'] = $address->name;
			$shouhuo_json['手机号'] = $address->phone;
			$shouhuo_json['所在地区'] = $address->areaName;
			$shouhuo_json['详细地址'] = $address->address;
		}
		$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
		$order = array();
		$order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
		$order['userId'] = $userId;
		$order['comId'] = (int)$order_comId;
		$order['mendianId'] = 0;
		$order['yushouId'] = 0;
		$order['type'] = 1; //2社区团 1普通订单或普通团单
		$order['status'] = 2;//待支付
		$pay_json = array();
		$pay_json['jifen']['price'] = 0;
		$pay_json['jifen']['desc'] = $jifen;
		$order['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
		$order['dtTime'] = date("Y-m-d H:i:s");
		$order['remark'] = '';
		$order['pay_endtime'] = date("Y-m-d H:i:s");
		$order['price'] = 0;
		$order['inventoryId'] = (int)$inventory->id;
		$order['storeId'] = $storeId;
		$order['pdtNums'] = $num;
		$order['pdtChanel'] = 0;
		$order['ifkaipiao'] = 0;
		$order['weight'] = 0;
		$order['jifen'] = 0;
		$order['areaId'] = $areaId;
		$order['address_id'] = $address_id;
		$order['product_json'] = $product_json;
		$order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
		$order['price_json'] = '';
		$order['fanli_json'] = '';
		$order['ifkaipiao'] = 0;
		$order['if_zong'] = 0;
		$order['if_jifen'] = 1;
		$order['ispay'] = 1;
		$order['shequ_id'] = $shequ_id = (int)$db->get_var("select shequ_id from users where id=$userId");
		$order['peisong_type'] = 0;
		$order['peisong_time'] = '';
		$order['tuan_id'] = 0;
		$order_fenbiao = getFenbiao($order_comId,20);
		//file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
		$db->insert_update('order'.$order_fenbiao,$order,'id');
		$order_id = $db->get_var("select last_insert_id();");
		/*if(!empty($xiangou_sql1)){
			$xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
	    	$xiangou_sql1 = substr($xiangou_sql1,1);
	    	$db->query($xiangou_sql.$xiangou_sql1);
	    }
		$timed_task = array();
		$timed_task['comId'] = (int)$_SESSION['demo_comId'];
		$timed_task['dtTime'] = $check_pay_time;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$order_id.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');*/
		/*if(!empty($yhq_id)){
			$db->query("update user_yhq$fenbiao set status=1,orderId=$order_id where id=$yhq_id");
		}*/
		foreach ($product_json_arry as $detail) {
			$pdt = new \StdClass();
			$pdt->sn = $detail['sn'];
			$pdt->title = $detail['title'];
			$pdt->key_vals = $detail['key_vals'];
			$order_detail = array();
			$order_detail['comId'] = (int)$order_comId;
			$order_detail['mendianId'] = 0;
			$order_detail['userId'] = $userId;
			$order_detail['orderId'] = $order_id;
			$order_detail['inventoryId'] = $detail['id'];
			$order_detail['productId'] = $detail['productId'];
			$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
			$order_detail['num'] = $detail['num'];
			$order_detail['unit'] = $detail['unit'];
			$order_detail['unit_price'] = $detail['price_sale'];
			$order_detail['status'] = 1;
			$db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
			if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
			if($tuan_type==0){
				$db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
			}
			$db->query("update demo_product_inventory set orders=orders+$num where id=".$detail['id']);
			$db->query("update demo_product set orders=orders+$num where id=".$detail['productId']);
		}
		//发货
		$fahuo = array();
		$fahuo['comId'] = $comId;
		$fahuo['mendianId'] = 0;
		$fahuo['addressId'] = $address_id;
		$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$fahuo['orderIds'] = $order_id;
		$fahuo['type'] = 1;
		$fahuo['showTime'] = date("Y-m-d H:i:s");
		$fahuo['storeId'] = $storeId;
		$fahuo['dtTime'] = date("Y-m-d H:i:s");
		$fahuo['shuohuo_json'] = $order['shuohuo_json'];
		$fahuo['productId'] = 0;
		$fahuo['tuanzhang'] = $userId;
		$fahuo['product_title'] = $inventory->title;
		$fahuo['fahuo_title'] = $inventory->title;
		$fahuo['product_num'] = $num;
		$fahuo['weight'] = 0;
		$fahuo['areaId'] = $areaId;
		$fahuo['shequ_id'] = $shequ_id;
		$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$order_id");

		//减积分
		$db->query("update users set jifen=jifen-$jifen where id=$userId");
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = -$jifen;
		$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
		$jifen_jilu['type'] = 2;
		$jifen_jilu['oid'] = $order_id;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '订单支付，订单号：'.$order['orderId'];
		//$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
		$db->insert_update('user_jifen'.$order_fenbiao,$jifen_jilu,'id');
		return '{"code":1,"message":"兑换成功","order_id":'.$order_id.'}';
	}
}