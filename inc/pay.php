<?php
header('Content-Type: text/html; charset=utf-8');
$dirName=dirname(__FILE__);
define('THISISADMINI',true);
$shlConfig='../config/dt-config.php';
require($shlConfig);
require_once('../inc/class.database.php');
require_once('../inc/function.php');
$request  = $_REQUEST;
if($request['a']=='getAreas'){
  $id = $request['id'];
  $areas = $db->get_results("select id,title from demo_area where parentId=$id order by id asc");
  $str = "";
  if(!empty($areas)){
    foreach($areas as $area){
	  $str .= "<option value=".$area->id.">".$area->title."</option>";
	}
  }
  echo $str;
}
?>
