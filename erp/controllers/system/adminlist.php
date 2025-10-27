<?php
function index(){}
function roles(){}
function addAdmin(){
    global $db,$request;
    if($request['type']==1){
    	$admin = array();
    	$admin['id'] = (int)$request['id'];
    	$admin['name'] = $request['name'];
    	$admin['username'] = $request['username'];
    	$admin['mendianId'] = (int)$request['mendianId'];
    	if($request['rolesid'] == 1){
    	    $admin['mendianId'] = 0;
    	}
    	$data=$db->get_row("select username from demo_user where username='".$admin['username']."' and id <> ".$admin['id']);
    	if(!empty($data)){
    	    redirect("?m=system&s=adminlist&a=index");
    	    exit();
    	}
    	if($request['pwd']!=""){
    	    $admin['pwd'] = sha1($request['pwd']);
    	}
    	if($admin['id']==0){
    	    $admin['dtTime']=date("Y-m-d H:i",time());
    	}
    	$userid=$db->insert_update('demo_user',$admin,'id');
    	$roles['rolesId'] = $request['rolesid'];
    	if((int)$request['id']==0){
    	    $roles['userId'] = $userid;
    	}else{
    	    $roles['userId'] = $request['id'];
    	}
    	$juese=$db->get_row("select * from roles_group where userId=".$roles['userId']);
    	if(!empty($juese)){
    	    $roles['id']=$juese->id;
    	}
    	$db->insert_update('roles_group',$roles,'id');
    	redirect("?m=system&s=adminlist&a=index");
    }
}
function del_admin(){
    global $db,$request;
    $id=(int)$request['id'];
    $db->query("delete from demo_user where id=$id");
    redirect("?m=system&s=adminlist&a=index");
}
function addnodes(){
    global $db,$request;
    if($request['tijiao']==1){
    	$admin = array();
    	if((int)$request['id']>0){
    	    $admin['id'] = (int)$request['id'];
    	}
    	$admin['name'] = $request['name'];
    	$admin['url'] = $request['url'];
    	$admin['topid'] = $request['topid'];
    	$admin['sort'] = $request['sort'];
    	$admin['imgurl'] = $request['imgurl'];
    	$admin['imgon'] = $request['imgon'];
    	$admin['isshow'] = $request['isshow'];
    	$admin['type'] = (int)$request['type'];
    	$db->insert_update('quanxian',$admin,'id');
    	redirect("?m=system&s=adminlist&a=addnodes");
    }
}
function addroles(){
    global $db,$request;
    if($request['type']==1){
    	$admin = array();
    	$admin['id'] = (int)$request['id'];
    	$admin['name'] = $request['name'];
    	$admin['mark'] = $request['mark'];
    	$admin['roles'] = $request['quanxian2'];
    	if($admin['id']==0){
    	    $admin['dtTime']=time();
    	}
    	$db->insert_update('roles',$admin,'id');
    	redirect("?m=system&s=adminlist&a=roles");
    }
}
function del_roles(){
    global $db,$request;
    $id=(int)$request['id'];
    $db->query("delete from roles where id=$id");
    redirect("?m=system&s=adminlist&a=roles");
}

function param(){}

function delParam()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$filedObj = $db->get_row("select * from demo_product_fields where id = $id");
	$db->query("update demo_product_fields set is_del = 1 where id in ($id) ");
	if($filedObj){
	    $filedTitle = $filedObj->field_title;
	    $db->query("ALTER TABLE `demo_product_params`  DROP COLUMN `$filedTitle`");
	}
	
	echo '{"code":1}';
}

function getParamList()
{
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	$channelId = (int)$request['channelId'];
	if(empty($request['order2'])){
		$order1 = 'ordering';
		$order2 = 'desc';
	}
	$sql = "select * from demo_product_fields where 1=1 and is_del = 0 ";

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or field_title like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
            $j->type = $j->type == 0 ? 'varchar' : 'text';
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function addParam(){
    global $db,$request;
    if($request['tijiao']==1){
    	$admin = array();
    	
    	$admin['id'] = $id = (int)$request['id'];
    	$admin['field_title'] = $fileTitle = $request['field_title'];
    	$admin['title'] = $title = $request['title'];
    	$admin['type'] = $type = (int)$request['type'];
    	$hadData = $db->get_row("select * from demo_product_fields where id <> $id and field_title = '$fileTitle' and is_del = 0 ");
    	if($hadData){
        	echo '<script>alert("您已经创建过这个数据字段了！");history.go(-1);</script>';
    		exit;
    	}
    	
    	if((int)$request['id']>0){
    	    $orignal = $db->get_row("select field_title,type from demo_product_fields where id = $id");
    	    if($orignal->field_title != $request['field_title'] || $orignal->type != $type){
    	        //todo 更新字段名称
    	        if($type == 0){
    	            $db->query("ALTER TABLE `demo_product_params` CHANGE COLUMN `$orignal->field_title` `$fileTitle`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '$title'");
    	        }else{
    	           $db->query("ALTER TABLE `demo_product_params` CHANGE COLUMN `$orignal->field_title` `$fileTitle`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL  COMMENT '$title'"); 
    	        }
    	    }
    	}else{//todo 创建字段
    	    $admin['dtTime'] = date("Y-m-d H:i:s");
    	    if($type == 0){
    	        $db->query("ALTER TABLE demo_product_params  ADD COLUMN `$fileTitle`  varchar(255) NULL DEFAULT '' COMMENT '$title' AFTER `api_desc`");
    	    }else{
    	        $db->query("ALTER TABLE demo_product_params  ADD COLUMN `$fileTitle`  text NULL COMMENT '$title' AFTER `api_desc`"); 
    	    }
    	}    
    	$admin['ordering'] = (int)$request['ordering'];

    	$db->insert_update('demo_product_fields', $admin, 'id');

    	redirect("?m=system&s=adminlist&a=param");
    }
}

function search(){}

function addProductChannel(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$id = (int)$request['id'];
	$parentId = (int)$request['parentId'];
	$id = (int)$request['id'];
	$submit = (int)$request['submit'];
	if($submit){
    	$title = $request['title'];
    	$enTitle = $request['en_title'];
    	$filedTitle = $request['field_title'];
    	$miaoshu = $request['miaoshu'];
    	$enMiaoshu = $request['en_miaoshu'];
    	$originalPic = $request['originalPic'];
    	$backimg = $request['backimg'];
    	$isHot = (int)$request['is_hot'];
    	$type = (int)$request['type'];
    	if(empty($id)){
    		$ifhas = $db->get_var("select id from demo_search_channel where comId=$comId and parentId=$parentId and title='$title'");
    		if(!empty($ifhas)){
    			echo '<script>alert("您已经创建过这个分类了！");history.go(-1);</script>';
    			exit;
    		}
    		$db->query("insert into demo_search_channel(comId,title,parentId,originalPic,backimg,is_hot,en_title,miaoshu,en_miaoshu,type,field_title) value($comId,'$title',$parentId,'$originalPic','$backimg', $isHot,'$enTitle', '$miaoshu','$enMiaoshu',$type,'$filedTitle')");
    		$id = $db->get_var("select last_insert_id();");
    		
    		$db->query("update demo_search_channel set ordering = $id where id = $id");
    	}else{
    		$db->query("update demo_search_channel set title='$title',en_title='$enTitle',miaoshu='$miaoshu', en_miaoshu='$enMiaoshu' ,parentId=$parentId,originalPic='$originalPic',backimg='$backimg',is_hot=$isHot,type=$type,field_title='$filedTitle' where id=$id and comId=$comId");
    	}

    	redirect("?m=system&s=adminlist&a=search&id=$id");
	}
}

//获取所有上级分类，用,分开
function getParentIds($id){
	global $db;
	$pid = $db->get_var("select parentId from demo_search_channel where id=$id");
	if($pid>0){
		return ','.$pid.getParentIds($pid);
	}
}

function delChannel()
{
    
}

