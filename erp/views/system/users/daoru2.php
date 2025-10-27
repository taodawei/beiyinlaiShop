<?
global $db,$request;
require_once ABSPATH.'inc/excel.php';
require_once(ABSPATH.'/inc/class.shlencryption.php');
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shezhi = $db->get_row("select sn,addrows from user_shezhi where comId=$comId");
$rowss = $shezhi->addrows;
$arrays = array();
if(!empty($rowss)){
    $arrays = unserialize($rowss);
}
$filepath = $request['filepath'];
$pandianJsonData = stripcslashes($request['pandianJsonData']);
$jilus = json_decode($pandianJsonData,true);
$hasSns = array();
$errorJilus = array();
$prev = array();
if(!empty($jilus)){
	$dtTime = date("Y-m-d H:i:s");
	foreach ($jilus as $jilu){
		$sql = 'insert into users(comId,sn,nickname,username,password,sex,birthday,level,mendianId,dtTime,status,addRows) values';
		$sql1 = '';
		$sn = trim($jilu[0]);
		$nickname = trim($jilu[1]);
		$username = trim($jilu[2]);
		$password = trim($jilu[3]);
		$sex = $jilu[6]=='男'?1:($jilu[6]=='女'?2:0);
		$birthday = date("Y-m-d",strtotime(trim($jilu['7'])));
		if(strlen($username)!=11||substr($username,0,1)!='1'||empty($password)){
			$errorJilus[] = $jilu;
			continue;
		}
		$ifhas = $db->get_var("select id from users where comId=$comId and username='$username' limit 1");
		if(!empty($ifhas)){
			$errorJilus[] = $jilu;
			continue;;
		}
		if(empty($sn)){
			$sn = $shezhi->sn.date("ymdHis").rand(1000,9999);
		}
		$shlencryption = new shlEncryption($password);
		$password = $shlencryption->to_string();
		$addRows = array();
		if(!empty($arrays)){
			foreach ($arrays as $i => $val) {
				$addRows[$val['name']] = $jilu[8+$i];
			}
		}
		$addRowstr = json_encode($addRows,JSON_UNESCAPED_UNICODE);
		$level = (int)$db->get_var("select id from user_level where comId=$comId and title='".trim($jilu[4])."' limit 1");
		$mendianId = (int)$db->get_var("select id from mendian where comId=$comId and title='".trim($jilu[5])."' limit 1");
		$sql1.=",($comId,'$sn','$nickname','$username','$password','$sex','$birthday',$level,$mendianId,'$dtTime',1,'$addRowstr')";
		$sql1 = substr($sql1,1);
		$db->query($sql.$sql1);
		$userId = $db->get_var("select last_insert_id();");
		$zhishangId = reg_zhishang($userId,$username,$password,$nickname);
		$db->query("update users set zhishangId=$zhishangId where id=$userId");
	}
	
	@unlink($filepath);
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		layui.use(['layer'], function(){
			<? if(empty($jilus)){
				$confirm = 0;
			?>
				layer.confirm('无法获取到导入的数据，请重新导入', {
				  btn: ['确定'],
				}, function(){
					location.href='?m=system&s=product&a=daoru';
				});
			<? }
			?>
		});
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_77.png"/> 会员导入
    </div>
	<div class="right_down">
    	<div class="kucunpandian">
        	<div class="kucunpandian_01">
            	<ul>
            		<li class="kucunpandian_01_right">
                    	<a class="kucunpandian_01_bj3">上传导入文件 <img src="images/biao_80.png"/></a>
                    </li>
                    <li class="kucunpandian_01_right">
                    	<a class="kucunpandian_01_bj3">导入文件预览 <img src="images/biao_80.png"/></a>
                    </li>
                    <li>
                    	<a class="kucunpandian_01_bj1">导入完成</a>
                    </li>
                    <div class="clearBoth"></div>
            	</ul>
            </div>
        	<div class="kucunpandian_daorushibai">
        		<?
        		if(!empty($errorJilus)){
        			$pandianJsonData = json_encode($errorJilus,JSON_UNESCAPED_UNICODE);
					$pandianJsonData = str_replace("'","\'",$pandianJsonData);
        		?>
            	<div class="kucunpandian_daorushibai_01">
                	<h2>导入失败！</h2>共<?=count($jilus)?>数据，成功导入<?=count($jilus)-count($errorJilus)?>条，导入失败<?=count($errorJilus)?>条。
                </div>
            	<div class="kucunpandian_daorushibai_02">
                	<h2>导入失败的原因可能有：</h2>
                    1、手机号不正确！<br>
                    2、手机号码已注册了会员<br>
                </div>
            	<div class="kucunpandian_daorushibai_03">
            		<form id="pandianForm" action="?m=system&s=users&a=daochuExcel" method="post" target="_blank">
            			<input type="hidden" name="pandianJsonData" value='<?=$pandianJsonData?>'>
            		</form>
                	<a href="javascript:$('#pandianForm').submit();"><img src="images/biao_81.png"/> 下载导入失败数据</a><br>
                    按上述要求检查修改后，重新上传
                </div>
            	<div class="kucunpandian_daorushibai_04">
                	<a href="?m=system&s=users&a=daoru">重新上传</a>
                </div>
            	<div class="kucunpandian_daorushibai_05">
                </div>
                <? }else{?>
	            	<div class="kucunpandian_daorushibai_011">
	                	<h2>恭喜您导入成功！</h2>共<?=count($jilus)?>条数据导入成功
	                </div>
	            <? }?>
            </div>
        </div>
    </div>
    <? require('views/help.html');?>
</body>
</html>