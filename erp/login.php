<?php
@session_start();
@error_reporting(E_ALL ^ E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
require(dirname(__FILE__).'/../config/dt-config.php');
require(ABSPATH.'/inc/function.php');
require_once(ABSPATH.'/inc/class.shlencryption.php');
require(ABSPATH.'/inc/class.database.php');
$_REQUEST = cleanArrayForMysql($_REQUEST);
$_GET = cleanArrayForMysql($_GET);
$_POST = cleanArrayForMysql($_POST);

function checkPwd($username,$pwd,$flag=false,$erweima)
{
	global $db;
	//$db_service = getCrmDb();

	$username=get_str($username);
	$sql="SELECT * FROM demo_user WHERE username='$username' and auditing=1 LIMIT 1";
	$rst=$db->get_row($sql);
	if($rst){
		$shlencryption = new shlEncryption($pwd);
		if ($rst->pwd==$shlencryption->to_string() || $rst->pwd==sha1($pwd)) {
			$userId= $rst->id;
			$comId = 888;
			$role = $rst->role;
			$shezhi = $db->get_row("select if_tongbu,if_tongbu_pdt,if_shequ,com_title,com_remark from demo_shezhi where comId=$comId");
			

	
			$db->query("update demo_user set lastlogin='".time()."' where id=$rst->id");
			$_SESSION[TB_PREFIX.'admin_userID'] = $rst->id;
			$_SESSION[TB_PREFIX.'admin_roleId'] = $rst->role;
			$_SESSION[TB_PREFIX.'comId'] = $comId;
			$_SESSION[TB_PREFIX.'departId'] = $rst->department;
			$_SESSION[TB_PREFIX.'admin_name']= $rst->username;
			$_SESSION[TB_PREFIX.'name']=$rst->name;
			$_SESSION[TB_PREFIX.'com_title']=empty($shezhi->com_remark)?$shezhi->com_title:$shezhi->com_remark;
			$_SESSION['if_tongbu']=$shezhi->if_tongbu;
			$_SESSION['if_shequ']=$shezhi->if_shequ;
			$_SESSION['mendianId'] = $rst->mendianId;

            redirect('/erp/index.php');
		}
		else
		{
			echo "<script>alert('帐号或密码有误，请重新输入');</script>";
			redirect('login.php');
		}
	}
	else
	{
		echo "<script>alert('帐号不存在，请重新输入');</script>";
		redirect('login.php');
	}
}
/**
 * 对验证码进行验证
 */
function checkCode($checkcode)
{
	$verifycode=$_SESSION['verifycode'];
	if ($verifycode != $checkcode)
	{
		return false;
	}
	else
	{
		return true;
	}
}
if($_GET['act']=='login')
{
	if($_REQUEST['checkcode']==$_SESSION['verifycode'] && !empty($_REQUEST['checkcode']))
	{
		checkPwd($_REQUEST['username'],$_REQUEST['pwd'],$_REQUEST['remamber'],$_REQUEST['erweima']);
	}
	else
	{
	    echo "<script>alert('验证码错误，请重新输入');</script>";
		redirect('login.php');
	}
}
if($_GET['action']=='logout')
{
	if(!empty($_SESSION[TB_PREFIX.'admin_userID'])){
		$db->query("update demo_user set verify_code='' where id=".$_SESSION[TB_PREFIX.'admin_userID']);
	}
	@session_start();
	@session_destroy();
	@setcookie('username','');
	@setcookie('pwd','');
	redirect('login.php');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理系统</title>
<link href="styles/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<SCRIPT type=text/javascript>
$(function(){
	if (typeof(Worker) !== "undefined"){}else {
     alert("系统检测到您的浏览器版本过低，请使用“360急速浏览器”或“Chrome浏览器”");
   }
});
function erweimas(){
	$("#erweima").attr("src","/inc/verifycode.php?time=<?=time()?>");
}
document.onkeydown=function(event){
  var e = event || window.event || arguments.callee.caller.arguments[0];
  if(e && e.keyCode==13){ // enter 键
      $("#form1").submit();
  }
}; 
</SCRIPT>
</head>
<body>
<div class="login">
	<div class="login_cont">
		<div class="login_title">登录</div>
		<form id="form1" name="form1" method="post" action="?act=login">
		<div class="login_form">
			<div class="login_line">
				<div class="login_line_left">
					管理员账号
				</div>
				<div class="login_line_right">
					<input type="text" name="username" placeholder="请填写账号">
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="login_line">
				<div class="login_line_left">
					管理员密码
				</div>
				<div class="login_line_right">
					<input type="password" name="pwd"  placeholder="请填写密码">
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="login_line">
				<div class="login_line_left">
					验证码
				</div>
				<div class="login_line_right">
					<input type="text" name="checkcode" placeholder="请填写验证码" style="width:100px;float:left;">
					<img src="/inc/verifycode.php" id="erweima" style="width:112px;height:36px;float:left;" onclick="erweimas();">
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="login_confirm">
				<div class="login_btn" onclick="$('#form1').submit();">
					登录
				</div>
			</div>
		</div>
	</form>
	</div>
	<div style="clear:both"></div>
</div>
</body>
</html>