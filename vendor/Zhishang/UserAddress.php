<?php
namespace Zhishang;
class UserAddress{
	public function lists(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$keyword = $request['keyword'];
		$page = empty($request['page'])?1:(int)$request['page'];
		$pageNum = empty($request['pagenum'])?10:(int)$request['pagenum'];
		$sql = "select * from user_address where userid=$userId and comId=$comId";
		if(!empty($keyword)){
			$sql.=" and (name like '%".$keyword."%' or phone like '%".$keyword."%' or address like '%".$keyword."%' or areaName like '%".$keyword."%')";
		}
		$sql.=" order by moren desc,id desc limit ".(($page-1)*$pageNum).",".$pageNum;
		$addresss = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		if(!empty($addresss)){
			foreach ($addresss as $addr) {
				$data = array();
				$data['id'] = $addr->id;
				$data['name'] = $addr->name;
				$data['phone'] = $addr->phone;
				$data['areaName'] = $addr->areaName;
				$data['address'] = $addr->address;
				$data['if_default'] = $addr->moren;
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function detail(){
		global $db,$request,$comId;
		$id = (int)$request['id'];
		$address = $db->get_row("select * from user_address where id=$id");
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['name'] = $address->name;
		$return['data']['phone'] = $address->phone;
		$return['data']['areaName'] = $address->areaName;
		$return['data']['address'] = $address->address;
		$return['data']['if_default'] = $address->moren;
		$firstId=0;$firstName='';
		$secondId=0;$secondName='';
		$thirdId=0;$thirdName='';
		if($address->id>0){
		    $area = $db->get_row("select * from demo_area where id=".$address->areaId);
		    if($area->parentId==0){
		        $firstId = $area->id;
		        $firstName = $area->title;
		    }else{
		        $firstId = $area->parentId;
		        $secondId = $area->id;
		        $secondName = $area->title;
		        $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
		        if($farea->parentId!=0){
		            $firstId = $farea->parentId;
		            $secondId = $farea->id;
		            $secondName = $farea->title;
		            $thirdId=$area->id;
		            $thirdName = $area->title;
		        }
		        $firstName = $db->get_var("select title from demo_area where id=$firstId");
		    }
		}
		$return['data']['province_id'] = $firstId;
		$return['data']['province_name'] = $firstName;
		$return['data']['city_id'] = $secondId;
		$return['data']['city_name'] = $secondName;
		$return['data']['area_id'] = $thirdId;
		$return['data']['area_name'] = $thirdName;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	public function delete(){
		global $db,$request,$comId;
		$id = (int)$request['id'];
		$userId = (int)$request['user_id'];
		$db->query("delete from user_address where id=$id and comId=$comId and userId=$userId");
		return '{"code":1,"message":"删除成功！"}';
	}
	public function setDefault(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$id = (int)$request['id'];
		$db->query("update user_address set moren=0 where userId=$userId and comId=$comId");
		$db->query("update user_address set moren=1 where id=$id and userId=$userId and comId=$comId");
		return '{"code":1,"message":"操作成功！"}';
	}
	public function add(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		if($request['name']==''||$request['phone']==''||$request['area_id']==''||$request['address']==''){
			return '{"code":0,"message":"所有字段不能有空值！"}';
		}
		$address = array();
		$address['id'] = (int)$request['id'];
		$address['comId'] = $comId;
		$address['userId'] = $userId;
		$address['name'] = $request['name'];
		$address['phone'] = $request['phone'];
		$address['areaId'] = $request['area_id'];
		$address['areaName'] = $request['province_name'].'-'.$request['city_name'].'-'.$request['area_name'];
		$address['address'] = $request['address'];
		$address['moren'] = (int)$request['moren'];
		if($address['moren']==1){
			$db->query("update user_address set moren=0 where userId=$userId and comId=$comId");
		}
		$db->insert_update('user_address',$address,'id');
		return '{"code":1,"message":"操作成功！"}';
	}
}