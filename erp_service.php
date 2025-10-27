<?php
@session_start();
header('Content-type: text/html; charset=utf-8');
error_reporting(E_ERROR);
$dirName=dirname(__FILE__);
define('THISISADMINI',true);
$shlConfig=$dirName.'/config/dt-config.php';
require($shlConfig);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$_REQUEST = cleanArrayForMysql($_REQUEST);
$request  = $_REQUEST;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(empty($comId)){
	echo '请重新登录获取';
	exit;
}
switch($request['action']){
    case 'get_search_channels':
        $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_search_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_search_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_search_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
        break;
    case 'get_study_channels':
        $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
        break;
    case 'get_recruit_channels1':
        
         $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_recruit_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_recruit_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'/'.$c->en_title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_recruit_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_recruit_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		
		echo $str;
		exit;
        break;
    case 'get_recharge_channels':
        $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
        break;
    case 'get_change_channels':
        $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
        break;
	case 'get_product_brands':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_product_brand where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_product_brand where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_product_brand where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
	break;    
        
	case 'get_product_channels':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
	break;
	case 'get_web_links':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
	
		if(empty($channels))$channels = $db->get_results("select * from web_links where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from web_links where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					$str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from web_links where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								$str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($c2->id!=$id){
											$str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo $str;
		exit;
	break;
	
	case 'get_recharge_channels1':
	    $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		echo $str;
	    
	    break;
	
	case 'get_change_channels1':
	    $id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_change_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		echo $str;
	    
	    break;
	case 'get_product_channels1':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		echo $str;
	break;
	
	case 'get_study_channels2':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
// 		if(is_file("cache/channels_$comId.php")){
// 			$cache = 1;
// 			$content = file_get_contents("cache/channels_$comId.php");
// 			$channels = json_decode($content);
// 		}
		if(empty($channels))$channels = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		echo $str;
	break;
	
	case 'get_study_channels1':
	    $id = (int)$request['id'];
		$str = '{"code":1,"message":"成功","items":[';
		$channelstr = '';
		$menus = $db->get_results("select id,title from demo_study_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($menus)){
			foreach ($menus as $m){
				$hasNext = $db->get_var("select id from demo_study_channel where comId=$comId and parentId=".$m->id." limit 1");
				$hasNext = empty($hasNext)?0:1;
				$channelstr.=',{"id":'.$m->id.',"title":"'.$m->title.'","hasNext":'.$hasNext.'}';
			}
			$channelstr = substr($channelstr,1);
		}
		$str .=$channelstr.']}';
		echo $str;
		exit;
	    break;
	case 'get_wenzhang_channels':
		$id = (int)$request['id'];
		$str = '{"code":1,"message":"成功","items":[';
		$channelstr = '';
		$menus = $db->get_results("select id,title from demo_list_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($menus)){
			foreach ($menus as $m){
				$hasNext = $db->get_var("select id from demo_list_channel where comId=$comId and parentId=".$m->id." limit 1");
				$hasNext = empty($hasNext)?0:1;
				$channelstr.=',{"id":'.$m->id.',"title":"'.$m->title.'","hasNext":'.$hasNext.'}';
			}
			$channelstr = substr($channelstr,1);
		}
		$str .=$channelstr.']}';
		echo $str;
		exit;
	    break;
	case 'get_zirecharge_channels':
	    $id = (int)$request['id'];
		$str = '{"code":1,"message":"成功","items":[';
		$channelstr = '';
		$menus = $db->get_results("select id,title from demo_recharge_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($menus)){
			foreach ($menus as $m){
				$hasNext = $db->get_var("select id from demo_recharge_channel where comId=$comId and parentId=".$m->id." limit 1");
				$hasNext = empty($hasNext)?0:1;
				$channelstr.=',{"id":'.$m->id.',"title":"'.$m->title.'","hasNext":'.$hasNext.'}';
			}
			$channelstr = substr($channelstr,1);
		}
		$str .=$channelstr.']}';
		echo $str;
		exit;
	    break;
    case 'get_zichange_channels':
		$id = (int)$request['id'];
		$str = '{"code":1,"message":"成功","items":[';
		$channelstr = '';
		$menus = $db->get_results("select id,title from demo_change_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($menus)){
			foreach ($menus as $m){
				$hasNext = $db->get_var("select id from demo_change_channel where comId=$comId and parentId=".$m->id." limit 1");
				$hasNext = empty($hasNext)?0:1;
				$channelstr.=',{"id":'.$m->id.',"title":"'.$m->title.'","hasNext":'.$hasNext.'}';
			}
			$channelstr = substr($channelstr,1);
		}
		$str .=$channelstr.']}';
		echo $str;
		exit;
	    break;	    
	    
	case 'get_zi_channels':
		$id = (int)$request['id'];
		$str = '{"code":1,"message":"成功","items":[';
		$channelstr = '';
		$menus = $db->get_results("select id,title from demo_product_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($menus)){
			foreach ($menus as $m){
				$hasNext = $db->get_var("select id from demo_product_channel where comId=$comId and parentId=".$m->id." limit 1");
				$hasNext = empty($hasNext)?0:1;
				$channelstr.=',{"id":'.$m->id.',"title":"'.$m->title.'","hasNext":'.$hasNext.'}';
			}
			$channelstr = substr($channelstr,1);
		}
		$str .=$channelstr.']}';
		echo $str;
		exit;
	break;
	
	 case 'get_pdts_channels':
        $id = (int)$request['id'];
        $pid = (int)$request['pid'];
        if(empty($channels))$channels = $db->get_results("select * from demo_list_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
        $str = '';
        if(!empty($channels)){
            foreach ($channels as $c) {
                if($cache==1){
                    $channels1 = $c->channels;
                }else{
                    $channels1 = $db->get_results("select * from demo_list_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
                }
                if($c->id!=$id){
                    $str.='<option value="'.$c->id.'" '.($c->id==$pid?'selected="true"':'').'>'.$c->title.'</option>';
                    if(!empty($channels1)){
                        foreach ($channels1 as $c1) {
                            if($cache==1){
                                $channels2 = $c1->channels;
                            }else{
                                $channels2 = $db->get_results("select * from demo_list_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
                            }
                            if($c1->id!=$id){
                                $str.='<option value="'.$c1->id.'" '.($c1->id==$pid?'selected="true"':'').'>----'.$c1->title.'</option>';
                                if(!empty($channels2)){
                                    foreach ($channels2 as $c2) {
                                        if($c2->id!=$id){
                                            $str.='<option value="'.$c2->id.'" '.($c2->id==$pid?'selected="true"':'').'>--------'.$c2->title.'</option>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo $str;
        exit;
    break;
	
	case 'get_pdtsn_info':
		$productId = (int)$request['productId'];
		$key_ids = $request['key_ids'];
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
		}
		$product = $db->get_row("select id,sn,code,price_market,price_sale,weight,cont1,cont2,cont3,kucun,productId from demo_product_inventory where productId=$productId and key_ids='$key_ids' limit 1");
		$retrun = array();
		if(!empty($product)){
			$product->price_market = getXiaoshu($product->price_market,$product_set->price_num);
			$product->price_sale = getXiaoshu($product->price_sale,$product_set->price_num);
			$product->kucun = getXiaoshu($product->kucun,$product_set->number_num);
			if(empty($product->cont1)){
				$pdt = $db->get_row("select cont1,cont2,cont3 from demo_product where id=".$product->productId);
				$product->cont1 = $pdt->cont1;
				$product->cont2 = $pdt->cont2;
				$product->cont3 = $pdt->cont3;
			}
			$retrun['code'] = 1;
			$retrun['message'] = '成功';
			$retrun['inventoryId'] = $product->id;
			$retrun['pdt_info'] = $product;
		}else{
			$retrun['code'] = 1;
			$retrun['message'] = '成功';
			$retrun['inventoryId'] = 0;
			$retrun['pdt_info'] = array();
		}
		echo json_encode($retrun,JSON_UNESCAPED_UNICODE);
		exit;
	break;
	case 'getDeparts':
		$id = $request['id'];
		$type = $request['type'];
		$showtitle = '选择审批人';
		$crmdb = getCrmDb();
		$str='<div id="add_lianhegj">
			    <div id="new_title">
				    <div class="new_title_01">'.$showtitle.'</div>
					<div class="new_title_02" onclick="hide_myModal();"></div>
					<div class="clearBoth"></div>
				</div>
			  <div id="add_lianhe_con" style="margin-bottom:20px;"><input type="hidden" id="xiefang_uids" value='.$request['uids'].'>
  	 			<ul>';
					$sql = "select * from demo_department where comId=$comId and parentId=0 order by id asc";
					$departs = $crmdb->get_results($sql);
					if(!empty($departs)){
						foreach($departs as $depart){
							$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart->id);
							$str.='
							<li class="add_lianhegj_01" onclick="showDepartUsers('.$depart->id.','.$renshu.','.$id.',\''.$type.'\')" title="点击展开成员">'.$depart->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart->id.'" class="users_div" style="display:none;"></div>';
						$departs1 = $crmdb->get_results("select * from demo_department where parentId=".$depart->id." order by id asc");
						if(!empty($departs1)){
							foreach($departs1 as $depart1){
								$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart1->id);
								$str.='<li class="add_lianhegj_02" onclick="showDepartUsers('.$depart1->id.','.$renshu.','.$id.',\''.$type.'\')" title="点击展开成员">'.$depart1->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart1->id.'" class="users_div" style="display:none;"></div>';
							$departs2 = $crmdb->get_results("select * from demo_department where parentId=".$depart1->id." order by id asc");
							if(!empty($departs2)){
								foreach($departs2 as $depart2){
									$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart2->id);
									$str.='<li class="add_lianhegj_02" onclick="showDepartUsers('.$depart2->id.','.$renshu.','.$id.',\''.$type.'\')" title="点击展开成员">　　'.$depart2->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart2->id.'" class="users_div" style="display:none;"></div>';
							}
						}
					}
				}
			}
		}
		$str.='</ul></div></div>';
		echo $str;
	break;
	case 'get_shenpi_users':
		$crmdb = getCrmDb();
		$department = $request['id'];
		$id = $request['typeId'];
		$type = $request['type'];
		$sql="SELECT id,name FROM demo_user WHERE department=$department and comId=$comId and auditing=1";
		$users=$crmdb->get_results($sql);
		$str = "";
		$functionName = 'selectUser';
		if(!empty($users)){
			foreach($users as $user){
				$str.='<li class="add_lianhegj_03">
		       		<div class="add_lianhegj_03_div" style="margin:0px;float:left;margin-right:5px;">'.substr($user->name,-6).'</div>'.$user->name.'<span>';
					$str.='<a href="javascript:'.$functionName.'('.$user->id.',\''.$user->name.'\',\''.$type.'\','.$id.');">+选择</a>';
		         $str.='</span></li>';
			}
		}
		echo $str;
	break;
	case 'getPdtList':
		$id = (int)$request['id'];
		$keyword = $request['keyword'];
		$hasIds = $request['hasIds'];
		$storeId = (int)$request['storeId'];
		$hasArry = explode(',',$hasIds);
		$sql = "select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId";
		if($comId==10){
			$sql = "select id,sn,title,key_vals,productId from demo_product_inventory where if_tongbu=1";
		}
		if(!empty($keyword)){
			$sql.=" and (sn like '%$keyword%' or title like '%$keyword%')";
		}
		$sql.=" limit 10";
		$pdts = $db->get_results($sql);
		$str = '';
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$zifu = $pdt->sn.' '.$pdt->title;
				if(!empty($pdt->key_vals)&&$pdt->key_vals!='无'){
					$zifu.=' 【'.$pdt->key_vals.'】';
				}
				$zifu = sys_substr($zifu,40,true);
				$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
				$unitstr = '';
				$units = json_decode($product->untis,true);
				$unitstr = $units[0]['title'];
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$pdt->id." and storeId=$storeId limit 1");
				if(empty($kucun))$kucun=0;
				if(in_array($pdt->id,$hasArry)){
					$str.='<li><a style="color:#aaa">'.$zifu.'</a></li>';
				}else{
					$str.='<li><a href="javascript:" onclick="selectRow('.$id.','.$pdt->id.',\''.$pdt->sn.'\',\''.$pdt->title.'\',\''.$pdt->key_vals.'\','.$pdt->productId.',\''.$unitstr.'\',\''.$kucun.'\')">'.$zifu.'</a></li>';
				}
			}
		}else{
			$str='<li style="padding:20px;text-align:center;">未找到产品</li>';
		}
		echo $str;
		exit;
	break;
	case 'getPdtByCode':
		$code = $request['code'];
		$storeId = (int)$request['storeId'];
		$sql = "select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId and code='$code' limit 1";
		$inventory = $db->get_row($sql);
		if(empty($inventory)){
			echo '{"code":0,"message":"没有检测到对应条码的商品，请重新扫码或核实！"}';
		}else{
			$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$inventory->productId);
			$unitstr = '';
			$units = json_decode($product->untis,true);
			$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=".$inventory->id." and storeId=$storeId limit 1");
			if(empty($kucun))$kucun=0;
			$unitstr = $units[0]['title'];
			$return = array();
			$return['code'] = 1;
			$return['data'] = array();
			$return['data']['id'] = $inventory->id;
			$return['data']['sn'] = $inventory->sn;
			$return['data']['title'] = $inventory->title;
			$return['data']['key_vals'] = $inventory->key_vals;
			$return['data']['productId'] = $inventory->productId;
			$return['data']['units'] = $unitstr;
			$return['data']['kucun'] = $kucun;
			echo json_encode($return,JSON_UNESCAPED_UNICODE);
		}
		exit;
	break;
	case 'getGonghuoList':
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
		}
		$id = (int)$request['id'];
		$keyword = $request['keyword'];
		$hasIds = $request['hasIds'];
		$supplierId = (int)$request['supplierId'];
		$hasArry = explode(',',$hasIds);
		$sql = "select id,sn,title,key_vals,productId from demo_product_inventory where comId=$comId";
		if($comId==10){
			$sql = "select id,sn,title,key_vals,productId from demo_product_inventory where if_tongbu=1";
		}
		if(!empty($keyword)){
			$sql.=" and (sn like '%$keyword%' or title like '%$keyword%')";
		}
		//是否是促销调用
		$cuxiao = (int)$request['cuxiao'];
		/*if($cuxiao==1){
			$startTime = $request['startTime'];
			$endTime = $request['endTime'];
			if(!empty($startTime)&&!empty($endTime)){
				$ids = $db->get_var("select group_concat(pdtIds) from cuxiao_pdt where comId=$comId and status=1 and ((startTime<'$startTime' and endTime>'$startTime') or (startTime<'$endTime' and endTime>'$endTime') or (startTime>'$startTime' and endTime<'$endTime'))");
			}
			if(empty($ids))$ids='0';
			$sql.=" and id not in($ids)";
		}*/
		if(!empty($supplierId)){
			$pdts = $db->get_var("select pdts from demo_supplier where id=$supplierId and comId=$comId");
			if(!empty($pdts)){
				$sql.=" and id in($pdts)";
			}else{
				echo '<li style="padding:20px;text-align:center;">尚未设置供货商品,<a href="javascript:" onclick="setGonghuo('.$supplierId.');" style="color:#03b8cc;text-decoration:underline;">前往设置</a></li>';
				exit;
			}
		}
		$sql.=" limit 10";
		$pdts = $db->get_results($sql);
		$str = '';
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$zifu = $pdt->sn.' '.$pdt->title;
				if(!empty($pdt->key_vals)&&$pdt->key_vals!='无'){
					$zifu.=' 【'.$pdt->key_vals.'】';
				}
				$zifu = sys_substr($zifu,40,true);
				$product=$db->get_row("select unit_type,untis,brandId from demo_product where id=".$pdt->productId);
				$unitstr = '';
				$units = json_decode($product->untis,true);
				$unitstr = $units[0]['title'];
				if(in_array($pdt->id,$hasArry)){
					$str.='<li><a style="color:#aaa">'.$zifu.'</a></li>';
				}else{
					if(!empty($supplierId)){
						$price = $db->get_var("select price from demo_supplier_gonghuo where supplierId=$supplierId and inventoryId=".$pdt->id.' limit 1');
					}
					if(empty($price))$price = 0;
					$price = getXiaoshu($price,$product_set->price_num);
					$str.='<li onclick="selectRow('.$id.','.$pdt->id.',\''.$pdt->sn.'\',\''.$pdt->title.'\',\''.$pdt->key_vals.'\','.$pdt->productId.',\''.$unitstr.'\',\''.$kucun.'\',\''.$price.'\')"><a href="javascript:">'.$zifu.'</a></li>';
				}
			}
		}else{
			$str='<li style="padding:20px;text-align:center;">未找到产品</li>';
		}
		echo $str;
		exit;
	break;
	case 'getKehuList':
		$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
		$keyword = $request['keyword'];
		$hasIds = $request['hasIds'];
		$sql = "select id,title,level from demo_kehu where comId=$comId";
		if(!empty($keyword)){
			$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or sn like '%$keyword%' or name like '%$keyword%' or phone like '%$keyword%')";
		}
		if(!empty($hasIds)){
			$sql.=" and id not in($hasIds)";
		}
		$sql.=" order by id desc limit 10";
		$pdts = $db->get_results($sql);
		$str = '';
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$level = $db->get_var("select title from demo_kehu_level where id=$pdt->level");
				$str.='<li onclick="selectKehu('.$pdt->id.',\''.$pdt->title.'\',\''.$level.'\')"><a href="javascript:" >'.$pdt->title.'</a></li>';
			}
		}else{
			$str='<li style="padding:20px;text-align:center;">未找到'.$kehu_title.'</li>';
		}
		echo $str;
		exit;
	break;
	case 'getRowKehuList':
		$id = (int)$request['id'];
		$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
		$keyword = $request['keyword'];
		$hasIds = $request['hasIds'];
		$sql = "select id,title,level from demo_kehu where comId=$comId";
		if(!empty($keyword)){
			$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or sn like '%$keyword%' or name like '%$keyword%' or phone like '%$keyword%')";
		}
		if(!empty($hasIds)){
			$sql.=" and id not in($hasIds)";
		}
		$sql.=" order by id desc limit 10";
		$pdts = $db->get_results($sql);
		$str = '';
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$level = $db->get_var("select title from demo_kehu_level where id=$pdt->level");
				$str.='<li onclick="selectRow('.$id.','.$pdt->id.',\''.$pdt->title.'\',\''.$level.'\')"><a href="javascript:" >'.$pdt->title.'</a></li>';
			}
		}else{
			$str='<li style="padding:20px;text-align:center;">未找到'.$kehu_title.'</li>';
		}
		echo $str;
		exit;
	break;
	case 'getTuihuoList':
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
		}
		$fenbiao = getFenbiao($comId,20);
		$id = (int)$request['id'];
		$keyword = $request['keyword'];
		$hasIds = $request['hasIds'];
		$storeId = (int)$request['storeId'];
		$caigouId = (int)$request['caigouId'];
		$hasArry = explode(',',$hasIds);
		$sql = "select * from demo_caigou_detail$fenbiao where jiluId=$caigouId and hasNum>0";
		if(!empty($keyword)){
			$sql.=" and pdtInfo like '%$keyword%'";
		}
		$sql.=" limit 10";
		$pdts = $db->get_results($sql);
		$str = '';
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
				if(empty($kucun))continue;
				if($kucun>$pdt->hasNum)$kucun = $pdt->hasNum;
				$kucun = $kucun-$pdt->tuihuoNum;
				if($kucun<0)$kucun=0;
				$pdtInfo = json_decode($pdt->pdtInfo,true);
				$zifu = $pdtInfo['sn'].' '.$pdtInfo['title'];
				if(!empty($pdtInfo['key_vals'])&&$pdtInfo['key_vals']!='无'){
					$zifu.=' 【'.$pdtInfo['key_vals'].'】';
				}
				$zifu = sys_substr($zifu,40,true);
				$units = $pdt->units;
				if(in_array($pdt->inventoryId,$hasArry)){
					$str.='<li><a style="color:#aaa">'.$zifu.'</a></li>';
				}else{
					$price = getXiaoshu($pdt->unit_price,$product_set->price_num);
					$str.='<li><a href="javascript:" onclick="selectRow('.$id.','.$pdt->inventoryId.',\''.$pdtInfo['sn'].'\',\''.$pdtInfo['title'].'\',\''.$pdtInfo['key_vals'].'\','.$pdt->productId.',\''.$units.'\',\''.$kucun.'\',\''.$price.'\',\''.$pdt->num.'\')">'.$zifu.'</a></li>';
				}
			}
		}else{
			$str='<li style="padding:20px;text-align:center;">未找到可退货商品</li>';
		}
		echo $str;
		exit;
	break;
	case 'getAllTuihuos':
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select price_num,number_num from demo_product_set where comId=$comId");
		}
		$fenbiao = getFenbiao($comId,20);
		$storeId = (int)$request['storeId'];
		$caigouId = (int)$request['caigouId'];
		$sql = "select * from demo_caigou_detail$fenbiao where jiluId=$caigouId and hasNum>0";
		$sql.=" limit 10";
		$pdts = $db->get_results($sql);
		$returnJson = array();
		$returnJson['code'] = 1;
		$str = '';
		$count = 0;
		if(!empty($pdts)){
			foreach ($pdts as $pdt){
				$count++;
				$kucun = $db->get_var("select kucun from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
				//if(empty($kucun))continue;
				if($kucun>$pdt->hasNum)$kucun = $pdt->hasNum;
				$kucun = $kucun-$pdt->tuihuoNum;
				if($kucun<0)$kucun=0;
				$pdtInfo = json_decode($pdt->pdtInfo,true);
				$price = getXiaoshu($pdt->unit_price,$product_set->price_num);
				$xiaoji = $price*$kucun;
				$str.='<tr height="48" id="rowTr'.$count.'">
				<td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle">1</td><td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle"><a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"></a>  <a href="javascript:" onclick="delRow('.$count.');"><img src="images/biao_66.png"></a> </td><td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'.$pdtInfo['sn'].'</td><td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'.$pdtInfo['title'].'</td><td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'.$pdtInfo['key_vals'].'</td><td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'.$pdt->units.'</td><td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'.$pdt->num.'</td><td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle"><input type="text" lay-verify="required|number|kucun" name="inventoryNum['.$count.']" max="'.$kucun.'" value="'.$kucun.'" onmouseenter="tips(this,\'最多可退'.$kucun.'\',1)" onmouseout="hideTips();" onchange="renderPrice('.$count.');" class="sprukuadd_03_tt_input"><input type="hidden" name="inventoryId['.$count.']" value="'.$pdt->inventoryId.'"><input type="hidden" name="inventorySn['.$count.']" value="'.$pdtInfo['sn'].'"><input type="hidden" name="inventoryTitle['.$count.']" value="'.$pdtInfo['title'].'"><input type="hidden" name="inventoryKey_vals['.$count.']" value="'.$pdtInfo['key_vals'].'"><input type="hidden" name="inventoryBeizhu['.$count.']" id="beizhu'.$count.'" value=""><input type="hidden" name="inventoryPdtId['.$count.']" value="'.$pdt->productId.'"><input type="hidden" name="inventoryUnits['.$count.']" value="'.$pdt->units.'"></td><td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"><input type="text" lay-verify="required|number|kucun" value="'.$price.'" onchange="renderPrice('.$count.');" name="inventoryPrice['.$count.']" class="sprukuadd_03_tt_input"></td><td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"><input type="text" lay-verify="required|number|kucun" name="inventoryHeji['.$count.']" value="'.$xiaoji.'" onchange="renderHeji('.$count.');" class="sprukuadd_03_tt_input"></td></tr>';
			}
		}
		$returnJson['count'] = $count;
		$returnJson['str'] = $str;
		echo json_encode($returnJson,JSON_UNESCAPED_UNICODE);
		exit;
	break;
	case 'getAreas':
	  $id = $request['id'];
	  $areas = $db->get_results("select id,title from demo_area where parentId=$id order by id asc");
	  $str = "";
	  if(!empty($areas) && count($areas)>0){
	    $str .= "<option value=''>请选择</option>";
	    foreach($areas as $area){
		  $str .= "<option value=".$area->id.">".$area->title."</option>";
		}
	  }
	  echo $str;
	break;
	case 'get_supplier_wanglai':
		$supplierId = (int)$request['id'];
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
		//现购
		$xiangouSql = "select sum(price) from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=1 and status=1";
		//赊购
		$shegouSql = "select sum(price) as alls,sum(price_payed) as payed,sum(price_weikuan) as weikuan from demo_caigou where comId=$comId and supplierId=$supplierId and price_type=2 and status=1";
		//已结
		$yijieSql = "select sum(money) from demo_caigou_repay where comId=$comId and supplierId=$supplierId";
		//退款
		$tuikuanSql = "select sum(money) from demo_caigou_tuikuan where comId=$comId and supplierId=$supplierId and status=1";
		if(!empty($startTime)){
			$xiangouSql.=" and dtTime>='$startTime 00:00:00'";
			$shegouSql.=" and dtTime>='$startTime 00:00:00'";
			$yijieSql.=" and dtTime>='$startTime 00:00:00'";
			$tuikuanSql.=" and dtTime>='$startTime 00:00:00'";
		}
		if(!empty($endTime)){
			$xiangouSql.=" and dtTime<='$startTime 00:00:00'";
			$shegouSql.=" and dtTime<='$startTime 00:00:00'";
			$yijieSql.=" and dtTime<='$startTime 00:00:00'";
			$tuikuanSql.=" and dtTime<='$startTime 00:00:00'";
		}
		$xiangou = $db->get_var($xiangouSql);
		$shegou = $db->get_row($shegouSql);
		$yijie = $db->get_var($yijieSql);
		$tuikuan = $db->get_var($tuikuanSql);
		$xiangou = empty($xiangou)?0:$xiangou;
		$yijie = empty($yijie)?0:$yijie;
		$tuikuan = empty($tuikuan)?0:$tuikuan;
		$dataJson = array("code"=>1,"message"=>"成功");
		$dataJson['price1'] = $xiangou+$shegou->alls;
		$dataJson['price2'] = $xiangou;
		$dataJson['price3'] = empty($shegou->payed)?0:$shegou->payed;
		$dataJson['price4'] = $yijie;
		$dataJson['price5'] = empty($shegou->weikuan)?0:$shegou->weikuan;
		$dataJson['price6'] = '-'.$tuikuan;
		$dataJson['price7'] = $dataJson['price1']-$tuikuan;
		echo json_encode($dataJson);
		exit;
	break;
	case 'get_caigou_huizong':
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
		$fenbiao = getFenbiao($comId,20);
		$orderNum = $db->get_row("select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and status=1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
		$pdtNum = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and status=1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
		$orderNum->orderNum=empty($orderNum->orderNum)?0:$orderNum->orderNum;
		$orderNum->priceNum=empty($orderNum->priceNum)?0:$orderNum->priceNum;
		$orderNum->priceNum = getXiaoshu($orderNum->priceNum,2);
		if(empty($pdtNum))$pdtNum=0;
		$dataJson = array("code"=>1,"message"=>"成功");
		$dataJson['price1'] = $orderNum->orderNum;
		$dataJson['price2'] = $pdtNum;
		$dataJson['price3'] = $orderNum->priceNum;
		echo json_encode($dataJson);
		exit;
	break;
	case 'get_supplier_huizong':
		$supplierId = $request['supplierId'];
		$startTime = $request['startTime'];
		$endTime = $request['endTime'];
		$fenbiao = getFenbiao($comId,20);
		$orderNum = $db->get_row("select count(*) as orderNum,sum(price) as priceNum from demo_caigou where comId=$comId and supplierId=$supplierId and status>-1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
		$pdtNum = $db->get_var("select sum(num) from demo_caigou_detail$fenbiao where comId=$comId and supplierId=$supplierId and status>-1".(!empty($startTime)?" and dtTime>='$startTime 00:00:00'":'').(!empty($endTime)?" and dtTime<='$endTime 23:59:59'":''));
		$orderNum->orderNum=empty($orderNum->orderNum)?0:$orderNum->orderNum;
		$orderNum->priceNum=empty($orderNum->priceNum)?0:$orderNum->priceNum;
		$orderNum->priceNum = getXiaoshu($orderNum->priceNum,2);
		if(empty($pdtNum))$pdtNum=0;
		$dataJson = array("code"=>1,"message"=>"成功");
		$dataJson['price1'] = $orderNum->orderNum;
		$dataJson['price2'] = $pdtNum;
		$dataJson['price3'] = $orderNum->priceNum;
		echo json_encode($dataJson);
		exit;
	break;
	case 'get_shoufa_huizong':
		$storeIds = $request['storeIds'];
		$keyword = $request['keyword'];
		$channelId = (int)$request['channelId'];
		$brandId = (int)$request['brandId'];
		$startTime = empty($request['startTime'])?date("Y-m-01"):$request['startTime'];
		$endTime = empty($request['endTime'])?date("Y-m-d"):$request['endTime'];
		$fenbiao = getFenbiao($comId,20);
		$sql1 = "SELECT distinct inventoryId,storeId  FROM `demo_kucun_jiludetail$fenbiao` where comId=$comId and status=1 and dtTime>'$startTime 00:00:00' and dtTime<'$endTime 23:59:59'";
		if(!empty($storeIds)){
			$sql1.=" and storeId in($storeIds)";
		}
		if(!empty($keyword)){
			$sql1.=" and pdtInfo like '%$keyword%'";
		}
		if(!empty($channelId)){
			$channelIds = $channelId.getZiIds($channelId);
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
			if(empty($pdtIds))$pdtIds='0';
			$sql1.=" and productId in($pdtIds)";
		}
		if(!empty($brandId)){
			$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
			if(empty($productIds))$productIds='0';
			$sql1.=" and productId in($productIds)";
		}
		$qichuJilus = $db->get_results($sql1);
		$price1 = 0;
		$price2 = 0;
		$price3 = 0;
		$price4 = 0;
		if(!empty($qichuJilus)){
			foreach ($qichuJilus as $jilu){
				$chengben = $db->get_var("select zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$jilu->inventoryId and storeId=$jilu->storeId and dtTime<'$startTime' and status=1 order by id desc limit 1");
				$chengben1= $db->get_var("select zongchengben from demo_kucun_jiludetail$fenbiao where inventoryId=$jilu->inventoryId and storeId=$jilu->storeId and dtTime<='$endTime 23:59:59' and status=1 order by id desc limit 1");
				$price1+=$chengben;
				$price4+=$chengben1;
			}
		}
		$sql2 = "select sum(chengben) as zongchengben,type from demo_kucun_jiludetail$fenbiao where comId=$comId and status=1 and dtTime>='$startTime 00:00:00' and dtTime<='$endTime 23:59:59'";
		if(!empty($storeIds)){
			$sql2.=" and storeId in($storeIds)";
		}
		if(!empty($keyword)){
			$sql2.=" and pdtInfo like '%$keyword%'";
		}
		if(!empty($channelId)){
			$channelIds = $channelId.getZiIds($channelId);
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and channelId in($channelIds)");
			if(empty($pdtIds))$pdtIds='0';
			$sql2.=" and productId in($pdtIds)";
		}
		if(!empty($brandId)){
			$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
			if(empty($productIds))$productIds='0';
			$sql2.=" and productId in($productIds)";
		}
		$sql2.=" group by type";
		$churuku = $db->get_results($sql2);
		if(!empty($churuku)){
			foreach ($churuku as $c) {
				switch ($c->type){
					case 1:
						$price2+=$c->zongchengben;
					break;
					case 2:
						$price3+=$c->zongchengben;
					break;
				}
			}
		}
		$dataJson = array("code"=>1,"message"=>'');
		$dataJson['price1'] = $price1;
		$dataJson['price2'] = $price2;
		$dataJson['price3'] = $price3;
		$dataJson['price4'] = $price4;
		echo json_encode($dataJson);
	break;
	case 'checkPdtTitle':
		$id = (int)$request['id'];
		$title = $request['title'];
		$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and title='$title' and productId<>$id limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"产品已经存在，请输入其他产品名称！"}';
		}else{
			echo '{"code":1,"message":"成功"}';
		}
		exit;
	break;
	case 'checkKehuUsername':
		$username = $request['username'];
		$ifhas = $db->get_var("select id from demo_kehu where comId=$comId and username='$username' limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"用户名已经存在，请重新输入！"}';
		}else{
			echo '{"code":1,"message":"成功"}';
		}
		exit;
	break;
	case 'checkKehuTitle':
		$id = (int)$request['id'];
		$title = $request['title'];
		$ifhas = $db->get_var("select id from demo_kehu where comId=$comId and title='$title' and id<>$id limit 1");
		if(!empty($ifhas)){
			echo '{"code":0,"message":"该客户名称已经存在！"}';
		}else{
			echo '{"code":1,"message":"成功"}';
		}
		exit;
	break;
	case 'getComDeparts':
		$crmdb = getCrmDb();
		$pid = (int)$request['pid'];
		$depars = "<option value=''>请选择部门</option>";
		$departs = $crmdb->get_results("select * from demo_department where comId=$comId and parentId=0 order by id asc");
		if(!empty($departs)){
			foreach($departs as $v){
				$depars .='<option value="'.$v->id.'" '.($pid==$v->id?'selected="selected"':'').'>'.$v->title.'</option>';
				$departs1 = $crmdb->get_results("select * from demo_department where parentId=".$v->id." order by id asc");
				if(!empty($departs1)){
					foreach($departs1 as $list){
						$depars .='<option value="'.$list->id.'" '.(($pid==$list->id)?'selected="selected"':'').'>---'.$list->title.'</option>';
						$departs2 = $crmdb->get_results("select * from demo_department where parentId=".$list->id." order by id asc");
						if(!empty($departs2)){
							foreach($departs2 as $list1){
								$depars .='<option value="'.$list1->id.'" '.(($pid==$list1->id)?'selected="selected"':'').'>-----'.$list1->title.'</option>';
							}
						}
					}
				}
			}
		}
		echo $depars;
		exit;
	break;
	case 'getZhishangDeparts':
		$showtitle = '选择员工';
		$crmdb = getCrmDb();
		$nousers = '0';
		$str='<div id="add_lianhegj">
			    <div id="new_title">
				    <div class="new_title_01">'.$showtitle.'</div>
					<div class="new_title_02" onclick="hide_myModal();"></div>
					<div class="clearBoth"></div>
				</div>
			  <div id="add_lianhe_con" style="margin-bottom:20px;"><input type="hidden" id="xiefang_uids" value='.$request['uids'].'>
  	 			<ul>';
					$sql = "select * from demo_department where comId=$comId and parentId=0 order by id asc";
					$departs = $crmdb->get_results($sql);
					if(!empty($departs)){
						foreach($departs as $depart){
							$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart->id." and id not in($nousers)");
							$str.='
							<li class="add_lianhegj_01" onclick="showDepartUsers('.$depart->id.','.$renshu.')" title="点击展开成员">'.$depart->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart->id.'" class="users_div" style="display:none;"></div>';
						if($cache==1){
							$departs1 = $depart->departs1;
						}else{
							$departs1 = $crmdb->get_results("select * from demo_department where parentId=".$depart->id." order by id asc");
						}
						if(!empty($departs1)){
							foreach($departs1 as $depart1){
								$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart1->id." and id not in($nousers)");
								$str.='<li class="add_lianhegj_02" onclick="showDepartUsers('.$depart1->id.','.$renshu.')" title="点击展开成员">'.$depart1->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart1->id.'" class="users_div" style="display:none;"></div>';
							if($cache==1){
								$departs2 = $depart1->departs2;
							}else{
								$departs2 = $crmdb->get_results("select * from demo_department where parentId=".$depart1->id." order by id asc");
							}
							if(!empty($departs2)){
								foreach($departs2 as $depart2){
									$renshu = $crmdb->get_var("select count(*) from demo_user where department=".$depart2->id." and id not in($nousers)");
									$str.='<li class="add_lianhegj_02" onclick="showDepartUsers('.$depart2->id.','.$renshu.')" title="点击展开成员">　　'.$depart2->title.'<span>（'.$renshu.'）</span><div><img src="images/jia.png" /></div></li>
						<div id="users'.$depart2->id.'" class="users_div" style="display:none;"></div>';
							}
						}
					}
				}
			}
		}
		$str.='</ul>
		</div>
		</div>';
		echo $str;
	break;
	case 'get_depart_users':
		$crmdb = getCrmDb();
		$department = $request['id'];
		$sql="SELECT id,name FROM demo_user WHERE department=$department and comId=$comId ";
		$users=$crmdb->get_results($sql);
		$str = "";
		$functionName = 'selectedUser';
		if(!empty($users)){
			foreach($users as $user){
				$str.='<li class="add_lianhegj_03">
		       		<div class="add_lianhegj_03_div" style="margin:0px;float:left;margin-right:5px;">'.substr($user->name,-6).'</div>'.$user->name.'<span>';
					$str.='<a href="javascript:'.$functionName.'('.$user->id.',\''.$user->name.'\');">+选择</a>';
		         $str.='</span></li>';
			}
		}
		echo $str;
	break;
	case 'getSupplierByCaigou':
		echo $db->get_var("select title from demo_supplier where id=(select supplierId from demo_caigou where id=".(int)$request['id'].")");
	break;
	case 'getDinghuoFanwei':
		$id = $request['shenpId'];
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
		$crmdb = getCrmDb();
		$str = '<div id="add_container">
				<div id="new_title">
					<div class="new_title_01">选择员工</div>
					<div class="new_title_02" onclick="hide_myModal();"></div>
					<div class="clearBoth"></div>
				</div>
			  <div id="splc_cont">
				<div class="splc_cont_left">
					<div class="splc_cont_left_title">已选择以下部门或人员</div>
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
					<div class="splc_cont_right_title">所有员工</div>
					<div class="splc_cont_right_search"><input type="text" stlye="border:0px;" onchange="search_users(this.value);" placeholder="请输入姓名/部门/手机号码进行搜索"></div>
					<div class="splc_cont_right_con">
						<div class="sp_nav1">
							   <ul id="depart_users">
							   	<li class="sp_nav_01">
										<img src="images/tree_bg2.jpg" data-id="1" class="depart_select_img" />
										<a href="#" class="sp_nav_01_01">
												<div class="sp_nav_01_01_img"></div>
											   <div  class="sp_nav_01_01_name">全公司</div>
											   <div class="clearBoth"></div>      
										</a>
										<ul>
							   ';
							if(is_file("cache/departs_$comId.php")){
								$cache = 1;
								$content = file_get_contents("cache/departs_$comId.php");
								$departs = @unserialize($content);
							}else{
								$departs = $crmdb->get_results("select * from demo_department where comId=$comId and parentId=0 order by id asc");
							}
							if(!empty($departs)){
								foreach($departs as $v){
									$str .='<li class="sp_nav_01_zimenu">
											  <img src="images/tree_bg2.jpg" onclick="get_users('.$v->id.')" data-id="'.$v->id.'" class="depart_select_img" />
											  <a href="javascript:add_depart('.$v->id.',\''.$v->title.'\')" class="sp_nav_01_02">
													<div class="sp_nav_01_01_img"></div>
												   <div  class="sp_nav_01_01_name">'.$v->title.'</div>
												   <div class="clearBoth"></div>
											  </a>
											  <ul id="departUsers'.$v->id.'" style="display:none;"></ul>
											  <ul>';
									if($cache==1){
										$departs1 = $v->departs1;
									}else{
										$departs1 = $crmdb->get_results("select * from demo_department where parentId=".$v->id." order by id asc");
									}
									if(!empty($departs1)){
										foreach($departs1 as $list){
											$str .='<li class="sp_nav_01_zimenu1">
											  <img src="images/tree_bg2.jpg" onclick="get_users('.$list->id.')" data-id="'.$list->id.'" class="depart_select_img" />
											  <a href="javascript:add_depart('.$list->id.',\''.$list->title.'\')" class="sp_nav_01_02">
													<div class="sp_nav_01_01_img"></div>
												   <div  class="sp_nav_01_01_name">'.$list->title.'</div>
												   <div class="clearBoth"></div>
											  </a>
											  <ul id="departUsers'.$list->id.'" style="display:none;"></ul>
											  <ul>';
											if($cache==1){
												$departs2 = $list->departs2;
											}else{
												$departs2 = $crmdb->get_results("select * from demo_department where parentId=".$list->id." order by id asc");
											}
											if(!empty($departs2)){
												foreach($departs2 as $depart2){
														$str .='<li class="sp_nav_01_zimenu1">
														  <img src="images/tree_bg2.jpg" onclick="get_users('.$depart2->id.')" data-id="'.$depart2->id.'" class="depart_select_img" />
														  <a href="javascript:add_depart('.$depart2->id.',\''.$depart2->title.'\')" class="sp_nav_01_02">
																<div class="sp_nav_01_01_img"></div>
															   <div  class="sp_nav_01_01_name">'.$depart2->title.'</div>
															   <div class="clearBoth"></div>
														  </a>
														  <ul id="departUsers'.$depart2->id.'" style="display:none;"></ul>
														  </li>';
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
	break;
	case 'get_fanwei_users':
		$crmdb = getCrmDb();
	    $department = $request['id'];
		$keyword = $request['keyword'];
		$request['comId'] = (int)$request['comId'];
		if(!empty($request['comId'])){
			$comId = $request['comId'];
		}
		$nowId = $request['nowId'];
		$selected = $request['selected'];
		$selectd_array = array();
		if(!empty($selected)){
			$selectd_array = explode(",",$selected);
		}
		if(!empty($department)){
			$sql="SELECT * FROM demo_user WHERE department=$department and comId=$comId";
		}else{
			$departs = $crmdb->get_var("select group_concat(id) from demo_department where comId=$comId and title like '%$keyword%'");
			$sql="SELECT * FROM demo_user WHERE comId=$comId and department>0 and (name like '%$keyword%' or username like '%$keyword%'";
			if(!empty($departs)){
				$sql.=" or department in($departs)";
			}
			$sql.=") limit 20";
		}
		$users=$crmdb->get_results($sql);
		$str = "";
		if(!empty($users)){
			foreach($users as $user){
				$str.='<li class="sp_nav_02" onclick="add_user('.$user->id.',\''.$user->name.'\')" ><div class="gg_people_show_3_1" style="float:left; margin-right:5px;">'.substr($user->name,-6).'</div>'.$user->name.'</li>';
			}
		}else{
			if(!empty($department)){
				$str.='<li class="sp_nav_02">该部门下没有员工</li>';
			}else{
				$str.='<li class="sp_nav_02">没有搜索到相关员工</li>';
			}
		}
		echo $str;
	break;
	case 'geShopList':
		$crmdb = getCrmDb();
		$keyword = $request['keyword'];
		$sql = "select id,com_title from demo_company where if_tongbu=1";
		if(!empty($keyword)){
			$sql.=" and (com_title like '%$keyword%' or com_phone='$keyword')";
		}
		$companys =$crmdb->get_results($sql);
		$str = "";
		if(!empty($companys)){
			foreach($companys as $user){
				$str.='<li class="sp_nav_02" onclick="add_mendian('.$user->id.',\''.$user->com_title.'\')" >'.$user->com_title.'</li>';
			}
		}else{
			$str.='<li class="sp_nav_02">没有搜索到相关门店</li>';
		}
		echo $str;
	break;
	case 'get_pdt_channels1':
		$id = (int)$request['id'];
		$pid = (int)$request['pid'];
		if(is_file("cache/channels_pdt_$comId.php")){
			$cache = 1;
			$content = file_get_contents("cache/channels_pdt_$comId.php");
			$channels = json_decode($content);
		}
		if(empty($channels))$channels = $db->get_results("select * from demo_pdt_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
		$str = '';
		if(!empty($channels)){
			foreach ($channels as $c) {
				if($cache==1){
					$channels1 = $c->channels;
				}else{
					$channels1 = $db->get_results("select * from demo_pdt_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
				}
				if($c->id!=$id){
					if(empty($channels1)){
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);" >'.$c->title.'</dd>';
					}else{
						$str.='<dd lay-value="'.$c->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c->id.')"><img src="images/biao_73.png"></span>'.$c->title.'</dd><div id="next_menu'.$c->id.'" class="next_menu">';
					}
					if(!empty($channels1)){
						foreach ($channels1 as $c1) {
							if($cache==1){
								$channels2 = $c1->channels;
							}else{
								$channels2 = $db->get_results("select * from demo_pdt_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
							}
							if($c1->id!=$id){
								if(empty($channels2)){
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);">'.$c1->title.'</dd>';
								}else{
									$str.='<dd lay-value="'.$c1->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c1->id.')"><img src="images/biao_73.png"></span>'.$c1->title.'</dd><div id="next_menu'.$c1->id.'" class="next_menu">';
								}
								if(!empty($channels2)){
									foreach ($channels2 as $c2) {
										if($cache==1){
											$channels3 = $c2->channels;
										}else{
											$channels3 = $db->get_results("select * from demo_pdt_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
										}
										if($c2->id!=$id){
											if(empty($channels3)){
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);">'.$c2->title.'</dd>';
											}else{
												$str.='<dd lay-value="'.$c2->id.'" onclick="selectMenu(event,this);"><span class="menuLeft" onclick="showNextMenus(event,this,'.$c2->id.')"><img src="images/biao_73.png"></span>'.$c2->title.'</dd><div id="next_menu'.$c2->id.'" class="next_menu">';
											}
											if(!empty($channels3)){
												foreach ($channels3 as $c3) {
													if($c3->id!=$id)$str.='<dd lay-value="'.$c3->id.'" onclick="selectMenu(event,this);" >'.$c3->title.'</dd>';
												}
											}
											if(!empty($channels3)){
												$str.='</div>';
											}
										}
									}
								}
								if(!empty($channels2)){
									$str.='</div>';
								}
							}
						}
					}
					if(!empty($channels1)){
						$str.='</div>';
					}
				}
			}
		}
		echo $str;
	break;
	case 'chekadmin':
	    $username=$request['username'];
	    $data=$db->get_row("select username from demo_user where username='$username'");
	    if(!empty($data)){
	        echo '{"code":0,"msg":"管理员账号已被占用！"}';
	    }else{
	        echo '{"code":1,"msg":""}';
	    }
	break;
}
function add_msg($toId,$userIds,$title,$content,$type,$msg_id,$msg_img,$comId=0){
	$crmdb = getCrmDb();
	if(empty($comId))$comId=0;
	$crmdb->query("insert into demo_msgs(fromId,toId,userIds,title,content,dtTime,status,type,msg_id,msg_img,comId) value(1,'$toId','$userIds','$title','$content','".date("Y-m-d H:i:s")."',0,$type,$msg_id,'$msg_img',$comId)");
}
function msgTitle($title){
	return '<div class="spxx_shanchu_tanchu_01"><div class="spxx_shanchu_tanchu_01_left">'.$title.'</div><div class="spxx_shanchu_tanchu_01_right"><a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a></div><div class="clearBoth"></div></div>';
}