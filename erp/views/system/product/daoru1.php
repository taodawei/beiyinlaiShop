<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$filepath = $request['filepath'];
if(empty($filepath)){
	echo '<script>history.go(-1);</script>';
	exit;
}
$filepath = ABSPATH.str_replace('../','',$filepath);
require_once ABSPATH.'inc/excel.php';
$pandians = excelToArray($filepath,2);
$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);

$confirm = 1;
$num = 50;
$biaotou = $pandians[0];
if(count($pandians)<50)$num=count($pandians);
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
			<? if(empty($pandians)||count($pandians)<1){
				// $confirm = 0;
			?>
				layer.confirm('无法获取到excel文件数据', {
				  btn: ['确定'],
				}, function(){
					location.href='?m=system&s=product&a=daoru';
				});
// 			<? }else if(count($pandians) > 1000){
// 				$confirm = 0;
// 				?>
// 				layer.confirm('数据超过一千条，请拆分上传，修改好后重新上传', {
// 					btn: ['确定'],
// 				}, function(){
// 					location.href='?m=system&s=product&a=daoru';
// 				});
				<?
			}?>
		});
		function pandian(){
			layer.load();
			$("#pandianForm").submit();
		} 
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_77.png"/> 商品新增导入
    </div>
	<div class="right_down">
    	<div class="kucunpandian">
        	<div class="kucunpandian_01">
            	<ul>
            		<li class="kucunpandian_01_right">
                    	<a class="kucunpandian_01_bj3">上传导入文件 <img src="images/biao_80.png"/></a>
                    </li>
                    <li class="kucunpandian_01_right">
                    	<a class="kucunpandian_01_bj1">导入文件预览</a>
                    </li>
                    <li>
                    	<a class="kucunpandian_01_bj2">导入完成</a>
                    </li>
                    <div class="clearBoth"></div>
            	</ul>
            </div>
        	<div class="kucunpandian_yulan">
            	<div class="kucunpandian_yulan_01">
                	前 <?=$num?> 条数据预览
                </div>
            	<div class="kucunpandian_yulan_02">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="43">
                        	<td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$biaotou[0]?>
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$biaotou[1]?>
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$biaotou[2]?>
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$biaotou[3]?>
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$biaotou[4]?>
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle"><?=$biaotou[5]?></td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle"><?=$biaotou[6]?></td>
                        </tr>
                        <?
                        if($confirm==1){
	                        for($i=1;$i<$num;$i++) {
	                        	$jilu = $pandians[$i];
	                        	?>
	                        	<tr height="58">
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$i?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[0]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="left" valign="middle">
	                        			<?=$jilu[1]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[2]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[3]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[4]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[5]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[6]?>
	                        		</td>
	                        	</tr>
	                        	<?
	                        }
	                    }else{
	                    	?>
	                    	<tr height="58"><td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle" colspan="10">
	                    		导入文件信息有误，请<a href="javascript:history.go(-1)">返回</a>重新导入
	                    	</td></tr>
	                    	<?
	                    }
                        ?>
                    </table>
                </div>
            </div>
        	<div class="kucunpandian_03">
        		<? if($confirm==1){?>
        		<form id="pandianForm" action="?m=system&s=product&a=daoru2" method="post">
        			<input type="hidden" name="pandianJsonData" value=''>
        			<input type="hidden" name="filepath" value="<?=$filepath?>">
            	</form>
            	<a href="javascript:" onclick="pandian();" class="kucunpandian_03_1">确定导入</a>
            	<? }else{?>
            	<a href="javascript:history.go(-1);" class="kucunpandian_03_1">返 回</a>
            	<? }?>
            	<a href="javascript:history.go(-1);" class="kucunpandian_03_2">取 消</a>
            </div>
        </div>
    </div>
    <? require('views/help.html');?>
</body>
</html>