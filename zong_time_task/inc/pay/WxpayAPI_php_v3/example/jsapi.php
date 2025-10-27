<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
//echo dirname(__FILE__);
//echo 456;
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
global $db,$orders;
//初始化日志
$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//file_put_contents('cccccc.txt',serialize($_SESSION));exit;
//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//echo 3333;
//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();
//var_dump($orders);
//处理网站订单数据

/*处理留言信息 Start*/
$rmk = str_replace(',', '，', $_SESSION[TB_PREFIX.'pay_orderInfo']['remark']);
/*处理留言信息 End*/

$basket['productinfo']=unserialize($orders->orederinfo);
if($basket['productinfo']){
	foreach($basket['productinfo'] as $k=>$v){
		$subject[] =$v['title'];
		$price[]   =$v['sellingPrice'].' x '.$v['num'];
		$body[]    =$v['title'].' * '.$v['num'].' * '.$v['spec'];
		$spec = $v['spec'];
		$val_ids = $v['val_ids'];
	}
	$subject = @implode('<@>',$subject);
	$subject = strtr($subject,'<@>',' + ');
	$price   = @implode('<@>',$price);
	$body    = @implode('<@>',$body);
}else{
	$subject = "微信1支付";
	$price   = "0.01";
	$body    = "微信1付款";
}
$body = sys_substr($body,40,false);
$subject = sys_substr($subject,40,false);

$pay_price = ($orders->payprice)*100;

$dtTime = date("YmdHis",strtotime($orders->dtTime));
$expireTime = date("YmdHis", time() + 60*60*24);


/*
$_SESSION[TB_PREFIX.'pay_orderId'] = $orderId;//订单号
$_SESSION[TB_PREFIX.'pay_orderInfo']['id'] = $request['id'];//产品id号
$_SESSION[TB_PREFIX.'pay_orderInfo']['t'] = $request['t'];//所参团号
$_SESSION[TB_PREFIX.'pay_orderInfo']['shr'] = $shr;//收货人信息
$_SESSION[TB_PREFIX.'pay_orderInfo']['orederinfo'] = $ddxx;//订单产品详细信息
$_SESSION[TB_PREFIX.'pay_orderInfo']['zhanzhang'] = $zhanzhang;//商家id
$_SESSION[TB_PREFIX.'pay_orderInfo']['payprice'] = ($tInfo->ptprice*$request['num']);//支付金额
$_SESSION[TB_PREFIX.'pay_orderInfo']['payType'] = $request['payType'];//支付方式
$_SESSION[TB_PREFIX.'pay_orderInfo']['remark'] = $request['remark'];//备注信息
*/

$str = array(
	$_SESSION[TB_PREFIX.'pay_orderInfo']['id'],
	$_SESSION[TB_PREFIX.'pay_orderInfo']['t'],
	$_SESSION[TB_PREFIX.'pay_orderInfo']['shrid'],
	$_SESSION[TB_PREFIX.'pay_orderInfo']['zhanzhang'],
	$_SESSION[TB_PREFIX.'user_ID']
);
$fhstr = implode(',',$str);

$lsData = array(
	'remark'=>$rmk,
	'num' =>$_SESSION[TB_PREFIX.'pay_orderInfo']['num'],
	'spec'=>$spec,
	'val_ids'=>$val_ids
);
$lsData = serialize($lsData);
//$ycaid = $db->get_var("select id from demo_linshi where orderId='".$orders->orderId."' limit 1");
//if(empty($ycaid)){//临时记录
$db->query("insert into demo_linshi (orderId, userid, info, dtTime) values ('".$orders->orderId."', ".$_SESSION[TB_PREFIX.'user_ID'].", '".$lsData."', '".date("Y-m-d H:i:s")."')");
//}



//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
$input->SetAttach($fhstr);//自定义数据
$input->SetOut_trade_no($orders->orderId);
$input->SetTotal_fee($pay_price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire($expireTime);
$input->SetGoods_tag($subject);
$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify.php");
$input->SetTrade_type("JSAPI");//JSAPI, MWEB    H5支付方式 ：trade_type=MWEB
$input->SetOpenid($openId);//echo $openId; //next stop 
$order = WxPayApi::unifiedOrder($input);
if($order['err_code']){
    echo $order['err_code'].':'.$order['err_code_des'];exit;
}

//var_dump($_SESSION);


$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
//$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
$url = '/index.php?f=userCenter&a=orderpt';
// if($orders->customer == '礼品卡充值'){
// 	$url = '/userCenter/huiyuanzhongxin.html';
// }

?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
<link href="/skins/wap/styles/common.css" rel="stylesheet" type="text/css">
<link href="/skins/wap/styles/main.css" rel="stylesheet" type="text/css">
<link href="/skins/wap/styles/zhifu.css" rel="stylesheet" type="text/css">
<title>微信支付</title>

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
					location.href='/index.php?f=userCenter&a=orderpt';
				}else{
					alert(res.err_msg);
					location.href='<?=$url?>';//支付失败返回订单列表
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
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};
	
	</script>
</head>
<body style="color:#000;">
<div id="pintuanzutuan">
    <div class="pintuanxiangqing_1">
        <div class="pintuanxiangqing_1_01">
            订单支付
        </div>
        <div class="pintuanxiangqing_1_02" onclick="history.back(-1);">
            <img src="/skins/wap/images/biao_20.png"/>
        </div>
        <div class="pintuanxiangqing_1_03" onclick="location.href='/';">
                关闭
        </div>
    </div>
    <div class="zhanghumingxi">
	    <div class="yudingchenggong">
			<div class="yudingchenggong_1">
		    	<!-- 订单支付金额 -->
		    </div>
			<div class="yudingchenggong_2">
		    	￥<span><?=$pay_price/100?></span>
		    </div>
			<div class="yudingchenggong_3">
		    	<a href="javascript:void(0);" onClick="callpay()">立即支付</a>
		    </div>
		</div>
        
    </div>
</div>


</body>
</html>
<?php exit();?>
