<?php
function index(){}
function detail(){}
function daochu(){}
function chuku(){}
function printMiandan(){} 
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = $request['status'];
	$keyword = $request['keyword'];
	$orderId = $request['orderId'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$kehuName = $request['kehuName'];
	$level = (int)$request['level'];
	$shouhuoInfo = $request['shouhuoInfo'];
	$departId = (int)$request['departId'];
	$pdtInfo = $request['pdtInfo'];
	$payStatus = $request['payStatus'];
	$tags = $request['tags'];
	$orderType = (int)$request['orderType'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('dinghuoPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	if($order1=='kehuName')$order1 = 'CONVERT(kehuName USING gbk)';
	$sql = "select * from demo_dinghuo_order where comId=$comId ";
	if(!empty($status)){
		$status = str_replace('-2','0',$status);
		if($status==1){
			$sql.=" and status>0 and price_weikuan>0";
		}else{
			$sql.=" and status in($status)";
		}
	}
	if(!empty($keyword)){
		$sql.=" and (orderId ='$keyword' or kehuName='$keyword')";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	if(!empty($kehuName)){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and title like '%$kehuName%'");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	if(!empty($level)){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	if(!empty($shouhuoInfo)){
		$sql.=" and shouhuoInfo like '%$shouhuoInfo%'";
	}
	if(!empty($departId)){
		$sql.=" and departId=$departId";
	}
	if(!empty($pdtInfo)){
		$jiluIds = $db->get_var("select group_concat(distinct(jiluId)) from demo_dinghuo_detail where comId=$comId and pdtInfo like '%$pdtInfo%'");
		if(empty($jiluIds))$jiluIds='0';
		$sql.=" and id in($jiluIds)";
	}
	if(!empty($payStatus)){
		$sql.=" and payStatus in($payStatus)";
	}
	if(!empty($tags)){
		if($tags==2)$tags=0;
		$sql.=" and orderTag=$tags";
	}
	if(!empty($orderType)){
		$sql.=" and orderType=$orderType";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	//if(empty($kczt))$count=$count*count($cangkus);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$k = $db->get_row("select title,sn from demo_kehu where id=$j->kehuId");
			$j->kehuName = $k->title;
			$j->kehuSn = $k->sn;
			$chuku = '';
			$fahuo = '';
			$wuliu = '';
			switch ($j->chukuStatus) {
				case 0:
					$chuku = '备货中';
				break;
				case 1:
					$chuku = '部分出库';
				break;
				case 2:
					$chuku = '已出库';
				break;
			}
			switch ($j->fahuoStatus) {
				case 0:
					$fahuo = '待发货';
				break;
				case 1:
					$fahuo = '部分发货';
				break;
				case 2:
					$fahuo = '已发货';
				break;
			}
			if($j->fahuoStatus>0){
				$fahuoInfo = $db->get_row("select kuaidi_company,kuaidi_order from demo_dinghuo_fahuo where dinghuoId=$j->id order by id desc limit 1");
				if(!empty($fahuoInfo)){
					$wuliu = '<span onclick="fahuoInfo('.$j->id.');" style="cursor:pointer;"><img src="images/biao_107.png"><b style="color:#35a5dc; font-weight:normal;">物流信息</b></span>';
				}
			}
			$j->chuku_fahuo = $chuku.'/'.$fahuo;
			if(!empty($wuliu)){
				$j->chuku_fahuo.='<br>'.$wuliu;
			}
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">订单待审核</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待财务审核</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">待出库</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">待出库审核</span>';
				break;
				case 4:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 5:
					$status = '<span style="color:#ff3333;">待收货</span>';
				break;
				case 6:
					$status = '<span style="color:green;">已完成</span>';
				break;
				case -1:
					$status = '<span>已驳回</span>';
					$j->layclass ='deleted';
				break;
			}

			$j->status = $status;
			switch ($j->payStatus) {
				case 0:
					$j->payStatus = '未付款';
				break;
				case 1:
					$j->payStatus = '未付款';
				break;
				case 2:
					$j->payStatus = '付款待审核';
				break;
				case 3:
					$j->payStatus = '部分付款';
				break;
				case 4:
					$j->payStatus = '已付款';
				break;
			}
			$j->beizhu = '';
			if(!empty($j->beizhu)){
				$beizhus = json_decode($j->beizhu,true);
				$j->beizhu = $beizhu[0]['content'];
				$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->beizhu);
				$j->beizhu = str_replace('"','',$j->beizhu);
				$j->beizhu = str_replace("'",'',$j->beizhu);
				$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			}

			$fapiaoType = json_decode($j->fapiaoInfo);
			switch ($fapiaoType->type){
				case 0:
					$j->fapiaoType = '不开发票';
				break;
				case 1:
					$j->fapiaoType = '普通发票';
				break;
				case 2:
					$j->fapiaoType = '增值税发票';
				break;
			}
			$j->jiaohuoTime = ($j->jiaohuoTime=='0000-00-00')?'':date("Y-m-d",strtotime($j->jiaohuoTime));
			$shouhuoInfo = json_decode($j->shouhuoInfo);
			$j->shouhuo_user = $shouhuoInfo->name;
			$j->shouhuo_phone = $shouhuoInfo->phone;
			$j->shouhuo_address = $shouhuoInfo->address;
			if($j->orderType==2){
				$j->orderId = $j->orderId.'<div class="table-tag"><div class="sub-tag">代下单</div></div>';
			}
			$j->orderId = '<span onclick="view_jilu(\'dinghuo\','.$j->id.')" style="cursor:pointer;">'.$j->orderId.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function rowsSet(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$showRows = array();
	$showRows['orderId']=1;
	foreach ($request['rowsSet'] as $key=>$val){
		$showRows[$key]=1;
	}
	$showRowstr = json_encode($showRows);
	$db->query("update demo_kehu_shezhi set showRows='$showRowstr' where comId=$comId");
	redirect("?m=system&s=dinghuo");
}
function getJilus(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluOrder = $db->get_row("select username,kehuId,kehuName,orderType,dtTime from demo_dinghuo_order where id=$jiluId and comId=$comId limit 1");
	if(empty($jiluOrder)){
		die("订单不存在！");
	}
	$jilus = $db->get_results("select * from demo_dinghuo_jilu where jiluId=$jiluId order by id desc limit 100");
	$str = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$str.='<tr height="43">
	                	<td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->company.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->username.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                     	'.date("Y-m-d H:i",strtotime($jilu->dtTime)).'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->statusType.'
	                    </td>
	                    <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
	                    	'.$jilu->content.'
	                    </td>
	                </tr>';
		}
	}
	$str.='<tr height="43">
            	<td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	'.($jiluOrder->orderType==1?$jiluOrder->kehuName:$_SESSION[TB_PREFIX.'com_title']).'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	'.$jiluOrder->username.'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                 	'.date("Y-m-d H:i",strtotime($jiluOrder->dtTime)).'
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	创建订货单
                </td>
                <td bgcolor="#f2fbff" class="dhd_dingdanxiangqing_5_down_tt" align="left" valign="middle">
                	已提交订货单，等待订货单审核
                </td>
            </tr>';
    echo $str;exit;
}
function editYunfei(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$price_wuliu = $request['price_wuliu'];
	$jiluOrder = $db->get_row("select orderId,status,price_wuliu,price,price_payed,price_weikuan,kehuId from demo_dinghuo_order where id=$jiluId and comId=$comId limit 1");
	if(empty($jiluOrder)){
		echo '{"code":0,"message":"修改失败，订单不存在！"}';
		exit;
	}
	if($price_wuliu<0){
		echo '{"code":0,"message":"修改失败，订单不能小于0！"}';
		exit;
	}
	if($jiluOrder->status>1){
		echo '{"code":0,"message":"修改失败，财务审核过的订单不允许修改运费！"}';
		exit;
	}
	if($jiluOrder->price_wuliu-$price_wuliu>$jiluOrder->price_weikuan){
		echo '{"code":0,"message":"修改失败，运费差大于待支付的尾款！"}';
		exit;
	}
	$price = $jiluOrder->price-$jiluOrder->price_wuliu+$price_wuliu;
	$price_weikuan = $price-$jiluOrder->price_payed;
	$payStatus=1;
	if($jiluOrder->price_payed>0){
		$payStatus=3;
	}
	if($price_weikuan==0)$payStatus=4;
	$db->query("update demo_dinghuo_order set price_wuliu='$price_wuliu',price='$price',price_weikuan='$price_weikuan',payStatus=$payStatus where id=$jiluId");
	echo '{"code":1,"price_all":"'.$price.'","price_wuliu":"'.$price_wuliu.'"}';
	addJilu($jiluId,'修改运费','运费由'.$jiluOrder->price_wuliu.'调整为'.$price_wuliu);
	$kehuId = $jiluOrder->kehuId;
	$content = '订货单：'.$jiluOrder->orderId.'运费由￥'.$jiluOrder->price_wuliu.'调整为￥'.$price_wuliu;
	add_dinghuo_msg($kehuId,$content,1,$jiluId);
	exit;
}
function editJiaohuoTime(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update demo_dinghuo_order set jiaohuoTime='".$request['jiaohuoTime']."' where id=$jiluId and comId=$comId");
	$jiluOrder = $db->get_row("select orderId,kehuId from demo_dinghuo_order where id=$jiluId");
	$kehuId = $jiluOrder->kehuId;
	$content = '订货单：'.$jiluOrder->orderId.'交货日期调整为'.$request['jiaohuoTime'];
	add_dinghuo_msg($kehuId,$content,1,$jiluId);
	echo '{"code":1,"message":"成功"}';exit;
}
function addBeizhu(){
	global $db,$request;
	$jiluId = (int)$request['jiluId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$jiluOrder = $db->get_row("select id,beizhu from demo_dinghuo_order where id=$jiluId and comId=$comId limit 1");
	if(!empty($jiluOrder)){
		$results = array();
		if(!empty($jiluOrder->beizhu)){
			$results = json_decode($jiluOrder->beizhu,true);
		}
		$fankui = array();
		$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['cont']);
		$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
		$fankui['time'] = date('Y-m-d H:i:s');
		if($_SESSION['kehuId']>0){
			$fankui['company'] = $_SESSION[TB_PREFIX.'name'];
		}else{
			$fankui['company'] = $_SESSION[TB_PREFIX.'com_title'];
		}
		array_unshift($results,$fankui);
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update demo_dinghuo_order set beizhu='$resultstr' where id=$jiluId");
		$fankui['content'] = str_replace('\n','<br>',$fankui['content']);
		$fankui['content'] = str_replace('"','',$fankui['content']);
		$fankui['content'] = str_replace("'",'',$fankui['content']);
		echo '{"code":1,"message":"<div style=\"padding-bottom:10px;\">'.$fankui['content'].'【'.$fankui['name'].'&nbsp;/&nbsp;'.$fankui['company'].'&nbsp;&nbsp;'.$fankui['time'].'】</div>"}';
	}else{
		echo '{"code":0,"message":"记录不存在"}';
	}
	exit;
}
function shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$cont = $request['cont'];
	$status = (int)$request['status'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu = $db->get_row("select id,status,userId,kehuId,price_payed from demo_dinghuo_order where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"任务不存在"}';
		exit;
	}
	$liucheng = getLiucheng();
	if($jilu->status!=0&&$jilu->status!=1&&$jilu->status!=3){
		echo '{"code":0,"message":"该任务不需要审核！"}';
		exit;
	}
	if($status==1){
		switch ($jilu->status){
			case 0:
				$status = 2;
				if($liucheng['if_caiwu']==1){
					$status = 1;
				}else if($liucheng['if_chuku']==0&&$liucheng['if_fahuo']==0){
					if($liucheng['if_shouhuo']==0){
						$status = 6;
					}else{
						$status = 5;
					}
				}else{
					if($liucheng['if_chuku']==0){
						addTaskMsg(14,$jiluId,'有新的订货单需要您进行发货操作，请及时处理！');
					}else{
						addTaskMsg(13,$jiluId,'有新的订货单需要您进行出库操作，请及时处理！');
					}
				}
				addTaskMsg(12,$jiluId,'有新的订货单需要您进行收款操作，请及时处理！');
				$statusType='订货单订单审核';
			break;
			case 1:
				$status = 2;$statusType='订货单财务审核';
			break;
			case 3:
				$status = 4;$statusType='订货单出库审核';
			break;
		}
		$content = str_replace('订货单','订货单已通过',$statusType);
		if(!empty($cont)){
			$content.='，说明：'.$cont;
		}
	}else{
		switch ($jilu->status){
			case 0:
			break;
			case 1:
			break;
			case 3:
			break;
		}
		$statusType='订货单审核不通过';$content=$statusType.',原因：'.$cont;
		//已付款的退款，未审核的转为无效
		if($jilu->price_payed>0){
			$db->query("update demo_dinghuo_money set status=-1 where jiluId=$jiluId and type=0 and status=0");
			$moneyJilus = $db->get_results("select sum(money) as money,pay_type from demo_dinghuo_money where jiluId=$jiluId and type=0 and status=1 group by pay_type");
			if(!empty($moneyJilus)){
				$xianjin = 0;
				foreach ($moneyJilus as $moneyJilu){
					$moneyType = getPayType($moneyJilu->pay_type);
					if($moneyJilu->pay_type>4||$moneyJilu->pay_type==1){
						$xianjin+=$moneyJilu->money;
					}else{
						addMoneyJilu($jiluId,1,$moneyJilu->money,0,$moneyJilu->pay_type,'订单退款',$moneyType,'退款充值');
					}
				}
				if($xianjin>0){
					$mt = getPayType(1);
					addMoneyJilu($jiluId,1,$xianjin,0,1,'订单退款',$mt,'退款充值');
				}
			}
			
		}
	}
	$db->query("update demo_dinghuo_order set status=$status where id=$jiluId");
	$db->query("update demo_dinghuo_detail$fenbiao set status=".$request['status']." where jiluId=$jiluId");
	addJilu($jiluId,$statusType,$content);
	add_dinghuo_msg($jilu->kehuId,$content,1,$jiluId);
	if($status==-1){
		send_message($jilu->userId,2,'您的采购单被驳回，请及时查看','您的采购单被驳回，请及时查看');
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function piliang_shenhe(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilus = $db->get_results("select id,userId,kehuId from demo_dinghuo_order where id in($ids) and comId=$comId and status=0");
	if(empty($jilus)){
		echo '{"code":0,"message":"没有待订单审核的订货单。"}';
		exit;
	}
	$status = 2;
	$liucheng = getLiucheng();
	if($liucheng['if_caiwu']==1){
		$status = 1;
	}else if($liucheng['if_chuku']==0&&$liucheng['if_fahuo']==0){
		if($liucheng['if_shouhuo']==0){
			$status = 6;
		}else{
			$status = 5;
		}
	}
	$statusType='订货单订单审核';
	$content = str_replace('订货单','订货单已通过',$statusType);
	foreach ($jilus as $jilu) {
		$db->query("update demo_dinghuo_order set status=$status where id=$jilu->id");
		$db->query("update demo_dinghuo_detail$fenbiao set status=1 where jiluId=$jilu->id");
		addJilu($jilu->id,$statusType,$content);
		add_dinghuo_msg($jilu->kehuId,$content,1,$jilu->id);
		addTaskMsg(12,$jilu->id,'有新的订货单需要您进行收款操作，请及时处理！');
		if($status==2){
			addTaskMsg(13,$jilu->id,'有新的订货单需要您进行出库\发货操作，请及时处理！');
		}
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function piliang_caiwu(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilus = $db->get_results("select id,userId,kehuId from demo_dinghuo_order where id in($ids) and comId=$comId and status=1");
	if(empty($jilus)){
		echo '{"code":0,"message":"没有待财务审核的订货单。"}';
		exit;
	}
	$status = 2;
	$statusType='订货单财务审核';
	$content = str_replace('订货单','订货单已通过',$statusType);
	foreach ($jilus as $jilu) {
		$db->query("update demo_dinghuo_order set status=$status where id=$jilu->id");
		$db->query("update demo_dinghuo_detail$fenbiao set status=$status where jiluId=$jilu->id");
		addJilu($jilu->id,$statusType,$content);
		add_dinghuo_msg($jilu->kehuId,$content,1,$jilu->id);
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
//插入订单操作记录
function addJilu($jiluId,$statusType,$content){
	$jilu = array();
	$jilu['jiluId'] = $jiluId;
	$jilu['username'] = $_SESSION[TB_PREFIX.'name'];
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['statusType'] = $statusType;
	$jilu['content'] = $content;
	$jilu['company'] = $_SESSION[TB_PREFIX.'com_title'];
	insert_update('demo_dinghuo_jilu',$jilu,'id');
}
//插入退款记录
function addMoneyJilu($jiluId,$type,$money,$status,$pay_type,$pay_info,$shoukuan_info,$beizhu){
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$order = $db->get_row("select kehuId,kehuName from demo_dinghuo_order where id=$jiluId");
	$jilu = array();
	$jilu['comId'] = $comId;
	$jilu['kehuId'] = $order->kehuId;
	$jilu['kehuName'] = $order->kehuName;
	$jilu['jiluId'] = $jiluId;
	$jilu['type'] = $type;
	$jilu['orderId'] = date("YmdHis").rand(100000,999999);
	$jilu['money'] = $money;
	$jilu['dtTime'] = date("Y-m-d H:i:s");
	$jilu['status'] = $status;
	$jilu['pay_type'] = $pay_type;
	$jilu['pay_info'] = $pay_info;
	$jilu['shoukuan_info'] =$shoukuan_info;
	$jilu['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$jilu['userName'] = $_SESSION[TB_PREFIX.'name'];
	$jilu['beizhu']=$beizhu;
	$jilu['statusType'] = $statusType;
	$jilu['content'] = $content;
	$jilu['company'] = $_SESSION[TB_PREFIX.'com_title'];
	insert_update('demo_dinghuo_money',$jilu,'id');
}
function create(){
	global $db,$request;
	if($request['tijiao']==1){
		$liucheng = getLiucheng();
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$fenbiao = getFenbiao($comId,20);
		$kehuId = (int)$request['kehuId'];
		$k = $db->get_row("select title,departId,storeId,areaId from demo_kehu where id=$kehuId and comId=$comId");
		$kehuName = $k->title;
		$departId = $k->departId;
		$orderId = 'DH-O_'.date("Ymd").'_'.rand(100000,999999);
		$iftejia = empty($request['ifxieshang'])?0:1;
		$price_wuliu = $request['price_wuliu'];
		$price = $request['price'];
		if($iftejia==1){
			$price = $request['xieshangMoney']+$price_wuliu;
		}
		$price_weikuan = $price;
		$weight = $request['weight'];
		$beizhu = '';
		if(!empty($request['beizhu'])){
			$results = array();
			$fankui = array();
			$fankui['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','\n',$request['beizhu']);
			$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
			$fankui['time'] = date('Y-m-d H:i:s');
			$fankui['company'] = $_SESSION[TB_PREFIX.'com_title'];
			$results[]=$fankui;
			$beizhu = json_encode($results,JSON_UNESCAPED_UNICODE);
		}
		$jiaohuoTime = $request['jiaohuoTime'];
		$dtTime = date("Y-m-d H:i:s");
		$shouhuoInfo = $request['shouhuoInfo'];
		$fapiao = $request['fapiaoInfo'];
		$fapiaoArry = explode(',',$fapiao);
		$fapiaoInfoArry = array();
		foreach ($fapiaoArry as $f){
			$a = explode('|',$f);
			$fapiaoInfoArry[$a[0]] = $a[1];
		}
		$fapiaoInfo = json_encode($fapiaoInfoArry,JSON_UNESCAPED_UNICODE);
		$fujianInfo = $request['fujianInfo'];
		$orderType = 2;
		$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$username = $_SESSION[TB_PREFIX.'name'];
		if(!empty($request['inventoryNum'])){
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
			$dinghuo['weight'] = $weight;
			$dinghuo['jiaohuoTime'] = $jiaohuoTime;
			$dinghuo['dtTime'] = $dtTime;
			$dinghuo['shouhuoInfo'] =$shouhuoInfo;
			$dinghuo['fapiaoInfo'] =$fapiaoInfo;
			$dinghuo['fujianInfo'] = $fujianInfo;
			$dinghuo['orderType'] = $orderType;
			$dinghuo['userId'] = $userId;
			$dinghuo['username'] = $username;
			$dinghuo['beizhu'] = $beizhu;
			$dinghuo['storeId'] = $k->storeId;
			insert_update('demo_dinghuo_order',$dinghuo,'id');
			$jiluId = $db->get_var("select last_insert_id();");
			$rukuSql = "insert into demo_dinghuo_detail$fenbiao(comId,kehuId,kehuName,jiluId,inventoryId,productId,pdtInfo,num,hasNum,tuihuoNum,status,price,unit_price,units,dtTime,weight,beizhu,dinghuoUnit,UnitNum,dinghuoNum) values";
			$rukuSql1 = '';
			foreach ($request['inventoryNum'] as $key => $num){
				$inventoryId = $request['inventoryId'][$key];
				$pdtInfoArry = array();
				$pdtInfoArry['sn'] = $request['inventorySn'][$key];
				$pdtInfoArry['title'] = $request['inventoryTitle'][$key];
				$pdtInfoArry['key_vals'] = $request['inventoryKey_vals'][$key];
				$weight = $request['inventoryWeight'][$key];
				$productId = $request['inventoryPdtId'][$key];
				$units = $request['inventoryUnits'][$key];
				$unit_price = str_replace(',','',$request['inventoryPrice'][$key]);
				$dinghuoUnit = $units;
				$UnitNum = 1;
				$dinghuoNum = $num;
				if(!empty($request['inventoryUnit'][$key])){
					$us = explode('|',$request['inventoryUnit'][$key]);
					$dinghuoUnit = $us[0];
					$UnitNum = $us[1];
					$num = $num*$UnitNum;
				}
				$price = $unit_price*$num;
				$beizhu = $request['inventoryBeizhu'][$key];
				$pdtInfo = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
				$rukuSql1.=",($comId,$kehuId,'$kehuName',$jiluId,$inventoryId,$productId,'$pdtInfo','$num',0,0,0,'$price','$unit_price','$units','$dtTime','$weight','$beizhu','$dinghuoUnit','$UnitNum','$dinghuoNum')";
				if(($liucheng['if_chuku']+$liucheng['if_fahuo'])>0){
					$db->query("update demo_kucun set yugouNum=yugouNum+$num where inventoryId=$inventoryId and storeId=$k->storeId limit 1");
				}
			}
			$rukuSql1 = substr($rukuSql1,1);
			$db->query($rukuSql.$rukuSql1);
			$content = '已为您代下订货单，单号：'.$orderId;
			add_dinghuo_msg($kehuId,$content,1,$jiluId);
			addTaskMsg(11,$jiluId,'有新的订货单需要您审核，请及时处理！');
			redirect("?m=system&s=dinghuo");
		}
	}
}
//获取搜索产品列表
function getPdtList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$id = (int)$request['id'];
	$keyword = $request['keyword'];
	$hasIds = $request['hasIds'];
	$kehuId = (int)$request['kehuId'];
	$hasArry = explode(',',$hasIds);
	$sql = "select id,sn,title,key_vals,productId,weight from demo_product_inventory where comId=$comId";
	if(!empty($keyword)){
		$sql.=" and (sn like '%$keyword%' or title like '%$keyword%')";
	}
	$sql.=" limit 10";
	$pdts = $db->get_results($sql);
	$str = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt){
			$zifu = $pdt->sn.' '.$pdt->title;
			if(!empty($pdt->key_vals)&&$pdt->key_vals!='无'){
				$zifu.=' 【'.$pdt->key_vals.'】';
			}
			$zifu = sys_substr($zifu,40,true);
			$product=$db->get_row("select unit_type,untis,dinghuo_units,brandId from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			if(in_array($pdt->id,$hasArry)){
				$str.='<li><a style="color:#aaa">'.$zifu.'</a></li>';
			}else{
				$price = getKehuPriceArry($pdt->id,$kehuId);
				if($price['price']==0){
					$str.='<li><a style="color:#aaa">'.$zifu.'</a></li>';
				}else{
					if($kucun_set->dinghuo_store==1){
						$storeId = $db->get_var("select storeId from demo_kehu where id=$kehuId and comId=$comId");
						$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$pdt->id and storeId=$storeId limit 1");
						$kucun = $kc->kucun-$kc->yugouNum;
					}else{
						$kc = $db->get_row("select sum(kucun) as kucun,sum(yugouNum) as yugouNum from demo_kucun where inventoryId=$pdt->id");
						$kucun = $kc->kucun-$kc->yugouNum;
					}
					$dinghuo_units = explode(',',$product->dinghuo_units);
					$duoUnits = '';
					foreach ($units as $u){
						if(in_array($u['title'],$dinghuo_units)){
							$duoUnits.=','.$u['title'].'|'.$u['num'];
						}
					}
					$duoUnits = substr($duoUnits,1);
					$price1 = getXiaoshu($price['price'],$product_set->price_num);
					$str.='<li onclick="selectRow('.$id.','.$pdt->id.',\''.$pdt->sn.'\',\''.$pdt->title.'\',\''.$pdt->key_vals.'\','.$pdt->productId.',\''.$unitstr.'\',\''.$kucun.'\',\''.$price1.'\',\''.$pdt->weight.'\',\''.$price['min'].'\',\''.$price['max'].'\',\''.$duoUnits.'\')"><a href="javascript:">'.$zifu.'</a></li>';
				}
			}
		}
	}else{
		$str='<li style="padding:20px;text-align:center;">未找到产品</li>';
	}
	echo $str;
	exit;
}
//根据客户id，产品id获取订货价格
function getKehuPrice($inventoryId,$kehuId){
	global $db;
	$dinghuo = $db->get_row("select ifsale,price_sale from demo_product_dinghuo where inventoryId=$inventoryId and kehuId=$kehuId limit 1");
	if(!empty($dinghuo)){
		if($dinghuo->ifsale==1){
			return $dinghuo->price_sale;
		}else{
			return 0;
		}
	}else{
		$level = $db->get_var("select level from demo_kehu where id=$kehuId");
		$dinghuo = $db->get_row("select ifsale,price_sale from demo_product_dinghuo where inventoryId=$inventoryId and levelId=$level limit 1");
		if(!empty($dinghuo)){
			if($dinghuo->ifsale==1){
				return $dinghuo->price_sale;
			}else{
				return 0;
			}
		}else{
			$pdtPrice = $db->get_var("select price_sale from demo_product_inventory where id=$inventoryId");
			$zhekou = $db->get_var("select zhekou from demo_kehu_level where id=$level");
			return $pdtPrice*100/$zhekou;
		}
	}
}
//获取客户的收货信息、发票信息
function getKehuInfo(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fapiao = $db->get_row("select id,caiwu from demo_kehu where id=$id and comId=$comId");
	$keyword = $request['keyword'];
	if(!empty($fapiao)){
		$sql = "select * from demo_kehu_address where kehuId=$id ";
		if(!empty($keyword)){
			$sql.=" and (name='$keyword' or phone='$keyword')";
		}
		$sql.= " order by moren desc,id desc limit 20";
		$shouhuos = $db->get_results($sql);
		$arry = array();
		$arry['code'] = 1;
		$arry['message'] = '成功';
		$arry['shouhuos'] = $shouhuos;
		$arry['fapiao']=json_decode($fapiao->caiwu,true);
		echo json_encode($arry,JSON_UNESCAPED_UNICODE);
	}else{
		echo '{"code":0,"message":"客户不存在！"}';
	}
	exit;
}
function delAddress(){
	global $db,$request;
	$id = (int)$request['id'];
	$kehuId = (int)$request['kehuId'];
	$db->query("delete from demo_kehu_address where id=$id and kehuId=$kehuId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function setMorenAddress(){
	global $db,$request;
	$id = (int)$request['id'];
	$kehuId = (int)$request['kehuId'];
	$db->query("update demo_kehu_address set moren=0 where kehuId=$kehuId and moren=1 limit 1");
	$db->query("update demo_kehu_address set moren=1 where id=$id and kehuId=$kehuId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function getAreaInfo(){
	global $db,$request;
	$areaId = (int)$request['id'];
	$firstId=0;
	$secondId=0;
	$thirdId=0;
	$areas = $db->get_results("select * from demo_area where parentId=0");
	if($areaId>0){
		$area = $db->get_row("select * from demo_area where id=".$areaId);
		if($area->parentId==0){
			$firstId = $area->id;
		}else{
			$firstId = $area->parentId;
			$secondId = $area->id;
			$farea = $db->get_row("select * from demo_area where id=".$area->parentId);
			if($farea->parentId!=0){
				$firstId = $farea->parentId;
				$secondId = $farea->id;
				$thirdId=$area->id;
			}
		}
	}
	if($secondId>0){
		$areas2 = $db->get_results("select * from demo_area where parentId=$secondId");
	}
	if($firstId>0){
		$areas1 = $db->get_results("select * from demo_area where parentId=$firstId");
	}
	$areastr = '';$areastr1 = '<option value=\"\">请选择市</option>';$areastr2 = '<option value=\"\">请选择区</option>';
	foreach ($areas as $a){
		$areastr.='<option value=\"'.$a->id.'\" '.($a->id==$firstId?'selected=\"true\"':'').'>'.$a->title.'</option>';
	}
	if(!empty($areas1)){
		foreach ($areas1 as $a){
			$areastr1.='<option value=\"'.$a->id.'\" '.($a->id==$secondId?'selected=\"true\"':'').'>'.$a->title.'</option>';
		}
	}
	if(!empty($areas2)){
		foreach ($areas2 as $a){
			$areastr2.='<option value=\"'.$a->id.'\" '.($a->id==$thirdId?'selected=\"true\"':'').'>'.$a->title.'</option>';
		}
	}
	echo '{"code":1,"message":"成功","areas1":"'.$areastr.'","areas2":"'.$areastr1.'","areas3":"'.$areastr2.'"}';
	exit;
}
function updateAddress(){
	global $db,$request;
	$kehu_address = array();
	$kehu_address['id'] = (int)$request['id'];
	$kehu_address['kehuId'] = (int)$request['kehuId'];
	$kehu_address['name'] = $request['name'];
	$kehu_address['phone'] = $request['phone'];
	$kehu_address['areaId'] = $request['areaId'];
	$kehu_address['areaName'] = getAreaName($request['areaId']);
	$kehu_address['title'] = $request['title'];
	$kehu_address['address'] = $request['address'];
	if(empty($kehu_address['id'])){
		$kehu_address['moren'] = 0;
	}
	insert_update('demo_kehu_address',$kehu_address,'id');
	echo '{"code":1,"message":"ok"}';
	exit;
}
function updateKehuFapiao(){
	global $db,$request;
	$kehuId = (int)$request['kehuId'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$caiwu = $db->get_var("select caiwu from demo_kehu where id=$kehuId and comId=$comId");
	if(empty($caiwu))die('{"code":0,"message":"客户不存在！"}');
	$caiwuArr = json_decode($caiwu,true);
	$caiwuArr['taitou'] = $request['taitou'];
	$caiwuArr['shibie'] = $request['shibie'];
	if(!empty($request['address']))$caiwuArr['address']=$request['address'];
	if(!empty($request['phone']))$caiwuArr['phone']=$request['phone'];
	if(!empty($request['kaihuming']))$caiwuArr['kaihuming']=$request['kaihuming'];
	if(!empty($request['kaihuhang']))$caiwuArr['kaihuhang']=$request['kaihuhang'];
	if(!empty($request['kaihubank']))$caiwuArr['kaihubank']=$request['kaihubank'];
	$caiwu = json_encode($caiwuArr,JSON_UNESCAPED_UNICODE);
	$db->query("update demo_kehu set caiwu='$caiwu' where id=$kehuId and comId=$comId");
	$returnArr = array();
	$returnArr['code']=1;
	$returnArr['message']='ok';
	$returnArr['fapiao']=$caiwuArr;
	echo json_encode($returnArr,JSON_UNESCAPED_UNICODE);
	exit;
}
function getPdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$kehuId = (int)$request['kehuId'];
	if(empty($kehuId)){
		echo '{"code":0,"msg":"","count":0,"data":[]}';
		exit;
	}
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
	$sql="select id,sn,title,key_vals,productId,weight from demo_product_inventory where comId=$comId and id not in($hasIds)";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn='$keyword' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId,weight','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId,dinghuo_units from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			$price = getKehuPriceArry($pdt->id,$kehuId);
			if($price['price']>0){
				if($kucun_set->dinghuo_store==1){
					$storeId = $db->get_var("select storeId from demo_kehu where id=$kehuId and comId=$comId");
					$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$pdt->id and storeId=$storeId limit 1");
					$kucun = $kc->kucun-$kc->yugouNum;
				}else{
					$kc = $db->get_row("select sum(kucun) as kucun,sum(yugouNum) as yugouNum from demo_kucun where inventoryId=$pdt->id");
					$kucun = $kc->kucun-$kc->yugouNum;
				}
				$tipstr = '';
				if($kucun_set->kucun_type==2){
					if($kucun>0){
						$tipstr = '库存：有<br>';
					}else{
						$tipstr = '库存：无<br>';
					}
				}else if($kucun_set->kucun_type==3){
					$tipstr = '库存：'.$kucun.'<br>';
				}
				if($price['min']>0){
					$tipstr = $tipstr.'起订量：'.$price['min'].$g->unit_unit.'<br>';
				}
				if($price['max']>0){
					$tipstr = $tipstr.'限购量：'.$price['max'].$g->unit_unit;
				}
				$dinghuo_units = explode(',',$product->dinghuo_units);
				$duoUnits = '';
				$unitstrs = '<div style=\"width:80%;margin:auto;display:inline-block;\"><select id=\"add_unit_'.$pdt->id.'\">';
				foreach ($units as $u){
					if(in_array($u['title'],$dinghuo_units)){
						$duoUnits.=','.$u['title'].'|'.$u['num'];
						$unitstrs .= '<option value=\"'.$u['title'].'\" data-num=\"'.$u['num'].'\">'.$u['title'].'('.$u['num'].$unitstr.')'.'</option>';
					}
				}
				$unitstrs.= '</select></div>';
				$duoUnits = substr($duoUnits,1);
				$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","shuliang":"<input type=\"text\" data-min=\"'.$price['min'].'\" onchange=\"checkKucun1(this);\" onmouseover=\"tips(this,\''.$tipstr.'\',1)\" onmouseout=\"hideTips();\" data-max=\"'.$price['max'].'\" data-kucun=\"'.$kucun.'\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" id=\"shuliang_'.$pdt->id.'\">","price":"'.$price['price'].'","units":"'.$unitstr.'","weight":"'.$pdt->weight.'","unit_select":"'.$unitstrs.'","duoUnits":"'.$duoUnits.'"}';
			}
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function getChukuPdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$storeId = (int)$request['storeId'];
	$kehuStore = (int)$request['kehuStore'];
	$ifkucun = empty($request['ifkucun'])?0:1;
	if(empty($jiluId)||empty($storeId)){
		echo '{"code":0,"msg":"","count":0,"data":[]}';
		exit;
	}
	$sql="select id,inventoryId,pdtInfo,num,hasNum,status,units,weight from demo_dinghuo_detail$fenbiao where comId=$comId and jiluId=$jiluId and num>hasNum";
	$count = $db->get_var(str_replace('id,inventoryId,pdtInfo,num,hasNum,status,units,weight','count(*)',$sql));
	$sql.=" order by id desc";
	//file_put_contents('request.txt',$ifkucun);
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$chuku = $pdt->num-$pdt->hasNum;
			$max = $chuku;
			if($kucun_set->chuku_limit==1){
				$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
				$kucun = $kc->kucun-$kc->yugouNum;
				if($storeId==$kehuStore){
					$kucun +=$chuku;//如果所选仓库=客户默认仓库，可用库存量应该加上本次订购量
				}
				$max = $kucun>$chuku?$chuku:$kucun;
			}
			$shuliang = getXiaoshu($pdt->num,$product_set->number_num);
			$weight = getXiaoshu($pdt->weight*$pdt->num,2);
			$hasNum = getXiaoshu($pdt->hasNum,$product_set->number_num);
			$pdtInfo = json_decode($pdt->pdtInfo);
			$kucun = getXiaoshu($kucun,$product_set->number_num);
			if($kucun>0||$ifkucun==0){
				$pdtstr.=',{"sn":"'.$pdtInfo->sn.'","title":"'.$pdtInfo->title.'","key_vals":"'.$pdtInfo->key_vals.'","kucun":"'.$kucun.'","shuliang":"'.$shuliang.'","weight":"'.$weight.'","hasNum":"'.$hasNum.'","units":"'.$pdt->units.'","chuku":"<input type=\"text\" name=\"chuku['.$pdt->id.']\" value=\"'.$chuku.'\" max=\"'.$max.'\" onmouseover=\"tips(this,\'剩余库存：'.$kucun.'\',1);\" onblur=\"checkMax(this);\" onmouseout=\"hideTips()\" class=\"sprkadd_xuanzesp_02_tt_input'.($max<($pdt->num-$pdt->hasNum)?' borderRed':'').'\" id=\"shuliang_'.$pdt->id.'\">","price":"'.$price.'","units":"'.$pdt->units.'","weight":"'.$pdt->weight.'"}';
			}
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
	exit;
}
function addChuku(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/kucun_set_$comId.php")){
		$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
	}else{
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
	}
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$kehuStore = (int)$request['kehuStore'];
	$storeId = (int)$request['storeId'];
	$chukus = $request['chuku'];
	$return = array();
	$liucheng = getLiucheng();
	if(empty($jiluId)||empty($kehuStore)||empty($storeId)||empty($chukus)){
		$return['code'] = 0;
		$return['message'] = '缺少必要的参数，请刷新页面重试';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$chukuArry = array();
	foreach($chukus as $id=>$num){
		if($num>0){
			$detail = $db->get_row("select inventoryId,num,hasNum,pdtInfo,productId,units from demo_dinghuo_detail$fenbiao where id=$id and jiluId=$jiluId");
			if(empty($detail)){
				$return['code'] = 0;
				$return['message'] = '系统错误，请刷新重试';
				echo json_encode($return,JSON_UNESCAPED_UNICODE);
				exit;
			}
			if($num>($detail->num-$detail->hasNum)){
				$return['code'] = 0;
				$return['message'] = '操作失败！原因：本次出库数量大于可出库数量。';
				echo json_encode($return,JSON_UNESCAPED_UNICODE);
				exit;
			}
			$kucun = $db->get_row("select kucun,yugouNum,chengben from demo_kucun where inventoryId=$detail->inventoryId and storeId=$storeId limit 1");
			$kykucun = $kucun->kucun-$kucun->yugouNum;
			if($storeId==$kehuStore){
				$kykucun+=$detail->num-$detail->hasNum;
			}
			if($kucun_set->chuku_limit==1){
				if($num>$kykucun){//库存-可用库存、、如果出库仓库是默认出库
					$return['code'] = 0;
					$return['message'] = '操作失败！原因：本次出库数量大于库存剩余数量。';
					echo json_encode($return,JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
			$chuku = array();
			$chuku['id'] = $id;
			$chuku['inventoryId'] = $detail->inventoryId;
			$chuku['productId'] = $detail->productId;
			$chuku['pdtInfo'] = $detail->pdtInfo;
			$chuku['num'] = $num;
			$chuku['kucun'] = $kucun->kucun-$num;
			$chuku['chengben'] = $num*$kucun->chengben;
			$chuku['units'] = $detail->units;
			$chukuArry[] = $chuku;
		}
	}
	$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId and comId=$comId");
	$dtTime = date("Y-m-d H:i:s");
	$type = 2;
	$orderInt = getOrderId($comId,$type);
	$orderId = $kucun_set->chuku_pre.'_'.date("Ymd").'_'.$orderInt;
	$status = 1;
	$shenheUser = 0;
	$shenheName = '';
	$type_info = '销售出库';
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$dinghuoId = $jiluId;
	$db->query("insert into demo_kucun_jilu$fenbiao(comId,type,storeId,store1Id,orderId,orderInt,dtTime,type_info,status,userId,username,jingbanren,shenheUser,shenheName,beizhu,storeName,dinghuoId) value($comId,$type,$storeId,0,'$orderId',$orderInt,'$dtTime','$type_info',$status,$userId,'$username','',$shenheUser,'$shenheName','','$storeName',$dinghuoId)");
	$jiluId = $db->get_var("select last_insert_id();");
	if($liucheng['if_fahuo']==0){
		$order = $db->get_row("select kehuId,orderId from demo_dinghuo_order where id=".$dinghuoId);
		$dinghuo_fahuo['comId'] = $comId;
		$dinghuo_fahuo['kehuId'] = $order->kehuId;
		$dinghuo_fahuo['jiluId'] = $jiluId;
		$dinghuo_fahuo['dinghuoId'] = $dinghuoId;
		$dinghuo_fahuo['type'] = 1;
		$dinghuo_fahuo['fahuoTime'] = date("Y-m-d H:i:s");
		$dinghuo_fahuo['kuaidi_type'] = 1;
		$dinghuo_fahuo['kuaidi_company'] = '';
		$dinghuo_fahuo['kuaidi_order'] = '';
		$dinghuo_fahuo['beizhu'] = '';
		$dinghuo_fahuo['dtTime'] = date("Y-m-d H:i:s");
		$dinghuo_fahuo['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$dinghuo_fahuo['userName'] = $_SESSION[TB_PREFIX.'name'];
		insert_update('demo_dinghuo_fahuo',$dinghuo_fahuo,'id');
	}
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
		if($storeId==$kehuStore){
			$db->query("update demo_kucun set kucun=kucun-$num,yugouNum=yugouNum-$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
			$db->query("update demo_product_inventory set kucun=kucun-$num where id=$inventoryId");
		}else{
			$db->query("update demo_kucun set kucun=kucun-$num where inventoryId=$inventoryId and storeId=$storeId limit 1");
			$db->query("update demo_product_inventory set kucun=kucun-$num where id=$inventoryId");
			$db->query("update demo_kucun set yugouNum=yugouNum-$num where inventoryId=$inventoryId and storeId=$kehuStore limit 1");
		}
		$db->query("update demo_dinghuo_detail$fenbiao set hasNum=hasNum+$num where id=".$chuku['id']);
		$rukuSql1.=",($comId,$jiluId,$inventoryId,".$chuku['productId'].",'".$chuku['pdtInfo']."',$storeId,'$storeName','-$num',$status,'$kucun','',$type,'$type_info','$dtTime','".$chuku['units']."','$rukuChengben','$zongchengben',".$chuku['id'].")";
	}
	$rukuSql1 = substr($rukuSql1,1);
	$db->query($rukuSql.$rukuSql1);
	$status = 2;
	$chukuStatus = 1;
	$isall = $db->get_var("select id from demo_dinghuo_detail$fenbiao where jiluId=$dinghuoId and num>hasNum limit 1");
	if(empty($isall)){
		$chukuStatus = 2;
		$status = 4;
		if($liucheng['if_fahuo']==0){
			if($liucheng['if_shouhuo']==0){
				$status = 6;
			}else{
				$status = 5;
			}
		}else{
			addTaskMsg(14,$dinghuoId,'有新的订货单需要您进行发货操作，请及时处理！');
		}
	}
	$db->query("update demo_dinghuo_order set status=$status,chukuStatus=$chukuStatus where id=$dinghuoId");
	//写记录
	addJilu($dinghuoId,'订货单出库','订货单'.($chukuStatus==1?'部分':'已全部').'出库');
	$return['code'] = 1;
	$return['message'] = '成功';
	$return['jiluId'] = $jiluId;
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function print_chuku(){}
function daochuChuku(){}
function zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$dinghuoId = (int)$request['dinghuoId'];
	$jiluId = (int)$request['id'];
	$fenbiao = getFenBiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$shenheName = $_SESSION[TB_PREFIX.'name'];
	$cont = $request['beizhu'];
	$jilu = $db->get_row("select orderId,shenheUser,userId,storeId,status,dinghuoId from demo_kucun_jilu$fenbiao where id=$jiluId and comId=$comId");
	if(empty($jilu)){
		echo '{"code":0,"message":"记录不存在"}';
		exit;
	}
	if($jilu->status<1){
		echo '{"code":0,"message":"该记录不能进行作废操作！"}';
		exit;
	}
	$kehuStore = $db->get_var("select storeId from demo_dinghuo_order where id=$dinghuoId");
	$jiluDetails = $db->get_results("select id,inventoryId,storeId,num,type,dinghuoId,chengben from demo_kucun_jiludetail$fenbiao where jiluId=$jiluId");
	$dtTime = date("Y-m-d H:i:s");
	if(!empty($jiluDetails)){
		if($jilu->status==1){
			foreach ($jiluDetails as $j) {
				$lastJilu = $db->get_row("select kucun,zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$j->inventoryId and storeId=$j->storeId and status=1 and id!=$j->id order by id desc limit 1");
				if(empty($lastJilu)){
					$lastJilu->zongchengben = 0;
					$lastJilu->kucun = 0;
				}
				$j->num = abs($j->num);
				$zongchengben = $lastJilu->zongchengben+$j->chengben;
				$zongNum = $lastJilu->kucun+$j->num;
				$chengben = getXiaoshu($zongchengben/$zongNum,4);
				if($chengben<0)$chengben=0;
				$sql = "update demo_kucun set kucun=kucun+".$j->num.",chengben='".$chengben."'";
				if($j->storeId==$kehuStore){
					$sql.=",yugouNum=yugouNum+".$j->num;
				}else{
					$db->query("update demo_kucun set yugouNum=yugouNum+".$j->num." where inventoryId=$j->inventoryId and storeId=$kehuStore");
				}
				$sql.=" where inventoryId=".$j->inventoryId." and storeId=".$j->storeId." limit 1";
				$db->query($sql);
				$db->query("update demo_product_inventory set kucun=kucun+$j->num where id=$j->inventoryId");
				$db->query("update demo_kucun_jiludetail$fenbiao set status=-2,kucun='".$zongNum."',shenheTime='$dtTime' where id=".$j->id);
				$db->query("update demo_dinghuo_detail$fenbiao set hasNum=hasNum-".$j->num." where id=".$j->dinghuoId);
			}
		}
		$db->query("update demo_kucun_jilu$fenbiao set status=-2,shenheTime='$dtTime',shenheCont='$cont',shenheUser=$userId,shenheName='$shenheName' where id=$jiluId");
		$status = 2;
		$chukuStatus = 0;
		$isall = $db->get_var("select id from demo_dinghuo_detail$fenbiao where jiluId=$dinghuoId and hasNum>0 limit 1");
		if(empty($isall)){
			$chukuStatus = 1;
		}
		$db->query("update demo_dinghuo_order set status=$status,chukuStatus=$chukuStatus where id=$dinghuoId");
		addJilu($dinghuoId,'作废出库记录','作废出库记录:'.$jilu->orderId.',原因：'.$cont);
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function addFahuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenBiao($comId,20);
	$dinghuoId = (int)$request['dinghuoId'];
	$dinghuo_fahuo = array();
	if(empty($dinghuoId)||empty($request['jiluId'])){
		echo '{"code":0,"message":"系统错误，请刷新重试"}';
		exit;
	}
	$order = $db->get_row("select kehuId,orderId from demo_dinghuo_order where id=".(int)$request['dinghuoId']);
	$dinghuo_fahuo['id'] = (int)$request['fahuoId'];
	$dinghuo_fahuo['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$dinghuo_fahuo['kehuId'] = $order->kehuId;
	$dinghuo_fahuo['jiluId'] = (int)$request['jiluId'];
	$dinghuo_fahuo['dinghuoId'] = (int)$request['dinghuoId'];
	$dinghuo_fahuo['type'] = (int)$request['type'];
	$dinghuo_fahuo['fahuoTime'] = date("Y-m-d H:i:s",strtotime($request['fahuo_time']));
	$dinghuo_fahuo['kuaidi_type'] = 1;
	$dinghuo_fahuo['kuaidi_company'] = $request['fahuo_company'];
	$dinghuo_fahuo['kuaidi_order'] = $request['fahuo_order'];
	$dinghuo_fahuo['beizhu'] = $request['fahuo_beizhu'];
	$dinghuo_fahuo['dtTime'] = date("Y-m-d H:i:s");
	$dinghuo_fahuo['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_fahuo['userName'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_fahuo',$dinghuo_fahuo,'id');
	//更新订货单状态及发货状态
	$status = 2;
	$fahuoStatus = 1;
	$isall = $db->get_var("select id from demo_dinghuo_detail$fenbiao where jiluId=$dinghuoId and num>hasNum limit 1");
	if(empty($isall)){
		$status = 4;
		$chukuNums = $db->get_var("select count(*) from demo_kucun_jilu$fenbiao where dinghuoId=$dinghuoId and status=1");
		$fahuoNums = $db->get_var("select count(*) from demo_dinghuo_fahuo where dinghuoId=$dinghuoId");
		if($chukuNums==$fahuoNums){
			$fahuoStatus = 2;
			$status = 6;
			$liucheng = getLiucheng();
			if($liucheng['if_shouhuo']==1){
				$status = 5;
			}
			
		}
	}
	$db->query("update demo_dinghuo_order set status=$status,fahuoStatus=$fahuoStatus where id=$dinghuoId");
	addJilu($dinghuoId,'订货单发货','订货单'.($fahuoStatus==1?'部分':'已全部').'发货');
	add_dinghuo_msg($order->kehuId,'订货单'.$order->orderId.($fahuoStatus==1?'部分':'已全部').'发货',1,$dinghuoId);
	echo '{"code":1}';exit;
}
function fahuo_kuaidiniao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenBiao($comId,20);
	$dinghuoId = (int)$request['dinghuoId'];
	$expressno = explode(',',$request['expressno']);
	$expressDesc = $request['expressDesc'];
	$fahuoId = (int)$request['fahuoId'];
	$dinghuo_fahuo = array();
	if(empty($dinghuoId)||empty($request['jiluId'])){
		echo '{"code":0,"message":"系统错误，请刷新重试"}';
		exit;
	}
	$order = $db->get_row("select kehuId,orderId from demo_dinghuo_order where id=".$dinghuoId);
	$dinghuo_fahuo['id'] = (int)$request['fahuoId'];
	$dinghuo_fahuo['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$dinghuo_fahuo['kehuId'] = $order->kehuId;
	$dinghuo_fahuo['jiluId'] = (int)$request['jiluId'];
	$dinghuo_fahuo['dinghuoId'] = $dinghuoId;
	$dinghuo_fahuo['type'] = 2;
	$dinghuo_fahuo['fahuoTime'] = empty($request['fahuo_time'])?date("Y-m-d H:i:s"):date("Y-m-d H:i:s",strtotime($request['fahuo_time']));
	$dinghuo_fahuo['kuaidi_type'] = 2;
	$dinghuo_fahuo['kuaidi_company'] = $expressno[1];
	$dinghuo_fahuo['kuaidi_order'] = '';
	$dinghuo_fahuo['beizhu'] = $expressDesc;
	$dinghuo_fahuo['dtTime'] = date("Y-m-d H:i:s");
	$dinghuo_fahuo['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_fahuo['userName'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_fahuo',$dinghuo_fahuo,'id');
	//更新订货单状态及发货状态
	if(empty($fahuoId))$fahuoId = $db->get_var("select last_insert_id();");
	$status = 2;
	$fahuoStatus = 1;
	$isall = $db->get_var("select id from demo_dinghuo_detail$fenbiao where jiluId=$dinghuoId and num>hasNum limit 1");
	if(empty($isall)){
		$status = 4;
		$chukuNums = $db->get_var("select count(*) from demo_kucun_jilu$fenbiao where dinghuoId=$dinghuoId and status=1");
		$fahuoNums = $db->get_var("select count(*) from demo_dinghuo_fahuo where dinghuoId=$dinghuoId");
		if($chukuNums==$fahuoNums){
			$fahuoStatus = 2;
			$status = 6;
			$liucheng = getLiucheng();
			if($liucheng['if_shouhuo']==1){
				$status = 5;
			}
		}else{
			addTaskMsg(14,$dinghuoId,'有新的订货单需要您进行发货操作，请及时处理！');
		}
	}
	$db->query("update demo_dinghuo_order set status=$status,fahuoStatus=$fahuoStatus where id=$dinghuoId");
	addJilu($dinghuoId,'订货单发货','订货单'.($fahuoStatus==1?'部分':'已全部').'发货');
	add_dinghuo_msg($order->kehuId,'订货单'.$order->orderId.($fahuoStatus==1?'部分':'已全部').'发货',1,$dinghuoId);
	//请求电子面单接口
	require(ABSPATH.'/inc/KdApiEOrder.php');
	$storeId = $db->get_var("select storeId from demo_kucun_jilu$fenbiao where id=".$dinghuo_fahuo['jiluId']);
	$store = $db->get_row("select areaId,address from demo_kucun_store where id=$storeId");
	dinghuo_fahuo($fahuoId,$expressno[0],$store->areaId,$store->address,$request['name'],$request['phone']);
	echo '{"code":1}';exit;
}
function zuofei_fahuo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenBiao($comId,20);
	$dinghuoId = (int)$request['dinghuoId'];
	$jiluId = (int)$request['id'];
	$beizhu = $request['beizhu'];
	if(empty($dinghuoId)||empty($jiluId)){
		echo '{"code":0,"message":"系统错误，请刷新重试"}';
		exit;
	}
	$kucunJiluId = $db->get_var("select jiluId from demo_dinghuo_fahuo where id=$jiluId");
	$orderId = $db->get_var("select orderId from demo_kucun_jilu$fenbiao where id=$kucunJiluId");
	$db->query("delete from demo_dinghuo_fahuo where id=$jiluId and dinghuoId=$dinghuoId");
	$status = 2;
	$fahuoStatus = 1;
	$isall = $db->get_var("select id from demo_dinghuo_detail$fenbiao where jiluId=$dinghuoId and num>hasNum limit 1");
	$ifhas = $db->get_var("select id from demo_dinghuo_fahuo where dinghuoId=$dinghuoId limit 1");
	if(empty($isall)){
		$status = 4;
	}
	if(empty($ifhas)){
		$fahuoStatus = 0;
	}
	$order = $db->get_row("select kehuId,orderId from demo_dinghuo_order where id=$dinghuoId");
	$db->query("update demo_dinghuo_order set status=$status,fahuoStatus=$fahuoStatus where id=$dinghuoId");
	//写记录
	addJilu($dinghuoId,'作废发货记录','作废发货记录：'.$orderId.',原因：'.$beizhu);
	add_dinghuo_msg($order->kehuId,'订货单'.$order->orderId.'作废发货记录,原因：'.$beizhu,1,$dinghuoId);
	echo '{"code":1}';exit;
}
function print_fahuo(){}
function daochuFahuo(){}
function shoukuan(){}
function getShoukuanInfo(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['dinghuoId'];
	$return = array();
	$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在，请刷新重试。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$return['code'] = 1;
	$return['message'] = '成功';
	$return['data']['money'] = $jilu->price;
	$return['data']['payed'] = $jilu->price_payed;
	$daiqueren = $db->get_var("select sum(money) from demo_dinghuo_money where jiluId=$id and status=0");
	$return['data']['daiqueren'] = $daiqueren;
	$daizhifu = $return['data']['daizhifu'] = $jilu->price_weikuan-$daiqueren;
	if($return['data']['daizhifu']<=0){
		$return['code']=0;
		$return['message']='已收回所有款项，不需要再添加。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$return['data']['orderId'] = $jilu->orderId;
	$return['data']['yue_account1'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=1 limit 1");
	$return['data']['yue_account2'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=2 limit 1");
	$return['data']['yue_account3'] = $db->get_var("select money from demo_kehu_account where kehuId=$jilu->kehuId and type=3 limit 1");
	if(empty($return['data']['yue_account1']))$return['data']['yue_account1']=0;
	if(empty($return['data']['yue_account2']))$return['data']['yue_account2']=0;
	if(empty($return['data']['yue_account3']))$return['data']['yue_account3']=0;
	if($return['data']['yue_account1']>=$daizhifu){
		$return['data']['account1'] = $daizhifu;
		$daizhifu=0;
	}else{
		$return['data']['account1'] = $return['data']['yue_account1'];
		$daizhifu-=$return['data']['account1'];
	}
	if($return['data']['yue_account2']>=$daizhifu){
		$return['data']['account2'] = $daizhifu;
		$daizhifu=0;
	}else{
		$return['data']['account2'] = $return['data']['yue_account2'];
		$daizhifu-=$return['data']['account2'];
	}
	if($return['data']['yue_account3']>=$daizhifu){
		$return['data']['account3'] = $daizhifu;
		$daizhifu=0;
	}else{
		$return['data']['account3'] = $return['data']['yue_account3'];
		$daizhifu-=$return['data']['account3'];
	}
	$return['data']['payMoney'] = $daizhifu;
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function add_shoukuan(){
	global $db,$request,$adminRole,$qx_arry;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$kehuId = (int)$_SESSION['kehuId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['dinghuoId'];
	$return = array();
	$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
	$kehu_shezhi = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
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
	if($kehu_shezhi->acc_xianjin_queren==1||$adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu'))$zongMoney+=$account1;
	if($kehu_shezhi->acc_yufu_queren==1||$adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu'))$zongMoney+=$account2;
	if($kehu_shezhi->acc_fandian_queren==1||$adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu'))$zongMoney+=$account3;
	if($account1>0){
		add_dinghuo_money($jilu,$account1,1);
	}
	if($account2>0){
		add_dinghuo_money($jilu,$account2,2);
	}
	if($account3>0){
		add_dinghuo_money($jilu,$account3,3);
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
		$dinghuo_money['status'] = 0;
		if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')){
			$dinghuo_money['status'] = 1;
			$zongMoney += $request['a_payMoney'];
		}
		/*if($kehuId>0){
			$dinghuo_money['status'] = 0;
		}else{
			$zongMoney += $request['a_payMoney'];
		}*/
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
		$dinghuo_money['files'] = $request['files'];
		$dinghuo_money['beizhu'] = $request['remark'];
		$dinghuo_money['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
		$dinghuo_money['userName'] = $_SESSION[TB_PREFIX.'name'];
		insert_update('demo_dinghuo_money',$dinghuo_money,'id');
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
			insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
		}else{
			//添加消息
		}
	}
	if($dinghuo_money['status']==1||$zongMoney>0){
		$payStatus = 3;
		if($zongMoney==$price_weikuan+$daiqueren)$payStatus=4;
		$new_status = $jilu->status;
		if($new_status==1&&$payStatus==4){
			$new_status = 2;
			$liucheng = getLiucheng();
			if($liucheng['if_chuku']==0&&$liucheng['if_fahuo']==0){
				if($liucheng['if_shouhuo']==0){
					$new_status = 6;
				}else{
					$new_status = 5;
				}
			}else{
				addTaskMsg(13,$id,'有新的订货单需要您进行出库\发货操作，请及时处理！');
			}
		}
		$db->query("update demo_dinghuo_order set status=$new_status,payStatus=$payStatus,price_payed=price_payed+$zongMoney,price_weikuan=price_weikuan-$zongMoney where id=$id");
	}
	if($hejiMoney>$zongMoney){
		addTaskMsg(12,$id,'有新的订货单需要您进行收款确认操作，请及时处理！');
	}
	add_dinghuo_msg($jilu->kehuId,'订货单'.$jilu->orderId.'代收款￥'.$hejiMoney,1,$jilu->id);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
//添加账户支付记录
function add_dinghuo_money($jilu,$money,$type){
	global $db,$adminRole,$qx_arry;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
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
	$status = $db->get_var("select $acctountType from demo_kehu_shezhi where comId=$comId");
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
	$dinghuo_money['status'] = $status;
	if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')){
		$dinghuo_money['status'] = 1;
	}
	$dinghuo_money['pay_type'] = $type;
	$dinghuo_money['pay_info'] = '订单付款';
	$dinghuo_money['userId'] = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$dinghuo_money['userName'] = $_SESSION[TB_PREFIX.'name'];
	insert_update('demo_dinghuo_money',$dinghuo_money,'id');
	if($status==1){
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
		insert_update('demo_kehu_liushui'.$fenbiao,$liushui,'id');
	}
}
function zuofeiShoukuan(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$dinghuoId = (int)$request['dinghuoId'];
	$jilu = $db->get_row("select * from demo_dinghuo_money where id=$id and jiluId=$dinghuoId");
	$return = array();
	if(empty($jilu)){
		$return['code']=0;
		$return['message']='记录不存在，请刷新重试。';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	if($jilu->status==-1){
		$return['code']=0;
		$return['message']='该记录已经作废，不能重复操作';
		echo json_encode($return,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$shenheUser = $_SESSION[TB_PREFIX.'name'];
	$db->query("update demo_dinghuo_money set status=-1,shenheUser=$userId,shenheCont='$shenheUser' where id=$id");
	if($jilu->status==1){
		$db->query("update demo_kehu_liushui$fenbiao set status=-1,shenheUser='$shenheUser' where kehuId=$jilu->kehuId and orderId='$jilu->orderId' and dinghuoId=$dinghuoId limit 1");
		if($jilu->pay_type<5){
			$db->query("update demo_kehu_account set money=money+$jilu->money where kehuId=$jilu->kehuId and type=$jilu->pay_type limit 1");
		}
		$payStatus = 3;
		$db->query("update demo_dinghuo_order set payStatus=$payStatus,price_payed=price_payed-$jilu->money,price_weikuan=price_weikuan+$jilu->money where id=$dinghuoId");
	}
	$orderId = $db->get_var("select orderId from demo_dinghuo_order where id=$dinghuoId");
	$content = '订货单：'.$orderId.'收款记录'.$jilu->orderId.'被作废';
	add_dinghuo_msg($jilu->kehuId,$content,1,$dinghuoId);
	$return['code']=1;
	$return['message']='ok';
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function getWuliu(){
	global $request;
	$type = (int)$request['type'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$com = $request['kuaidi_com'];
	$order = $request['kuaidi_order'];
	require_once(ABSPATH.'/inc/KdApiEOrder.php');
	if($type==2){
		$kdnArry = array('顺丰'=>'SF',"EMS"=>'EMS','宅急送'=>'ZJS','圆通'=>'YTO','百世快递'=>'HTKY','中通'=>'ZTO','韵达'=>'YD','申通'=>'STO','天天快递'=>'HHTT','邮政快递包裹'=>'YZPY','德邦'=>'DBL','优速'=>'UC','信丰'=>'XFEX','全峰'=>'QFKD','跨越速运'=>'KYSY','安能小包'=>'ANE','快捷快递'=>'FAST','国通'=>'GTO','中铁快运'=>'ZTKY','邮政国内标快'=>'YZBK');
		get_wuliu('',$kdnArry[$com],$order);
	}else if($type==3){
		global $db;
		$app = $db->get_var("select type3Info from demo_dinghuo_set where comId=$comId");
		if(empty($app)){
			echo '{"code":0,"message":"未找到配置信息，请登录知商总控制台-》应用管理-》物流跟踪 设置。"}';
			exit;
		}
		$info = json_decode($app,true);
		$com = 'auto';
        $host = "https://ali-deliver.showapi.com";
        $path = "/showapi_expInfo";
        $method = "GET";
        $appcode = $info['appCode'];
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "com=".$com."&nu=".$order;
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($curl);
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }else{
        	echo '{"code":0,"message":"appCode不正确，请重新配置"}';
        	exit;
        }
        $wlInfo = $body;
        $wlInfo = json_decode($wlInfo);
        $resultstr = '';
        if($wlInfo){
        	if($wlInfo->showapi_res_body->ret_code == 0){
        		foreach ($wlInfo->showapi_res_body->data as $key => $value) {
        			$resultstr.=$value->time.'&nbsp;&nbsp;'.$value->context.'<br>';
        		}
        	}else{
        		echo '{"code":0,"message":"appCode不正确，请重新配置"}';
        		exit;
        	}
        }else{
        	echo '{"code":0,"message":"appCode不正确，请重新配置"}';
        	exit;
        }
        echo '{"code":1,"message":"'.$resultstr.'"}';
		exit;
	}
}
function getKehuPriceArry($inventoryId,$kehuId){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	}
	$return = array();
	$dinghuo = $db->get_row("select ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where inventoryId=$inventoryId and kehuId=$kehuId limit 1");
	if(!empty($dinghuo)){
		if($dinghuo->ifsale==1){
			$return['price'] = $dinghuo->price_sale;
		}else{
			$return['price'] = '0.00';
		}
		$return['min'] = empty($dinghuo->dinghuo_min)?0:$dinghuo->dinghuo_min;
		$return['max'] = empty($dinghuo->dinghuo_max)?0:$dinghuo->dinghuo_max;
	}else{
		$level = $db->get_var("select level from demo_kehu where id=$kehuId");
		$dinghuo = $db->get_row("select ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where inventoryId=$inventoryId and levelId=$level limit 1");
		if(!empty($dinghuo)){
			if($dinghuo->ifsale==1){
				$return['price'] = $dinghuo->price_sale;
			}else{
				$return['price'] = '0.00';
			}
			$return['min'] = empty($dinghuo->dinghuo_min)?0:$dinghuo->dinghuo_min;
			$return['max'] = empty($dinghuo->dinghuo_max)?0:$dinghuo->dinghuo_max;
		}else{
			$return['price'] = '0.00';
			$return['min'] = 0;
			$return['max'] = 0;
		}
	}
	$return['price'] = getXiaoshu($return['price'],$product_set->price_num);
	if($product_set->if_dinghuo_min==0){$return['min'] = 0;}
	if($product_set->if_dinghuo_max==0){$return['max'] = 0;}
	return $return;
}
function getLiucheng(){
	global $db;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$liucheng = array('if_caiwu'=>0,'if_chuku'=>1,'if_fahuo'=>1,'if_shouhuo'=>0);
	$liuchengContent = $db->get_var("select content from demo_liucheng where comId=$comId and type=1");
	if(!empty($liuchengContent)){
		$liucheng = json_decode($liuchengContent,true);
	}
	return $liucheng;
}