<?php
namespace Zhishang;
class Card{
    
    public function orderInfo()
    {
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $fenbiao = getFenbiao($comId,20);
        $pdt = $db->get_row("select * from kmd_change_log where id=$id and userId = $userId ");
        if(empty($pdt)){
            return '{"code":0,"message":"订单不存在！"}';
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $data = array();
        $data['id'] = $pdt->id;
        $data['order_id'] = $pdt->orderId;
        $data['fahuo_id'] = $pdt->id;
        $data['remark'] = $pdt->remark;
        $data['card_no'] = $db->get_var("select card_no from kmd_change_card where id = $pdt->cardId");
        $data['theme_color'] = $db->get_var("select theme_color from kmd_change_card where id = $pdt->cardId");
        
        switch ($pdt->status) {
            case 0:
                $data['status_info'] = '待发货';
                break;
            case 1:
                $data['status_info'] = '待收货';
                break;    
            case 2:
                $data['status_info'] = '待发货';
                break;
            case 3:
                $data['status_info'] = '待收货';
                break;
            case 4:
                $data['status_info'] = '已完成';
                break;
            case -3:
                $data['status_info'] = '退换货';
                break;
            case -5:
                $pay_end = strtotime($pdt->pay_endtime);
                if($pay_end>$now){
                    $data['status_info'] = '待支付';
                    $data['if_cancel'] = 1;
                    $data['if_pay'] = 1;
                }else{
                    $data['status_info'] = '无效';
                    $data['if_del'] = 1;
                }
                break;
            case -1:
                $data['status_info'] = '已取消';
                break;
        }
        $product_json = json_decode($pdt->pdtInfo);
        $shouhuo_json = json_decode($pdt->shouhuo_json);
        
        $data['shouhuo_address'] = $shouhuo_json;
        $data['products'] = $product_json;
        $data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
        $data['shouhuoTime'] = $pdt->shouhuoTime;
        $data['status'] = $pdt->status;

        $return['data'][] = $data;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function orderList(){
        global $db,$request,$comId;
        $scene = (int)$request['scene'];
        $type = $request['type'];
        $userId = (int)$request['user_id'];
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select * from kmd_change_log where 1=1 and userId=$userId  ";

        if(!empty($scene)){
            switch($scene){//状态：0-全部  2-待发货  3-待收货 4-已完成
                case 1:
                    $sql.=" and status=-5 and pay_endtime>'$now'";
                    break;
                case 2:
                    $sql.=" and status=0";
                    break;
                case 3:
                    $sql.=" and status=1";
                    break;
                case 4:
                    $sql.=" and status=4";
                    break;
                case -1:
                    $sql.=" and status=-1";
                    break;
            }
        }
        if(!empty($keyword)){
            $sql.=" and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')";
        }
 
        $count = $db->get_var(str_replace('*','count(*)',$sql));


        $sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = array();
                $data['id'] = $pdt->id;
                $data['order_id'] = $pdt->orderId;
                $data['fahuo_id'] = $pdt->id;
                $data['remark'] = $pdt->remark;
                $data['card_no'] = $db->get_var("select card_no from kmd_change_card where id = $pdt->cardId");
                $data['theme_color'] = $db->get_var("select theme_color from kmd_change_card where id = $pdt->cardId");
                
                switch ($pdt->status) {
                    case 0:
                        $data['status_info'] = '待发货';
                        break;
                    case 1:
                        $data['status_info'] = '待收货';
                        break;    
                    case 2:
                        $data['status_info'] = '待发货';
                        break;
                    case 3:
                        $data['status_info'] = '待收货';
                        break;
                    case 4:
                        $data['status_info'] = '已完成';
                        break;
                    case -3:
                        $data['status_info'] = '退换货';
                        break;
                    case -5:
                        $pay_end = strtotime($pdt->pay_endtime);
                        if($pay_end>$now){
                            $data['status_info'] = '待支付';
                            $data['if_cancel'] = 1;
                            $data['if_pay'] = 1;
                        }else{
                            $data['status_info'] = '无效';
                            $data['if_del'] = 1;
                        }
                        break;
                    case -1:
                        $data['status_info'] = '已取消';
                        break;
                }
                $product_json = json_decode($pdt->pdtInfo);
                $shouhuo_json = json_decode($pdt->shouhuo_json);
                
                $data['shouhuo_address'] = $shouhuo_json;
                $data['products'] = $product_json;
                $data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
                $data['shouhuoTime'] = $pdt->shouhuoTime;
                $data['status'] = $pdt->status;
   
                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function orderReceipt()
    {
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $info = $db->get_row("select * from kmd_change_log where id = $id and userId = $userId ");
        if(!$info){
            return '{"code":0,"message":"未找到对应的兑换记录！"}';
        }
        
        if($info->status != 1){
             return '{"code":0,"message":"兑换订单不是待收货状态！"}';
        }
        
        $update = array(
            'id' => $info->id,
            'status' => 4,//5待评价
            'shouhuoTime' => date('Y-m-d H:i:s')
        );
        
        $db->insert_update('kmd_change_log', $update, 'id');
        
        return '{"code":1,"message":"确认收货完成！"}';
    }
    
    public function lists(){
        global $db,$request,$comId;
        
        $scene = (int)$request['scene'];

        $userId = (int)$request['user_id'];
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);

        $sql="select * from kmd_change_card where status = $scene and userId =  $userId ";

        $count = $db->get_var(str_replace('*','count(*)',$sql));
        
        $sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = array();
                $data['id'] = $pdt->id;
                $data['changeTitle'] = $pdt->changeTitle;
                $data['changeImg'] = $pdt->changeImg;
                $data['card_no'] = $pdt->card_no;
                $data['theme_color'] = $pdt->theme_color;
                $data['endTime'] = date('Y.m.d', strtotime($pdt->endTime));
                $data['change_time'] = $pdt->change_time;
                $data['had_time'] = $pdt->had_time;
                $data['status'] = $pdt->status;
                
                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function checkUser()
    {
        global $db,$request,$comId;

        $userId = (int)$request['user_id'];
        $phone = $request['phone'];
        $user = $db->get_row("select id, nickname, image from users where phone = '$phone' ");
        if(!$user){
            return '{"code":0,"message":"无效的手机号，未找到对应用户"}';
        }
        
        if($user->id == $userId){
            return '{"code":0,"message":"不能转赠给自己"}';
        }
        
        return json_encode(array("code"=>1,"message"=>"成功","data"=>$user),JSON_UNESCAPED_UNICODE);
    }
    
    public function give()
    {
        global $db,$request,$comId;

        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $toUid = (int)$request['to_uid'];
        $card = $db->get_row("select * from kmd_change_card where id = $id and userId = $userId ");
        if(!$card){
            return '{"code":0,"message":"无效的Id，未找到对应卡券信息"}';
        }
        
        if($card->status != 1){
            return '{"code":0,"message":"当前卡券状态不是未使用，不支持转赠"}';
        }
        
        if($card->is_give == 1){
            return '{"code":0,"message":"当前卡券已经被转赠过，不支持再次发起转赠"}';
        }
        
        if(!$toUid || $toUid == $userId){
            return '{"code":0,"message":"接收人不能为空或者接收人不能为自己"}';
        }
        $cardData = array(
            'id' => $card->id,
            'userId' => $toUid,
            'give_at' => time(),
            'give_user' => $userId,
            'is_give' => 1
        );
  
        $db->insert_update('cards', $cardData, 'id');
        
        return json_encode(array("code"=>1,"message"=>"转赠成功" ),JSON_UNESCAPED_UNICODE);
    }
    
    public function bind()
    {
        global $db,$request,$comId;

        $userId = (int)$request['user_id'];
        $code = $request['code'];
        $pass = $request['pass'];
        $card = $db->get_row("select * from kmd_change_card where card_no = '$code'");
        if(!$card){
            return '{"code":0,"message":"无效的code，未找到对应卡券信息"}';
        }
        
        if($card->is_open != 1){
            return '{"code":0,"message":"当前卡券处于未开通状态"}';
        }
        
        if($card->userId != 0){
            return '{"code":0,"message":"当前卡券已经有归属用户'.$card->userId.'"}';
        }
        
        if(!$pass || $pass != $card->card_pass){
            return '{"code":0,"message":"密码不正确"}';
        }
       
        $cardData = array(
            'id' => $card->id,
            'userId' => $userId
        );
  
        $db->insert_update('kmd_change_card', $cardData, 'id');
        
        return json_encode(array("code"=>1,"message"=>"绑定成功" ),JSON_UNESCAPED_UNICODE);
    }
    
    // 使用卡券
    public function change(){
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $address_id = (int)$request['address_id'];
        $content = $request['change_info'];
        $remark = $remark['remark'] ? $request['remark'] : '';
        $cards = $db->get_row("select * from kmd_change_card where id=$id AND userId= $userId");
        if(!$cards){
            return '{"code":0,"message":"兑换卡不存在！"}';
        }
        
        if($cards->status != 0){
            return '{"code":0,"message":"兑换卡不是待使用状态！"}';
        }
        
        $now = date('Y-m-d H:i:s');
        if($cards->startTime > $now){
            return '{"code":0,"message":"兑换卡未到生效时间！"}';
        }
        
        if($cards->endTime < $now){
            return '{"code":0,"message":"兑换卡已经过了失效时间！"}';
        }
        
        $content = str_replace('\\"','"',$content);
        $content = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$content));
        $content = json_decode($content, true);
        
        if(!$content){
            return '{"code":0,"message":"未找到兑换商品！"}';
        }
        $checkTime = $cards->change_time - $cards->had_time;
        $totalTime = 0;
        foreach ($content as $info){
            $totalTime = bcadd($totalTime, $info['num'], 0);
        }
        
        if($totalTime > $checkTime){
             return '{"code":0,"message":"您可以兑换商品数量为'.$checkTime.'个，现在提交兑换商品数量为'.$totalTime.'个！"}';
        }
        
        $data = array();
        $data['id'] = $id;
        $address = $db->get_row("select id, name, phone,  areaName, address from user_address where id = $address_id ");
        if(!$address){
            return '{"code":0,"message":"无效的地址id，没有找到对应的地址详情！"}';
        }
        
        $shouhuo = array();
        $shouhuo['name'] = $address->name;
        $shouhuo['mobile'] = $address->phone;
        $shouhuo['address'] = $address->areaName." ".$address->address;
        
        foreach ($content as $info){
            $inventory = $db->get_row("select id inventoryId, productId, title, key_vals, image from demo_product_inventory where id = ".$info['inventoryId']);
            if(!$inventory){
                return '{"code":0,"message":"兑换商品数据异常！"}';
            }
            $pdtInfo = array(
                'id' => $inventory->inventoryId,
                'productId' => $inventory->productId,
                'title' => $inventory->title,
                'key_vals' => $inventory->key_vals,
                'image' => ispic($inventory->image),
                'num' => $info['num']
            );
            
            $log = array(
                'userId' => $userId,
                'cardId' => $cards->id,
                'orderId' => "Ex".date('YmdHis').rand(1000,9999),
                'changeId' => $cards->changeId,
                'inventoryId' => $inventory->inventoryId,
                'num' => $info['num'],
                'mendianId' => $db->get_var("select mendianId from demo_product where id = $inventory->productId"),
                'pdtInfo' => json_encode($pdtInfo, JSON_UNESCAPED_UNICODE),
                'status' => 0,
                'remark' => $remark,
                'shouhuo_json' => json_encode($shouhuo, JSON_UNESCAPED_UNICODE),
                'dtTime' => date('Y-m-d H:i:s')
            );
            
            $db->insert_update("kmd_change_log", $log, "id");
        }
        
        $data['shouhuo_json'] = json_encode($shouhuo, JSON_UNESCAPED_UNICODE);
        $data['had_time'] = bcadd($cards->had_time, $totalTime, 0);
        if($data['had_time'] >= $cards->change_time){
            $data['status'] = 1;
        }

        $db->insert_update('kmd_change_card', $data, 'id');
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '兑换记录成功';
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function shouhuo()
    {   
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $info = $db->get_row("select * from kmd_change_card where id = $id and userId = $userId ");
        if(!$info){
            return '{"code":0,"message":"未找到对应的兑换记录！"}';
        }
        
        if($info->status != 4){
             return '{"code":0,"message":"订单当前状态不是待收货！"}';
        }
        $update = array(
            'id' => $info->id,
            'status' => 5,//5待评价
            'shouhuotime' => date('Y-m-d H:i:s')
        );
        
        $db->insert_update('cards', $update, 'id');
        
        return '{"code":1,"message":"确认收货完成！"}';
    }
    
    public function info()
    {
        global $db,$request,$comId;

        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $info = $db->get_row("select * from kmd_change_card where id = $id and userId = $userId ");
        if(!$info){
            return '{"code":0,"message":"未找到对应的兑换记录！"}';
        }
        
        $info->endTime = date("Y-m-d", strtotime($info->endTime));
        $current = date('Y-m-d H:i:s');
        $inventoryIds = $db->get_var("select group_concat(distinct(inventoryId)) from kmd_change_product where changeId = $info->changeId and is_del = 0 and startTime < '$current' and endTime > '$current' ");
        
        if(!$inventoryIds) $inventoryIds = 0;
        $inventorys = $db->get_results("select id inventoryId, productId, title, key_vals, image from demo_product_inventory where id in ($inventoryIds) ");
        $products = $hadChange = [];
        if($inventorys){
            foreach ($inventorys as $val){
                $temp = array();
                $temp['id'] = $val->inventoryId;
                $temp['prodcutId'] = $val->productId;
                $temp['title'] = $val->title;
                $temp['key_vals'] = $val->key_vals;
                $temp['image'] = ispic($val->image);
                $kucun =\Zhishang\Product::get_product_kucun($val->inventoryId, 0);
                $temp['kucun'] = $kucun>0 ? (int)$kucun : 0;
                
                $products[] = $temp;
            }
        }
        
        $logs = $db->get_results("select * from kmd_change_log where cardId = $id order by dtTime desc ");
        if($logs){
            foreach ($logs as $log){
                $pdtInfo = json_decode($log->pdtInfo, JSON_UNESCAPED_UNICODE);
                $pdtInfo['address'] = json_decode($log->shouhuo_json, JSON_UNESCAPED_UNICODE);
                $pdtInfo['remark'] = $log->remark;
                $hadChange[] = $pdtInfo;
            }
        }
        
        unset($info->card_pass);
        $info->products = $products;
        $info->hadChange = $hadChange;
        if($info->shouhuo_json){
            $info->shouhuo_json = json_decode($info->shouhuo_json, JSON_UNESCAPED_UNICODE);
        }
         
        $return = array();
        $return['code'] = 1;
        $return['message'] = '获取成功';
        $return['data'] = $info;
        // var_dump($return);die;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function comment(){
        global $db,$request,$comId;
        
        $id = (int)$request['id'];
        $userId = (int)$request['user_id'];
        $fenbiao = getFenbiao($comId,20);
        $cards = $db->get_row("select * from kmd_change_card where id = $id and userId = $userId ");
        if(!$cards){
            return '{"code":0,"message":"未找到对应的兑换记录！"}';
        }
        
        if($cards->status != 5){
            return '{"code":0,"message":"当前兑换记录不是待评价状态！"}';
        }
        
        $product = json_decode($cards->product_json, true);
        //$inventoryId = (int)$request['inventoryId'];
        $inventIds = [$product['inventory_id']];
        $star = (int)$request['star'];
        $star1 =(int)$request['star1'];
        $star2 =(int)$request['star2'];
        $imgs = $request['uploadedfile1'];
        if(!empty($request['uploadedfile2'])){
            $imgs.='|'.$request['uploadedfile2'];
        }
        if(!empty($request['uploadedfile3'])){
            $imgs.='|'.$request['uploadedfile3'];
        }
        $content = $request['content'];
        $fenbiao = getFenbiao($comId,20);
        
        $db->query("update kmd_change_card set status= 6 where id=$id");
        if(!empty($inventIds)){
            foreach ($inventIds as $inventoryId) {
                $u = $db->get_row("select nickname from users where id=$userId");
                $p = $db->get_row("select productId,title from demo_product_inventory where id=$inventoryId");
                $comment = array();
                $comment['orderId'] = (int)$db->get_var("select id from order$fenbiao where orderId = '$cards->orderId' ");
                $comment['pdtId'] = $p->productId;
                $comment['inventoryId'] = $inventoryId;
                $comment['comId'] = $comId;
                $comment['userId'] = $userId;
                $comment['giftId'] = $cards->giftId;
                $comment['cardId'] = $cards->id;
                $comment['name'] = $u->nickname;
                $comment['order_orderId'] = $cards->use_no;
                $comment['pdtName'] = $p->title;
                $comment['star'] = $star ? $star : 5;
                $comment['star1'] = $star1;
                $comment['star2'] = $star2;
                $comment['cont1'] = $content;
                $comment['images1'] = $imgs;
                $comment['dtTime1'] = date('Y-m-d H:i:s');
                
                $db->insert_update('order_comment'.$fenbiao,$comment,'id');
            }
        }
        
        return '{"code":1,"message":"评价成功"}';
    }
    
    public function changeList(){
        global $db,$request,$comId;
        
        $scene = (int)$request['scene'];

        $userId = (int)$request['user_id'];
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select * from kmd_change_card where comId=$comId and userId = $userId and status >= 3 ";

        //1 未使用  2已过期 3待发货  4 待收货 5待评价 6售后
        if(!empty($scene)){//类型：0-全部  3-待发货  4-待收货  5-待评价
            $sql .= " and status = $scene ";
        }
        $count = $db->get_var(str_replace('*','count(*)',$sql));
        
        $sql.=" order by use_at desc limit ".((int)($page-1)*$pageNum).",".$pageNum; 
        $pdts = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = array();
                $data['id'] = $pdt->id;
                
                $content = str_replace('\\"','"',$pdt->product_json);
                $content = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$content));
                
                $product_json = json_decode($content);
                $data['products'] = $product_json;
                $inventoryId = $product_json->inventory_id;
                $data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
                $data['end_at'] = date("Y-m-d H:i",strtotime($pdt->end_at));
                $data['code'] = $pdt->code;
                $data['pass'] = $pdt->pass;
                $data['is_give'] = $pdt->is_give;
                $data['orderId'] = $pdt->orderId;
    
                $data['use_at'] = date("Y-m-d H:i",$pdt->use_at);
                $data['changeOrderId'] = $pdt->changeOrderId;
                $data['gift_info'] =  json_decode($pdt->gift_json, JSON_UNESCAPED_UNICODE);
                if(!isset($data['gift_info']['price'])){
                    $data['gift_info']['price'] = $db->get_var("select price_sale from demo_product_inventory where id = ".$product_json->inventory_id);
                }
                
                $data['give_name'] = '';
                if($pdt->give_at){
                    $data['give_at'] = date("Y-m-d H:i",strtotime($pdt->give_at));
                    $data['give_name'] = $db->get_row("select nickname,phone,image from users where id = $pdt->give_user");
                }else{
                    $data['give_at'] = null;
                }

                $data['status'] = $pdt->status;
         
                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function showCode()
	{
		global $db,$request,$comId;

		$id = (int)$request['id'];
		
		$card = $db->get_row("select * from kmd_change_card where id = $id");
		if(!$card){
		    return '{"code":0,"message":"未找到卡信息"}'; 
		}
		
	    $filename = $comId.'_'.$card->card_no.'_'.rand(10000,99999).'.png'; //新图片名称
		$newFilePath = ABSPATH.'upload/card/'.$filename;
		$url = "https://".$_SERVER['HTTP_HOST'].'/upload/card/'.$filename;
		
		if(is_file($newFilePath)){
		    $db->query("update kmd_change_card set code_url = '$url' where id = $id ");
		    
			return '{"code":1,"message":"","data":"'.$url.'"}';
		}
		$access_token = self::getAccessToken(3);
	  	$ewm_url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
	  	$params = array("scene"=>"card_no=".$card->card_no."&card_id=".$id,"page"=>"pages/goods_detail/goods_detail");
	  	$ewm = self::curl_post($ewm_url,$params);
	    $result = json_decode($ewm, true);
	    if(isset($result['errmsg'])){
	        return '{"code":0,"message":"生成失败，失败原因：'.$result['errmsg'].'"}';
	    }
		$newFile = fopen($newFilePath,"w"); //打开文件准备写入
		fwrite($newFile,$ewm); //写入二进制流到文件
		fclose($newFile);
		
		
		$db->query("update kmd_change_card set code_url = '$url' where id = $id ");
		
	  	return '{"code":1,"message":"","data":"'.$url.'"}';
	}
	
	public static function getAccessToken($type = 3)
	{
		global $db,$comId;
		$token_file = cache_get('token',$comId);
		if(true){
			$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=$type limit 1");
			if(empty($weixin_set)||empty($weixin_set->info)){
				return '{"code":0,"message":"微信配置有误，无法登录"}';
			}
			$weixin_arr = json_decode($weixin_set->info);
			$appid = $weixin_arr->appid;
			$appsecret = $weixin_arr->appsecret;
			$token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		  	$token_info = self::https_request($token_url);
		  //	var_dump($token_info);die;
		  	//file_put_contents('request.txt',json_encode($token_info,JSON_UNESCAPED_UNICODE));
		  //	cache_push('token',$comId,$token_info,110);
		  //	var_dump($token_info);
		  	return $token_info['access_token'];
		}else{
		  	return $token_file->access_token;
		}
	}
	
	public static function https_request($url)
	{
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl,CURLOPT_HEADER,0); //
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
	    $response = curl_exec($curl);  
	    curl_close($curl);
	    $jsoninfo = json_decode($response,true); 
	    return $jsoninfo;
	}
	
	
}