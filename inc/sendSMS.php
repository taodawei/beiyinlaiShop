<?php

/*--------------------------------

:		йPHP HTTPӿ Ͷ

޸:	2009-04-08

˵:		http://http.c123.com/tx/?uid=û˺&pwd=MD5λ32&mobile=content=

״̬:

	100 ͳɹ

	101 ֤ʧ

	102 Ų

	103 ʧ

	104 Ƿַ

	105 ݹ

	106 

	107 Ƶʹ

	108 ݿ

	109 ˺Ŷ

	110 ֹƵ

	111 ϵͳݶ

	112 벻ȷ

	120 ϵͳ

--------------------------------*/





//ʱ

/*

$time = '2010-05-27 12:11';

$res = sendSMS($uid,$pwd,$mobile,$content,$time);

echo $res;

*/

function sendSMS($mobile,$content,$time='',$mid='')

{

	$uid = SMS_USER;

	$pwd = SMS_PASSWORD;	

	$content = iconv("UTF-8","GB2312",$content);

	

	$http = 'http://http.c123.com/tx/';

	$data = array

		(

		'uid'=>$uid,					//û˺

		'pwd'=>strtolower(md5($pwd)),	//MD5λ32

		'mobile'=>$mobile,				//

		'content'=>$content,			//

		'time'=>$time,		//ʱ

		'mid'=>$mid						//չ

		);

	$re= postSMS($http,$data);			//POSTʽύ

	if( trim($re) == '100' )

	{

		return "100";

	}

	else 

	{

		return "ʧ! ״̬".$re;

	}

}



function postSMS($url,$data='')

{

	$row = parse_url($url);

	$host = $row['host'];

	$port = $row['port'] ? $row['port']:80;

	$file = $row['path'];

	while (list($k,$v) = each($data)) 

	{

		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//תURL׼

	}

	$post = substr( $post , 0 , -1 );

	$len = strlen($post);

	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);

	if (!$fp) {

		return "$errstr ($errno)\n";

	} else {

		$receive = '';

		$out = "POST $file HTTP/1.1\r\n";

		$out .= "Host: $host\r\n";

		$out .= "Content-type: application/x-www-form-urlencoded\r\n";

		$out .= "Connection: Close\r\n";

		$out .= "Content-Length: $len\r\n\r\n";

		$out .= $post;		

		fwrite($fp, $out);

		while (!feof($fp)) {

			$receive .= fgets($fp, 128);

		}

		fclose($fp);

		$receive = explode("\r\n\r\n",$receive);

		unset($receive[0]);

		return implode("",$receive);

	}

}

?>