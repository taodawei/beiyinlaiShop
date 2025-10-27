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
$pandians = excelToArray($filepath);
$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
$pandianJsonData = str_replace("'","\'",$pandianJsonData);
$confirm = 1;
$num = 50;
if(count($pandians)<50)$num=count($pandians);
$allRows = array(
	"sn"=>array("title"=>"商品编码(请勿修改)","rowCode"=>"{field:'sn',title:'商品编码',width:200,sort:true}"),
	"title"=>array("title"=>"商品名称(请勿修改)","rowCode"=>"{field:'title',title:'商品名称',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"key_vals"=>array("title"=>"商品规格(请勿修改)","rowCode"=>"{field:'key_vals',title:'商品规格',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"ordering"=>array("title"=>"排序(降序)","rowCode"=>"{field:'ordering',title:'排序(降序)',width:100,sort:true}"),
	"status"=>array("title"=>"上架状态(0下架 1上架)","rowCode"=>"{field:'status',title:'上架状态(0下架 1上架)',width:100,sort:true}"),
	"sale_tuan"=>array("title"=>"是否开启拼团(0否 1是)","rowCode"=>"{field:'sale_tuan',title:'是否开启拼团(0否 1是)',width:100,sort:true}"),
	"tuan_num"=>array("title"=>"成团数量","rowCode"=>"{field:'tuan_num',title:'成团数量',width:100,sort:true}"),
	"weight"=>array("title"=>"重量","rowCode"=>"{field:'weight',title:'重量',width:100,sort:true}"),
	"price_sale"=>array("title"=>"零售价","rowCode"=>"{field:'price_sale',title:'零售价',width:100,sort:true}"),
	"price_market"=>array("title"=>"市场价","rowCode"=>"{field:'price_market',title:'市场价',width:100,sort:true}"),
	"price_cost"=>array("title"=>"成本(供货)价","rowCode"=>"{field:'price_cost',title:'成本(供货)价',width:100,sort:true}"),
	"price_tuan"=>array("title"=>"普通团价格","rowCode"=>"{field:'price_tuan',title:'普通团价格',width:100,sort:true}"),
	"price_shequ_tuan"=>array("title"=>"社区团价格","rowCode"=>"{field:'price_shequ_tuan',title:'社区团价格',width:100,sort:true}"),
	"fanli_tuanzhang"=>array("title"=>"分销提成(总)","rowCode"=>"{field:'fanli_tuanzhang',title:'分销提成(总)',width:100,sort:true}"),
	"code"=>array("title"=>"条形码","rowCode"=>"{field:'code',title:'条形码',width:100,sort:true}")
);
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
					location.href='?m=system&s=product&a=daoru_edit';
				});
			<? }else if(count($pandians[0])!=15){
				$confirm = 0;
				?>
				layer.confirm('数据格式有误，请先下载模板文件，修改好后重新上传', {
					btn: ['确定'],
				}, function(){
					location.href='?m=system&s=product&a=daoru_edit';
				});
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
    	<img src="images/biao_77.png"/> 导入修改商品
    	<!-- <div class="bangzhulist_up_right" onclick="showHelp(305);">帮助</div> -->
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
                            <?
                            foreach ($allRows as $row=>$isshow){
                            ?>
                            <td bgcolor="#bcdbea" class="kucunpandian_yulan_02_title" align="center" valign="middle">
                            	<?=$isshow['title']?>
                            </td>
                            <? }?>
                        </tr>
                        <?
                        if($confirm==1){
	                        for($i=0;$i<$num;$i++) {
	                        	$jilu = $pandians[$i];
	                        	?>
	                        	<tr height="58">
	                        		<td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
	                        			<?=$i+1?>
	                        		</td>
	                        		<?
	                        		$j = 0;
		                            foreach ($allRows as $row=>$isshow){
		                            ?>
		                            <td bgcolor="#ffffff" class="kucunpandian_yulan_02_tt" align="center" valign="middle">
		                            	<?=$jilu[$j]?>
		                            </td>
		                            <? 
		                            $j++;
		                        }?>
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
        		<form id="pandianForm" action="?m=system&s=product&a=daoru_edit2" method="post">
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