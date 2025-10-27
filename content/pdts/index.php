<?php
function index(){}
function view(){}
function queren(){}
function pay(){}
function orders(){}
function view_order(){}
function select_shi(){}
function haibao(){}
function qr_shouhuo(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$id = (int)$request['orderId'];
	$db->query("update demo_pdt_order set status=4 where id=$id and userId=$userId");
	die('{"code":1,"message":"成功"}');
}
function get_pdt_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$shi_id = (int)$request['shi_id'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$order1 = 'endTime';
	$order2 = 'desc,id desc';
	//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力
	$if_cache = 0;
	if(empty($keyword)){
		$if_cache = 1;
	}
	if($if_cache==1){
		$chache_file = 'pdt_'.$comId.'-'.$shi_id.'-'.$page.'-'.$pageNum.'.dat';
		$cache_content = file_get_contents(ABSPATH.'/cache/'.$chache_file);
		if(!empty($cache_content)){
			$now = time();
			$caches = json_decode($cache_content);
			if($caches->endTime>$now){
				echo $cache_content;
				exit;
			}
		}
	}
	$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime from demo_pdt_inventory where comId=$comId and shiId=$shi_id and endTime>'".date("Y-m-d H:i:s")."' ";
	if($comId==10){
		$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime from demo_pdt_inventory where shiId=$shi_id and if_tongbu=1 and endTime>'".date("Y-m-d H:i:s")."' ";
		$user_bili = $db->get_row("select user_bili,shangji_bili from demo_shezhi where comId=10");
	}else{
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
	//file_put_contents('request.txt',$sql);
	$count = $db->get_var(str_replace('productId,min(id) as inventoryId,min(price_sale) as price_sale,sum(orders) as orders,title,dtTime,image,price_market,comId,ordering,sale_area,max(price_sale) as price_sale1,min(price_cost) as price_cost,max(price_cost) as price_cost1,min(fanli_tuanzhang) as fanli_tuanzhang,max(fanli_tuanzhang) as fanli_tuanzhang1,endTime','count(distinct(productId))',$sql));
	$sql.=" group by productId";
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	//$zhekou = get_user_zhekou();
	$now = date("Y-m-d H:i:s");
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			//$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_card,originalPic from demo_product_inventory where id=$pdt->inventoryId");
			//$pro = $db->get_row("select brandId,price_name,originalPic,untis from demo_product where id=$pdt->productId");
			$data = array();
			$data['id'] = $pdt->productId;
			$data['title'] = $pdt->title;
			$data['img'] = ispic($pdt->image);
			$data['inventoryId'] = $pdt->inventoryId;
			$data['price_sale'] = getXiaoshu($pdt->price_sale,2);
			$data['price_market'] = getXiaoshu($pdt->price_market,2);
			$data['orders'] = $pdt->orders;
			if($comId==10){
				$fanli1 = getXiaoshu(($pdt->price_sale-$pdt->price_cost)*$user_bili->user_bili/100,2);
				$fanli2 = getXiaoshu(($pdt->price_sale1-$pdt->price_cost1)*$user_bili->user_bili/100,2);
			}else{
				$fanli1 = $pdt->fanli_tuanzhang;
				$fanli2 = $pdt->fanli_tuanzhang1;
			}
			$fanli1 = getXiaoshu($fanli1*(100-$user_bili->shangji_bili)/100,2);
			$fanli2 = getXiaoshu($fanli2*(100-$user_bili->shangji_bili)/100,2);
			if($fanli1==$fanli2){
				$data['fanli'] = $fanli1;
			}else{
				$data['fanli'] = $fanli1.'-'.$fanli2;
			}

			$data['jishiqi'] = 0;
			if(strtotime($pdt->endTime)-time()<86400){
				$data['jishiqi'] = 1;
			}
			$data['endTime'] = strtotime($pdt->endTime)*1000;
			$data['area'] = $db->get_var("select title from demo_area where id=$pdt->sale_area");
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
	echo $cache_content;
	exit;
}
function get_order_list (){
	global $db,$request;
	$scene = (int)$request['scene'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	if($comId==10){
		$sql = "select * from demo_pdt_order where zhishangId=$userId and if_zong=1";
	}else{
		$sql="select * from demo_pdt_order where comId=$comId and userId=$userId and if_zong=0 ";
	}
	if(!empty($scene)){
		switch($scene){
			case 1:
				$sql.=" and status=-5";
			break;
			case 2:
				$sql.=" and status=4 and ishexiao<hexiaos";
			break;
			case 3:
				$sql.=" and status=4 and ishexiao>=hexiaos";
			break;
		}
	}
	if(!empty($keyword)){
		$sql.=" and product_json like '%$keyword%' ";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	$now = time();
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$data = array();
			$data['id'] = $pdt->id;
			$data['orderId'] = $pdt->orderId;
			switch ($pdt->status) {
				case 0:
					$data['statusInfo'] = '<span style="color:#cf2950;">待审核</span>';
				break;
				case 2:
					$data['statusInfo'] = '<span style="color:#cf2950;">待发货</span>';
				break;
				case 3:
					$data['statusInfo'] = '<span style="color:#cf2950;">待收货</span>';
				break;
				case 4:
					if($pdt->iehexiao>=$pdt->hexiaos){
						$data['statusInfo'] = '<span style="color:green;">已完成</span>';
						$pdt->status = 5;
					}else{
						$data['statusInfo'] = '<span style="color:green;">待核销</span>';
					}
				break;
				case -5:
					$data['statusInfo'] = '<span style="color:#cf2950;">待支付</span>';
				break;
				case -1:
					$data['statusInfo'] = '<span>无效</span>';
				break;
			}
			$product_json = json_decode($pdt->product_json);
			$data['products'] = $product_json;
			$data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$data['endTime'] = strtotime($pdt->pay_endtime)*1000;
			$data['jishiqi'] = 0;
			if($data['statusInfo']=='<span style="color:#cf2950;">待支付</span>' && $pdt->yushouId==0){
				$data['jishiqi'] = 1;
			}
			//$data['jishiqi'] = $data['statusInfo']=='<span style="color:#cf2950;">待支付</span>'?1:0;
			$data['price'] = $pdt->price;
			$data['price_payed'] = $pdt->price_payed;
			$data['num'] = $pdt->pdtNums;
			$data['comId'] = $pdt->comId;
			$data['status'] = $pdt->status;
			if($pdt->status==4){
				$data['hexiaoma'] = get_36id($pdt->id);
			}
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_pdtsn_info(){
	global $db,$request;
	$productId = (int)$request['productId'];
	$key_ids = $request['key_ids'];
	$i = $db->get_row("select * from demo_pdt_inventory where productId=$productId and key_ids='$key_ids' and status=1 ".($_SESSION['demo_comId']==10?'and zong_status=1':'')." limit 1");
	//$zhekou = get_user_zhekou();
	$retrun = array();
	if(!empty($i)){
		$retrun['code'] = 1;
		$retrun['message'] = '成功';
		$inventoryId = $i->id;
		$retrun['inventoryId'] = $inventoryId;
		$retrun['kucun'] = $i->kucun;
		$retrun['sn'] = $i->sn;
		$retrun['price'] = getXiaoshu($i->price_sale,2);
		$retrun['price_market'] = $i->price_market;
	}else{
		$retrun['code'] = 1;
		$retrun['message'] = '成功';
		$retrun['inventoryId'] = 0;
		$retrun['price'] = 0;
		$retrun['price_market'] = 0;
		$retrun['sn'] = '无';
		$retrun['kucun'] = 0;
	}
	echo json_encode($retrun,JSON_UNESCAPED_UNICODE);
	exit;
}
function add_gouwuche(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$item = array();
	$item['productId'] = (int)$request['productId'];
	$item['inventoryId'] = (int)$request['inventoryId'];
	$item['num'] = $request['num'];
	$inventory = $db->get_row("select title,key_vals,price_sale,status,comId from demo_pdt_inventory where id=".$item['inventoryId']);
	$item['comId'] = $inventory->comId;
	$gouwuche = array();
	$gouwuche[$item['inventoryId']] = $item;
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
function create(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$shezhi = $db->get_row("select time_pay,user_bili,shangji_bili,fanli_type from demo_shezhi where comId=$comId");
	$time_pay = $shezhi->time_pay;
	$time_pay+=1;
	$user_level = (int)$_SESSION[TB_PREFIX.'user_level'];
	$contents = $db->get_row("select content2 from demo_gouwuche where userId=$userId and comId=".($_SESSION['demo_comId']==1009?'1009':$comId));
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
    $remark = $request['remark'];
    $pdtstr = '';
    $product_json_arry = array();
    $has_ids = array();
    //返利信息
    $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0);
    $shop = $db->get_row("select tuijianren,tuijian_bili,pay_info,pingtai_fanli from demo_shops where comId=".$_SESSION['demo_comId']);
    if($comId==10){
    	$db_service = getCrmDb();
    	$u = $db_service->get_row("select shangji,shangshangji,tuan_id,user_info from demo_user where id=$userId");
    	$fanli_json['shangji'] = $u->shangji;
    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;
    }else{
    	$u = $db->get_row("select shangji,shangshangji,tuan_id,user_info from users where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
    	$fanli_json['shangji'] = $u->shangji;
    	$fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangshangji;//根据返利类型设定返利的上上级会员
    }
    $fanli_json['tuijian'] = (int)$shop->tuijianren;
    //计算社区返利和团长返利
    $fanli_shequ =0;$fanli_tuanzhang = 0;
    foreach ($gouwuche as $i=>$g) {
    	$has_ids[] = $g['inventoryId'];
        $nowProductId = $g['productId'];
        $inventory = $db->get_row("select * from demo_pdt_inventory where id=".$g['inventoryId']);
        if($inventory->kucun<$g['num']){
        	die('{"code":0,"message":"下单失败！商品“'.$inventory->title.'”库存不足"}');
        }
        $product = $db->get_row("select youxiaoqi_start,youxiaoqi_end from demo_pdt where id=$inventory->productId");
        $order_comId = $inventory->comId;
    	if($inventory->status!=1){
        	die('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
        }
	    $price = $inventory->price_sale;
	    $zong_price+=$price*$g['num'];
        $zong_gonghuo_price+=$inventory->price_cost*$g['num'];
        
        $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":0,"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
        $num+=(int)$g['num'];
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $g['productId'];
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['num'] = $g['num'];
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $price;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
        $product_json_arry[] = $pdt;
        $fanli_tuanzhang +=$inventory->fanli_tuanzhang*$g['num'];
        $db->query("update demo_pdt_inventory set kucun=kucun-".$g['num']." where id=$inventory->id");
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
    //返利相关
    $zongfanli = $zong_price-$zong_gonghuo_price;//商家返利
    if($_SESSION['demo_comId']==10 && $zongfanli>0){
    	$fanli_json['shop_fanli'] = $zong_gonghuo_price;
		$user_fanli = intval($zongfanli*$shezhi->user_bili)/100;
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
		$fanli_json['pingtai_fanli'] = $zongfanli-$fanli_json['shangshangji_fanli']-$fanli_json['shangji_fanli']-$fanli_json['tuijian_fanli'];
		$fanli_json['if_shop_fanli'] = 0;
		$fanli_json['user_type'] = 1;//1总平台  2商家平台 确认收货时按user_type判断用户返利去向
    }else{
    	$fanli_json['user_type'] = 2;
		$user_fanli = $fanli_tuanzhang;
		$fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
		if(!empty($fanli_json['shangji'])){
			$fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli'];
		}
		if(empty($fanli_json['shangshangji'])){
			$fanli_json['shangshangji_fanli'] = 0;
		}
		if($shop->pingtai_fanli>0){
			$fanli_json['pingtai_fanli'] = intval($zong_price*$shop->pingtai_fanli)/100;
			$fanli_json['shop_fanli'] = $zong_price-$fanli_json['pingtai_fanli'];
		}else{
			$fanli_json['shop_fanli'] = $zong_price;
			$fanli_json['pingtai_fanli'] = 0;
		}
		$fanli_json['if_shop_fanli'] = 0;
    }
    //获取优惠券
    $now = date("Y-m-d H:i:s");
	$product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
    //$jifen = get_order_jifen($pdt_arr,$zong_price);
    //$storeId = get_fahuo_store($areaId,$order_comId);
	$order = array();
	$order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
	$order['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if($_SESSION['demo_comId']==10){
		$order['if_zong'] = 1;
		$order['zhishangId'] = $_SESSION[TB_PREFIX.'zhishangId'];
	}
	$order['comId'] = (int)$order_comId;
	$order['mendianId'] = (int)$order_comId;
	$order['status'] = -5;//待支付
	$order['dtTime'] = date("Y-m-d H:i:s");
	//$order['remark'] = $remark;
	$order['price'] = $zong_price;
	$order['pdtNums'] = $num;
	$order['pdtChanel'] = 0;
	$order['product_json'] = $product_json;
	$order['inventoryId'] = $inventory->id;
	$order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
	$order['hexiaos'] = $inventory->hexiaos;
	$order['youxiaoqi_start'] = $product->youxiaoqi_start;
	$order['youxiaoqi_end'] = $product->youxiaoqi_end;
	if($request['if_kuaidi']==1){
		$order['address_id'] = (int)$request['address_id'];
	}
	if(!empty($request['name']) && !empty($request['phone'])){
		$order['userInfo'] = '姓名：'.$request['name'].' 电话：'.$request['phone'];
		if(!empty($u->user_info)){
			$user_info = json_decode($u->user_info,true);
			$user_info['name'] = $request['name'];
			$user_info['phone'] = $request['phone'];
		}else{
			$user_info = array('name'=>$request['name'],'phone'=>$request['phone']);
		}
		if($comId==10){
			$db_service->query("update demo_user set user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
		}else{
			$db->query("update users set user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
		}
	}
	//file_put_contents('request.txt',json_encode($order,JSON_UNESCAPED_UNICODE));
	$db->insert_update('demo_pdt_order',$order,'id');
	$order_id = $db->get_var("select last_insert_id();");
	$timed_task = array();
	$timed_task['comId'] = (int)$_SESSION['demo_comId'];
	$timed_task['dtTime'] = $check_pay_time;
	$timed_task['router'] = 'order_checkPdtPay';
	$timed_task['params'] = '{"order_id":'.$order_id.'}';
	$db->insert_update('demo_timed_task',$timed_task,'id');
	foreach ($product_json_arry as $detail) {
		$pdt = new StdClass();
		$pdt->sn = $detail['sn'];
		$pdt->title = $detail['title'];
		$pdt->key_vals = $detail['key_vals'];
		$order_detail = array();
		$order_detail['comId'] = (int)$order_comId;
		$order_detail['mendianId'] = (int)$order_comId;
		$order_detail['userId'] = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$order_detail['orderId'] = $order_id;
		$order_detail['inventoryId'] = $detail['id'];
		$order_detail['productId'] = $detail['productId'];
		$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
		$order_detail['num'] = $detail['num'];
		$order_detail['dtTime'] = date("Y-m-d H:i:s");
		$db->insert_update('pdt_order_detail',$order_detail,'id');
	}
	
	die('{"code":1,"message":"下单成功","order_id":'.$order_id.',"comId":'.$order_comId.'}');
}
function yue_pay(){
	global $db,$request;
	$orderId = (int)$request['order_id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$order_comId = (int)$request['comId'];
	if(empty($order_comId))$order_comId = $comId;
	/*if($_SESSION['if_tongbu']==1){
		$db_service = getCrmDb();
		$comId = 10;
		$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
	}*/
	$fenbiao = getFenbiao($comId,20);
	$order_fenbiao = getFenbiao($order_comId,20);
	$zhifumm = $request['zhifumm'];
	if($comId==10){
		$db_service = getCrmDb();
		$u = $db_service->get_row("select payPass,money from demo_user where id=$userId");
	}else{
		$u = $db->get_row("select payPass,money from users where id=$userId");
	}
	if($_SESSION['if_tongbu']==1){
		if(empty($db_service))$db_service = getCrmDb();
		$u->payPass = $db_service->get_var("select payPass from demo_user where id=".$_SESSION[TB_PREFIX.'zhishangId']);
	}
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$shlencryption = new shlEncryption($zhifumm);
	if($u->payPass!=$shlencryption->to_string()){
		die('{"code":0,"message":"支付密码不正确"}');
	}
	$order = $db->get_row("select * from demo_pdt_order where id=$orderId and userId=$userId");
	if($comId==10){
		$fanli_json = json_decode($order->fanli_json,true);
		$fanli_json['if_shop_fanli'] = 1;
		$db->query("update demo_pdt_order set fanli_json='".json_encode($fanli_json,JSON_UNESCAPED_UNICODE)."' where id=$orderId");
	}
	$order->price = $order->price-$order->price_payed;
	if(empty($order)){
		die('{"code":0,"message":"订单不存在"}');
	}
	if($order->status!=-5){
		die('{"code":0,"message":"订单当前不是待支付状态"}');
	}
	if($u->money<$order->price){
		die('{"code":0,"message":"余额不足！请选择其他支付方式"}');
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
	if($comId==10){
		$db_service->query("update demo_user set money=money-$order->price where id=$userId");
	}else{
		$db->query("update users set money=money-$order->price where id=$userId");
	}
	$liushui = array();
	$liushui['userId']=$userId;
	$liushui['comId']=$comId;
	$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
	$liushui['money']=-$order->price;
	$liushui['yue']=$u->money-$order->price;
	$liushui['type']=1;
	$liushui['dtTime']=date("Y-m-d H:i:s");
	$liushui['remark']='订单支付';
	$liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
	$liushui['order_id']=$orderId;
	insert_update('user_liushui'.$fenbiao,$liushui,'id');
	//修改订单信息
	$o = array();
	$o['id'] = $orderId;
	$o['status'] = $order->address_id>0?2:4;//需要收货的设置2 不需要的设置4
	$o['ispay'] = 1;
	$o['pay_type'] = 1;
	$o['price_payed'] = $order->price+$order->price_payed;
	$pay_json = array();
	if(!empty($order->pay_json)){
		$pay_json = json_decode($order->pay_json,true);
	}
	//if($order->price_dingjin==0){
	$pay_json['yue']['price'] = $order->price;
	$pay_json['yue']['if_zong'] = $comId==10?1:0;//是否是总平台的余额,退款时要按这个字段来退款
	$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
	//if($comId==1009 && $order->lipinkaType==2){
	//}
	$db->insert_update('demo_pdt_order',$o,'id');
	$db->query("update demo_pdt_inventory set orders=orders+1 where id=$order->inventoryId");
	$fanli_json = json_decode($order->fanli_json);
	if($fanli_json->shangji>0 && $fanli_json->shangji_fanli>0){
		$yugu_shouru = array();
		$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
		$yugu_shouru['userId'] = $fanli_json->shangji;
		$yugu_shouru['order_type'] = 2;
		$yugu_shouru['orderId'] = $order->id;
		$yugu_shouru['dtTime'] = date("Y-m-d");
		$yugu_shouru['money'] = $fanli_json->shangji_fanli;
		$yugu_shouru['from_user'] = $order->userId;
		$yugu_shouru['remark'] = '下级返利';
		$yugu_shouru['order_orderId'] = $order->orderId;
		$yugu_shouru['order_comId'] = $order->comId;
		$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
	}
	if($fanli_json->shangshangji>0 && $fanli_json->shangshangji_fanli>0){
		$yugu_shouru = array();
		$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
		$yugu_shouru['userId'] = $fanli_json->shangshangji;
		$yugu_shouru['order_type'] = 2;
		$yugu_shouru['orderId'] = $order->id;
		$yugu_shouru['dtTime'] = date("Y-m-d");
		$yugu_shouru['money'] = $fanli_json->shangshangji_fanli;
		$yugu_shouru['from_user'] = $order->userId;
		$yugu_shouru['remark'] = '团队返利';
		$yugu_shouru['order_orderId'] = $order->orderId;
		$yugu_shouru['order_comId'] = $order->comId;
		$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
	}
	if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
		$yugu_shouru = array();
		$yugu_shouru['comId'] = $order->if_zong==1?10:$order->comId;
		$yugu_shouru['userId'] = $fanli_json->tuijian;
		$yugu_shouru['order_type'] = 2;
		$yugu_shouru['orderId'] = $order->id;
		$yugu_shouru['dtTime'] = date("Y-m-d");
		$yugu_shouru['money'] = $fanli_json->tuijian_fanli;
		$yugu_shouru['from_user'] = $order->userId;
		$yugu_shouru['remark'] = '推荐店铺返利';
		$yugu_shouru['order_orderId'] = $order->orderId;
		$yugu_shouru['order_comId'] = $order->comId;
		$db->insert_update('user_yugu_shouru',$yugu_shouru,'id');
	}
	if($order->address_id>0){
		$address_id = $order->address_id;
		$address = $db->get_row("select * from user_address where id=$address_id");
		$areaId = (int)$address->areaId;
		$shouhuo_json = array();
		if(!empty($address)){
			$shouhuo_json['收件人'] = $address->name;
			$shouhuo_json['手机号'] = $address->phone;
			$shouhuo_json['所在地区'] = $address->areaName;
			$shouhuo_json['详细地址'] = $address->address;
		}
		$pdt_title = $db->get_var("select title from demo_pdt_inventory where id=$order->inventoryId");
		$fahuo = array();
		$fahuo['comId'] = $order->comId;
		$fahuo['mendianId'] = 0;
		$fahuo['addressId'] = $address_id;
		$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$fahuo['orderIds'] = $order->id;
		$fahuo['type'] = 1;
		$fahuo['showTime'] = date("Y-m-d H:i:s");
		$fahuo['storeId'] = 0;
		$fahuo['dtTime'] = date("Y-m-d H:i:s");
		$fahuo['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
		$fahuo['productId'] = (int)$order->inventoryId;
		$fahuo['tuanzhang'] = 0;
		$fahuo['product_title'] = $pdt_title;
		$fahuo['fahuo_title'] = $pdt_title;
		$fahuo['product_num'] = $order->pdtNums;
		$fahuo['weight'] = 0;
		$fahuo['areaId'] = $areaId;
		$fahuo['shequ_id'] = 0;
		$db->insert_update('pdt_order_fahuo',$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update demo_pdt_order set fahuoId=$fahuoId where id=$order->id");
	}
	die('{"code":1,"message":"支付成功"}');
}
function weixin_pay(){
	if(is_weixin()){
		global $db,$request,$order;
		$orderId = (int)$request['order_id'];
		$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
		$comId = (int)$_SESSION['demo_comId'];
		$fenbiao = getFenbiao($comId,20);
		$order = $db->get_row("select * from demo_pdt_order where id=$orderId and userId=$userId");
		if(empty($order)){
			die('<script>alert("订单不存在");location.href="/index.php?p=22&a=orders";</script>');
		}
		if($order->status!=-5){
			die('<script>location.href="/index.php?p=22&a=orders";</script>');
		}
		require('inc/pay/WxpayAPI_php_v3/example/jsapi_pdts.php');
		exit;
	}
}
function alipay_pay(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$orderId = (int)$request['order_id'];
	$order = $db->get_row("select * from demo_pdt_order where id=$orderId and userId=$userId");
	$order->price = $order->price-$order->price_payed;
	if($order->price_dingjin>0){
		$order->price = $order->price_dingjin-$order->price_payed;
	}
	if(empty($order)){
		die('订单不存在');
	}
	if($order->status!=-5){
		die('订单当前不是待支付状态');
	}
	$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
	if(empty($alipay_set)||$alipay_set->status==0||empty($alipay_set->info)){
		die('支付宝配置信息有误');
	}
	$alipay_arr = json_decode($alipay_set->info);
	$subject = '';
	$product_json = json_decode($order->product_json);
	foreach ($product_json as $pdt) {
		$subject.=','.$pdt->title.'*'.$pdt->num;
	}
	$body = substr($subject,1);
	$subject = sys_substr($body,50,true);
	$subject = str_replace('_','',$subject).'_'.$comId;
	require_once(ABSPATH."/inc/pay/wappay/alipay.config.php");
	$alipay_config['partner'] = $alipay_arr->partnerId;
	$alipay_config['seller_id']	= $alipay_config['partner'];
	$alipay_config['private_key']	= $alipay_arr->private_key;
	$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
	require_once(ABSPATH."/inc/pay/wappay/lib/alipay_submit.class.php");
	$out_trade_no = $order->orderId;
	$total_fee =  $order->price;
	$show_url = "http://".$_SERVER['HTTP_HOST']."/index.php?p=22&a=orders";
	$parameter = array(
		"service"       => $alipay_config['service'],
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/notify_pdts_url.php",
		"return_url"	=> "http://".$_SERVER['HTTP_HOST']."/inc/pay/wappay/return_pdts_url.php",
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"show_url"	=> $show_url,
		"body"	=> $body,
	);
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
	exit;
}
//10进制转36进制
function get_36id($char){
	$num = intval($char);
	if ($num <= 0)
	return false;
	$charArr = array("1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$char = '';
	do {
		$key = ($num - 1) % 35;
		$char= $charArr[$key] . $char;
		$num = floor(($num - $key) / 35);
	} while ($num > 0);
	$char = buling($char,6);
	return $char;
}
//36进制转10进制
function get_hexiao_id($char){
	$array=array("1","2","3","4","5","6","7","8","9","A", "B", "C", "D","E", "F", "G", "H", "I", "J", "K", "L","M", "N", "O","P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y","Z");
	while (true) {
		if(substr($char,0,1)=='0'){
			$char = substr($char,1);
		}else{
			break;
		}
	}
	$len=strlen($char);
	for($i=0;$i<$len;$i++){
		$index=array_search($char[$i],$array);
		$sum+=($index+1)*pow(35,$len-$i-1);
	}
	return $sum;
}