<?php
global $db;

$payAli = $db->get_row("select status,info from demo_kehu_pay where comId=1210 and type=2 limit 1");
$pay = json_decode($payAli->info);

$alipay_config['partner']		= $pay->partnerId;

//安全检验码，以数字和字母组成的32位字符
$alipay_config['key']			= $pay->key;
$aliapy_config['seller_email'] = $pay->account;

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


//签名方式 不需修改
$alipay_config['sign_type']    = strtoupper('MD5');
$aliapy_config['notify_url']   = "http://". $_SERVER['HTTP_HOST'] ."/notify_alipay.php";
//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');
$aliapy_config['return_url'] = "http://". $_SERVER['HTTP_HOST'] ."/inc/pay/alipay.php";
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'\\cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';
?>