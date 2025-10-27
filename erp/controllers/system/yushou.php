<?php
function index(){}
function putong(){}
function putong_queren(){}
function chexiao(){}
//插入订单操作记录
function addJilu($orderId,$fenbiao,$type,$operate,$remark){
	$jilu = array();
	$jilu['orderId'] = $orderId;
	$jilu['username'] = $_SESSION[TB_PREFIX.'name'];
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['type'] = $type;
	$jilu['remark'] = $remark;
	$jilu['operate'] = $operate;
	insert_update('fahuo_jilu'.$fenbiao,$jilu,'id');
}
//获取列表
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$storeId = (int)$request['storeId'];
	$print_type = (int)$request['print_type'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing from order_fahuo$fenbiao where comId=$comId and status<>-1";
	$yushouId = (int)$request['yushouId'];
	if($yushouId>0){
		$sql.=" and yushouId=$yushouId";
	}else{
		$sql.=" and yushouId=0";
	}
	if($print_type==1){
		switch ($type){
			case 0:
				$sql.=" and (status=0 or kuaidi_type=1) ";
			break;
			case 1:
				$sql.=" and status=0";
			break;
			case 2:
				$sql.=" and status=1 and kuaidi_type=1";
			break;
			case 3:
				$sql.=" and status=3 and kuaidi_type=1";
			break;
		}
	}elseif($print_type==2){
		switch ($type){
			case 0:
				$sql.=" and (status=0 or kuaidi_type=2) ";
			break;
			case 1:
				$sql.=" and status=0";
			break;
			case 2:
				$sql.=" and status=1 and kuaidi_type=2";
			break;
			case 3:
				$sql.=" and status=3 and kuaidi_type=2";
			break;
		}
	}else if($print_type==-2){
		$sql.=" and status=-2";
	}
	if(!empty($keyword)){
		$ids = (int)$db->get_var("select id from order$fenbiao where orderId='$keyword' limit 1");
		$sql.=" and (orderId like '%$keyword%' or find_in_set($ids,orderIds) or shuohuo_json like '%$keyword%' or product_title like '%$keyword%')";
	}
	if(!empty($mendian)){
		$sql.=" and mendianId=$mendian";
	}
	if(!empty($storeId)){
		$sql.=" and storeId=$storeId";
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
	if(!empty($pdtInfo)){
		/*$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";*/
		$sql.=" and product_json like '%$pdtInfo%'";
	}
	
	if(!empty($mendian)){
		$mendianIds = $db->get_var("select group_concat(id) from mendian where title like '%$mendian%'");
		if(empty($mendianIds))$mendianIds='0';
		$sql.=" and mendianId in($mendianIds)";
	}
	$countsql = str_replace('id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing','count(*)',$sql);
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
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待上传/确认</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">已配货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">已完成</span>';
				break;
				case -1:
					$status = '<span style="color:green;">无效</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">暂停</span>';
				break;
			} 
			$zero1=strtotime (date("Y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
			$zero2=strtotime ($j->dtTime);  //过年时间，不能写2014-1-21 24:00:00  这样不对 
			$j->days=abs(ceil(($zero1-$zero2)/86400)).'天'; //60s*60min*24h   
			if($j->status==0){
				$j->daochutype='待导出';
			}else{
				$j->daochutype='已导出';
			}
			$j->dayinStatus = '未打印';
			$j->status_info = $status;
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			if(strpos($shuohuo_json['详细地址'],'【')===false){
				//$xiaoqu = $db->get_var("select title from user_address where id=$j->addressId");
				if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
			}
			if($j->is_hebing==1){
				$j->orderId='<span style="color:#f00;">'.$j->orderId.'</span>';
			}
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'];
			$j->tel = $shuohuo_json['手机号'];
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$product_array = array();
			if(!empty($j->product_json))$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			if(!empty($product_array)){
				foreach ($product_array as $val) {
					$j->pdt_info.=$val->title.'*'.$val->num;
				}
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
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
	$order = $db->get_row("select * from order_fahuo$fenbiao where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$shuohuo_json = array();
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	if(!empty($order->orderIds))$orderIds = explode(',', $order->orderIds);
	$orders = '';
	if(!empty($orderIds)){
		foreach ($orderIds as $key => $value) {
			$orders .= $db->get_var("select orderId from order$fenbiao where id=".$value).',';
		}
	}
	$orders = substr($orders, 0,strlen($orders)-1);
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';
	/*if($order->status>-1){
		$str .='<div class="ddxx_jibenxinxi_1" id="order_operate">订单操作：';
		switch ($order->status) {
			case 0:
				$str.='<a href="javascript:" onclick="fahuo_show('.$id.');">发货</a><a href="javascript:" onclick="order_tuihuan('.$id.');">打印订单</a>';
			break;
			default:
				$str.='<a href="javascript:" onclick="order_tuihuan('.$id.');">打印订单</a>';
			break;
		}
	    $str.='</div>';
	}*/
	$str.='<div class="dianzimiandanxx_jibenxinxi_02">
                	<div class="dianzimiandanxx_jibenxinxi_02_up">
                    	发货单信息'.(($order->status==0||$order->status==1)?'<a href="javascript:" style="color:red;margin-left:20px;" onclick="xiugai_shouhuo(\''.$order->id.'\',\''.$shuohuo_json["收件人"].'\',\''.$shuohuo_json["手机号"].'\',\''.$shuohuo_json["所在地区"].'\',\''.$shuohuo_json["详细地址"].'\')">修改收货信息</a><a href="javascript:" style="color:red;margin-left:20px;" onclick="fahuo_tuikuan('.$order->id.');">订单退款</a>':'').'
                    </div>
                	<div class="dianzimiandanxx_jibenxinxi_02_down">
                    	<div class="dianzimiandanxx_jibenxinxi_02_down_01">
                        	<ul>
                        		<li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	发货单号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$order->orderId.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	订单号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$orders.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                        	</ul>
                        </div>
                    	<div class="dianzimiandanxx_jibenxinxi_02_down_02">
                        	<ul>
                        		<li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	会员账号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	13730252145
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	成单时间：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$order->dtTime.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	配送方式：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	 '.$order->kuadi_company.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货人：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["收件人"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	手机号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	 '.$shuohuo_json["手机号"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货地区：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["所在地区"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>                                
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货地址：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["所在地区"].$shuohuo_json["详细地址"].'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                        	</ul>
                        </div>
                    </div>
                </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_5">
	    	<div class="ddxx_jibenxinxi_5_up">	
	        	备注信息：
	        </div>
	    	<div class="ddxx_jibenxinxi_5_down">
	        	<div class="ddxx_jibenxinxi_5_down_01">
	            	会员备注：'.(empty($order->remark)?'无':$order->remark).'
	            </div>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}
//电子订单详情
function order_dianzi_info(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from order_fahuo$fenbiao where id=$id and comId=$comId");
	if(empty($order))die("订单不存在！！");
	$shuohuo_json = array();
	if(!empty($order->shuohuo_json))$shuohuo_json = json_decode($order->shuohuo_json,true);
	if(!empty($order->orderIds))$orderIds = explode(',', $order->orderIds);
	$orders = '';
	if(!empty($orderIds)){
		foreach ($orderIds as $key => $value) {
			$orders .= $db->get_var("select orderId from order$fenbiao where id=".$value).',';
		}
	}
	$kuaidi = '';
	if($order->kuaidi_order){
		$kuaidi = '<li>
                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                    	快递公司：
                    </div>
                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                    	'.$order->kuaidi_title.'
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                    	发货单号：
                    </div>
                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                    	'.$order->kuaidi_order.'
                    </div>
                	<div class="clearBoth"></div>
                </li>';
	}
	$orders = substr($orders, 0,strlen($orders)-1);
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';
	/*if($order->status>-1){
		$str .='<div class="ddxx_jibenxinxi_1" id="order_operate">订单操作：';
		switch ($order->status) {
			case 0:
				$str.='<a href="javascript:" onclick="wuliu('.$id.');">打印物流单</a><a href="javascript:">打印发货单</a><a href="javascript:">撤销发货单</a>';
			break;
			default:
				$str.='<a href="javascript:">打印物流单</a><a href="javascript:">打印发货单</a><a href="javascript:">撤销发货单</a>';
			break;
		}
	    $str.='</div>';
	}*/
	$str.='<div class="dianzimiandanxx_jibenxinxi_02">
                	<div class="dianzimiandanxx_jibenxinxi_02_up">
                    	发货单信息'.(($order->status==0||$order->status==1)?'<a href="javascript:" style="color:red;margin-left:20px;" onclick="xiugai_shouhuo(\''.$order->id.'\',\''.$shuohuo_json["收件人"].'\',\''.$shuohuo_json["手机号"].'\',\''.$shuohuo_json["所在地区"].'\',\''.$shuohuo_json["详细地址"].'\')">修改收货信息</a><a href="javascript:" style="color:red;margin-left:20px;" onclick="fahuo_tuikuan('.$order->id.');">订单退款</a>':'').'
                    </div>
                	<div class="dianzimiandanxx_jibenxinxi_02_down">
                    	<div class="dianzimiandanxx_jibenxinxi_02_down_01">
                        	<ul>
                        		<li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	发货单号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$order->orderId.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	订单号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$orders.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                        	</ul>
                        </div>
                    	<div class="dianzimiandanxx_jibenxinxi_02_down_02">
                        	<ul>
                        	'.$kuaidi.'
                        		<li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	会员账号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["收件人"].'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	成单时间：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$order->dtTime.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	配送方式：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	 '.$order->kuadi_company.'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货人：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["收件人"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	手机号：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	 '.$shuohuo_json["手机号"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货地区：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["所在地区"].'	
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>                                
                                <li>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_left">
                                    	收货地址：
                                    </div>
                                	<div class="dianzimiandanxx_jibenxinxi_02_down_01_right">
                                    	'.$shuohuo_json["所在地区"].$shuohuo_json["详细地址"].'
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <div class="clearBoth"></div>
                        	</ul>
                        </div>
                    </div>
                </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_5">
	    	<div class="ddxx_jibenxinxi_5_up">	
	        	备注信息：
	        </div>
	    	<div class="ddxx_jibenxinxi_5_down">
	        	<div class="ddxx_jibenxinxi_5_down_01">
	            	会员备注：'.(empty($order->remark)?'无':$order->remark).'
	            </div>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}
//货品详情
function order_xiangqing_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$orderIds = $db->get_var("select orderIds from order_fahuo$fenbiao where id=$id");
	$orderlist = $db->get_results("select pdtInfo,num from order_detail$fenbiao where orderId in (".$orderIds.")");
	$products = array();
	if(!empty($orderlist)){
		foreach ($orderlist as $list) {
			$arr = json_decode($list->pdtInfo,true);
			$arr['num'] = $list->num;
			$products[] = $arr;
		}
	}
	$str = '<div class="dianzimiandanxx_huopinxx">
            	<div class="dianzimiandanxx_huopinxx_up">
                	货品详情：
                </div>
            	<div class="dianzimiandanxx_huopinxx_down">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="34">
                        	<td class="dianzimiandanxx_huopinxx_down_title" width="47" align="center" valign="middle">
                            
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="130" align="center" valign="middle">
                            	商品编码
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="394" align="center" valign="middle">
                            	商品名称
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="347" align="center" valign="middle">
                            	规格
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="142" align="center" valign="middle">
                            	市场价
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="142" align="center" valign="middle">
                            	优惠价
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="142" align="center" valign="middle">
                            	数量 
                            </td>
                        </tr>';
                       	foreach($products as $k=>$v){$i++;
                        $str .= '<tr height="38">
                        	<td align="center" valign="middle">
                            	'.$i.'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['sn'].'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['title'].'
                            </td>
                            <td align="center" valign="middle">
                            	 '.$v['key_vals'].'                                      
                            </td>
                            <td align="center" valign="middle">
                            	￥'.$v['price_market'].'
                            </td>
                            <td align="center" valign="middle">
                            	￥'.$v['price_sale'].'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['num'].'
                            </td>
                        </tr>';
                        }
                    $str .= '</table>
                </div>
            </div>';
	echo $str;
	exit;
}
//发货成功货品详情
function fahuo_order_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];//批次表Id
	$fahuoIds = $db->get_var("select fahuoIds from fahuo_pici$fenbiao where id=".$id);
	//$ifhas = $db->get_var("select id from order$fenbiao where orderId='".$request['keyword']."'");
	$orderIds = $db->get_results("select orderIds from order_fahuo$fenbiao where id in (".$fahuoIds.")");
	if(!empty($orderIds)){
		foreach ($orderIds as $order) {
			$dingdanId .= $order->orderIds.',';
		}
	}
	$dingdanId = substr($dingdanId, 0,strlen($dingdanId)-1);
	$orderlist = $db->get_results("select pdtInfo,num,orderId from order_detail$fenbiao where orderId in (".$dingdanId.")");
	$products = array();
	if(!empty($orderlist)){
		foreach ($orderlist as $k=>$list) {
			$products[$k] = json_decode($list->pdtInfo,true);
			$products[$k]['orderId'] = $db->get_var("select orderId from order$fenbiao where id=$list->orderId");
			$products[$k]['num'] = $list->num;
			$products[$k]['price_sale'] = $list->unit_price*$list->num;;
		}
	}
	$str = '<div class="dianzimiandanxx_huopinxx">
            	<div class="dianzimiandanxx_huopinxx_up">
                	货品详情：
                </div>
            	<div class="dianzimiandanxx_huopinxx_down">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="34">
                        	<td class="dianzimiandanxx_huopinxx_down_title" width="47" align="center" valign="middle">
                            
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="130" align="center" valign="middle">
                            	商品编码
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="130" align="center" valign="middle">
                            	订单号
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="394" align="center" valign="middle">
                            	商品名称
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="347" align="center" valign="middle">
                            	规格
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="142" align="center" valign="middle">
                            	价格
                            </td>
                            <td class="dianzimiandanxx_huopinxx_down_title" width="142" align="center" valign="middle">
                            	数量 
                            </td>
                        </tr>';
                       	foreach($products as $k=>$v){$i++;
                       	if($request['keyword']==$v['orderId']){
                       		$shai = '<span style="color:red;">'.$v['orderId'].'</span>';
                       	}else{
                       		$shai = $v['orderId'];
                       	}
                        $str .= '<tr height="38">
                        	<td align="center" valign="middle">
                            	'.$i.'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['sn'].'
                            </td>
                            <td align="center" valign="middle">
                            	'.$shai.'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['title'].'
                            </td>
                            <td align="center" valign="middle">
                            	 '.$v['key_vals'].'                                      
                            </td>
                            <td align="center" valign="middle">
                            	￥'.$v['price_sale'].'
                            </td>
                            <td align="center" valign="middle">
                            	'.$v['num'].'
                            </td>
                        </tr>';
                        }
                    $str .= '</table>
                </div>
            </div>';
	echo $str;
	exit;
}
//单个发货单发货
/*function order_fahuo(){
	global $db,$request;
	$id = $request['id'];
	$fenbiao = 0;
	//设置发货表订单状态
	if($request['id']){
		//订单列表
		$orderIds = $db->get_var("select orderIds from order_fahuo$fenbiao where id=".$request['id']);
		if(!empty($orderIds)){
			$orders = explode(',', $orderIds);
			foreach ($orders as $key => $value) {
				$sql = "update order$fenbiao set status=3 where id=".$value;
				$db->query($sql);
			}
		}
		$db->query("update order_fahuo$fenbiao set status=3,kuaidi_company='".$request['kuaidi_company']."',kuaidi_order='".$request['kuaidi_order']."',fahuotime='".date('Y-m-d H;i:s')."' where id=".$request['id']);
		//添加操作记录
		addJilu($id,$fenbiao,2,'订单发货','订单发货');
		echo '{"code":1,"message":"发货成功"}';
	}else{
		echo '{"code":0,"message":"订单错误"}';
	}
	exit;
}*/
function order_jilu_index(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$jilus = $db->get_results("select * from fahuo_jilu$fenbiao where orderId=$id order by type asc,id desc");
    $jilu2 = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			if($jilu->type==$nowtype){
				$bianliang = 'jilu'.$nowtype;
				$$bianliang.='<tr height="34">
                	<td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->username.'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->operate.'</div>
                    </td>
                    <td align="left" valign="middle">
                    	<div style="padding-left:3%;">'.$jilu->remark.'</div>
                    </td>
                </tr>';
			}else{
				$nowtype = $jilu->type;
				$bianliang = 'jilu'.$nowtype;
				$$bianliang = '<div class="ddxx_caozuojilu_1">
					<div class="ddxx_caozuojilu_1_up">
						发货操作记录
					</div>
					<div class="ddxx_dingdanfuwu_2_down">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
					    	<tbody><tr height="33">
					        	<td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作时间</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作人</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">行为</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">操作内容</div>
					            </td>
					        </tr>
					        <tr height="34">
			                	<td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->username.'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->operate.'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->remark.'</div>
			                    </td>
			                </tr>';
			}
		}
	}
    if(!empty($jilu2)){
    	$jilu2.='</tbody></table></div></div>';
    }
    echo $jilu2;
    exit;
}
//普通发货导出
function daochu(){}
//获取导入列表记录
function get_daoru_list(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = $request['userId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$username = $request['username'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('orderPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql = "select * from fahuo_pici$fenbiao where comId=$comId and type=1";
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($username)){
		$sql.=" and username = '$username'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if($jilus){
		foreach ($jilus as $i => $j) {
			if($j->realNum > 0 && $j->realNum==$j->num){
				$j->zhuangtai = '导入成功';
			}else if($j->realNum==0){
				$j->zhuangtai = '导入失败';
			}else{
				$j->zhuangtai = '部分导入成功';
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//导入订单
function daoru(){
	global $db,$request;
	print_r($_FILE);
}
function daoru_order(){
	global $db,$request;
	$arr = array('顺丰快递'=>'SF','EMS'=>'EMS','宅急送'=>'ZJS','圆通快递'=>'YTO','百世快递'=>'HTKY','中通快递'=>'ZTO','韵达快递'=>'YD','申通快递'=>'STO','天天快递'=>'HHTT','邮政快递包裹'=>'YZPY','德邦'=>'DBL','优速'=>'UC','信丰'=>'XFEX','全峰'=>'QFKD','跨越速运'=>'KYSY','安能小包'=>'ANE','国通'=>'GTO','中铁快运'=>'ZTKY');
	$return = array();
	$return['code'] = 1;
	$return['message'] = '上传成功';
	$reurn['data'] = array();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$filepath = $request['filepath'];
	$filepath = ABSPATH.str_replace('../','',$filepath);
	require_once ABSPATH.'inc/excel.php';
	$pandians = excelToArray($filepath);
	$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
	//file_put_contents('request.txt',$pandianJsonData);
	$pandianJsonData = str_replace("'","\'",$pandianJsonData);
	$pandianJsonData = preg_replace('/((\s)*(\n)+(\s)*)/','',$pandianJsonData);
	$pandianJsonData = stripcslashes($pandianJsonData);
	$jilus = json_decode($pandianJsonData,true);
	$errorJilus = array();
	$success_num = 0;
	$fail_num = 0;
	$dtTime = date("Y-m-d H:i:s");
	$fahuoIds = '';
	//$kuaidi_company = '';
	$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
	$shouhuo_time = strtotime("+$shuohuo_day days");
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$kuaidi_company = '';
			$order = $db->get_row("select id,status,storeId,mendianId from order_fahuo$fenbiao where orderId='".$jilu[0]."' limit 1");
			if(!empty($order->id)){
				foreach ($arr as $keys => $values) {
					if(strstr($keys, $jilu[1]) || strstr($jilu[1] , $keys)){
						//$pandianJsonData = str_replace("'","\'",$pandianJsonData);
						$kuaidi_company = $values;
						break;
					}
				}
				$success_num ++;
				$fahuoIds .= $order->id.',';
				$orderIds = $db->get_var("select orderIds from order_fahuo$fenbiao where orderId='".$jilu[0]."' limit 1");
				if(!empty($orderIds)){
					$db->query("update order$fenbiao set status=3 where id in (".$orderIds.") and status in(2,3)");//设置订单完成
				}
				$db->query("update order_fahuo$fenbiao set status=3,kuaidi_company='".$kuaidi_company."',kuaidi_title='".$jilu[1]."',kuaidi_order='".$jilu[2]."' where orderId='".$jilu[0]."'");//设置发货单完成
				$orderIds = explode(',',$orderIds);
				//减去库存，微信模板消息
				addJilu($jilu->id,$fenbiao,1,'导入发货信息','导入发货信息');
				$storeName = $db->get_var("select title from demo_kucun_store where id=$order->storeId");
				//$productId = $order->productId;
				//require_once(ABSPATH.'/wxmbxx.php');
				if(!empty($orderIds)){
					foreach ($orderIds as $k => $v) {
						$o = $db->get_row("select storeId from order$fenbiao where id=$v and status=3");
						if(!empty($o)){
							$details = $db->get_results("select inventoryId,num,pdtInfo,productId,unit from order_detail$fenbiao where orderId=$v");
							$timed_task = array();
							$timed_task['dtTime'] = $shouhuo_time;
							$timed_task['comId'] = $comId;
							$timed_task['router'] = 'order_autoShouhuo';
							$timed_task['params'] = '{"order_id":'.$v.'}';
							$db->insert_update('demo_timed_task',$timed_task,'id');
							//$o = $db->get_row("select inventoryId,storeId,pdtNums,userId,orderId from order$fenbiao where id=$v");
							if(!empty($details)){
								foreach ($details as $detail) {
									//减库存
									$orderInt = getOrderId($comId,2);
									$orderId = 'OUT_'.date("Ymd").'_'.$orderInt;
									$pdtInfo = $detail->pdtInfo;
									$db->query("update demo_kucun set kucun=kucun-$detail->num,yugouNum=yugouNum-$detail->num where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
									//创建定时收货任务
									/*$timed_task = array();
									$timed_task['dtTime'] = $shouhuo_time;
									$timed_task['router'] = 'order_autoShouhuo';
									$timed_task['params'] = '{"order_id":'.$v.'}';
									$db->insert_update('demo_timed_task',$timed_task,'id');*/
									//增加库存记录
									$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,2,$order->storeId,0,'$orderId',$orderInt,'$dtTime','销售出库',1,$userId,'$username','',0,'','','$storeName')");
									$jiluId = $db->get_var("select last_insert_id();");
									$k = $db->get_row("select kucun,chengben from demo_kucun where inventoryId=$detail->inventoryId and storeId=$order->storeId limit 1");
									$kucun = $k->kucun;
									$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values($comId,$jiluId,$detail->inventoryId,$detail->productId,'$pdtInfo',$order->storeId,'$storeName',-$detail->num,1,'$kucun','',2,'销售出库','$dtTime','$detail->unit','0',0)";
									$db->query($rukuSql);
								}
							}
						}
						
					}
				}
				//减去库存，微信模板消息
			}else{
				$fail_num++;
				$return['data'][] = $jilu;
			}
		}
		if(!empty($fahuoIds)){
			$fahuoIds = substr($fahuoIds, 0,strlen($fahuoIds)-1);
		}
		if(empty($fail_num)){
			$res = '导入成功';
			$content = '实际导入发货单'.$success_num.'条，全部导入成功！';
		}else{
			$res = '部分导入成功';
			$content = '实际导入发货单'.$success_num.'条，'.$fail_num.'个导入失败！';
		}
		//$mendianId = $_SESSION[TB_PREFIX.'mendianId'];
		$fahuo_pici = array();
		$fahuo_pici['comId'] = $comId;
		$fahuo_pici['type'] = 1;
		$fahuo_pici['orderId'] = date("YmdHis").rand(1000000000,9999999999);//批次Id;
		$fahuo_pici['fahuoIds'] = $fahuoIds;
		$fahuo_pici['num'] = count($jilus);
		$fahuo_pici['realNum'] = $success_num;
		$fahuo_pici['faliNum'] = $fail_num;
		$fahuo_pici['dtTime'] = $dtTime;
		$fahuo_pici['storeId'] = $order->storeId;
		$fahuo_pici['mendianId'] = $order->mendianId;
		$fahuo_pici['username'] = $username;
		$fahuo_pici['need_peihuo'] = 0;
		$db->insert_update('fahuo_pici'.$fenbiao,$fahuo_pici,'id');
		$return['content'] = $content;
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		//echo '{"code":1,"message":"上传成功","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);
	}
	exit;
}
//打印发货单
function print_fahuo(){}
//生成电子面单
//对接快递鸟发货
function fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//file_put_contents('000.txt',"0");
	$fail_num = 0;
	$success_num = 0;
	$fenbiao = getFenbiao($comId,20);
	//require_once(ABSPATH.'/inc/KdApiPrintDemo.php');
	//file_put_contents('000.txt',json_encode($request, JSON_UNESCAPED_UNICODE));
	if(!empty($request['ids']) && !empty($request['kuaidi_id'])){
		$fahuo_arr = explode(',', $request['ids']);
		$num = count($fahuo_arr);
		$orderId = date("YmdHis").rand(1000000000,9999999999);
		//插入批次记录表
		$pici_storeId = $db->get_var("select storeId from order_fahuo$fenbiao where id in(".$request['ids'].") limit 1");
		$db->query("insert into fahuo_pici$fenbiao(comId,type,orderId,fahuoIds,num,dtTime,storeId,kuaidiniao_id) values ($comId,2,'".$orderId."','',".$num.",'".date('Y-m-d H:i:s')."',$pici_storeId,".$request['kuaidi_id'].")");
		$piciId = $db->get_var("select last_insert_id();");
		//根据id查找订单
		$orders = $db->get_results("select * from order_fahuo$fenbiao where id in (".$request['ids'].") and status in(0,1)");
		//计算自动收货时间
		$shuohuo_day = $db->get_var("select time_shouhuo from demo_shezhi where comId=$comId");
		$shouhuo_time = strtotime("+$shuohuo_day days");
		$dtTime = date("Y-m-d H:i:s");
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		$kuaidiniao = $db->get_row("select * from demo_kuaidiniao where id=".(int)$request['kuaidi_id']);
		define('EBusinessID',$kuaidiniao->EBusinessID);
		//电商加密私钥，快递鸟提供，注意保管，不要泄漏
		define('AppKey', $kuaidiniao->AppKey);
		//请求url，正式环境地址：http://api.kdniao.cc/api/Eorderservice    测试环境地址：http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json
		define('ReqURL', 'http://api.kdniao.cc/api/Eorderservice');
		require_once(ABSPATH.'/inc/KdApiEOrderDemo_seller.php');
		//file_put_contents('111.txt',"1");
		//file_put_contents('222.txt',json_encode($eorder, JSON_UNESCAPED_UNICODE));
		if(!empty($orders)){
			//file_put_contents('222.txt',json_encode($eorder, JSON_UNESCAPED_UNICODE));
			foreach ($orders as $order) {
				$k_title = '';
				//new by zyc
				$eorder = [];
				$eorder["ShipperCode"] = $kuaidiniao->kuaidi_company;
				if(!empty($kuaidiniao->CustomerName)){
					$eorder["CustomerName"] =	$kuaidiniao->CustomerName;
				}
				if(!empty($kuaidiniao->CustomerPwd)){
					$eorder["CustomerPwd"] =	$kuaidiniao->CustomerPwd;
				}
				if(!empty($kuaidiniao->MonthCode)){
					$eorder["MonthCode"] =	$kuaidiniao->MonthCode;
				}
				$eorder["OrderCode"] = $order->orderId;
				$eorder["PayType"] = 1;
				$eorder["ExpType"] = 1;

				$addr=$db->get_row("select * from demo_kucun_store where id=".$order->storeId." limit 0,1");
				$sender = [];
				$sender["Name"] = $kuaidiniao->fahuo_user;
				$sender["Mobile"] = $kuaidiniao->fahuo_phone;
				$area=$db->get_row("select * from demo_area where id=".$addr->areaId);
				$city=$db->get_row("select * from demo_area where id=".$area->parentId);
				$province=$db->get_var("select title from demo_area where id=".$city->parentId);
				$sender["ProvinceName"] = $province;
				$sender["CityName"] = $city->title;
				$sender["ExpAreaName"] = $area->title;
				$sender["Address"] = $addr->address;
				$sender["PostCode"] = '000000';

				//买家地址
				$shuohuo_json = json_decode($order->shuohuo_json,true);
				if(strpos($shuohuo_json['详细地址'],'【')===false){
					//$xiaoqu = $db->get_var("select title from user_address where id=$order->addressId");
					if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
				}
				$shuohuo = explode("-", $shuohuo_json['所在地区']); 
				$receiver = [];
				$receiver["Name"] = $shuohuo_json['收件人'];
				$receiver["Mobile"] = $shuohuo_json['手机号'];
				$receiver["ProvinceName"] = $shuohuo[0];
				$receiver["CityName"] = $shuohuo[1];
				$receiver["ExpAreaName"] = $shuohuo[2];
				$receiver["Address"] = $shuohuo_json['详细地址'];
				$receiver["PostCode"] = '000000';
				//获取订单表产品名称
				//$product_json = $db->get_var("select product_json from order$fenbiao where id in (".$order->orderIds.")");
				//$product_json = json_decode($product_json,true);
				//规格
				$product_list = array();
				$gift_list = array();
				$dingdan = $db->get_results("select product_json from order$fenbiao where id in (".$order->orderIds.")");
				foreach($dingdan as $o){
				   	$pdts = json_decode($o->product_json);
				   	if(!empty($pdts)){
				   		foreach ($pdts as $pdt) {
				   			if(!empty($product_list[$pdt->id])){
						       //增加它的数量
						      $product_list[$pdt->id]['num']+=$pdt->num;
						   }else{
						       $arr = array();
						       $arr['num'] = $pdt->num;
						       $arr['title'] = $pdt->fahuo_title;
						       $arr['key_vals'] = str_replace("+", "", $pdt->key_vals);
						       $product_list[$pdt->id] = $arr;
						   }
				   		}
				   	}
				}
				//奖品
				foreach($dingdan as $o){
				   	$pdts = json_decode($o->product_json);
				   	if(!empty($pdts)){
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
				}
				//奖品
				foreach ($product_list as $k => $v) {
					$k_title .= $v['title'].'['.$v['key_vals'].']*'.$v['num'].',';
				}
				$k_title = substr($k_title, 0,strlen($k_title)-1);
				//奖品
				if($gift_list){
					foreach ($gift_list as $k => $v) {
						$k_titles .= $v['giftTitle'].'*'.$v['num'].',';
					}
					$jiangpin = substr($k_titles, 0,strlen($k_titles)-1);
					$k_title = $k_title.','.$jiangpin;
				}
				//奖品
				//规格
				$goodsName = $k_title;
				//$goodsName=substr($goodsName,1);
				$commodityOne = [];
				$commodityOne["GoodsName"] = $goodsName;
				$commodity = [];
				$commodity[] = $commodityOne;
				if($kuaidiniao->kuaidi_company!='YTO' && $kuaidiniao->kuaidi_company!='HTKY'){
					$eorder["TemplateSize"] = 180;
				}
				if($kuaidiniao->kuaidi_company=='YTO'){
					$eorder["TemplateSize"] = 18001;
				}
				$eorder["Sender"] = $sender;
				$eorder["Receiver"] = $receiver;
				$eorder["Commodity"] = $commodity;
				$eorder["CustomArea"] = $goodsName;
				//调用电子面单
				
				$jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);
				$jsonResult = submitEOrder($jsonParam);
				$result = json_decode($jsonResult, true);

				if($result["ResultCode"] == "100") {
					$check_status = $db->get_var("select status from order_fahuo$fenbiao where id=".$order->id);
					if($check_status!=0 && $check_status!=1){
						$fail_num++;
					}else{
						addJilu($order->id,$fenbiao,1,'电子面单发货','电子面单发货');
						//file_put_contents("express.txt",$result['Order']["LogisticCode"]);
						$success_num++;
						$db->query("update order_fahuo$fenbiao set status=3,kuaidi_title='".$request['kuaidi_title']."',kuaidi_company='".$request['kuaidi_company']."',kuaidi_order='".$result['Order']["LogisticCode"]."',fahuoTime='".date('Y-m-d H:i:s')."',piciId=".$piciId." where id=".$order->id);
						$ids .= $order->id.',';//成功id
						$orderIds = explode(',',$order->orderIds);
						$db->query("update order$fenbiao set status=3 where id in($order->orderIds)");
						//如果是第一次生成面单，进行减库存、创建自动收货定时任务、修改库存数据
						if($order->status==1||$order->status==0){
							
							$storeName = $db->get_var("select title from demo_kucun_store where id=$order->storeId");
							//$productId = $order->productId;
							//require_once(ABSPATH.'/wxmbxx.php');
							if(!empty($orderIds)){
								foreach ($orderIds as $k => $v) {
									$o = $db->get_row("select storeId,pdtNums,userId,orderId from order$fenbiao where id=$v");
									if(!empty($o)){
										$timed_task = array();
										$timed_task['dtTime'] = $shouhuo_time;
										$timed_task['comId'] = $comId;
										$timed_task['router'] = 'order_autoShouhuo';
										$timed_task['params'] = '{"order_id":'.$v.'}';
										$db->insert_update('demo_timed_task',$timed_task,'id');
										$details = $db->get_results("select inventoryId,num from order_detail$fenbiao where orderId=$v");
										if(!empty($details)){
											$orderInt = getOrderId($comId,2);
											$orderId = 'OUT_'.date("Ymd").'_'.$orderInt;
											$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName) value($comId,2,$o->storeId,0,'$orderId',$orderInt,'$dtTime','销售出库',1,$userId,'$username','',0,'','','$storeName')");
											$jiluId = $db->get_var("select last_insert_id();");
											foreach ($details as $detail) {
												$pdtInfoArry = array();
												$inventory = $db->get_row("select title,sn,key_vals,productId from demo_product_inventory where id=$detail->inventoryId");
												$pdtInfoArry['sn'] = $inventory->sn;
												$pdtInfoArry['title'] = $inventory->title;
												$pdtInfoArry['key_vals'] = $inventory->key_vals;
												$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
												$db->query("update demo_kucun set kucun=kucun-$detail->num,yugouNum=yugouNum-$detail->num where inventoryId=$detail->inventoryId and storeId=$o->storeId limit 1");
												
												$k = $db->get_row("select kucun,chengben from demo_kucun where inventoryId=$detail->inventoryId and storeId=$o->storeId limit 1");
												$kucun = $k->kucun;
												$rukuSql = "insert into demo_kucun_jiludetail$fenbiao(comId,jiluId,inventoryId,productId,pdtInfo,storeId,storeName,num,status,kucun,beizhu,type,typeInfo,dtTime,units,chengben,zongchengben) values($comId,$jiluId,$detail->inventoryId,$inventory->productId,'$pdtInfo',$o->storeId,'$storeName',$detail->num,1,'$kucun','',2,'销售出库','$dtTime','份','0',0)";
												$db->query($rukuSql);
											}
										}
									}
								}
							}
						}
					}
				}
				else {
					$fail_num++;
					file_put_contents('logs/res_'.date('Y-m-d').'.txt',$jsonResult,FILE_APPEND);
				}
			}
		}
		//更新批次的发货单id
		if($ids){
			$ids = substr($ids, 0,strlen($ids)-1);
			$db->query("update fahuo_pici$fenbiao set fahuoIds='".$ids."',num=".$success_num." where id=".$piciId);
		}
		echo '{"code":1,"message":"发货成功","fail_num":'.$fail_num.',"success_num":'.$success_num.',"piciId":'.$piciId.'}';
		exit;
	}
}
function yifahuo(){}
//获取已发货列表
function getLists(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$storeId = (int)$_SESSION[TB_PREFIX.'storeId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select * from fahuo_pici$fenbiao where comId=$comId and need_peihuo=1 and num>0";
	if($type==1){
		$sql.=" and is_peihuo=1";
	}else{
		$sql.=" and is_peihuo=0";
	}
	if(!empty($keyword)){
		$fahuoIds = (int)$db->get_var("select fahuoId from order$fenbiao where orderId='$keyword' limit 1");
		$fahuoId = (int)$db->get_var("select id from order_fahuo$fenbiao where orderId='$keyword' or kuaidi_order='$keyword' limit 1");
		if(!empty($fahuoIds)){
			$sql .= " and find_in_set($fahuoIds,fahuoIds) ";
			//file_put_contents('request.txt',$sql);
		}else if(!empty($fahuoId)){
			$sql .= " and find_in_set($fahuoId,fahuoIds) ";
		}else{
			$sql .=" and 1=2";
		}
		
		//$sql.=" and (orderId like '%$keyword%')";
	}
	$countsql = str_replace('*','count(*)',$sql);
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
			$j->caozuo = $j->is_peihuo==0?'<span class="peihuo_table"><a href="javascript:" onclick="peihuo_wancheng('.$j->id.');"><img src="images/peihuo_14.png"> 配货完成</a></span>':'';
			$j->print = '<span class="peihuo_table"><a href="?m=system&s=fahuo&a=print_peihuo&id='.$j->id.'" target="_blank"><img src="images/peihuo_13.png"> 配货单打印</a></span>';
			if($j->type==2){
				$j->print .= '<span class="peihuo_table"><a href="/inc/KdApiPrintDemo_seller.php?id='.$j->id.'" onclick="return window.confirm(\'是否重新打印？\')" target="_blank"><img src="images/peihuo_13.png"> 打印电子面单</a></span>';
			}
			$j->is_peihuo=$j->is_peihuo==1?'<span style="color:green">已配货</span>':'<span style="color:red">待配货</span>';
			$j->type=$j->type==1?'普通发货':'电子面单发货';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//获取已发货列表
function getPiciLists(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//$storeId = (int)$_SESSION[TB_PREFIX.'storeId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select * from fahuo_pici$fenbiao where comId=$comId and (type=2 or need_peihuo=0) and num>0";
	if($type==1){
		$sql.=" and type=1";
	}else if($type==2){
		$sql.=" and type=2";
	}
	if(!empty($keyword)){
		$fahuoIds = (int)$db->get_var("select fahuoId from order$fenbiao where orderId='$keyword' limit 1");
		$fahuoId = (int)$db->get_var("select id from order_fahuo$fenbiao where orderId='$keyword' or kuaidi_order='$keyword' limit 1");
		if(!empty($fahuoIds)){
			$sql .= " and find_in_set($fahuoIds,fahuoIds) ";
			//file_put_contents('request.txt',$sql);
		}else if(!empty($fahuoId)){
			$sql .= " and find_in_set($fahuoId,fahuoIds) ";
		}else{
			$sql .=" and 1=2";
		}
		
		//$sql.=" and (orderId like '%$keyword%')";
	}
	$countsql = str_replace('*','count(*)',$sql);
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
			$j->caozuo = $j->is_peihuo==0?'<span class="peihuo_table"><a href="javascript:" onclick="peihuo_wancheng('.$j->id.');"><img src="images/peihuo_14.png"> 配货完成</a></span>':'';
			$j->print = '<span class="peihuo_table"><a href="?m=system&s=fahuo&a=print_peihuo&id='.$j->id.'" target="_blank"><img src="images/peihuo_13.png"> 配货单打印</a></span>';
			if($j->type==2){
				$j->print .= '<span class="peihuo_table"><a href="/inc/KdApiPrintDemo_seller.php?id='.$j->id.'" onclick="return window.confirm(\'是否重新打印？\')" target="_blank"><img src="images/peihuo_13.png"> 打印电子面单</a></span>';
			}
			$j->is_peihuo=$j->is_peihuo==1?'<span style="color:green">已配货</span>':'<span style="color:red">待配货</span>';
			$j->type=$j->type==1?'普通发货':'电子面单发货';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//批次发货列表详情
function order_fahuo_info(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
    $id = (int)$request['id'];
	$fahuoids = $db->get_var("select fahuoIds from fahuo_pici$fenbiao where id=$id");
	$jilus = $db->get_results("select * from order_fahuo$fenbiao where id in (".$fahuoids.") order by id desc");
    $jilu2 = '<div class="ddxx_caozuojilu_1">
					<div class="ddxx_dingdanfuwu_2_down">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
					    	<tbody><tr height="33">
					    		<td align="center" valign="middle" class="ddxx_dingdanfuwu_2_down_title" style="width:5%">
					            	<div style="padding-left:3%;">打印</div>
					            </td>
					        	<td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">发货单号</div>
					            </td>
								<td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">订单号</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">运单号</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">收货人</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">收货人电话</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title" style="width:30%;">
					            	<div style="padding-left:3%;">收货人地址</div>
					            </td>
					            <td align="left" valign="middle" class="ddxx_dingdanfuwu_2_down_title">
					            	<div style="padding-left:3%;">备注</div>
					            </td>
					        </tr>';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$shuohuo_json = json_decode($jilu->shuohuo_json,true);
			$orderIds = $db->get_var("select group_concat(orderId) from order$fenbiao where id in($jilu->orderIds)");
			if($request['keyword']==$jilu->orderId){
				$str = '<span style="color:red;">'.$jilu->orderId.'</span>';
			}else{
				$str = $jilu->orderId;
			}
			$jilu2 .= '<tr height="34">
								<td align="center" valign="middle">
			                    	<div style="padding-left:3%;"><a href="/inc/KdApiPrintDemos_seller.php?id='.$jilu->id.'" onclick="return window.confirm(\'是否重新打印？\')" target="_blank">打印</a></div>
			                    </td>
			                	<td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$str.'</div>
			                    </td>
								<td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.str_replace(',','<br>',$orderIds).'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->kuaidi_order.'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$shuohuo_json['收件人'].'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$shuohuo_json['手机号'].'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$shuohuo_json['详细地址'].'</div>
			                    </td>
			                    <td align="left" valign="middle">
			                    	<div style="padding-left:3%;">'.$jilu->remark.'</div>
			                    </td>
			                </tr>';
		}
	}
    if(!empty($jilu2)){
    	$jilu2.='</tbody></table></div></div>';
    }
    echo $jilu2;
    exit;
}
//合并订单
function hebing(){}
//bingdan
function bingdan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(count($request['hebing'])>1){
		$fahuoIds = implode($request['hebing'], ',');
		$fahuo_title = '';
		$product_title = '';
		$orderIds = $db->get_results("select orderIds,weight,product_num,fahuo_title,product_title from order_fahuo$fenbiao where id in (".$fahuoIds.") and status=1");
		if(empty($orderIds)){
			echo "<script>history.go(-1);</script>";
			exit;
		}
		$db->query("update order_fahuo$fenbiao set status=-1 where id in (".$fahuoIds.")");
		if(!empty($orderIds)){
			foreach ($orderIds as $orderId) {
				$dingdanId .= $orderId->orderIds.',';
				$newweight += $orderId->weight;
				$newproduct_num += $orderId->product_num;
				if(empty($fahuo_title)){
					$fahuo_title = $orderId->fahuo_title;
				}elseif(strpos($fahuo_title, $orderId->fahuo_title)===false){
					$fahuo_title .= ','.$orderId->fahuo_title;
				}
				if(empty($product_title)){
					$product_title = $orderId->product_title;
				}elseif(strpos($product_title, $orderId->product_title)===false){
					$product_title .= ','.$orderId->product_title;
				}
			}
		}
		$dingdanId = substr($dingdanId, 0,strlen($dingdanId)-1);//订单Id
		//生成新的发货单
		$fahuos = $db->get_row("select * from order_fahuo$fenbiao where id=".$request['hebing'][0]);//获取原来数据
		$fahuo = array();
		$fahuo['comId'] = 10;
		$fahuo['mendianId'] = $fahuos->mendianId;
		$fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$fahuo['orderIds'] = $dingdanId;
		$fahuo['type'] = 1;
		//$fahuo['showTime'] = date("Y-m-d");
		$fahuo['showTime'] = $fahuos->showTime;
		$fahuo['storeId'] = $fahuos->storeId;
		$fahuo['dtTime'] = date("Y-m-d H:i:s");
		$fahuo['shuohuo_json'] = $fahuos->shuohuo_json;
		$fahuo['productId'] = $fahuos->productId;
		$fahuo['tuanzhang'] = $fahuos->tuanzhang;
		$fahuo['product_title'] = $product_title;
		$fahuo['fahuo_title'] = $fahuo_title;
		$fahuo['addressId'] = $fahuos->addressId;
		$fahuo['showTime'] = $fahuos->showTime;
		$fahuo['piciId'] = $fahuos->piciId;
		$fahuo['product_num'] = $newproduct_num;
		$fahuo['old_ids'] = $fahuoIds;
		$fahuo['weight'] = $newweight;
		$fahuo['is_hebing'] = 1;
		$fahuo['status'] = 1;
		$db->insert_update('order_fahuo$fenbiao',$fahuo,'id');
		$fahuoId = $db->get_var("select last_insert_id();");
		$db->query("update order$fenbiao set fahuoId=$fahuoId where id in (".$dingdanId.")");//修改订单
		//生成新发货单结束
		//修改原来的发货单为无效
		
		echo "<script>alert('合并成功！');history.go(-1);</script>";
	}else{
		echo "<script>alert('合并订单数必须大于1');history.go(-1);</script>";
	}
	exit;
}
function quxiao_hebing(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	if(count($request['hebing'])>0){
		$fahuoIds = implode($request['hebing'], ',');
		//修改原来的发货单为不需要合并
		$db->query("update order_fahuo$fenbiao set is_hebing=-1 where id in (".$fahuoIds.")");
		echo "<script>alert('设置成功！');history.go(-1);</script>";
	}else{
		echo "<script>alert('请选择发货单');history.go(-1);</script>";
	}
	exit;
}
//修改收货信息
function update_shouhuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$shouhuo_json = array();
	$shouhuo_json['收件人'] = $request['name'];
	$shouhuo_json['手机号'] = $request['phone'];
	$shouhuo_json['所在地区'] = $request['diqu'];
	$shouhuo_json['详细地址'] = $request['address'];
	$str = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
	$db->query("update order_fahuo$fenbiao set shuohuo_json='$str' where id=".(int)$request['id']);
	die('{"code":1,"message":"修改成功"}');
}
//获取待审核列表
function shenhe(){}
//获取列表
function getShenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$mendianId = $_SESSION[TB_PREFIX.'mendianId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing from order_fahuo$fenbiao where mendianId=$mendianId and status=0 ";
	$yushouId = (int)$request['yushouId'];
	if($yushouId>0){
		$sql.=" and yushouId=$yushouId";
	}else{
		$sql.=" and yushouId=0";
	}
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
	if($type==1){
		$sql.=" and status=0";
	}elseif($type==2){
		$sql.=" and status=1";
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
		$ids = (int)$db->get_var("select id from order$fenbiao where orderId='$keyword' limit 1");

		$sql.=" and (orderId like '%$keyword%' or find_in_set($ids,orderIds) or shuohuo_json like '%$keyword%' or product_title like '%$keyword%')";
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
	if(!empty($pdtInfo)){
		/*$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";*/
		$sql.=" and product_json like '%$pdtInfo%'";
	}
	
	if(!empty($mendian)){
		$mendianIds = $db->get_var("select group_concat(id) from mendian where title like '%$mendian%'");
		if(empty($mendianIds))$mendianIds='0';
		$sql.=" and mendianId in($mendianIds)";
	}
	$countsql = str_replace('id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing','count(*)',$sql);
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
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待审核</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">已配货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">已完成</span>';
				break;
				case -1:
					$status = '<span style="color:green;">无效</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">暂停</span>';
				break;
			} 
			$zero1=strtotime (date("Y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
			$zero2=strtotime ($j->dtTime);  //过年时间，不能写2014-1-21 24:00:00  这样不对 
			$j->days=abs(ceil(($zero1-$zero2)/86400)).'天'; //60s*60min*24h   
			if($j->status==1){
				$j->daochutype='待发货';
			}else{
				$j->daochutype='待审核';
			}
			$j->dayinStatus = '未打印';
			$j->status_info = $status;
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			if(strpos($shuohuo_json['详细地址'],'【')===false){
				//$xiaoqu = $db->get_var("select title from user_address where id=$j->addressId");
				if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
			}
			if($j->is_hebing==1){
				$j->orderId='<span style="color:#f00;">'.$j->orderId.'</span>';
			}
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'];
			$j->tel = $shuohuo_json['手机号'];
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$product_array = array();
			if(!empty($j->product_json))$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			if(!empty($product_array)){
				foreach ($product_array as $val) {
					$j->pdt_info.=$val->title.'*'.$val->num;
				}
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function shenhes(){
	global $db,$request;
	if($request['ids']){
		$sql = "update order_fahuo$fenbiao set status=1 where id in (".$request['ids'].")";
		if($db->query($sql)){
			die('{"code":1,"message":"审核成功"}');
		}else{
			die('{"code":0,"message":"审核成功"}');
		}
	}
}
//2019.1.14 增加发货成功
function fhsuccess(){}
//获取列表
function getfhsuccess(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//$mendianId = $_SESSION[TB_PREFIX.'mendianId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing,fahuoTime from order_fahuo$fenbiao where comId=$comId and status=3 ";
	$yushouId = (int)$request['yushouId'];
	if($yushouId>0){
		$sql.=" and yushouId=$yushouId";
	}else{
		$sql.=" and yushouId=0";
	}
	if($type==1){
		$sql.=" and kuaidi_type=1";
	}elseif($type==2){
		$sql.=" and kuaidi_type=2";
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
		$ids = (int)$db->get_var("select id from order$fenbiao where orderId='$keyword' limit 1");

		$sql.=" and (orderId='$keyword' or kuaidi_order='$keyword' or find_in_set($ids,orderIds) or shuohuo_json like '%$keyword%' or product_title like '%$keyword%')";
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
	if(!empty($pdtInfo)){
		/*$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";*/
		$sql.=" and product_json like '%$pdtInfo%'";
	}
	
	if(!empty($mendian)){
		$mendianIds = $db->get_var("select group_concat(id) from mendian where title like '%$mendian%'");
		if(empty($mendianIds))$mendianIds='0';
		$sql.=" and mendianId in($mendianIds)";
	}
	$countsql = str_replace('id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing','count(*)',$sql);
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
			$j->fahuoTime = date("Y-m-d H:i",strtotime($j->fahuoTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待审核</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">已配货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">已完成</span>';
				break;
				case -1:
					$status = '<span style="color:green;">无效</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">暂停</span>';
				break;
			} 
			$zero1=strtotime (date("Y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
			$zero2=strtotime ($j->dtTime);  //过年时间，不能写2014-1-21 24:00:00  这样不对 
			$j->days=abs(ceil(($zero1-$zero2)/86400)).'天'; //60s*60min*24h   
			if($j->status==1){
				$j->daochutype='待发货';
			}else{
				$j->daochutype='待审核';
			}
			$j->dayinStatus = '未打印';
			$j->status_info = $status;
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			if(strpos($shuohuo_json['详细地址'],'【')===false){
				//$xiaoqu = $db->get_var("select title from user_address where id=$j->addressId");
				if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
			}
			if($j->is_hebing==1){
				$j->orderId='<span style="color:#f00;">'.$j->orderId.'</span>';
			}
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'];
			$j->tel = $shuohuo_json['手机号'];
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$product_array = array();
			if(!empty($j->product_json))$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			if(!empty($product_array)){
				foreach ($product_array as $val) {
					$j->pdt_info.=$val->title.'*'.$val->num;
				}
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getChexiaos(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	//$mendianId = $_SESSION[TB_PREFIX.'mendianId'];
	$fenbiao = getFenbiao($comId,20);
	$scene = (int)$request['scene'];
	$status = $request['status'];
	$type = (int)$request['type'];
	$mendian = $request['mendian'];
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
	$sql = "select id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing,fahuoTime from order_fahuo$fenbiao where comId=$comId and status=-1 ";
	$yushouId = (int)$request['yushouId'];
	if($yushouId>0){
		$sql.=" and yushouId=$yushouId";
	}else{
		$sql.=" and yushouId=0";
	}
	if($type==1){
		$sql.=" and kuaidi_type=1";
	}elseif($type==2){
		$sql.=" and kuaidi_type=2";
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
		$ids = (int)$db->get_var("select id from order$fenbiao where orderId='$keyword' limit 1");

		$sql.=" and (orderId='$keyword' or kuaidi_order='$keyword' or find_in_set($ids,orderIds) or shuohuo_json like '%$keyword%' or product_title like '%$keyword%')";
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
	if(!empty($pdtInfo)){
		/*$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";*/
		$sql.=" and product_json like '%$pdtInfo%'";
	}
	
	if(!empty($mendian)){
		$mendianIds = $db->get_var("select group_concat(id) from mendian where title like '%$mendian%'");
		if(empty($mendianIds))$mendianIds='0';
		$sql.=" and mendianId in($mendianIds)";
	}
	$countsql = str_replace('id,orderId,comId,mendianId,type,status,dtTime,remark,shuohuo_json,product_title,weight,addressId,is_hebing','count(*)',$sql);
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
			$j->fahuoTime = date("Y-m-d H:i",strtotime($j->fahuoTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待审核</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">已配货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">已完成</span>';
				break;
				case -1:
					$status = '<span style="color:green;">无效</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">暂停</span>';
				break;
			} 
			$zero1=strtotime (date("Y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
			$zero2=strtotime ($j->dtTime);  //过年时间，不能写2014-1-21 24:00:00  这样不对 
			$j->days=abs(ceil(($zero1-$zero2)/86400)).'天'; //60s*60min*24h   
			if($j->status==1){
				$j->daochutype='待发货';
			}else{
				$j->daochutype='待审核';
			}
			$j->dayinStatus = '未打印';
			$j->status_info = $status;
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			if(strpos($shuohuo_json['详细地址'],'【')===false){
				//$xiaoqu = $db->get_var("select title from user_address where id=$j->addressId");
				if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
			}
			if($j->is_hebing==1){
				$j->orderId='<span style="color:#f00;">'.$j->orderId.'</span>';
			}
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'];
			$j->tel = $shuohuo_json['手机号'];
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
			$product_array = array();
			if(!empty($j->product_json))$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			if(!empty($product_array)){
				foreach ($product_array as $val) {
					$j->pdt_info.=$val->title.'*'.$val->num;
				}
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//新加方法
function peihuo(){
	global $db,$request;
}
function peihuo_wancheng(){
	global $db,$request;
	$ids = $request['ids'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$db->query("update fahuo_pici$fenbiao set is_peihuo=1 where id in($ids) and comId=$comId");
	die('{"code":1}');
}
function miandan_queren(){}
function queren_miandan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$content = preg_replace('/((\s)*(\n)+(\s)*)/','|',$request['content']);
	$arr = explode('|',$content);
	$return = '';
	$err = '';
	if(!empty($arr)){
		foreach ($arr as $kuaidi) {
			if(!empty($kuaidi)){
				$fahuoId = $db->get_var("select id from order_fahuo$fenbiao where comId=$comId and kuaidi_order='$kuaidi' limit 1");
				if(empty($fahuoId)){
					$err.='<li><img src="images/miandan_20.png"/> <span>快递单号【'.$kuaidi.'】不存在！请核实</span></li>';
				}else{
					$db->query("update order_fahuo$fenbiao set status=3 where id=$fahuoId");
					addJilu($fahuoId,$fenbiao,1,'电子面单发货确认','电子面单发货确认');
					$return.='<li><img src="images/miandan_19.png"/> 快递单号【'.$kuaidi.'】确认成功！</li>';
				}
			}
		}
	}
	echo $err.$return;
}
function pici(){}
function zanting(){}
//暂停发货
function zanting_fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$fahuos = $db->get_results("select id,status from order_fahuo$fenbiao where id in($ids) and comId=$comId");
	if(!empty($fahuos)){
		foreach ($fahuos as $fahuo) {
			if($fahuo->status==0){
				$db->query("update order_fahuo$fenbiao set status=-2 where id=$fahuo->id");
				addJilu($fahuo->id,$fenbiao,1,'发货暂停','发货暂停');
			}
		}
	}
	die('{"code":1}');
}
function huifu_fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$fahuos = $db->get_results("select id,status from order_fahuo$fenbiao where id in($ids) and comId=$comId");
	if(!empty($fahuos)){
		foreach ($fahuos as $fahuo) {
			if($fahuo->status==-2){
				$db->query("update order_fahuo$fenbiao set status=0 where id=$fahuo->id");
				addJilu($fahuo->id,$fenbiao,1,'恢复发货','恢复发货');
			}
		}
	}
	die('{"code":1}');
}
function get_miandans(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$storeId = (int)$request['storeId'];
	$kuaidis = $db->get_results("select * from demo_kuaidiniao where comId=$comId and storeId=$storeId");
	$str = '<option value="0">请选择快递</option>';
	if(!empty($kuaidis)){
		foreach ($kuaidis as $kuaidi) {
			$str .='<option value="'.$kuaidi->kuaidi_company.'" data-value="'.$kuaidi->id.'">'.$kuaidi->kuaidi_title.'</option>';
		}
	}
	die($str);
}
//取消发货
function qx_fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$fahuoIds = $request['ids'];
	$fenbiao = getFenbiao($comId,20);
	$fahuos = $db->get_results("select id,orderIds from order_fahuo$fenbiao where id in($fahuoIds) and status in(0,1)");
	if(!empty($fahuos)){
		//设置发货单状态
		$db->query("update order_fahuo$fenbiao set status=-1,remark='管理员取消，操作人：".$_SESSION[TB_PREFIX.'name']."' where id in($fahuoIds)");
		foreach ($fahuos as $fahuo){
			$oids = explode(',',$fahuo->orderIds);
			if(!empty($oids)){
				foreach ($oids as $orderId) {
					$order = $db->get_row("select * from order$fenbiao where id=$orderId");
					$db->query("update order$fenbiao set status=-1,remark='管理员取消订单' where id=$orderId");
					$db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
					$details = $db->get_results("select inventoryId,num,productId from order_detail$fenbiao where orderId=$orderId");
					foreach ($details as $detail){
						$db->query("update demo_kucun set yugouNum=yugouNum-".$detail->num." where inventoryId=$detail->inventoryId and storeId=".$order->storeId." limit 1");
						$db->query("update demo_product_inventory set orders=orders-$detail->num where id=$detail->inventoryId");
						$db->query("update demo_product set orders=orders-$detail->num where id=$detail->productId");
					}
					if($order->price_payed>0){
						//订单退款
						/*switch ($order->pay_type){
							case 1:*/
								$db->query("update users set money=money+$order->price_payed where id=$order->userId");
								$liushui = array();
								$liushui['userId']=$order->userId;
								$liushui['comId']=$comId;
								$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
								$liushui['money']=$order->price_payed;
								$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
								$liushui['type']=2;
								$liushui['dtTime']=date("Y-m-d H:i:s");
								$liushui['remark']='订单设为无效';
								$liushui['orderInfo']='管理员操作订单取消，订单号：'.$order->orderId;
								$liushui['order_id']=$order->id;
								$yzFenbiao = getFenbiao($comId,20);
								$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
						//break;
							/*case 2:
								//微信退款
								$price = round($order->price_payed*100);
								$pay_json = json_decode($order->pay_json,true);
								require_once 'inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php';
								require_once 'inc/pay/WxpayAPI_php_v3/example/log.php';
								$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
								$log = Log::Init($logHandler, 15);
								$transaction_id = $pay_json['weixin']['wx_orderId'][0];
								$total_fee = $order->price*100;
								$refund_fee = $price;
								$input = new WxPayRefund();
								$input->SetTransaction_id($transaction_id);
								$input->SetTotal_fee($total_fee);
								$input->SetRefund_fee($refund_fee);
								$input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
								$input->SetOp_user_id(WxPayConfig::MCHID);
								$result = WxPayApi::refund($input);
								if($result['result_code'] != "SUCCESS"){
									file_put_contents("tuikuan_err.logs",json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
								}
							break;
							case 3://支付宝退款到余额
								$db->query("update users set money=money+$order->price_payed where id=$order->userId");
								$liushui = array();
								$liushui['userId']=$order->userId;
								$liushui['comId']=$comId;
								$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
								$liushui['money']=$order->price_payed;
								$liushui['yue']=$db->get_var("select money from users where id=$order->userId");
								$liushui['type']=2;
								$liushui['dtTime']=date("Y-m-d H:i:s");
								$liushui['remark']='订单设为无效';
								$liushui['orderInfo']='管理员操作订单取消，订单号：'.$order->orderId;
								$liushui['order_id']=$order->id;
								$yzFenbiao = getFenbiao($comId,20);
								$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
							break;
						}*/
					}
				}
			}
		}
	}
	die('{"code":1,"message":"操作成功"}');

}
//预售
function yushou(){}