<?php
namespace Zhishang;
class Shequ{
	function dingwei(){
		global $db,$request,$comId;
		$latitude = $request['latitude'];//纬度
		$longitude = $request['longitude'];//经度
		$str = file_get_contents("http://api.map.baidu.com/reverse_geocoding/v3/?ak=cIUKusewZaKmqALQv6lKtIcY&output=json&coordtype=wgs84ll&location=".$latitude.",".$longitude);
		//file_put_contents('request.txt',$str);
		/*转换成百度坐标系*/
		$zuobiaoinfo = file_get_contents('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude);
		$zuobiao = json_decode($zuobiaoinfo,true);
		$latitude = base64_decode($zuobiao['y']);
		$longitude = base64_decode($zuobiao['x']);
		//echo $longitude;echo $latitude;
		$str = json_decode($str, true); 
		$city = $str['result']['addressComponent']['city'];//定位城市
		$district = $str['result']['addressComponent']['district'];//定位市区
		if($city){
			$cityid = $db->get_row("select id,title from demo_area where title like '".$city."%' limit 1");
			if(!empty($cityid)){
				$area = $db->get_row("select id,title from demo_area where parentId=$cityid->id and title like '$district%' limit 1");
				$areas = $db->get_results("select id,title from demo_area where parentId=$cityid->id");
				$return = array();
				$return['code'] = 1;
				$return['city'] = $cityid->id;
				$return['title'] = $cityid->title;
				$return['areas'] = $areas;
				$return['area_id'] = (int)$area->id;
				$return['area_title'] = $area->title;
				$return['address'] = $str['result']['formatted_address'];
				$shequs = $db->get_results("select id,title,name,phone,weixin,address,peisong_area,userId,originalPic,(st_distance (point(longitude,Latitude),point ($longitude,$latitude))*111195) as distance from demo_shequ where comId=$comId and status=1 order by distance asc limit 50");
				$return['data'] = array();
				if(!empty($shequs)){
					foreach ($shequs as $pdt){
						if($pdt->distance>10000){
							$pdt->distance = '>10公里';
						}else if($pdt->distance>1000){
							$pdt->distance = getXiaoshu($pdt->distance/1000,1).'公里';
						}else{
							$pdt->distance = $pdt->distance.'米';
						}
						//$pdt->tuanzhang = $db->get_var("select nickname from users where id=$pdt->userId");
						$pdt->originalPic = ispic($pdt->originalPic);
						$return['data'][] = $pdt;
					}
				}
				//file_put_contents('request.txt',json_encode($return,JSON_UNESCAPED_UNICODE));
				return json_encode($return,JSON_UNESCAPED_UNICODE);
			}else{
				return '{"code":0,"message":"定位失败"}';
			}
		}
		return '{"code":0,"message":"定位失败"}';
	}
	
	function listsBak(){
		global $db,$request,$comId;
		
		$area_id = (int)$request['area_id'];
		$shi_id = (int)$request['shi_id'];
		$keyword = $request['keyword'];
		$latitude =  empty($request['latitude'])?0:$request['latitude'];//纬度
		$longitude = empty($request['longitude'])?0:$request['longitude'];//经度
		$zuobiaoinfo = file_get_contents('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude);
		$zuobiao = json_decode($zuobiaoinfo,true);
		$latitude = base64_decode($zuobiao['y']);
		$longitude = base64_decode($zuobiao['x']);
		$order1 = 'id';
		$order2 = 'desc';
		$sql="select id,title,name,phone,weixin,address,peisong_area,userId,originalPic,longitude,Latitude,(st_distance (point(longitude,Latitude),point($longitude,$latitude))*111195) as distance from demo_shequ where comId=$comId and status=1";
		if($area_id>0){
			$sql.=" and (areaId=$area_id or find_in_set($area_id,areaIds))";
		}else if($shi_id>0){
			$sql.=" and shiId=$shi_id";
		}
		if(!empty($keyword)){
			$sql.=" and title like '%$keyword%'";
		}
		$count = $db->get_var(str_replace("id,title,name,phone,weixin,address,peisong_area,userId,originalPic,(st_distance (point(longitude,Latitude),point($longitude,$latitude))*111195) as distance",'count(*)',$sql));
		$sql.=" order by distance asc";
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				//$pdt->tuanzhang = $db->get_var("select nickname from users where id=$pdt->userId");
				//$pdt->originalPic = ispic($pdt->originalPic);
				if($pdt->distance>1000000){
					$pdt->distance = '>1000公里';
				}else if($pdt->distance>1000){
					$pdt->distance = getXiaoshu($pdt->distance/1000,1).'公里';
				}else{
					$pdt->distance = $pdt->distance.'米';
				}
				$pdt->originalPic = $db->get_var("select image from users where id=$pdt->userId");
				$return['data'][] = $pdt;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	function lists(){
		global $db,$request,$comId;
		
		$area_id = (int)$request['area_id'];
		$shi_id = (int)$request['shi_id'];
		$keyword = $request['keyword'];
		$latitude =  empty($request['latitude'])?0:$request['latitude'];//纬度
		$longitude = empty($request['longitude'])?0:$request['longitude'];//经度
		$userId = (int)$request['user_id'];
		if($userId == 2886){
		    $latitude = 40.60997;//兴成
		    $longitude = 120.734192;
		    
		  //  $latitude = 33.064362;//安徽
		  //  $longitude = 115.270814;
		} 

		$zuobiaoinfo = file_get_contents('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude);
     
		$zuobiao = json_decode($zuobiaoinfo,true);
		$latitude = base64_decode($zuobiao['y']);
		$longitude = base64_decode($zuobiao['x']);
		$order1 = 'id';
		$order2 = 'desc';
		$sql="select id,title,status,name,phone,address, weixin, peisong_area,peisong_time,areaId,userId,originalPic,longitude,Latitude,(st_distance (point(longitude,Latitude),point($longitude,$latitude))*111195) as distance from demo_shequ where comId=$comId and status =1 ";
		if($area_id>0){
			$sql.=" and (areaId=$area_id or find_in_set($area_id,areaIds))";
		}else if($shi_id>0){
			$sql.=" and shiId=$shi_id";
		}
		if(!empty($keyword)){
			$sql.=" and title like '%$keyword%'";
		}
		
		$count = $db->get_var(str_replace("id,title,status,name,phone,address, weixin, peisong_area,peisong_time,areaId,userId,originalPic,(st_distance (point(longitude,Latitude),point($longitude,$latitude))*111195) as distance",'count(*)',$sql));
		$sql.=" order by distance asc";
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		$fangyuan = (int)$db->get_var("select com_remark from demo_shezhi where comId = $comId ");
		if(!$fangyuan) $fangyuan = 5000;
		
		if($userId == 2886){
		    $fangyuan = 150000;
		}
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
			    $pdt->distance = getDistance($latitude, $longitude,  $pdt->Latitude, $pdt->longitude);
			    
				if($pdt->distance> 1000){
		          //  continue;
		            $pdt->distance = getXiaoshu($pdt->distance/1000,1).'公里';
				}else if($pdt->distance>1000){
					$pdt->distance = getXiaoshu($pdt->distance/1000,1).'公里';
				}else{
					$pdt->distance = $pdt->distance.'米';
				}
				// $pdt->originalPic = $db->get_var("select image from users where id=$pdt->userId");
				$pdt->area = $db->get_var("select title from demo_area where id = $pdt->areaId " );
				
				$return['data'][] = $pdt;
			}
		}
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	function detail(){
		global $db,$request,$comId;
		
		$shequId = (int)$request['id'];
		$sql="select id,title,name,phone,weixin,address,peisong_area,userId,originalPic as image,longitude,Latitude from demo_shequ where id=$shequId";
		$info = $db->get_row($sql);
		
		$latitude =  empty($request['latitude'])?0:$request['latitude'];//纬度
		$longitude = empty($request['longitude'])?0:$request['longitude'];//经度
		$userId = (int)$request['user_id'];
		if($userId == 2886){
		    $latitude = 40.60997;//兴成
		    $longitude = 120.734192;
		    
		  //  $latitude = 33.064362;//安徽
		  //  $longitude = 115.270814;
		} 
		$zuobiaoinfo = file_get_contents('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude);
     
		$zuobiao = json_decode($zuobiaoinfo,true);
		$latitude = base64_decode($zuobiao['y']);
		$longitude = base64_decode($zuobiao['x']);
		$info->distance = $info->real_distance = getDistance($latitude, $longitude,  $info->Latitude, $info->longitude);
		if($info->distance> 1000){
            $info->distance = getXiaoshu($info->distance/1000,1).'公里';
		}else if($info->distance>1000){
			$info->distance = getXiaoshu($info->distance/1000,1).'公里';
		}else{
			$info->distance = $info->distance.'米';
		}
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '获取成功';
		$return['data'] = $info;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	function info(){
		global $db,$request,$comId;
		
		$shop_shezhi = $db->get_row("select com_title,kaipiao_type,com_logo,if_dianzi_fapiao,shequ_yunfei,peisong_times,tihuo_info from demo_shezhi where comId=$comId");
		$shequ_yunfei_str = $shop_shezhi->shequ_yunfei;
		$peisong_types = array();
		$peisong_qisong = 0;
		$peisong_qisong1 = 0;
		$peisongfei = 0;
		if(!empty($shequ_yunfei_str)){
		    $shequ_yunfei = json_decode($shequ_yunfei_str);
		    $peisong_qisong = empty($shequ_yunfei->peisong_qisong)?0:$shequ_yunfei->peisong_qisong;
		    $peisong_qisong1 = empty($shequ_yunfei->peisong_qisong1)?0:$shequ_yunfei->peisong_qisong1;
		    if(!empty($shequ_yunfei->peisong_types)){
		        $peisong_types = explode(',',$shequ_yunfei->peisong_types);
		    }else{
		        $peisong_types = array(1,2,3);
		    }
		    $peisongfei = $shequ_yunfei->peisong_money;
		}else{
		    $peisong_types = array(1,2,3);
		}
		$peisong_times = array();
		if(!empty($shop_shezhi->peisong_times)){
			$peisong_times = explode('@_@', $shop_shezhi->peisong_times);
		}
		$tihuo_info = '';
		if(!empty($shop_shezhi->tihuo_info)){
			$tihuoInfo = json_decode($shop_shezhi->tihuo_info);
			$tihuo_info = $tihuoInfo->desc;
		}
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['peisong_types'] = $peisong_types;
		$return['data']['peisong_times'] = $peisong_times;
		$return['data']['peisong_qisong'] = $peisong_qisong;
		$return['data']['peisong_money'] = $peisongfei;
		$return['data']['if_peisong_man'] = empty($shequ_yunfei->peisong_man)?0:1;
		$return['data']['peisong_man_money'] = $shequ_yunfei->peisong_man;
		$return['data']['ziti_qisong'] = $peisong_qisong1;
		$return['data']['tihuo_desc'] = $tihuo_info;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	function bindShequ(){
		global $db,$request,$comId;
		
		$shequ_id = (int)$request['shequ_id'];
		$userId = (int)$request['user_id'];
		$db->query("update users set shequ_id=$shequ_id where id=$userId");
		
		return '{"code":1,"message":"绑定成功"}';
	}
	
	function applyShequ(){
		global $db,$request,$comId;
		
		$userId = (int)$request['user_id'];
		$if_shequ_tuan = $db->get_var("select if_shequ_tuan from users where id=$userId");
		if($if_shequ_tuan==1){
			return '{"code":0,"message":"您已经是社区团长了，请不要重复申请"}';
		}
		$if_shenqing = $db->get_var("select id from demo_shequ_shenqing where comId=$comId and userId=$userId and status>-1 limit 1");
		if($if_shenqing>0){
			return '{"code":0,"message":"请不要重复申请"}';
		}
		$shenqing = array();
		$shenqing['comId'] = $comId;
		$shenqing['userId'] = $userId;
		$shenqing['name'] = $request['name'];
		$shenqing['phone'] = $request['phone'];
		$shenqing['title'] = $request['title'];
		$shenqing['address'] = $request['address'];
		$shenqing['weixin'] = $request['weixin'];
		$shenqing['tuijianren'] = $request['tuijianren'];
		$shenqing['remark'] = $request['remark'];
		$shenqing['shenfenzheng'] = $request['shenfenzheng'];
		$shenqing['originalPic'] = $request['originalPic'];
		$shenqing['dtTime'] = date("Y-m-d H:i:s");
		$db->insert_update('demo_shequ_shenqing',$shenqing,'id');
		
		return '{"code":1,"message":"申请成功，请等待管理员审核"}';
	}
}