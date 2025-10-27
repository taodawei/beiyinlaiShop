<?php
function cleanArrayForMysql($data)
{
	if(!get_magic_quotes_gpc()){
		return (is_array($data))?array_map('cleanArrayForMysql', $data):trim(addslashes($data));
	}else{
		return $data;
	}
}
//生成新的文件($str为字符串,$filePath为生成时的文件路径包括文件名)
function string2file($str,$filePath)
{
	$fp=fopen($filePath,'w+');
	fwrite($fp,$str);
	fclose($fp);
}
//从文件中读取字符
function file2string($filePath)
{
	$fp = fopen($filePath,"r");
	$content_= fread($fp, filesize($filePath));
	fclose($fp);
	return $content_;

}
/*产生$length位随机数*/
function random($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
//获得客户端IP,并转换为long型
function getip()
{
	if(getenv('HTTP_CLIENT_IP'))
	{
		$client_ip = getenv('HTTP_CLIENT_IP');
	}
	elseif(getenv('HTTP_X_FORWARDED_FOR'))
	{
		$client_ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif(getenv('REMOTE_ADDR'))
	{
		$client_ip = getenv('REMOTE_ADDR');
	}
	else
	{
		$client_ip = $HTTP_SERVER_VAR['REMOTE_ADDR'];
	}
	return $client_ip;
}
/*截取字串 */
function sys_substr($str,$strcount,$isellipsis)
{
	if($strcount>0){
		if(!$isellipsis)
			return cnSubstr( $str,0,$strcount-1 ); //截取标题字数
		elseif($isellipsis && cnStrLen($str)>$strcount)
			return cnSubstr( $str,0,$strcount-1 )."...";
		else 
			return $str; //保留完整标
	}else{
		return $str;
	}
}
function cnStrLen($str)
{
	$i = 0;
	$tmp = 0;
	while ($i < strlen($str))
	{
		if (ord(substr($str,$i,1)) >127)
		{
			$tmp = $tmp+1;
			$i = $i + 3;
		}
		else
		{
			$tmp = $tmp + 1;;
			$i = $i + 1;
		}
	}
	return $tmp;
}
//获取中英文混合字符在字符串中的位置
function cnStrPos($str,$keyword)
{
	$i = 0;
	$tem = 0;
	$temStr = strpos($str,$keyword);
	while ($i < $temStr)
	{
		if (ord(substr($str,$i,1)) >127)
		{
			$tmp = $tmp+1;
			$i = $i + 3;
		}
		else
		{
			$tmp = $tmp + 1;;
			$i = $i + 1;
		}
	}
	return $tmp;
}
//截取字符数$str-字符串$N-多少字符
function cnSubStr($str, $start, $lenth)
{
	$len = strlen($str);
	$r = array();
	$n = 0;
	$m = 0;
	for($i = 0; $i < $len; $i++) {
		$x = substr($str, $i, 1);
		$a = base_convert(ord($x), 10, 2);
		$a = substr('00000000'.$a, -8);
		if ($n < $start){
			if (substr($a, 0, 1) == 0) {
			}elseif (substr($a, 0, 3) == 110) {
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$i += 2;
			}
			$n++;
		}else{
			if (substr($a, 0, 1) == 0) {
				$r[] = substr($str, $i, 1);
			}elseif (substr($a, 0, 3) == 110) {
				$r[] = substr($str, $i, 2);
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$r[] = substr($str, $i, 3);
				$i += 2;
			}else{
				$r[] = '';
			}
			if (++$m >= $lenth){
				break;
			}
		}
	}
	return join('', $r);

}
//获取中文的首字母
function getFirstCharter($str){
	if(empty($str)){return '';}
	$fchar=ord($str{0});
	if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
	$s1=iconv('UTF-8','gb2312',$str);
	$s2=iconv('gb2312','UTF-8',$s1);
	$s=$s2==$str?$s1:$str;
	$asc=ord($s{0})*256+ord($s{1})-65536;
	if($asc>=-20319&&$asc<=-20284) return 'A';
	if($asc>=-20283&&$asc<=-19776) return 'B';
	if($asc>=-19775&&$asc<=-19219) return 'C';
	if($asc>=-19218&&$asc<=-18711) return 'D';
	if($asc>=-18710&&$asc<=-18527) return 'E';
	if($asc>=-18526&&$asc<=-18240) return 'F';
	if($asc>=-18239&&$asc<=-17923) return 'G';
	if($asc>=-17922&&$asc<=-17418) return 'H';
	if($asc>=-17417&&$asc<=-16475) return 'J';
	if($asc>=-16474&&$asc<=-16213) return 'K';
	if($asc>=-16212&&$asc<=-15641) return 'L';
	if($asc>=-15640&&$asc<=-15166) return 'M';
	if($asc>=-15165&&$asc<=-14923) return 'N';
	if($asc>=-14922&&$asc<=-14915) return 'O';
	if($asc>=-14914&&$asc<=-14631) return 'P';
	if($asc>=-14630&&$asc<=-14150) return 'Q';
	if($asc>=-14149&&$asc<=-14091) return 'R';
	if($asc>=-14090&&$asc<=-13319) return 'S';
	if($asc>=-13318&&$asc<=-12839) return 'T';
	if($asc>=-12838&&$asc<=-12557) return 'W';
	if($asc>=-12556&&$asc<=-11848) return 'X';
	if($asc>=-11847&&$asc<=-11056) return 'Y';
	if($asc>=-11055&&$asc<=-10247) return 'Z';
	return 'Z';
}
//分表算法
function getFenBiao($comId,$num){
	return intval($comId%$num);
}
//一指专用分表
function getYzFenbiao($row,$num){
	return $row%$num;
}
//推送消息
function send_message($uids,$type,$title,$content){
	global $db;
	include_once(ABSPATH.'/config/dt-config.php');
	include_once(ABSPATH.'/tuisong/jpush.php');
	include_once(ABSPATH.'/tuisong/config.inc.php'); 
	include_once(ABSPATH.'/tuisong/db.class.php');
	$n_title   =  $title;
	$n_content =  $content;
	$receiver_value = $uids;
	dataConnect();
	$sql = "SELECT max(id) from ".DB_TAB."";
	$result = $db->get_var($sql);
	$sendno = $result+1;
	$platform = platform ;
	$msg_content = json_encode(array('n_builder_id'=>0, 'n_title'=>$title, 'n_content'=>$content));
	$obj = new jpush(masterSecret,appkeys);
	//echo  $receiver_value;exit;
	$res = $obj->send($sendno,$type,$receiver_value, 1, $msg_content, $platform);
	//echo $res;
}
function json_str($str){
	json_decode($str);
	if(json_last_error() == JSON_ERROR_NONE){
		return $str;
	}else{
		global $db,$request;
		$errInfo = json_encode($request,JSON_UNESCAPED_UNICODE);
		$content = 'request:'.$errInfo.'*****str:'.$str;
		$db->query("insert into demo_err_logs(userId,dtTime,content) value(0,'".date("Y-m-d H:i:s")."','$content')");
		return '{"code":0,"message":"系统错误，请重试，如果重复出现此错误请联系客服"}';
	}
}
//发送异步任务
function send_asy_task($action,$params){
	$task_connection = new \Workerman\Connection\AsyncTcpConnection('Text://127.0.0.1:2346');
    $task_data = array(
        'action' => $action,
        'params' => $params,
    );
    $task_connection->send(json_encode($task_data));
    $task_connection->onMessage = function($task_connection, $task_result)
    {
         var_dump($task_result);
         $task_connection->close();
    };
    $task_connection->connect();
}