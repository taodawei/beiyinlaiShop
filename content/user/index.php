<?php
global $request;
$no_login = array('login','login1','reg','wxlogin','wxlogin_com','bindwx','sendSms','findMima','sendSms1','sendSms2','send_login_msg','msg_login','reg_nobind','reg_yaoqing');
if( !in_array($request['a'], $no_login) && empty($_SESSION[TB_PREFIX.'user_ID'])){
	$url = urlencode('/index.php?'.$_SERVER["QUERY_STRING"]);
	redirect('/index.php?p=8&a=login&url='.$url);
}
function index(){}
function yongjin(){}
function shoucang(){}
function bing_phone(){
	global $db,$request;
	if($request['tijiao']==1){
		$username = $request['username'];
		$password = $request['password'];
		$comId = (int)$_SESSION['demo_comId'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$yzm = $request['yzm'];
		if($yzm!=$_SESSION['yzm']||empty($yzm)){
			echo '{"code":0,"message":"验证码错误,请重新输入"}';
			exit;
		}
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new shlEncryption($password);
		$password = $shlencryption->to_string();
		if($comId==10){
			$db_service = getCrmDb();
			$db_service->query("update demo_user set username='$username',pwd='$password' where id=$userId");
		}else{
			$db->query("update users set username='$username',password='$password' where id=$userId");
		}
		die('{"code":1,"message":"绑定成功"}');
	}
}
function jifen(){}
function jifen_rule(){}
function qdjf(){}
function shoucang1(){}
function history(){}
function msg(){}
function msglist(){}
function erweima(){}
function yaoqing(){}
function friends(){}
function reg_yaoqing(){}
function xinrenfuli(){}
function get_friends(){
	global $db;
	$db_service = getCrmDb();
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	$sql = "select id,username,dtTime from demo_user where shangji=$userId order by id desc";
	$res = $db_service->get_results($sql." limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db_service->get_var(str_replace('id,username,dtTime','count(*)',$sql));
    $lipinkaId = $db->get_var("select id from gift_card10 where userId=$userId and (endTime is NULL or endTime='0000-00-00') order by id asc limit 1");
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	$key->username = substr($key->username,0,3).'****'.substr($key->username,-4);
      	$key->dtTime = date("Y-m-d",strtotime($key->dtTime));
      	$key->dikoujin = (int)$db->get_row("select money from gift_card_liushui10 where cardId=$lipinkaId and userId=$key->id");
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_msg_list(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$sql = "select * from user_msg$fenbiao where userId=$userId and comId=$comId order by id desc ";
    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db->get_var(str_replace('*','count(id)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	$key->url = '';
      	if($key->type==1&&!empty($key->infoId)){
      		$key->url = '/index.php?p=19&a=view&id='.$key->infoId;
      	}else if($key->type==2&&!empty($key->infoId)){
      		$key->url = '/index.php?p=21&a=view&id='.$key->infoId;
      	}
      	$key->dtTime = date("Y-m-d H:i",strtotime($key->dtTime));
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function read_msg(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$db->query("update user_msg$fenbiao set ifread=1 where id=$id");
	die('{"code":1}');
}
function yhq(){}
function get_myyhq_list(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	$scene = (int)$request['scene'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	$sql = "select * from user_yhq$fenbiao where comId=$comId and userId=$userId ";
	switch ($scene){
		case 1:
			$sql.=" and status=0 and endTime>'".date("Y-m-d H:i:s")."'";
		break;
		case 2:
			$sql.=" and status=1";
		break;
		case 3:
			$sql.=" and status=0 and endTime<'".date("Y-m-d H:i:s")."'";
		break;
	}
	$sql.=' order by id desc ';
    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db->get_var(str_replace('*','count(id)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
    	$now = time();
	    foreach ($res as $key) {
	      	$tiaojian = '通用';
	      	$yhq=$db->get_row("select mendianIds,channelNames,pdtNames,color,useType from yhq where id=$key->jiluId");
	      	if(!empty($yhq->mendianIds)){
	      		$shop_name = $db->get_var("select com_title from demo_shezhi where comId=$yhq->mendianIds");
	      		$tiaojian = '仅限购买'.$shop_name.'指定商品购买';
	      	}elseif($yhq->useType>1){
				$tiaojian = '仅限'.$yhq->channelNames;
				if(!empty($yhq->pdtNames)){
					$tiaojian.=empty($tiaojian)?$yhq->pdtNames:','.$yhq->pdtNames;
				}
			}
			$key->tiaojian = sys_substr($tiaojian,30,true);
			$key->image = '';
			if($key->status==0){
				$endTime = strtotime($key->endTime);
				if($now>$endTime){
					$key->status = 2;
				}else{
					if($endTime-$now<259200){
						$key->image = 'a928_1';
					}else if(date("Y-m-d",strtotime($key->dtTime))==date("Y-m-d")){
						$key->image = 'a928_11';
					}
				}
			}
	      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
	      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
	      	$key->man = floatval($key->man);
	      	$key->jian = floatval($key->jian);
	      	$key->color = $yhq->color;
	      	$return['data'][] = $key;
	    }
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_yhq_list(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$areaId = (int)$_SESSION[TB_PREFIX.'sale_area'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	$sql = "select * from yhq where comId=$comId and endTime>'".date("Y-m-d H:i:s")."' and num>hasnum and status=1 and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds)) ";
	if($comId!=10){
		$sql.=" and mendianIds='' ";
	}
	$res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
	$count = $db->get_var(str_replace('*','count(*)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$today = date("Y-m-d");
    if($res){
      foreach ($res as $key) {
      	if($key->num_day>0){
      		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$key->id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
      		if($hasNum>=$key->num_day)continue;
      	}
      	$tiaojian = '通用';
		if(!empty($key->mendianIds)){
      		$shop_name = $db->get_var("select com_title from demo_shezhi where comId=$key->mendianIds");
      		$tiaojian = '仅限购买'.$shop_name.'指定商品购买';
      	}elseif(!empty($key->channels) || !empty($key->pdts)){
			$tiaojian = '仅限'.$key->channelNames;
			if(!empty($key->pdtNames)){
				$tiaojian.=empty($tiaojian)?$key->pdtNames:','.$key->pdtNames;
			}
		}
		$key->tiaojian = sys_substr($tiaojian,22,true);
      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
      	$key->man = floatval($key->man);
	    $key->money = floatval($key->money);
	    $lingqu_num = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id");
	    if($lingqu_num>0){
	    	$key->lingqu_id = $db->get_var("select id from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id limit 1");
	    }
	    $key->if_lingqu = $lingqu_num>0?1:0;
	    $key->if_ke_lingqu = $lingqu_num<$key->numlimit?1:0;
	    $key->width = intval($key->hasnum*10000/$key->num)/100;
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function yhqList(){}
function yhq_lingqu(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$yhq = $db->get_row("select * from yhq where id=$id and comId=$comId and status=1");
	if(empty($yhq)){
		die('{"code":0,"message":"优惠券已过期不存在"}');
	}
	if($yhq->hasNum>=$yhq->num){
		die('{"code":0,"message":"优惠券已被抢光了"}');
	}
	if($yhq->num_day>0){
		$today = date("Y-m-d");
  		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
  		if($hasNum>=$yhq->num_day){
  			die('{"code":0,"message":"今日领取已达上限，请明天再领"}');
  		}
  	}
  	if($yhq->numlimit>0){
  		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$id");
  		if($hasNum>=$yhq->numlimit){
  			die('{"code":0,"message":"您已经领过该券了~~"}');
  		}
  	}
  	$user_yhq = array();
  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
  	$user_yhq['comId'] = $comId;
  	$user_yhq['userId'] = $userId;
  	$user_yhq['jiluId'] = $id;
  	$user_yhq['fafangId'] = 0;
  	$user_yhq['title'] = $yhq->title;
  	$user_yhq['man'] = $yhq->man;
  	$user_yhq['jian'] = $yhq->money;
  	$user_yhq['startTime'] = $yhq->startTime;
  	$user_yhq['endTime'] = $yhq->endTime;
  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
  	$db->insert_update('user_yhq'.$fenbiao,$user_yhq,'id');
  	$yhq_id = $db->get_var("select last_insert_id();");
  	$db->query("update yhq set hasnum=hasnum+1 where id=$id");
  	die('{"code":1,"message":"领取成功","yhq_id":'.$yhq_id.'}');
}
function bangding(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$u = $db->get_var("select openId from users where id=$userId");
	if(empty($openId)){
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
		if(empty($weixin_set)||empty($weixin_set->info)){
			die('微信配置信息有误，请从后台支付设置处设置');
		}
		$weixin_arr = json_decode($weixin_set->info);
		$appid = $weixin_arr->appid;
		$appsecret = $weixin_arr->appsecret;
		$scope = 'snsapi_userinfo';
		$code = $_REQUEST["code"];
	  	if(empty($code)){
	    	$url = "http://". $_SERVER['HTTP_HOST']."/index.php?p=8&a=bangding&urltob=".$request['urltob'];
	    	$baseUrl = urlencode($url);
	    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/get-weixin-code.html?appid='.$appid.'&scope='.$scope.'&state=STATE&redirect_uri='.$baseUrl;
	    	Header("Location: $url");
	  	}else{
	  		$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
	  		$token_info = https_request($token_url);
	  		$access_token = $token_info['access_token']; 
	  		$openid = $token_info['openid'];
	  		$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN"; 
	  		$user_info = https_request($userinfo_url);
	  		if(empty($user_info)){
	  			print_r($user_info);
	  			die("微信获取授权失败！请返回重试");
	  		}
	  		$db->query("update users set openId='$openid',unionid='".$user_info['unionid']."' where id=$userId");
	  		add_user_oprate('绑定微信',2);
	  		echo '<script>alert("微信绑定成功");location.href="/";</script>';
	  	}
	}
	exit;
}
//非同步商家微信登录
function wxlogin_com(){
	if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/index.php');
	}
	global $db, $request;
	$comId = (int)$_SESSION['demo_comId'];
	if(!empty($request['urltob'])){
		$request['urltob'] = urlencode($request['urltob']);
	}
	$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
	if(empty($weixin_set)||empty($weixin_set->info)){
		die('微信配置信息有误，请从后台支付设置处设置');
	}
	$weixin_arr = json_decode($weixin_set->info);
	$appid = $weixin_arr->appid;
	$appsecret = $weixin_arr->appsecret;
	$scope = 'snsapi_userinfo';
	$code = $_REQUEST["code"];
	
  	if(empty($code)){
    	$url = "http://". $_SERVER['HTTP_HOST']."/index.php?p=8&a=wxlogin_com&urltob=".$request['urltob']."&return=".$request['return'];
    	$baseUrl = urlencode($url);
    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/get-weixin-code.html?appid='.$appid.'&scope='.$scope.'&state=STATE&redirect_uri='.$baseUrl;
    	Header("Location: $url");
  	}else{
  		$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
  		$token_info = https_request($token_url);
  		$access_token = $token_info['access_token']; 
  		$openid = $token_info['openid'];
  		$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN"; 
  		$user_info = https_request($userinfo_url);
  		if(empty($user_info)||empty($openid)){
  			print_r($user_info);
  			die("微信获取授权失败！请返回重试");
  		}
  		$user = $db->get_row("select * FROM users WHERE openid='".$user_info['openid']."' and comId=$comId order by id asc LIMIT 1");
  		$dtTime = time();
  		$weixin_name = addslashes(filtergl($user_info['nickname']));
  		$weixin_image = $user_info['headimgurl'];
  		if(!empty($user)){
  			if($user->status!=1){
				echo '<script>alert("您的账号尚未通过审核，请联系管理员。");location.href="/index.php";</script>';
				exit;
			}
			$update_sql = "update users set lastlogin='".date("Y-m-d H:i:s")."',nickname='$weixin_name'";
			if(empty($user->unionId)){
				$update_sql .= ",unionid='".$user_info['unionid']."'";
			}
			$update_sql .= " where id=$user->id";
			$db->query($update_sql);
			$userId = $user->id;
			$_SESSION[TB_PREFIX.'user_name'] = $user->nickname;
			$_SESSION[TB_PREFIX.'user_level'] = $user->level;
			$_SESSION[TB_PREFIX.'user_ID'] = $userId;
			$_SESSION[TB_PREFIX.'zhishangId'] = $user->zhishangId;
			$_SESSION['if_shequ_tuan'] = $user->if_shequ_tuan;
			$shouhuo = $db->get_row("select id,areaId from user_address where userId=$user->id and comId=$comId order by moren desc limit 1");
			if($user->shequ_id>0){
				$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=$user->shequ_id");
				$_SESSION[TB_PREFIX.'shequ_id'] = $user->shequ_id;
				$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
				$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
				$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
			}else{
				$shouhuo = $db->get_row("select id,areaId from user_address where userId=$user->id and comId=$comId order by moren desc limit 1");
				$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
				$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
			}
			//$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
			//$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
			$cookieTime =60*60*24*30;
			setcookie('dt_username_'.$comId,$user->username,time()+$cookieTime, '/');
			setcookie('dt_pwd_'.$comId,$user->password,time()+$cookieTime, '/');

			add_user_oprate('微信登录',1);
  		}else{
  			$_SESSION['openid'] = $openid;
  			$_SESSION['unionid'] = $user_info['unionid'];
  			$_SESSION['weixin_name'] = $weixin_name;
  			$_SESSION['weixin_image'] = $weixin_image;
  			if($request['return']=='reg_yaoqing'){
  				redirect('/index.php?p=8&a=reg_yaoqing');
  			}else{
  				if($comId==1041 || $comId==1113 || $comId==1135){
  					redirect('/index.php?p=8&a=reg_nobind&url='.$request['urltob']);
  				}else{
  					redirect('/index.php?p=8&a=bindwx&url='.$request['urltob']);
  				}  				
  			}
  		}
  		if(!empty($request['urltob'])){
  			redirect(urldecode($request['urltob']));
  		}else{
  			redirect("/index.php");
  		}
	}
	exit;
}
//知商以及同步商家微信登录
function wxlogin(){
	if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/index.php');
	}
	global $db, $request;
	if(!empty($request['urltob'])){
		$request['urltob'] = urlencode($request['urltob']);
	}
	$appid = 'wx7a91a4f2eccb30db';
	$appsecret = '368a5e47cb481c6aebfe0376ef71a463';
	$scope = 'snsapi_userinfo';
	$code = $_REQUEST["code"];
	$comId = (int)$_SESSION['demo_comId'];
  	if(empty($code)){
    	$url = "http://". $_SERVER['HTTP_HOST']."/index.php?p=8&a=wxlogin&urltob=".$request['urltob']."&return=".$request['return'];
    	$baseUrl = urlencode($url);
    	$url = 'https://www.zhishangez.com/get-weixin-code.html?appid='.$appid.'&scope='.$scope.'&state=STATE&redirect_uri='.$baseUrl;
    	Header("Location: $url");
  	}else{
  		$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
  		$token_info = https_request($token_url);
  		$access_token = $token_info['access_token']; 
  		$openid = $token_info['openid'];
  		$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN"; 
  		$user_info = https_request($userinfo_url);
  		if(empty($user_info)||empty($openid)){
  			print_r($user_info);
  			die("微信获取授权失败！请返回重试");
  		}
  		//print_r($user_info);exit;
  		$db_service = getCrmDb();
  		$user = $db_service->get_row("select * FROM demo_user WHERE unionid='".$user_info['unionid']."' order by id asc LIMIT 1");
  		$dtTime = time();
  		$weixin_name = addslashes(filtergl($user_info['nickname']));
  		if(!empty($user)){
			$update_sql = "update demo_user set lastlogin='$dtTime',weixin_name='$weixin_name',openid='$openid'";
			if(empty($user->unionId)){
				$update_sql .= ",unionid='".$user_info['unionid']."'";
			}
			$update_sql .= " where id=$user->id";
			$db_service->query($update_sql);
			if($comId>10&&$comId!=1009){
				$userId = $db->get_var("select id from users where zhishangId=$user->id and comId=$comId limit 1");
				if(empty($userId)){
					$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
					$db->query("insert into users(comId,nickname,username,weixin_name,password,areaId,city,level,dtTime,status,image) value($comId,'$user->name','$user->username','$weixin_name','$user->pwd',0,0,$level,'".date("Y-m-d H:i:s")."',1,'".$user_info['headimgurl']."')");
					$userId = $db->get_var("select last_insert_id();");
				}
			}else{
				 $userId = $user->id;
			}

			$_SESSION[TB_PREFIX.'user_name'] = $user->name;
			$_SESSION[TB_PREFIX.'user_level'] = $user->level;
			$_SESSION[TB_PREFIX.'user_ID'] = $userId;
			$_SESSION[TB_PREFIX.'zhishangId'] = $user->id;
			$_SESSION['if_shequ_tuan'] = $user->if_shequ_tuan;
			$shouhuo = $db->get_row("select id,areaId from user_address where userId=$user->id and comId=$comId order by moren desc limit 1");
			$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
			$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
			$cookieTime =60*60*24*30;
			setcookie('dt_username_'.$comId,$user->username,time()+$cookieTime, '/');
			setcookie('dt_pwd_'.$comId,$user->pwd,time()+$cookieTime, '/');
			add_user_oprate('微信登录',1);
  		}else{
  			$_SESSION['openid'] = $openid;
  			$_SESSION['unionid'] = $user_info['unionid'];
  			$_SESSION['weixin_name'] = $weixin_name;
  			if($request['return']=='reg_yaoqing'){
  				redirect('/index.php?p=8&a=reg_yaoqing');
  			}else{
  				redirect('/index.php?p=8&a=bindwx&url='.$request['urltob']);
  			}
  		}
  		if(!empty($request['urltob'])){
  			redirect(urldecode($request['urltob']));
  		}else{
  			redirect("/index.php");
  		}
	}
	exit;
}
//绑定微信
function bind_weixin(){
	if(empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/');
	}
	global $db, $request;
	if(!empty($request['urltob'])){
		$request['urltob'] = urlencode($request['urltob']);
	}
	$appid = 'wx7a91a4f2eccb30db';
	$appsecret = '368a5e47cb481c6aebfe0376ef71a463';
	$scope = 'snsapi_userinfo';
	$code = $_REQUEST["code"];
	$comId = (int)$_SESSION['demo_comId'];
  	if(empty($code)){
    	$url = "http://". $_SERVER['HTTP_HOST']."/index.php?p=8&a=bind_weixin&urltob=".$request['urltob'];
    	$baseUrl = urlencode($url);
    	$url = 'https://www.zhishangez.com/get-weixin-code.html?appid='.$appid.'&scope='.$scope.'&state=STATE&redirect_uri='.$baseUrl;
    	Header("Location: $url");
  	}else{
  		$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
  		$token_info = https_request($token_url);
  		$access_token = $token_info['access_token'];
  		$openid = $token_info['openid'];
  		$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN"; 
  		$user_info = https_request($userinfo_url);
  		if(empty($user_info)||empty($openid)){
  			die("微信获取授权失败！请返回重试");
  		}
  		//print_r($user_info);exit;
  		$db_service = getCrmDb();
		$update_sql = "update demo_user set openid='$openid',unionid='".$user_info['unionid']."',weixin_name='".$user_info['nickname']."' where id=".$_SESSION[TB_PREFIX.'zhishangId'];
		$db_service->query($update_sql);
		add_user_oprate('绑定微信',2);
  		if(!empty($request['urltob'])){
  			redirect(urldecode($request['urltob']));
  		}else{
  			redirect("/index.php");
  		}
	}
	exit;
}
function login1(){
	if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/index.php');
	}
}
function bindwx(){
	global $db,$request;
	if(empty($_SESSION['openid'])){
		redirect('/index.php?p=8&a=login');
	}
	if($request['tijiao']==1){
		$username = $request['username'];
		$password = rand(111111,999999);
		$yzm = $request['yzm'];
		$areaId = 0;
		$city = 0;
		if($yzm!=$_SESSION['yzm']||empty($yzm)){
			echo '{"code":0,"message":"验证码错误,请重新输入"}';
			exit;
		}

		$openid = $_SESSION['openid'];
  		$unionid = $_SESSION['unionid'];
  		$weixin_name = $_SESSION['weixin_name'];
  		$weixin_image = $_SESSION['weixin_image'];
  		$comId = (int)$_SESSION['demo_comId'];
  		if(empty($openid)){
  			echo '{"code":0,"message":"未检测到微信登录信息，请选择普通注册"}';
			exit;
  		}
  		if($comId==10){
			$db_service = getCrmDb();
			$sql="SELECT id FROM demo_user WHERE username='".$username."' limit 1";
			$isUser = (int)$db_service->get_var($sql);
			$level = (int)$db->get_var("select id from user_level where comId=10 order by id asc limit 1");
			if($isUser>0){
				$db_service->query("update demo_user set openid='$openid',unionid='$unionid',level=$level where id=$isUser");
				$ifhas = $db->get_var("select id from user_yhq10 where comId=10 and userId=$isUser and jiluId=8 limit 1");
				if(empty($ifhas)){
					zhuce_jiangli($isUser);
				}
				$user = $db_service->get_row("select * FROM demo_user WHERE id=$isUser order by id asc LIMIT 1");
				$_SESSION[TB_PREFIX.'user_name'] = $user->username;
				$_SESSION[TB_PREFIX.'user_level'] = $user->level;
				$_SESSION[TB_PREFIX.'user_ID'] = $user->id;
				$_SESSION[TB_PREFIX.'zhishangId'] = $user->id;
				$_SESSION['if_shequ_tuan'] = $user->if_shequ_tuan;
				$db_service->query("update demo_user set lastlogin='".time()."' where id=$isUser");
				$cookieTime =60*60*24*30;
				setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
				setcookie('dt_pwd_'.$comId,$user->pwd,time()+$cookieTime, '/');
				add_user_oprate('绑定微信',2);
				echo '{"code":1,"message":"绑定成功"}';
			}else{
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
				$user['name'] = $weixin_name;
				$user['mtel'] = $username;
				$user['phone'] = $username;
				$user['openid'] = $openid;
				$user['unionid'] = $unionid;
				$user['weixin_name'] = $weixin_name;
				$user['level'] = $level;
				if(!empty($_SESSION['tuijianren'])){
					$user['shangji'] = (int)$_SESSION['tuijianren'];
					$user['shangshangji'] = (int)$db_service->get_var("select shangji from demo_user where id=".$user['shangji']);
				}
				$db_service->insert_update('demo_user',$user,'id');
				$userId = $db_service->get_var("select last_insert_id();");
				if(!empty($_SESSION['tuijianren'])){
					$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
					$yaoqing_rules = json_decode($yaoqing_rule);
					//奖励抵扣金
					add_linpinka_money($user['shangji'], $yaoqing_rules->y_dikoujin,'邀请奖励','邀请奖励',0,$userId);
					if($yaoqing_rules->yhqId>0){
						//奖励优惠券
						$yhq = $db->get_row("select * from yhq where id=".$yaoqing_rules->yhqId." and comId=10 and status=1");
						if(!empty($yhq)){
							$user_yhq = array();
						  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
						  	$user_yhq['comId'] = 10;
						  	$user_yhq['userId'] = $user['shangji'];
						  	$user_yhq['jiluId'] = $yaoqing_rules->yhqId;
						  	$user_yhq['fafangId'] = 0;
						  	$user_yhq['title'] = $yhq->title;
						  	$user_yhq['man'] = $yhq->man;
						  	$user_yhq['jian'] = $yhq->money;
						  	$user_yhq['startTime'] = $yhq->startTime;
						  	$user_yhq['endTime'] = $yhq->endTime;
						  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
						  	$db->insert_update('user_yhq10',$user_yhq,'id');
						  	$db->query("update yhq set hasnum=hasnum+1 where id=".$yaoqing_rules->yhqId);
						}
					}
					$ifhas = $db->get_var("select userId from users_yaoqing where userId=".$user['shangji']);
					if(empty($ifhas)){
						$db->query("insert into users_yaoqing(userId,nums,dikoujin) value(".$user['shangji'].",1,".(int)$yaoqing_rules->y_dikoujin.")");
					}else{
						$db->query("update users_yaoqing set nums=nums+1,dikoujin=dikoujin+".(int)$yaoqing_rules->y_dikoujin." where userId=".$user['shangji']);
					}
				}
				//注册奖励
				zhuce_jiangli($userId);
				$_SESSION[TB_PREFIX.'user_name'] = $username;
				$_SESSION[TB_PREFIX.'user_level'] = 0;
				$_SESSION[TB_PREFIX.'user_ID'] = $userId;
				$_SESSION[TB_PREFIX.'zhishangId'] = $userId;
				$cookieTime =60*60*24*30;
				setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
				setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
				add_user_oprate('绑定微信',2);
				echo '{"code":1,"message":"绑定成功"}';
			}
		}else{
			$sql="SELECT * FROM users WHERE comId=$comId and username='".$username."' limit 1";
			$isUser = $db->get_row($sql);
			if(!empty($isUser)){
				if($_SESSION['if_tongbu']==1){
					$db_service = getCrmDb();
					$db_service->query("update demo_user set openid='$openid',unionid='$unionid' where id=$isUser->zhishangId");
					//zhuce_jiangli($isUser->zhishangId);
				}else{
					$db->query("update users set openid='$openid' where id=$isUser->id");
				}
				if($isUser->status!=1){
					echo '{"code":0,"message":"您的账号尚未通过审核，请联系管理员。"}';
					exit;
				}
				$_SESSION[TB_PREFIX.'user_name'] = $isUser->username;
				$_SESSION[TB_PREFIX.'user_level'] = $isUser->level;
				$_SESSION[TB_PREFIX.'user_ID'] = $isUser->id;
				$_SESSION[TB_PREFIX.'zhishangId'] = $isUser->zhishangId;
				$_SESSION['if_shequ_tuan'] = $isUser->if_shequ_tuan;
				$cookieTime =60*60*24*30;
				setcookie('dt_username_'.$comId,$isUser->username,time()+$cookieTime, '/');
				setcookie('dt_pwd_'.$comId,$isUser->password,time()+$cookieTime, '/');
				if($isUser->shequ_id>0){
					$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=$isUser->shequ_id");
					$_SESSION[TB_PREFIX.'shequ_id'] = $isUser->shequ_id;
					$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
					$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
					$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
				}else{
					$shouhuo = $db->get_row("select id,areaId from user_address where userId=$isUser->id and comId=$comId order by moren desc limit 1");
					$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
					$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
				}
				add_user_oprate('绑定微信',2);
			}else{
				$shangji = (int)$_SESSION['tuijianren'];
				$shangshangji = 0;
				$tuan_id = 0;
				if($shangji>0){
					$shangshangji = (int)$db->get_var("select shangji from users where id=".$shangji);
					$tuan_id = (int)$db->get_var("select tuan_id from users where id=$shangji");
				}
				$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
				$status = 1;
				if($comId==1121)$status=0;
				$db->query("insert into users(comId,nickname,username,weixin_name,password,areaId,city,level,dtTime,status,openId,unionid,shangji,shangshangji,tuan_id,image) value($comId,'$username','$username','$weixin_name','$password',$areaId,$city,$level,'".date("Y-m-d H:i:s")."',$status,'$openid','$unionid',$shangji,$shangshangji,$tuan_id,'$weixin_image')");
				$userId = $db->get_var("select last_insert_id();");
				$zhishangId = reg_zhishang($userId,$username,$password,$openid,$unionid);
				$db->query("update users set zhishangId=$zhishangId where id=$userId");
				//注册奖励
				if($_SESSION['if_tongbu']==1){
					zhuce_jiangli($zhishangId);
				}else{
					zhuce_jiangli($userId);
				}
				if($status==1){
					$_SESSION[TB_PREFIX.'user_name'] = $username;
					$_SESSION[TB_PREFIX.'user_level'] = $level;
					$_SESSION[TB_PREFIX.'user_ID'] = $userId;
					$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
					$cookieTime =60*60*24*30;
					setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
					setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
					add_user_oprate('绑定微信',2);
					addTaskMsg(41,$userId,'您的商城有新的会员注册',$comId);
				}else{
					add_user_oprate('绑定微信',2);
					addTaskMsg(41,$userId,'您的商城有新的会员注册',$comId);
					echo '{"code":2,"message":"注册成功，请等待管理员审核！"}';
					exit;
				}
				
			}
			echo '{"code":1,"message":"绑定成功"}';
		}
		exit;
	}
}
function reg_nobind(){
	global $db,$request;
	$username = $openid = $_SESSION['openid'];
	if(empty($openid)){
		die('未检测到微信登录信息');
	}
	if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/index.php');
	}
	$areaId = 0;
	$city = 0;
	$unionid = $_SESSION['unionid'];
	$weixin_name = $_SESSION['weixin_name'];
	$weixin_image = $_SESSION['weixin_image'];
	$comId = (int)$_SESSION['demo_comId'];
	$password = rand(111111,999999);
	if($comId==10){
		$db_service = getCrmDb();
		$user = array();
		$user['nickname'] = '';
		$user['email'] = '';
		$user['username'] = $openid;
		$user['pwd'] = $password;
		$user['role'] = 1;
		$user['dtTime'] = date("Y-m-d H:i:s");
		$user['ip'] = '';
		$user['qq'] = '';
		$user['msn'] = '';
		$user['name'] = $weixin_name;
		$user['mtel'] = '';
		$user['phone'] = '';
		$user['openid'] = $openid;
		$user['unionid'] = $unionid;
		$user['weixin_name'] = $weixin_name;
		$user['lastlogin'] = time();
		if(!empty($_SESSION['tuijianren'])){
			$user['shangji'] = (int)$_SESSION['tuijianren'];
			$user['shangshangji'] = (int)$db_service->get_var("select shangji from demo_user where id=".$user['shangji']);
		}
		$db_service->insert_update('demo_user',$user,'id');
		$userId = $db_service->get_var("select last_insert_id();");
		if(!empty($_SESSION['tuijianren'])){
			$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
			$yaoqing_rules = json_decode($yaoqing_rule);
			//奖励抵扣金
			add_linpinka_money($user['shangji'], $yaoqing_rules->y_dikoujin,'邀请奖励','邀请奖励',0,$userId);
			if($yaoqing_rules->yhqId>0){
				//奖励优惠券
				$yhq = $db->get_row("select * from yhq where id=".$yaoqing_rules->yhqId." and comId=10 and status=1");
				if(!empty($yhq)){
					$user_yhq = array();
				  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
				  	$user_yhq['comId'] = 10;
				  	$user_yhq['userId'] = $user['shangji'];
				  	$user_yhq['jiluId'] = $yaoqing_rules->yhqId;
				  	$user_yhq['fafangId'] = 0;
				  	$user_yhq['title'] = $yhq->title;
				  	$user_yhq['man'] = $yhq->man;
				  	$user_yhq['jian'] = $yhq->money;
				  	$user_yhq['startTime'] = $yhq->startTime;
				  	$user_yhq['endTime'] = $yhq->endTime;
				  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
				  	$db->insert_update('user_yhq10',$user_yhq,'id');
				  	$db->query("update yhq set hasnum=hasnum+1 where id=".$yaoqing_rules->yhqId);
				}
			}
			$ifhas = $db->get_var("select userId from users_yaoqing where userId=".$user['shangji']);
			if(empty($ifhas)){
				$db->query("insert into users_yaoqing(userId,nums,dikoujin) value(".$user['shangji'].",1,".(int)$yaoqing_rules->y_dikoujin.")");
			}else{
				$db->query("update users_yaoqing set nums=nums+1,dikoujin=dikoujin+".(int)$yaoqing_rules->y_dikoujin." where userId=".$user['shangji']);
			}
		}
		//注册奖励
		zhuce_jiangli($userId);
		$_SESSION[TB_PREFIX.'user_name'] = $openid;
		$_SESSION[TB_PREFIX.'user_level'] = 0;
		$_SESSION[TB_PREFIX.'user_ID'] = $userId;
		$_SESSION[TB_PREFIX.'zhishangId'] = $userId;
		$cookieTime =60*60*24*30;
		setcookie('dt_username_'.$comId,$openid,time()+$cookieTime, '/');
		setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
		$url = empty($request['url'])?'/index.php':$request['url'];
		redirect($url);
	}else{
		$shangji = (int)$_SESSION['tuijianren'];
		$shangshangji = 0;
		$tuan_id = 0;
		if($shangji>0){
			$shangshangji = (int)$db->get_var("select shangji from users where id=".$shangji);
			$tuan_id = (int)$db->get_var("select tuan_id from users where id=$shangji");
		}
		$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
		$status = 1;
		if($comId==1121)$status=0;
		$db->query("insert into users(comId,nickname,username,weixin_name,password,areaId,city,level,dtTime,status,openId,unionid,shangji,shangshangji,tuan_id,image) value($comId,'$weixin_name','$username','$weixin_name','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',$status,'$openid','$unionid',$shangji,$shangshangji,$tuan_id,'$weixin_image')");
		$userId = $db->get_var("select last_insert_id();");
		$zhishangId = reg_zhishang($userId,$username,$password,$openid,$unionid,0);
		$db->query("update users set zhishangId=$zhishangId where id=$userId");
		//注册奖励
		zhuce_jiangli($userId);
		addTaskMsg(41,$userId,'您的商城有新的会员注册',$comId);
		if($status==1){
			$_SESSION[TB_PREFIX.'user_name'] = $username;
			$_SESSION[TB_PREFIX.'user_level'] = $level;
			$_SESSION[TB_PREFIX.'user_ID'] = $userId;
			$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
			$cookieTime =60*60*24*30;
			setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
			setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
			$url = empty($request['url'])?'/index.php':$request['url'];
			redirect($url);
		}else{
			echo '<script>alert("注册成功，请等待管理员审核！");location.href="/index.php";</script>';
			exit;
		}
	}
}
//短信登录
function send_login_msg(){
	global $db,$request;
	$phone = trim($request['phone']);
	$comId = (int)$_SESSION['demo_comId'];
	if($comId==10){
		$db_service = getCrmDb();
		$sql="SELECT id FROM demo_user WHERE username='".$phone."' limit 1";
		$isUser = (int)$db_service->get_var($sql);
	}else{
		$sql="SELECT id FROM users WHERE comId=$comId and username='".$phone."' limit 1";
		$isUser = (int)$db->get_var($sql);
	}
	if(empty($isUser)){
		echo '{"code":0,"message":"该手机号不是会员，请先注册"}';
		exit;
	}
	$yzm = rand(1000,9999);
	$_SESSION['yzm'] = $yzm;
	$_SESSION['login_yzm'] = $phone.'_'.$yzm;
	$verify = md5(substr($phone.$yzm,5,5));
	$com_title = $_SESSION['demo_com_title'];
	if($comId==10)$com_title='直商易购';
	file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title);
	die('{"code":1,"message":"发送成功"}');
}
function msg_login(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	if($request['tijiao']==1){
		if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
			echo '{"code":1,"message":"登录成功"}';
			exit;
		}
		if(empty($_SESSION['errors'])){
			echo '{"code":0,"message":"异常访问"}';
			exit;
		}
		if($_SESSION['errors']>6){
			echo '{"code":0,"message":"错误次数太多，请半小时之后再进行登录"}';
			exit;
		}
		$username = trim($request['username']);
		$yzm = trim($request['yzm']);
		if($_SESSION['login_yzm'] == $username.'_'.$yzm){
			if($comId==10){
				$db_service = getCrmDb();
				$sql="SELECT id,name,level,pwd,status,if_shequ_tuan FROM demo_user WHERE username='$username' LIMIT 1";
				$rst=$db_service->get_row($sql);
				if($rst){
					$_SESSION[TB_PREFIX.'user_name'] = $rst->name;
					$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
					$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
					$_SESSION[TB_PREFIX.'zhishangId'] = $rst->id;
					$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
					$cookieTime =60*60*24*30;
					setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
					setcookie('dt_pwd_'.$comId,$rst->pwd,time()+$cookieTime, '/');

					$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id order by moren desc limit 1");
					$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
					$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
					add_user_oprate('短信登录',1);
					echo '{"code":1,"message":"登录成功"}';
					exit;
				}else{
					echo '{"code":0,"message":"用户不存在！"}';
					exit;
				}
			}else{
				$sql="SELECT id,nickname,level,password,status,city,zhishangId,shequ_id,if_shequ_tuan FROM users WHERE comId=$comId and username='$username' LIMIT 1";
				$rst=$db->get_row($sql);
				if($rst){
					if($rst->status!=1){
						echo '{"code":0,"message":"帐号异常，请联系我们的客服人员！"}';
						exit;
					}else{
						//$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id order by moren desc limit 1");
						$_SESSION[TB_PREFIX.'user_name'] = $rst->nickname;
						$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
						$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
						$_SESSION[TB_PREFIX.'zhishangId'] = $rst->zhishangId;
						$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
						if($rst->shequ_id>0){
							$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=$rst->shequ_id");
							$_SESSION[TB_PREFIX.'shequ_id'] = $rst->shequ_id;
							$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
							$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
							$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
						}else{
							$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id and comId=$comId order by moren desc limit 1");
							$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
							$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
						}
						$cookieTime =60*60*24*30;
						setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
						setcookie('dt_pwd_'.$comId,$rst->password,time()+$cookieTime, '/');
						add_user_oprate('短信登录',1);
						echo '{"code":1,"message":"登录成功"}';
						exit;
					}
				}else{
					echo '{"code":0,"message":"不存在该账户，请检查手机号是否输入有误"}';
					exit;
				}
			}
		}else{
			$_SESSION['errors'] = $_SESSION['errors']+1;
			echo '{"code":0,"message":"验证码不正确，还可尝试'.(6-$_SESSION['errors']).'次"}';
			exit;
		}
	}
}
function login(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	if($request['tijiao']==1){
		if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
			echo '{"code":1,"message":"登录成功"}';
			exit;
		}
		if(empty($_SESSION['errors'])){
			echo '{"code":0,"message":"异常访问"}';
			exit;
		}
		if($_SESSION['errors']>6){
			echo '{"code":0,"message":"错误次数太多，请半小时之后再进行登录"}';
			exit;
		}
		$username = $request['username'];
		$password = $request['password'];
		if($comId==10||$comId==1009){
			require_once(ABSPATH.'/inc/class.shlencryption.php');
			$shlencryption = new shlEncryption($password);
			$db_service = new DtDatabase1();
			$sql="SELECT id,name,level,pwd,status,if_shequ_tuan FROM demo_user WHERE username='$username' LIMIT 1";
			$rst=$db_service->get_row($sql);
			if($rst){
				if ($rst->pwd==$shlencryption->to_string()){
					$_SESSION[TB_PREFIX.'user_name'] = $rst->name;
					$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
					$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
					$_SESSION[TB_PREFIX.'zhishangId'] = $rst->id;
					$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
					$cookieTime =60*60*24*30;
					$db_service->query("update demo_user set lastlogin='".time()."' where id=$rst->id");
					setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
					setcookie('dt_pwd_'.$comId,$rst->pwd,time()+$cookieTime, '/');
					if($rst->shequ_id>0){
						$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=$rst->shequ_id");
						$_SESSION[TB_PREFIX.'shequ_id'] = $rst->shequ_id;
						$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
						$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
						$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
					}else{
						$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id and comId=$comId order by moren desc limit 1");
						$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
						$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
					}
					add_user_oprate('账号密码登录',1);
					echo '{"code":1,"message":"登录成功"}';
					exit;
				}else{
					$_SESSION['errors'] = $_SESSION['errors']+1;
					echo '{"code":0,"message":"密码不正确，还可尝试'.(6-$_SESSION['errors']).'次"}';
					exit;
				}
			}else{
				echo '{"code":0,"message":"不存在该账户，请检查手机号是否输入有误"}';
				exit;
			}
		}else{
			$sql="SELECT id,nickname,level,password,status,city,zhishangId,shequ_id,if_shequ_tuan FROM users WHERE comId=$comId and username='$username' LIMIT 1";
			$rst=$db->get_row($sql);
			if($rst){
				require_once(ABSPATH.'/inc/class.shlencryption.php');
				$shlencryption = new shlEncryption($password);
				if ($rst->password==$shlencryption->to_string()){
					if($rst->status!=1){
						echo '{"code":0,"message":"帐号异常，请联系我们的客服人员！"}';
						exit;
					}else{
						//$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id order by moren desc limit 1");
						$_SESSION[TB_PREFIX.'user_name'] = $rst->nickname;
						$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
						$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
						$_SESSION[TB_PREFIX.'zhishangId'] = $rst->zhishangId;
						$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
						$db->query("update users set lastlogin='".date("Y-m-d H:i:s")."' where id=$rst->id");
						if($rst->shequ_id>0){
							$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=$rst->shequ_id");
							$_SESSION[TB_PREFIX.'shequ_id'] = $rst->shequ_id;
							$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
							$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
							$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
						}else{
							$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id and comId=$comId order by moren desc limit 1");
							$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
							$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
						}
						$cookieTime =60*60*24*30;
						setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
						setcookie('dt_pwd_'.$comId,$rst->password,time()+$cookieTime, '/');
						echo '{"code":1,"message":"登录成功"}';
						add_user_oprate('账号密码登录',1);
						exit;
					}
				}else{
					$_SESSION['errors'] = $_SESSION['errors']+1;
					echo '{"code":0,"message":"密码不正确，还可尝试'.(6-$_SESSION['errors']).'次"}';
					exit;
				}
			}else{
				echo '{"code":0,"message":"不存在该账户，请检查手机号是否输入有误"}';
				exit;
			}
		}
	}else{
		if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
			redirect('/index.php');
		}
	}
}
function reg_wailai(){
	global $request;
	$db_service = getCrmDb();
	$username = $request['phone'];
	$name = $request['name'];
	$verify = $request['verify'];
	if(substr(md5($username),5,5)==$verify){
		$password = '0';
		$sql="SELECT id FROM demo_user WHERE username='".$username."' limit 1";
		$isUser = (int)$db_service->get_var($sql);
		if($isUser==0){
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
			$user['level'] = 0;
			$user['lastlogin'] = time();
			$db_service->insert_update('demo_user',$user,'id');
		}
	}
	exit;
}
function reg(){
	global $db,$request;
	if(!empty($_SESSION[TB_PREFIX.'user_ID'])){
		redirect('/index.php');
	}
	if($request['tijiao']==1){
		$username = $request['username'];
		$password = $request['password'];
		$comId = (int)$_SESSION['demo_comId'];
		$yzm = $request['yzm'];
		$areaId = (int)$request['areaId'];
		$city = (int)$db->get_var("select parentId from demo_area where id=$areaId");
		if($yzm!=$_SESSION['yzm']||empty($yzm)){
			echo '{"code":0,"message":"验证码错误,请重新输入"}';
			exit;
		}
		if(empty($password)){
			$password = rand(100000,999999);
		}
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new shlEncryption($password);
		$password = $shlencryption->to_string();
		if($comId==10||$comId==1009){
			$db_service = getCrmDb();
			$sql="SELECT id FROM demo_user WHERE username='".$username."' limit 1";
			$isUser = (int)$db_service->get_var($sql);
			if($isUser>0){
				echo '{"code":0,"message":"该手机号已经注册过知商了，请进行登录或找回密码"}';
				exit;
			}
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
			$user['name'] = '';
			$user['mtel'] = $username;
			$user['phone'] = $username;
			$user['level'] = (int)$db->get_var("select id from user_level where comId=10 order by id asc limit 1");
			$user['lastlogin'] = time();
			if(!empty($_SESSION['tuijianren'])){
				$user['shangji'] = (int)$_SESSION['tuijianren'];
				$user['shangshangji'] = (int)$db_service->get_var("select shangji from demo_user where id=".$user['shangji']);
			}
			$db_service->insert_update('demo_user',$user,'id');
			$userId = $db_service->get_var("select last_insert_id();");
			if(!empty($_SESSION['tuijianren'])){
				$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
				$yaoqing_rules = json_decode($yaoqing_rule);
				//奖励抵扣金
				add_linpinka_money($user['shangji'], $yaoqing_rules->y_dikoujin,'邀请奖励','邀请奖励',0,$userId);
				if($yaoqing_rules->yhqId>0){
					//奖励优惠券
					$yhq = $db->get_row("select * from yhq where id=".$yaoqing_rules->yhqId." and comId=10 and status=1");
					if(!empty($yhq)){
						$user_yhq = array();
					  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
					  	$user_yhq['comId'] = 10;
					  	$user_yhq['userId'] = $user['shangji'];
					  	$user_yhq['jiluId'] = $yaoqing_rules->yhqId;
					  	$user_yhq['fafangId'] = 0;
					  	$user_yhq['title'] = $yhq->title;
					  	$user_yhq['man'] = $yhq->man;
					  	$user_yhq['jian'] = $yhq->money;
					  	$user_yhq['startTime'] = $yhq->startTime;
					  	$user_yhq['endTime'] = $yhq->endTime;
					  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
					  	$db->insert_update('user_yhq10',$user_yhq,'id');
					  	$db->query("update yhq set hasnum=hasnum+1 where id=".$yaoqing_rules->yhqId);
					}
				}
				$ifhas = $db->get_var("select userId from users_yaoqing where userId=".$user['shangji']);
				if(empty($ifhas)){
					$db->query("insert into users_yaoqing(userId,nums,dikoujin) value(".$user['shangji'].",1,".(int)$yaoqing_rules->y_dikoujin.")");
				}else{
					$db->query("update users_yaoqing set nums=nums+1,dikoujin=dikoujin+".(int)$yaoqing_rules->y_dikoujin." where userId=".$user['shangji']);
				}
			}
			//注册奖励
			zhuce_jiangli($userId);
			$_SESSION[TB_PREFIX.'user_name'] = $username;
			$_SESSION[TB_PREFIX.'user_level'] = 0;
			$_SESSION[TB_PREFIX.'user_ID'] = $userId;
			$_SESSION[TB_PREFIX.'zhishangId'] = $userId;
			$cookieTime =60*60*24*30;
			setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
			setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
			echo '{"code":1,"message":"注册成功"}';
		}else{
			$sql="SELECT id FROM users WHERE comId=$comId and username='".$username."' limit 1";
			$isUser = (int)$db->get_var($sql);
			if($isUser>0){
				echo '{"code":0,"message":"该手机号已经注册过了，请进行登录或找回密码"}';
				exit;
			}
			$shangji = (int)$_SESSION['tuijianren'];
			$shangshangji = 0;
			$tuan_id = 0;
			if($shangji>0){
				$shangshangji = (int)$db->get_var("select shangji from users where id=".$shangji);
				$tuan_id = (int)$db->get_var("select tuan_id from users where id=$shangji");
			}
			$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
			$status = 1;
			if($comId==1121)$status=0;
			$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,shangji,shangshangji,tuan_id) value($comId,'$username','$username','$password',$areaId,$city,$level,'".date("Y-m-d H:i:s")."',$status,$shangji,$shangshangji,$tuan_id)");
			$userId = $db->get_var("select last_insert_id();");
			$zhishangId = reg_zhishang($userId,$username,$password);
			$db->query("update users set zhishangId=$zhishangId where id=$userId");
			//注册奖励
			zhuce_jiangli($userId);
			addTaskMsg(41,$userId,'您的商城有新的会员注册',$comId);
			if($status==1){
				$_SESSION[TB_PREFIX.'user_name'] = $username;
				$_SESSION[TB_PREFIX.'user_level'] = $level;
				$_SESSION[TB_PREFIX.'user_ID'] = $userId;
				$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
				$cookieTime =60*60*24*30;
				setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
				setcookie('dt_pwd_'.$comId,$password,time()+$cookieTime, '/');
				echo '{"code":1,"message":"注册成功"}';
			}else{
				echo '{"code":0,"message":"注册成功,请等待管理员审核！"}';
			}
		}
		
		exit;
	}
}
//同步知商账号
function reg_zhishang($userId,$username,$password,$openid='',$unionid='',$level=1){
	$db_service = new DtDatabase1();
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
		$user['name'] = '';
		$user['mtel'] = $username;
		$user['phone'] = $username;
		$user['openid'] = $_SESSION['if_tongbu']==1?$openid:'';
		$user['unionid'] = $_SESSION['if_tongbu']==1?$unionid:'';
		$user['lastlogin'] = time();
		if($level==1){
			global $db;
			$user['level'] = (int)$db->get_var("select id from user_level where comId=10 order by id asc limit 1");
		}
		$db_service->insert_update('demo_user',$user,'id');
		$ifhas = $db_service->get_var("select last_insert_id();");
	}else if(!empty($openid)){
		$db_service->query("update demo_user set openid='$openid',unionid='$unionid' where id=$ifhas");
	}
	$if_re = $db_service->get_var("select id from demo_user_relation where userId=$ifhas and comId=$comId limit 1");
	if(empty($if_re)){
		$db_service->query("insert into demo_user_relation(comId,dtTime,userId,company) value($comId,'".date("Y-m-d H:i:s")."',$ifhas,'$company')");
	}
	return $ifhas;
}
function logout()
{
	$comId = (int)$_SESSION['demo_comId'];
	session_destroy();
	$_SESSION['demo_comId'] = $comId;
    setcookie("dt_username_$comId",'', time()-3600,'/');
    setcookie("dt_pwd_$comId",'', time()-3600,'/');
    redirect('/index.php');
}
function qiandao(){
	global $db;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$comId = (int)$_SESSION['demo_comId'];
	$today = date("Y-m-d");
	$days = $db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='$today' limit 1");
	if($days>0){
		die('{"code":0,"message":"您今天已经签过到了"}');
	}
	$qiandao = $db->get_row("select if_qiandao,qiandao_rule from user_shezhi where comId=$comId");
	if($qiandao->if_qiandao!=1){
		die('{"code":0,"message":"签到功能已关闭"}');
	}
	$qiandao_rule = $qiandao->qiandao_rule;
	if(!empty($qiandao_rule)){
		$rule = json_decode($qiandao_rule,true);
	}
	$yesterday = (int)$db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='".date("Y-m-d",strtotime('-1 day'))."' limit 1");
	$yesterday++;
	$jifen = $rule['jifen'];
	if($rule['type']==2){
		$first =$rule['first'];
		$maxday = $rule['day'];
		$leijia = $rule['leijia'];
		if($yesterday>$maxday+1){
			$yesterday = $maxday+1;
		}
		$jifen = $first+($yesterday-1)*$leijia;
	}
	$db->query("delete from user_qiandao where userId=$userId and comId=$comId");
	$db->query("insert into user_qiandao(userId,comId,dtTime,days) value($userId,$comId,'$today',$yesterday)");
	$db->query("insert into user_qiandao_jilu(userId,comId,dtTime) value($userId,$comId,'$today')");
	if($comId==10){
		$db_service = getCrmDb();
		$db_service->query("update demo_user set jifen=jifen+$jifen where id=$userId");
		$return_jifen = $db_service->get_var("select jifen from demo_user where id=$userId");
	}else{
		$db_service = getCrmDb();
		$db->query("update users set jifen=jifen+$jifen where id=$userId");
		$return_jifen = $db->get_var("select jifen from users where id=$userId");
	}
	
	$jifen_jilu = array();
	$jifen_jilu['userId'] = $userId;
	$jifen_jilu['comId'] = $comId;
	$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$jifen_jilu['jifen'] = $jifen;
	$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
	$jifen_jilu['type'] = 1;
	$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
	$jifen_jilu['remark'] = '签到';
	$fenbiao = getFenbiao($comId,20);
	$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	//$jifen = $db->get_var("select jifen from *");
	die('{"code":1,"message":"签到成功","days":"'.$yesterday.'","jifen":"'.$return_jifen.'"}');
}
function sendSms(){
	global $db,$request;
	$phone = $request['phone'];
	$type = $request['type'];
	$comId = (int)$_SESSION['demo_comId'];
	if($comId==10){
		$db_service = getCrmDb();
		$sql="SELECT id FROM demo_user WHERE username='".$phone."' limit 1";
		$isUser = (int)$db_service->get_var($sql);
	}else{
		$sql="SELECT id FROM users WHERE comId=$comId and username='".$phone."' limit 1";
		$isUser = (int)$db->get_var($sql);
	}
	if($isUser>0){
		echo '{"code":0,"message":"该手机号已经注册过了，不能再进行注册或绑定"}';
		exit;
	}
	$yzm = rand(1000,9999);
	$_SESSION['yzm'] = $yzm;
	$verify = md5(substr($phone.$yzm,5,5));
	$com_title = $_SESSION['demo_com_title'];
	if($comId==10)$com_title='直商易购';
	file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title);
	die('{"code":1,"message":"发送成功"}');
}
function sendSms2(){
	global $db,$request;
	$phone = $request['phone'];
	$type = $request['type'];
	$comId = (int)$_SESSION['demo_comId'];
	$yzm = rand(1000,9999);
	$_SESSION['yzm'] = $yzm;
	$verify = md5(substr($phone.$yzm,5,5));
	$com_title = $_SESSION['demo_com_title'];
	$userId = 0;
	if($comId==10){
		$com_title='直商易购';
		$db_service = getCrmDb();
		$userId = (int)$db_service->get_var("SELECT id FROM demo_user WHERE username='".$phone."' limit 1");
	}
	file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title);
	die('{"code":1,"message":"发送成功","userId":'.$userId.'}');
}
function sendSms1(){
	global $db,$request;
	$phone = $request['phone'];
	$comId = (int)$_SESSION['demo_comId'];
	if($comId==10){
		$db_service = getCrmDb();
		$sql="SELECT id FROM demo_user WHERE username='".$phone."' limit 1";
		$isUser = (int)$db_service->get_var($sql);
	}else{
		$sql="SELECT id FROM users WHERE comId=$comId and username='".$phone."' limit 1";
		$isUser = (int)$db->get_var($sql);
	}
	if(empty($isUser)){
		echo '{"code":0,"message":"该手机号不是会员，请直接注册"}';
		exit;
	}
	$yzm = rand(1000,9999);
	$_SESSION['yzm'] = $yzm;
	$verify = md5(substr($phone.$yzm,5,5));
	$com_title = $_SESSION['demo_com_title']; 
	if($comId==10)$com_title='直商易购';
	file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title);
	die('{"code":1,"message":"发送成功"}');
}
/*
以下为新添加内容
1.账户管理、昵称、密码、支付密码(users表)
2.收货地址模块(user_address表)
3.我的资源模块(users表)
4.团长权益（先写死）
7.银行卡、提现(user_bank、user_tixian)
#
*/

//账户管理
function zhgl(){
	global $db,$request;
}
//头像上传
function touxiang(){
	global $db;
	global $request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	require(ABSPATH.'/inc/class.upload.php');	
	$upload = new Upload();
	$fileName = $upload->SaveFile('uploadfile');
	$tximage = '/upload/'.$fileName;
	//$old = $db->get_var("select image from users where id=$userId");
	$db->query("update users set image='$tximage' where id=$userId");
	///if(file_exists(ABSPATH.$old)){
        //unlink(ABSPATH.$old);
    //}
	redirect('/index.php?p=8&a=zhgl');
}

//昵称
function nc(){
	global $db,$request;
	if($request['tijiao'] == 1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$nickname = $request['nickname'];
		$db->query("update users set nickname='$nickname' where id=$userId");
		add_user_oprate('修改昵称',2);
		redirect('/index.php?p=8&a=zhgl');
	}
}
//修改密码
function editpwd(){
	global $db,$request;
	if($request['tijiao'] == 1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}
		require(ABSPATH.'/inc/class.validate.php');
		if($_SESSION['if_tongbu']==1){
			$db_service = getCrmDb();
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
			$sql='SELECT pwd FROM demo_user WHERE id='.$userId;
			$row = $db_service->get_row($sql);
			if(!empty($request['newpass'])){//预留修改密码
				if(!validate::password(6,16, $request['newpass']))die('{"code":0,"message":"密码长度6至16位！"}');
				require_once(ABSPATH.'/inc/class.shlencryption.php');
				$shlencryption = new shlEncryption($request['pwd']);
				$pwd = $shlencryption->to_string();
				if($request['newpass'] == $request['repwd'])
				{
					if($pwd == $row->pwd)
					{
						$shlencryption = new shlEncryption($request['newpass']);
						$newpwd = $shlencryption->to_string();
						$fh = $db_service->query("update demo_user set pwd='$newpwd' where id=$userId");
						if($comId!=10){
							$db->query("update users set password='$newpwd' where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
						}
						add_user_oprate('修改密码',2);
						if($fh)
						{	
							echo '{"code":1,"message":"修改成功"}';
							exit;
						}
						else
						{
							echo '{"code":0,"message":"修改失败"}';
							exit;
						}
					}
					else
					{
						echo '{"code":0,"message":"密码错误"}';
						exit;
					}
				}
				else
				{
					echo '{"code":0,"message":"重复密码错误"}';
					exit;
				}
			}
			else
			{
				echo '{"code":0,"message":"密码不能为空"}';
				exit;
			}
		}else{
			$sql='SELECT password FROM users WHERE id='.$userId;
			$row = $db->get_row($sql);
			if(!empty($request['newpass'])){//预留修改密码
				if(!validate::password(6,16, $request['newpass']))die('{"code":0,"message":"密码长度6至16位！"}');
				require_once(ABSPATH.'/inc/class.shlencryption.php');
				$shlencryption = new shlEncryption($request['pwd']);
				$pwd = $shlencryption->to_string();
				if($request['newpass'] == $request['repwd'])
				{
					if($pwd == $row->password)
					{
						$shlencryption = new shlEncryption($request['newpass']);
						$newpwd = $shlencryption->to_string();
						$fh = $db->query("update users set password='$newpwd' where id=$userId");
						add_user_oprate('修改密码',2);
						if($fh)
						{	
							echo '{"code":1,"message":"修改成功"}';
							exit;
						}
						else
						{
							echo '{"code":0,"message":"修改失败"}';
							exit;
						}
					}
					else
					{
						echo '{"code":0,"message":"密码错误"}';
						exit;
					}
				}
				else
				{
					echo '{"code":0,"message":"重复密码错误"}';
					exit;
				}
			}
			else
			{
				echo '{"code":0,"message":"密码不能为空"}';
				exit;
			}
		}
		//redirect('/index.php?p=8&a=zhgl');
	}
}
//支付密码
function editzfpwd(){
	global $db,$request;
	if($request['tijiao'] == 1 && !empty($request['zfpass'])){
		$yzm = $request['yzm'];
		$areaId = 0;
		$city = 0;
		if($yzm!=$_SESSION['yzm']||empty($yzm)){
			echo '{"code":0,"message":"验证码错误"}';
			exit;
		}
		require(ABSPATH.'/inc/class.validate.php');
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new shlEncryption($request['zfpass']);
		$pwd = $shlencryption->to_string();
		if($_SESSION['if_tongbu']==1){
			$db_service = getCrmDb();
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
			$fh = $db_service->query("update demo_user set payPass='$pwd' where id=$userId");
			if($comId!=10){
				$db->query("update users set payPass='$pwd' where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
			}
			add_user_oprate('修改支付密码',2);
		}else{
			$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
			$fh = $db->query("update users set payPass='$pwd' where id=$userId");
			add_user_oprate('修改支付密码',2);
		}
		if(!empty($request['url'])){
			redirect(urldecode($request['url']));
		}else{
			echo '{"code":1,"message":"设置成功"}';
			exit;
		}
	}
}

//收货地址
function shouhuo(){
	global $db,$request;
}
//添加、修改收货地址
function shouhuoEdit(){
	global $db,$request;
	if($request['tijiao'] == 1){
		// var_dump($request);
		// exit;
		$comId = (int)$_SESSION['demo_comId'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		if($_SESSION['if_tongbu']==1){
			$comId = 10;
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		}
		
		$id = (int)$request['id'];
		$name = filtergl($request['name']);
		$phone = $request['phone'];
		$address = filtergl(preg_replace('/((\s)*(\n)+(\s)*)/','',$request['address']));
		$areaId = (int)$request['areaId'];//最后一级areaid
		$areaName = $request['ssq'];
		$title = $request['title'];
		$shequId = (int)$request['shequId'];
		if($shequId>0){
			$address = $title.$address;
		}
		$moren = (int)$request['moren'];
		$moren = ($moren==2)?1:0;
		if(empty($name)){
			echo "<script>alert('姓名不能为空');history.go(-1)</script>";
			exit;
		}
		if(empty($phone)){
			echo "<script>alert('联系电话不能为空');history.go(-1)</script>";
			exit;
		}
		if(empty($address)){
			echo "<script>alert('详细地址不能为空');history.go(-1)</script>";
			exit;
		}
		if($moren == 1){
			$db->query("update user_address set moren=0 where userId=$userId and comId=$comId");
		}
		if(empty($id)){
		   $db->query("insert into user_address (name,areaId,areaName, address,phone,userId,moren,title,comId,shequId) values('$name',$areaId,'$areaName', '$address','$phone', $userId,$moren,'$title',$comId,$shequId)");
		}else{
		    $db->query("update user_address set name='$name',areaId=$areaId,areaName='$areaName',address='$address',phone='$phone', moren=$moren,title='$title',shequId=$shequId where id=$id");
		}
		add_user_oprate('修改收货地址',2);
		if(!empty($request['url'])){
			redirect(urldecode($request['url']));
		}
		redirect("/index.php?p=8&a=shouhuo");
	}
}
//收货地址列表页：设置为  常用
function shouhuoMoren(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$db->query("update user_address set moren=0 where userId=$userId and comId=$comId");
	$db->query("update user_address set moren=1 where id=$id");
	redirect("/index.php?p=8&a=shouhuo");
}
//收货地址删除
function shouhuoDel(){
	global $db,$request;
	$id = (int)$request['id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$db->query("delete from user_address where id=$id and userId=$userId");
	redirect("/index.php?p=8&a=shouhuo");
}
//积分记录
function jfjl(){
	global $db,$request;
}
function jifen_yue(){}
function jifen2yue(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$jifen = $request['jifen'];
	$jifen_yue = $db->get_var("select jifen from users where id=$userId");
	$jifen_rule = $db->get_row("select jifen_yue,jifen_yue_num,jifen_yue_limit from user_shezhi where comId=$comId");
	if($jifen>$jifen_yue){
		echo '{"code":0,"message":"未开放兑换该功能！"}';
		exit;
	}
	if(!empty($jifen_rule->jifen_yue_limit)){
		$has = $db->get_var("select sum(jifen) from user_jifen$fenbiao where userId=$userId and dtTime like '".date("Y-m-d")."%' and remark='积分兑换余额'");
		if($has+$jifen>$jifen_rule->jifen_yue_limit){
			echo '{"code":0,"message":"超出每日积分兑换限制"}';
			exit;
		}
	}
	$money = (int)($jifen*100/$jifen_rule->jifen_yue_num)/100;
	$db->query("update users set money=money+$money where id=$userId");
	$liushui = array();
	$liushui['userId']=$userId;
	$liushui['comId']=$comId;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=$money;
	$liushui['yue']=$db->get_var("select money from users where id=$userId");
	$liushui['type']=2;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='积分兑换余额';
	$liushui['orderInfo']='';
	insert_update('user_liushui'.$fenbiao,$liushui,'id');
	$db->query("update users set jifen=jifen-$jifen where id=$userId");
	$jifen_jilu = array();
	$jifen_jilu['userId'] = $userId;
	$jifen_jilu['comId'] = $comId;
	$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
	$jifen_jilu['jifen'] = -$jifen;
	$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
	$jifen_jilu['type'] = 2;
	$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
	$jifen_jilu['remark'] = '积分兑换余额';
	$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	die('{"code":1,"message":"兑换成功"}');
}
//积分记录
function get_jfjl_list(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	/*if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}*/
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;

	$yzFenbiao = getFenbiao($comId,20);
	$sql = "select * from user_jifen$yzFenbiao where userId=$userId order by id desc ";
    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db->get_var(str_replace('*','count(id)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['shoucang'] = $shoucang;
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	if($key->type==1){
          $key->jifen = '<span>+'.$key->jifen.'</span>';
        }else if($key->type==2){
          $key->jifen = '-'.$key->jifen;
        }
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}

//我的钱包
function qianbao(){
	global $db,$request;
}

//钱包明细
function qbmx(){
	global $db,$request;
}
function yongjinmx(){
	global $db,$request;
}
//钱包明细(余额列表)
function get_yejl_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];//test:15
	/*if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}*/
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;

	$where = "";
	$type = (int)$request['type'];
	$startDate = trim($request['sd']);
	$endDate = trim($request['ed']);
	$remark = trim($request['remark']);

	if(!empty($type)){
		$where .= " and type=$type ";
	}
	if(!empty($remark)){
		$where .= " and remark='$remark' ";
	}
	if($startDate){
		$where .= " and dtTime>='".date("Y-m-d H:i:s", strtotime($startDate))."' ";
	}
	if($endDate){
		$where .= " and dtTime<'".date("Y-m-d H:i:s", strtotime("$endDate +1 days"))."' ";
	}
	$yzFenbiao = getFenbiao($comId,20);//test:0;
	$sql = "select * from user_liushui$yzFenbiao where comId=$comId and userId=$userId $where order by id desc ";
    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db->get_var(str_replace('*','count(id)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	$key->dtTime = date("Y-m-d H:i", strtotime($key->dtTime));
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_yongjin_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];//test:15
	/*if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}*/
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;

	$where = "";
	$type = (int)$request['type'];
	$startDate = trim($request['sd']);
	$endDate = trim($request['ed']);
	$remark = trim($request['remark']);

	if(!empty($type)){
		$where .= " and type=$type ";
	}
	if(!empty($remark)){
		$where .= " and remark='$remark' ";
	}
	if($startDate){
		$where .= " and dtTime>='".date("Y-m-d H:i:s", strtotime($startDate))."' ";
	}
	if($endDate){
		$where .= " and dtTime<'".date("Y-m-d H:i:s", strtotime("$endDate +1 days"))."' ";
	}
	$yzFenbiao = getFenbiao($comId,20);//test:0;
	$sql = "select * from user_yongjin10 where comId=$comId and userId=$userId $where order by id desc ";
    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
    $count = $db->get_var(str_replace('*','count(id)',$sql));
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	$key->dtTime = date("Y-m-d H:i", strtotime($key->dtTime));
      	$return['data'][] = $key;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//银行卡列表
function yhk(){
	global $db,$request;
}
//解绑银行卡
function yhk_jiebang(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$db->query("delete from user_bank where userId=$userId and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
//添加银行卡
function tjyhk(){
	global $db,$request;
	if($request['tijiao'] == 1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		if($_SESSION['if_tongbu']==1){
			$comId = 10;
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		}
		//$id = (int)$request['id'];
		$name = trim($request['name']);
		$msn = trim($request['msn']);
		$bank_name = trim($request['bank_name']);
		$bank_card = trim($request['bank_card']);
		$ifhas = (int)$request['id'];
		if(empty($ifhas)){
			$db->query("insert into user_bank (name, msn, bank_name, bank_card, userId,comId) values ('$name', '$msn', '$bank_name', '$bank_card', $userId,$comId)");
		}else{
			$db->query("update user_bank set name='$name', msn='$msn', bank_name='$bank_name', bank_card='$bank_card' where id=$ifhas");
		}
		add_user_oprate('修改银行卡',2);
		redirect("/index.php?p=8&a=yhk");
	}
}

//删除银行卡
function delyhk(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("delete from user_bank where id=$id limit 1");
	redirect("/index.php?p=8&a=yhk");
}

//提现
function tixian(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($comId==10){
		$db_service = getCrmDb();
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	//$openId = $db->get_var("select openId from users where id=$userId limit 1");
	$ifktx = $db->get_var("select id from user_tixian where userId=$userId and comId=$comId and status=0 limit 1");
	if(!empty($ifktx)){
		echo '<script>alert("您的提现申请正在审核中，请耐心等待。");</script>';
		redirect("/index.php?p=8&a=qianbao");
	}

	if($request['tijiao'] == 1){
		$money = trim($request['txmoney']);
		if($money<1 || $money>5000){
			echo '<script>alert("提现金额必须大于1元，小于5000元！");</script>';
			redirect("/index.php?p=8&a=qianbao");
		}
		//$comId = (int)$_SESSION['demo_comId'];
		
		if($comId==10){
			$ktx = $db_service->get_var("select yongjin from demo_user where id=$userId limit 1");
		}else{
			$ktx = $db->get_var("select money from users where id=$userId limit 1");
		}
		if($ktx < $money || $money<=0){
			echo '<script>alert("余额不足，无法提现！");</script>';
			redirect("/index.php?p=8&a=qianbao");
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
		
		//$liushuiId = mysql_insert_id();
		//sendMoney($tixianId,$liushuiId,$liushui['orderId'],$money,$openId);
		//exit;
		echo '<script>alert("提交成功，请等待审核。");</script>';
		redirect("/index.php?p=8&a=qianbao");
	}
}
//提现申请时输入支付密码验证
function qrtxmm(){
	global $db,$request;
	$zfmm = $request['zf'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		$payPass = $db_service->get_var("select payPass from demo_user where id=$userId limit 1");
	}else{
		$payPass = $db->get_var("select payPass from users where id=$userId limit 1");
	}
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$shlencryption = new shlEncryption($zfmm);
	if($shlencryption->to_string() == $payPass){
		echo '{"code":1,"message":"ok"}';
	}else{
		echo '{"code":0,"message":"密码错误"}';
	}
	exit;
}
function findMima(){
	global $db,$request;
	if($request['tijiao']==1){
		$username = $request['username'];
		$password = $request['password'];
		$comId = (int)$_SESSION['demo_comId'];
		$yzm = $request['yzm'];
		$city = $db->get_var("select parentId from demo_area where id=$areaId");
		if($yzm!=$_SESSION['yzm']||empty($yzm)){
			echo '{"code":0,"message":"验证码错误,请重新输入"}';
			exit;
		}
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new shlEncryption($password);
		$password = $shlencryption->to_string();
		$db->query("update users set password='$password' where comId=$comId and username='$username' limit 1");
		echo '{"code":1,"message":"注册成功"}';
		exit;
	}
}
function chongzhi(){}
//礼品卡相关
function lipinka(){}
function lipinka1(){}
function lipinka2(){}
function lipinka3(){}
function card_liushui(){}
function bind_card(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$card_id = $request['card_id'];
	$card_pwd= $request['card_pwd'];
	$fenbiao = getFenbiao($comId,20);
	$card = $db->get_row("select * from gift_card$fenbiao where cardId='$card_id' limit 1");
	if(empty($card)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}else if($card->userId>0){
		die('{"code":0,"message":"礼品卡已经被绑定过了"}');
	}else if($card->password!=$card_pwd){
		die('{"code":0,"message":"礼品卡密码不正确"}');
	}
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
	$db->query("update gift_card$fenbiao set userId=$userId,bind_time='".date("Y-m-d H:i:s")."' where id=$card->id");
		$db->query("update gift_card_jilu set bind_num=bind_num+1 where id=$card->jiluId");
	echo '{"code":1,"message":"绑定成功！"}';
	exit;
}
function bind_lipinka(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$card_id = $request['card_id'];
	$card_pwd= $request['card_pwd'];
	$card = $db->get_row("select * from lipinka where cardId='$card_id' limit 1");
	if(empty($card)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}else if($card->userId>0){
		die('{"code":0,"message":"礼品卡已经被绑定过了"}');
	}else if($card->password!=$card_pwd){
		die('{"code":0,"message":"礼品卡密码不正确"}');
	}
	$time1 = time();
	$time2 = strtotime($card->endTime.' 23:59:59');
	if($time1>$time2){
		echo '{"code":0,"message":"该礼品卡已经过期了！"}';
		exit;
	}
	$db->query("update lipinka set userId=$userId,bind_time='".date("Y-m-d H:i:s")."' where id=$card->id");
		$db->query("update lipinka_jilu set bind_num=bind_num+1 where id=$card->jiluId");
	echo '{"code":1,"message":"绑定成功！"}';
	exit;
}
function get_card_liushui(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$id = $request['id'];
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request["pageNum"];
	$sql = "select * from gift_card_liushui$fenbiao where cardId=$id";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
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
function check_zeng(){
	global $request;
	$username = trim($request['username']);
	if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$user = $db_service->get_row("select id,name from demo_user where username='$username' limit 1");
	}else{
		global $db;
		$comId = (int)$_SESSION['demo_comId'];
		$user = $db->get_row("select id,nickname as name from users where comId=$comId and username='$username' limit 1");
	}
	if(empty($user)){
		echo '{"code":0,"message":"未找到该会员"}';
		exit;
	}
	die('{"code":1,"message":"","userId":'.$user->id.',"name":"'.$user->name.'"}');
}
function zeng_yhq(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$yhq_id = (int)$request['yhq_id'];
	$to_user = (int)$request['user_id'];

	$card = $db->get_var("select id from user_yhq$fenbiao where id=$yhq_id and comId=$comId and userId=$userId");
	if(empty($card)){
		die('{"code":0,"message":"优惠券不存在"}');
	}
	$db->query("update user_yhq$fenbiao set userId=$to_user where id=$yhq_id");
	die('{"code":1,"message":"操作成功"}');
}
function zeng_card(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$card_id = (int)$request['card_id'];
	$money = $request['money'];
	$to_user = (int)$request['user_id'];
	$card = $db->get_row("select * from gift_card$fenbiao where id=$card_id and userId=$userId");
	if(empty($card)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}
	if($money>$card->yue){
		die('{"code":0,"message":"礼品卡余额不足"}');
	}
	$db->query("update gift_card$fenbiao set yue=yue-$money where id=$card_id");
	$liushui = array();
	$liushui['cardId']=$card_id;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$card_id");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='赠送';
	$liushui['orderInfo']='赠送给会员：'.$request['username'];
	$liushui['orderId']=0;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	if(empty($card->endTime) || $card->endTime=='0000-00-00'){
		add_linpinka_money($to_user,$money,'赠送','来自“'.$_SESSION[TB_PREFIX.'user_name'].'”的赠送',$card->daili_id);
	}else{
		$sql = "insert into gift_card$fenbiao(comId,cardId,password,money,yue,jiluId,typeInfo,endTime,userId,bind_time,bili,from_id,daili_id) values";
		$sql1 = '';
		$cardId = $card->jiluId;
		$length = 16-strlen($cardId);
		for($j = 0; $j < $length; $j++) {
			$cardId .= rand(0,9);
		}
		$password = rand(100000,999999);
		$endTime = $card->endTime;
		$sql1.=" ($comId,'$cardId','$password','$money','$money',$card->jiluId,'$card->typeInfo','$endTime',$to_user,'".date("Y-m-d H:i:s")."','$card->bili',$card->id,$card->daili_id)";
		$db->query($sql.$sql1);
		$card_id = $db->get_var("select last_insert_id();");
		$liushui = array();
		$liushui['cardId']=$card_id;
		$liushui['money']=$money;
		$liushui['yue']=$money;
		$liushui['dtTime']=date("Y-m-d H:i:s");
		$liushui['remark']='赠送';
		$liushui['orderInfo']='来自“'.$_SESSION[TB_PREFIX.'user_name'].'”的赠送';
		$liushui['orderId']=0;
		insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	}
	die('{"code":1,"message":"操作成功"}');
}
//注册奖励
function zhuce_jiangli($userId){
	global $db;
	if($_SESSION['if_tongbu']==1){
		$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
		$yaoqing_rules = json_decode($yaoqing_rule);
		add_linpinka_money($userId,$yaoqing_rules->z_dikoujin,'注册/绑定奖励','注册/绑定奖励',0);
		if($yaoqing_rules->yhqId>0){
			//奖励优惠券
			$yhq = $db->get_row("select * from yhq where id=".$yaoqing_rules->yhqId." and comId=10 and status=1");
			if(!empty($yhq)){
				$user_yhq = array();
			  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
			  	$user_yhq['comId'] = 10;
			  	$user_yhq['userId'] = $userId;
			  	$user_yhq['jiluId'] = $yaoqing_rules->yhqId;
			  	$user_yhq['fafangId'] = 0;
			  	$user_yhq['title'] = $yhq->title;
			  	$user_yhq['man'] = $yhq->man;
			  	$user_yhq['jian'] = $yhq->money;
			  	$user_yhq['startTime'] = $yhq->startTime;
			  	$user_yhq['endTime'] = $yhq->endTime;
			  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
			  	$db->insert_update('user_yhq10',$user_yhq,'id');
			  	$db->query("update yhq set hasnum=hasnum+1 where id=".$yaoqing_rules->yhqId);
			}
		}
	}
	$comId = (int)$_SESSION['demo_comId'];
	$reg_gift = $db->get_row("select type,guizes from reg_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1");
	if(!empty($reg_gift)){
		$guizes = json_decode($reg_gift->guizes);
		$yzFenbiao = $fenbiao = getFenbiao($comId,20);
		$money = $guizes[0]->jian;
		switch ($reg_gift->type) {
			case 1:
				$db->query("update users set money=money+$money where id=$userId");
				$liushui = array();
				$liushui['userId']=$userId;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$money;
				$liushui['yue']=$db->get_var("select money from users where id=$userId");
				$liushui['type']=2;
				$liushui['dtTime']=date("Y-m-d H:i:s");
				$liushui['remark']='注册奖励';
				$liushui['orderInfo']='';
				insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			break;
			case 2:
				$db->query("update users set jifen=jifen+$money where id=$userId");
				$jifen_jilu = array();
				$jifen_jilu['userId'] = $userId;
				$jifen_jilu['comId'] = $comId;
				$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
				$jifen_jilu['jifen'] = $money;
				$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
				$jifen_jilu['type'] = 1;
				$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
				$jifen_jilu['remark'] = '注册奖励';
				$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
			break;
			case 3:
				foreach ($guizes as $guize) {
					$yhqId = $guize->yhqId;
					$money = $guize->jian;
					$yhq = $db->get_row("select * from yhq where id=$yhqId and comId=$comId and status=1");
					if(empty($yhq)){
						return false;
					}
					if($yhq->hasNum>=$yhq->num){
						return false;
					}
					for($i=0;$i<$money;$i++){
						$user_yhq = array();
					  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
					  	$user_yhq['comId'] = $comId;
					  	$user_yhq['userId'] = $userId;
					  	$user_yhq['jiluId'] = $yhqId;
					  	$user_yhq['fafangId'] = 0;
					  	$user_yhq['title'] = $yhq->title;
					  	$user_yhq['man'] = $yhq->man;
					  	$user_yhq['jian'] = $yhq->money;
					  	$user_yhq['startTime'] = $yhq->startTime;
					  	$user_yhq['endTime'] = $yhq->endTime;
					  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
					  	$db->insert_update('user_yhq'.$fenbiao,$user_yhq,'id');
					  	$db->query("update yhq set hasnum=hasnum+1 where id=$yhqId");
					}
				}
			break;
		}
	}
}
function share(){
	global $db,$request;
	$inventoryId = (int)$request['inventoryId'];
	$remark = $inventoryId==1?'分享活动':'分享商品';
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$jifen_set = $db->get_row("select if_share,share_jifen,share_limit,share_dikoujin,share_limit_dikoujin from user_shezhi where comId=$comId");
	if($jifen_set->if_share==1){
		$fenbiao = getFenbiao($comId,20);
		$count = $db->get_var("select sum(jifen) from user_jifen$fenbiao where userId=$userId and comId=$comId and remark='$remark' and dtTime>='".date("Y-m-d")."'");
		if($count>=$jifen_set->share_limit && $jifen_set->share_limit>0){
			echo '{"code":1}';exit;
		}else{
			if($comId==10){
				$db_service = getCrmDb();
				$db_service->query("update demo_user set jifen=jifen+$jifen_set->share_jifen where id=$userId");
				$yue = $db_service->get_var("select jifen from demo_user where id=$userId");
			}else{
				$db->query("update users set jifen=jifen+$jifen_set->share_jifen where id=$userId");
				$yue = $db->get_var("select jifen from users where id=$userId");
			}
			$jifen_jilu = array();
			$jifen_jilu['userId'] = $userId;
			$jifen_jilu['comId'] = $comId;
			$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$jifen_jilu['jifen'] = $jifen_set->share_jifen;
			$jifen_jilu['yue'] = $yue;
			$jifen_jilu['type'] = 1;
			$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
			$jifen_jilu['remark'] = $remark;
			$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
		}
	}else if($jifen_set->if_share==2){
		$userId = (int)$_SESSION['demo_zhishangId'];
		$card_id = $db->get_var("select id from gift_card10 where userId=$userId and (endTime is NULL or endTime='0000-00-00') order by id asc limit 1");
		if(!empty($card_id)){
			$count = $db->get_var("select sum(money) from gift_card_liushui10 where cardId=$card_id and dtTime>='".date("Y-m-d")."' and remark='$remark'");
			if($count>=$jifen_set->share_limit_dikoujin && $jifen_set->share_limit_dikoujin>0){
				echo '{"code":1}';exit;
			}
		}
		add_linpinka_money($userId,$jifen_set->share_dikoujin,$remark,'分享商品获得抵扣金',0);
	}
	echo '{"code":1}';
}
//给会员永久抵扣卡充值
function add_linpinka_money($userId,$money,$remark,$info,$daili_id,$fromId=0){
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
		$liushui['userId']=$fromId;
		insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	}	
}
function editfapiao(){
	global $db,$request;
	if($request['tijiao']==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION['demo_comId'];
		if($_SESSION['if_tongbu']==1){
			$comId = 10;
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		}
		$fapiao = array();
		$fapiao['id'] = (int)$db->get_var("select id from user_fapiao where userId=$userId and comId=$comId limit 1");
		$fapiao['userId'] = $userId;
		$fapiao['comId'] = $comId;
		$fapiao['type'] = 1;
		$fapiao['com_title'] = trim($request['com_title']);
		$fapiao['shibiema'] = trim($request['shibiema']);
		$fapiao['address'] = trim($request['address']);
		$fapiao['phone'] = trim($request['phone']);
		$fapiao['bank_name'] = trim($request['bank_name']);
		$fapiao['bank_card'] = trim($request['bank_card']);
		$fapiao['shoupiao_phone'] = trim($request['shoupiao_phone']);
		$fapiao['shoupiao_email'] = trim($request['shoupiao_email']);
		$db->insert_update('user_fapiao',$fapiao,'id');
		redirect('/index.php?p=8&a=editfapiao');
	}
}
function add_user_oprate($content,$type,$uid=0){
	global $db;
	$user_oprate = array();
	$user_oprate['comId'] = (int)$_SESSION['demo_comId'];
	$user_oprate['userId'] = $uid==0?(int)$_SESSION[TB_PREFIX.'user_ID']:$uid;
	$user_oprate['dtTime'] = date("Y-m-d H:i:s");
	$user_oprate['ip'] = getip();
	$user_oprate['terminal'] = 2;
	$user_oprate['content'] = $content;
	$user_oprate['type'] = $type;
	$fenbiao = getFenbiao($user_oprate['comId'],20);
	$db->insert_update('user_oprate'.$fenbiao,$user_oprate,'id');
}
function https_request($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl,CURLOPT_HEADER,0); //
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
    $response = curl_exec($curl);  
    curl_close($curl);
    $jsoninfo = json_decode($response,true); 
    return $jsoninfo;
}

function filtergl($ostr) {//昵称过滤特殊字符
	preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $ostr, $matches);
    $str = join('', $matches[0]);
    if($str==''){
        $returnstr = '';
        $i = 0;
        $str_length = strlen($ostr);
        while ($i<=$str_length){
            $temp_str = substr($ostr, $i, 1);
            $ascnum = Ord($temp_str);
            if ($ascnum>=224){
                $returnstr = $returnstr.substr($ostr, $i, 3);
                $i = $i + 3;
            }elseif ($ascnum>=192){
                $returnstr = $returnstr.substr($ostr, $i, 2);
                $i = $i + 2;
            }elseif ($ascnum>=65 && $ascnum<=90){
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }elseif ($ascnum>=128 && $ascnum<=191){ // 特殊字符
                $i = $i + 1;
            }else{
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }
        }
        $str = $returnstr;
        preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $str, $matches);
        $str = join('', $matches[0]);
    }
    return $str;
}
function GetRandStr($len) {
    $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G","H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R","S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2","3", "4", "5", "6", "7", "8", "9","_","+","-");
	$charsLen = count($chars) - 1;
	shuffle($chars);
	$output = "";
	for ($i=0; $i<$len; $i++){
	     $output .= $chars[mt_rand(0, $charsLen)];
    }
	return $output;
}
//佣金新加
function earn_shengji(){}
function earn_shouyi(){}
function earn_money(){}
function earn_order(){}
function earn_fans(){}
function earn_yaoqing(){}
function to_tuanzhang(){}
function get_shouyi_info(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$today = date("Y-m-d");
	$yesterday = date("Y-m-d",strtotime('-1 day'));
	$month = date("Y-m-01");//本月一号
	$lastmonth = date("Y-m-01",strtotime('-1 day',strtotime($month)));//上个月1号
	$todays = $db->get_row("select count(*) as num,sum(money) as yongjin from user_yugu_shouru where comId=$comId and userId=$userId and dtTime='$today'");
	$yesterdays = $db->get_row("select count(*) as num,sum(money) as yongjin from user_yugu_shouru where comId=$comId and userId=$userId and dtTime='$yesterday'");
	$today_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime='$today'");
	$yestday_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime='$yesterday'");
	$month_chengjiao = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$month'");
	$last_month_chengjiao = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$lastmonth' and dtTime<'$month'");
	$month_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime>='$month'");
	$last_month_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime>='$lastmonth' and qrTime<'$month'");
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['data'] = array();
	$return['data']['today_orders'] = empty($todays->num)?0:$todays->num;
	$return['data']['today_chengjiao'] = empty($todays->yongjin)?0:$todays->yongjin;
	$return['data']['today_jiesuan'] = empty($today_queren)?0:$today_queren;
	$return['data']['yestday_orders'] = empty($yesterdays->num)?0:$yesterdays->num;
	$return['data']['yestday_chengjiao'] = empty($yesterdays->yongjin)?0:$yesterdays->yongjin;
	$return['data']['yestday_jiesuan'] = empty($yestday_queren)?0:$yestday_queren;
	$return['data']['month_chengjiao'] = empty($month_chengjiao)?0:$month_chengjiao;
	$return['data']['month_jiesuan'] = empty($month_queren)?0:$month_queren;
	$return['data']['last_month_chengjiao'] = empty($last_month_chengjiao)?0:$last_month_chengjiao;
	$return['data']['last_month_jiesuan'] = empty($last_month_queren)?0:$last_month_queren;
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_fans_yugu(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$request['id'];
	$month = date("Y-m-01");//本月一号
	$lastmonth = date("Y-m-01",strtotime('-1 day',strtotime($month)));//上个月1号
	if($comId==10){
		$db_service = getCrmDb();
		$zong_shouru = $db_service->get_var("select earn from demo_user where id=$userId");
	}else{
		$zong_shouru = $db->get_var("select earn from users where id=$userId");
	}
	$last_shouru = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$lastmonth' and dtTime<'$month'");
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['zong_shouru'] = $zong_shouru;
	$return['last_shouru'] = empty($last_shouru)?0:$last_shouru;
	echo json_encode($return,true);
	exit;
}
function get_earn_fans(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$scene = (int)$request['scene'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	if($comId==10){
		$db_service = getCrmDb();
		$sql = "select id,image,username,level,dtTime,name as nickname,user_info,if_tuanzhang,cost from demo_user where 1=1";
		switch ($scene) {
			case 1:
				$sql.=" and shangji=$userId";
			break;
			case 2:
				$sql.=" and tuan_id=$userId";
			break;
			default:
				$sql.=" and (shangji=$userId or tuan_id=$userId)";
			break;
		}
		$count = (int)$db_service->get_var(str_replace('id,image,username,level,dtTime,name as nickname,user_info,if_tuanzhang,cost','count(*)',$sql));
		$hasnum = (int)$db->get_var(str_replace('id,image,username,level,dtTime,name as nickname,user_info,if_tuanzhang,cost','count(*)',$sql).' and cost>0');
		$weinum = $count-$hasnum;
		$sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
		$users = $db_service->get_results($sql);
	}else{
		$fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
		$sql = "select id,image,username,level,dtTime,nickname,user_info,cost from users where 1=1";
		switch ($scene) {
			case 1:
				$sql.=" and shangji=$userId";
			break;
			case 2:
				$sql.=" and ".($fanli_type==2?'tuan_id':'shangshangji')."=$userId and id<>$userId";
			break;
			default:
				$sql.=" and (shangji=$userId or ".($fanli_type==2?'tuan_id':'shangshangji')."=$userId) and id<>$userId";
			break;
		}
		$count = (int)$db->get_var(str_replace('id,image,username,level,dtTime,nickname,user_info,cost','count(*)',$sql));
		$hasnum = (int)$db->get_var(str_replace('id,image,username,level,dtTime,nickname,user_info,cost','count(*)',$sql).' and cost>0');
		$weinum = $count-$hasnum;
		$sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
		$users = $db->get_results($sql);
	}
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['hasnum'] = $hasnum;
	$return['weinum'] = $weinum;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$now = time();
	if(!empty($users)){
		foreach ($users as $i=>$pdt) {
			$data = array();
			$data['id'] = $pdt->id;
			if(!empty($pdt->image) && substr($pdt->image,0,4)!='http'){
				$pdt->image = 'https://www.zhishangez.com'.$pdt->image;
			}
			$data['image'] = ispic($pdt->image,'/skins/default/images/wode_1.png');
			$data['phone'] = substr($pdt->username,0,3).'****'.substr($pdt->username,7,4);
			$data['dtTime'] = $pdt->dtTime;
			if($comId==10){
				if($user->if_tuanzhang){
				  $level = '团长';
				}else if(empty($user->level)){
				  $level = '小白购';
				}else{
				  $level = $db->get_var("select title from user_level where id=$user->level");
				}
			}else{
				$level = '会员';
				if(!empty($pdt->level)){
				  $level = $db->get_var("select title from user_level where id=$pdt->level");
				}
			}
			$data['level'] = $level;
			if($comId==10){
				$data['fans'] = (int)$db_service->get_var("select count(*) from demo_user where shangji=$pdt->id or tuan_id=$pdt->id");
			}else{
				$data['fans'] = (int)$db->get_var("select count(*) from users where comId=$comId and shangji=$pdt->id or ".($fanli_type==2?'tuan_id':'shangshangji')."=$pdt->id");
			}
			$data['name'] = $pdt->nickname;
			if(!empty($pdt->user_info)){
				$user_info = json_decode($pdt->user_info,true);
			}
			$data['wxh'] = empty($user_info['wxh'])?'未填写':$user_info['wxh'];
			$data['hasbuy'] = $pdt->cost>0?1:0;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function saomabuy(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION['demo_comId'];
	$canzhuo = $db->get_row("select * from demo_shequ_table where id=$id and comId=$comId and status=1");
	if(empty($canzhuo)){
		die("该桌位暂不支持扫码点餐！");
	}
	$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=".$canzhuo->shequId);
	$_SESSION[TB_PREFIX.'shequ_id'] = $canzhuo->shequId;
	$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
	$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
	$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
	$_SESSION[TB_PREFIX.'table_id'] = $canzhuo->id;
	$_SESSION[TB_PREFIX.'table_title'] = $canzhuo->title;
	redirect('/index.php?p=4&a=channels&peisong_type=4');
}