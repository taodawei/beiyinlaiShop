<?php
function index(){}
function daoru(){}
function daoru1(){}
function daoru2(){}
function daoru_edit(){}
function daoru_edit1(){}
function daoru_edit2(){}
function daochuExcel(){
	global $db,$request;
	require_once ABSPATH.'inc/excel.php';
	$pandianJsonData = stripcslashes($request['pandianJsonData']);
	$jilus = json_decode($pandianJsonData,true);
	if($request['edit']==1){
		$indexKey = array('商品编码','商品名称','商品规格','排序','上架状态','是否开启拼团','成团数量','重量','零售价','市场价','拼团价格','分销提成','条形码');
	}else{
		$indexKey = array('商品编码','商品名称','商品品牌','一级分类','二级分类','三级分类','四级分类','多规格字段设置','规格1内容','规格2内容','规格3内容','商品介绍','计量单位','换算比率','可订货单位','条形码','关键词','状态','排序值','重量','零售价(元)','市场价(元)','','','','','');
	}
	exportExcel($jilus,'失败记录',$indexKey);
	exit;
}

function daorushuo2(){}

function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	$channelId = (int)$request['channelId'];
	$brandId = (int)$request['brandId'];
	$status = (int)$request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('pdtPageNum',$pageNum,time()+3600*24*30);
	$keyword = $request['keyword'];
	$tags = $request['tags'];
	$cangkus = !empty($request['cangkus'])?$request['cangkus']:'';
	$source = (int)$request['source'];
	$cuxiao = (int)$request['cuxiao'];
	$hebing = (int)$request['hebing'];
	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
	}
	$sql="select * from demo_product_inventory where comId=$comId";
	$numsql = "select count(distinct(productId)) from demo_product_inventory where comId=$comId";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
		$numsql.=" and channelId in($channelIds)";
	}
	if(!empty($brandId)){
		$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
		if(empty($productIds))$productIds='0';
		$sql.=" and productId in($productIds)";
		$numsql.=" and productId in($productIds)";
	}
	if(!empty($status)){
		$sql.=" and status=$status";
		$numsql.=" and status=$status";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		
		$pdtIds1 = $db->get_var("select group_concat(id) from demo_product where skuId like '%$keyword%' ");
		if(empty($pdtIds1)) $pdtIds1 = 0;
		
		$sql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or productId in ($pdtIds1) or code='$keyword')";
		$numsql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or productId in ($pdtIds1) or code='$keyword')";
	}
	if(!empty($tags)){
		$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
		$pdtIdsql.=" and(1!=1";
		foreach ($tags as $t) {
			$pdtIdsql.=" or tags like '%$t%'";
		}
		$pdtIdsql.=")";
		$pdtIds = $db->get_var($pdtIdsql);
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
		$numsql.=" and productId in($pdtIds)";
	}
	if(!empty($cuxiao)){
		$sql.=" and cuxiao=$cuxiao";
		$numsql.=" and cuxiao=$cuxiao";
	}
	if(!empty($source)){
		$sql.=" and source=$source";
		$numsql.=" and source=$source";
	}
	
	$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];
	if($mendianId > 0){
	    $pdtIds = $db->get_var("select group_concat(id) from demo_product where mendianId = $mendianId ");
		if(empty($pdtIds))$pdtIds='0';
	    $sql.=" and productId in($pdtIds)";
		$numsql.=" and productId in($pdtIds)";
	}
	
	if($hebing==1){
		$sql.=" group by productId";
	}else{
		$numsql = str_replace('count(distinct(productId))','count(*)',$numsql);
	}
	$count = $db->get_var($numsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId,mendianId,skuId from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			foreach ($units as $u) {
				$unitstr.=$u['title'].'/';
			}
			$unitstr = substr($unitstr,0,strlen($unitstr)-1);
			$brand = '无';
			if(!empty($product->brandId)){
				$brand = $db->get_var("select title from demo_product_brand where id=".$product->brandId);
			}
			if($comId==1009){
				$lipinka_orders = (int)$db->get_var("select count(*) from order_detail9 where inventoryId=$pdt->id and status=1");
				$lipinka_jilu = $db->get_row("select num,bind_num from lipinka_jilu where id=(select lipinkaId from demo_product_inventory where id=$pdt->id)");
				$kucun = $lipinka_jilu->num-$lipinka_jilu->bind_num-$lipinka_orders;
			}else{
				$kucun = $db->get_var("select sum(kucun) from demo_kucun where comId=$comId and inventoryId=".$pdt->id);
			}
			
			$layclass = '';
			if($pdt->status!=1)$layclass ='deleted';
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$pdt->updateTime = empty($pdt->updateTime)?'':date("Y-m-d H:i",strtotime($pdt->updateTime));
			$channel = $db->get_var("select title from demo_product_channel where id=".$pdt->channelId);
			$kuncun_cost = $db->get_var("select avg(chengben) from demo_kucun where comId=$comId and inventoryId=$pdt->id");
			$kuncun_cost = getXiaoshu($kuncun_cost,$product_set->price_num);
			$pdt->title = str_replace('\\','/',$pdt->title);
			if(!empty($pdt->baozhiqi)){
				$baozhiqi = date("Y-m-d",$pdt->baozhiqi);
				if(!empty($pdt->baozhiqi_days)){
					$now = time();
					$tixing = $pdt->baozhiqi_days*86400;
					if($pdt->baozhiqi - $now<=$tixing){
						$pdt->title = $pdt->title.'<font color=red>【保质期即将到期】</font>';
						$baozhiqi = '<font color=red>'.$baozhiqi.'</font>';
					}
				}
				$pdt->baozhiqi = $baozhiqi;
			}else{
				$pdt->baozhiqi = '';
			}
			
			$mendianTitle = '自营产品';
			if($product->mendianId > 0){
			    $mendianTitle = $db->get_var("select title from demo_shequ where id = $product->mendianId");
			}
			
			//$pdt->image
			//https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/MAB37407/MAB37407_1.jpg
			
				    //获取上级分类id
        		  //864  863  853    861  862
    		    $root_id = 864;
    		    if($pdt->channelId == 861 || $pdt->channelId == 862){
    		        $root_id = $pdt->channelId;
    		    }else{
    		       $root_id = $db->get_var("select parentId from demo_product_channel where id = $pdt->channelId"); 
    		    }
    		  //  $objectUrl = "product/$root_id/$product->skuId/";
        //         $fileList = listObjectsFile($objectUrl, 100);
        //         if(!empty($fileList['data'])){
        //              $originalPics = $fileList['data']; 
        //         }
        			    
			$image="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$product->skuId/$product->skuId".'_1.jpg';
			
			$image="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$product->skuId/$product->skuId".'_1.jpg';
			$statusInfo = $pdt->status==1?'<font color=\"green\">已上架</font>':($pdt->status==0?'<font color=\"red\">待审核</font>':'<font color=\"red\">已下架</font>');
			$pdtstr.=',{"id":'.$pdt->id.',"image":"<img src=\"'.ispic($image).'?x-oss-process=image/resize,w_54\" width=\"50\" height=\"50\">","sn":"<span onclick=\"view_product('.$pdt->id.')\">'.$pdt->sn.'</span>","title":"<span onclick=\"view_product('.$pdt->id.')\">'.$pdt->title.'</span>","key_vals":"'.$pdt->key_vals.'","units":"'.$unitstr.'","price_sale":"'.getXiaoshu($pdt->price_sale,$product_set->price_num).'","price_market":"'.getXiaoshu($pdt->price_market,$product_set->price_num).'","price_cost":"'.getXiaoshu($pdt->price_cost,$product_set->price_num).'","brand":"'.$brand.'","kucun":"'.getXiaoshu($kucun,$product_set->number_num).'","kuncun_cost":"'.$kuncun_cost.'","dtTime":"'.$pdt->dtTime.'","updateTime":"'.$pdt->updateTime.'","status":"'.$statusInfo.'","channel":"'.$channel.'","baozhiqi":"'.$pdt->baozhiqi.'","ordering":"'.$pdt->ordering.'","layclass":"'.$layclass.'","mendianTitle":"'.$mendianTitle.'"}';
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
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	file_put_contents("../cache/product_set_".$comId.".php",json_encode($product_set,JSON_UNESCAPED_UNICODE));
	
	redirect("?m=system&s=product");
}
function delPdt(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$isall = (int)$request['isall'];
	if($isall==0){
		$ifhas = $db->get_var("select id from demo_kucun where comId=$comId and inventoryId=$id and kucun<>0 limit 1");
		if(!empty($ifhas)){
// 			echo '<script>alert("删除失败！只有库存为0的产品才能删除。");</script>';
// 			redirect(urldecode($request['url']));
		}
		$inventory = $db->get_row("select id,productId,key_ids,key_vals,sn,code,image from demo_product_inventory where id=$id and comId=$comId");
		$db->query("delete from demo_product_inventory where id=$id and comId=$comId");
		$db->query("delete from demo_kucun where inventoryId=$id and comId=$comId");
		$db->query("insert into demo_product_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
		$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and productId=$inventory->productId limit 1");
		if(empty($ifhas)){
			$db->query("delete from demo_product where id=$inventory->productId");
			$db->query("delete from demo_product_key where productId=$inventory->productId");
			$db->query("delete from demo_kucun where inventoryId=$id");
		}
	}else{
		$productId = $db->get_var("select productId from demo_product_inventory where id=$id and comId=$comId");
		$ifhas = $db->get_var("select id from demo_kucun where comId=$comId and productId=$productId and kucun<>0 limit 1");
		if(!empty($ifhas)){
// 			echo '<script>alert("删除失败！该产品有库存不为0的规格。");</script>';
// 			redirect(urldecode($request['url']));
		}
		if($productId>0){
			$db->query("delete from demo_product_inventory where productId=$productId and comId=$comId");
			$db->query("delete from demo_kucun where productId=$productId and comId=$comId");
			$db->query("delete from demo_product where id=$productId");
			$db->query("delete from demo_product_key where productId=$productId");
			$db->query("insert into demo_product_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$productId.",2,'','','','','',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
		}
	}
	redirect(urldecode($request['url']));
}
function delete(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	if(!empty($ids)){
		$idsarry = explode(',',$ids);
		$produdctIds = $db->get_var("select group_concat(distinct(productId)) from demo_product_inventory where comId=$comId and id in($ids)");
		$ifhas = $db->get_var("select id from demo_kucun where comId=$comId and productId in($produdctIds) and kucun<>0 limit 1");
		if(!empty($ifhas)){
// 			echo '{"code":0,"message":"删除失败！只有库存为0的产品才能删除。"}';
// 			exit;
		}
		foreach ($idsarry as $id) {
			$inventory = $db->get_row("select id,productId,key_ids,key_vals,sn,code,image from demo_product_inventory where id=$id and comId=$comId");
			$db->query("delete from demo_product_inventory where id=$id and comId=$comId");
			$db->query("delete from demo_kucun where inventoryId=$id and comId=$comId");
			$db->query("insert into demo_product_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$userId.",'".date("Y-m-d H:i:s")."')");
		}
		if(!empty($produdctIds)){
			$pdtArrs = explode(',',$produdctIds);
			foreach ($pdtArrs as $productId) {
				$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and productId=$productId limit 1");
				if(empty($ifhas)){
					$db->query("delete from demo_product where id=$productId");
					$db->query("delete from demo_product_key where productId=$productId");
				}
			}
		}
	}
	echo '{"code":1,"message":"删除成功"}';
	exit;
}

function delPdf()
{
   	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = (int)$request['ids'];
	$productId = (int)$db->get_var("select productId from demo_product_inventory where id = $ids");
	$db->query("update demo_product set book_url='' where comId=$comId and id =  $productId");
	
	echo '{"code":1,"message":"操作成功"}';
	exit; 
}

function shangjia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$hebing = (int)$request['hebing'];
	
	if($hebing){
	    $produdctIds = $db->get_var("select group_concat(productId) from demo_product_inventory where id in ($ids) ");
	    
	    $db->query("update demo_product_inventory set status=1 where comId=$comId and id in($produdctIds)");
	    $db->query("update demo_product_inventory set status=1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and productId in($produdctIds)");
	}else{
	    $db->query("update demo_product_inventory set status=1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and id in($ids)");
	}
	
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function xiajia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	
	$hebing = (int)$request['hebing'];
	if($hebing){
	    $produdctIds = $db->get_var("select group_concat(productId) from demo_product_inventory where id in ($ids) ");
	    
	    $db->query("update demo_product_inventory set status=-1 where comId=$comId and id in($produdctIds)");
	    $db->query("update demo_product_inventory set status=-1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and productId in($produdctIds)");
	}else{
	   $db->query("update demo_product_inventory set status=-1,updateTime='".date("Y-m-d H:i:s")."' where comId=$comId and id in($ids)");
	}
	
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function setTags(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$tags = $request['tags'];
	$produdctIds = $db->get_var("select group_concat(distinct(productId)) from demo_product_inventory where comId=$comId and id in($ids)");
	if(!empty($produdctIds)){
		$db->query("update demo_product set tags='$tags' where id in($produdctIds)");
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function daochu(){
}
function create(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(!empty($request['tijiao'])&&$request['tijiao']==1&&$_SESSION['tijiao']==1){
		$_SESSION['tijiao'] = 0;
		$id = (int)$request['productId'];
		$title = str_replace('\\','/', $request['title']);
		$title = str_replace('"','“', $title);
		$title = str_replace("'",'', $title);
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
		if($_SESSION['if_tongbu']==1 && $_SESSION['if_tongbu_pdt']==1){
			$status = 0;
		}
		$originalPic = $request['originalPic'];
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$remark = $request['remark'];
		$shichangjia = !empty($request['shichangjia'])?(int)$request['shichangjia']:0;
		$if_dinghuo = empty($request['if_dinghuo'])?0:1;
		$if_lingshou = empty($request['if_lingshou'])?0:1;
		$yunfei_moban_ding = (int)$request['yunfei_moban_ding'];
		$yunfei_moban = (int)$request['yunfei_moban'];
		$if_tongbu = (int)$_SESSION['if_tongbu_pdt'];
		$share_img = $request['share_img'];
		$subtitle = $request['subtitle'];
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
		$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];
		
		$skuId = $request['skuId'];
		$skuDay = $request['skuDay'];
		
		if(empty($id)){
			$price_name = $db->get_var("select price_name from demo_shezhi where comId=$comId");
			$db->query("insert into demo_product(comId,title,channelId,brandId,status,ordering,addrows,unit_type,untis,dinghuo_units,keywords,tags,originalPic,cont1,cont2,cont3,remark,if_dinghuo,if_lingshou,dtTime,yunfei_moban,yunfei_moban_ding,price_name,share_img,subtitle,mendianId,skuId,skuDay) value($comId,'$title',$channelId,$brandId,$status,$ordering,'$addrows',$unit_type,'$units','$dinghuo_units','$keywords','$tags','$originalPic','$cont1','$cont2','$cont3','$remark',$if_dinghuo,$if_lingshou,'".date("Y-m-d H:i:s")."',$yunfei_moban,$yunfei_moban_ding,'$price_name','$share_img','$subtitle', $mendianId, '$skuId', '$skuDay')");
			$id = $db->get_var("select last_insert_id();");
		}else{
			$db->query("update demo_product set title='$title',channelId=$channelId,brandId=$brandId,status=$status,ordering=$ordering,addrows='$addrows',unit_type=$unit_type,untis='$units',dinghuo_units='$dinghuo_units',keywords='$keywords',tags='$tags',originalPic='$originalPic',cont1='$cont1',cont2='$cont2',cont3='$cont3',remark='$remark',if_dinghuo=$if_dinghuo,if_lingshou=$if_lingshou,yunfei_moban=$yunfei_moban,yunfei_moban_ding=$yunfei_moban_ding,share_img='$share_img',subtitle='$subtitle',mendianId=$mendianId,skuId='$skuId', skuDay = '$skuDay' where id=$id and comId=$comId");
		}
		if(!empty($keywords)){
			$keywordArr = explode(',',$keywords);
			$keywordsql = 'insert into demo_product_keyword(comId,keyword,productId) values';
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
		}
		$cangkus = $db->get_results("select id from demo_kucun_store where comId=$comId and status=1 order by id");
		$levels = $db->get_results("select id from demo_kehu_level where comId=$comId order by ordering desc,id asc");
		$ifmoresn = empty($request['ifmoresn'])?0:1;
		if($ifmoresn==0){
			//单规格
			$productId = $id;
			$sn = $request['sn0'];
			$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and sn='$sn' limit 1");
			if(!empty($ifhas)){
				echo '<script>alert("添加失败！该编码已被其他产品使用，请重新添加");location.href="?m=system&s=product&a=create&productId='.$productId.'";</script>';
				exit;
			}
			$weight = empty($request['weight0'])?0:$request['weight0'];
			$price_sale = empty($request['price_sale0'])?0:$request['price_sale0'];
			$sale_tuan = empty($request['sale_tuan'])?0:1;
			$tuan_num = (int)$request['tuan_num'];
			$price_tuan = !empty($request['price_tuan0'])?$request['price_tuan0']:0;
			$price_shequ_tuan = !empty($request['price_shequ_tuan0'])?(int)$request['price_shequ_tuan0']:0;
			$price_market = empty($request['price_market0'])?0:$request['price_market0'];
			$fanli_tuanzhang = empty($request['fanli_tuanzhang0'])?0:$request['fanli_tuanzhang0'];
			$code = $request['code0'];
			$kucun = (int)$request['kucun0'];
			$lipinkaId = (int)$request['lipinkaId0'];
			$lipinkaType = 0;
			if($lipinkaId>0){$lipinkaType = $db->get_var("select type from lipinka_jilu where id=$lipinkaId");}
			$image = '';
			if(!empty($originalPic)){
				$pics = explode('|',$originalPic);
				$image = $pics[0];
			}
			$if_lingshou = 1;
			$snInt = $db->get_var("select snInt from demo_product_inventory where comId=$comId order by id desc limit 1");
			$snInt = $snInt+1;
			$price_cost=(int)$request['price_cost0'];
			$db->query("insert into demo_product_inventory(comId,productId,channelId,title,key_vals,sn,weight,price_sale,price_market,price_cost,code,status,image,if_lingshou,ordering,dtTime,snInt,shichangjia,if_tongbu,lipinkaId,lipinkaType,fanli_tuanzhang,sale_tuan,tuan_num,price_tuan,price_shequ_tuan) value($comId,$productId,$channelId,'$title','无','$sn',$weight,$price_sale,$price_market,$price_cost,'$code',$status,'$image',$if_lingshou,$ordering,'".date("Y-m-d H:i:s")."',$snInt,$shichangjia,$if_tongbu,$lipinkaId,$lipinkaType,$fanli_tuanzhang,$sale_tuan,$tuan_num,$price_tuan,$price_shequ_tuan)");
			$inventoryId = $db->get_var("select last_insert_id()");
			$entitle = getFirstCharter($title);
			foreach ($cangkus as $ii=>$c){
				if($ii==0){
					$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle,kucun) value($comId,$inventoryId,$productId,".$c->id.",'$entitle',$kucun)");
				}else{
					$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle,kucun) value($comId,$inventoryId,$productId,".$c->id.",'$entitle',$kucun)");
				}
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
				// 	insert_update('demo_product_dinghuo',$product_dinghuo,'id');
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
				// 	insert_update('demo_product_dinghuo',$product_dinghuo,'id');
				}
			}
		}else{
			$keyIds = $request['keyIds'];
			if(!empty($keyIds)){
				$productId = $id;
				$db->query("delete from demo_product_key where productId=$productId and id not in($keyIds)");
				$db->query("update demo_product_key set isnew=0 where productId=$productId");
				if(!empty($request['sn'])){
					foreach ($request['sn'] as $key=>$sn) {
						$price_sale = $request['price_sale'][$key];
						$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and sn='$sn' limit 1");
						if(!empty($ifhas)){
							echo '<script>alert("添加失败！该编码已被其他产品使用，请重新添加");location.href="?m=system&s=product&a=create&productId='.$productId.'";</script>';
							exit;
						}
					}
				}
				$dtTime = date('Y-m-d H:i:s');
				if(!empty($request['sn'])){
					$snInt = $db->get_var("select snInt from demo_product_inventory where comId=$comId order by id desc limit 1");
					foreach ($request['sn'] as $key=>$sn) {
						$insertSql = "insert into demo_product_inventory(comId,productId,channelId,title,key_ids,key_vals,sn,weight,price_sale,price_market,price_cost,code,status,image,if_lingshou,ordering,dtTime,snInt,shichangjia,if_tongbu,lipinkaId,lipinkaType,fanli_tuanzhang,sale_tuan,tuan_num,price_tuan,price_shequ_tuan) values";
						$insertSql1 = '';
						$key_ids = $key;
						$valIds = str_replace('-',',',$key_ids);
						$keys = $db->get_results("select title,originalPic from demo_product_key where id in($valIds)");
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
							$pics = explode('|',$originalPic);
							$image = $pics[0];
						}
						$weight = (int)$request['weight'][$key];
						$price_sale = $request['price_sale'][$key];
						$sale_tuan = empty($request['sale_tuan'])?0:1;
						$tuan_num = (int)$request['tuan_num'];
						$price_tuan = !empty($request['price_tuan'][$key])?$request['price_tuan'][$key]:0;
						$price_shequ_tuan = !empty($request['price_shequ_tuan'][$key]) ?(int)$request['price_shequ_tuan'][$key]:0;
						$price_market = !empty($request['price_market'][$key])?$request['price_market'][$key]:0;
						$price_cost = !empty($request['price_cost'][$key])?(int)$request['price_cost'][$key]:0;
						$code = $request['code'][$key];
						$kucun = $request['kucun'][$key];
						$lipinkaId = (int)$request['lipinkaId'][$key];
						$fanli_tuanzhang = empty($request['fanli_tuanzhang'][$key])?0:$request['fanli_tuanzhang'][$key];
						$lipinkaType = 0;
						if($lipinkaId>0){$lipinkaType = $db->get_var("select type from lipinka_jilu where id=$lipinkaId");}
						$snInt = $snInt+1;
						$insertSql1="($comId,$productId,$channelId,'$title','$key','$key_vals','$sn',$weight,$price_sale,$price_market,$price_cost,'$code',$status,'$image',1,$ordering,'$dtTime',$snInt,$shichangjia,$if_tongbu,$lipinkaId,$lipinkaType,$fanli_tuanzhang,$sale_tuan,$tuan_num,$price_tuan,$price_shequ_tuan)";
						$db->query($insertSql.$insertSql1);
						$inventoryId = $db->get_var("select last_insert_id()");
						$entitle = getFirstCharter($title);
						foreach ($cangkus as $c){
							$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle,kucun) value($comId,$inventoryId,$productId,".$c->id.",'$entitle',$kucun)");
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
				// 			insert_update('demo_product_dinghuo',$product_dinghuo,'id');
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
								$product_dinghuo['price_cost'] = (int)$price_cost;
								$product_dinghuo['dinghuo_min'] = $request['k_dinghuo_min'.$kehuId][$key];
								$product_dinghuo['dinghuo_max'] = $request['k_dinghuo_max'.$kehuId][$key];
								// insert_update('demo_product_dinghuo',$product_dinghuo,'id');
							}
						}
					}
				}
			}
		}
		
		$product = $db->get_row("select * from demo_product where id = $productId");
		$addParam = array(
		    'channelId' => $product->channelId,
		    'brandId' => $product->brandId,
		    'productId' => $product->id,
		    'dtTime' => date("Y-m-d H:i:s")
        );
        
        $db->insert_update("demo_product_params", $addParam, "id");
		
		if($_SESSION['tongbu_zong']==1){
			tongbu_zong($productId);
		}
		redirect("?m=system&s=product");
		exit;
	}else{
		/*
		$linshis = $db->get_var("select group_concat(id) from demo_product where comId=$comId and status=-2");
		if(!empty($linshis)){
			$db->query("delete from demo_product where id in($linshis)");
			$db->query("delete from demo_product_key where productId in($linshis)");
		}*/
	}
}
function getPricesTabel(){//动态加载价格填写区域
	global $db,$request,$if_fenxiao,$if_pintuan;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	$productId = $request['productId'];
	$if_create = (int)$request['if_create'];
	$title = $request['title'];
	$channelId = (int)$request['channelId'];
	if(empty($productId)){
		$db->query("insert into demo_product(comId,title,channelId,status) value($comId,'$title',$channelId,-2)");
		$productId = $db->get_var("select last_insert_id();");
	}
	$newIdstr = '';
	$hasKeyIds = array();
	if(count($request['gg']) > 0){
		foreach ($request['gg'] as $key => $value) {
			$value = trim($value);
			$pdtKeyId = (int)$request['pdtKeyId'.$key];
			if(!empty($pdtKeyId)){
				$if_key_exist = $db->get_var("select id from demo_product_key where id=$pdtKeyId");
				if(!empty($if_key_exist)){
					$db->query("update demo_product_key set title='".$value."' where id=".$pdtKeyId);
				}else if(!empty($request['ggseci'.$key])){
					$db->query("insert into demo_product_key (id,productId,title,parentId,isnew) values ($pdtKeyId,$productId,'$value',0,1)");
				}
			}else{
				$db->query("insert into demo_product_key (productId,title,parentId,isnew) values (".$productId.",'".$value."',0,1)");
				$pdtKeyId = $db->get_var("select last_insert_id();");
			}
			if(!empty($request['ggseci'.$key])){
				$hasKeyIds[] = $pdtKeyId;
				foreach ($request['ggseci'.$key] as $kg => $vg) {
					$vg = trim($vg);
					if($vg){
						$pdtValId = $db->get_var("select id from demo_product_key where productId=$productId and parentId=$pdtKeyId and kg=$kg limit 1");
						$image = $request['image'.$key][$kg];
						if(empty($pdtValId)){
							$db->query("insert into demo_product_key(productId,title,parentId,originalPic,isnew,kg) values (".$productId.",'".$vg."',".$pdtKeyId.",'$image',1,$kg)");
							$pdtValId = $db->get_var("select last_insert_id();");
						}else{
							$db->query("update demo_product_key set title='".$vg."',originalPic='$image' where id=".$pdtValId);
						}
						$hasKeyIds[] = $pdtValId;
					}
				}
				$newIdstr .= ',{"index":'.$key.',"val":"'.$pdtKeyId.'"}';
			}else{
				if(!empty($if_key_exist)){
					$db->query("delete from demo_product_key where id=$pdtKeyId");
				}
			}
		}
	}
	if(!empty($newIdstr))$newIdstr=substr($newIdstr,1);
	$keyIds = '0';
	if(!empty($hasKeyIds)){
		$keyIds = implode(',', $hasKeyIds);
		$db->query("delete from demo_product_key where productId=$productId and isnew=1 and id not in($keyIds)");
	}
	$array = array();
	$keys = $db->get_results("select id,title from demo_product_key where productId=$productId and parentId=0 and id in($keyIds) order by id asc");
	$table_body_tr = '';
	//订货级别html标签
	$table_body_tr1 = '';
	$rowFirst = count($keys);
	$snInt = $db->get_var("select snInt from demo_product_inventory where comId=$comId order by id desc limit 1");
	$chushu = pow(10,$product_set->price_num);
	$step = 1/$chushu;
	$chushu = pow(10,$product_set->number_num);
	$step1 = 1/$chushu;
	if($rowFirst == 1){
		$table_tr_th = '<tr height="40">
		<th>'.$keys[0]->title.'</th><th>商品编码</th>
		'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th>
		<th>市场价（元）</th>
		<th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团价格</th>
		<th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th>'.($if_create==1?'<th>库存</th>':'').'</tr>';
		
		$table_tr_th1 = '<tr height="40"><th>'.$keys[0]->title.'</th><th>折扣</th><th>允许订货</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
		$vals = $db->get_results("select * from demo_product_key where productId=$productId and parentId=".$keys[0]->id." and id in($keyIds) order by id asc");
		$gg2 = count($vals);
		if($gg2 > 0){
        	foreach ($vals as $key => $value) {
        		$snInt++;
        		$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
        		$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='".$value->id."' limit 1");
        		$table_body_tr .= '<tr height="35">
        		<td>'.$value->title.'</td>
        		<td><input type="text" name="sn['.$value->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
        		if($product_set->if_weight==1){
        			$table_body_tr .= '<td><input type="number" class="piliang_weight" name="weight['.$value->id.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
        		}

    			$table_body_tr .= '<td><input type="number" class="piliang_sale" name="price_sale['.$value->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$value->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$value->id.']" value="'.(int)$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$value->id.']" value="'.(int)$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
        		$table_body_tr .= '<td><input type="text" name="code['.$value->id.']" value="'.$price->code.'" style="width:132px;" /></td>'.($if_create==1?'<td><input type="number" name="kucun['.$value->id.']" class="piliang_kucun" value="0" style="width:112px;" /></td>':'').'</tr>';
        		$table_body_tr1 .='<tr height="35" '.(empty($price)?'':'id="d_row_{levelId}_'.$price->id.'"').'><td>'.$value->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$value->id.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$value->id.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$value->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" onchange="checkDinghuoNum(this,1);" min="0"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$value->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$value->id.']" type="hidden" value=""/></td></tr>';
        	}
        	$table_body_tr .= '<tr><td>批量设置</td><td></td>';
        	if($product_set->if_weight==1){
        		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
        	}
    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
        	$table_body_tr .='<td></td>'.($if_create==1?'<td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" style="width:112px;" /></td>':'').'</tr>';
        	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
        }
		

    }else if($rowFirst == 2){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
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
    					$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='".$vals0[$m]->id.'-'.$vals1[$j]->id."' limit 1");
    					$ssss = '';
    					if($j==0){
    						$ssss .= '<td rowspan="'.($ggseci1).'">'.$vals0[$m]->title.'</td>';
    					}
    					$snInt+=1;
    					$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
    					$nr2 .= '<tr height="35">'.$ssss.'
    					<td>'.$vals1[$j]->title.'</td>
    					<td><input type="text" name="sn['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
    					if($product_set->if_weight==1){
    						$nr2 .= '<td><input type="number" class="piliang_weight" name="weight['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
    					}
						$nr2.= '<td><input type="number" class="piliang_sale" name="price_sale['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(int)$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(int)$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
    					$nr2.= '<td><input type="text" name="code['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->code.'" style="width:132px;" /></td>'.($if_create==1?'<td><input type="number" class="piliang_kucun" name="kucun['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="0" style="width:112px;" /></td>':'').'</tr>';
    					$table_body_tr1.='<tr height="35" '.(empty($price)?'':'id="d_row_{levelId}_'.$price->id.'"').'>'.$ssss.'<td>'.$vals1[$j]->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" min="0" onchange="checkDinghuoNum(this,1);"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="hidden" value=""/></td></tr>';
    				}
    			}
    		}
    	}
    	//}
    	$table_tr_th = '<tr height="40">
    	'.$bt.'<th>商品编码</th>
		'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th><th>市场价（元）</th><th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团价格</th><th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th>'.($if_create==1?'<th>库存</th>':'').'</tr>';
    	$table_tr_th1 = '<tr height="40">'.$bt.'<th>允许订货</th><th>折扣</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
    	$table_body_tr = $nr2;
    	$table_body_tr .= '<tr><td colspan="2">批量设置</td><td></td>';
    	if($product_set->if_weight==1){
    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
    	}
		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
    	$table_body_tr .='<td></td>'.($if_create==1?'<td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" style="width:112px;" /></td>':'').'</tr>';
    	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
    }else if($rowFirst == 3){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	$vals2 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[2]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr0 = ''; $nr1 = ''; $nr2 = '';
    	if($comId==1009){
    		if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$ggseci2 = count($vals2);
	    					if($ggseci2>0){
	    						for ($k=0; $k<$ggseci2; $k++) {
	    							$snInt+=1;
	    							$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
	    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
	    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,lipinkaId from demo_product_inventory where productId=$productId and key_ids='$pricekey' limit 1");
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
	    							$nr2 .= '<td><input type="number" class="piliang_sale" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><select name="lipinkaId['.$pricekey.']" lay-verify="required" lay-search><option>选择或搜索礼品卡</option>{lipinka_str}</select><input type="hidden" class="lipinka_str_val" value="'.$price->lipinkaId.'"></td>';
	    						}
	    					}
	    				}
	    			}
	    		}
	    	}
			$table_tr_th = '<tr height="40">
			'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>选择礼品卡</th></tr>';
			$table_body_tr = $nr2;
			$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
	    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td></td></tr>';
    	}else{
	    	if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$ggseci2 = count($vals2);
	    					if($ggseci2>0){
	    						for ($k=0; $k<$ggseci2; $k++) {
	    							$snInt+=1;
	    							$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
	    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
	    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='$pricekey' limit 1");
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
	    							if($product_set->if_weight==1){
	    								$nr2 .= '<td><input type="number" class="piliang_weight" name="weight['.$pricekey.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
	    							}
    								$nr2 .= '<td><input type="number" class="piliang_sale" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><input type="number" class="piliang_market" name="price_market['.$pricekey.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$pricekey.']" value="'.(int)$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$pricekey.']" value="'.(int)$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
	    							$nr2 .= '<td><input type="text" name="code['.$pricekey.']" value="'.$price->code.'" style="width:132px;" /></td>'.($if_create==1?'<td><input type="number" class="piliang_kucun" name="kucun['.$pricekey.']" value="0" style="width:112px;" /></td>':'').'</tr>';
	    							$table_body_tr1.='<tr height="35" '.(empty($price)?'':'id="d_row_{levelId}_'.$price->id.'"').'>'.$ssss.'<td>'.$vals2[$k]->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$pricekey.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$pricekey.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$pricekey.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" min="0" onchange="checkDinghuoNum(this,1);"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$pricekey.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$pricekey.']" type="hidden" value=""/></td></tr>';
	    						}
	    					}
	    				}
	    			}
	    		}
	    	}
			//}
			$table_tr_th = '<tr height="40">
			'.$bt.'<th>商品编码</th>
			'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th><th>市场价（元）</th><th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团价格</th><th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th>'.($if_create==1?'<th>库存</th>':'').'</tr>';
	    	$table_tr_th1 = '<tr height="40">'.$bt.'<th>允许订货</th><th>折扣</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
			$table_body_tr = $nr2;
			$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
	    	if($product_set->if_weight==1){
	    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
	    	}
    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
	    	$table_body_tr .='<td></td>'.($if_create==1?'<td><input type="number" onchange="piliang_set(\'kucun\',this.value);" value="0" style="width:82px;" /></td>':'').'</tr>';
	    	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
	    }
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
	global $db,$request,$if_fenxiao,$if_pintuan;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	$productId = $request['productId'];
	$newIdstr = '';
	$hasKeyIds = array();
	$keys = $db->get_results("select id from demo_product_key where productId=$productId and parentId=0 order by id asc");
	if(!empty($keys)){
		foreach ($keys as $key => $val) {
			$newIdstr .= ',{"index":'.($key+1).',"val":"'.$val->id.'"}';
		}
	}
	if(count($request['gg']) > 0){
		foreach ($request['gg'] as $key => $value) {
			$value = trim($value);
			$pdtKeyId = (int)$request['pdtKeyId'.$key];
			if(!empty($pdtKeyId)){
				$if_key_exist = $db->get_var("select id from demo_product_key where id=$pdtKeyId");
				if(!empty($if_key_exist)){
					$db->query("update demo_product_key set title='".$value."' where id=".$pdtKeyId);
				}else if(!empty($request['ggseci'.$key])){
					$db->query("insert into demo_product_key (id,productId,title,parentId,isnew) values ($pdtKeyId,$productId,'$value',0,1)");
				}
			}else{
				$db->query("insert into demo_product_key (productId,title,parentId,isnew) values (".$productId.",'".$value."',0,1)");
				$pdtKeyId = $db->get_var("select last_insert_id();");
			}
			if(!empty($request['ggseci'.$key])){
				$hasKeyIds[] = $pdtKeyId;
				foreach ($request['ggseci'.$key] as $kg => $vg) {
					$vg = trim($vg);
					if($vg){
						$pdtValId = $db->get_var("select id from demo_product_key where productId=$productId and parentId=$pdtKeyId and kg=$kg limit 1");
						$image = $request['image'.$key][$kg];
						if(empty($pdtValId)){
							$db->query("insert into demo_product_key(productId,title,parentId,originalPic,isnew,kg) values (".$productId.",'".$vg."',".$pdtKeyId.",'$image',1,$kg)");
							$pdtValId = $db->get_var("select last_insert_id();");
						}else{
							$db->query("update demo_product_key set title='".$vg."',originalPic='$image' where id=".$pdtValId);
						}
						$hasKeyIds[] = $pdtValId;
					}
				}
				$newIdstr .= ',{"index":'.$key.',"val":"'.$pdtKeyId.'"}';
			}else{
				if(!empty($if_key_exist)){
					$db->query("delete from demo_product_key where id=$pdtKeyId");
				}
			}
		}
	}
	if(!empty($newIdstr))$newIdstr=substr($newIdstr,1);
	$keyIds = $db->get_var("select group_concat(id) from demo_product_key where productId=$productId");
	if(empty($keyIds))$keyIds='0';
	$array = array();
	$keys = $db->get_results("select id,title from demo_product_key where productId=$productId and parentId=0 and id in($keyIds) order by id asc");
	$table_body_tr = '';
	//订货级别html标签
	$table_body_tr1 = '';
	$rowFirst = !empty($keys) ? count($keys):0;
	$chushu = pow(10,$product_set->price_num);
	$step = 1/$chushu;
	$chushu = pow(10,$product_set->number_num);
	$step1 = 1/$chushu;
	if($rowFirst == 1){
		if($comId==1009){
			$table_tr_th = '<tr height="40"><th>'.$keys[0]->title.'</th><th>商品编码</th><th>零售价（元）</th><th>选择礼品卡</th></tr>';
			$vals = $db->get_results("select * from demo_product_key where productId=$productId and parentId=".$keys[0]->id." and id in($keyIds) order by id asc");
			$gg2 = count($vals);
			if($gg2 > 0){
	        	foreach ($vals as $key => $value) {
	        		$snInt++;
	        		$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
	        		$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,lipinkaId from demo_product_inventory where productId=$productId and key_ids='".$value->id."' limit 1");
	        		$table_body_tr .= '<tr height="35">
	        		<td>'.$value->title.'</td>
	        		<td><input type="text" name="sn['.$value->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
	        		$table_body_tr .= '<td><input type="number" class="piliang_sale" name="price_sale['.$value->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><select name="lipinkaId['.$value->id.']" lay-verify="required" lay-search><option>选择或搜索礼品卡</option>{lipinka_str}</select><input type="hidden" class="lipinka_str_val" value="'.$price->lipinkaId.'"></td></tr>';
	        	}
	        	$table_body_tr .= '<tr><td>批量设置</td><td></td>';
	        	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td></td></tr>';
	        }
		}else{
			$table_tr_th = '<tr height="40">
			<th>'.$keys[0]->title.'</th><th>商品编码</th>
			'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th><th>市场价（元）</th><th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团团价格</th><th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th></tr>';
			$table_tr_th1 = '<tr height="40"><th>'.$keys[0]->title.'</th><th>折扣</th><th>允许订货</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
			$vals = $db->get_results("select * from demo_product_key where productId=$productId and parentId=".$keys[0]->id." and id in($keyIds) order by id asc");
			$gg2 = count($vals);
			if($gg2 > 0){
	        	foreach ($vals as $key => $value) {
	        		$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='".$value->id."' limit 1");
	        		if(!empty($price)){
		        		$table_body_tr .= '<tr height="35">
		        		<td>'.$value->title.'</td>
		        		<td><input type="text" name="sn['.$value->id.']" value="'.$price->sn.'" mustrow style="width:148px;" /></td>';
		        		if($product_set->if_weight==1){
		        			$table_body_tr .= '<td><input type="number" class="piliang_weight" name="weight['.$value->id.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
		        		}
	        			$table_body_tr .= '<td><input type="number" class="piliang_sale" name="price_sale['.$value->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" /></td><td><input type="number" class="piliang_market" name="price_market['.$value->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$value->id.']" value="'.$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$value->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
		        		$table_body_tr .= '<td><input type="text" name="code['.$value->id.']" value="'.$price->code.'" style="width:132px;" /></td></tr>';
						$table_body_tr1 .='<tr height="35" id="d_row_{levelId}_'.$price->id.'"><td>'.$value->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$value->id.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$value->id.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$value->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" min="0" onchange="checkDinghuoNum(this,1);"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$value->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$value->id.']" type="hidden" value=""/></td></tr>';
	        		}else{
	        			$table_body_tr .= '<tr height="35">
		        		<td>'.$value->title.'</td>
		        		<td>已删除，如需要请重新生成规格</td>';
		        		if($product_set->if_weight==1){
		        			$table_body_tr .= '<td></td>';
		        		}
		        		if($product_set->if_lingshou==1){
		        			$table_body_tr .= '<td></td><td></td><td></td>';
		        		}
		        		$table_body_tr .= '<td></td></tr>';
						$table_body_tr1 .='<tr height="35"><td>'.$value->title.'</td><td>已删除，如需要请重新生成规格</td><td></td><td></td><td></td></tr>';
	        		}
	        	}
				$table_body_tr .= '<tr><td>批量设置</td><td></td>';
	        	if($product_set->if_weight==1){
	        		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
	        	}
        		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);checkPrice(\'0\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
	        	$table_body_tr .='<td></td></tr>';
	        	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
	        }
	    }
    }else if($rowFirst == 2){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr2 = '';
    	if($comId==1009){
    		if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,lipinkaId from demo_product_inventory where productId=$productId and key_ids='".$vals0[$m]->id.'-'.$vals1[$j]->id."' limit 1");
	    					$ssss = '';
	    					if($j==0){
	    						$ssss .= '<td rowspan="'.($ggseci1).'">'.$vals0[$m]->title.'</td>';
	    					}
	    					$snInt+=1;
	    					$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
	    					$nr2 .= '<tr height="35">'.$ssss.'
	    					<td>'.$vals1[$j]->title.'</td>
	    					<td><input type="text" name="sn['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.(empty($price->sn)?$sn:$price->sn).'" mustrow style="width:148px;" /></td>';
	    					$nr2.= '<td><input type="number" class="piliang_sale" name="price_sale['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><select name="lipinkaId['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" lay-verify="required" lay-search><option>选择或搜索礼品卡</option>{lipinka_str}</select><input type="hidden" class="lipinka_str_val" value="'.$price->lipinkaId.'"></td></tr>';
    					}
    				}
    			}
    		}
	    	$table_tr_th = '<tr height="40">
	    	'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>选择礼品卡</th></tr>';
	    	$table_body_tr = $nr2;
	    	$table_body_tr .= '<tr><td colspan="2">批量设置</td><td></td>';
	    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td></td></tr>';
    	}else{
	    	if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='".$vals0[$m]->id.'-'.$vals1[$j]->id."' limit 1");
	    					$ssss = '';
	    					if($j==0){
	    						$ssss .= '<td rowspan="'.($ggseci1).'">'.$vals0[$m]->title.'</td>';
	    					}
	    					if(!empty($price)){
	    						$nr2 .= '<tr height="35">'.$ssss.'
	    						<td>'.$vals1[$j]->title.'</td>
	    						<td><input type="text" name="sn['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->sn.'" mustrow style="width:148px;" /></td>';
	    						if($product_set->if_weight==1){
	    							$nr2 .= '<td><input type="number" class="piliang_weight" name="weight['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
	    						}
    							$nr2.= '<td><input type="number" class="piliang_sale" name="price_sale['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" /></td><td><input type="number" class="piliang_market" name="price_market['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
	    						$nr2.= '<td><input type="text" name="code['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" value="'.$price->code.'" style="width:132px;" /></td>
	    						</tr>';
	    						$table_body_tr1.='<tr height="35" id="d_row_{levelId}_'.$price->id.'">'.$ssss.'<td>'.$vals1[$j]->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" min="0" onchange="checkDinghuoNum(this,1);"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$vals0[$m]->id.'-'.$vals1[$j]->id.']" type="hidden" value=""/></td></tr>';
	    					}else{
	    						$nr2 .= '<tr height="35">'.$ssss.'
	    						<td>'.$vals1[$j]->title.'</td>
	    						<td>已删除，如需要请重新生成规格</td>';
	    						if($product_set->if_weight==1){
	    							$nr2 .= '<td></td>';
	    						}
	    						if($product_set->if_lingshou==1){
	    							$nr2.= '<td></td><td></td><td></td>';
	    						}
	    						$nr2.= '<td></td>
	    						</tr>';
	    						$table_body_tr1 .='<tr height="35">'.$ssss.'<td>'.$vals1[$j]->title.'</td><td>已删除，如需要请重新生成规格</td><td></td><td></td><td></td></tr>';
	    					}
	    				}
	    			}
	    		}
	    	}
	    	$table_tr_th = '<tr height="40">
	    	'.$bt.'<th>商品编码</th>
			'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th><th>市场价（元）</th><th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团价格</th><th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th>
	    	</tr>';
	    	$table_tr_th1 = '<tr height="40">'.$bt.'<th>允许订货</th><th>折扣</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
	    	$table_body_tr = $nr2;
			$table_body_tr .= '<tr><td colspan="2">批量设置</td><td></td>';
	    	if($product_set->if_weight==1){
	    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
	    	}
    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);checkPrice(\'0\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
	    	$table_body_tr .='<td></td></tr>';
	    	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
	    }
    }else if($rowFirst == 3){
    	$bt = '';
    	$nr = '';
    	$i = 0;
    	$vals0 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[0]->id." order by id asc");
    	$vals1 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[1]->id." order by id asc");
    	$vals2 = $db->get_results("select * from demo_product_key where productId=$productId and id in($keyIds) and parentId=".$keys[2]->id." order by id asc");
    	for($i=0; $i< $rowFirst; $i++){
    		$bt .='<th>'.$keys[$i]->title.'</th>';
    	}
    	$ggseci0 = count($vals0);
    	$nr0 = ''; $nr1 = ''; $nr2 = '';
    	if($comId==1009){
    		if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$ggseci2 = count($vals2);
	    					if($ggseci2>0){
	    						for ($k=0; $k<$ggseci2; $k++) {
	    							$snInt+=1;
	    							$sn = $product_set->sn_rule.date("Ymd").rand(100,999).$snInt;
	    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
	    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,lipinkaId from demo_product_inventory where productId=$productId and key_ids='$pricekey' limit 1");
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
	    							$nr2 .= '<td><input type="number" class="piliang_sale" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" /></td><td><select name="lipinkaId['.$pricekey.']" lay-verify="required" lay-search><option>选择或搜索礼品卡</option>{lipinka_str}</select><input type="hidden" class="lipinka_str_val" value="'.$price->lipinkaId.'"></td>';
	    						}
	    					}
	    				}
	    			}
	    		}
	    	}
			$table_tr_th = '<tr height="40">
			'.$bt.'<th>商品编码</th><th>零售价（元）</th><th>选择礼品卡</th></tr>';
			$table_body_tr = $nr2;
			$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
	    	$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td></td></tr>';
    	}else{
	    	if($ggseci0>0){
	    		for($m=0; $m<$ggseci0; $m++) {
	    			$ggseci1 = count($vals1);
	    			if($ggseci1>0){
	    				for($j=0; $j<$ggseci1; $j++) {
	    					$ggseci2 = count($vals2);
	    					if($ggseci2>0){
	    						for ($k=0; $k<$ggseci2; $k++) {
	    							$pricekey = $vals0[$m]->id.'-'.$vals1[$j]->id.'-'.$vals2[$k]->id;
	    							$price = $db->get_row("select id,sn,weight,price_sale,price_market,price_cost,code,fanli_tuanzhang,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId and key_ids='$pricekey' limit 1");
	    							$ssss = '';
	    							if($j==0 && $k==0){
	    								$ssss .= '<td rowspan="'.($ggseci1*$ggseci2).'">'.$vals0[$m]->title.'</td>
	    								<td rowspan="'.($ggseci2).'">
	    								'.$vals1[$j]->title.'</td>';
	    							}elseif($k==0){
	    								$ssss .= '<td rowspan="'.($ggseci2).'">
	    								'.$vals1[$j]->title.'</td>';
	    							}
	    							if(!empty($price)){
	    								$nr2 .= '<tr height="35">'.$ssss.'
	    								<td>
	    								'.$vals2[$k]->title.'
	    								</td>
	    								<td><input type="text" name="sn['.$pricekey.']" value="'.$price->sn.'" mustrow style="width:148px;" /></td>';
	    								if($product_set->if_weight==1){
	    									$nr2 .= '<td><input type="number" class="piliang_weight" name="weight['.$pricekey.']" value="'.$price->weight.'" mustrow step="0.01" style="width:82px;" /></td>';
	    								}
    									$nr2 .= '<td><input type="number" class="piliang_sale" name="price_sale['.$pricekey.']" value="'.$price->price_sale.'" mustrow step="'.$step.'" style="width:82px;" onchange="checkPrice(\''.$price->price_sale.'\',this.value);" /></td><td><input type="number" class="piliang_market" name="price_market['.$pricekey.']" value="'.$price->price_market.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" class="piliang_tuan" name="price_tuan['.$pricekey.']" value="'.$price->price_tuan.'" mustrow step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" class="piliang_tuanzhang" name="fanli_tuanzhang['.$pricekey.']" value="'.$price->fanli_tuanzhang.'" mustrow step="'.$step.'" style="width:82px;" /></td>';
	    								$nr2 .= '<td><input type="text" name="code['.$pricekey.']" value="'.$price->code.'" style="width:132px;" /></td>
	    								</tr>';
	    								$table_body_tr1.='<tr height="35" id="d_row_{levelId}_'.$price->id.'">'.$ssss.'<td>'.$vals2[$k]->title.'</td><td>{zhekou}%</td><td><input name="d_ifsale_{levelId}['.$pricekey.']" class="checkbox" type="checkbox" lay-skin="primary" checked="true" title="" lay-filter="ifsale"/></td><td><input name="d_price_sale{levelId}['.$pricekey.']" class="piliang_d{levelId}_sale dinghuo_money" data-zhekou="{zhekou}" type="number" step="'.$step.'" min="0" mustrow/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input name="d_dinghuo_min{levelId}['.$pricekey.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_min" min="0" onchange="checkDinghuoNum(this,1);"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input name="d_dinghuo_max{levelId}['.$pricekey.']" type="number" step="'.$step1.'" value="0" class="piliang_d{levelId}_max" min="0" onchange="checkDinghuoNum(this,2);"/><input name="d_dinghuo_id{levelId}['.$pricekey.']" type="hidden" value=""/></td></tr>';
	    							}else{
	    								$nr2 .= '<tr height="35">'.$ssss.'
	    								<td>'.$vals2[$k]->title.'</td>
	    								<td>已删除，如需要请重新生成规格</td>';
	    								if($product_set->if_weight==1){
	    									$nr2 .= '<td></td>';
	    								}
	    								if($product_set->if_lingshou==1){
	    									$nr2 .= '<td></td><td></td><td></td>';
	    								}
	    								$nr2 .= '<td></td>
	    								</tr>';
	    								$table_body_tr1.='<tr height="35">'.$ssss.'<td>'.$vals2[$k]->title.'</td><td>已删除，如需要请重新生成规格</td><td></td><td></td><td></td></tr>';
	    							}
	    						}
	    					}
	    				}
			        }
			    }
			}
			$table_tr_th = '<tr height="40">
			'.$bt.'<th>商品编码</th>
			'.($product_set->if_weight==1?'<th>重量（'.$product_set->weight.'）</th>':'').'<th>零售价（元）</th><th>市场价（元）</th><th width="167px" '.($if_pintuan==0?'style="display:none;"':'').'>拼团价格</th><th '.($if_fenxiao==0?'style="display:none;"':'').'>分销提成(总)</th><th width="342px">条形码</th>
	    	</tr>';
	    	$table_tr_th1 = '<tr height="40">'.$bt.'<th>允许订货</th><th>折扣</th><th>订货价</th><th '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'>起订量</th><th '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'>限订量</th></tr>';
			$table_body_tr = $nr2;
			$table_body_tr .= '<tr><td colspan="3">批量设置</td><td></td>';
	    	if($product_set->if_weight==1){
	    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'weight\',this.value);" value="0" step="0.01" style="width:82px;" /></td>';
	    	}
    		$table_body_tr .= '<td><input type="number" onchange="piliang_set(\'sale\',this.value);checkPrice(\'0\',this.value);" value="0"  step="'.$step.'" style="width:82px;" /></td><td><input type="number" onchange="piliang_set(\'market\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_pintuan==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuan\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td><td '.($if_fenxiao==0?'style="display:none;"':'').'><input type="number" onchange="piliang_set(\'tuanzhang\',this.value);" value="0" step="'.$step.'" style="width:82px;" /></td>';
	    	$table_body_tr .='<td></td></tr>';
	    	$table_body_tr1 .= '<tr><td>批量设置</td><td></td><td></td><td></td><td></td><td><input type="number" onchange="piliang_set(\'d{levelId}_sale\',this.value);" value="0" step="'.$step.'"/></td><td '.(empty($product_set->if_dinghuo_min)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_min\',this.value);" value="0" step="'.$step1.'"/></td><td '.(empty($product_set->if_dinghuo_max)?'style="display:none"':'').'><input type="number" onchange="piliang_set(\'d{levelId}_max\',this.value);" value="0" step="'.$step1.'"/></td></tr>';
	    }
	}
    $table_body_tr= !empty($table_body_tr)?$table_body_tr:'';
    $table_body_tr= !empty($table_body_tr)?$table_body_tr:'';
    $table_tr_th = !empty($table_tr_th)?$table_tr_th:'';
    $table_tr_th1= !empty($table_tr_th1)?$table_tr_th1:'';
	$table = '<table width="100%">
	<input type="hidden" name="keyIds" id="keyIds" value="'.$keyIds.'">
	'.$table_tr_th.$table_body_tr.'</table>';
	$table = preg_replace('/((\s)*(\n)+(\s)*)/','',$table);
	$table = str_replace('"','\"',$table);
	$table_level = '<table width="100%">'.$table_tr_th1.$table_body_tr.'</table>';
	$table_level = preg_replace('/((\s)*(\n)+(\s)*)/','',$table_level);
	$table_level = str_replace('"','\"',$table_level);
	echo '{"code":1,"productId":'.$productId.',"newIdstr":['.$newIdstr.'],"table":"'.$table.'","table_level":"'.$table_level.'"}';
	exit;
}
function editProduct(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(!empty($request['tijiao'])&&$request['tijiao']==1){
		$id = (int)$request['productId'];
		$title = str_replace('\\','/', $request['title']);
		$title = str_replace('"','“', $title);
		$title = str_replace("'",'', $title);
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
		$zstatus = 1;
		if($_SESSION['if_tongbu']==1 && $_SESSION['if_tongbu_pdt']==1 && $request['pdt_status']==0 && $comId!=1009 && $comId!=1022){
			$zstatus = 0;
		}
		$originalPic = $request['originalPic'];
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$remark = $request['remark'];
		$if_dinghuo = empty($request['if_dinghuo'])?0:1;
		$if_lingshou = empty($request['if_lingshou'])?0:1;
		$yunfei_moban_ding = (int)$request['yunfei_moban_ding'];
		$yunfei_moban = (int)$request['yunfei_moban'];
		$addrows = '';
		$shichangjia = (int)$request['shichangjia'];
		$share_img = $request['share_img'];
		$subtitle = $request['title'];
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
		
		$skuId = $request['skuId'];
		$skuDay = $request['skuDay'];
		
		$db->query("update demo_product set title='$title',channelId=$channelId,brandId=$brandId,status=$status,ordering=$ordering,addrows='$addrows',unit_type=$unit_type,untis='$units',dinghuo_units='$dinghuo_units',keywords='$keywords',tags='$tags',originalPic='$originalPic',cont1='$cont1',cont2='$cont2',cont3='$cont3',remark='$remark',if_dinghuo=$if_dinghuo,if_lingshou=$if_lingshou,yunfei_moban=$yunfei_moban,yunfei_moban_ding=$yunfei_moban_ding,share_img='$share_img',subtitle='$subtitle',skuId='$skuId',skuDay='$skuDay' where id=$id and comId=$comId");
		$ppdt=$db->get_row("select channelId from demo_product_inventory where productId=$id limit 1");
		if($ppdt->channelId!=$channelId){
		    $db->query("update demo_product_inventory set channelId=$channelId where productId=$id");
		}
		$entitle = getFirstCharter($title);
		$db->query("update demo_kucun set entitle='$entitle' where comId=$comId and productId=$id");
		$db->query("delete from demo_product_keyword where comId=$comId and productId=$id");
		if(!empty($keywords)){
			$keywordArr = explode(',',$keywords);
			$keywordsql = 'insert into demo_product_keyword(comId,keyword,productId) values';
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
		}
		$keyIds = $request['keyIds'];
		$levels = $db->get_results("select id from demo_kehu_level where comId=$comId order by ordering desc,id asc");
		$cangkus = $db->get_results("select id from demo_kucun_store where comId=$comId and status=1");

		if(!empty($keyIds)){
			$productId = $id;
			$db->query("delete from demo_product_key where productId=$productId and id not in($keyIds)");
			$db->query("update demo_product_key set isnew=0 where productId=$productId");
			//file_put_contents('request.txt',json_encode($request['sn'],JSON_UNESCAPED_UNICODE));
			if(!empty($request['sn'])){
				foreach ($request['sn'] as $key=>$sn) {
					$price_sale = $request['price_sale'][$key];
					$price_cost = (int)$request['price_cost'][$key];
				// 	if($price_cost>$price_sale){
				// 		echo '<script>alert("操作失败！成本(供货)价不能大于售价");history.go(-1);</script>';
				// 		exit;
				// 	}
					$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and sn='$sn' and productId!=$productId limit 1");
					if(!empty($ifhas)){
						echo '<script>alert("操作失败！编码“'.$sn.'”已被其他产品使用，请确保编码的唯一性");history.go(-1);</script>';
						exit;
					}
				}
			}

			if(!empty($request['sn'])){
				
				$delInventorys = '0';
				$snInt = $db->get_var("select snInt from demo_product_inventory where comId=$comId order by id desc limit 1");
				foreach ($request['sn'] as $key=>$sn){
					$insertSql = "insert into demo_product_inventory(comId,productId,channelId,title,key_ids,key_vals,sn,weight,price_sale,price_market,price_cost,code,status,image,if_lingshou,ordering,snInt,shichangjia,lipinkaId,lipinkaType,fanli_tuanzhang,sale_tuan,tuan_num,price_tuan,price_shequ_tuan) values";
					$insertSql1 = '';
					$key_ids = $key;
					$valIds = str_replace('-',',',$key_ids);

					$keys = $db->get_results("select title,originalPic from demo_product_key where id in($valIds)");
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
						$pics = explode('|',$originalPic);
						$image = $pics[0];
					}
					$weight = floatval($request['weight'][$key]);

					$price_sale = $request['price_sale'][$key];
					$sale_tuan = empty($request['sale_tuan'])?0:1;
					$tuan_num = (int)$request['tuan_num'];
					$price_tuan = $request['price_tuan'][$key];
					$price_shequ_tuan = (int)$request['price_shequ_tuan'][$key];
					$price_market = $request['price_market'][$key];
					$price_cost = (int)$request['price_cost'][$key];
					$fanli_tuanzhang = empty($request['fanli_tuanzhang'][$key])?0:$request['fanli_tuanzhang'][$key];
					$code = empty($request['code'][$key])?'':$request['code'][$key];
					$lipinkaId = (int)$request['lipinkaId'][$key];
					$lipinkaType = 0;
					if($lipinkaId>0){$lipinkaType = $db->get_var("select type from lipinka_jilu where id=$lipinkaId");}
					$ifhas = $db->get_var("select id from demo_product_inventory where productId=$productId and key_ids='$key_ids' limit 1");
					if(empty($ifhas)){
						$snInt+=1;
						$insertSql1="($comId,$productId,$channelId,'$title','$key','$key_vals','$sn',$weight,$price_sale,$price_market,$price_cost,'$code',$status,'$image',1,$ordering,$snInt,$shichangjia,$lipinkaId,$lipinkaType,$fanli_tuanzhang,$sale_tuan,$tuan_num,$price_tuan,$price_shequ_tuan)";
						$db->query($insertSql.$insertSql1);
						$inventoryId = $db->get_var("select last_insert_id()");
						$entitle = getFirstCharter($title);
						foreach ($cangkus as $c){
							$db->query("insert into demo_kucun(comId,inventoryId,productId,storeId,entitle) value($comId,$inventoryId,$productId,".$c->id.",'$entitle')");
						}
					}else{
						$db->query("update demo_product_inventory set title='$title',channelId=$channelId,key_vals='$key_vals',sn='$sn',weight=$weight,price_sale=$price_sale,price_market=$price_market,price_cost=$price_cost,code='$code',status=$status,image='$image',if_lingshou=1,ordering=$ordering,updateTime='".date("Y-m-d H:i:s")."',shichangjia=$shichangjia,lipinkaId=$lipinkaId,lipinkaType=$lipinkaType,fanli_tuanzhang=$fanli_tuanzhang,sale_tuan=$sale_tuan,tuan_num=$tuan_num,price_tuan=$price_tuan,price_shequ_tuan=$price_shequ_tuan where id=$ifhas");
				// 		echo "update demo_product_inventory set title='$title',channelId=$channelId,key_vals='$key_vals',sn='$sn',weight=$weight,price_sale=$price_sale,price_market=$price_market,price_cost=$price_cost,code='$code',status=$status,image='$image',if_lingshou=1,ordering=$ordering,updateTime='".date("Y-m-d H:i:s")."',shichangjia=$shichangjia,lipinkaId=$lipinkaId,lipinkaType=$lipinkaType,fanli_tuanzhang=$fanli_tuanzhang,sale_tuan=$sale_tuan,tuan_num=$tuan_num,price_tuan=$price_tuan,price_shequ_tuan=$price_shequ_tuan where id=$ifhas";
						if($zstatus==0){
							$db->query("update demo_product_inventory set zstatus=0 where id=$ifhas");
						}
						$inventoryId = $ifhas;
					}
					$delInventorys.=','.$inventoryId;
					//级别订货价
					foreach ($levels as $l){
						$product_dinghuo = array();
						$product_dinghuo['id'] = (int)$request['d_dinghuo_id'.$l->id][$key];
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
				// 		insert_update('demo_product_dinghuo',$product_dinghuo,'id');
					}
					//客户订货价
					$delKehuDinghuo = '0';
					if(!empty($request['moreKehuId'])&&!empty($request['dinghuo_bykehu'])){
						foreach ($request['moreKehuId'] as $kehuId){
							$product_dinghuo = array();
							$product_dinghuo['id'] = (int)$request['k_dinghuo_id'.$kehuId][$key];
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
				// 			insert_update('demo_product_dinghuo',$product_dinghuo,'id');
							if(!empty($product_dinghuo['id'])){
								$delKehuDinghuo.=','.$product_dinghuo['id'];
							}else{
								$id = $db->get_var("select last_insert_id();");
								$delKehuDinghuo.=','.$id;
							}
						}
					}
					$db->query("delete from demo_product_dinghuo where inventoryId=$inventoryId and type=1 and comId=$comId and id not in($delKehuDinghuo)");
				}
				$delIds = $db->get_results("select id,productId,key_ids,key_vals,sn,code,image from demo_product_inventory where productId=$productId and id not in($delInventorys) and comId=$comId");
				if(!empty($delIds)){
					foreach ($delIds as $inventory) {
						$id =$inventory->id;
						$db->query("delete from demo_product_inventory where id=$id and comId=$comId");
						$db->query("delete from demo_kucun where inventoryId=$id and comId=$comId");
						$db->query("insert into demo_product_delete(comId,inventoryId,productId,type,key_ids,key_vals,sn,code,image,userId,dtTime) value($comId,$id,".$inventory->productId.",1,'".$inventory->key_ids."','".$inventory->key_vals."','".$inventory->sn."','".$inventory->code."','".$inventory->image."',".$_SESSION[TB_PREFIX.'admin_userID'].",'".date("Y-m-d H:i:s")."')");
					}
				}
			}
			if($_SESSION['tongbu_zong']==1){
				tongbu_zong($productId);
			}
		}
		$url = empty($request['url'])?'?m=system&s=product':$request['url'];
// 		die;
		redirect(urldecode($url));
		exit;
	}else{
		$productId = !empty($request['id'])?$request['id']:0;
		if(empty($productId) && !empty($request['inventoryId']))$productId = $db->get_var("select productId from demo_product_inventory where id=".$request['inventoryId']);
		$db->query("delete from demo_product_key where productId=".$productId." and isnew=1");
	}
}
function edit(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(!empty($request['tijiao'])&&$request['tijiao']==1){
		$id = (int)$request['productId'];
		$title = str_replace('\\','/', $request['title']);
		$title = str_replace('"','“', $title);
		$title = str_replace("'",'', $title);
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
		$zstatus = 1;
		if($_SESSION['if_tongbu']==1 && $_SESSION['if_tongbu_pdt']==1 && $request['pdt_status']==0 && $comId!=1009 && $comId!=1022){
			$zstatus = 0;
		}
		$originalPic = $request['originalPic'];
		$shichangjia = !empty($request['shichangjia'])?(int)$request['shichangjia']:0;
		$if_dinghuo = empty($request['if_dinghuo'])?0:1;
		$if_lingshou = empty($request['if_lingshou'])?0:1;
		$yunfei_moban_ding = (int)$request['yunfei_moban_ding'];
		$yunfei_moban = (int)$request['yunfei_moban'];
		$remark = $request['remark'];
		$share_img = $request['share_img'];
		$subtitle = $request['subtitle'];
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
		
		$skuId = $request['skuId'];
		$skuDay = $request['skuDay'];
		
		
		$update_sql = "update demo_product set title='$title',channelId=$channelId,brandId=$brandId,status=$status,ordering=$ordering,addrows='$addrows',unit_type=$unit_type,untis='$units',dinghuo_units='$dinghuo_units',keywords='$keywords',tags='$tags',if_dinghuo=$if_dinghuo,if_lingshou=$if_lingshou,share_img='$share_img',subtitle='$subtitle',skuId='$skuId',skuDay='$skuDay'";
		$if_key_ids = $db->get_var("select key_ids from demo_product_inventory where id=".$request['id']);
		if(empty($if_key_ids)){
			$update_sql.=",originalPic='$originalPic'";
		}
		$update_sql.=",remark='$remark',yunfei_moban=$yunfei_moban,yunfei_moban_ding=$yunfei_moban_ding where id=$id and comId=$comId";
		$db->query($update_sql);
		$ppdt=$db->get_row("select channelId from demo_product_inventory where productId=$id limit 1");
		if($ppdt->channelId!=$channelId){
		    $db->query("update demo_product_inventory set channelId=$channelId where productId=$id");
		}
		$entitle = getFirstCharter($title);
		$db->query("update demo_kucun set entitle='$entitle' where comId=$comId and productId=$id");
		$db->query("delete from demo_product_keyword where comId=$comId and productId=$id");
		$levels = $db->get_results("select id from demo_kehu_level where comId=$comId order by ordering desc,id asc");
		if(!empty($keywords)){
			$keywordArr = explode(',',$keywords);
			$keywordsql = 'insert into demo_product_keyword(comId,keyword,productId) values';
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
		}
		$productId = $id;
		$inventoryId = (int)$request['id'];
		$sn = $request['sn0'];
		$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and sn='$sn' and id!=$inventoryId limit 1");
		if(!empty($ifhas)){
			echo '<script>alert("修改失败！该编码已被其他产品使用，请确保编码的唯一性");history.go(-1);</script>';
			exit;
		}
		$weight = empty($request['weight0'])?'0':$request['weight0'];
		$price_sale = empty($request['price_sale0'])?'0':$request['price_sale0'];
		$sale_tuan = empty($request['sale_tuan'])?0:1;
		$tuan_num = (int)$request['tuan_num'];
		$price_tuan = !empty($request['price_tuan0'])?$request['price_tuan0']:0;
		$price_shequ_tuan = !empty($request['price_shequ_tuan0'])?(int)$request['price_shequ_tuan0']:0;
		$price_market = empty($request['price_market0'])?'0':$request['price_market0'];
		$price_cost = empty($request['price_cost0'])?'0':(int)$request['price_cost0'];
		$fanli_tuanzhang = empty($request['fanli_tuanzhang0'])?'0':$request['fanli_tuanzhang0'];
// 		if($price_cost>$price_sale){
// 			echo '<script>alert("操作失败！成本(供货)价不能大于售价");history.go(-1);";</script>';
// 			exit;
// 		}
		$lipinkaId = (int)$request['lipinkaId0'];
		$lipinkaType = 0;
		if($lipinkaId>0){$lipinkaType = $db->get_var("select type from lipinka_jilu where id=$lipinkaId");}
		$code = $request['code0'];
		$cont1 = $request['cont1'];
		$cont2 = $request['cont2'];
		$cont3 = $request['cont3'];
		$if_lingshou = 1;
		$image = '';
		if(!empty($originalPic)){
			$pics = explode('|',$originalPic);
			$image = $pics[0];
		}
		$update_sql = "update demo_product_inventory set title='$title',channelId=$channelId,sn='$sn',weight=$weight,price_sale=$price_sale,price_market=$price_market,price_cost=$price_cost,code='$code',status=$status,cont1='$cont1',cont2='$cont2',cont3='$cont3',updateTime='".date("Y-m-d H:i:s")."',image='$image',ordering=$ordering,shichangjia=$shichangjia,lipinkaId=$lipinkaId,lipinkaType=$lipinkaType,fanli_tuanzhang=$fanli_tuanzhang,sale_tuan=$sale_tuan,tuan_num=$tuan_num,price_tuan=$price_tuan,price_shequ_tuan=$price_shequ_tuan";
		$update_sql1 = "update demo_product_inventory set title='$title',channelId=$channelId,ordering=$ordering,sale_tuan=$sale_tuan,tuan_num=$tuan_num";
		if(!empty($if_key_ids) && !empty($originalPic)){
			$update_sql.=",originalPic='$originalPic'";
		}
		if($zstatus==0){
			$update_sql.=",zstatus=0";
		}
		$update_sql .= " where id=$inventoryId";
		$update_sql1.= " where productId=$productId";
		$db->query($update_sql);
		$db->query($update_sql1);
		if(!empty($request['d_price_sale0'])){
			foreach ($request['d_price_sale0'] as $key => $val){
				$product_dinghuo = array();
				$ifhas = (int)$db->get_var("select id from demo_product_dinghuo where inventoryId=$inventoryId and levelId=$key limit 1");
				$product_dinghuo['id'] = $ifhas;
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
				// insert_update('demo_product_dinghuo',$product_dinghuo,'id');
			}
		}
		if(!empty($request['k_price_sale0'])&&!empty($request['dinghuo_bykehu'])){
			$delIds = '0';
			foreach ($request['k_price_sale0'] as $key => $val){
				$product_dinghuo = array();
				$product_dinghuo['id'] = (int)$request['dinghuoId'][$key];
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
				// insert_update('demo_product_dinghuo',$product_dinghuo,'id');
				if(!empty($product_dinghuo['id'])){
					$delIds.=','.$product_dinghuo['id'];
				}else{
					$id = $db->get_var("select last_insert_id();");
					$delIds.=','.$id;
				}
			}
			$db->query("delete from demo_product_dinghuo where inventoryId=$inventoryId and type=1 and comId=$comId and id not in($delIds)");
		}else{
			$db->query("delete from demo_product_dinghuo where inventoryId=$inventoryId and type=1");
		}
		$url = empty($request['url'])?'?m=system&s=product':$request['url'];
		if($_SESSION['tongbu_zong']==1){
			tongbu_zong($productId);
		}
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
	$sql="select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId and id not in($hasIds)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			$kucun = $db->get_var("select kucun-yugouNum from demo_kucun where inventoryId=".$pdt->id." and storeId=$storeId limit 1");
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
	$dinghuos = $db->get_results("select levelId,kehuId,ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where comId=$comId and type=$type and inventoryId=$inventoryId order by id asc");
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
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function tongbu_zong($productId){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	require_once(ABSPATH.'/inc/class.crmdb.php');
	require_once(ABSPATH.'/config/dt-service.php');
	class Zongdb extends dtdb1{
		function Zongdb1() {
			$this->dtdb1(SERVICE_USER, SERVICE_PASSWORD, SERVICE_DBNAME, SERVICE_HOSTNAME);
		}
	}
	$db_zong = new Zongdb();
	$product = $db->get_row("select * from demo_product where id=$productId",'ARRAY_A');
	$product_id = (int)$db_zong->get_var("select id from demo_product where comId=$comId and yuan_id=$productId limit 1");
	$product['yuan_id'] = $product['id'];
	$product['id'] = $product_id;
	$product['status'] = 0;
	$product['channelId'] = 0;
	$product['brandId'] = 0;
	$product['ordering'] = 0;
	$product_id = $db_zong->insert_update('demo_product',$product,'id');
	$db_zong->query("delete from demo_product_key where productId=$product_id");
	//$db_zong->query("delete from demo_product_inventory where productId=$product_id");
	$keys = $db->get_results("select * from demo_product_key where productId=$productId",'ARRAY_A');
	if(!empty($keys)){
		foreach ($keys as $key) {
			$key['yuan_id'] = $key['id'];
			$key['id'] = 0;
			$key['productId'] = $product_id;
			$db_zong->insert_update('demo_product_key',$key,'id');
		}
	}
	$inventorys = $db->get_results("select * from demo_product_inventory where productId=$productId",'ARRAY_A');
	$ids = '';
	if(!empty($inventorys)){
		foreach ($inventorys as $inventory) {
			$ids.=','.$inventory['id'];
			$inventoryId = (int)$db_zong->get_var("select id from demo_product_inventory where comId=$comId and yuan_id=".$inventory['id']." limit 1");
			$inventory['yuan_id'] = $inventory['id'];
			$inventory['id'] = $inventoryId;
			$inventory['productId'] = $product_id;
			$inventory['status'] = 0;
			$inventory['channelId'] = 0;
			$inventory['ordering'] = 0;
			$db_zong->insert_update('demo_product_inventory',$inventory,'id');
		}
		$ids = substr($ids, 1);
		$db_zong->query("delete from demo_product_inventory where comId=$comId and productId=$product_id and yuan_id not in($ids)");
	}
}
//级别价格相关
function getLevelPrices_bak(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$productId = $request['productId'];
	$inventorys = $db->get_results("select id,title,key_vals,price_sale,price_market,price_cost,price_tuan,price_shequ_tuan from demo_product_inventory where productId=$productId order by id asc");
	$levels = $db->get_results("select id,title from user_level where comId=$comId order by jifen desc,id asc");
	$table = '<table width="100%"><tr height="40"><th>产品名称</th><th>规格</th>';
	$if_fixed_zhekou = $db->get_var("select if_fixed_zhekou from user_shezhi where comId=$comId");
	if($if_fixed_zhekou==0){
		$table.='<th>零售价（元）</th><th>普通团价格</th><th>社区团价格</th><th>市场价</th><th>成本(供货)价</th>';
	}else{
		foreach ($levels as $level) {
			$table.='<th>'.$level->title.'</th>';
		}
	}	
	$table.='</tr>';
	if(!empty($inventorys)){
		if($if_fixed_zhekou==0){
			foreach ($inventorys as $inventory) {
				$table.='<tr height="35"><td>'.$inventory->title.'</td><td>'.$inventory->key_vals.'</td>';
				$table.='<td><input type="text" class="piliang_sale" name="price_sale['.$inventory->id.']" value="'.getXiaoshu($inventory->price_sale,2).'" lay-verify="required"></td>';
				$table.='<td><input type="text" class="piliang_tuan" name="price_tuan['.$inventory->id.']" value="'.getXiaoshu($inventory->price_tuan,2).'" lay-verify="required"></td>';
				$table.='<td><input type="text" class="piliang_shequ_tuan" name="price_shequ_tuan['.$inventory->id.']" value="'.getXiaoshu($inventory->price_shequ_tuan,2).'" lay-verify="required"></td>';
				$table.='<td><input type="text" class="piliang_market" name="price_market['.$inventory->id.']" value="'.getXiaoshu($inventory->price_market,2).'" lay-verify="required"></td>';
				$table.='<td><input type="text" class="piliang_cost" name="price_cost['.$inventory->id.']" value="'.getXiaoshu($inventory->price_cost,2).'" lay-verify="required"></td>';
				$table.='<input type="hidden" name="inventoryIds[]" value="'.$inventory->id.'"></tr>';
			}
			$table.="<tr><td colspan=2>批量设置</td>";
			$table.='<td><input type="text" onchange="piliang_set(\'sale\',this.value);"></td>';
			$table.='<td><input type="text" onchange="piliang_set(\'tuan\',this.value);"></td>';
			$table.='<td><input type="text" onchange="piliang_set(\'shequ_tuan\',this.value);"></td>';
			$table.='<td><input type="text" onchange="piliang_set(\'market\',this.value);"></td>';
			$table.='<td><input type="text" onchange="piliang_set(\'cost\',this.value);"></td>';
			$table.='</tr>';
		}else{
			foreach ($inventorys as $inventory) {
				$table.='<tr height="35"><td>'.$inventory->title.'</td><td>'.$inventory->key_vals.'</td>';
				foreach ($levels as $level) {
					$price = $db->get_var("select price from demo_product_price where inventoryId=$inventory->id and levelId=$level->id");
					if(empty($price))$price = $inventory->price_sale;
					$table.='<td><input type="text" class="piliang_'.$level->id.'" name="price['.$inventory->id.'_'.$level->id.']" value="'.getXiaoshu($price,2).'" lay-verify="required"></td>';
				}
				$table.='</tr>';
			}
			$table.="<tr><td colspan=2>批量设置</td>";
			foreach ($levels as $level) {
				$table.='<td><input type="text" onchange="piliang_set(\''.$level->id.'\',this.value);"></td>';
			}
			$table.='</tr>';
		}
	}
	$table.='</table>';
	$table = preg_replace('/((\s)*(\n)+(\s)*)/','',$table);
	$table = str_replace('"','\"',$table);
	echo '{"code":1,"table":"'.$table.'"}';
	exit;
}
function edit_level_price_bak(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(!empty($request['tijiao'])&&$request['tijiao']==1){
		$if_fixed_zhekou = $db->get_var("select if_fixed_zhekou from user_shezhi where comId=$comId");
		if($if_fixed_zhekou==0){
			if(!empty($request['inventoryIds'])){
				foreach ($request['inventoryIds'] as $id) {
					$inventory = array();
					$inventory['id'] = $id;
					$inventory['price_sale'] = $request['price_sale'][$id];
					$inventory['price_tuan'] = $request['price_tuan'][$id];
					$inventory['price_shequ_tuan'] = (int)$request['price_shequ_tuan'][$id];
					$inventory['price_market'] = $request['price_market'][$id];
					$inventory['price_cost'] = $request['price_cost'][$id];
					$db->insert_update('demo_product_inventory',$inventory,'id');
				}
			}
		}else{
			if(!empty($request['price'])){
				foreach ($request['price'] as $key=>$pri) {
					$arr = explode('_',$key);
					$inventoryId = $arr[0];
					$levelId = $arr[1];
					$ifhas = $db->get_var("select inventoryId from demo_product_price where inventoryId=$inventoryId and levelId=$levelId limit 1");
					if(empty($ifhas)){
						$db->query("insert into demo_product_price(inventoryId,levelId,price) value($inventoryId,$levelId,'$pri')");
					}else{
						$db->query("update demo_product_price set price='$pri' where inventoryId=$inventoryId and levelId=$levelId");
					}
				}
			}
		}
		redirect('?m=system&s=product');
	}
}

//库存

function edit_level_price(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(!empty($request['tijiao'])&&$request['tijiao']==1){
		if(!empty($request['inventoryIds'])){
		    $dtTime = date('Y-m-d H:i:s');
			foreach ($request['inventoryIds'] as $id) {
				$inventory = $db->get_row("select * from demo_product_inventory where id = $id");
				$pdtInfoArry['sn'] = $inventory->sn;
				$pdtInfoArry['title'] = $inventory->title;
				$pdtInfoArry['key_vals'] = $inventory->key_vals;
		
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				
			    $num = $request['price_sale'][$id];
				//新增修改库存逻辑  num  大于 0  增加库存， 小于0 减少库存
            	$kucun = $db->get_row("select * from demo_kucun where inventoryId=$id and storeId=5");
        
            	if($num > 0){
            	    $db->query("update demo_kucun set kucun=kucun+$num where inventoryId=$id and storeId=5 limit 1");
            	    $type = 1;
            	    $type_info ='后台入库';
            	    $kucun_num = $kucun->kucun + $num;
            	}else{
            	     $type = 2;
            	     $type_info ='后台出库';
            	     $num = abs($num);
            	    if($kucun->kucun < $num){
            	        $db->query("update demo_kucun set kucun=0 where inventoryId=$id and storeId=5 limit 1");
            	        $num = $kucun->kucun;
            	        $kucun_num = 0;
            	    }else{
            	        $db->query("update demo_kucun set kucun=kucun-$num where inventoryId=$id and storeId=5 limit 1");
            	        $kucun_num = $kucun->kucun - $num;
            	    }
            	    
            	}
				//写入记录
				$rukuSql = "insert into demo_kucun_jiludetail8(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben,zhesun)  value(888,0,$id,$inventory->productId,'$pdtInfo',5,'默认库','$num',1,'$kucun_num','',$type,'$type_info','$dtTime','',0,0,0)";
				//echo $rukuSql;die;
				$db->query($rukuSql);
			}
		}

		redirect('?m=system&s=product');
	}
}

function getLevelPrices(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$productId = $request['productId'];
	$inventorys = $db->get_results("select p.id,p.title,p.key_vals,k.kucun from demo_kucun as k left join demo_product_inventory as p on k.inventoryId = p.id where k.productId=$productId order by id asc");

	$table = '<table width="100%"><tr height="40"><th>产品名称</th><th>规格</th>';

	$table.='<th>库存</th>';
	
	$table.='</tr>';
	if(!empty($inventorys)){
	
		foreach ($inventorys as $inventory) {
			$table.='<tr height="35"><td>'.$inventory->title.'</td><td>'.$inventory->key_vals.'</td>';
			$table.='<td><input type="text" class="piliang_sale" name="price_sale['.$inventory->id.']" value="'.getXiaoshu($inventory->kucun,0).'" lay-verify="required"></td>';
			$table.='<input type="hidden" name="inventoryIds[]" value="'.$inventory->id.'"></tr>';
		}
		$table.="<tr><td colspan=2>批量设置</td>";
		$table.='<td><input type="text" onchange="piliang_set(\'sale\',this.value);"></td>';
		$table.='</tr>';

	}
	$table.='</table>';
	$table = preg_replace('/((\s)*(\n)+(\s)*)/','',$table);
	$table = str_replace('"','\"',$table);
	echo '{"code":1,"table":"'.$table.'"}';
	exit;
}


function getBaozhiqi(){
	global $db,$request;
	$inventory = $db->get_row("select baozhiqi,baozhiqi_days from demo_product_inventory where id=".(int)$request['id']);
	$baozhiqi = empty($inventory->baozhiqi)?'':date("Y-m-d",$inventory->baozhiqi);
	$baozhiqi_days = empty($inventory->baozhiqi_days)?'':$inventory->baozhiqi_days;
	echo '{"code":1,"baozhiqi":"'.$baozhiqi.'","baozhiqi_days":"'.$baozhiqi_days.'"}';
	exit;
}
function setBaozhiqi(){
	global $db,$request;
	$id = (int)$request['id'];
	$baozhiqi = empty($request['baozhiqi'])?0:strtotime($request['baozhiqi']);
	$baozhiqi_days = (int)$request['baozhiqi_days'];
	$fanwei = (int)$request['fanwei'];
	$sql = "update demo_product_inventory set baozhiqi=$baozhiqi,baozhiqi_days=$baozhiqi_days";
	if($fanwei==2){
		$productId = $db->get_var("select productId from demo_product_inventory where id=$id");
		$sql.=" where productId=$productId";
	}else{
		$sql.=" where id=$id";
	}
	$db->query($sql);
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
//积分列表
function jifen(){}
function create_jifen(){
	global $db,$request;
	if(!empty($request['jifen'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$jifen = array();
		$jifen['id'] = (int)$request['id'];
		$jifen['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$jifen['jifen'] = (int)$request['jifen'];
		$jifen['inventoryId'] = $request['inventoryId'];
		$jifen['status'] = (int)$request['status'];
		if($jifen['id']==0){
			$jifen['dtTime'] = date("Y-m-d H:i:s");
			$ifhas = $db->get_var("select id from demo_jifenlist where inventoryId=".$jifen['inventoryId']);
			if(!empty($ifhas)){
				echo '<script>alert("产品已存在");history.go(-1);</script>';
				exit;
			}
		}else{
			$ifhas = $db->get_var("select id from demo_jifenlist where id<>$id and inventoryId=".$jifen['inventoryId']);
			if(!empty($ifhas)){
				echo '<script>alert("产品已存在");history.go(-1);</script>';
				exit;
			}
		}
		$db->insert_update('demo_jifenlist',$jifen,'id');
		redirect("?m=system&s=product&a=jifen");
	}
}
function get_jifen_list(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('gonggaoPageNum',$pageNum,time()+3600*24*30);
	$sql="select * from demo_jifenlist where comId=$comId";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->image = '';
			$j->title = $db->get_var("select title from demo_product_inventory where id=$j->inventoryId");;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->status = empty($j->status)?'下架':'上架';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function del_jifen(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_jifenlist where id=$id and comId=$comId");
	echo '{"code":1,"message":"ok"}';
}
function setorders(){
	global $db,$request;
	$id = (int)$request['id'];
	$orders = (int)$request['orders'];
	$db->query("update demo_product_inventory set orders=$orders where id=$id");
	echo '{"code":1,"message":"ok"}';
}
function add_kucun(){
	global $db,$request;
	$id = (int)$request['id'];
	$num = (int)$request['num'];
	//新增修改库存逻辑  num  大于 0  增加库存， 小于0 减少库存
	$kucun = $db->get_row("select * from demo_kucun where inventoryId=$id and storeId=5");
	
	
	if($num > 0){
	    $db->query("update demo_kucun set kucun=kucun+$num where inventoryId=$inventoryId and storeId=5 limit 1");
	    
	}else{
	    if($kucun->kucun < $num){
	        $db->query("update demo_kucun set kucun=0 where inventoryId=$inventoryId and storeId=5 limit 1");
	        
	    }else{
	        $db->query("update demo_kucun set kucun=kucun-$num where inventoryId=$inventoryId and storeId=5 limit 1");
	    }
	    
	}
	echo '{"code":1,"message":"ok"}';
}

//运费 模块
function set_yunfei(){
	global $db,$request;
}

function setBook()
{
    global $db,$request;
	
	if($request['submit']==1){
        $productId = (int)$request['productId'];
        $inventoryId = (int)$request['inventoryId'];
        $filepath = isset($request['filepath']) ? $request['filepath'] : '';
        
        $db->query("update demo_product set book_url = '$filepath' where id = $productId ");

		$return = array();
    	$return['code'] = 1;
    	$return['message'] = '上传成功';
    	$reurn['data'] = array();
		$content = '导入成功！';
		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		@unlink($filepath);exit();
	}
}

function setParam()
{
	global $db,$request;
	
	if($request['submit']==1){
        $param = array();
        $productId = (int)$request['productId'];
        if($productId){
            $param['id'] = (int)$db->get_var("select id from demo_product_params where productId = $productId");
            $countKey = count($request['addRowKey']);
            for($i = 0; $i < $countKey; $i++){
                if($request['addRowKey'][$i] == 'describe') continue;
                if($request['addRowKey'][$i] == 'gene_id'){
                    $param[$request['addRowKey'][$i]] = (int)$request['addRowValue'][$i];
                }else{
                    $param[$request['addRowKey'][$i]] = empty($request['addRowValue'][$i]) ? "" : $request['addRowValue'][$i];
                }
            }
            
            $db->insert_update("demo_product_params", $param, "id");
        }

		redirect("?m=system&s=product");
	}
}

function add_yunfei_moban(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$yunfei_moban = array();
		$yunfei_moban['id'] = (int)$request['id'];
		$yunfei_moban['comId'] = $comId;
		$yunfei_moban['title'] = $request['title'];
		$yunfei_moban['scene'] = $request['scene'];
		$yunfei_moban['accordby'] = (int)$request['accordby'];
		$yunfei_moban['if_man'] = (int)$request['if_man'];
		$yunfei_moban['man'] = empty($request['man'])?'0':$request['man'];
		$yunfei_moban['mantype'] = (int)$request['mantype'];
		if(!empty($yunfei_moban['id'])){
			$db->query("delete from yunfei_moban_rule where mobanId=".$yunfei_moban['id']);
		}
		$yunfei_moban['dtTime'] = date("Y-m-d H:i:s");

		$jiluId = $db->insert_update('yunfei_moban',$yunfei_moban,'id');

		$sql="insert into yunfei_moban_rule(mobanId,areaNames,areaIds,base,base_price,add_num,add_price,content) values";
		$sql1 = "";
		if(!empty($request['rows'])){
			foreach ($request['rows'] as $row) {
				$sql1 .= ",($jiluId,'".$request['areaNames_'.$row]."','".$request['areaIds_'.$row]."','".$request['base_'.$row]."','".$request['base_price_'.$row]."','".$request['add_num_'.$row]."','".$request['add_price_'.$row]."','".$request['content_'.$row]."')";
			}
			$sql1=substr($sql1,1);
			$db->query($sql.$sql1);
		}
		redirect("?m=system&s=product&a=set_yunfei&id=1305&scene=1");
	}
}

function delete_moban(){
	global $request,$db;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$ifhas = $db->get_var("select id from demo_product where comId=$comId and yunfei_moban=$id limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"已经有商品使用该模板，不能删除，请修改模板或先修改属于该模板的商品"}';
		exit;
	}else{
		$db->query("delete from yunfei_moban where id=$id and comId=$comId");
		$db->query("delete from yunfei_moban_rule where mobanId=$id");
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}

function get_moban_detail(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$weight = $db->get_var("select weight from demo_product_set where comId=$comId");
	$moban = $db->get_row("select * from yunfei_moban where id=$id");
	$rules = $db->get_results("select * from yunfei_moban_rule where mobanId=$id order by id asc");
	$str = '<tr id="tr_detail_'.$id.'"><td colspan="7"><div class="yunfeixiangxi_tc"><table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<tbody><tr height="36">
        	<td align="center" width="430" valign="middle">
            	可配送区域
            </td>
        	<td align="center" width="170" valign="middle">
             	起算量	
            </td>
        	<td align="center" width="155" valign="middle">
            	运费（元）
            </td>
        	<td align="center" width="147" valign="middle">
            	续件
            </td>
            <td align="center" width="150" valign="middle">
            	续运费（元）
            </td>
        </tr>';
        foreach ($rules as $rule) {
        	$str.='<tr height="36">
	        	<td align="left" width="430" valign="middle">
	            	<span>'.$rule->areaNames.'</span>
	            </td>
	        	<td align="center" width="170" valign="middle">
	             	'.$rule->base.' '.($moban->accordby==2?$weight:'').'
	            </td>
	        	<td align="center" width="155" valign="middle">
	            	'.$rule->base_price.'
	            </td>
	        	<td align="center" width="147" valign="middle">
	            	'.$rule->add_num.' '.($moban->accordby==2?$weight:'').'
	            </td>
	            <td align="center" width="150" valign="middle">
	            	'.$rule->add_price.'
	            </td>
	        </tr>';
        }
    $str.='</tbody></table></div></td></tr>';
    echo $str;
    exit;
}

/**
 * 京东产品采集
 *
 * @return void
 */
function collectJd(){
	global $db,$request;
	
	$url = $request['url'];
	$source = $request['source'];
	$channelId = $request['channelId'];
	
	
	// $content = file_get_contents('m2detail.html');
	// $url = 'http://zc.io/erp/m2detail.html';
	$return = array('code'=>0,'msg'=>'采集失败');
	
	if(empty($url)){
		$return['msg'] = '请输入链接地址';
		echo json_encode($return);die;
	}

	$purl = parse_url($url);
	if(empty($purl['host'])){
		$return['msg'] = '链接地址不正确';
		echo json_encode($return);die;
	}
	
	
	$res =(string) get_content_by_url($url,[],true);			
	//商品图
	preg_match('/[imageList: \[](".*")\]/',$res,$image);
	$imageList = json_decode($image[0],true);
	
	preg_match('/<img id="spec-img".+data-origin="(.+)".alt/',$res,$searchCdnUrl);	
	$searchCdnUrl = explode('_',$searchCdnUrl[1]);
	$cdnUrl = 'http:'.$searchCdnUrl[0];
	$finalList = array();
	
	foreach($imageList as $item){
		if(count($searchCdnUrl) > 1){
			$finalList[] = trim($cdnUrl.'_'.$item,'.avif');
		}else{
			$finalList[] = trim(substr($cdnUrl,0,strpos($cdnUrl,'jfs')).$item,'.avif');
		}
	}		
	//商品名称
	preg_match('/name:.\'(.+)\'/',$res,$matchName);
	//skuId
	preg_match('/skuid:.(\d+)/',$res,$matchSkuId);
	$skuId = !empty($matchSkuId[1])?$matchSkuId[1]:0;
	$skuPrice = 0;
	if($skuId){
// 		$priceRes = get_content_by_url("https://p.3.cn/prices/mgets?skuIds=J_{$skuId},J_&type=1");
        $priceRes = get_content_by_url("https://item-soa.jd.com/getWareBusiness?skuId={$skuId}");
        
        // var_dump($priceRes);die;
		// list($header,$priceRes) = explode("\r\n\r\n",$resCon);
// 		string(104) "[{"exception":"该接口即将下线，请联系(erp)wangjianyu1，liuhuimin9，liteng36;p.3.cn,null"}] " int(111)
		if(!empty($priceRes)){
			$priceRes = json_decode($priceRes,true);
			$skuPrice = $priceRes['price']['p'];
		}
	}else{
		$return['data'] = $res;
		echo json_encode($return,256);die;		
	}
	
	preg_match("/<li.title='(.*)'\s+商品名称/",$res,$matchModelNo);
	preg_match("/<li.title='(.*)'.+品牌/",$res,$matchBrand);
	
	preg_match('/\<dt\>型号\<\/dt\>\<dd\>(.*)\<\/dd\>/',$res,$matchModelNo2);
	preg_match("/<li.title='(.*)'.+货号/",$res,$barcode);
	preg_match("/<li.title='(.*)'.+商品产地/",$res,$madeInArr);

	$model_no = '';
	if(!empty($matchModelNo2[1])){
		$model_no = $matchModelNo2[1];
	}elseif(!empty($barcode[1])){
		$model_no = $barcode[1];
	}
	$madeIn = '国内';
	if(!empty($madeInArr[1])){
		$madeIn = $madeInArr[1];
		if($madeIn == '中国大陆'){
			$madeIn = '国内';
		}
	}
	
	$return['data']['channelId'] = $channelId;	
	$return['data']['madeIn'] = $madeIn;	
	$return['data']['source'] ='京东';
	$return['data']['model_no2'] = !empty($matchModelNo2[1])?$matchModelNo2[1]:0;	
	$return['data']['model_no'] = $model_no;	
	$return['data']['brand'] = !empty($matchBrand[1])?$matchBrand[1]:0;	
	$return['data']['skuId'] = $skuId;
	$return['data']['skuPrice'] = $skuPrice;
	$return['data']['marketPrice'] = round($skuPrice * 1.03,2);
	$return['data']['name'] = !empty($matchName[1])?$matchName[1]:'';	
	$return['data']['images'] = $finalList;	
	$return['code'] = 1;
	$return['msg']='成功';
	
	echo json_encode($return,256);die;
}

function get_content_by_url($url){
	if(empty($url)){
		return false;
	}
	$cookie = dirname(__FILE__) . '/../../cookie_jd/'. session_id() .'.txt';
	$ip = getIPs();
	$ch = curl_init();
	$url =  trim($url);
	if(stripos($url,'https:') === false && stripos($url,'http:') === false){
	    $url ='http:'.$url;
	}
	curl_setopt($ch, CURLOPT_URL, trim($url));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, []);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
	$headers = array();
	$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36';
	if(!empty($ip)){
		$headers[] = 'Referer: https://item.jd.com/';
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$content = curl_exec($ch);
	if (curl_errno($ch)) {
	   // var_dump(curl_errno($ch),$url);die;
		echo 'Error:' . curl_error($ch);die;
	}
	$information = curl_getinfo($ch);
	file_put_contents(dirname(__FILE__) . '/../../cookie_jd/request.txt',json_encode($information).PHP_EOL,FILE_APPEND);
	$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	curl_close($ch);
	return substr($content, $headerSize);
}

function getIPs(){
	$ip_long = array(
		array('607649792', '608174079'), //36.56.0.0-36.63.255.255		
		array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255		
		array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255		
		array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255		
		array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255		
		array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255		
		array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255		
		array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255		
		array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255		
		array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255	
	);
	
// 	$rand_key = mt_rand(0, 9);
	
// 	$ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
	
	$ip = mt_rand(11, 191) . "." . mt_rand(0, 240) . "." . mt_rand(1, 240) . "." . mt_rand(1, 240);
	return $ip;
}

function batchOnline(){
	global $db,$request;
	
	$return = array();
	$return['code'] = 1;
	$return['message'] = '上传成功';
	$reurn['data'] = array();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$filepath = $request['filepath'];
// 	var_dump($filepath);
// 	$filepath = ABSPATH.str_replace('../','',$filepath);
// var_dump( $filepath);\
    ini_set ("memory_limit","-1");
	require_once ABSPATH.'inc/excel.php';
	$pandians = excelToArray($filepath, 1);
// 	var_dump($pandians);die;
	$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
	//file_put_contents('request.txt',$pandianJsonData);
// 	$pandianJsonData = str_replace("'","\'",$pandianJsonData);
// 	$pandianJsonData = preg_replace('/((\s)*(\n)+(\s)*)/','',$pandianJsonData);
// 	$pandianJsonData = stripcslashes($pandianJsonData);
	$jilus = json_decode($pandianJsonData,true);
    
    if(count($jilus) > 20000){
        $res = '导入失败';
        $num = count($jilus) + 1;
		$content = '检测到'.$num.'条导入数据，请拆分附件或者删除多余空行！';
		

		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		//echo '{"code":1,"message":"上传成功","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);exit();
    }

	$errorJilus = array();
	$success_num = 0;
	$fail_num = 0;
	$dtTime = date("Y-m-d H:i:s");
	$fahuoIds = '';
	
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
		    if(empty($jilu[0]) || $jilu[0] == '货号'){
		        continue;
		    }
		    
		    $skuId = $jilu[0];
		    $result = $db->get_row("select * from demo_product where skuId = '$skuId' ");
			if($result){
			    $db->query("update demo_product set status = -1 where id = $result->id");
			    $db->query("update demo_product_inventory set status = -1 where productId = $result->id");
			    $success_num++;
			}else{
			    $fail_num++;
			}
		}
		
		if(empty($fail_num)){
			$res = '导入成功';
			$content = '实际下架'.$success_num.'条，全部下架成功！';
		}else{
			$res = '部分导入成功';
			$content = '实际下架'.$success_num.'条，'.$fail_num.'个下架失败！';
		}

		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		//echo '{"code":1,"message":"上传成功","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);
	}
	exit;
}

function daorushuo1(){
	global $db,$request;
	
	$return = array();
	$return['code'] = 1;
	$return['message'] = '上传成功';
	$reurn['data'] = array();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$filepath = $request['filepath'];
// 	var_dump($filepath);
// 	$filepath = ABSPATH.str_replace('../','',$filepath);
// var_dump( $filepath);\
    ini_set ("memory_limit","-1");
	require_once ABSPATH.'inc/excel.php';
	$pandians = excelToArray($filepath, 0);
// 	var_dump($pandians);die;
	$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
	//file_put_contents('request.txt',$pandianJsonData);
// 	$pandianJsonData = str_replace("'","\'",$pandianJsonData);
// 	$pandianJsonData = preg_replace('/((\s)*(\n)+(\s)*)/','',$pandianJsonData);
// 	$pandianJsonData = stripcslashes($pandianJsonData);
	$jilus = json_decode($pandianJsonData,true);
    
    if(count($jilus) > 20000){
        $res = '导入失败';
        $num = count($jilus) + 1;
		$content = '检测到'.$num.'条导入数据，请拆分附件或者删除多余空行！';
		

		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		//echo '{"code":1,"message":"上传成功","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);exit();
    }

	$errorJilus = array();
	$success_num = 0;
	$fail_num = 0;
	$dtTime = date("Y-m-d H:i:s");
	$fahuoIds = '';
	
	$type = (int)$request['type'];
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
		    if(empty($jilu[0])){
		        continue;
		    }
	
// 			$hadBook = $db->get_row("select * from demo_product_book where skuId='".$jilu[0]."' limit 1");
		
			$id = 0;
// 			if($hadBook){
// 			    $id = $hadBook->id;
// 			}
			$book = array(
			    'id' => $id,
			    'skuId' => $jilu[0],
			    'type' => $type,
			    // 'content' => addcslashes(json_encode($jilu, JSON_UNESCAPED_UNICODE)),
			    'content' =>  addslashes(json_encode($jilu, JSON_UNESCAPED_UNICODE)) ,
			    'dtTime' => date("Y-m-d H:i:s")
			);
			
			$db->insert_update("demo_product_book", $book, "id");
			$result = (int)$db->get_var("select last_insert_id();");
			if($result){
			    $success_num++;
			}else{
			    $fail_num++;
			}
		}
		
		if(empty($fail_num)){
			$res = '导入成功';
			$content = '实际导入说明书'.$success_num.'条，全部导入成功！';
		}else{
			$res = '部分导入成功';
			$content = '实际导入说明书'.$success_num.'条，'.$fail_num.'个导入失败！';
		}

		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		//echo '{"code":1,"message":"上传成功","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);
	}
	exit;
}



