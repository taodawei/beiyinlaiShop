<?php
function index(){
	if(empty($_SESSION[TB_PREFIX.'user_ID'])){
		$url = urlencode('/index.php?'.$_SERVER["QUERY_STRING"]);
		redirect('/index.php?p=8&a=login&url='.$url);
	}
}
//中奖
function win(){
	global $db,$request;
	$id=(int)$_SESSION['price_id'];//奖项id
	$_SESSION['price_id'] = 0;
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$name = $request['name'];
	$phone = $request['phone'];
	/*$u = $db->get_row("select username,nickname from users where id=$userId");
	$name = $u->nickname;
	$phone = $u->username;*/
	//判断user_id与username是否匹配
	if($id!=0 || !empty($id)){
		//file_put_contents('gift.txt', $id.'-----',FILE_APPEND);
		//中奖记录
		$gift=$db->get_var("select name from demo_dazhuanpan_prize where id=".$id);//奖品
		$sql = "insert into demo_dazhuanpan_record (dazhuanpan_id,user_id,name,tel,prize,prizeName,dtTime) values ($dazhuanpan_id,$userId,'$name','$tel',$id,'$gift','".date('Y-m-d H:i:s')."')";//插入中奖记录
		$db->query($sql);
	}
	echo '{"code":1}';exit;
}
function jilu(){
	global $db,$request;
	$prize_id=(int)$_SESSION['price_id'];//奖项id
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$user_id = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$dtTime = date('Y-m-d');
	$dzp = $db->get_row("select per_type,per_jifen from demo_dazhuanpan where id=$dazhuanpan_id");
	$per_type = $dzp->per_type;
	if($per_type==2){
		$ifhas = $db->get_var("select id from demo_dazhuanpan_jilu where dazhuanpan_id=$dazhuanpan_id and user_id=$user_id and dtTime='$dtTime' limit 1");
		if(empty($ifhas)){
			$db->query("insert into demo_dazhuanpan_jilu(dazhuanpan_id,user_id,dtTime,nums,prize_id) values($dazhuanpan_id,$user_id,'$dtTime',1,$prize_id)");
		}else{
			$db->query("update demo_dazhuanpan_jilu set nums=nums+1 where id=$ifhas");
		}
	}else{
		$ifhas = $db->get_var("select id from demo_dazhuanpan_jilu where dazhuanpan_id=$dazhuanpan_id and user_id=$user_id limit 1");
		if(empty($ifhas)){
			$db->query("insert into demo_dazhuanpan_jilu(dazhuanpan_id,user_id,dtTime,nums,prize_id) values($dazhuanpan_id,$user_id,'$dtTime',1,$prize_id)");
		}else{
			$db->query("update demo_dazhuanpan_jilu set nums=nums+1 where id=$ifhas");
		}
	}
	if($dzp->per_jifen>0){
		$jifen = $dzp->per_jifen;
		if($comId==10){
			$db_service = getCrmDb();
			$db_service->query("update demo_user set jifen=jifen-$jifen where id=$userId");
			$return_jifen = $db_service->get_var("select jifen from demo_user where id=$userId");
		}else{
			$db_service = getCrmDb();
			$db->query("update users set jifen=jifen-$jifen where id=$userId");
			$return_jifen = $db->get_var("select jifen from users where id=$userId");
		}
		
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = $jifen;
		$jifen_jilu['yue'] = $return_jifen;
		$jifen_jilu['type'] = 2;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '大转盘抽奖';
		$fenbiao = getFenbiao($comId,20);
		$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
	}
	echo '{"code":1}';exit;
}
function get_rnd(){
	global $db,$request;
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$randstr = rand(0,10000);
	$prizes = $db->get_results("select * from demo_dazhuanpan_prize where dazhuanpan_id=$dazhuanpan_id and status=1 order by ordering desc limit 10");
	$i=1; //计数器
	$id = 1; //页面中奖项的id
	$prize_id = 0; //数据库中的奖项id
	$chance = 0;//上一奖项的概率
	if(!empty($prizes)){
		foreach($prizes as $prize1){
			$i++;
			if($prize1->num<=0){
				continue;
			}
			$randstr = $randstr-$chance;	//获得的随机数要减去上次的概率，会一直减
			$chance = $prize1->chance;
			if($randstr<=$chance){
				$id = $i;
				$prize_id = $prize1->id;
				$db->query("update demo_dazhuanpan_prize set num=num-1 where id=$prize_id");
				break;
			}
		}
	}
	$_SESSION['price_id'] = $prize_id;
	echo '{"code":1,"id":'.$id.'}';exit;
}
/*function reg(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$username = $request['phone'];
	$name = $request['name'];
	$sql="SELECT * FROM users WHERE comId=$comId and username='".$username."' limit 1";
	$rst = $db->get_row($sql);
	if(!empty($rst)){
		$_SESSION[TB_PREFIX.'user_name'] = $username;
		$_SESSION[TB_PREFIX.'user_level'] = $rst->level;
		$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
		$_SESSION[TB_PREFIX.'zhishangId'] = $rst->zhishangId;
		echo '{"code":1,"message":"ok"}';
		exit;
	}
	$password = '0';
	$level = (int)$db->get_var("select id from user_level where comId=$comId order by id asc limit 1");
	$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status) value($comId,'$name','$username','$password',0,0,$level,'".date("Y-m-d H:i:s")."',1)");
	$userId = $db->get_var("select last_insert_id();");
	$_SESSION[TB_PREFIX.'user_name'] = $username;
	$_SESSION[TB_PREFIX.'user_level'] = $level;
	$_SESSION[TB_PREFIX.'user_ID'] = $userId;
	$_SESSION[TB_PREFIX.'zhishangId'] = $zhishangId;
	$verify = substr(md5($username),5,5);
	$url = "http://buy.zhishangez.com/index.php?p=8&a=reg_wailai&phone=$username&name=$name&verify=$verify";
	file_get_contents($url);
	echo '{"code":1,"message":"success"}';
	exit;
}*/