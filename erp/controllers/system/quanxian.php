<?php
function index(){}
function kucun(){}
function caigou(){}
function dinghuo(){}
function tuihuo(){}
function lingshou(){}
function shezhi(){}
function addQx(){
	global $db,$request;
	$qx = array();
	$qx['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$qx['departs'] = $request['departs'];
	$qx['userIds'] = $request['users'];
	$qx['userNames'] = $request['userNames'];
	$qx['departNames'] = $request['departNames'];
	$qx['storeIds'] = $request['storeIds'];
	if(!empty($request['functions'])){
		foreach ($request['functions'] as $key=>$val){
			$qx['model'] = $key;
			$qx['functions'] = implode(',',$val);
			insert_update('demo_quanxian',$qx,'id');
		}
	}
	redirect('?m=system&s=quanxian&a='.$request['return']);
}
function delete(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from demo_quanxian where id=$id and comId=$comId");
	redirect('?m=system&s=quanxian&a='.$request['return']);
}
function editFunc(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$function = $request['function'];
	$opt = $request['opt'];
	$qx = $db->get_row("select id,functions from demo_quanxian where id=$id and comId=$comId");
	if(!empty($qx)){
		if($opt=='add'){
			$functions = $function;
			if(!empty($qx->functions)){
				$functions = $qx->functions.','.$function;
			}
		}else{
			$functions = '';
			$farry = array();
			if(!empty($qx->functions)){
				$farry = explode(',',$qx->functions);
				foreach ($farry as $key=>$val){
					if($val==$function){
						unset($farry[$key]);
					}
				}
				$functions = implode(',',$farry);
			}
		}
		$db->query("update demo_quanxian set functions='$functions' where id=$id");
	}
	echo '{"code":1}';
	exit;
}
function updateZongkuguan(){
	global $db,$request;
	$qx['id'] = (int)$request['id'];
	$qx['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$qx['departs'] = $request['departs'];
	$qx['userIds'] = $request['users'];
	$qx['userNames'] = $request['userNames'];
	$qx['departNames'] = $request['departNames'];
	$qx['storeIds'] = 'all';
	$qx['model'] = 'kucun';
	$qx['functions'] = 'all';
	insert_update('demo_quanxian',$qx,'id');
	if(empty($qx['id'])){
		$qx['id'] = $db->get_var("select last_insert_id();");
	}
	echo '{"code":1,"id":'.$qx['id'].'}';
	exit;
}
function updateFanwei(){
	global $db,$request;
	$qx['id'] = (int)$request['id'];
	$qx['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$qx['departs'] = $request['departs'];
	$qx['userIds'] = $request['users'];
	$qx['userNames'] = $request['userNames'];
	$qx['departNames'] = $request['departNames'];
	$qx['storeIds'] = $request['storeId'];
	$qx['model'] = 'kucun';
	insert_update('demo_quanxian',$qx,'id');
	if(empty($qx['id'])){
		$qx['id'] = $db->get_var("select last_insert_id();");
	}
	echo '{"code":1,"id":'.$qx['id'].'}';
	exit;
}
function editLiucheng(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$content = array();
	$content['if_caiwu'] = (int)$request['if_caiwu'];
	$content['if_chuku'] = (int)$request['if_chuku'];
	$content['if_fahuo'] = (int)$request['if_fahuo'];
	$content['if_shouhuo'] = (int)$request['if_shouhuo'];
	$type = 1;
	$ifhas = $db->get_var("select comId from demo_liucheng where comId=$comId and type=$type");
	$contentStr = json_encode($content);
	if(empty($ifhas)){
		$db->query("insert into demo_liucheng(comId,type,content) value($comId,$type,'$contentStr')");
	}else{
		$db->query("update demo_liucheng set content='$contentStr' where comId=$comId and type=$type");
	}
	$qx['id'] = (int)$request['id_add'];
	$qx['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$qx['departs'] = $request['departs_add'];
	$qx['userIds'] = $request['users_add'];
	$qx['userNames'] = $request['userNames_add'];
	$qx['departNames'] = $request['departNames_add'];
	$qx['model'] = 'dinghuo';
	$qx['functions'] = 'add';
	if(!empty($request['functions']['add'])){
		$qx['functions'].=','.implode(',',$request['functions']['add']);
	}
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_shenhe'];
	$qx['departs'] = $request['departs_shenhe'];
	$qx['userIds'] = $request['users_shenhe'];
	$qx['userNames'] = $request['userNames_shenhe'];
	$qx['departNames'] = $request['departNames_shenhe'];
	$qx['model'] = 'dinghuo';
	$qx['functions'] = 'shenhe';
	if(!empty($request['functions']['shenhe'])){
		$qx['functions'].=','.implode(',',$request['functions']['shenhe']);
	}
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_caiwu'];
	$qx['departs'] = $request['departs_caiwu'];
	$qx['userIds'] = $request['users_caiwu'];
	$qx['userNames'] = $request['userNames_caiwu'];
	$qx['departNames'] = $request['departNames_caiwu'];
	$qx['model'] = 'dinghuo';
	$qx['functions'] = 'caiwu';
	if(!empty($request['functions']['caiwu'])){
		$qx['functions'].=','.implode(',',$request['functions']['caiwu']);
	}
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_chuku'];
	$qx['departs'] = $request['departs_chuku'];
	$qx['userIds'] = $request['users_chuku'];
	$qx['userNames'] = $request['userNames_chuku'];
	$qx['departNames'] = $request['departNames_chuku'];
	$qx['model'] = 'dinghuo';
	$qx['functions'] = 'chuku';
	if(!empty($request['functions']['chuku'])){
		$qx['functions'].=','.implode(',',$request['functions']['chuku']);
	}
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_fahuo'];
	$qx['departs'] = $request['departs_fahuo'];
	$qx['userIds'] = $request['users_fahuo'];
	$qx['userNames'] = $request['userNames_fahuo'];
	$qx['departNames'] = $request['departNames_fahuo'];
	$qx['model'] = 'dinghuo';
	$qx['functions'] = 'fahuo';
	if(!empty($request['functions']['fahuo'])){
		$qx['functions'].=','.implode(',',$request['functions']['fahuo']);
	}
	insert_update('demo_quanxian',$qx,'id');
	redirect('?m=system&s=quanxian&a=dinghuo');
}
function editTLiucheng(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$content = array();
	$content['if_shouhuo'] = (int)$request['if_shouhuo'];
	$content['if_caiwu'] = (int)$request['if_caiwu'];
	$content['if_queren'] = (int)$request['if_queren'];
	$type = 2;
	$ifhas = $db->get_var("select comId from demo_liucheng where comId=$comId and type=$type");
	$contentStr = json_encode($content);
	if(empty($ifhas)){
		$db->query("insert into demo_liucheng(comId,type,content) value($comId,$type,'$contentStr')");
	}else{
		$db->query("update demo_liucheng set content='$contentStr' where comId=$comId and type=$type");
	}
	$qx['id'] = (int)$request['id_add'];
	$qx['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
	$qx['departs'] = $request['departs_add'];
	$qx['userIds'] = $request['users_add'];
	$qx['userNames'] = $request['userNames_add'];
	$qx['departNames'] = $request['departNames_add'];
	$qx['model'] = 'tuihuo';
	$qx['functions'] = 'add';
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_shenhe'];
	$qx['departs'] = $request['departs_shenhe'];
	$qx['userIds'] = $request['users_shenhe'];
	$qx['userNames'] = $request['userNames_shenhe'];
	$qx['departNames'] = $request['departNames_shenhe'];
	$qx['model'] = 'tuihuo';
	$qx['functions'] = 'shenhe';
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_shouhuo'];
	$qx['departs'] = $request['departs_shouhuo'];
	$qx['userIds'] = $request['users_shouhuo'];
	$qx['userNames'] = $request['userNames_shouhuo'];
	$qx['departNames'] = $request['departNames_shouhuo'];
	$qx['model'] = 'tuihuo';
	$qx['functions'] = 'shouhuo';
	insert_update('demo_quanxian',$qx,'id');
	$qx['id'] = (int)$request['id_caiwu'];
	$qx['departs'] = $request['departs_caiwu'];
	$qx['userIds'] = $request['users_caiwu'];
	$qx['userNames'] = $request['userNames_caiwu'];
	$qx['departNames'] = $request['departNames_caiwu'];
	$qx['model'] = 'tuihuo';
	$qx['functions'] = 'caiwu';
	insert_update('demo_quanxian',$qx,'id');
	redirect('?m=system&s=quanxian&a=tuihuo');
}
function churuku(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$ruku_shenpi = empty($request['ruku_shenpi'])?0:1;
		$chuku_shenpi = empty($request['chuku_shenpi'])?0:1;
		$diaobo_shenpi = empty($request['diaobo_shenpi'])?0:1;
		$caigou_shenpi = empty($request['caigou_shenpi'])?0:1;
		$caigou_tuihuo_shenpi = empty($request['caigou_tuihuo_shenpi'])?0:1;
		$db->query("update demo_kucun_set set ruku_shenpi=$ruku_shenpi,chuku_shenpi=$chuku_shenpi,diaobo_shenpi=$diaobo_shenpi,caigou_shenpi=$caigou_shenpi,caigou_tuihuo_shenpi=$caigou_tuihuo_shenpi where comId=$comId");
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		file_put_contents("../cache/kucun_set_".$comId.".php",json_encode($kucun_set,JSON_UNESCAPED_UNICODE));
		$keysarry1 = array();
		$keysarry2 = array();
		$keysarry3 = array();
		$keysarry4 = array();
		$keysarry5 = array();
		if($ruku_shenpi==1){
			if(!empty($request['ruku_shenpi_store'])){
				foreach ($request['ruku_shenpi_store'] as $key => $storeId){
					if(!in_array($storeId,$keysarry1)){
						$keysarry1[] = $storeId;
						$shenpiId = (int)$request['ruku_shenpi_id'][$key];
						$shenpiUser = $request['ruku_shenpi_user'][$key];
						$userId = 0;
						$username = '';
						if(!empty($shenpiUser)&&strlen($shenpiUser)>2){
							$arry = explode('|',$shenpiUser);
							$userId = (int)$arry[0];
							$username = $arry[1];
						}
						if(empty($shenpiId)){
							$db->query("insert into demo_kucun_shenpi(comId,type,storeId,userId,username) value($comId,1,$storeId,$userId,'$username')");
						}else{
							$db->query("update demo_kucun_shenpi set storeId=$storeId,userId=$userId,username='$username' where id=$shenpiId and comId=$comId");
						}
					}
				}
			}
		}else{
			$db->query("delete from demo_kucun_shenpi where comId=$comId and type=1");
		}
		if($chuku_shenpi==1){
			if(!empty($request['chuku_shenpi_store'])){
				foreach ($request['chuku_shenpi_store'] as $key => $storeId){
					if(!in_array($storeId,$keysarry2)){
						$keysarry2[] = $storeId;
						$shenpiId = (int)$request['chuku_shenpi_id'][$key];
						$shenpiUser = $request['chuku_shenpi_user'][$key];
						$userId = 0;
						$username = '';
						if(!empty($shenpiUser)&&strlen($shenpiUser)>2){
							$arry = explode('|',$shenpiUser);
							$userId = (int)$arry[0];
							$username = $arry[1];
						}
						if(empty($shenpiId)){
							$db->query("insert into demo_kucun_shenpi(comId,type,storeId,userId,username) value($comId,2,$storeId,$userId,'$username')");
						}else{
							$db->query("update demo_kucun_shenpi set storeId=$storeId,userId=$userId,username='$username' where id=$shenpiId and comId=$comId");
						}
					}
				}
			}
		}else{
			$db->query("delete from demo_kucun_shenpi where comId=$comId and type=2");
		}
		if($diaobo_shenpi==1){
			if(!empty($request['diaobo_shenpi_store'])){
				foreach ($request['diaobo_shenpi_store'] as $key => $storeId){
					if(!in_array($storeId,$keysarry3)){
						$keysarry3[] = $storeId;
						$shenpiId = (int)$request['diaobo_shenpi_id'][$key];
						$shenpiUser = $request['diaobo_shenpi_user'][$key];
						$userId = 0;
						$username = '';
						if(!empty($shenpiUser)&&strlen($shenpiUser)>2){
							$arry = explode('|',$shenpiUser);
							$userId = (int)$arry[0];
							$username = $arry[1];
						}
						if(empty($shenpiId)){
							$db->query("insert into demo_kucun_shenpi(comId,type,storeId,userId,username) value($comId,3,$storeId,$userId,'$username')");
						}else{
							$db->query("update demo_kucun_shenpi set storeId=$storeId,userId=$userId,username='$username' where id=$shenpiId and comId=$comId");
						}
					}
				}
			}
		}else{
			$db->query("delete from demo_kucun_shenpi where comId=$comId and type=3");
		}
		if($caigou_shenpi==1){
			if(!empty($request['caigou_shenpi_store'])){
				foreach ($request['caigou_shenpi_store'] as $key => $storeId){
					if(!in_array($storeId,$keysarry4)){
						$keysarry4[] = $storeId;
						$shenpiId = (int)$request['caigou_shenpi_id'][$key];
						$shenpiUser = $request['caigou_shenpi_user'][$key];
						$userId = 0;
						$username = '';
						if(!empty($shenpiUser)&&strlen($shenpiUser)>2){
							$arry = explode('|',$shenpiUser);
							$userId = (int)$arry[0];
							$username = $arry[1];
						}
						if(empty($shenpiId)){
							$db->query("insert into demo_kucun_shenpi(comId,type,storeId,userId,username) value($comId,4,$storeId,$userId,'$username')");
						}else{
							$db->query("update demo_kucun_shenpi set storeId=$storeId,userId=$userId,username='$username' where id=$shenpiId and comId=$comId");
						}
					}
				}
			}
		}else{
			$db->query("delete from demo_kucun_shenpi where comId=$comId and type=4");
		}
		if($caigou_tuihuo_shenpi==1){
			if(!empty($request['caigou_tuihuo_shenpi_store'])){
				foreach ($request['caigou_tuihuo_shenpi_store'] as $key => $storeId){
					if(!in_array($storeId,$keysarry5)){
						$keysarry5[] = $storeId;
						$shenpiId = (int)$request['caigou_tuihuo_shenpi_id'][$key];
						$shenpiUser = $request['caigou_tuihuo_shenpi_user'][$key];
						$userId = 0;
						$username = '';
						if(!empty($shenpiUser)&&strlen($shenpiUser)>2){
							$arry = explode('|',$shenpiUser);
							$userId = (int)$arry[0];
							$username = $arry[1];
						}
						if(empty($shenpiId)){
							$db->query("insert into demo_kucun_shenpi(comId,type,storeId,userId,username) value($comId,5,$storeId,$userId,'$username')");
						}else{
							$db->query("update demo_kucun_shenpi set storeId=$storeId,userId=$userId,username='$username' where id=$shenpiId and comId=$comId");
						}
					}
				}
			}
		}else{
			$db->query("delete from demo_kucun_shenpi where comId=$comId and type=5");
		}
		redirect("?m=system&s=quanxian&a=kucun");
	}
}