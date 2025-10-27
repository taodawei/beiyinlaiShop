<?php
require_once('../aliyunoss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;
function banner(){}
function gonggao(){}
function addBanner(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$id = (int)$request['id'];
		$title = $request['title'];
		$linkUrl = $request['linkUrl'];
		$originalPic = $request['originalPic'];
		$counts = $request['counts'];
		if(empty($id)){
			$db->query("insert into dinghuo_banner(comId,title,linkUrl,originalPic,dtTime) value($comId,'$title','$linkUrl','$originalPic','".date("Y-m-d H:i:s")."')");
		}else{
			$db->query("update dinghuo_banner set title='$title',linkUrl='$linkUrl',originalPic='$originalPic' where id=$id and comId=$comId");
		}
		redirect("?m=system&s=yingxiao&a=banner");
	}
}
function topBanner(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$maxOrdering = $db->get_var("select max(ordering) from dinghuo_banner where comId=$comId");
	$maxOrdering+=1;
	$db->query("update dinghuo_banner set ordering=$maxOrdering where id=$id");
	redirect("?m=system&s=yingxiao&a=banner");
}
function del_banner(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from dinghuo_banner where id=$id and comId=$comId");
	redirect("?m=system&s=yingxiao&a=banner");
}
function delGongao(){
	global $db,$request;
	$id = (int)$request['id'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$db->query("delete from dinghuo_gonggao where id=$id and comId=$comId");
	echo '{"code":1,"message":"ok"}';
}
function addGonggao(){
	global $db,$request;
	if(!empty($request['title'])){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$gonggao = array();
		$gonggao['id'] = (int)$request['id'];
		$gonggao['comId'] = $comId;
		$gonggao['level'] = (int)$request['level'];
		$gonggao['type'] = (int)$request['type'];
		$gonggao['dtTime'] = date("Y-m-d H:i:s");
		$gonggao['title'] = $request['title'];
		//$gonggao['files'] = $request['files'];
		$gonggao['content'] = $request['content'];
		insert_update('dinghuo_gonggao',$gonggao,'id');
		redirect("?m=system&s=yingxiao&a=gonggao");
	}
}
function viewGonggao(){}
function getGonggaos(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['channelId'];
	$keyword = $request['keyword'];
	$page = (int)$request['page'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$pageNum = (int)$request["limit"];
	setcookie('gonggaoPageNum',$pageNum,time()+3600*24*30);
	$sql="select id,type,title,readed,dtTime,title from dinghuo_gonggao where comId=$comId";
	if(!empty($channelId)){
		$sql.=" and type=$channelId";
	}
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and dtTime>='$startTime 00:00:00'";
	}
	if(!empty($endTime)){
		$sql.=" and dtTime<='$endTime 23:59:59'";
	}
	$count = $db->get_var(str_replace('id,type,title,readed,dtTime,title','count(*)',$sql));
	$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $j){
			if($j->type==1){
				$j->channel = '公司公告';
			}else{
				$j->channel = '政策发文';
			}
			$yidu = empty($j->readed)?0:count(explode(',',$j->readed));
			$j->received = '已读('.$yidu.')';
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
//发现相关
function shipin(){}
function yuyin(){}
function addShipin(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = 10;
		$shipin = array();
		$shipin['id'] = (int)$request['id'];
		$shipin['shopId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$shipin['title'] = $request['title'];
		$shipin['originalPic'] = $request['originalPic'];
		$shipin['content'] = $request['content'];
		$shipin['shipin'] = $request['shipin'];
		$shipin['pdtIds'] = implode(',',$request['inventoryId']);
		$shipin['dtTime'] = date("Y-m-d H:i:s");
		$shipin['remark'] = $request['remark'];
		//$shipin['status'] = 0;
		$db->insert_update('demo_faxian',$shipin,'id');
		redirect("?s=yingxiao&a=addShipin");
	}
}
function get_shipin_list(){
	global $db,$request;
	$keyword = $request['keyword'];
	$status = (int)$request['status'];
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$page = (int)$request['page'];
	$pageNum = 6;
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$sql="select * from demo_faxian where shopId=$comId";
	if($status!=1){
		$sql.=" and status=0";
	}else{
		$sql.=" and status=1";
	}
	if(!empty($keyword)){
		/*$mendian_ids = $db->get_var("select group_concat(id) from demo_shezhi where com_title like '%$keyword%'");
		if(empty($mendian_ids))$mendian_ids='0';
		$sql.=" and (mendianId in($mendian_ids) or title like '%$keyword%')";*/
		$sql.=" and title like '%$keyword%'";
	}
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
	$nums = $db->get_results("select count(*) as num,status from demo_faxian where shopId=$comId group by status");
	$weishenhe = 0;
	$yishenhe = 0;
	if(!empty($nums)){
		foreach ($nums as $n){
			if($n->status==0){
				$weishenhe = $n->num;
			}else{
				$yishenhe = $n->num;
			}
		}
	}
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['wei_num'] = $weishenhe;
	$return['yi_num'] = $yishenhe;
	$return['data'] = array();
	if(!empty($pdts)){
		foreach ($pdts as $pdt) {
			$pdt->mendian = $db->get_var("select com_title from demo_shezhi where comId=$pdt->shopId");
			$pdt->title = sys_substr($pdt->title,15,true);
			$pdt->mendian = sys_substr($pdt->mendian,15,true);
			$pdt->originalPic = ispic($pdt->originalPic);
			$return['data'][] = $pdt;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}
function shenhe_shipin(){
	global $db,$request;
	$id = (int)$request['id'];
	$db->query("update demo_faxian set status=1 where id=$id");
	die('{"code":1}');
}
function del_shipin(){
	global $db,$request;
	$id = (int)$request['id'];
	$url = $db->get_var("select shipin from demo_faxian where id=$id");
	if(!empty($url)){
		$img = str_replace('https://zhishang-kucun.oss-cn-beijing.aliyuncs.com/','',$url);
		$objects[] = $img;
		$accessKeyId = "LTAIOgimVmDhlkck";
		$accessKeySecret = "MJkk2G2SJllFVx5ehTbyAvC6Kton0L";
		$endpoint = "http://oss-cn-beijing.aliyuncs.com";
		$bucket= "zhishang-kucun";
		$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
		try{
			$ossClient->deleteObjects($bucket, $objects);
		} catch(OssException $e) {
			file_put_contents('aliyunoss.err',$e->getMessage());
		}
	}
	$db->query("delete from demo_faxian where id=$id");
	die('{"code":1}');
}
function get_shipin_channels(){
	return array(1=>'产品展示',2=>'团长风采',3=>'商家风采',4=>'平台纪实',5=>'售后好评');
}
function upload_shipin(){
	global $db,$request;
	$file_name=$request['file_name'];
	$file_type=1;
	$file_suffix=$request['file_suffix'];
	$file_size=$request['file_size'];
	$file_parentId=$request['file_parentId'];
	$oss_fileId=$request['oss_fileId'];
	$oss_fileName=$request['oss_fileName'];
	$file_url="https://zhishang-kucun.oss-cn-beijing.aliyuncs.com/$oss_fileName";
	$file_name_arr=explode(".", $file_name);
	$size_arr=explode(" ",$file_size);
	echo '{"code":1,"message": "上传成功","file_url":"'.$file_url.'"}';
	exit;
}
//大转盘
function dazhuanpan(){}
function getDazhuanpans(){
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
	$sql = "select * from demo_dazhuanpan where comId=$comId";
	if(!empty($keyword)){
		$sql.=" and title like '%$keyword%'";
	}
	if(!empty($startTime)){
		$sql.=" and startTime>='$startTime'";
	}
	if(!empty($endTime)){
		$sql.=" and endTime<='$endTime 23:59:59'";
	}
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$j->time = date("Y-m-d H:i",strtotime($j->startTime)).'-'.date("Y-m-d H:i",strtotime($j->endTime));
			$startTime = strtotime($j->startTime);
			$endTime = strtotime($j->endTime);
			$now = time();
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
			$j->url = '前台链接地址：/index.php?p=23&id='.$j->id.'<br><a href="?s=yingxiao&a=erweima&id='.$j->id.'" target="_blank">查看活动二维码</a>';
			$j->remark = '每人'.($j->per_type==2?'每天':'').'可抽奖'.$j->per_num.'次';
			if($j->per_jifen>0){
				$j->remark.='(每次消耗'.$j->per_jifen.'积分)';
			}
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function erweima(){}
function addDazhuanpan(){
	global $db,$request;
	if($request['submit']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$yushou = array();
		$yushou['comId'] = $comId;
		$yushou['title'] = $request['title'];
		$yushou['startTime'] = $request['startTime'];
		$yushou['endTime'] = $request['endTime'];
		$yushou['per_type'] = (int)$request['per_type'];
		$yushou['per_num'] = (int)$request['per_num'];
		$yushou['per_jifen'] = (int)$request['per_jifen'];
		$yushou['content'] = $request['content'];
		$yushou['dtTime'] = date("Y-m-d H:i:s");
		$db->insert_update('demo_dazhuanpan',$yushou,'id');
		echo '{"code":1,"message":"ok"}';
		exit;
	}
}
function zuofei_dzp(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_dazhuanpan set status=-1 where id in($ids) and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function prizes(){}
function del_gift(){
	global $db,$request;
	$id = (int)$request['id'];
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$db->query("delete from demo_dazhuanpan_prize where id=$id and dazhuanpan_id=$dazhuanpan_id");
	redirect("?m=system&s=yingxiao&a=prizes&dazhuanpan_id=$dazhuanpan_id");
}
//大转盘禁用
function jinyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$db->query("update demo_dazhuanpan_prize set status=0 where id=$id and dazhuanpan_id=$dazhuanpan_id");
	redirect("?m=system&s=yingxiao&a=prizes&dazhuanpan_id=$dazhuanpan_id");
}
//大转盘启用
function qiyong(){
	global $db,$request;
	$id = (int)$request['id'];
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$db->query("update demo_dazhuanpan_prize set status=1 where id=$id and dazhuanpan_id=$dazhuanpan_id");
	redirect("?m=system&s=yingxiao&a=prizes&dazhuanpan_id=$dazhuanpan_id");
}
function addGift(){
	global $db,$request;
	if(!empty($request['name'])){
		//$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$prize = array();
		$prize['id'] = (int)$request['id'];
		$prize['dazhuanpan_id'] = (int)$request['dazhuanpan_id'];
		$prize['name'] = $request['name'];
		$prize['image'] = $request['originalPic'];
		$prize['num'] = (int)$request['num'];
		$prize['type'] = empty($request['type'])?1:(int)$request['type'];
		$prize['jifen'] = (int)$request['jifen'];
		$prize['chance'] = $request['chance']*100;
		$prize['ordering'] = (int)$request['ordering'];
		$prize['content'] = $request['content'];
		$db->insert_update('demo_dazhuanpan_prize',$prize,'id');
		redirect("?m=system&s=yingxiao&a=prizes&dazhuanpan_id=".$request['dazhuanpan_id']);
	}
}
function records(){}
function getRecords(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$dazhuanpan_id = (int)$request['dazhuanpan_id'];
	$channelId = (int)$request['channelId'];
	$isduihuan = (int)$request['isduihuan'];
	$keyword = $request['keyword'];
	$startTime = $request['startTime'];
	$endTime = $request['endTime'];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$sql = "select * from demo_dazhuanpan_record where dazhuanpan_id=$dazhuanpan_id";
	if($channelId>0){
		$sql.=" and prize=$channelId";
	}
	if(!empty($isduihuan)){
		$sql.=" and isduihuan=".($isduihuan==1?1:0);
	}
	if(!empty($keyword)){
		$sql.=" and (name like '%$keyword%' or tel='$keyword')";
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
			$j->layclass = '';
			if($j->isduihuan==1){
				$j->layclass= 'deleted';
				$j->status_info = '已领';
			}else{
				$j->status_info = '未领';
			}
			$j->status=$j->isduihuan;
			$dataJson['data'][] = $j;
		}
	}
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}
function record_yiling(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$ids = $request['ids'];
	$db->query("update demo_dazhuanpan_record set isduihuan=1,duihuan_time='".date("Y-m-d H:i:s")."' where id in($ids)");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function daochu_records(){}