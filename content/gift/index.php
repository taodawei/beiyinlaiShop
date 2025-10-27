<?php
//中奖
function win(){
	global $db,$request;
	$id=(int)$request['id'];//奖项id
	$order_id=$request['order_id'];
	//判断user_id与username是否匹配
	$order=$db->get_row("select * from order0 where id=".$order_id);
	if($order->status==2){
		if($id!=0 || !empty($id)){
			//file_put_contents('gift.txt', $id.'-----',FILE_APPEND);
			//中奖记录
			$gift=$db->get_row("select * from demo_gift where id=".$id);//奖品
			$sql = "insert into demo_gift_record (userId,orderId,giftId,giftTitle,giftsTitle,weight,is_win,dtTime) values (".$order->userId.",".$order->id.",".$gift->id.",'".$gift->title."','".$gift->stitle."',".$gift->weight.",1,'".date('Y-m-d H:i:s')."')";//插入中奖记录
			$db->query($sql);
			$db->query("update demo_gift set num=num-1 where id=".$id);
			$product = json_decode($order->product_json);
			$product->giftTitle = $gift->stitle;
			$product_josn = json_encode($product,JSON_UNESCAPED_UNICODE);
			$sql1 = "update order0 set choujiang_num=1,is_choujiang=1,is_win=1,product_json='".$product_josn."' where id=".$order->id;//修改订单抽奖状态
			$db->query($sql1);
			exit;
		}else{//谢谢参与
			$sql = "insert into demo_gift_record (userId,orderId,giftId,giftTitle,giftsTitle,weight,is_win,dtTime) values (".$order->userId.",".$order->id.",0,'谢谢参与','谢谢参与',0.00,0,'".date('Y-m-d H:i:s')."')";//插入中奖记录
			$db->query($sql);
			$sql1 = "update order0 set choujiang_num=1,is_choujiang=1 where id=".$order->id;//修改订单抽奖状态
			$db->query($sql1);
			exit;
		}
	}else{
		echo "<script>alert('已经抽奖！');location.href='/?p=8';</script>";
		exit;
	}
	
}
function index()
{
	global $db;
	global $request;
	global $params;
	global $tag;	// 标签数组
	global $menus,$subs,$pdtNum;
	$order_id=$request['id'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$order=$db->get_row("select * from order0 where id=".$order_id);
	/*//规格
	$product_list = array();
	$gift_list = array();
	$dingdan = $db->get_results("select product_json from order0 where id in (12,13)");
	foreach($dingdan as $o){
	   $pdt = json_decode($o->product_json);
	   if(!empty($product_list[$pdt->id])){
	       //增加它的数量
	      $product_list[$pdt->id]['num']+=$pdt->num;
	      //$gift_list[$pdt->giftTitle]['num']+=1;
	      //$product_list[$pdt->giftTitle]['num']+=1;
	   }else{
	       $arr = array();
	       //$gift = array();
	       $arr['num'] = $pdt->num;
	       $arr['title'] = $pdt->title;
	       //$gift['num'] = 1;
	       //$gift['giftTitle'] = $pdt->giftTitle;
	       $arr['key_vals'] = str_replace("+", "", $pdt->key_vals);
	       $product_list[$pdt->id] = $arr;
	       //$gift_list[$pdt->giftTitle] = $gift;
	   }
	}
	foreach($dingdan as $o){
	   $pdt = json_decode($o->product_json);
	   if($pdt->giftTitle){
	   		if(!empty($gift_list[$pdt->giftTitle])){
		       //增加它的数量
		      $gift_list[$pdt->giftTitle]['num']+=1;
		   }else{
		       $gift = array();
		       $gift['num'] = 1;
		       $gift['giftTitle'] = $pdt->giftTitle;
		       $gift_list[$pdt->giftTitle] = $gift;
		   }
	   }
	}
	foreach ($product_list as $k => $v) {
		$k_title .= $v['title'].'['.$v['key_vals'].']*'.$v['num'].',';
	}
	$k_title = substr($k_title, 0,strlen($k_title)-1);
	if($gift_list){
		foreach ($gift_list as $k => $v) {
			$k_titles .= $v['giftTitle'].'*'.$v['num'].',';
		}
		$jiangpin = substr($k_titles, 0,strlen($k_titles)-1);
		$k_title = $k_title.','.$jiangpin;
	}
	echo $k_title;
	//规格*/
	if($order->choujiang_num==1 || $order->status!=2 || $order->is_choujiang==1 || $order->if_choujiang==0 || $userId!=$order->userId){
		echo "<script>location.href='/?p=8';</script>";
		die;
	}
	//判断订单计数表是否含有当前订单
	$ifhas = $db->get_var("select id from order_num where orderId=".$order_id);
	if(empty($ifhas)){
		$sql = "insert into order_num (orderId) values (".$order_id.")";
		$db->query($sql);
	}
}
?>