<?php
namespace Zhishang;
class Kehu{
	public function lists(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$keyword = $request['keyword'];
		$page = empty($request['page'])?1:(int)$request['page'];
		$pageNum = empty($request['pagenum'])?10:(int)$request['pagenum'];
		$sql = "select id,title,address,dtTime,level from demo_kehu where comId=$comId and userId=$userId";
		if(!empty($keyword)){
			$sql.=" and (title like '%$keyword%' or name like '%$keyword%' or phone='$keyword')";
		}
		$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
		$kehus = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		if(!empty($kehus)){
			foreach ($kehus as $kehu) {
				$kehu->dtTime = date("Y-m-d",strtotime($kehu->dtTime));
				$kehu->level = $db->get_var("select title from demo_kehu_level where id=$kehu->level and comId=$comId");
				$return['data'][] = $kehu;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function levels(){
		global $db,$request,$comId;
		$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = $levels;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function create(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$kehu = array();
		$kehu['id'] = (int)$request['kehu_id'];
		$kehu['comId'] = $comId;
		$kehu['title'] = $request['title'];
		$kehu['level'] = (int)$request['level'];
		$kehu['sn'] = $request['sn'];
		if(empty($kehu['title']) || empty($kehu['level'])){
			return '{"code":0,"message":"title和level不能为空"}';
		}
		$ifhas = $db->get_var("select id from demo_kehu where comId=$comId and title='".$kehu['title']."' and id<>".$kehu['id']." limit 1");
		//echo "select id from demo_kehu where comId=$comId and title='".$kehu['title']."' and id<>".$kehu['id']." limit 1";
		if($ifhas>0){
			return '{"code":0,"message":"客户名称已存在"}';
		}
		if(!empty($request['username'])){
			$ifhas = $db->get_var("select id from demo_kehu where comId=$comId and username='".$request['username']."' and id<>".$kehu['id']." limit 1");
			if($ifhas>0){
				return '{"code":0,"message":"用户名已存在，请重新输入"}';
			}
		}
		$db_service = get_zhishang_db();
		$user = $db_service->get_row("select name,department from demo_user where id=$userId");
		$kehu['storeId'] = (int)$db->get_var("select storeIds from demo_quanxian where comId=$comId and(find_in_set($userId,userIds) or find_in_set($user->department,departs)) and model='kucun' limit 1");
		$kehu['userId'] = $userId;
		$kehu['uname'] = $user->name;
		$kehu['departId'] = $user->department;
		$kehu['areaId'] = (int)$request['area_id'];
		$kehu['address'] = $request['address'];
		$kehu['name'] = $request['name'];
		$kehu['phone'] = $request['phone'];
		require_once(ABSPATH.'/inc/class.shlencryption.php');
		$kehu['username'] = $request['username'];
		if(!empty($request['password'])){
			$shlencryption = new \shlEncryption($request['password']);
	  		$kehu['password'] = $shlencryption->to_string();
	  	}
		$kehu['linkPhone'] = 1;
		$request['caiwu'] = str_replace('"{','{',$request['caiwu']);
		$request['caiwu'] = str_replace('}"','}',$request['caiwu']);
		$request['caiwu'] = str_replace('\\"','"',$request['caiwu']);
		$kehu['caiwu'] = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$request['caiwu']));
		$kehu['status'] = 1;
		$kehu['dtTime'] = date("Y-m-d H:i:s");
		$kehuId = $db->insert_update('demo_kehu',$kehu,'id');
		//echo $kehuId;
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
			$db->insert_update('demo_kehu_address',$kehu_address,'id');
		}
		return '{"code":1,"message":"成功","kehu_id":'.$kehuId.'}';
	}
	public function info(){
		global $db,$request,$comId;
		$kehuId = (int)$request['kehu_id'];
		$kehu = $db->get_row("select * from demo_kehu where id=$kehuId and comId=$comId");
		if(empty($kehu)){
			return '{"code":0,"message":"客户不存在"}';
		}
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['kehu_id'] = $kehu->id;
		$return['title'] = $kehu->title;
		$return['level'] = $kehu->level;
		$return['level_name'] = $db->get_var("select title from demo_kehu_level where id=$kehu->level");
		$return['sn'] = $kehu->sn;
		$return['area_id'] = $kehu->areaId;
		$area = $db->get_row("select parentId,title from demo_area where id=$kehu->areaId");
		$return['area_name'] = $area->title;
		$return['shi_id'] = $area->parentId;
		$return['shi_name'] = '';
		$return['sheng_id'] = 0;
		$return['sheng_name'] = '';
		if($area->parentId>0){
			$area1 = $db->get_row("select parentId,title from demo_area where id=$area->parentId");
			$return['sheng_id'] = $area1->parentId;
			$return['shi_name'] = $area1->title;
			if($return['sheng_id']>0){
				$return['sheng_name'] = $db->get_var("select title from demo_area where id=$area1->parentId");
			}
		}
		$return['address'] = $kehu->address;
		$return['name'] = $kehu->name;
		$return['phone'] = $kehu->phone;
		$return['username'] = $kehu->username;
		$return['caiwu'] = json_decode($kehu->caiwu);
		//$address = $db->get_row("select id,name,phone,areaId,address from demo_kehu_address where kehuId=$kehu->id order by moren desc,id desc limit 1");
		//$return['address']  =$address;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function editcaiwu(){
		global $db,$request;
		$kehu = array();
		$kehu['id'] = (int)$request['kehu_id'];
		$request['caiwu'] = str_replace('\\"','"',$request['caiwu']);
		$kehu['caiwu'] = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$request['caiwu']));
		$db->insert_update('demo_kehu',$kehu,'id');
		return '{"code":1,"message":"成功"}';
	}
	public function editaddress(){
		global $db,$request,$comId;
		$address = array();
		$address['id'] = (int)$request['address_id'];
		$address['kehuId'] = (int)$request['kehu_id'];
		$address['name'] = $request['name'];
		$address['phone'] = $request['phone'];
		$address['areaId'] = (int)$request['area_id'];
		$address['areaName'] = getAreaName($address['areaId']);
		$address['address'] = $request['address'];
		$address['moren'] = (int)$request['if_moren'];
		if($address['moren']==1){
			$db->query("update demo_kehu_address set moren=0 where kehuId=".$address['kehuId']);
		}
		$db->insert_update('demo_kehu_address',$address,'id');
		return '{"code":1,"message":"成功"}';
	}
	public function account(){
		global $db,$request,$comId;
		$kehuId = (int)$request['kehu_id'];
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$zongs = $db->get_results("select type,sum(money) as money from demo_kehu_account where comId=$comId and kehuId=$kehuId group by type");
		$return['account1'] = 0.00;
		$return['account2'] = 0.00;
		$return['account3'] = 0.00;
		if(!empty($zongs)){
		    foreach ($zongs as $z) {
		        if($z->type==1){
		            $return['account1'] = $z->money;
		        }else if($z->type==2){
		            $return['account2'] = $z->money;
		        }else if($z->type==3){
		            $return['account3'] = $z->money;
		        }
		    }
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function banklist(){
		global $db,$request,$comId;
		$return = array();
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['data'] = array();
		$banks = $db->get_results("select id,bank_name,bank_user,bank_account from demo_kehu_bank where comId=$comId and status=1 ");
		if(!empty($banks)){
			$return['data'] = $banks;
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
}