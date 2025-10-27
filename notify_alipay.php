<?php 
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
$today = date('Y-m-d H:i:s');
file_put_contents('ali_pay_err.log',"paypal notify info--$today:\r\n".json_encode($_REQUEST).PHP_EOL,FILE_APPEND);
require_once 'config/dt-config.php';
require_once 'inc/class.database.php';
require_once 'inc/function.php';


require_once("inc/pay/alipay/alipay.config.php");
require_once("inc/pay/alipay/lib/alipay_notify.class.php");

//$comId = $_GET['comId'];
//$comId =1210;

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
if(!isset($_REQUEST['source'])){
    //$verify_result = $alipayNotify->verifyNotify();
    $verify_result = true;
}else{
    $verify_result = true;
}
if(DEBUGIP == get_client_ip()){
    // var_dump($_REQUEST,$verify_result);die;
}
if($verify_result){
    $out_trade_no	= $_REQUEST['out_trade_no'];	//获取订单号
    $transaction_id		= $_REQUEST['trade_no'];		//获取支付宝交易号
    $total_fee		= $_REQUEST['total_fee'];		//获取总价格
    $comId = substr($out_trade_no,0,4);
    $fenbiao = intval($comId%20);

//     $out_trade_no	= $fhData->out_trade_no;
// 	$total_fee		= bcdiv($fhData->total_fee, 100, 2);		//获取总价格
// 	$transaction_id		= $fhData->transaction_id;		//微信支付订单号
	$order = $db->get_row("select * from order$fenbiao where orderId='$out_trade_no' limit 1");
	if(empty($order)){
	    die;
	}
	$total_fee = $total_fee;
	$shouhuo_json = json_decode($order->shuohuo_json,true);
	$country = $shouhuo_json['country'];
	$areaId = $db->get_var(" select id  from country where DESCRIPTION = '".$country."'");
	if(($order->status==-5||$order->status==-1) && empty($order->qx_time)){
		$orderId = $order->id;
		$o = array();
		$o['id'] = $orderId;
		if($order->price_dingjin==0){
			$o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
			$o['ispay'] = 1;
			$o['pay_type'] = 3;
		}
		$o['price_payed'] = $total_fee+$order->price_payed;
		
		$pay_json = array();
		if(!empty($order->pay_json)){
			$pay_json = json_decode($order->pay_json,true);
		}
		if($order->price_dingjin==0){
			$pay_json['alipay']['price'] = $total_fee;
        	$pay_json['alipay']['desc'] = $transaction_id;
		}
		
		$o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
		$db->insert_update('order'.$fenbiao,$o,'id');
		if($order->price_dingjin==0){
		     order_pay_done($order);die;
			$product_json = json_decode($order->product_json, JSON_UNESCAPED_UNICODE);
		    if(count($product_json) == 1){
			   
		    }else{
		        //TODO 拆单
		        $db->get_row("update order$fenbiao set is_del = 2 where  id = ".$order->id);
		        $total = 0;
		        $youhuiKey = -1;
		        foreach ($product_json as $key => $product){
		            $price = $product['price_sale'] * $product['num'];
		            if($price > $total){
		                $total = $price;
		                $youhuiKey = $key;
		            }
		        }
		        $firstProduct = $product_json[0];
		        $product_json[0] = $product_json[$youhuiKey];
		        $product_json[$youhuiKey] = $firstProduct;
		        
		        $tableId = $order->id;
		        foreach ($product_json as $key => $product){
		            $order->table_id = $tableId;
		            unset($order->id);
		            $order->orderId = '1210_'.date("YmdHis").rand(10000,99999);
            		$order->status = 2;//待支付
            		$order->price = bcmul($product['price_sale'], $product['num'], 2);
            		$order->pdtNums = $product['num'];
            		$fanJifen = $db->get_var("select fan_jifen from demo_product_inventory where id=".$product['id']);
            		$order->jifen = bcmul($fanJifen, $product['num'], 0);
            		$productJson = array();
            		$productJson[] = $product;
            		$order->product_json = json_encode($productJson, JSON_UNESCAPED_UNICODE);
            		//TODO 处理支付金额
            		if($key == 0){
            		    $priceJson = json_decode($order->price_json, JSON_UNESCAPED_UNICODE);
            		    $newPriceJson = [];
            		    $newPriceJson['goods'] = ['price' => $order->price, 'desc' => ''];
            		    if(isset($priceJson['yhq'])){
            		          $newPriceJson['yhq'] = $priceJson['yhq'];
            		          $order->price = bcsub($order->price, $priceJson['yhq']['price'], 2);
            		    }
            		    
            		    if(isset($priceJson['yunfei'])){
            		          $yunfei = $priceJson['yunfei']['price'];
            		          $newPriceJson['yunfei'] = ['price' => $yunfei, 'desc' => ''];
            		          $order->price = bcadd($order->price, $yunfei, 2);
            		          //$order->price = bcsub($order->price, $priceJson['yhq']['price'], 2);
            		    }
            		    
            		    $order->price_json = json_encode($newPriceJson, JSON_UNESCAPED_UNICODE);
                        
                        $payJson = json_decode($order->pay_json, JSON_UNESCAPED_UNICODE);
                        $newPayJson = [];
                        if(isset($payJson['jifen'])){
                            $newPayJson['jifen'] = $payJson['jifen'];
                            $order->price = bcsub($order->price, $payJson['jifen']['price'], 2);
                        }
                     
                        if(isset($payJson['alipay'])){
                            $newPayJson['alipay'] = ['price' => $order->price, 'desc' => ''];
                        }
                        
                        $order->pay_json = json_encode($newPayJson, JSON_UNESCAPED_UNICODE);
                        $order->price_payed = $order->price;
            		}else{
            		    $priceJson = json_decode($order->price_json, JSON_UNESCAPED_UNICODE);
            		    $newPriceJson = [];
            		    $newPriceJson['goods'] = ['price' => $order->price, 'desc' => ''];
            		    
            		    $yunfei = 0;
        		        $newPriceJson['yunfei'] = ['price' => $yunfei, 'desc' => ''];
        		        $order->price = bcadd($order->price, $yunfei, 2);
            		    
            		    
            		    $order->price_json = json_encode($newPriceJson, JSON_UNESCAPED_UNICODE);
                        
                        $payJson = json_decode($order->pay_json, JSON_UNESCAPED_UNICODE);
                        $newPayJson = [];
                     
                        if(isset($payJson['alipay'])){
                            $newPayJson['alipay'] = ['price' => $order->price, 'desc' => ''];
                        }
                        
                        $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0,'shequ_fanli'=>0,"shequ_id"=>0,"buyer_fanli"=>0);
                        $order->fanli_json = json_encode($fanli_json, JSON_UNESCAPED_UNICODE);
                        //$order->jifen = 0;
                        $order->pay_json = json_encode($newPayJson, JSON_UNESCAPED_UNICODE);
                        $order->price_payed = $order->price;
            		}
            		
        			$order->ispay = 1;
        			$order->pay_type = 3;
        			$order->is_del = 0;
        			unset($order->qx_time);
            		//file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
            		$db->insert_update('order'.$fenbiao,(array)$order,'id');
            		$order_id = $db->get_var("select last_insert_id();");
            		
            	    $pdt = new \StdClass();
        			$pdt->sn = $product['sn'];
        			$pdt->title = $product['title'];
        			$pdt->key_vals = $product['key_vals'];
        			$order_detail = array();
        			$order_detail['comId'] = (int)$comId;
        			$order_detail['mendianId'] = 0;
        			$order_detail['userId'] = $order->userId;
        			$order_detail['orderId'] = $order_id;
        			$order_detail['inventoryId'] = $product['id'];
        			$order_detail['productId'] = $product['productId'];
        			$order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
        			$order_detail['num'] = $product['num'];
        			$order_detail['unit'] = $product['unit'];
        			$order_detail['unit_price'] = $product['price_sale'];
        		
        			$db->insert_update('order_detail'.$fenbiao,$order_detail,'id');
        			
        			$orderNew = $db->get_row("select * from order$fenbiao where id=$order_id limit 1");
        			order_pay_done($orderNew);
        			
		        }
		 
		    }
		    
		    require_once ABSPATH."inc/class.sms.php";
    		$sms = new \SMS;
    		if($sms->isOpen('admin_neworder')){				
    			$result = $sms->send('',['name'=>'']);		
    		}
		}    
	}else{
		file_put_contents('weixin_pay_err.log',json_encode($_POST).PHP_EOL,FILE_APPEND);
	}
	ob_clean();
	echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
	exit;
}


