<?php
@session_start();
@error_reporting(7);
header('Content-Type: text/html; charset=utf-8');
$shlConfig='../../../config/dt-config.php';
require($shlConfig);
require(ABSPATH.'/inc/function.php');
require_once(ABSPATH.'/inc/class.database.php');
if(empty($_SESSION[TB_PREFIX.'comId'])){
    die("非法请求");
}
$comId = $_SESSION[TB_PREFIX.'comId'];
$request  = cleanArrayForMysql($_REQUEST);
//file_put_contents("file_get.txt", $_SESSION[TB_PREFIX.'comId']);
    function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    $id= 'LTAIor4nXmLQBquw';
    $key= 'trEWEgglU9MCv6GQbKKKxI74BaN6Qd';
    $host = 'https://zhishang-file.oss-cn-beijing.aliyuncs.com';
    $callbackUrl = "https://buy.zhishangez.com/callback.php";

    $callback_param = array('callbackUrl'=>$callbackUrl, 
                 'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}', 
                 'callbackBodyType'=>"application/x-www-form-urlencoded");
    $callback_string = json_encode($callback_param);
    file_put_contents("file_get.txt", $callback_string);
    $base64_callback_body = base64_encode($callback_string);
    $now = time();
    $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
    $end = $now + $expire;
    $expiration = gmt_iso8601($end);

    //$dir = 'user-dir/';
    $dir = $_SESSION[TB_PREFIX.'comId'].'Folder/';

    //最大文件大小.用户可以自己设置
    $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
    $conditions[] = $condition; 

    //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
    $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
    $conditions[] = $start; 


    $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
    //echo json_encode($arr);
    //return;
    $policy = json_encode($arr);
    $base64_policy = base64_encode($policy);
    $string_to_sign = $base64_policy;
    $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

    $response = array();
    $response['accessid'] = $id;
    $response['host'] = $host;
    $response['policy'] = $base64_policy;
    $response['signature'] = $signature;
    $response['expire'] = $end;
    $response['callback'] = $base64_callback_body;
    //这个参数是设置用户上传指定的前缀
    $response['dir'] = $dir;
    file_put_contents("file_get.txt", json_encode($response));
    echo json_encode($response);
?>
