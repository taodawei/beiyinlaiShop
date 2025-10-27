<?php
function storey(){}

function chongzhi_del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['ids'];
	
	$db->query("update chongzhi_gift set is_del = 1 where id = $id ");
	
	echo '{"code":1}';
}

function index(){}
function yhq(){}
function viewYhq(){}
function cuxiao(){}
function order(){}
function view_cuxiao(){}
function view_order(){}
function chongzhi(){}
function reg(){}
function gift_card(){}
function yushou(){}
function viewGiftCardJilu(){}
function getYhqList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$type = (int)$request['type'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from yhq where comId=$comId ";
	if(!empty($type)){
		$sql.=" and type=$type";
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	$now = date("Y-m-d H:i:s");
	if(!empty($status)){
		switch ($status) {
			case 1:
				$sql.=" and status=1 and startTime>'$now'";
			break;
			case 2:
				$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
			break;
			case 3:
				$sql.=" and status=1 and endTime<'$now'";
			break;
			case 4:
				$sql.=" and status=0";
			break;
			case 5:
				$sql.=" and status=1";
			break;
		}
	}
	if(!empty($startTime)&&!empty($endTime)){
		$sql.=" and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime'))";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->layclass= '';
			$endTime = strtotime($j->endTime);
			$now = time();
			if($j->status!=1){
				$j->layclass= 'deleted';
			}
			if($j->type==1&&$now>$endTime){
				$j->layclass= 'deleted';
				$j->status = 0;
			}
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->jiazhi = $j->money.'<br>';
			$j->jiazhi.= empty($j->man)?'<span style="color:#b4b4b4;">无门槛</span>':'<span style="color:#b4b4b4;">满￥'.$j->man.'可用</span>';
			$j->xianzhi = '每人限领'.$j->numlimit.'张<br>';
			if($j->numlimit==0){
				$j->xianzhi = '不限制领取数量<br>';
			}
			$j->xianzhi .= '<span style="color:#b4b4b4;">共'.$j->num.'张</span>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			if(!empty($j->areaIds)){
				$areas = $db->get_var("select group_concat(title) from demo_area where id in($j->areaIds)");
				$j->areas = '<span onmouseover="tips(this,\''.$areas.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($areas),25,true).'</span>';
			}else{
				$j->areas = '所有区域';
			}
			$j->fanwei = $fanwei = '所有门店<br>';
			if(!empty($j->mendianIds)){
				$fanwei = $db->get_var("select group_concat(title) from mendian where id in($j->mendianIds)");
				$j->fanwei = '<span onmouseover="tips(this,\''.$fanwei.'\',1);" onmouseout="hideTips()">'.sys_substr($fanwei,10,true).'</span><br>';
			}
			if($j->useType==1){
				$j->fanwei.='<span style="color:#b4b4b4;">所有商品</span>';
			}else{
				$j->fanwei.='<span style="color:#b4b4b4;">指定商品</span>';
			}
			if(!empty($j->levelIds)){
				$levels = $db->get_var("select group_concat(title) from user_level where id in($j->levelIds)");
				$j->levels = '<span onmouseover="tips(this,\''.$levels.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($levels),25,true).'</span>';
			}else{
				$j->levels = '所有';
			}
			if($type==1){
				$lingqus = $db->get_row("select count(distinct(userId)) as num,count(*) as zong from user_yhq$fenbiao where comId=$comId and jiluId=$j->id");
				$j->lingqus = (int)$lingqus->num.'/'.(int)$lingqus->zong;
			}else{
				$j->lingqus = (int)$db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$j->id");
			}
			$j->usenum = (int)$db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$j->id and status=1");
			$j->select = '<a href="javascript:" onclick="select_yhq('.$j->id.',\''.$j->title.'\');" style="color:#31baf3">选择</a>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getpdts(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$title = $request['title'];
	$channelId = (int)$request['channelId'];
	$sn = $request['sn'];
	$status = $request['status'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,sn,title,key_vals,image,price_sale from demo_product_inventory where comId=$comId";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($title)){
		$sql.=" and title like '%$title%'";
	}
	if(!empty($sn)){
		$sql.=" and sn='$sn'";
	}
	if(!empty($status)){
		$sql.=" and status=$status";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,image,price_sale','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($pdts)){
		foreach ($pdts as $j){
			$j->image = '<img src="'.ispic($j->image).'?x-oss-process=image/resize,w_54" width="50">';
			$j->price = getXiaoshu($pdt->price_sale,$product_set->price_num);
			$ifhas = $db->get_var("select id from yushou where comId=$comId and pdtId=$j->id and status=1 and fahuoTime>'".date("Y-m-d")."' limit 1");
			if(empty($ifhas)){
				$j->operate = '<a href="javascript:" onclick="add_yushou('.$j->id.',\''.$j->sn.'\',\''.$j->price.'\');">设置</a>';
			}else{
				$j->operate = '预售进行中';
			}
			/*$price = getXiaoshu($price,$product_set->price_num);
			$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","shuliang":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" id=\"shuliang_'.$pdt->id.'\">","price":"<input type=\"text\" class=\"sprkadd_xuanzesp_02_tt_input disabled\" readonly=\"true\" value=\"'.$price.'\" id=\"price_'.$pdt->id.'\">","units":"'.$unitstr.'"}';*/
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getYhqFafang(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$jiluId = (int)$request['jiluId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from user_yhq$fenbiao where comId=$comId and jiluId=$jiluId";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$user = $db->get_row("select nickname,phone from users where id = $j->userId");
			$j->userInfo = $user->nickname."(".$user->phone.")";
			$j->useInfo = null;
			if($j->orderId > 0){
			    $orderId = $db->get_var("select orderId from order$fenbiao where id = $j->orderId");
			    $j->useInfo = "消费订单编号:".$orderId;
			}
			$j->statusInfo = '未使用';
			if($j->status == 1){
			    $j->statusInfo = '已使用';
			}elseif($now > $j->endTime && $j->status == 0){
			    $j->statusInfo = '已过期';
			}
			$j->time = date("Y-m-d",strtotime($j->startTime)).' 至 '.date("Y-m-d",strtotime($j->endTime));
			$j->lingqus = (int)$db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$jiluId and fafangId=$j->id");
			$j->usenum = (int)$db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$jiluId and fafangId=$j->id and status=1");
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function yhq_shixiao(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("update yhq set status=0 where id=".$request['id']." and comId=$comId");
	echo '{"code":1}';
	exit;
}
function add_yhq2(){}
function add_yhq3(){}
function add_yhq(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$yhq = array();
		$yhq['id'] = (int)$request['id'];
		$yhq['comId'] = $comId;
		$yhq['type'] = (int)$request['type'];
		$yhq['title'] = $request['title'];
		$yhq['money'] = $request['money'];
		$yhq['man'] = $request['man'];
		if(empty($request['ifman'])){
			$yhq['man'] = 0;
		}
		$yhq['color'] = $request['color'];
		$yhq['content'] = $request['content'];
		$yhq['numlimit'] = (int)$request['numlimit'];
		$yhq['num'] = $request['num'];
		if(!empty($request['if_day_limit'])){
			$yhq['num_day'] = (int)$request['num_day'];
			$yhq['day_time'] = $request['day_time'];
		}else{
			$yhq['num_day'] = 0;
			$yhq['day_time'] = '00:00:00';
		}
		$yhq['startTime'] = $request['startTime'];
		$yhq['endTime'] = $request['endTime'];
		$yhq['endDays'] = (int)$request['endDays'];
		$yhq['areaIds'] = '';
		if(!empty($request['if_area'])){
			$yhq['areaIds'] = $request['areaIds'];
		}
		$yhq['levelIds'] = '';
		if(!empty($request['if_level'])&&!empty($request['levels'])){
			$yhq['levelIds'] = implode(',',$request['levels']);
		}
		$yhq['mendianIds'] = '';
		if(!empty($request['if_mendian'])&&!empty($request['mendians'])){
			$yhq['mendianIds'] = implode(',',$request['mendians']);
		}
		$yhq['useType'] = (int)$request['useType'];
		if($yhq['useType']==1){
			$yhq['channels'] = '';
			$yhq['pdts'] = '';
			$yhq['channelNames'] = '';
			$yhq['pdtNames'] = '';
		}else{
			$yhq['channels'] = $request['channels'];
			$yhq['pdts'] = $request['pdts'];
			$yhq['channelNames'] = $request['channelNames'];
			$yhq['pdtNames'] = $request['pdtNames'];
		}
		if(empty($yhq['id'])){
			$yhq['dtTime'] = date("Y-m-d H:i:s");
		}
		$yhq['status'] = 1;
		$yhq['originalPic'] = $request['originalPic'];
		
		insert_update('yhq',$yhq,'id');
		redirect('?s=yyyx&a=yhq&type='.$yhq['type']);
	}
}
function add_fafang(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$fafang = array();
		$fafang['comId'] = $comId;
		$fafang['yhqId'] = $request['id'];
		$fafang['startTime'] = $request['startTime'];
		$fafang['endTime'] = $request['endTime'];
		$fafang['areaIds'] = '0';
		if(!empty($request['if_area'])&&!empty($request['areaIds'])){
			$fafang['areaIds'] = $request['areaIds'];
		}
		$fafang['type'] = (int)$request['type'];
		switch ($fafang['type']) {
			case 1:
				$fafang['levelIds'] = '';
			break;
			case 2:
				$fafang['levelIds'] = implode(',',$request['levels']);
			break;
			case 3:
				$fafang['userIds'] = $request['userIds'];
			break;
		}
		$fafang['dtTime'] = date("Y-m-d H:i:s");
		$fafang['num'] = (int)$request['num'];
		$fafang['username'] = $_SESSION[TB_PREFIX.'name'];
		insert_update('yhq_fafang',$fafang,'id');
		redirect('?s=yyyx&a=viewYhq&id='.$fafang['yhqId'].'&returnurl='.urlencode($request['url']));
	}
}
function getCuxiaoList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$scene = (int)$request['scene'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$pdtName = $request['pdtName'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$mendianIds = $request['mendianIds'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from cuxiao_pdt where comId=$comId";
	if($scene>0){
		$sql.=" and scene=$scene";
	}
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and endTime<'$now'";
		break;
		case 4:
			$sql.=" and status<>1";
		break;
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($pdtName)){
		$pdtIds = $db->get_var("select group_concat(id) from demo_product_inventory where (title like '%$pdtName%' or sn='$pdtName')");
		if(empty($pdtIds)){
			$sql.=" and pdtIds='0'";
		}else{
			$ids = explode(',',$pdtIds);
			$sql.=" and (";
			$sql1 = '';
			foreach ($ids as $id){
				$sql1.=" or find_in_set($id,pdtIds)";
			}
			$sql1 = substr($sql1,4);
			$sql.=$sql1.')';
		}
	}
	if(!empty($startTime)){
		$sql.=" and startTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and endTime<='$endTime 23:59:59'";
	}
	if(!empty($mendianIds)){
		$ids = explode(',',$mendianIds);
		$sql.=" and (";
		$sql1 = '';
		foreach ($ids as $id){
			$sql1.=" or find_in_set($id,mendianIds)";
		}
		$sql1 = substr($sql1,4);
		$sql.=$sql1.')';
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->layclass= '';
			$endTime = strtotime($j->endTime);
			$now = time();
			if($j->status!=1||$now>$endTime){
				$j->layclass= 'deleted';
				$j->status = 0;
			}
			$scene = '';
			switch ($j->scene) {
				case 1:
					$scene = '线上商城';
				break;
				case 2:
					$scene = '订货平台';
				break;
				case 3:
					$scene = '线下门店';
				break;
			}
			$j->scene = $scene;
			$pdts = $db->get_var("select group_concat(title) from demo_product_inventory where id in($j->pdtIds)");
			$j->pdts = '<span onmouseover="tips(this,\''.str_replace(',','<br>',$pdts).'\',1);" onmouseout="hideTips()">'.sys_substr($pdts,10,true).'</span>';
			$j->mendian = '';
			if(!empty($j->mendianIds)){
				$mendian = $db->get_var("select group_concat(title) from mendian where id in($j->mendianIds)");
				$j->mendian = '<span onmouseover="tips(this,\''.$mendian.'\',1);" onmouseout="hideTips()">'.sys_substr($mendian,10,true).'</span>';
			}
			$content = '限时活动';
			$type1 = $j->accordType == '1'?'个':'元';
			$type2 = $j->type==1?'赠':($j->type==2?'减':'享');
			$contents = json_decode($j->guizes);
			foreach ($contents as $rule){
				$content .='满'.$rule->man.$type1.$type2.$rule->jian.$rule->unit;
				switch ($j->type) {
					case 1:
						$inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
						$content .=$inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
					break;
					case 2:
						$content .='元';
					break;
					case 3:
						$content .='%';
					break;
				}
				$content.='<br>';
			}
			$icon = '';
			switch ($j->type){
				case 1:
					$icon = '<i class="yx_cuxiaoshangpin_icon" style="background-color:#8c7ee4;">买赠</i>';	
				break;
				case 2:
					$icon = '<i class="yx_cuxiaoshangpin_icon" style="background-color:#fe8d49;">满减</i>';	
				break;
				case 3:
					$icon = '<i class="yx_cuxiaoshangpin_icon">满折</i>';
				break;
			}
			if($j->accordType==3){
				$icon = '<i class="yx_cuxiaoshangpin_icon">限时</i>';
			}
			$j->content = '<span onmouseover="tips(this,\''.$content.'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($content),10,true).'</span>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			$j->title = '<span onclick="view_jilu('.$j->id.')">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getOrderList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$scene = (int)$request['scene'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$pdtName = $request['pdtName'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$mendianIds = $request['mendianIds'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from cuxiao_order where comId=$comId";
	if($scene>0){
		$sql.=" and scene=$scene";
	}
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and endTime<'$now'";
		break;
		case 4:
			$sql.=" and status<>1";
		break;
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and startTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and endTime<='$endTime 23:59:59'";
	}
	if(!empty($mendianIds)){
		$ids = explode(',',$mendianIds);
		$sql.=" and (";
		$sql1 = '';
		foreach ($ids as $id){
			$sql1.=" or find_in_set($id,mendianIds)";
		}
		$sql1 = substr($sql1,4);
		$sql.=$sql1.')';
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->layclass= '';
			$endTime = strtotime($j->endTime);
			$now = time();
			if($j->status!=1||$now>$endTime){
				$j->layclass= 'deleted';
				$j->status = 0;
			}
			$scene = '';
			switch ($j->scene) {
				case 1:
					$scene = '线上商城';
				break;
				case 2:
					$scene = '订货平台';
				break;
				case 3:
					$scene = '线下门店';
				break;
			}
			$j->scene = $scene;
			$j->mendian = '';
			if(!empty($j->mendianIds)){
				$mendian = $db->get_var("select group_concat(title) from mendian where id in($j->mendianIds)");
				$j->mendian = '<span onmouseover="tips(this,\''.$mendian.'\',1);" onmouseout="hideTips()">'.sys_substr($mendian,10,true).'</span>';
			}
			$content = '';
			$type1 = '元';
			$type2 = $j->type==1?'赠':($j->type==2?'减':'享');
			$contents = json_decode($j->guizes);
			foreach ($contents as $rule){
				$content .='满'.$rule->man.$type1.$type2.$rule->jian.$rule->unit;
				switch ($j->type) {
					case 1:
						$inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
						$content .=$inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
					break;
					case 2:
						$content .='元';
					break;
					case 3:
						$content .='%';
					break;
				}
				$content.='<br>';
			}
			$icon = '';
			switch ($j->type){
				case 1:
					$icon = '<i class="yx_cuxiaoshangpin_icon" style="background-color:#8c7ee4;">买赠</i>';	
				break;
				case 2:
					$icon = '<i class="yx_cuxiaoshangpin_icon" style="background-color:#fe8d49;">满减</i>';	
				break;
				case 3:
					$icon = '<i class="yx_cuxiaoshangpin_icon">满折</i>';
				break;
			}
			$j->content = '<span onmouseover="tips(this,\''.$content.'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($content),10,true).'</span>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			$j->title = '<span onclick="view_jilu('.$j->id.')">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getChongzhiList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from chongzhi_gift where comId=$comId and is_del = 0 ";
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and endTime<'$now'";
		break;
		case 4:
			$sql.=" and status<>1";
		break;
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->layclass= '';
			$endTime = strtotime($j->endTime);
			$now = time();
			if($j->status!=1||$now>$endTime){
				$j->layclass= 'deleted';
				$j->status = 0;
			}
			$content = '';
			$type1 = '元';
			$type2 = '赠';
			$contents = json_decode($j->guizes);
			foreach ($contents as $rule){
				$content .='满'.$rule->man.$type1.$type2.$rule->jian;
				switch ($j->type) {
					case 1:
						$content .='元';
					break;
					case 2:
						$content .='积分';
					break;
					case 3:
						$yhq = $db->get_var("select title from yhq where id=$rule->yhqId");
						$content .=$yhq;
					break;
				}
				$content.='<br>';
			}
			$j->content = '<span onmouseover="tips(this,\''.$content.'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($content),10,true).'</span>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getRegList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = (int)$request['status'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from reg_gift where comId=$comId";
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and endTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and endTime<'$now'";
		break;
		case 4:
			$sql.=" and status<>1";
		break;
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->layclass= '';
			$endTime = strtotime($j->endTime);
			$now = time();
			if($j->status!=1||$now>$endTime){
				$j->layclass= 'deleted';
				$j->status = 0;
			}
			$content = '';
			//$type1 = '元';
			$type2 = '赠';
			$contents = json_decode($j->guizes);
			foreach ($contents as $rule){
				$content .='注册'.$type2.$rule->jian;
				switch ($j->type) {
					case 1:
						$content .='元';
					break;
					case 2:
						$content .='积分';
					break;
					case 3:
						$yhq = $db->get_var("select title from yhq where id=$rule->yhqId");
						$content .=$yhq;
					break;
				}
				$content.='<br>';
			}
			$j->content = '<span onmouseover="tips(this,\''.$content.'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($content),10,true).'</span>';
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).' 至<br>'.date("Y-m-d H:i",strtotime($j->endTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getGiftCardList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$type = (int)$request['type'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from gift_card_jilu where comId=$comId and type=$type";
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->layclass= '';
			if($j->status!=1){
				$j->layclass= 'deleted';
			}else if(!empty($j->endTime)){
				$endTime = strtotime($j->endTime);
				$now = time();
				if($now>$endTime){
					$j->layclass= 'deleted';
				}
			}
			$j->endTime = empty($j->endTime)?'无限制':date("Y-m-d H:i",strtotime($j->endTime));
			$j->daochuTime = empty($j->daochuTime)?'无':date("Y-m-d H:i",strtotime($j->daochuTime));
			$j->title = '<span onclick="view('.$j->id.')">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getCardDetails(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$jiluId = (int)$request['jiluId'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from gift_card$fenbiao where comId=$comId and jiluId=$jiluId";
	switch ($status) {
		case 1:
			$sql.=" and userId=0";
		break;
		case 2:
			$sql.=" and userId>0";
		break;
	}
	if(!empty($keyword)){
		$uid = $db->get_var("select id from users where comId=$comId and username='$keyword' limit 1");
		$uid = empty($uid)?-1:$uid;
		$sql.=" and (cardId like '%$keyword%' or userId=$uid)";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			if($j->userId>0){
				$j->binduser = $db->get_var("select nickname from users where id=$j->userId");
				$j->bind_time = date("Y-m-d H:i",strtotime($j->bind_time));
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getYushouList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$type = (int)$request['type'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from yushou where comId=$comId";
	if($type>0){
		$sql.=" and type=$type";
	}
	$now = date("Y-m-d H:i:s");
	switch ($status){
		case 1:
			$sql.=" and status=1 and startTime>'$now'";
		break;
		case 2:
			$sql.=" and status=1 and startTime<='$now' and fahuoTime>='$now'";
		break;
		case 3:
			$sql.=" and status=1 and fahuoTime>'$now'";
		break;
		case 4:
			$sql.=" and status=-1";
		break;
	}
	if(!empty($keyword)){
		$sql.=" and pdtInfo like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			
			$startTime = strtotime($j->startTime);
			$endTime = strtotime($j->endTime);
			$now = time();
			$pdt = json_decode($j->pdtInfo);
			$j->image = '<img src="'.ispic($pdt->image).'?x-oss-process=image/resize,w_54" width="50">';
			$j->title = $pdt->title;
			$j->sn = $pdt->sn;
			$j->fukuan_time = '付定金时间：'.date("Y-m-d H:i",strtotime($j->startTime)).' - '.date("Y-m-d H:i",strtotime($j->endTime));
			if($j->weikuan>0){
				$j->fukuan_time .= '<br>付尾款时间：'.date("Y-m-d H:i",strtotime($j->startTime1)).' - '.date("Y-m-d H:i",strtotime($j->endTime1));
			}
			$j->fahuoTime = date("Y-m-d H:i",strtotime($j->fahuoTime));
			$price_json = json_decode($j->price_json);
			if(count($price_json)==1){
				$j->price = '预售价:<span style="color:#ff0000;">'.$price_json[0]->price.'</span>';
			}else{
				$j->price = '预售价:<span style="color:#ff0000;">￥'.$price_json[0]->price.'</span>';
				foreach ($price_json as $i => $price) {
					if($i>0){
						$j->price .= '<br>满'.$price->num.'份:<span style="color:#ff0000;">￥'.$price->price.'</span>';
					}
				}
			}
			$j->dingjin_weikuan = '定金：'.$j->dingjin.'<br>尾款：'.$j->weikuan;
			$j->nums = $j->num.'/'.$j->num_saled;
			$orders = $db->get_var("select count(*) as orderNum,count(distinct(userId)) as userNum,sum(price_payed) as money from order$fenbiao where yushouId=$j->id and status!=-1");
			$j->orders = (empty($orders->userNum)?0:$orders->userNum).'/'.(empty($orders->orderNum)?0:$orders->orderNum);
			$j->money = empty($orders->money)?'0':$orders->money;
			$j->status_info = '';
			$j->layclass = '';
			if($j->status==-1){
				$j->layclass= 'deleted';
				$j->status_info = '已作废';
			}else{
				if($now>$endTime){
					$j->status_info = '已结束';
				}else if($now<$startTime){
					$j->status_info = '未开始';
				}else{
					$j->status_info = '进行中';
				}
			}
			$j->title = '<span onclick="view_jilu('.$j->id.')">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function piliang_zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update cuxiao_pdt set status=0 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function piliang_zuofei_order(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update cuxiao_order set status=0 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function piliang_zuofei_chongzhi(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update chongzhi_gift set status=0 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function piliang_zuofei_reg(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update reg_gift set status=0 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function zuofei_gift_card(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("update gift_card_jilu set status=0 where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function zuofei_yushou(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update yushou set status=-1 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}

function create_cuxiao()
{
    global $db,$request;
    
	if(isset($request['id']) && $request['id'] > 0){
	    $accordType = $db->get_var("select accordType from  cuxiao_pdt where id = ".$request['id']);
	    if($accordType == 2){
	       	redirect('?s=yyyx&a=create_cuxiao&id='.$request['id']);
	    }
	}
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$mendianIds = $_SESSION[TB_PREFIX.'mendianId'];
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
// 		$timeRange = (int)$request['timeRange'];
// 		switch($timeRange){
// 		    case 12:
// 		        $startTime = $startTime1." 22:00:00";
// 		        $time = strtotime($startTime1) + 24*3600;
// 		        $endTime = date('Y-m-d', $time)." 00:00:00";
// 		        break;
// 		    default:
// 		        $startHour = 2*($timeRange-1);
// 		        $endHour = 2*$timeRange;
// 		        $startTime = $startTime1." $startHour:00:00";
// 		        $endTime = $startTime1." $endHour:00:00";
// 		        break;  
// 		}
        $request['accordType'] = 3;
		$cuxiao = array();
		$cuxiao['scene'] = (int)$request['scene'];
		$cuxiao['mendianIds'] = $mendianIds;
		$nowNum = $db->get_var("select count(*) from cuxiao_pdt where comId=$comId and scene=".$cuxiao['scene']." and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime'))");
		if($nowNum>9){
			echo '{"code":0,"message":"每种场景的促销活动在同一时间段内最多创建10个！"}';
			exit;
		}
		if(!empty($request['inventoryId'])){
			foreach ($request['inventoryId'] as $inventoryId){
			    $activityType = inventoryActivity($inventoryId, $startTime, $endTime);
			    //活动：1-普通商品  2-积分商品  3-秒杀   4-团购   5-预售
			    $activity = [1=>'未参加', 2=>'积分商品',3=>'秒杀', 4=>'团购', 5=>'预售'];
			    if($activityType != 1){
					$inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$inventoryId");
			        echo '{"code":0,"message":"产品“'.$inventory->title.($inventory->key_vals=='无'?'':'['.$inventory->key_vals.']').'”在该时间段已经参加了'.$activity[$activityType].'活动，不能再创建新的促销"}';
					exit;
			    }
				$ifhas = $db->get_var("select id from cuxiao_pdt where comId=$comId and scene=".$cuxiao['scene']." and status=1 and find_in_set($inventoryId,pdtIds) and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime')) limit 1");
				if(!empty($ifhas)){
					$inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$inventoryId");
					echo '{"code":0,"message":"产品“'.$inventory->title.($inventory->key_vals=='无'?'':'['.$inventory->key_vals.']').'”在该时间段已经参加了其他的促销活动，不能再创建新的促销"}';
					exit;
				}
			}
		}else{
			echo '{"code":0,"message":"未检测到任何商品！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['pdtIds'] = implode(',',$request['inventoryId']);
		$cuxiao['comId'] = $comId;
		$cuxiao['title'] = $request['title'];
		$cuxiao['accordType'] = (int)$request['accordType'];
		$cuxiao['type'] = (int)$request['type'];
		switch ($cuxiao['scene']) {
			case 1:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 2:
				$cuxiao['levelIds1'] = empty($request['if_level1'])?'':implode(',',$request['levels1']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 3:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				// $cuxiao['mendianIds'] = empty($request['if_mendian'])?'':implode(',',$request['mendians']);
			break;
		}
		$cuxiao['dtTime'] = date("Y-m-d H:i:s");
		$guizes = array();
		if($cuxiao['accordType']!=3){
			foreach ($request['rows_'.$cuxiao['type']] as $row){
				$guize  = array();
				$guize['man'] = $request['man_'.$cuxiao['type'].'_'.$row];
				$guize['jian'] = $request['jian_'.$cuxiao['type'].'_'.$row];
				$guize['inventoryId'] = empty($request['inventoryId_'.$cuxiao['type'].'_'.$row])?0:$request['inventoryId_'.$cuxiao['type'].'_'.$row];
				$guize['unit'] = empty($request['unit_'.$cuxiao['type'].'_'.$row])?'':$request['unit_'.$cuxiao['type'].'_'.$row];
				$guizes[] = $guize;
			}
		}
		$cuxiao['guizes'] = json_encode($guizes,JSON_UNESCAPED_UNICODE);
// 		$cuxiao['status'] = 0;
		$cuxiao_id = $db->insert_update('cuxiao_pdt',$cuxiao,'id');
// 		if(!empty($request['xiangou'])){
			foreach ($request['xiangou'] as $key => $val) {
				if($val>=0){
				    $price = (float)$request['activity_price'][$key];
				    if($price <= 0){
				        $price = (float)$db->get_var("select price_sale from demo_product_inventory where id = $key");
				    }

					$db->query("insert into cuxiao_pdt_xiangou(cuxiao_id,inventoryId,num,price) value($cuxiao_id,$key,$val,$price)");
				}
			}
// 		}
		echo '{"code":1,"message":"ok"}';
		exit;
	}	
}

//活动：1-普通商品  2-积分商品  3-秒杀   4-团购   5-预售
function inventoryActivity($inventoryId, $startTime, $endTime){
    global $db,$comId;
    $type = 1;//普通商品
    $inventory = $db->get_row("select * from demo_product_inventory where id = $inventoryId");
    if($inventory->mendianId == 0){
        $type = 1;//积分商品
    }elseif($inventory->sale_tuan == 1 && $inventory->tuan_num > 0){
        $type = 4;//团购商品
    }else{
        $now = date('Y-m-d H:i:s');
        $cuxiao_jilus = $db->get_results("select id,endTime,pdtIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and ((endTime > '$startTime' and endTime <'$endTime') or (startTime > '$startTime' and startTime <'$endTime') or (startTime < '$startTime' and endTime > '$endTime')) and accordType=3 order by id desc ");
        foreach ($cuxiao_jilus as $ck => $jilu){
            $pdtIds = explode(',', $jilu->pdtIds);
            if(in_array($inventoryId, $pdtIds)){
                $type = 3;
                break;
            }
        }
    }
    if($type == 1){
        //判断预售
        $yushou = $db->get_row("select * from yushou where comId=$comId and ((endTime > '$startTime' and endTime <'$endTime') or (startTime > '$startTime' and startTime <'$endTime') or (startTime < '$startTime' and endTime > '$endTime')) and status=1 and pdtId = ".$inventoryId);
       // var_dump("select * from yushou where comId=$comId and startTime<'".date('Y-m-d H:i:s')."' and endTime>'".date('Y-m-d H:i:s')."' and status=1 and pdtId = ".$inventory->productId);die;
        if($yushou){
            $type = 5;
        }
    }
    
    return $type;
}

function create_cuxiaoBak(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
		$cuxiao = array();
		$cuxiao['scene'] = (int)$request['scene'];
		$nowNum = $db->get_var("select count(*) from cuxiao_pdt where comId=$comId and scene=".$cuxiao['scene']." and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime'))");
		if($nowNum>9){
			echo '{"code":0,"message":"每种场景的促销活动在同一时间段内最多创建10个！"}';
			exit;
		}
		if(!empty($request['inventoryId'])){
			foreach ($request['inventoryId'] as $inventoryId){
				$ifhas = $db->get_var("select id from cuxiao_pdt where comId=$comId and scene=".$cuxiao['scene']." and status=1 and find_in_set($inventoryId,pdtIds) and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime')) limit 1");
				if(!empty($ifhas)){
					$inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$inventoryId");
					echo '{"code":0,"message":"产品“'.$inventory->title.($inventory->key_vals=='无'?'':'['.$inventory->key_vals.']').'”在该时间段已经参加了其他的促销活动，不能再创建新的促销"}';
					exit;
				}
			}
		}else{
			echo '{"code":0,"message":"未检测到任何商品！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['pdtIds'] = implode(',',$request['inventoryId']);
		$cuxiao['comId'] = $comId;
		$cuxiao['title'] = $request['title'];
		$cuxiao['accordType'] = (int)$request['accordType'];
		$cuxiao['type'] = (int)$request['type'];
		switch ($cuxiao['scene']) {
			case 1:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 2:
				$cuxiao['levelIds1'] = empty($request['if_level1'])?'':implode(',',$request['levels1']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 3:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				$cuxiao['mendianIds'] = empty($request['if_mendian'])?'':implode(',',$request['mendians']);
			break;
		}
		$cuxiao['dtTime'] = date("Y-m-d H:i:s");
		$guizes = array();
		if($cuxiao['accordType']!=3){
			foreach ($request['rows_'.$cuxiao['type']] as $row){
				$guize  = array();
				$guize['man'] = $request['man_'.$cuxiao['type'].'_'.$row];
				$guize['jian'] = $request['jian_'.$cuxiao['type'].'_'.$row];
				$guize['inventoryId'] = empty($request['inventoryId_'.$cuxiao['type'].'_'.$row])?0:$request['inventoryId_'.$cuxiao['type'].'_'.$row];
				$guize['unit'] = empty($request['unit_'.$cuxiao['type'].'_'.$row])?'':$request['unit_'.$cuxiao['type'].'_'.$row];
				$guizes[] = $guize;
			}
		}
		$cuxiao['guizes'] = json_encode($guizes,JSON_UNESCAPED_UNICODE);
		$cuxiao_id = $db->insert_update('cuxiao_pdt',$cuxiao,'id');
		if(!empty($request['xiangou'])){
			foreach ($request['xiangou'] as $key => $val) {
				if($val>0){
					$db->query("insert into cuxiao_pdt_xiangou(cuxiao_id,inventoryId,num) value($cuxiao_id,$key,$val)");
				}
			}
		}
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function create_yushou(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$yushou = array();
		$yushou['comId'] = $comId;
		$yushou['pdtId'] = (int)$request['pdtId'];
		$yushou['type'] = (int)$request['type'];
		$yushou['paytype'] = (int)$request['paytype'];
		$yushou_price = $request['yushou_price'];
		$price_json = array();
		$price = array();
		$price['num'] = 0;
		$price['price'] = $yushou_price;
		$price_json[] = $price;
		$yushou['dingjin'] = $yushou['paytype']==1?$yushou_price:$request['dingjin'];
		if($yushou['dingjin']>$yushou_price){
			echo '{"code":0,"message":"定金不能大于预售金额！"}';
			exit;
		}
		if($yushou['type']==2&&!empty($request['rows'])){
			foreach ($request['rows'] as $val){
				if(!empty($request['man_'.$val])&&!empty($request['price_'.$val])){
					$guize  = array();
					$guize['num'] = $request['man_'.$val];
					$guize['price'] = $request['price_'.$val];
					$price_json[] = $guize;
					if($yushou['dingjin']>$guize['price']){
						echo '{"code":0,"message":"定金不能大于所有的阶梯价格！"}';
						exit;
					}
				}
			}
		}
		$yushou['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
		$yushou['weikuan'] = $yushou['paytype']==1?0:($yushou_price-$request['dingjin']);
		$yushou['num'] = $request['num'];
		$yushou['num_limit'] = $request['num_limit'];
		$yushou['startTime'] = $request['startTime'];
		$yushou['endTime'] = $request['endTime'];
		$startTime = strtotime($yushou['startTime']);
		$endTime = strtotime($yushou['endTime']);
		if($endTime<=$startTime){
			echo '{"code":0,"message":"结束时间不能小于开始时间！"}';
			exit;
		}
		if($yushou['paytype']==2){
			$yushou['startTime1'] = $request['startTime1'];
			$yushou['endTime1'] = $request['endTime1'];
			$startTime1 = strtotime($yushou['startTime1']);
			$endTime1 = strtotime($yushou['endTime1']);
			if($endTime1<$endTime){
				echo '{"code":0,"message":"付尾款时间不能小于付定金时间！"}';
				exit;
			}
		}
		$yushou['fahuoTime'] = $request['fahuoTime'];
		$yushou['dtTime'] = date("Y-m-d H:i:s");
		$pdt = $db->get_row("select image,sn,title,key_vals,price_sale from demo_product_inventory where id=".$yushou['pdtId']);
		$pdtInfoArry = array();
		$pdtInfoArry['image'] = '';
		if(!empty($pdt->image)){
			$imgs = explode('|',$pdt->image);
			$pdtInfoArry['image'] = $imgs[0];
		}
		$pdtInfoArry['sn'] = $pdt->sn;
		$pdtInfoArry['title'] = $pdt->title;
		$pdtInfoArry['key_vals'] = $pdt->key_vals;
		$pdtInfoArry['price_sale'] = $pdt->price_sale;
		$yushou['pdtInfo'] = json_encode($pdtInfoArry,JSON_UNESCAPED_UNICODE);
		insert_update('yushou',$yushou,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function create_order(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$startTime = $request['startTime'].' 00:00:00';
		$endTime = $request['endTime'].' 23:59:59';
		$cuxiao = array();
		$cuxiao['scene'] = (int)$request['scene'];
		$nowNum = $db->get_var("select count(*) from cuxiao_order where comId=$comId and scene=".$cuxiao['scene']." and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime'))");
		if($nowNum>9){
			echo '{"code":0,"message":"每种场景的促销活动在同一时间段内最多创建10个！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['comId'] = $comId;
		$cuxiao['title'] = $request['title'];
		$cuxiao['type'] = (int)$request['type'];
		switch ($cuxiao['scene']) {
			case 1:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 2:
				$cuxiao['levelIds1'] = empty($request['if_level1'])?'':implode(',',$request['levels1']);
				$cuxiao['areaIds'] = empty($request['if_area'])?'':$request['areaIds'];
			break;
			case 3:
				$cuxiao['levelIds'] = empty($request['if_level'])?'':implode(',',$request['levels']);
				$cuxiao['mendianIds'] = empty($request['if_mendian'])?'':implode(',',$request['mendians']);
			break;
		}
		$cuxiao['dtTime'] = date("Y-m-d H:i:s");
		$guizes = array();
		foreach ($request['rows_'.$cuxiao['type']] as $row){
			$guize  = array();
			$guize['man'] = $request['man_'.$cuxiao['type'].'_'.$row];
			$guize['jian'] = $request['jian_'.$cuxiao['type'].'_'.$row];
			$guize['inventoryId'] = empty($request['inventoryId_'.$cuxiao['type'].'_'.$row])?0:$request['inventoryId_'.$cuxiao['type'].'_'.$row];
			$guize['unit'] = empty($request['unit_'.$cuxiao['type'].'_'.$row])?'':$request['unit_'.$cuxiao['type'].'_'.$row];
			$guizes[] = $guize;
		}
		$cuxiao['guizes'] = json_encode($guizes,JSON_UNESCAPED_UNICODE);
		insert_update('cuxiao_order',$cuxiao,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function create_chongzhi(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$startTime = $request['startTime'].' 00:00:00';
		$endTime = $request['endTime'].' 23:59:59';
		$cuxiao = array();
		$cuxiao['scene'] = 1;
		$nowNum = $db->get_var("select id from chongzhi_gift where comId=$comId and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime')) limit 1");
		if($nowNum>0){
			echo '{"code":0,"message":"该时间段已经存在充值赠送活动！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['comId'] = $comId;
		$cuxiao['type'] = (int)$request['type'];
		$cuxiao['dtTime'] = date("Y-m-d H:i:s");
		$guizes = array();
		foreach ($request['rows_'.$cuxiao['type']] as $row){
			$guize  = array();
			$guize['man'] = $request['man_'.$cuxiao['type'].'_'.$row];
			$guize['jian'] = $request['jian_'.$cuxiao['type'].'_'.$row];
			$guize['yhqId'] = $request['yhqId_'.$cuxiao['type'].'_'.$row];
			$guizes[] = $guize;
		}
		$cuxiao['guizes'] = json_encode($guizes,JSON_UNESCAPED_UNICODE);
		insert_update('chongzhi_gift',$cuxiao,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function create_reg(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$startTime = $request['startTime'].' 00:00:00';
		$endTime = $request['endTime'].' 23:59:59';
		$cuxiao = array();
		$cuxiao['scene'] = 1;
		$nowNum = $db->get_var("select id from reg_gift where comId=$comId and status=1 and ((startTime<='$startTime' and endTime>='$startTime') or (startTime<='$endTime' and endTime>='$endTime') or (startTime>='$startTime' and endTime<='$endTime')) limit 1");
		if($nowNum>0){
			echo '{"code":0,"message":"该时间段已经存在注册赠送活动！"}';
			exit;
		}
		$cuxiao['startTime'] = $startTime;
		$cuxiao['endTime'] = $endTime;
		$cuxiao['comId'] = $comId;
		$cuxiao['type'] = (int)$request['type'];
		$cuxiao['dtTime'] = date("Y-m-d H:i:s");
		$guizes = array();
		foreach ($request['rows_'.$cuxiao['type']] as $row){
			$guize  = array();
			//$guize['man'] = $request['man_'.$cuxiao['type'].'_'.$row];
			$guize['jian'] = $request['jian_'.$cuxiao['type'].'_'.$row];
			$guize['yhqId'] = $request['yhqId_'.$cuxiao['type'].'_'.$row];
			$guizes[] = $guize;
		}
		$cuxiao['guizes'] = json_encode($guizes,JSON_UNESCAPED_UNICODE);
		insert_update('reg_gift',$cuxiao,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function create_card(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$fenbiao = getFenbiao($comId,20);
		$card_jilu = array();
		$card_jilu['comId'] = $comId;
		$card_jilu['title'] = $request['title'];
		$card_jilu['type'] = (int)$request['type'];
		$card_jilu['money'] = $request['money'];
		$card_jilu['price'] = $request['price'];
		$card_jilu['num'] = (int)$request['num'];
		$card_jilu['dtTime'] = date("Y-m-d H:i:s");
		if(!empty($request['endTime'])){
			$card_jilu['endTime'] = $request['endTime'].' 23:59:59';
		}
		$jiluId = $db->insert_update('gift_card_jilu',$card_jilu,'id');
		$sql = "insert into gift_card$fenbiao(comId,cardId,password,money,yue,jiluId,typeInfo".(empty($request['endTime'])?'':',endTime').") values";
		$sql1 = '';
		for ($i=0; $i < $card_jilu['num']; $i++) {
			$cardId = $jiluId.($i+1);
			$length = 16-strlen($cardId);
			for($j = 0; $j < $length; $j++) {
				$cardId .= rand(0,9);
			}
			$password = rand(100000,999999);
			$endTime = empty($request['endTime'])?'NULL':$request['endTime'];
			$sql1.=",($comId,'$cardId','$password','".$card_jilu['money']."','".$card_jilu['money']."',$jiluId,'".$card_jilu['title']."'".(empty($request['endTime'])?'':",'$endTime'").")";
		}
		$sql1 = substr($sql1,1);
		$db->query($sql.$sql1);
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function daochu_gift_card(){}



//获取区域相关方法
function getAreas(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$ds = $request['departs'];
	$dNames = $request['departNames'];
	if(!empty($ds)){
		$departs = explode(',',$ds);
		$departNames = explode(',',$dNames);
	}
	$str = '<div id="add_container">
			<div id="new_title">
				<div class="new_title_01">选择地区</div>
				<div class="new_title_02" onclick="hide_myModal();"></div>
				<div class="clearBoth"></div>
			</div>
		  <div id="splc_cont">
			<div class="splc_cont_left">
				<div class="splc_cont_left_title">已选择以下地区</div>
				<div class="splc_cont_left_con">
					<ul>';
					if(!empty($departs)){
						$i=0;
						foreach($departs as $depart){
							$str.='<li id="left_depart'.$depart.'">
								<div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_area_depart('.$depart.',\''.$departNames[$i].'\')"><img src="images/close1.png" border="0" /></a></div>
								<div class="clearBoth"></div>
								<div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png" /></div>'.$departNames[$i].'</div>
							</li>';
							$i++;
						}
					}
					$str.='</ul>
				</div>
			</div>
			<div class="splc_cont_right">
				<div class="splc_cont_right_title">所有地区</div>
				<div class="splc_cont_right_search"><input type="text" stlye="border:0px;" onchange="search_areas(this.value);" placeholder="请输入地区名称"></div>
				<div class="splc_cont_right_con">
					<div class="sp_nav1">
						   <ul id="depart_users">
						   	<li class="sp_nav_01">
							<ul>
						   ';
						$departs = $db->get_results("select * from demo_area where parentId=0 order by id asc");
						if(!empty($departs)){
							foreach($departs as $v){
								$str .='<li class="sp_nav_01_zimenu">
										  <img src="images/tree_bg2.jpg" data-id="'.$v->id.'" class="depart_select_img" />
										  <a href="javascript:add_area_depart('.$v->id.',\''.$v->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$v->title.'">'.sys_substr($v->title,10,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$v->id.'" style="display:none;"></ul>
										  <ul>';
								$departs1 = $db->get_results("select * from demo_area where parentId=".$v->id." order by id asc");
								if(!empty($departs1)){
									foreach($departs1 as $list){
										$str .='<li class="sp_nav_01_zimenu1">
										  <img src="images/tree_bg2.jpg" onclick="get_areas('.$list->id.')" data-id="'.$list->id.'" class="depart_select_img" />
										  <a href="javascript:add_area_depart('.$list->id.',\''.$list->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$list->title.'">'.sys_substr($list->title,9,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$list->id.'" style="display:none;"></ul>
										  </li>';
									}
								}
								$str .='</ul></li>';
							}
						}  
				$str .='</ul></li></ul>
					<ul id="search_users"></ul>
					 </div>
				</div>
				
			</div>
			<div class="clearBoth"></div>
			<div class="splc_cont_bottom">
			<input type="button" onclick="area_baocun();" value="保存" />
			<input type="button" onclick="hide_myModal();" value="取消" />
			</div>
		  </div>
		</div>';
	echo $str;
	exit;	
}
//获取子区域列表
function getAreasByPid(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['id'];
	$keyword = $request['keyword'];
	if(!empty($channelId)){
		$sql="SELECT id,title FROM demo_area WHERE parentId=$channelId";
	}else{
		$sql="SELECT id,title FROM demo_area WHERE title like '%$keyword%' limit 20";
	}
	$users=$db->get_results($sql);
	$str = "";
	if(!empty($users)){
		foreach($users as $user){
			$str.='<li class="sp_nav_02" onclick="add_area_depart('.$user->id.',\''.$user->title.'\')" title="'.$user->title.'"><div class="gg_people_show_3_1" style="float:left; margin-right:5px;">分类</div>'.sys_substr($user->title,7,true).'</li>';
		}
	}else{
		if(!empty($department)){
			$str.='<li class="sp_nav_02">该分类下没有地区</li>';
		}else{
			$str.='<li class="sp_nav_02">没有搜索到相关地区</li>';
		}
	}
	echo $str;
	exit;
}