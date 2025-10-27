<?php
function index(){}
function daochu(){}
function daochu_fapiao(){}
function tuikuan_order(){}
function tuihuo_order(){}
function huanhuo_order(){}
function caiwu_queren(){}
function tuikuan_queren(){}
function service(){}
function comment(){}
function guidang(){}
function fapiao(){}
function quehuo(){}
function yushou(){}
function yushou_order(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
	$mendianId = $comId;
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$moneystart = $request['moneystart'];
	$moneyend = $request['moneyend'];
	$payStatus = $request['payStatus'];
	$pdtInfo = $request['pdtInfo'];
	$kaipiao = (int)$request['kaipiao'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select id,orderId,userId,comId,mendianId,status,dtTime,ispay,price,product_json,ishexiao,hexiaos,ifpingjia from demo_pdt_order where mendianId=$mendianId";
	switch ($scene){
		case 0:
			//$sql .= " and status in(0,1,2,3)";
		break;
		case 1:
			$sql .= " and type=2";
		break;
		case 2:
			//一小时内未支付的
			$last_time = date("Y-m-d H:i:s");
			$sql .= " and status=-5 and pay_endtime>'$last_time'";
		break;
		case 3:
			$sql .= " and status=-2";
		break;
		case 4:
			$sql .= " and status=-3";
		break;
		case 5:
			$sql .= " and status=-4";
		break;
		case 6:
			$sql .= " and type=4";
		break;
		case 7:
			$sql .= " and status=4";
		break;
		case 8:
			$last_time = date("Y-m-d H:i:s");
			$sql .= " and (status=-1 or (status=-5 and pay_endtime<'$last_time'))";
		break;
	}
	if(!empty($status)){
		$status = str_replace('9','0',$status);
		if(strstr($status,'-1')){
			$last_time = date("Y-m-d H:i:s");
			$sql.=" and (status in($status) or (status=-5 and pay_endtime<'$last_time'))";
		}else{
			$sql.=" and status in($status)";
		}
	}
	if(!empty($keyword)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$keyword%' or username='$keyword')");
		if(empty($userIds))$userIds='0';
		$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($kehuName)){
		$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
		if(empty($userIds))$userIds='0';
		$sql.=" and userId in($userIds)";
	}
	if(!empty($moneystart)){
		$sql.=" and price>='$moneystart'";
	}
	if(!empty($moneyend)){
		$sql.=" and price<='$moneyend'";
	}
	if(!empty($payStatus)){
		$payStatus = $payStatus%2;
		$sql.=" and payStatus=$payStatus";
	}
	if(!empty($pdtInfo)){
		$sql.=" and product_json like '%$pdtInfo%'";
	}
	$countsql = str_replace('id,orderId,userId,comId,mendianId,status,dtTime,ispay,price,product_json,ishexiao,hexiaos,ifpingjia','count(*)',$sql);
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	//file_put_contents('request.txt',$sql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->view = '<a href="javascript:" onclick="order_show('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			$j->layclass = '';
			if($j->status==-5){
				$status = '<span style="color:#ff3333;">待支付</span>';
			}elseif($j->status==4){
				$status = '<span style="color:#ff3333;">已核销'.$j->ishexiao.'/'.$j->hexiaos.'</span>';
			}elseif($j->status==-1){
				$status = '<span style="color:#ff3333;">无效</span>';
			}
			
			$j->status_info = $status;
			switch ($j->ispay){
				case 0:
					$j->payStatus = '未付款';
				break;
				case 1:
					$j->payStatus = '已付款';
				break;
			}
			$j->username = $db->get_var("select username from users where id=$j->userId");
			//$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$product_array = array();
			if(!empty($j->product_json))$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			if(!empty($product_array)){
				$j->pdt_info.=$product_array->title.'*'.$product_array->num;
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getCommentList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$mendianId = $_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$status = empty($request['status'])?1:(int)$request['status'];
	$star = (int)$request['star'];
	$keyword = $request['keyword'];
	$pdtName = $request['pdtName'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$username = $request['username'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('tuihuanPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from pdt_order_comment where comId=$comId ";
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	switch ($star) {
		case 1:
			$sql.=" and star in(1,2)";
		break;
		case 3:
			$sql.=" and star=3";
		break;
		case 5:
			$sql.=" and star in(4,5)";
		break;
	}
	if(!empty($keyword)){
		$sql.=" and (pdtName like '%$keyword%' or name='$keyword' or order_orderId like '%$keyword%')";
	}
	if(!empty($pdtName)){
		$sql.=" and pdtName like '%$pdtName%'";
	}
	if(!empty($orderId)){
		$sql.=" and order_orderId='$orderId'";
	}
	if(!empty($orderId)){
		$sql.=" and orderId like '%$orderId%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($username)){
		$sql.=" and name='$username'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$pingjia = '';
			switch ($j->star){
				case 1:
					$pingjia = '<img src="images/pingjia_12.png" style="margin-right:5px;">差评';
				break;
				case 2:
					$pingjia = '<img src="images/pingjia_12.png" style="margin-right:5px;">差评';
				break;
				case 3:
					$pingjia = '<img src="images/pingjia_11.png" style="margin-right:5px;">中评';
				break;
				case 4:
					$pingjia = '<img src="images/pingjia_1.png" style="margin-right:5px;">好评';
				break;
				case 5:
					$pingjia = '<img src="images/pingjia_1.png" style="margin-right:5px;">好评';
				break;
			}
			$j->pingjia=$pingjia;
			$j->content = '<div style="word-break:break-all;white-space:normal;">'.$j->cont1.'</div>';
			if(!empty($j->images1)){
				$j->content .= '<div style="padding-top:6px;">';
				$imgs = explode('|',$j->images1);
				foreach ($imgs as $img){
					$j->content .= '<a href="'.$img.'" target="_blank" style="margin-right:5px;"><img src="'.$img.'?x-oss-process=image/resize,w_63" height="63"></a>';
				}
				$j->content .= '</div>';
			}
			$j->content.= '<div style="font-size:12px;color:#919191;">'.date("Y-m-d H:i",strtotime($j->dtTime1)).'</div>';
			if(!empty($j->cont2)){
				$j->content .= '<div style="word-break:break-all;white-space:normal;">追加：'.$j->cont2.'</div>';
				if(!empty($j->images2)){
					$imgs = explode('|',$j->images2);
					foreach ($imgs as $img){
						$j->content .= '<img src="'.$img.'?x-oss-process=image/resize,w_63" height="63">';
					}
				}
				$j->content .= '<div style="font-size:12px;color:#919191;">'.date("Y-m-d H:i",strtotime($j->dtTime2)).'</div>';
			}
			if(!empty($j->reply)){
				$j->content .= '<div style="color:#2786bc;word-break:break-all;white-space:normal;">掌柜回复：'.$j->reply.'</div><div style="font-size:12px;color:#2786bc;">'.date("Y-m-d H:i",strtotime($j->dtTime3)).'</div>';
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function comment_shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$db->query("update pdt_order_comment set status=2 where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function comment_delete(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$db->query("delete from pdt_order_comment where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function comment_huifu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$cont = $request['cont'];
	$db->query("update pdt_order_comment set status=3,reply='$cont',dtTime3='".date("Y-m-d H:i:s")."' where id in($ids) and comId=$comId");
	echo '{"code":1}';
	exit;
}
function order_info_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from demo_pdt_order where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$price_json = json_decode($order->price_json,true);
	$pay_json = array();
	$fahuo_json = array();
	$shuohuo_json = array();
	if(!empty($order->pay_json))$pay_json=json_decode($order->pay_json,true);
	if(!empty($order->fahuo_json))$fahuo_json=json_decode($order->fahuo_json,true);
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	if($order->if_zong==1){
		$db_service = getCrmDb();
		$user = $db_service->get_row("select name as nickname,username from demo_user where id=$order->userId");
	}else{
		$user = $db->get_row("select nickname,username,level from users where id=$order->userId");
		if($user->level>0)$user_level = $db->get_var("select title from user_level where id=$user->level");
	}
	$details = $db->get_results("select * from pdt_order_detail where orderId=$id order by id asc");
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';

	$str.='<div class="ddxx_jibenxinxi_2">
	    	<div class="ddxx_jibenxinxi_2_01" id="order_info_price">
	        	<div class="ddxx_jibenxinxi_2_01_up">
	            	商品价格
	            </div>
	        	<div class="ddxx_jibenxinxi_2_01_down">
	            	<ul>
	            		<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	订单总额：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>￥'.$order->price.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                    if(!empty($price_json['goods'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品总额：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['goods']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_03">	
	        	<div class="ddxx_jibenxinxi_2_03_up">	
	            	其它信息
	            </div>
	        	<div class="ddxx_jibenxinxi_2_03_down">
	            	<div class="ddxx_jibenxinxi_2_03_down_1">
	                	<ul>';
	                	$str.='<li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员名称：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->nickname.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员级别：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user_level.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">手机号：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->username.'</div>
	                            <div class="clearBoth"></div>
	                        </li>';
	                        if(!empty($order->userInfo)){
	                        	$str.='<li>
		                            <div class="ddxx_jibenxinxi_2_02_down_left">用户信息：</div>
		                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$order->userInfo.'</div>
		                            <div class="clearBoth"></div>
		                        </li>';
	                        }
	                    $str.='</ul>
	                </div>
	            </div>
	        </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_4">
	    	<div class="ddxx_jibenxinxi_4_up">
	        	订单明细：
	        </div>
	    	<div class="ddxx_jibenxinxi_4_down">
	        	<table width="100%" border="0" cellpadding="0" cellspacing="0">	
	            	<tr height="34">
	                	<td align="center" width="34" valign="middle" class="ddxx_jibenxinxi_4_down_bj"></td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品名称</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">数量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单价</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">核销状态(总次数/已核销次数)</td>
	                </tr>';
	                foreach ($details as $i=>$jilu){
	                	$pdtInfo = json_decode($jilu->pdtInfo);
	                	$str.='<tr height="34">
	                	<td align="center" valign="middle">'.($i+1).'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->title.'</td>
	                    <td align="center" valign="middle">'.$order->pdtNums.'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->price_sale.'</td>
	                    <td align="center" valign="middle">'.$order->hexiaos.'/'.$order->ishexiao.'</td>
	                </tr>';
	                }
	                $str.='</table>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}
function hexiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$pingtai_mendianId = $mendianId = $_SESSION[TB_PREFIX.'comId'];
	$id = get_hexiao_id(trim($request['code']));
	$order = $db->get_row("select * from demo_pdt_order where id=$id and comId=$comId and status=4 limit 1");
	if(empty($order) || $order->mendianId!=$mendianId){
		echo '{"code":0,"message":"订单不存在或不是待核销状态！"}';
		exit;
	}
	if($order->ishexiao>=$order->hexiaos){
		echo '{"code":0,"message":"该订单已经核销过了！"}';
		exit;
	}
	$today = strtotime(date("Y-m-d 00:00:00"));
	$startTime = strtotime($order->youxiaoqi_start);
	$endTime = strtotime($order->youxiaoqi_end);
	if($today<$startTime){
		echo '{"code":0,"message":"该订单还没到开始使用时间！"}';
		exit;
	}
	if($today>$endTime){
		echo '{"code":0,"message":"该订单已超过使用时间！"}';
		exit;
	}
	$db->query("update demo_pdt_order set ishexiao=ishexiao+1 where id=$id and comId=$comId");
	$date = $time = date("Y-m-d H:i:s");
	if($order->ishexiao-$order->hexiaos==1){
		//计算返利
		if(!empty($order->fanli_json)){
			$userId = $order->userId;
			$order_comId = $comId;
			if($order->if_zong==1){
				$db_service = getCrmDb();
				$comId = 10;
			}
			$fanli_json = json_decode($order->fanli_json);
			//上级收入，如果shagnji为0算到平台收益
			if($fanli_json->shangji_fanli>0 && $fanli_json->shangji){
				//返利给团长
				if($order->if_zong==1){
					$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->shangji_fanli.",earn=earn+".$fanli_json->shangji_fanli." where id=$fanli_json->shangji");
					$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->shangji");
				}else{
					$db->query("update users set money=money+".$fanli_json->shangji_fanli.",earn=earn+".$fanli_json->shangji_fanli." where id=$fanli_json->shangji");
					$yue = $db->get_var("select money from users where id=$fanli_json->shangji");
				}
				//$yzFenbiao = getYzFenbiao($fanli_json->shangji,20);
				$liushui = array();
				$liushui['userId']=$fanli_json->shangji;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$fanli_json->shangji_fanli;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=$date;
				$liushui['remark']='下级返利';
				$liushui['orderInfo']='下级返利，订单号：'.$order->orderId;
				$liushui['order_id']=$orderId;
				$liushui['from_user']=$userId;
				if($order->if_zong==1){
					$db->insert_update('user_yongjin10',$liushui,'id');
				}else{
					$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				}
				/*$fromUser = $db->get_var("select name from users where id=$userId");
				$openId = $db->get_var("select openId from users where id=$fanli_json->shangji");
				//返现到账通知
				$arr = array(
				    'first' => array(
				        'value' => '佣金到账通知',
				        'color' => '#FF0000'
				    ),
				    'order' => array(
				        'value' => $order->orderId,
				        'color' => '#FF0000'
				    ),
				    'money' => array(
				        'value' => $fanli_json->shangji_fanli,
				        'color' => '#FF0000'
				    ),
				    'remark' => array(
				        'value' => '收入类型：自营收入，来自成员：'.$fromUser.'购买的'.$product_json->title,
				        'color' => '#FF0000'
				    )
				);
				post_template_msg('47ycPbcQAkqZQ9OY0zw0SjyagxCNMJ1m2SVnVYkdbG8',$arr,$openId,'https://new.nmgyzwc.com/index.php?p=8&a=qianbao');*/
				//上级得积分
			}
			//团队奖励
			if($fanli_json->shangshangji_fanli>0 && $fanli_json->shangshangji>0){
				if($order->if_zong==1){
					$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
					$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->shangshangji");
				}else{
					$db->query("update users set money=money+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
					$yue = $db->get_var("select money from users where id=$fanli_json->shangshangji");
				}
				$liushui = array();
				$liushui['userId']=$fanli_json->shangshangji;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$fanli_json->shangshangji_fanli;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=$date;
				$liushui['remark']='下下级返利';
				$liushui['orderInfo']='下下级返利，订单号：'.$order->orderId;
				$liushui['order_id']=$orderId;
				$liushui['from_user']=$userId;
				if($order->if_zong==1){
					$db->insert_update('user_yongjin10',$liushui,'id');
				}else{
					$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				}
			}
			//店铺推荐返利
			if($fanli_json->tuijian>0 && $fanli_json->tuijian_fanli>0){
				if($order->if_zong==1){
					$db_service->query("update demo_user set yongjin=yongjin+".$fanli_json->tuijian_fanli.",earn=earn+".$fanli_json->tuijian_fanli." where id=$fanli_json->tuijian");
					$yue = $db_service->get_var("select yongjin from demo_user where id=$fanli_json->tuijian");
				}else{
					$db->query("update users set money=money+".$fanli_json->tuijian_fanli.",earn=earn+".$fanli_json->tuijian_fanli." where id=$fanli_json->tuijian");
					$yue = $db->get_var("select money from users where id=$fanli_json->tuijian");
				}
				$liushui = array();
				$liushui['userId']=$fanli_json->tuijian;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$fanli_json->tuijian_fanli;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=$date;
				$liushui['remark']='推荐商铺奖励';
				$liushui['orderInfo']='推荐商铺奖励，订单号：'.$order->orderId;
				$liushui['order_id']=$orderId;
				$liushui['from_mendian']=$order->comId;
				$liushui['from_user']=0;
				if($order->if_zong==1){
					$db->insert_update('user_yongjin10',$liushui,'id');
				}else{
					$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				}
			}
			//平台收益计算
			$pingtai_shouyi = array();
			$pingtai_shouyi['mendianId'] = $order->comId;
			$pingtai_shouyi['type'] = 1;
			$pingtai_shouyi['money'] = $fanli_json->pingtai_fanli;
			$pingtai_shouyi['money_order'] = $order->price;
			$pingtai_shouyi['money_gonghuo'] = empty($fanli_json->shop_fanli)?0:$fanli_json->shop_fanli;
			$pingtai_shouyi['money_tuanzhang'] = $fanli_json->tuijian_fanli;
			if(!empty($fanli_json->shangji)){
				$pingtai_shouyi['money_tuanzhang'] += $fanli_json->shangji_fanli;
			}
			if(!empty($fanli_json->shangshangji)){
				$pingtai_shouyi['money_tuanzhang'] += $fanli_json->shangshangji_fanli;
			}
			$pingtai_shouyi['money_tuijian'] = $fanli_json->tuijian_fanli;
			$pingtai_shouyi['dtTime'] = $date;
			$pingtai_shouyi['orderId'] = $orderId;
			$pingtai_shouyi['remark'] = '';
			$pingtai_shouyi['ifcount'] = 1;
			$db->insert_update('demo_pingtai_shouyi',$pingtai_shouyi,'id');
			//商家收益计算
			if(!empty($fanli_json->shop_fanli) && $fanli_json->if_shop_fanli==1){
				$db->query("update demo_shops set money=money+".$fanli_json->shop_fanli." where comId=$order->comId");
				//$yzFenbiao = getYzFenbiao($order->mendianId,20);
				$mendian_liushui = array();
				$mendian_liushui['mendianId'] = $order->comId;
				$mendian_liushui['comId'] = 10;
				$mendian_liushui['type'] = 1;
				$mendian_liushui['money'] = $fanli_json->shop_fanli;
				$mendian_liushui['yue'] = $db->get_var("select money from demo_shops where comId=$order->comId");
				$mendian_liushui['dtTime'] = $date;
				$mendian_liushui['typeInfo'] = '订单收益';
				$mendian_liushui['orderId'] = date("YmdHis").rand(1000000000,9999999999);
				$mendian_liushui['remark'] = '订单号：'.$order->orderId;
				$db->insert_update('demo_mendian_liushui10',$mendian_liushui,'id');
			}
			/*if(!empty($fanli_json->daili_fanli)){
				$ifhas = $db->get_var("select daili_id from demo_daili where daili_id=$fanli_json->daili_id");
				if(empty($ifhas)){
					$db->query("insert into demo_daili(daili_id,money) value($fanli_json->daili_id,'$fanli_json->daili_fanli')");
				}else{
					$db->query("update demo_daili set money=money+$fanli_json->daili_fanli where daili_id=$fanli_json->daili_id");
				}
				$daili_liushui = array();
				$daili_liushui['daili_id'] = $fanli_json->daili_id;
				$daili_liushui['type'] = 1;
				$daili_liushui['money'] = $fanli_json->daili_fanli;
				$daili_liushui['yue'] = $db->get_var("select money from demo_daili where daili_id=$fanli_json->daili_id");
				$daili_liushui['dtTime'] = $date;
				$daili_liushui['typeInfo'] = '订单收益';
				$daili_liushui['orderId'] = $order->id;
				$daili_liushui['remark'] = '订单号：'.$order->orderId;
				$db->insert_update('demo_daili_liushui',$daili_liushui,'id');
			}*/
		}
		$db->query("update user_yugu_shouru set status=1 where comId=$comId and orderId=$order->id and order_type=2");
		$pay_json = json_decode($order->pay_json,true);
		if(!empty($pay_json['yibao']['price'])){
			$yibao_orderId = $pay_json['yibao']['orderId'];
			$uniqueOrderNo = $pay_json['yibao']['desc'];
			file_get_contents('http://buy.zhishangez.com/yop-api/sendDivide.php?orderId='.$order->id.'&comId='.$order->comId.'&payId='.$order->pay_id.'&yibao_orderId='.$yibao_orderId.'&uniqueOrderNo='.$uniqueOrderNo);
		}else{
			$db->query("update demo_yibao_fenzhang set status=2 where orderId=$order->id and comId=$order->comId and status=1 limit 1");
		}
	}
	echo '{"code":1}';
	exit;
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