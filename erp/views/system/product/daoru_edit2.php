<?
global $db,$request;
require_once ABSPATH.'inc/excel.php';
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$filepath = $request['filepath'];
$pandianJsonData = stripcslashes($request['pandianJsonData']);
$jilus = json_decode($pandianJsonData,true);
$hasSns = array();
$errorJilus = array();
$prev = array();
if(!empty($jilus)){
	//$cangkus = $db->get_results("select id from demo_kucun_store where comId=$comId and status=1");
	$sql = '';
	$kg = 0;
	foreach ($jilus as $jilu){
		$sn = $jilu[0];
		$id = $db->get_var("select id from demo_product_inventory where comId=$comId and sn='$sn' limit 1");
		if(!empty($id)){
			$ordering = (int)$jilu[3];
			$status = intval($jilu[4])==1?1:-1;
			$sale_tuan = intval($jilu[5])==1?1:0;
			$tuan_num = intval($jilu[6]);
			$weight = $jilu[7];
			$price_sale = $jilu[8];
			$price_market = $jilu[9];
			$price_cost = $jilu[10];
			$price_tuan = $jilu[11];
			$price_shequ_tuan = $jilu[12];
			$fanli_tuanzhang = $jilu[13];
			$code = $jilu[14];
			if(!is_numeric($weight) || !is_numeric($price_sale) || !is_numeric($price_market) || !is_numeric($price_cost) || !is_numeric($price_tuan) || !is_numeric($price_shequ_tuan) || !is_numeric($fanli_tuanzhang)){
				$errorJilus[] = $jilu;
			}else{
				$inventory = array();
				$inventory['id'] = $id;
				$inventory['ordering'] = $ordering;
				$inventory['status'] = $status;
				$inventory['sale_tuan'] = $sale_tuan;
				$inventory['tuan_num'] = $tuan_num;
				$inventory['weight'] = $weight;
				$inventory['price_sale'] = $price_sale;
				$inventory['price_market'] = $price_market;
				$inventory['price_cost'] = $price_cost;
				$inventory['price_tuan'] = $price_tuan;
				$inventory['price_shequ_tuan'] = $price_shequ_tuan;
				//$inventory['fanli_shequ'] = $fanli_shequ;
				$inventory['fanli_tuanzhang'] = $fanli_tuanzhang;
				$inventory['code'] = $code;
				$db->insert_update('demo_product_inventory',$inventory,'id');
			}
		}else{
			$errorJilus[] = $jilu;
		}
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
					location.href='?m=system&s=product&a=daoru_eidt';
				});
			<? }
			?>
		});
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_77.png"/> 导入修改商品
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
                    1、商品编码不存在！<br>
                    2、产品的价格不是一个有效的数字(注意要清除单元格格式)<br>
                </div>
            	<div class="kucunpandian_daorushibai_03">
            		<form id="pandianForm" action="?m=system&s=product&a=daochuExcel&edit=1" method="post" target="_blank">
            			<input type="hidden" name="pandianJsonData" value='<?=$pandianJsonData?>'>
            		</form>
                	<a href="javascript:$('#pandianForm').submit();"><img src="images/biao_81.png"/> 下载导入失败数据</a><br>
                    按上述要求检查修改后，重新上传
                </div>
            	<div class="kucunpandian_daorushibai_04">
                	<a href="?m=system&s=product&a=daoru_edit">重新上传</a>
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