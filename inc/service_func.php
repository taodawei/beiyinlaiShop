<?php

function getDistance($lat1, $lng1,  $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters
    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;
    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

//检查图片是否存在 grysoft/
function ispic($picUrl,$nopicUrl="/inc/img/nopic.svg")
{
	$tempUrl = explode('|',$picUrl);
	$tempPic = explode('/',$tempUrl[0]); //分割图片地址信息
	if($tempPic[0]!='http:' && $tempPic[0]!='https:')
	{
		if(is_file(ABSPATH.$tempUrl[0]))
		return get_root_path().$tempUrl[0];
		else
		return 'http://'.$_SERVER['HTTP_HOST'].$nopicUrl;
	}
	else
	{
		return $tempUrl[0];
	}
}
//检查文件是否存在 2011-09-10
function isfile($fileUrl)
{
	$tempPic = explode('/',$fileUrl); 
	if($tempPic[0]!='http:')
	{
		return is_file(ABSPATH.$fileUrl)?true:false;  
	}else{
		return false;
	}
}
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

function sys_mail($title,$body,$to=smtpReceiver)
{
	@require_once(ABSPATH.'/inc/class.smtp.php');
	@require_once(ABSPATH.'/inc/class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;         // SMTP服务器是否需要验证
	$mail->Host       = smtpServer;   // 设置SMTP服务器
	$mail->Port		  = smtpPort; 		  // 设置端口
	$mail->Username   = smtpId;      // 开通SMTP服务的邮箱帐号
	$mail->Password   = smtpPwd;      // 开通SMTP服务的邮箱密码
	$mail->From       = smtpSender;       // 发件人Email
	$mail->FromName   = $_SERVER['HTTP_HOST'];        // 发件人昵称或姓名
	$mail->Subject    = $title;       // 邮件标题（主题）
	$mail->WordWrap   = 50;			  // 自动换行的字数
	$mail->MsgHTML($body);            // 邮件内容
	
	$receiver   = explode(';',$to);
	for($i=0;$i<count($receiver);$i++)
	{
		$mail->AddAddress($receiver[$i],"尊敬的会员"); //收件人地址。参数一：收信人的邮箱地址，可添加多个。参数二：收件人称呼
	}
	$mail->IsHTML(true); // 是否以HTML形式发送，如果不是，请删除此行
	
	$mail->Send();  //邮件发送
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
function getFenBiao($comId,$num){
	return intval($comId%$num);
}
//获取出入库订单号
function getOrderId($comId,$type){
	global $db;
	$fenbiao = getFenBiao($comId,20);
	if($type==5){
		$lastOrderInt = $db->get_var("select orderInt from demo_caigou_tuikuan where comId=$comId order by id desc limit 1");
	}else{
		$lastOrderInt = $db->get_var("select orderInt from demo_kucun_jilu$fenbiao where comId=$comId and type=$type order by id desc limit 1");
	}
	$lastOrderInt = $lastOrderInt+1;
	return buling($lastOrderInt,6);
}
function buling($str,$num){
	$length = strlen($str);
	if($length>=$num){
		return $str;
	}else{
		for ($i=0; $i <($num-$length) ; $i++) { 
			$str = '0'.$str;
		}
		return $str;
	}
}
//获取仓库审批人
function getShenpUser($comId,$type,$storeId){
	global $db;
	$shenpi = $db->get_row("select id,userId from demo_kucun_shenpi where comId=$comId and type=$type and storeId=$storeId limit 1");
	if(!empty($shenpi)){
		return $shenpi->userId;
	}else{
		$shenpi = $db->get_row("select id,userId from demo_kucun_shenpi where comId=$comId and type=$type and storeId=0 limit 1");
		if(!empty($shenpi)){
			return $shenpi->userId;
		}else{
			return 0;
		}
	}
}
function getPayType($type){
	global $db;
	$pay_type = '';
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$kehu_shezhi = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
	switch($type){
		case 1:$pay_type= $kehu_shezhi->acc_xianjin_name;break;
		case 2:$pay_type= $kehu_shezhi->acc_yufu_name;break;
		case 3:$pay_type= $kehu_shezhi->acc_fandian_name;break;
		case 4:$pay_type= $kehu_shezhi->acc_baozheng_name;break;
		case 5:$pay_type= '现金';break;
		case 6:$pay_type= '银行转账';break;
		case 7:$pay_type= '支付宝';break;
		case 8:$pay_type= '微信';break;
		default:$pay_type= '其他';break;
	}
	return $pay_type;
}
//给客户发送业务消息
function add_dinghuo_msg($toId,$content,$type,$infoId){
	global $db,$comId;
	$msg = array();
	$msg['comId'] = $comId;
	$msg['toId'] = $toId;
	$msg['content'] = $content;
	$msg['type'] = $type;
	$msg['infoId'] = $infoId;
	$msg['dtTime'] = date("Y-m-d H:i:s");
	$db->insert_update('dinghuo_msg',$msg,'id');
}
//给会员发消息
function addUserMsg($toId,$fenbiao,$content,$type,$infoId){
	global $db;
	$msg = array();
	$msg['userId'] = $toId;
	$msg['content'] = $content;
	$msg['type'] = $type;
	$msg['infoId'] = $infoId;
	$msg['dtTime'] = date("Y-m-d H:i:s");
	$db->insert_update('user_msg'.$fenbiao,$msg,'id');
}
//添加任务消息并推送
function addTaskMsg($type,$infoId,$content){
	global $db,$comId;
//	$crmdb = get_zhishang_db();
	$fenbiao = getFenBiao($comId,20);
	$model = 'dinghuo';
	$function = '';
	switch ($type){
		case 11:
			$function = 'shenhe';
		break;
		case 12:
			$function = 'caiwu';
		break;
		case 13:
			$function = 'chuku';
		break;
		case 14:
			$function = 'fahuo';
		break;
		case 15:
			$function = 'shenhe';
		break;
		case 21:
			$model = 'tuihuo';
			$function = 'shenhe';
		break;
		case 22:
			$model = 'tuihuo';
			$function = 'shouhuo';
		break;
		case 23:
			$model = 'tuihuo';
			$function = 'caiwu';
		break;
		case 24:
			$model = 'tuihuo';
			$function = 'shenhe';
		break;
		default:
			$function = '';
		break;
	}
	if(!empty($function)){
		$quanxian = $db->get_row("select departs,userIds from demo_quanxian where comId=$comId and model='$model' and INSTR(functions,'$function') limit 1");
	}
	$task = array();
	$task['comId'] = $comId;
	$task['type'] = $type;
	$task['infoId'] = $infoId;
	$task['title'] = $content;
	$task['content'] = $content;
	$task['dtTime'] = date("Y-m-d H:i:s");
	$task['userIds'] = $quanxian->userIds;
// 	if(!empty($quanxian->departs)){
// 		$userIds = $crmdb->get_var("select group_concat(id) from demo_user where comId=$comId and department in($quanxian->departs) and auditing=1");
// 		if(!empty($userIds)){
// 			if(!empty($task['userIds'])){
// 				$task['userIds'].=','.$userIds;
// 			}else{
// 				$task['userIds']=$userIds;
// 			}
// 		}
// 	}
// var_dump($task);die;
	$db->insert_update('demo_task'.$fenbiao,$task,'id');
// 	if(empty($task['userIds'])){
// 		$task['userIds'] = $crmdb->get_var("select group_concat(userId) from demo_user_quanxian where comId=$comId and INSTR(quanxian,'erp')>0");
// 	}
// 	if(empty($task['userIds'])){
// 		$task['userIds'] = $crmdb->get_var("select group_concat(id) from demo_user where comId=$comId and role=7");
// 	}
// 	$db->insert_update('demo_task'.$fenbiao,$task,'id');
// 	if(!empty($quanxian->departs)){
// 		send_message($quanxian->departs,3,$content,$content);
// 	}
// 	if(!empty($quanxian->userIds)){
// 		send_message($quanxian->userIds,2,$content,$content);
// 	}else if(empty($quanxian->departs)){
// 		send_message($task['userIds'],2,$content,$content);
// 	}
}
//获取运费方法：PS 如果商品隶属于不同运费模板，运费会分别计算并累加
//pdts格式：购物车格式array数组，每条数据中至少要包含yunfei_moban（运费模板id）、num(数量)、weight（每单位重量）,示例
//$pdts = json_decode('[{"38":{"productId":26,"yunfei_moban":1,"num":"4","weight":"1"},"42":{"productId":26,"yunfei_moban":1,"num":"4","weight":"1"}}]');
//areaId：收货地区id
//order_price:订单总价（优惠后的)
//scene：场景（1默认零售 2订货）
function get_yunfei($pdts,$order_price,$areaId,$scene=1){
	global $db;
	if(empty($pdts)){
		return 0;
	}
	if(empty($areaId)){
		return 0;
	}
	//1.按运费模板重新划分数组，多个运费模板的费用最后要加在一起
	$mobanArr = array();
	if(!empty($pdts)){
		foreach($pdts as $pdts){
			foreach ($pdts as $pdt) {
				if(!empty($pdt->yunfei_moban)){
					if(empty($mobanArr[$pdt->yunfei_moban])){
						$arr = array();
						$arr['num'] = $pdt->num;
						$arr['weight'] = $pdt->weight*$pdt->num;
						$mobanArr[$pdt->yunfei_moban] = $arr;
					}else{
						$mobanArr[$pdt->yunfei_moban]['num']+=$pdt->num;
						$mobanArr[$pdt->yunfei_moban]['weight']+=$pdt->weight;
					}
				}
			}
		}
	}
	//file_put_contents('mobanArr.txt',json_encode($mobanArr,JSON_UNESCAPED_UNICODE));
	// 没有找到运费模板对应的规则，此处应该考虑是否要设置一个默认运费模板，这样商品就不需要挨个设置了
	if(count($mobanArr)==0){
		return 0;
	}
	//2.算出省id和市id
	$sheng_id = 0;//收货地址省份id
	$shi_id = 0;//收货地址市id
	$area = $db->get_row("select * from demo_area where id=$areaId");
	if($area->parentId==0){
		$sheng_id = $areaId;
	}else{
		$sheng_id = $area->parentId;
		$shi_id = $areaId;
		$area1 = $db->get_row("select * from demo_area where id=$area->parentId");
		if($area1->parentId!=0){
			$sheng_id = $area1->parentId;
			$shi_id = $area->parentId;
		}
	}
	//3.计算运费
	$yunfei = 0;
	foreach ($mobanArr as $mobanId => $arr){
		$moban = $db->get_row("select accordby,if_man,man,mantype from yunfei_moban where id=$mobanId");
		$moban_rule = $db->get_row("select base,base_price,add_num,add_price from yunfei_moban_rule where mobanId=$mobanId and (areaIds='0' or find_in_set($sheng_id,areaIds) or find_in_set($shi_id,areaIds)) order by id desc limit 1");
		if(!empty($moban_rule)){
			$base_price = 0;//基础邮费
			$add_price = 0;//超重邮费
			//情况1：规则允许满包邮而且订单金额满足条件
			if($moban->if_man==1&&$order_price>=$moban->man){
				//仅考虑超重运费即可
				if($moban->mantype==2&&$moban_rule->add_num>0){
					if($moban->accordby==1&&$arr['num']>$moban_rule->base){
						$chaozhong = $arr['num']-$moban_rule->base;//超出数量
						$multi = ceil($chaozhong/$moban_rule->add_num);//超出数量除以超重单位
						$add_price = $moban_rule->add_price*$multi;
					}else if($moban->accordby==2&&$arr['weight']>$moban_rule->base){
						$chaozhong = $arr['weight']-$moban_rule->base;//超出数量
						$multi = ceil($chaozhong/$moban_rule->add_num);//超出数量除以超重单位
						$add_price = $moban_rule->add_price*$multi;
					}
				}
			}else{
				$base_price = $moban_rule->base_price;
				if($moban_rule->add_num>0){
					if($moban->accordby==1&&$arr['num']>$moban_rule->base){
						$chaozhong = $arr['num']-$moban_rule->base;//超出数量
						$multi = ceil($chaozhong/$moban_rule->add_num);//超出数量除以超重单位
						$add_price = $moban_rule->add_price*$multi;
					}else if($moban->accordby==2&&$arr['weight']>$moban_rule->base){
						$chaozhong = $arr['weight']-$moban_rule->base;//超出数量
						$multi = ceil($chaozhong/$moban_rule->add_num);//超出数量除以超重单位
						$add_price = $moban_rule->add_price*$multi;
					}
				}
			}
			$yunfei+=$base_price;
			$yunfei+=$add_price;
		}
	}
	return $yunfei;
}
//根据地区id获取省市区地址
function getAreaName($id){
	global $db;
	$area = $db->get_row("select title,parentId from demo_area where id=$id");
	$address = '';
	if(!empty($area)){
		$address = $area->title;
		if(!empty($area->parentId)){
			$area1 = $db->get_row("select title,parentId from demo_area where id=$area->parentId");
			if(!empty($area1)){
				$address = $area1->title.$address;
				if(!empty($area1->parentId)){
					$area2 = $db->get_var("select title from demo_area where id=$area1->parentId");
					if(!empty($area2)){
						$address = $area2.$address;
					}
				}
			}
		}
	}
	return $address;
}
function filtergl($ostr) {//昵称过滤特殊字符
	preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $ostr, $matches);
    $str = join('', $matches[0]);
    if($str==''){
        $returnstr = '';
        $i = 0;
        $str_length = strlen($ostr);
        while ($i<=$str_length){
            $temp_str = substr($ostr, $i, 1);
            $ascnum = Ord($temp_str);
            if ($ascnum>=224){
                $returnstr = $returnstr.substr($ostr, $i, 3);
                $i = $i + 3;
            }elseif ($ascnum>=192){
                $returnstr = $returnstr.substr($ostr, $i, 2);
                $i = $i + 2;
            }elseif ($ascnum>=65 && $ascnum<=90){
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }elseif ($ascnum>=128 && $ascnum<=191){ // 特殊字符
                $i = $i + 1;
            }else{
                $returnstr = $returnstr.substr($ostr, $i, 1);
                $i = $i + 1;
            }
        }
        $str = $returnstr;
        preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $str, $matches);
        $str = join('', $matches[0]);
    }
    return $str;
}
//推送消息
function send_message($uids,$type,$title,$content){
	global $db;
	include_once(ABSPATH.'/config/dt-config.php');
	include_once(ABSPATH.'/tuisong/jpush.php');
	include_once(ABSPATH.'/tuisong/config.inc.php'); 
	//include_once(ABSPATH.'/tuisong/db.class.php');
	$n_title   =  $title;
	$n_content =  $content;
	$receiver_value = $uids;
	//dataConnect();
	$sql = "SELECT max(id) from ".DB_TAB."";
	$result = $db->get_var($sql);
	$sendno = $result+1;
	$platform = platform ;
	$msg_content = json_encode(array('n_builder_id'=>0, 'n_title'=>$title, 'n_content'=>$content));
	$obj = new \jpush(masterSecret,appkeys);
	//echo  $receiver_value;exit;
	$res = $obj->send($sendno,$type,$receiver_value, 1, $msg_content, $platform);
	//echo $res;
}
function echostr($str){
	json_decode($str);
	if(json_last_error() == JSON_ERROR_NONE){
		echo $str;exit;
	}else{
		global $request;
		$db = get_zhishang_db();
		$errInfo = json_encode($request,JSON_UNESCAPED_UNICODE);
		$content = 'request:'.$errInfo.'*****str:'.$str;
		$db->query("insert into demo_err_logs(userId,dtTime,content) value(".(empty($request['userId'])?0:$request['userId']).",'".date("Y-m-d H:i:s")."','$content')");
		echo '{"code":0,"message":"系统错误，请重试，如果重复出现此错误请联系客服"}';
		exit;
	}
}
function get_zhishang_db(){
	return new \Workerman\MySQL\Connection(SERVICE_HOSTNAME, '3306', SERVICE_USER, SERVICE_PASSWORD, SERVICE_DBNAME);
}
function getXiaoshu($num,$weishu=2){
	return str_replace(',','',number_format($num,$weishu));
}
function order_jisuan_fanli($order,$if_shop_fanli){
	global $db;
	if(!empty($order->fanli_json)){
		$fanli_json=json_decode($order->fanli_json,true);
	}else{
		$fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0);
	}
	$order_money = $order->price-$order->price_payed;
	$shop = $db->get_row("select tuijianren,tuijian_bili,pingtai_fanli from demo_shops where comId=$order->comId");
	if($order->if_zong==1){
		$shezhi = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=10");
		if(empty($fanli_json['shop_fanli'])){
			file_put_contents('fanli_err.txt',json_encode($order,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
		}
		$pay_json = array();
		$lipinka_money = 0;//礼品卡支付的钱计算在返利中
		if(!empty($order->pay_json)){
			$pay_json = json_decode($order->pay_json,true);
			if(!empty($pay_json['lipinka1']['price']))$lipinka_money = $pay_json['lipinka1']['price'];
		}
		$zongfanli = $order_money+$lipinka_money-$fanli_json['shop_fanli'];
		if($zongfanli>0){
			$user_fanli = intval($zongfanli*$shezhi->user_bili)/100;
			$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
			if(!empty($fanli_json['shangji'])){
				$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
			}
			if(empty($fanli_json['shangshangji'])){
				$fanli_json['shangshangji_fanli'] = 0;
			}
			if(!empty($shop->tuijianren) && !empty($shop->tuijian_bili)){
				$fanli_json['tuijian_fanli'] = intval($zongfanli*$shop->tuijian_bili)/100;
			}
			$fanli_json['pingtai_fanli'] = $zongfanli-$fanli_json['shangshangji_fanli']-$fanli_json['shangji_fanli']-$fanli_json['tuijian_fanli'];
			$fanli_json['if_shop_fanli'] = $if_shop_fanli;
			$fanli_json['user_type'] = 1;//1总平台  2商家平台 确认收货时按user_type判断用户返利去向
			$fenbiao = getFenBiao($order->comId,20);
			$db->query("update order$fenbiao set fanli_json='".json_encode($fanli_json,JSON_UNESCAPED_UNICODE)."' where id=$order->id");
		}else{
			file_put_contents('fanli_err.txt',json_encode($order,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
		}
		return $fanli_json;
	}else{//商家自行销售的返利计算
		
		if($shop->pingtai_fanli>0){
			$fanli_json['pingtai_fanli'] = intval($order_money*$shop->pingtai_fanli)/100;
			$fanli_json['shop_fanli'] = $order_money-$fanli_json['pingtai_fanli'];
		}else{
			$fanli_json['shop_fanli'] = $order_money;
			$fanli_json['pingtai_fanli'] = 0;
		}
		$fanli_json['if_shop_fanli'] = $if_shop_fanli;
		/*
		$shezhi = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=$order->comId");
		$fanli_json['user_type'] = 2;
		$user_fanli = intval($order_money*$shezhi->user_bili)/100;
		$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
		if(!empty($fanli_json['shangji'])){
			$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
		}
		if(empty($fanli_json['shangshangji'])){
			$fanli_json['shangshangji_fanli'] = 0;
		}*/
		$fenbiao = getFenBiao($order->comId,20);
		$db->query("update order$fenbiao set fanli_json='".json_encode($fanli_json,JSON_UNESCAPED_UNICODE)."' where id=$order->id");
		return $fanli_json;
	}
}
/*
	添加文件缓存：dir:目录名称 name:缓存名称 content:缓存内容(string/数组/对象)  expire几分钟过期(0代表不过期)
*/
function cache_push($dir,$name,$content,$expire=0){
	if($dir!='' && !is_dir(ABSPATH.'cache/'.$dir)){
		@mkdir(ABSPATH.'cache/'.$dir);
	}
	if($expire>0){
		$expire = time()+$expire*60;
	}
	$data = array();
	$data['data'] = $content;
	$data['expire']=$expire;
	$file_dir = ABSPATH.'cache/'.($dir==''?'':$dir.'/').$name.'.dat';
	file_put_contents($file_dir,json_encode($data,JSON_UNESCAPED_UNICODE),LOCK_EX);
}
//获取缓存 dir:目录名称 name:缓存名称
function cache_get($dir,$name){
	$file_dir = ABSPATH.'cache/'.($dir==''?'':$dir.'/').$name.'.dat';
	$str = file_get_contents($file_dir);
	if(!empty($str)){
		$now = time();
		$datajson = json_decode($str);
		if($datajson->expire > $now || $datajson->expire==0){
			return $datajson->data;
		}else{
			return '';
		}
	}else{
		return '';
	}
}