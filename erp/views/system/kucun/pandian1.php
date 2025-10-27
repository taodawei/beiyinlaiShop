<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$storeId = (int)$request['storeId'];
$filepath = $request['filepath'];
if(empty($storeId)||empty($filepath)){
	echo '<script>history.go(-1);</script>';
	exit;
}
$filepath = ABSPATH.str_replace('../','',$filepath);
require_once ABSPATH.'inc/excel.php';
$pandians = excelToArray($filepath);
$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
$pandianJsonData = str_replace("'","\'",$pandianJsonData);
$confirm = 1;
$num = 50;
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
				$confirm = 0;
			?>
				layer.confirm('无法获取到excel文件数据', {
				  btn: ['确定'],
				}, function(){
					location.href='?m=system&s=kucun&a=pandian&storeId=<?=$storeId?>';
				});
			<? }else if(count($pandians[0])<9){
				$confirm = 0;
				?>
				layer.confirm('数据格式有误，请先下载库存文件，修改好“盘点数量”后上传', {
					btn: ['确定'],
				}, function(){
					location.href='?m=system&s=kucun&a=pandian&storeId=<?=$storeId?>';
				});
				<?
			}else{
				foreach ($pandians as $data) {
					if(!is_numeric($data[5])||!is_numeric($data[6])||!is_numeric($data[7])||!is_numeric($data[8])){
						$confirm = 0;
						?>
						layer.confirm('数据格式有误，请确保每一条数据盘点数量都不为空', {
							btn: ['确定'],
						}, function(){
							location.href='?m=system&s=kucun&a=pandian&storeId=<?=$storeId?>';
						});
						<?
						break;
					}
				}
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
    	<img src="images/biao_77.png"/> 库存盘点
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
                	前 <?=$num?> 条数据预览  &nbsp;&nbsp;     盘点仓库：<?=$db->get_var("select title from demo_kucun_store where id=$storeId")?>  &nbsp;&nbsp;    盘点时间：<?=date("Y-m-d H:i")?>    &nbsp;&nbsp;   经办人：<?=$_SESSION[TB_PREFIX.'name']?> 
                </div>
            	<div class="kucunpandian_yulan_02">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="43">
                        	<td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	编码
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	商品
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	规格
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	条形码
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	单位
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	库存上限
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	库存下限
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	库存数量
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	盘点数量
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	盘盈盘亏 
                            </td>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	备注
                            </td>
                        </tr>
                        <?
                        if($confirm==1){
	                        for($i=0;$i<$num;$i++) {
	                        	$jilu = $pandians[$i];
	                        	$yingkui = $jilu[8]-$jilu[7];
	                        	if($yingkui>0){
	                        		$yingkui='<span style="color:green">+'.$yingkui.'</span>';
	                        	}else if($yingkui<0){
	                        		$yingkui='<span style="color:red">'.$yingkui.'</span>';
	                        	}
	                        	?>
	                        	<tr height="58">
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$i+1?>
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
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[7]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[8]?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$yingkui?>
	                        		</td>
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$jilu[9]?>
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
        		<form id="pandianForm" action="?m=system&s=kucun&a=pandian2&storeId=<?=$storeId?>" method="post">
        			<input type="hidden" name="pandianJsonData" value='<?=$pandianJsonData?>'>
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