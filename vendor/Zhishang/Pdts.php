<?php
namespace Zhishang;
class Pdts{
	public function lists(){
		global $db,$request,$comId;
		$shi_id = (int)$request['city_id'];
		$city_name = $request['city_name'];
		if(empty($shi_id) && !empty($city_name)){
			$shi_id = (int)$db->get_var("select id from demo_area where title='$city_name' limit 1");
		}
		if(empty($shi_id)){
			return '{"code":0,"message":"没找到对应的城市"}';
		}
		$keyword = $request['keyword'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
		//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力
		$if_cache = 0;
		if(empty($keyword)){
			//$if_cache = 1;
		}
		if($if_cache==1){
			$chache_file = 'pdt_'.$comId.'-'.$shi_id.'-'.$page.'-'.$pageNum.'.dat';
			$cache_content = file_get_contents(ABSPATH.'/cache/'.$chache_file);
			if(!empty($cache_content)){
				$now = time();
				$caches = json_decode($cache_content);
				if($caches->endTime>$now){
					return $cache_content;
					//exit;
				}
			}
		}
		if($comId==10){
			$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime,if_kuaidi from demo_pdt_inventory where shiId=$shi_id and if_tongbu=1 and endTime>'".date("Y-m-d H:i:s")."'";
			$user_bili = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=10");
		}else{
			$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime,if_kuaidi from demo_pdt_inventory where comId=$comId and shiId=$shi_id and endTime>'".date("Y-m-d H:i:s")."'";
			$user_bili = $db->get_row("select shangji_bili from demo_shezhi where comId=$comId");
		}
		if(!empty($keyword)){
			$sql.=" and title like '%$keyword%'";
		}
		if($comId==10){
			$sql.=" and status=1 and zong_status=1";
		}else{
			$sql.=" and status=1";
		}
		$count = $db->get_var(str_replace('productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime,if_kuaidi','count(distinct(productId))',$sql));
		$sql.=" group by productId";
		$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
		//file_put_contents('request.txt',$sql);
		$pdts = $db->get_results($sql);
		//print_r($pdts);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['city_id'] = $shi_id;
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		//$zhekou = get_user_zhekou();
		$now = date("Y-m-d H:i:s");
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$data = array();
				$data['inventory_id'] = $pdt->inventoryId;
				$data['product_id'] = $pdt->productId;
				$data['title'] = $pdt->title;
				$data['img'] = ispic($pdt->image);
				$data['price_sale'] = $this->getXiaoshu($pdt->price_sale,2);
				$data['price_market'] = $this->getXiaoshu($pdt->price_market,2);
				$data['orders'] = $pdt->orders;
				$data['area'] = $db->get_var("select title from demo_area where id=$pdt->sale_area");
				if($comId==10){
					$fanli1 = $this->getXiaoshu(($pdt->price_sale-$pdt->price_cost)*$user_bili->user_bili/100,2);
					$fanli2 = $this->getXiaoshu(($pdt->price_sale1-$pdt->price_cost1)*$user_bili->user_bili/100,2);
				}else{
					$fanli1 = $pdt->fanli_tuanzhang;
					$fanli2 = $pdt->fanli_tuanzhang1;
				}
				$fanli1 = $this->getXiaoshu($fanli1*(100-$user_bili->shangji_bili)/100,2);
				$fanli2 = $this->getXiaoshu($fanli2*(100-$user_bili->shangji_bili)/100,2);
				if($fanli1==$fanli2){
					$data['fanli'] = $fanli1;
				}else{
					$data['fanli'] = $fanli1.'-'.$fanli2;
				}

				$data['jishiqi'] = 0;
				if(strtotime($pdt->endTime)-time()<86400){
					$data['jishiqi'] = 1;
				}
				$data['endTime'] =$pdt->endTime;
				$data['if_kuaidi'] =$pdt->if_kuaidi;
				$return['data'][] = $data;
			}
		}
		if($if_cache==1){
			$cache_endtime = strtotime('+10 minutes');
			$return['endTime'] = $cache_endtime;
		}
		$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
		if($if_cache==1){
			file_put_contents(ABSPATH.'/cache/'.$chache_file,$cache_content,LOCK_EX);
		}
		return $cache_content;
	}
	function detail(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$id = (int)$request['inventory_id'];
		$product_inventory = $db->get_row("select * from demo_pdt_inventory where id=$id and status=1 and endTime>'".date('Y-m-d H:i:s')."'");
		if(empty($product_inventory)){
			return '{"code":0,"message":"产品已下架"}';
		}
		$nowSelect = array();
		if(!empty($product_inventory->key_ids)){
			$nowSelect = explode('-', $product_inventory->key_ids);
		}
		$productId = $product_inventory->productId;
		$product = $db->get_row("select * from demo_pdt where id=$productId");
		$inventorys = array();
		$keys = $db->get_results("select * from demo_pdt_inventory where productId=$product_inventory->productId order by id asc");
		if(count($keys)>1){
			foreach ($keys as $key){
				$k = array();
				$k['inventory_id']=$key->id;
				$k['key_vals'] = $key->key_vals;
				$k['sn'] = $key->sn;
				$k['price_sale'] = $key->price_sale;
				$k['price_market'] = $key->price_market;
				$k['is_selected'] = $key->id==$product_inventory->id?1:0;
				$k['kucun'] = $product_inventory->kucun;
				$inventorys[] = $k;
			}
		}
		$originalPics = array();
		if(!empty($product->originalPic)){
		    $originalPics = explode('|',$product->originalPic);
		}
		$now = date("Y-m-d H:i:s");
		$shezhi = $db->get_row("select com_phone,com_title,com_address,zuobiao,user_bili,shangji_bili from demo_shezhi where comId=$product_inventory->comId");
		$fanli_pdt = $db->get_row("select min(price_sale) as price_sale,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1 from demo_pdt_inventory where productId=$productId");
		if($comId==10){
		    $pdt_shezhi = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=10");
		    $shezhi->user_bili=$pdt_shezhi->user_bili;
		    $shezhi->shangji_bili=$pdt_shezhi->shangji_bili;
		    $fanli1 = $this->getXiaoshu(($fanli_pdt->price_sale-$fanli_pdt->price_cost)*$shezhi->user_bili/100,2);
		    $fanli2 = $this->getXiaoshu(($fanli_pdt->price_sale1-$fanli_pdt->price_cost1)*$shezhi->user_bili/100,2);
		}else{
		    $fanli1 = $fanli_pdt->fanli_tuanzhang;
		    $fanli2 = $fanli_pdt->fanli_tuanzhang1;
		}
		$fanli1 = $this->getXiaoshu($fanli1*(100-$shezhi->shangji_bili)/100,2);
		$fanli2 = $this->getXiaoshu($fanli2*(100-$shezhi->shangji_bili)/100,2);
		if(!empty($shezhi)){
		    $zuobiaos = explode('|',$shezhi->zuobiao);
		    if(!empty($zuobiaos)){
		        $zuobiao = $zuobiaos[1].','.$zuobiaos[0];
		    }
		}
		$product->cont1 = str_replace('src="/','src="http://'.$_SERVER['HTTP_HOST']."/",$product->cont1);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['inventory_id'] = $product_inventory->id;
		$return['data']['product_id'] = $product_inventory->productId;
		$return['data']['title'] = $product_inventory->title;
		$return['data']['if_user_info'] = $product->if_user_info;
		$return['data']['key_vals'] = $product_inventory->key_vals=='无'?'':$product_inventory->key_vals;
		$return['data']['sn'] = $product_inventory->sn;
		$return['data']['imgs'] = $originalPics;
		$return['data']['orders'] = (int)$db->get_var("select sum(orders) from demo_pdt_inventory where productId=$product_inventory->productId");
		$return['data']['kucun'] = $product_inventory->kucun;
		$return['data']['price_sale'] = $this->getXiaoshu($product_inventory->price_sale,2);
		$return['data']['price_market'] = $this->getXiaoshu($product_inventory->price_market,2);
		$return['data']['fanli'] = $fanli1==$fanli2?$fanli1:$fanli1.'-'.$fanli2;
		$return['data']['inventorys'] = $inventorys;
		$return['data']['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','',$product->cont1);
		$return['data']['shop_name'] = $shezhi->com_title;
		$return['data']['shop_phone'] = $shezhi->com_phone;
		$return['data']['shop_address'] = $shezhi->com_address;
		$return['data']['shop_zuobiao'] = $shezhi->zuobiao;
		$return['data']['if_kuaidi'] = $product_inventory->if_kuaidi;
		if($comId==10){
			$share_url = 'https://buy.zhishangez.com/index.php?p=22&a=view&id='.$id.'&tuijianren='.$userId;
		}else{
			$share_url = 'http://'.$comId.'.buy.zhishangez.com/index.php?p=22&a=view&id='.$id.'&tuijianren='.$userId;
		}
		$share_file = 'cache/pdts_qrcode/'.$comId.'_'.$id.'_'.$userId.'.png';
		$return['data']['share_qrcode'] = 'https://buy.zhishangez.com/'.$share_file;
		if(!is_file(ABSPATH.$share_file)){
			//echo ABSPATH.'erp/phpqrcode.php';
			require_once(ABSPATH.'erp/phpqrcode.php');
			\QRcode::png($share_url,$share_file,'L',8);
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function citys(){
		global $db,$request,$comId;
		$channels = array();
		if(is_file("cache/pdt_area_$comId.php")){
		  $cache = 1;
		  $content = file_get_contents("cache/pdt_area_$comId.php");
		  $channels = json_decode($content);
		}
		if(empty($channels)){
		    $areas = $db->get_results("select * from demo_pdt_area where comId=$comId order by orders asc");
		    if(!empty($areas)){
		        $now_orders = '';
		        foreach ($areas as $area){
		            $channels[$area->orders][] = $area;
		        }
		    }
		}
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$remens = array();
		if(!empty($channels)){
			foreach ($channels as $channel) {
				foreach ($channel as $c) {
					if($c->if_remen==1){
						$remens[] = $c;
					}
				}
			}
		}
		$return['data']['hot_citys'] = $remens;
		$return['data']['citys'] = $channels;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function getXiaoshu($num,$weishu=2){
		return str_replace(',','',number_format($num,$weishu));
	}
}