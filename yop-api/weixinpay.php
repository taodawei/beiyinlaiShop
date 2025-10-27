<?php
$jsApiParameters = file_get_contents('weixinpay.txt');
$url = '/index.php?p=8&a=qianbao';
$website = '';
$shoukuanfang = '知商购-企业自主电商平台';
?>
<html>
<head>
	<title>微信支付</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/> 
	<link href="/skins/erp_zong/styles/common.css" rel="stylesheet" type="text/css">
	<link href="/skins/erp_zong/styles/zhifu.css" rel="stylesheet" type="text/css">
	<script src="/skins/resource/scripts/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:ok'){
					location.href='<?=$website.$url?>';
				}else{
					//alert(res.err_msg);
					location.href='<?=$website?>/index.php?p=8';//支付失败返回订单列表
				}
			}
			);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}
</script>
</head>
<body style="background-color:#f6f6f6;">
<div class="zhifu">
	<div class="zhifu_1">
    	支付
        <div class="zhifu_1_left" onclick="history.go(-1);">
        	取消
        </div>
    </div>
	<div class="zhifu_2">
        <h2>￥<?=$_REQUEST['money']?></h2>
    </div>
	<div class="zhifu_3">
    	<div class="zhifu_3_left">
        	收款方
        </div>
    	<div class="zhifu_3_right">
        	<?=$shoukuanfang?>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="zhifu_4">
    	<a href="javascript:void(0);" onClick="callpay()"><img src="/skins/erp_zong/images/pay_1.png" /></a>
    </div>
</div>
</body>
</html>