<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$now = date("Y-m-d H:i:s");
$cuxiao = $db->get_row("select title,pdtIds,endTime,accordType,type,guizes,areaIds,levelIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and startTime<'$now' and endTime>'$now' limit 1");
$ifhas = 0;
if(!empty($cuxiao)){
	$ifhas++;
	?>
	<div class="huodong_1">
		<img src="/skins/default/images/huodong_1.gif"/>
	</div>
	<div class="huodong_2">
		促销主题：<?=$cuxiao->title?><br>
		促销时间：<?=date("m月d日H:i",strtotime($cuxiao->endTime))?>结束促销<br>
		<? 
	        $type1 = $cuxiao->accordType == '1'?'':'元';
	        $type2 = $cuxiao->type==1?'赠':($cuxiao->type==2?'减':'享');
	        $guizes = json_decode($cuxiao->guizes);
	        if(!empty($guizes)){
		        foreach ($guizes as $rule){
		            echo '• 以下商品每满<span>'.$rule->man.'</span>'.$type1.$type2.'<span>'.$rule->jian.'</span> ';
		            switch($cuxiao->type){
		                case 1:
		                    $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
		                    echo $inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
		                break;
		                case 2:
		                    echo '元';
		                break;
		                case 3:
		                    echo '折';
		                break;
		            }
		            echo '<br>';
		      	}
		  	}
	      ?>
	</div>
	<div class="shouye_8_down">
	    <ul>
	    	<?
	    	$products = $db->get_results("select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market from demo_product_inventory where id in($cuxiao->pdtIds) and comId=$comId and if_lingshou=1 and status=1 group by productId order by dtTime desc limit 50");
			if(empty($products)){
			  echo '<div class="shangxin_1" style="padding:1rem 0;"><span>暂无促销商品！</span></div>';
			}else{
			  foreach ($products as $key => $product) {
			    $product->image = empty($product->image)?'/inc/img/nopic.svg':$product->image;
			    $product->price_sale = number_format($product->price_sale,2);
			    $product->price_market = number_format($product->price_market,2);
	    	?>
	        <li>
	            <a href="/index.php?p=4&a=view&id=<?=$product->inventoryId?>">
	                <div class="shouye_8_down_img">
	                    <img src="<?=ispic($product->image)?>"/>
	                </div>
	                <div class="shouye_8_down_tt1">
	                    <?=$product->title?>
	                </div>
	                <div class="shouye_8_down_tt2">
	                    ￥<?=$product->price_sale?> <span>原价:￥<?=$product->price_market?></span>
	                </div>
	            </a>
	        </li>
	    	<? }
	    	}
	    	?>
	        <div class="clearBoth"></div>
	    </ul>
	</div>
	<?
}
$cuxiao = $db->get_row("select * from cuxiao_order where comId=$comId and scene=1 and status=1 and startTime<'$now' and endTime>'$now' limit 1");
if(!empty($cuxiao)){
	$ifhas++;
	?>
	<div class="huodong_1">
		<img src="/skins/default/images/huodong_1.gif"/>
	</div>
	<div class="huodong_2">
		促销主题：<?=$cuxiao->title?><br>
		促销时间：<?=date("m月d日H:i",strtotime($cuxiao->endTime))?>结束促销<br>
		<? 
            $type1 = '元';
            $type2 = $cuxiao->type==1?'赠':($cuxiao->type==2?'减':'享');
            $guizes = json_decode($cuxiao->guizes);
            if(!empty($guizes)){
	            foreach ($guizes as $rule){
	                echo '• 每满<span>'.$rule->man.'</span>'.$type1.$type2.'<span>'.$rule->jian.'</span> ';
	                switch($cuxiao->type){
	                    case 1:
	                        $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
	                        echo $inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
	                    break;
	                    case 2:
	                        echo '元';
	                    break;
	                    case 3:
	                        echo '折';
	                    break;
	                }
	                echo '<br>';
		      	}
		    }
	    ?>
	</div>
	<?
}
$cuxiao = $db->get_row("select * from chongzhi_gift where comId=$comId and scene=1 and status=1 and startTime<'$now' and endTime>'$now' limit 1");
if(!empty($cuxiao)){
	$ifhas++;
	?>
	<div class="huodong_2" style="margin-top:1rem">
		活动主题：充值赠送<br>
		活动时间：<?=date("m月d日H:i",strtotime($cuxiao->endTime))?>结束<br>
		<? 
            $content = '';
			$type1 = '元';
			$type2 = '赠';
			$contents = json_decode($cuxiao->guizes);
			if(!empty($contents)){
				foreach ($contents as $rule){
					$content .='满'.$rule->man.$type1.$type2.$rule->jian;
					switch ($cuxiao->type) {
						case 1:
							$content .='元';
						break;
						case 2:
							$content .='积分';
						break;
						case 3:
							$yhq = $db->get_var("select title from yhq where id=$rule->yhqId");
							$content .=$yhq;
						break;
					}
					$content.='<br>';
				}
			}
	    ?>
	</div>
	<?
}
if($ifhas==0){
	echo '<div class="shangxin_1" style="padding:1rem 0;"><span>暂无活动！</span></div>';
}
require(ABSPATH.'/skins/default/bottom.php');
?>
<script type="text/javascript">
	var share_url = 'http://<?=$_SERVER['HTTP_HOST']?>/index.php?<?=$_SERVER["QUERY_STRING"]?>';
    var share_title = '活动-<?=$_SESSION['demo_com_title']?>';
    var share_img = '<?=$_SESSION['demo_com_logo']?>';
    var share_desc = '<?=$_SESSION['demo_com_remark']?>';
    $(function(){
      var url = window.location.href;
      url = encodeURIComponent(url);
      WeChat(url,share_url,share_title,share_img,share_desc,0);
    });
</script>