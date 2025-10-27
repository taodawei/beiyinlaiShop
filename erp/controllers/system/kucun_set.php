<?php
function index(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$dinghuo_store = (int)$request['dinghuo_store'];
		$dinghuo_limit = empty($request['dinghuo_limit'])?0:1;
		$kucun_type = (int)$request['kucun_type'];
		$chuku_limit = (int)$request['chuku_limit'];
		$db->query("update demo_kucun_set set dinghuo_store=$dinghuo_store,dinghuo_limit=$dinghuo_limit,kucun_type=$kucun_type,chuku_limit=$chuku_limit where comId=$comId");
		$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		file_put_contents("../cache/kucun_set_".$comId.".php",json_encode($kucun_set,JSON_UNESCAPED_UNICODE));
		redirect("?m=system&s=kucun_set");
	}
}
function churuku(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$ruku_pre = $request['ruku_pre'];
		$chuku_pre = $request['chuku_pre'];
		$diaobo_pre = $request['diaobo_pre'];
		$caigou_pre = $request['caigou_pre'];
		$caigou_tuihuo_pre = $request['caigou_tuihuo_pre'];
		$ruku_types = '';
		if(!empty($request['ruku_types'])){
			$request['ruku_types'] = array_unique($request['ruku_types']);
			$ruku_types = implode('@_@',array_filter($request['ruku_types']));
		}
		$chuku_types = '';
		if(!empty($request['chuku_types'])){
			$request['chuku_types'] = array_unique($request['chuku_types']);
			$chuku_types = implode('@_@',array_filter($request['chuku_types']));
		}
		$ruku_shenpi = empty($request['ruku_shenpi'])?0:1;
		$chuku_shenpi = empty($request['chuku_shenpi'])?0:1;
		$diaobo_shenpi = empty($request['diaobo_shenpi'])?0:1;
		$caigou_shenpi = empty($request['caigou_shenpi'])?0:1;
		$caigou_tuihuo_shenpi = empty($request['caigou_tuihuo_shenpi'])?0:1;
		$db->query("update demo_kucun_set set ruku_pre='$ruku_pre',chuku_pre='$chuku_pre',diaobo_pre='$diaobo_pre',caigou_pre='$caigou_pre',caigou_tuihuo_pre='$caigou_tuihuo_pre',ruku_types='$ruku_types',chuku_types='$chuku_types',ruku_shenpi=$ruku_shenpi,chuku_shenpi=$chuku_shenpi,diaobo_shenpi=$diaobo_shenpi,caigou_shenpi=$caigou_shenpi,caigou_tuihuo_shenpi=$caigou_tuihuo_shenpi where comId=$comId");
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
		redirect("?m=system&s=kucun_set&a=churuku");
	}
}