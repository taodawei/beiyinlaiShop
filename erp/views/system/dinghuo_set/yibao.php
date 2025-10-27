<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$pay_info = $db->get_var("select pay_info from demo_shops where comId=$comId");
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
		.churukushezhi_01_down ul li{height:45px;line-height:45px;}
		.churukushezhi_01_down_1{width:140px;float:left;text-align:right;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2{display:inline-block;padding-left:10px;line-height:45px;font-size:13px;color:#333}
		.churukushezhi_01_down_2 a{font-weight:bold;font-size:16px;margin-right: 20px;}
	</style>
</head>
<body>
	<div class="spshezhi">
		<div class="spshezhi_1">
			<img src="images/biao_35.png"> 支付设置
		</div>
		<?
		if(!empty($pay_info)){
			$info = json_decode($pay_info);
			if(!empty($info->merchantNo)&&$request['re']!=1){
				?><iframe src="/yop-api/sendRegstatusquery.php?merchantNo=<?=$info->merchantNo?>" border="0" frameborder="no" width="100%" height="500"></iframe><?
				exit;
			}
		}
		?>
		<div class="spshezhi_2" style="padding-top:0px;">
			<div class="churukushezhi_01">
				<div class="spshezhi_2_up">
					<span>支付申请</span>
				</div>
				<div class="churukushezhi_01_down">
					<ul>
						<li style="height:auto;">
							<div class="churukushezhi_01_down_1">
								选择商户类型 ：
							</div>
							<div class="churukushezhi_01_down_2">
								<a href="?s=dinghuo_set&a=yibao1&requestNo=<?=$request['requestNo']?>">企业</a>
								<a href="?s=dinghuo_set&a=yibao2&requestNo=<?=$request['requestNo']?>">个体工商户</a>
								<a href="?s=dinghuo_set&a=yibao3&requestNo=<?=$request['requestNo']?>">个人</a>
							</div>
							<div class="clearBoth"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
</html>