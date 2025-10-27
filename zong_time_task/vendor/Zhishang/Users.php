<?php
namespace Zhishang;
class Users{
	public function test(){
		global $db;
		$params = array("id"=>1);
		send_asy_task('users_index',$params);
		//return $db->get_var("select username from demo_user order by rand() limit 1");
		return '{"code":"adfdsf"}';
	}
	public function index($params){
		return '{"code":"function'.$params['id'].'"}';
	}
}