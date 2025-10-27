<?php
namespace Zhishang;
class Users{
    
    public function myselfComments()
	{
        global $db,$request,$comId;
        
        $fenbiao = getFenbiao($comId,20);
        $userId = $request['user_id'];
        // $userId = 469;
        $page = (int)$request['page'];
        $star = (int)$request['star'];
        $pageNum = (int)$request['pagenum'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $sql = "select * from order_comment$fenbiao where userId=$userId and (status=2 or status = 1)";
        
        if(!empty($star)){
			switch ($star) {
				case 1:
					$sql .=" and star>3 ";
				break;
				case 2:
					$sql .=" and star=3 ";
				break;
				case 3:
					$sql .=" and star<3 ";
				break;
			}
		}
  
        $counts = $db->get_var(str_replace('*','count(*)',$sql));
        $sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
        $jilus = $db->get_results($sql);
    

        $return['code'] = 1;
        $return['message'] = '请求数据成功';
        $return['data'] = array();
        $return['data']['count'] = $counts;
        $return['data']['pages'] = ceil($counts/$pageNum);
        $return['data']['list'] = [];
        //$db_service = get_crm_db();
        if(!empty($jilus)){
            foreach ($jilus as $i=>$j) {
                $pingjia = array();
  
                $u = $db->get_row("select nickname,image from users where id=$j->userId");
           
                $pingjia['id'] = $j->id;
                $pingjia['touxiang'] = $u->image;
                $pingjia['username'] = $j->name;
                $pingjia['dtTime'] = date("Y-m-d H:i",strtotime($j->dtTime1));
                $j->cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->cont1);
                $pingjia['content'] = $j->cont1;
                $pingjia['imgs'] = empty($j->images1)?array():explode('|',$j->images1);
                $pingjia['reply'] = '';
                if(!empty($j->reply)){
                    $j->reply = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->reply);
                    $pingjia['reply'] = '掌柜回复：'.$j->reply;
                }
                $pingjia['star'] = $j->star;
                $guige = $db->get_row("select * from demo_product_inventory where id=$j->inventoryId");
                $pingjia['key_vals'] = $guige->key_vals;
                $pingjia['title'] = $guige->title;
                $pingjia['price_sale'] = $guige->price_sale;
                $pingjia['guige_img'] = $guige->image;
                $pingjia['inventory_id'] = $j->inventoryId;
                $pingjia['star1'] = $j->star1;
                $pingjia['star2'] = $j->star2;
                $pingjia['anonymous'] = $j->anonymous;
                $pingjia['pdtNum'] = (int)$db->get_var("select num from order_detail$fenbiao where orderId =".$j->orderId." and inventoryId =".$j->inventoryId);
                $return['data']['list'][] = $pingjia;
            }
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
	public function index()
	{
		global $db,$request,$comId;
		
		$userId = (int)$request['user_id'];
		$return = array();
		$return['code'] = 1;
		$return['data'] = array();
		$now = date('Y-m-d H:i:s');
		$fenbiao = getFenbiao($comId,20);
		$num8 = 0;

		//待支付
		$num1 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=-5 and pay_endtime>'$now'");
		//待发货
		$num2 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=2");
		//待收货
		$num3 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=3");
		//待评价
		$num4 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=3 and status=4 and ifpingjia=0");
		//售后
		$num5 = (int)$db->get_var("select count(*) from order_tuihuan where comId=$comId and userId=$userId and status>-1 and status<6");
		//优惠券数量
		$num6 = (int)$db->get_var("select count(*) from  user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
		
		$order_num_6 = (int)$db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and status=1");
		
		//未读消息
		if($_SESSION['if_tongbu']==1){
			$comId = 10;
			$userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
		}
		$myMsgId = (int)$db->get_var("select msgId from user_msg_read where userId=$userId and comId=$comId");
		$num7 = (int)$db->get_var("select count(*) from user_msg$fenbiao where id>$myMsgId and comId=$comId and userId=$userId");
		$num9 = $db->get_var("select sum(yue) from lipinka where comId=10 and userId=$userId and (endTime>'".date("Y-m-d H:i:s")."' or endTime is null)");
		$fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
		$return['data']['order_num_1'] = $num1;
		$return['data']['order_num_2'] = $num2;
		$return['data']['order_num_3'] = $num3;
		$return['data']['order_num_4'] = $num4;
		$return['data']['order_num_5'] = $num5;
		$return['data']['order_num_6'] = $order_num_6;
		$return['data']['yhq_num'] = $num6;
		$return['data']['msg_num'] = $num7;
		$return['data']['fanli_type'] = $fanli_type;
		/*$return['data'][] = empty($num8)?0:$num8;
		$return['data'][] = empty($num9)?0:$num9;*/
		//$return['data'] = array(1,2,3,4);
		return json_encode($return);
	}
	
	public function myYhqList()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$scene = (int)$request['scene'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=20;
		$sql = "select * from user_yhq$fenbiao where comId=$comId and userId=$userId ";
		switch ($scene){
			case 1:
				$sql.=" and status=0 and endTime>'".date("Y-m-d H:i:s")."'";
			break;
			case 2:
				$sql.=" and status=1";
			break;
			case 3:
				$sql.=" and status=0 and endTime<'".date("Y-m-d H:i:s")."'";
			break;
		}
		$sql.=' order by id desc ';
	    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('*','count(id)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
	    if($res){
	    	$now = time();
		    foreach ($res as $key) {
		      	$tiaojian = '通用';
		      	$yhq=$db->get_row("select mendianIds,channelNames,pdtNames,color,useType,content from yhq where id=$key->jiluId");
		      	if(!empty($yhq->mendianIds)){
		      		$shop_name = $db->get_var("select com_title from demo_shezhi where comId=$yhq->mendianIds");
		      		$tiaojian = '仅限购买'.$shop_name.'指定商品购买';
		      	}elseif($yhq->useType>1){
					$tiaojian = '仅限'.$yhq->channelNames;
					if(!empty($yhq->pdtNames)){
						$tiaojian.=empty($tiaojian)?$yhq->pdtNames:','.$yhq->pdtNames;
					}
				}
				$key->tiaojian = sys_substr($tiaojian,30,true);
				$key->image = '';
				if($key->status==0){
					$endTime = strtotime($key->endTime);
					if($now>$endTime){
						$key->status = 2;
					}else{
						if($endTime-$now<259200){
							$key->image = 'a928_1';
						}else if(date("Y-m-d",strtotime($key->dtTime))==date("Y-m-d")){
							$key->image = 'a928_11';
						}
					}
				}
		      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
		      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
		      	$key->man = floatval($key->man);
		      	$key->jian = floatval($key->jian);
		      	$key->color = $yhq->color;
		      	$key->content = $yhq->content;
		      	$return['data'][] = $key;
		    }
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function teamInfo()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$shezhi = $db->get_row("select tuanzhang_rule,fanli_type,if_shequ_tuan from demo_shezhi where comId=$comId");
		$tuanzhang_rule = $shezhi->tuanzhang_rule;
		$rule = array();
		if(!empty($tuanzhang_rule)){
		    $rule = json_decode($tuanzhang_rule,true);
		}
		if($comId==10){
		    $db_service = get_zhishang_db();
		    $userNums = (int)$db_service->get_var("select count(*) from demo_user where shangji=$userId");
		    $u = $db_service->get_row("select earn,name,image,if_tuanzhang,tuan_id from demo_user where id=$userId");
		    $earn = $u->earn;
		    $uname = $u->name;
		}else{
		    $userNums = (int)$db->get_var("select count(*) from users where shangji=$userId");
		    $u = $db->get_row("select earn,nickname,image,if_tuanzhang,tuan_id from users where id=$userId");
		    $earn = $u->earn;
		    $uname = $u->nickname;
		}

		$shengji = 1;$msg = '';
		$rule['yaoqing_num'] = empty($rule['yaoqing_num'])?0:$rule['yaoqing_num'];
		$rule['yaoqing_yongjin'] = empty($rule['yaoqing_yongjin'])?0:$rule['yaoqing_yongjin'];
		if($userNums<$rule['yaoqing_num']){
		    $shengji = 0;
		    $msg = '邀请人数不足'.$rule['yaoqing_num'].'，不能升级成为团长';
		}else if($earn<$rule['yaoqing_yongjin']){
		    $shengji = 0;
		    $msg = '佣金不足'.$rule['yaoqing_yongjin'].'，不能升级成为团长';
		}
		if($u->if_tuanzhang){
		  $level = '团长';
		}else if(empty($u->level)){
		  $level = $comId==10?'小白购':'会员';
		}else{
		  $level = $db->get_var("select title from user_level where id=$user->level");
		}
		if(!empty($u->image) && substr($u->image,0,4)!='http')$u->image='http://www.zhishangez.com'.$u->image;
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['name'] = $uname;
		$return['data']['image'] = $u->image;
		$return['data']['level'] = $level;
		$return['data']['if_open_tuanzhang'] = $shezhi->fanli_type==2?1:0;
		$return['data']['tuanzhang_info'] = array();
		if($shezhi->fanli_type==2){
			$return['data']['tuanzhang_info']['yaoqing_num'] = empty($rule['yaoqing_num'])?0:$rule['yaoqing_num'];
			$return['data']['tuanzhang_info']['yaoqing_yongjin'] = empty($rule['yaoqing_yongjin'])?0:$rule['yaoqing_yongjin'];
			$return['data']['tuanzhang_info']['my_yaoqing_num'] = $userNums;
			$return['data']['tuanzhang_info']['my_yaoqing_yongjin'] = $earn;
			$return['data']['tuanzhang_info']['if_shenqing'] = $shengji;
			$return['data']['tuanzhang_info']['reason'] = $msg;
		}
		$return['data']['if_open_shequ'] = $shezhi->if_shequ_tuan==1?1:0;
		$return['data']['myTuanzhang'] = array();
		if($comId==10){
            $tuanzhang = $db_service->get_row("select name as nickname,weixin_name,image,user_info from demo_user where id=$u->tuan_id");
        }else{
            $tuanzhang = $db->get_row("select nickname,weixin_name,image,user_info from users where id=$u->tuan_id");
        }
        if(!empty($tuanzhang->user_info)){
            $user_info = json_decode($tuanzhang->user_info,true);
            //$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg&tuijianren='.$userId;
            if(!empty($tuanzhang->image) && substr($tuanzhang->image,0,4)!='http')$tuanzhang->image='http://www.zhishangez.com'.$tuanzhang->image;
            $return['data']['myTuanzhang']['name'] = $tuanzhang->nickname;
            $return['data']['myTuanzhang']['image'] = $tuanzhang->image;
            $return['data']['myTuanzhang']['wx_img'] = $user_info['wx_img'];
            $return['data']['myTuanzhang']['wxh'] = $user_info['wxh'];
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function applyTuanzhang()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$wxh = $request['wxh'];
		$wx_img = $request['wx_img'];
		if($comId==10){
			$db_service = get_zhishang_db();
			$if_tuan = $db_service->get_var("select if_tuanzhang from demo_user where id=$userId");
		}else{
			$if_tuan = $db->get_var("select if_tuanzhang from users where id=$userId");
		}
		if($if_tuan==1){
			return '{"code":0,"message":"您已经是团长了，请不要重复申请"}';
		}else if($if_tuan==-1){
			return '{"code":0,"message":"您被管理员撤销了团长，不能再次申请"}';
		}
		$tuanzhang_rule = $db->get_var("select tuanzhang_rule from demo_shezhi where comId=$comId");
		$rule = array();
		if(!empty($tuanzhang_rule)){
		    $rule = json_decode($tuanzhang_rule,true);
		}
		$rule['yaoqing_num'] = empty($rule['yaoqing_num'])?0:$rule['yaoqing_num'];
		$rule['yaoqing_yongjin'] = empty($rule['yaoqing_yongjin'])?0:$rule['yaoqing_yongjin'];
		if($comId==10){
			$userNums = (int)$db_service->get_var("select count(*) from demo_user where comId=$comId and shangji=$userId");
			$u = $db_service->get_row("select earn,tuan_id from demo_user where id=$userId");
			$earn = $u->earn;
		}else{
			$userNums = (int)$db->get_var("select count(*) from users where comId=$comId and shangji=$userId");
			$u = $db->get_row("select earn,tuan_id from users where id=$userId");
			$earn = $u->earn;
		}
		if($userNums<$rule['yaoqing_num']){
		    return '{"code":0,"message":"邀请人数不足'.$rule['yaoqing_num'].'，不能升级成为团长"}';
		}else if($earn<$rule['yaoqing_yongjin']){
		    return '{"code":0,"message":"佣金不足'.$rule['yaoqing_yongjin'].'，不能升级成为团长"}';
		}
		$user_info = array();
		if($comId==10){
			$user_info_str = $db_service->get_var("select user_info from demo_user where id=$userId");
			if(!empty($user_info_str)){
				$user_info = json_decode($user_info_str,true);				
			}
			$user_info['wxh'] = $wxh;
			$user_info['wx_img'] = $wx_img;
			$db_service->query("update demo_user set if_tuanzhang=1,user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
		}else{
			$user_info_str = $db->get_var("select user_info from users where id=$userId");
			if(!empty($user_info_str)){
				$user_info = json_decode($user_info_str,true);				
			}
			$user_info['wxh'] = $wxh;
			$user_info['wx_img'] = $wx_img;
			$db->query("update users set if_tuanzhang=1,user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
		}
		self::update_user_tuanid($userId,$u->tuan_id,$userId);
		return '{"code":1,"message":"申请成功"}';
	}
	
	public static function update_user_tuanid($uid,$old_tuanid,$new_tuanid)
	{
		global $db,$comId;
		if($comId==10){
			$db_service = get_zhishang_db();
			$db_service->query("update demo_user set tuan_id=$new_tuanid where id=$uid and tuan_id=$old_tuanid");
			self::add_user_oprate('所属团队由'.$old_tuanid.'变更为'.$new_tuanid,2,$uid);
			$xiajistr = $db_service->get_var("select group_concat(id) from demo_user where shangji=$uid and tuan_id=$old_tuanid");
			if(!empty($xiajistr)){
				$xiajis = explode(',',$xiajistr);
				foreach ($xiajis as $userid) {
					self::update_user_tuanid($userid,$old_tuanid,$new_tuanid);
				}
			}
		}else{
			$db->query("update users set tuan_id=$new_tuanid where id=$uid and tuan_id=$old_tuanid");
			self::add_user_oprate('所属团队由'.$old_tuanid.'变更为'.$new_tuanid,2,$uid);
			$xiajistr = $db->get_var("select group_concat(id) from users where comId=$comId and shangji=$uid and tuan_id=$old_tuanid");
			if(!empty($xiajistr)){
				$xiajis = explode(',',$xiajistr);
				foreach ($xiajis as $userid) {
					self::update_user_tuanid($userid,$old_tuanid,$new_tuanid);
				}
			}
		}
	}
	
	public static function add_user_oprate($content,$type,$uid=0)
	{
		global $db,$comId;
		$user_oprate = array();
		$user_oprate['comId'] = $comId;
		$user_oprate['userId'] = $uid;
		$user_oprate['dtTime'] = date("Y-m-d H:i:s");
		$user_oprate['ip'] = getip();
		$user_oprate['terminal'] = 2;
		$user_oprate['content'] = $content;
		$user_oprate['type'] = $type;
		$fenbiao = getFenbiao($comId,20);
		$db->insert_update('user_oprate'.$fenbiao,$user_oprate,'id');
	}
	
	public function yhqList()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$scene = (int)$request['scene'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=20;
		$u = $db->get_row("select level,areaId from users where id=$userId");
		$level = $u->level;
		$areaId = $u->areaId;
		$fenbiao = getFenbiao($comId,20);
		$sql = "select * from yhq where comId=$comId and endTime>'".date("Y-m-d H:i:s")."' and num>hasnum and status=1 and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds)) ";
		$res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
		$count = $db->get_var(str_replace('*','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		$today = date("Y-m-d");
	    if($res){
	      foreach ($res as $key) {
	      	if($key->num_day>0){
	      		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$key->id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
	      		if($hasNum>=$key->num_day)continue;
	      	}
	      	$tiaojian = '通用';
			if(!empty($key->mendianIds)){
	      		$shop_name = $db->get_var("select com_title from demo_shezhi where comId=$key->mendianIds");
	      		$tiaojian = '仅限购买'.$shop_name.'指定商品购买';
	      	}elseif(!empty($key->channels) || !empty($key->pdts)){
				$tiaojian = '仅限'.$key->channelNames;
				if(!empty($key->pdtNames)){
					$tiaojian.=empty($tiaojian)?$key->pdtNames:','.$key->pdtNames;
				}
			}
			$key->tiaojian = sys_substr($tiaojian,22,true);
	      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
	      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
	      	$key->man = floatval($key->man);
		    $key->money = floatval($key->money);
		    $lingqu_num = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id");
		    if($lingqu_num>0){
		    	$key->lingqu_id = $db->get_var("select id from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id limit 1");
		    }
		    $key->if_lingqu = $lingqu_num>0?1:0;
		    $key->if_ke_lingqu =1;
		    if(!empty($key->numlimit)){
		         $key->if_ke_lingqu = $lingqu_num<$key->numlimit?1:0;  
		    }
		 
		    $key->width = intval($key->hasnum*10000/$key->num)/100;
	      	$return['data'][] = $key;
	      }
	  	}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function yhqLingqu()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$id = (int)$request['id'];
		$fenbiao = getFenbiao($comId,20);
		$yhq = $db->get_row("select * from yhq where id=$id and comId=$comId and status=1");
		if(empty($yhq)){
			return '{"code":0,"message":"优惠券已过期不存在"}';
		}
		if($yhq->hasNum>=$yhq->num){
			return '{"code":0,"message":"优惠券已被抢光了"}';
		}
		if($yhq->num_day>0){
			$today = date("Y-m-d");
	  		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
	  		if($hasNum>=$yhq->num_day){
	  			return '{"code":0,"message":"今日领取已达上限，请明天再领"}';
	  		}
	  	}
	  	if($yhq->numlimit>0){
	  		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$id");
	  		if($hasNum>=$yhq->numlimit){
	  			return '{"code":0,"message":"您已经领过该券了~~"}';
	  		}
	  	}
	  	$user_yhq = array();
	  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
	  	$user_yhq['comId'] = $comId;
	  	$user_yhq['userId'] = $userId;
	  	$user_yhq['jiluId'] = $id;
	  	$user_yhq['fafangId'] = 0;
	  	$user_yhq['title'] = $yhq->title;
	  	$user_yhq['man'] = $yhq->man;
	  	$user_yhq['originalPic'] = $yhq->originalPic;
	  	$user_yhq['jian'] = $yhq->money;
	  	$user_yhq['startTime'] = $yhq->startTime;
	  	$user_yhq['endTime'] = $yhq->endTime;
	  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
	  	$db->insert_update('user_yhq'.$fenbiao,$user_yhq,'id');
	  	$yhq_id = $db->get_var("select last_insert_id();");
	  	//todo 增加领取记录表
	  	// $hadLog = $db->get_row("select * from yhq_fafang where userIds");
	  	
	  	$db->query("update yhq set hasnum=hasnum+1 where id=$id");
	  	return '{"code":1,"message":"领取成功","yhq_id":'.$yhq_id.'}';
	}
	
	public function login()
	{
		global $request,$comId;
		$db_service = get_zhishang_db();
		$phone = $request['phone'];
		$pass=$request['pass'];
		$return = array();
		$return['code'] = 0;
		if(empty($phone) || empty($pass)){
			$return['message'] = "账号或密码不能为空";
			return json_encode($return,JSON_UNESCAPED_UNICODE);
		}
		$sql="SELECT id,pwd,department,name,auditing FROM demo_user WHERE username='".$phone."' and comId=$comId limit 1";
		$rst=$db_service->get_row($sql);
		$str = "";
		if($rst)
		{
			require_once(ABSPATH.'/inc/class.shlencryption.php');
			$shlencryption = new \shlEncryption($pass);
			if($rst->auditing==0){
				$return['message'] = "该账号已经被禁止登陆";
			}else if($rst->auditing==-1){
				$return['message'] = "您的账号管理员尚未通过审核，请联系管理员";
			}else if ($rst->pwd==$shlencryption->to_string() || $rst->pwd==sha1($pass)){
				//$this->zhuce_jiangli($rst->id,10);
				$lastlogin = time();
				$token = substr(md5($comId.$rst->id.$lastlogin),5,10);
				$db_service->query("update demo_user set lastlogin='$lastlogin',token='$token',tokenTime='$token' where id=$rst->id"); 
				global $db;
				$qxs = $db->get_results("select model,group_concat(functions) as functions,group_concat(storeIds) as storeIds from demo_quanxian where comId=$comId and(find_in_set($rst->id,userIds) or find_in_set($rst->department,departs)) group by model");
				if(!empty($qxs)){
					$functions = '';
					$storeIds = '';
					foreach ($qxs as $q){
						if(!empty($q->functions) && $q->model=='dinghuo'){
							$functions .= ','.$q->functions;
						}
						if(!empty($q->storeIds)){
							$storeIds .= ','.$q->storeIds;
						}
					}
					$fun_array = array();
					if(!empty($functions))$fun_array=explode(',',substr($functions,1));
					if(empty($storeIds) || !in_array('add',$fun_array)){
						$return['message'] = "您没有权限，请先联系管理员为您分配仓库和订货权限";
					}else{
						$return['code'] = 1;
						$return['message'] = '成功';
						$return['user_id'] = $rst->id;
						$return['name'] = $rst->name;
						$return['token'] = $token;
					}
				}else{
					$return['message'] = "您没有权限，请先联系管理员为您分配仓库和订货权限";
				}
			}else{
				$return['message'] = "密码不正确";
			}
		}else{
			$return['message'] = "帐号不存在或未激活";
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function codeLogin(){
		global $request,$db,$comId;
		
		$fenbiao = getFenbiao($comId,20);
		$code = $request['nonce_str'];
		$return = array();
		$return['code'] = 0;
		if(empty($code)){
			$return['message'] = "code不能为空";
			return json_encode($return,JSON_UNESCAPED_UNICODE);
		}
	
  		$rst = $db->get_row("select * FROM users WHERE qrcode='$code' and comId=$comId order by id asc LIMIT 1");
  		if(!$rst){
  		    $return['message'] = "还未扫码成功";
			return json_encode($return,JSON_UNESCAPED_UNICODE);
  		}
  		$dtTime = time();
		$applet_info = json_encode(array("session_key"=>$session_key,"openid"=>$openid),JSON_UNESCAPED_UNICODE);
        $lastlogin = date('Y-m-d H:i:s',$dtTime);
		
		$token = empty($rst->token)?substr(md5($comId.$rst->id.time()),5,10):$rst->token;
		$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin',applet_info='$applet_info',nickname='$weixin_name',image='$avatarurl',qrcode='' where id=$rst->id");
	    $userId = $rst->id;
		
		$return['code'] = 1;
		$return['message'] = '登录成功';
		$return['data'] = array();
		$return['data']['user_id'] = $userId;
		$return['data']['token'] = $token;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function wxLogin(){
		global $request,$db,$comId;
		
		$fenbiao = getFenbiao($comId,20);
		$code = $request['code'];
		$tuijianren = (int)$request['invite_id'];
		$return = array();
		$return['code'] = 0;
		if(empty($code)){
			$return['message'] = "code不能为空";
			return json_encode($return,JSON_UNESCAPED_UNICODE);
		}
		//获取微信小程序的配置，注意不是公众号的
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
		if(empty($weixin_set)||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置有误，无法登录"}';
		}
		$weixin_arr = json_decode($weixin_set->info);
		$appid = $weixin_arr->appid;
		$appsecret = $weixin_arr->appsecret;
		$scope = 'snsapi_userinfo';
		
		//$token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
  		//$token_info = $this->https_request($token_url);
  		$avatarurl = $weixin_name = '';
  	    if(empty($_SESSION[$code])){
	        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
      		$token_info = $this->https_request($token_url);
      		$session_key = $token_info['session_key'];
      		$openid = $token_info['openid'];
      		$unionid = $token_info['unionid'];
      	
      		$access_token = $token_info['access_token'];
      		if(empty($openid)){
      			return '{"code":0,"message":"获取不到用户的openid，请联系技术人员"}';
      		}
      		$api = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
            $data = $this->https_request($api);
            $avatarurl = $data['headimgurl'];
            $weixin_name = filterEmoji($data['nickname']);
            $_SESSION[$code] = $openid;
	    }else{
	        $openid = $_SESSION[$code];
	    }

  		if(empty($openid)){
  			return '{"code":0,"message":"获取不到用户的openid，请联系技术人员，错误原因：'.$token_info['errmsg'].'"}';
  		}
  		$rst = $db->get_row("select * FROM users WHERE openId='$openid' and comId=$comId order by id asc LIMIT 1");
  		$dtTime = time();
		$applet_info = json_encode(array("session_key"=>$session_key,"openid"=>$openid),JSON_UNESCAPED_UNICODE);
        $lastlogin = date('Y-m-d H:i:s',$dtTime);
		if($rst){
			$token = empty($rst->token)?substr(md5($comId.$rst->id.time()),5,10):$rst->token;
			$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin',applet_info='$applet_info',nickname='$weixin_name',image='$avatarurl' where id=$rst->id");
		    $userId = $rst->id;
		}else{
			$username = $openid;
			$areaId = $shangji = $city =$shangshangji = $tuan_id = 0;
			$password = rand(111111,999999);
			$level_row = $db->get_row("select id,title from user_level where comId=$comId order by id asc limit 1");
			$level = (int)$level_row->id;
			if(!empty($tuijianren)){
				$shangji = $tuijianren;
				$shangshangji = (int)$db->get_var("select shangji from users where id=$tuijianren");
				$tuan_id = (int)$db->get_var("select tuan_id from users where id=$tuijianren");
			}
			$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,unionid,applet_info,shangji,shangshangji,tuan_id,image) value($comId,'$weixin_name','$username','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'$openid','','$applet_info',$shangji,$shangshangji,$tuan_id,'$avatarurl')");
			$userId = $db->get_var("select last_insert_id();");

			$token = substr(md5($comId.$userId.time()),5,10);
			$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin' where id=$userId");
		}
		
		$return['code'] = 1;
		$return['message'] = '登录成功';
		$return['data'] = array();
		$return['data']['user_id'] = $userId;
		$return['data']['token'] = $token;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function miniLogin(){
		global $request,$db,$comId;
		
		$fenbiao = getFenbiao($comId,20);
		$code = $request['code'];
		$nickname = $request['nickname'];
		$avatarurl = $request['avatarurl'];
		$tuijianren = (int)$request['invite_id'];
		$return = array();
		$return['code'] = 0;
		if(empty($code)){
			$return['message'] = "code不能为空";
			return json_encode($return,JSON_UNESCAPED_UNICODE);
		}
		//获取微信小程序的配置，注意不是公众号的
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 limit 1");
		if(empty($weixin_set)||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置有误，无法登录"}';
		}
		$weixin_arr = json_decode($weixin_set->info);
		$appid = $weixin_arr->appid;
		$appsecret = $weixin_arr->appsecret;
		$scope = 'snsapi_userinfo';
		$token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
  		$token_info = $this->https_request($token_url);

  		$session_key = $token_info['session_key'];
  		$openid = $token_info['openid'];
  		$unionid = $token_info['unionid'];

  		if(empty($openid)){
  			return '{"code":0,"message":"获取不到用户的openid，请联系技术人员，错误原因：'.$token_info['errmsg'].'"}';
  		}
  		// echo 111;die;
  		$rst = $db->get_row("select * FROM users WHERE mini_openId='$openid' and comId=$comId order by id asc LIMIT 1");
  		$dtTime = time();
  		$weixin_name = $nickname ? $nickname :'用户8'.time();
		$applet_info = json_encode(array("session_key"=>$session_key,"openid"=>$openid),JSON_UNESCAPED_UNICODE);
		if($rst){
			$lastlogin = date('Y-m-d H:i:s',$dtTime);
			$token = empty($rst->token)?substr(md5($comId.$rst->id.time()),5,10):$rst->token;
			$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin',applet_info='$applet_info' where id=$rst->id");
		    $userId = $rst->id;
		}else{
			$username = $openid;
			$areaId = $tuan_id = $shangshangji = $shangji = $city = 0;
			$password = rand(111111,999999);
			$level_row = $db->get_row("select id,title from user_level where comId=$comId order by id asc limit 1");
			$level = (int)$level_row->id;
			if(!empty($tuijianren)){
				$shangji = $tuijianren;
				$shangshangji = (int)$db->get_var("select shangji from users where id=$tuijianren");
				$tuan_id = (int)$db->get_var("select tuan_id from users where id=$tuijianren");
			}
			$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,mini_openId,applet_info,shangji,shangshangji,tuan_id,image) value($comId,'$weixin_name','$username','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'','$openid','$applet_info',$shangji,$shangshangji,$tuan_id,'$avatarurl')");
			$userId = $db->get_var("select last_insert_id();");

			$token = substr(md5($comId.$userId.time()),5,10);
			$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin' where id=$userId");
		}
		
		$now = date('Y-m-d H:i:s');
		$db->query("update users set lastLogin = '$now' where id = $userId");
		$return['code'] = 1;
		$return['message'] = '登录成功';
		$return['data'] = array();
		$return['data']['user_id'] = $userId;
		$return['data']['token'] = $token;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
    
	
	public function wxRegist()
	{
		global $request,$db,$comId;
		
		$userId = $request['user_id'];
		$nickname = filtergl($request['nickname']);
		$avatarurl = $request['avatarurl'];
		$province=$request['province'];
		$city=$request['city'];
		if(empty($nickname) || empty($avatarurl)){
			return '{"code":0,"message":"头像和昵称不能为空"}';
		}
		if($comId==10){
			$db_service = get_zhishang_db();
			$db_service->query("update demo_user set name='$nickname',image='$avatarurl' where id=$userId");
		}else{
			$provinceId=$db->get_var("select id from demo_area where parentId=0 and title like'%".$province."%'");
      		$cityId=$db->get_var("select id from demo_area where parentId=$provinceId and title like'%".$city."%'");
      		if(empty($cityId)){
      		    $idk=$db->get_var("select id from demo_area where parentId=$provinceId");
      		    $cityId=$db->get_var("select id from demo_area where parentId=$idk and title like'%".$city."%'");
      		}
			$db->query("update users set province=$provinceId,city=$cityId,nickname='$nickname',image='$avatarurl' where id=$userId");
		}
		$return['code'] = 1;
		$return['message'] = '成功';
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function jifenInfo(){
		global $request,$db,$comId;
		$userId = $request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		if($comId==10){
			$db_service = get_zhishang_db();
			$zongjifen = $db_service->get_var("select jifen from demo_user where id=$userId");
		}else{
			$zongjifen = $db->get_var("select jifen from users where id=$userId");
		}
		$jifens = $db->get_results("select sum(jifen) as jifen,type from user_jifen$fenbiao where userId=$userId and comId=$comId group by type");
		$jifen1 = 0;
		$jifen2 = 0;
		if(!empty($jifens)){
		  foreach ($jifens as $j) {
		    if($j->type==1){
		      $jifen1 = $j->jifen;
		    }else{
		      $jifen2=$j->jifen;
		    }
		  }
		}
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['total_jifen'] = $zongjifen;
		$return['data']['total_earn'] = (int)$jifen1;
		$return['data']['total_cost'] = (int)$jifen2;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function yueRecord()
	{
		global $request,$db,$comId;
		$userId = $request['user_id'];
		$page = (int)$request['page'];
		$type = (int)$request['type'];
		
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$yzFenbiao = getFenbiao($comId,20);
		$where = '';
		if($type){
		    $where = ' and type='.$type;
		}
		
		if($ifQian){
		    $where = ' and remark= \'签到\'';
		}
		
	    $zong_money = $db->get_var("select wx_money from users where id=$userId and comId=$comId");
        $sql = "select money,yue,type,dtTime,remark,orderInfo,order_id from user_liushui$yzFenbiao where userId=$userId and comId=$comId and cardId = 0 ";

		if($type>0){
			$sql.=" and type=$type";
		}

	    $res = $db->get_results($sql." order by id desc limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('money,yue,type,dtTime,remark,orderInfo','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['count'] = $count;
		$return['zong_money'] = $zong_money;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		
	    if($res){
	      foreach ($res as $key) {
	      	$return['data'][] = $key;
	      }
	  	}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function jifenRecord()
	{
		global $request,$db,$comId;
		$userId = $request['user_id'];
		$page = (int)$request['page'];
		$type = (int)$request['type'];
		$ifQian = (int)$request['if_qiandao'];
		
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$yzFenbiao = getFenbiao($comId,20);
		$where = '';
		if($type){
		    $where = ' and type='.$type;
		}
		
		if($ifQian){
		    $where = ' and remark= \'签到\'';
		}
		
		$sql = "select id,jifen,yue,type,dtTime,remark,orderId,oid from user_jifen$yzFenbiao where userId=$userId and comId=$comId $where order by id desc ";
	    $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('id,jifen,yue,type,dtTime,remark,orderId,oid','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
	    if($res){
	      foreach ($res as $key) {
	          $key->product_info = null;
	          if($key->oid > 0){
	              $order = $db->get_row("select orderId,product_json from order8 where id = $key->oid");
	              if($order){
	                  $key->orderId = $order->orderId;
	                  $key->product_info = json_decode($order->product_json,true);
	              }
	          }
	          
	          $return['data'][] = $key;
	      }
	  	}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function moneyTotal()
	{
		global $db,$request,$comId;
		$fenbiao = getFenbiao($comId,20);
		$userId = (int)$request['user_id'];
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d",strtotime('-1 day'));
		$month = date("Y-m-01");//本月一号
		$lastmonth = date("Y-m-01",strtotime('-1 day',strtotime($month)));//上个月1号
		$todays = $db->get_row("select count(*) as num,sum(money) as yongjin from user_yugu_shouru where comId=$comId and userId=$userId and dtTime='$today'");
		$yesterdays = $db->get_row("select count(*) as num,sum(money) as yongjin from user_yugu_shouru where comId=$comId and userId=$userId and dtTime='$yesterday'");
		$today_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime='$today'");
		$yestday_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime='$yesterday'");
		$month_chengjiao = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$month'");
		$last_month_chengjiao = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$lastmonth' and dtTime<'$month'");
		$month_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime>='$month'");
		$last_month_queren = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and qrTime>='$lastmonth' and qrTime<'$month'");
		$fans_num = (int)$db->get_var("select count(*) from users where comId=$comId and shangji=$userId");
		$u = $db->get_row("select earn,money from users where id=$userId");
		$zong_yugu = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and status=0");
		$zong_tixian = $db->get_var("select sum(money) from user_liushui$fenbiao where comId=$comId and userId=$userId and type=3");
		$zong_tixian = empty($zong_tixian)?0:$zong_tixian;
		$zong_yugu = empty($zong_yugu)?0:$zong_yugu;
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['fans_num'] = $fans_num;
		$return['data']['total_earn'] = $u->earn;
		$return['data']['money'] = $u->money;
		$return['data']['total_yugu'] = $zong_yugu;
		$return['data']['total_tixian'] = $zong_tixian;
		$return['data']['today_orders'] = empty($todays->num)?'0':$todays->num;
		$return['data']['today_chengjiao'] = empty($todays->yongjin)?'0':$todays->yongjin;
		$return['data']['today_jiesuan'] = empty($today_queren)?'0':$today_queren;
		$return['data']['yestday_orders'] = empty($yesterdays->num)?'0':$yesterdays->num;
		$return['data']['yestday_chengjiao'] = empty($yesterdays->yongjin)?'0':$yesterdays->yongjin;
		$return['data']['yestday_jiesuan'] = empty($yestday_queren)?'0':$yestday_queren;
		$return['data']['month_chengjiao'] = empty($month_chengjiao)?'0':$month_chengjiao;
		$return['data']['month_jiesuan'] = empty($month_queren)?'0':$month_queren;
		$return['data']['last_month_chengjiao'] = empty($last_month_chengjiao)?'0':$last_month_chengjiao;
		$return['data']['last_month_jiesuan'] = empty($last_month_queren)?'0':$last_month_queren;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}

	public function moneyRecord(){
		global $request,$db,$comId;
		
		$userId = $request['user_id'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$type = (int)$request['type'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$yzFenbiao = getFenbiao($comId,20);
// 		$cardId = (int)$request['cardId'];
// 		$info = $db->get_row("select * from user_card where userId = $userId and id = $cardId");
//         if(!$info){
//     	    return '{"code":0,"message":"未找到储值卡信息"}';
//         }
        
        $zong_money = $db->get_var("select money from users where id=$userId and comId=$comId");
        //$sql = "select money,yue,type,dtTime,remark,orderInfo,order_id from user_liushui$yzFenbiao where userId=$userId and comId=$comId and cardId = $cardId ";
        $sql = "select money,yue,type,dtTime,remark,orderInfo,order_id from user_liushui$yzFenbiao where userId=$userId and comId=$comId ";
		if($type>0){
			$sql.=" and type=$type";
		}

	    $res = $db->get_results($sql." order by id desc limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('money,yue,type,dtTime,remark,orderInfo','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = $res;
// 		$return['data'] = array(
// 		    'info' => $info,
// 		    'list' => []
// 		);
	   // if($res){
	   //   foreach ($res as $key) {
	   //   	$return['data']['list'][] = $key;
	   //   }
	  	// }
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * 佣金记录
	*/
	public function yongjinRecord(){
		global $request,$db,$comId;
		
		$userId = $request['user_id'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$type = (int)$request['type'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$yzFenbiao = getFenbiao($comId,20);

		$zong_money = $db->get_var("select yongjin from users where id=$userId and comId=$comId");
		$sql = "select money,yue,type,dtTime,remark,orderInfo,order_id from user_yongjin$yzFenbiao where userId=$userId and comId=$comId";
		
		if($type>0){
			$sql.=" and type=$type";
		}
		//$sql = "select jifen,yue,type,dtTime,remark from user_jifen$yzFenbiao where userId=$userId and comId=$comId order by id desc ";
	    $res = $db->get_results($sql." order by id desc limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('money,yue,type,dtTime,remark,orderInfo','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '数据获取成功';
		$return['zong_money'] = $zong_money;
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
        if($res){
            foreach ($res as $key) {
                $return['data'][] = $key;
            }
        }
	  	
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}

	public function moneyOrders(){
		global $request,$db,$comId;
		$userId = $request['user_id'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$keyword = $request['keyword'];
		$scene = (int)$request['scene'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$sql="select order_type,orderId,money,dtTime,status,order_comId from user_yugu_shouru where comId=$comId and userId=$userId";
		switch ($scene) {
			case 1:
				$sql.=" and status=0";
			break;
			case 2:
				$sql.=" and status=1";
			break;
			case 3:
				$sql.=" and status=-1";
			break;
		}
		if(!empty($keyword)){
			if($comId==10){
				$db_service = get_zhishang_db();
				$userIds = $db_service->get_var("select group_concat(id) from demo_user where username='$keyword' or name='$keyword'");
			}else{
				$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and username='$keyword' or nickname='$keyword'");
			}
			if(empty($userIds))$userIds = '0';
			$sql.=" and (order_orderId='$keyword' or from_user in($userIds)) ";
		}
		$count = $db->get_var(str_replace('order_type,orderId,money,dtTime,status,order_comId','count(*)',$sql));
		$sql.=" order by id desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
		//file_put_contents('request.txt',$sql);
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
				if($pdt->order_type==1){
					$fenbiao = getFenbiao($pdt->order_comId,20);
					$order = $db->get_row("select product_json,orderId,price,dtTime,userId,shuohuo_json from order$fenbiao where id=$pdt->orderId");
				}else{
					$order = $db->get_row("select product_json,orderId,price,dtTime,userId from demo_pdt_order where id=$pdt->orderId");
				}
				$data['id'] = $pdt->orderId;
				$data['orderId'] = $order->orderId;
				switch ($pdt->status) {
					case 0:
						$data['statusInfo'] = '已付款';
					break;
					case 1:
						$data['statusInfo'] = '已结算';
					break;
					case -1:
						$data['statusInfo'] = '无效';
					break;
				}
				$product_json = json_decode($order->product_json);
				$data['products'] = $product_json;
				$data['dtTime'] = date("Y-m-d H:i",strtotime($order->dtTime));
				$data['status'] = $pdt->status;
				$data['yongjin'] = $pdt->money;
				$data['price'] = $order->price;
				if(!empty($order->shuohuo_json)){
					$shuohuo_json = json_decode($order->shuohuo_json,true);
					$data['uname'] = $shuohuo_json['收件人'];
	      			$data['uphone'] = $shuohuo_json['手机号'];
				}else{
					$u = $db->get_row("select username,nickname from users where id=$order->userId");
	      			$data['uname'] = empty($u->nickname)?'':$u->nickname;
	      			$data['uphone'] = empty($u->username)?'':$u->username;
				}
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);

		$yzFenbiao = getFenbiao($comId,20);
		if($comId==10){
			$db_service = get_zhishang_db();
			$zong_money = $db_service->get_var("select yongjin from demo_user where id=$userId");
			$sql = "select money,yue,type,dtTime,remark,orderInfo from user_yongjin10 where userId=$userId";
		}else{
			$zong_money = $db->get_var("select money from users where id=$userId and comId=$comId");
			$sql = "select money,yue,type,dtTime,remark,orderInfo from user_liushui$yzFenbiao where userId=$userId and comId=$comId";
		}
		if($type>0){
			$sql.=" and type=$type";
		}
		//$sql = "select jifen,yue,type,dtTime,remark from user_jifen$yzFenbiao where userId=$userId and comId=$comId order by id desc ";
	    $res = $db->get_results($sql." order by id desc limit ".(($page-1)*$pageNum).",".$pageNum);
	    $count = $db->get_var(str_replace('money,yue,type,dtTime,remark,orderInfo','count(*)',$sql));
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['zong_money'] = $zong_money;
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
	    if($res){
	      foreach ($res as $key) {
	      	$return['data'][] = $key;
	      }
	  	}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function fansInfo(){
		global $request,$db,$comId;
		$userId = $request['user_id'];
		$tuanzhang = null;
		if($comId==10){
		  $db_service = get_zhishang_db();
		  $tuanzhang_id = $db_service->get_var("select tuan_id from demo_user where id=$userId");
		  $fans = (int)$db_service->get_var("select count(*) from demo_user where shangji=$userId or tuan_id=$userId");
		  if($tuanzhang_id>0){
		    $tuanzhang = $db_service->get_row("select image,name as nickname,user_info from demo_user where id=$tuanzhang_id");
		  }
		}else{
		  $tuanzhang_id = $db->get_var("select tuan_id from users where id=$userId");
		  $fanli_type = $db->get_var("select fanli_type from demo_shezhi where comId=$comId");
		  $fans = (int)$db->get_var("select count(*) from users where comId=$comId and (shangji=$userId or ".($fanli_type==2?'tuan_id':'shangshangji')."=$userId) and id<>$userId");
		  if($tuanzhang_id>0 && $fanli_type==2){
		    $tuanzhang = $db->get_row("select image,nickname,user_info from users where id=$tuanzhang_id");
		  }
		}
		$buy_num = (int)$db->get_var("select count(*) from users where comId=$comId and (shangji=$userId or ".($fanli_type==2?'tuan_id':'shangshangji')."=$userId) and cost>0");
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['fans'] = $fans;
		$return['data']['fans_hasbuy'] = $buy_num;
		$return['data']['fans_nobuy'] = $fans - $buy_num;
		$return['data']['tuanzhang'] = $tuanzhang;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function fenhongRecord()
	{
	    global $request,$db,$comId;
	    
        $fenbiao = getFenbiao($comId,20);
        $userId = $request['user_id'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $yzFenbiao = getFenbiao($comId,20);
        $sql = "select * from user_month_fenhong where userId=$userId order by id desc ";
        // echo $sql;die;
        $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
        $count = $db->get_var(str_replace('*','count(*)',$sql));
        $count++;
        $info = $db->get_row("select nickname, image, phone from users where id = $userId");
        $info->phone = substr($info->phone,0,3).'****'.substr($info->phone,7,4);
        
         
        $config = $db->get_results("select * from zc_release where id > 0");
        //获取vip 用户级别以上业绩
        $users = $db->get_results("select id,order_price from users where level >=74 and id = $userId ");

        $sw_price = 100000;    //达标 
        $startTime = date('Y-m-01 00:00:00');
        $endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
        $endSecond = strtotime($endTime) - 1;
        $endTime = date('Y-m-d H:i:s', $endSecond);
        
        foreach($users as $k=>$v){
            $userId = $v->id;
            $selfTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $userId and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $userId ");
            $selfTotal = $selfTotal ? $selfTotal : 0;
            // var_dump($selfTotal);
            $bili = $selfFenhong = 0;
            if($selfTotal > 0){
                foreach($config as $kk=>$vv){ 
                    if($selfTotal >= $vv->min && $selfTotal < $vv->max){
                         $bili = $vv->bili ;
                    }
                }
                $selfFenhong = bcmul($selfTotal, $bili, 2);
            }

            $user_price = $xiaji_price = $xiajisub_price = $bili = 0;
            //查询所有下级  大于10万用户
            $user_xiaji = $db->get_results("select id,nickname,level from users where  shangji =$v->id ");
            $userXiajiData = [];
            foreach($user_xiaji as $user_xiaji_k=>$user_xiaji_v){
                //当前用户 返利金额减去所有下级 返利金额
                $childTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $user_xiaji_v->id and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $user_xiaji_v->id ");
                $childTotal = $childTotal ? $childTotal : 0;
                $xiaji_price = bcadd($xiaji_price, $childTotal, 2);
                foreach($config as $kk=>$vv){
                    if($childTotal >= $vv->min && $childTotal < $vv->max){
                        $childFenhong = bcmul($vv->bili, $childTotal, 2);
                        $userXiajiData[] = array(
                            'total' => $childTotal,
                            'bili' => $vv->bili,
                            'fenhong' => $childFenhong
                        );
                        $xiajisub_price = bcadd($xiajisub_price, $childFenhong, 2);
                    }
                }        
            }
            
            foreach($config as $kk=>$vv){ 
                //当前用户返利金额及比例
                if($xiaji_price >= $vv->min && $xiaji_price < $vv->max){
                     $bili = $vv->bili ;
                }
            }
   
            $user_price1 = bcmul($xiaji_price, $bili, 2);
            $user_price = bcsub($user_price1, $xiajisub_price, 2);
            
            $directTotal = $directFenhong = $indirectTotal = $indirectFenhong = 0;
            $directTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $directTotal = $directTotal ? $directTotal : 0;
            if($directTotal > 0){
                $directFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
                $directFenhong = $directFenhong ? $directFenhong : 0;
            }
            
            $indirectTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $indirectTotal = $indirectTotal ? $indirectTotal : 0;
            if($indirectTotal > 0){
                $indirectFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
                $indirectFenhong = $indirectFenhong ? $indirectFenhong : 0;
            }
            
            $totalFenhong = bcadd($selfFenhong, $user_price, 2);
            $totalFenhong = bcadd($totalFenhong, $directFenhong, 2);
            $totalFenhong = bcadd($totalFenhong, $indirectFenhong, 2);
            $fenhongLog = array(
                'id' => 0,
                'userId' => $userId,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'self_total' => $selfTotal,
                'self_fenhong' => $selfFenhong,
                'team_total' => $xiaji_price,
                'team_fenhong' => $user_price,
                'direct_total' => $directTotal,//直推业绩
                'direct_fenhong' => $directFenhong,//直推分红
                'indirect_total' => $indirectTotal,//间推业绩
                'indirect_fenhong' => $indirectFenhong,//间推分红
                'total_fenhong' => $totalFenhong,
                'xiajiFenhong_info' => $userXiajiData,
                'dtTime' => date('Y-m-d H:i:s')
            );
            
            $res[] = $fenhongLog;
            $res = (array)$res;
            $last_names = array_column($res,'endTime');
            array_multisort($last_names,SORT_DESC,$res);
        }
        
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array(
            'info' => $info,
            'list' => []
        );
        if($res){
            // foreach($res as $k=>$v){
            // }
            
            $return['data']['list'] = $res;
        }
        
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function fansRecordNew()
	{
	    global $request,$db,$comId;
	    
        $userId = $request['user_id'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $startTime = date('Y-m-01 00:00:00');
        $endTime = date('Y-m-d 23:59:59');
        
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $yzFenbiao = getFenbiao($comId,20);
        $sql = "select id,username,nickname,dtTime,order_price,total_order_price,level as level_id,image,is_dabiao from users where shangji=$userId and comId=$comId order by id desc ";
        // echo $sql;die;
        $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
        $count = $db->get_var(str_replace('id,username,nickname,dtTime,order_price,total_order_price,level as level_id,image,is_dabiao','count(*)',$sql));
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['tuan_num'] = $db->get_var("select count(*) from users where shangji=$userId or shangshangji = $userId");
        $return['child_total'] = $return['team_total'] = 0;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        if($res){
            foreach($res as $k=>$v){
                $res[$k]->xiaji_num = $db->get_var("select count(*) from users where shangji = $v->id" );
            }
            
            $return['data'] = $res;
        }
        
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	
	public function fansRecord(){
	   global $request,$db,$comId;
        $userId = $request['user_id'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $startTime = $request['startTime'];
        $endTime = $request['endTime'];
        $type = $request['type'];
        $where = '';
        if($type==1){ //团队
             $where = ' and is_dabiao = 0';
        }else if($type==2){ //平级
            $level_id = $db->get_row("select level from users where id = $userId");
             $where = ' and level = '.$level_id;
        }
        
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $yzFenbiao = getFenbiao($comId,20);

        $sql = "select id,username,nickname,dtTime,month_money,order_price,total_order_price,level as level_id,image,is_dabiao from users where shangji=$userId and comId=$comId $where order by id desc ";
        // echo $sql;die;
        $res = $db->get_results($sql."limit ".(($page-1)*$pageNum).",".$pageNum);
        $count = $db->get_var(str_replace('id,username,nickname,dtTime,month_money,order_price,total_order_price,level as level_id,image,is_dabiao','count(*)',$sql));
        $return = array();
        $return['code'] = 1;
        $shangji = $db->get_var("select shangji from users where id =$userId");
        foreach($res as $k=>$v){
            $res[$k]->xiaji_num = $db->get_var("select count(*) from users where shangji = $v->id" );
        }
    
        $return['message'] = '';
        $return['shangji'] = $db->get_row("select * from users where id = $shangji");
        $return['count'] = $count;
        $return['tuan_num'] = $db->get_var("select count(*) from users where tuan_id = $userId ");
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = $res;
   
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function fansIncome(){
		global $request,$db,$comId;
		$userId = (int)$request['id'];
		$month = date("Y-m-01");//本月一号
		$lastmonth = date("Y-m-01",strtotime('-1 day',strtotime($month)));//上个月1号
		if($comId==10){
			$db_service = get_zhishang_db();
			$zong_shouru = $db_service->get_var("select earn from demo_user where id=$userId");
		}else{
			$zong_shouru = $db->get_var("select earn from users where id=$userId");
		}
		$last_shouru = $db->get_var("select sum(money) from user_yugu_shouru where comId=$comId and userId=$userId and dtTime>='$lastmonth' and dtTime<'$month'");
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['zong_shouru'] = $zong_shouru;
		$return['data']['last_shouru'] = empty($last_shouru)?'0':$last_shouru;
		return json_encode($return,true);
	}
	
	public function shareInfo(){
		global $request,$db,$comId;
		$userId = (int)$request['user_id'];
        
        // $shareId = (int)$request['shareId'];
        
        // $db->query("update banner set counts = counts + 1 where id = $shareId");
		$filename = $comId.'_'.$userId.'.png'; //新图片名称
        $newFilePath = ABSPATH.'upload/invite/'.$filename;
  	    $url = "https://".$_SERVER['HTTP_HOST'].'/upload/invite/'.$filename;
  	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		
        if(is_file($newFilePath)){
        	$return['data']['code'] = $url;
		    $return['data']['back_img'] = $back_img;
		
		    return json_encode($return,JSON_UNESCAPED_UNICODE);
        }
		$access_token = Product::getAccessToken();
	  	$ewm_url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
	  	$params = array("scene"=>"invite_id=".$userId,"page"=>"pages/index/index");
	  	$ewm = Product::curl_post($ewm_url,$params);
		$newFile = fopen($newFilePath,"w"); //打开文件准备写入
		fwrite($newFile,$ewm); //写入二进制流到文件
		fclose($newFile);
		
		$return['data']['code'] = $url;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function qiandao(){
		global $request,$db,$comId;
		$userId = $request['user_id'];
		//$level = (int)$db->get_var("select level from users where id=$userId");
		$today = date("Y-m-d");
		$days = $db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='$today' limit 1");
		if($days>0){
			return '{"code":0,"message":"您今天已经签过到了"}';
		}
		$qiandao = $db->get_row("select if_qiandao,qiandao_rule from user_shezhi where comId=$comId");
		if($qiandao->if_qiandao!=1){
			return '{"code":0,"message":"签到功能已关闭"}';
		}
		$qiandao_rule = $qiandao->qiandao_rule;
		if(!empty($qiandao_rule)){
			$rule = json_decode($qiandao_rule,true);
		}
		$yesterday = (int)$db->get_var("select days from user_qiandao where userId=$userId and comId=$comId and dtTime='".date("Y-m-d",strtotime('-1 day'))."' limit 1");
		$yesterday++;
		$jifen = $rule['jifen'];
		if($rule['type']==2){
			$first =$rule['first'];
			$maxday = $rule['day'];
			$leijia = $rule['leijia'];
			if($yesterday>$maxday+1){
				$yesterday = $maxday+1;
			}
			$jifen = $first+($yesterday-1)*$leijia;
		}
		$db->query("delete from user_qiandao where userId=$userId and comId=$comId");
		$db->query("insert into user_qiandao(userId,comId,dtTime,days) value($userId,$comId,'$today',$yesterday)");
		$db->query("insert into user_qiandao_jilu(userId,comId,dtTime) value($userId,$comId,'$today')");
		if($comId==10){
			$db_service = get_zhishang_db();
			$db_service->query("update demo_user set jifen=jifen+$jifen where id=$userId");
			$return_jifen = $db_service->get_var("select jifen from demo_user where id=$userId");
		}else{
			$db->query("update users set jifen=jifen+$jifen where id=$userId");
			$return_jifen = $db->get_var("select jifen from users where id=$userId");
		}
		
		$jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = $jifen;
		$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
		$jifen_jilu['type'] = 1;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '签到';
		$fenbiao = getFenbiao($comId,20);
		$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
		//$jifen = $db->get_var("select jifen from *");
		return '{"code":1,"message":"签到成功","days":"'.$yesterday.'","jifen":"'.$return_jifen.'"}';
	}
	
	public function userInfo(){
		global $db,$request,$comId;
		
		$fenbiao = getFenbiao($comId,20);
		$userId = $request['user_id'];
		$rst = $db->get_row("select * from users where id=$userId");
		$level = $db->get_var("select title from user_level where id=$rst->level");
		if(empty($level))$level='会员';
		if(!empty($rst->image) && substr($rst->image,0,4)!='http'){
			$rst->image = 'http://'.$_SERVER['HTTP_HOST'].$rst->image;
		}
		if(!empty($rst->shequ_id)){
			$shequ_status = $db->get_var("select status from demo_shequ where id=$rst->shequ_id");
			if($shequ_status!=1){
				$db->query("update users set shequ_id=0 where id=$userId");
				$rst->shequ_id = 0;
			}
		}
		$yhq_num = (int)$db->get_var("select count(*) from  user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>'".date("Y-m-d H:i:s")."'");
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['data'] = array();
		$return['data']['user_id'] = $rst->id;
		$return['data']['if_need_regist'] = strlen($rst->phone) == 11? 0 : 1;
		$return['data']['name'] = $rst->nickname;
		$return['data']['username'] = $rst->phone;
		$return['data']['token'] = $rst->token;
		$return['data']['user_image'] = $rst->image;
		$return['data']['level'] = $level;
		$return['data']['if_tuanzhang'] = $rst->if_tuanzhang;
		$return['data']['jifen'] = $rst->jifen;
		
		$card = $db->get_row("select sum(yue) as money,count(id) as num from user_card where userId = $userId");
		$changeNum = $db->get_var("select count(id) from kmd_change_card where userId = $userId and status = 0 ");
		$return['data']['card_money'] = floatval($card->money);
		$return['data']['card_num'] = intval($card->num);
		$return['data']['change_num'] = intval($changeNum);
		
		$return['data']['wx_money'] = $rst->wx_money;
		$return['data']['money'] = $rst->wx_money;
		
		$return['data']['earn'] = $rst->earn;
		$return['data']['shequ_id'] = $rst->shequ_id;
		$return['data']['yhq_num'] = $yhq_num;
		$return['data']['if_edit_phone'] = empty($rst->phone)?0:1;
		
		$return['data']['is_pay_pass'] = !empty($rst->payPass) ? 1 : 0;
		
// 		if(!empty($rst->shequ_id)){
// 			$return['data']['shequ_title'] = $db->get_var("select title from demo_shequ where id=".$rst->shequ_id);
// 		}
		$return['data']['renzheng'] = $rst->renzheng;//认证：0-未提交  1-待审核 2-审核通过  -1审核失败
		$return['data']['real_name'] = $rst->real_name;
// 		$return['data']['identity_card_back'] = $rst->identity_card_back;
// 		$return['data']['identity_card_front'] = $rst->identity_card_front;
// 		$return['data']['identity_id'] = $rst->identity_id;
// 		$return['data']['renzheng_msg'] = '';
		$return['data']['level_id'] = $rst->level;
		$startTime = date('Y-m-01 00:00:00');
		$endTime = date('Y-m-d H:i:s');
        $selfTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $rst->id and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $rst->id ");
        $selfTotal = $selfTotal ? $selfTotal : 0;
		$return['data']['order_price'] =  $selfTotal;
// 		$return['data']['order_price'] =  $rst->order_price;
		
		$return['data']['sex'] = $rst->sex;
		$return['data']['birthday'] = $rst->birthday;
		$return['data']['areaId'] = $rst->areaId;
		$return['data']['address'] = $rst->address;
		$return['data']['u_group'] = $rst->u_group;
		$return['data']['worker'] = $rst->worker;
		$return['data']['company'] = $rst->company;
		
		if($rst->renzheng == -1){
		    $return['data']['renzheng_msg'] = $rst->renzheng_msg; 
		}
		
		$levelRules = $db->get_results("select id,title,jifen,content,price,yq_num from user_level where comId = $comId order by ordering desc,id asc");
		
		$return['data']['level_rules'] = $levelRules;
		if($rst->level == 76){
		    $return['data']['next_level'] = null;
		}else{
		    foreach ($levelRules as $key => $rule){
    		    if($rule->id == $rst->level){//当前等级
    		        $nextRule = $levelRules[$key+1];
    		        $nextRule->differ = bcsub($nextRule->jifen, $rst->total_order_price, 2);
    		        $return['data']['next_level'] = $nextRule;
    		        
    		        break;
    		    }
    		}
		}
		$yq_num = $db->get_var("select count(*) from users where shangji=$rst->id and comId=$comId and level >74");
		$return['data']['yq_num'] = $yq_num>0?$yq_num:0;
		//$days = $db->get_var("select days from user_qiandao where userId=$rst->id and comId=$comId and dtTime='".date("Y-m-d H:i:s")."' limit 1");
		//$return['data']['if_qiandao'] = $days>0?1:0;
		
		$return['data']['pdt_collect_num'] = (int)$db->get_var("select count(inventoryId) from user_pdt_collect where userId = $rst->id ");
		$now = date('Y-m-d H:i:s');
		$return['data']['order_need_pay'] = (int)$db->get_var("select count(*) from order$fenbiao  where comId = $comId and userId = $userId and is_del = 0 and status = -5 and pay_endtime > '$now' and if_read = 0 ");
	    $return['data']['order_need_delivery'] = (int)$db->get_var("select count(*) from order$fenbiao  where comId = $comId and userId = $userId and is_del = 0 and status = 2 and if_read = 0 ");
	    $return['data']['order_need_receive'] = (int)$db->get_var("select count(*) from order$fenbiao  where comId = $comId and userId = $userId and is_del = 0 and status = 3 and if_read = 0 ");
	    $return['data']['order_need_pingjia'] = (int)$db->get_var("select count(*) from order$fenbiao  where comId = $comId and userId = $userId and is_del = 0 and status = 4 and ifpingjia = 0 and if_read = 0 ");
	    $return['data']['order_need_refund'] = (int)$db->get_var("select count(*) from order_tuihuan where comId=$comId and userId=$userId and status>-1 and status < 6 ");
	    
	    $return['data']['change_need_delivery'] = (int)$db->get_var("select count(*) from kmd_change_log where 1=1 and userId=$userId and status = 0  ");
	    $return['data']['change_need_receive'] = (int)$db->get_var("select count(*) from kmd_change_log where 1=1 and userId=$userId and status = 1  ");
		$return['data']['change_finished'] = (int)$db->get_var("select count(*) from kmd_change_log where 1=1 and userId=$userId and status = 4  ");
		
 		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public static function verify_token($userId,$token){
		global $comId,$db;
		if(empty($userId) || empty($token)){
			die('{"code":-2,"message":"参数错误,user_id或token不能为空111！"}');
		}
		$user_token = $db->get_var("select token from users where id=$userId limit 1");
		if($token!=$user_token){
			echo '{"code": -1,"message":"登录已过期，请重新登录！"}';
			exit;
		}
	}

	//修改、设置支付密码
	function payPass(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$payPass = $request['paypass'];
		$code = $request['code'];
		$u = $db->get_row("select verify_code from users where id=$userId");
		$phone = $db->get_var("select phone from users where id = $userId ");
		$u = $db->get_row("select * from verify where phone = '$phone'");
		if($code!=$u->code || empty($code)){
			return '{"code":0,"message":"验证码错误"}';
		}
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new \shlEncryption($payPass);
		$pwd = $shlencryption->to_string();
		$db->query("update users set payPass='$pwd' where id=$userId");
		self::add_user_oprate('修改支付密码',2,$userId);
		return '{"code":1,"message":"操作成功"}';
	}
	
	//注册奖励
	function zhuce_jiangli($userId,$comId){
		global $db;
		if($comId==10){
			$yaoqing_rule = $db->get_var("select yaoqing_rules from demo_shezhi where comId=10");
			$yaoqing_rules = json_decode($yaoqing_rule);
			$this->add_linpinka_money($userId,$yaoqing_rules->z_dikoujin,'注册/绑定奖励','注册/绑定奖励',0);
			if($yaoqing_rules->yhqId>0){
				//奖励优惠券
				$yhq = $db->get_row("select * from yhq where id=".$yaoqing_rules->yhqId." and comId=10 and status=1");
				if(!empty($yhq)){
					$user_yhq = array();
				  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
				  	$user_yhq['comId'] = 10;
				  	$user_yhq['userId'] = $userId;
				  	$user_yhq['jiluId'] = $yaoqing_rules->yhqId;
				  	$user_yhq['fafangId'] = 0;
				  	$user_yhq['title'] = $yhq->title;
				  	$user_yhq['man'] = $yhq->man;
				  	$user_yhq['jian'] = $yhq->money;
				  	$user_yhq['startTime'] = $yhq->startTime;
				  	$user_yhq['endTime'] = $yhq->endTime;
				  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
				  	$db->insert_update('user_yhq10',$user_yhq,'id');
				  	$db->query("update yhq set hasnum=hasnum+1 where id=".$yaoqing_rules->yhqId);
				}
			}
		}
		//$comId = (int)$_SESSION['demo_comId'];
		$reg_gift = $db->get_row("select type,guizes from reg_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 limit 1");
		if(!empty($reg_gift)){
			$guizes = json_decode($reg_gift->guizes);
			$yzFenbiao = $fenbiao = getFenbiao($comId,20);
			$money = $guizes[0]->jian;
			switch ($reg_gift->type) {
				case 1:
					$db->query("update users set money=money+$money where id=$userId");
					$liushui = array();
					$liushui['userId']=$userId;
					$liushui['comId']=$comId;
					$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
					$liushui['money']=$money;
					$liushui['yue']=$db->get_var("select money from users where id=$userId");
					$liushui['type']=2;
					$liushui['dtTime']=date("Y-m-d H:i:s");
					$liushui['remark']='注册奖励';
					$liushui['orderInfo']='';
					$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
				break;
				case 2:
					$db->query("update users set jifen=jifen+$money where id=$userId");
					$jifen_jilu = array();
					$jifen_jilu['userId'] = $userId;
					$jifen_jilu['comId'] = $comId;
					$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
					$jifen_jilu['jifen'] = $money;
					$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
					$jifen_jilu['type'] = 1;
					$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
					$jifen_jilu['remark'] = '注册奖励';
					$db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
				break;
				case 3:
					foreach ($guizes as $guize) {
						$yhqId = $guize->yhqId;
						$money = $guize->jian;
						$yhq = $db->get_row("select * from yhq where id=$yhqId and comId=$comId and status=1");
						if(empty($yhq)){
							return false;
						}
						if($yhq->hasNum>=$yhq->num){
							return false;
						}
						for($i=0;$i<$money;$i++){
							$user_yhq = array();
						  	$user_yhq['yhqId'] = uniqid().rand(1000,9999);
						  	$user_yhq['comId'] = $comId;
						  	$user_yhq['userId'] = $userId;
						  	$user_yhq['jiluId'] = $yhqId;
						  	$user_yhq['fafangId'] = 0;
						  	$user_yhq['title'] = $yhq->title;
						  	$user_yhq['man'] = $yhq->man;
						  	$user_yhq['jian'] = $yhq->money;
						  	$user_yhq['startTime'] = $yhq->startTime;
						  	$user_yhq['endTime'] = $yhq->endTime;
						  	$user_yhq['dtTime'] = date("Y-m-d H:i:s");
						  	$db->insert_update('user_yhq'.$fenbiao,$user_yhq,'id');
						  	$db->query("update yhq set hasnum=hasnum+1 where id=$yhqId");
						}
					}
				break;
			}
		}
	}
	//给会员永久抵扣卡充值
	function add_linpinka_money($userId,$money,$remark,$info,$daili_id,$fromId=0){
		global $db;
		$fenbiao = 10;
		$card = $db->get_row("select * from gift_card10 where userId=$userId and (endTime is NULL or endTime='0000-00-00') order by id asc limit 1");
		if(empty($card)){
			$sql = "insert into gift_card$fenbiao(comId,cardId,password,money,yue,jiluId,typeInfo,userId,bind_time,bili,from_id,daili_id) values";
			$sql1 = '';
			$cardId = $userId;
			$length = 16-strlen($cardId);
			for($j = 0; $j < $length; $j++) {
				$cardId .= rand(0,9);
			}
			$password = rand(100000,999999);
			$sql1.=" (10,'$cardId','$password','$money','$money',1,'抵扣卡',$userId,'".date("Y-m-d H:i:s")."','100.00',0,$daili_id)";
			$db->query($sql.$sql1);
			$card_id = $db->get_var("select last_insert_id();");
			$liushui = array();
			$liushui['cardId']=$card_id;
			$liushui['money']=$money;
			$liushui['yue']=$money;
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']=$remark;
			$liushui['orderInfo']=$info;
			$liushui['orderId']=0;
			$db->insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
		}else{
			$db->query("update gift_card$fenbiao set yue=yue+$money where id=$card->id");
			$liushui = array();
			$liushui['cardId']=$card->id;
			$liushui['money']=$money;
			$liushui['yue']=$db->get_var("select yue from gift_card$fenbiao where id=$card->id");
			$liushui['dtTime']=date("Y-m-d H:i:s");
			$liushui['remark']=$remark;
			$liushui['orderInfo']=$info;
			$liushui['orderId']=0;
			$liushui['userId']=$fromId;
			$db->insert_update('gift_card_liushui'.$fenbiao,$liushui,'id');
		}	
	}
	public function https_request($url){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl,CURLOPT_HEADER,0); //
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
	    $response = curl_exec($curl);  
	    curl_close($curl);
	    $jsoninfo = json_decode($response,true); 
	    return $jsoninfo;
	}
	//订货权限
	public function quanxian(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$db_service = get_zhishang_db();
		$department = $db_service->get_var("select department from demo_user where id=$userId");
		$qxs = $db->get_results("select model,group_concat(functions) as functions,group_concat(storeIds) as storeIds from demo_quanxian where comId=$comId and(find_in_set($userId,userIds) or find_in_set($department,departs)) group by model");
		$return['code'] = 0;
		if(!empty($qxs)){
			$functions = '';
			$storeIds = '';
			foreach ($qxs as $q){
				if(!empty($q->functions) && $q->model=='dinghuo'){
					$functions .= ','.$q->functions;
				}
				if(!empty($q->storeIds)){
					$storeIds .= ','.$q->storeIds;
				}
			}
			$fun_array = array();
			$storeArrs = array();
			if(!empty($functions))$fun_array=explode(',',substr($functions,1));
			if(!empty($storeIds))$storeArrs=explode(',',substr($storeIds,1));
			$storeArrs = array_filter($storeArrs);
			if(empty($storeArrs) || !in_array('add',$fun_array)){
				$return['message'] = "您没有权限，请先联系管理员为您分配仓库和订货权限";
			}else{
				$return['code'] = 1;
				$return['message'] = '成功';
			}
		}else{
			$return['message'] = "您没有权限，请先联系管理员为您分配仓库和订货权限";
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	//订货统计
	public function tongji(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$zong_money = $db->get_var("select sum(money) from demo_dinghuo_money where comId=$comId and userId=$userId and type=0 and status>-1");
		$today_order = $db->get_var("select count(*) from demo_dinghuo_order where comId=$comId and dtTime like '".date("Y-m-d")."%' and userId=$userId and status>-1");
		$today_money = $db->get_var("select sum(money) from demo_dinghuo_money where comId=$comId and userId=$userId and dtTime like '".date("Y-m-d")."%' and type=0 and status=1");
		$tops = $db->get_results("select kehuId as kehu_id,sum(money) as money from demo_dinghuo_money where comId=$comId and userId=$userId and type=0 and status>-1 group by kehuId order by money desc limit 3");
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['zong_money'] = empty($zong_money)?'0':$zong_money;
		$return['today_order'] = empty($today_order)?'0':$today_order;
		$return['today_money'] = empty($today_money)?'0':$today_money;
		$return['top3'] = array();
		if(!empty($tops)){
			foreach ($tops as $top) {
				$top->kehu_name = $db->get_var("select title from demo_kehu where id=$top->kehu_id");
				$return['top3'][] = $top;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	//外部注册
	function appletReg(){
		global $db,$request,$comId;
		if(empty($request['token']) || empty($request['openid']) || empty($request['session_key'])){
			return '{"code":0,"message":"参数不能为空"}';
		}
		$token = $request['token'];
		$openid = $username = $request['openid'];
		$session_key = $request['session_key'];
		$ifhas = $db->get_var("select id from users where comId=$comId and unionid='$openid' limit 1");
		if($ifhas>0){
			$applet_info = json_encode(array("session_key"=>$session_key,"openid"=>$openid),JSON_UNESCAPED_UNICODE);
			$db->query("update users set token='$token',applet_info='$applet_info' where id=$ifhas");
			$return['code'] = 1;
			$return['message'] = '成功';
			$return['data'] = array();
			$return['data']['user_id'] = $ifhas;
		}else{
			$level_row = $db->get_row("select id,title from user_level where comId=$comId order by id asc limit 1");
			$level = (int)$level_row->id;
			$shangji = 0;
			$shangshangji = 0;
			$weixin_name = '用户8'.time();
			$password = rand(1000,9999);
			$areaId = 0;
			$applet_info = json_encode(array("session_key"=>$session_key,"openid"=>$openid),JSON_UNESCAPED_UNICODE);
			$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,unionid,applet_info,shangji,shangshangji) value($comId,'$weixin_name','$username','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'','$openid','$applet_info',$shangji,$shangshangji)");
			$userId = $db->get_var("select last_insert_id();");
			//$zhishangId = reg_zhishang($userId,$username,$password,$openid,$unionid,0);
			//$db->query("update users set zhishangId=$zhishangId where id=$userId");
			//注册奖励
			$db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin' where id=$userId");
			$return['code'] = 1;
			$return['message'] = '成功';
			$return['data'] = array();
			$return['data']['user_id'] = $userId;
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}

	//邀请分享海报
	function invite(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$tuanzhang_rule = $db->get_var("select tuanzhang_rule from demo_shezhi where comId=$comId");
		if(!empty($tuanzhang_rule)){
		    $rules = json_decode($tuanzhang_rule,true);
		    $back_img = $rules['yaoqing_back'];
		}
		if(empty($back_img)||substr($back_img,0,4)!='http')$back_img='https://buy.zhishangez.com/skins/default/images/fenxianghaoyou_1.png';
		$back_img = str_replace('http://','https://',$back_img);
		$filename = $comId.'_'.$userId.'.png'; //新图片名称
		$newFilePath = ABSPATH.'upload/invite/'.$filename;
		$url = 'https://buy.zhishangez.com/upload/invite/'.$filename;
		if(is_file($newFilePath)){
			return '{"code":1,"message":"","data":"'.$url.'","back_img":"'.$back_img.'"}';
		}
		$access_token = Product::getAccessToken();
	  	$ewm_url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
	  	$params = array("scene"=>"invite_id=".$userId,"page"=>"pages/index/index");
	  	$ewm = Product::curl_post($ewm_url,$params);
		$newFile = fopen($newFilePath,"w"); //打开文件准备写入
		fwrite($newFile,$ewm); //写入二进制流到文件
		fclose($newFile);
	  	return '{"code":1,"message":"","data":"'.$url.'","back_img":"'.$back_img.'"}';
	}
	//修改昵称
	public function editName(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$name = $request['name'];
		$db->query("update users set nickname='$name' where id=$userId");
		return '{"code":1,"message":"操作成功"}';
	}
	
    public function registerSendSms(){
        global $request,$db,$comId;
        $phone = $request['phone'];
        $yzm = rand(1000,9999);
        $verify = md5(substr($phone.$yzm,5,5));
        if(!$phone){
            return '{"code":0,"message":"手机号不能为空！"}';
        }

        $row = $db->get_row("select * from send_sms where comId=$comId AND phone=$phone AND status = 0 order by id desc limit 1");
        $time = time();
        if($row && ($row->created_at + 120) > $time){
            return '{"code":0,"message":"验证码发送频繁请两分钟后在试"}';
        }

        //echo "insert into send_sms(comId,phone,code,created_at,status) value($comId,$phone,$yzm,$time,0)";die;
        $db->query("insert into send_sms(comId,phone,code,created_at,status) value($comId,$phone,$yzm,$time,0)");
        file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&type=1');
        return '{"code":1,"message":"验证码已发送"}';
    }

    public function retrievePassword(){
        global $request,$db,$comId;
        $phone = $request['phone'];
        $pass = $request['pwd'];
        $code = $request['code'];
        if(empty($phone) || empty($pass)){
            return '{"code":0,"message":"手机号或密码不能为空"}';
        }
        if(!$code){
            return '{"code":0,"message":"验证码不能为空"}';
        }
        $row = $db->get_row("select * from send_sms where comId=$comId AND phone=$phone AND status = 0 AND code = '$code' order by id asc limit 1");
        if(!$row){
            return '{"code":0,"message":"验证码错误"}';
        }
        if($row->created_at +120 < time()){
            return '{"code":0,"message":"验证码已失效"}';
        }
        $db->query("update send_sms set status = 1 where id = $row->id");

        $password = sha1($pass);
        $table_name = 'users';
        $user_row = $db->get_row("select * from $table_name where comId=$comId AND phone=$phone ");
        if(!$user_row){
            return '{"code":0,"message":"手机号不存在！"}';
        }

        //echo "update $table_name set password = $password where id = $user_row->id";die;
        $db->query("update $table_name set password = '$password' where id = $user_row->id");

        $return['code'] = 1;
        $return['data'] = $user_row;
        $return['message'] = '修改成功';
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    //修改用户信息
    public function updateUserInfo(){
        global $request,$db,$comId;
        $user_id = $request['user_id'];
        $nickname = $request['nickname'];
        $address = $request['address'];
        $birthday = $request['birthday'];
        $sex = $request['sex'];
        $token = $request['token'];
        if(!$user_id){
            return '{"code":0,"message":"参数不能为空！"}';
        }
        $table_name = 'users';

        $user_row = $db->get_row("select * from $table_name where comId=$comId AND id=$user_id AND token= '".$token."'");
        if(!$user_row){
            return '{"code":0,"message":"用户不存在！"}';
        }
        $db->query("update $table_name set nickname = '$nickname',address='$address',birthday='$birthday',sex=$sex where id = $user_row->id");
        $return['code'] = 1;
        $return['message'] = '成功';
        return json_encode($return,JSON_UNESCAPED_UNICODE);

    }

    //修改用户头像
    public function updateUserImage(){
        global $db,$request,$comId;

        $token = $request['token'];
        $user_id = $request['user_id'];
        if(!$user_id ||!$token){
            return '{"code":0,"message":"参数不能为空！"}';
        }
        require(ABSPATH . '/inc/class.upload.php.oss');
        $upload = new \Upload();
        $fileName = $upload->SaveFile('uploadfile');
        $tximage = '/upload/'.$fileName;

        $a = $db->query("update users set image='$tximage' where comId=$comId AND id=$user_id AND token = '$token'");
        $return['code'] = 1;
        $return['message'] = '成功';
        $return['image'] = $tximage;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }

	/*修改会员姓名、电话*/
	public function editInfo(){
		global $db,$request,$comId;
		
		$data = array();
		$userId = (int)$request['user_id'];
		$data['id'] = $userId;
		
		$user = $db->get_row("select * from users where id = $userId");
		if($request['nickname']){
		    $data['nickname'] = $request['nickname'];
		}
		if($request['username']){
		    $data['username'] = $request['username'];
		}
		if($request['hobby']){
		    $data['hobby'] = $request['hobby'];
		}
		
		if($request['company']){
		    $data['company'] = $request['company']; 
		}
		
		if($request['u_group']){
		    $data['u_group'] = $request['u_group']; 
		}
		
		if($request['worker']){
		    $data['worker'] = $request['worker']; 
		}
		
		if($request['address']){
		    $data['address'] = $request['address']; 
		}
		
		
		if($request['phone'] && strlen($request['phone']) == 11){
		    $phone = $request['phone'];
		    $hadUser = $db->get_row("select * from users where phone = '$phone' and id <> $userId");
		    if($hadUser){
		        return '{"code":0,"message":"该手机号已经被注册，请更换手机号"}';
		    }
		    $data['phone'] = $request['phone'];
		}
		
		if($request['email']){
		    $email = $request['email'];
		    $hadUser = $db->get_row("select * from users where email = '$email' and id <> $userId");
		    if($hadUser){
		        return '{"code":0,"message":"该邮箱已经被注册，请更换邮箱"}';
		    }
		    $data['email'] = $request['email'];
		}
		
		if($request['birthday']){
		    $data['birthday'] = $request['birthday'];
		}
		
		if($request['sex']){
		    $data['sex'] = $request['sex'];
		}
		
    	if($request['areaId']){
		    $data['areaId'] = $request['areaId'];
		    $area = $db->get_row("select * from demo_area where id=".$request['areaId']);
            $firstId = $area->parentId;
            $secondName = $area->title;
            $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
            if($farea->parentId!=0){
                $firstId = $farea->parentId;
                $secondName = $farea->title;
            }
            $firstName = $db->get_var("select title from demo_area where id=$firstId");
            $data['address'] = $firstName.$secondName.$area->title.$request['address'];
		}
		
		if($request['image']){
		    $data['image'] = $request['image'];
		}
		
		if($request['real_name']){
		    $data['real_name'] = $request['real_name'];
		}
		
		if($user->renzheng != 2){//认证：0-未提交  1-待审核 2-审核通过  -1审核失败
		    if($request['identity_card_front']){
		        $data['identity_card_front'] = $request['identity_card_front'];
		    }
		    
		    if($request['identity_id']){
		        $data['identity_id'] = $request['identity_id'];
		    }
		    
		    if($request['identity_card_back']){
		        $data['identity_card_back'] = $request['identity_card_back'];
		    }
		    
		    if($request['identity_card_back'] && $request['identity_card_back']){
		        $data['renzheng'] = 1;
		    }
		}

		$db->insert_update('users', $data, 'id');
		
		return '{"code":1,"message":"操作成功"}';
	}
    
    //发票 删除
    public function delFaPiao()
    {
        global $db,$request,$comId;
        
        $userId = $request['user_id'];
        $ids = $request['ids'];
        // $data = $db->get_row("select * from user_fapiao where userId = $userId and id = $id ");
        // if(!$data){
        //     return '{"code":0,"message":"发票信息不存在"}';    
        // }
        
        $db->query("delete from user_fapiao where id in ($ids)");
        
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '删除成功';

		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    //发票 详情
    public function faPiaoInfo()
    {
        global $db,$request,$comId;
        
        $userId = $request['user_id'];
        $id = $request['id'];
        $data = $db->get_row("select * from user_fapiao where userId = $userId and id = $id ");
        if(!$data){
            return '{"code":0,"message":"发票信息不存在"}';    
        }
        
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '获取成功';
		$return['data'] = array();
		$return['data'] = $data;

		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    //发票 列表
    public function faPiaoList()
    {
        global $db,$request,$comId;
        
        $userId = $request['user_id'];
        $data = $db->get_results("select * from user_fapiao where userId = $userId order by id desc ");
        
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '获取成功';
		$return['data'] = array();
		$return['data'] = $data;

		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    //增加或者修改发票
    public function saveFaPiao()
    {
        global $db,$request,$comId;
        
        $userId = $request['userId'] = $request['user_id'];
        $request['comId'] = $comId;
        
        $data = [];
        $accessKey = ['userId', 'comId', 'type', 'com_title', 'shibiema', 'address', 'phone', 'bank_name', 'bank_card', 'shoupiao_phone', 'shoupiao_email'];
        foreach ($accessKey as $val){
            $data[$val] = $request[$val];
        }
        $data['id'] = (int)$request['id'];
        
        $faPiaoId = $db->insert_update("user_fapiao", $data, 'id');
        if($faPiaoId){
            $return['code'] = 1;
            $return['message'] = '操作成功';
        }else{
            $return['code'] = 0;
            $return['message'] = '操作失败';
        }

        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    //绑定手机号
	public function bindPhone()
	{
		global $request,$db,$comId;
		
		$userId = $request['user_id'];
		$phone = $request['phone'];
		$code = $request['code'];

		$u = $db->get_row("select * from users where id=$userId");
		$old_user = $db->get_row("select * from users where comId=$comId and phone='$phone' limit 1");
		
		$verifyCode = $db->get_var("select code from verify where phone = '$phone'");
        // if($verifyCode != $code){
        //     return '{"code":0,"message":"验证码错误"}';
        // }
		
		if(empty($old_user)){
			$db->query("update users set phone='$phone' where id=$userId");
			$token = $u->token;
		}else{
			$lastlogin = time();
			$update_user = array();
			$update_user['id'] = $old_user->id;
			//将公众号的openID 更新到 老用户身份上
// 			if(!empty($u->mini_openId)){
// 				$update_user['mini_openId'] = $u->mini_openId;
// 			}
			if(!empty($u->openId)){
				$update_user['openId'] = $u->openId;
			}
			if(!empty($u->unionid)){
				$update_user['unionid'] = $u->unionid;
			}
			if(!empty($u->applet_info)){
				$update_user['applet_info'] = $u->applet_info;
			}
			if(!empty($u->douyin_openId)){
				$update_user['douyin_openId'] = $u->douyin_openId;
			}
			if(!empty($u->douyin_info)){
				$update_user['douyin_info'] = $u->douyin_info;
			}
			$update_user['lastlogin'] = $lastlogin;
			$token = empty($old_user->token)?substr(md5($comId.$userId.$lastlogin),5,10):$old_user->token;
			$update_user['token'] = $token;
			$update_user['tokenTime'] = $lastlogin;

    		$db->insert_update('users',$update_user,'id');
    		$db->query("update users set openid='',unionid='',applet_info='',douyin_openId='',douyin_info='' where id=$userId");
		}
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '绑定成功';
		$return['data'] = array();
		$return['data']['user_id'] = $old_user->id;
		$return['data']['token'] = $token;

		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
    
    //合并账号-授权手机号
	public function bindWxPhone(){
		global $request,$db,$comId;
		
		$userId = $request['user_id'];
		$encryptedData = str_replace(' ','+', $request['encryptedData']);
		$iv = $request['iv'];

		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 limit 1");
		if(empty($weixin_set)||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置有误，无法登录"}';
		}
		$weixin_arr = json_decode($weixin_set->info);
		$appid = $weixin_arr->appid;
		$applet_info = $db->get_var("select applet_info from users where id=$userId");
				
		$applet_info_arr = json_decode($applet_info);
		$session_key = $applet_info_arr->session_key;
		if(empty($session_key)){
			return '{"code":0,"message":"session_key为空"}';
		}
		include_once ABSPATH."inc/wxBizDataCrypt.php";
		$pc = new \WXBizDataCrypt($appid, $session_key);
		$errCode = $pc->decryptData($encryptedData, $iv, $data);
		
		if($errCode != 0){
	    	return '{"code":0,"message":"获取手机号失败，error_code:'.$errCode.'"}';
		}
	
		//file_put_contents('wx_phone.txt',$data);
		$data_arr = json_decode($data,true);
	    $phone = $data_arr['phoneNumber'];
	    if(empty($phone)){
	    	return '{"code":0,"message":"获取不到手机号，请联系技术人员排查"}';
	    }

		$u = $db->get_row("select * from users where id=$userId");
		$old_user = $db->get_row("select * from users where comId=$comId and phone='$phone' limit 1");
		
		if(empty($old_user)){
			$db->query("update users set username='$phone',phone = '$phone' where id=$userId");
			$token = $u->token;
		}else{
			if($old_user->id == $userId){
				return '{"code":0,"message":"您已经绑定过手机号了，不需要再绑定了"}';
			}
			$lastlogin = time();
			$update_user = array();
			$update_user['id'] = $old_user->id;
			//将小程序的openID 更新到 老用户身份上
			if(!empty($u->mini_openId)){
				$update_user['mini_openId'] = $u->mini_openId;
			}
// 			if(!empty($u->openId)){
// 				$update_user['openId'] = $u->openId;
// 			}
			if(!empty($u->unionid)){
				$update_user['unionid'] = $u->unionid;
			}
			if(!empty($u->applet_info)){
				$update_user['applet_info'] = $u->applet_info;
			}
			if(!empty($u->douyin_openId)){
				$update_user['douyin_openId'] = $u->douyin_openId;
			}
			if(!empty($u->douyin_info)){
				$update_user['douyin_info'] = $u->douyin_info;
			}
			
			$update_user['image'] = $u->image;
			$update_user['nickname'] = $u->nickname;
			
			$update_user['lastlogin'] = date('Y-m-d H:i:s');
			$token = empty($old_user->token)?substr(md5($comId.$userId.$lastlogin),5,10):$old_user->token;
			$update_user['token'] = $token;
			$update_user['tokenTime'] = date('Y-m-d H:i:s');
	
			//file_put_contents('wxbind.txt',json_encode($update_user,JSON_UNESCAPED_UNICODE));
			$db->insert_update('users',$update_user,'id');
			$db->query("update users set openid='',mini_openId='',unionid='',applet_info='',douyin_openId='',douyin_info='',username='$old_user->id' where id=$userId");
			//todo  $userId 数据合并到  $old_user->id 
			$userId = $old_user->id;
		}
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '授权成功';
		$return['data'] = array();
		$return['data']['user_id'] = $userId;
		$return['data']['token'] = $u->token;

		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function changePhone()
	{
	    global $request,$db,$comId;
        
        $type = (int)$request['source'];//0-短信验证  1-不需要验证
        
        $userId = (int)$request['user_id'];
        $user = $db->get_row("select * from users where id = $userId");
        $phone = $request['phone'];
        switch($type){
            case 0:
                $code = $request['code'];
                $verifyCode = $db->get_var("select code from verify where phone = '$phone'");
                if($verifyCode != $code){
                    return '{"code":0,"message":"短信证码不正确"}';
                }
                break;
            case 1:
                
                break;
        }
        
        $hadUser = $db->get_row("select * from users where phone = '$phone' ");
        if(!$user){
            return '{"code":0,"message":"未找到用户信息！"}';
        }
        
        if($hadUser){
            return '{"code":0,"message":"该手机号已经被注册，请更换手机号！"}';
        }
        
        
        $db->query("update users set phone='$phone'  where id=$userId");

        return '{"code":1,"message":"绑定手机号更换成功！"}';
	}
    
    //修改密码
    public function changePass()
    {
        global $request,$db,$comId;
        
        $type = (int)$request['source'];//0-邮箱验证  1-短信验证  2-不需要验证
        
        switch($type){
            case 0:
                $email = $request['email'];
                $user = $db->get_row("select * from users where email = '$email' ");
                
                $code = $request['code'];
                $verifyCode = $db->get_var("select code from verify where email = '$email'");
                if($verifyCode != $code){
                    return '{"code":0,"message":"邮箱验证码不正确"}';
                }
                break;
            case 1:
                $phone = $request['phone'];
                $user = $db->get_row("select * from users where phone = '$phone' ");
                $code = $request['code'];
                $verifyCode = $db->get_var("select code from verify where phone = '$phone'");
                if($verifyCode != $code){
                    return '{"code":0,"message":"短信证码不正确"}';
                }
                break;
            case 2:
                $userId = (int)$request['user_id'];
                $user = $db->get_row("select * from users where id = $userId");
                break;
        }
        
        if(!$user){
            return '{"code":0,"message":"您还尚未注册，请先完成注册！"}';
        }
       
        if ($request['pass'] && $request['confirm_pass'] && ($request['pass'] == $request['confirm_pass'])){
            require_once(ABSPATH.'/inc/class.shlencryption.php');
            $pass = $request['pass'];
			$shlencryption = new \shlEncryption($pass);
            $pass = $shlencryption->to_string();

            $db->query("update users set password='$pass'  where id=$user->id");

            return '{"code":1,"message":"密码修改成功！"}';
        }

        return '{"code":0,"message":"两次输入密码不一样，请确认。"}';
    }
    
    //发送短信
	public function sendSms()
	{
		global $request,$db,$comId;
		
		$phone = $request['phone'];
		$yzm = rand(1000,9999);
		$userId = $request['user_id'];
	
		if(strlen($phone) == 13 && strpos($phone,'86') === 0){
		    $phone = str_replace('86','', $phone);
		}
		$verify = md5(substr($phone.$yzm,5,5));

		$updateLog = array();
            
        $updateLog['id'] = (int)$db->get_var("select id from verify where phone = '$phone'");
        $updateLog['phone'] = $phone;
        $updateLog['code'] = $yzm;
        $updateLog['dtTime'] = date("Y-m-d H:i:s");
        $db->insert_update('verify', $updateLog, 'id');
		
// 		echo 'http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?type=1&phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title;die;
		file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/alsend/api_demo/SmsDemo.php?type=1&phone='.$phone.'&yzm='.$yzm.'&verify='.$verify.'&product='.$com_title);
		
		return '{"code":1,"message":"验证码已发送"}';
	}
    
    //手机号登录
    public function phoneLogin()
    {
        global $request,$db,$comId;
        
        $fenbiao = getFenbiao($comId,20);
        
        $type = (int)$request['type'];
        $checkField = ['phone', 'password'];
        if($type){
            $checkField = ['phone', 'code'];
        }
        
        foreach ($checkField as $value){
            if(empty($request[$value])){
                return '{"code":0,"message":"参数'.$value.'不能为空"}';
            }
        }
        $phone = $request['phone'];
        if(strlen($phone) == 13 && strpos($phone,'86') === 0){
		    $phone = str_replace('86','', $phone);
		}
        $user = $db->get_row("select * from users where phone = '$phone'");
        if (!$user){
            return '{"code":0,"message":"未找到手机号对应的用户信息!"}';
        }
        
        if($type){
            $code = $request['code'];
            $codeRow = $db->get_row("select id,code from verify where phone = '$phone' and status = 0");
            $verifyCode = $codeRow->code;
            if($verifyCode != $code){
                return '{"code":0,"message":"手机号验证码不正确"}';
            }
            $db->query("update verify set status = 1 where id=$codeRow->id");
        }else{
            $pass = $request['password'];
            require_once(ABSPATH.'/inc/class.shlencryption.php');
    		$shlencryption = new \shlEncryption($pass);
    		$pwd = $shlencryption->to_string();
    		if($pwd != $user->password){
    		     return '{"code":0,"message":"密码不正确!"}';
    		}
        }
       
        $lastlogin = date('Y-m-d H:i:s');
        $token = empty($user->token)?substr(md5($comId.$user->id.$lastlogin),5,10):$user->token;
        $db->query("update users set lastlogin='$lastlogin',token='$token',tokenTime='$lastlogin' where id=$user->id");
        //todo 合并账号
        $return['code'] = 1;
        $return['message'] = '登录成功！';
        $return['data'] = array();
        $return['data']['user_id'] = $user->id;
        $return['data']['token'] = $token;
       
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    //短信注册
	public function phoneRegister()
    {
        global $request,$db,$comId;
        
        $phone = $request['phone'];
        if(strlen($phone) == 13 && strpos($phone,'86') === 0){
		    $phone = str_replace('86','', $phone);
		}
        $user = $db->get_row("select * from users where phone = '$phone'");
        if ($user){
            return '{"code":0,"message":"该手机号已经被注册"}';
        }
        
        $code = $request['code'];
        $codeRow = $db->get_row("select id,code from verify where phone = '$phone' and status = 0");
        $verifyCode = $codeRow->code;
        if($verifyCode != $code){
            return '{"code":0,"message":"短信验证码不正确"}';
        }
        $db->query("update verify set status = 1 where id=$codeRow->id");
        $pass = $request['pass'];
        require_once(ABSPATH.'/inc/class.shlencryption.php');
		$shlencryption = new \shlEncryption($pass);
		$pwd = $shlencryption->to_string();
		
		$level_row = $db->get_row("select id,title from user_level where comId=$comId order by id asc limit 1");
		$level = (int)$level_row->id;
		$shangji = 0;
		$shangshangji = 0;
		$tuan_id = 0;
		$areaId = 0;
		$tuijianren = (int)$request['invite_id'];
		if(!empty($tuijianren)){
			$shangji = $tuijianren;
			$shangshangji = (int)$db->get_var("select shangji from users where id=$tuijianren");
			$tuan_id = (int)$db->get_var("select tuan_id from users where id=$tuijianren");
		}
		$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,unionid,applet_info,shangji,shangshangji,tuan_id,image,phone) value($comId,'$phone','$phone','$pwd',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'','','',$shangji,$shangshangji,$tuan_id,'','$phone')");
		$userId = $db->get_var("select last_insert_id();");
        
		//注册奖励
        if($userId){
            self::sendRegisterGift($userId);
           	$return['code'] = 1;
		    $return['message'] = '注册成功，请去登录'; 
        }else{
        	$return['code'] = 0;
		    $return['message'] = '注册失败';
        }
	
		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function sendRegisterGift($userId)
    {
        global $request,$db,$comId;
        
        $fenbiao = getFenbiao($comId,20);
        $shezhi = $db->get_row("select * from demo_shezhi where comId = $comId");
        $user = $db->get_row("select * from users where id = $userId");
        if($shezhi->register_jifen > 0){
            $money = (int)$shezhi->register_jifen;
            // $db->query("update users set jifen=jifen+$money where id=$userId");
		
    		$jifen_jilu = array();
    		$jifen_jilu['userId'] = $userId;
    		$jifen_jilu['comId'] = $comId;
    		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
    		$jifen_jilu['jifen'] = $money;
    		$jifen_jilu['yue'] = $money;
    		$jifen_jilu['type'] = $money > 0 ? 1 : 2;
    		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
    		$jifen_jilu['remark'] = '注册奖励';
    		$jifen_jilu['orderInfo'] = '新用户注册奖励';
    	
    		$db->insert_update('user_jifen'.$fenbiao, $jifen_jilu,'id');
        }
        
        if($user->shangji > 0 && $shezhi->invite_jifen > 0){
            $money = (int)$shezhi->invite_jifen;
            $userId = $user->shangji;
            // $db->query("update users set jifen=jifen+$money where id=$userId");
		    
		    $yue = $db->get_var("select jifen from users where id = $userId");
    		$jifen_jilu = array();
    		$jifen_jilu['userId'] = $userId;
    		$jifen_jilu['comId'] = $comId;
    		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
    		$jifen_jilu['jifen'] = $money;
    		$jifen_jilu['yue'] = $yue;
    		$jifen_jilu['type'] = $money > 0 ? 1 : 2;
    		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
    		$jifen_jilu['remark'] = '邀请注册奖励';
    		$jifen_jilu['orderInfo'] = "成功邀请".$user->phone."注册成功";
    		
    		$db->insert_update('user_jifen'.$fenbiao, $jifen_jilu,'id');
        }
       
    }
    
    //用户本月业绩和往期业绩详情
    public function myOrderDetail(){  
        global $request,$db,$comId;
        $user_id = $request['user_id'];    
        $start_time = $request['start_time']. ' 00:00:00'; 
        $end_time = $request['end_time']. ' 23:59:59';
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $fenbiao = getFenbiao($comId,20);
        $sql="select id,orderId,dtTime,product_json,price from order$fenbiao where comId=$comId and userId=$user_id and status >= 2 and dtTime >='$start_time' and dtTime<='$end_time'";
        $count = $db->get_var(str_replace('id,orderId,dtTime,product_json','count(*)',$sql));


        $sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
       // echo $sql;die;
        $pdts = $db->get_results($sql);
        foreach($pdts as $k=>$v){
            $pdts[$k]->product_json = json_decode($v->product_json,true);
        }
   
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = $pdts;

        return json_encode($return,JSON_UNESCAPED_UNICODE);
        
    }
        //用户本月业绩和往期业绩详情
    public function myOrderList(){
        global $request,$db,$comId;
        $user_id = $request['user_id'];    
    
        $sql = "SELECT YEAR(dtTime) year,MONTH(dtTime) month,sum(price) as price FROM `order8` WHERE comId=$comId and userId=$user_id and status >= 2 GROUP BY year desc,month desc";
        $pdts = $db->get_results($sql);

        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $pdts;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
        
    }
    //团队往期分红
    public function myFenHongrList(){
        global $request,$db,$comId;
        $user_id = $request['user_id'];
        $type = (int)$request['type'];
        $sql = "SELECT * FROM `user_tuan_fenhong` WHERE userId=$user_id and  type = $type order by id desc";
        $pdts = $db->get_results($sql);

        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $pdts;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
        
    }
    //往期分红详情
    public function myFenHongDetail(){
        global $request,$db,$comId;
        $user_id = $request['user_id'];
        $id = $request['id'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $data = $db->get_row("select * from user_tuan_fenhong  where id=$id");

        $sql = "select u.id,u.nickname,u.image,u.username,u.dtTime,u.level,t.time,t.money from users as u left join  user_tuan_price as t on t.userId = u.shangji where u.shangji = $user_id and t.time = '$data->time' and t.type <= '$data->type'" ;
     
        $count = $db->get_var(str_replace('u.id,u.nickname,u.image,u.username,u.dtTime,u.level,t.time,t.money','count(*)',$sql));
        $sql.=" order by t.dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
       // echo $sql;die;
        $pdts = $db->get_results($sql);

        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $pdts;
        $return['count'] = $count;
        $return['res'] = $data;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
        
        
    }
     /**
     * 获取省市县列表
     */

    public function getAreaList(){
        global $db,$request;
        $parentId = (int)$request['parent_id'];
        $data = $db->get_results("select * from demo_area where parentId=$parentId");
        $return['code'] = 1;
        $return['message'] = '请求成功';
        $return['data'] = $data;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    //每月定时平级奖
    public function pjTask(){
        global $db,$request;
      //查询所有合伙人
        $pj_shezhi = 0.1; //平级返利比例
        $min_num = 10; //最小返利值
        $hyr_all  = $db->get_results("select id,shangji,month_money,pj_price from users where level=76 group by id desc");
        foreach($hyr_all as $v){
            //查询所有上级
            if($v->shangji){
                $shangji_arr = getShangJi($v->shangji,0,76);
                //平级奖励
                $totail_price = $v->month_money;
                foreach($shangji_arr as $kk=>$vv){
                    //平级奖励 每级别 10%  到10元为止
                    $n = $kk+1;
                    $fanli_price = $totail_price * pow($pj_shezhi * $n); //返利值
                    if($fanli_price >= $min_num){
                        //修改返利
                        $db->query("update users set money=money+$fanli_price,earn= earn+ $fanli_price,pj_price = pj_price+$fanli_price  where id=$vv->shangji");
                        //写入日志  user_tuan_price
                        $user_tuan_price = array();
                        $user_tuan_price['comId'] = 888;
                        $user_tuan_price['userId'] = $vv->shangji;
                        $user_tuan_price['dtTime'] = date("Y-m-d H:i:s");
                        $user_tuan_price['orderId'] = date("YmdHis").rand(1000000000,9999999999);
                        $user_tuan_price['money'] = $fanli_price;
                        $user_tuan_price['remark'] = '平级返利';
                        $user_tuan_price['type'] = 1;
                        $user_tuan_price['orderInfo'] = '平级返利';
                        $user_tuan_price['time'] = strtotime(date('Y-m-d'));
                        $user_tuan_price['bili'] = $pj_shezhi;
                        $user_tuan_price['yu'] = $db->get_var('select money from users where id='.$vv->shangji);
                        $user_tuan_price['from_user'] =$v->id;
                        $db->insert_update('user_tuan_price',$user_tuan_price,'id');
                    }
                }
            }
        }
    }

    //定时团队奖励
    public function tdTask(){
        global $db,$request;
        $td_shezhi = 0.1; //团队返利比例
        $hyr_all  = $db->get_results("select id,shangji,month_money,pj_price from users where level=76 group by id desc");
        foreach($hyr_all as $v) {
            if ($v->month_money) {
                $fanli_price = $v->month_money * $td_shezhi;
                $db->query("update users set money=money+$fanli_price,earn= earn+ $fanli_price,month_money = month_money+$fanli_price  where id=$v->id");
                //写入日志  user_tuan_price
                $user_tuan_price = array();
                $user_tuan_price['comId'] = 888;
                $user_tuan_price['userId'] = $v->id;
                $user_tuan_price['dtTime'] = date("Y-m-d H:i:s");
                $user_tuan_price['orderId'] = date("YmdHis") . rand(1000000000, 9999999999);
                $user_tuan_price['money'] = $fanli_price;
                $user_tuan_price['remark'] = '团队分红';
                $user_tuan_price['type'] = 2;
                $user_tuan_price['orderInfo'] = '团队分红';
                $user_tuan_price['time'] = strtotime(date('Y-m-d'));
                $user_tuan_price['bili'] = $td_shezhi;
                $user_tuan_price['yu'] = $db->get_var('select money from users where id=' . $v->id);
                $user_tuan_price['from_user'] = $v->id;
                $db->insert_update('user_tuan_price', $user_tuan_price, 'id');
                //写入月统计表

                $liushui = array();
                $liushui['userId'] = $v->id;
                $liushui['comId'] = 888;
                $liushui['orderId'] = date("YmdHis") . rand(1000000000, 9999999999);
                $liushui['order_price'] = $fanli_price;
                $liushui['total_order_price'] = $v->month_money;
                $liushui['bili'] = $td_shezhi;
                $liushui['type'] = 2;
                $liushui['dtTime'] = date("Y-m-d H:i:s");
                $liushui['time'] = strtotime(date("Y-m-d"));
                $liushui['remark'] =  date("Y年m月").'团队分红';
                $db->insert_update('user_tuan_fenhong', $liushui, 'id');
            }
            if ($v->pj_price) {
                $liushui = array();
                $liushui['userId'] = $v->id;
                $liushui['comId'] = 888;
                $liushui['orderId'] = date("YmdHis") . rand(1000000000, 9999999999);
                $liushui['order_price'] = $v->pj_price;
                $liushui['total_order_price'] = $v->pj_price;
                $liushui['bili'] = $td_shezhi;
                $liushui['type'] = 1;
                $liushui['dtTime'] = date("Y-m-d H:i:s");
                $liushui['time'] = strtotime(date("Y-m-d"));
                $liushui['remark'] =  date("Y年m月").'平级奖';
                $db->insert_update('user_tuan_fenhong', $liushui, 'id');
            }
            $db->query("update users set month_money =0,pj_price=0  where id=$v->id");
        }
    }
}