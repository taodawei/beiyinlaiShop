<?php
namespace Zhishang;
class Order{
	public function lists(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$keyword = $request['keyword'];
		$status = (int)$request['status'];
		$page = empty($request['page'])?1:(int)$request['page'];
		$pageNum = empty($request['pagenum'])?10:(int)$request['pagenum'];
		$sql = "select id,kehuId,orderId,status,dtTime,price,price_payed,price_weikuan,shouhuoInfo,fujianInfo from demo_dinghuo_order where comId=$comId and userId=$userId";
		if(!empty($keyword)){
			$sql.=" and (orderId like '%$keyword%' or shouhuoInfo like '%$keyword%')";
		}
		if(!empty($status)){
			$sql.=" and status=$status";
		}
		$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
		$orders = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['data'] = array();
		$fenbiao = getFenbiao($comId,20);
		if(!empty($orders)){
			foreach ($orders as $order) {
				$order->kehu_name=$db->get_var("select title from demo_kehu where id=$order->kehuId");
				switch ($order->status) {
					case 0:
						$order->status = '待审核';
					break;
					case 1:
						$order->status = '待审核';
					break;
					case 2:
						$order->status = '待出库';
					break;
					case 3:
						$order->status = '待出库审核';
					break;
					case 4:
						$order->status = '待配送';
					break;
					case 5:
						$order->status = '待收货';
					break;
					case 6:
						$order->status = '已完成';
					break;
					case -1:
						$order->status = '已作废';
					break;
				}
				$order->product_num = 0;
				$order->products = array();
				$products = array();
				$details = $db->get_results("select productId,pdtInfo,num,price,units from demo_dinghuo_detail$fenbiao where jiluId=$order->id");
				if(!empty($details)){
					foreach ($details as $detail) {
						$order->product_num++;
						$pdt = array();
						$pics = $db->get_var("select originalPic from demo_product where id=$detail->productId");
						$pdtInfo = json_decode($detail->pdtInfo);
						$pdt['image'] = ispic($pics);
						$pdt['title'] = $pdtInfo->title;
						$pdt['key_vals']  = $pdtInfo->key_vals;
						$pdt['price']  = self::getXiaoshu($detail->price,2);
						$pdt['num']  = $detail->num;
						$pdt['unit'] = $detail->units;
						$products[] = $pdt;
					}
					$order->products = $products;
				}
				$shouhuoInfo = json_decode($order->shouhuoInfo);
				$order->phone = $shouhuoInfo->phone;
				$order->address = $shouhuoInfo->address;
				$order->fujian = array();
				if(!empty($order->fujianInfo)){
					$fujians = explode('|',$order->fujianInfo);
					foreach ($fujians as $fujian) {
						if(substr($fujian,0,4)!='http'){
							$fujian = 'http://'.$_SERVER['HTTP_HOST'].$fujian;
						}
						$order->fujian[] = $fujian;
					}
				}
				$return['data'][] = $order;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	//代下单
	public function create(){
		global $db,$request,$comId;
		//$liucheng = getLiucheng();
		$db_service = get_zhishang_db();
		$userId = (int)$request['user_id'];
		$username = $db_service->get_var("select name from demo_user where id=$userId");
		$fenbiao = getFenbiao($comId,20);
		$kehuId = (int)$request['kehu_id'];
		$k = $db->get_row("select title,departId,storeId,areaId,caiwu from demo_kehu where id=$kehuId and comId=$comId");
		$kehuName = $k->title;
		$departId = $k->departId;
		$orderId = 'DH-O_'.date("Ymd").'_'.rand(100000,999999);
		$iftejia = empty($request['if_tejia'])?0:1;
		$price_wuliu = $request['price_wuliu'];
		$price = $request['price']+$price_wuliu;
		$price_weikuan = $price;
		$if_fapiao = (int)$request['if_fapiao'];
		//$weight = $request['weight'];
		$beizhu = '';
		if(!empty($request['beizhu'])){
			$results = array();
			$fankui = array();
			$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['beizhu']);
			$fankui['name'] = $username;
			$fankui['time'] = date('Y-m-d H:i:s');
			$fankui['company'] = $db_service->get_var("select com_title from demo_company where id=$comId");
			$results[]=$fankui;
			$beizhu = json_encode($results,JSON_UNESCAPED_UNICODE);
		}
		$jiaohuoTime = date("Y-m-d H:i:s");
		$dtTime = date("Y-m-d H:i:s");
		$address = $db->get_row("select * from demo_kehu_address where kehuId=$kehuId order by moren desc,id desc limit 1");
		$shouhuoInfo = '{"company":"'.$k->title.'","name":"'.$address->name.'","phone":"'.$address->phone.'","address":"'.$address->areaName.$address->address.'"}';
		if($if_fapiao==1){
			$caiwu = json_decode($k->caiwu);
			$fapiaoInfo = '{"type":"1","taitou":"'.$caiwu->taitou.'","content":"商品明细","shibie":"'.$caiwu->shibie.'"}';
		}else{
			$fapiaoInfo = '{"type":"0"}';
		}
		$fujianInfo = '';
		$target_path  = "upload/".date("Ymd").'/';
		if(!empty($_FILES['uploadfile1'])){
			if(strstr($_FILES['uploadfile1']['name'],'.php')){
				echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
				exit;
			}
			preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile1']['name'],$exts);
			if($exts[1]!='gif'&&$exts[1]!='jpg'&&$exts[1]!='jpeg'&&$exts[1]!='bmp'&&$exts[1]!='png'){
				file_put_contents('file.txt',$_FILES['uploadfile1']['name']);
				echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
				exit;
			}
			$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
			if(!is_dir($target_path)){
				mkdir($target_path);
			}
			@move_uploaded_file($_FILES['uploadfile1']['tmp_name'], $target_path.$fileName);
			$fujianInfo = '/'.$target_path.$fileName;
			if(!empty($_FILES['uploadfile2'])){
				if(strstr($_FILES['uploadfile2']['name'],'.php')){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile2']['name'],$exts);
				if($exts[1]!='gif' && $exts[1]!='jpg' && $exts[1]!='jpeg' && $exts[1]!='bmp' && $exts[1]!='png'){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
				@move_uploaded_file($_FILES['uploadfile2']['tmp_name'], $target_path.$fileName);
				$fujianInfo .= '|/'.$target_path.$fileName;
			}
			if(!empty($_FILES['uploadfile3'])){
				if(strstr($_FILES['uploadfile3']['name'],'.php')){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile3']['name'],$exts);
				if($exts[1]!='gif' && $exts[1]!='jpg' && $exts[1]!='jpeg' && $exts[1]!='bmp' && $exts[1]!='png'){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
				@move_uploaded_file($_FILES['uploadfile3']['tmp_name'], $target_path.$fileName);
				$fujianInfo .= '|/'.$target_path.$fileName;
			}
		}
		$orderType = 2;
		$username = $username;
		$request['product_list'] = str_replace('\\"','"', $request['product_list']);
		$request['product_list'] = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$request['product_list']));
		$pdtList = json_decode($request['product_list']);
		if(!empty($pdtList)){
			$dinghuo = array();
			$dinghuo['comId'] = $comId;
			$dinghuo['kehuId'] = $kehuId;
			$dinghuo['kehuName'] = $kehuName;
			$dinghuo['departId'] = $departId;
			$dinghuo['orderId'] = $orderId;
			$dinghuo['areaId'] = $k->areaId;
			$dinghuo['iftejia'] = $iftejia;
			$dinghuo['price_wuliu'] = $price_wuliu;
			$dinghuo['price'] = $price;
			$dinghuo['price_weikuan'] = $price_weikuan;
			//$dinghuo['weight'] = $weight;
			$dinghuo['jiaohuoTime'] = $jiaohuoTime;
			$dinghuo['dtTime'] = $dtTime;
			$dinghuo['shouhuoInfo'] =$shouhuoInfo;
			$dinghuo['fapiaoInfo'] =$fapiaoInfo;
			$dinghuo['fujianInfo'] = $fujianInfo;
			$dinghuo['orderType'] = $orderType;
			$dinghuo['userId'] = $userId;
			$dinghuo['username'] = $username;
			$dinghuo['yewuId'] = $userId;
			$dinghuo['yewuyuan'] = $username;
			$dinghuo['beizhu'] = $beizhu;
			$dinghuo['storeId'] = $k->storeId;
			$dinghuo['status'] = 4;
			$db->insert_update('demo_dinghuo_order',$dinghuo,'id');
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_dinghuo_detail$fenbiao(comId,kehuId,kehuName,jiluId,inventoryId,productId,pdtInfo,num,hasNum,tuihuoNum,status,price,unit_price,units,dtTime,weight,beizhu,dinghuoUnit,UnitNum,dinghuoNum) values";
			$rukuSql1 = '';
			$zong_weight = 0;
			foreach ($pdtList as $pdt){
				$inventoryId = $pdt->inventory_id;
				$num = $pdt->num;
				$inventory = $db->get_row("select sn,title,key_vals,weight,productId from demo_product_inventory where id=$inventoryId");
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $inventory->sn;
				$pdtInfoArry['title'] = $inventory->title;
				$pdtInfoArry['key_vals'] = $inventory->key_vals;
				$weight = $inventory->weight;
				$productId = $inventory->productId;
				$units = $pdt->unit;
				$unit_price = $pdt->unit_price;
				$dinghuoUnit = $units;
				$UnitNum = 1;
				$dinghuoNum = $num;
				$price = $unit_price*$num;
				$beizhu = '';
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$rukuSql1.=",($comId,$kehuId,'$kehuName',$jiluId,$inventoryId,$productId,'$pdtInfo','$num',0,0,0,'$price','$unit_price','$units','$dtTime','$weight','$beizhu','$dinghuoUnit','$UnitNum','$dinghuoNum')";
				//if(($liucheng['if_chuku']+$liucheng['if_fahuo'])>0){
				$db->query("update demo_kucun set yugouNum=yugouNum+$num where inventoryId=$inventoryId and storeId=$k->storeId limit 1");
				//}
				$zong_weight += $num*$inventory->weight;
			}
			$db->query("update demo_dinghuo_order set weight='$zong_weight' where id=$jiluId");
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			$content = '已为您代下订货单，单号：'.$orderId;
			add_dinghuo_msg($kehuId,$content,1,$jiluId);
			return '{"code":1,"message":"下单成功","order_id":'.$jiluId.'}';
		}else{
			return '{"code":0,"message":"未检测到商品信息"}';
		}
	}
	public function shenhe(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$orderId = (int)$request['order_id'];
		$fenbiao = getFenbiao($comId,20);
		$nowStatus = $db->get_var("select status from demo_dinghuo_order where id=$orderId and comId=$comId");
		if($nowStatus!=0 && $nowStatus!=1){
			return '{"code":0,"message":"该订单不需要审核"}';
		}
		$db->query("update demo_dinghuo_order set status=4 where id=$orderId and comId=$comId");
		self::addJilu($orderId,'订货单订单审核','订货单已通过审核');
	}
	function shoukuan(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$id = (int)$request['order_id'];
		$return = array();
		$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
		if(empty($jilu)){
			$return['code']=0;
			$return['message']='记录不存在，请刷新重试。';
			echo json_encode($return,JSON_UNESCAPED_UNICODE);
			exit;
		}
		$account1 = $request['a_account1'];
		$account2 = $request['a_account2'];
		$account3 = $request['a_account3'];
		if($account1>0){
			$account1_yue = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=1 limit 1");
			$account1_yue = empty($account1_yue)?0:$account1_yue;
			if($account1>$account1_yue){
				$return['code']=0;
				$return['message']='现金账户余额不足！';
				echo json_encode($return,JSON_UNESCAPED_UNICODE);
				exit;
			}
		}
		if($account2>0){
			$account2_yue = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=2 limit 1");
			$account2_yue = empty($account2_yue)?0:$account2_yue;
			if($account2>$account2_yue){
				$return['code']=0;
				$return['message']='预付款账户余额不足！';
				echo json_encode($return,JSON_UNESCAPED_UNICODE);
				exit;
			}
		}
		if($account3>0){
			$account3_yue = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=3 limit 1");
			$account3_yue = empty($account3_yue)?0:$account3_yue;
			if($account3>$account3_yue){
				$return['code']=0;
				$return['message']='返点账户余额不足！';
				echo json_encode($return,JSON_UNESCAPED_UNICODE);
				exit;
			}
		}
		$hejiMoney = $account1+$account2+$account3+$request['a_payMoney'];
		$price_weikuan = $jilu->price_weikuan;
		$daiqueren = $db->get_var("select sum(money) from demo_dinghuo_money where jiluId=$id and status=0");
		if(empty($daiqueren))$daiqueren=0;
		if($hejiMoney>$price_weikuan+$daiqueren){
			$return['code']=0;
			$return['message']='总金额大于待支付金额，请刷新重试！';
			echo json_encode($return,JSON_UNESCAPED_UNICODE);
			exit;
		}
		$zongMoney = 0;
		$zongMoney+=$account1;
		$zongMoney+=$account2;
		$zongMoney+=$account3;
		if($account1>0){
			self::add_dinghuo_money($jilu,$account1,1);
		}
		if($account2>0){
			self::add_dinghuo_money($jilu,$account2,2);
		}
		if($account3>0){
			self::add_dinghuo_money($jilu,$account3,3);
		}
		if($request['a_payMoney']>0){
			$dinghuo_money = array();
			$dinghuo_money['comId'] = $comId;
			$dinghuo_money['kehuId'] = $jilu->kehuId;
			$dinghuo_money['kehuName'] = $jilu->kehuName;
			$dinghuo_money['jiluId'] = $jilu->id;
			$dinghuo_money['type'] = 0;
			$dinghuo_money['orderId'] = date("YmdHis").rand(1000000000,9999999999);
			$dinghuo_money['money'] = $request['a_payMoney'];
			$dinghuo_money['shenheTime'] = date("Y-m-d H:i:s");
			$dinghuo_money['dtTime'] = date("Y-m-d H:i:s");
			$dinghuo_money['status'] = 1;
			/*if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')){
				$dinghuo_money['status'] = 1;
				$zongMoney += $request['a_payMoney'];
			}*/
			/*if($kehuId>0){
				$dinghuo_money['status'] = 0;
			}else{
				$zongMoney += $request['a_payMoney'];
			}*/
			$zongMoney += $request['a_payMoney'];
			$dinghuo_money['pay_type'] = $request['pay_type'];
			$dinghuo_money['pay_info'] = '订单付款';
			$dinghuo_money['shoukuan_info'] = '';
			if($dinghuo_money['pay_type']==6){
				$bankId = (int)$request['bank_id'];
				$account = $db->get_row("select bank_name,bank_user,bank_account from demo_kehu_bank where id=$bankId");
				if(!empty($account)){
					$dinghuo_money['shoukuan_info'] = json_encode($account,JSON_UNESCAPED_UNICODE);
				}
			}
			$fujianInfo = '';
			$target_path  = "upload/".date("Ymd").'/';
			if(!empty($_FILES['uploadfile1'])){
				if(strstr($_FILES['uploadfile1']['name'],'.php')){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile1']['name'],$exts);
				if($exts[1]!='gif'&&$exts[1]!='jpg'&&$exts[1]!='jpeg'&&$exts[1]!='bmp'&&$exts[1]!='png'){
					echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
					exit;
				}
				$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
				if(!is_dir($target_path)){
					mkdir($target_path);
				}
				@move_uploaded_file($_FILES['uploadfile1']['tmp_name'], $target_path.$fileName);
				$fujianInfo = '/'.$target_path.$fileName;
				if(!empty($_FILES['uploadfile2'])){
					if(strstr($_FILES['uploadfile2']['name'],'.php')){
						echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
						exit;
					}
					preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile2']['name'],$exts);
					if($exts[1]!='gif' && $exts[1]!='jpg' && $exts[1]!='jpeg' && $exts[1]!='bmp' && $exts[1]!='png'){
						echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
						exit;
					}
					$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
					@move_uploaded_file($_FILES['uploadfile2']['tmp_name'], $target_path.$fileName);
					$fujianInfo .= '|/'.$target_path.$fileName;
				}
				if(!empty($_FILES['uploadfile3'])){
					if(strstr($_FILES['uploadfile3']['name'],'.php')){
						echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
						exit;
					}
					preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile3']['name'],$exts);
					if($exts[1]!='gif' && $exts[1]!='jpg' && $exts[1]!='jpeg' && $exts[1]!='bmp' && $exts[1]!='png'){
						echo '{"code":0,"message":"文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式","path":""}';
						exit;
					}
					$fileName = date("YmdHis").rand(1,999).'.'.$exts[1];
					@move_uploaded_file($_FILES['uploadfile3']['tmp_name'], $target_path.$fileName);
					$fujianInfo .= '|/'.$target_path.$fileName;
				}
			}
			$dinghuo_money['files'] = $fujianInfo;
			$dinghuo_money['beizhu'] = $request['remark'];
			$dinghuo_money['userId'] = $userId;
			$dinghuo_money['userName'] = $jilu->username;
			$db->insert_update('demo_dinghuo_money',$dinghuo_money,'id');
			if($dinghuo_money['status']==1){
				$liushui = array();
				$liushui['comId'] = $comId;
				$liushui['orderId'] = $dinghuo_money['orderId'];
				$liushui['order_type'] = 1;
				$liushui['dinghuoId'] = $jilu->id;
				$liushui['dinghuoOrderId'] = $jilu->orderId;
				$liushui['kehuId'] = $jilu->kehuId;
				$liushui['type'] = 2;
				$liushui['accountType'] =$dinghuo_money['pay_type'];
				$liushui['remark'] = '订单付款';
				$liushui['typeInfo'] = getPayType($dinghuo_money['pay_type']);
				if($dinghuo_money['pay_type']==6){
					$liushui['typeInfo'] = $dinghuo_money['shoukuan_info'];
				}
				$liushui['dtTime'] = date("Y-m-d H:i:s");
				$liushui['money'] = $request['a_payMoney'];
				$liushui['status'] = 1;
				$liushui['userName'] = $dinghuo_money['userName'];
				$liushui['shenheUser'] = '系统自动';
				$db->insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
			}else{
				//添加消息
			}
		}
		if($dinghuo_money['status']==1||$zongMoney>0){
			$payStatus = 3;
			if($zongMoney==$price_weikuan+$daiqueren)$payStatus=4;
			
			$db->query("update demo_dinghuo_order set payStatus=$payStatus,price_payed=price_payed+$zongMoney,price_weikuan=price_weikuan-$zongMoney where id=$id");
		}
		add_dinghuo_msg($jilu->kehuId,'订货单'.$jilu->orderId.'代收款￥'.$hejiMoney,1,$jilu->id);
		$return['code']=1;
		$return['message']='ok';
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function peisong(){
		global $db,$request,$comId;
		$db_service = get_zhishang_db();
		$userId = (int)$request['user_id'];
		$username = $db_service->get_var("select name from demo_user where id=$userId");
		$fenbiao = getFenbiao($comId,20);
		$jiluId = $id = (int)$request['order_id'];
		if(is_file("cache/kucun_set_$comId.php")){
			$kucun_set = json_decode(file_get_contents("cache/kucun_set_$comId.php"));
		}else{
			$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		}
		$order = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId limit 1");
		if(empty($order) || $order->status!=4){
			return '{"code":0,"message":"订单不存在或不是待配送状态"}';
		}
		$details = $db->get_results("select * from demo_dinghuo_detail$fenbiao where jiluId=$order->id");
		$storeId = $order->storeId;
		$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
		$chukuArry = array();
		foreach($details as $detail){
			$kucun = $db->get_row("select kucun,yugouNum,chengben from demo_kucun where inventoryId=$detail->inventoryId and storeId=$storeId limit 1");
			$chuku = array();
			$chuku['id'] = $detail->id;
			$chuku['inventoryId'] = $detail->inventoryId;
			$chuku['productId'] = $detail->productId;
			$chuku['pdtInfo'] = $detail->pdtInfo;
			$chuku['num'] = $detail->num;
			$chuku['kucun'] = $kucun->kucun-$detail->num;
			$chuku['chengben'] = $detail->num*$kucun->chengben;
			$chuku['units'] = $detail->units;
			$chukuArry[] = $chuku;
		}
		$dtTime = date("Y-m-d H:i:s");
		$type = 2;
		$orderInt = getOrderId($comId,$type);
		$orderId = $kucun_set->chuku_pre.'_'.date("Ymd").'_'.$orderInt;
		$status = 1;
		$shenheUser = 0;
		$shenheName = '';
		$type_info = '销售出库';
		$dinghuoId = $jiluId;
		$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName,dinghuoId) value($comId,$type,$storeId,0,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','',$shenheUser,'$shenheName','','$storeName',$dinghuoId)");
		$jiluId = $db->get_var("select last_insert_id();");
		$dinghuo_fahuo['comId'] = $comId;
		$dinghuo_fahuo['kehuId'] = $order->kehuId;
		$dinghuo_fahuo['jiluId'] = $jiluId;
		$dinghuo_fahuo['dinghuoId'] = $dinghuoId;
		$dinghuo_fahuo['type'] = 1;
		$dinghuo_fahuo['fahuoTime'] = $dtTime;
		$dinghuo_fahuo['kuaidi_type'] = 1;
		$dinghuo_fahuo['kuaidi_company'] = '';
		$dinghuo_fahuo['kuaidi_order'] = '';
		$dinghuo_fahuo['beizhu'] = '';
		$dinghuo_fahuo['dtTime'] = $dtTime;
		$dinghuo_fahuo['userId'] = $userId;
		$dinghuo_fahuo['userName'] = $username;
		$db->insert_update('demo_dinghuo_fahuo',$dinghuo_fahuo,'id');

		$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben,dinghuoId) values";
		$rukuSql1 = '';
		foreach ($chukuArry as $chuku){
			$inventoryId = $chuku['inventoryId'];
			$num = $chuku['num'];
			$kucun = $chuku['kucun'];
			$rukuChengben = $chuku['chengben'];
			$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$inventoryId and storeId=$storeId and status=1 order by id desc limit 1");
			if(empty($lastJilu)){
				$lastJilu->zongchengben = 0;
				$lastJilu->kucun = 0;
			}
			$zongchengben = $lastJilu->zongchengben-$rukuChengben;
			$db->query("update demo_kucun set kucun=kucun-$num,yugouNum=yugouNum-$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
			$db->query("update demo_product_inventory set kucun=kucun-$num where id=$inventoryId");
			$db->query("update demo_dinghuo_detail$fenbiao set hasNum=hasNum+$num where id=".$chuku['id']);
			$rukuSql1.=",($comId,$jiluId,$inventoryId,".$chuku['productId'].",'".$chuku['pdtInfo']."',$storeId,'$storeName','-$num',$status,'$kucun','',$type,'$type_info','$dtTime','".$chuku['units']."','$rukuChengben','$zongchengben',".$chuku['id'].")";
		}
		$rukuSql1 = substr($rukuSql1,1);
		$db->query($rukuSql.$rukuSql1);
		$chukuStatus = 2;
		$status = 6;
		$db->query("update demo_dinghuo_order set status=$status,chukuStatus=$chukuStatus,fahuoStatus=2 where id=$dinghuoId");
		//写记录
		self::addJilu($dinghuoId,'订货单出库','订货单'.($chukuStatus==1?'部分':'已全部').'出库');
		$return['code']=1;
		$return['message']='成功';
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	function addJilu($jiluId,$statusType,$content){
		global $db;
		$jilu = array();
		$jilu['jiluId'] = $jiluId;
		$jilu['username'] = $_SESSION[TB_PREFIX.'name'];
		$jilu['dtTime'] = date("Y-m-d H:i:s");
		$jilu['statusType'] = $statusType;
		$jilu['content'] = $content;
		$jilu['company'] = $_SESSION[TB_PREFIX.'name'];
		$db->insert_update('demo_dinghuo_jilu',$jilu,'id');
	}
	//添加账户支付记录
	function add_dinghuo_money($jilu,$money,$type){
		global $db,$comId;
		$acctountType = '';
		switch ($type) {
			case 1:
				$acctountType = 'acc_xianjin_queren';
			break;
			case 2:
				$acctountType = 'acc_yufu_queren';
			break;
			case 3:
				$acctountType = 'acc_fandian_queren';
			break;
			case 4:
				$acctountType = 'acc_baozheng_queren';
			break;
		}
		$status = 1;
		$fenbiao = getFenbiao($comId,20);
		$dinghuo_money = array();
		$dinghuo_money['comId'] = $comId;
		$dinghuo_money['kehuId'] = $jilu->kehuId;
		$dinghuo_money['kehuName'] = $jilu->kehuName;
		$dinghuo_money['jiluId'] = $jilu->id;
		$dinghuo_money['type'] = 0;
		$dinghuo_money['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$dinghuo_money['money'] = $money;
		$dinghuo_money['shenheTime'] = date("Y-m-d H:i:s");
		$dinghuo_money['dtTime'] = date("Y-m-d H:i:s");
		$dinghuo_money['status'] = 1;
		$dinghuo_money['pay_type'] = $type;
		$dinghuo_money['pay_info'] = '订单付款';
		$dinghuo_money['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$dinghuo_money['userName'] = $_SESSION[TB_PREFIX.'name'];
		$db->insert_update('demo_dinghuo_money',$dinghuo_money,'id');
		$db->query("update demo_kehu_account set money=money-$money where kehuId=$jilu->kehuId and type=$type limit 1");
		$liushui = array();
		$liushui['comId'] = $comId;
		$liushui['orderId'] = $dinghuo_money['orderId'];
		$liushui['order_type'] = 1;
		$liushui['dinghuoId'] = $jilu->id;
		$liushui['dinghuoOrderId'] = $jilu->orderId;
		$liushui['kehuId'] = $jilu->kehuId;
		$liushui['type'] = 2;
		$liushui['accountType'] =$type;
		$liushui['typeInfo'] = getPayType($type);
		$liushui['dtTime'] = date("Y-m-d H:i:s");
		$liushui['money'] = $money;
		$liushui['status'] = 1;
		$liushui['remark'] = '订单付款';
		$liushui['userName'] = $dinghuo_money['userName'];
		$liushui['shenheUser'] = '系统自动';
		$db->insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	}
	function getXiaoshu($num,$weishu){
		return str_replace(',','',number_format($num,$weishu));
	}
}