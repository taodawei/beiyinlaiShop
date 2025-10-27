<?php
function index(){}
function view(){}
function get_faxian_list(){
	global $db,$request;
	$userId = (int)$_SESSION['demo_zhishangId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$chache_file = 'faxian-'.$page.'-'.$pageNum.'.dat';
	$cache_content = file_get_contents(ABSPATH.'/cache/'.$chache_file);
	$if_cache = 0;
	if(!empty($cache_content)){
		$now = time();
		$caches = json_decode($cache_content);
		if($caches->endTime>$now){
			$count = $caches->count;
			$pdts = $caches->pdts;
			$if_cache = 1;
		}
	}
	if($if_cache==0){
		$order1 = 'ordering';
		$order2 = 'desc,id desc';
		$sql="select * from demo_faxian";
		$count = $db->get_var(str_replace('*','count(*)',$sql));
		$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
		$pdts = $db->get_results($sql);
		$cache_arr = array();
		$cache_arr['endTime'] = strtotime('+10 minutes');
		$cache_arr['count'] = $count;
		$cache_arr['pdts'] = $pdts;
		file_put_contents(ABSPATH.'/cache/'.$chache_file,json_encode($cache_arr,JSON_UNESCAPED_UNICODE),LOCK_EX);
	}
	
	$return = array();
	$return['code'] = 1;
	$return['message'] = '';
	$return['count'] = $count;
	$return['pages'] = ceil($count/$pageNum);
	$return['data'] = array();
	//$zhekou = get_user_zhekou();
	$now = time();
	if(!empty($pdts)){
		foreach ($pdts as $i=>$pdt) {
			$db->query("update demo_faxian set views=views+1 where id=$pdt->id");
			$company = $db->get_row("select com_title,com_logo from demo_shezhi where comId=$pdt->shopId");
			$data = array();
			$data['id'] = $pdt->id;
			$data['com_id'] = $pdt->shopId;
			$data['com_logo'] = $company->com_logo;
			$data['com_title'] = $company->com_title;
			$dtTime = strtotime($pdt->dtTime);
			$hours = intval(($now-$dtTime)/3600);
			if($hours>24){
				$data['dtTime'] = date("Y-m-d",strtotime($pdt->dtTime));
			}else{
				$data['dtTime'] = $hours.'小时前';
			}
			$data['pics'] = array();
			if(!empty($pdt->originalPic)){
				$data['pics'] = explode('|',$pdt->originalPic);
			}
			$data['shipin'] = $pdt->shipin;
			$data['guanzhu'] = (int)$db->get_var("select count(*) from user_shop_collect where userId=$userId and shopId=$pdt->shopId");
			$data['comId'] = $pdt->shopId;
			$data['title'] = $pdt->title;
			$data['content'] = sys_substr(strip_tags($pdt->content),60,true);
			$data['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','',$data['content']);
			$data['views'] = $pdt->views;
			$return['data'][] = $data;
		}
	}
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}