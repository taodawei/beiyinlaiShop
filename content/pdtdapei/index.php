<?php
function index(){}
function view(){}
function get_dapei_list(){
	global $db,$request;
	$comId = (int)$_SESSION['demo_comId'];
	$page = (int)$request['page'];
	$pageNum = (int)$request['pageNum'];
	if($page<1)$page=1;
	if(empty($pageNum))$pageNum=10;
	$cache_name = 'dapei-'.$page.'-'.$pageNum;
	$cache_content = cache_get('product',$cache_name);
	if(!empty($cache_content)){
		echo json_encode($cache_content,JSON_UNESCAPED_UNICODE);
		exit;
	}
	$order1 = 'id';
	$order2 = 'desc';
	$sql="select * from demo_product_dapei where comId=$comId";
	$count = $db->get_var(str_replace('*','count(*)',$sql));
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;
	$pdts = $db->get_results($sql);
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
			$db->query("update demo_product_dapei set views=views+1 where id=$pdt->id");
			//$company = $db->get_row("select com_title,com_logo from demo_shezhi where comId=$pdt->shopId");
			$data = array();
			$data['id'] = $pdt->id;
			/*$data['com_id'] = $pdt->shopId;
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
			$data['comId'] = $pdt->shopId;*/
			$data['originalPic'] = ispic($pdt->originalPic);
			$data['remark'] = $pdt->remark;
			$data['title'] = $pdt->title;
			/*$data['content'] = sys_substr(strip_tags($pdt->content),60,true);
			$data['content'] = preg_replace('/((\s)*(\n)+(\s)*)/','',$data['content']);*/
			$data['views'] = $pdt->views;
			$return['data'][] = $data;
		}
	}
	cache_push('product',$cache_name,$return,5);
	echo json_encode($return,JSON_UNESCAPED_UNICODE);
	exit;
}