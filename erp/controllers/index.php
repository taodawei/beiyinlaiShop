<?php
function index()
{
	global $db;$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//初始化企业仓库设置
	$ifhas = $db->get_var("select comId from demo_kucun_set where comId=$comId");
	if(empty($ifhas)){
		$db->query("insert into demo_kucun_set(comId,ruku_pre,chuku_pre,diaobo_pre,caigou_pre,caigou_tuihuo_pre,ruku_types,chuku_types,sn_rule) value($comId,'IN','OUT','DB','CG','CG-R','其他入库','其他出库','P')");
		$db->query("insert into demo_kucun_store(comId,title,sn,del) value($comId,'默认库','001',0)");
		$showRowsArry = array("image"=>1,"sn"=>1,"title"=>1,"key_vals"=>1,"untis"=>1,"price_market"=>1,"price_cost"=>1,"brand"=>1,"kucun"=>1,"kuncun_cost"=>1,"status"=>1,"channel"=>1,"ordering"=>1,"dtTime"=>1,"updateTime"=>1);
		$showRows = json_encode($showRowsArry,JSON_UNESCAPED_UNICODE);
		$db->query("insert into demo_product_set(comId,if_image,showRows) value($comId,0,'$showRows')");
	}
	//初始化订货设置
	$ifhas = $db->get_var("select comId from demo_kehu_shezhi where comId=$comId");
	if(empty($ifhas)){
		$db->query("insert into demo_kehu_shezhi(comId) value($comId)");
		$db->query("insert into demo_kehu_level(comId,title,zhekou) value($comId,'普通','100.00')");
		$db->query("insert into user_level(comId,title,zhekou) value($comId,'普通','10.00')");
	}
	$_SESSION[TB_PREFIX.'kehu_title'] = $db->get_var("select kehu_title from demo_kehu_shezhi where comId=$comId");
	//初始化零售设置
	$set = $db->get_row("select comId,storeId from demo_shezhi where comId=$comId");
	if(empty($set)){
		$storeId = $db->get_var("select id from demo_kucun_store where comId=$comId order by id asc limit 1");
		$db_service = getCrmDb();
		$if_tongbu = $db_service->get_var("select if_tongbu from demo_company where id=$comId");
		$db->query("insert into demo_shezhi(comId,com_title,com_logo,com_remark,time_pay,time_shouhuo,tuihuan_reason,website,storeId,if_tongbu) value($comId,'','','','30','7','','',$storeId,$if_tongbu)");
	}else if($set->storeId==0){
		$storeId = $db->get_var("select id from demo_kucun_store where comId=$comId order by id asc limit 1");
		$db->query("update demo_shezhi set storeId=$storeId where comId=$comId");
	}
}
function shezhi(){}