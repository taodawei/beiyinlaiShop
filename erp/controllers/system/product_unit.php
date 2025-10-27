<?php
function index(){}
function addProductUnit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$title = $request['title'];
	if(empty($id)){
		$ifhas = $db->get_var("select id from demo_product_unit where comId=$comId and title='$title'");
		if(!empty($ifhas)){
			echo '<script>alert("您已经创建过这个单位了！");history.go(-1);</script>';
			exit;
		}
		$db->query("insert into demo_product_unit(comId,title) value($comId,'$title')");
		$id = $db->get_var("select last_insert_id();");
	}else{
		$db->query("update demo_product_unit set title='$title' where id=$id and comId=$comId");
	}
	redirect("?m=system&s=product_unit");
}
function delUnit(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ifhas = $db->get_var("select id from demo_product_unit where comId=$comId and id=$id limit 1");
	if(!empty($ifhas)){
		$db->query("delete from demo_product_unit where id=$id");
		echo '{"code":1,"message":"删除成功！","ids":"'.$id.'"}';
	}else{
		$no_units = $db->get_row("select comId,no_units from demo_product_set where comId=$comId");
		if(empty($no_units)){
			$db->query("insert into demo_product_set(comId,no_units) value($comId,'$id')");
		}else if(empty($no_units->no_units)){
			$db->query("update demo_product_set set no_units='$id' where comId=$comId");
		}else{
			$unitstr = $no_units->no_units.','.$id;
			$db->query("update demo_product_set set no_units='$unitstr' where comId=$comId");
		}
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
		file_put_contents("../cache/product_set_".$comId.".php",json_encode($product_set,JSON_UNESCAPED_UNICODE));
		echo '{"code":1,"message":"删除成功！","ids":"'.$id.'"}';
	}
	exit;
}