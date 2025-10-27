<?php
function index(){}
function view(){}
//获取库存
function get_product_kucun($inventoryId,$comId=0){
	global $db;
	if($comId==0)$comId = (int)$_SESSION['demo_comId'];
	if($comId==1009){
		$fenbiao = getFenbiao(1009,20);
		$lipinka_orders = (int)$db->get_var("select count(*) from order_detail$fenbiao where inventoryId=$inventoryId and status=1");
		$lipinka_jilu = $db->get_row("select num,bind_num from lipinka_jilu where id=(select lipinkaId from demo_product_inventory where id=$inventoryId)");
		return $lipinka_jilu->num-$lipinka_jilu->bind_num-$lipinka_orders;
	}else{
		$areaId = (int)$_SESSION['demo_sale_area'];
		$storeId = get_fahuo_store($areaId,$comId);
		$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
		return (int)($kc->kucun-$kc->yugouNum);
	}
	
}