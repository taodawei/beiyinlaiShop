<?php
//发货方法
function dinghuo_fahuo($fahuoId,$expressno,$areaId,$address,$name,$phone){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$dianzimiandan = $db->get_row("select info,status from demo_dianzimiandan where comId=10 and type=1 limit 1");
	if(empty($dianzimiandan)||$dianzimiandan->status!=1){
		echo '{"code":1}';exit;
	}
	$peizhi = json_decode($dianzimiandan->info);
	//电商ID
	$EBusinessID = $peizhi->EBusinessID;
	//电商加密私钥，快递鸟提供，注意保管，不要泄漏
	$AppKey = $peizhi->AppKey;
	//请求url，正式环境地址：http://api.kdniao.cc/api/Eorderservice    测试环境地址：http://testapi.kdniao.cc:8081/api/EOrderService
	$ReqURL = 'http://testapi.kdniao.cc:8081/api/Eorderservice';
	$fahuo=$db->get_row("select * from demo_dinghuo_fahuo where id=$fahuoId and comId=$comId");
	if(empty($fahuo)){
		echo '{"code":1}';exit;
	}
	$shouhuoInfo = $db->get_var("select shouhuoInfo from demo_dinghuo_order where id=$fahuo->dinghuoId");

	if(!empty($shouhuoInfo)){
		$shouhuo = json_decode($shouhuoInfo);
	}
	//file_put_contents('request.txt',$shouhuo->areaId);
	//构造电子面单提交信息
	$eorder = [];
	$eorder["ShipperCode"] = $expressno;
	if(!empty($peizhi->CustomerName)&&!empty($peizhi->CustomerPwd)){
		$eorder["CustomerName"] = $peizhi->CustomerName;
		$eorder["CustomerPwd"] = $peizhi->CustomerPwd;
	}
	$eorder["OrderCode"] = $fahuo->comId.'_'.$fahuo->id;
	$eorder["PayType"] = 1;
	$eorder["ExpType"] = 1;

	//玉林书店的地址
	$sender = [];
	$sender["Name"] = $name;
	$sender["Mobile"] = $phone;
	$area=$db->get_row("select * from demo_area where id=".$areaId);
	$city=$db->get_row("select * from demo_area where id=".$area->parentId);
	if(!empty($city->parentId)){
		$province=$db->get_var("select title from demo_area where id=".$city->parentId);
	}else{
		$province=$city->title;
		$city->title=$area->title;
		$area->title = '';
	}
	$sender["ProvinceName"] = $province;
	$sender["CityName"] = $city->title;
	$sender["ExpAreaName"] = $area->title;
	$sender["Address"] = $address;

	//买家地址
	$receiver = [];
	$receiver["Name"] = $shouhuo->name;
	$receiver["Mobile"] = $shouhuo->phone;
	$area2=$db->get_row("select * from demo_area where id=".$shouhuo->areaId);
	$city2=$db->get_row("select * from demo_area where id=".$area2->parentId);
	if(!empty($city->parentId)){
		$province2=$db->get_var("select title from demo_area where id=".$city2->parentId);
	}else{
		$province2=$city2->title;
		$city2->title=$area2->title;
		$area2->title = '';
	}
	//$province2=$db->get_var("select title from demo_area where id=".$city2->parentId);
	$receiver["ProvinceName"] = $province2;
	$receiver["CityName"] = $city2->title;
	$receiver["ExpAreaName"] = $area2->title;
	$receiver["Address"] = $shouhuo->address;

	$commodityOne = [];
	$commodityOne["GoodsName"] = $fahuo->beizhu;
	$commodity = [];
	$commodity[] = $commodityOne;

	$eorder["Sender"] = $sender;
	$eorder["Receiver"] = $receiver;
	$eorder["Commodity"] = $commodity;


	//调用电子面单
	$jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);
	//file_put_contents('request1.txt',$jsonParam);
	$jsonParam = JSON($eorder);//兼容php5.2（含）以下
	//echo "电子面单接口提交内容：<br/>".$jsonParam;
	$jsonResult = submitEOrder($jsonParam,$EBusinessID,$ReqURL,$AppKey);
	//echo "<br/><br/>电子面单提交结果:<br/>".$jsonResult;

	
	/*
	$ip='';
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	$ip= $ip ? $ip : $_SERVER['REMOTE_ADDR'];*/
	//$userId = $_SESSION[TB_PREFIX.'admin_userID'];

	//解析电子面单返回结果
	$result = json_decode($jsonResult, true);
	//file_put_contents('request.txt',$jsonResult);
	//echo "<br/><br/>返回码:".$result["ResultCode"];
	if($result["ResultCode"] == "100") {
		//file_put_contents("express.txt",$result['Order']["LogisticCode"]);
		$db->query("update demo_dinghuo_fahuo set kuaidi_order='".$result['Order']["LogisticCode"]."' where id=$fahuoId");
	}
}
function get_wuliu($orderCode,$shipperCode,$logisticCode){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$info = $db->get_var("select type2Info from demo_dinghuo_set where comId=10");
	if(empty($info)){
		//file_put_contents('request.txt','2');
		echo '{"code":0,"message":"未找到配置信息，请登录知商总控制台-》应用管理-》物流跟踪 设置。"}';
		exit;
	}
	$peizhi = json_decode($info);
	//电商ID
	$EBusinessID = $peizhi->appId;
	//电商加密私钥，快递鸟提供，注意保管，不要泄漏
	$AppKey = $peizhi->appKey;
	$requestData= "{'OrderCode':'$orderCode','ShipperCode':'$shipperCode','LogisticCode':'$logisticCode'}";
	$datas = array(
        'EBusinessID' => $EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData),
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, $AppKey);
	$result=sendPost('http://sandboxapi.kdniao.cc:8080/kdniaosandbox/gateway/exterfaceInvoke.json', $datas);
	$wuliuInfo=json_decode($result, true);
	if(empty($wuliuInfo['Traces'])){
		echo '{"code":0,"message":"未找到物流信息。"}';
		exit;
	}
	$resultstr = '';
	$wuliu=array_reverse($wuliuInfo['Traces']);
	if($wuliu){
		$i=0;
		foreach ($wuliu as $k =>$v) {
			$resultstr.=$v['AcceptTime'].'&nbsp;&nbsp;'.$v['AcceptStation'].'<br>';
		}
	}
	echo '{"code":1,"message":"'.$resultstr.'"}';
	exit;
}
/**
 * Json方式 调用电子面单接口
 */
function submitEOrder($requestData,$EBusinessID,$ReqURL,$AppKey){
	$datas = array(
        'EBusinessID' => $EBusinessID,
        'RequestType' => '1007',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, $AppKey);
	$result=sendPost($ReqURL, $datas);	
	
	//根据公司业务处理返回的信息......
	
	return $result;
}

 
/**
 *  post提交数据 
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据 
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();	
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);		
    }	
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;	
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);  
    
    return $gets;
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
/************************************************************** 
 * 
 *  使用特定function对数组中所有元素做处理 
 *  @param  string  &$array     要处理的字符串 
 *  @param  string  $function   要执行的函数 
 *  @return boolean $apply_to_keys_also     是否也应用到key上 
 *  @access public 
 * 
 *************************************************************/  
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)  
{  
    static $recursive_counter = 0;  
    if (++$recursive_counter > 1000) {  
        die('possible deep recursion attack');  
    }  
    foreach ($array as $key => $value) {  
        if (is_array($value)) {  
            arrayRecursive($array[$key], $function, $apply_to_keys_also);  
        } else {  
            $array[$key] = $function($value);  
        }  
   
        if ($apply_to_keys_also && is_string($key)) {  
            $new_key = $function($key);  
            if ($new_key != $key) {  
                $array[$new_key] = $array[$key];  
                unset($array[$key]);  
            }  
        }  
    }  
    $recursive_counter--;  
}  


/************************************************************** 
 * 
 *  将数组转换为JSON字符串（兼容中文） 
 *  @param  array   $array      要转换的数组 
 *  @return string      转换得到的json字符串 
 *  @access public 
 * 
 *************************************************************/  
function JSON($array) {  
    arrayRecursive($array, 'urlencode', true);  
    $json = json_encode($array);  
    return urldecode($json);  
}