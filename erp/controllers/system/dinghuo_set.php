<?php
function level(){}
function shoukuan(){}
function xianxia(){}
function index(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$shezhi = array();
		$shezhi['comId'] = $comId;
		$shezhi['kehu_title'] = $request['kehu_title'];
		$shezhi['acc_ifxianjin'] = 1;
		$shezhi['acc_xianjin_pre'] = $request['acc_xianjin_pre'];
		$shezhi['acc_xianjin_name'] = $request['acc_xianjin_name'];
		$shezhi['acc_xianjin_queren'] = empty($request['acc_xianjin_queren'])?0:1;
		$shezhi['acc_ifyufu'] = empty($request['acc_ifyufu'])?0:1;
		$shezhi['acc_yufu_pre'] = $request['acc_yufu_pre'];
		$shezhi['acc_yufu_name'] = $request['acc_yufu_name'];
		$shezhi['acc_yufu_queren'] = empty($request['acc_yufu_queren'])?0:1;
		$shezhi['acc_iffandian'] = empty($request['acc_iffandian'])?0:1;
		$shezhi['acc_fandian_pre'] = $request['acc_fandian_pre'];
		$shezhi['acc_fandian_name'] = $request['acc_fandian_name'];
		$shezhi['acc_fandian_queren'] = empty($request['acc_fandian_queren'])?0:1;
		$shezhi['acc_ifbaozheng'] = empty($request['acc_ifbaozheng'])?0:1;
		$shezhi['acc_baozheng_pre'] = $request['acc_baozheng_pre'];
		$shezhi['acc_baozheng_name'] = $request['acc_baozheng_name'];
		$shezhi['acc_baozheng_queren'] = empty($request['acc_baozheng_queren'])?0:1;
		insert_update('demo_kehu_shezhi',$shezhi,'comId');
		$_SESSION[TB_PREFIX.'kehu_title'] = $shezhi['kehu_title'];
		redirect('?m=system&s=dinghuo_set');
	}
}
function addLevel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$title = $request['title'];
	$zhekou = $request['zhekou'];
	if(empty($id)){
		$ifhas = $db->get_var("select id from demo_kehu_level where comId=$comId and title='$title'");
		if(!empty($ifhas)){
			echo '<script>alert("您已经创建过这个级别了！");history.go(-1);</script>';
			exit;
		}
		$count = $db->get_var("select count(*) from demo_kehu_level where comId=$comId");
		if($count>9){
			echo '<script>alert("最多可以创建10个级别！");history.go(-1);</script>';
			exit;
		}
		$db->query("insert into demo_kehu_level(comId,title,zhekou,ordering,del) value($comId,'$title','$zhekou',0,1)");
	}else{
		$db->query("update demo_kehu_level set title='$title',zhekou='$zhekou' where id=$id and comId=$comId");
	}
	redirect("?m=system&s=dinghuo_set&a=level");
}
function addBank(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$bank_user = $request['bank_user'];
	$bank_name = $request['bank_name'];
	$bank_account = $request['bank_account'];
	if(empty($id)){
		$db->query("insert into demo_kehu_bank(comId,bank_name,bank_user,bank_account,status) value($comId,'$bank_name','$bank_user','$bank_account',1)");
	}else{
		$db->query("update demo_kehu_bank set bank_name='$bank_name',bank_user='$bank_user',bank_account='$bank_account' where id=$id and comId=$comId");
	}
	redirect("?m=system&s=dinghuo_set&a=xianxia");
}
function totop(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$maxOrdering = $db->get_var("select max(ordering) from demo_kehu_level where comId=$comId");
	$maxOrdering+=1;
	$db->query("update demo_kehu_level set ordering=$maxOrdering where id=$id");
	redirect("?m=system&s=dinghuo_set&a=level");
}
function setWeixin(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$type = 1;
	$info = array();
	$info['appid'] = $request['appid'];
	$info['mch_id'] = $request['mch_id'];
	$info['key'] = $request['key'];
	$info['appsecret'] = $request['appsecret'];
	$info['sslkey'] = $request['sslkey'];
	$info['sslcert'] = $request['sslcert'];
	$infostr = json_encode($info,JSON_UNESCAPED_UNICODE);
	$status = (int)$request['status'];
	$ifhas = $db->get_var("select id from demo_kehu_pay where comId=$comId and type=1 limit 1");
	if(empty($ifhas)){
		$db->query("insert into demo_kehu_pay(comId,type,info,status,ifsuccess) value($comId,1,'$infostr',$status,0)");
	}else{
		$db->query("update demo_kehu_pay set info='$infostr',status=$status where id=$ifhas");
	}
	redirect("?m=system&s=dinghuo_set&a=shoukuan");
}
function setWeixin1(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$type = 1;
	$info = array();
	$info['appid'] = $request['appid'];
	$info['mch_id'] = $request['mch_id'];
	$info['key'] = $request['key'];
	$info['appsecret'] = $request['appsecret'];
	$info['sslkey'] = $request['sslkey'];
	$info['sslcert'] = $request['sslcert'];
	$infostr = json_encode($info,JSON_UNESCAPED_UNICODE);
	$status = (int)$request['status'];
	$ifhas = $db->get_var("select id from demo_kehu_pay where comId=$comId and type=3 limit 1");
	if(empty($ifhas)){
		$db->query("insert into demo_kehu_pay(comId,type,info,status,ifsuccess) value($comId,3,'$infostr',$status,0)");
	}else{
		$db->query("update demo_kehu_pay set info='$infostr',status=$status where id=$ifhas");
	}
	redirect("?m=system&s=dinghuo_set&a=shoukuan");
}
function setAlipay(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$type = 2;
	$info = array();
	$info['account'] = $request['account'];
	$info['partnerId'] = $request['partnerId'];
	$info['key'] = $request['key'];
	$info['private_key'] = preg_replace('/((\s)*(\n)+(\s)*)/','',$request['private_key']);
	$info['alipay_public_key'] = preg_replace('/((\s)*(\n)+(\s)*)/','',$request['alipay_public_key']);
	$infostr = json_encode($info,JSON_UNESCAPED_UNICODE);
	$status = (int)$request['status'];
	$ifhas = $db->get_var("select id from demo_kehu_pay where comId=$comId and type=2 limit 1");
	if(empty($ifhas)){
		$db->query("insert into demo_kehu_pay(comId,type,info,status,ifsuccess) value($comId,2,'$infostr',$status,0)");
	}else{
		$db->query("update demo_kehu_pay set info='$infostr',status=$status where id=$ifhas");
	}
	redirect("?m=system&s=dinghuo_set&a=shoukuan");
}
function delLevel(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_kehu_level where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function delBank(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_kehu_bank where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function yibao(){}
function yibao1(){}
function yibao2(){}
function yibao3(){}