<?php
global $db,$request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$fenbiao = getFenbiao($comId,20);
$orderId = (int)$request['order_id'];
$order = $db->get_row("select * from demo_pdt_order where id=$orderId and userId=$userId");
$order->price = $order->price-$order->price_payed;
if(empty($order)){
	die('订单不存在');
}
if($order->status!=-5){
	die('订单当前不是待支付状态<script>location.href="/index.php?p=22&a=orders"</script>');
}
$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
	die('微信配置信息有误');
}
$weixin_arr = json_decode($weixin_set->info);
$subject = '';
$product_json = json_decode($order->product_json);
foreach ($product_json as $pdt) {
	$subject.=','.$pdt->title.'*'.$pdt->num;
}
$body = substr($subject,1);
$subject = sys_substr($body,30,true);
$subject = str_replace('_','',$subject).'_'.$comId;
$money= round($order->price*100);
$userip = get_client_ip();
$appid  = $weixin_arr->appid;
$mch_id = $weixin_arr->mch_id;
$key    = $weixin_arr->key;
$out_trade_no = $order->orderId;
$nonce_str = createNoncestr();
$total_fee = $money; //金额
$spbill_create_ip = $userip; //IP
$notify_url = "http://".$_SERVER['HTTP_HOST']."/notify_pdts.php"; //回调地址
$trade_type = 'MWEB';//交易类型 具体看 API 里面有详细介绍
$scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://'.$_SERVER['HTTP_HOST'].'","wap_name":"支付"}}';//场景信息 必要参数
$signA ="appid=$appid&attach=$comId&body=$subject&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
$strSignTmp = $signA."&key=$key";
$sign = strtoupper(MD5($strSignTmp));
$post_data = "<xml>
                    <appid>$appid</appid>
                    <mch_id>$mch_id</mch_id>
                    <body>$subject</body>
                    <out_trade_no>$out_trade_no</out_trade_no>
                    <total_fee>$total_fee</total_fee>
                    <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                    <notify_url>$notify_url</notify_url>
                    <trade_type>$trade_type</trade_type>
                    <scene_info>$scene_info</scene_info>
                    <attach>$comId</attach>
                    <nonce_str>$nonce_str</nonce_str>
                    <sign>$sign</sign>
            </xml>";
$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
$dataxml = postXmlCurl($post_data,$url); //后台 POST 微信传参地址  同时取得微信返回的参数 
$objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的 XML 转换成数组
//print_r($objectxml);
?>
<style type="text/css">
     body{font-family: "Microsoft YaHei";}
     .pay-box{text-align:center;}
     .ico{width: 5rem;height: 5rem;border-radius: 5rem;background: #3FB837;color: #fff;display: inline-block;font-size: 4rem;}
     .txt{font-size:1rem; padding-top: 2rem; color: #333;} .val{font-size:2.5rem; font-weight: bold;}
     .pay{width: 8rem;height: 2rem;border-radius: .5rem;font-size: 1rem;color: #fff;background: #07BF05;border: 0px;text-align: center;}
     a{color: #fff; background: transparent !important;}
 </style>
 <div class="pay-box" style="text-align: center;margin-top:2rem">
     <div class="ico">￥</div>
     <div class="txt">支付金额</div>
     <div class="val">￥<span><?php echo $total_fee/100 ?></span> </div>
     <a href="<?php echo $objectxml['mweb_url'] ?>" style="height:2rem;display: block;margin-top: 3rem;"><button class="pay">确认支付</button></a> 
     <a href="/index.php?p=8" style="display:block;padding-top:1rem;font-size:1rem" class="txt">返回会员中心</a>
 </div>