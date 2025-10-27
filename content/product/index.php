<?php
function index(){}
function shangxin(){}
function huodong(){}
function yushou(){}
function cantuan(){}
function gouwuche(){
	if(empty($_SESSION[TB_PREFIX.'user_ID'])){
		$url = urlencode('/index.php?p=4&a=gouwuche');
		redirect('/index.php?p=8&a=login&url='.$url);
	}
}
function queren(){}
function view(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$id = (int)$request['id'];
	if($userId>0){
		$ifhas = $db->get_var("select userId from user_pdt_history where userId=$userId and comId=$comId and inventoryId=$id limit 1");
		if(empty($ifhas)){
			$db->query("insert into user_pdt_history(userId,inventoryId,dtTime,comId) value($userId,$id,'".date("Y-m-d H:i:s")."',$comId)");
		}else{
			$db->query("update user_pdt_history set dtTime='".date("Y-m-d H:i:s")."' where userId=$userId and comId=$comId and inventoryId=$id");
		}
	}
	history_view($id);
}
//积分产品详情
function views(){
	global $db,$request;
}
//积分确认页面
function querens(){}
function channels(){}
function get_pdt_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$channelId = (int)$request['channelId'];
	$fenleiId = (int)$request['fenleiId'];
	$tags = strip_tags($request['tags']);
	$cuxiao_id = (int)$request['cuxiao_id'];
	$yhq_id = (int)$request['yhq_id'];
	$lipinka_id = (int)$request['lipinka_id'];
	$xinren = (int)$request['xinren'];
	$keyword = strip_tags($request['keyword']);
	$miaoshaId = (int)$request['miaoshaId'];//秒杀活动id
	$shoucang = (int)$request['shoucang'];//我的收藏
	$history = (int)$request['history'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
	}
	if(!empty($request['miaoshaId']) && $request['rand']==1){
		$order1 = 'rand()';
		$order2 = '';
	}
	//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力
	$if_cache = 0;
	if(empty($tags) && empty($yhq_id) && empty($lipinka_id) && empty($keyword) && empty($shoucang) && empty($history) && empty($request['rand']) && empty($request['shopId']) && empty($request['is_jifen'])){
		$if_cache = 1;
	}
	if($if_cache==1){
		$chache_file = $comId.'-'.$channelId.'-'.$fenleiId.'-'.$cuxiao_id.'-'.$miaoshaId.'-'.$page.'-'.$pageNum.'-'.$request['order1'].'-'.$request['order2'].$level.'.dat';
		$cache_content = file_get_contents(ABSPATH.'/cache/product/'.$chache_file);
		if(!empty($cache_content)){
			$now = time();
			$caches = json_decode($cache_content);
			if($caches->endTime>$now){
				echo $cache_content;
				exit;
			}
		}
	}
	$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering from demo_product_inventory where comId=$comId and if_lingshou=1 and fenleiId<>387";
	if($comId==10){
		$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering from demo_product_inventory where ".(empty($request['shopId'])?'if_tongbu=1':'comId='.(int)$request['shopId'])." and fenleiId<>387 ";
	}
	if($xinren==1){
		$ids = $db->get_var("select group_concat(inventoryId) from demo_xinren_discount where comId=$comId and status=1");
		if(empty($ids))$ids = '0';
		$sql.=" and id in($ids)";
	}
	if(!empty($yhq_id)){
		$yhq_comId = $_SESSION['if_tongbu']==1?10:$comId;
		$fenbiao = getFenbiao($comId,20);
		$yhq=$db->get_row("select * from yhq where id=(select jiluId from user_yhq$fenbiao where id=$yhq_id)");
		if(!empty($yhq->mendianIds)){
			$sql.=" and comId in($yhq->mendianIds)";
		}
		if($yhq->useType>1){
			if(!empty($yhq->channels)){
				$sql.=" and ".($comId==10?'fenleiId':'channelId')." in($yhq->channels)";
			}
		}
		if(!empty($yhq->pdts)){
			$sql.=" and id in($yhq->pdts)";
		}
	}
	if(!empty($lipinka_id)){
		$lipinka_jilu=$db->get_row("select mendianId,channels,pdts from lipinka_jilu where id=(select jiluId from lipinka where id=$lipinka_id)");
		if(!empty($lipinka_jilu->mendianId)){
			$sql.=" and comId=$lipinka_jilu->mendianId";
		}
		if(!empty($lipinka_jilu->channels)){
			$sql.=" and fenleiId in($lipinka_jilu->channels)";
		}
		if(!empty($lipinka_jilu->pdts)){
			$sql.=" and id in($lipinka_jilu->pdts)";
		}
	}
	if(!empty($request['is_jifen'])){
		$jifenIds = $db->get_var("select group_concat(inventoryId) from demo_jifenList where comId=$comId and status=1");
		if(empty($jifenIds)){
			$jifenIds = '0';
		}
		$sql .= " and id in ($jifenIds)";
	}
	if(!empty($miaoshaId)){
		$pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$miaoshaId");
		if(empty($pdtIds))$pdtIds = '0';
		$sql.=" and id in($pdtIds)";
	}
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($fenleiId)){
		$channelIds = $fenleiId.getZiIds($fenleiId);
		$sql.=" and fenleiId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or productId in($pdtIds))";
		search_history($keyword);
	}
	if(!empty($tags)){
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and tags like '%$tags%'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($brandId)){
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=".$brandId);
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($cuxiao_id)){
		$sql.=" and find_in_set($cuxiao_id,cuxiao_ids)";
	}
	if($shoucang==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_collect where userId=$userId and comId=$comId");
		if(empty($shoucangIds))$shoucangIds='0';
		$sql.=" and id in($shoucangIds)";
	}
	if($history==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_history where userId=$userId and comId=$comId");
		if(empty($shoucangIds))$shoucangIds='0';
		$sql.=" and id in($shoucangIds)";
	}
	if($comId==10){
		$sql.=" and status=1 and zstatus=1";
	}else{
		$sql.=" and status=1";
	}
	
	$count = $db->get_var(str_replace('productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering','count(distinct(productId))',$sql));
	$sql.=" group by productId";
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	//file_put_contents('request.txt',$sql);
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['shoucang'] = $shoucang;
	$return['data'] = array();
	//$zhekou = get_user_zhekou();
	
	$now = date("Y-m-d H:i:s");
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_sale,price_card,originalPic,channelId from demo_product_inventory where id=$pdt->inventoryId");
			$pro = $db->get_row("select brandId,price_name,originalPic,untis,remark,subtitle from demo_product where id=$pdt->productId");
			$data = array();
			$data['id'] = $pdt->productId;
			$data['title'] = $pdt->title;
			if($comId==1142 && $_SESSION[TB_PREFIX.'user_level']!=118){
				$data['title'] = $pro->subtitle;
				$data['hidden'] = 1;
			}
			$data['img'] = empty($inventory->originalPic)?ispic($pro->originalPic):ispic($inventory->originalPic);
			$data['inventoryId'] = $pdt->inventoryId;
			$data['price_market'] = getXiaoshu($inventory->price_market,2);
			if($xinren==1){
				$data['price_user'] = $data['price_sale'] = $db->get_var("select money from demo_xinren_discount where inventoryId=$pdt->inventoryId and comId=$comId limit 1");
			}else{
				$data['price_sale'] = get_user_zhekou($pdt->inventoryId,$inventory->price_sale);
				$data['price_user'] = getXiaoshu($data['price_sale']-$inventory->price_card,2);
			}
	        $units_arr = json_decode($pro->untis);
	        $data['unit'] = $units_arr[0]->title;
			$data['orders'] = $pdt->orders;
			if($comId==10){
				$data['comId'] = $pdt->comId;
				$data['com_title'] = $db->get_var("select com_title from demo_shezhi where comId=$pdt->comId");
			}
			$data['brand'] = '';
			
			$brandId = $pro->brandId;
			if($brandId>0){
				$data['brand'] = $db->get_var("select originalPic from demo_product_brand where id=$brandId");
			}
			$price_name = empty($pro->price_name)?'市场价':$pro->price_name;
			$data['price_name'] = $price_name;
			$data['price_name1'] = '';
			if($comId==1113&&$level==88){
				$data['price_name'] = '零售价';
				$data['price_name1'] = '供货价';
				$data['price_market'] = getXiaoshu($inventory->price_sale,2);
			}
			$data['fenleiId'] = $inventory->fenleiId;
			$data['channelId'] = $inventory->channelId;
			$data['key_vals'] = $inventory->key_vals;
			$data['remark'] = $pro->remark;
			if($miaoshaId>0){
				$kucun = get_product_kucun($pdt->inventoryId,$pdt->comId);
				if($inventory->orders<10){
					$chushu = $pdt->inventoryId%5+2;
					$inventory->orders = (int)($kucun/$chushu)  + ($pdt->inventoryId%10);
				}
				$data['orders'] = $inventory->orders;
				$zongNum = $kucun + $inventory->orders;
				$width = 0;
				if($zongNum>0){
					$width =  intval($inventory->orders*10000/$zongNum)/100;
				}
				$data['width'] = $width;
			}
			$data['mark'] = '';
			if($request['show_mark']==1){
				$cuxiao_pdt = $db->get_row("select accordType,type from cuxiao_pdt where comId=10 and find_in_set($pdt->inventoryId,pdtIds) and startTime<'$now' and endTime>'$now' and status=1 order by startTime asc limit 1");
				if(!empty($cuxiao_pdt)){
					if($cuxiao_pdt->accordType==3){
						$data['mark'] = '秒';
					}else{
						switch ($cuxiao_pdt->type){
							case '1':
								$data['mark'] = '赠';
							break;
							case '2':
								$data['mark'] = '减';
							break;
							case '3':
								$data['mark'] = '折';
							break;
						}
					}
				}else if($inventory->price_card>0){
					$data['mark'] = '抵';
				}
			}
			//if($comId==1041 && empty($kucun)){
			$data['kucun'] = get_product_kucun($pdt->inventoryId,$pdt->comId);
			//}
			if($request['is_jifen']==1){
				$jifen = $db->get_var("select jifen from demo_jifenList where inventoryId=".$pdt->inventoryId." limit 1");
				$data['jifen'] = $jifen;
			}
			if($comId==1121){
				$addrows = $db->get_var("select addrows from demo_product where id=$pdt->productId");
				$addrows_arr = json_decode($addrows,true);
				$data['remark'] = '盆径：'.$addrows_arr['盆径'].'&nbsp;&nbsp;规格：'.$addrows_arr['规格'].'&nbsp;&nbsp;头数：'.$addrows_arr['头数'].'&nbsp;&nbsp;数量：'.$addrows_arr['数量'];
			}
			$return['data'][] = $data;
		}
	}
	if($if_cache==1){
		$cache_endtime = strtotime('+10 minutes');
		$return['endTime'] = $cache_endtime;
	}
	$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
	if($if_cache==1){
		file_put_contents(ABSPATH.'/cache/product/'.$chache_file,$cache_content,LOCK_EX);
	}
	echo $cache_content;
	exit;
}
function get_pdt_list_third(){/*尚德社区购中产品分类中的三级分类产品调取1106.buy.zhishangez.com*/
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$channelId = (int)$request['channelId'];
	$fenleiId = (int)$request['fenleiId'];
	$tags = $request['tags'];
	$cuxiao_id = (int)$request['cuxiao_id'];
	$yhq_id = (int)$request['yhq_id'];
	$lipinka_id = (int)$request['lipinka_id'];
	$keyword = $request['keyword'];
	$miaoshaId = (int)$request['miaoshaId'];//秒杀活动id
	$shoucang = (int)$request['shoucang'];//我的收藏
	$history = (int)$request['history'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$order1 = empty($request['order1'])?'ordering':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
	}
	if(!empty($request['miaoshaId']) && $request['rand']==1){
		$order1 = 'rand()';
		$order2 = '';
	}
	//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力
	$if_cache = 0;
	if(empty($tags) && empty($yhq_id) && empty($lipinka_id) && empty($keyword) && empty($shoucang) && empty($history) && empty($request['rand']) && empty($request['shopId'])){
		//$if_cache = 1;
	}
	if($if_cache==1){
		$chache_file = $comId.'-'.$channelId.'-'.$fenleiId.'-'.$cuxiao_id.'-'.$miaoshaId.'-'.$page.'-'.$pageNum.'-'.$request['order1'].'-'.$request['order2'].'.dat';
		$cache_content = file_get_contents(ABSPATH.'/cache/product/'.$chache_file);
		if(!empty($cache_content)){
			$now = time();
			$caches = json_decode($cache_content);
			if($caches->endTime>$now){
				echo $cache_content;
				exit;
			}
		}
	}
	$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId from demo_product_inventory where comId=$comId and if_lingshou=1 and fenleiId<>387";
	if($comId==10){
		$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId from demo_product_inventory where ".(empty($request['shopId'])?'if_tongbu=1':'comId='.(int)$request['shopId'])." and fenleiId<>387 ";
	}
	if(!empty($yhq_id)){
		$yhq_comId = $_SESSION['if_tongbu']==1?10:$comId;
		$fenbiao = getFenbiao($comId,20);
		$yhq=$db->get_row("select * from yhq where id=(select jiluId from user_yhq$fenbiao where id=$yhq_id)");
		if(!empty($yhq->mendianIds)){
			$sql.=" and comId in($yhq->mendianIds)";
		}
		if($yhq->useType>1){
			if(!empty($yhq->channels)){
				$sql.=" and ".($comId==10?'fenleiId':'channelId')." in($yhq->channels)";
			}
		}
		if(!empty($yhq->pdts)){
			$sql.=" and id in($yhq->pdts)";
		}
	}
	if(!empty($lipinka_id)){
		$lipinka_jilu=$db->get_row("select mendianId,channels,pdts from lipinka_jilu where id=(select jiluId from lipinka where id=$lipinka_id)");
		if(!empty($lipinka_jilu->mendianId)){
			$sql.=" and comId=$lipinka_jilu->mendianId";
		}
		if(!empty($lipinka_jilu->channels)){
			$sql.=" and fenleiId in($lipinka_jilu->channels)";
		}
		if(!empty($lipinka_jilu->pdts)){
			$sql.=" and id in($lipinka_jilu->pdts)";
		}
	}
	if(!empty($miaoshaId)){
		$pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$miaoshaId");
		if(empty($pdtIds))$pdtIds = '0';
		$sql.=" and id in($pdtIds)";
	}
	if(!empty($channelId)){
		//$channelIds = $channelId.getZiIds($channelId);
		//$sql.=" and channelId in($channelIds)";
	}
	if(!empty($fenleiId)){
		//$channelIds = $fenleiId.getZiIds($fenleiId);
		//$sql.=" and fenleiId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or productId in($pdtIds))";
		search_history($keyword);
	}
	if(!empty($tags)){
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and tags like '%$tags%'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($brandId)){
		$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=".$brandId);
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and productId in($pdtIds)";
	}
	if(!empty($cuxiao_id)){
		$sql.=" and find_in_set($cuxiao_id,cuxiao_ids)";
	}
	if($shoucang==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_collect where userId=$userId and comId=$comId");
		if(empty($shoucangIds))$shoucangIds='0';
		$sql.=" and id in($shoucangIds)";
	}
	if($history==1){
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_history where userId=$userId and comId=$comId");
		if(empty($shoucangIds))$shoucangIds='0';
		$sql.=" and id in($shoucangIds)";
	}
	if($comId==10){
		$sql.=" and status=1 and zstatus=1";
	}else{
		$sql.=" and status=1";
	}
	
	$count = $db->get_var(str_replace('productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market','count(distinct(productId))',$sql));
	// $sql.=" group by productId";
	// $sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['shoucang'] = $shoucang;
	$return['data'] = array();
	//$zhekou = get_user_zhekou();
	$now = date("Y-m-d H:i:s");
	$fenleiRes = $db->get_results("select * from demo_product_channel where parentId=$channelId order by ordering desc");
	if($fenleiRes){
		foreach ($fenleiRes as $flK => $flV) {
			$items = array();
			$sql3 = $sql." and channelId=".$flV->id;
			$sql2 = "";
			$sql2 .=" group by productId";
			$sql2 .=" order by $order1 $order2";
			$pdts = $db->get_results($sql3.$sql2);
			if(!empty($pdts)){
				foreach ($pdts as $i=>$pdt) {
					$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_card,originalPic from demo_product_inventory where id=$pdt->inventoryId");
					$pro = $db->get_row("select brandId,price_name,originalPic,untis from demo_product where id=$pdt->productId");
					$data = array();
					$data['id'] = $pdt->productId;
					$data['title'] = $pdt->title;
					$data['img'] = empty($inventory->originalPic)?ispic($pro->originalPic):ispic($inventory->originalPic);
					$data['inventoryId'] = $pdt->inventoryId;
					$data['price_sale'] = get_user_zhekou($pdt->inventoryId,$pdt->price_sale);
					$data['price_market'] = getXiaoshu($inventory->price_market,2);
					$data['price_user'] = getXiaoshu($data['price_sale']-$inventory->price_card,2);
			        $units_arr = json_decode($pro->untis);
			        $data['unit'] = $units_arr[0]->title;
					$data['orders'] = $pdt->orders;
					if($comId==10){
						$data['comId'] = $pdt->comId;
						$data['com_title'] = $db->get_var("select com_title from demo_shezhi where comId=$pdt->comId");
					}
					$data['brand'] = '';
					
					$brandId = $pro->brandId;
					if($brandId>0){
						$data['brand'] = $db->get_var("select originalPic from demo_product_brand where id=$brandId");
					}
					$price_name = empty($pro->price_name)?'市场价':$pro->price_name;
					$data['price_name'] = $price_name;
					$data['fenleiId'] = $inventory->fenleiId;
					$data['key_vals'] = $inventory->key_vals;
					if($miaoshaId>0){
						$kucun = get_product_kucun($pdt->inventoryId,$pdt->comId);
						if($inventory->orders<10){
							$chushu = $pdt->inventoryId%5+2;
							$inventory->orders = (int)($kucun/$chushu)  + ($pdt->inventoryId%10);
						}
						$data['orders'] = $inventory->orders;
						$zongNum = $kucun + $inventory->orders;
						$width = 0;
						if($zongNum>0){
							$width =  intval($inventory->orders*10000/$zongNum)/100;
						}
						$data['width'] = $width;
					}
					$data['mark'] = '';
					if($request['show_mark']==1){
						$cuxiao_pdt = $db->get_row("select accordType,type from cuxiao_pdt where comId=10 and find_in_set($pdt->inventoryId,pdtIds) and startTime<'$now' and endTime>'$now' and status=1 order by startTime asc limit 1");
						if(!empty($cuxiao_pdt)){
							if($cuxiao_pdt->accordType==3){
								$data['mark'] = '秒';
							}else{
								switch ($cuxiao_pdt->type){
									case '1':
										$data['mark'] = '赠';
									break;
									case '2':
										$data['mark'] = '减';
									break;
									case '3':
										$data['mark'] = '折';
									break;
								}
							}
						}else if($inventory->price_card>0){
							$data['mark'] = '抵';
						}
					}
					if(empty($kucun)){
						$data['kucun'] = get_product_kucun($pdt->inventoryId,$pdt->comId);
					}
					$items[] = $data;
				}
			}
			$return['data'][] = array("title"=>$flV->title, "itemdetail"=>$items);
		}
	}else{
		$items = array();
		$sql3 = $sql." and channelId=".$channelId;
		$sql2 = "";
		$sql2 .=" group by productId";
		$sql2 .=" order by $order1 $order2";
		$pdts = $db->get_results($sql3.$sql2);
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_card,originalPic from demo_product_inventory where id=$pdt->inventoryId");
				$pro = $db->get_row("select brandId,price_name,originalPic,untis from demo_product where id=$pdt->productId");
				$data = array();
				$data['id'] = $pdt->productId;
				$data['title'] = $pdt->title;
				$data['img'] = empty($inventory->originalPic)?ispic($pro->originalPic):ispic($inventory->originalPic);
				$data['inventoryId'] = $pdt->inventoryId;
				$data['price_sale'] = get_user_zhekou($pdt->inventoryId,$pdt->price_sale);
				$data['price_market'] = getXiaoshu($inventory->price_market,2);
				$data['price_user'] = getXiaoshu($data['price_sale']-$inventory->price_card,2);
		        $units_arr = json_decode($pro->untis);
		        $data['unit'] = $units_arr[0]->title;
				$data['orders'] = $pdt->orders;
				if($comId==10){
					$data['comId'] = $pdt->comId;
					$data['com_title'] = $db->get_var("select com_title from demo_shezhi where comId=$pdt->comId");
				}
				$data['brand'] = '';
				
				$brandId = $pro->brandId;
				if($brandId>0){
					$data['brand'] = $db->get_var("select originalPic from demo_product_brand where id=$brandId");
				}
				$price_name = empty($pro->price_name)?'市场价':$pro->price_name;
				$data['price_name'] = $price_name;
				$data['fenleiId'] = $inventory->fenleiId;
				$data['key_vals'] = $inventory->key_vals;
				if($miaoshaId>0){
					$kucun = get_product_kucun($pdt->inventoryId,$pdt->comId);
					if($inventory->orders<10){
						$chushu = $pdt->inventoryId%5+2;
						$inventory->orders = (int)($kucun/$chushu)  + ($pdt->inventoryId%10);
					}
					$data['orders'] = $inventory->orders;
					$zongNum = $kucun + $inventory->orders;
					$width = 0;
					if($zongNum>0){
						$width =  intval($inventory->orders*10000/$zongNum)/100;
					}
					$data['width'] = $width;
				}
				$data['mark'] = '';
				if($request['show_mark']==1){
					$cuxiao_pdt = $db->get_row("select accordType,type from cuxiao_pdt where comId=10 and find_in_set($pdt->inventoryId,pdtIds) and startTime<'$now' and endTime>'$now' and status=1 order by startTime asc limit 1");
					if(!empty($cuxiao_pdt)){
						if($cuxiao_pdt->accordType==3){
							$data['mark'] = '秒';
						}else{
							switch ($cuxiao_pdt->type){
								case '1':
									$data['mark'] = '赠';
								break;
								case '2':
									$data['mark'] = '减';
								break;
								case '3':
									$data['mark'] = '折';
								break;
							}
						}
					}else if($inventory->price_card>0){
						$data['mark'] = '抵';
					}
				}
				if(empty($kucun)){
					$data['kucun'] = get_product_kucun($pdt->inventoryId,$pdt->comId);
				}
				$items[] = $data;
			}
		}
		$ctitle = $db->get_var("select title from demo_product_channel where id=$channelId");
		$return['data'][] = array("title"=>$ctitle, "itemdetail"=>$items);
	}
	if($if_cache==1){
		$cache_endtime = strtotime('+10 minutes');
		$return['endTime'] = $cache_endtime;
	}
	$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
	if($if_cache==1){
		file_put_contents(ABSPATH.'/cache/product/'.$chache_file,$cache_content,LOCK_EX);
	}
	echo $cache_content;
	exit;
}
function get_product_channels(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$channelId = (int)$request['channelId'];
	$channels = $db->get_results("select id,title from demo_product_channel where comId=$comId and parentId=$channelId order by ordering desc,id asc");
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['data'] = array();
	if(!empty($channels)){
		foreach ($channels as $val) {
			$ziChannels = $db->get_results("select id,title,originalPic from demo_product_channel where parentId=$val->id order by ordering desc,id asc");
			$val->channels = $ziChannels;
			$val->tags = '';
			$return['data'][] = $val;
		}
	}
	if($comId==1124 && in_array($channelId,[962,963,965,1023])){
		$tags = new StdClass();
		$tags->id = $channelId;
		$tags->title = '海鲜冻品';
		$tags->tags = '海鲜冻品';
		$tags1 = new StdClass();
		$tags1->id = $channelId;
		$tags1->title = '鲜活海鲜';
		$tags1->tags = '鲜活海鲜';
		$return['data'][] = $tags;
		$return['data'][] = $tags1;
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//搜索记录
function search_history($data)
{
	if (!$data)
	{
		return false;
	}
    //判断cookie类里面是否有浏览记录
	if ($_COOKIE['search_history'])
	{
		$history = json_decode($_COOKIE['search_history'],true);
		//echo $_COOKIE['history'];
        array_unshift($history, $data); //在浏览记录顶部加入
        /* 去除重复记录 */
        $rows = array();
        foreach ($history as $v)
        {
        	if (in_array($v, $rows))
        	{
        		continue;
        	}
        	$rows[] = $v;
        }
        while (count($rows) > 10)
        {
            array_pop($rows);
        }
        setcookie('search_history', json_encode($rows,JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 30, '/');
    }else{
    	$history = json_encode(array($data));
    	setcookie('search_history', $history, time() + 3600 * 24 * 30, '/');
    }
}
function history_view($data)
{
	if (!$data)
	{
		return false;
	}
    //判断cookie类里面是否有浏览记录
	if ($_COOKIE['view_history'])
	{
		$history = json_decode($_COOKIE['view_history'],true);
		//echo $_COOKIE['history'];
        array_unshift($history, $data); //在浏览记录顶部加入
        /* 去除重复记录 */
        $rows = array();
        foreach ($history as $v)
        {
        	if (in_array($v, $rows))
        	{
        		continue;
        	}
        	$rows[] = $v;
        }
        while (count($rows) > 30)
        {
            array_pop($rows);
        }
        setcookie('view_history', json_encode($rows,JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 300, '/');
    }else{
    	$history = json_encode(array($data));
    	setcookie('view_history', $history, time() + 3600 * 24 * 300, '/');
    }
}
function shoucang(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$inventoryId = (int)$request['inventoryId'];
	$ifshoucang = (int)$request['ifshoucang'];
	if($ifshoucang==0){
		$db->query("insert into user_pdt_collect(userId,inventoryId,dtTime,comId) value($userId,$inventoryId,'".date("Y-m-d H:i:s")."',$comId)");
		echo '{"code":1,"message":"收藏成功"}';
	}else{
		$db->query("delete from user_pdt_collect where userId=$userId and comId=$comId and inventoryId=$inventoryId limit 1");
		echo '{"code":1,"message":"取消收藏成功"}';
	}
	exit;
}
function get_pdt_comments(){
	global $db,$request;
	$productId = (int)$request['productId'];
	$comId = (int)$db->get_var("select comId from demo_product where id=$productId");
	$fenbiao = getFenbiao($comId,20);
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$sql = "select * from order_comment$fenbiao where pdtId=$productId ";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['shoucang'] = $shoucang;
	$return['data'] = array();
	$db_service = getCrmDb();
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pingjia = array();
			if($_SESSION['if_tongbu']==1){
				$u = $db_service->get_row("select name as nickname,image from demo_user where id=$j->userId");
			}else{
				$u = $db->get_row("select nickname,image from users where id=$j->userId");
			}
			$pingjia['touxiang'] = ispic($u->image);
			$pingjia['username'] = sys_substr($j->name,1,false).'**';
			$pingjia['dtTime'] = date("Y-m-d H:i",strtotime($j->dtTime1));
			$j->cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->cont1);
			$pingjia['content'] = '<div style="word-break:break-all;white-space:normal;">'.$j->cont1.'</div>';
			$pingjia['imgs'] = '';
			if(!empty($j->images1)){
				$imgs = explode('|',$j->images1);
				foreach ($imgs as $img){
					$pingjia['imgs'] .= '<a href="'.$img.'"><img src="'.$img.'?x-oss-process=image/resize,w_100"></a>';
				}
			}
			$pingjia['reply'] = '';
			if(!empty($j->reply)){
				$j->reply = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->reply);
				$pingjia['reply'] = '<div style="color:#2786bc;word-break:break-all;white-space:normal;">掌柜回复：'.$j->reply.'</div>';
			}
			for ($i=0;$i < $j->star; $i++) { 
				$pingjia['xing'].='<img src="/skins/default/images/biao_13.png">';
			}
			for ($i=$j->star;$i < 5; $i++) {
				$pingjia['xing'].='<img src="/skins/default/images/biao_14.png">';
			}
			$pingjia['key_vals'] = $db->get_var("select key_vals from demo_product_inventory where id=$j->inventoryId");
			$return['data'][] = $pingjia;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_product_info(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$inventoryId = (int)$request['inventoryId'];
	$productId = (int)$request['productId'];
	$inventory = $db->get_row("select title,key_ids,key_vals,image,sn,price_sale,price_market,price_gonghuo from demo_product_inventory where id=$inventoryId");
	$keys = $db->get_results("select id,title,parentId,originalPic from demo_product_key where productId=$productId and isnew=0 order by parentId asc,id asc");
	$keysArry = array();
	$rows = 0;
	$key_ids = explode('-',$inventory->key_ids);
	if(count($keys)>1){
		foreach ($keys as $k){
			$keysArry[$k->parentId][$k->id]['title'] = $k->title;
			$keysArry[$k->parentId][$k->id]['image'] = $k->originalPic;
			$keysArry[$k->parentId][$k->id]['selected'] = in_array($k->id,$key_ids)?1:0;
		}
		$rows = count($keysArry[0]);
	}
	$return = array();
	$return['title'] = $inventory->title;
	$return['image'] = ispic($inventory->image);
	$return['sn'] = $inventory->sn;
	$return['price_sale'] = get_user_zhekou($inventoryId,$inventory->price_sale);
	$return['price_market'] = $inventory->price_market;
	$return['price_gonghuo'] = $inventory->price_gonghuo;
	$return['key_vals'] = $inventory->key_vals;
	$return['keys'] = $keysArry;
	//$return['kucun'] = get_product_kucun($inventoryId,$comId);
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_pdtsn_info(){
	global $db,$request;
	$productId = (int)$request['productId'];
	$key_ids = $request['key_ids'];
	$i = $db->get_row("select * from demo_product_inventory where productId=$productId and key_ids='$key_ids' and status=1 ".($_SESSION['demo_comId']==10?'and zstatus=1':'')." limit 1");
	//$zhekou = get_user_zhekou();
	$retrun = array();
	if(!empty($i)){
		$p = $db->get_row("select cont1,cont2,cont3,originalPic from demo_product where id=$productId");
		if(empty($i->cont1)){
			$i->cont1 = $p->cont1;
			$i->cont2 = empty($i->cont2)?$p->cont2:$i->cont2;
			$i->cont3 = empty($i->cont3)?$p->cont3:$i->cont3;
		}
		if(empty($i->originalPic)){
			$i->originalPic = $p->originalPic;
		}
		$retrun['code'] = 1;
		$retrun['message'] = '成功';
		$inventoryId = $i->id;
		$retrun['inventoryId'] = $inventoryId;
		//$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
		$retrun['kucun'] = get_product_kucun($inventoryId,$i->comId);
		$retrun['sn'] = $i->sn;
		$retrun['price'] = get_user_zhekou($inventoryId,$i->price_sale);
		$retrun['price_user'] = getXiaoshu($retrun['price'] - $i->price_card);
		if($i->comId==1113){
			$retrun['price_user'] = $i->price_sale;
		}
		$retrun['price_tuan'] = $i->price_tuan;
		$retrun['price_shequ_tuan'] = $i->price_shequ_tuan;
		$retrun['price_user_tuan1'] = getXiaoshu($i->price_tuan-$i->price_card);
		$retrun['price_user_shequ1'] = getXiaoshu($i->price_shequ_tuan-$i->price_card);
		$retrun['price_market'] = $i->price_market;
		$retrun['price_gonghuo'] = $i->price_gonghuo;
		$retrun['title'] = $i->title;
		$retrun['image'] = ispic($i->image);
		$retrun['key_vals'] = $i->key_vals;
		$retrun['images'] = explode('|',$i->originalPic);
		$retrun['cont1'] = str_replace('src="', 'class="lazy" data-original="',preg_replace('/((\s)*(\n)+(\s)*)/','',$i->cont1));
		$retrun['cont2'] = str_replace('src="', 'class="lazy" data-original="',preg_replace('/((\s)*(\n)+(\s)*)/','',$i->cont2));
		$retrun['cont3'] = str_replace('src="', 'class="lazy" data-original="',preg_replace('/((\s)*(\n)+(\s)*)/','',$i->cont3));
	}else{
		$retrun['code'] = 1;
		$retrun['message'] = '成功';
		$retrun['inventoryId'] = 0;
		$retrun['price'] = 0;
		$retrun['price_market'] = 0;
		$retrun['price_gonghuo'] = 0;
		$retrun['images'] = '';
		$retrun['sn'] = '';
		$retrun['title'] = '';
		$retrun['image'] = '';
		$retrun['key_vals'] = '';
		$retrun['kucun'] = 0;
		$retrun['cont1'] = '';
		$retrun['cont2'] = '';
		$retrun['cont3'] = '';
	}
	echo json_encode($retrun,JSON_UNESCAPED_UNICODE);
	exit;
}
function init_pdt_info(){
	global $db,$request;
	$inventoryId = (int)$request['inventoryId'];
	$productId = (int)$request['productId'];
	$comId = (int)$db->get_var("select comId from demo_product_inventory where id=$inventoryId");
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$fenbiao = getFenbiao($comId,20);
	$sql = "select * from order_comment$fenbiao where pdtId=$productId ";
	$countsql = str_replace('*','count(*)',$sql);
	$comment_num = (int)$db->get_var($countsql);
	$sql.=" order by id desc limit 3";
	$comments = $db->get_results($sql);
	$comment_list = array();
	$db_service = getCrmDb();
	if(!empty($comments)){
		foreach ($comments as $i=>$j) {
			$pingjia = array();
			if($_SESSION['if_tongbu']==1){
				$u = $db_service->get_row("select name as nickname,image from demo_user where id=$j->userId");
			}else{
				$u = $db->get_row("select nickname,image from users where id=$j->userId");
			}
			$pingjia['touxiang'] = ispic($u->image);
			$pingjia['username'] = sys_substr($u->nickname,1,false).'**';
			$pingjia['dtTime'] = date("Y-m-d H:i",strtotime($j->dtTime1));
			$j->cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->cont1);
			$pingjia['content'] = '<div style="word-break:break-all;white-space:normal;">'.$j->cont1.'</div>';
			$pingjia['imgs'] = '';
			$pingjia['key_vals'] = $j->key_vals;
			if(!empty($j->images1)){
				$imgs = explode('|',$j->images1);
				foreach ($imgs as $img){
					$pingjia['imgs'] .= '<a href="'.$img.'"><img src="'.$img.'?x-oss-process=image/resize,w_100"></a>';
				}
			}
			for ($i=0;$i < $j->star; $i++) { 
				$pingjia['xing'].='<img src="/skins/default/images/biao_13.png">';
			}
			for ($i=$j->star;$i < 5; $i++) {
				$pingjia['xing'].='<img src="/skins/default/images/biao_14.png">';
			}
			$comment_list[] = $pingjia;
		}
	}
	$channelId = $db->get_var("select ".($_SESSION['if_tongbu']==1?'fenleiId':'channelId')." from demo_product_inventory where id=$inventoryId");
	$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market from demo_product_inventory where comId=$comId and channelId=$channelId and productId<>$productId and if_lingshou=1 group by productId limit 6";
	$products = $db->get_results($sql);
	if(empty($products)){
		$products = array();
	}else{
		foreach ($products as $i=>$p) {
			$products[$i]->price_sale = get_user_zhekou($p->inventoryId,$p->price_sale);
			if($comId==1142 && $_SESSION[TB_PREFIX.'user_level']!=118){
				$products[$i]->title = $db->get_var("select subtitle from demo_product where id=$p->productId");
				$products[$i]->price_sale = '';
			}
		}
	}
	$gouwuche_num = 0;
	if(!empty($userId)){
		$content = $db->get_var("select content from demo_gouwuche where comId=$comId and userId=$userId");
		if(!empty($content))$gouwuche=json_decode($content,true);
		foreach ($gouwuche as $g){
			$gouwuche_num+=$g['num'];
		}
	}
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$areaId = (int)$_SESSION[TB_PREFIX.'sale_area'];
	$pchannel = (int)$db->get_var("select parentId from demo_product_channel where id=$channelId");
	$sql = "select * from yhq where comId=".($_SESSION['if_tongbu']==1?10:$comId)." and endTime>'".date("Y-m-d H:i:s")."' and num>hasnum and status=1 and (mendianIds='' or mendianIds='$comId') and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds)) and (useType=1 or find_in_set($channelId,channels) or find_in_set($pchannel,channels) or find_in_set($inventoryId,pdts)) limit 5";
	//file_put_contents('request.txt',$sql);
	$res = $db->get_results($sql);
	$yhq_list = array();
	if($res){
		if($_SESSION['if_tongbu']==1){
			$comId = 10;
			$userId = (int)$_SESSION['demo_zhishangId'];
			$fenbiao = getFenbiao($comId,20);
		}
      foreach ($res as $key) {
      	if($key->num_day>0){
      		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$key->id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
      		if($hasNum>=$key->num_day)continue;
      	}
      	$tiaojian = '通用';
		if($key->useType>1){
			$tiaojian = $key->channelNames;
			if(!empty($key->pdtNames)){
				$tiaojian.=empty($tiaojian)?$key->pdtNames:','.$key->pdtNames;
			}
		}
		$key->tiaojian = $tiaojian;
      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
      	$key->money = floatval($key->money);
      	$key->man = floatval($key->man);
      	//$userId = $_SESSION['if_tongbu']==1?$_SESSION['demo_zhishangId']:$userId;
      	$lingqu_num = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id");
	    if($lingqu_num>0){
	    	$key->lingqu_id = $db->get_var("select id from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id limit 1");
	    }
	    $key->if_lingqu = $lingqu_num>0?1:0;
	    $key->if_ke_lingqu = $lingqu_num<$key->numlimit?1:0;
      	$yhq_list[] = $key;
      }
  	}
	$retrun['code'] = 1;
	$retrun['message'] = '成功';
	$retrun['comment_num'] = $comment_num;
	$retrun['gwc_num'] = $gouwuche_num;
	$retrun['comment_list'] = $comment_list;
	$retrun['tuijian_list'] = $products;
	$retrun['yhq_list'] = $yhq_list;
	echo json_encode($retrun,JSON_UNESCAPED_UNICODE);
	exit;
}
function add_gouwuche(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	
	$addType = (int)$request['addType'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	if($comId==1142&&$_SESSION[TB_PREFIX.'user_level']!=118){
		die('{"code":0,"message":"您没有购买的权限，请联系我们"}');
	}
	$item = array();
	$item['productId'] = (int)$request['productId'];
	$item['inventoryId'] = (int)$request['inventoryId'];
	$item['num'] = $request['num'];
	$inventory = $db->get_row("select comId,if_kuaidi,channelId,price_sale,price_diancan,price_market,price_gonghuo,image,key_vals,title from demo_product_inventory where id=".$item['inventoryId']);
	if($_SESSION['if_shequ']==2){
		$kucun = get_product_kucun($item['inventoryId'],$inventory->comId);
		if($item['num']>$kucun){
			die('{"code":0,"message":"该商品库存不足~~"}');
		}
	}
	if($_SESSION['peisong_type']==4)$inventory->price_sale=$inventory->price_diancan;
	$item['comId'] = $inventory->comId;
	$item['if_kuaidi'] = $inventory->if_kuaidi;
	$item['channelId'] = $inventory->channelId;
	$item['price_sale'] = $inventory->price_sale;
	$item['price_market'] = $inventory->price_market;
	$item['price_gonghuo'] = $inventory->price_gonghuo;
	$item['title'] = $inventory->title;
	$item['key_vals'] = $inventory->key_vals;
	$item['image'] = $inventory->image;
	if($comId==1009){
		$item['lipinkaType'] = $db->get_var("select lipinkaType from demo_product_inventory where id=".$item['inventoryId']);
	}
	$content = $request['content'];
	if($addType==1||$addType==2||$addType==3){
		if(empty($content)){
			$content = $item['inventoryId'].'@@'.$item['productId'].'@@'.$item['num'].'@@'.$item['comId'];
		}
		add_gouwuche1($content);
	}else{
		$gouwuche = array();
		$g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId");
		if(!empty($g)){
			$content = $g->content;
			if(!empty($content)){
				$gouwuche = json_decode($content,true);
			}
		}
		if(count($gouwuche)>=20){
			die('{"code":0,"message":"添加失败！购物车最多能添加20种商品，请分开下单"}');
		}
		if(array_key_exists($item['inventoryId'],$gouwuche)){
			$gouwuche[$item['inventoryId']]['num'] += $item['num'];
		}else{
			$gouwuche[$item['inventoryId']] = $item;
		}
		$gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
		if(empty($g)){
			$db->query("insert into demo_gouwuche(comId,userId,content) value($comId,$userId,'$gouwucheStr')");
		}else{
			$db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
		}
		$count = 0;
		foreach ($gouwuche as $g){
			$count+=$g['num'];
		}
		echo '{"code":1,"message":"ok","count":'.$count.'}';
		exit;
	}
	
}
function add_gouwuche1($content){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	if(!empty($content)){
		$pdts = explode('||',$content);
		foreach ($pdts as $p){
			$product = explode('@@',$p);
			$item = array();
			$id = $item['inventoryId'] = $product[0];
			$item['productId'] = $product[1];
			$item['num'] = $product[2];
			$item['comId'] = $product[3];
			$kucun = 0;
			$inventory = $db->get_row("select title,key_vals,price_sale,status,if_lingshou,comId,lipinkaType,fenleiId,if_kuaidi from demo_product_inventory where id=$id");
			if($inventory->status!=1||$inventory->if_lingshou!=1){
				echo '{"code":0,"message":"商品：'.$content.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'已下架，不能下单！"}';
				exit;
			}
			if($inventory->price_sale<=0 && $inventory->fenleiId!=387){
				echo '{"code":0,"message":"商品：'.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'是非卖品，不能下单！"}';
				exit;
			}
			$kucun = get_product_kucun($id,$inventory->comId);
			if($kucun<$item['num']){
				echo '{"code":0,"message":"商品：'.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'库存不足，不能下单！"}';
				exit;
			}
			if($comId==1009){
				$item['lipinkaType'] = $inventory->lipinkaType;
			}
			$item['if_kuaidi'] = $inventory->if_kuaidi;
			$gouwuche[$item['inventoryId']] = $item;
		}
	}
	$g = $db->get_var("select comId from demo_gouwuche where userId=$userId and comId=$comId");
	$gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
	if(empty($g)){
		$db->query("insert into demo_gouwuche(comId,userId,content,content1) value($comId,$userId,'','$gouwucheStr')");
	}else{
		$db->query("update demo_gouwuche set content1='$gouwucheStr' where userId=$userId and comId=$comId");
	}
	echo '{"code":1,"message":"ok"}';
	exit;
}
function del_gouwuche(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$id = (int)$request['id'];
	$gouwuche = array();
	$g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId");
	if(!empty($g)){
		$content = $g->content;
		if(!empty($content)){
			$gouwuche = json_decode($content,true);
		}
	}
	unset($gouwuche[$id]);
	$gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
	$db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
	$count = 0;
	foreach ($gouwuche as $g){
		$count+=$g['num'];
	}
	echo '{"code":1,"message":"ok","count":'.$count.'}';
	exit;
}
function edit_gouwuche_num(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$id = (int)$request['id'];
	$num = (int)$request['num'];
	$gouwuche = array();
	$g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId");
	if(!empty($g)){
		$content = $g->content;
		if(!empty($content)){
			$gouwuche = json_decode($content,true);
			$gouwuche[$id]['num'] = $num;
		}
	}
	$gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
	$db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
	$count = 0;
	foreach ($gouwuche as $g){
		$count+=$g['num'];
	}
	echo '{"code":1,"message":"ok","count":'.$count.'}';
	exit;
}
function qingkong_gouwuche(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$db->query("update demo_gouwuche set content='' where userId=$userId and comId=$comId");
	echo '{"code":1,"message":"ok","count":0}';
	exit;
}
//加餐
function jiacan(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$order_fenbiao = $fenbiao = getFenbiao($comId,20);
	$contents = $db->get_row("select content from demo_gouwuche where userId=$userId and comId=$comId");
	$gouwuche=json_decode($contents->content,true);
	if(empty($gouwuche)){
		die('{"code":0,"message":"没检测到加餐商品"}');
	}
	$orderId = (int)$_SESSION['demo_jiacan_id'];
	$jiacan_type = (int)$_SESSION['demo_jiacan_type'];
	if(empty($orderId) || empty($jiacan_type)){
		die('{"code":0,"message":"系统错误，请从会员中心处操作加餐"}');
	}
	$order = $db->get_row("select * from order$fenbiao where id=$orderId");
	if($order->status!=-5){
		die('{"code":0,"message":"该订单目前状态不支持加餐服务"}');
	}
	$num = 0;
    $zong_price = 0;
    $remark = $request['remark'];
    $pdtstr = '';
    $product_json_arry = array();
    if($jiacan_type==1 && !empty($order->jiacan_json)){
    	$product_json_arry = json_decode($order->jiacan_json,true);
    }else if($jiacan_type==2 && !empty($order->waimai_json)){
    	$product_json_arry = json_decode($order->waimai_json,true);
    }
    $new_add_arry = array();//新增商品列表
    $has_ids = array();
    foreach ($gouwuche as $i=>$g) {
    	$has_ids[] = $g['inventoryId'];
        $nowProductId = $g['productId'];
        $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_diancan,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=".$g['inventoryId']);
        $inventory->price_sale=$inventory->price_diancan;
    	if($inventory->status!=1){
        	die('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
        }
        $kucun = get_product_kucun($g['inventoryId'],$inventory->comId);
        if($g['num']>$kucun)$g['num']=$kucun;
        if($kucun<=0){
        	die('{"code":0,"message":"商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”库存不足"}');
        }
        $price = get_user_zhekou($inventory->id,$inventory->price_sale);
        $zong_price+=$price*$g['num'];
        $num+=(int)$g['num'];
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $g['productId'];
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['weight'] = $inventory->weight;
        $pdt['num'] = $g['num'];
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $price;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
        $pdt['price_card'] = $inventory->price_card;
        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
        $units_arr = json_decode($units);
        $pdt['unit'] = $units_arr[0]->title;
        $product_json_arry[] = $pdt;
        $new_add_arry[] = $pdt;
    }
    $price_json = json_decode($order->price_json,true);
    $price_json['goods']['price'] = getXiaoshu($price_json['goods']['price']+$zong_price,2);
    $update_order = array();
    $update_order['id'] = $order->id;
    $update_order['price'] = getXiaoshu($order->price+$zong_price,2);
    $update_order['pdtNums'] = $order->pdtNums+$num;
    $update_order['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
    if($jiacan_type==1){
    	$update_order['jiacan_json'] = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
    }else{
    	$update_order['waimai_json'] = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
    }
    if(!empty($remark)){
    	$update_order['remark'] = $order->remark.'；'.$remark;
    }
    $db->insert_update('order'.$fenbiao,$update_order,'id');
    foreach ($new_add_arry as $detail) {
		$pdt = new StdClass();
		$pdt->sn = $detail['sn'];
		$pdt->title = $detail['title'];
		$pdt->key_vals = $detail['key_vals'];
		$order_detail = array();
		$order_detail['comId'] = (int)$order_comId;
		$order_detail['mendianId'] = 0;
		$order_detail['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$order_detail['orderId'] = $order_id;
		$order_detail['inventoryId'] = $detail['id'];
		$order_detail['productId'] = $detail['productId'];
		$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
		$order_detail['num'] = $detail['num'];
		$order_detail['unit'] = $detail['unit'];
		$order_detail['unit_price'] = $detail['price_sale'];
		$db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
	}
	print_jiacan_order($order,$jiacan_type,$new_add_arry);
	$db->query("update demo_gouwuche set content='' where userId=$userId and comId=$comId");
	unset($_SESSION[TB_PREFIX.'jiacan_id']);
	unset($_SESSION[TB_PREFIX.'jiacan_type']);
	die('{"code":1,"message":"操作成功","order_id":'.$order->id.'}');
}
//下单
function create(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($_SESSION['demo_comId'],20);
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	if($_SESSION['demo_comId']==1009){
		$lipinkaType = (int)$request['lipinkaType'];
		$lipinka_tiqu_type = (int)$request['lipinka_tiqu_type'];
		$tiqu_phone = $request['tiqu_phone'];
		$tiqu_yzm = $request['tiqu_yzm'];
		if($lipinka_tiqu_type==2 && ($tiqu_yzm!=$_SESSION['yzm'] || empty($tiqu_yzm))){
			die('{"code":0,"message":"验证码不正确，请重新输入"}');
		}
	}
	$shequ_id = (int)$request['shequ_id'];
	$shezhi = $db->get_row("select time_pay,storeId,user_bili,shangji_bili,fanli_type,time_tuan,shequ_yunfei,peisong_time_money,shang_bili from demo_shezhi where comId=".($shequ_id>0?$_SESSION['demo_comId']:$comId));
	$time_pay = $shezhi->time_pay;
	$time_pay+=1;
	$user_level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$address_id = (int)$request['address_id'];
	$address = $db->get_row("select * from user_address where id=$address_id");
	$areaId = (int)$address->areaId;
	$yhq_id = (int)$request['yhq_id'];
	$yushouId = (int)$request['yushouId'];
	$xinren = (int)$request['xinren'];
	$tuan_type = (int)$request['tuan_type'];
	$tuan_id = (int)$request['tuan_id'];
	$table_id = (int)$request['table_id'];
	$contents = $db->get_row("select content,content1 from demo_gouwuche where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
	if(!empty($contents->content1)){
		$gouwuche=json_decode($contents->content1,true);
	}else{
		if($request['waimai']==1){
			$gouwuche=json_decode($contents->content,true);
			if(empty($gouwuche)){
				die('{"code":0,"message":"没检测到下单商品"}');
			}
		}else{
			file_put_contents('request.txt',"select content,content1 from demo_gouwuche where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
			die('{"code":0,"message":"没检测到下单商品"}');
		}
		
	}
	$peisong_type = (int)$request['peisong_type'];
	//$zhekou = get_user_zhekou();
	$check_pay_time = strtotime("+$time_pay minutes");
	if($peisong_type==4){
		$check_pay_time = strtotime("+12 hours");
	}
    $num = 0;
    $zong_price = 0;
    $zong_gonghuo_price = 0;
    $zong_baozhuang = 0;
    $zong_weight = 0;
    $remark = $request['remark'];
    $pdtstr = '';
    $product_json_arry = array();
    $has_ids = array();
    
    
    if($peisong_type==3){
    	$shequ_id = 0;
    }
    $shequ_user_id = 0;
    if($shequ_id>0){
    	$shequ_user_id = $db->get_var("select userId from demo_shequ where id=$shequ_id");
    }
    //返利信息
    $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0,'shequ_fanli'=>0,"shequ_id"=>$shequ_user_id,"buyer_fanli"=>0);
    $shop = $db->get_row("select tuijianren,tuijian_bili,pay_info,pingtai_fanli from demo_shops where comId=".$_SESSION['demo_comId']);
    if($tuan_type>0){
    	if($tuan_id>0){
    		$tuanzhang = (int)$db->get_var("select tuanzhang from demo_tuan where id=$tuan_id and status=0");
			if(empty($tuanzhang)){
				die('{"code":0,"message":"该团购已结束，请参与其他的团购吧~~"}');
			}
    		$fanli_json['shangji'] = $tuanzhang;
    		if($comId==10){
    			$u = $db_service->get_row("select shangji,tuan_id from demo_user where id=$tuanzhang");
    			$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
    		}else{
    			$u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=$tuanzhang");
    			$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
    		}
    	}else{
    		$fanli_json['shangji'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
    		if($comId==10){
    			$u = $db_service->get_row("select shangji,tuan_id from demo_user where id=$userId");
    			$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
    		}else{
    			$u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
    			$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
    		}
    	}
    }else if($comId==10){
    	$u = $db_service->get_row("select shangji,shangshangji,tuan_id from demo_user where id=$userId");
    	$fanli_json['shangji'] = $u->shangji;
    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;
    }else{
    	$u = $db->get_row("select shangji,shangshangji,tuan_id,if_tuanzhang from users where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
    	$fanli_json['shangji'] = $u->shangji;
    	if($comId==1135 && $u->if_tuanzhang==1){
    		$fanli_json['shangji'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
    	}
    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;//根据返利类型设定返利的上上级会员
    }
    $fanli_json['tuijian'] = $shop->tuijianren;
    //计算社区返利和团长返利
    $fanli_shequ =0;$fanli_tuanzhang = 0;
    foreach ($gouwuche as $i=>$g) {
    	$has_ids[] = $g['inventoryId'];
        $nowProductId = $g['productId'];
        $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_diancan,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=".$g['inventoryId']);
        if($tuan_type>0){
        	$tuan_inventory = $inventory;
        }
        $order_comId = $inventory->comId;
        if(!empty($yushouId)){
        	$now = date("Y-m-d H:i:s");
        	$yushou = $db->get_row("select * from yushou where id=$yushouId and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
            if(empty($yushou)){
                die('{"code":0,"message":"预售活动已结束"}');
            }
            $left = $yushou->num - $yushou->num_saled;
            if($g['num']>$left){
            	die('{"code":0,"message":"库存不足，下单失败!"}');
            }
            $price_json = json_decode($yushou->price_json,true);
           	$price = $price_json[0]['price'];
            if($yushou->type==2){
                $columns = array_column($price_json,'num');
                array_multisort($columns,SORT_DESC,$price_json);
                foreach ($price_json as $val) {
                    if($yushou->num_saled>=$val['num']){
                        $price = $val['price'];
                        break;
                    }
                }
            }
            $zong_price = $price*$g['num'];
            $db->query("update yushou set num_saled=num_saled+".$g['num']." where id=$yushouId");
        }else if($xinren==1){
        	if($g['num']!=1){
            	die('{"code":0,"message":"新人专享只能购买一个商品！"}');
            }
            if(count($gouwuche)>1){
            	die('{"code":0,"message":"新人专享每次只能下单一个商品"}');
            }
            $fenbiao = getFenbiao($inventory->comId,20);
            $if_order = $db->get_var("select id from order$fenbiao where comId=$inventory->comId and userId=$userId and status!=-1 limit 1");
            
            if($if_order>0){
                echo '<script>alert("只有没有下过单的会员可以享受新人专享优惠！您不符合条件。");go_prev_page();</script>';
                exit;
            }
            $price = $db->get_var("select money from demo_xinren_discount where inventoryId=".$g['inventoryId']." and comId=$inventory->comId limit 1");
            if(empty($price)){
            	die('{"code":0,"message":"该活动已结束!"}');
            }
            $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
            $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'","comId":'.$inventory->comId.'}}';
            $zong_price+=$price;
        }else{
        	if($inventory->status!=1){
	        	die('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
	        }
	        $kucun = get_product_kucun($g['inventoryId'],$inventory->comId);
	        if($g['num']>$kucun)$g['num']=$kucun;
	        if($kucun<=0){
	        	die('{"code":0,"message":"商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”库存不足"}');
	        }
	        if($tuan_type==1){
	        	$price = $inventory->price_tuan;
	        }else if($tuan_type==2){
	        	$price = $inventory->price_shequ_tuan;
	        }else{
	        	if($peisong_type==4)$inventory->price_sale=$inventory->price_diancan;
	        	$price = get_user_zhekou($inventory->id,$inventory->price_sale);
	        }
	        if($comId==1121){
	        	$addrows = $db->get_var("select addrows from demo_product where id=".$g['productId']);
	            $addrows_arr = json_decode($addrows,true);
	            $zhuangxiangshu = $addrows_arr['数量'];
		        $zong_price+=$price*$g['num'];
	        }else{
	        	$zong_price+=$price*$g['num'];
	        }
	        
	        $zong_baozhuang+=$inventory->price_gonghuo;
        }
        $zong_gonghuo_price+=$inventory->price_cost*$g['num'];
        $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
        $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
        $num+=(int)$g['num'];
        
        $zong_weight+=$inventory->weight*$g['num'];
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $g['productId'];
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['weight'] = $inventory->weight;
        $pdt['num'] = $g['num'];
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $price;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
        $pdt['price_card'] = $inventory->price_card;
        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
        $units_arr = json_decode($units);
        $pdt['unit'] = $units_arr[0]->title;
        $product_json_arry[] = $pdt;
        if(!empty($yushouId)){
        	break;
        }
        $fanli_shequ +=$inventory->fanli_shequ*$g['num'];
        $fanli_tuanzhang +=$inventory->fanli_tuanzhang*$g['num'];
    }
    
    //价格相关
    $price_json = new StdClass();
    $price_json_product = new StdClass();
    $price_json_product->price = $zong_price;
    $price_json_product->desc = '';
    $price_json->goods = $price_json_product;
    if($_SESSION['if_shequ']==2 && $peisong_type!=4){
    	$zong_price+=$zong_baozhuang;
    	$price_json_baozhuang = new StdClass();
	    $price_json_baozhuang->price = $zong_baozhuang;
	    $price_json_baozhuang->desc = '';
	    $price_json->baozhuang = $price_json_baozhuang;
    }
    if(!empty($pdtstr)){
        $pdtstr = substr($pdtstr,1);
        $pdt_arr = json_decode('['.$pdtstr.']');
    }
 	$cuxiao_money = $zong_price;
 	if(!empty($lpk_id) && !empty($lpk_kedi)){
 		$cuxiao_money-=$lpk_kedi;
 	}
 	//限购判断
 	$xiangou_sql = "insert into cuxiao_pdt_buy(cuxiao_id,inventoryId,userId,num,comId,orderId) values";
 	$xiangou_sql1 = '';
    $pdt_cuxiao = get_pdt_cuxiao($pdt_arr,$areaId,$cuxiao_money);
    if(!empty($pdt_cuxiao['cuxiao_ids'])){
    	foreach ($pdt_cuxiao['cuxiao_ids'] as $key => $cuxiaoId) {
    		$cuxiao_pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$cuxiaoId");
    		$pdtArr = explode(',',$cuxiao_pdtIds);
    		foreach ($gouwuche as $i=>$g) {
    			$inventId = $g['inventoryId'];
    			$num = (int)$g['num'];
    			if(in_array($inventId,$pdtArr)){
    				$buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
    				$xiangou_num = (int)$db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiaoId and inventoryId=$inventId");
    				if($xiangou_num>0 && ($buy_num+$num)>$xiangou_num){
    					$inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card from demo_product_inventory where id=$inventId");
    					die('{"code":0,"message":"下单失败，商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”限购'.$xiangou_num.'份！您还可购买'.($xiangou_num-$buy_num).'份"}');
    				}else{
    					if($buy_num>0){
    						$db->query("update cuxiao_pdt_buy set num=num+$num where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
    					}else{
    						$xiangou_sql1.=",($cuxiaoId,$inventId,$userId,$num,$comId,order_id)";
    					}
    				}
    			}
    		}
    	}
    }
    
    if($pdt_cuxiao['jian']>0){
        $zong_price-=$pdt_cuxiao['jian'];
        $price_json_cuxiao = new StdClass();
	    $price_json_cuxiao->price = $pdt_cuxiao['jian'];
	    $price_json_cuxiao->desc = $pdt_cuxiao['cuxiao_title'];
	    $price_json->cuxiao = $price_json_cuxiao;
    }
    //订单促销
    $order_cuxiao = get_order_cuxiao($zong_price,$areaId);
    if($order_cuxiao['jian']>0){
        $zong_price-=$order_cuxiao['jian'];
        $price_json_order = new StdClass();
	    $price_json_order->price = $order_cuxiao['jian'];
	    $price_json_order->desc = $order_cuxiao['cuxiao_title'];
	    $price_json->cuxiao_order = $price_json_order;
    }
    //返利相关
    $zongfanli = $zong_gonghuo_price;//商家返利
    if($_SESSION['comId']==10 && $zongfanli>0){
    	$fanli_json['shop_fanli'] = $zongfanli;
    	/*$user_fanli = intval($zongfanli*$shezhi->user_bili)/100;
    	$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
    	if(!empty($fanli_json['shangji'])){
    		$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
    	}
    	if(empty($fanli_json['shangshangji'])){
    		$fanli_json['shangshangji_fanli'] = 0;
    	}
    	if(!empty($shop->tuijianren) && !empty($shop->tuijian_bili)){
    		$fanli_json['tuijian_fanli'] = intval($zongfanli*$shop->tuijian_bili)/100;
    	}
    	//$fanli_json['shop_fanli'] = $zong_gonghuo_price;
    	$fanli_json['pingtai_fanli'] = $zongfanli-$fanli_json['shangshangji_fanli']-$fanli_json['shangji_fanli']-$fanli_json['tuijian_fanli'];*/
    }else{
    	$fanli_json['shequ_fanli'] = $fanli_shequ;
    	$fanli_json['user_type'] = 2;
		$user_fanli = $fanli_tuanzhang;
		$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
		$fanli_json['buyer_fanli'] = intval($user_fanli * $shezhi->shang_bili)/100;
		if(!empty($fanli_json['shangji'])){
			$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli']-$fanli_json['buyer_fanli'];
		}
		if(empty($fanli_json['shangshangji'])){
			$fanli_json['shangshangji_fanli'] = 0;
		}
    }
    //获取优惠券
    $now = date("Y-m-d H:i:s");
    $yhq = $db->get_row("select id,yhqId,title,man,jian,jiluId from user_yhq$fenbiao where id=$yhq_id and userId=$userId and status=0 and endTime>='$now' and startTime<='$now'");
    if(!empty($yhq)){
        $zong_price-=$yhq->jian;
        $price_json_yhq = new StdClass();
	    $price_json_yhq->price = $yhq->jian;
	    $price_json_yhq->desc = $yhq->id;
	    $price_json->yhq = $price_json_yhq;
	    if($zong_price<0)$zong_price=0;
    }
    //获取运费
    if($shequ_id>0){
    	$yunfei = 0;
    	if($peisong_type==2 && !empty($shezhi->shequ_yunfei)){
    		$shequ_yunfei = json_decode($shezhi->shequ_yunfei);
    		$peisong_time = $request['peisong_time'];
    		$peisong_time_money = array();
            if(!empty($shezhi->peisong_time_money)){
                $peisong_time_money = json_decode($shezhi->peisong_time_money,true);
            }
            $peisongfei = isset($peisong_time_money[$peisong_time]['peisong_money'])?$peisong_time_money[$peisong_time]['peisong_money']:$shequ_yunfei->peisong_money;
            $peisongfei_man = isset($peisong_time_money[$peisong_time]['peisong_man'])?$peisong_time_money[$peisong_time]['peisong_man']:$shequ_yunfei->peisong_man;
    		$yunfei = $peisongfei;
    		if($peisongfei_man>0 && $zong_price>=$peisongfei_man){
    			$yunfei = 0;
    		}
    	}
    }else{
    	$yunfei = get_yunfei($pdt_arr,$zong_price,$areaId);
    }
    
    
    if(!empty($yunfei)){
        $zong_price+=$yunfei;
        $price_json_yunfei = new StdClass();
	    $price_json_yunfei->price = $yunfei;
	    $price_json_yunfei->desc = '';
	    $price_json->yunfei = $price_json_yunfei;
    }
    if(!empty($pdt_cuxiao['zengpin'])){
		foreach ($pdt_cuxiao['zengpin'] as $zeng) {
			$inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
	        $pdt = array();
	        $pdt['id'] = $inventory->id;
	        $pdt['productId'] = $inventory->productId;
	        $pdt['title'] = $inventory->title;
	        $pdt['sn'] = $inventory->sn;
	        $pdt['key_vals'] = $inventory->key_vals;
	        $pdt['weight'] = $inventory->weight;
	        $pdt['num'] = $zeng['num'];
	        $pdt['price_sale'] = 0;
	        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
	        $pdt['price_card'] = 0;
	        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
	        $units_arr = json_decode($units);
	        $pdt['unit'] = $units_arr[0]->title;
	        $product_json_arry[] = $pdt;
	        $num+=$zeng['num'];
        	$zong_weight+=$inventory->weight*$zeng['num'];
		}
	}
	if(!empty($order_cuxiao['zengpin'])){
		foreach ($order_cuxiao['zengpin'] as $zeng) {
			$inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
	        $pdt = array();
	        $pdt['id'] = $inventory->id;
	        $pdt['productId'] = $inventory->productId;
	        $pdt['title'] = $inventory->title;
	        $pdt['sn'] = $inventory->sn;
	        $pdt['key_vals'] = $inventory->key_vals;
	        $pdt['weight'] = $inventory->weight;
	        $pdt['num'] = $zeng['num'];
	        $pdt['price_sale'] = 0;
	        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
	        $pdt['price_card'] = 0;
	        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
	        $units_arr = json_decode($units);
	        $pdt['unit'] = $units_arr[0]->title;
	        $product_json_arry[] = $pdt;
	        $num+=$zeng['num'];
        	$zong_weight+=$inventory->weight*$zeng['num'];
		}
	}
	$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
    $jifen = get_order_jifen($pdt_arr,$zong_price);
    $storeId = get_fahuo_store($areaId,$order_comId);
	$shouhuo_json = array();
	if($peisong_type==4){
		$address_id = 0;
		$shouhuo_json['收件人'] = $_SESSION['demo_table_title'];
		$shouhuo_json['详细地址'] = $_SESSION['demo_shequ_title'].$_SESSION['demo_table_title'];
	}else{
		if(!empty($address)){
			$shouhuo_json['收件人'] = $address->name;
			$shouhuo_json['手机号'] = $address->phone;
			$shouhuo_json['所在地区'] = $address->areaName;
			$shouhuo_json['详细地址'] = $address->address;
		}else if(!empty($shequ_id) && $tuan_type==2){
			$shequ = $db->get_row("select * from demo_shequ where id=$shequ_id");
			$shouhuo_json['收件人'] = $shequ->name;
			$shouhuo_json['手机号'] = $shequ->phone;
			$shouhuo_json['所在地区'] = $db->get_var("select title from demo_area where id=$shequ->areaId");
			$shouhuo_json['详细地址'] = $shequ->address;
		}
	}
	if($tuan_type>0 && $tuan_id==0){
		$tuan = array();
		$tuan['comId'] = (int)$_SESSION['demo_comId'];
		$tuan['inventoryId'] = $tuan_inventory->id;
		$tuan['productId'] = $tuan_inventory->productId;
		$tuan['type'] = $tuan_type;
		$tuan['pdt_comId'] = $tuan_inventory->comId;
		$tuan['user_num'] = $tuan_inventory->tuan_num;
		$tuan['tuanzhang'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$tuan['addressId'] = $address_id;
		$tuan['dtTime'] = date("Y-m-d H:i:s");
		$tuan['endTime'] = date("Y-m-d H:i:s",strtotime('+ '.$shezhi->time_tuan.' hours'));
		$tuan['shouhuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
		$tuan_id = $db->insert_update('demo_tuan',$tuan,'id');
		$time_tuan = $shezhi->time_tuan;
		$check_tuan_time = strtotime("+$time_tuan hours");
		$timed_task = array();
		$timed_task['comId'] = (int)$_SESSION['demo_comId'];
		$timed_task['dtTime'] = $check_tuan_time;
		$timed_task['router'] = 'order_checkTuan';
		$timed_task['params'] = '{"tuan_id":'.$tuan_id.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
	}
	$order = array();
	$order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
	$order['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$order['zhishangId'] = $userId;
	}
	$order['comId'] = (int)$order_comId;
	$order['mendianId'] = 0;
	$order['yushouId'] = $yushouId;
	$order['type'] = $tuan_type>0?$tuan_type:1; //2社区团 1普通订单或普通团单
	if($peisong_type==4){
		$order['type'] = 3;
	}
	$order['status'] = -5;//待支付
	$order['dtTime'] = date("Y-m-d H:i:s");
	$order['remark'] = $remark;
	$order['pay_endtime'] = date("Y-m-d H:i:s",$check_pay_time);
	$order['price'] = $zong_price;
	if($yushou->paytype==2){
		$order['price_dingjin'] = $yushou->dingjin;
	}
	$order['inventoryId'] = (int)$tuan_inventory->id;
	$order['storeId'] = $storeId;
	$order['pdtNums'] = $num;
	$order['pdtChanel'] = 0;
	$order['ifkaipiao'] = 0;
	$order['weight'] = $zong_weight;
	$order['jifen'] = $jifen;
	$order['areaId'] = $areaId;
	$order['address_id'] = $address_id;
	$order['product_json'] = $product_json;
	$order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
	$order['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
	$order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
	$order['shangji'] = $fanli_json['shangji'];
	$order['shangshangji'] = $fanli_json['shangshangji'];
	$order['ifkaipiao'] = (int)$request['if_fapiao'];
	if($request['if_fapiao']>0){
		$fapiao_json = array();
		$fapiao_json['发票类型'] = $request['fapiao_leixing'];
		$fapiao_json['抬头类型'] = $request['fapiao_type']==1?'个人':'公司';
		if($request['fapiao_type']==2){
			$fapiao_json['公司名称'] = $request['fapiao_com_title'];
			$fapiao_json['识别码'] = $request['fapiao_shibiema'];
			$fapiao_json['注册地址'] = $request['fapiao_address'];
			$fapiao_json['注册电话'] = $request['fapiao_phone'];
			$fapiao_json['开户银行'] = $request['fapiao_bank_name'];
			$fapiao_json['银行账号'] = $request['fapiao_bank_card'];
		}
		$fapiao_json['发票明细'] = $request['fapiao_cont'];
		$fapiao_json['收票人手机'] = $request['shoupiao_phone'];
		$fapiao_json['收票人邮箱'] = $request['shoupiao_email'];
		$order['fapiao_json'] = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
		if($request['fapiao_id']==0){
			$fapiao = array();
			$fapiao['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
			$fapiao['comId'] = $comId;
			$fapiao['type'] = 1;
			$fapiao['com_title'] = trim($request['fapiao_com_title']);
			$fapiao['shibiema'] = trim($request['fapiao_shibiema']);
			$fapiao['shoupiao_phone'] = trim($request['shoupiao_phone']);
			$fapiao['shoupiao_email'] = trim($request['shoupiao_email']);
			$db->insert_update('user_fapiao',$fapiao,'id');
		}
	}
	if($_SESSION['demo_comId']==1009){
		$order['lipinkaType'] = $lipinkaType;
		if($lipinkaType==2){
			$lipinkaInfo = array();
			$lipinkaInfo['tiqu_type']=$lipinka_tiqu_type;
			$lipinkaInfo['tiqu_phone']=$tiqu_phone;
			$order['lipinkaInfo'] = json_encode($lipinkaInfo,JSON_UNESCAPED_UNICODE);
		}
	}
	$order['if_zong'] = $_SESSION['demo_comId']==10?1:0;
	$order['shequ_id'] = $shequ_id;
	$order['peisong_type'] = (int)$request['peisong_type'];
	$order['peisong_time'] = $request['peisong_time'];
	$order['tuan_id'] = $tuan_id;
	$order['table_id'] = $table_id;
	$order_fenbiao = getFenbiao($order_comId,20);
	//file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
	$db->insert_update('order'.$order_fenbiao,$order,'id');
	$order_id = $db->get_var("select last_insert_id();");
	if(!empty($xiangou_sql1)){
		$xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
    	$xiangou_sql1 = substr($xiangou_sql1,1);
    	$db->query($xiangou_sql.$xiangou_sql1);
    }
    if($peisong_type!=4){
		$timed_task = array();
		$timed_task['comId'] = (int)$_SESSION['demo_comId'];
		$timed_task['dtTime'] = $check_pay_time;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$order_id.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
	}
	if(!empty($yhq_id)){
		$db->query("update user_yhq$fenbiao set status=1,orderId=$order_id where id=$yhq_id");
	}
	$gouwuches = array();
	if(!empty($contents->content)){
		$gouwuches = json_decode($contents->content,true);
	}
	foreach ($product_json_arry as $detail) {
		$pdt = new StdClass();
		$pdt->sn = $detail['sn'];
		$pdt->title = $detail['title'];
		$pdt->key_vals = $detail['key_vals'];
		$order_detail = array();
		$order_detail['comId'] = (int)$order_comId;
		$order_detail['mendianId'] = 0;
		$order_detail['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$order_detail['orderId'] = $order_id;
		$order_detail['inventoryId'] = $detail['id'];
		$order_detail['productId'] = $detail['productId'];
		$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
		$order_detail['num'] = $detail['num'];
		$order_detail['unit'] = $detail['unit'];
		$order_detail['unit_price'] = $detail['price_sale'];
		if($_SESSION['demo_comId']==1009){
			$order_detail['lipinkaId'] = $db->get_var("select lipinkaId from demo_product_inventory where id=".$detail['id']);
		}
		$db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
		if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
		if($tuan_type==0){
			$db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
		}
	}
	$lpk_id = (int)$request['lpk_id'];
	$lpk_kedi = $request['lpk_kedi'];
	if(!empty($lpk_id) && !empty($lpk_kedi)){
    	card_pay($order_id,$_SESSION['demo_comId'],$lpk_id,$lpk_kedi);
    }
	$content = '';
	if(!empty($gouwuches)){
		$content=json_encode($gouwuches,JSON_UNESCAPED_UNICODE);
	}
	$db->query("update demo_gouwuche set content='$content' where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
	if($peisong_type==4){
		$diancan_order = $db->get_row("select * from order$fenbiao where id=$order_id"); 
		$yuyue = $db->get_row("select id,tableId,money,order_id from demo_shequ_yuyue where comId=$comId and yuyue_date='".date("Y-m-d")."' and userId=$userId and status=1 limit 1");
		if($yuyue->money>0 && $yuyue->order_id==0 && $diancan_order->table_id==$yuyue->tableId){
			$o = array();
			$o['id'] = $order_id;
			$o['price_payed'] = $yuyue->money+$diancan_order->price_payed;
			$pay_json = array();
			if(!empty($diancan_order->pay_json)){
				$pay_json = json_decode($diancan_order->pay_json,true);
			}
			$pay_json['yuyue']['price'] = $yuyue->money;
			/*if($order->price_dingjin==0){
				
			}else{
				$pay_json['dingjin']['price'] = $order->price;
				$pay_json['dingjin']['paytype'] = '礼品卡，卡号：'.$u->cardId;
			}*/
			$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
			$db->insert_update('order'.$fenbiao,$o,'id');
			$db->query("update demo_shequ_yuyue set order_id=$order_id where id=$yuyue->id");
		}
		print_diancan_order($diancan_order);
	}
	die('{"code":1,"message":"下单成功","order_id":'.$order_id.',"comId":'.$order_comId.'}');
}
//知商下单
function create_zong(){
	global $db,$request,$db_service;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$shezhi = $db->get_row("select time_pay,storeId,fanli_type from demo_shezhi where comId=$comId");
	$time_pay = $shezhi->time_pay;
	$time_pay+=1;
	$user_level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$address_id = (int)$request['address_id'];
	$address = $db->get_row("select * from user_address where id=$address_id");
	$areaId = $address->areaId;
	$yhq_id = (int)$request['yhq_id'];
	$yushouId = (int)$request['yushouId'];
	$lingyuangou = (int)$request['lingyuangou'];
	if($lingyuangou==1){
	    $users_yaoqing = $db->get_row("select * from users_yaoqing where userId=$userId");
	    /*if($users_yaoqing->if_buy==1){
	    	die('{"code":0,"message":"您已经使用过助力购的权限购买过商品了，助力购权限只能使用一次！"}');
	    }*/
	    $buy_ids = array();
    	if(!empty($users_yaoqing->buy_ids))$buy_ids = explode(',',$users_yaoqing->buy_ids);
	    $yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
	    $yaoqing_rules = json_decode($yaoqing_rule);
	    $guizes = $yaoqing_rules->guizes;
	}
	$contents = $db->get_row("select content,content1 from demo_gouwuche where userId=$userId and comId=$comId");
	if(!empty($contents->content1))$gouwuche=json_decode($contents->content1,true);
	$gouwuches = array();
	if(!empty($contents->content)){
		$gouwuches = json_decode($contents->content,true);
	}
	//$zhekou = get_user_zhekou();
	$check_pay_time = strtotime("+$time_pay minutes");
	if($lingyuangou==1 && count($gouwuche)>1){
		die('{"code":0,"message":"助力购只能下单一个商品~~"}');
	}
	$comIds = array();
	foreach ($gouwuche as $i=>$g) {
		foreach ($guizes as $guize){
            if($g['inventoryId']==$guize->inventoryId){
                if($users_yaoqing->nums<$guize->man){
                	die('{"code":0,"message":"您没达到购买此商品助力购的资格！"}');
                }
                if(in_array($guize->id,$buy_ids)){
					die('{"code":0,"message":"您已经使用过助力购的权限购买过这个商品了！"}');
	            }
	            $zhuligou_id = $guize->id;
            }
        }
		if(!in_array($g['comId'],$comIds)){
			$comIds[] = $g['comId'];
		}
	}
	$db_service = getCrmDb();
	$u = $db_service->get_row("select shangji,shangshangji,tuan_id from demo_user where id=$userId");
	$pay_zong = 0;
	$pay_orderInfo = array();
	$pay_card = 0;
	$lipinka_id = (int)$request['lipinka_id'];
	$lipinka_kedi = $request['lipinka_kedi'];
	if(!empty($lipinka_id)){
		$lipinka = $db->get_row("select mendianId,channels,pdts from lipinka where id=$lipinka_id");
	}
	foreach ($comIds as $index=>$cid){
		$order_fenbiao = getFenbiao($cid,20);
	    $num = 0;
	    $zong_price = 0;
	    $zong_gonghuo_price = 0;
	    $price_card = 0;
	    $price_lipinka = 0;
	    $zong_weight = 0;
	    $remark = $request['remark'];
	    $pdtstr = '';
	    $product_json_arry = array();
	    $has_ids = array();
	    //返利信息
	    $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0);
    	$shop = $db->get_row("select tuijianren,tuijian_bili,pay_info from demo_shops where comId=$cid");
    	$fanli_json['shangji'] = $u->shangji;
    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;
    	$fanli_json['tuijian'] = $shop->tuijianren;
	    foreach ($gouwuche as $i=>$g) {
	    	if($g['comId']==$cid){
		    	$has_ids[] = $g['inventoryId'];
		        $nowProductId = $g['productId'];
		        $inventory = $db->get_row("select id,fenleiId,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost from demo_product_inventory where id=".$g['inventoryId']);
		        if(!empty($yushouId)){
		        	$now = date("Y-m-d H:i:s");
		        	$yushou = $db->get_row("select * from yushou where id=$yushouId and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
		            if(empty($yushou)){
		                die('{"code":0,"message":"预售活动已结束"}');
		            }
		            $left = $yushou->num - $yushou->num_saled;
		            if($g['num']>$left){
		            	die('{"code":0,"message":"库存不足，下单失败!"}');
		            }
		            $price_json = json_decode($yushou->price_json,true);
		           	$price = $price_json[0]['price'];
		            if($yushou->type==2){
		                $columns = array_column($price_json,'num');
		                array_multisort($columns,SORT_DESC,$price_json);
		                foreach ($price_json as $val) {
		                    if($yushou->num_saled>=$val['num']){
		                        $price = $val['price'];
		                        break;
		                    }
		                }
		            }
		            $zong_price = $price*$g['num'];
		            $db->query("update yushou set num_saled=num_saled+".$g['num']." where id=$yushouId");
		        }else{
		        	if($inventory->status!=1){
			        	die('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
			        }
			        $kucun = get_product_kucun($g['inventoryId'],$inventory->comId);
			        if($g['num']>$kucun)$g['num']=$kucun;
			        if($kucun<=0){
			        	die('{"code":0,"message":"商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”库存不足"}');
			        }
                    $price = get_user_zhekou($inventory->id,$inventory->price_sale);
			        $zong_price+=$price*$g['num'];
		        }
		        $zong_gonghuo_price+=$inventory->price_cost*$g['num'];
		        $pay_card +=$inventory->price_card*$g['num'];
		        $price_card+=$inventory->price_card*$g['num'];
		        if(!empty($lipinka)){
		        	$jilupdts = array();$jiluchanels = array();
					if(!empty($lipinka->pdts))$jilupdts = explode(',', $lipinka->pdts);
					if(!empty($lipinka->channels))$jiluchanels = explode(',', $lipinka->channels);
					if(in_array($inventory->id,$jilupdts) || in_array($inventory->fenleiId,$jiluchanels) || (empty($jilupdts) && empty($jiluchanels) && $cid==$lipinka->mendianId)){
						$price_lipinka += $price*$g['num'];
					}
		        }
		        $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
		        $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
		        $num+=(int)$g['num'];
		        
		        $zong_weight+=$inventory->weight*$g['num'];
		        $pdt = array();
		        $pdt['id'] = $inventory->id;
		        $pdt['productId'] = $g['productId'];
		        $pdt['title'] = $inventory->title;
		        $pdt['sn'] = $inventory->sn;
		        $pdt['key_vals'] = $inventory->key_vals;
		        $pdt['weight'] = $inventory->weight;
		        $pdt['num'] = $g['num'];
		        $pdt['image'] = ispic($inventory->image);
		        $pdt['price_sale'] = $price;
		        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
		        $pdt['price_card'] = $inventory->price_card;
		        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
		        $units_arr = json_decode($units);
		        $pdt['unit'] = $units_arr[0]->title;
		        $product_json_arry[] = $pdt;
		        if(!empty($yushouId)){
		        	break;
		        }
		    }
	    }
	    //价格相关
	    $price_json = new StdClass();
	    $price_json_product = new StdClass();
	    $price_json_product->price = $zong_price;
	    $price_json_product->desc = '';
	    $price_json->goods = $price_json_product;
	    if($request['lingyuangou']==1){
	    	$price_json_lyg = new StdClass();
		    $price_json_lyg->lingyuangou = 1;
		    $price_json->lingyuangou = $price_json_lyg;
	    }
	    if(!empty($pdtstr)){
	        $pdtstr = substr($pdtstr,1);
	        $pdt_arr = json_decode('['.$pdtstr.']');
	    }
	    $cuxiao_money = $zong_price;
	 	if(!empty($lpk_id) && !empty($lpk_kedi)){
	 		$cuxiao_money-=$lpk_kedi;
	 	}
	    //限购判断
	 	$xiangou_sql = "insert into cuxiao_pdt_buy(cuxiao_id,inventoryId,userId,num,comId,orderId) values";
	 	$xiangou_sql1 = '';
	    $pdt_cuxiao = get_pdt_cuxiao($pdt_arr,$areaId,$cuxiao_money);
	    if(!empty($pdt_cuxiao['cuxiao_ids'])){
	    	foreach ($pdt_cuxiao['cuxiao_ids'] as $key => $cuxiaoId) {
	    		$cuxiao_pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$cuxiaoId");
	    		$pdtArr = explode(',',$cuxiao_pdtIds);
	    		foreach ($gouwuche as $i=>$g) {
	    			$inventId = $g['inventoryId'];
	    			$num = (int)$g['num'];
	    			if(in_array($inventId,$pdtArr)){
	    				$buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
	    				$xiangou_num = (int)$db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiaoId and inventoryId=$inventId");
	    				if($xiangou_num>0 && ($buy_num+$num)>$xiangou_num){
	    					$inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card from demo_product_inventory where id=$inventId");
	    					die('{"code":0,"message":"下单失败，商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”限购'.$xiangou_num.'份！您还可购买'.($xiangou_num-$buy_num).'份"}');
	    				}else{
	    					$xiangou_sql1.=",($cuxiaoId,$inventId,$userId,$num,10,order_id)";
	    				}
	    			}
	    		}
	    	}
	    }
	    //$pdt_cuxiao = get_pdt_cuxiao($pdt_arr,$areaId);
	    if($pdt_cuxiao['jian']>0){
	        $zong_price-=$pdt_cuxiao['jian'];
	        $price_json_cuxiao = new StdClass();
		    $price_json_cuxiao->price = $pdt_cuxiao['jian'];
		    $price_json_cuxiao->desc = $pdt_cuxiao['cuxiao_title'];
		    $price_json->cuxiao = $price_json_cuxiao;
	    }
	    //订单促销
	    $order_cuxiao = get_order_cuxiao($zong_price,$areaId);
	    if($order_cuxiao['jian']>0){
	        $zong_price-=$order_cuxiao['jian'];
	        $price_json_order = new StdClass();
		    $price_json_order->price = $order_cuxiao['jian'];
		    $price_json_order->desc = $order_cuxiao['cuxiao_title'];
		    $price_json->cuxiao_order = $price_json_order;
	    }
	    //商家返利
	    $zongfanli = $zong_gonghuo_price;//总返利
	    if($zongfanli>0){
	    	$fanli_json['shop_fanli'] = $zong_gonghuo_price;
	    	/*$user_fanli = intval($zongfanli*$shezhi->user_bili)/100;
	    	$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
	    	if(!empty($fanli_json['shangji'])){
	    		$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
	    	}
	    	if(empty($fanli_json['shangshangji'])){
	    		$fanli_json['shangshangji_fanli'] = 0;
	    	}
	    	if(!empty($shop->tuijianren) && !empty($shop->tuijian_bili)){
	    		$fanli_json['tuijian_fanli'] = intval($zongfanli*$shop->tuijian_bili)/100;
	    	}
	    	$fanli_json['pingtai_fanli'] = $zongfanli-$fanli_json['shangshangji_fanli']-$fanli_json['shangji_fanli']-$fanli_json['tuijian_fanli'];*/
	    }
	    //获取运费
	    $yunfei = get_yunfei($pdt_arr,$zong_price,$areaId);
	    //获取优惠券
	    $now = date("Y-m-d H:i:s");
	    if($yhq_id>0){
	    	$yhq = $db->get_row("select id,yhqId,title,man,jian,jiluId from user_yhq$fenbiao where id=$yhq_id and userId=$userId and status=0 and endTime>='$now' and startTime<='$now'");
		    if(!empty($yhq)){
		    	$yhq_comId = $db->get_var("select mendianIds from yhq where id=$yhq->jiluId");
		    	if($yhq_comId>0 && $yhq_comId==$cid){
		    		$zong_price-=$yhq->jian;
			        $price_json_yhq = new StdClass();
				    $price_json_yhq->price = $yhq->jian;
				    $price_json_yhq->desc = $yhq->id;
				    $price_json->yhq = $price_json_yhq;
				    $db->query("update user_yhq$fenbiao set status=1 where id=$yhq_id");
		    	}else if(empty($yhq_comId) && $zong_price>=$yhq->jian){
		    		$zong_price-=$yhq->jian;
			        $price_json_yhq = new StdClass();
				    $price_json_yhq->price = $yhq->jian;
				    $price_json_yhq->desc = $yhq->id;
				    $price_json->yhq = $price_json_yhq;
				    $db->query("update user_yhq$fenbiao set status=1 where id=$yhq_id");
				    $yhq_id = 0;
		    	}
		    }
	    }
	    
	    if(!empty($yunfei)){
	        $zong_price+=$yunfei;
	        $price_json_yunfei = new StdClass();
		    $price_json_yunfei->price = $yunfei;
		    $price_json_yunfei->desc = '';
		    $price_json->yunfei = $price_json_yunfei;
	    }
	    if(!empty($pdt_cuxiao['zengpin'])){
			foreach ($pdt_cuxiao['zengpin'] as $zeng) {
				$inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
		        $pdt = array();
		        $pdt['id'] = $inventory->id;
		        $pdt['productId'] = $inventory->productId;
		        $pdt['title'] = $inventory->title;
		        $pdt['sn'] = $inventory->sn;
		        $pdt['key_vals'] = $inventory->key_vals;
		        $pdt['weight'] = $inventory->weight;
		        $pdt['num'] = $zeng['num'];
		        $pdt['price_sale'] = 0;
		        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
		        $pdt['price_card'] = 0;
		        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
		        $units_arr = json_decode($units);
		        $pdt['unit'] = $units_arr[0]->title;
		        $product_json_arry[] = $pdt;
		        $num+=$zeng['num'];
	        	$zong_weight+=$inventory->weight*$zeng['num'];
			}
		}
		if(!empty($order_cuxiao['zengpin'])){
			foreach ($order_cuxiao['zengpin'] as $zeng) {
				$inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
		        $pdt = array();
		        $pdt['id'] = $inventory->id;
		        $pdt['productId'] = $inventory->productId;
		        $pdt['title'] = $inventory->title;
		        $pdt['sn'] = $inventory->sn;
		        $pdt['key_vals'] = $inventory->key_vals;
		        $pdt['weight'] = $inventory->weight;
		        $pdt['num'] = $zeng['num'];
		        $pdt['price_sale'] = 0;
		        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
		        $pdt['price_card'] = 0;
		        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
		        $units_arr = json_decode($units);
		        $pdt['unit'] = $units_arr[0]->title;
		        $product_json_arry[] = $pdt;
		        $num+=$zeng['num'];
	        	$zong_weight+=$inventory->weight*$zeng['num'];
			}
		}
		$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
	    $jifen = get_order_jifen($pdt_arr,$zong_price);
	    $storeId = get_fahuo_store($areaId,$cid);
		$shouhuo_json = array();
		$shouhuo_json['收件人'] = $address->name;
		$shouhuo_json['手机号'] = $address->phone;
		$shouhuo_json['所在地区'] = $address->areaName;
		$shouhuo_json['详细地址'] = $address->address;
		$order = array();
		$order['orderId'] = $cid.'_'.date("YmdHis").rand(10000,99999);
		$order['userId'] = $userId;
		$order['zhishangId'] = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		$order['comId'] = $cid;
		$order['mendianId'] = 0;
		$order['yushouId'] = $yushouId;
		$order['type'] = 1;
		$order['status'] = -5;//待支付
		$order['dtTime'] = date("Y-m-d H:i:s");
		$order['remark'] = $remark;
		$order['pay_endtime'] = date("Y-m-d H:i:s",$check_pay_time);
		$order['price'] = $zong_price;
		if($yushou->paytype==2){
			$order['price_dingjin'] = $yushou->dingjin;
		}
		$order['storeId'] = $storeId;
		$order['pdtNums'] = $num;
		$order['pdtChanel'] = 0;
		$order['ifkaipiao'] = 0;
		$order['weight'] = $zong_weight;
		$order['jifen'] = $jifen;
		$order['areaId'] = $areaId;
		$order['address_id'] = $address_id;
		$order['product_json'] = $product_json;
		$order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
		$order['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
		$order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
		$order['shangji'] = $fanli_json['shangji'];
		$order['shangshangji'] = $fanli_json['shangshangji'];
		$order['ifkaipiao'] = (int)$request['if_fapiao'];
		if($request['if_fapiao']>0){
			$fapiao_json = array();
			$fapiao_json['发票类型'] = $request['fapiao_leixing'];
			$fapiao_json['抬头类型'] = $request['fapiao_type']==1?'个人':'公司';
			if($request['fapiao_type']==2){
				$fapiao_json['公司名称'] = $request['fapiao_com_title'];
				$fapiao_json['识别码'] = $request['fapiao_shibiema'];
				$fapiao_json['注册地址'] = $request['fapiao_address'];
				$fapiao_json['注册电话'] = $request['fapiao_phone'];
				$fapiao_json['开户银行'] = $request['fapiao_bank_name'];
				$fapiao_json['银行账号'] = $request['fapiao_bank_card'];
			}
			$fapiao_json['发票明细'] = $request['fapiao_cont'];
			$fapiao_json['收票人手机'] = $request['shoupiao_phone'];
			$fapiao_json['收票人邮箱'] = $request['shoupiao_email'];
			$order['fapiao_json'] = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
			if($request['fapiao_id']==0&&$index==0){
				$fapiao = array();
				$fapiao['userId'] = $userId;
				$fapiao['comId'] = $comId;
				$fapiao['type'] = 1;
				$fapiao['com_title'] = trim($request['fapiao_com_title']);
				$fapiao['shibiema'] = trim($request['fapiao_shibiema']);
				$fapiao['shoupiao_phone'] = trim($request['shoupiao_phone']);
				$fapiao['shoupiao_email'] = trim($request['shoupiao_email']);
				$db->insert_update('user_fapiao',$fapiao,'id');
			}
		}
		$order['if_zong'] = 1;
		//file_put_contents('request.txt',json_encode($order,JSON_UNESCAPED_UNICODE));
		$db->insert_update('order'.$order_fenbiao,$order,'id');
		$order_id = $db->get_var("select last_insert_id();");
		if(!empty($xiangou_sql1)){
			$xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
			$xiangou_sql1 = substr($xiangou_sql1,1);
			$db->query($xiangou_sql.$xiangou_sql1);
		}
		foreach ($product_json_arry as $detail) {
			$pdt = new StdClass();
			$pdt->sn = $detail['sn'];
			$pdt->title = $detail['title'];
			$pdt->key_vals = $detail['key_vals'];
			$order_detail = array();
			$order_detail['comId'] = $cid;
			$order_detail['mendianId'] = 0;
			$order_detail['userId'] = $userId;
			$order_detail['orderId'] = $order_id;
			$order_detail['inventoryId'] = $detail['id'];
			$order_detail['productId'] = $detail['productId'];
			$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
			$order_detail['num'] = $detail['num'];
			$order_detail['unit'] = $detail['unit'];
			$order_detail['unit_price'] = $detail['price_sale'];
			$db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
			if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
			$db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
		}
		$pay_zong+=$order['price'];
		$order_info = array('orderId'=>$order_id,'comId'=>$cid,"price_card"=>$price_card,"price_lipinka"=>$price_lipinka);
		$pay_orderInfo[] = $order_info;
		if($lingyuangou==1 && $zong_price==0){
			if(empty($users_yaoqing)){
				$db->query("insert into users_yaoqing(userId,nums,dikoujin,buy_ids) value($userId,0,0,'".$zhuligou_id."')");
			}else{
				$buy_ids_str = empty($users_yaoqing->buy_ids)?$zhuligou_id:$users_yaoqing->buy_ids.','.$zhuligou_id;
				$db->query("update users_yaoqing set buy_ids='$buy_ids_str' where userId=$userId");
			}
			
			$order = $db->get_row("select * from order$order_fenbiao where id=$order_id");
			$if_shop_fanli = 1;
			order_jisuan_fanli($order,$if_shop_fanli);
			$db->query("update order_detail$order_fenbiao set status=1 where orderId=$order_id");
			$product_json = json_decode($order->product_json);
			$product_title = '';
			foreach ($product_json as $pdt){
				$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
			}
			if(!empty($product_title)){
				$product_title = substr($product_title,1);
			}
			$fahuo = array();
			$fahuo['comId'] = $order->comId;
			$fahuo['mendianId'] = $order->mendianId;
			$fahuo['addressId'] = $order->address_id;
			$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$fahuo['orderIds'] = $order_id;
			$fahuo['type'] = 1;
			$fahuo['showTime'] = date("Y-m-d H:i:s");
			$fahuo['storeId'] = $order->storeId;
			$fahuo['dtTime'] = date("Y-m-d H:i:s");
			$fahuo['shuohuo_json'] = $order->shuohuo_json;
			$fahuo['productId'] = 0;
			$fahuo['tuanzhang'] = $userId;
			$fahuo['product_title'] = $product_title;
			$fahuo['fahuo_title'] = $product_title;
			$fahuo['product_num'] = $order->pdtNums;
			$fahuo['weight'] = $order->weight;
			$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
			$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
			$fahuoId = $db->get_var("select last_insert_id();");
			$db->query("update order$order_fenbiao set status=2,fahuoId=$fahuoId where id=$order_id");
			$yibao_fenzhang = array();
			$yibao_fenzhang['comId'] = $order->comId;
			$yibao_fenzhang['money'] = $fanli_json->shop_fanli;
			$yibao_fenzhang['dtTime'] = date("Y-m-d H:i:s");
			$yibao_fenzhang['orderId'] = $order_id;
			$yibao_fenzhang['payId'] = 0;
			$yibao_fenzhang['type'] = 1;
			$yibao_fenzhang['income_type'] = 2;
			$yibao_fenzhang['status'] = 1;
			$db->insert_update('demo_yibao_fenzhang',$yibao_fenzhang,'id');
			$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
			foreach ($details as $detail){
				$detail->num = (int)$detail->num;
				$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
				$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
			}
			die('{"code":1,"message":"下单成功","order_id":0,"pay_id":"0","lingyuangou":1}');
		}
		tongbu_user($cid);
	}
	if(empty($users_yaoqing)){
		$db->query("insert into users_yaoqing(userId,nums,dikoujin,buy_ids) value($userId,0,0,'".$zhuligou_id."')");
	}else{
		$buy_ids_str = empty($users_yaoqing->buy_ids)?$zhuligou_id:$users_yaoqing->buy_ids.','.$zhuligou_id;
		$db->query("update users_yaoqing set buy_ids='$buy_ids_str' where userId=$userId");
	}
	//$yhq = $db->get_row("select id,yhqId,title,man,jian,jiluId from user_yhq$fenbiao where id=$yhq_id and userId=$userId and status=0 and endTime>='$now' and startTime<='$now'");
    /*$price_json = new StdClass();
    if(!empty($yhq)){
        //$pay_zong-=$yhq->jian;
        $price_json_yhq = new StdClass();
	    $price_json_yhq->price = $yhq->jian;
	    $price_json_yhq->desc = $yhq->id;
	    $price_json->yhq = $price_json_yhq;
    }*/
    $order_pay = array();
    $order_pay['orderId'] = date("YmdHis").rand(1000000000,9999999999);
    $order_pay['orderInfo'] = json_encode($pay_orderInfo,JSON_UNESCAPED_UNICODE);
    $order_pay['price'] = $pay_zong;
    $order_pay['pay_json'] = '';
    $order_pay['card_kedi'] = $pay_card;
    $pay_id = $db->insert_update('order_pay',$order_pay,'id');
    $timed_task = array();
	$timed_task['comId'] = $comId;
	$timed_task['dtTime'] = $check_pay_time;
	$timed_task['router'] = 'order_checkPays';
	$timed_task['params'] = '{"pay_id":'.$pay_id.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
    foreach ($pay_orderInfo as $info) {
    	$fenbiao = getFenbiao($info['comId'],20);
    	$db->query("update order$fenbiao set pay_id=$pay_id where id=".$info['orderId']);
    }
    //抵扣金扣除
    $lpk_id = (int)$request['lpk_id'];
    $lpk_kedi = $request['lpk_kedi'];
    if(!empty($lpk_id) && !empty($lpk_kedi)){
    	card_pay_zong($pay_id,$lpk_id,$lpk_kedi);
    }
    if(!empty($lipinka_id) && !empty($lipinka_kedi)){
    	lipinka_pay_zong($pay_id,$lipinka_id,$lipinka_kedi);
    	if($lipinka_kedi==$pay_zong){
    		die('{"code":1,"message":"下单成功","order_id":0,"pay_id":"'.$pay_id.'","lingyuangou":1}');
    	}
    }
	$content = '';
	if(!empty($gouwuches)){
		$content=json_encode($gouwuches,JSON_UNESCAPED_UNICODE);
	}
	$db->query("update demo_gouwuche set content='$content' where userId=$userId and comId=$comId");
	die('{"code":1,"message":"下单成功","order_id":0,"pay_id":"'.$pay_id.'"}');
}
function lipinka_pay_zong($payId,$giftId,$money){
	global $db;
	//$payId = (int)$request['pay_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	//$giftId = (int)$request['cardId'];
	$u = $db->get_row("select * from lipinka where id=$giftId and userId=$userId");
	if(empty($u)){
		return 0;
	}
	$order_pay = $db->get_row("select * from order_pay where id=$payId");
	//$money = $request['money'];
	$order_price = $order_pay->price-$order_pay->price_payed;
	if($money>$order_price || $money>$u->yue){
		return 0;
	}
	if(empty($order_pay)){
		return 0;
	}
	if($order->is_pay!=0){
		return 0;
	}
	$db->query("update lipinka set yue=yue-$money where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from lipinka where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，支付号：'.$order_pay->orderId;
	$liushui['orderId']=$payId;
	insert_update('lipinka_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $payId;
	$o['price_payed'] = $money+$order_pay->price_payed;
	$pay_json = array();
	if(!empty($order_pay->pay_json)){
		$pay_json = json_decode($order_pay->pay_json,true);
	}
	$pay_json['lipinka1']['price'] = $money;
	$pay_json['lipinka1']['desc'] = $u->cardId;
	$pay_json['lipinka1']['cardId'] = $giftId;
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($money==$order_price){
		$o['is_pay'] = 1;
	}
	$db->insert_update('order_pay',$o,'id');
	$orders = json_decode($order_pay->orderInfo,true);
	$zong_price_card = $money;
	//$bili = $db->get_var("select daili_bili from demo_shezhi where comId=10");
	if(!empty($orders)){
		foreach ($orders as $ord) {
			if($ord['price_lipinka']>0 && $zong_price_card>0){
				$order_fenbiao = getFenbiao($ord['comId'],20);
				$order_comId = $ord['comId'];
				$orderId = $ord['orderId'];
				$order = $db->get_row("select * from order$order_fenbiao where id=".$ord['orderId']);
				$o = array();
				$p_card = $ord['price_lipinka']>$zong_price_card?$zong_price_card:$ord['price_lipinka'];
				$o['id'] = $ord['orderId'];
				$o['price_payed'] = $order->price_payed+$p_card;
				$pay_json = array();
				if(!empty($order->pay_json)){
					$pay_json = json_decode($order->pay_json,true);
				}
				$pay_json['lipinka1']['price'] = $p_card;
				$pay_json['lipinka1']['desc'] = $u->cardId;
				$pay_json['lipinka1']['cardId'] = $giftId;
				$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
				/*if($u->daili_id>0){
					$fanli_json = json_decode($order->fanli_json);
					$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli;
					$daili_fanli = intval($zong_fanli*$bili)/100;
					$fanli_json->daili_id = $u->daili_id;
					$fanli_json->daili_fanli = $daili_fanli;
					$fanli_json->pingtai_fanli = $fanli_json->pingtai_fanli-$daili_fanli;
					$o['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
				}*/
				if($o['price_payed'] == $order->price){
					order_jisuan_fanli($order,1);
					$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
					$o['ispay'] = 1;
					$o['pay_type'] = 4;
					$db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
					$product_json = json_decode($order->product_json);
					$product_title = '';
					foreach ($product_json as $pdt){
						$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
					}
					if(!empty($product_title)){
						$product_title = substr($product_title,1);
					}
					$fahuo = array();
					$fahuo['comId'] = $order_comId;
					$fahuo['mendianId'] = $order->mendianId;
					$fahuo['addressId'] = $order->address_id;
					$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
					$fahuo['orderIds'] = $orderId;
					$fahuo['type'] = 1;
					$fahuo['showTime'] = date("Y-m-d H:i:s");
					$fahuo['storeId'] = $order->storeId;
					$fahuo['dtTime'] = date("Y-m-d H:i:s");
					$fahuo['shuohuo_json'] = $order->shuohuo_json;
					$fahuo['productId'] = 0;
					$fahuo['tuanzhang'] = $userId;
					$fahuo['product_title'] = $product_title;
					$fahuo['fahuo_title'] = $product_title;
					$fahuo['product_num'] = $order->pdtNums;
					$fahuo['weight'] = $order->weight;
					$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
					if($order->yushouId>0){
						$fahuo['yushouId'] = $order->yushouId;
						$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
					}
					$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
					$fahuoId = $db->get_var("select last_insert_id();");
					$o['fahuoId'] = $fahuoId;
					$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
					foreach ($details as $detail){
						$detail->num = (int)$detail->num;
						$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
						$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
					}
					addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
				}
				$db->insert_update('order'.$order_fenbiao,$o,'id');
				$zong_price_card-=$p_card;
			}
		}
	}
	return 0;
}
function card_pay_zong($payId,$giftId,$money){
	global $db;
	//$payId = (int)$request['pay_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	//$giftId = (int)$request['cardId'];
	$u = $db->get_row("select * from gift_card$fenbiao where id=$giftId and userId=$userId");
	if(empty($u)){
		return 0;
	}
	$order_pay = $db->get_row("select * from order_pay where id=$payId");
	//$money = $request['money'];
	$order_price = $order_pay->price-$order_pay->price_payed;
	if($money>$order_price || $money>$u->yue){
		return 0;
	}
	if(empty($order_pay)){
		return 0;
	}
	if($order->is_pay!=0){
		return 0;
	}
	$db->query("update gift_card$fenbiao set yue=yue-$money where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，支付号：'.$order_pay->orderId;
	$liushui['orderId']=$payId;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $payId;
	$o['price_payed'] = $money+$order_pay->price_payed;
	$pay_json = array();
	if(!empty($order_pay->pay_json)){
		$pay_json = json_decode($order_pay->pay_json,true);
	}
	$pay_json['lipinka']['price'] = $money;
	$pay_json['lipinka']['desc'] = $u->cardId;
	$pay_json['lipinka']['cardId'] = $giftId;
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($money==$order_price){
		$o['is_pay'] = 1;
	}
	$db->insert_update('order_pay',$o,'id');
	$orders = json_decode($order_pay->orderInfo,true);
	$zong_price_card = $money;
	$bili = $db->get_var("select daili_bili from demo_shezhi where comId=10");
	if(!empty($orders)){
		foreach ($orders as $ord) {
			if($ord['price_card']>0 && $zong_price_card>0){
				$order_fenbiao = getFenbiao($ord['comId'],20);
				$order_comId = $ord['comId'];
				$orderId = $ord['orderId'];
				$order = $db->get_row("select * from order$order_fenbiao where id=".$ord['orderId']);
				$o = array();
				$p_card = $ord['price_card']>$zong_price_card?$zong_price_card:$ord['price_card'];
				$o['id'] = $ord['orderId'];
				$o['price_payed'] = $order->price_payed+$p_card;
				$pay_json = array();
				if(!empty($order->pay_json)){
					$pay_json = json_decode($order->pay_json,true);
				}
				$pay_json['lipinka']['price'] = $p_card;
				$pay_json['lipinka']['desc'] = $u->cardId;
				$pay_json['lipinka']['cardId'] = $giftId;
				$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
				/*if($u->daili_id>0){
					$fanli_json = json_decode($order->fanli_json);
					$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli;
					$daili_fanli = intval($zong_fanli*$bili)/100;
					$fanli_json->daili_id = $u->daili_id;
					$fanli_json->daili_fanli = $daili_fanli;
					$fanli_json->pingtai_fanli = $fanli_json->pingtai_fanli-$daili_fanli;
					$o['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
				}*/
				if($o['price_payed'] == $order->price){
					order_jisuan_fanli($order,1);
					$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
					$o['ispay'] = 1;
					$o['pay_type'] = 4;
					$db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
					$product_json = json_decode($order->product_json);
					$product_title = '';
					foreach ($product_json as $pdt){
						$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
					}
					if(!empty($product_title)){
						$product_title = substr($product_title,1);
					}
					$fahuo = array();
					$fahuo['comId'] = $order_comId;
					$fahuo['mendianId'] = $order->mendianId;
					$fahuo['addressId'] = $order->address_id;
					$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
					$fahuo['orderIds'] = $orderId;
					$fahuo['type'] = 1;
					$fahuo['showTime'] = date("Y-m-d H:i:s");
					$fahuo['storeId'] = $order->storeId;
					$fahuo['dtTime'] = date("Y-m-d H:i:s");
					$fahuo['shuohuo_json'] = $order->shuohuo_json;
					$fahuo['productId'] = 0;
					$fahuo['tuanzhang'] = $userId;
					$fahuo['product_title'] = $product_title;
					$fahuo['fahuo_title'] = $product_title;
					$fahuo['product_num'] = $order->pdtNums;
					$fahuo['weight'] = $order->weight;
					$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
					if($order->yushouId>0){
						$fahuo['yushouId'] = $order->yushouId;
						$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
					}
					$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
					$fahuoId = $db->get_var("select last_insert_id();");
					$o['fahuoId'] = $fahuoId;
					$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
					foreach ($details as $detail){
						$detail->num = (int)$detail->num;
						$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
						$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
					}
					addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
				}
				$db->insert_update('order'.$order_fenbiao,$o,'id');
				$zong_price_card-=$p_card;
			}
		}
	}
	return 0;
}
function card_pay($orderId,$order_comId,$giftId,$money){
	global $db;
	//$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	//$order_comId = (int)$request['comId'];
	if(empty($order_comId))$order_comId = $comId;
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($order_comId,20);
	//$giftId = (int)$request['cardId'];
	$u = $db->get_row("select cardId,password,yue from gift_card$fenbiao where id=$giftId and userId=$userId");
	if(empty($u)){
		die('{"code":0,"message":"礼品卡不存在"}');
	}
	$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
	//$money = $request['money'];
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
	    $order->price = $order->price_dingjin-$order->price_payed;
	}
	if($money>$order->price || $money>$u->yue){
		return 0;
	}
	if(empty($order)){
		return 0;
	}
	if($order->status!=-5){
		return 0;
	}
	$pay_end = strtotime($order->pay_endtime);
	$now = time();
	if($pay_end<$now){
		return 0;
	}
	/*$details = $db->get_results("select inventoryId,num,pdtInfo from order_detail$fenbiao where orderId=$orderId");
	foreach ($details as $detail) {
		$kucun = $db->get_row("select yugouNum,kucun from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
		$kc = $kucun->kucun-$kucun->yugouNum;
		if($kc<$detail->num){
			$product = json_decode($detail->pdtInfo);
			die('{"code":0,"message":"商品'.$product->title.'【'.$product->key_vals.'】'.'库存不足，不能进行支付"}');
		}
	}*/
	//修改账号余额及流水记录
	//$yzFenbiao = $fenbiao = getFenbiao($comId,20);
	$db->query("update gift_card$fenbiao set yue=yue-$money where id=$giftId");
	$liushui = array();
	$liushui['cardId']=$giftId;
	$liushui['money']=-$money;
	$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$giftId");
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
	$liushui['orderId']=$orderId;
	insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	$o['price_payed'] = $money+$order->price_payed;
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	$pay_json['lipinka']['price'] = $money;
	$pay_json['lipinka']['desc'] = $u->cardId;
	$pay_json['lipinka']['cardId'] = $giftId;
	/*if($order->price_dingjin==0){
		
	}else{
		$pay_json['dingjin']['price'] = $order->price;
		$pay_json['dingjin']['paytype'] = '礼品卡，卡号：'.$u->cardId;
	}*/
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	if($money==$order->price){
		if($order->price_dingjin==0){
			$o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
			$o['ispay'] = 1;
			$o['pay_type'] = 4;
		}
	}
	/*if($u->daili_id>0){
		$fanli_json = json_decode($order->fanli_json);
		$zong_fanli = $fanli_json->shangji_fanli + $fanli_json->shangshangji_fanli + $fanli_json->tuijian_fanli + $fanli_json->pingtai_fanli;
		$bili = $db->get_var("select daili_bili from demo_shezhi where comId=10");
		$daili_fanli = intval($zong_fanli*$bili)/100;
		$fanli_json->daili_id = $u->daili_id;
		$fanli_json->daili_fanli = $daili_fanli;
		$fanli_json->pingtai_fanli = $fanli_json->pingtai_fanli-$daili_fanli;
		$o['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
	}*/
	if($money==$order->price && $order->price_dingjin==0){
		order_jisuan_fanli($order,1);
	}
	$db->insert_update('order'.$order_fenbiao,$o,'id');
	if($money==$order->price && $order->price_dingjin==0){
		$db->query("update order_detail$order_fenbiao set status=1 where orderId=$orderId");
		$product_json = json_decode($order->product_json);
		$product_title = '';
		foreach ($product_json as $pdt){
			$product_title.=','.$pdt->title.'【'.$pdt->key_vals.'】'.'*'.$pdt->num;
		}
		if(!empty($product_title)){
			$product_title = substr($product_title,1);
		}
		$fahuo = array();
		$fahuo['comId'] = $order_comId;
		$fahuo['mendianId'] = $order->mendianId;
		$fahuo['addressId'] = $order->address_id;
		$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$fahuo['orderIds'] = $orderId;
		$fahuo['type'] = 1;
		$fahuo['showTime'] = date("Y-m-d H:i:s");
		$fahuo['storeId'] = $order->storeId;
		$fahuo['dtTime'] = date("Y-m-d H:i:s");
		$fahuo['shuohuo_json'] = $order->shuohuo_json;
		$fahuo['productId'] = 0;
		$fahuo['tuanzhang'] = $userId;
		$fahuo['product_title'] = $product_title;
		$fahuo['fahuo_title'] = $product_title;
		$fahuo['product_num'] = $order->pdtNums;
		$fahuo['weight'] = $order->weight;
		$fahuo['areaId'] = (int)$db->get_var("select areaId from user_address where id=$order->address_id");
		if($order->yushouId>0){
			$fahuo['yushouId'] = $order->yushouId;
			$fahuo['fahuoTime'] = $db->get_var("select fahuoTime from yushou where id=$order->yushouId");
		}
		$db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$orderId");
		$details = $db->get_results("select inventoryId,num,productId from order_detail$order_fenbiao where orderId=$orderId");
		foreach ($details as $detail){
			$detail->num = (int)$detail->num;
			$db->query("update demo_product_inventory set orders=orders+$detail->num where id=$detail->inventoryId");
			$db->query("update demo_product set orders=orders+$detail->num where id=$detail->productId");
		}
		addTaskMsg(31,$orderId,'您的商城有新的订单，请及时处理',$order_comId);
		print_order($order);
		die('{"code":2,"message":"支付成功","buy_type":'.$order->type.'}');
	}else if($money==$order->price && $order->price_dingjin>0){
		$yushou = $db->get_row("select * from yushou where id=$order->yushouId");
		$tixing_time = strtotime($yushou->startTime1);
		$pay_endtime = strtotime($yushou->endTime1);
		$db->query("update order$order_fenbiao set pay_endtime='$yushou->endTime1',price_dingjin=0 where id=$orderId");
		$db->query("delete from demo_timed_task where comId=$comId and params='{\"order_id\":".$orderId."}' and router='order_checkPay' limit 1");
		$timed_task = array();
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $pay_endtime;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$orderId.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		$timed_task['comId'] = $order_comId;
		$timed_task['dtTime'] = $tixing_time;
		$timed_task['router'] = 'order_payTixing';
		$timed_task['params'] = '{"order_id":'.$orderId.',"user_id":"'.$order->userId.'"}';
		$db->insert_update('demo_timed_task',$timed_task,'id');
		return 0;
	}
	return 0;
}
//获取会员的折扣
function get_user_zhekou($inventoryId,$price){
	$inventoryId = (int)$inventoryId;
	global $db;
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	if($level==0)return getXiaoshu($price,2);
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
	}
	$shezhi = $db->get_row("select if_fixed_zhekou,fixed_zhekou from user_shezhi where comId=$comId");
	if($shezhi->if_fixed_zhekou==1){
		$if_price = $db->get_var("select price from demo_product_price where inventoryId=$inventoryId and levelId=$level");
		$price = empty($if_price)?$price:$if_price;
		$price = getXiaoshu($price,2);
	}else{
		$zhekou = $db->get_var("select zhekou from user_level where id=$level and comId=$comId");
		$zhekou = $zhekou/10;
		if(empty($zhekou))$zhekou=1;
		$price = getXiaoshu($price*$zhekou,2);
	}
	return $price;
}
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
//获取发货仓库
function get_fahuo_store($areaId,$comId=0){
	global $db;
	if($comId==0)$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_shequ']==2){
		//餐饮版调取门店的库存
		if(empty($_SESSION['demo_shequ_id'])){
			$storeId = $db->get_var("select storeId from demo_shezhi where comId=$comId");
		}else{
			$storeId = $db->get_var("select storeId from demo_shequ where id=".$_SESSION['demo_shequ_id']);
		}
	}else if(!empty($areaId)){
		$fuarea = (int)$db->get_var("select parentId from demo_area where id=$areaId");
		$fuarea1 = (int)$db->get_var("select parentId from demo_area where id=$fuarea");
		$storeId = $db->get_var("select storeId from demo_shezhi_fahuo where comId=$comId and (find_in_set($areaId,areaIds) or find_in_set($fuarea,areaIds) or find_in_set($fuarea1,areaIds)) limit 1");
		if(empty($storeId)){
			$storeId = $db->get_var("select storeId from demo_shezhi where comId=$comId");
		}
	}else{
		$storeId = $db->get_var("select storeId from demo_shezhi where comId=$comId");
	}
	return $storeId;
}
//根据购物车获取商品促销信息
function get_pdt_cuxiao($pdts,$areaId,$money=0){
	global $db;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$pareaId = (int)$db->get_var("select parentId from demo_area where id=$areaId");
	$cuxiaos = $db->get_results("select * from cuxiao_pdt where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))");
	//echo "select * from cuxiao_pdt where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))";
	$return = array();
	$return['jian'] = 0;//总优惠价格
	$return['zengpin'] = array();
	$return['cuxiao_pdt'] = array();
	$return['cuxiao_ids'] = array();
	$return['cuxiao_title'] = '';
	if(!empty($cuxiaos)){
		foreach ($cuxiaos as $cuxiao){
			$zong_money = 0;
			$zong_num = 0;
			$pdtIds = explode(',', $cuxiao->pdtIds);
			$rules = json_decode($cuxiao->guizes,true);
			if(!empty($rules)){
				$columns = array_column($rules,'man');
				array_multisort($columns,SORT_DESC,$rules);
				//计算出符合条件的商品的总数量和总价格
				
				foreach ($pdts as $pdts1){
					foreach ($pdts1 as $inventoryId => $pdt) {
						if(in_array($inventoryId, $pdtIds)){
							$zong_money += $pdt->price*$pdt->num;
							$zong_num += $pdt->num;
							if(!in_array($cuxiao->id,$return['cuxiao_ids'])){
								$return['cuxiao_ids'][] = $cuxiao->id;
							}
						}
					}
				}
				if($money>0)$zong_money = $money;
				//如果是已数量来计算
				if($cuxiao->accordType==1){
					foreach ($rules as $rule){
						if($zong_num>=$rule['man']){
							if(!in_array($cuxiao->id,$return['cuxiao_pdt'])){
								$return['cuxiao_pdt'][] = $cuxiao->id;
								$return['cuxiao_title'].=empty($return['cuxiao_title'])?$cuxiao->title:','.$cuxiao->title;
							}
							//满折
							if($cuxiao->type==3){
								$zhekou_money = (int)($zong_money*$rule['jian'])/100;
								$return['jian'] += $zong_money-$zhekou_money;
								break;
							}else if($cuxiao->type==1){//满赠
								$zengpin = array();
								$zengpin['id'] = $rule['inventoryId'];
								$zengpin['num'] = floor($zong_num/$rule['man']);
								$return['zengpin'][] = $zengpin;
								$zong_num = $zong_num%$rule['man'];//剩余的数量继续判断是否满足后边的条件
							}else if($cuxiao->type==2){//满减
								$cheng = floor($zong_num/$rule['man']);
								$return['jian'] += $cheng*$rule['jian'];
								$zong_num = $zong_num%$rule['man'];//剩余的数量继续判断是否满足后边的条件
							}
						}
					}
				}else{
					foreach ($rules as $rule){
						if($zong_money>=$rule['man']){
							$return['cuxiao_pdt'][] = $cuxiao->id;
							//满折
							if($cuxiao->type==3){
								$zhekou_money = (int)($zong_money*$rule['jian'])/100;
								$return['jian'] += $zong_money-$zhekou_money;
								break;
							}else if($cuxiao->type==1){//满赠
								$zengpin = array();
								$zengpin['id'] = $rule['inventoryId'];
								$zengpin['num'] = floor($zong_money/$rule['man']);
								$return['zengpin'][] = $zengpin;
								$zong_money = $zong_money%$rule['man'];//剩余的数量继续判断是否满足后边的条件
							}else if($cuxiao->type==2){//满减
								$cheng = floor($zong_money/$rule['man']);
								$return['jian'] += $cheng*$rule['jian'];
								$zong_money = $zong_money%$rule['man'];//剩余的数量继续判断是否满足后边的条件
							}
						}
					}
				}
			}else{
				foreach ($pdts as $pdts1){
					foreach ($pdts1 as $inventoryId => $pdt) {
						if(in_array($inventoryId, $pdtIds)){
							if(!in_array($cuxiao->id,$return['cuxiao_ids'])){
								$return['cuxiao_ids'][] = $cuxiao->id;
								$return['cuxiao_pdt'][] = $cuxiao->id;
								$return['cuxiao_title'].=empty($return['cuxiao_title'])?$cuxiao->title:','.$cuxiao->title;
							}
						}
					}
				}
			}
		}
	}
	$return['cuxiao_pdt'] = array_unique($return['cuxiao_pdt']);
	return $return;
}
//根据购物车获取订单促销信息
function get_order_cuxiao($price,$areaId){
	global $db;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$pareaId = (int)$db->get_var("select parentId from demo_area where id=$areaId");
	$cuxiaos = $db->get_results("select * from cuxiao_order where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))");
	$return = array();
	$return['jian'] = 0;//总优惠价格
	$return['zengpin'] = array();
	$return['cuxiao_order'] = '';
	$return['cuxiao_title'] = '';
	if(!empty($cuxiaos)){
		foreach ($cuxiaos as $cuxiao){
			$rules = json_decode($cuxiao->guizes,true);
			$columns = array_column($rules,'man');
			array_multisort($columns,SORT_DESC,$rules);
			foreach ($rules as $rule) {
				if($price>=$rule['man']){
					$return['cuxiao_order'] = $cuxiao->id;
					$return['cuxiao_title'] = $cuxiao->title;
					if($cuxiao->type==3){//满折
						$zhekou_money = (int)($price*$rule['jian'])/100;
						$return['jian'] += $price-$zhekou_money;
						break;
					}else if($cuxiao->type==1){//满赠
						$zengpin = array();
						$zengpin['id'] = $rule['inventoryId'];
						$zengpin['num'] = floor($price/$rule['man']);
						$return['zengpin'][] = $zengpin;
						$price = $price%$rule['man'];//剩余的数量继续判断是否满足后边的条件
					}else if($cuxiao->type==2){//满减
						$cheng = floor($price/$rule['man']);
						$return['jian'] += $cheng*$rule['jian'];
						$price = $price%$rule['man'];//剩余的数量继续判断是否满足后边的条件
					}
				}
			}
			if(!empty($return['cuxiao_order'])){
				break;
			}
		}
	}
	return $return;
}
//获取可用优惠券
function get_yhqs($pdts,$zong_price){
	global $db;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$return = array();
	$now = date("Y-m-d H:i:s");
	$yhqs = $db->get_results("select id,yhqId,title,man,jian,jiluId,startTime,endTime from user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>='$now' and startTime<='$now' and man<='$zong_price' order by id desc");
	if(!empty($yhqs)){
		foreach ($yhqs as $yhq) {
			$money = 0;
			$jilu = $db->get_row("select mendianIds,useType,channels,pdts from yhq where id=$yhq->jiluId");
			if($jilu->mendianIds!='' && $_SESSION['if_tongbu']!=1){
				continue;
			}
			if(empty($jilu->mendianIds) && empty($jilu->channels) && empty($jilu->pdts)){
				foreach ($pdts as $pdts1) {
					foreach ($pdts1 as $inventoryId => $pdt) {
						$money += $pdt->price*$pdt->num;
					}
				}
			}else if(!empty($jilu->channels) || !empty($jilu->pdts)){
				foreach ($pdts as $pdts1) {
					foreach ($pdts1 as $inventoryId => $pdt) {
						$inventory = $db->get_row("select fenleiId,channelId from demo_product_inventory where id=$inventoryId");
						$jilupdts = array();$jiluchanels = array();
						if(!empty($jilu->pdts))$jilupdts = explode(',', $jilu->pdts);
						if(!empty($jilu->channels))$jiluchanels = explode(',', $jilu->channels);
						if(in_array($inventoryId,$jilupdts) || in_array($inventory->fenleiId,$jiluchanels)){
							$money += $pdt->price*$pdt->num;
						}
					}
				}
			}else if(!empty($jilu->mendianIds)){
				foreach ($pdts as $pdts1) {
					foreach ($pdts1 as $inventoryId => $pdt) {
						if($pdt->comId==$jilu->mendianIds){
							$money += $pdt->price*$pdt->num;
						}
					}
				}
			}
			$money = round($money,2);
			//file_put_contents('request.txt',$money.'-'.$yhq->man.'-'.$zong_price);
			if($money<$yhq->man || $money==0){
				continue;
			}
			$return[] = $yhq;
		}
	}
	$arry = json_decode(json_encode($return,JSON_UNESCAPED_UNICODE),true);
	$columns = array_column($arry,'jian');
	array_multisort($columns,SORT_DESC,$arry);
	return $arry;
}
//获取可用礼品卡
function get_lipinkas($pdts){
	global $db;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$return = array();
	$now = date("Y-m-d H:i:s");
	$yhqs = $db->get_results("select id,typeInfo,yue,mendianId,channels,pdts,endTime from lipinka where userId=$userId and (endTime is NULL or endTime>='$now') and yue>0 order by id desc");
	if(!empty($yhqs)){
		foreach ($yhqs as $yhq) {
			$yhq->kedi = 0;
			foreach ($pdts as $pdts1) {
				foreach ($pdts1 as $inventoryId => $pdt) {
					if(empty($yhq->mendianId) && empty($yhq->channels) && empty($yhq->pdts)){
						$yhq->kedi+=$pdt->price*$pdt->num;
					}else{
						$jilupdts = array();$jiluchanels = array();
						if(!empty($yhq->pdts))$jilupdts = explode(',', $yhq->pdts);
						if(!empty($yhq->channels))$jiluchanels = explode(',', $yhq->channels);
						$inventory = $db->get_row("select fenleiId,channelId from demo_product_inventory where id=$inventoryId");
						if(!empty($yhq->pdts) && in_array($inventoryId,$jilupdts)){
							$yhq->kedi+=$pdt->price*$pdt->num;
						}else if(!empty($yhq->channels)){
							if(empty($yhq->mendianId) && in_array($inventory->fenleiId,$jiluchanels)){
								$yhq->kedi+=$pdt->price*$pdt->num;
							}else if(!empty($yhq->mendianId) && in_array($inventory->channelId,$jiluchanels)){
								$yhq->kedi+=$pdt->price*$pdt->num;
							}
						}else if(empty($yhq->pdts) && empty($yhq->channels) && $pdt->comId==$yhq->mendianId){
							$yhq->kedi+=$pdt->price*$pdt->num;
						}
					}
				}
			}
			if($yhq->kedi>$yhq->yue){
				$yhq->kedi = $yhq->yue;
			}
			if($yhq->kedi>0)$return[] = $yhq;
		}
	}
	$arry = json_decode(json_encode($return,JSON_UNESCAPED_UNICODE),true);
	$columns = array_column($arry,'kedi');
	array_multisort($columns,SORT_DESC,$arry);
	return $arry;
}
//获得积分
function get_order_jifen($pdts,$zong_price){
	global $db;
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$jifen_rule = $db->get_row("select jifen_type,jifen_content from user_shezhi where comId=$comId");
	$jifen = 0;
	if(!empty($jifen_rule->jifen_content)){
		$rule = json_decode($jifen_rule->jifen_content);
		switch ($jifen_rule->jifen_type) {
			case 1:
				$jifen = floor($zong_price/$rule->money);
				if($rule->shangxin>0 && $jifen>$rule->shangxin){
					$jifen = $rule->shangxin;
				}
			break;
			case 2:
				$jifen = floor($zong_price/$rule->man)*$rule->song;
			break;
			case 3:
				foreach ($pdts as $pdts1) {
					foreach ($pdts1 as $inventoryId => $pdt) {
						$channelId = (int)$db->get_var("select channelId from demo_product_inventory where id=$inventoryId");
						$fuChannel = (int)$db->get_var("select parentId from demo_product_channel where id=$channelId");
						foreach ($rule->items as $item) {
							$channels = array();
							$pdts = array();
							if(!empty($item->channels)){
								$channels = explode(',',$item->channels);
							}
							if(!empty($item->pdts)){
								$pdts = explode(',',$item->pdts);
							}
							if(in_array($inventoryId,$pdts) || in_array($channelId, $channels) || in_array($fuChannel, $channels)){
								$jifen+=$item->jifen * $pdt->num;
								break;
							}
						}
					}
				}
			break;
		}
	}
	return $jifen;
}
function select_address(){
	global $db,$request;
	$id = (int)$request['id'];
	$areaId = $db->get_var("select areaId from user_address where id=$id");
	$_SESSION[TB_PREFIX.'sale_area'] = (int)$areaId;
	$_SESSION[TB_PREFIX.'address_id'] = $id;
	die('{"code":1}');
}
function get_buy_shops(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	$sql = "select distinct(comId) from order0 where zhishangId=$userId union all
				select distinct(comId) from order1 where zhishangId=$userId union all
				select distinct(comId) from order2 where zhishangId=$userId union all
				select distinct(comId) from order3 where zhishangId=$userId union all
				select distinct(comId) from order4 where zhishangId=$userId union all
				select distinct(comId) from order5 where zhishangId=$userId union all
				select distinct(comId) from order6 where zhishangId=$userId union all
				select distinct(comId) from order7 where zhishangId=$userId union all
				select distinct(comId) from order8 where zhishangId=$userId union all
				select distinct(comId) from order9 where zhishangId=$userId union all
				select distinct(comId) from order10 where zhishangId=$userId union all
				select distinct(comId) from order11 where zhishangId=$userId union all
				select distinct(comId) from order12 where zhishangId=$userId union all
				select distinct(comId) from order13 where zhishangId=$userId union all
				select distinct(comId) from order14 where zhishangId=$userId union all
				select distinct(comId) from order15 where zhishangId=$userId union all
				select distinct(comId) from order16 where zhishangId=$userId union all
				select distinct(comId) from order17 where zhishangId=$userId union all
				select distinct(comId) from order18 where zhishangId=$userId union all
				select distinct(comId) from order19 where zhishangId=$userId limit 20
	";
	$shops = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['data'] = array();
	if(!empty($shops)){
		foreach ($shops as $s){
			$comId = $s->comId;
			$shop = $db->get_row("select com_title,com_logo from demo_shezhi where comId=$comId");
			$data = array();
			$data['id'] = $comId;
			$data['com_title'] = $shop->com_title;
			$data['com_logo'] = $shop->com_logo;
			$data['guanzhu'] = (int)$db->get_var("select count(userId) from user_shop_collect where shopId=$comId");
			$data['product_num'] = (int)$db->get_var("select count(*) from demo_product where comId=$comId and dtTime>'".date("Y-m-d 00:00:00",strtotime('-30 days'))."'");
			$ifguanzhu = (int)$db->get_var("select userId from user_shop_collect where userId=$userId and shopId=$comId");
			$data['ifguanzhu'] = $ifguanzhu>0?1:0;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_collet_shops(){
	global $db,$request;
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	$sql = "select shopId from user_shop_collect where userId=$userId";
	$count = $db->get_var(str_replace('shopId','count(*)',$sql));
	$sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
	$shops = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	if(!empty($shops)){
		foreach ($shops as $s){
			$comId = $s->shopId;
			$shop = $db->get_row("select com_title,com_logo from demo_shezhi where comId=$comId");
			$data = array();
			$data['id'] = $comId;
			$data['com_title'] = $shop->com_title;
			$data['com_logo'] = $shop->com_logo;
			$data['guanzhu'] = (int)$db->get_var("select count(userId) from user_shop_collect where shopId=$comId");
			$data['product_num'] = (int)$db->get_var("select count(*) from demo_product where comId=$comId and dtTime>'".date("Y-m-d 00:00:00",strtotime('-30 days'))."'");
			$data['ifguanzhu'] = 1;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function tongbu_user($comId){
	global $db,$db_service;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$username = $_COOKIE["dt_username_10"];
	$ifhas = $db->get_var("select id from users where comId=$comId and (zhishangId=$userId or username='$username') limit 1");
	if(empty($ifhas)){
		$password = $_COOKIE["dt_pwd_10"];
		$name = $_SESSION[TB_PREFIX.'user_name'];
		$areaId = (int)$_SESSION[TB_PREFIX.'address_id'];
		$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
		$city = $db->get_var("select parentId from demo_area where id=$areaId");
		$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,zhishangId) value($comId,'$name','$username','$password',$areaId,$city,$level,'".date("Y-m-d H:i:s")."',1,$userId)");
	}
}
//打印点餐
function print_diancan_order($order){
	global $db;
	$comId = $order->comId;
	$print = $db->get_row("select * from demo_prints where comId=$comId and storeId=$order->storeId and status=1 and if_auto=1 limit 1");
	if(!empty($print)){
		require_once(ABSPATH.'/inc/print.class.php');
		$shouhuo_json = json_decode($order->shuohuo_json,true);
		$product_json = json_decode($order->product_json,true);
		$content = '';                          //打印内容
		$content .= '<FB><center>订单详情</center></FB>';
		$content .= '\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$content .= '<FB>餐桌:'.$shouhuo_json['收件人'].'</FB>\n';
		$content .= '<FB>下单时间: '.$order->dtTime.'</FB>\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$num = 0;
		if(!empty($product_json))
		{
			foreach($product_json as $k=>$v){
				$num+=$v['num'];
				$content .= '<FS>'.$v['title'].($v['key_vals']=='无'?'':'【'.$v['key_vals'].'】').'：'.$v['price_sale'].'*'.$v['num'].'</FS>\n';
			}
		}
		$content .= str_repeat('-',32)."\n";
		$content .= '\n';
		$content .= '<FS>数量: '.$num.'</FS>\n';
		$content .= '<FS>总计: '.$order->price.'元</FS>\n';
		$content .= '<FS>备注: '.$order->remark.'</FS>\n';
		$content .= '<FS>订单编号: '.$order->orderId.'</FS>\n';
		$prints = new Yprint();
		$content = $content;
		//$apiKey = "40f9b00bd79d73c056db5dcf906cbc97f02b920e";
		//$msign = 'a86n3hyzrfdy';
		//打印
		//file_put_contents('print.txt',$content);
		$prints->action_print($print->userId,$print->Tnumber,$content,$print->Akey,$print->Tkey);
	}
}
//打印加餐
function print_jiacan_order($order,$type,$pdts){
	global $db;
	$comId = $order->comId;
	$print = $db->get_row("select * from demo_prints where comId=$comId and storeId=$order->storeId and status=1 and if_auto=1 limit 1");
	if(!empty($print)){
		require_once(ABSPATH.'/inc/print.class.php');
		$shouhuo_json = json_decode($order->shuohuo_json,true);
		$product_json = json_decode($order->product_json,true);
		$content = '';                          //打印内容
		$content .= '<FB><center>加餐订单</center></FB>';
		$content .= '\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$content .= '<FB>餐桌:'.$shouhuo_json['收件人'].'</FB>\n';
		$content .= '<FB>加餐类型:'.($type==1?'加餐':'打包带走').'</FB>\n';
		$content .= str_repeat('-',32);
		$content .= '\n';
		$num = 0;
		if(!empty($pdts))
		{
			foreach($pdts as $k=>$v){
				$num+=$v['num'];
				$content .= '<FS>'.$v['title'].($v['key_vals']=='无'?'':'【'.$v['key_vals'].'】').'：'.$v['price_sale'].'*'.$v['num'].'</FS>\n';
			}
		}
		$content .= str_repeat('-',32)."\n";
		$content .= '\n';
		$content .= '<FS>数量: '.$num.'</FS>\n';
		$content .= '<FS>备注: '.$order->remark.'</FS>\n';
		$content .= '<FS>订单编号: '.$order->orderId.'</FS>\n';
		$prints = new Yprint();
		$content = $content;
		//$apiKey = "40f9b00bd79d73c056db5dcf906cbc97f02b920e";
		//$msign = 'a86n3hyzrfdy';
		//打印
		//file_put_contents('print.txt',$content);
		$prints->action_print($print->userId,$print->Tnumber,$content,$print->Akey,$print->Tkey);
	}
}
//首页积分兑换列表
function jifen(){}
//为了判断积分是否足够购买新加
function add_gouwuches(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	
	$addType = (int)$request['addType'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$item = array();
	$item['productId'] = (int)$request['productId'];
	$item['inventoryId'] = (int)$request['inventoryId'];
	$item['num'] = $request['num'];
	$jifen = $db->get_var("select jifen from demo_jifenList where inventoryId=".$item['inventoryId']);
	$user_jifen = $db->get_var("select jifen from users where id=".$userId);
	$zongjifen = $jifen*$item['num'];
	if($zongjifen>$user_jifen){
		die('{"code":0,"message":"对不起，您的积分不足！"}');
	}
	$inventory = $db->get_row("select comId,if_kuaidi from demo_product_inventory where id=".$item['inventoryId']);
	$item['comId'] = $inventory->comId;
	$item['if_kuaidi'] = $inventory->if_kuaidi;
	if($comId==1009){
		$item['lipinkaType'] = $db->get_var("select lipinkaType from demo_product_inventory where id=".$item['inventoryId']);
	}
	$content = $request['content'];
	$content = $item['inventoryId'].'@@'.$item['productId'].'@@'.$item['num'].'@@'.$item['comId'];
	add_gouwuche2($content);
}
function add_gouwuche2($content){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1 && $comId!=1009){
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	if(!empty($content)){
		$pdts = explode('||',$content);
		foreach ($pdts as $p){
			$product = explode('@@',$p);
			$item = array();
			$id = $item['inventoryId'] = $product[0];
			$item['productId'] = $product[1];
			$item['num'] = $product[2];
			$item['comId'] = $product[3];
			$kucun = 0;
			$inventory = $db->get_row("select title,key_vals,price_sale,status,if_lingshou,comId,lipinkaType,fenleiId,if_kuaidi from demo_product_inventory where id=$id");
			if($inventory->status!=1||$inventory->if_lingshou!=1){
				echo '{"code":0,"message":"商品：'.$content.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'已下架，不能下单！"}';
				exit;
			}
			if($inventory->price_sale<=0 && $inventory->fenleiId!=387){
				echo '{"code":0,"message":"商品：'.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'是非卖品，不能下单！"}';
				exit;
			}
			$kucun = get_product_kucun($id,$inventory->comId);
			if($kucun<$item['num']){
				echo '{"code":0,"message":"商品：'.$inventory->title.($inventory->key_vals=='无'?'':',规格：'.$inventory->key_vals).'库存不足，不能下单！"}';
				exit;
			}
			if($comId==1009){
				$item['lipinkaType'] = $inventory->lipinkaType;
			}
			$item['if_kuaidi'] = $inventory->if_kuaidi;
			$gouwuche[$item['inventoryId']] = $item;
		}
	}
	$g = $db->get_var("select comId from demo_gouwuche where userId=$userId and comId=$comId");
	$gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
	if(empty($g)){
		$db->query("insert into demo_gouwuche(comId,userId,content,content2) value($comId,$userId,'','$gouwucheStr')");
	}else{
		$db->query("update demo_gouwuche set content2='$gouwucheStr' where userId=$userId and comId=$comId");
	}
	echo '{"code":1,"message":"ok"}';
	exit;
}
//积分下单
function creates(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($_SESSION['demo_comId'],20);
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$shequ_id = (int)$db->get_var("select shequ_id from users where id=$userId");
	$shezhi = $db->get_row("select time_pay,storeId,user_bili,shangji_bili,fanli_type,time_tuan,shequ_yunfei from demo_shezhi where comId=".($shequ_id>0?$_SESSION['demo_comId']:$comId));
	$time_pay = $shezhi->time_pay;
	$time_pay+=1;
	$user_level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$address_id = (int)$request['address_id'];
	$address = $db->get_row("select * from user_address where id=$address_id");
	$areaId = (int)$address->areaId;
	$yhq_id = (int)$request['yhq_id'];
	$yushouId = (int)$request['yushouId'];
	$tuan_type = (int)$request['tuan_type'];
	$tuan_id = (int)$request['tuan_id'];
	$contents = $db->get_row("select content,content2 from demo_gouwuche where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
	if(!empty($contents->content2)){
		$gouwuche=json_decode($contents->content2,true);
	}else{
		//file_put_contents('request.txt',"select content,content1 from demo_gouwuche where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
		die('{"code":0,"message":"没检测到下单商品"}');
	}

	//$zhekou = get_user_zhekou();
	$check_pay_time = strtotime("+$time_pay minutes");
    $num = 0;
    $zong_price = 0;
    $zong_gonghuo_price = 0;
    $zong_weight = 0;
    $remark = $request['remark'];
    $pdtstr = '';
    $product_json_arry = array();
    $has_ids = array();
    
    $peisong_type = (int)$request['peisong_type'];
    if($peisong_type==3){
    	$shequ_id = 0;
    }
    $shequ_user_id = 0;
    if($shequ_id>0){
    	$shequ_user_id = $db->get_var("select userId from demo_shequ where id=$shequ_id");
    }
    //返利信息
    $fanli_json = array('yiji' =>0,'yiji_fanli' =>0,'erji' =>0,'erji_fanli' =>0,'sanji' =>0,'sanji_fanli' =>0,'siji' =>0,'siji_fanli' =>0,'wuji' =>0,'wuji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0,'shequ_fanli'=>0,"shequ_id"=>$shequ_user_id);
    $shop = $db->get_row("select tuijianren,tuijian_bili,pay_info,pingtai_fanli from demo_shops where comId=".$_SESSION['demo_comId']);
    

    $fanli_json['tuijian'] = $shop->tuijianren;
    //计算社区返利和团长返利
    $fanli_shequ =0;$fanli_tuanzhang = 0;
    foreach ($gouwuche as $i=>$g) {
    	$has_ids[] = $g['inventoryId'];
        $nowProductId = $g['productId'];
        $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=".$g['inventoryId']);
        if($tuan_type>0){
        	$tuan_inventory = $inventory;
        }
        $order_comId = $inventory->comId;
        if(!empty($yushouId)){
        	$now = date("Y-m-d H:i:s");
        	$yushou = $db->get_row("select * from yushou where id=$yushouId and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
            if(empty($yushou)){
                die('{"code":0,"message":"预售活动已结束"}');
            }
            $left = $yushou->num - $yushou->num_saled;
            if($g['num']>$left){
            	die('{"code":0,"message":"库存不足，下单失败!"}');
            }
            $price_json = json_decode($yushou->price_json,true);
           	$price = $price_json[0]['price'];
            if($yushou->type==2){
                $columns = array_column($price_json,'num');
                array_multisort($columns,SORT_DESC,$price_json);
                foreach ($price_json as $val) {
                    if($yushou->num_saled>=$val['num']){
                        $price = $val['price'];
                        break;
                    }
                }
            }
            $zong_price = $price*$g['num'];
            $db->query("update yushou set num_saled=num_saled+".$g['num']." where id=$yushouId");
        }else{
        	if($inventory->status!=1){
	        	die('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
	        }
	        $kucun = get_product_kucun($g['inventoryId'],$inventory->comId);
	        if($g['num']>$kucun)$g['num']=$kucun;
	        if($kucun<=0){
	        	die('{"code":0,"message":"商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”库存不足"}');
	        }
	        if($tuan_type==1){
	        	$price = $inventory->price_tuan;
	        }else if($tuan_type==2){
	        	$price = $inventory->price_shequ_tuan;
	        }else{
	        	$price = get_user_zhekou($inventory->id,$inventory->price_sale);
	        }
	        $zong_price+=$price*$g['num'];
        }
        $zong_gonghuo_price+=$inventory->price_cost*$g['num'];
        $yunfei_moban = (int)$db->get_var("select yunfei_moban from demo_product where id=".$g['productId']);
        $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
        $num+=(int)$g['num'];
        
        $zong_weight+=$inventory->weight*$g['num'];
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $g['productId'];
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['weight'] = $inventory->weight;
        $pdt['num'] = $g['num'];
        $pdt['jifen'] = $db->get_var("select jifen from demo_jifenList where inventoryId=".$g['inventoryId']." limit 1");
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $price;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
        $pdt['price_card'] = $inventory->price_card;
        $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
        $units_arr = json_decode($units);
        $pdt['unit'] = $units_arr[0]->title;
        $product_json_arry[] = $pdt;
        if(!empty($yushouId)){
        	break;
        }
        //$fanli_shequ +=$inventory->fanli_shequ*$g['num'];
        //$fanli_tuanzhang +=$inventory->fanli_tuanzhang*$g['num'];
    }
    
    //价格相关
    $price_json = new StdClass();
    $price_json_product = new StdClass();
    $price_json_product->price = $zong_price;
    $price_json_product->desc = '';
    $price_json->goods = $price_json_product;
    if(!empty($pdtstr)){
        $pdtstr = substr($pdtstr,1);
        $pdt_arr = json_decode('['.$pdtstr.']');
    }
 	$cuxiao_money = $zong_price;
 	if(!empty($lpk_id) && !empty($lpk_kedi)){
 		$cuxiao_money-=$lpk_kedi;
 	}
 	//限购判断
 	/*$xiangou_sql = "insert into cuxiao_pdt_buy(cuxiao_id,inventoryId,userId,num,comId,orderId) values";
 	$xiangou_sql1 = '';
    $pdt_cuxiao = get_pdt_cuxiao($pdt_arr,$areaId,$cuxiao_money);
    if(!empty($pdt_cuxiao['cuxiao_ids'])){
    	foreach ($pdt_cuxiao['cuxiao_ids'] as $key => $cuxiaoId) {
    		$cuxiao_pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$cuxiaoId");
    		$pdtArr = explode(',',$cuxiao_pdtIds);
    		foreach ($gouwuche as $i=>$g) {
    			$inventId = $g['inventoryId'];
    			$num = (int)$g['num'];
    			if(in_array($inventId,$pdtArr)){
    				$buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
    				$xiangou_num = (int)$db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiaoId and inventoryId=$inventId");
    				if($xiangou_num>0 && ($buy_num+$num)>$xiangou_num){
    					$inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card from demo_product_inventory where id=$inventId");
    					die('{"code":0,"message":"下单失败，商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”限购'.$xiangou_num.'份！您还可购买'.($xiangou_num-$buy_num).'份"}');
    				}else{
    					$xiangou_sql1.=",($cuxiaoId,$inventId,$userId,$num,$comId,order_id)";
    				}
    			}
    		}
    	}
    }*/
    
    if($pdt_cuxiao['jian']>0){
        $zong_price-=$pdt_cuxiao['jian'];
        $price_json_cuxiao = new StdClass();
	    $price_json_cuxiao->price = $pdt_cuxiao['jian'];
	    $price_json_cuxiao->desc = $pdt_cuxiao['cuxiao_title'];
	    $price_json->cuxiao = $price_json_cuxiao;
    }
    //订单促销
    $order_cuxiao = get_order_cuxiao($zong_price,$areaId);
    if($order_cuxiao['jian']>0){
        $zong_price-=$order_cuxiao['jian'];
        $price_json_order = new StdClass();
	    $price_json_order->price = $order_cuxiao['jian'];
	    $price_json_order->desc = $order_cuxiao['cuxiao_title'];
	    $price_json->cuxiao_order = $price_json_order;
    }
    
    //获取运费
    /*if($shequ_id>0){
    	$yunfei = 0;
    	if($peisong_type==2 && !empty($shezhi->shequ_yunfei)){
    		$shequ_yunfei = json_decode($shezhi->shequ_yunfei);
    		$yunfei = $shequ_yunfei->peisong_money;
    		if($shequ_yunfei->peisong_man>0 && $zong_price>=$shequ_yunfei->peisong_man){
    			$yunfei = 0;
    		}
    	}
    }else{
    	$yunfei = get_yunfei($pdt_arr,$zong_price,$areaId);
    }*/
    $yunfei = 0;
    
    //获取优惠券
    $now = date("Y-m-d H:i:s");
    $yhq = $db->get_row("select id,yhqId,title,man,jian,jiluId from user_yhq$fenbiao where id=$yhq_id and userId=$userId and status=0 and endTime>='$now' and startTime<='$now'");

	$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
    $jifen = 0;
    $storeId = get_fahuo_store($areaId,$order_comId);
	$shouhuo_json = array();
	if(!empty($address)){
		$shouhuo_json['收件人'] = $address->name;
		$shouhuo_json['手机号'] = $address->phone;
		$shouhuo_json['所在地区'] = $address->areaName;
		$shouhuo_json['详细地址'] = $address->address;
	}else if(!empty($shequ_id) && $tuan_type==2){
		$shequ = $db->get_row("select * from demo_shequ where id=$shequ_id");
		$shouhuo_json['收件人'] = $shequ->name;
		$shouhuo_json['手机号'] = $shequ->phone;
		$shouhuo_json['所在地区'] = $db->get_var("select title from demo_area where id=$shequ->areaId");
		$shouhuo_json['详细地址'] = $shequ->address;
	}
	
	$order = array();
	$order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
	$order['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['if_tongbu']==1){
		$order['zhishangId'] = $userId;
	}
	$order['comId'] = (int)$order_comId;
	$order['mendianId'] = 0;
	$order['yushouId'] = 0;
	$order['type'] = $tuan_type>0?$tuan_type:1; //2社区团 1普通订单或普通团单
	$order['status'] = -5;//待支付
	$order['dtTime'] = date("Y-m-d H:i:s");
	$order['remark'] = $remark;
	$order['pay_endtime'] = date("Y-m-d H:i:s",$check_pay_time);
	$order['price'] = 0;
	if($yushou->paytype==2){
		$order['price_dingjin'] = $yushou->dingjin;
	}
	$order['inventoryId'] = (int)$tuan_inventory->id;
	$order['storeId'] = $storeId;
	$order['pdtNums'] = $num;
	$order['pdtChanel'] = 0;
	$order['ifkaipiao'] = 0;
	$order['weight'] = $zong_weight;
	$order['jifen'] = 0;
	$order['areaId'] = $areaId;
	$order['address_id'] = $address_id;
	$order['product_json'] = $product_json;
	$order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
	$order['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
	$order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
	$order['ifkaipiao'] = (int)$request['if_fapiao'];
	if($request['if_fapiao']>0){
		$fapiao_json = array();
		$fapiao_json['发票类型'] = $request['fapiao_leixing'];
		$fapiao_json['抬头类型'] = $request['fapiao_type']==1?'个人':'公司';
		if($request['fapiao_type']==2){
			$fapiao_json['公司名称'] = $request['fapiao_com_title'];
			$fapiao_json['识别码'] = $request['fapiao_shibiema'];
			$fapiao_json['注册地址'] = $request['fapiao_address'];
			$fapiao_json['注册电话'] = $request['fapiao_phone'];
			$fapiao_json['开户银行'] = $request['fapiao_bank_name'];
			$fapiao_json['银行账号'] = $request['fapiao_bank_card'];
		}
		$fapiao_json['发票明细'] = $request['fapiao_cont'];
		$fapiao_json['收票人手机'] = $request['shoupiao_phone'];
		$fapiao_json['收票人邮箱'] = $request['shoupiao_email'];
		$order['fapiao_json'] = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
		if($request['fapiao_id']==0){
			$fapiao = array();
			$fapiao['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
			$fapiao['comId'] = $comId;
			$fapiao['type'] = 1;
			$fapiao['com_title'] = trim($request['fapiao_com_title']);
			$fapiao['shibiema'] = trim($request['fapiao_shibiema']);
			$fapiao['shoupiao_phone'] = trim($request['shoupiao_phone']);
			$fapiao['shoupiao_email'] = trim($request['shoupiao_email']);
			$db->insert_update('user_fapiao',$fapiao,'id');
		}
	}
	if($_SESSION['demo_comId']==1009){
		$order['lipinkaType'] = $lipinkaType;
		if($lipinkaType==2){
			$lipinkaInfo = array();
			$lipinkaInfo['tiqu_type']=$lipinka_tiqu_type;
			$lipinkaInfo['tiqu_phone']=$tiqu_phone;
			$order['lipinkaInfo'] = json_encode($lipinkaInfo,JSON_UNESCAPED_UNICODE);
		}
	}
	$order['if_zong'] = $_SESSION['demo_comId']==10?1:0;
	$order['shequ_id'] = $shequ_id;
	$order['peisong_type'] = (int)$request['peisong_type'];
	$order['peisong_time'] = $request['peisong_time'];
	$order['tuan_id'] = $tuan_id;
	$order_fenbiao = getFenbiao($order_comId,20);
	//file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
	$db->insert_update('order'.$order_fenbiao,$order,'id');
	$order_id = $db->get_var("select last_insert_id();");
	/*if(!empty($xiangou_sql1)){
		$xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
    	$xiangou_sql1 = substr($xiangou_sql1,1);
    	$db->query($xiangou_sql.$xiangou_sql1);
    }*/
	$timed_task = array();
	$timed_task['comId'] = (int)$_SESSION['demo_comId'];
	$timed_task['dtTime'] = $check_pay_time;
	$timed_task['router'] = 'order_checkPay';
	$timed_task['params'] = '{"order_id":'.$order_id.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
	/*if(!empty($yhq_id)){
		$db->query("update user_yhq$fenbiao set status=1,orderId=$order_id where id=$yhq_id");
	}*/
	$gouwuches = array();
	if(!empty($contents->content)){
		$gouwuches = json_decode($contents->content,true);
	}
	foreach ($product_json_arry as $detail) {
		$pdt = new StdClass();
		$pdt->sn = $detail['sn'];
		$pdt->title = $detail['title'];
		$pdt->key_vals = $detail['key_vals'];
		$order_detail = array();
		$order_detail['comId'] = (int)$order_comId;
		$order_detail['mendianId'] = 0;
		$order_detail['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$order_detail['orderId'] = $order_id;
		$order_detail['inventoryId'] = $detail['id'];
		$order_detail['productId'] = $detail['productId'];
		$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
		$order_detail['num'] = $detail['num'];
		$order_detail['unit'] = $detail['unit'];
		$order_detail['unit_price'] = $detail['price_sale'];
		if($_SESSION['demo_comId']==1009){
			$order_detail['lipinkaId'] = $db->get_var("select lipinkaId from demo_product_inventory where id=".$detail['id']);
		}
		$db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
		if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
		if($tuan_type==0){
			$db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
		}
	}
	/*$lpk_id = (int)$request['lpk_id'];
	$lpk_kedi = $request['lpk_kedi'];
	if(!empty($lpk_id) && !empty($lpk_kedi)){
    	card_pay($order_id,$_SESSION['demo_comId'],$lpk_id,$lpk_kedi);
    }
	$content = '';
	if(!empty($gouwuches)){
		$content=json_encode($gouwuches,JSON_UNESCAPED_UNICODE);
	}
	$db->query("update demo_gouwuche set content='$content' where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));*/
	die('{"code":1,"message":"下单成功","order_id":'.$order_id.',"comId":'.$order_comId.'}');
}
