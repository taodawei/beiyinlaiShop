<?php
die;
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials:true');
header("Access-Control-Allow-Headers:X-PINGOTHER,X-Requested-With,Content-Type,Accept");
header("Access-Control-Allow-Methods:GET,POST");

include("index.html");exit;
session_start();
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
@ini_set("session.cookie_httponly", 1);
//根据域名获取comId
$domain = $_SERVER['HTTP_HOST'];
if($domain=='buy.zhishangez.com'){
	$comId = 10;
}else{
	if(strpos($domain,'.buy.')>0){
		$arr = explode('.buy.',$domain);
		$comId = (int)$arr[0];
	}else{
		$domain = str_replace('www.','',$domain);
		$comId = (int)@file_get_contents('config/domains/'.$domain.'.txt');
	}
}
if(empty($comId)){
	die('域名未绑定，请登录后台绑定！');
}
//加载配置
$init = 0;//加载企业信息
if($comId!=$_SESSION['demo_comId']){
	session_destroy();session_start();
	$_SESSION['demo_comId'] = $comId;
	require('config/dt-config.php');
	require_once(ABSPATH.'/inc/class.database.php');
	require_once(ABSPATH.'/inc/function.php');
	$shezhi = $db->get_row("select com_title,com_logo,com_remark,com_back,if_tongbu,if_shequ from demo_shezhi where comId=$comId");
	if(empty($shezhi)){
		die('店铺不存在或已关闭！');
	}
	if(empty($shezhi->com_title)){
		$db_service = new DtDatabase1();
		$company = $db_service->get_row("select com_title,com_logo from demo_company where id=$comId");
		if(substr($company->com_logo,0,4)!='http'){
			$company->com_logo = 'https://www.zhishangez.com/'.$company->com_logo;
		}
		$_SESSION['demo_com_title'] = $company->com_title;
		$_SESSION['demo_com_logo'] = $company->com_logo;
		$_SESSION['demo_com_back'] = '';
	}else{
		$_SESSION['demo_com_title'] = $shezhi->com_title;
		$_SESSION['demo_com_logo'] = $shezhi->com_logo;
		$_SESSION['demo_com_remark'] = $shezhi->com_remark;
		$_SESSION['demo_com_back'] = $shezhi->com_back;
	}
	$_SESSION['if_tongbu'] = (int)$shezhi->if_tongbu;
	$_SESSION['if_shequ'] = (int)$shezhi->if_shequ;
	$username = $_COOKIE["dt_username_$comId"];
	$pwd = $_COOKIE["dt_pwd_$comId"];
	if(!empty($username)){
		if($comId==10){
			$db_service = new DtDatabase1();
			$sql="SELECT id,name,level,pwd,status,if_shequ_tuan FROM demo_user WHERE username='$username' LIMIT 1";
			$rst=$db_service->get_row($sql);
			if($rst){
				if ($rst->pwd==$pwd){
					$_SESSION[TB_PREFIX.'user_name'] = $rst->name;
					$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
					$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
					$_SESSION[TB_PREFIX.'zhishangId'] = $rst->id;
					$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
					$db_service->query("update demo_user set lastlogin='".time()."' where id=$rst->id");
					$cookieTime =60*60*24*30;
					setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
					setcookie('dt_pwd_'.$comId,$rst->pwd,time()+$cookieTime, '/');
					$shouhuo = $db->get_row("select id,areaId from user_address where userId=$rst->id and comId=$comId order by moren desc limit 1");
					$_SESSION[TB_PREFIX.'sale_area'] = (int)$shouhuo->areaId;
					$_SESSION[TB_PREFIX.'address_id'] = (int)$shouhuo->id;
					$user_oprate = array();
					$user_oprate['comId'] = (int)$_SESSION['demo_comId'];
					$user_oprate['userId'] = $rst->id;
					$user_oprate['dtTime'] = date("Y-m-d H:i:s");
					$user_oprate['ip'] = getip();
					$user_oprate['terminal'] = 2;
					$user_oprate['content'] = '登录';
					$user_oprate['type'] = 1;
					$fenbiao = getFenbiao($user_oprate['comId'],20);
					$db->insert_update('user_oprate'.$fenbiao,$user_oprate,'id');
				}
			}
		}else{
			$sql="SELECT id,nickname,level,password,status,city,zhishangId,shequ_id,if_shequ_tuan FROM users WHERE comId=$comId and username='$username' LIMIT 1";
			$rst=$db->get_row($sql);
			if($rst){
				if($rst->status!=1){
					die("您的账号已禁用，请联系管理员！");
				}
				if ($rst->password==$pwd){
					$_SESSION[TB_PREFIX.'user_name'] = $rst->nickname;
					$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
					$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
					$_SESSION[TB_PREFIX.'zhishangId'] = $rst->zhishangId;
					$_SESSION['if_shequ_tuan'] = $rst->if_shequ_tuan;
					$db->query("update users set lastlogin='".date("Y-m-d H:i:s")."' where id=$rst->id");
					$cookieTime =60*60*24*30;
					setcookie('dt_username_'.$comId,$username,time()+$cookieTime, '/');
					setcookie('dt_pwd_'.$comId,$rst->password,time()+$cookieTime, '/');
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
				}
			}
		}
	}
}else{
	require('config/dt-config.php');
	require_once(ABSPATH.'/inc/class.database.php');
	require_once(ABSPATH.'/inc/function.php');
}
/*if($comId==1041){
	$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=10");
	$_SESSION[TB_PREFIX.'shequ_id'] = 10;
	$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
	$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
	$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
}*/
$_REQUEST = cleanArrayForMysql($_REQUEST);
$request  = $_REQUEST;
if(!empty($request['tuijianren'])){
	$_SESSION['tuijianren'] = (int)$request['tuijianren'];
}
if($comId==1121 && empty($_SESSION['demo_user_ID'])){
	$no_login_actions = ['login','login1','reg','wxlogin','wxlogin_com','bindwx','sendSms','findMima','sendSms1','sendSms2','send_login_msg','msg_login','reg_nobind','reg_yaoqing'];
	if(!in_array($request['a'],$no_login_actions)){
		redirect('/index.php?p=8&a=login');
	}
}
$stylename = $db->get_var("select moban from demo_shezhi where comId=$comId");
if(empty($stylename))$stylename='default';
define('STYLENAME',$stylename);
$pfileName = $request['f'];
if(!empty($pfileName) && empty($request['p']))$request['p'] = getIdByMenuName($pfileName);
$params['id']		=	$request['p']		=isset($request['p'])?intval($request['p']):0;
$menu_arr=get_model_type($params['id']);
$params['model']	=	empty($request['m'])?$menu_arr['type']:$request['m'];
$request['a']		=	!isset($request['a'])?'':$request['a'];
$params['action']	=	empty($request['a'])?'index':$request['a'];
$params['related_common']=	empty($menu_arr['related_common'])?$params['model']:$menu_arr['related_common'];
$controller=ABSPATH.'/content/'.$params['model'].'/index.php';
if(is_file($controller))
{
	require_once($controller);
	empty($params['action'])?index():(function_exists($params['action'])?$params['action']():exit("无此Action #".RemoveXSS($params['action'])." 或栏目类型已被修改"));
}
$part_path=ABSPATH.'/skins/'.STYLENAME.'/';
$part_common_path=ABSPATH.'/skins/'.STYLENAME.'/common/';
$loadSkinIndex=$part_path.'index.php';
$loadSkinCommon=$part_path.'common.php';
$loadSkinOtherCommon=$part_common_path.$params['related_common'];
switch ($params['related_common'])
{
	case 'index':
		if(is_file($loadSkinIndex)){
		  require_once($loadSkinIndex);
		}
		break;
	default:
		$defaultSkin = $_SESSION['if_shequ']==1?'shequ':'default';
		if(is_file($loadSkinOtherCommon)) 
			require_once($loadSkinOtherCommon);
		elseif(is_file(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$loadSkinOtherCommon)))
			require_once(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$loadSkinOtherCommon));
		elseif(is_file($loadSkinCommon))
			require_once($loadSkinCommon);
		elseif(is_file(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$loadSkinCommon)))
			require_once(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$loadSkinCommon));
		else
			exit ('<span style="color:RED"><strong>pager error!</strong></span>');
		break;
}

function sys_layout_part($style='')
{
	global $request,$params,$tag,$path,$data;
	if(!empty($style)) $style = '_'.$style;
	$defaultSkin = $_SESSION['if_shequ']==1?'shequ':'default';//社区购默认模板shequ,新零售默认模板default
	$part_path=ABSPATH.'/skins/'.STYLENAME.'/parts/'.$params['model'].'_'.$params['action'].$style.'.php';
	
	if(is_file($part_path)){
		require_once($part_path);
	}else if(is_file(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$part_path))){
		require_once(str_replace('skins/'.STYLENAME,'skins/'.$defaultSkin,$part_path));
	}else{
		if($defaultSkin=='shequ' && is_file(str_replace('skins/'.STYLENAME,'skins/default',$part_path))){
			require_once(str_replace('skins/'.STYLENAME,'skins/default',$part_path));
		}else{
			echo '<span style="color:RED"><strong>error page!</strong></span>';
		}
	}
}
function get_model_type($id)
{
	global $db;
	if($id==0)
	return array('type'=>'index','level'=>0);
	else
	return $db->get_row("SELECT related_common,type FROM ".TB_PREFIX."menu WHERE id=$id",ARRAY_A);
}
function getIdByMenuName($file)
{
	global $db;
	$sql="SELECT id FROM ".TB_PREFIX."menu WHERE menuName='$file'";
	return $db->get_var($sql);
}