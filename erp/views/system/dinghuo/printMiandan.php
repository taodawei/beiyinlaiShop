<?
defined('API_URL') or define('API_URL', 'http://www.kdniao.com/External/PrintOrder.aspx');
defined('IP_SERVICE_URL') or define('IP_SERVICE_URL', 'http://www.kdniao.com/External/GetIp.aspx');
global $request;
build_form((int)$request['id']);
function build_form($fahuoId) {
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$dianzimiandan = $db->get_row("select info,status from demo_dianzimiandan where comId=$comId and type=1 limit 1");
	if(empty($dianzimiandan)||$dianzimiandan->status!=1){
		echo '请先设置电子面单！';exit;
	}
	$peizhi = json_decode($dianzimiandan->info);
	$EBusinessID = $peizhi->EBusinessID;
	$AppKey = $peizhi->AppKey;
	$fahuo=$db->get_row("select * from demo_dinghuo_fahuo where id=$fahuoId and comId=$comId");
	$request_data = '[{"OrderCode":"'.$fahuo->comId.'_'.$fahuo->id.'","PortName":"'.$peizhi->PortName.'"}]';
	$request_data_encode = urlencode($request_data);
	$data_sign = encrypt(get_ip().$request_data_encode, $AppKey);
	//是否预览，0-不预览 1-预览
	$is_priview = '1';
	$form = '<form id="form1" method="POST" action="'.API_URL.'"><input type="text" name="RequestData" value="'.$request_data.'"/><input type="text" name="EBusinessID" value="'.$EBusinessID.'"/><input type="text" name="DataSign" value="'.$data_sign.'"/><input type="text" name="IsPriview" value="'.$is_priview.'"/></form><script>form1.submit();</script>';
	print_r($form);
}

/**
 * 判断是否为内网IP
 * @param ip IP
 * @return 是否内网IP
 */
function is_private_ip($ip) {
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
}

/**
 * 获取客户端IP(非用户服务器IP)
 * @return 客户端IP
 */
function get_ip() {
	//获取客户端IP
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

	if(!$ip || is_private_ip($ip)) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, IP_SERVICE_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		return $output;
	}
	else{
		return $ip;
	}
}

/**
 * 电商Sign签名生成
 * @param data 内容   
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}
