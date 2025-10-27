<?php
$alipay_config['sign_type']    = strtoupper('RSA');
$alipay_config['input_charset']= strtolower('utf-8');
$alipay_config['cacert']    = getcwd().'\\cacert.pem';
$alipay_config['transport']    = 'http';
$alipay_config['payment_type'] = "1";
$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";