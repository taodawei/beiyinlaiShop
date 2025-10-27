<?
global $db,$request;
require_once ABSPATH.'inc/excel.php';
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$filepath = $request['filepath'];

$pandianJsonData = '';
$jilus = excelToArray($filepath,2);
//$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);

//$pandianJsonData = stripcslashes($request['pandianJsonData']);
// $jilus = json_decode($pandianJsonData,true);


$hasSns = array();
$errorJilus = array();
$prev = array();
if(!empty($jilus)){
    $formRow = array_shift($jilus);
    foreach($jilus as $jiu){
        $channelTitle = $jiu[0];
        $brandTitle = $jiu[1];
        $skuId = $jiu[2];
        $title = addslashes($jiu[3]);
        $skuDay = $jiu[4];
        $specs = $jiu[5];
        $prices = $jiu[6];
        
        $hadSku = $db->get_row("select * from demo_product where skuId = '$skuId' ");
        $pid = 0;
        if($hadSku){
           $pid = $hadSku->id;
        }
        
        // if($pid == 0){
        //     $errorJilus[] = $jiu[0];
        // }
        // continue;
        
        // Step1 - 先存入主表数据
        $channelId = (int)$db->get_var("select id from demo_product_channel where title = '$channelTitle' or en_title = '$channelTitle' ");
        $brandId = (int)$db->get_var("select id from demo_product_brand where title = '$brandTitle' or en_title = '$brandTitle' ");
        $product = array(
            'id' => $pid,
            'comId' => 888,
            'skuId' => $skuId,
            'skuDay' => $skuDay,
            'title' => $title,
            'channelId' => $channelId,
            'brandId' => $brandId,
            'status' => 1
        );

        
        // $productId = 2499;
        $db->insert_update("demo_product", $product, "id");
        if($pid == 0){
            $productId = $db->get_var("select last_insert_id();");
        }else{
            $productId = $pid;
        }
        
       
        if($productId){
            // Step2 - 存入规格表数据
            $priceArr = explode('/', $prices);
            $specArr = explode('/', $specs);
            $num = count($priceArr);
            $db->query("insert into demo_product_key(productId,title,parentId,kg) value($productId,'".$formRow[5]."',0,0)");
            $parentKeyId = $db->get_var("select last_insert_id();");
            if($parentKeyId){
                //删除老的规格
                $db->query("delete from demo_product_inventory where productId = $productId");
                for($i = 0; $i < $num; $i++){
                    
                    $db->query("insert into demo_product_key(productId,title,parentId,kg) value($productId,'".$specArr[$i]."', $parentKeyId, $i)");
                    $key_ids = $db->get_var("select last_insert_id();");
                    if($key_ids){
                        $inventory = array();
                        $inventory['comId'] = 888;
                        $inventory['productId'] = $productId;
                        $inventory['channelId'] = $channelId;
                        $inventory['title'] = $title;
                        $inventory['key_vals'] = $specArr[$i];
                        $inventory['key_ids'] = $key_ids;
                        $inventory['price_sale'] = $priceArr[$i];
                        $inventory['sn'] = $skuId."-".rand(1000,9999);
                        $inventory['price_market'] = $priceArr[$i];
                        $inventory['price_cost'] = 0;
                        $inventory['dtTime'] = date("Y-m-d H:i:s");
                        $inventory['status'] = 1;
                        $inventory['if_lingshou'] = 1;
                  
                        $db->insert_update("demo_product_inventory", $inventory, "id");
                    }
                }
            }
        }
        
        //Step3 添加副表数据
        $param = array();
        $param['id'] = (int)$db->get_var("select id from demo_product_params where productId = $productId ");
        $param['productId'] = $productId;
        $param['channelId'] = $channelId;
        $param['brandId'] = $brandId;
        foreach ($formRow as $k => $val){
            if($k > 6){//从第六个开始计算
                $field = $db->get_row("select * from demo_product_fields where is_del = 0 and title = '$val' ");
                if($field){
                    $param[$field->field_title] = $jiu[$k] ? addslashes($jiu[$k]) : '';
                }
            }
        }

        $db->insert_update("demo_product_params", $param, "id");
    }
    
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
                    1、商品货号已经存在！<br>
                    2、产品的分类不存在<br>
                    <br>
                </div>
            	<div class="kucunpandian_daorushibai_03">
            		<form id="pandianForm" action="?m=system&s=product&a=daochuExcel" method="post" target="_blank">
            			<input type="hidden" name="pandianJsonData" value='<?=$pandianJsonData?>'>
            		</form>
                	<a href="javascript:$('#pandianForm').submit();"><img src="images/biao_81.png"/> 下载导入失败数据</a><br>
                    按上述要求检查修改后，重新上传
                </div>
            	<div class="kucunpandian_daorushibai_04">
                	<a href="?m=system&s=product&a=daoru">重新上传</a>
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