<?php
function index(){}
function areas(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$mendianId = (int)$_SESSION[TB_PREFIX.'mendianId'];
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$status = (int)$request['status'];
	$if_tongbu = (int)$request['if_tongbu'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('pdtPageNum',$pageNum,time()+3600*24*30);
	$keyword = $request['keyword'];
	$tags = $request['tags'];
	$cangkus = $request['cangkus'];
	$source = (int)$request['source'];
	$cuxiao = (int)$request['cuxiao'];
	$payType = (int)$request['payType'];
	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
	}
	$sql="select * from demo_pdt_inventory where comId=$comId";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_pdt where mendianId=$mendianId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and (title like '%$keyword%' or sn='$keyword')";
	}
	if(!empty($cuxiao)){
		$sql.=" and find_in_set($cuxiao,cuxiao_ids)";
	}
	if(!empty($payType)){
		switch ($payType) {
			case 1:
				$sql.=" and sale_tuan=1";
			break;
			case 2:
				$sql.=" and sale_area>0";
			break;
			case 3:
				$sql.=" and sale_lingyuangou=1";
			break;
			case 4:
				$sql.=" and sale_sharegou=1";
			break;
		}
	}
	if(!empty($source)){
		$sql.=" and source=$source";
	}
	if(!empty($if_tongbu)){
		$sql.=" and if_tongbu=$if_tongbu";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($product_set->tags)){
		$tagsarry = explode('@_@',$product_set->tags);
	}
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$mendian = $db->get_row("select com_title from demo_shezhi where comId=$pdt->mendianId");
			$layclass = '';
			if($pdt->status!=1)$layclass ='deleted';
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$pdt->updateTime = empty($pdt->updateTime)?'':date("Y-m-d H:i",strtotime($pdt->updateTime));
			$channel = $db->get_var("select title from demo_pdt_channel where id=".$pdt->channelId);
			$sale_fanwei = $db->get_var("select title from demo_area where id=$pdt->sale_area");
			$com_title=$mendian->com_title;
			$sale_type = '';
			$cuxiao = '';
			if($pdt->status==1){
				$statusInfo = '<font color=\"green\">已上架</font>';
			}else if($pdt->status==-1){
				$statusInfo = '<font color=\"red\">商家下架</font>';
			}else{
				$statusInfo = '<font color=\"red\">待审核</font>';
			}
			$tongbu = $pdt->if_tongbu==1?'<font color=\"green\">已同步</font>':'<font color=\"red\">未同步</font>';
			$pdtstr.=',{"id":'.$pdt->id.',"image":"<img src=\"'.ispic($pdt->image).'?x-oss-process=image/resize,w_54\" width=\"50\">","sn":"<span onclick=\"view_product('.$pdt->id.')\">'.$pdt->sn.'</span>","title":"<span onclick=\"view_product('.$pdt->id.')\">'.$pdt->title.'</span>","key_vals":"'.$pdt->key_vals.'","units":"'.$unitstr.'","price_sale":"'.getXiaoshu($pdt->price_sale,$product_set->price_num).'","price_market":"'.getXiaoshu($pdt->price_market,$product_set->price_num).'","price_cost":"'.getXiaoshu($pdt->price_cost,$product_set->price_num).'","price_gonghuo":"'.getXiaoshu($pdt->price_gonghuo,$product_set->price_num).'","brand":"'.$brand.'","kucun":"'.$kucun.'","kuncun_cost":"'.$kuncun_cost.'","dtTime":"'.$pdt->dtTime.'","updateTime":"'.$pdt->updateTime.'","status":"'.$statusInfo.'","channel":"'.$channel.'","ordering":"'.$pdt->ordering.'","layclass":"'.$layclass.'","com_title":"'.$com_title.'","sale_type":"'.$sale_type.'","sale_fanwei":"'.$sale_fanwei.'","cuxiao":"'.$cuxiao.'","orders":"'.$pdt->orders.'","tongbu":"'.$tongbu.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function rowsSet(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$showRows = array();
	$showRows['image'] =empty($request['rowsSet']['image'])?0:1;
	$showRows['sn'] =empty($request['rowsSet']['sn'])?0:1;
	$showRows['title'] =1;
	$noarry = array('image','sn','title');
	foreach ($request['rowsSet'] as $key=>$val){
		if(!in_array($key,$noarry)){
			$showRows[$key]=1;
		}
	}
	$showRowstr = json_encode($showRows);
	$db->query("update demo_product_set set showRows='$showRowstr' where comId=$comId");
	$product_set = $db->get_row("select * from demo_product_set where comId=comId");
	file_put_contents("../cache/product_set_".$comId.".php",json_encode($product_set,JSON_UNESCAPED_UNICODE));
	redirect("?m=system&s=product");
}
function delPdt(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$isall = (int)$request['isall'];
	$shiId = $db->get_var("select shiId from demo_pdt_inventory where id=$id");
	if($isall==0){
		/*$ifhas = $db->get_var("select id from demo_kucun where comId=10 and inventoryId=$id and kucun<>0 limit 1");
		if(!empty($ifhas)){
			echo '<script>alert("删除失败！只有库存为0的产品才能删除。");</script>';
			redirect(urldecode($request['url']));
		}*/
		$inventory = $db->get_row("select id,productId,key_ids,key_vals,sn,code,image from demo_pdt_inventory where id=$id and comId=$comId");
		$db->query("delete from demo_pdt_inventory where id=$id and comId=$comId");
		//$db->query("delete from demo_kucun where inventoryId=$id and comId=10");
		//$db->query("insert into demo_pdt_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
		$ifhas = $db->get_var("select id from demo_pdt_inventory where comId=$comId and productId=$inventory->productId limit 1");
		if(empty($ifhas)){
			$db->query("delete from demo_pdt where id=$inventory->productId");
			$db->query("delete from demo_pdt_key where productId=$inventory->productId");
		}
	}else{
		$productId = $db->get_var("select productId from demo_pdt_inventory where id=$id and comId=$comId");
		//$ifhas = $db->get_var("select id from demo_kucun where comId=10 and productId=$productId and kucun<>0 limit 1");
		if($productId>0){
			$db->query("delete from demo_pdt_inventory where productId=$productId and comId=$comId");
			//$db->query("delete from demo_kucun where productId=$productId and comId=10");
			$db->query("delete from demo_pdt where id=$productId");
			$db->query("delete from demo_pdt_key where productId=$productId");
			//$db->query("insert into demo_pdt_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$productId.",2,'','','','','',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
		}
	}
	update_pdt_city($shiId,'',2);
	redirect(urldecode($request['url']));
}
function delete(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$shiIds = $db->get_results("select distinct(shiId) as shiId from demo_pdt_inventory where id in($ids)");
	if(!empty($ids)){
		$idsarry = explode(',',$ids);
		$produdctIds = $db->get_var("select group_concat(distinct(productId)) from demo_pdt_inventory where comId=$comId and id in($ids)");
		/*$ifhas = $db->get_var("select id from demo_kucun where comId=10 and productId in($produdctIds) and kucun<>0 limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"删除失败！只有库存为0的产品才能删除。"}';
			exit;
		}*/
		foreach ($idsarry as $id) {
			$inventory = $db->get_row("select id,productId,key_ids,key_vals,sn,code,image from demo_pdt_inventory where id=$id and comId=$comId");
			$db->query("delete from demo_pdt_inventory where id=$id and comId=$comId");
			//$db->query("delete from demo_kucun where inventoryId=$id and comId=10");
			//$db->query("insert into demo_pdt_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$userId.",'".date("Y-m-d H:i:s")."')");
		}
		if(!empty($produdctIds)){
			$pdtArrs = explode(',',$produdctIds);
			foreach ($pdtArrs as $productId) {
				$ifhas = $db->get_var("select id from demo_pdt_inventory where comId=$comId and productId=$productId limit 1");
				if(empty($ifhas)){
					$db->query("delete from demo_pdt where id=$productId");
					$db->query("delete from demo_pdt_key where productId=$productId");
				}
			}
		}
	}
	if(!empty($shiIds)){
		foreach ($shiIds as $s) {
			update_pdt_city($s->shiId,'',2);
		}
	}
	
	echo '{"code":1,"message":"删除成功"}';
	exit;
}
function shangjia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_pdt_inventory set status=1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and id in($ids)");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function xiajia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_pdt_inventory set status=-1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and id in($ids)");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function setTags(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$tags = $request['tags'];
	$produdctIds = $db->get_var("select group_concat(distinct(productId)) from demo_pdt_inventory where comId=$comId and id in($ids)");
	if(!empty($produdctIds)){
		$db->query("update demo_pdt set tags='$tags' where id in($produdctIds)");
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function daochu(){
}
function create(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($request['tijiao']==1&&$_SESSION['tijiao']==1){
		$_SESSION['tijiao'] = 0;
		$id = (int)$request['productId'];
		$title = $request['title'];
		$keywords = $request['keywords'];
		$keywords = str_replace('，',',',$keywords);
		$mendianId = $comId;
		$channelId = (int)$request['channelId'];
		$ordering = (int)$request['ordering'];
		$unit_type = (int)$request['unit_type'];
		$units = $request['units'];
		$unit = $request['unit'];
		$brandId = (int)$request['brandId'];
		$dinghuo_units = $request['dinghuo_units'];
		$status = empty($request['status'])?-1:1;
		$originalPic = $request['originalPic'];
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$shichangjia = $request['shichangjia'];
		$if_dinghuo = empty($request['if_dinghuo'])?0:1;
		$if_lingshou = empty($request['if_lingshou'])?0:1;
		$sale_area = (int)$request['sale_area'];
		$youxiaoqi = $request['youxiaoqi'];
		$if_user_info = empty($request['if_user_info'])?0:1;
		$if_kuaidi = empty($request['if_kuaidi'])?0:1;
		$endTime = $request['endTime'];
		$youxiaoqi_start = $request['youxiaoqi_start'];
		$youxiaoqi_end = $request['youxiaoqi_end'];
		$share_img = $request['share_img'];

		if(!empty($sale_area)){
			$db->query("update demo_shezhi set sale_area=$sale_area where comId=$comId");
		}
		$shiId = (int)$request['shiId'];
		$addrows = '';
		if(!empty($request['addrows'])){
			$addrows = json_encode($request['addrows'],JSON_UNESCAPED_UNICODE);
		}
		$tags = '';
		if(!empty($request['tags'])){
			$tags = implode(',',$request['tags']);
		}
		$unitarry = array();
		if($unit_type==0){
			$u = array();
			$u['title'] = $unit;
			$u['num'] = 1;
			$unitarry[] = $u;
			$dinghuo_units = $unit;
		}else{
			$unitsarr = explode(',',$units);
			if(!empty($unitsarr)){
				foreach ($unitsarr as $us) {
					$uarr = explode('|',$us);
					$u = array();
					$u['title'] = $uarr[0];
					$u['num'] = $uarr[1];
					$unitarry[] = $u;
				}
			}
		}
		$units = json_encode($unitarry,JSON_UNESCAPED_UNICODE);
		if(empty($id)){
			$db->query("insert into demo_pdt(comId,title,channelId,brandId,mendianId,status,ordering,addrows,unit_type,untis,dinghuo_units,keywords,tags,originalPic,cont1,cont2,cont3,dtTime,youxiaoqi,if_user_info,youxiaoqi_start,youxiaoqi_end,share_img) value($comId,'$title',$channelId,$brandId,$mendianId,$status,$ordering,'$addrows',$unit_type,'$units','$dinghuo_units','$keywords','$tags','$originalPic','$cont1','$cont2','$cont3','".date("Y-m-d H:i:s")."','$youxiaoqi',$if_user_info,'$youxiaoqi_start','$youxiaoqi_end','$share_img')");
			$id = $db->get_var("select last_insert_id();");
		}
		/*if(!empty($keywords)){
			$keywordArr = explode(',',$keywords);
			$keywordsql = 'insert into demo_pdt_keyword(comId,keyword,productId) values';
			$keywordsql1 = '';
			$keywordArr = array_unique($keywordArr);
			foreach ($keywordArr as $k) {
				$k = trim($k);
				if(!empty($k)){
					$keywordsql1.=",($comId,'$k',$id)";
				}
			}
			$keywordsql1 = substr($keywordsql1,1);
			$keywordsql.=$keywordsql1;
			$db->query($keywordsql);
		}*/
		$ifmoresn = empty($request['ifmoresn'])?0:1;
		if($ifmoresn==0){
			//单规格
			$productId = $id;
			$sn = $request['sn0'];
			$ifhas = $db->get_var("select id from demo_pdt_inventory where mendianId=$mendianId and sn='$sn' limit 1");
			if(!empty($ifhas)){
				echo '<script>alert("添加失败！该编码已被其他产品使用，请重新添加");location.href="?m=system&s=product&a=create&productId='.$productId.'";</script>';
				exit;
			}
			$weight = empty($request['weight0'])?'0':$request['weight0'];
			$price_sale = empty($request['price_sale0'])?'0':$request['price_sale0'];
			$price_market = empty($request['price_market0'])?'0':$request['price_market0'];
			$fanli_tuanzhang = empty($request['fanli_tuanzhang0'])?'0':$request['fanli_tuanzhang0'];
			$fanli_pingtai = empty($request['fanli_pingtai0'])?'0':$request['fanli_pingtai0'];
			$price_cost = empty($request['price_cost0'])?'0':$request['price_cost0'];
			$code = $request['code0'];
			$hexiaos = (int)$request['hexiaos0'];
			$kucun = (int)$request['kucun0'];
			$image = '';
			if(!empty($originalPic)){
				$pics = explode(',',$originalPic);
				$image = $pics[0];
			}
			$if_lingshou = 1;
			$snInt = $db->get_var("select snInt from demo_pdt_inventory where comId=$comId order by id desc limit 1");
			$snInt = $snInt+1;
			$product = array();
			$product['comId'] =$comId;
			$product['productId'] =$productId;
			$product['channelId'] =$channelId;
			$product['mendianId'] =$mendianId;
			$product['ordering'] =$ordering;
			$product['title'] = $title;
			$product['key_vals'] = '无';
			$product['sn'] = $sn;
			$product['if_kuaidi'] = $if_kuaidi;
			$product['price_sale'] = $price_sale;
			$product['price_market'] = $price_market;
			$product['price_cost'] = $price_cost;
			$product['fanli_tuanzhang'] = $fanli_tuanzhang;
			$product['fanli_pingtai'] = $fanli_pingtai;
			$product['tags'] = $tags;
			$product['sale_area'] = $sale_area;
			$product['shiId'] = $shiId;
			$product['image'] = $image;
			$product['dtTime'] = date("Y-m-d H:i:s");
			$product['snInt'] = $snInt;
			$product['hexiaos'] = $hexiaos;
			$product['kucun'] = $kucun;
			$product['endTime'] = $endTime;
			$db->insert_update('demo_pdt_inventory',$product,'id');
			/*foreach ($cangkus as $c){
				$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle) value($comId,$inventoryId,$productId,".$c->id.",'$entitle')");
			}
			if(!empty($request['d_price_sale0'])){
				foreach ($request['d_price_sale0'] as $key => $val){
					$product_dinghuo = array();
					$product_dinghuo['comId'] = $comId;
					$product_dinghuo['productId'] = $productId;
					$product_dinghuo['inventoryId'] = $inventoryId;
					$product_dinghuo['type'] = 0;
					$product_dinghuo['levelId'] = $key;
					$product_dinghuo['kehuId'] = 0;
					$product_dinghuo['ifsale'] = empty($request['d_ifsale_0'][$key])?0:1;
					$product_dinghuo['price_sale'] = $val;
					$product_dinghuo['price_market'] = $price_market;
					$product_dinghuo['price_cost'] = $price_cost;
					$product_dinghuo['dinghuo_min'] = $request['dinghuo_min0'][$key];
					$product_dinghuo['dinghuo_max'] = $request['dinghuo_max0'][$key];
					insert_update('demo_pdt_dinghuo',$product_dinghuo,'id');
				}
			}
			if(!empty($request['k_price_sale0'])&&!empty($request['dinghuo_bykehu'])){
				foreach ($request['k_price_sale0'] as $key => $val){
					$product_dinghuo = array();
					$product_dinghuo['comId'] = $comId;
					$product_dinghuo['productId'] = $productId;
					$product_dinghuo['inventoryId'] = $inventoryId;
					$product_dinghuo['type'] = 1;
					$product_dinghuo['levelId'] = 0;
					$product_dinghuo['kehuId'] = (int)$request['kehuId'][$key];
					$product_dinghuo['ifsale'] = empty($request['k_ifsale_0'][$key])?0:1;
					$product_dinghuo['price_sale'] = $val;
					$product_dinghuo['price_market'] = $price_market;
					$product_dinghuo['price_cost'] = $price_cost;
					$product_dinghuo['dinghuo_min'] = $request['k_dinghuo_min0'][$key];
					$product_dinghuo['dinghuo_max'] = $request['k_dinghuo_max0'][$key];
					insert_update('demo_pdt_dinghuo',$product_dinghuo,'id');
				}
			}*/
		}else{
			$keyIds = $request['keyIds'];
			if(!empty($keyIds)){
				$productId = $id;
				$db->query("delete from demo_pdt_key where productId=$productId and id not in($keyIds)");
				$db->query("update demo_pdt_key set isnew=0 where productId=$productId");
				if(!empty($request['sn'])){
					foreach ($request['sn'] as $key=>$sn) {
						$ifhas = $db->get_var("select id from demo_pdt_inventory where comId=$comId and sn='$sn' limit 1");
						if(!empty($ifhas)){
							echo '<script>alert("添加失败！该编码已被其他产品使用，请重新添加");location.href="?m=system&s=product&a=create&productId='.$productId.'";</script>';
							exit;
						}
					}
				}
				$dtTime = date('Y-m-d H:i:s');
				if(!empty($request['sn'])){
					$snInt = $db->get_var("select snInt from demo_pdt_inventory where comId=$comId order by id desc limit 1");
					foreach ($request['sn'] as $key=>$sn) {
						$insertSql = "insert into demo_pdt_inventory(comId,productId,channelId,title,key_ids,key_vals,sn,weight,price_sale,price_market,price_cost,code,status,image,ordering,dtTime,snInt,shichangjia,fanli_tuanzhang,sale_area,shiId,hexiaos,kucun,endTime,if_kuaidi) values";
						$insertSql1 = '';
						$key_ids = $key;
						$valIds = str_replace('-',',',$key_ids);
						$keys = $db->get_results("select title,originalPic from demo_pdt_key where id in($valIds)");
						$key_vals = '';
						$image = '';
						if(!empty($keys)){
							foreach ($keys as $k) {
								$key_vals.=','.$k->title;
								if(!empty($k->originalPic))$image=$k->originalPic;
							}
							$key_vals = substr($key_vals,1);
						}
						if(empty($image)&&!empty($originalPic)){
							$pics = explode(',',$originalPic);
							$image = $pics[0];
						}
						$weight = $request['weight'][$key];
						$price_sale = $request['price_sale'][$key];
						$price_market = $request['price_market'][$key];
						$price_cost = $request['price_cost'][$key];
						$fanli_tuanzhang = $request['fanli_tuanzhang'][$key];
						$code = $request['code'][$key];
						$hexiaos = (int)$request['hexiaos'][$key];
						$kucun = (int)$request['kucun'][$key];
						$snInt = $snInt+1;
						$insertSql1="($comId,$productId,$channelId,'$title','$key','$key_vals','$sn','$weight','$price_sale','$price_market','$price_cost','$code',$status,'$image',$ordering,'$dtTime',$snInt,'$shichangjia','$fanli_tuanzhang',$sale_area,$shiId,$hexiaos,$kucun,'$endTime',$if_kuaidi)";
						$db->query($insertSql.$insertSql1);
						$inventoryId = $db->get_var("select last_insert_id()");
						$entitle = getFirstCharter($title);
						/*foreach ($cangkus as $c){
							$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle) value($comId,$inventoryId,$productId,".$c->id.",'$entitle')");
						}
						//级别订货价
						foreach ($levels as $l){
							$product_dinghuo = array();
							$product_dinghuo['comId'] = $comId;
							$product_dinghuo['productId'] = $productId;
							$product_dinghuo['inventoryId'] = $inventoryId;
							$product_dinghuo['type'] = 0;
							$product_dinghuo['levelId'] = $l->id;
							$product_dinghuo['kehuId'] = 0;
							$product_dinghuo['ifsale'] = empty($request['d_ifsale_'.$l->id][$key])?0:1;
							$product_dinghuo['price_sale'] = $request['d_price_sale'.$l->id][$key];
							$product_dinghuo['price_market'] = $price_market;
							$product_dinghuo['price_cost'] = $price_cost;
							$product_dinghuo['dinghuo_min'] = $request['d_dinghuo_min'.$l->id][$key];
							$product_dinghuo['dinghuo_max'] = $request['d_dinghuo_max'.$l->id][$key];
							insert_update('demo_pdt_dinghuo',$product_dinghuo,'id');
						}
						//客户订货价
						if(!empty($request['moreKehuId'])&&!empty($request['dinghuo_bykehu'])){
							foreach ($request['moreKehuId'] as $kehuId){
								$product_dinghuo = array();
								$product_dinghuo['comId'] = $comId;
								$product_dinghuo['productId'] = $productId;
								$product_dinghuo['inventoryId'] = $inventoryId;
								$product_dinghuo['type'] = 1;
								$product_dinghuo['levelId'] = 0;
								$product_dinghuo['kehuId'] = $kehuId;
								$product_dinghuo['ifsale'] = empty($request['k_ifsale_'.$kehuId][$key])?0:1;
								$product_dinghuo['price_sale'] = $request['k_price_sale'.$kehuId][$key];
								$product_dinghuo['price_market'] = $price_market;
								$product_dinghuo['price_cost'] = $price_cost;
								$product_dinghuo['dinghuo_min'] = $request['k_dinghuo_min'.$kehuId][$key];
								$product_dinghuo['dinghuo_max'] = $request['k_dinghuo_max'.$kehuId][$key];
								insert_update('demo_pdt_dinghuo',$product_dinghuo,'id');
							}
						}*/
					}
				}
			}
		}
		$cityName = $db->get_var("select title from demo_area where id=$shiId");
		update_pdt_city($shiId,$cityName,1);
		redirect("?m=system&s=pdts");
		exit;
	}else{
		//删除临时的未点击保存时的创建的虚拟产品
		$linshis = $db->get_var("select group_concat(id) from demo_pdt where comId=$comId and status=-2");
		if(!empty($linshis)){
			$db->query("delete from demo_pdt where id in($linshis)");
			$db->query("delete from demo_pdt_key where productId in($linshis)");
		}
	}
}
function getPricesTabel(){//动态加载价格填写区域
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$productId = $request['productId'];
	$title = $request['title'];
	$channelId = (int)$request['channelId'];
	if(empty($productId)){
		$db->query("insert into demo_pdt(comId,title,channelId,status) value($comId,'$title',$channelId,-2)");
		$productId = $db->get_var("select last_insert_id();");
	}
	$newIdstr = '';
	$hasKeyIds = array();
	if(count($request['gg']) > 0){
		foreach ($request['gg'] as $key => $value) {
			$value = trim($value);
			$pdtKeyId = (int)$request['pdtKeyId'.$key];
			if(!empty($pdtKeyId)){
				$if_key_exist = $db->get_var("select id from demo_pdt_key where id=$pdtKeyId");
				if(!empty($if_key_exist)){
					$db->query("update demo_pdt_key set title='".$value."' where id=".$pdtKeyId);
				}else if(!empty($request['ggseci'.$key])){
					$db->query("insert into demo_pdt_key (id,productId,title,parentId,isnew) values ($pdtKeyId,$productId,'$value',0,1)");
				}
			}else{
				$db->query("insert into demo_pdt_key (productId,title,parentId,isnew) values (".$productId.",'".$value."',0,1)");
				$pdtKeyId = $db->get_var("select last_insert_id();");
			}
			if(!empty($request['ggseci'.$key])){
				$hasKeyIds[] = $pdtKeyId;
				foreach ($request['ggseci'.$key] as $kg => $vg) {
					$vg = trim($vg);
					if($vg){
						$pdtValId = $db->get_var("select id from demo_pdt_key where productId=$productId and parentId=$pdtKeyId and kg=$kg limit 1");
						$image = $request['image'.$key][$kg];
						if(empty($pdtValId)){
							$db->query("insert into demo_pdt_key(productId,title,parentId,originalPic,isnew,kg) values (".$productId.",'".$vg."',".$pdtKeyId.",'$image',1,$kg)");
							$pdtValId = $db->get_var("select last_insert_id();");
						}else{
							$db->query("update demo_pdt_key set title='".$vg."',originalPic='$image' where id=".$pdtValId);
						}
						$hasKeyIds[] = $pdtValId;
					}
				}
				$newIdstr .= ',{"index":'.$key.',"val":"'.$pdtKeyId.'"}';
			}else{
				if(!empty($if_key_exist)){
					$db->query("delete from demo_pdt_key where id=$pdtKeyId");
				}
			}
		}
	}
	if(!empty($newIdstr))$newIdstr=substr($newIdstr,1);
	$keyIds = '0';
	if(!empty($hasKeyIds)){
		$keyIds = implode(',', $hasKeyIds);
		$db->query("delete from demo_pdt_key where productId=$productId and isnew=1 and id not in($keyIds)");
	}
	$array = array();
	$keys = $db->get_results("select id,title from demo_pdt_key where productId=$productId and parentId=0 and id in($keyIds) order by id asc");
	$table_body_tr = '';
	//订货级别html标签
	$table_body_tr1 = '';
	$rowFirst = count($keys);
	$snInt = $db->get_var("select snInt from demo_pdt_inventory where comId=$comId order by id desc limit 1");
	$chushu = pow(10,$product_set->price_num);
	$step = 1/$chushu;
	$chushu = pow(10,$product_set->number_num);
	$step1 = 1/$chushu;
	if($rowFirst == 1){
		$table_tr_th = '<tr height="40">
		<th>'.$keys[0]->title.'</th><th>商品编码</th><th>售价（元）</th><th>门市价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
		$vals = $db->get_results("select * from demo_pdt_key where productId=$productId and parentId=".$keys[0]->id." and id in($keyIds) order by id asc");
		$gg2 = count($vals);
		if($gg2 > 0){
        	foreach ($vals as $key => $value) {
        		$snInt++;
        		$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
        		$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='".$value->id."' limit 1");
        		$table_body_tr .= '<tr height="35">
        		<td>'.$value->title.'</td>
        		<td><input type="text" name="sn['.$value->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
        		$table_body_tr .= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$value->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$value->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" class="piliang_cost" name="price_cost['.$value->id.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$value->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$value->id.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$value->id.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
        		$table_body_tr .= '</tr>';
        	}
        	$table_body_tr .= '<tr><td>批量设置</td><td></td>';
        	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
        	$table_body_tr .='</tr>';
        }
    }else if($rowFirst == 2){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr2 = '';
    	if($ggseci0>0){
    		for($m=0; $m<$ggseci0; $m++) {
    			$ggseci1 = count($vals1);
    			if($ggseci1>0){
    				for($j=0; $j<$ggseci1; $j++) {
    					$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='".$vals0[$m]->id.'-'.$vals1[$j]->id."' limit 1");
    					$ssss = '';
    					if($j==0){
    						$ssss .= '<td rowspan="'.($ggseci1).'">'.$vals0[$m]->title.'</td>';
    					}
    					$snInt+=1;
    					$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
    					$nr2 .= '<tr height="35">'.$ssss.'
    					<td>'.$vals1[$j]->title.'</td>
    					<td><input type="text" name="sn['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
    					$nr2.= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_cost" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" name="price_cost['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
    					$nr2.= '</tr>';
    				}
    			}
    		}
    	}
    	//}
    	$table_tr_th = '<tr height="40">
    	'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>市场价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
    	$table_body_tr = $nr2;
    	$table_body_tr .= '<tr><td colspan="2">批量设置</td><td></td>';
    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
    	$table_body_tr .='</tr>';
    }else if($rowFirst == 3){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	$vals2 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[2]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr0 = ''; $nr1 = ''; $nr2 = '';
    	if($ggseci0>0){
    		for($m=0; $m<$ggseci0; $m++) {
    			$ggseci1 = count($vals1);
    			if($ggseci1>0){
    				for($j=0; $j<$ggseci1; $j++) {
    					$ggseci2 = count($vals2);		                		
    					if($ggseci2>0){
    						for ($k=0; $k<$ggseci2; $k++) {
    							$snInt+=1;
    							$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='$pricekey' limit 1");
    							$ssss = '';
    							if($j==0 && $k==0){
    								$ssss .= '<td rowspan="'.($ggseci1*$ggseci2).'">'.$vals0[$m]->title.'</td>
    								<td rowspan="'.($ggseci2).'">
    								'.$vals1[$j]->title.'</td>';
    							}elseif($k==0){
    								$ssss .= '<td rowspan="'.($ggseci2).'">
    								'.$vals1[$j]->title.'</td>';
    							}
    							$nr2 .= '<tr height="35">'.$ssss.'
    							<td>
    							'.$vals2[$k]->title.'
    							</td>
    							<td><input type="text" name="sn['.$pricekey.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
    							$nr2 .= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$pricekey.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" class="piliang_cost" name="price_cost['.$pricekey.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$pricekey.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$pricekey.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$pricekey.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
    							$nr2 .= '</tr>';
    						}
    					}
    				}
    			}
    		}
    	}
		//}
		$table_tr_th = '<tr height="40">
		'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>市场价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
		$table_body_tr = $nr2;
		$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
    	$table_body_tr .='</tr>';
	}
	$table = '<table width="100%">
	<input type="hidden" name="keyIds" id="keyIds" value="'.$keyIds.'">
	'.$table_tr_th.$table_body_tr.'</table>';
	$table = preg_replace('/((\s)*(\n)+(\s)*)/','',$table);
	$table = str_replace('"','\"',$table);
	$table_level = '<table width="100%">'.$table_tr_th1.$table_body_tr1.'</table>';
	$table_level = preg_replace('/((\s)*(\n)+(\s)*)/','',$table_level);
	$table_level = str_replace('"','\"',$table_level);
	echo '{"code":1,"productId":'.$productId.',"newIdstr":['.$newIdstr.'],"table":"'.$table.'","table_level":"'.$table_level.'"}';
	exit;
}
function getPricesTabel1(){//动态加载价格填写区域
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$productId = $request['productId'];
	$newIdstr = '';
	$hasKeyIds = array();
	$keys = $db->get_results("select id from demo_pdt_key where productId=$productId and parentId=0 order by id asc");
	if(!empty($keys)){
		foreach ($keys as $key => $val) {
			$newIdstr .= ',{"index":'.($key+1).',"val":"'.$val->id.'"}';
		}
	}
	/*if(count($request['gg']) > 0){
		foreach ($request['gg'] as $key => $value) {
			$value = trim($value);
			$pdtKeyId = (int)$request['pdtKeyId'.$key];
			if(!empty($pdtKeyId)){
				$db->query("update demo_pdt_key set title='".$value."' where id=".$pdtKeyId);
			}else{
				$db->query("insert into demo_pdt_key (productId,title,parentId,isnew) values (".$productId.",'".$value."',0,1)");
				$pdtKeyId = $db->get_var("select last_insert_id();");
			}
			if(!empty($request['ggseci'.$key])){
				$hasKeyIds[] = $pdtKeyId;
				foreach ($request['ggseci'.$key] as $kg => $vg) {
					$vg = trim($vg);
					if($vg){
						$pdtValId = $db->get_var("select id from demo_pdt_key where productId=$productId and parentId=$pdtKeyId and kg=$kg limit 1");
						$image = $request['image'.$key][$kg];
						if(empty($pdtValId)){
							$db->query("insert into demo_pdt_key(productId,title,parentId,originalPic,isnew,kg) values (".$productId.",'".$vg."',".$pdtKeyId.",'$image',1,$kg)");
							$pdtValId = $db->get_var("select last_insert_id();");
						}else{
							$db->query("update demo_pdt_key set title='".$vg."',originalPic='$image' where id=".$pdtValId);
						}
						$hasKeyIds[] = $pdtValId;
					}
				}
				$newIdstr .= ',{"index":'.$key.',"val":"'.$pdtKeyId.'"}';
			}else{
				$db->query("delete from demo_pdt_key where id=$pdtKeyId");
			}
		}
	}*/
	if(!empty($newIdstr))$newIdstr=substr($newIdstr,1);
	$keyIds = $db->get_var("select group_concat(id) from demo_pdt_key where productId=$productId");
	$array = array();
	$keys = $db->get_results("select id,title from demo_pdt_key where productId=$productId and parentId=0 and id in($keyIds) order by id asc");
	$table_body_tr = '';
	//订货级别html标签
	$table_body_tr1 = '';
	$rowFirst = count($keys);
	$chushu = pow(10,$product_set->price_num);
	$step = 1/$chushu;
	$chushu = pow(10,$product_set->number_num);
	$step1 = 1/$chushu;
	if($rowFirst == 1){
		$table_tr_th = '<tr height="40">
		<th>'.$keys[0]->title.'</th><th>商品编码</th><th>售价（元）</th><th>门市价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
		$vals = $db->get_results("select * from demo_pdt_key where productId=$productId and parentId=".$keys[0]->id." and id in($keyIds) order by id asc");
		$gg2 = count($vals);
		if($gg2 > 0){
        	foreach ($vals as $key => $value) {
        		$snInt++;
        		$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
        		$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='".$value->id."' limit 1");
        		$table_body_tr .= '<tr height="35">
        		<td>'.$value->title.'</td>
        		<td><input type="text" name="sn['.$value->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
        		$table_body_tr .= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$value->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$value->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_cost" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" name="price_cost['.$value->id.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$value->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$value->id.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$value->id.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
        		$table_body_tr .= '</tr>';
        	}
        	$table_body_tr .= '<tr><td>批量设置</td><td></td>';
        	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
        	$table_body_tr .='</tr>';
        }
    }else if($rowFirst == 2){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr2 = '';
    	if($ggseci0>0){
    		for($m=0; $m<$ggseci0; $m++) {
    			$ggseci1 = count($vals1);
    			if($ggseci1>0){
    				for($j=0; $j<$ggseci1; $j++) {
    					$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='".$vals0[$m]->id.'-'.$vals1[$j]->id."' limit 1");
    					$ssss = '';
    					if($j==0){
    						$ssss .= '<td rowspan="'.($ggseci1).'">'.$vals0[$m]->title.'</td>';
    					}
    					$snInt+=1;
    					$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
    					$nr2 .= '<tr height="35">'.$ssss.'
    					<td>'.$vals1[$j]->title.'</td>
    					<td><input type="text" name="sn['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
    					$nr2.= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" class="piliang_cost" name="price_cost['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
    					$nr2.= '</tr>';
    				}
    			}
    		}
    	}
    	//}
    	$table_tr_th = '<tr height="40">
    	'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>市场价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
    	$table_body_tr = $nr2;
    	$table_body_tr .= '<tr><td colspan="2">批量设置</td><td></td>';
    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
    	$table_body_tr .='</tr>';
    }else if($rowFirst == 3){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	$vals2 = $db->get_results("select * from demo_pdt_key where productId=$productId and id in($keyIds) and parentId=".$keys[2]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr0 = ''; $nr1 = ''; $nr2 = '';
    	if($ggseci0>0){
    		for($m=0; $m<$ggseci0; $m++) {
    			$ggseci1 = count($vals1);
    			if($ggseci1>0){
    				for($j=0; $j<$ggseci1; $j++) {
    					$ggseci2 = count($vals2);		                		
    					if($ggseci2>0){
    						for ($k=0; $k<$ggseci2; $k++) {
    							$snInt+=1;
    							$sn = $product_set->sn_rule.date("Ymd").rand(1000,9999).$snInt;
    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,hexiaos,kucun from demo_pdt_inventory where productId=$productId and key_ids='$pricekey' limit 1");
    							$ssss = '';
    							if($j==0 && $k==0){
    								$ssss .= '<td rowspan="'.($ggseci1*$ggseci2).'">'.$vals0[$m]->title.'</td>
    								<td rowspan="'.($ggseci2).'">
    								'.$vals1[$j]->title.'</td>';
    							}elseif($k==0){
    								$ssss .= '<td rowspan="'.($ggseci2).'">
    								'.$vals1[$j]->title.'</td>';
    							}
    							$nr2 .= '<tr height="35">'.$ssss.'
    							<td>
    							'.$vals2[$k]->title.'
    							</td>
    							<td><input type="text" name="sn['.$pricekey.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
    							$nr2 .= '<td><input type="number" class="piliang_sale" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$pricekey.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="checkPrice(\''.$price->price_cost.'\',this.value);" class="piliang_cost" name="price_cost['.$pricekey.']" value="'.$price->price_cost.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$pricekey.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:102px;" /></td><td><input type="number" class="piliang_hexiaos" name="hexiaos['.$pricekey.']" value="'.$price->hexiaos.'" mustrow step="1" style="width:102px;" /></td><td><input type="number" class="piliang_kucun" name="kucun['.$pricekey.']" value="'.$price->kucun.'" mustrow step="1" style="width:102px;" /></td>';
    							$nr2 .= '</tr>';
    						}
    					}
    				}
    			}
    		}
    	}
		//}
		$table_tr_th = '<tr height="40">
		'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>市场价（元）</th><th width="167px">成本价(供货价)</th><th width="167px">会员返利</th><th width="167px">使用次数</th><th width="167px">库存</th></tr>';
		$table_body_tr = $nr2;
		$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'cost\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'hexiaos\',this.value);" value="0" step="1" style="width:102px;" /></td><td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" step="1" style="width:102px;" /></td>';
    	$table_body_tr .='</tr>';
	}
	$table = '<table width="100%">
	<input type="hidden" name="keyIds" id="keyIds" value="'.$keyIds.'">
	'.$table_tr_th.$table_body_tr.'</table>';
	$table = preg_replace('/((\s)*(\n)+(\s)*)/','',$table);
	$table = str_replace('"','\"',$table);
	$table_level = '<table width="100%">'.$table_tr_th1.$table_body_tr1.'</table>';
	$table_level = preg_replace('/((\s)*(\n)+(\s)*)/','',$table_level);
	$table_level = str_replace('"','\"',$table_level);
	echo '{"code":1,"productId":'.$productId.',"newIdstr":['.$newIdstr.'],"table":"'.$table.'","table_level":"'.$table_level.'"}';
	exit;
}
function editProduct(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($request['tijiao']==1){
		$id = (int)$request['productId'];
		$title = $request['title'];
		$keywords = $request['keywords'];
		$keywords = str_replace('，',',',$keywords);
		$channelId = (int)$request['channelId'];
		$ordering = (int)$request['ordering'];
		$unit_type = (int)$request['unit_type'];
		$units = $request['units'];
		$unit = $request['unit'];
		$brandId = (int)$request['brandId'];
		$dinghuo_units = $request['dinghuo_units'];
		$status = empty($request['status'])?-1:1;
		$zong_status = $request['zong_status'];
		$originalPic = $request['originalPic'];
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$if_dinghuo = empty($request['if_dinghuo'])?0:1;
		$if_lingshou = empty($request['if_lingshou'])?0:1;
		$sale_area = (int)$request['sale_area'];
		$shiId = (int)$request['shiId'];
		$addrows = '';
		$shichangjia = $request['shichangjia'];
		$youxiaoqi = $request['youxiaoqi'];
		$if_user_info = empty($request['if_user_info'])?0:1;
		$if_kuaidi = empty($request['if_kuaidi'])?0:1;
		$endTime = $request['endTime'];
		$youxiaoqi_start = $request['youxiaoqi_start'];
		$youxiaoqi_end = $request['youxiaoqi_end'];
		$share_img = $request['share_img'];
		if(!empty($request['addrows'])){
			$addrows = json_encode($request['addrows'],JSON_UNESCAPED_UNICODE);
		}
		$tags = '';
		if(!empty($request['tags'])){
			$tags = implode(',',$request['tags']);
		}
		$unitarry = array();
		if($unit_type==0){
			$u = array();
			$u['title'] = $unit;
			$u['num'] = 1;
			$unitarry[] = $u;
			$dinghuo_units = $unit;
		}else{
			$unitsarr = explode(',',$units);
			if(!empty($unitsarr)){
				foreach ($unitsarr as $us) {
					$uarr = explode('|',$us);
					$u = array();
					$u['title'] = $uarr[0];
					$u['num'] = $uarr[1];
					$unitarry[] = $u;
				}
			}
		}
		$units = json_encode($unitarry,JSON_UNESCAPED_UNICODE);
		$db->query("update demo_pdt set title='$title',channelId=$channelId,brandId=$brandId,status=$status,ordering=$ordering,addrows='$addrows',unit_type=$unit_type,untis='$units',dinghuo_units='$dinghuo_units',keywords='$keywords',tags='$tags',originalPic='$originalPic',cont1='$cont1',cont2='$cont2',cont3='$cont3',youxiaoqi='$youxiaoqi',if_user_info=$if_user_info,youxiaoqi_start='$youxiaoqi_start',youxiaoqi_end='$youxiaoqi_end',share_img='$share_img' where id=$id and comId=$comId");
		$entitle = getFirstCharter($title);
		//$db->query("update demo_kucun set entitle='$entitle' where comId=10 and productId=$id");
		/*$db->query("delete from demo_pdt_keyword where comId=10 and productId=$id");
		if(!empty($keywords)){
			$keywordArr = explode(',',$keywords);
			$keywordsql = 'insert into demo_pdt_keyword(comId,keyword,productId) values';
			$keywordsql1 = '';
			$keywordArr = array_unique($keywordArr);
			foreach ($keywordArr as $k) {
				$k = trim($k);
				if(!empty($k)){
					$keywordsql1.=",($comId,'$k',$id)";
				}
			}
			$keywordsql1 = substr($keywordsql1,1);
			$keywordsql.=$keywordsql1;
			$db->query($keywordsql);
		}*/
		$keyIds = $request['keyIds'];
		//$levels = $db->get_results("select id from demo_kehu_level where comId=$comId order by ordering desc,id asc");
		//$cangkus = $db->get_results("select id from demo_kucun_store where comId=10 and status=1");
		if(!empty($keyIds)){
			$productId = $id;
			$db->query("delete from demo_pdt_key where productId=$productId and id not in($keyIds)");
			$db->query("update demo_pdt_key set isnew=0 where productId=$productId");
			if(!empty($request['sn'])){
				foreach ($request['sn'] as $key=>$sn) {
					$ifhas = $db->get_var("select id from demo_pdt_inventory where comId=$comId and sn='$sn' and productId!=$productId limit 1");
					if(!empty($ifhas)){
						echo '<script>alert("添加失败！编码“'.$sn.'”已被其他产品使用，请确保编码的唯一性");history.go(-1);</script>';
						exit;
					}
				}
			}
			if(!empty($request['sn'])){
				$delInventorys = '0';
				$snInt = $db->get_var("select snInt from demo_pdt_inventory where comId=$comId order by id desc limit 1");
				foreach ($request['sn'] as $key=>$sn){
					$insertSql = "insert into demo_pdt_inventory(comId,productId,channelId,title,key_ids,key_vals,sn,weight,price_sale,price_market,price_cost,code,status,image,ordering,snInt,shichangjia,fanli_tuanzhang,zong_status,sale_area,shiId,hexiaos,dtTime,mendianId,kucun,endTime,if_kuaidi) values";
					$insertSql1 = '';
					$key_ids = $key;
					$valIds = str_replace('-',',',$key_ids);
					$keys = $db->get_results("select title,originalPic from demo_pdt_key where id in($valIds)");
					$key_vals = '';
					$image = '';
					if(!empty($keys)){
						foreach ($keys as $k) {
							$key_vals.=','.$k->title;
							if(!empty($k->originalPic))$image=$k->originalPic;
						}
						$key_vals = substr($key_vals,1);
					}
					if(empty($image)&&!empty($originalPic)){
						$pics = explode(',',$originalPic);
						$image = $pics[0];
					}
					$weight = $request['weight'][$key];
					$price_sale = $request['price_sale'][$key];
					$price_market = $request['price_market'][$key];
					$price_cost = $request['price_cost'][$key];
					$fanli_tuanzhang = $request['fanli_tuanzhang'][$key];
					$code = $request['code'][$key];
					$hexiaos = (int)$request['hexiaos'][$key];
					$kucun = (int)$request['kucun'][$key];
					$ifhas = $db->get_var("select id from demo_pdt_inventory where productId=$productId and key_ids='$key_ids' limit 1");
					if(empty($ifhas)){
						$snInt+=1;
						$insertSql1="($comId,$productId,$channelId,'$title','$key','$key_vals','$sn','$weight','$price_sale','$price_market','$price_cost','$code',$status,'$image',$ordering,$snInt,'$shichangjia','$fanli_tuanzhang',$zong_status,$sale_area,$shiId,$hexiaos,'".date("Y-m-d H:i:s")."',$comId,$kucun,'$endTime',$if_kuaidi)";
						$db->query($insertSql.$insertSql1);
						$inventoryId = $db->get_var("select last_insert_id()");
						$entitle = getFirstCharter($title);
						/*foreach ($cangkus as $c){
							$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle) value($comId,$inventoryId,$productId,".$c->id.",'$entitle')");
						}*/
					}else{
						$db->query("update demo_pdt_inventory set title='$title',key_vals='$key_vals',sn='$sn',weight='$weight',price_sale='$price_sale',price_market='$price_market',price_cost='$price_cost',code='$code',status=$status,image='$image',ordering=$ordering,updateTime='".date("Y-m-d H:i:s")."',shichangjia='$shichangjia',fanli_tuanzhang='$fanli_tuanzhang',sale_area=$sale_area,shiId=$shiId,zong_status=$zong_status,hexiaos=$hexiaos,kucun=$kucun,endTime='$endTime',if_kuaidi=$if_kuaidi where id=$ifhas");
						$inventoryId = $ifhas;
					}
					$delInventorys.=','.$inventoryId;
				}
				$delIds = $db->get_results("select id,productId,key_ids,key_vals,sn,code,image from demo_pdt_inventory where productId=$productId and id not in($delInventorys) and comId=$comId");
				if(!empty($delIds)){
					foreach ($delIds as $inventory) {
						$id =$inventory->id;
						$db->query("delete from demo_pdt_inventory where id=$id and comId=$comId");
						//$db->query("delete from demo_kucun where inventoryId=$id and comId=10");
						//$db->query("insert into demo_pdt_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
					}
				}
			}
		}
		$url = empty($request['url'])?'?m=system&s=product':$request['url'];
		redirect(urldecode($url));
		exit;
	}else{
		$db->query("delete from demo_pdt_key where productId=".$request['id']." and isnew=1");
	}
}
function edit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if($request['tijiao']==1){
		$id = (int)$request['productId'];
		$title = $request['title'];
		$keywords = $request['keywords'];
		$keywords = str_replace('，',',',$keywords);
		$channelId = (int)$request['channelId'];
		$ordering = (int)$request['ordering'];
		$mendianId = $comId;
		$unit_type = (int)$request['unit_type'];
		$units = $request['units'];
		$unit = $request['unit'];
		$brandId = (int)$request['brandId'];
		$dinghuo_units = $request['dinghuo_units'];
		$status = empty($request['status'])?-1:1;
		$zong_status = (int)$request['zong_status'];
		if($status<0)$status=0;
		$originalPic = $request['originalPic'];
		$shichangjia = $request['shichangjia'];
		$sale_area = (int)$request['sale_area'];
		$shiId = (int)$request['shiId'];
		$youxiaoqi = $request['youxiaoqi'];
		$if_user_info = empty($request['if_user_info'])?0:1;
		$if_kuaidi = empty($request['if_kuaidi'])?0:1;
		$endTime = $request['endTime'];
		$youxiaoqi_start = $request['youxiaoqi_start'];
		$youxiaoqi_end = $request['youxiaoqi_end'];
		$share_img = $request['share_img'];
		$addrows = '';
		if(!empty($request['addrows'])){
			$addrows = json_encode($request['addrows'],JSON_UNESCAPED_UNICODE);
		}
		$tags = '';
		if(!empty($request['tags'])){
			$tags = implode(',',$request['tags']);
		}
		$unitarry = array();
		if($unit_type==0){
			$u = array();
			$u['title'] = $unit;
			$u['num'] = 1;
			$unitarry[] = $u;
			$dinghuo_units = $unit;
		}else{
			$unitsarr = explode(',',$units);
			if(!empty($unitsarr)){
				foreach ($unitsarr as $us) {
					$uarr = explode('|',$us);
					$u = array();
					$u['title'] = $uarr[0];
					$u['num'] = $uarr[1];
					$unitarry[] = $u;
				}
			}
		}
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$units = json_encode($unitarry,JSON_UNESCAPED_UNICODE);
		$db->query("update demo_pdt set title='$title',channelId=$channelId,brandId=$brandId,status=$status,ordering=$ordering,addrows='$addrows',unit_type=$unit_type,untis='$units',dinghuo_units='$dinghuo_units',keywords='$keywords',tags='$tags',originalPic='$originalPic',cont1='$cont1',cont2='$cont2',cont3='$cont3',youxiaoqi='$youxiaoqi',if_user_info=$if_user_info,youxiaoqi_start='$youxiaoqi_start',youxiaoqi_end='$youxiaoqi_end',share_img='$share_img' where id=$id and comId=$comId");
		$entitle = getFirstCharter($title);
		$productId = $id;
		$inventoryId = (int)$request['id'];
		$sn = $request['sn0'];
		$weight = empty($request['weight0'])?'0':$request['weight0'];
		$price_sale = empty($request['price_sale0'])?'0':$request['price_sale0'];
		$price_market = empty($request['price_market0'])?'0':$request['price_market0'];
		$price_cost = empty($request['price_cost0'])?'0':$request['price_cost0'];
		$fanli_tuanzhang = empty($request['fanli_tuanzhang0'])?'0':$request['fanli_tuanzhang0'];
		$fanli_pingtai = empty($request['fanli_pingtai0'])?'0':$request['fanli_pingtai0'];
		$hexiaos = (int)$request['hexiaos0'];
		$kucun = (int)$request['kucun0'];
		$if_lingshou = 1;
		$image = '';
		if(!empty($originalPic)){
			$pics = explode(',',$originalPic);
			$image = $pics[0];
		}

		$product = array();
		$product['id'] =$inventoryId;
		$product['comId'] =$comId;
		$product['productId'] =$productId;
		$product['sn'] =$sn;
		$product['channelId'] =$channelId;
		$product['ordering'] =$ordering;
		$product['title'] = $title;
		$product['price_sale'] = $price_sale;
		$product['price_market'] = $price_market;
		$product['price_cost'] = $price_cost;
		$product['fanli_tuanzhang'] = $fanli_tuanzhang;
		$product['fanli_pingtai'] = $fanli_pingtai;
		$product['status'] = $status;
		$product['image'] = $image;
		$product['sale_area'] = $sale_area;
		$product['shiId'] = $shiId;
		$product['hexiaos'] = $hexiaos;
		$product['updateTime'] = date("Y-m-d H:i:s");
		$product['kucun'] = $kucun;
		$product['endTime'] = $endTime;
		$product['if_kuaidi'] = $if_kuaidi;
		$db->insert_update('demo_pdt_inventory',$product,'id');
		$url = empty($request['url'])?'?m=system&s=pdts':$request['url'];
		$cityName = $db->get_var("select title from demo_area where id=$shiId");
		update_pdt_city($shiId,$cityName,1);
		redirect(urldecode($url));
		exit;
	}
}
function view(){}
function getpdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$storeId = (int)$request['storeId'];
	$channelId = (int)$request['channelId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$keyword = $request['keyword'];
	$hasIds = $request['hasIds'];
	if(empty($hasIds))$hasIds='0';
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql="select id,sn,title,key_vals,productId from demo_pdt_inventory where comId=$comId and id not in($hasIds)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		/*$pdtIds = $db->get_var("select group_concat(productId) from demo_pdt_keyword where comId=10 and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';*/
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId from demo_pdt where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			//$kucun = $db->get_var("select kucun-yugouNum from demo_kucun where inventoryId=".$pdt->id." and storeId=$storeId limit 1");
			if(empty($kucun))$kucun=0;
			$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","shuliang":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" onfocus=\"showTips(this,\''.$kucun.'\');\" max=\"'.$kucun.'\" id=\"shuliang_'.$pdt->id.'\">","units":"'.$unitstr.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function get_dinghuo_price(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$type = (int)$request['type']-1;
	$inventoryId = (int)$request['inventoryId'];
	$return = array();
	$return['code'] = 1;
	$return['message'] = 'ok';
	$return['data'] = array();
	/*$dinghuos = $db->get_results("select levelId,kehuId,ifsale,price_sale,dinghuo_min,dinghuo_max from demo_pdt_dinghuo where comId=10 and type=$type and inventoryId=$inventoryId order by id asc");
	if(!empty($dinghuos)){
		foreach ($dinghuos as $d) {
			$data = array();
			if($type==0){
				$data['name'] = $db->get_var("select title from demo_kehu_level where id=$d->levelId");
			}else{
				$data['name'] = $db->get_var("select title from demo_kehu where id=$d->kehuId");
			}
			$data['if_dinghuo'] = $d->ifsale==1?'是':'否';
			$data['price'] = $d->price_sale;
			$data['min'] = getXiaoshu($d->dinghuo_min,$product_set->number_num);
			$data['max'] = getXiaoshu($d->dinghuo_max,$product_set->number_num);
			$return['data'][] = $data;
		}
	}*/
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function bohui(){
	global $db,$request;
	$productId = (int)$request['productId'];
	$cont = $request['cont'];
	$db->query("update demo_pdt set status=-2 where id=$productId");
	$db->query("update demo_pdt_inventory set status=-2,shenhe_reason='$cont' where productId=$productId");
	die('{"code":1}');
}
//更新城市列表$city:城市id $oprate:1添加  2删除
function update_pdt_city($city,$cityName,$oprate){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$uptate_cache = false;
	if($oprate==1){
		$ifhas = $db->get_var("select id from demo_pdt_area where comId=$comId and shiId=$city limit 1");
		if(empty($ifhas)){
			$orders = getFirstCharter($cityName);
			$db->query("insert into demo_pdt_area(comId,shiId,title,orders) value($comId,$city,'$cityName','$orders')");
			$uptate_cache = true;
		}
	}else{
		$ifhas = $db->get_var("select id from demo_pdt_inventory where comId=$comId and shiId=$city limit 1");
		if(empty($ifhas)){
			$db->query("delete from demo_pdt_area where comId=$comId and shiId=$city limit 1");
			$uptate_cache = true;
		}
	}
	if($uptate_cache){
		//写缓存
		cache_area();
	}
}
function delarea(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_pdt_area where comId=$comId and id=".(int)$request['id']);
	cache_area();
	die('{"code":1}');
}
function setremen(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_pdt_area set if_remen=1 where comId=$comId and id=".(int)$request['id']);
	cache_area();
	die('{"code":1}');
}
function qxremen(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_pdt_area set if_remen=0 where comId=$comId and id=".(int)$request['id']);
	cache_area();
	die('{"code":1}');
}
//缓存城市
function cache_area(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$areas = $db->get_results("select * from demo_pdt_area where comId=$comId order by orders asc");
	if(!empty($areas)){
		$now_orders = '';
		$array = array();
		foreach ($areas as $area){
			$array[$area->orders][] = $area;
		}
		$content = json_encode($array,JSON_UNESCAPED_UNICODE);
		file_put_contents("../cache/pdt_area_".$comId.".php",$content);
	}
}