<?php
require_once('aliyunoss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;
function index(){}
function search(){}
function ruzhu1(){}
function tc(){}
function shop(){}
function shops(){}
function shequ_map(){}
function get_shops(){
	global $db,$request;
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=11;
	$sql = "select comId from demo_shops where if_tongbu=1 and if_tongbu_pdt=1 and comId<>969 ";
    $count = $db->get_var(str_replace('comId ','count(*) ',$sql));
    $res = $db->get_results($sql."order by ordering desc,comId asc limit ".(($page-1)*$pageNum).",".$pageNum);
    $return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
    if($res){
      foreach ($res as $key) {
      	$a = array();
      	$a['id'] = $key->comId;
      	$shezhi = $db->get_row("select com_title,com_logo,com_back,zhishang_back from demo_shezhi where comId=$key->comId");
        $pro_num = $db->get_var("select count(*) from demo_product where comId=$key->comId");
        $a['img'] = empty($shezhi->zhishang_back)?$shezhi->com_back:$shezhi->zhishang_back;
        $a['logo'] = $shezhi->com_logo;
        $a['pro_num'] = $pro_num;
        $a['com_title'] = sys_substr($shezhi->com_title,8,false);
      	$return['data'][] = $a;
      }
  	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function upload_content(){
	global $db,$request;
	$accessKeyId = "LTAIOgimVmDhlkck";
	$accessKeySecret = "MJkk2G2SJllFVx5ehTbyAvC6Kton0L";
	$endpoint = "http://oss-cn-beijing.aliyuncs.com";
	$bucket= "zhishang-kucun";
	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
	global $db,$request;
	$comId = 10;
	$imgstr = $request['content'];
	if(empty($imgstr)){
		echo '{"code":0,"msg":"未检测到上传的内容","url":""}';
		exit;
	}
	list($type,$data) = explode(',', $imgstr);
    if(strstr($type,'image/jpeg')!==''){
        $ext = '.jpg';  
    }elseif(strstr($type,'image/gif')!==''){
        $ext = '.gif';  
    }elseif(strstr($type,'image/png')!==''){
        $ext = '.png';  
    }elseif(strstr($type,'image/bmp')!==''){
        $ext = '.bmp';  
    }else{
    	$ext = '.png'; 
    }
    $decodedData = base64_decode(str_replace(' ','+',$data));
	$picsize = strlen($decodedData);
	$fileName = $comId.'/'.$comId.'_'.date("YmdHis").rand(1,999).$ext;
	try {
		$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
		$ossClient->putObject($bucket,$fileName, $decodedData);
		$image = "https://zhishang-kucun.oss-cn-beijing.aliyuncs.com/".$fileName;
		if($request['touxiang']==1){
			if($_SESSION['if_tongbu']==1){
				$db_service = getCrmDb();
				$db_service->query("update demo_user set image='$image' where id=".(int)$_SESSION[TB_PREFIX.'zhishangId']);
			}else{
				$db->query("update users set image='$image' where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
			}
			
		}
		echo '{"code":1,"msg":"上传成功","url":"'.$image.'"}';
		exit;
	} catch (OssException $e) {
		file_put_contents('upload_log.txt',$e->getMessage());
		echo '{"code":0,"msg":"文件上传失败，请重试","url":""}';
		exit;
	}
}
function dingwei(){
	global $db,$request;
	$latitude = $request['latitude'];//纬度
	$longitude = $request['longitude'];//经度
	$str = file_get_contents("http://api.map.baidu.com/reverse_geocoding/v3/?ak=cIUKusewZaKmqALQv6lKtIcY&output=json&coordtype=wgs84ll&location=".$latitude.",".$longitude);
	//file_put_contents('request.txt',$str);
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
			//file_put_contents('request.txt',json_encode($return,JSON_UNESCAPED_UNICODE));
			echo json_encode($return,JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	echo '{"code":0}';
	exit;
}
function update_shequ(){
	global $db,$request;
	$request['shequ_id'] = (int)$request['shequ_id'];
	$url = $request['url'];
	$db->query("update users set shequ_id=".$request['shequ_id']." where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
	$shequ = $db->get_row("select title,areaId,originalPic from demo_shequ where id=".$request['shequ_id']);
	$_SESSION[TB_PREFIX.'shequ_id'] = $request['shequ_id'];
	$_SESSION[TB_PREFIX.'shequ_title'] = $shequ->title;
	$_SESSION[TB_PREFIX.'shequ_img'] = $shequ->originalPic;
	$_SESSION[TB_PREFIX.'sale_area'] = (int)$shequ->areaId;
	if(!empty($url)){
		redirect(urldecode($url));
	}else{
		redirect('/index.php');
	}
}
function update_area(){
	global $db,$request;
	$request['sale_area'] = (int)$request['sale_area'];
	$db->query("update users set city=".$request['sale_area']." where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
	$_SESSION[TB_PREFIX.'sale_area'] = $request['sale_area'];
	echo '{"code":1}';
}
//2018.12.21  by zyc
//商圈分类别表
function shangquan_channel(){
	global $db,$request;
}
//获取商品分类
function get_pdt_channels(){
	global $db,$request;
	$channelId = (int)$request['channelId'];
	$parenttitle = $db->get_var("select title from demo_pdt_channel where id=$channelId order by ordering desc,id asc");
	$channels = $db->get_results("select id,title from demo_pdt_channel where parentId=$channelId order by ordering desc,id asc");
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['parenttitle'] = $parenttitle;
	$return['data'] = array();
	if(!empty($channels)){
		foreach ($channels as $val) {
			$val->originalPic = ispic($val->originalPic);
			$return['data'][] = $val;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function guanzhu(){
	global $db,$request;
	$shopId = (int)$_SESSION['demo_comId'];
	$userId = (int)$_SESSION['demo_zhishangId'];
	if(!empty($request['shopId'])){
		$shopId = (int)$request['shopId'];
	}
	//$ifguanzhu = (int)$request['ifguanzhu'];
	if(empty($userId)){
		die('{"code":0,"message":"请先登录"}');
	}
	$ifguanzhu = (int)$db->get_var("select count(*) from user_shop_collect where userId=$userId and shopId=$shopId");
	if($ifguanzhu==1){
		$db->query("delete from user_shop_collect where userId=$userId and shopId=$shopId");
		die('{"code":1,"guanzhu":0,"message":"取消关注成功"}');
	}else{
		$db->query("insert into user_shop_collect(userId,shopId,dtTime) values($userId,$shopId,'".date("Y-m-d H:i:s")."')");
		die('{"code":1,"guanzhu":1,"message":"关注成功"}');
	}
}
//社区购相关
function select_shequ(){}
function select_shi(){}
function get_shequ_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$area_id = (int)$request['area_id'];
	$shi_id = (int)$request['shi_id'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=20;
	$order1 = 'id';
	$order2 = 'desc';
	$sql="select * from demo_shequ where comId=$comId and status=1";
	if($area_id>0){
		$sql.=" and (areaId=$area_id or find_in_set($area_id,areaIds))";
	}else if($shi_id>0){
		$sql.=" and shiId=$shi_id";
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	if(!empty($pdts)){
		foreach ($pdts as $pdt){
			$pdt->tuanzhang = $db->get_var("select nickname from users where id=$pdt->userId");
			$pdt->originalPic = ispic($pdt->originalPic);
			$return['data'][] = $pdt;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function get_area_list(){
	global $db,$request;
	$shi_id = (int)$request['shi_id'];
	$areas = $db->get_results("select id,title from demo_area where parentId=$shi_id");
	$return = array();
	$return['code'] = 1;
	$return['areas'] = $areas;
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function shenqing_tuanzhang(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if(empty($userId)){
		redirect('/index.php?p=8&a=login');
	}
	if($request['tijiao']==1){
		$if_shequ_tuan = $db->get_var("select if_shequ_tuan from users where id=$userId");
		if($if_shequ_tuan==1){
			echo '<script>alert("您已经是社区团长了，请不要重复申请");location.href="/index.php?p=8";</script>';
			exit;
		}
		$shenqing = array();
		$shenqing['comId'] = (int)$_SESSION['demo_comId'];
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
		$id = $db->insert_update('demo_shequ_shenqing',$shenqing,'id');
		addTaskMsg(52,$id,'您的商城有新的社区团长申请，请及时处理',$_SESSION['demo_comId']);
		echo '<script>alert("申请成功，请等待管理员审核");location.href="/index.php";</script>';
		exit;
	}
}
function shenqing1113(){
	global $db,$request;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	$comId = (int)$_SESSION['demo_comId'];
	if(empty($userId)){
		redirect('/index.php?p=8&a=login');
	}
	if($request['tijiao']==1){
		if($_SESSION[TB_PREFIX.'user_level']==88){
			echo '<script>alert("您已是经销商了");location.href="/index.php";</script>';
			exit;
		}
		$ifhas = $db->get_var("select id from demo_level_shenqing where comId=$comId and userId=$userId and status>-1 limit 1");
		if($ifhas>0){
			echo '<script>alert("请不要重复申请");location.href="/index.php";</script>';
			exit;
		}
		$content = array();
		$content['img_id'] = $request['originalPic'];
		$content['name'] = $request['name'];
		$content['phone'] = $request['phone'];
		$content['address'] = $request['address'];
		$shenqing = array();
		$shenqing['comId'] = $comId;
		$shenqing['userId'] = $userId;
		$shenqing['toLevel'] = (int)$request['toLevel'];
		$shenqing['content'] = json_encode($content,JSON_UNESCAPED_UNICODE);
		$shenqing['dtTime'] = date("Y-m-d H:i:s");
		$db->insert_update('demo_level_shenqing',$shenqing,'id');
		echo '<script>alert("申请成功，请等待管理员审核");location.href="/index.php?p=8";</script>';
		exit;
	}
}
function shenqing_tuan(){
	global $db,$request,$db_service;
	$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
	if(empty($userId)){
		redirect('/index.php?p=8&a=login');
	}
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$wxh = $request['wxh'];
		$wx_img = $request['wx_img'];
		if($comId==10){
			$db_service = getCrmDb();
			$if_tuan = $db_service->get_var("select if_tuanzhang from demo_user where id=$userId");
		}else{
			$if_tuan = $db->get_var("select if_tuanzhang from users where id=$userId");
		}
		if($if_tuan==1){
			echo '{"code":0,"message":"您已经是团长了，请不要重复申请"}';
		    exit;
		}else if($if_tuan==-1){
			echo '{"code":0,"message":"您被管理员撤销了团长，不能再次申请"}';
		    exit;
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
		    echo '{"code":0,"message":"邀请人数不足'.$rule['yaoqing_num'].'，不能升级成为团长"}';
		    exit;
		}else if($earn<$rule['yaoqing_yongjin']){
		    echo '{"code":0,"message":"佣金不足'.$rule['yaoqing_yongjin'].'，不能升级成为团长"}';
		    exit;
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
			update_user_tuanid($userId,$u->tuan_id,$userId);
		}else{
			/*
			$user_info_str = $db->get_var("select user_info from users where id=$userId");
			if(!empty($user_info_str)){
				$user_info = json_decode($user_info_str,true);				
			}
			$user_info['wxh'] = $wxh;
			$user_info['wx_img'] = $wx_img;
			$db->query("update users set if_tuanzhang=1,user_info='".json_encode($user_info,JSON_UNESCAPED_UNICODE)."' where id=$userId");
			update_user_tuanid($userId,$u->tuan_id,$userId);
			*/
			$name = RemoveXSS($request['name']);
			$phone = RemoveXSS($request['phone']);
			$shenqing = array();
			$shenqing['comId'] = $comId;
			$shenqing['userId'] = $userId;
			$shenqing['name'] = $name;
			$shenqing['phone'] = $phone;
			$shenqing['wxh'] = $wxh;
			$shenqing['wx_img'] = $wx_img;
			$shenqing['dtTime'] = date("Y-m-d H:i:s");
			$id = $db->insert_update('demo_tuanzhang_shenq',$shenqing,'id');
			addTaskMsg(51,$id,'您的商城有新的团长申请，请及时处理',$comId);
		}
		echo '{"code":1,"message":"申请成功"}';
		exit;
	}
}
function shenqing_shequ(){
	
}
//修改会员的团id
function update_user_tuanid($uid,$old_tuanid,$new_tuanid){
	$comId = (int)$_SESSION['demo_comId'];
	if($comId==10){
		global $db_service;
		$db_service->query("update demo_user set tuan_id=$new_tuanid where id=$uid and tuan_id=$old_tuanid");
		add_user_oprate('所属团队由'.$old_tuanid.'变更为'.$new_tuanid,2,$uid);
		$xiajistr = $db_service->get_var("select group_concat(id) from demo_user where shangji=$uid and tuan_id=$old_tuanid");
		if(!empty($xiajistr)){
			$xiajis = explode(',',$xiajistr);
			foreach ($xiajis as $userid) {
				update_user_tuanid($userid,$old_tuanid,$new_tuanid);
			}
		}
	}else{
		global $db;
		$db->query("update users set tuan_id=$new_tuanid where id=$uid and tuan_id=$old_tuanid");
		add_user_oprate('所属团队由'.$old_tuanid.'变更为'.$new_tuanid,2,$uid);
		$xiajistr = $db->get_var("select group_concat(id) from users where comId=$comId and shangji=$uid and tuan_id=$old_tuanid");
		if(!empty($xiajistr)){
			$xiajis = explode(',',$xiajistr);
			foreach ($xiajis as $userid) {
				update_user_tuanid($userid,$old_tuanid,$new_tuanid);
			}
		}
	}
}
function add_user_oprate($content,$type,$uid=0){
	global $db;
	$user_oprate = array();
	$user_oprate['comId'] = (int)$_SESSION['demo_comId'];
	$user_oprate['userId'] = $uid==0?(int)$_SESSION[TB_PREFIX.'user_ID']:$uid;
	$user_oprate['dtTime'] = date("Y-m-d H:i:s");
	$user_oprate['ip'] = getip();
	$user_oprate['terminal'] = 2;
	$user_oprate['content'] = $content;
	$user_oprate['type'] = $type;
	$fenbiao = getFenbiao($user_oprate['comId'],20);
	$db->insert_update('user_oprate'.$fenbiao,$user_oprate,'id');
}
function check_yhk(){
	global $request;
	$cardId = $request['cardId'];
	$result = file_get_contents('https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='.$cardId.'&cardBinCheck=true');
	$banks = array("SRCB"=>"深圳农村商业银行", "BGB"=>"广西北部湾银行", "SHRCB"=>"上海农村商业银行", "BJBANK"=>"北京银行", "WHCCB"=>"威海市商业银行", "BOZK"=>"周口银行", "KORLABANK"=>"库尔勒市商业银行", "SPABANK"=>"平安银行", "SDEB"=>"顺德农商银行", "HURCB"=>"湖北省农村信用社", "WRCB"=>"无锡农村商业银行", "BOCY"=>"朝阳银行", "CZBANK"=>"浙商银行", "HDBANK"=>"邯郸银行", "BOC"=>"中国银行", "BOD"=>"东莞银行", "CCB"=>"中国建设银行", "ZYCBANK"=>"遵义市商业银行", "SXCB"=>"绍兴银行", "GZRCU"=>"贵州省农村信用社", "ZJKCCB"=>"张家口市商业银行", "BOJZ"=>"锦州银行", "BOP"=>"平顶山银行", "HKB"=>"汉口银行", "SPDB"=>"上海浦东发展银行", "NXRCU"=>"宁夏黄河农村商业银行", "NYNB"=>"广东南粤银行", "GRCB"=>"广州农商银行", "BOSZ"=>"苏州银行", "HZCB"=>"杭州银行", "HSBK"=>"衡水银行", "HBC"=>"湖北银行", "JXBANK"=>"嘉兴银行", "HRXJB"=>"华融湘江银行", "BODD"=>"丹东银行", "AYCB"=>"安阳银行", "EGBANK"=>"恒丰银行", "CDB"=>"国家开发银行", "TCRCB"=>"江苏太仓农村商业银行", "NJCB"=>"南京银行", "ZZBANK"=>"郑州银行", "DYCB"=>"德阳商业银行", "YBCCB"=>"宜宾市商业银行", "SCRCU"=>"四川省农村信用", "KLB"=>"昆仑银行", "LSBANK"=>"莱商银行", "YDRCB"=>"尧都农商行", "CCQTGB"=>"重庆三峡银行", "FDB"=>"富滇银行", "JSRCU"=>"江苏省农村信用联合社", "JNBANK"=>"济宁银行", "CMB"=>"招商银行", "JINCHB"=>"晋城银行JCBANK", "FXCB"=>"阜新银行", "WHRCB"=>"武汉农村商业银行", "HBYCBANK"=>"湖北银行宜昌分行", "TZCB"=>"台州银行", "TACCB"=>"泰安市商业银行", "XCYH"=>"许昌银行", "CEB"=>"中国光大银行", "NXBANK"=>"宁夏银行", "HSBANK"=>"徽商银行", "JJBANK"=>"九江银行", "NHQS"=>"农信银清算中心", "MTBANK"=>"浙江民泰商业银行", "LANGFB"=>"廊坊银行", "ASCB"=>"鞍山银行", "KSRB"=>"昆山农村商业银行", "YXCCB"=>"玉溪市商业银行", "DLB"=>"大连银行", "DRCBCL"=>"东莞农村商业银行", "GCB"=>"广州银行", "NBBANK"=>"宁波银行", "BOYK"=>"营口银行", "SXRCCU"=>"陕西信合", "GLBANK"=>"桂林银行", "BOQH"=>"青海银行", "CDRCB"=>"成都农商银行", "QDCCB"=>"青岛银行", "HKBEA"=>"东亚银行", "HBHSBANK"=>"湖北银行黄石分行", "WZCB"=>"温州银行", "TRCB"=>"天津农商银行", "QLBANK"=>"齐鲁银行", "GDRCC"=>"广东省农村信用社联合社", "ZJTLCB"=>"浙江泰隆商业银行", "GZB"=>"赣州银行", "GYCB"=>"贵阳市商业银行", "CQBANK"=>"重庆银行", "DAQINGB"=>"龙江银行", "CGNB"=>"南充市商业银行", "SCCB"=>"三门峡银行", "CSRCB"=>"常熟农村商业银行", "SHBANK"=>"上海银行", "JLBANK"=>"吉林银行", "CZRCB"=>"常州农村信用联社", "BANKWF"=>"潍坊银行", "ZRCBANK"=>"张家港农村商业银行", "FJHXBC"=>"福建海峡银行", "ZJNX"=>"浙江省农村信用社联合社", "LZYH"=>"兰州银行", "JSB"=>"晋商银行", "BOHAIB"=>"渤海银行", "CZCB"=>"浙江稠州商业银行", "YQCCB"=>"阳泉银行", "SJBANK"=>"盛京银行", "XABANK"=>"西安银行", "BSB"=>"包商银行", "JSBANK"=>"江苏银行", "FSCB"=>"抚顺银行", "HNRCU"=>"河南省农村信用", "COMM"=>"交通银行", "XTB"=>"邢台银行", "CITIC"=>"中信银行", "HXBANK"=>"华夏银行", "HNRCC"=>"湖南省农村信用社", "DYCCB"=>"东营市商业银行", "ORBANK"=>"鄂尔多斯银行", "BJRCB"=>"北京农村商业银行", "XYBANK"=>"信阳银行", "ZGCCB"=>"自贡市商业银行", "CDCB"=>"成都银行", "HANABANK"=>"韩亚银行", "CMBC"=>"中国民生银行", "LYBANK"=>"洛阳银行", "GDB"=>"广东发展银行", "ZBCB"=>"齐商银行", "CBKF"=>"开封市商业银行", "H3CB"=>"内蒙古银行", "CIB"=>"兴业银行", "CRCBANK"=>"重庆农村商业银行", "SZSBK"=>"石嘴山银行", "DZBANK"=>"德州银行", "SRBANK"=>"上饶银行", "LSCCB"=>"乐山市商业银行", "JXRCU"=>"江西省农村信用", "ICBC"=>"中国工商银行", "JZBANK"=>"晋中市商业银行", "HZCCB"=>"湖州市商业银行", "NHB"=>"南海农村信用联社", "XXBANK"=>"新乡银行", "JRCB"=>"江苏江阴农村商业银行", "YNRCC"=>"云南省农村信用社", "ABC"=>"中国农业银行", "GXRCU"=>"广西省农村信用", "PSBC"=>"中国邮政储蓄银行", "BZMD"=>"驻马店银行", "ARCU"=>"安徽省农村信用社", "GSRCU"=>"甘肃省农村信用", "LYCB"=>"辽阳市商业银行", "JLRCU"=>"吉林农信", "URMQCCB"=>"乌鲁木齐市商业银行", "XLBANK"=>"中山小榄村镇银行", "CSCB"=>"长沙银行", "JHBANK"=>"金华银行", "BHB"=>"河北银行", "NBYZ"=>"鄞州银行", "LSBC"=>"临商银行", "BOCD"=>"承德银行", "SDRCU"=>"山东农信", "NCB"=>"南昌银行", "TCCB"=>"天津银行", "WJRCB"=>"吴江农商银行", "CBBQS"=>"城市商业银行资金清算中心", "HBRCU"=>"河北省农村信用社");
	$array = json_decode($result);
	if($array->validated=='true'){
		echo '{"code":1,"message":"","bank_name":"'.$banks[$array->bank].'"}';
	}else{
		echo '{"code":0,"message":"未能检测到银行卡信息，请检查您输入的银行卡号"}';
	}
	exit;
}
function get_tanchu_msg(){
	global $db;
	$comId = (int)$_SESSION['demo_comId'];
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select userId,product_json from order$fenbiao where comId=$comId order by rand() limit 1");
	if(empty($order)){
		die('');
	}
	$u = $db->get_row("select nickname,image from users where id=$order->userId");
	$u->image = empty($u->image)?'/skins/default/images/wode_1.png':$u->image;
	$product_json = json_decode($order->product_json,true);
	$title = $product_json[0]['title'];
	echo "<img src='".$u->image."' height=20> ".($u->nickname)." 刚刚购买了".sys_substr($title,10,true);
	exit;
}
//总平台弹窗
function get_tanchu_zong(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = $db->get_var("select comId from demo_product_inventory where id=$id");
	$fenbiao = getFenbiao($comId,20);
	$order = $db->get_row("select userId,product_json from order$fenbiao where comId=$comId order by rand() limit 1");
	if(empty($order)){
		die('');
	}
	$u = $db->get_row("select nickname,image from users where id=$order->userId");
	$u->image = empty($u->image)?'/skins/default/images/wode_1.png':$u->image;
	$product_json = json_decode($order->product_json,true);
	$title = $product_json[0]['title'];
	echo "<img src='".$u->image."' height=20> ".($u->nickname)." 刚刚购买了此商品";
	exit;
}
function search_pdt(){
	global $db,$request;
	$keyword = $request['query'];
	$comId = (int)$_SESSION['demo_comId'];
	if($comId==1094 && empty($keyword)){
		echo '{"query":"'.$keyword.'","suggestions":["网红产品","正大食品","路雪雪糕"],"data":["网红产品","正大食品","路雪雪糕"]}';
		exit;
	}
	if($comId==10){
		$pdts = $db->get_results("select title from demo_product where if_tongbu=1 and title like '%$keyword%' order by ordering desc,orders desc limit 5");
	}else{
		$pdts = $db->get_results("select title from demo_product where comId=$comId and title like '%$keyword%' order by ordering desc,orders desc limit 5");
	}
	if(!empty($pdts)){
		$str = '';
		foreach ($pdts as $key => $pdt) {
			$str .=',"'.sys_substr($pdt->title,10,true).'"';
		}
		$str = substr($str,1);
		echo '{"query":"'.$keyword.'","suggestions":['.$str.'],"data":['.$str.']}';
	}else{
		echo '';
	}
	exit;
}