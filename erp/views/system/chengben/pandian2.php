<?
global $db,$request;
require_once ABSPATH.'inc/excel.php';
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
$username = $_SESSION[TB_PREFIX.'name'];
if(is_file("../cache/kucun_set_$comId.php")){
	$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$fenbiao = getFenbiao($comId,20);
$storeId = (int)$request['storeId'];
$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
if(empty($storeName))die('异常访问');
$filepath = $request['filepath'];
$pandianJsonData = stripcslashes($request['pandianJsonData']);
$jilus = json_decode($pandianJsonData,true);
$hasSns = array();
$errorJilus = array();
$type_info = '成本调整';
$type = 6;
if(!empty($jilus)){
	$rukuOrderInt = getOrderId($comId,$type);
	$rukuOrder = 'CB_'.date("Ymd").'_'.$rukuOrderInt;
	$status = 1;
	$shenheUser = 0;
	$shenheName = '';
	$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,6,$storeId,0,'$rukuOrder',".(int)$rukuOrderInt.",'".date("Y-m-d H:i:s")."','$type_info',$status,$userId,'$username','',$shenheUser,'$shenheName','','$storeName')");
	$rukuId = $db->get_var("select last_insert_id();");
	$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values";
	$rukuSql1 = '';

	foreach ($jilus as $jilu){
		$inventory = $db->get_row("select id,productId from demo_product_inventory where comId=$comId and sn='".$jilu[0]."' limit 1");
		if(!empty($inventory)&&!in_array($jilu[0],$hasSns)){
			$pdtInfoArry = array();
			$pdtInfoArry['sn'] = $jilu[0];
			$pdtInfoArry['title'] = $jilu[1];
			$pdtInfoArry['key_vals'] = $jilu[2];
			$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
			$hasSns[] = $jilu[0];
			$rukuChengben = $jilu[3];
			$typeInfo = $type_info;
			$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=$inventory->id and storeId=$storeId limit 1");
			$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventory->id and storeId=$storeId and status=1 order by id desc limit 1");
			if(empty($lastJilu)){
				$lastJilu->zongchengben = 0;
				$lastJilu->kucun = 0;
			}
			$zongchengben = $rukuChengben+$lastJilu->zongchengben;
			$zongNum = $kucun;
			$chengben = getXiaoshu($zongchengben/$zongNum,4);
			if($chengben<0)$chengben=0;
			$db->query("update demo_kucun set chengben='$chengben' where inventoryId=$inventory->id and storeId=$storeId limit 1");
			$rukuSql1.=",($comId,$rukuId,".$inventory->id.",".$inventory->productId.",'$pdtInfo',$storeId,'$storeName','0',$status,'$kucun','',1,'$typeInfo','".date("Y-m-d H:i:s")."','','$rukuChengben','$zongchengben')";
		}else{
			$errorJilus[] = $jilu;
		}
	}
	if(!empty($rukuSql1)){
		$rukuSql1 = substr($rukuSql1,1);
		$db->query($rukuSql.$rukuSql1);
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
					location.href='?m=system&s=chengben&a=daoru&storeId=<?=$storeId?>';
				});
			<? }
			?>
		});
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_77.png"/> 商品成本调整导入
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
                    1、系统中没有指定编码的商品存在<br>
                    2、如果两条或多条数据的编码相同，则只按第一条数据执行<br>
                </div>
            	<div class="kucunpandian_daorushibai_03">
            		<form id="pandianForm" action="?m=system&s=chengben&a=daochuExcel" method="post" target="_blank">
            			<input type="hidden" name="pandianJsonData" value='<?=$pandianJsonData?>'>
            		</form>
                	<a href="javascript:$('#pandianForm').submit();"><img src="images/biao_81.png"/> 下载导入失败数据</a><br>
                    按上述要求检查修改后，重新上传
                </div>
            	<div class="kucunpandian_daorushibai_04">
                	<a href="?m=system&s=chengben&a=daoru&storeId=<?=$storeId?>">重新上传</a>
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