<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$pay_info = $db->get_var("select pay_info from demo_shops where comId=$comId");
$info = json_decode($pay_info);
$requestNo = $request['requestNo'];
if(empty($requestNo)){
	$requestNo = "DS" . date("ymd_His") . rand(10, 99);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style type="text/css">
		table tr{height:50px;}
		.layui-form input{height: 38px;width:495px;border-color: #e6e6e6;
		    line-height: 1.3;
		    line-height: 38px;
		    border-width: 1px;
		    border-style: solid;
		    background-color: #fff;
		    border-radius: 2px;padding-left:5px;
		}
    	.layui-form-select{width:500px;display:inline-block;}
    	.layui-form-select .layui-input {padding-right: 30px;cursor: pointer;width: 500px;}

	</style>
</head>
<body>
	<div class="spshezhi">
		<div class="spshezhi_1">
			<img src="images/biao_35.png"> 个人注册
		</div>
		<div>
			<form method="post" action="/yop-api/sendPersonreginfoadd.php" targe="_blank" class="layui-form">
			<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:20px;">
				<tr style="display:none">
					<td align="left">入网请求号</td>
					<td align="left"><input size="50" type="text" name="requestNo" lay-verify="required" id="requestNo"  value="<?php echo $requestNo; ?>"/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>

				<tr>
					<td align="left">商户品牌名称/简称</td>
					<td align="left"><input size="50" type="text" name="merShortName" id="merShortName" lay-verify="required" value=""/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">法人姓名</td>
					<td align="left"><input size="50" type="text" name="legalName" lay-verify="required" id="legalName"  value=""/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">法人身份证号</td>
					<td align="left"><input size="50" type="text" name="legalIdCard" lay-verify="required" id="legalIdCard"  value=""/> 
					&nbsp;<span style="color:#FF0000;font-weight:100;">*</span></td>
				</tr>
				<tr>
					<td align="left">商户联系人手机</td>
					<td align="left"><input size="50" type="text" name="merLegalPhone" lay-verify="required" id="merLegalPhone"  value=""/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">商户联系人邮箱</td>
					<td align="left"><input size="50" type="text" name="merLegalEmail" lay-verify="required" id="merLegalEmail"  value=""/> 
					&nbsp;<span style="color:#FF0000;font-weight:100;">*</span></td>
				</tr>
				<tr>
					<td align="left">商户一级分类编码</td>
					<td align="left"><input size="50" type="text" name="merLevel1No" lay-verify="required" id="merLevel1No" value="" />		  		
						&nbsp;<span style="color:#FF0000;font-weight:100;">*<br><a href="images/新商户一二级分类.xlsx" style="color:red" target="_blank">下载分类编码</a></span>
					</td>
				</tr>
				<tr>
					<td align="left">商户二级分类编码</td>
					<td align="left"><input size="50" type="text" name="merLevel2No" lay-verify="required" id="merLevel2No"  value=""/> 
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">商户经营地址所在省编码</td>
					<td align="left"><input size="50" type="text" name="merProvince" lay-verify="required" id="merProvince"  value=""/>
						&nbsp;<span style="color:#FF0000;font-weight:100;">*<br><a href="images/省市区编码.xlsx" style="color:red" target="_blank">下载省市区编码</a></span>
					</td>
				</tr>

				<tr>
					<td align="left">商户经营地址所在市编码</td>
					<td align="left"><input size="50" type="text" name="merCity" lay-verify="required" id="merCity"  value=""/> 
						&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>

				<tr>
					<td align="left">商户经营地址所在区编码</td>
					<td align="left"><input size="50" type="text" name="merDistrict" lay-verify="required" id="merDistrict" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span> </td>
				</tr>

				<tr>
					<td align="left">商户经营地具体地址</td>
					<td align="left"><input size="50" type="text" name="merAddress" id="merAddress" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span> </td>
				</tr>
				<tr>
					<td align="left">商户经营范围</td>
					<td align="left"><input size="50" type="text" name="merScope" id="merScope" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span> </td>
				</tr>
				<tr>
					<td align="left">银行账户</td>
					<td align="left"><input size="50" type="text" name="cardNo" id="cardNo" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">开户银行总行编码</td>
					<td align="left"><input size="50" type="text" name="headBankCode" id="headBankCode" value="" lay-verify="required" />		  		
						&nbsp;<span style="color:#FF0000;font-weight:100;">*<br><a href="images/易宝银行编号表.xlsx" style="color:red" target="_blank">下载银行编码表</a></span>
					</td>
				</tr>
				<tr>
					<td align="left">开户银行编码</td>
					<td align="left"><input size="50" type="text" name="bankCode" id="bankCode" value="" lay-verify="required" />		  		
						&nbsp;<span style="color:#FF0000;font-weight:100;">*<br><a href="/yop-api/bankBranchInfo.php" style="color:red" target="_blank">编码查询</a></span>
					</td>
				</tr>
				<tr>
					<td align="left">开户省编码</td>
					<td align="left"><input size="50" type="text" name="bankProvince" id="bankProvince" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*<br><a href="images/省市区编码.xlsx" style="color:red" target="_blank">下载省市区编码</a></span>
					</td>
				</tr>
				<tr>
					<td align="left">开户市编码</td>
					<td align="left"><input size="50" type="text" name="bankCity" id="bankCity" lay-verify="required" value=""/>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr>
					<td align="left">法人身份证正面</td>
					<td align="left">
						<img src="<?=ispic($shezhi->com_logo)?>" id="upload1" width="100" style="cursor:pointer;">
						&nbsp;<span style="color:#FF0000;font-weight:100;">* 1M以内</span>
						<input type="hidden" id="IDCARD_FRONT" name="IDCARD_FRONT" lay-verify="required">
					</td>
				</tr>
				<tr>
					<td align="left">法人身份证反面</td>
					<td align="left">
						<img src="/inc/img/nopic.svg" id="upload2" width="100" style="cursor:pointer;">
						&nbsp;<span style="color:#FF0000;font-weight:100;">* 1M以内</span>
						<input type="hidden" id="IDCARD_BACK" name="IDCARD_BACK" lay-verify="required">
					</td>
				</tr>
				<tr>
					<td align="left">结算银行卡</td>
					<td align="left">
						<img src="/inc/img/nopic.svg" id="upload9" width="100" style="cursor:pointer;">
						&nbsp;<span style="color:#FF0000;font-weight:100;">1M以内</span>
						<input type="hidden" id="SETTLE_BANKCARD" name="SETTLE_BANKCARD" lay-verify="required">
					</td>
				</tr>
				<tr>
					<td align="left">法人手持身份证</td>
					<td align="left">
						<img src="/inc/img/nopic.svg" id="upload8" width="100" style="cursor:pointer;">
						&nbsp;<span style="color:#FF0000;font-weight:100;">* 1M以内</span>
						<input type="hidden" id="HAND_IDCARD" name="HAND_IDCARD" lay-verify="required">
					</td>
				</tr>
				<tr>
					<td align="left">法人手持银行卡</td>
					<td align="left">
						<img src="/inc/img/nopic.svg" id="upload10" width="100" style="cursor:pointer;">
						&nbsp;<span style="color:#FF0000;font-weight:100;">* 1M以内</span>
						<input type="hidden" id="HAND_BANKCARD" name="HAND_BANKCARD" lay-verify="required">
					</td>
				</tr>
				<tr style="display:none">
					<td align="left">开通产品</td>
					<td align="left"><textarea id="productInfo" style="width: 69%;" name="productInfo" rows="5" ></textarea>	&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
				</tr>
				<tr style="display:none">
					<td align="left">资质影印件</td>
					<td align="left"><textarea id="fileInfo" style="width: 69%;" rows="5"  > [{"quaType":"HAND_IDCARD",
						"quaUrl":"http://attachment.yeepay.com/yop/201707/446ae3e115474f1e85bc91a9a444fe37.JPG"},
						{"quaType":"SETTLE_BANKCARD",
						"quaUrl":"http://attachment.yeepay.com/yop/201707/446ae3e115474f1e85bc91a9a444fe37.JPG"},
						{"quaType":"IDCARD_BACK",
						"quaUrl":"http://attachment.yeepay.com/yop/201707/446ae3e115474f1e85bc91a9a444fe37.JPG"},
						{"quaType":"IDCARD_FRONT",
					"quaUrl":"http://attachment.yeepay.com/yop/201707/446ae3e115474f1e85bc91a9a444fe37.JPG"}]</textarea>&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
				</td>
			</tr>
			<tr style="display:none">
				<td align="left">业务功能</td>
				<td align="left"><textarea id="businessFunction" class="layui-textarea" style="width: 69%;" name="businessFunction" rows="5"  > </textarea></td>
			</tr>

			<tr style="display:none">
				<td align="left">商户回调地址</td>
				<td align="left"><input size="50" type="text" name="notifyUrl" id="notifyUrl" value="http://buy.zhishangez.com/yop-api/callback.php" />		  		
					&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
				</td>
			</tr>
			<tr style="display: none">
				<td align="left">授权类型</td>
				<td align="left"><input size="50" type="text" name="merAuthorizeType" id="merAuthorizeType" value="SMS_AUTHORIZE" />		  		
					&nbsp;<span style="color:#FF0000;font-weight:100;">*</span>
				</td>
			</tr>
			<tr>
				<td align="left">&nbsp;</td>
				<td align="left"><button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button></td>
			</tr>
	</table>
	</form>
</div>
</div>
<script type="text/javascript" src="js/shezhi/yibao.js"></script>
</body>
</html>