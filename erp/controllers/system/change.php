<?php
function zuofei(){
	global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$ids = $request['ids'];
	$isOpend = (int)$request['is_open'];
	$cards = $db->get_results("select id,status from kmd_change_card where id in($ids) and status = 0 ");
	
    $statusInfo = "关闭";
    if($isOpend == 1){
        $statusInfo = "开通";
    }
	
	if(!$cards){
	    die('{"code":0,"message":"没有未兑换卡设置为'.$statusInfo.'！"}');
	}
	$now = date("Y-m-d H:i:s");
	if($isOpend){//1-开通  0-关闭
	    $db->query("update kmd_change_card set is_open = $isOpend,openTime = '$now',closeTime=null where id in ($ids) and status = 0 ");
	}else{
	    $db->query("update kmd_change_card set is_open = $isOpend,closeTime = '$now',openTime=null where id in ($ids) and status = 0 "); 
	}
// 	if(!empty($fahuos)){
// 		foreach ($fahuos as $fahuo) {
// 			if($fahuo->status==0){
// 				$db->query("update order_fahuo$fenbiao set status=-2 where id=$fahuo->id");
// 				addJilu($fahuo->id,$fenbiao,1,'发货暂停','发货暂停');
// 			}
// 		}
// 	}
	die('{"code":1,"message":"批量'.$statusInfo.'完成！"}');
}

function importCards()
{
    global $db,$request;
    
	$return = array();
	$return['code'] = 1;
	$return['message'] = '上传成功';
	$reurn['data'] = array();
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$userId = (int)$_SESSION[TB_PREFIX.'admin_userID'];
	$username = $_SESSION[TB_PREFIX.'name'];
	$filepath = $request['filepath'];
	$filepath = ABSPATH.str_replace('../','',$filepath);
	require_once ABSPATH.'inc/excel.php';
	$pandians = excelToArray($filepath);
	$pandianJsonData = json_encode($pandians,JSON_UNESCAPED_UNICODE);
	//file_put_contents('request.txt',$pandianJsonData);
	$pandianJsonData = str_replace("'","\'",$pandianJsonData);
	$pandianJsonData = preg_replace('/((\s)*(\n)+(\s)*)/','',$pandianJsonData);
	$pandianJsonData = stripcslashes($pandianJsonData);
	$jilus = json_decode($pandianJsonData,true);
	
	$errorJilus = array();
	$success_num = 0;
	$fail_num = 0;
	$dtTime = date("Y-m-d H:i:s");
	$fahuoIds = '';

    $id = (int)$request['changeId'];
	$change = $db->get_row("select * from kmd_change where id=$id");
    if(!$change){
        @unlink($filepath);
        echo '{"code":0, "message":"未找到主卡信息"}';exit();
    }


    $errorNo = '';
	if(!empty($jilus)){
		foreach ($jilus as $jilu){
			$card = $db->get_row("select * from kmd_change_card where card_no='".$jilu[0]."' ");
			if(empty($card)){
			    $success_num++;
			    
			    $data = array();
                $data['card_no'] = $jilu[0];
                $data['changeTitle'] = $change->title;
                $data['changeImg'] = $change->story_img;
                $data['changeId'] = $change->id;
                $data['change_time'] = $change->change_time;
                $data['theme_color'] = $change->theme_color;
                
                $data['card_pass'] = $jilu[1];
                $data['dtTime'] = date('Y-m-d H:i:s');
                $data['status'] = $data['had_time'] = 0;
                $data['startTime'] = date("Y-m-d 00:00:00", strtotime($jilu[2]));
                $data['endTime'] = date("Y-m-d 23:59:59", strtotime($jilu[3]));
                //todo 产生二维码
                
                $db->insert_update('kmd_change_card', $data,'id');
			}else{
				$fail_num++;
				$errorJilus[] = $jilu;
				$errorNo .= $jilu[0]."<br>";
			}
		}
		
		if(empty($fail_num)){
			$res = '导入成功';
			$content = '实际导入兑换卡'.$success_num.'条，全部导入成功！';
		}else{
			$res = '部分导入成功';
			$content = '实际导入兑换卡'.$success_num.'条，'.$fail_num.'个失败！卡号<br>'.$errorNo.'已经存在！';
		}
		
		echo '{"code":1,"message":"上传成功,'.$content.'","content":"'.$content.'","errorJilus":"'.$errorJilus.'"}';
		@unlink($filepath);
	}
	exit;
}

function index(){}

function product(){}

function addProduct()
{
    global $db,$request;
    
    if($request['tijiao'] == 1){
        $inventorys = $request['inventoryId'];
        
        foreach ($inventorys as $inventory){
            $change = array();
            $change['id'] = (int)$request['id'];
            $change['changeId'] = (int)$request['changeId'];
            $change['inventoryId'] = (int)$inventory;
            $change['startTime'] = $request['startTime'] ? $request['startTime'] : null;
            $change['endTime'] = $request['endTime'] ? $request['endTime'] : null;
            if($change['id'] == 0){
                $change['dtTime'] = date('Y-m-d H:i:s');
            }
            
            $db->insert_update('kmd_change_product', $change, 'id');
        }
       
        
        redirect("?s=change&a=product&id=".$request['changeId']);
    }
}

function delProduct()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['ids'];
	
	$db->query("update kmd_change_product set is_del = 1 where id = $id ");
	
	echo '{"code":1}';
}

function getProductList()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'p.dtTime';
		$order2 = 'desc';
	}
	
	$changeId = (int)$request['id'];
	$sql = "select i.image,p.*,i.sn,i.title,i.key_vals from kmd_change_product p inner join demo_product_inventory i on i.id = p.inventoryId where p.changeId = $changeId and p.is_del = 0 ";
	
    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (i.title like '%$keyword%' or i.sn like '%$keyword%') ";
    }
	$countsql = str_replace('i.image,p.*,i.sn,i.title,i.key_vals','count(p.id)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());

	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->logo = '<img src="'.ispic($j->image).'?x-oss-process=image/resize,w_54" width="50" height="50">';
		    $j->subtitle = '<span onmouseover="tips(this,\''.stripcslashes($j->subtitle).'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($j->subtitle),100,true).'</span>';
            $j->change_num = 0;
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function add()
{
    global $db,$request;
    
    if($request['tijiao'] == 1){
        $change = array();
        $change['id'] = (int)$request['id'];
        $change['title'] = $request['title'];
        $change['ordering'] = (int)$request['ordering'];
        $change['channelId'] = (int)$request['channelId'];
        $change['beizhu'] = $request['beizhu'] ? $request['beizhu'] : '';
        $change['change_time'] = $request['change_time'] ? $request['change_time'] : 0;
        $change['story_img'] = $request['logo'];
        $change['theme_color'] = $request['theme_color'] ? $request['theme_color'] : '';
        if($change['id'] == 0){
            $change['dtTime'] = date('Y-m-d H:i:s');
        }

        $db->insert_update('kmd_change', $change, 'id');
        
        redirect("?s=change");
    }
}

function del()
{
    global $db,$request;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);
	$id = $request['ids'];
	
	$db->query("update kmd_change set is_del = 1 where id in ($id) ");
	
	echo '{"code":1}';
}

function getList()
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
	$sql = "select * from kmd_change where 1=1 and is_del = 0 ";
	
	if($channelId > 0){
	    $ziIds = getZiChangeIds($channelId);
	    if(!$ziIds)  $ziIds = "";
	    $channelIds = $channelId.$ziIds;
	    $sql .= " and channelId in ($channelIds) ";
	}

    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
      
        $sql .= " and (title like '%$keyword%' or content like '%$keyword%') ";
    }

	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());
    $now = date("Y-m-d H:i:s");
    $nextWeek = date("Y-m-d H:i:s", strtotime("+1 week"));
    
	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->logo = '<img src="'.ispic($j->story_img).'?x-oss-process=image/resize,w_54" width="50" height="50">';
		    $j->subtitle = '<span onmouseover="tips(this,\''.stripcslashes($j->subtitle).'\',1);" onmouseout="hideTips()">'.$icon.sys_substr(strip_tags($j->subtitle),100,true).'</span>';
		    $totalNum = $db->get_var("select count(p.id) from  kmd_change_product p inner join demo_product_inventory i on i.id = p.inventoryId where p.changeId = $j->id and p.is_del = 0  ");
		    $todayNum = $db->get_var("select count(p.id) from  kmd_change_product p inner join demo_product_inventory i on i.id = p.inventoryId where p.changeId = $j->id and p.is_del = 0 and p.startTime < '$now' and p.endTime > '$now' ");
	        $weekNum = $db->get_var("select count(p.id) from  kmd_change_product p inner join demo_product_inventory i on i.id = p.inventoryId where p.changeId = $j->id and p.is_del = 0 and p.startTime < '$nextWeek' and p.endTime > '$nextWeek' ");
	        $change_num = intval($todayNum)."/".intval($totalNum);
	        $change_num1 = intval($weekNum)."/".intval($totalNum);
	        
            if($totalNum < 10){
	            $totalNum = '<span style="color:red;">'.$totalNum.'</span>';
	        }else{
	            $totalNum = '<span style="color:green;">'.$totalNum.'</span>';  
	        }
	        
	        if($todayNum < 10){
	            $todayNum = '<span style="color:red;">'.$todayNum.'</span>';
	        }else{
	            $todayNum = '<span style="color:green;">'.$todayNum.'</span>';  
	        }
	        
	        if($weekNum < 10){
	            $weekNum = '<span style="color:red;">'.$weekNum.'</span>';
	        }else{
	            $weekNum = '<span style="color:green;">'.$weekNum.'</span>';  
	        }
	        
	        
	        $j->todayNum = $todayNum;
	        $j->weekNum = $weekNum;
	        $j->totalNum = $totalNum;
	        
	        if($j->theme_color){
	            $j->title = '<span style="color:'.$j->theme_color.'">'.$j->title.'</span>';
	        }
	        
            $j->change_num = $change_num;
            $j->change_num1 = $change_num1;
            
            $j->channelTitle = $db->get_var("select title from demo_change_channel where id = $j->channelId");
            
            $j->title = '<a href="?s=change&a=card&id='.$j->id.'">'.$j->title.'</a>';
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function card(){}

function getCardList()
{
    global $db,$request;
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$fenbiao = getFenbiao($comId,20);

	$page = (int)$request['page'];
	$pageNum = (int)$request["limit"];
	$order1 = empty($request['order1'])?'id':$request['order1'];
	$order2 = empty($request['order2'])?'desc':$request['order2'];
	if(empty($request['order2'])){
		$order1 = 'id';
		$order2 = 'desc';
	}
	
	$changeId = $request['changeId'];
	$sql = "select * from kmd_change_card where 1=1  ";
	
	if($changeId > 0){
	    $sql .= " and changeId = $changeId ";
	}
	
	$type = (int)$request['type'];
    if(!empty($request['keyword'])){
        $keyword = $request['keyword'];
        
        $userIds = $db->get_var("select group_concat(id) from users where nickname like '%$keyword%' or phone like '%$keyword%' ");
        if(!$userIds) $userIds = 0;
      
        $sql .= " and (changeTitle like '%$keyword%' or card_no like '%$keyword%' or userId in ($userIds) ) ";
    }
    
    if($type > 0 && $type == 1){
        $sql .= " and status = 0 ";
    }elseif ($type > 0 && $type == 2) {
        $sql .= " and status = 1 ";
    }
    
	$countsql = str_replace('*','count(*)',$sql);
	$count = $db->get_var($countsql);
	$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum; 
	$jilus = $db->get_results($sql);
	$dataJson = array("code"=>0,"msg"=>'成功',"count"=>$count,"data"=>array());

	if(!empty($jilus)){
		foreach ($jilus as $i=>$j) {
		    $j->view = '<a href="javascript:" onclick="order_show_card('.$i.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';
            $j->startTime = date('Y-m-d H:i', strtotime($j->startTime));
            $j->endTime = date('Y-m-d H:i', strtotime($j->endTime));
            
            if($j->openTime){
                $j->openTime = date('Y-m-d H:i', strtotime($j->openTime));    
            }
            
            switch ($j->status) {//状态：0-未兑换  1-已兑换 -1-已失效
                case 0:
                    $status_info = '<span style="color:red;">未兑换</span>';
                    break;
                case 1:
                    $status_info = '<span style="color:green;">已兑换</span>';
                    break;
                case -1:
                    $status_info = '<span style="color:gray;">已失效</span>';
                    break;
            }
            $j->status_info = $status_info;
            
            switch ($j->is_open) {//状态：0-未兑换  1-已兑换 -1-已失效
                case 0:
                    $open_info = '<span style="color:red;">未开通</span>';
                    break;
                case 1:
                    $open_info = '<span style="color:green;">已开通</span>';
                    break;
            }
            $j->open_info = $open_info;
            
            $userInfo = '';
            if($j->userId > 0){
                $user = $db->get_row("select * from users where id = $j->userId");
                $userInfo = $user->nickname."(".$user->phone.")";
            }
            
            $j->userInfo = $userInfo;
            
			$dataJson['data'][] = $j;
		}
	}
	
	echo json_encode($dataJson,JSON_UNESCAPED_UNICODE);
	exit;
}

function order_info_index()
{
    global $db,$request,$arr;
    
	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
	$fenbiao = getFenbiao($comId,20);
	$id = (int)$request['id'];
	$order = $db->get_row("select * from kmd_change_card where id=$id ");
	if(empty($order))die("卡信息不存在！！");
	$price_json = json_decode($order->price_json,true);
	
	if(!$order->code_url){
        $url = 'https://mkd.zhishangez.cn/service.php?action=card_showCode&id='.$id;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_HEADER,0); //
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
        $response = curl_exec($curl);  
        curl_close($curl);
	}
	
	$pay_json = array();
	$fahuo_json = array(
	    'kuaidi_type' => '快递配送'
	);
	$shuohuo_json = array();
	if(!empty($order->pay_json))$pay_json=json_decode($order->pay_json,true);
	if(!empty($order->fahuo_json))$fahuo_json=json_decode($order->fahuo_json,true);
	if(!empty($order->shouhuo_json))$shuohuo_json = json_decode($order->shouhuo_json,true);
	$user = $db->get_row("select nickname,username,level,phone from users where id=$order->userId");
	if($user->level>0)$user_level = $db->get_var("select title from user_level where id=$user->level");
	$details = $db->get_results("select * from kmd_change_log where cardId=$id order by id asc");
	//拼接字符串
	$str = '<div class="ddxx_jibenxinxi">';

	$str.='<div class="ddxx_jibenxinxi_2">
	    	<div class="ddxx_jibenxinxi_2_01" id="order_info_price">
	        	<div class="ddxx_jibenxinxi_2_01_up">
	            	卡信息
	            </div>
	        	<div class="ddxx_jibenxinxi_2_01_down">
	            	<ul>
            	        <li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	标题：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>'.$order->changeTitle.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>
	            		<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	卡号：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>'.$order->card_no.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>
	                    <li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	密码：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>'.$order->card_pass.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>
	                    <li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	生效时间：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>'.$order->startTime.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>
	                    <li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	结束时间：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">
	                        	<b>'.$order->endTime.'</b>
	                        </div>
	                    	<div class="clearBoth"></div>
	                    </li>
	                    
	                    ';
	                    if(!empty($price_json['goods'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品总额：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['goods']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yunfei'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	运费：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	+￥'.$price_json['yunfei']['price'].'
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['yhq'])){
		                	$yhq_title = $db->get_var("select title from user_yhq$fenbiao where id=".$price_json['yhq']['desc']);
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	优惠券：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['yhq']['price'].'('.$yhq_title.')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	商品促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao']['price'].'('.$price_json['cuxiao']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['cuxiao_order'])){
	                    	$str.='<li>
		                    	<div class="ddxx_jibenxinxi_2_01_down_left">
		                        	订单促销：
		                        </div>
		                    	<div class="ddxx_jibenxinxi_2_01_down_right">
		                        	-￥'.$price_json['cuxiao_order']['price'].'('.$price_json['cuxiao_order']['desc'].')
		                        </div>
		                    	<div class="clearBoth"></div>
		                    </li>';
		                }
		                if(!empty($price_json['admin'])){
		                	if($price_json['admin']['price']>0){
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动优惠：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">-￥'.$price_json['admin']['price'].'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}else{
		                		$str.='<li>
		                		<div class="ddxx_jibenxinxi_2_01_down_left">手动提价：</div>
		                		<div class="ddxx_jibenxinxi_2_01_down_right">+￥'.abs($price_json['admin']['price']).'</div>
		                		<div class="clearBoth"></div>
		                		</li>';
		                	}
		                }
		                if(!empty($pay_json)){
	                    $str.='<li>
	                    	<div class="ddxx_jibenxinxi_2_01_down_left">
	                        	 支付：
	                        </div>
	                    	<div class="ddxx_jibenxinxi_2_01_down_right">';
	                    		if(!empty($pay_json['jifen'])){
	                    			$str.='积分抵现 <b>￥'.$pay_json['jifen']['price'].'</b>('.$pay_json['jifen']['desc'].'积分)<br>';
	                    		}
	                    		if(!empty($pay_json['yue'])){
	                    		    $yue = 0;
	                    		    foreach ($pay_json['yue'] as $pv){
	                    		        $yue = bcadd($yue, $pv['price'], 2);
	                    		    }
	                    			$str.='余额支付 <b>￥'.$yue.'</b><br>';
	                    		}
	                    		if(!empty($pay_json['weixin'])){
	                    			$str.='微信支付 <b>￥'.$pay_json['weixin']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['applet'])){
	                    			$str.='小程序支付 <b>￥'.$pay_json['applet']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['alipay'])){
	                    			$str.='支付宝支付 <b>￥'.$pay_json['alipay']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['cash'])){
	                    			$str.='现金支付 <b>￥'.$pay_json['cash']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['paypal'])){
	                    			$str.='银联支付 <b>￥'.$pay_json['paypal']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka'])){
	                    			$str.='抵扣金支付 <b>￥'.$pay_json['lipinka']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['lipinka1'])){
	                    			$str.='礼品卡支付 <b>￥'.$pay_json['lipinka1']['price'].'</b><br>';
	                    		}
	                    		if(!empty($pay_json['other'])){
	                    			$str.='其他支付 <b>￥'.$pay_json['other']['price'].'</b>('.$pay_json['ohter']['desc'].')<br>';
	                    		}
	                    		if(!empty($pay_json['yibao'])){
	                    			$pay_way = $pay_json['yibao']['pay_way']=='NCPAY'?'银行卡支付':'易宝微信支付';
                    				$str.=$pay_way.' ：<b>￥'.$pay_json['yibao']['price'].'</b>';
	                    		}
	                        $str.='</div>
	                    	<div class="clearBoth"></div>
	                    </li>';
	                }
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_02" id="order_info_fapiao">
	        	<div class="ddxx_jibenxinxi_2_02_up">
	            	<div class="ddxx_jibenxinxi_2_02_up_left">
	                	二维码信息
	                </div>
	            	<div class="ddxx_jibenxinxi_2_02_up_right">
	                	
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_02_down">
	            	<ul>';
	            	if($order->ifkaipiao>0){
	            		$fapiao_json = json_decode($order->fapiao_json,true);
	            		foreach ($fapiao_json as $key => $val){
	            			$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">'.$key.'：</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right" style="word-break:break-all;">'.$val.'</div>
                            	<div class="clearBoth"></div>
                            </li>';
	            		}
	            	}
	            	if($fapiao_json['发票类型']=='电子普通发票'){
	            		$fapiao_type = 1;
	            		$fapiao_cont = empty($fapiao_json['电子发票地址'])?'http://':$fapiao_json['电子发票地址'];
	            	}else{
	            		$fapiao_type = 2;
	            		$fapiao_cont = empty($fapiao_json['发票快递'])?'':$fapiao_json['发票快递'];
	            	}
	            	
	            	if($order->status==4 && $order->ifkaipiao>0 && $order->kaipiao_status==1){
	            		$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">&nbsp;</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right"><a href="javascript:" style="color:red" onclick="order_kaipiao('.$id.','.$fapiao_type.',\''.$fapiao_cont.'\');">开票</a></div>
                            	<div class="clearBoth"></div>
                            </li>';
	            	}else if($order->status==4 && $order->ifkaipiao>0 && $order->kaipiao_status==2){
	            		$str.='<li>
                            	<div class="ddxx_jibenxinxi_2_02_down_left">&nbsp;</div>
                            	<div class="ddxx_jibenxinxi_2_02_down_right"><a href="javascript:" onclick="order_kaipiao('.$id.','.$fapiao_type.',\''.$fapiao_cont.'\');" style="color:red">修改发票信息</a></div>
                            	<div class="clearBoth"></div>
                            </li>';
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_03">	
	        	<div class="ddxx_jibenxinxi_2_03_up">	
	            	用户信息
	            </div>
	        	<div class="ddxx_jibenxinxi_2_03_down">
	         
	                	<ul>';
	                	$str.='<li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员名称：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->nickname.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">会员级别：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user_level.'</div>
	                            <div class="clearBoth"></div>
	                        </li>
	                        <li>
	                            <div class="ddxx_jibenxinxi_2_02_down_left">手机号：</div>
	                            <div class="ddxx_jibenxinxi_2_02_down_right">'.$user->phone.'</div>
	                            <div class="clearBoth"></div>
	                        </li>';
	                    $str.='</ul>
	               
	            </div>
	        </div>
	    	<div class="ddxx_jibenxinxi_2_04" id="order_info_shouhuo">
	        	<div class="ddxx_jibenxinxi_2_04_up">
	            	<div class="ddxx_jibenxinxi_2_04_up_left">
	                	收货人信息 
	                </div>
	            	<div class="ddxx_jibenxinxi_2_04_up_right">
	               
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	        	<div class="ddxx_jibenxinxi_2_04_down">
	            	<ul>';
	            	foreach ($shuohuo_json as $key => $val){
	            		if($key == 'name'){
	            		    $key = '姓名';
	            		}elseif($key == 'mobile'){
	            		    $key = '联系电话';
	            		}else{
	            		    $key = '详细地址';
	            		}
	            		$str.='<li>
		            		<div class="ddxx_jibenxinxi_2_01_down_left">'.$key.'：</div>
		            		<div class="ddxx_jibenxinxi_2_01_down_right" '.($key=='收件人'?'id="order_shoujianren" data-val="'.$username.'" data-hide="'.$val.'"':($key='手机号'?'id="order_shoujihao" data-val="'.$phone.'" data-hide="'.$val.'"':'')).'>'.$val.'</div>
		            		<div class="clearBoth"></div>
	            		</li>';
	            	}
	            	$str.='</ul>
	            </div>
	        </div>
	    	<div class="clearBoth"></div>
	    </div>
		<div class="ddxx_jibenxinxi_4">
	    	<div class="ddxx_jibenxinxi_4_up">
	        	兑换明细：
	        </div>
	    	<div class="ddxx_jibenxinxi_4_down">
	        	<table width="100%" border="0" cellpadding="0" cellspacing="0">	
	            	<tr height="34">
	                	<td align="center" width="34" valign="middle" class="ddxx_jibenxinxi_4_down_bj"></td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">商品名称</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">规格</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">数量</td>
	                    <td align="center" valign="middle" class="ddxx_jibenxinxi_4_down_bj">单位</td>
	                    <td align="center" width="400" valign="middle" class="ddxx_jibenxinxi_4_down_bj">收货地址</td>
	                </tr>';
	                foreach ($details as $i=>$jilu){
	                	$pdtInfo = json_decode($jilu->pdtInfo);
				// 		$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$jilu->inventoryId and storeId=$order->storeId");
						$kucun = $kc->kucun-$kc->yugouNum;
						if($order->fahuoId==0 && $order->status!=-1){
							$kucun += $jilu->num;
						}
						
						$jilu->unit = $db->get_var("select dinghuo_units from demo_product where id = $pdtInfo->productId");
						$shouhuoJson = json_decode($jilu->shouhuo_json, true);
						$jilu->address = $shouhuoJson['name']."(".$shouhuoJson['mobile'].")"." ".$shouhuoJson['address'];
						
	                	$str.='<tr height="34">
	                	<td align="center" valign="middle">'.($i+1).'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->title.'</td>
	                    <td align="center" valign="middle">'.$pdtInfo->key_vals.'</td>
	                    <td align="center" valign="middle">'.getXiaoshu($jilu->num,$product_set->number_num).'</td>
	                    <td align="center" valign="middle">'.$jilu->unit.'</td>
	                    <td align="center" valign="middle">'.$jilu->address.'</td>
	                </tr>';
	                }
	                $str.='</table>
	        </div>
	    </div>
		<div class="ddxx_jibenxinxi_5" style="display:none;">
	    	<div class="ddxx_jibenxinxi_5_up">	
	        	备注信息：
	        </div>
	    	<div class="ddxx_jibenxinxi_5_down">
	        	<div class="ddxx_jibenxinxi_5_down_01">
	            	会员备注：'.(empty($order->remark)?'无':$order->remark).'
	            </div>
	        	<div class="ddxx_jibenxinxi_5_down_02">
	            	<div class="ddxx_jibenxinxi_5_down_02_left">
	                	商家备注：
	                </div>
	            	<div class="ddxx_jibenxinxi_5_down_02_right">';
	            	if(!empty($order->beizhu_json)){
	            		$beizhus = json_decode($order->beizhu_json,true);
	            		foreach ($beizhus as $b){
	            			$str.='<div style="padding-bottom:10px;">'.$b['content'].'【'.$b['name'].'&nbsp;&nbsp;'.$b['time'].'】</div>';
	            		}
	            	}
	                $str.='<textarea id="add_order_beizhu_content"></textarea>
	                </div>
	            	<div class="clearBoth"></div>
	            </div>
	            <div class="ddxx_jibenxinxi_5_down_03">
	            	<a href="javascript:" onclick="add_order_beizhu('.$id.');">新增备注</a>
	            </div>
	        </div>
	    </div>
	</div>';
	echo $str;
	exit;
}

function batchAdd()
{
    global $db,$request;
    
    $comId = (int)$_SESSION[TB_PREFIX.'comId'];
    if($request['tijiao']==1){
        $changeId = (int)$request['changeId'];
        $num = (int)$request['num'];
        if(!$changeId){
            echo '<script>alert("请选择生成兑换卡模板");location.href="?m=system&s=change&a=card&id='.$changeId.'";</script>';
            exit;
        }
        
        if(!$num){
            echo '<script>alert("请输入生成兑换卡数量");location.href="?m=system&s=change&a=batchAdd&changeId='.$changeId.'";</script>';
            exit;
        }
        
        $last_id = $db->get_var("select id from kmd_change_card where id >0 order by id desc limit 1");
        $str = 'JX';
        $s = 889000+$last_id;
        $e = 889000+$last_id+$num;
        
        $change = $db->get_row("select * from kmd_change where id = $changeId");
        
        $startTime = $request['startTime'] ? $request['startTime'] : date('Y-m-d H:i:s');
        $endTime = $request['endTime'] ? $request['endTime'] : date('Y-m-d H:i:s', strtotime("+1 month"));
        for ($i = $s ;$i < $e; $i++){
            $data = array();
            
            $data['card_no'] = $str.$i;
            $data['changeTitle'] = $change->title;
            $data['changeImg'] = $change->story_img;
            $data['changeId'] = $changeId;
            $data['change_time'] = $change->change_time;
            $data['theme_color'] = $change->theme_color;
            
            $data['card_pass'] = substr(md5($data['card_no']),10,6);
            $data['dtTime'] = date('Y-m-d H:i:s');
            $data['status'] = $data['had_time'] = 0;
            $data['startTime'] = $startTime;
            $data['endTime'] = $endTime;
            //todo 产生二维码
            
            $db->insert_update('kmd_change_card', $data,'id');
        }
        
        redirect("?m=system&s=change&a=card&id=$changeId");
        exit;
    }
}

function batchExport(){}
