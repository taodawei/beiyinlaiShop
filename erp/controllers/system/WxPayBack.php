<?php

class WxPayBack{
    protected $appid = "wxc0e123bec52dd511";
    protected $mch_id = "1622625033";
    //签名时会用到
    protected $mch_key = "bC67HQOHKeTK6f80bw6cBq02zShuKVEi";
     /**
     * 小程序退款
     * @param $second
     * @param string $orderCode 订单号
     * @return string
     */
    //微信支付退款接口
    public function unifiedorder($out_trade_no,$out_refund_no,$total_fee,$refund_fee) {
        //改成退款的地址
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $parameters = array(
            'appid' => $this->appid, //小程序ID
            'mch_id' => $this->mch_id, //商户号
            'nonce_str' => $this->createNoncestr(), //随机字符串
            'out_trade_no'=> $out_trade_no,
            'out_refund_no'=> $out_refund_no,
            'total_fee' => $total_fee,
            'refund_fee' => $refund_fee,
        );

        //签名
        $parameters['sign'] = $this->getSign($parameters);

        $xmlData = $this->arrayToXml($parameters);

        $sign_xml = $this->postXmlCurl($xmlData, $url, 60);

        return $sign_xml;
        
    }

    /**
     * 发送请求
     * @return xml
     */
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置证书
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,"PEM");
        curl_setopt($ch,CURLOPT_SSLCERT,ABSPATH."/config/sslkey/888_cert1.pem");
        // echo file_get_contents(ABSPATH."/config/sslkey/888_cert1.pem");die;
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,"PEM");
        curl_setopt($ch,CURLOPT_SSLKEY,ABSPATH."/config/sslkey/888_key1.pem");
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            //失败咯，看看怎么处理呀
            // var_dump($error);
            curl_close($ch);
        }
    }

    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    //数组转换成xml
    private function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    //作用：产生随机字符串，不长于32位
    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //作用：生成签名
    private function getSign($Obj) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }

        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);

        //签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $this->mch_key;

        //var_dump($String);die;
        //签名步骤三：MD5加密
        $String = md5($String);

        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }


    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

}