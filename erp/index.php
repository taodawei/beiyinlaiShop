<?php
@session_start();
@error_reporting(E_ERROR);
// @error_reporting(E_ALL );
header('Content-Type: text/html; charset=utf-8');
define('THISISADMINI',true);
if(!is_file(dirname(__FILE__).'/../config/dt-config.php')||filesize(dirname(__FILE__).'/../config/dt-config.php')==0||filesize(dirname(__FILE__).'/../config/dt-config.php')==3)die('系统异常');
require(dirname(__FILE__).'/../config/dt-config.php');
function_exists('date_default_timezone_set') && @date_default_timezone_set('Etc/GMT-'.TIMEZONENAME);
require(ABSPATH.'/inc/function.php');
if(is_file(ABSPATH.'/inc/common.php'))require_once(ABSPATH.'/inc/common.php');
require(ABSPATH.'/inc/class.database.php');
require(ABSPATH.'/inc/class.pager.php');
if(empty($_SESSION[TB_PREFIX.'admin_name'])){
	if(!empty($_SESSION['kehuId'])){
		$arry = array('upload','dinghuo','tuihuo','money');
		if(!in_array($_REQUEST['s'],$arry)){
			die('错误访问');
		}
	}else{
		echo "<script>location.href='login.php';</script>";
		exit;
	}
}
if($_SESSION['if_shequ']==1){
	//redirect('/erp/index.php');
}
$_REQUEST = cleanArrayForMysql($_REQUEST);
$_GET 	  = cleanArrayForMysql($_GET);
$_POST 	  = cleanArrayForMysql($_POST);
$request  = $_REQUEST;
$p = !empty($request['p']) ? $request['p']: 0;
$mdtp = !empty($request['mdtp']) ? $request['mdtp']: 0;
$request['p']=intval($p);
$request['mdtp']=intval($mdtp);
$pageInfo=array();
$pageInfo['display']=true;
$pageInfo['header']=ABSPATH."/erp/views/header.php";
global $adminRole,$qx_arry,$db,$arr,$if_fenxiao,$if_pintuan;
$adminRole = (int)$_SESSION[TB_PREFIX.'admin_roleId'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
$qx_arry = array();
$request['m'] = '';
$shezhis=$db->get_row("select if_fenxiao,if_pintuan from demo_shezhi where comId=$comId");
$if_fenxiao=$shezhis->if_fenxiao;
$if_pintuan=$shezhis->if_pintuan;
if(empty($request['m'])&&!empty($request['s'])){$request['m']='system';}
if($request['m']=='system'&&!empty($request['s'])){
    $roles=$db->get_var("select a.roles from roles as a,roles_group as b where a.id=b.rolesId and b.userId=$userId");
    $models=$request['m'];
    $controllers=$request['s'];
    $actions=empty($request['a'])?'index':$request['a'];
    $quanxian=$db->get_results("select * from quanxian where id in($roles) and url<>'' order by id asc");
    $is_qx=0;
    if(!empty($quanxian)){

        foreach($quanxian as $j){
            $url=get_url($j->url);
            if(empty($url['a'])){
                $url['a']="index";
            }
            if($models==$url['m']&&$controllers==$url['s']&&$actions==$url['a']){
                $is_qx=1;
                $kk=[];
                $ss=$db->get_var("select url from quanxian where id in($roles) and isshow=1 and id=$j->id");
                $arr1=$db->get_results("select id,url,topid from quanxian where id in($roles) and isshow=1 and topid=$j->id");
                
                if(!empty($arr1)){
                    foreach($arr1 as $sa){
                        array_push($kk,$sa->url);
                        $yy=$db->get_results("select url from quanxian where id in($roles) and isshow=1 and topid=$sa->id");
                        
                        if(!empty($yy)){
                            foreach($yy as $pp){
                                array_push($kk,$pp->url);
                            }
                        }
                    }
                }
                $topId = (int)$j->topid;
                $arr2=$db->get_results("select url from quanxian where id in($roles) and isshow=1 and topid=$topId");
              
                if(!empty($arr2)){
                    foreach($arr2 as $sa1){
                        array_push($kk,$sa1->url);
                    }
                }
                array_push($kk,$ss);
                $arr1=array_filter($kk);
                $arr=implode('|',$arr1);
                break;
            }
        }
        
        if($controllers == 'caigou' || $controllers == 'ueditor'|| $controllers == 'upload'){
            $is_qx = 1;
        }
        
        if($is_qx==0){
            die("您当前没有权限，请联系管理员！");
        }
    }else{
        die("您当前没有任何权限，请联系管理员！");
    }
}
switch($request['m'])
{
	case 'system':
		$module_name = empty($request['s'])?'index':$request['s'];
		$controller = ABSPATH.'/erp/controllers/system/'.$module_name.'.php';
		if(is_file($controller))
		{
			require_once($controller);
			empty($request['a'])?index():(function_exists($request['a'])?$request['a']():die("无此Action #$request[a]"));
		}else{
			die('尚未安装'.$module_name.'模块。');
		}

		$view = empty($request['a'])?ABSPATH.'/erp/views/system/'.$module_name.'/index.php':ABSPATH.'/erp/views/system/'.$module_name.'/'.$request['a'].'.php';
		break;
	default:
		$module_name = empty($request['p'])?'index':get_model_type($request['p']);
		$controller = ABSPATH.'/erp/controllers/'.$module_name.'.php';
		if(is_file($controller))
		{
			require_once($controller);
			$view = empty($request['a'])?ABSPATH.'/erp/views/'.$module_name.'/index.php':ABSPATH.'/erp/views/'.$module_name.'/'.$request['a'].'.php';
			empty($request['a'])?index():(function_exists($request['a'])?$request['a']():die("无此Action #$request[a]"));
		}else{ die('尚未安装'.$module_name.'模块。');}
}
if(empty($request['s'])){
	if(!empty($request['a'])&&$request['a']=='shezhi'){
		$pageInfo['header']=ABSPATH."/erp/views/header1.php";
	}
	require_once($pageInfo['header']);
}
if(is_file($view)){require_once($view);}
function _getToplist($top=0){
    $arr=$db->get_results("select url from quanxian where id in($roles) and topid=$top");
    foreach($arr as $j){
        $arr[]=_getToplist($j->id);
    }
    return $arr;
}