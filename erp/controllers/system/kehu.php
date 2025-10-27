<?php
function banner(){}
function index(){}
function daochu(){}
function baojiadan(){}
function daochuBaojia(){}
function jiameng(){}
function daochuJiameng(){}
function fankui(){}
function getList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$level = (int)$request['level'];
	$status = (int)$request['status'];
	$keyword = $request['keyword'];
	$uname = $request['uname'];
	$areaId = (int)$request['areaId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('kehuPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select id,title,sn,username,areaId,level,name,phone,dtTime,status from demo_kehu where comId=$comId ";
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and (title like '%$keyword%' or sn='$keyword' or name='$keyword' or phone='$keyword')";
	}
	if(!empty($uname)){
		$sql.=" and uname like '%$uname%'";
	}
	if(!empty($areaId)){
		$areaIds = $areaId.getZiAreas($areaId);
		$sql.=" and areaId in($areaIds)";
	}
	$count = $db->get_var(str_replace('id,title,sn,username,areaId,level,name,phone,dtTime,status','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$status = '';
			$j->layclass = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已禁用</span>';
				break;
				case 0:
					$status = '未开通';
				break;
				case 1:
					$status = '<span style="color:green">已开通</span>';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->level = $db->get_var("select title from demo_kehu_level where id=$j->level");
			$j->areaName = '';
			$j->areaName = getAreaName((int)$j->areaId);
			$j->title = '<span onclick="view_jilu(\'kehu\','.$j->id.')" style="cursor:pointer;">'.$j->title.'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function delete(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ifhas = $db->query("select id from demo_dinghuo_order where comId=$comId and kehuId=$id limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"此客户已有订单发生,不可删除！！"}';
	}else{
		$db->query("delete from demo_kehu where id=$id and comId=$comId");
		echo '{"code":1,"message":"删除成功！"}';
	}
	exit;
}
function delFankui(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$status = $db->get_var("select status from demo_kehu_fankui where id=$id");
	if($status==0){
		echo '{"code":0,"message":"该反馈尚未结束，无法删除！"}';
		exit;
	}
	$db->query("update demo_kehu_fankui set del=1 where id=$id and comId=$comId");
	echo '{"code":1,"message":"删除成功！"}';
	exit;
}
function edit(){
	global $db,$request;
	if($request['tijiao']==1){
		$crmdb = getCrmDb();
		$kehu = array();
		$kehu['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$kehu['id'] = (int)$request['id'];
		if(!empty($request['username'])){
			$ifhas = $db->get_var("select id from demo_kehu where comId=".$kehu['comId']." and username='".$request['username']."' and id<>".$kehu['id']." limit 1");
			if($ifhas>0){
				die('用户名已存在，请重新输入');
			}
		}
		$kehu['title'] = $request['title'];
		$kehu['level'] = (int)$request['level'];
		$kehu['sn'] = $request['sn'];
		$kehu['storeId'] = (int)$request['storeId'];
		$kehu['departId'] = (int)$request['departId'];
		$kehu['userId'] = (int)$request['userId'];
		if(!empty($kehu['userId'])){
			$kehu['uname'] = $crmdb->get_var("select name from demo_user where id=".$kehu['userId']);
		}
		if(!empty($request['startTime'])){
			$kehu['startTime'] = $request['startTime'];
		}
		if(!empty($request['endTime'])){
			$kehu['endTime'] = $request['endTime'];
		}
		$kehu['youbian'] = $request['youbian'];
		$kehu['chuanzhen'] = $request['chuanzhen'];
		$kehu['areaId'] = (int)$request['psarea'];
		$kehu['address'] = $request['address'];
		$kehu['wuliuCode'] = $request['wuliuCode'];
		$kehu['beizhu'] = $request['beizhu'];
		$kehu['name'] = $request['name'];
		$kehu['phone'] = $request['phone'];
		$kehu['phone1'] = $request['phone1'];
		$kehu['job'] = $request['job'];
		$kehu['email'] = $request['email'];
		$kehu['qq'] = $request['qq'];
		$kehu['caiwu'] = json_encode($request['caiwu'],JSON_UNESCAPED_UNICODE);
		$kehu['status'] = empty($request['status'])?0:1;
		if(empty($request['id'])){
			$kehu['dtTime'] = date("Y-m-d H:i:s");
			if($kehu['status']==1){
				require_once(ABSPATH.'/inc/class.shlencryption.php');
				$kehu['username'] = $request['username'];
				$shlencryption = new shlEncryption($request['password']);
			  	$kehu['password'] = $shlencryption->to_string();
				$kehu['linkPhone'] = 1;
			}
		}else{
			if($kehu['status']==1&&!empty($request['password'])){
				require_once(ABSPATH.'/inc/class.shlencryption.php');
				$kehu['username'] = $request['username'];
				$shlencryption = new shlEncryption($request['password']);
			  	$kehu['password'] = $shlencryption->to_string();
			}
		}
		insert_update('demo_kehu',$kehu,'id');
		if(empty($kehu['id'])){
			$kehuId = $db->get_var("select last_insert_id();");
			$kehu_address = array();
			$kehu_address['kehuId'] = $kehuId;
			$kehu_address['name'] = $kehu['name'];
			$kehu_address['phone'] = $kehu['phone'];
			$kehu_address['areaId'] = $kehu['areaId'];
			$kehu_address['areaName'] = getAreaName($kehu['areaId']);
			$kehu_address['title'] = $kehu['title'];
			$kehu_address['moren'] = 1;
			$kehu_address['address'] = $kehu['address'];
			insert_update('demo_kehu_address',$kehu_address,'id');
		}
		redirect('?m=system&s=kehu');
	}
}
function updatePass(){
	global $db,$request;
	require_once(ABSPATH.'/inc/class.shlencryption.php');
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$shlencryption = new shlEncryption($request['password']);
	$password = $shlencryption->to_string();
	$db->query("update demo_kehu set password='$password' where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function jiebang(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("update demo_kehu set linkPhone=0 where id=$id and comId=$comId");
	echo '{"code":1,"message":"成功"}';
	exit;
}
function shangjia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_kehu set status=1 where comId=$comId and id in($ids) and status=-1");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function xiajia(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_kehu set status=-1 where comId=$comId and id in($ids) and status=1");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function getBaojiaList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	if(is_file("../cache/product_set_$comId.php")){
		$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
	}else{
		$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
	}
	$kehuId = (int)$request['id'];
	$channelId = (int)$request['channelId'];
	$keyword = (int)$request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	setcookie('baojiaPageNum',$pageNum,time()+3600*24*30);
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if($order1=='title'){
		$order1 = 'CONVERT(title USING gbk)';
	}
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql="select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId ";
	if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and channelId in($channelIds)";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or key_vals like '%$keyword%' or productId in($pdtIds) or code='$keyword')";
	}
	$count = $db->get_var(str_replace('id,sn,title,key_vals,productId','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$str = '{"code":0,"msg":"","count":'.$count.',"data":[';
	$pdtstr = '';
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$unitstr = $units[0]['title'];
			$price = getKehuPrice($pdt->id,$kehuId);
			$price = getXiaoshu($price,$product_set->price_num);
			$pdtstr.=',{"id":'.$pdt->id.',"productId":'.$pdt->productId.',"sn":"'.$pdt->sn.'","title":"'.$pdt->title.'","key_vals":"'.$pdt->key_vals.'","price":"'.$price.'","units":"'.$unitstr.'"}';
		}
		$pdtstr = substr($pdtstr,1);
	}
	$str .=$pdtstr.']}';
	echo $str;
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
function getJiamengList(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$keyword = (int)$request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	$sql="select * from demo_kehu_jiameng where comId=$comId";
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->beizhu);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getFankuis(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$isnew = (int)$request['isnew'];
	$keyword = (int)$request['keyword'];
	$page = (int)$request['page'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$pageNum = (int)$request["limit"];
	$sql="select * from demo_kehu_fankui where comId=$comId and del=0";
	if(!empty($isnew)){
		$isnew = $isnew%2;
		$sql.=" and isnew=$isnew";
	}
	if(!empty($keyword)){
		$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and title like '%$keyword%'");
		if(empty($kehuIds))$kehuIds='0';
		$sql.=" and kehuId in($kehuIds)";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by isnew desc,id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$k = $db->get_row("select title,name,phone,email from demo_kehu where id=$j->kehuId");
			$j->title = '<span onmouseover="tips(this,\''.$k->title.'&nbsp;&nbsp;'.$k->name.'<br>手机&nbsp;'.$k->phone.'<br>邮箱&nbsp;'.$k->email.'\',2);" onmouseout="hideTips()">'.$k->title.'</span>';
			$j->content = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->content);
			$j->content = str_replace('"','',$j->content);
			$j->content = str_replace("'",'',$j->content);
			$j->content = '<span onmouseover="tips(this,\''.$j->content.'\',1);" onmouseout="hideTips()" '.(($j->isnew==1&&$j->status==0)?'style="color:red;"':'').'>'.($j->status==1?'<span class="feedback-solved-tag">解</span>':'').sys_substr(strip_tags($j->content),40,true).'</span>';
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function getFankuiList(){
	global $db,$request;
	$id = (int)$request['id'];
	$result = $db->get_var("select results from demo_kehu_fankui where id=$id");
	$str = '';
	if(!empty($result)){
		$results = json_decode($result);
		foreach ($results as $r) {
			$r->content = str_replace('\n','<br>',$r->content);
			$str.='<div class="khfk_ljhuifu_02" '.($r->type==1?'':'style="margin-left:15px;"').'><div class="khfk_ljhuifu_02_left"><img src="images/biao_10'.($r->type==1?'2':'1').'.png"></div>
            		<div class="khfk_ljhuifu_02_right">
                		<h2>'.$r->name.'  '.$r->time.'</h2>'.$r->content.'<br>';
            if(!empty($r->images)){
            	$imgs = explode('|',$r->images);
            	foreach ($imgs as $i){
            		$str.='<a href="'.$i.'" target="_blank"><img src="'.$i.'?x-oss-process=image/resize,w_45"></a>';
            	}
            }
            $str.='</div>
            	<div class="clearBoth"></div>
            </div>';
		}
	}
	echo $str;
	exit;
}
function addFankui(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$kehu_fankui = $db->get_row("select id,results from demo_kehu_fankui where id=$id and comId=$comId");
	if(!empty($kehu_fankui)){
		$results = array();
		if(!empty($kehu_fankui->results)){
			$results = json_decode($kehu_fankui->results,true);
		}
		$fankui = array();
		$fankui['type'] = 2;
		$fankui['name'] = $_SESSION[TB_PREFIX.'name'];
		$fankui['time'] = date('Y-m-d H:i:s');
		$fankui['content'] =preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$request['content']);
		$fankui['images'] = $request['images'];
		$results[] = $fankui;
		$resultstr = json_encode($results,JSON_UNESCAPED_UNICODE);
		$db->query("update demo_kehu_fankui set isnew=0,results='$resultstr' where id=$id");
	}
	echo '{"code":1,"message":"成功"}';
	exit;
}