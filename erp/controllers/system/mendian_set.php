<?php

function links(){}

function addLink(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$parentId = (int)$request['parentId'];
	$title = $request['title'];
	$originalPic = $request['originalPic'];
	$backimg = $request['links'];
	if(empty($id)){
		$ifhas = $db->get_var("select id from web_links where comId=$comId and parentId=$parentId and title='$title'");
		if(!empty($ifhas)){
			echo '<script>alert("您已经创建过这个导航了！");history.go(-1);</script>';
			exit;
		}
		$db->query("insert into web_links(comId,title,parentId,originalPic,links) value($comId,'$title',$parentId,'$originalPic','$backimg')");
		$id = $db->get_var("select last_insert_id();");
		
		$db->query("update web_links set ordering = $id where id = $id");
	}else{
		$db->query("update web_links set title='$title',parentId=$parentId,originalPic='$originalPic',links='$backimg' where id=$id and comId=$comId");
	}

	redirect("?m=system&s=mendian_set&a=links&id=$id");
}

function totop(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$maxOrdering = $db->get_var("select ordering from web_links where comId=$comId order by ordering desc limit 1");
	$maxOrdering+=1;
	$db->query("update web_links set ordering=$maxOrdering where id=$id");
	cache_channel();
	redirect("?m=system&s=product_channel&id=$id");
}

function moveLink()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$type = (int)$request['type'];//类型：0-向下  1-向上
	
	$currentChannel = $db->get_row("select * from web_links where id = $id");
	if($type){//1-向上
	    $move = $db->get_row("select * from web_links where parentId = $currentChannel->parentId and ordering > $currentChannel->ordering order by ordering asc ");
	}else{
	    $move = $db->get_row("select * from web_links where parentId = $currentChannel->parentId and ordering < $currentChannel->ordering order by ordering desc ");
	}
	if($move){
	    $db->query("update web_links set ordering = $move->ordering where id = $id");
	    $db->query("update web_links set ordering = $currentChannel->ordering where id = $move->id");
	}

	redirect("?m=system&s=mendian_set&a=links&id=$id");
}

function delLink(){
	global $db,$request;
	$id = (int)$request['id'];
	if($id>0){
        
		$ifhas = $db->get_var("select id from web_links where parentId  = $id ");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"删除分类前请先转移该导航下的子导航！"}';
		}else{
			$db->query("delete from web_links where id in($id)");

			echo '{"code":1,"message":"删除成功！","ids":"'.$id.'"}';
		}
	}else{
		echo '{"code":0,"message":"分类选择有误!"}';
	}
	
	exit;
}

//获取所有上级分类，用,分开
function getParentIds($id){
	global $db;
	$pid = $db->get_var("select parentId from web_links where id=$id");
	if($pid>0){
		return ','.$pid.getParentIds($pid);
	}
}

function index(){
    global $db,$request;
    if($request['tijiao']==1){
        $shezhi = array();
        $comId = $shezhi['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
        $shezhi['com_title'] = $request['com_title'];
        $shezhi['com_logo'] = $request['com_logo'];
        $shezhi['com_remark'] = $request['com_remark'];
        $shezhi['com_kefu'] = $request['com_kefu'];
        $shezhi['wx_kefu'] = $request['wx_kefu'];
        $shezhi['share_desc'] = $request['share_desc'];
        $shezhi['com_back'] = $request['com_back'];
        $shezhi['zhishang_back'] = $request['zhishang_back'];
        $shezhi['time_pay'] = $request['time_pay'];
        $shezhi['time_shouhuo'] = $request['time_shouhuo'];
        $shezhi['com_address'] = $request['com_address'];
        $shezhi['com_phone'] = $request['phone'];
        $shezhi['time_tuan'] = $request['time_tuan'];
        $shezhi['video_jifen'] = $request['video_jifen'];
        $shezhi['offline_company'] = $request['offline_company'];
        $shezhi['offline_code'] = $request['offline_code'];
        $shezhi['offline_bank'] = $request['offline_bank'];
        $shezhi['index_video'] = $request['index_video'];
        $shezhi['show_nav_jingxiao'] = (int)$request['show_nav_jingxiao'];
        
        $request['tuihuan_reason'] = array_unique(array_filter($request['tuihuan_reason']));
        $tuihuan_reason = implode('@_@',$request['tuihuan_reason']);
        
        $request['qx_reason'] = array_unique(array_filter($request['qx_reason']));
        $qx_reason = implode('@_@',$request['qx_reason']);
        $shezhi['qx_reason'] = $qx_reason;
        
        $shezhi['if_tixian'] = $request['if_tixian'];
        $shezhi['tixian_bili'] = $request['tixian_bili'];
        
        $accessKey = ['express_type', 'kdn_EBusinessID', 'kdn_key', 'kdn_port', 'kd100_key', 'kd100_customer','com_coordinate', 'com_beian', 'share_img', 'com_phone','pc_phone','com_email', 'pdt_max_num', 'invite_jifen', 'register_jifen'];
        foreach ($accessKey as $key){
            $shezhi[$key] = $request[$key];
        }
        
        $request['peisong_times'] = array_unique(array_filter($request['peisong_times']));
        $peisong_times = implode('@_@',$request['peisong_times']);
        $shezhi['tuihuan_reason'] = $tuihuan_reason;
        $shezhi['peisong_times'] = $peisong_times;
        $peisong_time_money = array();
        //file_put_contents('request.txt',json_encode($request['peisong_time_money'],JSON_UNESCAPED_UNICODE));
        if(!empty($request['peisong_times'])){
            foreach ($request['peisong_times'] as $time) {
                if(isset($request['peisong_time_money'][$time])){
                    $peisong_time_money[$time]['peisong_money'] = $request['peisong_time_money'][$time];
                    $peisong_time_money[$time]['peisong_man'] = $request['peisong_time_man'][$time];
                }
            }
        }
        $shezhi['peisong_time_money'] = json_encode($peisong_time_money,JSON_UNESCAPED_UNICODE);
        $shezhi['website'] = '';
        $shezhi['xieyi'] = $request['xieyi'];
        $shezhi['moban'] = $request['moban'];
        $shezhi['price_name'] = $request['price_name'];
        $db->query("update demo_product set price_name='".$shezhi['price_name']."' where comId=$comId");
        $shezhi['storeId'] = (int)$request['storeId'];
        $shezhi['kaipiao_type'] = (int)$request['kaipiao_type'];
        $shezhi['if_dianzi_fapiao'] = (int)$request['if_dianzi_fapiao'];
        $shezhi['fanli_type'] = (int)$request['fanli_type'];
        $shezhi['shangji_price'] = empty($request['shangji_price'])?0:$request['shangji_price'];
        $shezhi['shangshangji_price'] = empty($request['shangshangji_price'])?0:$request['shangshangji_price'];
        $shezhi['shangshangji_jt_price'] = empty($request['shangshangji_jt_price'])?0:$request['shangshangji_jt_price'];
        $shezhi['shang_bili'] = empty($request['shang_bili'])?0:$request['shang_bili'];
        $peisong_qisong = $request['peisong_qisong'];
        $peisong_qisong1 = $request['peisong_qisong1'];
        $peisong_money = $request['peisong_money'];
        $peisong_man = $request['peisong_man'];
        $peisong_types = implode(',',$request['peisong_types']);
        $shezhi['tihuo_info'] = json_encode($request['tihuo_info'],JSON_UNESCAPED_UNICODE);
        $shezhi['shequ_yunfei'] = json_encode(array('peisong_qisong'=>$peisong_qisong,'peisong_qisong1'=>$peisong_qisong1,'peisong_money' =>$peisong_money,'peisong_man'=>$peisong_man,'peisong_types'=>$peisong_types),JSON_UNESCAPED_UNICODE);
        $tuanzhang_rule = array();
        if($shezhi['fanli_type']==2){
            $tuanzhang_rule['yaoqing_num'] = (int)$request['yaoqing_num'];
            $tuanzhang_rule['yaoqing_yongjin'] = empty($request['yaoqing_yongjin'])?'0':$request['yaoqing_yongjin'];
        }
        $tuanzhang_rule['yaoqing_back'] = $request['yaoqing_back'];
        $shezhi['tuanzhang_rule'] = json_encode($tuanzhang_rule,JSON_UNESCAPED_UNICODE);
        $shezhi['time_comment'] = (int)$request['time_comment'];
        if(!empty($request['website'])){
            foreach ($request['website'] as $website) {
                $website = str_replace('http://','', $website);
                $website = str_replace('https://','', $website);
                if(!file_exists(ABSPATH.'/config/domains/'.$website.'.txt')){
                    file_put_contents(ABSPATH.'/config/domains/'.$website.'.txt',$comId);
                    $shezhi['website'].='|'.$website;
                }
            }
        }
        if(!empty($shezhi['website'])){
            $shezhi['website'] = substr($shezhi['website'],1);
        }
        $ifhas = $db->get_var("select comId from demo_shezhi where comId=$comId");
        $shezhi['open_shequ_sq'] = (int)$request['open_shequ_sq'];
        $shezhi['if_fenxiao'] = $request['if_fenxiao'];
        $shezhi['if_pintuan'] = $request['if_pintuan'];
        if(empty($ifhas)){
            $db->query("insert into demo_shezhi(comId,com_title,com_logo,com_remark,share_desc,time_pay,time_shouhuo,time_tuan,tuihuan_reason,website,kaipiao_type,if_dianzi_fapiao,fanli_type,shangji_bili,tuanzhang_rule,com_kefu,if_fenxiao,if_pintuan) value($comId,'".$shezhi['com_title']."','".$shezhi['com_logo']."','".$shezhi['com_remark']."','".$shezhi['share_desc']."','".$shezhi['time_pay']."','".$shezhi['time_shouhuo']."','".$shezhi['time_tuan']."','".$shezhi['tuihuan_reason']."','".$shezhi['website']."',".$shezhi['kaipiao_type'].",".$shezhi['if_dianzi_fapiao'].",".$shezhi['fanli_type'].",".$shezhi['shangji_bili'].",'".$shezhi['tuanzhang_rule']."','".$shezhi['com_kefu']."','".$shezhi['if_fenxiao']."','".$shezhi['if_pintuan']."')");
        }else{
            $db->insert_update('demo_shezhi',$shezhi,'comId');
        }
        redirect("?s=mendian_set&a=index");
    }
}

function index1(){
	global $db,$request;
	if($request['tijiao']==1){
		$shezhi = array();
		$comId = $shezhi['comId'] = (int)$_SESSION[TB_PREFIX.'comId'];
		$shezhi['com_phone'] = $request['com_phone'];
		$shezhi['com_address'] = $request['com_address'];
		$shezhi['com_desc'] = $request['com_desc'];
		$shezhi['com_honor'] = $request['com_honor'];
		$heng = $request['hengzuobiao'];
		$zong = $request['zongzuobiao'];
		if(!empty($heng) && !empty($zong)){
			$shezhi['zuobiao'] = $heng.'|'.$zong;
		}
		$ifhas = $db->get_var("select comId from demo_shezhi where comId=$comId");
		if(empty($ifhas)){
			$db->query("insert into demo_shezhi(comId,com_phone,com_address,zuobiao,com_desc,com_honor) value($comId,'".$shezhi['com_phone']."','".$shezhi['com_address']."','".$shezhi['zuobiao']."','".$shezhi['com_desc']."','".$shezhi['com_honor']."')");
		}else{
			$db->insert_update('demo_shezhi',$shezhi,'comId');
		}
		redirect("?s=mendian_set&a=index1");
	}
}
function type(){}
function addrows(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$arrays = array();
		$j= 0;
		$sn = $request['sn_rule'];
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		$rows = $request['rows'];
		if($rows>0){
			for($i=1;$i<=$rows;$i++){
				if(!empty($request['name'.$i])){
					$j++;
					$arr = array();
					$arr['id'] = $j;
					$arr['name'] = $request['name'.$i];
					$arr['type'] = $request['type'.$i];
					$arr['if_must'] = $request['if_must'.$i];
					$arr['detail'] = $request['detail'.$i];
					if($arr['type']=='select'){
						if(!empty($request['select'.$i])){
							$arr['select'] = implode('@',$request['select'.$i]);
							if(empty($arr['select'])){
								echo '<script>alert("多项选择框至少需要一个列表项");history.go(-1);</script>';
								exit;
							}
						}
					}
					$arrays[] = $arr;
				}
			}
			$content = serialize($arrays);
			if($ifhas>0){
				$db->query("update user_shezhi set sn='$sn',addRows='$content' where comId=$comId");
			}else{
				$db->query("insert into user_shezhi(comId,sn,addRows) value($comId,'$sn','$content')");
			}
		}
		redirect("?m=system&s=mendian_set&a=addrows");
	}
}

function bili(){}

function add_bili(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$level = array();
		$level['id'] = (int)$request['id'];
		$level['min'] = $request['min'];
		$level['max'] = $request['max'];
		$level['bili'] = $request['bili'];

		insert_update('zc_release',$level,'id');
		redirect("?s=mendian_set&a=bili");
	}
}

function del_bili(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];

	$db->query("delete from zc_release where id=$id ");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}

function level(){}
function jifen(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$jifen_type = (int)$request['jifen_type'];
		$jifen_content = array();
		switch ($jifen_type){
			case 1:
				$jifen_content['money'] = (int)$request['content']['money'];
				$jifen_content['shangxian'] = (int)$request['content']['shangxian'];
			break;
			case 2:
				$jifen_content['man'] = (int)$request['content']['man'];
				$jifen_content['song'] = (int)$request['content']['song'];
			break;
			case 3:
				if(!empty($request['rows'])){
					foreach ($request['rows'] as $id){
						$arry = array();
						$arry['channels'] = $request['departs_'.$id];
						$arry['pdts'] = $request['users_'.$id];
						$arry['departNames'] = $request['departNames_'.$id];
						$arry['userNames'] = $request['userNames_'.$id];
						$arry['jifen'] = $request['jifen_'.$id];
						if(!empty($arry['jifen'])&&(!empty($arry['channels'])||!empty($arry['pdt']))){
							$jifen_content['items'][] = $arry;
						}
					}
				}
			break;
		}
		$content = '';
		if(!empty($jifen_content))$content = json_encode($jifen_content,JSON_UNESCAPED_UNICODE);
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		if($ifhas>0){
			$db->query("update user_shezhi set jifen_type=$jifen_type,jifen_content='$content' where comId=$comId");
		}else{
			$db->query("insert into user_shezhi(comId,jifen_type,jifen_content) value($comId,'$jifen_type','$content')");
		}
		redirect('?s=mendian_set&a=jifen');
	}
}
function jifen_jiazhi(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$if_jifen_pay = (int)$request['if_jifen_pay'];
		$jifen_pay_rule = '';
		if($if_jifen_pay==1){
			$jifen_pay_rule = json_encode($request['content'],JSON_UNESCAPED_UNICODE);
		}
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		if($ifhas>0){
			$db->query("update user_shezhi set if_jifen_pay=$if_jifen_pay,jifen_pay_rule='$jifen_pay_rule' where comId=$comId");
		}else{
			$db->query("insert into user_shezhi(comId,if_jifen_pay,jifen_pay_rule) value($comId,'$if_jifen_pay','$jifen_pay_rule')");
		}
		redirect('?s=mendian_set&a=jifen_jiazhi');
	}
}
function jifen_qiandao(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$if_qiandao = (int)$request['if_qiandao'];
		$qiandao_rule = '';
		if($if_qiandao==1){
			$qiandao_rule = json_encode($request['content'],JSON_UNESCAPED_UNICODE);
		}
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		if($ifhas>0){
			$db->query("update user_shezhi set if_qiandao=$if_qiandao,qiandao_rule='$qiandao_rule' where comId=$comId");
		}else{
			$db->query("insert into user_shezhi(comId,if_qiandao,qiandao_rule) value($comId,'$if_qiandao','$qiandao_rule')");
		}
		redirect('?s=mendian_set&a=jifen_qiandao');
	}
}
function jifen_share(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$if_share = (int)$request['if_share'];
		$share_jifen = (int)$request['share_jifen'];
		$share_limit = (int)$request['share_limit'];
		$share_dikoujin = (int)$request['share_dikoujin'];
		$share_limit_dikoujin = (int)$request['share_limit_dikoujin'];
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		if($ifhas>0){
			$db->query("update user_shezhi set if_share=$if_share,share_jifen='$share_jifen',share_limit='$share_limit',share_dikoujin='$share_dikoujin',share_limit_dikoujin='$share_limit_dikoujin' where comId=$comId");
		}else{
			$db->query("insert into user_shezhi(comId,if_share,share_jifen,share_limit,share_dikoujin,share_limit_dikoujin) value($comId,$if_share,'$share_jifen','$share_limit','$share_dikoujin','$share_limit_dikoujin')");
		}
		
		redirect('?s=mendian_set&a=jifen_share');
	}
}
function yue(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$if_yue_tixian = (int)$request['if_yue_tixian'];
		$jifen_yue = (int)$request['jifen_yue'];
		$jifen_yue_num = (int)$request['jifen_yue_num'];
		$jifen_yue_limit = (int)$request['jifen_yue_limit'];
		$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
		if($ifhas>0){
			$db->query("update user_shezhi set if_yue_tixian=$if_yue_tixian,jifen_yue='$jifen_yue',jifen_yue_num='$jifen_yue_num',jifen_yue_limit='$jifen_yue_limit' where comId=$comId");
		}else{
			$db->query("insert into user_shezhi(comId,if_yue_tixian,jifen_yue,jifen_yue_num,jifen_yue_limit) value($comId,'$if_yue_tixian','$jifen_yue','$jifen_yue_num','$jifen_yue_limit')");
		}
		redirect('?s=mendian_set&a=yue');
	}
}
function updateLevel(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	//$fixed_zhekou = $request['fixed_zhekou'];
	$if_fixed_zhekou = (int)$request['if_fixed_zhekou'];
	$ifhas = $db->get_var("select comId from user_shezhi where comId=$comId");
	if($ifhas>0){
		$db->query("update user_shezhi set if_fixed_zhekou=$if_fixed_zhekou where comId=$comId");
	}else{
		$db->query("insert into user_shezhi(comId,if_fixed_zhekou) value($comId,'$if_fixed_zhekou')");
	}
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function editLevel(){
	global $db,$request;
	if($request['submit']==1){
		$comId = $_SESSION[TB_PREFIX.'comId'];
		$level = array();
		$level['id'] = (int)$request['id'];
		$level['comId'] = $comId;
		$level['title'] = $request['title'];
		$level['yq_num'] = $request['yq_num'];
		$level['price'] = $request['price'];
		$level['zhekou'] = $request['zhekou'];
		$level['jifen'] = $request['jifen'];
		$level['content'] = $request['content'];
		insert_update('user_level',$level,'id');
		redirect("?s=mendian_set&a=level");
	}
}
function getPdtFanwei(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$ds = $request['departs'];
	$us = $request['users'];
	$dNames = $request['departNames'];
	$uNames = $request['userNames'];
	if(!empty($ds)){
		$departs = explode(',',$ds);
		$departNames = explode(',',$dNames);
	}
	if(!empty($us)){
		$users = explode(',',$us);
		$userNames = explode(',',$uNames);
	}
	$str = '<div id="add_container">
			<div id="new_title">
				<div class="new_title_01">选择分类/产品</div>
				<div class="new_title_02" onclick="hide_myModal();"></div>
				<div class="clearBoth"></div>
			</div>
		  <div id="splc_cont">
			<div class="splc_cont_left">
				<div class="splc_cont_left_title">已选择以下分类或产品</div>
				<div class="splc_cont_left_con">
					<ul>';
					if(!empty($departs)){
						$i=0;
						foreach($departs as $depart){
							$str.='<li id="left_depart'.$depart.'">
								<div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_depart('.$depart.',\''.$departNames[$i].'\')"><img src="images/close1.png" border="0" /></a></div>
								<div class="clearBoth"></div>
								<div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png" /></div>'.$departNames[$i].'</div>
							</li>';
							$i++;
						}
					}
					if(!empty($users)){
						$i=0;
						foreach($users as $userId){
							$str.='<li id="left_user'.$userId.'">
								<div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_user('.$userId.',\''.$userNames[$i].'\')"><img src="images/close1.png" border="0" /></a></div>
								<div class="clearBoth"></div>
								<div class="shenpi_set_add_03"><div class="gg_people_show_3_1">'.substr($userNames[$i],-6).'</div>'.$userNames[$i].'</div>
							</li>';
							$i++;
						}
					}
					$str.='</ul>
				</div>
			</div>
			<div class="splc_cont_right">
				<div class="splc_cont_right_title">所有分类</div>
				<div class="splc_cont_right_search"><input type="text" stlye="border:0px;" onchange="search_users(this.value);" placeholder="请输入产品名称"></div>
				<div class="splc_cont_right_con">
					<div class="sp_nav1">
						   <ul id="depart_users">
						   	<li class="sp_nav_01">
							<ul>
						   ';
						if(is_file("../cache/channels_$comId.php")){
							$cache = 1;
							$content = file_get_contents("../cache/channels_$comId.php");
							$departs = json_decode($content);
						}else{
							$departs = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
						}
						if(!empty($departs)){
							foreach($departs as $v){
								$str .='<li class="sp_nav_01_zimenu">
										  <img src="images/tree_bg2.jpg" onclick="get_users('.$v->id.')" data-id="'.$v->id.'" class="depart_select_img" />
										  <a href="javascript:add_depart('.$v->id.',\''.$v->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$list->title.'">'.sys_substr($v->title,10,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$v->id.'" style="display:none;"></ul>
										  <ul>';
								if($cache==1){
									$departs1 = $v->channels;
								}else{
									$departs1 = $db->get_results("select * from demo_product_channel where parentId=".$v->id." order by ordering desc,id asc");
								}
								if(!empty($departs1)){
									foreach($departs1 as $list){
										$str .='<li class="sp_nav_01_zimenu1">
										  <img src="images/tree_bg2.jpg" onclick="get_users('.$list->id.')" data-id="'.$list->id.'" class="depart_select_img" />
										  <a href="javascript:add_depart('.$list->id.',\''.$list->title.'\')" class="sp_nav_01_02">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name" title="'.$list->title.'">'.sys_substr($list->title,9,true).'</div>
											   <div class="clearBoth"></div>
										  </a>
										  <ul id="departUsers'.$list->id.'" style="display:none;"></ul>
										  <ul>';
										if($cache==1){
											$departs2 = $list->channels;
										}else{
											$departs2 = $db->get_results("select * from demo_product_channel where parentId=".$list->id." order by ordering desc,id asc");
										}
										if(!empty($departs2)){
											foreach($departs2 as $depart2){
													$str .='<li class="sp_nav_01_zimenu1">
													  <img src="images/tree_bg2.jpg" onclick="get_users('.$depart2->id.')" data-id="'.$depart2->id.'" class="depart_select_img" />
													  <a href="javascript:add_depart('.$depart2->id.',\''.$depart2->title.'\')" class="sp_nav_01_02">
															<div class="sp_nav_01_01_img"></div>
														   <div  class="sp_nav_01_01_name" title="'.$depart2->title.'">'.sys_substr($depart2->title,8,true).'</div>
														   <div class="clearBoth"></div>
													  </a>
													  <ul id="departUsers'.$depart2->id.'" style="display:none;"></ul>
													  <ul>';
														if($cache==1){
															$departs3 = $depart2->channels;
														}else{
															$departs3 = $db->get_results("select * from demo_product_channel where parentId=".$depart2->id." order by ordering desc,id asc");
														}
														if(!empty($departs3)){
															foreach($departs3 as $depart3){
																	$str .='<li class="sp_nav_01_zimenu1">
																	  <img src="images/tree_bg2.jpg" onclick="get_users('.$depart3->id.')" data-id="'.$depart3->id.'" class="depart_select_img" />
																	  <a href="javascript:add_depart('.$depart3->id.',\''.$depart3->title.'\')" class="sp_nav_01_02">
																			<div class="sp_nav_01_01_img"></div>
																		   <div  class="sp_nav_01_01_name" title="'.$depart3->title.'">'.sys_substr($depart3->title,7,true).'</div>
																		   <div class="clearBoth"></div>
																	  </a>
																	  <ul id="departUsers'.$depart3->id.'" style="display:none;"></ul>
																	  </li>';
															}
														}
														$str .='</ul></li>';
											}
										}
										$str .='</ul></li>';
									}
								}
								$str .='</ul></li>';
							}
						}  
				$str .='</ul></li></ul>
					<ul id="search_users"></ul>
					 </div>
				</div>
				
			</div>
			<div class="clearBoth"></div>
			<div class="splc_cont_bottom">
			<input type="button" onclick="baocun();" value="保存" />
			<input type="button" onclick="hide_myModal();" value="取消" />
			</div>
		  </div>
		</div>';
	echo $str;
	exit;
}
function getPdtsByChannel(){
	global $db,$request;
	$comId = $_SESSION[TB_PREFIX.'comId'];
	$channelId = (int)$request['id'];
	$keyword = $request['keyword'];
	if(!empty($channelId)){
		$sql="SELECT id,title,key_vals FROM demo_product_inventory WHERE channelId=$channelId and comId=$comId limit 200";
	}else{
		$sql="SELECT id,title,key_vals FROM demo_product_inventory WHERE comId=$comId and title like '%$keyword%' limit 50";
	}
	$users=$db->get_results($sql);
	$str = "";
	if(!empty($users)){
		foreach($users as $user){
			$str.='<li class="sp_nav_02" onclick="add_user('.$user->id.',\''.$user->title.($user->key_vals=='无'?'':'('.$user->key_vals.')').'\')" title="'.$user->title.($user->key_vals=='无'?'':'('.$user->key_vals.')').'"><div class="gg_people_show_3_1" style="float:left; margin-right:5px;">商品</div><div style="height:40px;display: table-cell;vertical-align: middle;">'.sys_substr($user->title,7,true).($user->key_vals=='无'?'':'<br>'.sys_substr($user->key_vals,8,true)).'</div><div class="clearBoth"></div></li>';
		}
	}else{
		if(!empty($department)){
			$str.='<li class="sp_nav_02">该分类下没有商品</li>';
		}else{
			$str.='<li class="sp_nav_02">没有搜索到相关商品</li>';
		}
	}
	echo $str;
	exit;
}
function addFahuoStore(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$areaIds = $request['areaIds'];
	$storeId = (int)$request['storeId'];
	$db->query("insert into demo_shezhi_fahuo(comId,areaIds,storeId) values($comId,'$areaIds',$storeId)");
	$id = $db->get_var("select last_insert_id();");
	echo '{"code":1,"id":'.$id.',"message":"添加成功"}';
	exit;
}
function delFahuoStore(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$db->query("delete from demo_shezhi_fahuo where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function del_level(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$ifhas = $db->get_var("select id from users where comId=$comId and level=$id limit 1");
	if(!empty($ifhas)){
		echo '{"code":0,"message":"该级别下已存在会员，不能删除"}';
		exit;
	}
	$db->query("delete from user_level where id=$id and comId=$comId");
	echo '{"code":1,"message":"操作成功"}';
	exit;
}
function dayin(){
	global $db,$request;
	if($request['tijiao']==1){
		$comId = (int)$_SESSION[TB_PREFIX.'comId'];
		$prints = array();
		$prints['id'] = (int)$request['id'];
		$prints['comId'] = $comId;
		$prints['userId'] = $request['userId'];
		$prints['Akey'] = $request['Akey'];
		$prints['Tnumber'] = $request['Tnumber'];
		$prints['Tkey'] = $request['Tkey'];
		$prints['status'] =  $request['status']=='on'?1:0;
		$prints['if_auto'] =  $prints['status'];
		$prints['storeId'] = (int)$request['storeId'];
		$db->insert_update('demo_prints',$prints,'id');
		redirect('?s=mendian_set&a=dayin&storeId='.$prints['storeId']);
	}
}