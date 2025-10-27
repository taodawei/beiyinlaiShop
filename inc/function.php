<?php

use OSS\OssClient;
use OSS\Core\OssException;
/**
 * 获取某个文件下所有文件
 * @param $prefix
 * @param $number
 * @return array
 */
function listObjectsFile($prefix, $number)
{

    require_once('/www/wwwroot/admin.bio-swamp.com/aliyunoss/autoload.php');
    #配置OSS基本配置
    $accessKeyId = "LTAI5tGoUQzcPa17Cn3BAbg5";
	$accessKeySecret = "mXE4WHD4Af3hpFOeAL6LA8EoLM3Ikw";
	$endpoint = "http://oss-cn-nanjing.aliyuncs.com";
	$bucket= "bio-swamp";
	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	
    //组合请求数据
    $options = array(
        'delimiter' => '/',
        'prefix' => $prefix,
        'max-keys' => $number,
        'marker' => '',
    );
    try {
        #执行阿里云上传
        $result = $ossClient->listObjects($bucket, $options);
        $objectList = $result->getObjectList();
        $list = array();
        foreach ($objectList as $info) {
            //去除部分
            $size = $info->getSize();
            if ($size > 0) {
                $list[] = 'https://bio-swamp.oss-cn-nanjing.aliyuncs.com/'.$info->getKey();
            }
        }
        return array('code' => 200, 'data' => $list);
    } catch (OssException $e) {
        return array('code' => 404, 'msg' => $e->getMessage());
    }
}


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
		return get_root_path().$nopicUrl;  
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
//检查请求字符串
function checkme($power)
{
	if(empty($_SESSION[TB_PREFIX.'admin_name']) or $_SESSION[TB_PREFIX.'admin_roleId']<$power)
	{
		print_error('You do not have permission to access this page!');
	}
}
function get_str($string)
{
	if (!get_magic_quotes_gpc()) {
		return addslashes($string);
	}
	return trim($string);
}
function cleanArrayForMysql($data)
{
    $s = !empty($_GET['s'])?$_GET['s']:'';
	if(!get_magic_quotes_gpc()&&$s!='managetemplete'){
		return (is_array($data))?array_map('cleanArrayForMysql', $data):trim(addslashes($data));
		}
	else{
	  if($s=='managetemplete'){
	    $_POST["content"] = stripslashes($_POST["content"]);
	  }
	  return $data;
	  }
}
function checkSqlStr($string)
{
	$string = strtolower($string);
	return preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|_user/i', $string);
}
//重定向到某页
function redirect($url)
{
	//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>window.location.href='".$url."'</script>";
	exit;
}
//重定向到某页
function redirect_to($model,$action='index',$query='')
{
	$url='./index.php?p='.$model.'&a='.$action;
	$url.=empty($query)?'':'&'.$query;
	//echo  "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=".$url."\">";
	echo "<script>window.location.href='".$url."'</script>";
	exit;
}
function print_error($info)
{
	echo $info;
	exit;
}
function success()
{
	echo '<script>alert("授权文件安装成功！");location.href="./";</script>';
	exit;
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
/*
	Add 2007-07-07
	获取中英文混合字符串的长度
	autor:suny
*/
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
/*
	Add 2007-07-07
	获取中英文混合字符在字符串中的位置
	autor:suny
*/
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
//截取字符数
//$str-字符串
//$N-多少字符
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

} // End subString_UTF8
//去除HTML字符标记
function trimTags($string)
{
	$string=strip_tags($string);
	$string=str_replace(" ","",$string);
	$string=trim($string);
	return $string;
}
function mkdirs($path, $mode = 0777) //creates directory tree recursively
{
	$path=str_replace('\\','/',$path);
	$dirs = explode('/',$path);
	$pos = strrpos($path, ".");
	if ($pos === false) { // note: three equal signs
		// not found, means path ends in a dir not file
		$subamount=0;
	}
	else {
		$subamount=1;
	}

	for ($c=0;$c < count($dirs) - $subamount; $c++) {
		$thispath="";
		for ($cc=0; $cc <= $c; $cc++) {
			$thispath.=$dirs[$cc].'/';
		}
		if (!file_exists($thispath)) {
			//print "$thispath<br>";
			mkdir($thispath,$mode);
		}
	}
}
//验证器
function validates_presence_of($fieldName,$info)
{
	global $request;
	if(empty($request[$fieldName]))
	{
		echo $info.'was required to field!<br />';
		exit;
	}
}

function validates_email_of($fieldName,$info)
{
	if(function_exists(checkdnsrr))
	{
		global $request;
		if (!preg_match('/^[0-9a-z_\-\.]+@([0-9a-z\-]+.)+([a-z]){2,4}$/i', $request[$fieldName]))
		{		
			echo "E-mail address wrong.";
			exit;
		}
		else
		{
			list($name,$domain)=split("@",$request[$fieldName]);
			if(!checkdnsrr($domain,'MX'))
			{
				echo "E-mail not exist.";
				exit;
			}
		}
	}
}
function select($str_arr,$name,$select=null,$ev=null)
{
	if($ev)
	echo '<select id="'.$name.'" name="'.$name.'" '.$ev.'>';
	else
	echo '<select name="'.$name.'">';
	foreach ($str_arr as $k=>$v)
	{
		$selected=($select==$k)?' selected="selected" ':'';
		?>
    	<option value="<?php echo $k ?>"<?php echo $selected ?>><?php echo $v ?></option>
		<?php
	}
	echo '</select>';
}
function selectUser($str_arr,$name,$select=null,$ev=null){
    global $request;
	echo '<select id="selectUserGroup" name="'.$name.'" '.$ev.'>';
	foreach ($str_arr as $k=>$v)
	{
		$selected=($select==$k)?' selected="selected" ':'';
		?>
    	<option value="<?php echo $k ?>"<?php echo $selected ?>><?php echo $v ?></option>
		<?php
	}
	echo '</select>';
}
function selectZu($str_arr,$name,$select=null,$ev=null){
    global $request;
	echo '<select id="selectUserZu" name="'.$name.'" '.$ev.'>';
	foreach ($str_arr as $k=>$v)
	{
		$selected=($select==$k)?' selected="selected" ':'';
		?>
    	<option value="<?php echo $k ?>"<?php echo $selected ?>><?php echo $v ?></option>
		<?php
	}
	echo '</select>';
}
function db_select_box($str_arr,$key_feild,$value_feild,$name,$select=null,$ev=null)
{
	if($ev)
	echo '<select id="'.$name.'" name="'.$name.'" '.$ev.'>';
	else
	echo '<select id="'.$name.'" name="'.$name.'">';
	foreach ($str_arr as $o)
	{
		$selected=($select==$o->$key_feild)?' selected="selected" ':' ';
		?>
    	<option value="<?php echo $o->$key_feild ?>" <?php echo $selected ?>><?php echo $o->$value_feild ?></option>
		<?php
	}
	echo '</select>';
}
function db_radio_box($str_arr,$key_feild,$value_feild,$name,$select=null)
{
	foreach ($str_arr as $o)
	{
		$selected=($select==$o->$key_feild)?' checked="checked" ':' ';
		?>
    	<span><input type="radio" <?php echo $selected ?> id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $o->$key_feild ?>"><?php echo $o->$value_feild ?></span>
		<?php
	}
}
/*
	Add 2007-07-07
	截取一段字符串中的字符并标示出来
	autor:suny
*/
function get_keyword_str($str,$keyword,$getstrlen)
{
	if(cnStrLen($str)> $getstrlen) 
	{
		$strlen = cnStrLen($keyword);
		$strpos = cnStrPos($str,$keyword);
		$halfStr = intval(($getstrlen-$strlen)/2);
		$str = cnSubStr($str,($strpos - $halfStr),$halfStr).$keyword.cnSubStr($str,($strpos + $strlen),$halfStr);
		return str_replace($keyword,'<span style="font-size: 12px; color: #F30;">'.$keyword.'</span>',$str).'...';
	}
	else
	{
		return str_replace($keyword,'<span style="font-size: 12px; color: #F30;">'.$keyword.'</span>',$str);
	}
}
/*检验文件大小*/
function DisplayFileSize($filesize){
	$array = array(
	'YB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
	'ZB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024, 'EB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
	'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
	'TB' => 1024 * 1024 * 1024 * 1024,
	'GB' => 1024 * 1024 * 1024,
	'MB' => 1024 * 1024,
	'KB' => 1024,     );
	if($filesize <= 1024)
	{
		$filesize = $filesize . ' B';
	}
	foreach($array AS $name => $size)
	{
		if($filesize > $size || $filesize == $size)
		{
			$filesize = round((round($filesize / $size * 100) / 100), 0) . ' ' . $name;
		}
	}
	return $filesize;
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
//获得文件格式前缀
function extend_1($file_name)
{
	$extend =explode("." , $file_name); 
	$va=count($extend)-2;
	return $extend[$va];
}
//获得文件格式后缀
function extend_2($file_name)
{
	$extend =explode("." , $file_name); 
	$va=count($extend)-1;
	return $extend[$va];
}
//列出某文件夹内所有文件
function rec_listFiles($from = '.',$type='php')
{
    if(! is_dir($from))
        return false;
   
    $files = array();
    if( $dh = opendir($from))
    {
        while( false !== ($file = readdir($dh)))
        {
            // Skip '.' and '..'
            if( $file == '.' || $file == '..'||extend_2($file)!=$type)
                continue;
            //$path = $from . '/' . $file;
            $path = $file;
            if( is_dir($path) )
                //$files += rec_listFiles($path);
				continue;
            else
                $files[] = $path;
        }
        closedir($dh);
    }
    return $files;
}
//对整个目录进行拷贝
function dir_copy($fdir,$tdir)
{   
	if(is_dir($fdir))
	{
		if (!is_dir($tdir))
		{
			mkdir($tdir);
		}
		$handle =opendir($fdir);
		while(false!==($filename=readdir($handle)))
		{	  
			if($filename!="."&&$filename!="..")dir_copy($fdir."/".$filename,$tdir."/".$filename);	 
		}
		closedir($handle);		
		return true;
	}
	else 
	{
		copy($fdir,$tdir);
		return true;
	}	
}
function RemoveXSS($val) { 
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 
    // this prevents some character re-spacing such as <java\0script> 
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some          // inputs 
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val); 
    
    // straight replacements, the user should never need these since they're normal characters 
    // this prevents like <IMG SRC=@avascript:alert('XSS')> 
    $search = 'abcdefghijklmnopqrstuvwxyz'; 
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $search .= '1234567890!@#$%^&*()'; 
    $search .= '~`";:?+/={}[]-_|\'\\'; 
    for ($i = 0; $i < strlen($search); $i++) { 
        // ;? matches the ;, which is optional 
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 

        // @ @ search for the hex values 
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);//with a ; 
        // @ @ 0{0,7} matches '0' zero to seven times 
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
    } 

    // now the only remaining whitespace attacks are \t, \n, and \r 
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
    $ra = array_merge($ra1, $ra2); 
   
    $found = true; // keep replacing as long as the previous round replaced something 
    while ($found == true) { 
        $val_before = $val; 
        for ($i = 0; $i < sizeof($ra); $i++) { 
            $pattern = '/'; 
            for ($j = 0; $j < strlen($ra[$i]); $j++) { 
                if ($j > 0) { 
                    $pattern .= '('; 
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
                    $pattern .= '|'; 
                    $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
                    $pattern .= ')*'; 
                } 
                $pattern .= $ra[$i][$j]; 
            } 
            $pattern .= '/i'; 
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 
            if($val!='com_title'){
            	$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 
            }
            if ($val_before == $val) { 
                // no replacements were made, so exit the loop 
                $found = false; 
            } 
        } 
    } 
    return $val; 
}
//邮件发送  by grysoft
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
function get_style_file($func_name,$style=0)
{
	if(defined('MOBILE')&&MOBILE===true)
		$style_file1 = get_abs_skin_root().'m.index/'.$func_name.'_'.$style.'.php';
	else
		$style_file1 = get_abs_skin_root().'index/'.$func_name.'_'.$style.'.php';
	$style_file2 = ABSPATH.'content/index/style/index_'.$func_name.'_0.php';
	return is_file($style_file1)?$style_file1:$style_file2;
}
/*注销db*/
function destorydb(){
	global $db,$tempdb;
	$tempdb=$db;
	$db=null;
}
/*还原db*/
function recoverdb(){
	global $db,$tempdb;
	$db=$tempdb;
	$tempdb=null;
}
/*截取字串  一滴水*/
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
function string_join($var,$join='-'){
	$join=$join?" $join ":"";
	return $var?$var.$join:'';
}
//链接主站数据库
$dirName=dirname(__FILE__);
require($dirName.'/class.crmdb.php');
require($dirName.'/../config/dt-service.php');
class Crmdb extends dtdb1{
	function __construct() {
		parent::__construct(SERVICE_USER, SERVICE_PASSWORD, SERVICE_DBNAME, SERVICE_HOSTNAME);
	}
}
function getCrmDb(){
	$crmdb = new Crmdb();
	return $crmdb;
}

//获取所有下级的分类，用,分开
function getZiChangeIds($id){
	global $db;
	$str = '';
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ziIds = $db->get_results("select id from demo_change_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
	if(!empty($ziIds)){
		foreach ($ziIds as $ziId) {
			$str .= ','.$ziId->id.getZiChangeIds($ziId->id);
		}
	}
	return $str;
}

function getZiDataByTable($id, $table)
{
    global $db;
    
	$str = '';
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ziIds = $db->get_results("select id from $table where comId=$comId and parentId=$id order by ordering desc,id asc");
	if(!empty($ziIds)){
		foreach ($ziIds as $ziId) {
			$str .= ','.$ziId->id.getZiDataByTable($ziId->id, $table);
		}
	}
	
	return $str;
}

//获取所有下级的分类，用,分开
function getZiIds($id){
	global $db;
	$str = '';
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ziIds = $db->get_results("select id from demo_product_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
	if(!empty($ziIds)){
		foreach ($ziIds as $ziId) {
			$str .= ','.$ziId->id.getZiIds($ziId->id);
		}
	}
	return $str;
}
//获取所有下级的地区，用,分开
function getZiAreas($id){
	global $db;
	$str = '';
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ziIds = $db->get_results("select id from demo_area where parentId=$id order by id asc");
	if(!empty($ziIds)){
		foreach ($ziIds as $ziId) {
			$str .= ','.$ziId->id.getZiIds($ziId->id);
		}
	}
	return $str;
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
function getXiaoshu($num,$weishu=2){
	return str_replace(',','',number_format($num,$weishu));
	/*$chushu = pow(10,$weishu);
	return floor($num*$chushu)/$chushu;*/
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
//获取审批人
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
function insert_update($table,$obj,$id){
	global $db;
	//主键不为空，执行修改
	if(!empty($obj[$id])){
		$sql = "update $table set ";
		$sql1 = '';
		foreach ($obj as $key => $val) {
			if($key!=$id){
				$sql1.=','.$key."='".$val."'";
			}
		}
		if(!empty($sql1)){
			$sql1 = substr($sql1,1);
			$sql.=$sql1;
			$sql.=" where $id=".$obj[$id];
		}
	}else{
		$sql = "insert into $table(";
		$sql1 = '';
		$sql2 = '';
		foreach ($obj as $key => $val) {
			if($key!=$id){
				$sql1.=','.$key;
				$sql2.=",'".$val."'";
			}
		}
		if(!empty($sql1)){
			$sql1 = substr($sql1,1);
			$sql2 = substr($sql2,1);
			$sql.=$sql1.') value('.$sql2.')';
		}
	}
   // echo $sql;die;
	return $db->query($sql);

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
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$msg = array();
	$msg['comId'] = $comId;
	$msg['toId'] = $toId;
	$msg['content'] = $content;
	$msg['type'] = $type;
	$msg['infoId'] = $infoId;
	$msg['dtTime'] = date("Y-m-d H:i:s");
	insert_update('dinghuo_msg',$msg,'id');
}
//给会员发消息
function addUserMsg($toId,$fenbiao,$content,$type,$infoId){
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
	}
	$fenbiao = getFenBiao($comId,20);
	$msg = array();
	$msg['comId'] = $comId;
	$msg['userId'] = $toId;
	$msg['content'] = $content;
	$msg['type'] = $type;
	$msg['infoId'] = $infoId;
	$msg['dtTime'] = date("Y-m-d H:i:s");
	insert_update('user_msg'.$fenbiao,$msg,'id');
}
//添加任务消息并推送
function addTaskMsg($type,$infoId,$content,$comId=0){
	global $db;
	$crmdb = new Crmdb();
	if(empty($comId)){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	}
	$comId = 888;
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
		case 31:
			$model = 'lingshou';
			$function = 'order_index';
		break;
		case 41:
			$model = 'users';
			$function = 'users_index';
		break;
		default:
			$function = '';
		break;
	}
	if(!empty($function)){
		$quanxian = $db->get_row("select departs,userIds from demo_quanxian where comId=$comId and model='$model' and INSTR(functions,'$function') limit 1");
	}
	$title = str_replace('，请及时处理', '',$content);
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
// 	if(empty($task['userIds'])){
// 		$task['userIds'] = $crmdb->get_var("select group_concat(userId) from demo_user_quanxian where comId=$comId and INSTR(quanxian,'erp')>0");
// 	}
// 	if(empty($task['userIds'])){
// 		$task['userIds'] = $crmdb->get_var("select group_concat(id) from demo_user where comId=$comId and role=7");
// 	}
// var_dump($task);die;
	$db->insert_update('demo_task'.$fenbiao,$task,'id');
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
		foreach($pdts as $pdts1){
			foreach ($pdts1 as $pdt) {
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
/*计算订单的返利信息
	order：订单  
	if_shop_fanli：确认收货后是否给商家返利(用于礼品卡支付和余额支付的时候传1)
	返回：返利对象
*/
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
	$obj = new jpush(masterSecret,appkeys);
	//echo  $receiver_value;exit;
	$res = $obj->send($sendno,$type,$receiver_value, 1, $msg_content, $platform);
	//echo $res;
}
function dt_article_index($id){
	global $db;
	return $db->get_var("select content from demo_article where id=$id");
}
function echostr($str){
    // var_dump($str);die;
	json_decode($str);
	if(json_last_error() == JSON_ERROR_NONE){
		echo $str;exit;
	}else{
// 		global $request;
// 		$db = get_zhishang_db();
// 		$errInfo = json_encode($request,JSON_UNESCAPED_UNICODE);
// 		$content = 'request:'.$errInfo.'*****str:'.$str;
// 		$db->query("insert into demo_err_logs(userId,dtTime,content) value(".(empty($request['userId'])?0:$request['userId']).",'".date("Y-m-d H:i:s")."','$content')");
// 		echo '{"code":0,"message":"系统错误，请重试，如果重复出现此错误请联系客服"}';
// 		exit;
	}
}

// function get_zhishang_db(){
// 	return new \Workerman\MySQL\Connection(SERVICE_HOSTNAME, '3306', SERVICE_USER, SERVICE_PASSWORD, SERVICE_DBNAME);
// }
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
function get_url($url){
    $url=substr_replace($url,"",0,1);
    $url=explode("&",$url);
    $data=[];
    if(!empty($url)){
        foreach($url as $k){
            $aa=explode('=',$k);
            $data[$aa[0]]=!empty($aa[1])?$aa[1]:'';
        }
    }
    return $data;
}

function checkUrl($arr, $action){
    $arr=explode('|',$arr);
    if(in_array($action, $arr)){
        return 1;
    }
    
    return  0;
}

function chekurl($arr,$html,$type=0){
    $preg='/<a .*?href="(.*?)".*?>/is';
    preg_match_all($preg,$html,$array2);
    $str=$array2[1][0];
    if($str=="javascript:"||$str=="javascript:void(0)"||$str=="javascript:;"||$str=="javascript:void(0);"){
        $preg='/<a .*?_href="(.*?)".*?>/is';
        preg_match_all($preg,$html,$array2);
        $str=$array2[1][0];
    }
    $str=get_url($array2[1][0]);
    $url="?m=".$str['m']."&s=".$str['s']."&a=".(empty($str['a'])?'index':$str['a']);
    $arr=explode('|',$arr);
    $ht="";
    foreach($arr as $j){
        $arr1=get_url($j);
        $arr2="?m=".$arr1['m']."&s=".$arr1['s']."&a=".(empty($arr1['a'])?'index':$arr1['a']);
        if($url==$arr2){
            $ht=$html;
            break;
        }
    }
    if($type==0){
        echo $ht;
    }else{
        return $ht;
    }
}

//支付完成方法
function order_pay_done($order){ 
    global $db;
    if($order->peisong_type==4){
        /*$db->query("update users set `cost`=`cost`+$order->price where id=$order->userId and comId=$order->comId");
		$request['orderId'] = $orderId;
		diancan_pay_done($order->id,$order->userId,$order->comId);*/
    }else{
        $order_fenbiao = getFenbiao($order->comId,20);
        $orderId = $order->id;
        $userId = $order->userId;
        $order_comId = $order->comId;
        $if_tongbu = $db->get_var("select if_tongbu from demo_shezhi where comId=$order_comId");
        $db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
        if($order->tuan_id>0){
            $tuan = $db->get_row("select * from demo_tuan where id=$order->tuan_id");
            if($tuan->status==0){
                $userIds = empty($tuan->userIds)?$order->userId:$tuan->userIds.','.$order->userId;
                $uids = explode(',',$userIds);
                $userIds = implode(',',array_unique($uids));
                $orderIds = empty($tuan->orderIds)?$orderId:$tuan->orderIds.','.$orderId;
                $nums = $tuan->nums+$order->pdtNums;
                $db->query("update demo_tuan set userIds='$userIds',orderIds='$orderIds',nums=$nums where id=$order->tuan_id");
                //团购成功
                if($nums>=$tuan->user_num){
                    //检查库存
                    $orders = $db->get_results("select inventoryId,storeId,pdtNums from order$order_fenbiao where id in($orderIds)");
                    foreach ($orders as $ord){
                        $kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$ord->inventoryId and storeId=$ord->storeId limit 1");
                        $kc = $kucun->kucun;
                        if($kc<$ord->pdtNums){
                            $db->query("update demo_tuan set status=-1,reason='库存不足' where id=$order->tuan_id");
                            $oids = explode(',',$orderIds);
                            foreach ($oids as $oid){
                                $timed_task = array();
                                $timed_task['dtTime'] = 0;
                                $timed_task['comId'] = $order_comId;
                                $timed_task['router'] = 'order_autotuikuan';
                                $timed_task['params'] = '{"order_id":'.$oid.',"message":"拼团失败"}';
                                $db->insert_update('demo_timed_task',$timed_task,'id');
                            }
                            return;
                        }
                    }
                    $db->query("update demo_tuan set status=1 where id=$order->tuan_id");
                    //$db->query("update order$order_fenbiao set status=2 where id=$order->tuan_id");
                    if($tuan->type==2){
                        $product =  $db->get_row("select title from demo_product where id=$tuan->productId");
                        $weight = $db->get_var("select sum(weight) from order$order_fenbiao where id in($orderIds)");
                        $fahuo = array();
                        $fahuo['comId'] = $order_comId;
                        $fahuo['mendianId'] = $order->mendianId;
                        $fahuo['addressId'] = $tuan->addressId;
                        $fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
                        $fahuo['orderIds'] = $orderIds;
                        $fahuo['type'] = 2;
                        $fahuo['showTime'] = date("Y-m-d H:i:s");
                        $fahuo['storeId'] = $order->storeId;
                        $fahuo['dtTime'] = date("Y-m-d H:i:s");
                        $fahuo['shuohuo_json'] = $tuan->shouhuo_json;
                        $fahuo['productId'] = $tuan->productId;
                        $fahuo['tuanzhang'] = $tuan->tuanzhang;
                        $fahuo['product_title'] = $product->title;
                        $fahuo['fahuo_title'] = $product->title;
                        $fahuo['product_num'] = $nums;
                        $fahuo['weight'] = $weight;
                        $fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
                        $db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
                        $fahuoId = $db->get_var("select last_insert_id();");
                        $db->query("update order$order_fenbiao set fahuoId=$fahuoId,status=2 where id in($orderIds)");
                        $db->query("update order_detail$order_fenbiao set status=1 where orderId in($orderIds)");
                        //增加库存的预购数量
                        $oids = explode(',',$orderIds);
                        foreach ($oids as $oid){
                            $o = $db->get_row("select * from order$order_fenbiao where id=$oid");
                            //$db->query("update demo_kucun set yugouNum=yugouNum+".$o->pdtNums." where inventoryId=$o->inventoryId and storeId=$o->storeId limit 1");
                            //增加产品订单数量
                            $db->query("update demo_product_inventory set orders=orders+".$o->pdtNums." where id=$o->inventoryId");
                            $db->query("update demo_product set orders=orders+".$o->pdtNums." where id=$inventory->productId");
                        
                        
                           // addTaskMsg(31,$oid,'您的商城有新的团购订单，请及时处理',$order_comId);
                            print_order($o);
                            addFaHuo($o);
                        }
                    }else{
                        $oids = explode(',',$orderIds);
                        foreach ($oids as $oid){
                            $order = $db->get_row("select * from order$order_fenbiao where id=$oid");
                            //发货
                            addFaHuo($order);
            
                    }
                }
                }else{
                //增加退款任务
                $timed_task = array();
                $timed_task['dtTime'] = 0;
                $timed_task['comId'] = $order_comId;
                $timed_task['router'] = 'order_autotuikuan';
                $timed_task['params'] = '{"order_id":'.$order->id.',"message":"拼团失败"}';
                $db->insert_update('demo_timed_task',$timed_task,'id');
                $no_yugu = 1;
            }
            }
        }else{
            addFaHuo($order);
        }
        if($no_yugu!=1){
            $fanli_json = json_decode($order->fanli_json);
            if($fanli_json->shangji>0 && $fanli_json->shangji_fanli>0){
                $yugu_shouru = array();
                $yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
                $yugu_shouru['userId'] = $fanli_json->shangji;
                $yugu_shouru['order_type'] = 1;
                $yugu_shouru['orderId'] = $order->id;
                $yugu_shouru['dtTime'] = date("Y-m-d");
                $yugu_shouru['money'] = $fanli_json->shangji_fanli;
                $yugu_shouru['from_user'] = $order->userId;
                $yugu_shouru['remark'] = '下级返利';
                $yugu_shouru['order_orderId'] = $order->orderId;
                $yugu_shouru['order_comId'] = $order->comId;
                $db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
            }
            if($fanli_json->buyer_fanli>0){
                $yugu_shouru = array();
                $yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
                $yugu_shouru['userId'] = $order->userId;
                $yugu_shouru['order_type'] = 1;
                $yugu_shouru['orderId'] = $order->id;
                $yugu_shouru['dtTime'] = date("Y-m-d");
                $yugu_shouru['money'] = $fanli_json->buyer_fanli;
                $yugu_shouru['from_user'] = $order->userId;
                $yugu_shouru['remark'] = '自购返利';
                $yugu_shouru['order_orderId'] = $order->orderId;
                $yugu_shouru['order_comId'] = $order->comId;
                $db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
            }
            if($fanli_json->shangshangji>0 && $fanli_json->shangshangji_fanli>0){
                $yugu_shouru = array();
                $yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
                $yugu_shouru['userId'] = $fanli_json->shangshangji;
                $yugu_shouru['order_type'] = 1;
                $yugu_shouru['orderId'] = $order->id;
                $yugu_shouru['dtTime'] = date("Y-m-d");
                $yugu_shouru['money'] = $fanli_json->shangshangji_fanli;
                $yugu_shouru['from_user'] = $order->userId;
                $yugu_shouru['remark'] = '团队返利';
                $yugu_shouru['order_orderId'] = $order->orderId;
                $yugu_shouru['order_comId'] = $order->comId;
                $db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
            }
            if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
                $yugu_shouru = array();
                $yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
                $yugu_shouru['userId'] = $fanli_json->tuijian;
                $yugu_shouru['order_type'] = 1;
                $yugu_shouru['orderId'] = $order->id;
                $yugu_shouru['dtTime'] = date("Y-m-d");
                $yugu_shouru['money'] = $fanli_json->tuijian_fanli;
                $yugu_shouru['from_user'] = $order->userId;
                $yugu_shouru['remark'] = '推荐店铺返利';
                $yugu_shouru['order_orderId'] = $order->orderId;
                $yugu_shouru['order_comId'] = $order->comId;
                $db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
            }
            if($fanli_json->shequ_id>0 && $fanli_json->shequ_fanli>0){
                $yugu_shouru = array();
                $yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
                $yugu_shouru['userId'] = $fanli_json->shequ_id;
                $yugu_shouru['order_type'] = 1;
                $yugu_shouru['orderId'] = $order->id;
                $yugu_shouru['dtTime'] = date("Y-m-d");
                $yugu_shouru['money'] = $fanli_json->shequ_fanli;
                $yugu_shouru['from_user'] = $order->userId;
                $yugu_shouru['remark'] = '社区返利';
                $yugu_shouru['order_orderId'] = $order->orderId;
                $yugu_shouru['order_comId'] = $order->comId;
                $db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
            }
        }
        if($order->if_zong!=1){
            $db->query("update users set `cost`=`cost`+$order->price where id=$order->userId and comId=$order->comId");
        }
       
    }
}
//打印订单
function print_order($order){
    global $db;
    $comId = $order->comId;
    $print = $db->get_row("select * from demo_prints where comId=$comId and storeId=$order->storeId and status=1 and if_auto=1 limit 1");
    $level = '';
    if(!empty($print)){
        require_once(ABSPATH.'inc/print.class.php');
        $shouhuo_json = json_decode($order->shuohuo_json,true);
        $product_json = json_decode($order->product_json,true);
        $title = '订单详情';
        if($order->shequ_id>0){
            $title = $db->get_var("select title from demo_shequ where id=$order->shequ_id");
        }
        $content = '';                          //打印内容
        $content .= '<FB><center>'.$title.'</center></FB>';
        $content .= '\n';
        $content .= str_repeat('-',32);
        $content .= '\n';
        $content .= '<FB>姓名:'.$shouhuo_json['收件人'].($level==''?'':'(级别：'.$level.')').'</FB>\n';
        $content .= '<FB>联系电话:</FB><FS>'.$shouhuo_json['手机号'].'</FS>\n';
        $content .= '<FB>配送地址:'.($order->peisong_type==1?'站点自提':$shouhuo_json['所在地区'].$shouhuo_json['详细地址']).'</FB>\n';
        $content .= '<FB>下单时间: '.$order->dtTime.'</FB>\n';
        if(!empty($order->peisong_time) && $order->peisong_type==2){
            $content .= '<FB>配送时间: '.$order->peisong_time.'</FB>\n';
        }
        $content .= str_repeat('-',32);
        $content .= '\n';
        $num = 0;
        if(!empty($product_json))
        {
            foreach($product_json as $k=>$v){
                $num+=$v['num'];
                $content .= '<FS>'.$v['title'].($v['key_vals']=='无'?'':'【'.$v['key_vals'].'】').'：'.$v['price_sale'].'*'.$v['num'].'</FS>\n';
            }
        }
        $content .= str_repeat('-',32)."\n";
        $content .= '\n';
        $content .= '<FS>数量: '.$num.'</FS>\n';
        $content .= '<FS>总计: '.$order->price.'元</FS>\n';
        $content .= '<FS>备注: '.$order->remark.'</FS>\n';
        $content .= '<FS>订单编号: '.$order->orderId.'</FS>\n';
        $content .= '<FS>支付状态: 已支付</FS>\n';
        $content .= '<FS>打印时间: '.date("Y-m-d H:i:s").'</FS>\n';
        $prints = new \Yprint();
        $content = $content;
        //$apiKey = "40f9b00bd79d73c056db5dcf906cbc97f02b920e";
        //$msign = 'a86n3hyzrfdy';
        //打印
        //file_put_contents('print.txt',$content);
        $prints->action_print($print->userId,$print->Tnumber,$content,$print->Akey,$print->Tkey);
    }
}


//支付成功后减去库存
function deductKuCun($product_json){
    global $db;  
    $product_arr = json_decode($product_json);
    $dtTime = date('Y-m-d H:i:s');
    foreach($product_arr as $p){
        $db->query("update demo_kucun set kucun=kucun-".$p->num." where inventoryId=".$p->id." limit 1");
        $kucun_num = $db->get_var("select kucun from demo_kucun where inventoryId=".$p->id." limit 1");
          //库存记录	//写入记录
  		$pdtInfoArry['sn'] = $p->sn;
		$pdtInfoArry['title'] = $p->title;
		$pdtInfoArry['key_vals'] = $p->key_vals;
		$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
		$rukuSql = "insert into demo_kucun_jiludetail8(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben,zhesun)  value(888,0,$p->id,$p->productId,'$pdtInfo',5,'默认库','-$p->num',1,'$kucun_num','',2,'下单出库','$dtTime','','','','')";

		$db->query($rukuSql);
        
    }

}

  //取消订单加库存
function addKuCun($orderId){
    global $db;   
    $details = $db->get_results("select inventoryId,num,productId,pdtInfo from order_detail8 where orderId=$orderId");
    $dtTime = date('Y-m-d H:i:s');
    if(!empty($details)){
        foreach ($details as $detail){
          //  $this->addKuCun($detail->inventoryId,$detail->num);
            $db->query("update demo_kucun set kucun=kucun+".$detail->num." where inventoryId=".$detail->inventoryId." limit 1");
            $kucun_num = $db->get_var("select kucun from demo_kucun where inventoryId=".$detail->inventoryId." limit 1");
            //库存记录	//写入记录
			$rukuSql = "insert into demo_kucun_jiludetail8(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben,zhesun)  value(888,0,$detail->inventoryId,$detail->productId,'$detail->pdtInfo',5,'默认库','$detail->num',1,'$kucun_num','',1,'取消订单入库','$dtTime','','','','')";
			//echo $rukuSql;die;
			$db->query($rukuSql);
            
            $db->query("update demo_product_inventory set orders=orders-".$detail->num." where id=".$detail->inventoryId);
            $db->query("update demo_product set orders=orders-".$detail->num." where id=".$detail->inventoryId);
    
        }
    }
}



   //计算升级返利
function handleFanLi($user,$order){
    global $db;
    /**
     *  1、会员购买499 成为代理
        2、代理推荐代理 奖励100元
        3、推荐两名代理成为合伙人
        4、合伙人推荐代理奖励 300元
        5、代理成为合伙人后， 跳出原有团队 ， 该下级两名代理成为该上级的下级
        6、成为合伙人后可获得间推奖 200 元
        7、平级奖，合伙人上级为合伙人的情况下， 上级合伙人拿下级收益的 10%
     */
    $comId = 888;
    $user_level = $db->get_results("select * from user_level where id  > 74");   //升级条件

    $shengji_num = $shengji_price = $shengji_jl = '';
    foreach($user_level as $v){
        $shengji_num[$v->id] = $v->yq_num;
        $shengji_price[$v->id] = $v->jifen;
        $shengji_jl[$v->id] = $v->price;
        $shengji_jt[$v->id] = $v->jt_price;
    }

 //   $demo_shezhi  = $db->get_row("select shangji_price,shangshangji_price,shangshangji_jt_price from demo_shezhi where comId = 888");   //设置
    //升级并累计个人业绩
    switch($user_level){
        case 74:
            $db->query("update users set level=75,order_price =order_price+$order->price,total_order_price=total_order_price+$order->price  where id=$user->id");
            break;
        case 75:
            //推荐两名代理升级合伙人
            $db->query("update users set order_price =order_price+$order->price,total_order_price=total_order_price+$order->price  where id=$user->id");
            break;
        case 76:
            $db->query("update users set order_price =order_price+$order->price,total_order_price=total_order_price+$order->price  where id=$user->id");
            break;
    }
    //上级升级累计业绩和分红
    $shangji = $user->shangji;
    $shangshangji = $user->shangshangji;
    //查看上级级别
    $shangji_level = $db->get_var("select level from users where id = $shangji");
    if($shangji_level == 75){
        //查看有几名下级
        $xj_num =  $db->get_results("select id,shangji,shangshangji from users where shangji = $shangji and level = 75");
        if(count($xj_num) >= $shengji_num[$shangji_level]){
            $db->query("update users set level=76,money=money+$shengji_jl[$shangji_level],earn= earn+ $shengji_jl[$shangji_level],month_money = month_money+$shengji_jl[$shangji_level]  where id=$shangji");
            //代理成为合伙人后， 跳出原有团队 ， 该下级两名代理成为该上级的下级
            $shangshangji_user = $db->get_row("select * from users where id = $shangshangji");
            foreach($xj_num as $vv){
                $db->query("update users set  shangji= $shangshangji_user->id,shangshangji = $shangshangji_user->shangji,y_shangji= $vv->shangji where id=$vv->id");
            }

        }
        $shangjishangji_level = $db->get_var("select level from users where id = $shangshangji");
        if($shangjishangji_level == 76){
            $db->query("update users set money=money+$shengji_jt[$shangjishangji_level],earn= earn+ $shengji_jt[$shangjishangji_level],month_money = month_money+$shengji_jt[$shangjishangji_level]  where id=$shangshangji");
        }
    }
    if($shangji_level == 76){
       $db->query("update users set money=money+$shengji_jl[$shangji_level],earn= earn+ $shengji_jl[$shangji_level],month_money = month_money+$shengji_jl[$shangji_level]  where id=$shangji");
    }

}


//退款方法
function tuikuan($order,$jilu=0){
  
    global $db;
    $userId = $order->userId;
    $comId = $order->comId;
    $orderId = $order->id;
    $zong_fenbiao = $fenbiao = getFenbiao($comId,20);

    $pay_json = json_decode($order->pay_json,true);
    if($jilu){
        $pay_json = array(
    	    'yue' => array(
    	        'if_zong' => 0,
    	        'price' => $jilu->tk_price
    	    ), 
    	    'jifen' => array(
    	        'desc' => $jilu->tk_price,
    	        'price' => $jilu->tk_price
    	    ),    
    	);    
    }

    //file_put_contents('pay.txt',json_encode($pay_json,JSON_UNESCAPED_UNICODE));
    //积分返回
    if(!empty($pay_json['jifen']['desc'])){
        $jifen = (int)$pay_json['jifen']['desc'];
        $db->query("update users set jifen=jifen+$jifen where id=$userId");
        $yue = $db->get_var('select jifen from users where id='.$userId);
        $jifen_jilu = array();
        $jifen_jilu['userId'] = $userId;
        $jifen_jilu['comId'] = $comId;
        $jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
        $jifen_jilu['jifen'] = $jifen;
        $jifen_jilu['yue'] = $yue;
        $jifen_jilu['type'] = 1;
        $jifen_jilu['oid'] = $order->id;
        $jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
        $jifen_jilu['remark'] = '取消订单,返还支付积分';
        $db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
    }
    //优惠券返还
    if(!empty($pay_json['yhq']['desc'])){
        $db->query("update user_yhq$zong_fenbiao set status=0,orderId=0 where id=".(int)$pay_json['yhq']['desc']);
    }
    //抵扣金支付
    if(!empty($pay_json['lipinka']['price'])){
        $giftId = (int)$pay_json['lipinka']['cardId'];
        $money = $pay_json['lipinka']['price'];
        $db->query("update gift_card$zong_fenbiao set yue=yue+$money where id=$giftId");
        $liushui = array();
        $liushui['cardId']=$giftId;
        $liushui['money']=$money;
        $liushui['yue']=$db->get_var("select yue from gift_card$zong_fenbiao where id=$giftId");
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单取消';
        $liushui['orderInfo']='订单取消，支付号：'.$order->orderId;
        $liushui['orderId']=$orderId;
        insert_update('gift_card_liushui'.$zong_fenbiao,$liushui,'id');
    }
    //礼品卡支付
    if(!empty($pay_json['lipinka1']['price'])){
        $giftId = (int)$pay_json['lipinka1']['cardId'];
        $money = $pay_json['lipinka1']['price'];
        $db->query("update lipinka set yue=yue+$money where id=$giftId");
        $liushui = array();
        $liushui['cardId']=$giftId;
        $liushui['money']=$money;
        $liushui['yue']=$db->get_var("select yue from lipinka where id=$giftId");
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单取消';
        $liushui['orderInfo']='订单取消，支付号：'.$order->orderId;
        $liushui['orderId']=$orderId;
        insert_update('lipinka_liushui',$liushui,'id');
    }
    //余额支付
    if(!empty($pay_json['yue'])){
        // $money = $pay_json['yue']['price'];
   
        // $db->query("update users set money=money+$money where id=".$order->userId);
        // $yue = $db->get_var('select money from users where id='.$order->userId);
        
        // $liushui = array();
        // $liushui['userId']=$order->userId;
        // $liushui['comId']=$comId;
        // $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
        // $liushui['money']=$money;
        // $liushui['yue']=$yue;
        // $liushui['type']=2;
        // $liushui['dtTime']=date("Y-m-d H:i:s");
        // $liushui['remark']='订单取消';
        // $liushui['orderInfo']='订单取消，订单号：'.$order->orderId;
        // $liushui['order_id']=$order->id;
        // $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
        
        foreach ($pay_json['yue'] as $info){
            $money = $info['price'];
            $userId = $order->userId;
            $cardId = $info['cardId'];
            
            $db->query("update user_card set yue=yue+$money where id= $cardId");
            $db->query("update users set money=money+$money where id= $userId");
            
            $liushui = array();
            $liushui['userId']=$userId;
            $liushui['comId']=$comId;
            $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
            $liushui['money']= $money;
            $liushui['cardId'] = $cardId;
            $liushui['yue']= $db->get_var("select yue from user_card where id = $cardId");
            $liushui['type']=2;
            $liushui['dtTime']=date("Y-m-d H:i:s");
            $liushui['remark']='订单取消';
            $liushui['orderInfo'] = '订单取消，订单号：'.$order->orderId;
            $liushui['order_id'] = $order->id;
            
            $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
            
        }
        
    }
    //微信支付返余额
    if(!empty($pay_json['weixin']['price'])){
        $money = $pay_json['weixin']['price'];
        
        $db->query("update users set wx_money=wx_money+$money where id= $userId");
        
        $liushui = array();
        $liushui['userId']=$userId;
        $liushui['comId']=$comId;
        $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
        $liushui['money']= $money;
        $liushui['cardId'] = 0;
        $liushui['yue']= $db->get_var("select wx_money from users where id = $userId");
        $liushui['type']=2;
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单取消';
        $liushui['orderInfo'] = '订单取消，订单号：'.$order->orderId;
        $liushui['order_id'] = $order->id;
        
        $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
    }
    //微信小程序返余额
    if(!empty($pay_json['applet']['price'])){
        $money = $pay_json['applet']['price'];
        $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 and status=1 limit 1");
        if(!empty($weixin_set->info)){
            $weixin_arr = json_decode($weixin_set->info);
        }
        if(!empty($weixin_arr->sslkey) && !empty($weixin_arr->sslcert)){
            define('WX_APPID',$weixin_arr->appid);
            define('WX_MCHID',$weixin_arr->mch_id);
            define('WX_KEY',$weixin_arr->key);
            define('WX_APPSECRET',$weixin_arr->appsecret);
            define('WX_SSLKEY',ABSPATH.$weixin_arr->sslkey);
            define('WX_SSLCERT',ABSPATH.$weixin_arr->sslcert);
            require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
            require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
            $logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
            $log = \Log::Init($logHandler, 15);
            $transaction_id = $pay_json['applet']['desc'][0];
            $total_fee = $money*100;
            $refund_fee = $total_fee;
            $input = new \WxPayRefund();
            $input->SetTransaction_id($transaction_id);
            $input->SetTotal_fee($total_fee);
            $input->SetRefund_fee($refund_fee);
            $input->SetOut_refund_no(WX_MCHID.date("YmdHis"));
            $input->SetOp_user_id(WX_MCHID);
            //file_put_contents('refund.txt',json_encode($input,JSON_UNESCAPED_UNICODE));
            $result = \WxPayApi::refund($input);
            if($result['result_code'] != "SUCCESS"){
                file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
            }
        }else{
        
            $db->query("update users set money=money+$money where id=$userId");
            $yue = $db->get_var('select money from users where id='.$userId);
            
            $liushui = array();
            $liushui['userId']=$userId;
            $liushui['comId']=$comId;
            $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
            $liushui['money']=$money;
            $liushui['yue']=$yue;
            $liushui['type']=2;
            $liushui['dtTime']=date("Y-m-d H:i:s");
            $liushui['remark']='订单取消';
            $liushui['orderInfo']='订单取消,微信支付返回账号余额，订单号：'.$order->orderId;
            $liushui['order_id']=$order->id;
            $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
        }
    }
    //支付宝返余额
    if(!empty($pay_json['alipay']['price'])){
        $money = $pay_json['alipay']['price'];
        $db->query("update users set money=money+$money where id=$userId");
        $yue = $db->get_var('select money from users where id='.$userId);
        
        $liushui = array();
        $liushui['userId']=$userId;
        $liushui['comId']=$comId;
        $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
        $liushui['money']=$money;
        $liushui['yue']=$yue;
        $liushui['type']=2;
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单取消';
        $liushui['orderInfo']='订单取消,支付宝支付返回账号余额，订单号：'.$order->orderId;
        $liushui['order_id']=$order->id;
        $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
    }
    if(!empty($pay_json['yibao']['price'])){
        $money = $pay_json['yibao']['price'];
        $yibao_orderId = $pay_json['yibao']['desc'];
        $verify = md5(substr($yibao_orderId.$money,0,10));
        //是否已经分过账
        $fenzhang = $db->get_row("select id,payId,ledgerNo,ledgerName from demo_yibao_fenzhang where orderId=$order->id and income_type=1 limit 1");
        if(!empty($fenzhang) && $fenzhang->status==2){
            file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
            if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
                $money = $pay_json['dingjin']['price'];
                $yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
                $verify = md5(substr($yibao_orderId.$money,0,10));
                file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&ledgerNo='.$fenzhang->ledgerNo.'&ledgerName='.$fenzhang->ledgerName.'&comId='.$order->comId.'&oid='.$order->id);
            }
        }else{
            $db->query("update demo_yibao_fenzhang set status=-1 where orderId=$order->id and income_type=1 and status=1 limit 1");
            file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['yibao']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
            if($pay_json['dingjin']['price'] && strpos($pay_json['dingjin']['paytype'],'易宝')!==false){
                $money = $pay_json['dingjin']['price'];
                $yibao_orderId = str_replace('易宝，订单号：','',$pay_json['dingjin']['paytype']);
                $verify = md5(substr($yibao_orderId.$money,0,10));
                file_get_contents('http://buy.zhishangez.com/yop-api/sendRefund.php?orderId='.$pay_json['dingjin']['orderId'].'&money='.$money.'&yibao_orderId='.$yibao_orderId.'&verify='.$verify.'&payId='.$fenzhang->payId);
            }
        }
    }
}

//去除微信昵称表情
function filterEmoji($str){
    $str = preg_replace_callback( '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

     return $str;
}

//发货
function addFaHuo($order){
    global $db;
    $userId = $order->userId;
    $order_comId = $order->comId;
    $orderId = $order->id;  
    $order_fenbiao = getFenbiao($order->comId,20);
    $product_json = json_decode($order->product_json);
    $product_title = '';
    foreach ($product_json as $pdt){
        $product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
    }
    if(!empty($product_title)){
        $product_title = substr($product_title,1);
    }
    $fahuo = array();
    $fahuo['comId'] = $order_comId;
    $fahuo['mendianId'] = $order->mendianId;
    $fahuo['addressId'] = $order->address_id;
    $fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
    $fahuo['orderIds'] = $orderId;
    $fahuo['type'] = 1;
    $fahuo['showTime'] = date("Y-m-d H:i:s");
    $fahuo['storeId'] = $order->storeId;
    $fahuo['dtTime'] = date("Y-m-d H:i:s");
    $fahuo['shuohuo_json'] = $order->shuohuo_json;
    $fahuo['productId'] = 0;
    $fahuo['tuanzhang'] = $userId;
    $fahuo['product_title'] = $product_title;
    $fahuo['fahuo_title'] = $product_title;
    $fahuo['product_num'] = $order->pdtNums;
    $fahuo['weight'] = $order->weight;
    $fahuo['pay_types'] = $db->get_var("select pay_types from order$order_fenbiao where id = $order->id");
    $fahuo['peisong_time'] = $order->peisong_time;
    $fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
    if($order->yushouId>0){
        $fahuo['yushouId'] = $order->yushouId;
        $fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
    }
    $fahuo['shequ_id'] = $order->shequ_id;

    $db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
    $fahuoId = $db->get_var("select last_insert_id();");
    $db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$orderId");
   
    $details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
    foreach ($details as $detail){
        $detail->num = (int)$detail->num;
        $db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
        $db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
    }
    addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
    print_order($order);
    
}
//获取所有上级
function getShangJi($user_id,$is_shengji = 0,$level=76){
    global $db;
    if($is_shengji){
        $sql = "
            SELECT
                count(*) as num 
            FROM (
                SELECT
                    t1.*,
                IF (
                    find_in_set(shangji, @pids) > 0,
                    @pids := concat(@pids, ',', id),
                    0
                ) AS ischild
                FROM(
                    SELECT
                        *
                    FROM
                        users t
                    ORDER BY
                        shangji,
                        id
                ) t1,
                (
                    SELECT
                        @pids := $user_id
                ) t2
            ) t3
        WHERE
            ischild != 0
            and level >= $level
                 ";
    }else{
        $sql = "
        SELECT T2.id as shangji,T2.level
        FROM ( 
            SELECT 
                @r AS _id, 
                (SELECT @r := shangji FROM users WHERE id = _id) AS shangji, 
                @l := @l + 1 AS lvl 
            FROM 
                (SELECT @r := $user_id, @l := 0) vars, 
                users h 
            WHERE @r <> 0) T1 
        JOIN users T2 
        ON T1._id = T2.id 
        WHERE T2.level >= $level
        ORDER BY T1.lvl asc 
        ";
    }
    $shangji_arr = $db->get_results($sql);
    return $shangji_arr;
}



