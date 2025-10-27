<?php

class wechat_paymentchange { 
    public $appid='';
    public $mchid='';
    public $secrectKey='';
    public $ip='';
    public function __construct($appid,$mchid,$secrectKey,$ip){
       
        $this->appid = $appid;
        $this->mchid = $mchid;
        $this->secrectKey = $secrectKey;
        $this->ip = $ip;
    }

	/**
	 * [xmltoarray xml格式转换为数组]
	 * @param  [type] $xml [xml]
	 * @return [type]      [xml 转化为array]
	 */
	public function xmltoarray($xml) { 
	     //禁止引用外部xml实体 
	    libxml_disable_entity_loader(true); 
	    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
	    $val = json_decode(json_encode($xmlstring),true); 
	    return $val;
	}


	/**
	 * [arraytoxml 将数组转换成xml格式（简单方法）:]
	 * @param  [type] $data [数组]
	 * @return [type]       [array 转 xml]
	 */
	public function arraytoxml($data){
	    $str='<xml>';
	    foreach($data as $k=>$v) {
	        $str.='<'.$k.'>'.$v.'</'.$k.'>';
	    }
	    $str.='</xml>';
	    return $str;
	}

	/**
	 * [createNoncestr 生成随机字符串]
	 * @param  integer $length [长度]
	 * @return [type]          [字母大小写加数字]
	 */
	public function createNoncestr($length =32){
	    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYabcdefghijklmnopqrstuvwxyz0123456789";  
	    $str ="";

	    for($i=0;$i<$length;$i++){  
	        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);   
	    }  
	    return $str;
	}

	/**
	 * [curl_post_ssl 发送curl_post数据]
	 * @param  [type]  $url     [发送地址]
	 * @param  [type]  $xmldata [发送文件格式]
	 * @param  [type]  $second [设置执行最长秒数]
	 * @param  [type]  $aHeader [设置头部]
	 * @return [type]           [description]
	 */
	function curl_post_ssl($url, $vars, $second = 30, $aHeader = array()){
        $isdir = $_SERVER['DOCUMENT_ROOT']."/config/sslkey/";//证书位置

        $ch = curl_init();//初始化curl

        curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
        curl_setopt($ch, CURLOPT_SSLCERT, $isdir . '1114_cert.pem');//证书位置
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//CURLOPT_SSLKEY中规定的私钥的加密类型
        curl_setopt($ch, CURLOPT_SSLKEY, $isdir . '1114_key.pem');//证书位置
        curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $isdir . 'rootca.pem');
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置头部
        }
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);//全部数据使用HTTP协议中的"POST"操作来发送

        $data = curl_exec($ch);//执行回话
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }




	/**
	 * [sendMoney 企业付款到零钱]
	 * @param  [type] $amount     [发送的金额（分）目前发送金额不能少于1元]
	 * @param  [type] $re_openid  [发送人的 openid]
	 * @param  string $desc       [企业付款描述信息 (必填)]
	 * @param  string $check_name [收款用户姓名 (选填)]
	 * @return [type]             [description]
	 */
	public function sendMoney($amount,$re_openid,$desc='测试',$check_name=''){

	    $total_amount = (100) * $amount;
	    
	    $data=array(
	        'mch_appid'=>$this->appid,//商户账号appid
	        'mchid'=> $this->mchid,//商户号
	        'nonce_str'=>$this->createNoncestr(),//随机字符串
	        'partner_trade_no'=> date('YmdHis').rand(1000, 9999),//商户订单号
	        'openid'=> $re_openid,//用户openid
	        'check_name'=>'NO_CHECK',//校验用户姓名选项,
	        're_user_name'=> $check_name,//收款用户姓名
	        'amount'=>$total_amount,//金额
	        'desc'=> $desc,//企业付款描述信息
	        'spbill_create_ip'=>$this->ip,//Ip地址
	    );

	    //生成签名算法
	    $secrect_key=$this->secrectKey;///这个就是个API密码。MD5 32位。
	    $data=array_filter($data);
	    ksort($data);
	    $str='';
	    foreach($data as $k=>$v) {
	       $str.=$k.'='.$v.'&';
	    }
	    $str.='key='.$secrect_key;
	    $data['sign']=md5($str);
	    //生成签名算法


	    $xml=$this->arraytoxml($data);
	  
	    $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
	    $res=$this->curl_post_ssl($url,$xml);
	    $return=$this->xmltoarray($res);
	    
	 	return $return;
	 
	    print_r($return);
	    //返回来的结果
	    // [return_code] => SUCCESS [return_msg] => Array ( ) [mch_appid] => wxd44be6876gg73 [mchid] => 76775512 [nonce_str] => 616615516 [result_code] => SUCCESS [partner_trade_no] => 20186505080216815 
	    // [payment_no] => 1000018361251805057502564679 [payment_time] => 2018-05-15 15:29:50
	    
	    $responseObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
	    echo $res= $responseObj->return_code;  //SUCCESS  如果返回来SUCCESS,则发生成功，处理自己的逻辑
	    
	    return $res;
	}


}

?>