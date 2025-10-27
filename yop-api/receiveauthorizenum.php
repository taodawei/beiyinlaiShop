<?php
session_start();
include 'conf.php';
$_SESSION['fasong']=1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="/erp/js/jquery.min.js"></script>
</head>
<body>
	<form method="post" action="sendReceiveauthorizenum.php" targe="_blank">
		<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" style="border:solid 1px #107929">
			<tr>
				<td><table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
				</tr>
				<tr>
					<td colspan="2" bgcolor="#CEE7BD">短信授权码确认</td>
				</tr>
				<tr>
					<td align="left">&nbsp;&nbsp;商户编号</td>
					<td align="left">&nbsp;&nbsp;<input size="50" type="text" name="merchantNo" id="merchantNo" readonly="true" style="height:25px;" value="<?php echo $_REQUEST['merchantNo'];?>"/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">&nbsp;&nbsp;手机号</td>
					<td align="left">&nbsp;&nbsp;<input size="50" type="text" name="phone" id="phone" readonly="true" style="height:25px;" value="<?php echo $_REQUEST['phone'];?>"/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span></td>
					</tr>
					<tr>
						<td align="left">&nbsp;&nbsp;授权码</td>
						<td align="left">&nbsp;&nbsp;<input size="50" type="text" name="merAuthorizeNum" style="height:25px;width:100px" id="merAuthorizeNum"  value=""/>
							&nbsp;<span style="color:#FF0000;font-weight:100;cursor:pointer;" id="yzmBtn" onclick="getYzm();">重新发送授权码</span>
						</td>
					</tr>

					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;&nbsp;<input type="submit" value="submit" value="提交" /></td>
					</tr>
					
					<tr>
						<td height="5" bgcolor="#6BBE18" colspan="2"></td>
					</tr>
				</table></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
	function getYzm(){
	  var phone = $("#phone").val();
	  $.ajax({
			type:"get",
			url:"sendAuthorizenum.php?phone="+phone,
			timeout:"4000",
			dataType:"text",
			success: function(html){
				alert("发送成功，如果二分钟之内没有接收到请刷新重试。");
			  	$("#yzmBtn").attr("onclick","");
			  	$("#yzmBtn").html("已发送");
			},
			error:function(html){
			}
		});
	}
</script>
</body>
</html>
