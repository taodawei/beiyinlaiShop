<?php
session_start();
error_reporting(E_ERROR);
require('config/dt-config.php');
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$_REQUEST = cleanArrayForMysql($_REQUEST);
$request  = $_REQUEST;
$comId = (int)$request['comId'];
$zhishangId = (int)$_SESSION['demo_zhishangId'];
$accept = (int)$request['accept'];
$url = urlencode($request['url']);
//$db_service = getCrmDb();
$private = '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC9/XDVPZVmL4rYH33/XckePYxCSZZQOhUprp00+LINxyhRvoC+
7muE0sKh7DmqO6DI6YobZDE8mz9rPmsPKNEdQMlyXWMX0P5xaFCTcJ514C75DPtj
SYaDd0USk7WEk5i0qsVUb2DO3Y+2KbgClxHXEAQI6vD63oWB/mxfEuhZlwIDAQAB
AoGAcNtl9SWZ45OGNI+wdsstNut0r8OqqIl4HMR+2gKJMioFx1kUfVZ/Q+02dJ0w
O/Ejt3US9uZzYP8PkxMG4YBrhp6ubmtVPjmTXjIW3ocBadwvcd2YOd49GMdFCosu
wMQTD2QjfC8bJTp3sY8HIn3gwDbyZr/WTaUpRVdU1WrBhUECQQD7zTTXtwPdFXnS
Qw1YtPusDs946noyDRJxqsHHO6OPUhK9X8KLyYscCrkYGR3LZiie94JG/N9dJKc6
KZsgoWDxAkEAwShlmOcAPvhX1ayeARSzoIcFu5zqQlfK32XhbX+S9IwoEWMzT3rG
kP8TnpPFPNwvR1qIBRKfgB914AlJfhnjBwJAVQ6afQvLeFEa15Xi2kY4hYRzPQsn
v+R+iHr//kb9FxrITcQdOY8ZOJ2+rI8/a0fVDO3ayhP9d7875f/L8RfSYQJAORei
qAYnWXV4KM0jyrf+vAUM2b5ws3lVmqB3eDEME8JVmYYTxXtJs9PhTa7pzqpaQyHs
MGunv2wNIFI+acpnAwJBAImoJcj96h47EbVniiAsv4IYYhvrKGAemWekVVkdSn92
DsGFriOLAwV/CVud0QzHj1AiIsjiWCxv6MnaOBYbcGE=
-----END RSA PRIVATE KEY-----';
$public = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC9/XDVPZVmL4rYH33/XckePYxC
SZZQOhUprp00+LINxyhRvoC+7muE0sKh7DmqO6DI6YobZDE8mz9rPmsPKNEdQMly
XWMX0P5xaFCTcJ514C75DPtjSYaDd0USk7WEk5i0qsVUb2DO3Y+2KbgClxHXEAQI
6vD63oWB/mxfEuhZlwIDAQAB
-----END PUBLIC KEY-----';
//accept=0跳转,accept=1是跳转过来后同步会员信息
if($accept==0){
	$tourl = 'http://'.$comId.'.buy.zhishangez.com/toshop.php?accept=1&comId='.$comId;
	if($comId==10)$tourl='http://buy.zhishangez.com/toshop.php?accept=1&comId=10';
	if($zhishangId!=0){
		$now = time();
		$now = round($now/10);
		$verifyCode=$now.'|'.$zhishangId;
		$pu_key = openssl_pkey_get_private($private);
		openssl_private_encrypt($verifyCode,$output,$pu_key);
		$verifyCode =base64_encode($output);
		$tourl .='&verifyCode='.$verifyCode;
	}
	redirect($tourl.'&url='.$url);
}else{
	$verifyCode = $request['verifyCode'];
	if($verifyCode){
		$verifyCode = str_replace(' ', '+', $verifyCode);
		$verifyCode = base64_decode($verifyCode);
		$pi_key =  openssl_pkey_get_public($public);
		openssl_public_decrypt($verifyCode,$decrypted,$pi_key);
		if(!empty($decrypted)){
			$keys = explode('|',$decrypted);
			$now = time();
			$now = round($now/10);
			//验证验证，验证时间戳（30秒内有效）和传过来的comId与dt-config中的comId进行对比
			if(abs($now-$keys[0])<=20){

				$zhishangId= (int)$keys[1];
				$db_service = getCrmDb();
				if($comId==10){
					$rst = $db_service->get_row("select username,name,level,pwd,status from demo_user where id=$zhishangId");
					$_SESSION[TB_PREFIX.'user_name'] = $rst->name;
					$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
					$_SESSION[TB_PREFIX.'user_ID'] = $zhishangId;
					$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
				}else{
					$rst = $db->get_row("select id,nickname,level,password,status,city,zhishangId from users where zhishangId=$zhishangId and comId=$comId limit 1");
					if(empty($rst)){
						$db_service = getCrmDb();
						$rst = $db_service->get_row("select username,name,level,pwd,status from demo_user where id=$zhishangId");
						$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
						$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,zhishangId) value($comId,'$rst->name','$rst->username','$rst->pwd',0,0,$level,'".date("Y-m-d H:i:s")."',1,$zhishangId)");

						$userId = $db->get_var("select last_insert_id();");
						$_SESSION[TB_PREFIX.'user_name'] = $rst->name;
						$_SESSION[TB_PREFIX.'user_level'] = $level;
						$_SESSION[TB_PREFIX.'user_ID'] = $comId==1009?$zhishangId:$userId;
						$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
					}else{
						$_SESSION[TB_PREFIX.'user_name'] = $rst->nickname;
						$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
						$_SESSION[TB_PREFIX.'user_ID'] = $comId==1009?$rst->zhishangId:$rst->id;
						$_SESSION[TB_PREFIX.'zhishangId'] = $rst->zhishangId;
					}
					$_SESSION[TB_PREFIX.'tongbu_menu'] = 1;
				}
				
			}
		}
	}
	$url = empty($request['url'])?'/':urldecode($request['url']);
	redirect($url);
}