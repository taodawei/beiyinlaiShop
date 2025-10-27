<?php
namespace Zhishang;

class Jssdk
{
    private $appId;
	private $appSecret;
	private $url;


	public function __construct() {
	    global $db,$request;
		$this->appId = 'wx84bd3968cbfc4777';
		$this->appSecret = '103d781af6d69bc1abd0aa37f4ea64fd';
		
		
// 		$this->appId = 'wx3c0898d6aa740729';
// 		$this->appSecret = '7fca10f6943b0b9a2c2b569d6d2ae61c';
		$url = $request['url'];
		if(!empty($url)){
		  $this->url = $url;
		}else{
		  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		  $this->url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
	}

	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		// $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url = $this->url;

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
		  "appId"     => $this->appId,
		  "nonceStr"  => $nonceStr,
		  "timestamp" => $timestamp,
		  "url"       => $url,
		  "signature" => $signature,
		  "rawString" => $string
		);
		return json_encode($signPackage);
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
		  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() {
		$accessToken = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
		$res = json_decode($this->httpGet($url));
		$ticket = $res->ticket;
		return $ticket;
	}
	
	public function getCodeUrl()
	{
        global $db,$request;
        
        $access_token = $this->getAccessToken();
        $qrcode_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token; 
        
        $nonceStr = $this->createNonceStr(5);

        $params = [
            "action_name" => "QR_LIMIT_STR_SCENE",
            "action_info" => [
                'scene' => ['scene_str' => $nonceStr],
            ],
        ];
        $json = self::curl_post($qrcode_url, $params); 
        $json = json_decode($json, true);
        if (!$json['errcode']) { 
          
            $ticket = $json['ticket']; 
            $ticket_img = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket); 
            
            $return = array();
            $return['code'] = 1;
            $return['message'] = '创建成功';
            $return['data'] = array(
                'url' => $ticket_img,
                'nonce_str' => $nonceStr
            );

            echo json_encode($return,JSON_UNESCAPED_UNICODE);
            exit;
            
        } else { 
            echo '发生错误：错误代码 ' . $json['errcode'] . '，微信返回错误信息：' . $json['errmsg']; 
            exit; 
        }
	}

	private function getAccessToken() {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
		$res = json_decode($this->httpGet($url));
		$access_token = $res->access_token;
		return $access_token;
	}
	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}
	
	public static function curl_post($url, array $params = array())
	{
		$data_string = json_encode($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
			'Content-Type: application/json'
			)
		);
		$data = curl_exec($ch);
		curl_close($ch);
		return ($data);
	}
	
	
	//微信h5支付
    public function jspay(){
       	global $request,$db,$comId,$order;
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		//$order = $db->get_row("select * from order$fenbiao where id=$orderId ");
		$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"订单不存在"}';
		}
		if($order->status!=-5){
			return '{"code":0,"message":"订单不是待支付状态，不能进行支付"}';
		}
		$pay_end = strtotime($order->pay_endtime);
		$now = time();
		if($pay_end<$now){
			return '{"code":0,"message":"该订单已超过支付时间"}';
		}

        
        // $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
        // if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
        //     return '{"code":0,"message":"微信配置信息有误"}';
        // }
        
        $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
        if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
        	die('微信配置信息有误');
        }
        $weixin_arr = json_decode($weixin_set->info);
        
        define('WX_MCHID',$weixin_arr->mch_id);
        define('WX_KEY',$weixin_arr->key);
        define('WX_APPID', 'wxf60efc606bf76bff');
        define('WX_APPSECRET', 'eb6c1ff98080c85d3ee67a89c0590744');

        //获取订单信息
        require_once(ABSPATH."/inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");
 
        require_once(ABSPATH."/inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
        //require_once ABSPATH.'/inc/pay/WxpayAPI_php_v3/example/WxPay.NativePay.php';
        require_once(ABSPATH."/inc/pay/WxpayAPI_php_v3/example/log.php");
        $logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

        $tools = new \JsApiPay();
        $openid = $db->get_var("select openId from users where id=".$userId);
        if(empty($openid)){
            $code = $request['code'];
            if(empty($code)){
                return '{"code":0,"message":"code不能为空"}';
            }
            $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".WX_APPID."&secret=".WX_APPSECRET."&code=".$code."&grant_type=authorization_code";

            $token_info = self::https_request($token_url);

            $access_token = $token_info['access_token'];
            $openid = $token_info['openid'];
            // $db->query("update users set openId='$openid' where id=".$userId);
        }
 
        if(empty($openid)){
            return '{"code":0,"message":"openId不能为空"}';
        }
        $daizhifu = getXiaoshu($order->price-$order->price_payed,2);
		if($order->price_dingjin>0){
		    $daizhifu = getXiaoshu($order->price_dingjin-$order->price_payed,2);
		}
		$product_json = json_decode($order->product_json);
		foreach ($product_json as $pdt) {
			$subject.=','.$pdt->title.'*'.$pdt->num;
		}
		$body = substr($subject,1);
		$subject = sys_substr($body,30,true);
		$subject = str_replace('_','',$subject).'_'.$comId;
		$pay_price = round($daizhifu*100);

		$dtTime = date("YmdHis",strtotime($order->dtTime));
		$expireTime = date("YmdHis", time() + 60*60*24);
        // var_dump($subject); var_dump($orderId);var_dump($expireTime);var_dump($notifyUrl);var_dump($price);die;

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetOut_trade_no($order->orderId);
        $input->SetTotal_fee($pay_price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire($expireTime);
        $input->SetGoods_tag($subject);
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify_applet.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        //file_put_contents('request.txt',serialize($input));
        $orders = \WxPayApi::unifiedOrder($input);

        if($orders['err_code']){
            echo $orders['err_code'].':'.$orders['err_code_des'];exit;
        }

        $jsApiParameters = $tools->GetJsApiParameters($orders);
        //统一下单结束
        $return['code'] = 1;
        $return['time'] = time();
        $return['msg'] = '创建成功';
        $return['data'] = '';
        $return['data'] = $jsApiParameters;
        //开始写支付参数
        echo json_encode($return,JSON_UNESCAPED_UNICODE);

        exit;
    }
    
     public function https_request($url){

        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $url);
    
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    
        curl_setopt($curl,CURLOPT_HEADER,0); //
    
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
    
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
    
        $response = curl_exec($curl);  
    
        curl_close($curl);
    
        $jsoninfo = json_decode($response,true); 
    
        return $jsoninfo;
    
    }
}
