<?php
function index(){}
function set(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$if_image = empty($request['if_image'])?0:1;
	$if_brand = empty($request['if_brand'])?0:1;
	$if_weight = empty($request['if_weight'])?0:1;
	$weight = $request['weight'];
	$if_addrows = empty($request['if_addrows'])?0:1;
	$addrows = '';
	if(!empty($request['addrows'])){
		$addrows = implode('@_@',array_filter($request['addrows']));
	}
	$if_tags = empty($request['if_tags'])?0:1;
	$tags = '';
	if(!empty($request['tags'])){
		$tags = implode('@_@',array_filter($request['tags']));
	}
	$if_lingshou = empty($request['if_lingshou'])?0:1;
	$if_dinghuo = empty($request['if_dinghuo'])?0:1;
	if($if_dinghuo==0){
		$if_dinghuo_min = 0;
		$if_dinghuo_max = 0;
	}else{
		$if_dinghuo_min = empty($request['if_dinghuo_min'])?0:1;
		$if_dinghuo_max = empty($request['if_dinghuo_max'])?0:1;
	}
	$price_num = (int)$request['price_num'];
	$number_num = (int)$request['number_num'];
	$sn_rule = $request['sn_rule'];
	$ifhas = $db->get_var("select comId from demo_product_set where comId=$comId");
	if(empty($ifhas)){
		$db->query("insert into demo_product_set(comId,if_image,if_brand,if_weight,weight,if_addrows,addrows,if_tags,tags,if_lingshou,if_dinghuo,if_dinghuo_min,if_dinghuo_max,price_num,number_num,sn_rule) value($comId,$if_image,$if_brand,$if_weight,'$weight',$if_addrows,'$addrows',$if_tags,'$tags',$if_lingshou,$if_dinghuo,$if_dinghuo_min,$if_dinghuo_max,$price_num,$number_num,'$sn_rule')");
	}else{
		$db->query("update demo_product_set set if_image=$if_image,if_brand=$if_brand,if_weight=$if_weight,weight='$weight',if_addrows=$if_addrows,addrows='$addrows',if_tags=$if_tags,tags='$tags',if_lingshou=$if_lingshou,if_dinghuo=$if_dinghuo,if_dinghuo_min=$if_dinghuo_min,if_dinghuo_max=$if_dinghuo_max,price_num=$price_num,number_num=$number_num,sn_rule='$sn_rule' where comId=$comId");
	}
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	file_put_contents("../cache/product_set_".$comId.".php",json_encode($product_set,JSON_UNESCAPED_UNICODE));
	redirect("?m=system&s=product_set");
}